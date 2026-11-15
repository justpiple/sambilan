<?php

namespace Tests\Feature\BusinessRules;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Milestone3BusinessRulesChallengeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Perusahaan $defaultPerusahaan;

    private Kategori $defaultKategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Empirical Challenger',
            'email' => 'challenger-admin@sambilan.id',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        $this->defaultPerusahaan = Perusahaan::create([
            'nama' => 'PT Sumber Solusi Nusantara',
            'email' => 'hr@sumbersolusi.id',
            'sosial_media' => 'https://sumbersolusi.id',
        ]);

        $this->defaultKategori = Kategori::create([
            'nama' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Pengembangan web dan aplikasi',
        ]);
    }

    /**
     * Create a standard job posting payload for tests.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validJobPayload(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Junior Fullstack Engineer Intern',
            'perusahaan_id' => $this->defaultPerusahaan->id,
            'kategori_id' => $this->defaultKategori->id,
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung, Jawa Barat',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Bertanggung jawab atas modul aplikasi berbasis Laravel dan Blade.',
            'persyaratan' => 'Mahasiswa aktif tingkat 2 ke atas dengan dasar PHP dan OOP yang kuat.',
            'link_pendaftaran' => 'https://sumbersolusi.id/karir/intern',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides);
    }

    /**
     * Persist a job posting into the database.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function createJob(array $attributes = []): Lowongan
    {
        return Lowongan::create($this->validJobPayload($attributes));
    }

    // =========================================================================
    // BR-03 CHALLENGES: Relational Deletion Protection
    // =========================================================================

    public function test_br03_kategori_destroy_with_active_job_blocks_deletion_with_flash_error(): void
    {
        $kategori = Kategori::create(['nama' => 'Desain Grafis', 'deskripsi' => 'UI/UX']);
        $this->createJob([
            'kategori_id' => $kategori->id,
            'status' => 'buka',
            'tenggat' => now()->addDays(7)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_kategori_destroy_with_expired_job_blocks_deletion_with_flash_error(): void
    {
        $kategori = Kategori::create(['nama' => 'Kategori Kedaluwarsa', 'deskripsi' => 'Testing']);
        $this->createJob([
            'kategori_id' => $kategori->id,
            'status' => 'buka',
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_kategori_destroy_with_closed_job_blocks_deletion_with_flash_error(): void
    {
        $kategori = Kategori::create(['nama' => 'Kategori Ditutup', 'deskripsi' => 'Testing']);
        $this->createJob([
            'kategori_id' => $kategori->id,
            'status' => 'ditutup',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_kategori_destroy_with_multiple_mixed_jobs_blocks_deletion(): void
    {
        $kategori = Kategori::create(['nama' => 'Kategori Multi Lowongan', 'deskripsi' => 'Testing']);

        $this->createJob([
            'kategori_id' => $kategori->id,
            'judul' => 'Job 1 Aktif',
            'status' => 'buka',
            'tenggat' => now()->addDays(5)->toDateString(),
        ]);
        $this->createJob([
            'kategori_id' => $kategori->id,
            'judul' => 'Job 2 Kedaluwarsa',
            'status' => 'buka',
            'tenggat' => now()->subDays(3)->toDateString(),
        ]);
        $this->createJob([
            'kategori_id' => $kategori->id,
            'judul' => 'Job 3 Ditutup',
            'status' => 'ditutup',
            'tenggat' => now()->subDays(10)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data lowongan.');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
        $this->assertEquals(3, $kategori->lowongans()->count());
    }

    public function test_br03_orphaned_kategori_destroy_succeeds_cleanly(): void
    {
        $orphanedKategori = Kategori::create([
            'nama' => 'Kategori Bebas Relasi',
            'deskripsi' => 'Tidak memiliki relasi lowongan sama sekali',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $orphanedKategori));

        $response->assertRedirect(route('admin.kategoris.index'));
        $response->assertSessionHas('success', 'Kategori berhasil dihapus.');
        $this->assertDatabaseMissing('kategoris', ['id' => $orphanedKategori->id]);
    }

    public function test_br03_kategori_can_be_deleted_after_all_associated_jobs_are_deleted(): void
    {
        $kategori = Kategori::create(['nama' => 'Kategori Transisi', 'deskripsi' => 'Testing']);
        $job1 = $this->createJob(['kategori_id' => $kategori->id]);
        $job2 = $this->createJob(['kategori_id' => $kategori->id]);

        $job1->delete();
        $job2->delete();

        $response = $this->actingAs($this->admin)->delete(route('admin.kategoris.destroy', $kategori));

        $response->assertRedirect(route('admin.kategoris.index'));
        $response->assertSessionHas('success', 'Kategori berhasil dihapus.');
        $this->assertDatabaseMissing('kategoris', ['id' => $kategori->id]);
    }

    public function test_br03_perusahaan_destroy_with_active_job_blocks_deletion_with_flash_error(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Startup Aktif', 'email' => 'hr@startupaktif.id']);
        $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'status' => 'buka',
            'tenggat' => now()->addDays(12)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_br03_perusahaan_destroy_with_expired_job_blocks_deletion_with_flash_error(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Startup Arsip', 'email' => 'hr@startuparsip.id']);
        $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'status' => 'buka',
            'tenggat' => now()->subDays(2)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_br03_perusahaan_destroy_with_closed_job_blocks_deletion_with_flash_error(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Startup Tutup', 'email' => 'hr@startuptutup.id']);
        $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'status' => 'ditutup',
            'tenggat' => now()->addDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
    }

    public function test_br03_perusahaan_destroy_with_multiple_mixed_jobs_blocks_deletion(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Korporasi Besar', 'email' => 'hr@korporasibesar.id']);

        $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'judul' => 'Posisi 1 Aktif',
            'status' => 'buka',
            'tenggat' => now()->addDays(5)->toDateString(),
        ]);
        $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'judul' => 'Posisi 2 Expired',
            'status' => 'buka',
            'tenggat' => now()->subDay()->toDateString(),
        ]);
        $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'judul' => 'Posisi 3 Closed',
            'status' => 'ditutup',
            'tenggat' => now()->subMonth()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data lowongan terkait.');
        $this->assertDatabaseHas('perusahaans', ['id' => $perusahaan->id]);
        $this->assertEquals(3, $perusahaan->lowongans()->count());
    }

    public function test_br03_orphaned_perusahaan_destroy_succeeds_cleanly(): void
    {
        $orphanedPerusahaan = Perusahaan::create([
            'nama' => 'PT Kosong Relasi',
            'email' => 'info@kosong.id',
            'sosial_media' => 'https://kosong.id',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $orphanedPerusahaan));

        $response->assertRedirect(route('admin.perusahaans.index'));
        $response->assertSessionHas('success', 'Perusahaan berhasil dihapus.');
        $this->assertDatabaseMissing('perusahaans', ['id' => $orphanedPerusahaan->id]);
    }

    public function test_br03_perusahaan_can_be_deleted_after_all_associated_jobs_are_deleted(): void
    {
        $perusahaan = Perusahaan::create(['nama' => 'PT Sementara', 'email' => 'hr@sementara.id']);
        $job = $this->createJob(['perusahaan_id' => $perusahaan->id]);

        $job->delete();

        $response = $this->actingAs($this->admin)->delete(route('admin.perusahaans.destroy', $perusahaan));

        $response->assertRedirect(route('admin.perusahaans.index'));
        $response->assertSessionHas('success', 'Perusahaan berhasil dihapus.');
        $this->assertDatabaseMissing('perusahaans', ['id' => $perusahaan->id]);
    }

    // =========================================================================
    // BR-01 CHALLENGES: Active Expiration in Dashboard
    // =========================================================================

    public function test_br01_dashboard_counts_only_active_open_unexpired_vacancies(): void
    {
        // 1. Active: open and future deadline (counted)
        $this->createJob([
            'judul' => 'Aktif Masa Depan',
            'status' => 'buka',
            'tenggat' => now()->addDays(5)->toDateString(),
        ]);

        // 2. Active: open and today deadline (counted)
        $this->createJob([
            'judul' => 'Aktif Batas Hari Ini',
            'status' => 'buka',
            'tenggat' => now()->toDateString(),
        ]);

        // 3. Inactive: open but deadline was yesterday (excluded)
        $this->createJob([
            'judul' => 'Kedaluwarsa Kemarin',
            'status' => 'buka',
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        // 4. Inactive: open but deadline was 30 days ago (excluded)
        $this->createJob([
            'judul' => 'Kedaluwarsa Bulan Lalu',
            'status' => 'buka',
            'tenggat' => now()->subDays(30)->toDateString(),
        ]);

        // 5. Inactive: closed but future deadline (excluded)
        $this->createJob([
            'judul' => 'Ditutup Masa Depan',
            'status' => 'ditutup',
            'tenggat' => now()->addDays(10)->toDateString(),
        ]);

        // 6. Inactive: closed with today deadline (excluded)
        $this->createJob([
            'judul' => 'Ditutup Hari Ini',
            'status' => 'ditutup',
            'tenggat' => now()->toDateString(),
        ]);

        // 7. Inactive: closed and expired (excluded)
        $this->createJob([
            'judul' => 'Ditutup dan Kedaluwarsa',
            'status' => 'ditutup',
            'tenggat' => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalLowonganAktif', 2);
        $response->assertViewHas('totalLowongan', 7);
        $response->assertViewHas('totalPerusahaan', 1);
        $response->assertViewHas('totalKategori', 1);
    }

    public function test_br01_dashboard_active_count_time_boundaries_across_the_day(): void
    {
        $targetDate = '2026-11-13';

        // Candidate 1: deadline on target date, status 'buka'
        $jobOnDate = $this->createJob([
            'judul' => 'Batas Tanggal Target',
            'status' => 'buka',
            'tenggat' => $targetDate,
        ]);

        // Candidate 2: deadline 1 day before target date, status 'buka'
        $jobPast = $this->createJob([
            'judul' => 'Batas Kemarin',
            'status' => 'buka',
            'tenggat' => '2026-11-12',
        ]);

        // Scenario A: Check at 00:00:00 (start of day)
        Carbon::setTestNow(Carbon::parse('2026-11-13 00:00:00'));
        $respStart = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $respStart->assertViewHas('totalLowonganAktif', 1);

        // Scenario B: Check at 12:00:00 (midday)
        Carbon::setTestNow(Carbon::parse('2026-11-13 12:00:00'));
        $respNoon = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $respNoon->assertViewHas('totalLowonganAktif', 1);

        // Scenario C: Check at 23:59:59 (last second of day)
        Carbon::setTestNow(Carbon::parse('2026-11-13 23:59:59'));
        $respNight = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $respNight->assertViewHas('totalLowonganAktif', 1);

        // Scenario D: Move to next day (2026-11-14 00:00:00) -> now jobOnDate is expired
        Carbon::setTestNow(Carbon::parse('2026-11-14 00:00:00'));
        $respNextDay = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $respNextDay->assertViewHas('totalLowonganAktif', 0);

        Carbon::setTestNow();
    }

    public function test_br01_dashboard_with_zero_records_returns_zero(): void
    {
        Lowongan::query()->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalLowonganAktif', 0);
        $response->assertViewHas('totalLowongan', 0);
    }

    // =========================================================================
    // BR-02 CHALLENGES: Deadline Validation in Admin Lowongan CRUD
    // =========================================================================

    public function test_br02_admin_store_rejects_past_deadline_yesterday_with_indonesian_error(): void
    {
        $payload = $this->validJobPayload([
            'judul' => 'Lowongan Deadline Kemarin',
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tenggat']);

        $errors = session('errors');
        $this->assertEquals(
            'Tanggal tenggat pendaftaran tidak boleh sebelum tanggal hari ini.',
            $errors->first('tenggat')
        );

        $this->assertDatabaseMissing('lowongans', ['judul' => 'Lowongan Deadline Kemarin']);
    }

    public function test_br02_admin_store_accepts_today_deadline_and_creates_record(): void
    {
        $today = now()->toDateString();
        $payload = $this->validJobPayload([
            'judul' => 'Lowongan Deadline Hari Ini',
            'tenggat' => $today,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $payload);

        $response->assertRedirect(route('admin.lowongans.index'));
        $response->assertSessionHas('success', 'Lowongan kerja berhasil ditambahkan.');

        $job = Lowongan::where('judul', 'Lowongan Deadline Hari Ini')->first();
        $this->assertNotNull($job);
        $this->assertEquals($today, $job->tenggat->toDateString());
    }

    public function test_br02_admin_store_accepts_future_deadline(): void
    {
        $futureDate = now()->addMonth()->toDateString();
        $payload = $this->validJobPayload([
            'judul' => 'Lowongan Masa Depan',
            'tenggat' => $futureDate,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $payload);

        $response->assertRedirect(route('admin.lowongans.index'));
        $response->assertSessionHas('success', 'Lowongan kerja berhasil ditambahkan.');

        $job = Lowongan::where('judul', 'Lowongan Masa Depan')->first();
        $this->assertNotNull($job);
        $this->assertEquals($futureDate, $job->tenggat->toDateString());
    }

    public function test_br02_admin_update_rejects_past_deadline_yesterday_with_indonesian_error(): void
    {
        $originalDeadline = now()->addDays(10)->toDateString();
        $job = $this->createJob(['tenggat' => $originalDeadline]);

        $updatePayload = $this->validJobPayload([
            'judul' => $job->judul,
            'tenggat' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.lowongans.update', $job), $updatePayload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tenggat']);

        $errors = session('errors');
        $this->assertEquals(
            'Tanggal tenggat pendaftaran tidak boleh sebelum tanggal hari ini.',
            $errors->first('tenggat')
        );

        // Verify the database record remains unmodified
        $this->assertEquals($originalDeadline, $job->fresh()->tenggat->toDateString());
    }

    public function test_br02_admin_update_accepts_today_deadline(): void
    {
        $job = $this->createJob(['tenggat' => now()->addDays(10)->toDateString()]);
        $today = now()->toDateString();

        $updatePayload = $this->validJobPayload([
            'judul' => 'Judul Baru Hari Ini',
            'tenggat' => $today,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.lowongans.update', $job), $updatePayload);

        $response->assertRedirect(route('admin.lowongans.index'));
        $response->assertSessionHas('success', 'Data lowongan berhasil diperbarui.');

        $this->assertEquals($today, $job->fresh()->tenggat->toDateString());
        $this->assertEquals('Judul Baru Hari Ini', $job->fresh()->judul);
    }

    public function test_br02_admin_crud_rejects_empty_or_malformed_deadline(): void
    {
        // 1. Empty deadline
        $emptyPayload = $this->validJobPayload(['tenggat' => '']);
        $respEmpty = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $emptyPayload);
        $respEmpty->assertSessionHasErrors(['tenggat']);
        $this->assertEquals(
            'Batas waktu pendaftaran (tenggat) wajib diisi.',
            session('errors')->first('tenggat')
        );

        // 2. Malformed non-date string
        $malformedPayload = $this->validJobPayload(['tenggat' => 'not-a-valid-date']);
        $respMalformed = $this->actingAs($this->admin)->post(route('admin.lowongans.store'), $malformedPayload);
        $respMalformed->assertSessionHasErrors(['tenggat']);
        $this->assertEquals(
            'Format tanggal batas waktu pendaftaran tidak valid.',
            session('errors')->first('tenggat')
        );
    }
}
