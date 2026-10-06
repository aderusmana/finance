<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributorDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-distributor-docs') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'distributor_id' => ['required', 'exists:distributors,id'],
            'doc_type' => ['required', 'in:bupot,transfer,top_insentif'],

            'year' => ['required_if:doc_type,bupot,top_insentif', 'nullable', 'integer', 'digits:4'],
            'month' => [
                'required_if:doc_type,bupot,top_insentif',
                'prohibited_if:doc_type,transfer',
                'nullable',
                'integer',
                'between:1,12',
            ],

            'transaction_date' => [
                'required_if:doc_type,transfer',
                'prohibited_unless:doc_type,transfer',
                'nullable',
                'date',
            ],

            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            // Maksimal 1MB = 1024 KB, format PDF only
            'file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:1024',
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('doc_type') === 'transfer') {
            $this->merge([
                'month' => null,
            ]);
        } else {
            $this->merge([
                'transaction_date' => null,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'file.max' => 'Ukuran file tidak boleh melebihi 1 MB.',
            'file.mimes' => 'Format file harus berupa PDF.',
            'transaction_date.required_if' => 'Tanggal transaksi wajib diisi untuk dokumen Penjelasan Transfer.',
            'transaction_date.prohibited_unless' => 'Tanggal transaksi hanya berlaku untuk dokumen Penjelasan Transfer.',
            'year.required_if' => 'Tahun wajib diisi untuk Bukti Potong dan TOP Insentif.',
            'month.required_if' => 'Bulan wajib diisi untuk Bukti Potong dan TOP Insentif.',
            'month.prohibited_if' => 'Bulan dokumen tidak berlaku untuk dokumen Penjelasan Transfer.',
        ];
    }
}
