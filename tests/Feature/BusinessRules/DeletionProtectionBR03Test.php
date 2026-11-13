<?php

namespace Tests\Feature\BusinessRules;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeletionProtectionBR03Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test Sambilan',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    private function createLowongan(Perusahaan $perusahaan, Kategori $kategori, array $attributes = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Lowongan Uji Proteksi Relasional BR-03',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $attributes));
    }

    public function test_br03_rejects_perusahaan_deletion_when_holding_active_lowongan(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Mitra Terproteksi', 'email' => 'mitra@proteksi.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Uji', 'deskripsi' => 'Deskripsi']);

        $this->createLowongan($perusahaan, $kategori, [
            'status' => 'buka',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_br03_rejects_perusahaan_deletion_when_holding_expired_or_closed_lowongan(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Mitra Arsip', 'email' => 'arsip@mitra.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Arsip', 'deskripsi' => 'Deskripsi']);

        $this->createLowongan($perusahaan, $kategori, [
            'status' => 'ditutup',
            'tenggat' => now()->subMonth()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_br03_allows_perusahaan_deletion_after_all_associated_lowongans_are_removed(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Siap Hapus', 'email' => 'hapus@mitra.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Bebas', 'deskripsi' => 'Deskripsi']);

        $lowongan = $this->createLowongan($perusahaan, $kategori);

        $lowongan->delete();

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertRedirect(route('admin.perusahaans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_br03_rejects_kategori_deletion_when_assigned_to_active_lowongan(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Perusahaan Rekrut', 'email' => 'hr@perusahaan.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Aktif Terikat', 'deskripsi' => 'Deskripsi']);

        $this->createLowongan($perusahaan, $kategori, [
            'status' => 'buka',
            'tenggat' => now()->addDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_rejects_kategori_deletion_when_assigned_to_closed_lowongan(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Perusahaan Tua', 'email' => 'old@perusahaan.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Arsip Terikat', 'deskripsi' => 'Deskripsi']);

        $this->createLowongan($perusahaan, $kategori, [
            'status' => 'ditutup',
            'tenggat' => now()->subDays(10)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_allows_kategori_deletion_after_all_associated_lowongans_are_removed(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Vendor Sementara', 'email' => 'v@corp.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Mandiri', 'deskripsi' => 'Deskripsi']);

        $lowongan = $this->createLowongan($perusahaan, $kategori);

        $lowongan->delete();

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertRedirect(route('admin.kategoris.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_exact_error_message_wording_matches_specification(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Wording Test', 'email' => 'w@corp.id']);
        $kategori = Kategori::create(['nama' => 'Kategori Wording', 'deskripsi' => 'Deskripsi']);
        $this->createLowongan($perusahaan, $kategori);

        $respPerusahaan = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));
        $respPerusahaan->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');

        $respKategori = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));
        $respKategori->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
    }
}
