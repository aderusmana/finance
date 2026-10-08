<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerImportTemplateSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Import Template';
    }

    public function headings(): array
    {
        return [
            'user_id',
            'code',
            'no_pkd',
            'pic',
            'name',
            'sort_name',
            'customer_type',
            'customer_class',
            'account_group',
            'address1',
            'address2',
            'address3',
            'city',
            'postal_code',
            'country',
            'shipping_to_name',
            'shipping_to_address',
            'purchasing_manager_name',
            'purchasing_manager_email',
            'finance_manager_name',
            'finance_manager_email',
            'penagihan_nama_kontak',
            'penagihan_telepon',
            'penagihan_address',
            'surat_menyurat_address',
            'email',
            'tax_contact_name',
            'tax_contact_email',
            'tax_contact_phone',
            'npwp',
            'tanggal_npwp',
            'nppkp',
            'tanggal_nppkp',
            'no_pengukuhan_kaber',
            'output_tax',
            'term_of_payment',
            'lead_time',
            'credit_limit',
            'ccar',
            'bank_garansi',
            'area',
            'status',
            'pembagian',
            'customer_total',
            'virtual_account',
            'payment_days',
            'payment_date',
            'faktur_days',
            'faktur_date',
            'join_date',
        ];
    }

    public function array(): array
    {
        return [
            [
                '1', // user_id
                'CUST-001', // code
                '', // no_pkd
                'Mr. Budi Santoso', // pic
                'Maju Jaya Corporation', // name
                'MJC', // sort_name
                'Company/Badan Usaha', // customer_type (See Sheet 4)
                '1', // customer_class (See ID on Sheet 2)
                '1', // account_group (See ID on Sheet 3)
                'Sudirman Central Tower, 1st Floor', // address1
                '', // address2
                '', // address3
                'South Jakarta', // city
                '12190', // postal_code
                'Indonesia', // country
                'Warehouse Andi', // shipping_to_name
                'Industrial Estate Block B2, Jakarta', // shipping_to_address
                'Coki Pranata', // purchasing_manager_name
                'coki@majujaya.com', // purchasing_manager_email
                'Dini Anggraini', // finance_manager_name
                'dini@majujaya.com', // finance_manager_email
                'Eka Rahayu', // penagihan_nama_kontak
                '08123456789', // penagihan_telepon
                'Sudirman Central Tower, 1st Floor, Jakarta', // penagihan_address
                'Sudirman Central Tower, 1st Floor, Jakarta', // surat_menyurat_address
                'info@majujaya.com', // email
                'Fani Wardani', // tax_contact_name
                'tax@majujaya.com', // tax_contact_email
                '08123456780', // tax_contact_phone
                '01.234.567.8-012.000', // npwp
                '2020-01-01', // tanggal_npwp
                '', // nppkp
                '', // tanggal_nppkp
                '', // no_pengukuhan_kaber
                'PPN', // output_tax
                '30', // term_of_payment
                '0', // lead_time
                '10000000', // credit_limit
                'smd_idr', // ccar
                'TIDAK', // bank_garansi
                'Greater Jakarta', // area
                'active', // status
                '', // pembagian
                '0', // customer_total
                '1234567890', // virtual_account
                'Monday,Tuesday', // payment_days
                '15,30', // payment_date
                'Wednesday,Thursday', // faktur_days
                '10,20', // faktur_date
                '2026-03-11', // join_date
            ],
            [
                '1', // user_id
                'CUST-002', // code
                '', // no_pkd
                'Mrs. Siti Nurhaliza', // pic
                'Berkah Bakery Store', // name
                'BBS', // sort_name
                'Individual/Perorangan', // customer_type (See Sheet 4)
                '2', // customer_class (See ID on Sheet 2)
                '1', // account_group (See ID on Sheet 3)
                'Ahmad Yani St. No 45', // address1
                '', // address2
                '', // address3
                'Surabaya', // city
                '60234', // postal_code
                'Indonesia', // country
                'Mrs. Siti Nurhaliza', // shipping_to_name
                'Ahmad Yani St. No 45, Surabaya', // shipping_to_address
                'Mrs. Siti Nurhaliza', // purchasing_manager_name
                'siti@berkahbakery.com', // purchasing_manager_email
                'Mrs. Siti Nurhaliza', // finance_manager_name
                'siti@berkahbakery.com', // finance_manager_email
                'Mrs. Siti Nurhaliza', // penagihan_nama_kontak
                '08198765432', // penagihan_telepon
                'Ahmad Yani St. No 45, Surabaya', // penagihan_address
                'Ahmad Yani St. No 45, Surabaya', // surat_menyurat_address
                'siti@berkahbakery.com', // email
                '', // tax_contact_name
                '', // tax_contact_email
                '', // tax_contact_phone
                '', // npwp
                '', // tanggal_npwp
                '', // nppkp
                '', // tanggal_nppkp
                '', // no_pengukuhan_kaber
                'NON-PPN', // output_tax
                '0', // term_of_payment
                '0', // lead_time
                '0', // credit_limit
                'smd_idr', // ccar
                'TIDAK', // bank_garansi
                'East Java', // area
                'active', // status
                '', // pembagian
                '0', // customer_total
                '', // virtual_account
                '', // payment_days
                '', // payment_date
                '', // faktur_days
                '', // faktur_date
                '2026-03-11', // join_date
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'] // Dark Blue
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
