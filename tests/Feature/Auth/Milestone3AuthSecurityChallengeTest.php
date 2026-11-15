<?php

namespace Tests\Feature\Auth;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Milestone3AuthSecurityChallengeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Beni Admin',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->regularUser = User::create([
            'name' => 'Mahasiswa Biasa',
            'email' => 'student@universitas.ac.id',
            'password' => Hash::make('secret123'),
            'role' => 'user',
        ]);
    }

    public function test_unauthenticated_get_admin_dashboard_redirects_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_get_admin_kategoris_redirects_to_login(): void
    {
        $response = $this->get('/admin/kategoris');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_get_admin_kategoris_create_redirects_to_login(): void
    {
        $response = $this->get('/admin/kategoris/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_get_admin_perusahaans_redirects_to_login(): void
    {
        $response = $this->get('/admin/perusahaans');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_get_admin_perusahaans_create_redirects_to_login(): void
    {
        $response = $this->get('/admin/perusahaans/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_get_admin_lowongans_redirects_to_login(): void
    {
        $response = $this->get('/admin/lowongans');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_get_admin_lowongans_create_redirects_to_login(): void
    {
        $response = $this->get('/admin/lowongans/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_post_kategori_is_blocked_and_does_not_mutate(): void
    {
        $initialCount = Kategori::count();

        $response = $this->post('/admin/kategoris', [
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Kategori bidang IT',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertSame($initialCount, Kategori::count());
        $this->assertDatabaseMissing('kategoris', [
            'nama' => 'Teknologi Informasi',
        ]);
    }

    public function test_unauthenticated_put_kategori_is_blocked_and_does_not_mutate(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Desain Grafis',
            'deskripsi' => 'Deskripsi lama',
        ]);

        $response = $this->put("/admin/kategoris/{$kategori->id}", [
            'nama' => 'Desain Grafis Diubah',
            'deskripsi' => 'Deskripsi baru yang diretas',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $kategori->refresh();
        $this->assertSame('Desain Grafis', $kategori->nama);
        $this->assertSame('Deskripsi lama', $kategori->deskripsi);
    }

    public function test_unauthenticated_delete_kategori_is_blocked_and_does_not_mutate(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Pemasaran Digital',
            'deskripsi' => 'Bidang marketing',
        ]);

        $response = $this->delete("/admin/kategoris/{$kategori->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseHas('kategoris', [
            'id' => $kategori->id,
            'nama' => 'Pemasaran Digital',
        ]);
    }

    public function test_unauthenticated_post_perusahaan_is_blocked_and_does_not_mutate(): void
    {
        $initialCount = Perusahaan::count();

        $response = $this->post('/admin/perusahaans', [
            'nama' => 'PT Hax Inovasi',
            'email' => 'attacker@hack.com',
            'sosial_media' => 'https://hack.com',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertSame($initialCount, Perusahaan::count());
        $this->assertDatabaseMissing('perusahaans', [
            'nama' => 'PT Hax Inovasi',
        ]);
    }

    public function test_unauthenticated_put_perusahaan_is_blocked_and_does_not_mutate(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Asli Teknologi',
            'email' => 'kontak@asli.com',
            'sosial_media' => 'https://asli.com',
        ]);

        $response = $this->put("/admin/perusahaans/{$perusahaan->id}", [
            'nama' => 'PT Asli Dibajak',
            'email' => 'hacked@asli.com',
            'sosial_media' => 'https://hacked.com',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $perusahaan->refresh();
        $this->assertSame('PT Asli Teknologi', $perusahaan->nama);
        $this->assertSame('kontak@asli.com', $perusahaan->email);
    }

    public function test_unauthenticated_delete_perusahaan_is_blocked_and_does_not_mutate(): void
    {
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Solusi Aman',
            'email' => 'info@aman.com',
        ]);

        $response = $this->delete("/admin/perusahaans/{$perusahaan->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseHas('perusahaans', [
            'id' => $perusahaan->id,
            'nama' => 'PT Solusi Aman',
        ]);
    }

    public function test_unauthenticated_post_lowongan_is_blocked_and_does_not_mutate(): void
    {
        $kategori = Kategori::create(['nama' => 'IT Support']);
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Cyber Prima',
            'email' => 'hr@cyber.com',
        ]);

        $initialCount = Lowongan::count();

        $response = $this->post('/admin/lowongans', [
            'judul' => 'Unauthorized Job Posting',
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Bandung',
            'deskripsi' => 'Deskripsi pekerjaan liar',
            'persyaratan' => 'Persyaratan pekerjaan liar',
            'jam_per_minggu' => 20,
            'kompensasi' => 1500000,
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_narahubung' => 'hr@example.com',
            'tenggat' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'buka',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertSame($initialCount, Lowongan::count());
    }

    public function test_unauthenticated_put_lowongan_is_blocked_and_does_not_mutate(): void
    {
        $kategori = Kategori::create(['nama' => 'Data Science']);
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Data Solusi',
            'email' => 'data@solusi.com',
        ]);

        $lowongan = Lowongan::create([
            'judul' => 'Data Analyst Intern',
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Jakarta',
            'deskripsi' => 'Deskripsi asli',
            'persyaratan' => 'Persyaratan asli',
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'link_pendaftaran' => 'https://data.com/apply',
            'kontak_narahubung' => 'recruiter@data.com',
            'tenggat' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'buka',
        ]);

        $response = $this->put("/admin/lowongans/{$lowongan->id}", [
            'judul' => 'Defaced Title By Malicious Actor',
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Unknown',
            'deskripsi' => 'Defaced',
            'persyaratan' => 'Defaced',
            'jam_per_minggu' => 10,
            'tenggat' => now()->addDays(20)->format('Y-m-d'),
            'status' => 'buka',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $lowongan->refresh();
        $this->assertSame('Data Analyst Intern', $lowongan->judul);
    }

    public function test_unauthenticated_delete_lowongan_is_blocked_and_does_not_mutate(): void
    {
        $kategori = Kategori::create(['nama' => 'Finance']);
        $perusahaan = Perusahaan::create([
            'nama' => 'PT Finansial Unggul',
            'email' => 'finance@unggul.com',
        ]);

        $lowongan = Lowongan::create([
            'judul' => 'Finance Assistant',
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Surabaya',
            'deskripsi' => 'Deskripsi finance',
            'persyaratan' => 'Persyaratan finance',
            'jam_per_minggu' => 15,
            'kompensasi' => 1000000,
            'link_pendaftaran' => 'https://finance.com/apply',
            'kontak_narahubung' => 'hr@finance.com',
            'tenggat' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'buka',
        ]);

        $response = $this->delete("/admin/lowongans/{$lowongan->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseHas('lowongans', [
            'id' => $lowongan->id,
            'judul' => 'Finance Assistant',
        ]);
    }

    public function test_login_with_empty_fields_returns_validation_errors(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_login_with_malformed_email_returns_format_error(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'not-a-valid-email-format',
            'password' => 'secret123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $errors = session('errors');
        $this->assertStringContainsString('Format email tidak valid', $errors->first('email'));
    }

    public function test_login_with_non_existent_email_fails_and_remains_guest(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'ghost@sambilan.id',
            'password' => 'secret123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_admin_accessing_login_page_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_unauthenticated_post_logout_redirects_to_login(): void
    {
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_login_with_invalid_password_returns_validation_error_and_remains_guest(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@sambilan.id',
            'password' => 'wrong-secret-password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_non_admin_user_role_fails_authentication_and_remains_guest(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'student@universitas.ac.id',
            'password' => 'secret123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Check exact error message defined in LoginController
        $errors = session('errors');
        $this->assertNotNull($errors);
        $this->assertStringContainsString(
            'tidak memiliki hak akses administrator',
            $errors->first('email')
        );
    }

    public function test_login_with_valid_admin_role_succeeds_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@sambilan.id',
            'password' => 'secret123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_session_id_regenerates_on_admin_login_to_prevent_session_fixation(): void
    {
        // Start a session
        $this->get('/login');
        $initialSessionId = session()->getId();
        $this->assertNotEmpty($initialSessionId);

        // Perform login
        $response = $this->post('/login', [
            'email' => 'admin@sambilan.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $newSessionId = session()->getId();

        $this->assertNotEmpty($newSessionId);
        $this->assertNotSame(
            $initialSessionId,
            $newSessionId,
            'Session ID must regenerate upon successful login to prevent session fixation attacks.'
        );
    }

    public function test_post_logout_invalidates_session_and_redirects_to_home(): void
    {
        // Authenticate admin
        $this->actingAs($this->admin);
        $this->assertAuthenticatedAs($this->admin);

        // Populate session with arbitrary state to verify invalidation
        session(['custom_security_key' => 'secret_val_12345']);
        $sessionIdBeforeLogout = session()->getId();

        // Issue POST /logout
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertGuest();

        // Session should be invalidated / regenerated token
        $this->assertNull(session('custom_security_key'));
        $this->assertNotSame($sessionIdBeforeLogout, session()->getId());
    }

    public function test_unauthenticated_get_bare_admin_endpoint(): void
    {
        $response = $this->get('/admin');

        // Route /admin is not explicitly mapped; returns 404
        $response->assertStatus(404);
    }

    public function test_authenticated_non_admin_user_requesting_admin_dashboard_observation(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/dashboard');

        // Document: currently returns 200 because routes/web.php uses 'auth' without an admin role check
        $this->assertSame(200, $response->getStatusCode());
    }
}
