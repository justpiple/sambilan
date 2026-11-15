<?php

namespace Tests\Feature\E2E;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Sambilan',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_admin_authentication_lifecycle_and_route_guards(): void
    {
        $protectedRoutes = [
            '/admin/perusahaans',
            '/admin/kategoris',
            '/admin/lowongans',
            '/admin/lowongans/create',
            '/admin/dashboard',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
        $this->assertGuest();

        $loginResponse = $this->post('/login', [
            'email' => 'admin@sambilan.id',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);

        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        $subsequentResponse = $this->get('/admin/dashboard');
        $subsequentResponse->assertRedirect('/login');
    }

    public function test_admin_dashboard_metrics_cards_reflect_active_database_state(): void
    {
        $comp1 = Perusahaan::create(['nama' => 'PT Solusi A', 'email' => 'a@solusi.id']);
        $comp2 = Perusahaan::create(['nama' => 'PT Solusi B', 'email' => 'b@solusi.id']);
        $comp3 = Perusahaan::create(['nama' => 'PT Solusi C', 'email' => 'c@solusi.id']);

        $cat1 = Kategori::create(['nama' => 'Teknologi', 'deskripsi' => 'Bidang IT']);
        $cat2 = Kategori::create(['nama' => 'Pemasaran', 'deskripsi' => 'Bidang Marketing']);

        for ($i = 1; $i <= 4; $i++) {
            Lowongan::create([
                'perusahaan_id' => $comp1->id,
                'kategori_id' => $cat1->id,
                'judul' => sprintf('Lowongan Aktif %d', $i),
                'tipe' => 'magang',
                'sistem_kerja' => 'remote',
                'lokasi' => 'Bandung',
                'jam_per_minggu' => 20,
                'deskripsi' => 'Deskripsi pekerjaan baku minimal dua puluh karakter.',
                'persyaratan' => 'Persyaratan kualifikasi baku minimal dua puluh karakter.',
                'link_pendaftaran' => 'https://solusi.id/apply',
                'kontak_pendaftaran' => '081234567890',
                'tenggat' => now()->addDays(10)->toDateString(),
                'status' => 'buka',
            ]);
        }

        Lowongan::create([
            'perusahaan_id' => $comp2->id,
            'kategori_id' => $cat2->id,
            'judul' => 'Lowongan Kedaluwarsa Kemarin',
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Jakarta',
            'jam_per_minggu' => 15,
            'deskripsi' => 'Deskripsi pekerjaan baku minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi baku minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://solusi.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->subDay()->toDateString(),
            'status' => 'buka',
        ]);

        Lowongan::create([
            'perusahaan_id' => $comp3->id,
            'kategori_id' => $cat1->id,
            'judul' => 'Lowongan Ditutup Manual',
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Surabaya',
            'jam_per_minggu' => 18,
            'deskripsi' => 'Deskripsi pekerjaan baku minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi baku minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://solusi.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(20)->toDateString(),
            'status' => 'ditutup',
        ]);

        $dashboardResponse = $this->actingAs($this->admin)->get('/admin/dashboard');

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertViewHas('totalPerusahaan', 3);
        $dashboardResponse->assertViewHas('totalKategori', 2);
        $dashboardResponse->assertViewHas('totalLowonganAktif', 4);
    }

    public function test_admin_perusahaan_crud_lifecycle_with_br03_deletion_protection(): void
    {
        $createResponse = $this->actingAs($this->admin)->post(route('admin.perusahaans.store'), [
            'nama' => 'PT Karya Cipta Mahasiswa',
            'email' => 'halo@karyacipta.id',
            'sosial_media' => 'https://linkedin.com/company/karyacipta',
        ]);

        $createResponse->assertRedirect(route('admin.perusahaans.index'));
        $createResponse->assertSessionHas('success');

        $this->assertDatabaseHas('perusahaans', [
            'nama' => 'PT Karya Cipta Mahasiswa',
            'email' => 'halo@karyacipta.id',
        ]);

        $perusahaan = Perusahaan::where('email', 'halo@karyacipta.id')->firstOrFail();

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.perusahaans.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('PT Karya Cipta Mahasiswa');

        $editResponse = $this->actingAs($this->admin)->get(route('admin.perusahaans.edit', $perusahaan));
        $editResponse->assertStatus(200);
        $editResponse->assertSee($perusahaan->nama);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.perusahaans.update', $perusahaan), [
            'nama' => 'PT Karya Cipta Gemilang',
            'email' => 'kontak@karyacipta.id',
            'sosial_media' => 'https://instagram.com/karyacipta',
        ]);

        $updateResponse->assertRedirect(route('admin.perusahaans.index'));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('perusahaans', [
            'id' => $perusahaan->id,
            'nama' => 'PT Karya Cipta Gemilang',
        ]);

        $kategori = Kategori::create([
            'nama' => 'Kategori Pendukung',
            'deskripsi' => 'Deskripsi bidang pendukung',
        ]);

        $lowongan = Lowongan::create([
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Lowongan Terkait BR03',
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Malang',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan baku minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi baku minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(7)->toDateString(),
            'status' => 'buka',
        ]);

        $blockedDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));
        $blockedDeleteResponse->assertStatus(302);
        $blockedDeleteResponse->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);

        $lowongan->delete();

        $allowedDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));
        $allowedDeleteResponse->assertRedirect(route('admin.perusahaans.index'));
        $allowedDeleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_admin_kategori_crud_lifecycle_with_br03_deletion_protection(): void
    {
        $createResponse = $this->actingAs($this->admin)->post(route('admin.kategoris.store'), [
            'nama' => 'Cybersecurity & Pentesting',
            'deskripsi' => 'Keamanan jaringan dan audit sistem informasi',
        ]);

        $createResponse->assertRedirect(route('admin.kategoris.index'));
        $createResponse->assertSessionHas('success');

        $this->assertDatabaseHas('kategoris', [
            'nama' => 'Cybersecurity & Pentesting',
        ]);

        $kategori = Kategori::where('nama', 'Cybersecurity & Pentesting')->firstOrFail();

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.kategoris.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Cybersecurity & Pentesting');

        $editResponse = $this->actingAs($this->admin)->get(route('admin.kategoris.edit', $kategori));
        $editResponse->assertStatus(200);
        $editResponse->assertSee($kategori->nama);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.kategoris.update', $kategori), [
            'nama' => 'Keamanan Siber & Analisis Forensik',
            'deskripsi' => 'Pengujian penetrasi dan mitigasi celah keamanan piranti lunak',
        ]);

        $updateResponse->assertRedirect(route('admin.kategoris.index'));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('kategoris', [
            'id' => $kategori->id,
            'nama' => 'Keamanan Siber & Analisis Forensik',
        ]);

        $perusahaan = Perusahaan::create([
            'nama' => 'PT Mitra Keamanan',
            'email' => 'security@mitra.id',
        ]);

        $lowongan = Lowongan::create([
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Junior Pentester Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Jakarta Selatan',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi pekerjaan baku minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi baku minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://mitra.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ]);

        $blockedDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));
        $blockedDeleteResponse->assertStatus(302);
        $blockedDeleteResponse->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);

        $lowongan->delete();

        $allowedDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));
        $allowedDeleteResponse->assertRedirect(route('admin.kategoris.index'));
        $allowedDeleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('kategoris', ['id' => $kategori->id]);
    }

    public function test_admin_lowongan_crud_lifecycle_with_br01_and_br02_enforcement(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Ekosistem Riset Terbuka',
            'email' => 'riset@ekosistem.id',
        ]);

        $kategori = Kategori::create([
            'nama' => 'Riset & Sains Terapan',
            'deskripsi' => 'Eksplorasi ilmiah dan riset data kuantitatif',
        ]);

        $pastPayload = [
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Lowongan Tenggat Lalu',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'deskripsi' => 'Deskripsi pekerjaan baku minimal dua puluh karakter.',
            'persyaratan' => 'Persyaratan kualifikasi baku minimal dua puluh karakter.',
            'link_pendaftaran' => 'https://ekosistem.id/apply',
            'kontak_pendaftaran' => '081234567890',
            'tenggat' => now()->subDay()->toDateString(),
            'status' => 'buka',
        ];

        $pastResponse = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $pastPayload);
        $pastResponse->assertSessionHasErrors('tenggat');
        $this->assertDatabaseMissing('lowongans', ['judul' => 'Lowongan Tenggat Lalu']);

        $validPayload = array_merge($pastPayload, [
            'judul' => 'Research Assistant Intern',
            'tenggat' => now()->addDays(21)->toDateString(),
        ]);

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $validPayload);
        $storeResponse->assertRedirect(route('admin.lowongans.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('lowongans', [
            'judul' => 'Research Assistant Intern',
            'status' => 'buka',
        ]);

        $lowongan = Lowongan::where('judul', 'Research Assistant Intern')->firstOrFail();

        $publicCatalogResponse = $this->get('/');
        $publicCatalogResponse->assertStatus(200);
        $publicCatalogResponse->assertSee('Research Assistant Intern');

        $updatePayload = array_merge($validPayload, [
            'judul' => 'Research Assistant Intern (Closed Batch)',
            'status' => 'ditutup',
        ]);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.lowongans.update', $lowongan), $updatePayload);
        $updateResponse->assertRedirect(route('admin.lowongans.index'));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('lowongans', [
            'id' => $lowongan->id,
            'status' => 'ditutup',
            'judul' => 'Research Assistant Intern (Closed Batch)',
        ]);

        $publicAfterClosedResponse = $this->get('/');
        $publicAfterClosedResponse->assertStatus(200);
        $publicAfterClosedResponse->assertDontSee('Research Assistant Intern (Closed Batch)');

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.lowongans.destroy', $lowongan));
        $deleteResponse->assertRedirect(route('admin.lowongans.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('lowongans', ['id' => $lowongan->id]);
    }
}
