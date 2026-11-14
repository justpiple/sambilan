<?php

namespace Tests\Feature\Public;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Solusi Teknologi Mahasiswa',
            'email' => 'rekrutmen@solusitek.id',
            'sosial_media' => 'https://linkedin.com/company/solusitek',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Software Engineering',
            'deskripsi' => 'Pengembangan perangkat lunak web dan mobile',
        ]);
    }

    private function createLowongan(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Frontend Developer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung, Jawa Barat',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Pengembangan antarmuka menggunakan Blade dan Tailwind CSS.',
            'persyaratan' => 'Mahasiswa aktif tingkat 2 atau 3 jurusan informatika.',
            'link_pendaftaran' => 'https://solusitek.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides));
    }

    public function test_public_catalog_is_accessible_at_root_url_and_renders_successfully(): void
    {
        $this->createLowongan(['judul' => 'Junior Backend Engineer']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Junior Backend Engineer');
        $response->assertSee('PT Solusi Teknologi Mahasiswa');
    }

    public function test_catalog_is_accessible_via_dedicated_katalog_or_lowongan_route(): void
    {
        $this->createLowongan(['judul' => 'Quality Assurance Intern']);

        $response = $this->get('/lowongan');

        $response->assertOk();
        $response->assertSee('Quality Assurance Intern');
    }

    public function test_catalog_paginates_vacancies_at_nine_items_per_page(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->createLowongan([
                'judul' => sprintf('Lowongan Posisi Ke-%02d', $i),
                'created_at' => now()->subMinutes(15 - $i),
            ]);
        }

        $page1Response = $this->get('/');
        $page1Response->assertOk();

        $page2Response = $this->get('/?page=2');
        $page2Response->assertOk();

        $page1Response->assertSee('Lowongan Posisi Ke-12');
        $page1Response->assertSee('Lowongan Posisi Ke-04');
        $page1Response->assertDontSee('Lowongan Posisi Ke-03');

        $page2Response->assertSee('Lowongan Posisi Ke-03');
        $page2Response->assertSee('Lowongan Posisi Ke-01');
        $page2Response->assertDontSee('Lowongan Posisi Ke-12');
    }

    public function test_br01_expired_vacancies_are_excluded_from_public_catalog(): void
    {
        $activeJob = $this->createLowongan([
            'judul' => 'Active Lowongan Posisi',
            'tenggat' => now()->addDays(7)->toDateString(),
            'status' => 'buka',
        ]);

        $expiredJob = $this->createLowongan([
            'judul' => 'Expired Lowongan Posisi',
            'tenggat' => now()->subDays(1)->toDateString(),
            'status' => 'buka',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($activeJob->judul);
        $response->assertDontSee($expiredJob->judul);
    }

    public function test_br01_closed_vacancies_are_excluded_from_public_catalog(): void
    {
        $activeJob = $this->createLowongan([
            'judul' => 'Active Lowongan Terbuka',
            'status' => 'buka',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $closedJob = $this->createLowongan([
            'judul' => 'Closed Lowongan Ditutup',
            'status' => 'ditutup',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($activeJob->judul);
        $response->assertDontSee($closedJob->judul);
    }

    public function test_br01_vacancies_with_deadline_today_are_included_in_public_catalog(): void
    {
        $todayJob = $this->createLowongan([
            'judul' => 'Deadline Hari Ini Lowongan',
            'status' => 'buka',
            'tenggat' => now()->toDateString(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($todayJob->judul);
    }

    public function test_job_detail_page_renders_successfully_with_complete_attributes(): void
    {
        $job = $this->createLowongan([
            'judul' => 'Staff Analis Data Kampus',
            'deskripsi' => 'Melakukan pembersihan data survei mahasiswa menggunakan Python dan SQL.',
            'persyaratan' => 'Menguasai pandas dan visualisasi data dasar.',
            'jam_per_minggu' => 18,
            'lokasi' => 'Yogyakarta',
        ]);

        $response = $this->get('/lowongan/'.$job->id);

        $response->assertOk();
        $response->assertSee('Staff Analis Data Kampus');
        $response->assertSee('PT Solusi Teknologi Mahasiswa');
        $response->assertSee('Software Engineering');
        $response->assertSee('Yogyakarta');
        $response->assertSee('18 jam/minggu');
        $response->assertSee('Melakukan pembersihan data survei mahasiswa');
        $response->assertSee('Menguasai pandas dan visualisasi data dasar.');
    }

    public function test_job_detail_page_displays_formatted_compensation_in_rupiah(): void
    {
        $job = $this->createLowongan([
            'judul' => 'Paid UI/UX Designer',
            'kompensasi' => 3250000,
        ]);

        $response = $this->get('/lowongan/'.$job->id);

        $response->assertOk();
        $response->assertSee('Rp 3.250.000 / bulan');
    }

    public function test_job_detail_page_displays_non_kompensasi_when_null_or_zero(): void
    {
        $unpaidJobNull = $this->createLowongan([
            'judul' => 'Unpaid Volunteer Null',
            'kompensasi' => null,
        ]);

        $unpaidJobZero = $this->createLowongan([
            'judul' => 'Unpaid Volunteer Zero',
            'kompensasi' => 0,
        ]);

        $responseNull = $this->get('/lowongan/'.$unpaidJobNull->id);
        $responseNull->assertOk();
        $responseNull->assertSee('Non-kompensasi');

        $responseZero = $this->get('/lowongan/'.$unpaidJobZero->id);
        $responseZero->assertOk();
        $responseZero->assertSee('Non-kompensasi');
    }

    public function test_job_detail_page_renders_application_link_and_recruiter_contact(): void
    {
        $job = $this->createLowongan([
            'link_pendaftaran' => 'https://karir.mitra.id/apply/123',
            'kontak_pendaftaran' => 'hrd-kampus@mitra.id',
        ]);

        $response = $this->get('/lowongan/'.$job->id);

        $response->assertOk();
        $response->assertSee('https://karir.mitra.id/apply/123');
        $response->assertSee('hrd-kampus@mitra.id');
    }

    public function test_job_detail_page_returns_404_for_non_existent_vacancy_id(): void
    {
        $response = $this->get('/lowongan/999999');

        $response->assertNotFound();
    }

    public function test_job_detail_page_returns_404_or_closed_notice_for_inactive_vacancy(): void
    {
        $expiredJob = $this->createLowongan([
            'judul' => 'Lowongan Kadaluarsa Langsung',
            'tenggat' => now()->subDays(5)->toDateString(),
            'status' => 'buka',
        ]);

        $response = $this->get('/lowongan/'.$expiredJob->id);

        if ($response->status() === 200) {
            $this->assertTrue(
                str_contains($response->getContent(), 'Berakhir') ||
                str_contains($response->getContent(), 'Ditutup') ||
                str_contains($response->getContent(), 'tidak lagi menerima')
            );
        } else {
            $response->assertNotFound();
        }
    }
}
