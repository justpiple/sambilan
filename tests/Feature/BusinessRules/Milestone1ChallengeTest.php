<?php

namespace Tests\Feature\BusinessRules;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Milestone1ChallengeTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Challenge Inovasi',
            'email' => 'hr@challenge.id',
            'sosial_media' => 'https://challenge.id',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Quality Assurance',
            'deskripsi' => 'Pengujian perangkat lunak dan keandalan sistem',
        ]);
    }

    private function createJob(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Software QA Engineer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'deskripsi' => 'Menguji reliabilitas perangkat lunak dan batasan sistem.',
            'persyaratan' => 'Menguasai PHPUnit dan pengujian boundary condition.',
            'link_pendaftaran' => 'https://challenge.id/apply',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->toDateString(),
            'status' => 'buka',
        ], $overrides));
    }

    /**
     * BR-01 Boundary Test:
     * Job with deadline = today at 00:00:00 vs now vs 23:59:59 must be included if status = 'buka'.
     */
    public function test_br01_scope_aktif_includes_today_at_various_times_of_day(): void
    {
        // Case A: System time at 00:00:00 (start of day)
        Carbon::setTestNow(Carbon::parse('2026-11-10 00:00:00'));

        $jobA = $this->createJob([
            'judul' => 'Job Today Start of Day',
            'tenggat' => '2026-11-10 00:00:00',
            'status' => 'buka',
        ]);

        $this->assertTrue(Lowongan::aktif()->where('id', $jobA->id)->exists(), 'Job with deadline today 00:00:00 at time 00:00:00 should be active');
        $this->assertTrue($jobA->fresh()->isAktif(), 'isAktif() should return true for today 00:00:00 at 00:00:00');

        // Case B: System time in the afternoon (14:30:15)
        Carbon::setTestNow(Carbon::parse('2026-11-10 14:30:15'));

        $jobB = $this->createJob([
            'judul' => 'Job Today Midday',
            'tenggat' => '2026-11-10',
            'status' => 'buka',
        ]);

        $this->assertTrue(Lowongan::aktif()->where('id', $jobB->id)->exists(), 'Job with deadline today at midday should be active');
        $this->assertTrue($jobB->fresh()->isAktif(), 'isAktif() should return true for today at midday');

        // Case C: System time at 23:59:59 (last second of today)
        Carbon::setTestNow(Carbon::parse('2026-11-10 23:59:59'));

        $jobC = $this->createJob([
            'judul' => 'Job Today End of Day',
            'tenggat' => '2026-11-10 23:59:59',
            'status' => 'buka',
        ]);

        $this->assertTrue(Lowongan::aktif()->where('id', $jobC->id)->exists(), 'Job with deadline today at 23:59:59 should be active');
        $this->assertTrue($jobC->fresh()->isAktif(), 'isAktif() should return true at 23:59:59');

        Carbon::setTestNow(); // Reset time
    }

    /**
     * BR-01 Boundary Stress Test:
     * Precision test at the exact second of midnight transition (2026-11-09 23:59:59 -> 2026-11-10 00:00:00).
     */
    public function test_br01_scope_aktif_exact_midnight_transition(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 00:00:00'));

        // Yesterday deadline (2026-11-09)
        $jobYesterday = $this->createJob([
            'judul' => 'Job Expired Yesterday',
            'tenggat' => '2026-11-09',
            'status' => 'buka',
        ]);

        // Today deadline (2026-11-10)
        $jobToday = $this->createJob([
            'judul' => 'Job Today Midnight',
            'tenggat' => '2026-11-10',
            'status' => 'buka',
        ]);

        $activeIds = Lowongan::aktif()->pluck('id')->all();

        $this->assertNotContains($jobYesterday->id, $activeIds, 'Job expiring yesterday must NOT be active at midnight today');
        $this->assertContains($jobToday->id, $activeIds, 'Job expiring today MUST be active at midnight today');

        $this->assertFalse($jobYesterday->fresh()->isAktif());
        $this->assertTrue($jobToday->fresh()->isAktif());

        Carbon::setTestNow();
    }

    /**
     * BR-01 Edge Case Test:
     * Verify isAktif() when tenggat is null on unpersisted model instance.
     */
    public function test_br01_is_aktif_handles_null_deadline_safely(): void
    {
        $job = new Lowongan([
            'status' => 'buka',
            'tenggat' => null,
        ]);

        $this->assertFalse($job->isAktif(), 'isAktif() must safely return false when deadline is null');
    }

    /**
     * BR-01 Boundary Test:
     * Job with deadline = yesterday must be excluded.
     */
    public function test_br01_scope_aktif_excludes_deadline_yesterday(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 10:00:00'));

        $yesterdayJob = $this->createJob([
            'judul' => 'Job Expired Yesterday',
            'tenggat' => '2026-11-09',
            'status' => 'buka',
        ]);

        $this->assertFalse(Lowongan::aktif()->where('id', $yesterdayJob->id)->exists(), 'Job with deadline yesterday must be excluded from scopeAktif');
        $this->assertFalse($yesterdayJob->fresh()->isAktif(), 'isAktif() must return false for yesterday deadline');

        Carbon::setTestNow();
    }

    /**
     * BR-01 Boundary Test:
     * Job with deadline = tomorrow must be included.
     */
    public function test_br01_scope_aktif_includes_deadline_tomorrow(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 10:00:00'));

        $tomorrowJob = $this->createJob([
            'judul' => 'Job Expiring Tomorrow',
            'tenggat' => '2026-11-11',
            'status' => 'buka',
        ]);

        $this->assertTrue(Lowongan::aktif()->where('id', $tomorrowJob->id)->exists(), 'Job with deadline tomorrow must be included in scopeAktif');
        $this->assertTrue($tomorrowJob->fresh()->isAktif(), 'isAktif() must return true for tomorrow deadline');

        Carbon::setTestNow();
    }

    /**
     * BR-01 Boundary Test:
     * Job with status = 'ditutup' must be excluded regardless of future deadline.
     */
    public function test_br01_scope_aktif_excludes_closed_job_regardless_of_future_deadline(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 10:00:00'));

        // Closed job with far-future deadline (+30 days)
        $closedJobFuture = $this->createJob([
            'judul' => 'Job Closed Manual Future',
            'tenggat' => '2026-12-10',
            'status' => 'ditutup',
        ]);

        // Closed job with today's deadline
        $closedJobToday = $this->createJob([
            'judul' => 'Job Closed Manual Today',
            'tenggat' => '2026-11-10',
            'status' => 'ditutup',
        ]);

        $this->assertFalse(Lowongan::aktif()->where('id', $closedJobFuture->id)->exists(), 'Closed job with future deadline must be excluded');
        $this->assertFalse($closedJobFuture->fresh()->isAktif(), 'isAktif() must return false for closed job with future deadline');

        $this->assertFalse(Lowongan::aktif()->where('id', $closedJobToday->id)->exists(), 'Closed job with today deadline must be excluded');
        $this->assertFalse($closedJobToday->fresh()->isAktif(), 'isAktif() must return false for closed job with today deadline');

        Carbon::setTestNow();
    }

    /**
     * BR-03 Relational Integrity at Database Level:
     * Attempting to delete a Perusahaan referenced by a Lowongan must throw FOREIGN KEY constraint failed.
     */
    public function test_br03_database_prevents_perusahaan_deletion_when_lowongan_exists(): void
    {
        $lowongan = $this->createJob();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/FOREIGN KEY constraint failed/i');

        // Attempting to delete the parent Perusahaan row directly at the database level
        $this->perusahaan->delete();
    }

    /**
     * BR-03 Relational Integrity at Database Level:
     * Raw DB deletion of Perusahaan must also fail with FOREIGN KEY constraint failed.
     */
    public function test_br03_raw_sql_prevents_perusahaan_deletion_when_lowongan_exists(): void
    {
        $lowongan = $this->createJob();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/FOREIGN KEY constraint failed/i');

        DB::table('perusahaans')->where('id', $this->perusahaan->id)->delete();
    }

    /**
     * BR-03 Relational Integrity at Database Level:
     * Attempting to delete a Kategori referenced by a Lowongan must throw FOREIGN KEY constraint failed.
     */
    public function test_br03_database_prevents_kategori_deletion_when_lowongan_exists(): void
    {
        $lowongan = $this->createJob();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/FOREIGN KEY constraint failed/i');

        // Attempting to delete the parent Kategori row directly at the database level
        $this->kategori->delete();
    }

    /**
     * BR-03 Relational Integrity at Database Level:
     * Raw DB deletion of Kategori must also fail with FOREIGN KEY constraint failed.
     */
    public function test_br03_raw_sql_prevents_kategori_deletion_when_lowongan_exists(): void
    {
        $lowongan = $this->createJob();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/FOREIGN KEY constraint failed/i');

        DB::table('kategoris')->where('id', $this->kategori->id)->delete();
    }

    /**
     * BR-03 Positive Integrity Test:
     * After deleting the referencing Lowongan, Perusahaan and Kategori can be safely deleted.
     */
    public function test_br03_allows_parent_deletion_after_referencing_lowongans_are_deleted(): void
    {
        $lowongan = $this->createJob();

        // Delete child row
        $lowongan->delete();

        // Deleting parent rows now must succeed without exception
        $perusahaanDeleted = $this->perusahaan->delete();
        $kategoriDeleted = $this->kategori->delete();

        $this->assertTrue($perusahaanDeleted);
        $this->assertTrue($kategoriDeleted);
        $this->assertDatabaseMissing('perusahaans', ['id' => $this->perusahaan->id]);
        $this->assertDatabaseMissing('kategoris', ['id' => $this->kategori->id]);
    }

    /**
     * BR-03 Foreign Key Stress Test:
     * Inserting a Lowongan with a non-existent Perusahaan or Kategori ID must fail with FOREIGN KEY constraint failed.
     */
    public function test_br03_cannot_create_lowongan_with_invalid_foreign_keys(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/FOREIGN KEY constraint failed/i');

        Lowongan::create([
            'perusahaan_id' => 999999,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Orphaned Job',
            'tipe' => 'magang',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'deskripsi' => 'Deskripsi minimal dua puluh karakter untuk pengujian orphan.',
            'persyaratan' => 'Persyaratan kualifikasi minimal dua puluh karakter.',
            'tenggat' => now()->toDateString(),
            'status' => 'buka',
        ]);
    }
}
