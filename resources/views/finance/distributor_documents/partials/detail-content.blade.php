{{-- Header Card Distributor --}}
<div class="detail-header-card mb-3">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h3 class="h4 mb-0 fw-bold text-white d-flex align-items-center">
                    <i class="iconoir-building me-2"></i>{{ $distributor->name }}
                </h3>
                <span class="distributor-code-badge-light">{{ $distributor->code }}</span>
            </div>
            <div class="small" style="color: #cbd5e1;">
                <span>
                    <i class="iconoir-mail me-1"></i><strong>Email:</strong> {{ $distributor->email ?: '-' }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Filter Year inside Modal --}}
            <div class="d-inline-flex align-items-center gap-2">
                <label for="detail_modal_year" class="small mb-0 text-white-50 text-nowrap">Year:</label>
                <select id="detail_modal_year" class="form-select form-select-sm" style="min-width: 100px;" onchange="changeDetailYear({{ $distributor->id }}, this.value, '{{ $tab }}')">
                    @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                        <option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}</option>
                    @endfor
                </select>
            </div>

            {{-- Download All .ZIP Button --}}
            <a href="{{ route('distributor.documents.download.zip', ['distributor_id' => $distributor->id, 'year' => $year]) }}" class="btn btn-sm btn-success text-nowrap">
                <i class="iconoir-archive me-1"></i> Download All .ZIP
            </a>

            {{-- Upload Document Button --}}
            <button type="button" class="btn btn-sm btn-light text-nowrap fw-semibold text-primary" onclick="openUploadModal('bupot', 1, {{ $distributor->id }}, {{ $year }})">
                <i class="iconoir-upload me-1 text-primary"></i> Upload Document
            </button>
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="mb-3">
    <ul class="nav nav-tabs-custom" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link {{ $tab === 'monthly' ? 'active' : '' }}" onclick="switchDetailTab({{ $distributor->id }}, {{ $year }}, 'monthly')">
                <i class="iconoir-calendar me-2"></i>Dokumen Bulanan (BuPot & TOP Insentif)
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link {{ $tab === 'transfer' ? 'active' : '' }}" onclick="switchDetailTab({{ $distributor->id }}, {{ $year }}, 'transfer')">
                <i class="iconoir-calendar-rotate me-2"></i>Penjelasan Transfer (Running Multi-Year)
                @if ($transferDocs->count() > 0)
                    <span class="badge bg-primary-subtle text-primary ms-1">{{ $transferDocs->count() }}</span>
                @endif
            </button>
        </li>
    </ul>
</div>

{{-- TAB 1: MONTHLY DOCUMENT (12 MONTHS MATRIX) --}}
@if ($tab === 'monthly')
    <div class="row g-3">
        @php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
        @endphp

        @foreach ($months as $monthNumber => $monthName)
            @php
                $monthDocuments = $monthlyDocs->get($monthNumber, collect());
                $bupotDocuments = $monthDocuments->get('bupot', collect());
                $topDocuments = $monthDocuments->get('top_insentif', collect());
                $totalDocs = $bupotDocuments->count() + $topDocuments->count();

                if ($bupotDocuments->isNotEmpty() && $topDocuments->isNotEmpty()) {
                    $badgeClass = 'badge-status-complete';
                    $statusIconClass = 'iconoir-check-circle text-success';
                } elseif ($bupotDocuments->isNotEmpty() || $topDocuments->isNotEmpty()) {
                    $badgeClass = 'badge-status-partial';
                    $statusIconClass = 'iconoir-warning-triangle text-warning';
                } else {
                    $badgeClass = 'badge-status-empty';
                    $statusIconClass = 'iconoir-circle text-secondary';
                }
            @endphp

            <div class="col-12 col-md-6">
                <div class="month-card h-100 shadow-sm d-flex flex-column" id="month-card-{{ $monthNumber }}">
                    {{-- Card Header --}}
                    <div class="month-card-header d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark">
                            <i class="iconoir-calendar me-1 text-primary"></i> {{ strtoupper($monthName) }} {{ $year }}
                        </div>
                        <span class="badge {{ $badgeClass }} px-2 py-1 d-inline-flex align-items-center">
                            <i class="{{ $statusIconClass }} me-1"></i> {{ $totalDocs }} Dokumen
                        </span>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body p-3 d-flex flex-column gap-3 flex-grow-1">
                        {{-- Section BuPot --}}
                        <div class="doc-section">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark d-inline-flex align-items-center">
                                    <i class="iconoir-notes me-1 text-primary"></i> Bukti Potong ({{ $bupotDocuments->count() }} File)
                                </span>
                                <button type="button" class="btn btn-outline-primary btn-add-quick d-inline-flex align-items-center" onclick="openUploadModal('bupot', {{ $monthNumber }}, {{ $distributor->id }}, {{ $year }})">
                                    <i class="iconoir-plus me-1"></i> Tambah BuPot
                                </button>
                            </div>

                            @forelse ($bupotDocuments as $doc)
                                <div class="doc-item-row d-flex justify-content-between align-items-center">
                                    @include('finance.distributor_documents.partials.document-file', ['document' => $doc])
                                    @include('finance.distributor_documents.partials.document-actions', ['document' => $doc, 'year' => $year, 'tab' => 'monthly'])
                                </div>
                            @empty
                                <div class="text-center py-2 text-muted small border border-dashed rounded bg-white">
                                    Belum ada Bukti Potong (PDF)
                                </div>
                            @endforelse
                        </div>

                        {{-- Section TOP Insentif --}}
                        <div class="doc-section">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark d-inline-flex align-items-center">
                                    <i class="iconoir-medal me-1 text-warning"></i> TOP Insentif ({{ $topDocuments->count() }} File)
                                </span>
                                <button type="button" class="btn btn-outline-primary btn-add-quick d-inline-flex align-items-center" onclick="openUploadModal('top_insentif', {{ $monthNumber }}, {{ $distributor->id }}, {{ $year }})">
                                    <i class="iconoir-plus me-1"></i> Tambah TOP Insentif
                                </button>
                            </div>

                            @forelse ($topDocuments as $doc)
                                <div class="doc-item-row d-flex justify-content-between align-items-center">
                                    @include('finance.distributor_documents.partials.document-file', ['document' => $doc])
                                    @include('finance.distributor_documents.partials.document-actions', ['document' => $doc, 'year' => $year, 'tab' => 'monthly'])
                                </div>
                            @empty
                                <div class="text-center py-2 text-muted small border border-dashed rounded bg-white">
                                    Belum ada TOP Insentif (PDF)
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

