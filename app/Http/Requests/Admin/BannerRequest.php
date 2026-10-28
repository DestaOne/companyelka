<?php

namespace App\Http\Requests\Admin;

use App\Models\Banner;
use App\Support\UploadLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public const IMAGE_MAX_KB = 2048;

    public function authorize(): bool
    {
        return true;
    }

    private function hasButton(): bool
    {
        return isset((Banner::definition((string) $this->route('type'), (string) $this->route('slug')) ?? [])['button_url']);
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('content'))) {
            $this->merge(['content' => trim(str_replace(["\r\n", "\r"], "\n", $this->input('content')))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1500', function (string $attribute, mixed $value, \Closure $fail) {
                // Perlu minimal satu judul (baris "# ...") yang tidak kosong.
                if (! preg_match('/^# \S/m', $value)) {
                    $fail('Teks harus memiliki judul. Pilih teks lalu tekan tombol H untuk menjadikannya judul.');
                }
            }],
            'button_text' => $this->hasButton() ? ['required', 'string', 'max:40'] : ['prohibited'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.UploadLimit::kilobytes(self::IMAGE_MAX_KB),
                'dimensions:min_width=1200',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'content' => 'teks',
            'button_text' => 'teks tombol',
            'image' => 'gambar',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $limit = UploadLimit::label(UploadLimit::kilobytes(self::IMAGE_MAX_KB));

        return [
            'required' => ':attribute wajib diisi.',
            'content.max' => 'Teks maksimal :max karakter.',
            'button_text.max' => 'Teks tombol maksimal :max karakter.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => "Ukuran gambar maksimal {$limit}.",
            'image.uploaded' => "Gambar gagal diunggah. Ukuran gambar maksimal {$limit}.",
            'image.dimensions' => 'Lebar gambar minimal 1200 piksel agar tetap tajam di layar lebar.',
        ];
    }
}
