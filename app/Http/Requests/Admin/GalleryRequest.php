<?php

namespace App\Http\Requests\Admin;

use App\Support\UploadLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
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
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:100'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titles.*.max' => 'Judul gambar maksimal 100 karakter.',
            'images.required' => 'Pilih minimal satu gambar.',
            'images.max' => 'Maksimal 10 gambar sekali unggah.',
            'images.*.image' => 'Setiap berkas harus berupa gambar.',
            'images.*.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'images.*.uploaded' => 'Gambar gagal diunggah. Ukuran gambar kemungkinan melebihi batas server ('.UploadLimit::label(UploadLimit::serverKilobytes()).' per gambar).',
        ];
    }
}
