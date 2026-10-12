@if ($distributor->transfer_documents_count > 0)
    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }}, 'transfer')">
        <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
            {{ $distributor->transfer_documents_count }} File
        </span>
        @if (!empty($range))
            <div class="small text-muted mt-1 distributor-transfer-range">({{ $range }})</div>
        @endif
    </button>
@else
    <span class="text-muted small">0 File</span>
@endif
