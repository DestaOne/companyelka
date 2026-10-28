<?php

namespace App\Http\Requests\Admin;

use App\Models\Contact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Nomor WhatsApp diseragamkan (hanya angka, awalan 62) dan email dijadikan huruf kecil sebelum divalidasi. */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('whatsapp'))) {
            $this->merge(['whatsapp' => Contact::normalizeWhatsapp($this->input('whatsapp'))]);
        }

        // Kolom tautan opsional: kosong berarti tautan dibuat otomatis dari datanya.
        foreach (['whatsapp_link', 'email_link'] as $field) {
            if (is_string($this->input($field))) {
                $value = trim($this->input($field));
                $this->merge([$field => $value === '' ? null : $value]);
            }
        }

        if (is_string($this->input('maps_link'))) {
            $link = trim($this->input('maps_link'));

            // Kode "Sematkan peta" dari Google berupa <iframe>; yang diambil hanya alamat src-nya.
            if (preg_match('/<iframe[^>]*\ssrc\s*=\s*["\']([^"\']+)["\']/i', $link, $m)) {
                $link = html_entity_decode($m[1]);
            }

            $this->merge(['maps_link' => $link === '' ? null : $link]);
        }

        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => ['required', 'string', 'max:300'],
            'whatsapp' => ['required', 'starts_with:62', 'digits_between:10,15'],
            'whatsapp_link' => ['nullable', 'string', 'max:2000', 'url:https', function (string $attribute, mixed $value, \Closure $fail) {
                if (! in_array(parse_url($value, PHP_URL_HOST), Contact::WHATSAPP_HOSTS, true)) {
                    $fail('Link WhatsApp harus berasal dari wa.me atau whatsapp.com.');
                }
            }],
            'email' => ['required', 'string', 'email', 'max:255'],
            'email_link' => ['nullable', 'string', 'max:2000', 'regex:/^mailto:[^\s@]+@[^\s@]+\.[^\s@]+$/i'],
            'maps_link' => ['nullable', 'string', 'max:2000', 'url:https', function (string $attribute, mixed $value, \Closure $fail) {
                if (! in_array(parse_url($value, PHP_URL_HOST), Contact::MAPS_HOSTS, true)) {
                    $fail('Link peta harus berasal dari Google Maps.');
                }
            }],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'address' => 'alamat',
            'whatsapp' => 'nomor WhatsApp',
            'email' => 'email',
            'maps_link' => 'link Google Maps',
            'whatsapp_link' => 'link WhatsApp',
            'email_link' => 'link email',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'address.max' => 'Alamat maksimal :max karakter.',
            'whatsapp.starts_with' => 'Nomor WhatsApp harus nomor Indonesia (awalan 0 atau +62).',
            'whatsapp.digits_between' => 'Nomor WhatsApp harus 10 sampai 15 angka.',
            'email.email' => 'Format email tidak valid.',
            'whatsapp_link.url' => 'Link WhatsApp harus berupa alamat https yang valid.',
            'whatsapp_link.max' => 'Link WhatsApp maksimal :max karakter.',
            'email_link.regex' => 'Link email harus diawali mailto: diikuti alamat email, mis. mailto:nama@contoh.com.',
            'email_link.max' => 'Link email maksimal :max karakter.',
            'maps_link.url' => 'Link Google Maps harus berupa alamat https yang valid.',
            'maps_link.max' => 'Link Google Maps maksimal :max karakter.',
            'email.max' => 'Email maksimal :max karakter.',
        ];
    }
}
