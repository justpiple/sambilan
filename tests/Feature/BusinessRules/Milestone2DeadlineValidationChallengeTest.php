<?php

namespace Tests\Feature\BusinessRules;

use App\Http\Requests\LowonganRequest;
use App\Models\Kategori;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Milestone2DeadlineValidationChallengeTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Challenger Test Lab',
            'email' => 'lab@challenger.test',
            'sosial_media' => 'https://challenger.test',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Adversarial Security',
            'deskripsi' => 'Pengujian penetrasi dan stress test batasan sistem',
        ]);

        Route::post('/_challenger/lowongan-validate', function (LowonganRequest $request) {
            return response()->json([
                'success' => true,
                'data' => $request->validated(),
            ]);
        });
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Empirical QA Specialist Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Melakukan pengujian ketahanan dan verifikasi batasan sistem.',
            'persyaratan' => 'Menguasai PHPUnit, boundary testing, dan analisis data.',
            'link_pendaftaran' => 'https://challenger.test/apply',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides);
    }

    /**
     * Test exact date boundaries: today when current time is 00:00:00, midday, and 23:59:59.
     */
    public function test_br02_accepts_today_across_all_times_of_day(): void
    {
        // 1. Current time is exactly 00:00:00 (midnight start of day)
        Carbon::setTestNow(Carbon::parse('2026-11-11 00:00:00'));
        $resMidnight = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '2026-11-11']));
        $resMidnight->assertStatus(200);
        $resMidnight->assertJson(['success' => true]);

        // 2. Current time is midday 12:00:00
        Carbon::setTestNow(Carbon::parse('2026-11-11 12:00:00'));
        $resMidday = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '2026-11-11']));
        $resMidday->assertStatus(200);
        $resMidday->assertJson(['success' => true]);

        // 3. Current time is 23:59:59 (last second of day)
        Carbon::setTestNow(Carbon::parse('2026-11-11 23:59:59'));
        $resEndOfDay = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '2026-11-11']));
        $resEndOfDay->assertStatus(200);
        $resEndOfDay->assertJson(['success' => true]);

        Carbon::setTestNow();
    }

    /**
     * Test exact date boundaries: today with explicit timestamps (00:00:00, current time, 23:59:59).
     */
    public function test_br02_accepts_today_with_explicit_timestamp_inputs(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 15:30:00'));

        // Input today 00:00:00
        $resMidnight = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '2026-11-11 00:00:00']));
        $resMidnight->assertStatus(200);

        // Input today matching current time
        $resCurrent = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '2026-11-11 15:30:00']));
        $resCurrent->assertStatus(200);

        // Input today 23:59:59
        $resEndOfDay = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '2026-11-11 23:59:59']));
        $resEndOfDay->assertStatus(200);

        Carbon::setTestNow();
    }

    /**
     * Test exact date boundaries: yesterday is rejected with HTTP 422.
     */
    public function test_br02_rejects_yesterday_with_http_422(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 12:00:00'));

        $res = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
            'tenggat' => '2026-11-10',
        ]));

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['tenggat']);
        $this->assertEquals(
            'Tanggal tenggat pendaftaran tidak boleh sebelum tanggal hari ini.',
            $res->json('errors.tenggat.0')
        );

        Carbon::setTestNow();
    }

    /**
     * Test exact date boundaries: 1 second in the past before midnight (2026-11-10 23:59:59).
     */
    public function test_br02_rejects_one_second_in_past_before_midnight(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 00:00:00'));

        $res = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
            'tenggat' => '2026-11-10 23:59:59',
        ]));

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['tenggat']);
        $this->assertEquals(
            'Tanggal tenggat pendaftaran tidak boleh sebelum tanggal hari ini.',
            $res->json('errors.tenggat.0')
        );

        Carbon::setTestNow();
    }

    /**
     * Test exact date boundaries: tomorrow (+1 day).
     */
    public function test_br02_accepts_tomorrow_with_http_200(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 12:00:00'));

        $res = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
            'tenggat' => '2026-11-12',
        ]));

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        Carbon::setTestNow();
    }

    /**
     * Test exact date boundaries: 1 year in future.
     */
    public function test_br02_accepts_one_year_in_future_with_http_200(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 12:00:00'));

        $res = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
            'tenggat' => '2027-11-11',
        ]));

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // Far future (10 years)
        $resTenYears = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
            'tenggat' => '2036-11-11',
        ]));
        $resTenYears->assertStatus(200);

        // Leap year date
        $resLeap = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
            'tenggat' => '2028-02-29',
        ]));
        $resLeap->assertStatus(200);

        Carbon::setTestNow();
    }

    /**
     * Test invalid date strings: 'not-a-date' and other corrupted formats reject with HTTP 422.
     */
    public function test_br02_rejects_invalid_date_strings_with_http_422(): void
    {
        $invalidStrings = [
            'not-a-date',
            '2026-02-31',
            '2026-13-45',
            'invalid-string',
            '31/12/2026',
            'tomorrow-never-dies',
            'null',
            '123456',
        ];

        foreach ($invalidStrings as $invalidDate) {
            $res = $this->postJson('/_challenger/lowongan-validate', $this->validPayload([
                'tenggat' => $invalidDate,
            ]));

            $res->assertStatus(422);
            $res->assertJsonValidationErrors(['tenggat']);
        }
    }

    /**
     * Test empty date: empty string, whitespace, null, or omitted reject with HTTP 422.
     */
    public function test_br02_rejects_empty_date_with_http_422(): void
    {
        // 1. Empty string
        $resEmpty = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '']));
        $resEmpty->assertStatus(422);
        $resEmpty->assertJsonValidationErrors(['tenggat']);

        // 2. Whitespace only
        $resWhitespace = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => '   ']));
        $resWhitespace->assertStatus(422);
        $resWhitespace->assertJsonValidationErrors(['tenggat']);

        // 3. Null value
        $resNull = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => null]));
        $resNull->assertStatus(422);
        $resNull->assertJsonValidationErrors(['tenggat']);

        // 4. Missing tenggat key
        $payloadMissing = $this->validPayload();
        unset($payloadMissing['tenggat']);
        $resMissing = $this->postJson('/_challenger/lowongan-validate', $payloadMissing);
        $resMissing->assertStatus(422);
        $resMissing->assertJsonValidationErrors(['tenggat']);
        $this->assertEquals(
            'Batas waktu pendaftaran (tenggat) wajib diisi.',
            $resMissing->json('errors.tenggat.0')
        );
    }

    /**
     * Adversarial edge test: non-scalar date types (array, boolean).
     */
    public function test_br02_rejects_non_scalar_date_types(): void
    {
        $resArray = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => ['2026-11-15']]));
        $resArray->assertStatus(422);
        $resArray->assertJsonValidationErrors(['tenggat']);

        $resBool = $this->postJson('/_challenger/lowongan-validate', $this->validPayload(['tenggat' => true]));
        $resBool->assertStatus(422);
        $resBool->assertJsonValidationErrors(['tenggat']);
    }
}
