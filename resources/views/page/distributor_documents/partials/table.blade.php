<div class="card border-0 shadow-sm" id="distributorTableCard">
    <div class="card-header bg-white border-bottom py-2 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <div id="tableLengthContainer" class="d-flex align-items-center"></div>
        <div class="d-flex align-items-center">
            <span class="badge bg-light text-primary border px-2 py-1 fw-semibold" style="font-size: 0.8rem;">
                <i class="iconoir-calendar me-1"></i>Periode Aktif: <span id="activeYearBadge" class="fw-bold">{{ $year }}</span>
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-100" id="distributorDocumentsTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 140px;">KODE</th>
                        <th style="min-width: 240px;">NAMA DISTRIBUTOR</th>
                        <th class="text-center" style="min-width: 440px;" id="headerMonthlyProgress">PROGRES BULANAN {{ $year }} (JAN - DES)</th>
                        <th class="text-center" style="width: 170px;">TRF RUNNING</th>
                        <th class="text-end pe-3" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card Footer: Legend --}}
    <div class="card-footer bg-white border-top p-3">
        <div class="d-flex flex-wrap align-items-center small text-muted">
            <span class="fw-bold me-2">Keterangan Badge:</span>
            <span class="me-3"><span class="legend-indicator legend-complete"></span> <strong>Lengkap</strong> (BuPot & TOP ada)</span>
            <span class="me-3"><span class="legend-indicator legend-partial"></span> <strong>Sebagian</strong> terisi</span>
            <span><span class="legend-indicator legend-empty"></span> <strong>Kosong</strong> (Belum ada dokumen)</span>
        </div>
    </div>
</div>
