<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LowonganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('kontak_narahubung') && ! $this->filled('kontak_pendaftaran')) {
            $this->merge([
                'kontak_pendaftaran' => $this->input('kontak_narahubung'),
            ]);
        } elseif ($this->filled('kontak_pendaftaran') && ! $this->filled('kontak_narahubung')) {
            $this->merge([
                'kontak_narahubung' => $this->input('kontak_pendaftaran'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'perusahaan_id' => ['required', 'integer', 'exists:perusahaans,id'],
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'tipe' => ['required', 'string', Rule::in(['magang', 'paruh_waktu'])],
            'sistem_kerja' => ['required', 'string', Rule::in(['onsite', 'remote', 'hybrid'])],
            'lokasi' => ['required', 'string', 'max:150'],
            'jam_per_minggu' => ['required', 'integer', 'min:1', 'max:40'],
            'kompensasi' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['required', 'string', 'min:20'],
            'persyaratan' => ['required', 'string', 'min:20'],
            'link_pendaftaran' => ['required', 'url', 'max:255'],
            'kontak_pendaftaran' => ['required_without:kontak_narahubung', 'nullable', 'string', 'max:100'],
            'kontak_narahubung' => ['required_without:kontak_pendaftaran', 'nullable', 'string', 'max:255'],
            'tenggat' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', 'string', Rule::in(['buka', 'ditutup'])],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul lowongan wajib diisi.',
            'judul.string' => 'Judul lowongan harus berupa teks.',
            'judul.max' => 'Judul lowongan tidak boleh lebih dari :max karakter.',

            'perusahaan_id.required' => 'Perusahaan wajib dipilih.',
            'perusahaan_id.integer' => 'Pilihan perusahaan tidak valid.',
            'perusahaan_id.exists' => 'Perusahaan yang dipilih tidak terdaftar di sistem.',

            'kategori_id.required' => 'Kategori lowongan wajib dipilih.',
            'kategori_id.integer' => 'Pilihan kategori tidak valid.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak terdaftar di sistem.',

            'tipe.required' => 'Tipe pekerjaan wajib dipilih.',
            'tipe.in' => 'Tipe pekerjaan harus berupa magang atau paruh waktu.',

            'sistem_kerja.required' => 'Sistem kerja wajib dipilih.',
            'sistem_kerja.in' => 'Sistem kerja harus berupa onsite, remote, atau hybrid.',

            'lokasi.required' => 'Lokasi pekerjaan wajib diisi.',
            'lokasi.string' => 'Lokasi pekerjaan harus berupa teks.',
            'lokasi.max' => 'Lokasi pekerjaan tidak boleh lebih dari :max karakter.',

            'jam_per_minggu.required' => 'Komitmen jam kerja per minggu wajib diisi.',
            'jam_per_minggu.integer' => 'Komitmen jam kerja per minggu harus berupa angka bulat.',
            'jam_per_minggu.min' => 'Komitmen jam kerja per minggu minimal :min jam.',
            'jam_per_minggu.max' => 'Komitmen jam kerja per minggu maksimal :max jam.',

            'kompensasi.integer' => 'Besaran kompensasi harus berupa angka bulat.',
            'kompensasi.min' => 'Besaran kompensasi tidak boleh bernilai negatif.',

            'deskripsi.required' => 'Deskripsi pekerjaan wajib diisi.',
            'deskripsi.string' => 'Deskripsi pekerjaan harus berupa teks.',
            'deskripsi.min' => 'Deskripsi pekerjaan minimal :min karakter.',

            'persyaratan.required' => 'Persyaratan kualifikasi wajib diisi.',
            'persyaratan.string' => 'Persyaratan kualifikasi harus berupa teks.',
            'persyaratan.min' => 'Persyaratan kualifikasi minimal :min karakter.',

            'link_pendaftaran.required' => 'Tautan pendaftaran eksternal wajib diisi.',
            'link_pendaftaran.url' => 'Tautan pendaftaran harus berupa format URL yang valid (diawali http:// atau https://).',
            'link_pendaftaran.max' => 'Tautan pendaftaran tidak boleh lebih dari :max karakter.',

            'kontak_pendaftaran.required_without' => 'Kontak pendaftaran atau narahubung wajib diisi.',
            'kontak_pendaftaran.string' => 'Kontak pendaftaran harus berupa teks.',
            'kontak_pendaftaran.max' => 'Kontak pendaftaran tidak boleh lebih dari :max karakter.',

            'kontak_narahubung.required_without' => 'Kontak narahubung atau pendaftaran wajib diisi.',
            'kontak_narahubung.string' => 'Kontak narahubung harus berupa teks.',
            'kontak_narahubung.max' => 'Kontak narahubung tidak boleh lebih dari :max karakter.',

            'tenggat.required' => 'Batas waktu pendaftaran (tenggat) wajib diisi.',
            'tenggat.date' => 'Format tanggal batas waktu pendaftaran tidak valid.',
            'tenggat.after_or_equal' => 'Tanggal tenggat pendaftaran tidak boleh sebelum tanggal hari ini.',

            'status.required' => 'Status lowongan wajib dipilih.',
            'status.in' => 'Status lowongan harus berupa buka atau ditutup.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul lowongan',
            'perusahaan_id' => 'perusahaan',
            'kategori_id' => 'kategori',
            'tipe' => 'tipe pekerjaan',
            'sistem_kerja' => 'sistem kerja',
            'lokasi' => 'lokasi pekerjaan',
            'jam_per_minggu' => 'jam kerja per minggu',
            'kompensasi' => 'besaran kompensasi',
            'deskripsi' => 'deskripsi pekerjaan',
            'persyaratan' => 'persyaratan kualifikasi',
            'link_pendaftaran' => 'tautan pendaftaran',
            'kontak_pendaftaran' => 'kontak pendaftaran',
            'kontak_narahubung' => 'kontak narahubung',
            'tenggat' => 'tanggal batas tenggat',
            'status' => 'status lowongan',
        ];
    }
}
