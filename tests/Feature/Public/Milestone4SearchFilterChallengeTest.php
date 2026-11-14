<?php

namespace Tests\Feature\Public;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Milestone4SearchFilterChallengeTest extends TestCase
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
            'nama' => 'PT Nusantara Solusindo Tech',
            'email' => 'hr@nusantarasolusindo.id',
            'telepon' => '021-5551234',
            'website' => 'https://nusantarasolusindo.id',
            'alamat' => 'Jakarta Selatan',
        ]);

        $this->companyBeta = Perusahaan::create([
            'nama' => 'CV Aksara Visual Studio',
            'email' => 'recruitment@aksarastudio.id',
            'telepon' => '022-7775678',
            'website' => 'https://aksarastudio.id',
            'alamat' => 'Bandung, Jawa Barat',
        ]);

        $this->catTech = Kategori::create([
            'nama' => 'Teknologi Informasi & Rekayasa Perangkat Lunak',
            'deskripsi' => 'Pengembangan software, web, mobile, DevOps, dan cloud computing.',
        ]);

        $this->catDesign = Kategori::create([
            'nama' => 'Desain Komunikasi Visual & Multimedia',
            'deskripsi' => 'Desain grafis, UI/UX, motion graphics, dan animasi video.',
        ]);
    }

    private function createVacancy(array $attributes = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $this->companyAlpha->id,
            'kategori_id' => $this->catTech->id,
            'judul' => 'Fullstack Laravel Developer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Jakarta Selatan',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Deskripsi pekerjaan lowongan magang dengan kualifikasi standar.',
            'persyaratan' => 'Persyaratan lowongan magang dengan kualifikasi standar.',
            'link_pendaftaran' => 'https://nusantarasolusindo.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $attributes));
    }

    /**
     * Edge Case 1: Whitespace-only search queries.
     * Whitespace-only input (spaces, tabs) must not cause SQL errors and should behave
     * as an unfiltered catalog or return all active vacancies.
     */
    public function test_whitespace_only_query_does_not_break_catalog_and_returns_all_active_vacancies(): void
    {
        $job1 = $this->createVacancy(['judul' => 'Job Alpha Active']);
        $job2 = $this->createVacancy(['judul' => 'Job Beta Active']);

        // Test with URL encoded spaces (%20%20%20)
        $responseSpaces = $this->get('/?q=%20%20%20');
        $responseSpaces->assertOk();
        $responseSpaces->assertSee($job1->judul);
        $responseSpaces->assertSee($job2->judul);

        // Test with tabs and newlines
        $responseWhitespace = $this->get('/?q='.urlencode(" \t \n "));
        $responseWhitespace->assertOk();
        $responseWhitespace->assertSee($job1->judul);
        $responseWhitespace->assertSee($job2->judul);
    }

    /**
     * Edge Case 2: Special SQL wildcards (% and _) in search keyword.
     * Tests that SQL LIKE wildcards % and _ do not trigger SQL syntax errors or crash the query builder.
     */
    public function test_special_sql_wildcards_in_search_keyword_do_not_crash_database(): void
    {
        $jobPercent = $this->createVacancy(['judul' => 'Data Analyst 100% Remote']);
        $jobUnderscore = $this->createVacancy(['judul' => 'Lead_Software_Engineer']);
        $jobNormal = $this->createVacancy(['judul' => 'Mobile iOS Specialist']);

        // Search %
        $responsePercent = $this->get('/?q=%');
        $responsePercent->assertOk();

        // Search _
        $responseUnderscore = $this->get('/?q=_');
        $responseUnderscore->assertOk();

        // Search combined wildcards
        $responseCombo = $this->get('/?q='.urlencode('%_%'));
        $responseCombo->assertOk();
    }

    /**
     * Edge Case 3: SQL Injection / Malicious characters in search query.
     * Prepared statements / PDO bindings must prevent SQL injection and return clean HTTP 200 responses.
     */
    public function test_sql_injection_payloads_in_search_are_safely_parameterized(): void
    {
        $this->createVacancy(['judul' => 'Security Analyst Intern']);

        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE lowongans; --",
            "' UNION SELECT * FROM users --",
            '" OR ""="',
            "1' ORDER BY 1--+",
            "<script>alert('xss')</script>",
        ];

        foreach ($payloads as $payload) {
            $response = $this->get('/?q='.urlencode($payload));
            $response->assertOk();
            // Should not output unescaped script tag
            $response->assertDontSee('<script>alert(\'xss\')</script>', false);
        }

        // Verify lowongans table still exists and data is intact
        $this->assertDatabaseHas('lowongans', [
            'judul' => 'Security Analyst Intern',
        ]);
    }

    /**
     * Edge Case 4: Strict BR-01 Isolation with Keyword Search on Job Title.
     * An expired vacancy whose title exactly matches the search term must NEVER appear in search results.
     */
    public function test_expired_vacancy_matching_keyword_is_never_leaked_to_catalog(): void
    {
        // Active vacancy matching keyword
        $activeJob = $this->createVacancy([
            'judul' => 'SvelteKit Frontend Architect Intern',
            'status' => 'buka',
            'tenggat' => now()->addDays(7)->toDateString(),
        ]);

        // Expired vacancy matching keyword (deadline yesterday)
        $expiredJob = $this->createVacancy([
            'judul' => 'SvelteKit Frontend Architect Intern Expired',
            'status' => 'buka',
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        // Expired vacancy matching keyword (deadline 30 days ago)
        $oldExpiredJob = $this->createVacancy([
            'judul' => 'SvelteKit Senior Lead Expired',
            'status' => 'buka',
            'tenggat' => now()->subDays(30)->toDateString(),
        ]);

        $response = $this->get('/?q=SvelteKit');

        $response->assertOk();
        $response->assertSee($activeJob->judul);
        $response->assertDontSee($expiredJob->judul);
        $response->assertDontSee($oldExpiredJob->judul);

        // Also verify on alternate routes /lowongan and /katalog
        $responseLowongan = $this->get('/lowongan?q=SvelteKit');
        $responseLowongan->assertOk();
        $responseLowongan->assertSee($activeJob->judul);
        $responseLowongan->assertDontSee($expiredJob->judul);

        $responseKatalog = $this->get('/katalog?q=SvelteKit');
        $responseKatalog->assertOk();
        $responseKatalog->assertSee($activeJob->judul);
        $responseKatalog->assertDontSee($expiredJob->judul);
    }

    /**
     * Edge Case 5: Strict BR-01 Isolation with Keyword Search for Closed Vacancies.
     * Vacancies with status = 'ditutup' or non-buka status matching keyword must NEVER leak into catalog.
     */
    public function test_closed_vacancy_matching_keyword_is_never_leaked_to_catalog(): void
    {
        // Active vacancy
        $activeJob = $this->createVacancy([
            'judul' => 'Solidity Blockchain Researcher Intern',
            'status' => 'buka',
            'tenggat' => now()->addDays(20)->toDateString(),
        ]);

        // Closed vacancy with future deadline
        $closedJob = $this->createVacancy([
            'judul' => 'Solidity Blockchain Lead Closed',
            'status' => 'ditutup',
            'tenggat' => now()->addDays(20)->toDateString(),
        ]);

        $response = $this->get('/?q=Solidity');

        $response->assertOk();
        $response->assertSee($activeJob->judul);
        $response->assertDontSee($closedJob->judul);
    }

    /**
     * Edge Case 6: Strict BR-01 Isolation with Company Name Search and Nested Boolean Logic.
     * When searching by company name, expired or closed vacancies from that company must NEVER leak.
     * This tests against SQL operator precedence bugs: (A AND B) AND (C OR D) vs A AND B AND C OR D.
     */
    public function test_company_search_strictly_isolates_expired_and_closed_vacancies_of_that_company(): void
    {
        $targetCompany = Perusahaan::create([
            'nama' => 'PT Cakra Data Nusantara',
            'email' => 'careers@cakradata.id',
        ]);

        // Active job from target company
        $activeJob = $this->createVacancy([
            'perusahaan_id' => $targetCompany->id,
            'judul' => 'Active Big Data Engineer',
            'status' => 'buka',
            'tenggat' => now()->addDays(15)->toDateString(),
        ]);

        // Expired job from target company
        $expiredJob = $this->createVacancy([
            'perusahaan_id' => $targetCompany->id,
            'judul' => 'Expired Machine Learning Scientist',
            'status' => 'buka',
            'tenggat' => now()->subDays(5)->toDateString(),
        ]);

        // Closed job from target company
        $closedJob = $this->createVacancy([
            'perusahaan_id' => $targetCompany->id,
            'judul' => 'Closed Data Operations Lead',
            'status' => 'ditutup',
            'tenggat' => now()->addDays(15)->toDateString(),
        ]);

        $response = $this->get('/?q=Cakra');

        $response->assertOk();
        $response->assertSee($activeJob->judul);
        $response->assertDontSee($expiredJob->judul);
        $response->assertDontSee($closedJob->judul);
    }

    /**
     * Edge Case 7: Case Insensitivity and Partial String Matching.
     * Search should match regardless of uppercase, lowercase, or mixed case, and on partial words.
     */
    public function test_search_matches_case_insensitively_and_on_partial_substrings(): void
    {
        $job = $this->createVacancy([
            'judul' => 'Cyber Security Incident Responder Intern',
        ]);

        // All lowercase
        $responseLower = $this->get('/?q=cyber');
        $responseLower->assertOk();
        $responseLower->assertSee($job->judul);

        // All uppercase
        $responseUpper = $this->get('/?q=CYBER');
        $responseUpper->assertOk();
        $responseUpper->assertSee($job->judul);

        // Mixed case
        $responseMixed = $this->get('/?q=cYbEr');
        $responseMixed->assertOk();
        $responseMixed->assertSee($job->judul);

        // Partial middle word
        $responsePartial = $this->get('/?q=incid');
        $responsePartial->assertOk();
        $responsePartial->assertSee($job->judul);
    }

    /**
     * Edge Case 8: Multi-criteria Combinations (q + kategori_id + tipe + sistem_kerja).
     * Tests that all four filter dimensions are applied conjunctively (AND logic).
     */
    public function test_multi_criteria_four_dimensional_filter_conjunctive_logic(): void
    {
        // Target match: all 4 criteria match
        $targetJob = $this->createVacancy([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'judul' => 'Quantum Computing Research Intern',
        ]);

        // Mismatch 1: wrong tipe (paruh_waktu)
        $mismatchTipe = $this->createVacancy([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'remote',
            'judul' => 'Quantum Computing Part-time Specialist',
        ]);

        // Mismatch 2: wrong sistem_kerja (onsite)
        $mismatchSistem = $this->createVacancy([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'judul' => 'Quantum Computing Onsite Intern',
        ]);

        // Mismatch 3: wrong kategori (Design)
        $mismatchKategori = $this->createVacancy([
            'kategori_id' => $this->catDesign->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'judul' => 'Quantum Computing Visual Designer Intern',
        ]);

        // Mismatch 4: wrong keyword
        $mismatchKeyword = $this->createVacancy([
            'kategori_id' => $this->catTech->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'judul' => 'Bioinformatics Genomics Intern',
        ]);

        $url = sprintf(
            '/?q=Quantum&kategori_id=%d&tipe=magang&sistem_kerja=remote',
            $this->catTech->id
        );

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee($targetJob->judul);
        $response->assertDontSee($mismatchTipe->judul);
        $response->assertDontSee($mismatchSistem->judul);
        $response->assertDontSee($mismatchKategori->judul);
        $response->assertDontSee($mismatchKeyword->judul);
    }

    /**
     * Edge Case 9: Multi-criteria Tampered / Invalid Parameter Values.
     * System should gracefully handle non-existent IDs, invalid enum values, and array inputs without 500 errors.
     */
    public function test_tampered_and_invalid_filter_parameters_are_handled_gracefully(): void
    {
        $job = $this->createVacancy(['judul' => 'Robustness Verification Intern']);

        // Non-numeric kategori_id (e.g. ?kategori_id=exploit)
        $respNonNumericCat = $this->get('/?kategori_id=malicious_string');
        $respNonNumericCat->assertOk();
        $respNonNumericCat->assertSee($job->judul);

        // Out-of-bounds non-existent kategori_id
        $respOutOfBoundsCat = $this->get('/?kategori_id=99999999');
        $respOutOfBoundsCat->assertOk();
        $respOutOfBoundsCat->assertSee('Tidak Ada Lowongan yang Sesuai');

        // Invalid tipe (not in allowed enums)
        $respInvalidTipe = $this->get('/?tipe=contract_hacker');
        $respInvalidTipe->assertOk();
        $respInvalidTipe->assertSee($job->judul);

        // Invalid sistem_kerja (not in allowed enums)
        $respInvalidSistem = $this->get('/?sistem_kerja=outer_space');
        $respInvalidSistem->assertOk();
        $respInvalidSistem->assertSee($job->judul);
    }

    /**
     * Edge Case 10: Pagination Persistence with Multi-criteria Filters.
     * Verifies that moving between page 1 and page 2 preserves all query parameters.
     */
    public function test_pagination_links_persist_all_query_parameters_in_both_directions(): void
    {
        // Create 15 matching vacancies
        for ($i = 1; $i <= 15; $i++) {
            $this->createVacancy([
                'kategori_id' => $this->catTech->id,
                'tipe' => 'magang',
                'sistem_kerja' => 'remote',
                'judul' => sprintf('Paging Cohort Vacancy #%02d', $i),
                'created_at' => now()->subMinutes(30 - $i),
            ]);
        }

        $baseFilterQuery = sprintf(
            'q=Cohort&kategori_id=%d&tipe=magang&sistem_kerja=remote',
            $this->catTech->id
        );

        // 1. Visit Page 1
        $responsePage1 = $this->get('/?'.$baseFilterQuery);
        $responsePage1->assertOk();

        $content1 = $responsePage1->getContent();

        // Must contain link to page 2 with all query parameters intact
        $this->assertStringContainsString('page=2', $content1);
        $this->assertStringContainsString('q=Cohort', $content1);
        $this->assertStringContainsString('kategori_id='.$this->catTech->id, $content1);
        $this->assertStringContainsString('tipe=magang', $content1);
        $this->assertStringContainsString('sistem_kerja=remote', $content1);

        // 2. Visit Page 2
        $responsePage2 = $this->get('/?'.$baseFilterQuery.'&page=2');
        $responsePage2->assertOk();

        $content2 = $responsePage2->getContent();

        // Must contain link to page 1 with all query parameters intact
        $this->assertStringContainsString('page=1', $content2);
        $this->assertStringContainsString('q=Cohort', $content2);
        $this->assertStringContainsString('kategori_id='.$this->catTech->id, $content2);
        $this->assertStringContainsString('tipe=magang', $content2);
        $this->assertStringContainsString('sistem_kerja=remote', $content2);
    }

    /**
     * Edge Case 11: Empty Result Handling and Clean Reset Action.
     * When no vacancies match, clean empty state is displayed with reset button leading to clean URL.
     */
    public function test_empty_search_results_render_clean_empty_state_with_reset_button(): void
    {
        $this->createVacancy(['judul' => 'Standard Developer Intern']);

        $response = $this->get('/?q=KeywordPalingMustahilDitemukanXYZ999');

        $response->assertOk();
        $response->assertSee('Tidak Ada Lowongan yang Sesuai');
        $response->assertSee('Reset Semua Filter');
        $response->assertDontSee('Standard Developer Intern');

        // Check active filter badge bar exists and provides removal links
        $response->assertSee('Filter Aktif:');
        $response->assertSee('Kata Kunci: &quot;KeywordPalingMustahilDitemukanXYZ999&quot;', false);
    }

    /**
     * Edge Case 12: Search query matching exclusively inactive vacancies returns empty state.
     * If all vacancies matching the search term are expired or closed, the public catalog must render
     * the empty state rather than leaking the expired/closed jobs.
     */
    public function test_search_matching_only_expired_or_closed_vacancies_yields_clean_empty_state(): void
    {
        // Only expired job with this keyword
        $this->createVacancy([
            'judul' => 'Obsolete Pascal Developer Intern',
            'status' => 'buka',
            'tenggat' => now()->subDays(10)->toDateString(),
        ]);

        // Only closed job with this keyword
        $this->createVacancy([
            'judul' => 'Obsolete Pascal Developer Specialist',
            'status' => 'ditutup',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->get('/?q=Pascal');

        $response->assertOk();
        $response->assertSee('Tidak Ada Lowongan yang Sesuai');
        $response->assertDontSee('Obsolete Pascal Developer');
    }
}
