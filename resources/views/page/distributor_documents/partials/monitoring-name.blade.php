<div class="fw-bold text-dark">{{ $distributor->name }}</div>
<div class="small mt-1">
    @if (!empty($bupotEmails))
        <div class="d-flex flex-wrap gap-1 align-items-center">
            @foreach ($bupotEmails as $bEmail)
                <span class="badge bg-light text-dark border fw-normal text-truncate distributor-bupot-badge" title="{{ $bEmail }}">
                    <i class="iconoir-mail me-1 text-primary"></i>{{ $bEmail }}
                </span>
            @endforeach
        </div>
    @else
        <span class="text-muted fst-italic">-</span>
    @endif
</div>
