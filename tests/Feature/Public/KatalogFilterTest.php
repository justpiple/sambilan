<?php

namespace Tests\Feature\Public;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $companyAlpha;

    private Perusahaan $companyBeta;

    private Kategori $catTech;

    private Kategori $catDesign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAlpha = Perusahaan::create([
            'nama' => 'Nusantara Cloud Tech',
            'email' => 'hr@nusantaracloud.id',
        ]);

        $this->companyBeta = Perusahaan::create([
            'nama' => 'Aksara Creative Studio',
            'email' => 'careers@aksarastudio.id',
        ]);

        $this->catTech = Kategori::create([
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Pengembangan rekayasa perangkat lunak dan infrastruktur',
        ]);

        $this->catDesign = Kategori::create([
            'nama' => 'Desain Grafis & Kreatif',
            'deskripsi' => 'Desain visual, ilustrasi, dan pengalaman antarmuka',
        ]);
    }

    private function createJob(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $this->companyAlpha->id,
            'kategori_id' => $this->catTech->id,
            'judul' => 'Standard Software Engineer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Jakarta Selatan',
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'deskripsi' => 'Deskripsi pekerjaan baku untuk pengujian filter.',
            'persyaratan' => 'Persyaratan kualifikasi baku untuk pengujian filter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides));
    }

    public function test_keyword_search_filters_by_job_title(): void
    {
        $this->createJob(['judul' => 'Senior Laravel Backend Developer']);
        $this->createJob(['judul' => 'Motion Graphics Animator']);

        $response = $this->get('/?q=Laravel');

        $response->assertOk();
        $response->assertSee('Senior Laravel Backend Developer');
        $response->assertDontSee('Motion Graphics Animator');
    }

    public function test_keyword_search_filters_by_company_name(): void
    {
        $this->createJob([
            'perusahaan_id' => $this->companyAlpha->id,
            'judul' => 'Cloud Infrastructure Intern',
        ]);

        $this->createJob([
            'perusahaan_id' => $this->companyBeta->id,
            'judul' => 'Typography Designer Intern',
        ]);

        $response = $this->get('/?q=Aksara');

        $response->assertOk();
        $response->assertSee('Typography Designer Intern');
        $response->assertDontSee('Cloud Infrastructure Intern');
    }

    public function test_keyword_search_is_case_insensitive_and_matches_partial_terms(): void
    {
        $this->createJob(['judul' => 'Fullstack JavaScript Engineer']);

        $responseLower = $this->get('/?q=javascript');
        $responseLower->assertOk();
        $responseLower->assertSee('Fullstack JavaScript Engineer');

        $responseUpper = $this->get('/?q=SCRIPT');
        $responseUpper->assertOk();
        $responseUpper->assertSee('Fullstack JavaScript Engineer');
    }

    public function test_keyword_search_does_not_leak_inactive_or_expired_vacancies(): void
    {
        $this->createJob([
            'judul' => 'Active Python Machine Learning Intern',
            'status' => 'buka',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $this->createJob([
            'judul' => 'Expired Python Data Analyst Intern',
            'status' => 'buka',
            'tenggat' => now()->subDays(3)->toDateString(),
        ]);

        $this->createJob([
            'judul' => 'Closed Python Backend Developer',
            'status' => 'ditutup',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->get('/?q=Python');

        $response->assertOk();
        $response->assertSee('Active Python Machine Learning Intern');
        $response->assertDontSee('Expired Python Data Analyst Intern');
        $response->assertDontSee('Closed Python Backend Developer');
    }

    public function test_category_filter_returns_only_matching_vacancies(): void
    {
        $techJob = $this->createJob([
            'kategori_id' => $this->catTech->id,
            'judul' => 'DevOps Automation Engineer',
        ]);

        $designJob = $this->createJob([
            'kategori_id' => $this->catDesign->id,
            'judul' => 'Brand Identity Illustrator',
        ]);

        $response = $this->get('/?kategori_id='.$this->catTech->id);

        $response->assertOk();
        $response->assertSee($techJob->judul);
        $response->assertDontSee($designJob->judul);
    }

    public function test_job_type_filter_magang_returns_only_internships(): void
    {
        $internship = $this->createJob([
            'tipe' => 'magang',
            'judul' => 'Mahasiswa Magang Riset',
        ]);

        $partTime = $this->createJob([
            'tipe' => 'paruh_waktu',
            'judul' => 'Tenaga Paruh Waktu Logistik',
        ]);

        $response = $this->get('/?tipe=magang');

        $response->assertOk();
        $response->assertSee($internship->judul);
        $response->assertDontSee($partTime->judul);
    }

    public function test_job_type_filter_paruh_waktu_returns_only_part_time_jobs(): void
    {
        $internship = $this->createJob([
            'tipe' => 'magang',
            'judul' => 'Mahasiswa Magang Riset',
        ]);

        $partTime = $this->createJob([
            'tipe' => 'paruh_waktu',
            'judul' => 'Tenaga Paruh Waktu Logistik',
        ]);

        $response = $this->get('/?tipe=paruh_waktu');

        $response->assertOk();
        $response->assertSee($partTime->judul);
        $response->assertDontSee($internship->judul);
    }

    public function test_work_arrangement_filter_returns_expected_vacancies(): void
    {
        $remoteJob = $this->createJob([
            'sistem_kerja' => 'remote',
            'judul' => 'Remote Golang Developer',
        ]);

        $onsiteJob = $this->createJob([
            'sistem_kerja' => 'onsite',
            'judul' => 'Onsite Network Technician',
        ]);

        $hybridJob = $this->createJob([
            'sistem_kerja' => 'hybrid',
            'judul' => 'Hybrid System Administrator',
        ]);

        $response = $this->get('/?sistem_kerja=remote');

        $response->assertOk();
        $response->assertSee($remoteJob->judul);
        $response->assertDontSee($onsiteJob->judul);
        $response->assertDontSee($hybridJob->judul);
    }

    public function test_multi_criteria_filter_combines_all_parameters_with_and_logic(): void
    {
        $match = $this->createJob([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'judul' => 'Target Flutter Mobile Intern',
        ]);

        $partialMatch1 = $this->createJob([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'remote',
            'judul' => 'Partial Flutter Paruh Waktu',
        ]);

        $partialMatch2 = $this->createJob([
            'kategori_id' => $this->catDesign->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'judul' => 'Partial Flutter Desain UI',
        ]);

        $partialMatch3 = $this->createJob([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'judul' => 'Partial Flutter Onsite',
        ]);

        $response = $this->get(sprintf(
            '/?kategori_id=%d&tipe=magang&sistem_kerja=remote&q=Flutter',
            $this->catTech->id
        ));

        $response->assertOk();
        $response->assertSee($match->judul);
        $response->assertDontSee($partialMatch1->judul);
        $response->assertDontSee($partialMatch2->judul);
        $response->assertDontSee($partialMatch3->judul);
    }

    public function test_query_string_persists_across_pagination_links(): void
    {
        for ($i = 1; $i <= 14; $i++) {
            $this->createJob([
                'tipe' => 'magang',
                'judul' => sprintf('Magang Engineer Batch %02d', $i),
                'created_at' => now()->subMinutes(20 - $i),
            ]);
        }

        $response = $this->get('/?tipe=magang&q=Batch');

        $response->assertOk();
        $content = $response->getContent();

        $this->assertTrue(
            str_contains($content, 'page=2') &&
            str_contains($content, 'tipe=magang') &&
            str_contains($content, 'q=Batch'),
            'Pagination links must preserve query parameters withQueryString()'
        );
    }

    public function test_empty_state_is_rendered_when_zero_vacancies_match_criteria(): void
    {
        $response = $this->get('/?q=KeywordPalingMustahilDitemukan12345');

        $response->assertOk();
        $content = $response->getContent();

        $this->assertTrue(
            str_contains($content, 'Tidak Ada Lowongan') ||
            str_contains($content, 'Tidak ada lowongan') ||
            str_contains($content, 'Belum Ada Lowongan') ||
            str_contains($content, 'tidak ditemukan'),
            'Empty state notification should be visible when zero vacancies match.'
        );

        $this->assertTrue(
            str_contains($content, 'Hapus') ||
            str_contains($content, 'Reset') ||
            str_contains($content, 'Semua Lowongan'),
            'Reset filter button must be offered in empty state.'
        );
    }
}
