<?php

namespace Tests\Feature\Admin;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerusahaanCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Sambilan',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_guest_cannot_access_perusahaan_management(): void
    {
        $this->get('/admin/perusahaans')->assertRedirect('/login');
        $this->get('/admin/perusahaans/create')->assertRedirect('/login');
        $this->post('/admin/perusahaans', ['nama' => 'PT Test'])->assertRedirect('/login');
    }

    public function test_admin_can_view_perusahaan_listing(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Inovasi Digital',
            'email' => 'karir@inovasi.id',
            'sosial_media' => 'https://linkedin.com/company/inovasi',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/perusahaans');

        $response->assertStatus(200);
        $response->assertSee('PT Inovasi Digital');
    }

    public function test_admin_can_view_create_perusahaan_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/perusahaans/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah Perusahaan');
    }

    public function test_admin_can_store_new_perusahaan(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/perusahaans', [
            'nama' => 'PT Solusi Mahasiswa Mandiri',
            'email' => 'hr@solusimahasiswa.id',
            'sosial_media' => 'https://instagram.com/solusimahasiswa',
        ]);

        $response->assertRedirect(route('admin.perusahaans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('perusahaans', [
            'nama' => 'PT Solusi Mahasiswa Mandiri',
            'email' => 'hr@solusimahasiswa.id',
        ]);
    }

    public function test_perusahaan_store_validation_requires_nama_and_valid_email(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/perusahaans', [
            'nama' => '',
            'email' => 'bukan-email-valid',
        ]);

        $response->assertSessionHasErrors(['nama', 'email']);
    }

    public function test_admin_can_view_edit_perusahaan_form(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Mitra Karir',
            'email' => 'recruitment@mitrakarir.com',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.perusahaans.edit', $perusahaan));

        $response->assertStatus(200);
        $response->assertSee('PT Mitra Karir');
    }

    public function test_admin_can_update_perusahaan(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Mitra Karir',
            'email' => 'old@mitrakarir.com',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.perusahaans.update', $perusahaan), [
            'nama' => 'PT Mitra Karir Gemilang',
            'email' => 'new@mitrakarir.com',
            'sosial_media' => 'https://mitrakarir.com',
        ]);

        $response->assertRedirect(route('admin.perusahaans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('perusahaans', [
            'id' => $perusahaan->id,
            'nama' => 'PT Mitra Karir Gemilang',
            'email' => 'new@mitrakarir.com',
        ]);
    }

    public function test_admin_can_delete_unassociated_perusahaan(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Mitra Kosong',
            'email' => 'kosong@mitra.com',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertRedirect(route('admin.perusahaans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_admin_cannot_delete_perusahaan_with_existing_lowongan_br03(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Berelasi Aktif',
            'email' => 'contact@berelasi.id',
        ]);

        $kategori = Kategori::create([
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Deskripsi bidang',
        ]);

        Lowongan::create([
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Lowongan Terkait Perusahaan',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(7)->toDateString(),
            'status' => 'buka',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
    }
}
