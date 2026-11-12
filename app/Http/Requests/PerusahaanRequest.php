<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PerusahaanRequest extends FormRequest
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
        return [
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'sosial_media' => [
                'nullable',
                'string',
                'url',
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
            'nama.required' => 'Nama perusahaan wajib diisi.',
            'nama.string' => 'Nama perusahaan harus berupa teks.',
            'nama.max' => 'Nama perusahaan tidak boleh lebih dari :max karakter.',
            'email.required' => 'Alamat email perusahaan wajib diisi.',
            'email.string' => 'Alamat email harus berupa teks.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.max' => 'Alamat email tidak boleh lebih dari :max karakter.',
            'sosial_media.string' => 'Tautan media sosial harus berupa teks.',
            'sosial_media.url' => 'Tautan media sosial harus berupa format URL yang valid (diawali http:// atau https://).',
            'sosial_media.max' => 'Tautan media sosial tidak boleh lebih dari :max karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama perusahaan',
            'email' => 'alamat email perusahaan',
            'sosial_media' => 'media sosial / website',
        ];
    }
}
