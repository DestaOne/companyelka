<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\UploadLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileRequest extends FormRequest
{
    public const PHOTO_MAX_KB = 2048;

    public function authorize(): bool
    {
        return true;
    }

    /** Email dinormalisasi agar "Budi@Mail.com" dan "budi@mail.com" dianggap sama. */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    /** Apakah admin memilih opsi "Ubah password". */
    public function changesPassword(): bool
    {
        return $this->boolean('change_password');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.UploadLimit::kilobytes(self::PHOTO_MAX_KB),
                'dimensions:min_width=200,min_height=200',
            ],
            'remove_photo' => ['nullable', 'boolean'],
        ];

        // Password hanya divalidasi bila opsi ubah password dipilih; password lama wajib benar.
        if ($this->changesPassword()) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'confirmed', Password::min(8), 'different:current_password'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'username',
            'email' => 'email',
            'photo' => 'foto profil',
            'current_password' => 'password lama',
            'password' => 'password baru',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $limit = UploadLimit::label(UploadLimit::kilobytes(self::PHOTO_MAX_KB));

        return [
            'required' => ':attribute wajib diisi.',
            'photo.image' => 'Berkas harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'photo.max' => "Ukuran foto maksimal {$limit}.",
            'photo.uploaded' => "Foto gagal diunggah. Ukuran foto maksimal {$limit}.",
            'photo.dimensions' => 'Foto minimal 200 x 200 piksel agar tetap tajam.',
            'name.max' => 'Username maksimal :max karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal :min karakter.',
            'password.different' => 'Password baru harus berbeda dari password lama.',
        ];
    }
}
