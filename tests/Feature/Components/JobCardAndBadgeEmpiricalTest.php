<?php

namespace Tests\Feature\Components;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class JobCardAndBadgeEmpiricalTest extends TestCase
{
    use RefreshDatabase;

    private Perusahaan $perusahaan;

    private Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = Perusahaan::create([
            'nama' => 'PT Solusi Digital Kampus',
            'email' => 'partner@kampus.id',
            'sosial_media' => 'https://linkedin.com/company/kampus',
        ]);

        $this->kategori = Kategori::create([
            'nama' => 'Software Engineering',
            'deskripsi' => 'Pengembangan perangkat lunak web dan mobile',
        ]);
    }

    private function createJob(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Junior Web Developer',
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => 'Bandung, Jawa Barat',
            'jam_per_minggu' => 20,
            'kompensasi' => 2500000,
            'deskripsi' => 'Membantu pengembangan modul frontend dan API backend.',
            'persyaratan' => 'Menguasai PHP dasar, HTML/CSS, dan Git.',
            'link_pendaftaran' => 'https://example.com/apply',
            'kontak_pendaftaran' => 'recruiter@example.com',
            'tenggat' => Carbon::now()->addDays(14)->toDateString(),
            'status' => 'buka',
        ], $overrides));
    }

    public function test_standard_full_vacancy_renders_all_fields_correctly(): void
    {
        $job = $this->createJob([
            'judul' => 'Backend Laravel Engineer Intern',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'kompensasi' => 2500000,
            'jam_per_minggu' => 20,
        ]);

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString('PT Solusi Digital Kampus', $rendered);
        $this->assertStringContainsString('Backend Laravel Engineer Intern', $rendered);
        $this->assertStringContainsString('Magang', $rendered);
        $this->assertStringContainsString('Remote (WFH)', $rendered);
        $this->assertStringContainsString('Bandung, Jawa Barat', $rendered);
        $this->assertStringContainsString('20 jam/minggu', $rendered);
        $this->assertStringContainsString('Rp 2.500.000 / bulan', $rendered);
        $this->assertStringContainsString('text-emerald-700', $rendered);
        $this->assertStringContainsString('Lihat Detail', $rendered);
        $this->assertStringContainsString('min-h-[44px]', $rendered);
        $this->assertStringContainsString('/lowongan/'.$job->id, $rendered);
    }

    public function test_all_work_systems_render_appropriate_labels(): void
    {
        $onsite = $this->createJob(['sistem_kerja' => 'onsite']);
        $hybrid = $this->createJob(['sistem_kerja' => 'hybrid']);
        $remote = $this->createJob(['sistem_kerja' => 'remote']);

        $renderOnsite = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $onsite]);
        $renderHybrid = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $hybrid]);
        $renderRemote = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $remote]);

        $this->assertStringContainsString('Onsite (WFO)', $renderOnsite);
        $this->assertStringContainsString('Hybrid', $renderHybrid);
        $this->assertStringContainsString('Remote (WFH)', $renderRemote);
    }

    public function test_non_compensated_vacancy_with_null_compensation_renders_cleanly(): void
    {
        $job = $this->createJob(['kompensasi' => null]);
        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString('Non-kompensasi', $rendered);
        $this->assertStringNotContainsString('Rp', $rendered);
        $this->assertStringNotContainsString('text-emerald-700', $rendered);
        $this->assertStringContainsString('text-stone-500', $rendered);
    }

    public function test_non_compensated_vacancy_with_zero_compensation_renders_cleanly(): void
    {
        $job = $this->createJob(['kompensasi' => 0]);
        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString('Non-kompensasi', $rendered);
        $this->assertStringNotContainsString('Rp', $rendered);
        $this->assertStringNotContainsString('text-emerald-700', $rendered);
        $this->assertStringContainsString('text-stone-500', $rendered);
    }

    public function test_non_compensated_vacancy_with_negative_compensation_renders_cleanly(): void
    {
        $job = new Lowongan([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Volunteer Social Media Assistant',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Online',
            'jam_per_minggu' => 10,
            'kompensasi' => -100000,
            'tenggat' => Carbon::now()->addDays(7),
        ]);
        $job->id = 999;
        $job->setRelation('perusahaan', $this->perusahaan);

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString('Non-kompensasi', $rendered);
        $this->assertStringNotContainsString('Rp -', $rendered);
        $this->assertStringNotContainsString('text-emerald-700', $rendered);
        $this->assertStringContainsString('text-stone-500', $rendered);
    }

    public function test_very_long_job_title_exceeding_100_chars_wraps_without_visual_break(): void
    {
        $longTitle = 'Programmer Magang Sistem Terdistribusi Skala Besar Berbasis Cloud Computing & Artificial Intelligence Kampus Merdeka 2026 Batch VII';
        $this->assertGreaterThan(100, mb_strlen($longTitle));

        $job = $this->createJob(['judul' => $longTitle]);
        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString(e($longTitle), $rendered);
        $this->assertStringContainsString('line-clamp-2', $rendered);
        $this->assertStringContainsString('text-balance', $rendered);
    }

    public function test_very_long_company_name_and_location(): void
    {
        $longCompanyName = 'PT Solusi Terintegrasi Mahasiswa Inovatif Bangsa & Cakrawala Teknologi Nusantara Bersama';
        $longLocation = 'Gedung Inkubasi Bisnis & Kolaborasi Riset Lantai 5, Kawasan Sains dan Teknologi Terpadu, Jatinangor, Sumedang';

        $perusahaan = Perusahaan::create([
            'nama' => $longCompanyName,
            'email' => 'partner.long@kampus.id',
        ]);

        $job = $this->createJob([
            'perusahaan_id' => $perusahaan->id,
            'lokasi' => $longLocation,
        ]);
        $job->load('perusahaan');

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString(e($longCompanyName), $rendered);
        $this->assertStringContainsString('line-clamp-1', $rendered);
        $this->assertStringContainsString(e($longLocation), $rendered);
        $this->assertStringContainsString('flex-wrap', $rendered);
    }

    public function test_missing_optional_fields_render_gracefully(): void
    {
        $job = $this->createJob([
            'link_pendaftaran' => null,
            'kontak_pendaftaran' => null,
        ]);

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertNotEmpty($rendered);
        $this->assertStringContainsString('Lihat Detail', $rendered);
        $this->assertStringContainsString('/lowongan/'.$job->id, $rendered);
    }

    public function test_null_deadline_displays_hyphen(): void
    {
        $job = new Lowongan([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Open Ended Part Time Helper',
            'tipe' => 'paruh_waktu',
            'sistem_kerja' => 'onsite',
            'lokasi' => 'Kampus',
            'jam_per_minggu' => 15,
            'kompensasi' => 1000000,
            'tenggat' => null,
        ]);
        $job->id = 888;
        $job->setRelation('perusahaan', $this->perusahaan);

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString('<time datetime=""', $rendered);
        $this->assertMatchesRegularExpression('/<time[^>]*>\s*-\s*<\/time>/', $rendered);
    }

    public function test_orphaned_or_null_perusahaan_relationship_falls_back(): void
    {
        $job = new Lowongan([
            'perusahaan_id' => 9999,
            'kategori_id' => $this->kategori->id,
            'judul' => 'Job Without Loaded Perusahaan',
            'tipe' => 'magang',
            'sistem_kerja' => 'remote',
            'lokasi' => 'Jakarta',
            'jam_per_minggu' => 20,
            'kompensasi' => 1500000,
            'tenggat' => Carbon::now()->addDays(5),
        ]);
        $job->id = 777;

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringContainsString('Perusahaan Mitra', $rendered);
    }

    public function test_xss_injection_in_job_fields_is_properly_escaped(): void
    {
        $xssTitle = 'Developer <script>alert("XSS")</script>';
        $xssLocation = 'Jakarta <img src=x onerror=alert(1)>';

        $job = new Lowongan([
            'perusahaan_id' => $this->perusahaan->id,
            'kategori_id' => $this->kategori->id,
            'judul' => $xssTitle,
            'tipe' => 'magang',
            'sistem_kerja' => 'hybrid',
            'lokasi' => $xssLocation,
            'jam_per_minggu' => 20,
            'kompensasi' => 2000000,
            'tenggat' => Carbon::now()->addDays(10),
        ]);
        $job->id = 555;
        $job->setRelation('perusahaan', $this->perusahaan);

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $rendered);
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $rendered);
        $this->assertStringNotContainsString('<img src=x onerror=alert(1)>', $rendered);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $rendered);
    }

    public function test_badge_color_variants(): void
    {
        $variants = [
            'persimmon' => ['border-orange-200', 'bg-orange-50', 'text-orange-800'],
            'emerald' => ['border-emerald-200', 'bg-emerald-50', 'text-emerald-800'],
            'obsidian' => ['border-slate-800', 'bg-slate-900', 'text-white'],
            'slate' => ['border-slate-200', 'bg-slate-100', 'text-slate-800'],
            'stone' => ['border-stone-200', 'bg-stone-100/70', 'text-stone-800'],
            'magang' => ['border-stone-200', 'bg-stone-100/70', 'text-stone-800', 'font-semibold'],
            'paruh_waktu' => ['border-orange-200', 'bg-orange-50', 'text-orange-800', 'font-semibold'],
            'onsite' => ['border-stone-200', 'bg-white', 'text-stone-700'],
            'remote' => ['border-stone-200', 'bg-white', 'text-stone-700'],
            'hybrid' => ['border-stone-200', 'bg-white', 'text-stone-700'],
            'earning' => ['border-emerald-200', 'bg-emerald-50', 'text-emerald-800'],
            'ink' => ['border-slate-800', 'bg-slate-900', 'text-white'],
        ];

        foreach ($variants as $variant => $expectedClasses) {
            $rendered = Blade::render("<x-badge variant=\"{$variant}\">Test Label</x-badge>");

            $this->assertStringContainsString('Test Label', $rendered);
            $this->assertStringStartsWith('<span', trim($rendered));
            $this->assertStringEndsWith('</span>', trim($rendered));

            foreach ($expectedClasses as $class) {
                $this->assertStringContainsString($class, $rendered, "Badge variant [{$variant}] must contain [{$class}]");
            }
        }
    }

    public function test_badge_size_variants(): void
    {
        $sizes = [
            'xs' => 'text-[11px] px-2 py-0.5',
            'sm' => 'text-xs px-2.5 py-0.5',
            'md' => 'text-sm px-3 py-1',
            'default' => 'text-xs px-2.5 py-0.5',
        ];

        foreach ($sizes as $size => $expectedClass) {
            $tag = $size === 'default' ? '<x-badge>Size Test</x-badge>' : "<x-badge size=\"{$size}\">Size Test</x-badge>";
            $rendered = Blade::render($tag);

            $this->assertStringContainsString($expectedClass, $rendered, "Badge size [{$size}] must contain [{$expectedClass}]");
        }
    }

    public function test_badge_forwards_custom_html_attributes(): void
    {
        $rendered = Blade::render('<x-badge data-testid="custom-badge" aria-label="Job Status" id="badge-root">Active</x-badge>');

        $this->assertStringContainsString('data-testid="custom-badge"', $rendered);
        $this->assertStringContainsString('aria-label="Job Status"', $rendered);
        $this->assertStringContainsString('id="badge-root"', $rendered);
        $this->assertStringContainsString('Active', $rendered);
    }

    public function test_card_lowongan_alias_renders_identical_to_job_card(): void
    {
        $job = $this->createJob();

        $jobCard = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);
        $cardLowongan = Blade::render('<x-card-lowongan :lowongan="$job" />', ['job' => $job]);

        $this->assertSame(trim($jobCard), trim($cardLowongan));
    }

    public function test_job_card_honors_named_route_lowongan_show_when_defined(): void
    {
        $job = $this->createJob();

        Route::get('/lowongan/{id}', fn ($id) => 'detail')->name('lowongan.show');
        app('router')->getRoutes()->refreshNameLookups();

        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        $expectedUrl = route('lowongan.show', $job->id);
        $this->assertStringContainsString('href="'.$expectedUrl.'"', $rendered);
    }

    public function test_job_card_markup_validity_and_accessibility(): void
    {
        $job = $this->createJob();
        $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $job]);

        // Validate top-level article tag
        $this->assertStringStartsWith('<article', trim($rendered));
        $this->assertStringEndsWith('</article>', trim($rendered));

        // Validate heading hierarchy: contains <h3> and closing </h3>
        $this->assertStringContainsString('<h3', $rendered);
        $this->assertStringContainsString('</h3>', $rendered);

        // Validate min 44px touch target on interactive link
        $this->assertStringContainsString('min-h-[44px]', $rendered);

        // Validate tactile feedback
        $this->assertStringContainsString('active:scale-[0.98]', $rendered);

        // Validate accessible focus rings
        $this->assertStringContainsString('focus-visible:ring-orange-600', $rendered);

        // Validate aria-hidden on decorative icons
        $this->assertStringContainsString('aria-hidden="true"', $rendered);

        // Validate HTML tag balance (simple sanity check on tags)
        $this->assertSame(substr_count($rendered, '<article'), substr_count($rendered, '</article>'));
        $this->assertSame(substr_count($rendered, '<h3'), substr_count($rendered, '</h3>'));
        $this->assertSame(substr_count($rendered, '<time'), substr_count($rendered, '</time>'));
    }

    public function test_all_seeded_vacancies_render_without_errors(): void
    {
        $this->seed(DatabaseSeeder::class);

        $vacancies = Lowongan::with('perusahaan', 'kategori')->get();
        $this->assertCount(14, $vacancies);

        foreach ($vacancies as $vacancy) {
            $rendered = Blade::render('<x-job-card :lowongan="$job" />', ['job' => $vacancy]);

            $this->assertNotEmpty($rendered);
            $this->assertStringContainsString(e($vacancy->judul), $rendered);
            $this->assertStringContainsString(e($vacancy->perusahaan->nama), $rendered);
            $this->assertStringContainsString('Lihat Detail', $rendered);

            if ($vacancy->kompensasi !== null && $vacancy->kompensasi > 0) {
                $this->assertStringContainsString('Rp ', $rendered);
                $this->assertStringContainsString('text-emerald-700', $rendered);
            } else {
                $this->assertStringContainsString('Non-kompensasi', $rendered);
                $this->assertStringNotContainsString('text-emerald-700', $rendered);
            }
        }
    }

    public function test_badge_with_empty_slot_or_html_entities(): void
    {
        $empty = Blade::render('<x-badge></x-badge>');
        $this->assertStringContainsString('<span', $empty);
        $this->assertStringContainsString('</span>', $empty);

        $withEntities = Blade::render('<x-badge variant="persimmon">Tag &amp; Label</x-badge>');
        $this->assertStringContainsString('Tag &amp; Label', $withEntities);
    }

    public function test_job_card_merges_custom_attributes(): void
    {
        $job = $this->createJob();
        $rendered = Blade::render('<x-job-card :lowongan="$job" id="custom-card-id" data-test="job-item" class="custom-card-class" />', ['job' => $job]);

        $this->assertStringContainsString('id="custom-card-id"', $rendered);
        $this->assertStringContainsString('data-test="job-item"', $rendered);
        $this->assertStringContainsString('custom-card-class', $rendered);
        // Base classes should still be present
        $this->assertStringContainsString('group relative bg-white', $rendered);
    }
}
