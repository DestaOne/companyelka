<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /** Jumlah gagal login berturut-turut sebelum akun dikunci sementara. */
    public const MAX_ATTEMPTS = 3;

    /** Lama penguncian (detik) setelah mencapai batas gagal login. */
    public const LOCKOUT_SECONDS = 10;

    /** Rentang waktu (detik) percobaan gagal dihitung sebagai satu rangkaian. */
    private const ATTEMPT_WINDOW = 600;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt([...$this->only('email', 'password'), 'is_admin' => true], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), self::ATTEMPT_WINDOW);

            // Gagal ke-3: kunci selama 10 detik, lalu hitungan gagal dimulai dari nol lagi.
            if (RateLimiter::attempts($this->throttleKey()) >= self::MAX_ATTEMPTS) {
                RateLimiter::clear($this->throttleKey());
                RateLimiter::hit($this->lockoutKey(), self::LOCKOUT_SECONDS);
                event(new Lockout($this));

                throw ValidationException::withMessages(['email' => $this->lockoutMessage(self::LOCKOUT_SECONDS)]);
            }

            $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => "Email atau password salah. Sisa percobaan: {$remaining}.",
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->lockoutKey());
    }

    /**
     * Ensure the login request is not locked out.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->lockoutKey(), 1)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => $this->lockoutMessage(RateLimiter::availableIn($this->lockoutKey())),
        ]);
    }

    private function lockoutMessage(int $seconds): string
    {
        return "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.";
    }

    /** Kunci penguncian: terpisah dari kunci hitungan gagal. */
    public function lockoutKey(): string
    {
        return static::keyFor($this->string('email'), $this->ip()).'|lockout';
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return static::keyFor($this->string('email'), $this->ip());
    }

    public static function keyFor(string $email, ?string $ip): string
    {
        return Str::transliterate(Str::lower($email).'|'.$ip);
    }

    /** Sisa detik penguncian untuk email dan IP ini; 0 bila tidak sedang terkunci. */
    public static function lockoutRemaining(string $email, ?string $ip): int
    {
        $key = static::keyFor($email, $ip).'|lockout';

        return RateLimiter::tooManyAttempts($key, 1) ? RateLimiter::availableIn($key) : 0;
    }
}
