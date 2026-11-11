<?php

namespace Tests\Feature\BusinessRules;

use App\Http\Requests\LowonganRequest;
use App\Models\Kategori;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeadlineValidationBR02Test extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Validasi Uji Mandiri',
            'email' => 'recruitment@validasi.id',
            'sosial_media' => 'https://validasi.id',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Quality Assurance',
            'deskripsi' => 'Pengujian perangkat lunak dan keandalan sistem',
        ]);

        Route::post('/_test/lowongan-validation', function (LowonganRequest $request) {
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
            'judul' => 'Junior QA Automation Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Mengembangkan pengujian otomatis dan memverifikasi integritas sistem.',
            'persyaratan' => 'Memahami PHPUnit, HTTP assertions, dan lifecycle Laravel.',
            'link_pendaftaran' => 'https://validasi.id/apply',
            'kontak_narahubung' => '081298765432',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides);
    }

    public function test_br02_rejects_deadline_in_the_past(): void
    {
        $payload = $this->validPayload([
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tenggat']);
    }

    public function test_br02_rejects_deadline_one_week_in_the_past(): void
    {
        $payload = $this->validPayload([
            'tenggat' => now()->subWeek()->toDateString(),
        ]);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tenggat']);
    }

    public function test_br02_accepts_today_as_registration_deadline(): void
    {
        $payload = $this->validPayload([
            'tenggat' => now()->toDateString(),
        ]);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_br02_accepts_future_registration_deadline(): void
    {
        $payload = $this->validPayload([
            'tenggat' => now()->addDays(30)->toDateString(),
        ]);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_br02_rejects_missing_deadline(): void
    {
        $payload = $this->validPayload();
        unset($payload['tenggat']);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tenggat']);
    }

    public function test_br02_rejects_invalid_date_format(): void
    {
        $payload = $this->validPayload([
            'tenggat' => 'bukan-tanggal-valid',
        ]);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tenggat']);
    }

    public function test_br02_boundary_at_different_times_of_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-11 23:59:59'));

        $todayResponse = $this->postJson('/_test/lowongan-validation', $this->validPayload([
            'tenggat' => '2026-11-11',
        ]));
        $todayResponse->assertStatus(200);

        $pastResponse = $this->postJson('/_test/lowongan-validation', $this->validPayload([
            'tenggat' => '2026-11-10',
        ]));
        $pastResponse->assertStatus(422);
        $pastResponse->assertJsonValidationErrors(['tenggat']);

        $futureResponse = $this->postJson('/_test/lowongan-validation', $this->validPayload([
            'tenggat' => '2026-11-12',
        ]));
        $futureResponse->assertStatus(200);

        Carbon::setTestNow();
    }

    public function test_br02_validation_error_message_is_in_indonesian(): void
    {
        $payload = $this->validPayload([
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        $response = $this->postJson('/_test/lowongan-validation', $payload);

        $response->assertStatus(422);
        $errorMessage = $response->json('errors.tenggat.0');
        $this->assertNotEmpty($errorMessage);
        $this->assertMatchesRegularExpression('/(hari ini|setelah atau sama dengan|masa lalu|tidak boleh sebelum)/i', $errorMessage);
    }

    public function test_br02_direct_validator_rules_inspection(): void
    {
        $request = new LowonganRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('tenggat', $rules);

        $tenggatRule = is_array($rules['tenggat'])
            ? implode('|', $rules['tenggat'])
            : $rules['tenggat'];

        $this->assertStringContainsString('required', $tenggatRule);
        $this->assertStringContainsString('date', $tenggatRule);
        $this->assertStringContainsString('after_or_equal:today', $tenggatRule);
    }
}
