<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributorListRequest extends FormRequest
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
        $rules = [
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer', 'digits:4'],
            'with_upload' => ['nullable', 'boolean'],
        ];

        if ($this->boolean('with_upload')) {
            $rules['doc_type'] = ['required', 'in:bupot,transfer,top_insentif'];
            $rules['month'] = [
                'required_if:doc_type,bupot,top_insentif',
                'nullable',
                'integer',
                'between:1,12',
            ];
            $rules['transaction_date'] = [
                'required_if:doc_type,transfer',
                'nullable',
                'date',
            ];
            $rules['title'] = ['nullable', 'string', 'max:255'];
            $rules['notes'] = ['nullable', 'string'];
            $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:1024'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'distributor_id.required' => 'Distributor wajib dipilih.',
            'distributor_id.exists' => 'Distributor tidak valid.',
            'year.required' => 'Tahun dokumen wajib diisi.',
            'year.digits' => 'Format tahun harus 4 digit.',
            'file.required' => 'Berkas PDF wajib diunggah jika opsi upload dicentang.',
            'file.max' => 'Ukuran file tidak boleh melebihi 1 MB.',
            'file.mimes' => 'Format file harus berupa PDF.',
            'doc_type.required' => 'Jenis dokumen wajib dipilih.',
            'transaction_date.required_if' => 'Tanggal transaksi wajib diisi untuk Penjelasan Transfer.',
            'month.required_if' => 'Bulan wajib dipilih untuk BuPot dan TOP Insentif.',
        ];
    }
}
