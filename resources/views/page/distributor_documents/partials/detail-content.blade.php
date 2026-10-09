@php
    // SECURE BY DEFAULT:
    // Requires authenticated internal user with permission AND explicit internal mode.
    $isPortal = isset($isPortal) ? (bool) $isPortal : (!auth()->check());
    $canManage = auth()->check() && auth()->user()->can('manage-distributor-docs') && !$isPortal;

    $routePrefix = $isPortal ? 'portal.distributor.' : 'distributor.documents.';
    $yearChangeCallback = $isPortal ? 'changePortalYear' : 'changeDetailYear';
    $tabSwitchCallback = $isPortal ? 'switchPortalTab' : 'switchDetailTab';
    $transferYearCallback = $isPortal ? 'changePortalTransferYear' : 'changeTransferYear';
    $modalYearSelectId = $isPortal ? 'portal_modal_year' : 'detail_modal_year';
    $modalTransferSelectId = $isPortal ? 'portal_transfer_year_select' : 'transfer_year_select';
    $transferTableId = $isPortal ? 'portalTransferDocsTable' : 'transferDocsTable';
    $totalTransferDocs = $transferDocsCount ?? (isset($transferDocs) ? $transferDocs->count() : 0);
@endphp

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
            @php
                $bupotEmails = $distributor->bupot_email_list;
            @endphp
            <div class="small d-flex align-items-center flex-wrap gap-2 mt-1" style="color: #cbd5e1;">
                <span class="d-inline-flex align-items-center">
                    <i class="iconoir-mail me-1 text-info"></i><strong>Email BuPot:</strong>
                </span>
                @if (!empty($bupotEmails))
                    <div class="d-inline-flex flex-wrap gap-1 align-items-center">
                        @foreach ($bupotEmails as $bEmail)
                            <span class="badge border fw-normal text-white px-2 py-1" style="font-size: 0.75rem; background-color: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25) !important;" title="{{ $bEmail }}">
                                {{ $bEmail }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-white-50 fst-italic">-</span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Filter Year inside Modal --}}
            <div class="d-inline-flex align-items-center gap-2">
                <label for="{{ $modalYearSelectId }}" class="small mb-0 text-white-50 text-nowrap">Year:</label>
                <select id="{{ $modalYearSelectId }}" class="form-select form-select-sm" style="min-width: 100px;" onchange="{{ $yearChangeCallback }}({{ $distributor->id }}, this.value, '{{ $tab }}')">
                    @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                        <option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}</option>
                    @endfor
                </select>
            </div>

            {{-- Download All .ZIP Button --}}
            <a href="{{ route($routePrefix . 'download.zip', ['distributor_id' => $distributor->id, 'year' => $year]) }}" class="btn btn-sm btn-success text-nowrap">
                <i class="iconoir-archive me-1"></i> Download All .ZIP
            </a>

            @if ($canManage)
                {{-- Upload Document Button --}}
                <button type="button" class="btn btn-sm btn-light text-nowrap fw-semibold text-primary" onclick="openUploadModal('bupot', 1, {{ $distributor->id }}, {{ $year }})">
                    <i class="iconoir-upload me-1 text-primary"></i> Upload Document
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="mb-3">
    <ul class="nav nav-tabs-custom" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link {{ $tab === 'monthly' ? 'active' : '' }}" onclick="{{ $tabSwitchCallback }}({{ $distributor->id }}, {{ $year }}, 'monthly')">
                <i class="iconoir-calendar me-2"></i>Dokumen Bulanan (BuPot & TOP Insentif)
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link {{ $tab === 'transfer' ? 'active' : '' }}" onclick="{{ $tabSwitchCallback }}({{ $distributor->id }}, {{ $year }}, 'transfer')">
                <i class="iconoir-calendar-rotate me-2"></i>Penjelasan Transfer (Running Multi-Year)
                @if ($totalTransferDocs > 0)
                    <span class="badge bg-primary-subtle text-primary ms-1">{{ $totalTransferDocs }}</span>
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
                                @if ($canManage)
                                    <button type="button" class="btn btn-outline-primary btn-add-quick d-inline-flex align-items-center" onclick="openUploadModal('bupot', {{ $monthNumber }}, {{ $distributor->id }}, {{ $year }})">
                                        <i class="iconoir-plus me-1"></i> Tambah BuPot
                                    </button>
                                @endif
                            </div>

                            @forelse ($bupotDocuments as $doc)
                                <div class="doc-item-row d-flex justify-content-between align-items-center">
                                    @include('page.distributor_documents.partials.document-file', ['document' => $doc])
                                    @include('page.distributor_documents.partials.document-actions', [
                                        'document' => $doc,
                                        'isPortal' => $isPortal,
                                        'canManage' => $canManage,
                                        'year' => $year,
                                        'tab' => 'monthly'
                                    ])
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
                                @if ($canManage)
                                    <button type="button" class="btn btn-outline-primary btn-add-quick d-inline-flex align-items-center" onclick="openUploadModal('top_insentif', {{ $monthNumber }}, {{ $distributor->id }}, {{ $year }})">
                                        <i class="iconoir-plus me-1"></i> Tambah TOP Insentif
                                    </button>
                                @endif
                            </div>

                            @forelse ($topDocuments as $doc)
                                <div class="doc-item-row d-flex justify-content-between align-items-center">
                                    @include('page.distributor_documents.partials.document-file', ['document' => $doc])
                                    @include('page.distributor_documents.partials.document-actions', [
                                        'document' => $doc,
                                        'isPortal' => $isPortal,
                                        'canManage' => $canManage,
                                        'year' => $year,
                                        'tab' => 'monthly'
                                    ])
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
                        <label for="{{ $modalTransferSelectId }}" class="small mb-0 text-muted text-nowrap">Filter Tahun:</label>
                        <select id="{{ $modalTransferSelectId }}" class="form-select form-select-sm" style="min-width: 140px;" onchange="{{ $transferYearCallback }}({{ $distributor->id }}, {{ $year }}, this.value)">
                            <option value="all" @selected(!$transferYear || $transferYear === 'all')>Semua Tahun</option>
                            @foreach ($availableTransferYears as $availYear)
                                <option value="{{ $availYear }}" @selected((string)$transferYear === (string)$availYear)>Tahun {{ $availYear }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($canManage)
                        {{-- Quick Upload Transfer Button --}}
                        <button type="button" class="btn btn-sm btn-primary text-nowrap" onclick="openUploadModal('transfer', null, {{ $distributor->id }}, {{ $year }})">
                            <i class="iconoir-upload me-1"></i> Upload File Transfer Baru
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="{{ $transferTableId }}" class="table table-hover align-middle mb-0 w-100" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 140px;">TGL / TAHUN</th>
                            <th style="min-width: 250px;">NAMA FILE</th>
                            <th style="min-width: 250px;">KETERANGAN / CATATAN</th>
                            <th class="text-center" style="width: 120px;">UKURAN</th>
                            <th class="text-end pe-3" style="width: 150px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endif
