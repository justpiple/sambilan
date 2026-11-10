<?php

namespace App\Models;

use Database\Factories\PerusahaanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    /** @use HasFactory<PerusahaanFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perusahaans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'sosial_media',
    ];

    /**
     * Get the job postings associated with the company.
     *
     * @return HasMany<Lowongan, $this>
     */
    public function lowongans(): HasMany
    {
        return $this->hasMany(Lowongan::class);
    }

    /**
     * Get only active job postings associated with the company.
     *
     * @return HasMany<Lowongan, $this>
     */
    public function lowonganAktif(): HasMany
    {
        return $this->hasMany(Lowongan::class)->aktif();
    }
}
