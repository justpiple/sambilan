<?php

namespace Tests\Unit;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScopeAktifTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'Mitra Kampus Utama',
            'email' => 'partner@mitrakampus.id',
            'sosial_media' => 'https://mitrakampus.id',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Desain Grafis',
            'deskripsi' => 'Desain media sosial dan UI/UX',
        ]);
    }

    private function createLowongan(string $status, string $tenggat, string $judul): Lowongan
    {
        return Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => $judul,
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Yogyakarta',
            'jam_per_minggu' => 20,
            'kompensasi' => 1500000,
            'deskripsi' => 'Uraian tugas pekerjaan magang untuk menguji scope aktif.',
            'persyaratan' => 'Persyaratan kualifikasi kandidat minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://mitrakampus.id/karir',
            'kontak_narahubung' => '08987654321',
            'tenggat' => $tenggat,
            'status' => $status,
        ]);
    }

    public function test_scope_aktif_includes_open_job_with_future_deadline(): void
    {
        $job = $this->createLowongan('buka', now()->addDays(7)->toDateString(), 'Lowongan Masa Depan');

        $result = Lowongan::aktif()->get();

        $this->assertTrue($result->contains('id', $job->id));
        $this->assertTrue($job->isAktif());
    }

    public function test_scope_aktif_includes_open_job_with_today_deadline(): void
    {
        $job = $this->createLowongan('buka', now()->toDateString(), 'Lowongan Berakhir Hari Ini');

        $result = Lowongan::aktif()->get();

        $this->assertTrue($result->contains('id', $job->id));
        $this->assertTrue($job->isAktif());
    }

    public function test_scope_aktif_excludes_open_job_with_past_deadline(): void
    {
        $job = $this->createLowongan('buka', now()->subDay()->toDateString(), 'Lowongan Kemarin');

        $result = Lowongan::aktif()->get();

        $this->assertFalse($result->contains('id', $job->id));
        $this->assertFalse($job->isAktif());
    }

    public function test_scope_aktif_excludes_closed_job_even_with_future_deadline(): void
    {
        $job = $this->createLowongan('ditutup', now()->addDays(10)->toDateString(), 'Lowongan Ditutup Manual');

        $result = Lowongan::aktif()->get();

        $this->assertFalse($result->contains('id', $job->id));
        $this->assertFalse($job->isAktif());
    }

    public function test_scope_aktif_filters_mixed_dataset_accurately(): void
    {
        // 1. Valid: Buka + Masa Depan (+5 hari) -> Harus Masuk
        $job1 = $this->createLowongan('buka', now()->addDays(5)->toDateString(), 'Valid Masa Depan');
        // 2. Valid: Buka + Hari Ini (H+0) -> Harus Masuk
        $job2 = $this->createLowongan('buka', now()->toDateString(), 'Valid Hari Ini');
        // 3. Invalid: Buka + Kedaluwarsa Kemarin (H-1) -> Harus Dieliminasi
        $job3 = $this->createLowongan('buka', now()->subDay()->toDateString(), 'Kedaluwarsa');
        // 4. Invalid: Ditutup + Masa Depan (+14 hari) -> Harus Dieliminasi
        $job4 = $this->createLowongan('ditutup', now()->addDays(14)->toDateString(), 'Ditutup');

        $aktif = Lowongan::aktif()->get();

        $this->assertCount(2, $aktif);
        $this->assertTrue($aktif->contains('id', $job1->id));
        $this->assertTrue($aktif->contains('id', $job2->id));
        $this->assertFalse($aktif->contains('id', $job3->id));
        $this->assertFalse($aktif->contains('id', $job4->id));
    }
}
