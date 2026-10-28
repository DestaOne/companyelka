<?php

namespace App\Http\Requests\Admin;

use App\Models\HomeCard;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeCardRequest extends FormRequest
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
        $isCta = (int) $this->route('slot') === HomeCard::CTA_SLOT;

        return [
            'eyebrow' => ['required', 'string', 'max:60'],
            'title' => ['required', 'string', 'max:80'],
            // Kartu biru (kotak 4) tidak punya deskripsi di desainnya, jadi di sana opsional.
            'description' => [$isCta ? 'nullable' : 'required', 'string', 'max:250'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'eyebrow' => 'judul kecil',
            'title' => 'judul',
            'description' => 'deskripsi',
            'service_id' => 'tujuan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'eyebrow.max' => 'Judul kecil maksimal :max karakter.',
            'title.max' => 'Judul maksimal :max karakter.',
            'description.max' => 'Deskripsi maksimal :max karakter.',
            'service_id.exists' => 'Layanan tujuan tidak ditemukan.',
        ];
    }
}
