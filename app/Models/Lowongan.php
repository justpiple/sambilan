<?php

namespace App\Models;

use Database\Factories\LowonganFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lowongan extends Model
{
    /** @use HasFactory<LowonganFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lowongans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'perusahaan_id',
        'kategori_id',
        'judul',
        'tipe',
        'sistem_kerja',
        'lokasi',
        'jam_per_minggu',
        'kompensasi',
        'deskripsi',
        'persyaratan',
        'link_pendaftaran',
        'kontak_pendaftaran',
        'kontak_narahubung',
        'tenggat',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tenggat' => 'date',
            'jam_per_minggu' => 'integer',
            'kompensasi' => 'integer',
            'perusahaan_id' => 'integer',
            'kategori_id' => 'integer',
        ];
    }

    /**
     * Get the company providing the job posting.
     *
     * @return BelongsTo<Perusahaan, $this>
     */
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /**
     * Get the category of the job posting.
     *
     * @return BelongsTo<Kategori, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Scope a query to only include active job postings (BR-01).
     * Active criteria: status equals 'buka' and deadline is greater than or equal to today.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'buka')
            ->whereDate('tenggat', '>=', now()->toDateString());
    }

    /**
     * Determine if the job posting is active according to BR-01.
     */
    public function isAktif(): bool
    {
        return $this->status === 'buka'
            && $this->tenggat !== null
            && $this->tenggat->startOfDay()->greaterThanOrEqualTo(now()->startOfDay());
    }

    /**
     * Get human-readable compensation formatted in Indonesian Rupiah or non-compensated label.
     */
    public function getFormattedKompensasiAttribute(): string
    {
        if ($this->kompensasi === null || $this->kompensasi <= 0) {
            return 'Non-kompensasi';
        }

        return 'Rp '.number_format($this->kompensasi, 0, ',', '.').' / bulan';
    }

    /**
     * Accessor alias for kontak_narahubung to maintain compatibility across specifications.
     */
    public function getKontakNarahubungAttribute(): ?string
    {
        return $this->kontak_pendaftaran;
    }

    /**
     * Mutator alias for kontak_narahubung to maintain compatibility across specifications.
     */
    public function setKontakNarahubungAttribute(?string $value): void
    {
        $this->attributes['kontak_pendaftaran'] = $value;
    }

    /**
     * Get human-readable job type label.
     */
    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            'magang' => 'Magang',
            'paruh_waktu' => 'Paruh Waktu',
            default => ucfirst($this->tipe ?? ''),
        };
    }

    /**
     * Get human-readable work system label.
     */
    public function getSistemKerjaLabelAttribute(): string
    {
        return match ($this->sistem_kerja) {
            'onsite' => 'Onsite (WFO)',
            'remote' => 'Remote (WFH)',
            'hybrid' => 'Hybrid',
            default => ucfirst($this->sistem_kerja ?? ''),
        };
    }
}
