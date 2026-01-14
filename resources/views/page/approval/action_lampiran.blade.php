<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Approval - {{ $submission->form_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Menggunakan Phosphor Icons agar lebih modern --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #334155; display: flex; flex-direction: column; min-height: 100vh;}
        .sticky-panel { position: sticky; top: 20px; }
        /* Agar footer selalu di bawah jika konten sedikit */
        .main-content { flex: 1; }
    </style>
</head>

<body>

    {{-- NAVBAR SEDERHANA --}}
    <div style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 16px 0; margin-bottom: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div style="font-weight: 800; color: #0f172a; font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: #2563eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="ph-bold ph-shield-check"></i>
                    </div>
                    PT. Sinar Meadow
                </div>
                <div style="font-size: 0.85rem; color: #64748b; background: #f1f5f9; padding: 6px 12px; border-radius: 20px; font-weight: 600;">
                    Approval System &bull; {{ date('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- WRAPPER KONTEN UTAMA --}}
    <div class="container main-content">
        <form id="approvalForm" action="{{ route('approval.submit', $token) }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="{{ $action }}">

            <div class="row g-4">

                {{-- KOLOM KIRI: INFORMASI UTAMA --}}
                <div class="col-lg-8">

                    {{-- 1. CARD DATA CUSTOMER (LAMPIRAN D) --}}
                    <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; margin-bottom: 24px; overflow: hidden;">

                        {{-- Card Header --}}
                        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff; display: flex; align-items: center; gap: 10px;">
                            <div style="background: #eff6ff; color: #2563eb; padding: 8px; border-radius: 8px;">
                                <i class="ph-bold ph-user-list fs-5"></i>
                            </div>
                            <div>
                                <h5 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Data Analisa (Lampiran D)</h5>
                                <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Rincian data keuangan dan limit kredit customer.</p>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div style="padding: 24px;">
                            @php
                                $rec = $submission->recommendation;
                                $cust = $rec->customer;
                            @endphp

                            {{-- Grid Layout untuk Data --}}
                            <div class="row g-3">
                                {{-- Item Data --}}
                                @php
                                    $dataItems = [
                                        ['label' => 'Nama Distributor', 'value' => $cust->name, 'full' => true],
                                        ['label' => 'Kota / Wilayah', 'value' => $cust->city . ' / ' . ($cust->area ?? '-'), 'full' => true],
                                        ['label' => 'Rata-rata Penjualan', 'value' => 'Rp ' . number_format($rec->average, 0, ',', '.'), 'highlight' => false],
                                        ['label' => 'Limit Kredit (Updated)', 'value' => 'Rp ' . number_format($rec->credit_limit_updated, 0, ',', '.'), 'highlight' => true, 'color' => '#2563eb'],
                                        ['label' => 'TOP', 'value' => $rec->top . ' Hari'],
                                        ['label' => 'Lead Time', 'value' => $rec->lead_time . ' Hari'],
                                        ['label' => 'Faktor Fluktuasi', 'value' => number_format((float)($rec->inflation ?? 130), 0, ',', '.') . '%'],
                                        ['label' => 'Nilai BG Ditetapkan', 'value' => 'Rp ' . number_format($rec->set_bg, 0, ',', '.')],
                                    ];
                                @endphp

                                @foreach($dataItems as $item)
                                <div class="{{ isset($item['full']) ? 'col-12' : 'col-md-6' }}">
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px;">
                                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">
                                            {{ $item['label'] }}
                                        </div>
                                        <div style="font-size: 1rem; font-weight: 700; color: {{ $item['color'] ?? '#334155' }};">
                                            {{ $item['value'] }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                {{-- Total BG Card Khusus --}}
                                <div class="col-12">
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 50%;">
                                                <i class="ph-bold ph-money fs-4"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.8rem; color: #166534; font-weight: 700; text-transform: uppercase;">Total BG Diserahkan</div>
                                                <div style="font-size: 0.85rem; color: #15803d;">Akumulasi dari rincian bank</div>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.5rem; font-weight: 800; color: #15803d;">
                                            Rp {{ number_format($totalBgDiserahkan ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. CARD RINCIAN BANK --}}
                    <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; overflow: hidden;">
                        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff; display: flex; align-items: center; gap: 10px;">
                            <div style="background: #f0f9ff; color: #0ea5e9; padding: 8px; border-radius: 8px;">
                                <i class="ph-bold ph-bank fs-5"></i>
                            </div>
                            <div>
                                <h5 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Rincian Bank</h5>
                                <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Daftar bank garansi yang diajukan.</p>
                            </div>
                        </div>

                        <div style="padding: 0;">
                            @if($bgs->count() > 0)
                                @foreach($bgs as $bg)
                                    @php $detail = $bg->details->first(); @endphp
                                    <div style="padding: 16px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 16px;">
                                            <div style="width: 42px; height: 42px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #64748b;">
                                                <i class="ph-bold ph-buildings"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                                                    {{ $detail->bank_name ?? 'Bank Name' }}
                                                </div>
                                                <div style="font-size: 0.8rem; color: #64748b;">
                                                    {{ $detail->branch_name ?? 'Cabang Utama' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-weight: 700; color: #334155; font-size: 1rem;">
                                                Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #10b981; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                                <i class="ph-fill ph-check-circle"></i> Verified
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="padding: 30px; text-align: center; color: #94a3b8;">
                                    <i class="ph-duotone ph-file-dashed fs-1 mb-2"></i>
                                    <p>Tidak ada data bank.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: ACTION PANEL --}}
                <div class="col-lg-4">
                    <div class="sticky-panel" style="background: #ffffff; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; overflow: hidden;">

                        {{-- Header Action --}}
                        <div style="padding: 30px 24px; text-align: center; color: white; background: {{ $action == 'reject' ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #3b82f6, #2563eb)' }}; position: relative; overflow: hidden;">
                            {{-- Pattern Background --}}
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 10px 10px;"></div>

                            <div style="position: relative; z-index: 1;">
                                @if($action == 'reject')
                                    <div style="background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                        <i class="ph-bold ph-x fs-2"></i>
                                    </div>
                                    <h4 style="font-weight: 800; margin-bottom: 4px;">Reject Submission</h4>
                                    <p style="font-size: 0.9rem; margin: 0; opacity: 0.9;">Kembalikan dokumen ke Admin</p>
                                @else
                                    <div style="background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                        <i class="ph-bold ph-check fs-2"></i>
                                    </div>
                                    <h4 style="font-weight: 800; margin-bottom: 4px;">Approve Submission</h4>
                                    <p style="font-size: 0.9rem; margin: 0; opacity: 0.9;">Setujui dokumen & terbitkan Lampiran D</p>
                                @endif
                            </div>
                        </div>

                        {{-- Form Body --}}
                        <div style="padding: 24px;">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                                    Catatan / Pesan <span class="text-danger">*</span>
                                </label>
                                <textarea id="noteTextarea" name="notes" class="form-control" rows="5"
                                    placeholder="Tuliskan catatan revisi atau alasan penolakan secara detail..."
                                    style="font-size: 0.95rem; border-radius: 10px; border: 1px solid #cbd5e1; padding: 12px; resize: none; background: #f8fafc;"
                                    required></textarea>
                                <div class="form-text text-muted" style="font-size: 0.8rem; margin-top: 8px;">
                                    <i class="ph-bold ph-info me-1"></i> Catatan ini wajib diisi dan akan terkirim via email.
                                </div>
                            </div>

                            <div class="d-grid gap-3">
                                <button type="submit" id="btnSubmit"
                                    style="padding: 16px; font-weight: 700; border-radius: 50px; font-size: 1rem; transition: all 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; border: none; cursor: pointer; color: white; background: {{ $action == 'reject' ? '#ef4444' : '#2563eb' }}; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <i class="ph-bold ph-paper-plane-right fs-5"></i> Kirim Keputusan
                                </button>

                                <a href="#" onclick="window.close()"
                                    style="padding: 12px; font-weight: 600; border-radius: 50px; font-size: 0.95rem; width: 100%; display: block; text-align: center; color: #64748b; background: transparent; text-decoration: none; border: 1px solid transparent; transition: 0.2s;">
                                    Batal & Tutup
                                </a>
                            </div>
                        </div>

                    </div>

                    {{-- COPYRIGHT DIHAPUS DARI SINI --}}
                </div>

            </div>
        </form>
    </div>

    {{-- FOOTER GLOBAL BARU --}}
    <footer style="margin-top: 60px; padding: 30px 0; background-color: #ffffff; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 0.85rem; width: 100%;">
        &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia. All rights reserved.
    </footer>

    <script>
        document.getElementById('approvalForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let noteValue = document.getElementById('noteTextarea').value.trim();

            if (!noteValue) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Catatan tidak boleh kosong!',
                    confirmButtonColor: '#334155'
                });
                return;
            }

            let hasLetters = /[a-zA-Z]/.test(noteValue);

            if (!hasLetters) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Tidak Valid',
                    text: 'Mohon tuliskan catatan yang jelas menggunakan kata-kata.',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            let actionType = "{{ $action }}";
            let confirmColor = actionType === 'reject' ? '#ef4444' : '#2563eb';
            let confirmText = actionType === 'reject' ? 'Ya, Tolak!' : 'Ya, Setujui!';

            Swal.fire({
                title: 'Konfirmasi Keputusan',
                html: "Apakah Anda yakin ingin mengirim keputusan ini?<br>Tindakan ini tidak dapat dibatalkan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });
                    this.submit();
                }
            });
        });
    </script>

</body>
</html>
