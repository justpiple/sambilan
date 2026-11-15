<?php

namespace Tests\Feature\BusinessRules;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\KategoriSeeder;
use Database\Seeders\LowonganSeeder;
use Database\Seeders\PerusahaanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Milestone2SeederIntegrityChallengeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test running db:seed twice produces NO duplicate key constraint errors
     * and NO duplicate records for users, categories, companies, or vacancies.
     */
    public function test_double_db_seed_execution_is_strictly_idempotent(): void
    {
        // First run
        $this->seed(DatabaseSeeder::class);

        $usersFirst = User::count();
        $categoriesFirst = Kategori::count();
        $companiesFirst = Perusahaan::count();
        $vacanciesFirst = Lowongan::count();

        $this->assertEquals(1, $usersFirst, 'DatabaseSeeder must seed exactly 1 admin user.');
        $this->assertEquals(5, $categoriesFirst, 'DatabaseSeeder must seed exactly 5 categories.');
        $this->assertEquals(6, $companiesFirst, 'DatabaseSeeder must seed exactly 6 companies.');
        $this->assertEquals(14, $vacanciesFirst, 'DatabaseSeeder must seed exactly 14 vacancies.');

        // Second run (Idempotency verification)
        $this->seed(DatabaseSeeder::class);

        $usersSecond = User::count();
        $categoriesSecond = Kategori::count();
        $companiesSecond = Perusahaan::count();
        $vacanciesSecond = Lowongan::count();

        $this->assertEquals($usersFirst, $usersSecond, 'User count must not change on second seed run.');
        $this->assertEquals($categoriesFirst, $categoriesSecond, 'Kategori count must not change on second seed run.');
        $this->assertEquals($companiesFirst, $companiesSecond, 'Perusahaan count must not change on second seed run.');
        $this->assertEquals($vacanciesFirst, $vacanciesSecond, 'Lowongan count must not change on second seed run.');

        // Third run (Stress check)
        $this->seed(DatabaseSeeder::class);
        $this->assertEquals(1, User::count());
        $this->assertEquals(5, Kategori::count());
        $this->assertEquals(6, Perusahaan::count());
        $this->assertEquals(14, Lowongan::count());
    }

    /**
     * Test running individual seeders repeatedly does not cause duplicate key constraints.
     */
    public function test_individual_seeders_run_repeatedly_without_duplication(): void
    {
        // Seed categories 3 times
        $this->seed(KategoriSeeder::class);
        $this->seed(KategoriSeeder::class);
        $this->seed(KategoriSeeder::class);
        $this->assertEquals(5, Kategori::count());

        // Seed companies 3 times
        $this->seed(PerusahaanSeeder::class);
        $this->seed(PerusahaanSeeder::class);
        $this->seed(PerusahaanSeeder::class);
        $this->assertEquals(6, Perusahaan::count());

        // Seed vacancies 3 times
        $this->seed(LowonganSeeder::class);
        $this->seed(LowonganSeeder::class);
        $this->seed(LowonganSeeder::class);
        $this->assertEquals(14, Lowongan::count());
    }

    /**
     * Verify that all 14 seeded vacancies link to valid existing companies and categories.
     */
    public function test_all_14_seeded_vacancies_link_to_valid_companies_and_categories(): void
    {
        $this->seed(DatabaseSeeder::class);

        $lowongans = Lowongan::with(['perusahaan', 'kategori'])->get();
        $this->assertCount(14, $lowongans);

        $validCompanyIds = Perusahaan::pluck('id')->all();
        $validCategoryIds = Kategori::pluck('id')->all();

        foreach ($lowongans as $lowongan) {
            // Relational ID validation
            $this->assertNotNull($lowongan->perusahaan_id, "Lowongan [{$lowongan->judul}] has null perusahaan_id");
            $this->assertNotNull($lowongan->kategori_id, "Lowongan [{$lowongan->judul}] has null kategori_id");

            $this->assertContains(
                $lowongan->perusahaan_id,
                $validCompanyIds,
                "Lowongan [{$lowongan->judul}] references non-existent perusahaan_id [{$lowongan->perusahaan_id}]"
            );
            $this->assertContains(
                $lowongan->kategori_id,
                $validCategoryIds,
                "Lowongan [{$lowongan->judul}] references non-existent kategori_id [{$lowongan->kategori_id}]"
            );

            // Relational Eloquent instance validation
            $this->assertInstanceOf(Perusahaan::class, $lowongan->perusahaan);
            $this->assertInstanceOf(Kategori::class, $lowongan->kategori);
            $this->assertEquals($lowongan->perusahaan_id, $lowongan->perusahaan->id);
            $this->assertEquals($lowongan->kategori_id, $lowongan->kategori->id);
            $this->assertNotEmpty($lowongan->perusahaan->nama);
            $this->assertNotEmpty($lowongan->kategori->nama);

            // Data attribute integrity
            $this->assertNotEmpty($lowongan->judul);
            $this->assertContains($lowongan->tipe, ['magang', 'paruh_waktu']);
            $this->assertContains($lowongan->sistem_kerja, ['onsite', 'remote', 'hybrid']);
            $this->assertContains($lowongan->status, ['buka', 'ditutup']);
            $this->assertGreaterThanOrEqual(1, $lowongan->jam_per_minggu);
            $this->assertLessThanOrEqual(40, $lowongan->jam_per_minggu);
            $this->assertMatchesRegularExpression('/^https?:\/\//', $lowongan->link_pendaftaran);
            $this->assertNotEmpty($lowongan->kontak_pendaftaran);
            $this->assertNotEmpty($lowongan->tenggat);
        }
    }

    /**
     * Verify data distribution for business rules:
     * - Exactly 11 active vacancies for BR-01 scopeAktif
     * - Exactly 2 expired vacancies
     * - Exactly 1 manually closed vacancy
     * - All 5 categories and all 6 companies referenced
     */
    public function test_seeded_data_distribution_integrity(): void
    {
        $this->seed(DatabaseSeeder::class);

        $activeVacancies = Lowongan::aktif()->get();
        $this->assertCount(11, $activeVacancies, 'BR-01 active scope must yield exactly 11 vacancies.');

        $expiredVacancies = Lowongan::where('tenggat', '<', now()->toDateString())->get();
        $this->assertCount(2, $expiredVacancies, 'Expected exactly 2 vacancies with past deadline.');

        $closedVacancies = Lowongan::where('status', 'ditutup')->get();
        $this->assertCount(1, $closedVacancies, 'Expected exactly 1 manually closed vacancy.');

        // Verify full representation of categories and companies
        $referencedCategories = Lowongan::distinct()->pluck('kategori_id')->all();
        $this->assertCount(5, $referencedCategories, 'All 5 categories must be utilized in seeded jobs.');

        $referencedCompanies = Lowongan::distinct()->pluck('perusahaan_id')->all();
        $this->assertCount(6, $referencedCompanies, 'All 6 companies must be utilized in seeded jobs.');
    }
}
