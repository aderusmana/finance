<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class RequestDistributorOtpRequest extends FormRequest
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
            'website_hp' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode Distributor wajib diisi.',
            'email.required' => 'Email terdaftar wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'website_hp.prohibited' => 'Permintaan tidak valid.',
        ];
    }
}
