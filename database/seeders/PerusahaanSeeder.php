<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use Illuminate\Database\Seeder;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perusahaans = [
            [
                'nama' => 'PT Solusi Digital Kampus',
                'email' => 'karir@solusikampus.co.id',
                'sosial_media' => 'https://linkedin.com/company/solusi-digital-kampus',
            ],
            [
                'nama' => 'PT Inovasi Kreatif Nusantara',
                'email' => 'talent@inovasikreatif.id',
                'sosial_media' => 'https://instagram.com/inovasikreatif.id',
            ],
            [
                'nama' => 'PT Edukasi Masa Depan',
                'email' => 'rekrutmen@edukasimasadepan.org',
                'sosial_media' => 'https://linkedin.com/company/edukasi-masa-depan',
            ],
            [
                'nama' => 'PT Sintesis Media Cakrawala',
                'email' => 'people@sintesismedia.com',
                'sosial_media' => 'https://instagram.com/sintesismedia',
            ],
            [
                'nama' => 'PT Algoritma Data Solusindo',
                'email' => 'hr@algoritmasolusindo.co.id',
                'sosial_media' => 'https://linkedin.com/company/algoritma-data-solusindo',
            ],
            [
                'nama' => 'Koperasi Mahasiswa & Inkubator Usaha',
                'email' => 'inkubator@kampusmahasiswa.ac.id',
                'sosial_media' => 'https://instagram.com/inkubator_kampus',
            ],
        ];

        foreach ($perusahaans as $perusahaan) {
            Perusahaan::updateOrCreate(
                ['nama' => $perusahaan['nama']],
                $perusahaan
            );
        }
    }
}
