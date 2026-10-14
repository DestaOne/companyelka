<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Support\UploadLimit;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public const IMAGE_MAX_KB = 2048;

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
            'title' => ['required', 'string', 'max:150'],
            'summary' => ['required', 'string', 'max:600'],
            'detail' => ['required', 'string', 'max:10000'],
            'status' => ['required', Rule::in([Service::STATUS_DRAFT, Service::STATUS_PUBLISHED])],
            'image' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.UploadLimit::kilobytes(self::IMAGE_MAX_KB),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'summary' => 'deskripsi halaman layanan',
            'detail' => 'deskripsi detail layanan',
            'status' => 'status',
            'image' => 'gambar',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max.string' => ':attribute maksimal :max karakter.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal '.UploadLimit::label(UploadLimit::kilobytes(self::IMAGE_MAX_KB)).'.',
            'image.uploaded' => 'Gambar gagal diunggah. Ukuran gambar maksimal '.UploadLimit::label(UploadLimit::kilobytes(self::IMAGE_MAX_KB)).'.',
        ];
    }
}
