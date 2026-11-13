<?php

namespace Tests\Feature\Admin;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LowonganCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Sambilan',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Mitra Solusi Digital',
            'email' => 'hr@mitrasolusi.id',
            'sosial_media' => 'https://mitrasolusi.id',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Pengembangan web dan aplikasi mobile',
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Frontend Blade Developer Intern',
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung, Jawa Barat',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Bertanggung jawab menyusun antarmuka Blade responsif dengan Tailwind CSS.',
            'persyaratan' => 'Mahasiswa aktif jurusan Informatika atau sistem informasi semester 3-7.',
            'link_pendaftaran' => 'https://mitrasolusi.id/karir/frontend-intern',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides);
    }

    public function test_guest_cannot_access_lowongan_management(): void
    {
        $this->get('/admin/lowongans')->assertRedirect('/login');
        $this->get('/admin/lowongans/create')->assertRedirect('/login');
        $this->post('/admin/lowongans', [])->assertRedirect('/login');
    }

    public function test_admin_can_view_lowongan_listing_with_relations(): void
    {
        $lowongan = Lowongan::create($this->validPayload());

        $response = $this->actingAs($this->admin)->get('/admin/lowongans');

        $response->assertStatus(200);
        $response->assertSee($lowongan->judul);
        $response->assertSee($this->perusahaan->nama);
        $response->assertSee($this->kategori->nama);
    }

    public function test_admin_can_view_create_lowongan_form_with_dropdowns(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/lowongans/create');

        $response->assertStatus(200);
        $response->assertSee($this->perusahaan->nama);
        $response->assertSee($this->kategori->nama);
    }

    public function test_admin_can_store_valid_lowongan(): void
    {
        $payload = $this->validPayload(['judul' => 'Fullstack Laravel Developer Intern']);

        $response = $this->actingAs($this->admin)->post('/admin/lowongans', $payload);

        $response->assertRedirect(route('admin.lowongans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lowongans', [
            'judul' => 'Fullstack Laravel Developer Intern',
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
        ]);
    }

    public function test_admin_cannot_store_lowongan_with_past_deadline_br02(): void
    {
        $payload = $this->validPayload([
            'judul' => 'Lowongan Tanggal Lalu',
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/lowongans', $payload);

        $response->assertSessionHasErrors('tenggat');
        $this->assertDatabaseMissing('lowongans', ['judul' => 'Lowongan Tanggal Lalu']);
    }

    public function test_admin_can_view_edit_lowongan_form(): void
    {
        $lowongan = Lowongan::create($this->validPayload());

        $response = $this->actingAs($this->admin)->get(route('admin.lowongans.edit', $lowongan));

        $response->assertStatus(200);
        $response->assertSee($lowongan->judul);
    }

    public function test_admin_can_update_lowongan(): void
    {
        $lowongan = Lowongan::create($this->validPayload());

        $payload = $this->validPayload([
            'judul' => 'Senior Frontend Blade Developer Intern',
            'jam_per_minggu' => 25,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.lowongans.update', $lowongan), $payload);

        $response->assertRedirect(route('admin.lowongans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lowongans', [
            'id' => $lowongan->id,
            'judul' => 'Senior Frontend Blade Developer Intern',
            'jam_per_minggu' => 25,
        ]);
    }

    public function test_admin_can_delete_lowongan(): void
    {
        $lowongan = Lowongan::create($this->validPayload());

        $response = $this->actingAs($this->admin)->delete(route('admin.lowongans.destroy', $lowongan));

        $response->assertRedirect(route('admin.lowongans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('lowongans', ['id' => $lowongan->id]);
    }

    public function test_admin_index_displays_status_and_deadline_indicators_br01(): void
    {
        $aktif = Lowongan::create($this->validPayload([
            'judul' => 'Lowongan Masih Aktif',
            'status' => 'buka',
            'tenggat' => now()->addDays(5)->toDateString(),
        ]));

        $kedaluwarsa = Lowongan::create($this->validPayload([
            'judul' => 'Lowongan Sudah Kedaluwarsa',
            'status' => 'buka',
            'tenggat' => now()->subDays(2)->toDateString(),
        ]));

        $response = $this->actingAs($this->admin)->get('/admin/lowongans');

        $response->assertStatus(200);
        $response->assertSee('Lowongan Masih Aktif');
        $response->assertSee('Lowongan Sudah Kedaluwarsa');
        $response->assertSee('Kedaluwarsa');
    }
}
