<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        @php
            $reportCount = isset($requisitions) ? count($requisitions) : 1;
            $firstReport = isset($requisitions) ? $requisitions->first() : $requisition;
        @endphp
        @if($reportCount > 1)
            Bulk RS Complain Reports - {{ $reportCount }} Reports
        @else
            RS SAMPLE {{ $firstReport->sub_category }} - {{ $firstReport->no_srs }}
        @endif
    </title>
    <style>
        @page { margin: 0.5cm; }
        /* [DIUBAH] Ukuran font dasar dikecilkan lagi */
        body { font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size: 10pt; margin: 0; }
        .page { width: 93%; padding: 1cm; }
        .page-break { page-break-before: always; }
        
        /* CSS khusus untuk bulk report */
        .bulk-page-break {
            page-break-before: always;
        }
        
        /* Pastikan setiap report dimulai di halaman baru */
        .report-section {
            page-break-after: always;
        }
        
        .report-section:last-child {
            page-break-after: auto;
        }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th {
            /* [DIUBAH] Padding dikecilkan lagi */
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .bordered, .bordered th, .bordered td { border: 1px solid black; }
        .no-border, .no-border tr, .no-border td { border: none; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        tfoot th,
        tfoot td {
            text-align: center;
            vertical-align: top;
            padding: 5px 3px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
        }

        /* Garis atas judul tanda tangan */
        tfoot tr.signature-box th {
            border-top: 1px solid #000;
            border-bottom: none;
            padding: 3px 0;
        }

        /* Baris tanda tangan */
        tfoot tr.signature-row td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding-top: 10px;
            padding-bottom: 0;
        }

        /* khusus kolom Requester dan Issued (biar turun ke bawah) */
        tfoot tr.signature-row td:first-child,
        tfoot tr.signature-row td:nth-child(5) {
            vertical-align: bottom; /* turunkan posisi teks ke bawah */
            padding-top: 60px;       /* tambahkan jarak agar sejajar dengan Approved */
        }

        /* Nama tanda tangan */
        tfoot tr.signature-row td .name-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 12px;
            padding-bottom: 1px;
        }

        /* Baris jabatan */
        tfoot tr.title-row td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding-top: 0;
            padding-bottom: 2px;
            font-size: 12px;
        }

        /* Table Approved (tanpa garis tengah) */
        .approver-table {
            width: 100%;
            border-collapse: collapse;
        }
        .approver-table td {
            border: none !important;
            padding: 0;
            text-align: center;
        }

        .header-info { font-size: 8pt; }
        .main-title { font-size: 16pt; }
        .sub-title { font-size: 12pt; }
        .notes-column {
            vertical-align: top;
        }

        table.outer {
            font-family: 'Times New Roman', Times, serif;
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 12px;
            margin: 0 auto;
        }

        table.outer td,
        table.outer th {
            border: 1px solid black;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* PERUBAHAN DI SINI: Menambahkan beberapa class baru */
        .section-header td {
            border-left: none;
            border-right: none;
            border-top: none;
            background-color: #d6d0d0ff; /* Menambahkan background abu-abu seperti template */
        }

        .signature-title-row td {
            border-bottom: none;
            border-left: none;
            border-right: none;
        }

        .signature-name-row td {
            border: none;
        }

        /* [FINAL] Penyempurnaan CSS untuk Status & Word Wrapping */
        .status-indicator {
            padding: 3px 10px; /* Padding diatur untuk bentuk pil */
            border-radius: 12px; /* Dibuat lebih bulat */
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            border-width: 1.5px;
            border-style: solid;
            background-color: transparent !important; /* Latar belakang dihilangkan */
        }

        .status-approved {
            border-color: #28a745; /* Warna border hijau */
            color: #28a745;       /* Warna teks hijau */
        }

        .status-review {
            border-color: #E8A903; /* Warna border kuning/oranye */
            color: #E8A903;       /* Warna teks kuning/oranye */
        }

        .status-rejected {
            border-color: #dc3545; /* Warna border merah */
            color: #dc3545;       /* Warna teks merah */
        }

        .status-pending {
            border-color: #6c757d; /* Warna border abu-abu */
            color: #6c757d;       /* Warna teks abu-abu */
        }

        /* Style untuk membuat tabel lebih menarik */
        .striped-table thead th {
            background-color: #ffffffff;
            border-bottom: 1px solid #333;
            font-size: 10pt; /* Menambah ukuran font header */
            padding: 5px 3px; /* Menambah sedikit padding vertikal */
            text-transform: uppercase; /* Membuat teks menjadi kapital semua */
        }

        .striped-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* [DIUBAH] Menambahkan word-wrap & vertical-align */
        .striped-table td {
            padding: 4px 6px;
            word-wrap: break-word; /* INI SOLUSINYA: Memaksa teks untuk wrap */
            vertical-align: middle; /* Membuat konten di tengah secara vertikal */
        }
    </style>
</head>

<body>
    @php
        $reports = isset($requisitions) ? $requisitions : [$requisition];
    @endphp

    @foreach($reports as $index => $currentRequisition)
        @if($index > 0)
            <div style="page-break-before: always;"></div>
        @endif

        @php
            $approvals = $currentRequisition->approvalLogs->map(function ($log) {
                $statusText = 'NOT REVIEWED';
                if ($log->status === 'Approved' && !empty($log->notes) && $log->notes !== 'Approved by ' . ($log->approver ? $log->approver->name : '')) {
                    $statusText = 'APPROVED WITH REVIEW';
                } elseif ($log->status === 'Approved') {
                    $statusText = 'APPROVED NOT REVIEW';
                } elseif ($log->status === 'Rejected') {
                    $statusText = 'NOT APPROVED';
                }
                
                // Get role display
                $roleNames = $log->approver && $log->approver->roles ? $log->approver->roles->pluck('name')->toArray() : [];
                $roleDisplay = !empty($roleNames) ? implode(', ', $roleNames) : ($log->level ? $log->level : 'Unknown');
                
                return (object) [
                    'name' => $log->approver ? $log->approver->name : 'Unknown',
                    'position' => $roleDisplay,
                    'status' => $statusText,
                    'approved_at' => $log->updated_at,
                    'notes' => $log->notes
                ];
            });
            
            $requester = $currentRequisition->requester ? $currentRequisition->requester : (object) ['name' => 'Unknown', 'department' => (object) ['name' => 'Unknown']];
        @endphp

        {{-- ======================================================= --}}
        {{-- ========= HALAMAN 1: TEMPLATE UTAMA (SEMUA TIPE) ========= --}}
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
                            <td style="width: 60%;" class="text-center">
                                <div class="font-bold main-title" style="margin-bottom: 4px;">REQUISITION SLIP</div>
                                <div class="sub-title" style="margin-bottom: 1px;">SALES & MARKETING</div>
                                <div class="sub-title">PACKAGING REPLACEMENT</div>
                            </td>
                            <td style="width: 25%;" class="header-info">
                                <table class="bordered" style="width: 100%;">
                                    <tr>
                                        <td><strong>FORM NO.:</strong></td>
                                        <td>{{ $revision ? $revision->revision_count : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>REVISION:</strong></td>
                                        <td>{{ $revision ? $revision->revision_number : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>DATE:</strong></td>
                                        <td>{{ $revision ? $revision->revision_date : '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div style="height: 20px;"></div>

                    {{-- CUSTOMER & RS INFO --}}
                    <table class="no-border" style="vertical-align: top;">
                        <tr>
                            <td style="width: 16%; vertical-align: top; padding-right: 30px;">
                                {{-- Tabel untuk info customer --}}
                                <table class="no-border">
                                    <tr>
                                        <td style="text-align: left; padding-right: 5px;"><strong>CUSTOMER NAME</strong></td>
                                        <td style="vertical-align: top; white-space: nowrap;">: {{ $currentRequisition->customer->name ?? '-' }}</td>
                                    </tr>
                                    <br>
                                    <tr>
                                        <td style="vertical-align: top; text-align: left; padding-right: 5px;"><strong>ADDRESS</strong></td>
                                        <td style="vertical-align: top; white-space: nowrap; width: 100%;">: {{ $currentRequisition->customer->address ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 40%; vertical-align: top; padding-left: 483px;">
                                {{-- Tabel untuk info RS --}}
                                <table class="no-border">
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Account</strong></td>
                                        <td>: {{ $currentRequisition->account ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Cost Center</strong></td>
                                        <td>: {{ $currentRequisition->cost_center ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($currentRequisition->approved_at)->format('d F Y') }}</td>
                                    </tr>
                                    <br>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Nomor RS</strong></td>
                                        <td>: <strong style="font-size: 14pt;">{{ $currentRequisition->no_srs }}</strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- PRODUCT ITEM -->
                    <table class="bordered">
                        <thead>
                            <tr>
                                {{-- [BARU] Tampilkan kolom Material Type jika sub category adalah Packaging --}}
                                @if($currentRequisition->sub_category == 'Packaging')
                                    <th style="width: 12%;">MATERIAL TYPE</th>
                                    <th style="width: 12%;">PRODUCT CODE</th>
                                @else
                                    <th style="width: 15%;">PRODUCT CODE</th>
                                @endif
                                <th>PRODUCT NAME</th>
                                <th style="width: 8%;">UNIT</th>
                                <th style="width: 8%;">QTY REQUIRED</th>
                                <th style="width: 8%;">QTY ISSUED</th>
                                <th style="width: 15%;">OBJECTIVES</th>

                                @if($currentRequisition->category == 'Complain')
                                <th style="width: 15%;">Remarks <br> (Batch Code)</th>
                                @else
                                <th style="width: 15%;">Estimasi Potensi (Remarks in Carton)</th>
                                @endif
                            
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $itemCount = $currentRequisition->requisitionItems->count();
                                $minRows = 15;
                                $totalRows = max($itemCount, $minRows);
                            @endphp

                            @foreach($currentRequisition->requisitionItems as $item)
                            <tr>
                                {{-- [MODIFIKASI] Logika untuk menampilkan data berdasarkan sub_category --}}
                                @if($currentRequisition->sub_category == 'Packaging')
                                    <td class="text-center">{{ $item->material_type ?? '-' }}</td>
                                    <td class="text-center">{{ $item->itemDetail->item_detail_code ?? '-' }}</td>
                                    <td class="text-center">{{ $item->itemDetail->item_detail_name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->itemDetail->unit ?? '-' }}</td>
                                @else
                                    {{-- Ini adalah blok untuk 'Finished Goods' & 'Special Order' --}}
                                    <td class="text-center">{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                    <td class="text-center">{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->itemMaster->unit ?? '-' }}</td>
                                @endif

                                <td class="text-center">{{ $item->quantity_required }}</td>
                                <td class="text-center">{{ $item->quantity_issued }}</td>

                                {{-- Kolom Objectives dan Estimasi tetap sama, digabung dengan rowspan --}}
                                @if($loop->first)
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $currentRequisition->objectives }}</td>
                                @endif
                                <td class="text-center">{{ $item->batch_number ? strtoupper($item->batch_number->format('d M y')) : '-' }} . {{ $item->remarks ?? '-'}}</td>
                            </tr>
                            @endforeach

                            {{-- Logika untuk baris kosong tetap sama --}}
                            @for ($i = $itemCount; $i < $minRows; $i++)
                            <tr>
                                @if($currentRequisition->sub_category == 'Packaging')
                                    <td>&nbsp;</td> {{-- Kolom ekstra untuk Material Type --}}
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                @else
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                @endif

                                {{-- Pastikan kolom rowspan hanya dirender sekali jika tidak ada item sama sekali --}}
                                @if($itemCount == 0 && $i == 0)
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $currentRequisition->objectives }}</td>
                                @endif
                                <td>&nbsp;</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ======================================================= --}}
    {{-- ============== HALAMAN 3: APPROVAL STATUS ============= --}}
    {{-- ======================================================= --}}
    <div class="page page-break">
        {{-- [DIUBAH] Menambahkan table luar agar konsisten dengan halaman 1 --}}
        <table class="bordered">
            <tr>
                <td style="padding: 10px;">
                    {{-- [DIUBAH] Header baru yang lebih profesional --}}
                    <table class="no-border" style="margin-bottom: 20px;">
                        <tr>
                            <td style="width: 25%;">
                                <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="max-height: 40px; vertical-align: middle;">
                                <span style="font-family: 'Times New Roman', Times, serif; font-size: 15pt; font-weight: bold; vertical-align: middle;">
                                    SINAR MEADOW
                                </span>
                            </td>
                            <td style="width: 60%;" class="text-center">
                                <div class="font-bold main-title" style="margin-bottom: 4px;">REQUISITION SLIP STATUS</div>
                                <div class="sub-title">SAMPLE PRODUCT: <strong>{{ $currentRequisition->no_srs }}</strong></div>
                            </td>
                            <td style="width: 25%;">
                                <table class="no-border" style="table-layout: auto; width: auto;">
                                    <tr>
                                        {{-- KOLOM JUDUL: Dibuat agar lebarnya pas dengan teksnya --}}
                                        <td style="white-space: nowrap; padding-right: 3px;"><strong>Requester</strong></td>

                                        {{-- KOLOM TITIK DUA: Dibuat sempit --}}
                                        <td style="width: 5px;">:</td>

                                        {{-- KOLOM ISI --}}
                                        <td>{{ $requester->name ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="white-space: nowrap; padding-right: 3px;"><strong>Dept</strong></td>
                                        <td style="width: 5px;">:</td>
                                        <td>{{ $requester->department->name ?? 'Unknown' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- [DIUBAH] Menambahkan class 'striped-table' untuk efek belang --}}
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
                            @if(isset($approvals) && $approvals->count() > 0)
                                @foreach($approvals as $approval)
                                <tr>
                                    <td>{{ $approval->name }}</td>
                                    <td>{{ $approval->position }}</td>
                                    <td class="text-center">
                                        @if($approval->status == 'APPROVED NOT REVIEW')
                                            <span class="status-indicator status-approved">APPROVED NOT REVIEW</span>
                                        @elseif($approval->status == 'APPROVED WITH REVIEW')
                                            <span class="status-indicator status-review">APPROVED WITH REVIEW</span>
                                        @elseif($approval->status == 'NOT APPROVED')
                                            <span class="status-indicator status-rejected">NOT APPROVED</span>
                                        @else
                                            <span class="status-indicator status-pending">NOT REVIEWED</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($approval->approved_at)
                                            {{ \Carbon\Carbon::parse($approval->approved_at)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $approval->status == 'APPROVED NOT REVIEW' ? '' : $approval->notes ?? 'Tidak ada komentar' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center" style="padding: 20px;">No approval history found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </td>
            </tr>
        </table>
    </div>

    @endforeach
</body>

</html>
