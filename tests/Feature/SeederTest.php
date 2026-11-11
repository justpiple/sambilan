<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\KategoriSeeder;
use Database\Seeders\PerusahaanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_all_tables_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@sambilan.id',
            'role' => 'admin',
        ]);

        $admin = User::where('email', 'admin@sambilan.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('password', $admin->password));

        $this->assertGreaterThanOrEqual(5, Kategori::count());
        $this->assertGreaterThanOrEqual(6, Perusahaan::count());
        $this->assertGreaterThanOrEqual(14, Lowongan::count());
    }

    public function test_kategori_seeder_populates_expected_domains(): void
    {
        $this->seed(KategoriSeeder::class);

        $expectedCategories = [
            'Software Engineering',
            'Desain Grafis & UI/UX',
            'Digital Marketing',
            'Data & Analytics',
            'Administrasi & Operasional',
        ];

        foreach ($expectedCategories as $categoryName) {
            $this->assertDatabaseHas('kategoris', [
                'nama' => $categoryName,
            ]);
        }

        $allCategories = Kategori::all();
        $this->assertCount(5, $allCategories);
        foreach ($allCategories as $category) {
            $this->assertNotEmpty($category->deskripsi);
        }
    }

    public function test_perusahaan_seeder_populates_valid_partner_companies(): void
    {
        $this->seed(PerusahaanSeeder::class);

        $expectedCompanies = [
            'PT Solusi Digital Kampus',
            'PT Inovasi Kreatif Nusantara',
            'PT Edukasi Masa Depan',
            'PT Sintesis Media Cakrawala',
            'PT Algoritma Data Solusindo',
            'Koperasi Mahasiswa & Inkubator Usaha',
        ];

        foreach ($expectedCompanies as $companyName) {
            $this->assertDatabaseHas('perusahaans', [
                'nama' => $companyName,
            ]);
        }

        $allCompanies = Perusahaan::all();
        $this->assertCount(6, $allCompanies);
        foreach ($allCompanies as $company) {
            $this->assertNotEmpty($company->email);
            $this->assertNotEmpty($company->sosial_media);
        }
    }

    public function test_lowongan_seeder_creates_valid_relational_structure(): void
    {
        $this->seed(DatabaseSeeder::class);

        $lowongans = Lowongan::with(['perusahaan', 'kategori'])->get();
        $this->assertGreaterThanOrEqual(14, $lowongans->count());

        foreach ($lowongans as $lowongan) {
            $this->assertNotNull($lowongan->perusahaan);
            $this->assertNotNull($lowongan->kategori);
            $this->assertNotEmpty($lowongan->judul);
            $this->assertNotEmpty($lowongan->lokasi);
            $this->assertGreaterThan(0, $lowongan->jam_per_minggu);
        }
    }

    public function test_lowongan_seeder_contains_both_active_and_expired_data_for_br01(): void
    {
        $this->seed(DatabaseSeeder::class);

        $totalCount = Lowongan::count();
        $activeCount = Lowongan::aktif()->count();
        $inactiveCount = Lowongan::where('status', 'ditutup')
            ->orWhere('tenggat', '<', now()->toDateString())
            ->count();

        $this->assertEquals(14, $totalCount);
        $this->assertEquals(11, $activeCount);
        $this->assertEquals(3, $inactiveCount);
        $this->assertEquals($totalCount, $activeCount + $inactiveCount);
    }

    public function test_lowongan_seeder_covers_diverse_filters_and_compensation(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Lowongan::where('tipe', 'magang')->exists());
        $this->assertTrue(Lowongan::where('tipe', 'paruh_waktu')->exists());

        $this->assertTrue(Lowongan::where('sistem_kerja', 'onsite')->exists());
        $this->assertTrue(Lowongan::where('sistem_kerja', 'remote')->exists());
        $this->assertTrue(Lowongan::where('sistem_kerja', 'hybrid')->exists());

        $this->assertTrue(Lowongan::whereNotNull('kompensasi')->where('kompensasi', '>', 0)->exists());
        $this->assertTrue(Lowongan::whereNull('kompensasi')->orWhere('kompensasi', 0)->exists());
    }

    public function test_seeder_execution_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $firstCount = Lowongan::count();

        $this->seed(DatabaseSeeder::class);
        $secondCount = Lowongan::count();

        $this->assertEquals($firstCount, $secondCount);
    }
}
