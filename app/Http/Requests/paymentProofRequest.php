<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class paymentProofRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'complain_id' => 'required|exists:requisitions,id',
            'payment_date' => 'required|date',
            'payment_document' => 'required|file|mimes:jpeg,jpg,png,pdf|max:1024'
        ];
    }

    public function messages()
    {
        return [
            'complain_id.required' => 'Complain ID is required.',
            'complain_id.exists' => 'Invalid complain ID.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.date' => 'Payment date must be a valid date.',
            'payment_document.required' => 'Payment document is required.',
            'payment_document.file' => 'Payment document must be a file.',
            'payment_document.mimes' => 'Payment document must be a JPEG, JPG, PNG, or PDF file.',
            'payment_document.max' => 'Payment document size must not exceed 1MB.'
        ];
    }
}
