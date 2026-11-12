<?php

namespace App\Http\Requests;

use App\Models\Kategori;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kategori = $this->route('kategori');
        $kategoriId = $kategori instanceof Kategori ? $kategori->id : $kategori;

        return [
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategoris', 'nama')->ignore($kategoriId),
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.string' => 'Nama kategori harus berupa teks.',
            'nama.max' => 'Nama kategori tidak boleh lebih dari :max karakter.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
            'deskripsi.string' => 'Deskripsi kategori harus berupa teks.',
            'deskripsi.max' => 'Deskripsi kategori tidak boleh lebih dari :max karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama kategori',
            'deskripsi' => 'deskripsi kategori',
        ];
    }
}
