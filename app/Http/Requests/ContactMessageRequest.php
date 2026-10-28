<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Bila formulir gagal divalidasi, pengunjung kembali ke bagian formulir, bukan ke awal halaman. */
    protected function getRedirectUrl(): string
    {
        return url('/tentang-kami').'#kontak';
    }

    protected function prepareForValidation(): void
    {
        foreach (['name', 'phone', 'email', 'subject', 'body'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }

    /** Kolom "website" disembunyikan dari pengunjung; hanya robot yang mengisinya. */
    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s\-]{6,}$/'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'phone' => 'nomor telepon/WhatsApp',
            'email' => 'email',
            'subject' => 'subjek',
            'body' => 'pesan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'phone.regex' => 'Nomor telepon/WhatsApp tidak valid. Gunakan angka, contoh 0811 3383 679.',
            'email.email' => 'Format email tidak valid.',
            'body.min' => 'Pesan minimal :min karakter.',
        ];
    }
}
