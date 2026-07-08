<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Master Report Batch</title>
    <style>
        /* =======================================================
           GLOBAL & TYPOGRAPHY
           ======================================================= */
        @page { 
            margin: 30px 35px; 
            size: a4 portrait; 
        }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 8pt; 
            margin: 0; 
            color: #000000; /* Teks Hitam Peat */
            line-height: 1.35;
        }
        .page { 
            width: 100%; 
            page-break-after: always; 
            position: relative;
        }
        .page:last-child { 
            page-break-after: avoid; 
        }
        
        /* =======================================================
           ISO DOCUMENT HEADER (FIXED)
           ======================================================= */
        .doc-header { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px; 
            border: 1px solid #000000; 
            background-color: #ffffff; 
        }
        .header-cell { 
            border: 1px solid #000000; 
            padding: 6px 10px; 
            vertical-align: middle; 
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .logo-text { 
            font-size: 14pt; 
            font-weight: 900; 
            color: #000000; 
            margin: 0; 
            text-align: center; 
            font-family: 'Arial Black', sans-serif; 
            letter-spacing: 1px; 
        }
        .doc-title { 
            font-size: 12pt; 
            font-weight: bold; 
            text-align: center; 
            margin: 0; 
            letter-spacing: 0.5px; 
            color: #000000; 
        }
        
        /* Doc control table inside the header */
        .doc-control { 
            width: 100%; 
            /* height: 100%; Dihapus agar tidak merusak layout PDF (penyebab blank page/header panjang) */
            border-collapse: collapse; 
            font-size: 8pt; 
        }
        .doc-control td { 
            border: none; 
            padding: 4px 6px; /* Dirapetin agar tidak ada jarak berlebih */
            vertical-align: middle; 
            color: #000000; /* Warna biru sesuai gambar contoh */
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .doc-control td.label { 
            font-weight: bold; 
            width: 40%; 
        }

        /* =======================================================
           FORMAL REPORT MAIN TITLE BAR
           ======================================================= */
        .report-title-bar {
            background-color: #0f172a; 
            color: #ffffff;
            padding: 6px 12px;
            margin-bottom: 10px;
            border: 1px solid #000000;
        }
        .report-title-text {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        /* =======================================================
           STRUCTURED REPORT GRID
           ======================================================= */
        .section-title { 
            background-color: #1e3a8a; 
            color: #ffffff !important; 
            font-weight: bold; 
            font-size: 8.5pt; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            padding: 5px 8px !important;
            border: 1px solid #000000 !important;
        }
        
        table.report-grid { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed;
            border: 1px solid #000000;
            margin-bottom: 12px;
        }
        table.report-grid td {
            padding: 5px 8px;
            border: 1px solid #000000; 
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        
        /* Shaded Label Cell Style */
        .lbl-cell {
            font-weight: bold;
            font-size: 7.5pt;
            color: #000000; 
            background-color: #f1f5f9; 
            text-transform: uppercase;
            width: 13%;
        }
        /* Crisp Value Cell Style */
        .val-cell {
            font-size: 8pt;
            font-weight: 600;
            color: #000000; 
            background-color: #ffffff;
            width: 37%;
        }
        
        .lbl-cell-full { width: 18%; }
        .val-cell-full { width: 82%; }

        /* Highlight Exceptions */
        .highlight-val { color: #b91c1c; font-size: 8.5pt; font-weight: bold; }
        .highlight-email { color: #1e3a8a; font-weight: normal; }
        .highlight-code { color: #1e3a8a; font-family: monospace; font-size: 9pt; }
        
        /* =======================================================
           DATA & LIST TABLES
           ======================================================= */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 1px solid #000000;
            margin-top: 2px;
        }
        table.data-table th { 
            background-color: #334155; 
            color: #ffffff; 
            font-weight: bold; 
            text-align: left; 
            padding: 6px 8px; 
            font-size: 7.5pt; 
            text-transform: uppercase; 
            border: 1px solid #000000;
        }
        table.data-table td { 
            padding: 5px 8px; 
            font-size: 8pt; 
            color: #000000;
            border: 1px solid #000000;
            vertical-align: middle;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        table.data-table tbody tr:nth-child(even) td { 
            background-color: #f8fafc; 
        }
        
        /* =======================================================
           BADGES
           ======================================================= */
        .status-badge { 
            padding: 3px 6px; 
            border-radius: 3px; 
            font-size: 7pt; 
            font-weight: bold; 
            text-align: center; 
            display: inline-block;
        }
        .bg-approved { background-color: #dcfce7; color: #166534; }
        .bg-rejected { background-color: #fee2e2; color: #991b1b; }
        .bg-pending { background-color: #fef3c7; color: #92400e; }
        
        /* =======================================================
           UTILITIES
           ======================================================= */
        .watermark-draft {
            position: absolute; top: 35%; left: 12%; font-size: 110pt; color: rgba(0, 0, 0, 0.05);
            transform: rotate(-45deg); z-index: -1; font-weight: 900; text-transform: uppercase; letter-spacing: 12px;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    @php
        $formatArrayData = function($data) {
            if (empty($data) || $data === 'null') return '-';
            if (is_string($data)) {
                $decoded = json_decode($data, true);
                if (is_array($decoded)) return implode(', ', $decoded);
                return trim($data, '[]"');
            }
            if (is_array($data)) return implode(', ', $data);
            return '-';
        };
    @endphp

    @foreach($customers as $customer)
    <div class="page">
        
        @if($customer->status_approval !== 'Approved' && $customer->status_approval !== 'Completed')
            <div class="watermark-draft">DRAFT</div>
        @endif

        {{-- 1. ISO DOCUMENT HEADER --}}
        <table class="doc-header">
            <tr>
                <td class="header-cell" style="width: 30%; text-align: center; vertical-align: middle;">
                    <div style="white-space: nowrap;">
                        <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="height: 32px; vertical-align: middle; margin-right: 6px;">
                        <h1 class="logo-text" style="display: inline; vertical-align: middle;">SINAR MEADOW</h1>
                    </div>
                </td>
                
                <td class="header-cell" style="width: 45%; text-align: center;">
                    <h2 class="doc-title">CUSTOMER SELECTION FORM</h2>
                </td>
                <td class="header-cell" style="width: 25%; padding: 0; vertical-align: top;">
                    <table class="doc-control">
                        <tr>
                            <td class="label" style="border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Doc No.</td>
                            <td style="border-bottom: 1px solid #000000; font-weight: bold;">: F/C.1.5-02</td>
                        </tr>
                        <tr>
                            <td class="label" style="border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Revision</td>
                            <td style="border-bottom: 1px solid #000000;">: {{ $revision->revision_count ?? '1' }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border-right: 1px solid #000000;">Dated</td>
                            <td>: {{ $revision->revision_date ? \Carbon\Carbon::parse($revision->revision_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- 3. STRUCTURED REPORT GRID --}}
        <table class="report-grid">
            <tbody>
                {{-- SECTION 1 --}}
                <tr>
                    <td colspan="4" class="section-title">1. GENERAL DATA</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Customer Name:</td>
                    <td class="val-cell" style="font-size: 9pt; font-weight: 800;">{{ strtoupper($customer->name ?? '-') }}</td>
                    <td class="lbl-cell">Customer Code:</td>
                    <td class="val-cell highlight-code">{{ strtoupper($customer->code ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">No PKD:</td>
                    <td class="val-cell">{{ strtoupper($customer->no_pkd ?? '-') }}</td>
                    <td class="lbl-cell">Short / Sort Name:</td>
                    <td class="val-cell">{{ strtoupper($customer->sort_name ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Region / Area:</td>
                    <td class="val-cell">{{ strtoupper($customer->area ?? '-') }}</td>
                    <td class="lbl-cell">Customer Class:</td>
                    <td class="val-cell">{{ $customer->customerClass ? strtoupper($customer->customerClass->name_class) : strtoupper($customer->customer_class ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Account Group:</td>
                    <td class="val-cell">{{ $customer->accountGroup ? strtoupper($customer->accountGroup->name_account_group) : strtoupper($customer->account_group ?? '-') }}</td>
                    <td class="lbl-cell">Join Date:</td>
                    <td class="val-cell">{{ $customer->join_date ? \Carbon\Carbon::parse($customer->join_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Account Status:</td>
                    <td class="val-cell" colspan="3">
                        <span class="font-bold" style="font-size: 7.5pt;">{{ strtoupper($customer->status ?? '-') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">Main Office Address</td>
                    <td class="val-cell" colspan="3">
                        <span style="font-weight: bold;">{{ strtoupper($customer->address1 ?? '-') }}
                        {{ trim($customer->address2 . ' ' . $customer->address3 ?? '-') }}<br></span>
                        <span style="font-weight: normal;">
                            <strong>City:</strong> {{ $customer->city ?? '-' }} &nbsp;|&nbsp; 
                            <strong>Postal Code:</strong> {{ $customer->postal_code ?? '-' }} &nbsp;|&nbsp; 
                            <strong>Country:</strong> {{ strtoupper($customer->country ?? '-') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">Mailing & Corresp. Address</td>
                    <td class="val-cell" colspan="3" style="font-weight: normal;">{{ $customer->surat_menyurat_address ?? 'Sesuai dengan Alamat Utama (Main Office Address)' }}</td>
                </tr>

                {{-- SECTION 2 --}}
                <tr>
                    <td colspan="4" class="section-title">2. MANAGEMENT, KEY CONTACTS & ADDRESSES</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Primary PIC Name:</td>
                    <td class="val-cell">{{ strtoupper($customer->pic ?? '-') }}</td>
                    <td class="lbl-cell">Company Email:</td>
                    <td class="val-cell highlight-email">{{ strtolower($customer->email ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Purchasing Manager:</td>
                    <td class="val-cell" style="font-weight: normal;">
                        <strong>Name:</strong> <span style="font-weight: bold;">{{ strtoupper($customer->purchasing_manager_name ?? '-') }}</span><br>
                        <strong>Phone:</strong> {{ $customer->purchasing_manager_telepon ?? '-' }}<br>
                        <strong>Email:</strong> <span class="highlight-email">{{ strtolower($customer->purchasing_manager_email ?? '-') }}</span>
                    </td>
                    <td class="lbl-cell">Finance / Accounting:</td>
                    <td class="val-cell" style="font-weight: normal;">
                        <strong>Name:</strong> <span style="font-weight: bold;">{{ strtoupper($customer->finance_manager_name ?? '-') }}</span><br>
                        <strong>Phone:</strong> {{ $customer->finance_manager_telepon ?? '-' }}<br>
                        <strong>Email:</strong> <span class="highlight-email">{{ strtolower($customer->finance_manager_email ?? '-') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">Billing / Penagihan Info</td>
                    <td class="val-cell" colspan="3" style="font-weight: normal;">
                        <strong>Contact Name:</strong> <span style="font-weight: bold;">{{ strtoupper($customer->penagihan_nama_kontak ?? '-') }}</span> &nbsp;|&nbsp; 
                        <strong>Phone:</strong> <span style="font-weight: bold;">{{ $customer->penagihan_telepon ?? '-' }}</span><br>
                        <div style="margin-top: 2px;"><strong>Billing Address:</strong> {{ strtoupper($customer->penagihan_address ?? '-') }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">Shipping / Delivery To</td>
                    <td class="val-cell" colspan="3" style="font-weight: normal;">
                        <strong>Name:</strong> <span style="font-weight: bold;">{{ strtoupper($customer->shipping_to_name ?? '-') }}</span><br>
                        <div style="margin-top: 2px;"><strong>Shipping Address:</strong> {{ strtoupper($customer->shipping_to_address ?? '-') }}</div>
                    </td>
                </tr>

                {{-- SECTION 3 --}}
                <tr>
                    <td colspan="4" class="section-title">3. FINANCIAL & TAX INFORMATION</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Tax ID (NPWP:)</td>
                    <td class="val-cell">
                        <span style="font-weight: 700;">{{ $customer->npwp ?? '-' }}</span><br>
                        <span style="font-weight: normal;">Reg Date: {{ $customer->tanggal_npwp ? \Carbon\Carbon::parse($customer->tanggal_npwp)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                    </td>
                    <td class="lbl-cell">NPPKP / PKP:</td>
                    <td class="val-cell">
                        <span style="font-weight: 700;">{{ $customer->nppkp ?? '-' }}</span><br>
                        <span style="font-weight: normal;">Reg Date: {{ $customer->tanggal_nppkp ? \Carbon\Carbon::parse($customer->tanggal_nppkp)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">Other Tax Info:</td>
                    <td class="val-cell" style="font-weight: normal;">
                        <strong>Pengukuhan Kaber:</strong> {{ $customer->no_pengukuhan_kaber ?? '-' }}<br>
                        <strong>Output Tax:</strong> <span style="font-weight: 600;">{{ strtoupper($customer->output_tax ?? 'PPN') }}</span>
                    </td>
                    <td class="lbl-cell">Tax Contact Person:</td>
                    <td class="val-cell" style="font-weight: normal;">
                        <strong>Name:</strong> <span style="font-weight: bold;">{{ strtoupper($customer->tax_contact_name ?? '-') }}</span><br>
                        <strong>Phone:</strong> {{ $customer->tax_contact_phone ?? '-' }}<br>
                        <strong>Email:</strong> <span class="highlight-email">{{ strtolower($customer->tax_contact_email ?? '-') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">TOP & Lead Time:</td>
                    <td class="val-cell">
                        <strong>Term of Payment:</strong> {{ $customer->term_of_payment ?? '-' }} Days<br>
                        <strong>Lead Time:</strong> {{ $customer->lead_time ?? '-' }} Days
                    </td>
                    <td class="lbl-cell">Credit Limit & CCAR:</td>
                    <td class="val-cell">
                        <div class="highlight-val">IDR {{ number_format($customer->credit_limit ?? 0, 0, ',', '.') }}</div>
                        <div style="font-weight: normal; margin-top: 2px;"><strong>CCAR:</strong> {{ strtoupper($customer->ccar ?? '-') }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="lbl-cell">Bank Garansi (BG:)</td>
                    <td class="val-cell" colspan="3">{{ strtoupper($customer->bank_garansi ?? 'TIDAK') }}</td>
                </tr>

                {{-- SECTION 4 --}}
                <tr>
                    <td colspan="4" class="section-title">4. BILLING, PAYMENT & INVOICE SCHEDULE</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Virtual Account (VA:)</td>
                    <td class="val-cell highlight-code" style="font-weight: bold; font-size: 8.5pt;">{{ $customer->virtual_account ?? '-' }}</td>
                    <td class="lbl-cell">Payment Proc. Days:</td>
                    <td class="val-cell" style="font-weight: normal;">{{ $formatArrayData($customer->payment_days) }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Payment Dates (Tgl:)</td>
                    <td class="val-cell" style="font-weight: normal;">{{ $formatArrayData($customer->payment_date) }}</td>
                    <td class="lbl-cell">Faktur / Invoice Days:</td>
                    <td class="val-cell" style="font-weight: normal;">{{ $formatArrayData($customer->faktur_days) }}</td>
                </tr>
                <tr>
                    <td class="lbl-cell">Faktur Dates (Tgl:)</td>
                    <td class="val-cell" colspan="3" style="font-weight: normal;">{{ $formatArrayData($customer->faktur_date) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- REGISTERED ITEMS SECTION --}}
        @if($customer->items && $customer->items->count() > 0)
        <div style="background-color: #1e3a8a; color: white; font-weight: bold; font-size: 8.5pt; text-transform: uppercase; padding: 5px 8px; border: 1px solid #000000; letter-spacing: 0.5px;">
            5. REGISTERED CUSTOMER ITEMS & PRICING
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 20%;">Item Code</th>
                    <th style="width: 45%;">Item Description / Name</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 20%; text-align: right;">Unit Price (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalItemAmount = 0; @endphp
                @foreach($customer->items as $idx => $item)
                    @php $totalItemAmount += ($item->quantity * $item->price); @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td class="highlight-code" style="font-weight: bold;">{{ $item->item_code ?? '-' }}</td>
                        <td style="font-weight: 600; text-transform: uppercase;">{{ $item->item_name }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($item->price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-size: 8pt; font-weight: bold; background-color: #f1f5f9; border-top: 1.5px solid #000000;">TOTAL ESTIMATED VALUE:</td>
                    <td style="text-align: right; font-size: 9pt; background-color: #f1f5f9; border-top: 1.5px solid #000000;" class="highlight-val">IDR {{ number_format($totalItemAmount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- ========================================================================= --}}
        {{-- HALAMAN BARU KHUSUS UNTUK LOG APPROVAL --}}
        {{-- ========================================================================= --}}
        <div class="page-break"></div>

        <table class="doc-header">
            <tr>
                <td class="header-cell" style="width: 30%; text-align: center; vertical-align: middle;">
                    <div style="white-space: nowrap;">
                        <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="height: 32px; vertical-align: middle; margin-right: 6px;">
                        <h1 class="logo-text" style="display: inline; vertical-align: middle;">SINAR MEADOW</h1>
                    </div>
                </td>
                <td class="header-cell" style="width: 45%; text-align: center; vertical-align: middle; padding: 8px 5px;">
                    <div style="font-size: 10pt; color: #000000; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 3px;">
                        Approval Status
                    </div>
                    <div style="font-size: 11.5pt; font-weight: 900; color: #1e3a8a; letter-spacing: 0.5px; line-height: 1.2;">
                        {{ strtoupper($customer->name) }}
                    </div>
                </td>
                <td class="header-cell" style="width: 25%; padding: 0; vertical-align: top;">
                    <table class="doc-control">
                        <tr>
                            <td class="label" style="border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Doc No.</td>
                            <td style="border-bottom: 1px solid #000000; font-weight: bold;">: F/C.1.5-02</td>
                        </tr>
                        <tr>
                            <td class="label" style="border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Revision</td>
                            <td style="border-bottom: 1px solid #000000;">: {{ $revision->revision_count ?? '1' }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border-right: 1px solid #000000;">Dated</td>
                            <td>: {{ $revision->revision_date ? \Carbon\Carbon::parse($revision->revision_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%; text-align: center;">Step</th>
                    <th style="width: 24%;">Approver Name</th>
                    <th style="width: 22%;">Role / Designation</th>
                    <th style="width: 14%; text-align: center;">Decision</th>
                    <th style="width: 14%; text-align: center;">Timestamp</th>
                    <th style="width: 18%;">Remarks / Notes</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($customer->logs) && $customer->logs->count() > 0)
                    @foreach($customer->logs as $log)
                        @php
                            $statusClass = 'bg-pending';
                            $statusText = 'WAITING';
                            
                            if ($log->status === 'Approved') { 
                                $statusText = 'APPROVED'; $statusClass = 'bg-approved'; 
                            } elseif ($log->status === 'Rejected') { 
                                $statusText = 'REJECTED'; $statusClass = 'bg-rejected'; 
                            }

                            $roleNames = $log->approver && $log->approver->roles ? $log->approver->roles->pluck('name')->toArray() : [];
                            $roleDisplay = !empty($roleNames) ? implode(', ', $roleNames) : 'Approver Level ' . $log->level;
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold; color: #000000;">{{ $log->level }}</td>
                            <td><strong>{{ strtoupper($log->approver ? $log->approver->name : $log->approver_nik) }}</strong></td>
                            <td style="font-size: 7.5pt;">{{ strtoupper($roleDisplay) }}</td>
                            <td style="text-align: center;">
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td style="text-align: center; font-size: 7.5pt;">
                                {{ $log->updated_at ? \Carbon\Carbon::parse($log->updated_at)->locale('id')->translatedFormat('d F Y - H:i') : '-' }}
                            </td>
                            <td style="font-size: 7.5pt; font-style: italic;">{{ $log->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; font-style: italic;">
                            No workflow or approval log records found for this document.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
    @endforeach

</body>
</html>