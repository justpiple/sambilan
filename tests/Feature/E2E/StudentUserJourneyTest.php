<?php

namespace Tests\Feature\E2E;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategoriTech;

    private Kategori $kategoriDesign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Solusi Teknologi Nusantara',
            'email' => 'rekrutmen@solusinusantara.id',
            'sosial_media' => 'https://linkedin.com/company/solusinusantara',
        ]);

        $this->kategoriTech = Kategori::create([
            'nama' => 'Software Engineering',
            'deskripsi' => 'Pengembangan web dan aplikasi piranti lunak',
        ]);

        $this->kategoriDesign = Kategori::create([
            'nama' => 'Desain Grafis & UI/UX',
            'deskripsi' => 'Perancangan antarmuka pengguna visual dan riset UX',
        ]);
    }

    private function createJob(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategoriTech->id,
            'judul' => 'Frontend Developer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung, Jawa Barat',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Pengembangan antarmuka responsif menggunakan Tailwind CSS dan Blade components.',
            'persyaratan' => 'Mahasiswa aktif Teknik Informatika/Sistem Informasi minimal semester 4.',
            'link_pendaftaran' => 'https://solusinusantara.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(20)->toDateString(),
            'status' => 'buka',
        ], $overrides));
    }

    public function test_student_can_explore_public_catalog_with_nine_items_per_page_pagination(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->createJob([
                'judul' => sprintf('Posisi Unggulan %02d', $i),
                'tenggat' => now()->addDays(10 + $i)->toDateString(),
            ]);
        }

        $pageOneResponse = $this->get('/');
        $pageOneResponse->assertStatus(200);
        $pageOneResponse->assertSee('Menampilkan <span class="font-mono font-semibold text-slate-800">15</span> lowongan aktif', false);

        for ($i = 15; $i >= 7; $i--) {
            $pageOneResponse->assertSee(sprintf('Posisi Unggulan %02d', $i));
        }
        $pageOneResponse->assertDontSee('Posisi Unggulan 06');
        $pageOneResponse->assertDontSee('Posisi Unggulan 01');

        $pageTwoResponse = $this->get('/?page=2');
        $pageTwoResponse->assertStatus(200);

        for ($i = 6; $i >= 1; $i--) {
            $pageTwoResponse->assertSee(sprintf('Posisi Unggulan %02d', $i));
        }
        $pageTwoResponse->assertDontSee('Posisi Unggulan 15');
        $pageTwoResponse->assertDontSee('Posisi Unggulan 07');
    }

    public function test_student_can_search_by_title_and_company_with_br01_expiration_isolation(): void
    {
        $companyFintech = Perusahaan::create([
            'nama' => 'Bank Mandiri Digital',
            'email' => 'talent@bankmandiri.co.id',
            'sosial_media' => 'https://bankmandiri.co.id',
        ]);

        $activeLaravel = $this->createJob([
            'judul' => 'Backend Laravel Cloud Architect Intern',
            'perusahaan_id' => $this->perusahaan->id,
            'status' => 'buka',
            'tenggat' => now()->addDays(14)->toDateString(),
        ]);

        $activeFintech = $this->createJob([
            'judul' => 'DevOps Cloud SRE Intern',
            'perusahaan_id' => $companyFintech->id,
            'status' => 'buka',
            'tenggat' => now()->addDays(14)->toDateString(),
        ]);

        $unrelatedJob = $this->createJob([
            'judul' => 'Copywriting & Social Media Lead',
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategoriDesign->id,
            'status' => 'buka',
            'tenggat' => now()->addDays(14)->toDateString(),
        ]);

        $expiredLaravel = $this->createJob([
            'judul' => 'Laravel Backend Senior Specialist',
            'perusahaan_id' => $this->perusahaan->id,
            'status' => 'buka',
            'tenggat' => now()->subDays(3)->toDateString(),
        ]);

        $closedLaravel = $this->createJob([
            'judul' => 'Laravel Security Researcher',
            'perusahaan_id' => $this->perusahaan->id,
            'status' => 'ditutup',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $searchTitleResponse = $this->get('/?q=Laravel');
        $searchTitleResponse->assertStatus(200);
        $searchTitleResponse->assertSee($activeLaravel->judul);
        $searchTitleResponse->assertDontSee($expiredLaravel->judul);
        $searchTitleResponse->assertDontSee($closedLaravel->judul);
        $searchTitleResponse->assertDontSee($unrelatedJob->judul);

        $searchCompanyResponse = $this->get('/?q=Bank+Mandiri');
        $searchCompanyResponse->assertStatus(200);
        $searchCompanyResponse->assertSee($activeFintech->judul);
        $searchCompanyResponse->assertDontSee($activeLaravel->judul);
        $searchCompanyResponse->assertDontSee($unrelatedJob->judul);
    }

    public function test_student_can_filter_by_multiple_criteria_with_pagination_query_persistence(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->createJob([
                'judul' => sprintf('Targeted Tech Remote Intern %02d', $i),
                'kategori_id' => $this->kategoriTech->id,
                'tipe' => 'magang',
                'sistem_kerja' => 'remote',
            ]);
        }

        $otherTypeJob = $this->createJob([
            'judul' => 'Tech Onsite Parttime Job',
            'kategori_id' => $this->kategoriTech->id,
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'onsite',
        ]);

        $otherCatJob = $this->createJob([
            'judul' => 'Design Remote Intern Job',
            'kategori_id' => $this->kategoriDesign->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
        ]);

        $filterUrl = sprintf(
            '/?kategori_id=%d&tipe=magang&sistem_kerja=remote',
            $this->kategoriTech->id
        );

        $filteredResponse = $this->get($filterUrl);
        $filteredResponse->assertStatus(200);

        $filteredResponse->assertSee('Menampilkan <span class="font-mono font-semibold text-slate-800">12</span> lowongan aktif', false);
        $filteredResponse->assertSee('Targeted Tech Remote Intern 12');
        $filteredResponse->assertDontSee($otherTypeJob->judul);
        $filteredResponse->assertDontSee($otherCatJob->judul);

        $filteredResponse->assertSee('kategori_id='.$this->kategoriTech->id);
        $filteredResponse->assertSee('tipe=magang');
        $filteredResponse->assertSee('sistem_kerja=remote');

        $pageTwoUrl = $filterUrl.'&page=2';
        $pageTwoResponse = $this->get($pageTwoUrl);
        $pageTwoResponse->assertStatus(200);
        $pageTwoResponse->assertSee('Targeted Tech Remote Intern 01');
        $pageTwoResponse->assertDontSee('Targeted Tech Remote Intern 12');
        $pageTwoResponse->assertDontSee($otherTypeJob->judul);
    }

    public function test_student_can_view_job_detail_with_compensation_hours_and_links(): void
    {
        $paidJob = $this->createJob([
            'judul' => 'Fullstack AI Engineer Intern',
            'kompensasi' => 3500000,
            'jam_per_minggu' => 20,
            'link_pendaftaran' => 'https://solusinusantara.id/careers/apply-ai',
            'kontak_pendaftaran' => '081234567890',
        ]);

        $unpaidJob = $this->createJob([
            'judul' => 'Open Source Community Volunteer Intern',
            'kompensasi' => null,
            'jam_per_minggu' => 10,
            'link_pendaftaran' => 'https://solusinusantara.id/careers/volunteer',
            'kontak_pendaftaran' => 'community@solusinusantara.id',
        ]);

        $expiredJob = $this->createJob([
            'judul' => 'Arsip Berkas Magang Lampau',
            'tenggat' => now()->subDays(5)->toDateString(),
            'status' => 'buka',
            'link_pendaftaran' => 'https://solusinusantara.id/careers/expired',
            'kontak_pendaftaran' => '081234567890',
        ]);

        $paidDetailResponse = $this->get(route('katalog.show', $paidJob));
        $paidDetailResponse->assertStatus(200);
        $paidDetailResponse->assertSee('Fullstack AI Engineer Intern');
        $paidDetailResponse->assertSee('Rp 3.500.000');
        $paidDetailResponse->assertSee('20 jam/minggu');
        $paidDetailResponse->assertSee('https://solusinusantara.id/careers/apply-ai');
        $paidDetailResponse->assertSee('Lamar Sekarang');
        $paidDetailResponse->assertSee('WhatsApp Narahubung');
        $paidDetailResponse->assertSee('https://wa.me/6281234567890');

        $unpaidDetailResponse = $this->get(route('katalog.show', $unpaidJob));
        $unpaidDetailResponse->assertStatus(200);
        $unpaidDetailResponse->assertSee('Open Source Community Volunteer Intern');
        $unpaidDetailResponse->assertSee('Non-kompensasi');
        $unpaidDetailResponse->assertSee('10 jam/minggu');
        $unpaidDetailResponse->assertSee('Email Narahubung');
        $unpaidDetailResponse->assertSee('mailto:community@solusinusantara.id');

        $expiredDetailResponse = $this->get(route('katalog.show', $expiredJob));
        $expiredDetailResponse->assertStatus(200);
        $expiredDetailResponse->assertSee('Lowongan Telah Berakhir');
        $expiredDetailResponse->assertSee('Pendaftaran Ditutup');
    }

    public function test_student_sees_empty_state_when_no_jobs_match_with_reset_action(): void
    {
        $this->createJob(['judul' => 'Standard Active Job']);

        $response = $this->get('/?q=QueryPencarianYangPastiTidakAdaSamaSekali');

        $response->assertStatus(200);
        $response->assertSee('Tidak Ada Lowongan yang Sesuai');
        $response->assertSee('Reset Semua Filter');
        $response->assertDontSee('Standard Active Job');
    }
}
