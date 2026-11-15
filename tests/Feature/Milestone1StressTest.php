<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class Milestone1StressTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Mandiri Jaya Nusantara',
            'email' => 'contact@mandirijaya.co.id',
            'sosial_media' => 'https://linkedin.com/company/mandirijaya',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Desain Grafis & Multimedia',
            'deskripsi' => 'Desain UI/UX, ilustrasi digital, dan motion graphics',
        ]);
    }

    /**
     * 1.1 Stress-test kompensasi with NULL value.
     * Verify attribute returns strict null and formatted accessor returns 'Non-kompensasi'.
     */
    public function test_kompensasi_null_returns_null_and_formatted_returns_non_kompensasi(): void
    {
        // In-memory test
        $inMemory = new Lowongan(['kompensasi' => null]);
        $this->assertNull($inMemory->kompensasi, 'Attribute kompensasi must be null');
        $this->assertSame('Non-kompensasi', $inMemory->formatted_kompensasi, 'Formatted accessor must return Non-kompensasi for null');

        // Persisted database test
        $persisted = Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Volunteer Graphic Designer',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 10,
            'kompensasi' => null,
            'deskripsi' => 'Mendesain poster dan konten visual edukasi sosial.',
            'persyaratan' => 'Menguasai Figma atau Illustrator.',
            'link_pendaftaran' => 'https://mandirijaya.co.id/apply',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(7)->toDateString(),
            'status' => 'buka',
        ]);

        // Direct DB column check
        $rawDbValue = DB::table('lowongans')->where('id', $persisted->id)->value('kompensasi');
        $this->assertNull($rawDbValue, 'Raw DB column kompensasi must be NULL');

        // Fresh model reload from database
        $fresh = $persisted->fresh();
        $this->assertNull($fresh->kompensasi, 'Fresh model kompensasi must be strictly null');
        $this->assertSame('Non-kompensasi', $fresh->formatted_kompensasi, 'Fresh model formatted_kompensasi must be Non-kompensasi');
    }

    /**
     * 1.2 Stress-test kompensasi edge case: zero (0) and negative values.
     * Both must return 'Non-kompensasi' instead of 'Rp 0 / bulan'.
     */
    public function test_kompensasi_zero_and_negative_returns_non_kompensasi(): void
    {
        $zeroJob = new Lowongan(['kompensasi' => 0]);
        $this->assertSame(0, $zeroJob->kompensasi);
        $this->assertSame('Non-kompensasi', $zeroJob->formatted_kompensasi, '0 kompensasi should yield Non-kompensasi');

        $negativeJob = new Lowongan(['kompensasi' => -500000]);
        $this->assertSame('Non-kompensasi', $negativeJob->formatted_kompensasi, 'Negative kompensasi should yield Non-kompensasi');
    }

    /**
     * 1.3 Stress-test kompensasi with positive integer (e.g. 2500000).
     * Verify formatted accessor returns 'Rp 2.500.000 / bulan' and integer cast works.
     */
    public function test_kompensasi_positive_integer_returns_formatted_currency(): void
    {
        // Test standard 2,500,000 IDR
        $job = Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'UI/UX Designer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Membangun desain antarmuka berbasis design system.',
            'persyaratan' => 'Pengalaman dengan design system dan prototype interaktif.',
            'link_pendaftaran' => 'https://mandirijaya.co.id/apply-ui',
            'kontak_narahubung' => '081234567890',
            'tenggat' => now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ]);

        $this->assertIsInt($job->kompensasi);
        $this->assertSame(2500000, $job->kompensasi);
        $this->assertSame('Rp 2.500.000 / bulan', $job->formatted_kompensasi);

        // Verify fresh model from DB retains integer cast
        $fresh = $job->fresh();
        $this->assertIsInt($fresh->kompensasi);
        $this->assertSame(2500000, $fresh->kompensasi);
        $this->assertSame('Rp 2.500.000 / bulan', $fresh->formatted_kompensasi);

        // String integer input cast verification
        $stringCast = new Lowongan(['kompensasi' => '3500000']);
        $this->assertIsInt($stringCast->kompensasi);
        $this->assertSame(3500000, $stringCast->kompensasi);
        $this->assertSame('Rp 3.500.000 / bulan', $stringCast->formatted_kompensasi);

        // Large number test (10,000,000 IDR)
        $tenMillion = new Lowongan(['kompensasi' => 10000000]);
        $this->assertSame('Rp 10.000.000 / bulan', $tenMillion->formatted_kompensasi);

        // Irregular number test (1,750,500 IDR)
        $irregular = new Lowongan(['kompensasi' => 1750500]);
        $this->assertSame('Rp 1.750.500 / bulan', $irregular->formatted_kompensasi);
    }

    /**
     * 1.4 Stress-test kontak_narahubung accessor/mutator alias for kontak_pendaftaran.
     */
    public function test_kontak_narahubung_acts_as_transparent_alias_for_kontak_pendaftaran(): void
    {
        // Case A: Set kontak_narahubung via property assignment on new model
        $modelA = new Lowongan;
        $modelA->kontak_narahubung = '081298765432';
        $this->assertSame('081298765432', $modelA->kontak_pendaftaran);
        $this->assertSame('081298765432', $modelA->kontak_narahubung);

        // Case B: Set kontak_pendaftaran directly on model, read via kontak_narahubung
        $modelB = new Lowongan;
        $modelB->kontak_pendaftaran = 'hr@ptmandiri.com';
        $this->assertSame('hr@ptmandiri.com', $modelB->kontak_narahubung);

        // Case C: Mass-assign kontak_narahubung during creation and verify DB storage
        $persisted = Lowongan::create([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Social Media Specialist Part-Time',
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Jakarta',
            'jam_per_minggu' => 15,
            'kompensasi' => 1800000,
            'deskripsi' => 'Pengelolaan akun Instagram dan TikTok perusahaan.',
            'persyaratan' => 'Kreatif, update tren medsos, terbiasa editing video.',
            'link_pendaftaran' => 'https://mandirijaya.co.id/apply-socmed',
            'kontak_narahubung' => '+628111222333',
            'tenggat' => now()->addDays(10)->toDateString(),
            'status' => 'buka',
        ]);

        // Raw database inspection: column in DB is kontak_pendaftaran
        $rawKontak = DB::table('lowongans')->where('id', $persisted->id)->value('kontak_pendaftaran');
        $this->assertSame('+628111222333', $rawKontak, 'DB column kontak_pendaftaran must hold the kontak_narahubung value');

        // Fresh model query
        $fresh = $persisted->fresh();
        $this->assertSame('+628111222333', $fresh->kontak_narahubung);
        $this->assertSame('+628111222333', $fresh->kontak_pendaftaran);

        // Case D: Update using update() with kontak_narahubung key
        $fresh->update(['kontak_narahubung' => '+628999888777']);
        $this->assertSame('+628999888777', $fresh->fresh()->kontak_narahubung);
        $this->assertSame('+628999888777', $fresh->fresh()->kontak_pendaftaran);
        $this->assertSame('+628999888777', DB::table('lowongans')->where('id', $fresh->id)->value('kontak_pendaftaran'));

        // Case E: Null value propagation
        $fresh->kontak_narahubung = null;
        $this->assertNull($fresh->kontak_pendaftaran);
        $this->assertNull($fresh->kontak_narahubung);
    }

    /**
     * 1.5 Stress-test isAdmin() on User with role 'admin' vs 'student' and edge cases.
     */
    public function test_user_is_admin_method(): void
    {
        // Admin user
        $admin = new User(['name' => 'Beni Admin', 'email' => 'admin@sambilan.id', 'role' => 'admin']);
        $this->assertTrue($admin->isAdmin(), 'User with role admin must return true for isAdmin()');

        // Student user
        $student = new User(['name' => 'Wara Student', 'email' => 'student@sambilan.id', 'role' => 'student']);
        $this->assertFalse($student->isAdmin(), 'User with role student must return false for isAdmin()');

        // Other non-admin roles
        $guest = new User(['role' => 'guest']);
        $this->assertFalse($guest->isAdmin(), 'User with role guest must return false for isAdmin()');

        $emptyRole = new User(['role' => '']);
        $this->assertFalse($emptyRole->isAdmin(), 'User with empty role must return false for isAdmin()');

        $nullRole = new User(['role' => null]);
        $this->assertFalse($nullRole->isAdmin(), 'User with null role must return false for isAdmin()');

        // DB persisted test with default role
        $persistedDefault = User::create([
            'name' => 'Default Role User',
            'email' => 'default@sambilan.id',
            'password' => bcrypt('password123'),
        ]);

        // In migration, default role is 'admin'
        $this->assertSame('admin', $persistedDefault->fresh()->role);
        $this->assertTrue($persistedDefault->fresh()->isAdmin());

        // Explicit student in DB
        $persistedStudent = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@sambilan.id',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);
        $this->assertSame('student', $persistedStudent->fresh()->role);
        $this->assertFalse($persistedStudent->fresh()->isAdmin());
    }

    /**
     * 2.1 Stress-test View rendering: welcome.blade.php renders cleanly without Vite manifest missing exception.
     */
    public function test_welcome_view_renders_cleanly_without_vite_manifest(): void
    {
        // Ensure manifest.json does NOT exist in public/build to stress-test the defensive check
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileDoesNotExist($manifestPath, 'public/build/manifest.json should not exist in test runner environment');

        // HTTP GET request to root route '/'
        $response = $this->get('/');

        // Must return 200 OK
        $response->assertOk();
        $response->assertViewIs('welcome');

        // Assert crucial branding and layout content is rendered
        $response->assertSee('Sambilan');
        $response->assertSee('Katalog Lowongan Magang &amp; Kerja Paruh Waktu Mahasiswa', false);
        $response->assertSee('Ramah Jadwal Kuliah');
        $response->assertSee('Kejelasan Kompensasi');
        $response->assertSee('Koneksi Mitra Langsung');
        $response->assertSee('Navigasi Mahasiswa');
        $response->assertSee('Masuk Pengelola');
    }

    /**
     * 2.2 Stress-test layouts.app direct Blade view rendering with custom slot content.
     */
    public function test_layouts_app_renders_standalone_with_slot_and_content(): void
    {
        // Render view directly through Laravel Blade engine
        $renderedWelcome = View::make('welcome')->render();
        $this->assertNotEmpty($renderedWelcome);
        $this->assertStringContainsString('Sambilan', $renderedWelcome);
        $this->assertStringContainsString('<!DOCTYPE html>', $renderedWelcome);

        // Render layout directly with custom slot
        $renderedLayout = View::make('layouts.app', ['slot' => '<div id="test-content">Slot Content OK</div>'])->render();
        $this->assertStringContainsString('Slot Content OK', $renderedLayout);
        $this->assertStringContainsString('Logogram Sambilan', $renderedLayout);
    }

    /**
     * 2.3 Stress-test JSON serialization and numeric cast boundaries for kompensasi and jam_per_minggu.
     */
    public function test_kompensasi_serialization_and_cast_boundaries(): void
    {
        $paid = new Lowongan([
            'kompensasi' => '2500000',
            'jam_per_minggu' => '20',
        ]);

        $unpaid = new Lowongan([
            'kompensasi' => null,
            'jam_per_minggu' => 10,
        ]);

        // Casts verification
        $this->assertIsInt($paid->kompensasi);
        $this->assertSame(2500000, $paid->kompensasi);
        $this->assertIsInt($paid->jam_per_minggu);
        $this->assertSame(20, $paid->jam_per_minggu);

        $this->assertNull($unpaid->kompensasi);
        $this->assertIsInt($unpaid->jam_per_minggu);
        $this->assertSame(10, $unpaid->jam_per_minggu);

        // Serialization checks
        $paidArray = $paid->toArray();
        $this->assertArrayHasKey('kompensasi', $paidArray);
        $this->assertSame(2500000, $paidArray['kompensasi']);

        $unpaidArray = $unpaid->toArray();
        $this->assertArrayHasKey('kompensasi', $unpaidArray);
        $this->assertNull($unpaidArray['kompensasi']);
    }

    /**
     * 2.4 Stress-test kontak alias dirty tracking and fill behavior.
     */
    public function test_kontak_narahubung_dirty_tracking_and_fill(): void
    {
        $job = new Lowongan;
        $job->fill(['kontak_narahubung' => '089912345678']);

        $this->assertTrue($job->isDirty('kontak_pendaftaran'));
        $this->assertSame('089912345678', $job->kontak_pendaftaran);
        $this->assertSame('089912345678', $job->kontak_narahubung);
    }

    /**
     * 2.5 Stress-test User::isAdmin() case sensitivity and uninitialized state.
     */
    public function test_user_is_admin_case_sensitivity_and_uninitialized(): void
    {
        // Capitalized 'Admin' or 'ADMIN' should fail strict match
        $capitalized = new User(['role' => 'Admin']);
        $this->assertFalse($capitalized->isAdmin());

        $upper = new User(['role' => 'ADMIN']);
        $this->assertFalse($upper->isAdmin());

        // Completely uninitialized model
        $blank = new User;
        $this->assertFalse($blank->isAdmin());
    }
}
