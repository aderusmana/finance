<div class="card" style="max-width:520px; margin:0 auto;">
    <style>
        .header-approve { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: #fff; }
        .header-reject { background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%); color: #fff; }
        .header-review { background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%); color: #fff; }
        .icon-circle { width: 72px; height: 72px; background: rgba(255,255,255,0.15); border-radius: 50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; }
        .info-box { background: linear-gradient(180deg, #ffffff, #f8fafc); border: 1px solid #e6eef9; border-radius: 10px; padding: 14px; margin: 18px 0; text-align: left; }
        .info-row { display:flex; justify-content:space-between; gap:20px; align-items:center; padding: 6px 0; border-bottom: 1px dashed rgba(0,0,0,0.04); }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #475569; font-weight: 600; width:40%; }
        .info-value { color: #0b1220; font-weight: 700; text-align:right; width:60%; }
        @media (max-width:576px) { .info-label, .info-value { display:block; width:100%; text-align:left; } .info-row { flex-direction:column; align-items:flex-start; gap:6px; } }
    </style>
    @php
        $headerClass = 'header-approve';
        $icon = 'fa-check';
        $title = 'Approval Successful';

        if($action === 'reject') {
            $headerClass = 'header-reject';
            $icon = 'fa-times';
            $title = 'Request Rejected';
        } elseif($action === 'review') {
            $headerClass = 'header-review';
            $icon = 'fa-clipboard-list';
            $title = 'Review Submitted';
        }
    @endphp

    <div class="card-header {{ $headerClass }} text-center py-4">
        <div class="icon-circle">
            <i class="fas {{ $icon }} fa-2x"></i>
        </div>
        <h4 class="m-0 fw-bold">{{ $title }}</h4>
    </div>

    <div class="card-body text-center p-4">
        <p class="text-muted mb-3">Thank you, your response has been recorded successfully. This dialog will close automatically in <b id="countdown">3</b> seconds.</p>

        <div class="info-box">
            <div class="info-row"><span class="info-label">Customer</span><span class="info-value">{{ $customerName }}</span></div>
            <div class="info-row"><span class="info-label">Action Taken</span><span class="info-value" style="text-transform:capitalize;">{{ $action }}</span></div>
            <div class="info-row"><span class="info-label">Route To</span><span class="info-value text-primary">{{ $routeTo ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">Date</span><span class="info-value">{{ date('d M Y H:i') }}</span></div>
        </div>

        <div class="mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Now</button>
        </div>
    </div>
</div>
    <script>
        // idempotent auto-close: only run once per page load
        if (!window.__approvalSuccessAutoClosed) {
            window.__approvalSuccessAutoClosed = true;
            (function(){
                var countdownEl = document.getElementById('countdown');
                var seconds = 3;
                if (countdownEl) countdownEl.innerText = seconds;

                var iv = setInterval(function(){
                    seconds--;
                    if (countdownEl) countdownEl.innerText = seconds;
                    if (seconds <= 0) {
                        clearInterval(iv);
                        // try hide modal if present
                        try {
                            var modalEl = document.getElementById('ajaxSuccessModal') || document.querySelector('.modal');
                            if (modalEl) {
                                try { var m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); m.hide(); } catch(e){}
                            }
                        } catch(e){}

                        // try close the window/tab
                        try { window.open('','_self'); window.close(); } catch(e) {}

                        // fallback: replace body with inactive message
                        setTimeout(function(){
                            try { document.body.innerHTML = "<div style='display:flex; height:100vh; justify-content:center; align-items:center; color:#64748b;'>Halaman sudah tidak aktif. Silakan tutup tab ini.</div>"; } catch(e){}
                        }, 500);
                    }
                }, 1000);
            })();
        }
    </script>
