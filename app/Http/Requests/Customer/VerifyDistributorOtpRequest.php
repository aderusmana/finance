<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class VerifyDistributorOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'otp' => ['required', 'digits:6'],
            'year' => ['nullable', 'integer', 'digits:4'],
            'website_hp' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode Distributor wajib diisi.',
            'email.required' => 'Email terdaftar wajib diisi.',
            'otp.required' => 'Kode OTP 6-digit wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
            'website_hp.prohibited' => 'Permintaan tidak valid.',
        ];
    }
}
