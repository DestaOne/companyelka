<?php

namespace App\Http\Requests\Admin;

use App\Support\UploadLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeFeatureRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:300'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.UploadLimit::kilobytes(self::IMAGE_MAX_KB),
                'dimensions:min_width=800',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['title' => 'judul', 'description' => 'teks', 'image' => 'gambar'];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $limit = UploadLimit::label(UploadLimit::kilobytes(self::IMAGE_MAX_KB));

        return [
            'required' => ':attribute wajib diisi.',
            'title.max' => 'Judul maksimal :max karakter.',
            'description.max' => 'Teks maksimal :max karakter.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => "Ukuran gambar maksimal {$limit}.",
            'image.uploaded' => "Gambar gagal diunggah. Ukuran gambar maksimal {$limit}.",
            'image.dimensions' => 'Lebar gambar minimal 800 piksel agar tetap tajam.',
        ];
    }
}
