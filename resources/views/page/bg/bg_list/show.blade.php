<x-app-layout>
    @section('title', 'BG Detail: ' . $bg->bg_number)

    <div class="row m-1">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="main-title mb-1">BG Detail View</h4>
                    <ul class="app-line-breadcrumbs">
                        <li><a href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                        <li class="active">Detail</li>
                    </ul>
                </div>
                <a href="{{ route('bg-list.index') }}" class="btn btn-secondary">
                    <i class="ph-bold ph-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Side Info (Ringkasan) --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-initial rounded bg-light-primary text-primary mx-auto" style="width: 60px; height: 60px; font-size: 24px; display:flex; align-items:center; justify-content:center;">
                            <i class="ph-duotone ph-bank"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">{{ $bg->bg_number }}</h6>
                    <p class="text-muted small mb-2">{{ $bg->customer->name ?? 'Unknown Customer' }}</p>

                    @php
                        $statusColors = [
                            'draft' => 'secondary', 'sent_to_customer' => 'info',
                            'submitted' => 'primary', 'reviewed' => 'warning',
                            'approved' => 'success', 'expired' => 'danger'
                        ];
                        $badgeColor = $statusColors[$bg->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $badgeColor }} text-uppercase mb-3">{{ str_replace('_', ' ', $bg->status) }}</span>

                    <div class="d-grid gap-2">
                        {{-- Tombol Edit Trigger Modal (Logic JS di Index, kita bisa pasang lagi disini kalau mau, atau redirect edit) --}}
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            <i class="ph-bold ph-printer"></i> Print Summary
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Type</small>
                            <span class="fw-bold text-uppercase">{{ $bg->bg_type }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Creator</small>
                            <span class="fw-bold">{{ $bg->creator->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content (Tabs) --}}
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="bgTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">
                                <i class="ph-bold ph-info me-1"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="details-tab" data-bs-toggle="tab" href="#details" role="tab">
                                <i class="ph-bold ph-buildings me-1"></i> Bank Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab">
                                <i class="ph-bold ph-clock-counter-clockwise me-1"></i> History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="submissions-tab" data-bs-toggle="tab" href="#submissions" role="tab">
                                <i class="ph-bold ph-file-text me-1"></i> Submissions
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="bgTabsContent">

                        {{-- TAB 1: OVERVIEW --}}
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <h6 class="text-primary fw-bold mb-3">General Information</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="small text-muted">Customer Name</label>
                                    <div class="fw-bold">{{ $bg->customer->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">BG Number</label>
                                    <div class="fw-bold">{{ $bg->bg_number }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Total Nominal</label>
                                    <div class="fw-bold fs-5 text-dark">Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Base BG (If Extension)</label>
                                    <div class="fw-bold">
                                        @if($bg->baseBg)
                                            <a href="{{ route('bg-list.show', $bg->base_bg_id) }}">{{ $bg->baseBg->bg_number }}</a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Issued Date</label>
                                    <div class="fw-bold">{{ $bg->issued_date ? $bg->issued_date->format('d M Y') : '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Expiry Date</label>
                                    <div class="fw-bold text-danger">{{ $bg->exp_date ? $bg->exp_date->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: BANK DETAILS --}}
                        <div class="tab-pane fade" id="details" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-success fw-bold m-0">Issuing Banks</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Bank Name</th>
                                            <th>Branch</th>
                                            <th>Address</th>
                                            <th>PIC</th>
                                            <th class="text-end">Nominal (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bg->details as $detail)
                                            <tr>
                                                <td class="fw-bold">{{ $detail->bank_name }}</td>
                                                <td>{{ $detail->branch_name ?? '-' }}</td>
                                                <td>{{ $detail->bank_address ?? '-' }}</td>
                                                <td>{{ $detail->contact_person ?? '-' }}</td>
                                                <td class="text-end fw-bold">{{ number_format($detail->nominal, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No bank details found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($bg->details->count() > 0)
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="4" class="text-end fw-bold">Total</td>
                                                <td class="text-end fw-bold">{{ number_format($bg->details->sum('nominal'), 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>

                        {{-- TAB 3: HISTORY --}}
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <h6 class="text-warning fw-bold mb-3">Changes Log</h6>
                            <div class="timeline p-2">
                                @forelse($bg->histories->sortByDesc('created_at') as $history)
                                    <div class="border-start border-2 border-warning ps-3 pb-3 position-relative">
                                        <div class="position-absolute bg-warning rounded-circle" style="width: 12px; height: 12px; left: -7px; top: 0;"></div>
                                        <div class="mb-1">
                                            <span class="fw-bold text-dark">{{ $history->user->name ?? 'System' }}</span>
                                            <span class="text-muted small ms-2">{{ $history->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                        <p class="mb-1 text-secondary f-s-13">
                                            {{ $history->description ?? 'Updated record details.' }}
                                        </p>
                                        @if($history->remarks)
                                            <div class="alert alert-light p-2 mb-0 f-s-12 border">
                                                <em>"{{ $history->remarks }}"</em>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center text-muted">No history recorded yet.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- TAB 4: SUBMISSIONS --}}
                        <div class="tab-pane fade" id="submissions" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-info fw-bold m-0">Related Submissions</h6>
                            </div>
                            <div class="alert alert-info border-0 d-flex align-items-center">
                                <i class="ph-bold ph-info me-2"></i>
                                <span>Submissions are linked via Recommendations. This section will show submissions related to this Customer/BG.</span>
                            </div>
                            {{-- Placeholder jika nanti relasi submission sudah fix (via recommendation) --}}
                            <div class="text-center py-5 text-muted">
                                <i class="ph-duotone ph-file-dashed f-s-32 mb-2"></i>
                                <p>No direct submissions linked to this BG ID yet.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
