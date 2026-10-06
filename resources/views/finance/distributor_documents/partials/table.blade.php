<div class="card border-0 shadow-sm" id="distributorTableCard">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Daftar Distributor & Progres Dokumen</h5>
            <small class="text-muted">Periode Tahun Aktif: <span class="fw-bold text-primary">{{ $year }}</span></small>
        </div>
        <span class="badge bg-light text-secondary border px-2 py-1">
            Total: {{ $distributors->total() }} Distributor
        </span>
    </div>

    @if ($distributors->isEmpty())
        <div class="card-body p-5 text-center">
            <i class="iconoir-empty-page fs-1 text-muted"></i>
            <h5 class="mt-3">Distributor Tidak Ditemukan</h5>
            <p class="text-muted mb-0">Ubah kata kunci pencarian atau ganti filter tahun.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 140px;">KODE</th>
                        <th style="min-width: 240px;">NAMA DISTRIBUTOR</th>
                        <th class="text-center" style="min-width: 440px;">PROGRES BULANAN {{ $year }} (JAN - DES)</th>
                        <th class="text-center" style="width: 170px;">TRF RUNNING</th>
                        <th class="text-end pe-3" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($distributors as $distributor)
                        <tr id="distributor-row-{{ $distributor->id }}">
                            <td class="ps-3">
                                <span class="distributor-code-badge">{{ $distributor->code }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $distributor->name }}</div>
                                @php
                                    $bupotEmails = $distributor->bupot_email_list;
                                @endphp
                                <div class="small mt-1">
                                    @if (!empty($bupotEmails))
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            @foreach ($bupotEmails as $bEmail)
                                                <span class="badge bg-light text-dark border fw-normal text-truncate" style="font-size: 0.75rem; max-width: 250px;" title="{{ $bEmail }}">
                                                    <i class="iconoir-mail me-1 text-primary"></i>{{ $bEmail }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="month-matrix-container">
                                    @php
                                        $monthNames = [
                                            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                        ];
                                    @endphp
                                    @for ($m = 1; $m <= 12; $m++)
                                        @php
                                            $summary = $distributor->monthly_summary[$m] ?? ['status' => 'empty', 'total' => 0, 'has_bupot' => false, 'has_top_insentif' => false];
                                            $status = $summary['status'];
                                            $tooltip = $monthNames[$m] . ': ' . ($status === 'complete' ? 'Lengkap (BuPot & TOP)' : ($status === 'partial' ? 'Sebagian (' . ($summary['has_bupot'] ? 'BuPot' : 'TOP') . ')' : 'Belum Ada Dokumen'));
                                        @endphp
                                        <button type="button"
                                                class="month-matrix-pill {{ $status }}"
                                                title="{{ $tooltip }}"
                                                data-bs-toggle="tooltip"
                                                onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }}, 'monthly', {{ $m }})">
                                            <span class="month-num">{{ $m }}</span>
                                            <span class="month-icon">
                                                @if ($status === 'complete')
                                                    ✓
                                                @elseif ($status === 'partial')
                                                    !
                                                @else
                                                    •
                                                @endif
                                            </span>
                                        </button>
                                    @endfor
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($distributor->transfer_documents_count > 0)
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }}, 'transfer')">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                            {{ $distributor->transfer_documents_count }} File
                                        </span>
                                        @if ($distributor->transfer_range)
                                            <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                                ({{ $distributor->transfer_range }})
                                            </div>
                                        @endif
                                    </button>
                                @else
                                    <span class="text-muted small">0 File</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }})">
                                    <i class="iconoir-folder me-1"></i> Kelola File
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Card Footer: Legend & Pagination --}}
        <div class="card-footer bg-white border-top p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="small text-muted">
                    <span class="fw-bold me-2">Keterangan Badge:</span>
                    <span class="me-3"><span class="legend-indicator legend-complete"></span> <strong>Lengkap</strong> (BuPot & TOP ada)</span>
                    <span class="me-3"><span class="legend-indicator legend-partial"></span> <strong>Sebagian</strong> terisi</span>
                    <span><span class="legend-indicator legend-empty"></span> <strong>Kosong</strong> (Belum ada dokumen)</span>
                </div>
                <div>
                    {{ $distributors->withQueryString()->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
