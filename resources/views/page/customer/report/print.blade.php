<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        @php
            $reportCount = isset($requisitions) ? count($requisitions) : 1;
            $firstReport = isset($requisitions) ? $requisitions->first() : $requisition;
        @endphp
        @if($reportCount > 0)
            Requisition Slip SAMPLE - {{ $reportCount }} Reports
        @else
            Bulk RS Complain Reports - {{ $reportCount }} Reports
        @endif
    </title>
    <style>
        @page { margin: 0.5cm; }
        /* [DIUBAH] Ukuran font dasar dikecilkan lagi */
        body { font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size: 10pt; margin: 0; }
        .page { width: 93%; padding: 1cm; }
        .page-break { page-break-before: always; }
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
    {{-- ======================================================= --}}
    {{-- ========= HALAMAN 1: TEMPLATE UTAMA (SEMUA TIPE) ========= --}}
    {{-- ======================================================= --}}
    @php
        $reports = isset($requisitions) ? $requisitions : [$requisition];
    @endphp

    @foreach($reports as $index => $requisitions)
    @if($index > 0)
        <div style="page-break-before: always;"></div>
    @endif

    @php
        $approvals = $requisitions->approvalLogs->map(function ($log) {
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
                'updated_at' => $log->updated_at,
                'notes' => $log->notes
            ];
        });

        $requester = $requisitions->requester ? $requisitions->requester : (object) ['name' => 'Unknown', 'department' => (object) ['name' => 'Unknown']];
    @endphp
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
                                <div class="sub-title">SAMPLE PRODUCT</div>
                            </td>
                            <td style="width: 23%;" class="header-info">
                                <table class="bordered" style="width: 100%;">
                                    <tr>
                                        <td><strong>FORM NO.</strong></td>

                                        <td>: {{ $revision->revision_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>REVISION</strong></td>
                                        <td>: {{ $revision->revision_count ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>DATE</strong></td>
                                        <td>:
                                            @if(isset($revision) && $revision->revision_date)
                                                {{ \Carbon\Carbon::parse($revision->revision_date)->format('d F Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
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
                                        <td style="vertical-align: top; white-space: nowrap;">: {{ $requisitions->customer->name ?? '-' }}</td>
                                    </tr>
                                    <br>
                                    <tr>
                                        <td style="vertical-align: top; text-align: left; padding-right: 5px;"><strong>ADDRESS</strong></td>
                                        <td style="vertical-align: top; white-space: nowrap; width: 100%;">: {{ $requisitions->customer->address ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 40%; vertical-align: top; padding-left: 450px;">
                                {{-- Tabel untuk info RS --}}
                                <table class="no-border">
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Account</strong></td>
                                        <td>: {{ $requisitions->account ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Cost Center</strong></td>
                                        <td>: {{ $requisitions->cost_center ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($requisitions->request_date)->format('d F Y') }}</td>
                                    </tr>
                                    <br>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Nomor RS</strong></td>
                                        <td>: <strong style="font-size: 14pt;">{{ $requisitions->no_srs }}</strong></td>
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
                                @if($requisitions->sub_category == 'Packaging')
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
                                <th style="width: 15%;">Estimasi Potensi (Remarks in Carton)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $itemCount = $requisitions->requisitionItems->count();
                                $minRows = 15;
                                $totalRows = max($itemCount, $minRows);
                            @endphp

                            @foreach($requisitions->requisitionItems as $item)
                            <tr>
                                {{-- [MODIFIKASI] Logika untuk menampilkan data berdasarkan sub_category --}}
                                @if($requisitions->sub_category == 'Packaging')
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
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisitions->objectives }}</td>
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisitions->estimated_potential }}</td>
                                @endif
                            </tr>
                            @endforeach

                            {{-- Logika untuk baris kosong tetap sama --}}
                            @for ($i = $itemCount; $i < $minRows; $i++)
                            <tr>
                                @if($requisitions->sub_category == 'Packaging')
                                    <td>&nbsp;</td> {{-- Kolom ekstra untuk Material Type --}}
                                @endif
                                <td>&nbsp;</td> <td></td> <td></td> <td></td> <td></td>

                                {{-- Pastikan kolom rowspan hanya dirender sekali jika tidak ada item sama sekali --}}
                                @if($itemCount == 0 && $i == 0)
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisitions->objectives }}</td>
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisitions->estimated_potential }}</td>
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
    {{-- ========= HALAMAN 2: KHUSUS JIKA TIPE SPECIAL ORDER ========= --}}
    {{-- =================================================================== --}}

    @if($requisitions->sub_category === 'Special Order' && $requisitions->requisitionSpecial)
        @php
            $special = $requisitions->requisitionSpecial;

            // Blok logika terpusat
            $weight_options = ['25Kg', '15Kg', '250gr', '500g', '1Kg', '500ml', '1lt', '5lt'];
            $packaging_options = ['Tub', 'Karton', 'Botol', 'Jerrycan'];
            $shipment_options = ['Sales', 'Delivery (DHL)', 'Container', 'Kurir'];
            $source_options = ['WH', 'Reference Sample', 'Batch Refinery', 'Packing Room'];
            $preparation_options = ['Tidak berubah', 'Rework Karton', 'Rework Stencill', 'Rework Label'];
            $notes_options = ['Tempel sticker'];

            $is_other_weight = $special->weight_selection && !in_array($special->weight_selection, $weight_options);
            $is_other_packaging = $special->packaging_selection && !in_array($special->packaging_selection, $packaging_options);
            $is_other_shipment = $special->shipment_method && !in_array($special->shipment_method, $shipment_options);
            $is_other_source = $special->source && !in_array($special->source, $source_options);
            $is_other_preparation = $special->preparation_method && !in_array($special->preparation_method, $preparation_options);
            $is_other_notes = $special->sample_notes && !in_array($special->sample_notes, $notes_options);

            $batch_no = '........';
            $pallet_no = '........';
            $wb_deo_no = '........';
            $tank_no = '........';

            if ($special->description) {
                $desc = $special->description;

                if (str_contains($desc, 'P')) {
                    $parts = explode('P', $desc, 2);
                    $batch_no = "<b><u>" . e($parts[0] ?: 'N/A') . "</u></b>";
                    $pallet_no = "<b><u>" . e($parts[1] ?: 'N/A') . "</u></b>";
                }
                elseif (str_starts_with($desc, 'WB:')) {
                    $wb_deo_no = "<b><u>" . e(substr($desc, 3)) . "</u></b>";
                }
                elseif (str_starts_with($desc, 'TANK:')) {
                    $tank_no = "<b><u>" . e(substr($desc, 5)) . "</u></b>";
                }
                else {
                    $wb_deo_no = "<b><u>" . e($desc) . "</u></b>";
                }
            }
        @endphp
        <div class="page page-break">
            <table class="outer">
                {{-- HEADER --}}
                <tr>
                    <td colspan="6" style="text-align: center; font-weight: bold; font-size: 14px; padding: 4px 8px;">
                        PT. Sinar Meadow International Indonesia
                    </td>
                    <td rowspan="2" style="width: 25%; padding: 0;">
                        <table style="width: 100%; font-size: 10px;">
                            <tr><td style="border: none; padding: 3px 8px;">No</td><td style="border: none; padding: 3px 8px;">: F/F 08-01</td></tr>
                            <tr><td style="border: none; padding: 3px 8px;">Revision</td><td style="border: none; padding: 3px 8px;">: 0</td></tr>
                            <tr><td style="border: none; padding: 3px 8px;">Date</td><td style="border: none; padding: 3px 8px;">: 23 Jan 20</td></tr>
                            <tr><td style="border: none; padding: 3px 8px;">Page</td><td style="border: none; padding: 3px 8px;">: 1 of 1</td></tr>
                        </table>
                    </td>
                </tr>
                <tr style="background-color: #e4e4e4ff;"><td colspan="6" class="text-center" style="font-weight: bold; font-size: 16px;">PERMINTAAN SAMPLE</td></tr>

                {{-- BAGIAN PERMINTAAN SAMPLE (MARKETING) --}}
                <tr class="section-header"><td colspan="7" class="text-center" style="font-style: italic; font-weight: bold;">Diisi oleh Marketing</td></tr>
                <tr>
                    {{-- [MODIFIKASI] Tambahkan border-right dan padding --}}
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Tgl Permintaan</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="5">: {{ \Carbon\Carbon::parse($requisitions->request_date)->locale('id_ID')->isoFormat('D MMMM YYYY') }}</td>
                    <td style="white-space: nowrap; border: none;">No. SRS : <strong style="font-size: 14px;">{{ $requisitions->no_srs }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Tgl Selesai Sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">: {{ \Carbon\Carbon::parse($special->end_date)->locale('id_ID')->isoFormat('D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Produk</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">: <strong>{{ $special->products }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Berat sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @foreach(['a. 25Kg', 'b. 15Kg', 'c. 250gr', 'd. 500gr', 'e. 1Kg', 'f. 500ml', 'g. 1lt', 'h. 5lt'] as $option)
                            @if(str_contains($option, $special->weight_selection) && !$is_other_weight)<b><u>{{ $option }}</u></b>@else{{ $option }}@endif
                            &nbsp;&nbsp;&nbsp;
                        @endforeach
                        i. Lainnya: @if($is_other_weight)<b><u>{{ $special->weight_selection }}</u></b>@else.............@endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Kemasan sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @foreach(['a. Tub', 'b. Karton', 'c. Botol', 'd. Jerrycan'] as $option)
                            @if(str_contains($option, $special->packaging_selection) && !$is_other_packaging)<b><u>{{ $option }}</u></b>@else{{ $option }}@endif
                            &nbsp;&nbsp;&nbsp;
                        @endforeach
                        e. Lainnya: @if($is_other_packaging)<b><u>{{ $special->packaging_selection }}</u></b>@else.............@endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Jumlah sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">: <strong>{{ $special->sample_count }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Tujuan sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">: <strong>{{ $special->purpose }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Certificate of Analysis</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @if($special->coa_required)<b><u>a. Ya</u></b>@else a. Ya @endif
                        &nbsp;&nbsp;&nbsp;
                        @if(!$special->coa_required)<b><u>b. Tidak</u></b>@else b. Tidak @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Dikirim melalui</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @foreach(['a. Sales', 'b. Delivery (DHL)', 'c. Container', 'd. Kurir'] as $option)
                            @if(str_contains($option, $special->shipment_method) && !$is_other_shipment)<b><u>{{ $option }}</u></b>@else{{ $option }}@endif
                            &nbsp;&nbsp;&nbsp;
                        @endforeach
                        e. Lainnya: @if($is_other_shipment)<b><u>{{ $special->shipment_method }}</u></b>@else.............@endif
                    </td>
                </tr>

                {{-- BAGIAN QA/QM --}}
                <tr class="section-header"><td colspan="7" class="text-center" style="font-style: italic; font-weight: bold;">Diisi oleh QA</td></tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Asal sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @if($special->source == 'WH')<b><u>a. WH</u></b>@else a. WH @endif&nbsp;&nbsp;&nbsp;
                        @if($special->source == 'Reference Sample')<b><u>b. Reference Sample</u></b>@else b. Reference Sample @endif&nbsp;&nbsp;&nbsp;
                        @if($special->source == 'Batch Refinery')<b><u>c. Batch Refinery</u></b>@else c. Batch Refinery @endif&nbsp;&nbsp;&nbsp;
                        @if($special->source == 'Packing Room')<b><u>d. Packing Room</u></b>@else d. Packing Room @endif&nbsp;&nbsp;&nbsp;
                        e. Lainnya: @if($is_other_source)<b><u>{{ $special->source }}</u></b>@else.............@endif&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Keterangan sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        : a. Batch / Pallet No: {!! $batch_no !!} P {!! $pallet_no !!} &nbsp;&nbsp; b. WB/DEO No: {!! $wb_deo_no !!} &nbsp;&nbsp; c. Tank No: {!! $tank_no !!}
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Tgl Produksi</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        {{ $special->production_date ? \Carbon\Carbon::parse($special->production_date)->locale('id_ID')->isoFormat('D MMMM YYYY') : '...........................' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Persiapan sample</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @if($special->preparation_method == 'Tidak berubah')<b><u>a. Tidak berubah</u></b>@else a. Tidak berubah @endif&nbsp;&nbsp;&nbsp;
                        @if($special->preparation_method == 'Rework Karton')<b><u>b. Rework Karton</u></b>@else b. Rework Karton @endif&nbsp;&nbsp;&nbsp;
                        @if($special->preparation_method == 'Rework Stencill')<b><u>c. Rework Stencil</u></b>@else c. Rework Stencil @endif&nbsp;&nbsp;&nbsp;
                        @if($special->preparation_method == 'Rework Label')<b><u>d. Rework Label</u></b>@else d. Rework Label @endif&nbsp;&nbsp;&nbsp;
                        e. Lainnya: @if($is_other_preparation)<b><u>{{ $special->preparation_method }}</u></b>@else.............@endif&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; white-space: nowrap; border: none; border-right: 1px solid #333; padding-right: 8px;">Keterangan</td>
                    <td style="white-space: nowrap; border: none; padding-left: 8px;" colspan="6">
                        :
                        @if($special->sample_notes == 'Tempel sticker')<b><u>a. Tempel sticker</u></b>@else a. Tempel sticker @endif&nbsp;&nbsp;&nbsp;
                        b. Lainnya: @if($is_other_notes)<b><u>{{ $special->sample_notes }}</u></b>@else.............@endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

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
                                <div class="sub-title">SAMPLE PRODUCT: <strong>{{ $requisitions->no_srs }}</strong></div>
                            </td>
                            <td style="width: 25%;">
                                <table class="no-border" style="table-layout: auto; width: auto;">
                                    <tr>
                                        {{-- KOLOM JUDUL: Dibuat agar lebarnya pas dengan teksnya --}}
                                        <td style="white-space: nowrap; padding-right: 3px;"><strong>Requester</strong></td>

                                        {{-- KOLOM TITIK DUA: Dibuat sempit --}}
                                        <td style="width: 5px;">:</td>

                                        {{-- KOLOM ISI --}}
                                        <td>{{ $requester->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="white-space: nowrap; padding-right: 3px;"><strong>Dept</strong></td>
                                        <td style="width: 5px;">:</td>
                                        <td>{{ $requester->department->name }}</td>
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
                                    @php
                                        $isDefaultNote = in_array($approval->notes, [
                                            'Approved without Review',
                                            'Approved without Review (Quick Action)'
                                        ]);

                                        $correctStatus = $approval->status;

                                        if ($isDefaultNote) {
                                            $correctStatus = 'APPROVED NOT REVIEW';
                                        } elseif (!empty($approval->notes) && !$isDefaultNote) {
                                            $correctStatus = 'APPROVED WITH REVIEW';
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $approval->name }}</td>
                                        <td>{{ $approval->position }}</td>
                                        <td class="text-center">
                                            @if($correctStatus == 'APPROVED NOT REVIEW')
                                                <span class="status-indicator status-approved">APPROVED NOT REVIEW</span>
                                            @elseif($correctStatus == 'APPROVED WITH REVIEW')
                                                <span class="status-indicator status-review">APPROVED WITH REVIEW</span>
                                            @elseif($correctStatus == 'NOT APPROVED')
                                                <span class="status-indicator status-rejected">NOT APPROVED</span>
                                            @else
                                                <span class="status-indicator status-pending">NOT REVIEWED</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($approval->updated_at)
                                                {{ \Carbon\Carbon::parse($approval->updated_at)->format('d M Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $correctStatus == 'APPROVED NOT REVIEW' ? '-' : $approval->notes ?? '-' }}</td>
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
