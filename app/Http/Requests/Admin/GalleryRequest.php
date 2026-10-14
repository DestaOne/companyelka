<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Support\UploadLimit;
use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public const APP_MAX_KB = 2048;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.UploadLimit::kilobytes(self::APP_MAX_KB), 'dimensions:min_width=800'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.required' => 'Pilih minimal satu gambar.',
            'images.max' => 'Maksimal 10 gambar sekali unggah.',
            'images.*.image' => 'Setiap berkas harus berupa gambar.',
            'images.*.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran tiap gambar maksimal '.UploadLimit::label(UploadLimit::kilobytes(self::APP_MAX_KB)).'.',
            'images.*.dimensions' => 'Lebar gambar minimal 800 piksel agar tetap tajam saat di-zoom.',
            'images.*.uploaded' => 'Gambar gagal diunggah. Ukuran tiap gambar maksimal '.UploadLimit::label(UploadLimit::kilobytes(self::APP_MAX_KB)).'.',
        ];
    }
}
