<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Software Engineering',
                'deskripsi' => 'Pengembangan web, mobile app, komputasi awan, dan rekayasa perangkat lunak modern.',
            ],
            [
                'nama' => 'Desain Grafis & UI/UX',
                'deskripsi' => 'Perancangan antarmuka pengguna, riset interaksi visual, identitas merek, dan desain produk digital.',
            ],
            [
                'nama' => 'Digital Marketing',
                'deskripsi' => 'Strategi pemasaran konten, optimasi SEO/SEM, periklanan digital, dan pengelolaan media sosial.',
            ],
            [
                'nama' => 'Data & Analytics',
                'deskripsi' => 'Analisis data bisnis, pemrosesan visualisasi statistik, pelaporan intelijen bisnis, dan pengolahan basis data.',
            ],
            [
                'nama' => 'Administrasi & Operasional',
                'deskripsi' => 'Tata kelola administrasi perkantoran, dukungan logistik operasional, arsip dokumen, dan layanan pelanggan.',
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::updateOrCreate(
                ['nama' => $kategori['nama']],
                $kategori
            );
        }
    }
}
