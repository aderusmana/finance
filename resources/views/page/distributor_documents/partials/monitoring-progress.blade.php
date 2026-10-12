<div class="month-matrix-container justify-content-center">
    @for ($m = 1; $m <= 12; $m++)
        @php
            $summary = $monthlySummary[$m] ?? ['status' => 'empty', 'total' => 0, 'has_bupot' => false, 'has_top_insentif' => false];
            $status = $summary['status'];
            $tooltip = $monthNames[$m] . ': ' . ($status === 'complete' ? 'Lengkap (BuPot & TOP)' : ($status === 'partial' ? 'Sebagian (' . ($summary['has_bupot'] ? 'BuPot' : 'TOP') . ')' : 'Belum Ada Dokumen'));
            $icon = ($status === 'complete') ? '✓' : (($status === 'partial') ? '!' : '•');
        @endphp
        <button type="button"
                class="month-matrix-pill {{ $status }}"
                title="{{ $tooltip }}"
                data-bs-toggle="tooltip"
                aria-label="Bulan {{ $monthNames[$m] }}: {{ $tooltip }}"
                onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }}, 'monthly', {{ $m }})">
            <span class="month-num">{{ $m }}</span>
            <span class="month-icon">{{ $icon }}</span>
        </button>
    @endfor
</div>
