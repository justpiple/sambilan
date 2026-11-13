<?php

namespace Tests\Feature\Admin;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_view(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Dashboard');
    }

    public function test_dashboard_computes_and_displays_accurate_metrics(): void
    {
        $perusahaan1 = Perusahaan::create(['nama' => 'PT Alpha', 'email' => 'alpha@corp.id']);
        $perusahaan2 = Perusahaan::create(['nama' => 'PT Beta', 'email' => 'beta@corp.id']);
        $perusahaan3 = Perusahaan::create(['nama' => 'PT Gamma', 'email' => 'gamma@corp.id']);

        $kategori1 = Kategori::create(['nama' => 'Kategori 1', 'deskripsi' => 'Deskripsi 1']);
        $kategori2 = Kategori::create(['nama' => 'Kategori 2', 'deskripsi' => 'Deskripsi 2']);

        Lowongan::create([
            'perusahaan_id' => $perusahaan1->id,
            'kategori_id' => $kategori1->id,
            'judul' => 'Aktif 1',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(10)->toDateString(),
            'status' => 'buka',
        ]);

        Lowongan::create([
            'perusahaan_id' => $perusahaan2->id,
            'kategori_id' => $kategori2->id,
            'judul' => 'Aktif 2',
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Jakarta',
            'jam_per_minggu' => 15,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->toDateString(),
            'status' => 'buka',
        ]);

        Lowongan::create([
            'perusahaan_id' => $perusahaan3->id,
            'kategori_id' => $kategori1->id,
            'judul' => 'Kedaluwarsa',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Surabaya',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->subDay()->toDateString(),
            'status' => 'buka',
        ]);

        Lowongan::create([
            'perusahaan_id' => $perusahaan1->id,
            'kategori_id' => $kategori2->id,
            'judul' => 'Ditutup',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => '08123456789',
            'tenggat' => now()->addDays(5)->toDateString(),
            'status' => 'ditutup',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalPerusahaan', 3);
        $response->assertViewHas('totalKategori', 2);
        $response->assertViewHas('totalLowonganAktif', 2);
    }

    public function test_dashboard_renders_quick_navigation_action_links(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee(route('admin.perusahaans.index'));
        $response->assertSee(route('admin.kategoris.index'));
        $response->assertSee(route('admin.lowongans.index'));
    }
}
