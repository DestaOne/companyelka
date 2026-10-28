<?php

namespace App\Http\Requests\Admin;

use App\Models\AboutSection;
use App\Support\UploadLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AboutSectionRequest extends FormRequest
{
    public const IMAGE_MAX_KB = 2048;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    private function definition(): array
    {
        return AboutSection::definition((string) $this->route('key')) ?? [];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('body'))) {
            $this->merge(['body' => trim(str_replace(["\r\n", "\r"], "\n", $this->input('body')))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $definition = $this->definition();

        $body = match ($definition['body'] ?? 'text') {
            'paragraphs' => ['required', 'string', 'max:2500'],
            'list' => ['required', 'string', 'max:1500', function (string $attribute, mixed $value, \Closure $fail) {
                $items = array_filter(array_map('trim', explode("\n", $value)));

                if (count($items) > 8) {
                    $fail('Maksimal 8 butir.');
                }

                foreach ($items as $item) {
                    if (mb_strlen($item) > 250) {
                        $fail('Setiap butir maksimal 250 karakter.');
                    }
                }
            }],
            default => ['required', 'string', 'max:800'],
        };

        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => $body,
            'eyebrow' => empty($definition['eyebrow']) ? ['prohibited'] : ['required', 'string', 'max:60'],
            'image' => empty($definition['image']) ? ['prohibited'] : [
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
        $list = ($this->definition()['body'] ?? '') === 'list';

        return [
            'title' => 'judul',
            'body' => $list ? 'daftar' : 'teks',
            'eyebrow' => 'judul kecil',
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
            'title.max' => 'Judul maksimal :max karakter.',
            'eyebrow.max' => 'Judul kecil maksimal :max karakter.',
            'body.max' => 'Teks terlalu panjang (maksimal :max karakter).',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => "Ukuran gambar maksimal {$limit}.",
            'image.uploaded' => "Gambar gagal diunggah. Ukuran gambar maksimal {$limit}.",
            'image.dimensions' => 'Lebar gambar minimal 800 piksel agar tetap tajam.',
        ];
    }
}
