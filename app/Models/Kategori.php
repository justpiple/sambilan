<?php

namespace App\Models;

use Database\Factories\KategoriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    /** @use HasFactory<KategoriFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategoris';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Get the job postings associated with this category.
     *
     * @return HasMany<Lowongan, $this>
     */
    public function lowongans(): HasMany
    {
        return $this->hasMany(Lowongan::class);
    }

    /**
     * Get only active job postings in this category.
     *
     * @return HasMany<Lowongan, $this>
     */
    public function lowonganAktif(): HasMany
    {
        return $this->hasMany(Lowongan::class)->aktif();
    }
}
