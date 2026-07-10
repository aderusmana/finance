<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class CustomerRequest extends FormRequest
{
    /**
     * Tentukan apakah user boleh melakukan request ini.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Hanya user login yang boleh
    }

    /**
     * Aturan validasi.
     */
    public function rules(): array
    {
        $dynamicFileRule = function ($attribute, $value, $fail) {
            if (!$value instanceof UploadedFile) {
                return;
            }

            $extension = strtolower($value->getClientOriginalExtension());
            $sizeInKb = $value->getSize() / 1024;

            // Validasi Image (JPG, JPEG, PNG) - Max 1MB
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                if ($sizeInKb > 1024) {
                    $fail("File {$attribute} berformat Gambar (JPG/PNG) tidak boleh lebih dari 1MB.");
                }
            }
            // Validasi PDF - Max 5MB
            elseif ($extension === 'pdf') {
                if ($sizeInKb > 5120) {
                    $fail("File {$attribute} berformat PDF tidak boleh lebih dari 5MB.");
                }
            }
            // Format tidak dikenali
            else {
                $fail("File {$attribute} harus berformat PDF, JPG, atau PNG.");
            }
        };

        return [
            // --- 1. Requester Info ---
            'user_id' => 'required|exists:users,id',

            // --- 2. Classification ---
            'account_group' => 'required|exists:account_groups,id',
            'customer_class' => 'required|exists:customer_classes,id',

            // --- 3. Documents (LOGIC BARU DISINI) ---

            // NPWP: Required + Dynamic Rule
            'file_npwp' => [
                'required',
                'file',
                $dynamicFileRule
            ],

            // NIB: Required + Dynamic Rule
            'file_nib' => [
                'required',
                'file',
                $dynamicFileRule
            ],

            // KTP: Required + Dynamic Rule
            'file_ktp' => [
                'required',
                'file',
                $dynamicFileRule
            ],

            // AKTE: Nullable + PDF Only + Max 5MB
            'file_akte' => [
                'nullable',
                'file',
                'mimes:pdf', // Hanya PDF
                'max:5120'   // Max 5MB
            ],

            // --- 4. General Info ---
            'name' => 'required|string|max:255',
            'sort_name' => 'nullable|string|max:50',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'area' => 'nullable|string|max:100',

            // --- 5. Shipping & Management ---
            'shipping_to_name' => 'nullable|string|max:255',
            'shipping_to_address' => 'nullable|string',
            'purchasing_manager_name' => 'nullable|string|max:255',
            'purchasing_manager_email' => 'nullable|email|max:255',
            'purchasing_manager_telepon' => 'nullable|string|max:50',
            'finance_manager_name' => 'nullable|string|max:255',
            'finance_manager_email' => 'nullable|email|max:255',
            'finance_manager_telepon' => 'nullable|string|max:50',

            // --- 6. Billing & Tax ---
            'penagihan_nama_kontak' => 'nullable|string|max:255',
            'penagihan_telepon' => 'nullable|string|max:50',
            'penagihan_address' => 'nullable|string',
            'surat_menyurat_address' => 'nullable|string',

            'tax_contact_name' => 'nullable|string|max:255',
            'tax_contact_email' => 'nullable|email|max:255',
            'tax_contact_phone' => 'nullable|string|max:50',

            'npwp' => 'nullable|string|max:50',
            'tanggal_npwp' => 'required|date',
            'nppkp' => 'nullable|string|max:50',
            'tanggal_nppkp' => 'required|date',
            'no_pengukuhan_kaber' => 'nullable|string|max:255',

            // --- 7. Financial Terms ---
            'term_of_payment' => 'required|string',
            'output_tax' => 'required|in:Terhutang PPN,NON-PPN,PPN',
            'credit_limit' => [
                'required',
                'numeric',
                'min:0',
            ],
            'ccar' => 'nullable|string',
            'bank_garansi' => 'required|in:YA,TIDAK',
            'lead_time' => 'required|numeric|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $top = $this->input('term_of_payment');
        $bg = $this->input('bank_garansi');
        
        $creditLimit = preg_replace('/[^0-9]/', '', (string)$this->input('credit_limit'));
        if ($creditLimit === '') $creditLimit = 0;

        if (strtoupper((string)$top) === 'CBD' || strtoupper((string)$bg) === 'YA') {
            $creditLimit = 0;
        }

        $this->merge([
            'credit_limit' => $creditLimit,
        ]);
    }

    /**
     * Custom pesan error agar lebih mudah dipahami user.
     */
    public function messages(): array
    {
        return [
            // --- 1. Requester ---
            'user_id.required' => 'User (Requester) wajib dipilih.',
            'user_id.exists'   => 'User yang dipilih tidak valid.',

            // --- 2. Classification ---
            'account_group.required'  => 'Account Group wajib dipilih.',
            'customer_class.required' => 'Customer Class wajib dipilih.',

            // --- 3. Documents (Files) ---
            'file_npwp.required' => 'Dokumen NPWP wajib diupload.',
            'file_npwp.file'     => 'Dokumen NPWP harus berupa file yang valid.',

            'file_nib.required'  => 'Dokumen NIB/SIUP wajib diupload.',
            'file_nib.file'      => 'Dokumen NIB/SIUP harus berupa file yang valid.',

            'file_ktp.required'  => 'Dokumen KTP wajib diupload.',
            'file_ktp.file'      => 'Dokumen KTP harus berupa file yang valid.',

            // --- 4. General Info ---
            'name.required'     => 'Nama Customer wajib diisi.',
            'address1.required' => 'Alamat baris 1 wajib diisi.',

            // --- 5. Tax (Sesuai dengan rules: tanggal required, string npwp nullable) ---
            'tanggal_npwp.required'  => 'Tanggal NPWP wajib diisi.',
            'tanggal_nppkp.required' => 'Tanggal NPPKP wajib diisi.',

            // --- 6. Financial Terms ---
            'term_of_payment.required' => 'Term of Payment (TOP) wajib dipilih.',
            'output_tax.required'      => 'Output Tax wajib dipilih.',
            'output_tax.in'            => 'Pilihan Output Tax tidak valid.',

            'credit_limit.required' => 'Credit Limit wajib dihitung/diisi.',
            'credit_limit.min'      => 'Credit Limit tidak boleh bernilai negatif.',

            'bank_garansi.required' => 'Status Bank Garansi wajib dipilih.',
            
            'lead_time.required'    => 'Lead Time wajib diisi.',
            'lead_time.min'         => 'Lead Time tidak boleh bernilai negatif.',
        ];
    }
}
