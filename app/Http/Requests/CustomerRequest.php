<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        return [
            // --- 1. Requester Info ---
            'user_id' => 'required|exists:users,id',

            // --- 2. Classification ---
            'account_group' => 'required|exists:account_groups,id',
            'customer_class' => 'required|exists:customer_classes,id',

            // --- 3. Documents (File Uploads) ---
            'file_npwp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_nib'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_ktp'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_akte' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // --- 4. General Info ---
            'name' => 'required|string|max:255',
            'sort_name' => 'nullable|string|max:50',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'area' => 'required|string|max:100',

            // --- 5. Shipping & Management ---
            'shipping_to_name' => 'required|string|max:255',
            'shipping_to_address' => 'required|string',
            'purchasing_manager_name' => 'required|string|max:255',
            'purchasing_manager_email' => 'required|email|max:255',
            'finance_manager_name' => 'required|string|max:255',
            'finance_manager_email' => 'required|email|max:255',

            // --- 6. Billing & Tax ---
            'penagihan_nama_kontak' => 'required|string|max:255',
            'penagihan_telepon' => 'required|string|max:50',
            'penagihan_address' => 'required|string',
            'surat_menyurat_address' => 'required|string',

            'tax_contact_name' => 'required|string|max:255',
            'tax_contact_email' => 'required|email|max:255',
            'tax_contact_phone' => 'required|string|max:50',

            'npwp' => 'required|string|max:50',
            'tanggal_npwp' => 'required|date',
            'nppkp' => 'nullable|string|max:50',
            'tanggal_nppkp' => 'nullable|date',
            'no_pengukuhan_kaber' => 'nullable|string|max:255',

            // --- 7. Financial Terms ---
            'term_of_payment' => 'required|string', // Sesuaikan jika ini relasi ID
            'output_tax' => 'required|in:Terhutang PPN,NON-PPN,PPN', // Sesuaikan opsi
            'credit_limit' => [
            'required',
            'numeric',
            'min:0',
                function ($attribute, $value, $fail) {
                    if (request('bank_garansi') === 'TIDAK' && $value <= 0) {
                        $fail('Jika Bank Garansi NO, Credit Limit harus diisi (lebih dari 0).');
                    }
                },
            ],
            'ccar' => 'required|string',
            'bank_garansi' => 'required|in:YA,TIDAK',
            'lead_time' => 'nullable|numeric|min:0',
        ];
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
            // NPWP
            'file_npwp.required' => 'Dokumen NPWP wajib diupload.',
            'file_npwp.mimes'    => 'Format file NPWP harus PDF, JPG, atau PNG.',
            'file_npwp.max'      => 'Ukuran file NPWP maksimal 5MB.',
            // NIB/SIUP
            'file_nib.mimes'    => 'Format file NIB/SIUP harus PDF, JPG, atau PNG.',
            'file_nib.max'      => 'Ukuran file NIB/SIUP maksimal 5MB.',
            // KTP
            'file_ktp.mimes'    => 'Format file KTP harus PDF, JPG, atau PNG.',
            'file_ktp.max'      => 'Ukuran file KTP maksimal 5MB.',
            // Akte Pendirian
            'file_akte.mimes'    => 'Format Akte harus PDF, JPG, atau PNG.',
            'file_akte.max'      => 'Ukuran file Akte maksimal 5MB.',

            // --- 4. General Info ---
            'name.required'        => 'Nama Customer wajib diisi.',
            'address1.required'    => 'Alamat baris 1 wajib diisi.',
            'city.required'        => 'Kota wajib diisi.',
            'postal_code.required' => 'Kode Pos wajib diisi.',
            'country.required'     => 'Negara wajib diisi.',
            'email.required'       => 'Email general wajib diisi.',
            'email.email'          => 'Format email general tidak valid.',
            'area.required'        => 'Area wajib diisi.',

            // --- 5. Shipping & Mgmt ---
            'shipping_to_name.required'    => 'Nama penerima pengiriman wajib diisi.',
            'shipping_to_address.required' => 'Alamat pengiriman wajib diisi.',

            'purchasing_manager_name.required'  => 'Nama Purchasing Manager wajib diisi.',
            'purchasing_manager_email.required' => 'Email Purchasing Manager wajib diisi.',
            'purchasing_manager_email.email'    => 'Format email Purchasing Manager tidak valid.',

            'finance_manager_name.required'  => 'Nama Finance Manager wajib diisi.',
            'finance_manager_email.required' => 'Email Finance Manager wajib diisi.',
            'finance_manager_email.email'    => 'Format email Finance Manager tidak valid.',

            // --- 6. Billing & Tax ---
            'penagihan_nama_kontak.required'  => 'Nama kontak penagihan wajib diisi.',
            'penagihan_telepon.required'      => 'Nomor telepon penagihan wajib diisi.',
            'penagihan_address.required'      => 'Alamat penagihan wajib diisi.',
            'surat_menyurat_address.required' => 'Alamat surat menyurat wajib diisi.',

            'tax_contact_name.required'  => 'Nama kontak pajak wajib diisi.',
            'tax_contact_email.required' => 'Email kontak pajak wajib diisi.',
            'tax_contact_phone.required' => 'Telepon kontak pajak wajib diisi.',

            'npwp.required'          => 'Nomor NPWP wajib diisi.',
            'tanggal_npwp.required'  => 'Tanggal NPWP wajib diisi.',

            // --- 7. Financial Terms ---
            'term_of_payment.required' => 'Term of Payment (TOP) wajib dipilih.',
            'output_tax.required'      => 'Output Tax wajib dipilih.',
            'output_tax.in'            => 'Pilihan Output Tax tidak valid.',

            'credit_limit.required' => 'Credit Limit wajib dihitung/diisi.',
            'credit_limit.min'      => 'Credit Limit tidak boleh bernilai negatif.',

            'ccar.required'         => 'CCAR wajib dipilih.',
            'bank_garansi.required' => 'Status Bank Garansi wajib dipilih.',
        ];
    }
}
