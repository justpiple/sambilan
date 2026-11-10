<?php

namespace Tests\Unit;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowonganModelTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Teknologi Mahasiswa Mandiri',
            'email' => 'hr@tekno-mahasiswa.com',
            'sosial_media' => 'https://linkedin.com/company/tekno-mahasiswa',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Pengembangan perangkat lunak dan infrastruktur TI',
        ]);
    }

    public function test_lowongan_belongs_to_perusahaan(): void
    {
        $lowongan = Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Frontend Blade Developer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Membangun antarmuka Blade responsif dengan Tailwind CSS.',
            'persyaratan' => 'Menguasai HTML, CSS, Tailwind, dan konsep Blade Laravel.',
            'link_pendaftaran' => 'https://tekno-mahasiswa.com/apply',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ]);

        $this->assertInstanceOf(BelongsTo::class, $lowongan->perusahaan());
        $this->assertInstanceOf(Perusahaan::class, $lowongan->perusahaan);
        $this->assertEquals($this->perusahaan->id, $lowongan->perusahaan->id);
        $this->assertEquals('PT Teknologi Mahasiswa Mandiri', $lowongan->perusahaan->nama);
    }

    public function test_lowongan_belongs_to_kategori(): void
    {
        $lowongan = Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Backend Laravel Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Jakarta',
            'jam_per_minggu' => 15,
            'kompensasi' => 3000000,
            'deskripsi' => 'Mengembangkan REST API dan logika bisnis Eloquent.',
            'persyaratan' => 'Memahami arsitektur MVC Laravel dan relational database.',
            'link_pendaftaran' => 'https://tekno-mahasiswa.com/apply-backend',
            'kontak_narahubung' => 'hr@tekno-mahasiswa.com',
            'tenggat' => now()->addDays(20)->toDateString(),
            'status' => 'buka',
        ]);

        $this->assertInstanceOf(BelongsTo::class, $lowongan->kategori());
        $this->assertInstanceOf(Kategori::class, $lowongan->kategori);
        $this->assertEquals($this->kategori->id, $lowongan->kategori->id);
        $this->assertEquals('Teknologi Informasi', $lowongan->kategori->nama);
    }

    public function test_perusahaan_has_many_lowongans(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->perusahaan->lowongans());

        Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Posisi 1',
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => null,
            'deskripsi' => 'Deskripsi pekerjaan posisi 1 minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi posisi 1 minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(5)->toDateString(),
            'status' => 'buka',
        ]);

        Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Posisi 2',
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 10,
            'kompensasi' => 1500000,
            'deskripsi' => 'Deskripsi pekerjaan posisi 2 minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi posisi 2 minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(10)->toDateString(),
            'status' => 'buka',
        ]);

        $this->assertCount(2, $this->perusahaan->fresh()->lowongans);
    }

    public function test_kategori_has_many_lowongans(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->kategori->lowongans());

        Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Posisi TI 1',
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => null,
            'deskripsi' => 'Deskripsi pekerjaan bidang TI minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi bidang TI minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(5)->toDateString(),
            'status' => 'buka',
        ]);

        $this->assertCount(1, $this->kategori->fresh()->lowongans);
    }

    public function test_lowongan_casts_attributes_properly(): void
    {
        $lowongan = new Lowongan([
            'tenggat' => '2026-11-20',
            'jam_per_minggu' => '20',
            'kompensasi' => '2500000',
        ]);

        $this->assertEquals('2026-11-20', $lowongan->tenggat->format('Y-m-d'));
        $this->assertIsInt($lowongan->jam_per_minggu);
        $this->assertIsInt($lowongan->kompensasi);
    }

    public function test_formatted_kompensasi_accessor(): void
    {
        $paid = new Lowongan(['kompensasi' => 1500000]);
        $unpaid = new Lowongan(['kompensasi' => null]);
        $zero = new Lowongan(['kompensasi' => 0]);

        $this->assertEquals('Rp 1.500.000 / bulan', $paid->formatted_kompensasi);
        $this->assertEquals('Non-kompensasi', $unpaid->formatted_kompensasi);
        $this->assertEquals('Non-kompensasi', $zero->formatted_kompensasi);
    }

    public function test_label_accessors(): void
    {
        $intern = new Lowongan(['tipe' => 'magang', 'sistem_kerja' => 'hybrid']);
        $partTime = new Lowongan(['tipe' => 'paruh_waktu', 'sistem_kerja' => 'remote']);
        $onsite = new Lowongan(['sistem_kerja' => 'onsite']);

        $this->assertEquals('Magang', $intern->tipe_label);
        $this->assertEquals('Hybrid', $intern->sistem_kerja_label);
        $this->assertEquals('Paruh Waktu', $partTime->tipe_label);
        $this->assertEquals('Remote (WFH)', $partTime->sistem_kerja_label);
        $this->assertEquals('Onsite (WFO)', $onsite->sistem_kerja_label);
    }
}
