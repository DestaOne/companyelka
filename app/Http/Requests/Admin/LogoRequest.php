<?php

namespace App\Http\Requests\Admin;

use App\Support\UploadLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LogoRequest extends FormRequest
{
    public const IMAGE_MAX_KB = 1024;

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
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.UploadLimit::kilobytes(self::IMAGE_MAX_KB),
                'dimensions:min_width=200',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $limit = UploadLimit::label(UploadLimit::kilobytes(self::IMAGE_MAX_KB));

        return [
            'image.required' => 'Pilih berkas logo terlebih dahulu.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Logo harus berformat JPG, PNG, atau WebP.',
            'image.max' => "Ukuran logo maksimal {$limit}.",
            'image.uploaded' => "Logo gagal diunggah. Ukuran logo maksimal {$limit}.",
            'image.dimensions' => 'Lebar logo minimal 200 piksel agar tetap tajam.',
        ];
    }
}