{{-- TAB 2: TRANSFER EXPLANATION (RUNNING MULTI-YEAR) --}}
@else
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="mb-1 fw-bold">Riwayat Dokumen Penjelasan Transfer</h5>
                    <p class="text-muted small mb-0">Daftar seluruh berkas transfer distributor ini (running terus sejak awal kerjasama).</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Filter year Transfer --}}
                    <div class="d-inline-flex align-items-center gap-2">
                        <label for="transfer_year_select" class="small mb-0 text-muted text-nowrap">Filter Tahun:</label>
                        <select id="transfer_year_select" class="form-select form-select-sm" style="min-width: 140px;" onchange="changeTransferYear({{ $distributor->id }}, {{ $year }}, this.value)">
                            <option value="all" @selected(!$transferYear || $transferYear === 'all')>Semua Tahun</option>
                            @foreach ($availableTransferYears as $availYear)
                                <option value="{{ $availYear }}" @selected((string)$transferYear === (string)$availYear)>Tahun {{ $availYear }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Quick Upload Transfer Button --}}
                    <button type="button" class="btn btn-sm btn-primary text-nowrap" onclick="openUploadModal('transfer', null, {{ $distributor->id }}, {{ $year }})">
                        <i class="iconoir-upload me-1"></i> Upload File Transfer Baru
                    </button>
                </div>
            </div>
        </div>

        @if ($transferDocs->isEmpty())
            <div class="card-body p-5 text-center">
                <i class="iconoir-empty-page fs-1 text-muted"></i>
                <h5 class="mt-3">Belum Ada Dokumen Penjelasan Transfer</h5>
                <p class="text-muted mb-3">Unggah berkas transfer pelunasan, rekap transfer, atau arsip kerja sama distributor ini.</p>
                <button type="button" class="btn btn-sm btn-primary" onclick="openUploadModal('transfer', null, {{ $distributor->id }}, {{ $year }})">
                    <i class="iconoir-upload me-1"></i> Upload File Transfer (PDF)
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 140px;">TGL / TAHUN</th>
                            <th style="min-width: 250px;">NAMA FILE</th>
                            <th style="min-width: 250px;">KETERANGAN / CATATAN</th>
                            <th class="text-center" style="width: 120px;">UKURAN</th>
                            <th class="text-end pe-3" style="width: 150px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transferDocs as $doc)
                            <tr>
                                <td class="ps-3 fw-semibold text-nowrap">
                                    @if ($doc->transaction_date)
                                        {{ $doc->transaction_date->format('d M Y') }}
                                    @elseif ($doc->year)
                                        Tahun {{ $doc->year }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @include('finance.distributor_documents.partials.document-file', ['document' => $doc])
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $doc->notes ?: '-' }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-light text-dark border">{{ $doc->human_file_size }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    @include('finance.distributor_documents.partials.document-actions', ['document' => $doc, 'year' => $year, 'tab' => 'transfer'])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top p-3 text-muted small">
                Menampilkan <strong>{{ $transferDocs->count() }}</strong> berkas transfer penjelasan running.
            </div>
        @endif
    </div>
@endif
