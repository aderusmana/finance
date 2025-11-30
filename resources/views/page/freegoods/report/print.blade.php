<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    {{-- [DIUBAH] Judul dinamis untuk Free Goods --}}
    <title>FG FREE GOODS - {{ $requisitions->pluck('no_srs')->join(', ') }}</title>
    <style>
        /* CSS Sebagian besar tetap sama karena generik */
        @page { margin: 0.5cm; }
        body { font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size: 10pt; margin: 0; }
        .page { width: 93%; padding: 1cm; }
        .page-break { page-break-before: always; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 2px 3px; vertical-align: middle; word-wrap: break-word; }
        .bordered, .bordered th, .bordered td { border: 1px solid black; }
        .no-border, .no-border tr, .no-border td { border: none; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .header-info { font-size: 8pt; }
        .main-title { font-size: 16pt; }
        .sub-title { font-size: 12pt; }
        .notes-column { vertical-align: top; }
        .status-indicator { padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; display: inline-block; text-align: center; border-width: 1.5px; border-style: solid; background-color: transparent !important; }
        .status-approved { border-color: #28a745; color: #28a745; }
        .status-review { border-color: #E8A903; color: #E8A903; }
        .status-rejected { border-color: #dc3545; color: #dc3545; }
        .status-pending { border-color: #6c757d; color: #6c757d; }
        .striped-table thead th { background-color: #ffffffff; border-bottom: 1px solid #333; font-size: 10pt; padding: 5px 3px; text-transform: uppercase; }
        .striped-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .striped-table td { padding: 4px 6px; }
    </style>
</head>

<body>
    {{-- [DITAMBAHKAN] Loop untuk mencetak setiap requisition yang dipilih --}}
    @foreach($requisitions as $requisition)
        {{-- ======================================================= --}}
        {{-- ========= HALAMAN 1: SLIP REQUISITION UTAMA ========= --}}
        {{-- ======================================================= --}}
        <div class="page">
            <table class="bordered">
                <tr>
                    <td style="padding: 10px;">
                        {{-- HEADER --}}
                        <table class="no-border">
                            <tr>
                                <td style="width: 25%;">
                                    <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="max-height: 40px; vertical-align: middle;">
                                    <span style="font-family: 'Times New Roman', Times, serif; font-size: 15pt; font-weight: bold; vertical-align: middle;">
                                        SINAR MEADOW
                                    </span>
                                </td>
                                <td style="width: 50%;" class="text-center">
                                    {{-- [DIUBAH] Judul disesuaikan untuk Free Goods --}}
                                    <div class="font-bold main-title" style="margin-bottom: 4px;">REQUISITION SLIP</div>
                                    <div class="sub-title" style="margin-bottom: 1px;">FREE GOODS</div>
                                </td>
                                {{-- [DIHAPUS] Kotak Form No./Revisi dihapus --}}
                                <td style="width: 25%;"></td>
                            </tr>
                        </table>

                        <div style="height: 20px;"></div>

                        {{-- CUSTOMER & FG INFO --}}
                        <table class="no-border" style="vertical-align: top;">
                            <tr>
                                {{-- Info Customer --}}
                                <td style="width: 50%; vertical-align: top;">
                                    <table class="no-border">
                                        <tr>
                                            <td style="white-space: nowrap; padding-right: 5px;"><strong>CUSTOMER NAME</strong></td>
                                            <td>: {{ $requisition->customer->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: top; white-space: nowrap; padding-right: 5px;"><strong>ADDRESS</strong></td>
                                            <td style="vertical-align: top;">: {{ $requisition->customer->address ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </td>
                                {{-- Info FG --}}
                                <td style="width: 50%; vertical-align: top;">
                                    <table class="no-border" style="float: right;">
                                        <tr>
                                            <td style="text-align: right; padding-right: 5px;"><strong>Account</strong></td>
                                            <td>: {{ $requisition->account ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; padding-right: 5px;"><strong>Cost Center</strong></td>
                                            <td>: {{ $requisition->cost_center ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; padding-right: 5px;"><strong>Tanggal</strong></td>
                                            <td>: {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            {{-- [DIUBAH] "Nomor RS" menjadi "Nomor FG" --}}
                                            <td style="text-align: right; padding-right: 5px;"><strong>Nomor FG</strong></td>
                                            <td>: <strong style="font-size: 14pt;">{{ $requisition->no_srs }}</strong></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <div style="height: 10px;"></div>

                        <table class="bordered">
                            <thead>
                                <tr>
                                    {{-- [DIUBAH] Kolom disederhanakan untuk Free Goods --}}
                                    <th style="width: 15%;">PRODUCT CODE</th>
                                    <th>PRODUCT NAME</th>
                                    <th style="width: 8%;">UNIT</th>
                                    <th style="width: 8%;">QTY REQUIRED</th>
                                    <th style="width: 8%;">QTY ISSUED</th>
                                    <th style="width: 15%;">OBJECTIVES</th>
                                    <th style="width: 15%;">Estimasi Potensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $itemCount = $requisition->requisitionItems->count();
                                    $minRows = 15;
                                    $totalRows = max($itemCount, $minRows);
                                @endphp
                                @foreach($requisition->requisitionItems as $item)
                                <tr>
                                    {{-- [DIUBAH] Struktur disederhanakan --}}
                                    <td class="text-center">{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                    <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->itemMaster->unit ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity_required }}</td>
                                    <td class="text-center">{{ $item->quantity_issued }}</td>
                                    @if($loop->first)
                                        <td class="notes-column" rowspan="{{ $totalRows }}">{{ $requisition->objectives }}</td>
                                        <td class="notes-column" rowspan="{{ $totalRows }}">{{ $requisition->estimated_potential }}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @for ($i = $itemCount; $i < $minRows; $i++)
                                <tr>
                                    <td>&nbsp;</td> <td></td> <td></td> <td></td> <td></td>
                                    @if($itemCount == 0 && $i == 0)
                                        <td class="notes-column" rowspan="{{ $totalRows }}">{{ $requisition->objectives }}</td>
                                        <td class="notes-column" rowspan="{{ $totalRows }}">{{ $requisition->estimated_potential }}</td>
                                    @endif
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- =================================================================== --}}
        {{-- ========= HALAMAN 2: KHUSUS JIKA TIPE SPECIAL ORDER (DIHAPUS) ========= --}}
        {{-- =================================================================== --}}
        {{-- Bagian ini telah dihapus seluruhnya --}}

        {{-- ======================================================= --}}
        {{-- ============== HALAMAN 2: APPROVAL STATUS ============= --}}
        {{-- ======================================================= --}}
        <div class="page page-break">
            <table class="bordered">
                <tr>
                    <td style="padding: 10px;">
                        <table class="no-border" style="margin-bottom: 20px;">
                            <tr>
                                <td style="width: 25%;">
                                    <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="max-height: 40px; vertical-align: middle;">
                                    <span style="font-family: 'Times New Roman', Times, serif; font-size: 15pt; font-weight: bold; vertical-align: middle;">
                                        SINAR MEADOW
                                    </span>
                                </td>
                                <td style="width: 60%;" class="text-center">
                                    {{-- [DIUBAH] Judul disesuaikan --}}
                                    <div class="font-bold main-title" style="margin-bottom: 4px;">REQUISITION SLIP STATUS</div>
                                    <div class="sub-title">FREE GOODS: <strong>{{ $requisition->no_srs }}</strong></div>
                                </td>
                                <td style="width: 25%;">
                                    <table class="no-border" style="table-layout: auto; width: auto;">
                                        <tr>
                                            <td style="white-space: nowrap; padding-right: 3px;"><strong>Requester</strong></td>
                                            <td style="width: 5px;">:</td>
                                            {{-- [DIUBAH] Mengambil relasi dari objek requisition saat ini --}}
                                            <td>{{ $requisition->requester->name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="white-space: nowrap; padding-right: 3px;"><strong>Dept</strong></td>
                                            <td style="width: 5px;">:</td>
                                            <td>{{ $requisition->requester->department->name }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table class="bordered striped-table">
                            <thead>
                                <tr>
                                    <th style="width: 17%;">PCC Name</th>
                                    <th style="width: 15%;">PCC Position</th>
                                    <th style="width: 13%;">Status</th>
                                    <th style="width: 13%;">Tanggal Approved</th>
                                    <th>Notes / Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- [DIUBAH] Mengambil data approval dari relasi --}}
                                @php
                                    $approvals = $requisition->approvals ?? collect();
                                @endphp
                                @forelse($approvals as $approval)
                                <tr>
                                    <td>{{ $approval->approver->name ?? '-' }}</td>
                                    <td>{{ $approval->approver->position ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($approval->status == 'Approved')
                                            <span class="status-indicator status-approved">APPROVED</span>
                                        @elseif($approval->status == 'Review')
                                            <span class="status-indicator status-review">REVIEW</span>
                                        @elseif($approval->status == 'Rejected')
                                            <span class="status-indicator status-rejected">REJECTED</span>
                                        @else
                                            <span class="status-indicator status-pending">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($approval->updated_at && $approval->status != 'Pending')
                                            {{ \Carbon\Carbon::parse($approval->updated_at)->format('d M Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $approval->notes ?? '' }}</td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center" style="padding: 20px;">No approval history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Menambahkan page break jika ini bukan requisition terakhir dalam batch --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>