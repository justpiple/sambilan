<?php

namespace Tests\Feature\BusinessRules;

use App\Models\Kategori;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Milestone3AdversarialChallengeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->regularUser = User::create([
            'name' => 'Mahasiswa Biasa',
            'email' => 'student@universitas.ac.id',
            'password' => Hash::make('secret123'),
            'role' => 'student',
        ]);

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Sumber Inovasi',
            'email' => 'hr@sumberinovasi.com',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Pengembangan perangkat lunak',
        ]);
    }

    private function validLowonganPayload(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Backend Laravel Engineer Intern',
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung, Jawa Barat',
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'deskripsi' => 'Deskripsi pekerjaan minimal dua puluh karakter wajib diisi.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter wajib diisi.',
            'link_pendaftaran' => 'https://sumberinovasi.com/karir/backend',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(10)->toDateString(),
            'status' => 'buka',
        ], $overrides);
    }

    public function test_non_admin_cannot_login_to_admin_portal(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'student@universitas.ac.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_lowongan_boundary_working_hours(): void
    {
        // 0 hours should be rejected (min: 1)
        $respZero = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['jam_per_minggu' => 0])
        );
        $respZero->assertSessionHasErrors('jam_per_minggu');

        // 41 hours should be rejected (max: 40)
        $respOver = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['jam_per_minggu' => 41])
        );
        $respOver->assertSessionHasErrors('jam_per_minggu');

        // Exactly 1 hour should be accepted
        $respMin = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['judul' => 'Lowongan Min 1 Jam', 'jam_per_minggu' => 1])
        );
        $respMin->assertRedirect(route('admin.lowongans.index'));

        // Exactly 40 hours should be accepted
        $respMax = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['judul' => 'Lowongan Max 40 Jam', 'jam_per_minggu' => 40])
        );
        $respMax->assertRedirect(route('admin.lowongans.index'));
    }

    public function test_lowongan_boundary_compensation(): void
    {
        // Negative compensation should be rejected
        $respNeg = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['kompensasi' => -500000])
        );
        $respNeg->assertSessionHasErrors('kompensasi');

        // Zero compensation should be accepted (non-kompensasi)
        $respZero = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['judul' => 'Lowongan Relawan 0', 'kompensasi' => 0])
        );
        $respZero->assertRedirect(route('admin.lowongans.index'));

        // Null compensation should be accepted
        $respNull = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['judul' => 'Lowongan Non Kompensasi Null', 'kompensasi' => null])
        );
        $respNull->assertRedirect(route('admin.lowongans.index'));
    }

    public function test_lowongan_deadline_today_is_valid_br02(): void
    {
        // Today is allowed by after_or_equal:today
        $respToday = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload([
                'judul' => 'Lowongan Deadline Hari Ini',
                'tenggat' => now()->toDateString(),
            ])
        );
        $respToday->assertRedirect(route('admin.lowongans.index'));
        $this->assertDatabaseHas('lowongans', ['judul' => 'Lowongan Deadline Hari Ini']);
    }

    public function test_lowongan_invalid_url_and_enum_types_rejected(): void
    {
        // Invalid URL
        $respUrl = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['link_pendaftaran' => 'not-a-valid-url'])
        );
        $respUrl->assertSessionHasErrors('link_pendaftaran');

        // Invalid tipe
        $respTipe = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['tipe' => 'kontrak_penuh'])
        );
        $respTipe->assertSessionHasErrors('tipe');

        // Invalid sistem_kerja
        $respSistem = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['sistem_kerja' => 'anywhere'])
        );
        $respSistem->assertSessionHasErrors('sistem_kerja');

        // Invalid status
        $respStatus = $this->actingAs($this->admin)->post(
            route('admin.lowongans.store'),
            $this->validLowonganPayload(['status' => 'draft'])
        );
        $respStatus->assertSessionHasErrors('status');
    }

    public function test_kategori_unique_validation_on_update(): void
    {
        $kategori1 = Kategori::create(['nama' => 'Desain Grafis']);
        $kategori2 = Kategori::create(['nama' => 'Pemasaran Digital']);

        // Updating with its own current name is valid (unique ignore self)
        $respSelf = $this->actingAs($this->admin)->put(
            route('admin.kategoris.update', $kategori1),
            ['nama' => 'Desain Grafis', 'deskripsi' => 'Aset visual']
        );
        $respSelf->assertRedirect(route('admin.kategoris.index'));

        // Updating with another existing kategori name is rejected
        $respDupe = $this->actingAs($this->admin)->put(
            route('admin.kategoris.update', $kategori1),
            ['nama' => 'Pemasaran Digital', 'deskripsi' => 'Duplikasi nama']
        );
        $respDupe->assertSessionHasErrors('nama');
    }

    public function test_admin_lowongans_search_sanitization_and_xss_safety(): void
    {
        $xssQuery = '<script>alert("XSS")</script>';

        $response = $this->actingAs($this->admin)->get(
            route('admin.lowongans.index', ['q' => $xssQuery])
        );

        $response->assertStatus(200);
        // Ensure literal script tags are escaped in HTML output
        $response->assertDontSee($xssQuery, false);
    }
}
