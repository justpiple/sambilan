<?php

namespace Tests\Feature\Admin;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KategoriCrudTest extends TestCase
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

    public function test_guest_cannot_access_kategori_management(): void
    {
        $this->get('/admin/kategoris')->assertRedirect('/login');
        $this->get('/admin/kategoris/create')->assertRedirect('/login');
        $this->post('/admin/kategoris', ['nama' => 'Test'])->assertRedirect('/login');
    }

    public function test_admin_can_view_kategori_index_listing(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Pengembangan perangkat lunak',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/kategoris');

        $response->assertStatus(200);
        $response->assertSee('Teknologi Informasi');
    }

    public function test_admin_can_view_create_kategori_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/kategoris/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah Kategori');
    }

    public function test_admin_can_store_new_kategori(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/kategoris', [
            'nama' => 'Desain Grafis',
            'deskripsi' => 'Perancangan aset visual dan UI/UX',
        ]);

        $response->assertRedirect(route('admin.kategoris.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kategoris', [
            'nama' => 'Desain Grafis',
            'deskripsi' => 'Perancangan aset visual dan UI/UX',
        ]);
    }

    public function test_kategori_store_validation_requires_nama(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/kategoris', [
            'nama' => '',
            'deskripsi' => 'Deskripsi tanpa nama',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_admin_can_view_edit_kategori_form(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Pemasaran Digital',
            'deskripsi' => 'Pemasaran produk online',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kategoris.edit', $kategori));

        $response->assertStatus(200);
        $response->assertSee('Pemasaran Digital');
    }

    public function test_admin_can_update_kategori(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Pemasaran Lama',
            'deskripsi' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kategoris.update', $kategori), [
            'nama' => 'Pemasaran Baru',
            'deskripsi' => 'Deskripsi diperbarui',
        ]);

        $response->assertRedirect(route('admin.kategoris.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kategoris', [
            'id' => $kategori->id,
            'nama' => 'Pemasaran Baru',
        ]);
    }

    public function test_admin_can_delete_unassociated_kategori(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Kategori Kosong',
            'deskripsi' => 'Belum ada lowongan terkait',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertRedirect(route('admin.kategoris.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kategoris', [
            'id' => $kategori->id,
        ]);
    }

    public function test_admin_cannot_delete_kategori_with_existing_lowongan_br03(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Kategori Terkait',
            'deskripsi' => 'Memiliki lowongan aktif',
        ]);

        $perusahaan = Perusahaan::create([
            'nama' => 'PT Mitra Utama',
            'email' => 'mitra@utama.id',
        ]);

        Lowongan::create([
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Lowongan Terkait',
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(7)->toDateString(),
            'status' => 'buka',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
    }
}
