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

class CustomerInstructionsSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Field Descriptions & Guide';
    }

    public function headings(): array
    {
        return [
            'COLUMN NAME',
            'REQUIREMENT',
            'ALLOWED VALUES / EXAMPLES',
            'DESCRIPTION & INSTRUCTIONS',
        ];
    }

    public function array(): array
    {
        return [
            [
                'customer_type',
                'Required',
                'Individual/Perorangan  OR  Company/Badan Usaha',
                'Legal customer type. Choose either "Individual/Perorangan" (for personal / retail accounts) or "Company/Badan Usaha" (for corporate / legal entities).',
            ],
            [
                'customer_class',
                'Required',
                'ID Number (e.g. 1, 2, 3...)',
                'Customer class ID. Please refer to the complete list of IDs and class names on Sheet 2 ("Customer Class Reference").',
            ],
            [
                'account_group',
                'Required',
                'ID Number (e.g. 1, 2, 3...)',
                'Account group / region ID. Please refer to the complete list of IDs and names on Sheet 3 ("Account Group Reference").',
            ],
            [
                'user_id',
                'Optional',
                'User ID Number (e.g. 1)',
                'ID of the sales representative or user responsible for this customer. If left blank, automatically defaults to the user currently performing the import. See Sheet 5 for user reference.',
            ],
            [
                'code',
                'Required',
                'Unique text (e.g. CUST-001, CUST-002)',
                'Unique customer code. If the code already exists in the database, the record will automatically be updated with the imported values.',
            ],
            [
                'name',
                'Required',
                'Text (e.g. Maju Jaya Corporation)',
                'Full legal name of the customer or company.',
            ],
            [
                'sort_name',
                'Optional',
                'Text (e.g. MJC)',
                'Short name, abbreviation, or alias for the customer.',
            ],
            [
                'pic',
                'Optional',
                'Text (e.g. Mr. Budi Santoso)',
                'Primary Person In Charge (contact person). Defaults to "-" if empty.',
            ],
            [
                'address1',
                'Optional',
                'Street address text',
                'Primary billing or head office address. Defaults to "-" if empty.',
            ],
            [
                'address2',
                'Optional',
                'Street address text line 2',
                'Additional address details (if any).',
            ],
            [
                'address3',
                'Optional',
                'Street address text line 3',
                'Additional address details (if any).',
            ],
            [
                'city',
                'Optional',
                'City name (e.g. South Jakarta)',
                'City of the customer location.',
            ],
            [
                'postal_code',
                'Optional',
                'Postal code (e.g. 12190)',
                'ZIP or postal code.',
            ],
            [
                'country',
                'Optional',
                'Indonesia',
                'Country of the customer. Defaults to "Indonesia" if left empty.',
            ],
            [
                'shipping_to_name',
                'Optional',
                'Recipient / Warehouse name (e.g. Warehouse Andi)',
                'Name of the delivery contact or destination warehouse.',
            ],
            [
                'shipping_to_address',
                'Optional',
                'Shipping address text',
                'Destination address where goods should be shipped.',
            ],
            [
                'purchasing_manager_name',
                'Optional',
                'Purchasing manager name',
                'Name of the customer purchasing manager.',
            ],
            [
                'purchasing_manager_email',
                'Optional',
                'Email (e.g. purchasing@customer.com)',
                'Email address of the customer purchasing manager.',
            ],
            [
                'finance_manager_name',
                'Optional',
                'Finance manager name',
                'Name of the customer finance manager.',
            ],
            [
                'finance_manager_email',
                'Optional',
                'Email (e.g. finance@customer.com)',
                'Email address of the customer finance manager.',
            ],
            [
                'penagihan_nama_kontak',
                'Optional',
                'Billing contact name',
                'Name of the accounts payable or invoice billing contact.',
            ],
            [
                'penagihan_telepon',
                'Optional',
                'Phone number (e.g. 08123456789)',
                'Phone number of the billing department.',
            ],
            [
                'penagihan_address',
                'Optional',
                'Billing address text',
                'Mailing address for physical invoices or billing documents.',
            ],
            [
                'surat_menyurat_address',
                'Optional',
                'Correspondence address text',
                'General correspondence mailing address.',
            ],
            [
                'email',
                'Optional',
                'Email (e.g. info@customer.com)',
                'General contact email of the customer.',
            ],
            [
                'tax_contact_name',
                'Optional',
                'Tax contact person name',
                'Contact person responsible for taxation matters.',
            ],
            [
                'tax_contact_email',
                'Optional',
                'Email (e.g. tax@customer.com)',
                'Email address for tax affairs.',
            ],
            [
                'tax_contact_phone',
                'Optional',
                'Phone number',
                'Phone number of the tax department.',
            ],
            [
                'npwp',
                'Optional',
                'Tax ID number (e.g. 01.234.567.8-012.000)',
                'Indonesian Tax ID (NPWP) number.',
            ],
            [
                'tanggal_npwp',
                'Optional',
                'Format: YYYY-MM-DD (e.g. 2020-01-15)',
                'NPWP registration date.',
            ],
            [
                'nppkp',
                'Optional',
                'Taxable Enterprise Number (NPPKP)',
                'VAT taxable enterprise certificate number.',
            ],
            [
                'tanggal_nppkp',
                'Optional',
                'Format: YYYY-MM-DD (e.g. 2020-01-15)',
                'NPPKP inauguration date.',
            ],
            [
                'no_pengukuhan_kaber',
                'Optional',
                'Bonded zone license number',
                'Bonded zone / customs license number (if applicable).',
            ],
            [
                'output_tax',
                'Optional',
                'PPN, NON-PPN, or Terhutang PPN',
                'Value Added Tax treatment. Options: PPN, NON-PPN, or Terhutang PPN. Default: "PPN".',
            ],
            [
                'term_of_payment',
                'Optional',
                '0, CBD, 7, 14, 30, 45, 60',
                'Payment terms in number of days. Value "0" or "CBD" signifies Cash Before Delivery. Default: "0".',
            ],
            [
                'lead_time',
                'Optional',
                'Number of days (e.g. 0, 1, 2)',
                'Estimated shipping lead time in days. Default: 0.',
            ],
            [
                'credit_limit',
                'Optional',
                'Number without dots/commas (e.g. 10000000)',
                'Approved credit limit ceiling in IDR. Default: 0.',
            ],
            [
                'ccar',
                'Optional',
                'smd_idr  OR  smd_usd',
                'Customer currency code. Options: smd_idr or smd_usd. Default: "smd_idr".',
            ],
            [
                'bank_garansi',
                'Optional',
                'YA  OR  TIDAK (or YES / NO)',
                'Whether the customer uses a Bank Guarantee facility. Default: "TIDAK".',
            ],
            [
                'area',
                'Optional',
                'Sales area (e.g. Greater Jakarta, East Java)',
                'Operational sales area or region name.',
            ],
            [
                'status',
                'Optional',
                'Active  OR  Inactive',
                'Account active status. Note: All imported customer records are automatically set to "Active" and "Approved".',
            ],
            [
                'pembagian',
                'Optional',
                'Text',
                'Segment allocation (if applicable).',
            ],
            [
                'customer_total',
                'Optional',
                'Number (e.g. 0)',
                'Initial cumulative transaction amount. Default: 0.',
            ],
            [
                'virtual_account',
                'Optional',
                'Virtual Account number',
                'Bank Virtual Account number for payments (if applicable).',
            ],
            [
                'payment_days',
                'Optional',
                'Monday,Tuesday  OR  All Days',
                'Scheduled payment days. Separate multiple days with commas (,).',
            ],
            [
                'payment_date',
                'Optional',
                '15,30',
                'Monthly scheduled payment dates. Separate multiple dates with commas (,).',
            ],
            [
                'faktur_days',
                'Optional',
                'Wednesday,Thursday  OR  All Days',
                'Scheduled invoice exchange days. Separate multiple days with commas (,).',
            ],
            [
                'faktur_date',
                'Optional',
                '10,20',
                'Monthly scheduled invoice exchange dates. Separate multiple dates with commas (,).',
            ],
            [
                'join_date',
                'Optional',
                'Format: YYYY-MM-DD (e.g. 2026-03-11)',
                'Customer official join date. Defaults to current date if left empty.',
            ],
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
                    'startColor' => ['rgb' => '374151'] // Dark Slate Gray
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
