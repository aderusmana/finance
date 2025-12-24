<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Approval - {{ $submission->form_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: #f3f4f6; font-family: 'Inter', sans-serif; color: #1f2937; padding-bottom: 40px; margin: 0;">

    <div style="background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 15px 0; margin-bottom: 30px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div style="font-weight: 700; color: #111827; letter-spacing: -0.5px; font-size: 1.25rem;">
                    <i class="bi bi-grid-3x3-gap-fill text-warning me-2"></i> PT. Sinar Meadow
                </div>
                <div class="text-muted small">
                    Approval System &bull; {{ date('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <form id="approvalForm" action="{{ route('approval.submit', $token) }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="{{ $action }}">

            <div class="row g-4">
                
                <div class="col-lg-8">
                    
                    <div style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; margin-bottom: 24px; overflow: hidden;">
                        <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6; background-color: #ffffff;">
                            <h5 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-person-lines-fill text-primary"></i> Data Customer & Analisa (Lampiran D)
                            </h5>
                        </div>
                        <div style="padding: 24px;">
                            @php
                                $rec = $submission->recommendation;
                                $cust = $rec->customer;
                            @endphp
                            
                            <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">1. Nama Distributor</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">{{ $cust->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">2. Kota / Wilayah</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">{{ $cust->city }} / {{ $cust->area ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">3. Periode</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">
                                        @php
                                            $periods = $rec->periods;
                                            $pStart = $periods->min('period_date');
                                            $pEnd   = $periods->max('period_date');
                                        @endphp
                                        
                                        @if($pStart && $pEnd)
                                            {{ \Carbon\Carbon::parse($pStart)->isoFormat('MMMM Y') }} - {{ \Carbon\Carbon::parse($pEnd)->isoFormat('MMMM Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">4. Rata-rata Penjualan</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">Rp {{ number_format($rec->average, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">5. Syarat Pembayaran (TOP)</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">{{ $rec->top }} Hari</td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">6. Lead Time</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">{{ $rec->lead_time }} Hari</td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">7. Faktor Fluktuasi</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">{{ $rec->inflation }}%</td>
                                </tr>
                                <tr style="background-color: #ffffeb;">
                                    <td style="width: 40%; color: #111827; font-size: 0.9rem; font-weight: 600; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">8. Limit Kredit (Updated)</td>
                                    <td style="width: 60%; color: #0d6efd; font-weight: 700; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: 1px solid #f3f4f6; vertical-align: top;">Rp {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; color: #6b7280; font-size: 0.9rem; font-weight: 500; padding: 12px 0; border-bottom: none; vertical-align: top;">9. Nilai BG Ditetapkan</td>
                                    <td style="width: 60%; color: #111827; font-weight: 600; font-size: 0.95rem; text-align: right; padding: 12px 0; border-bottom: none; vertical-align: top;">Rp {{ number_format($rec->set_bg, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; margin-bottom: 24px; overflow: hidden;">
                        <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6; background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                            <h5 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-bank2 text-success"></i> Rincian Pengajuan Bank Garansi
                            </h5>
                            <span style="background-color: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                Total: Rp {{ number_format($bg->bg_nominal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="padding: 24px;">
                            @if($bg && $bg->details->count() > 0)
                                @foreach($bg->details as $detail)
                                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div style="font-weight: 700; color: #1e40af; font-size: 1rem; margin-bottom: 4px;">{{ $detail->bank_name }}</div>
                                                <div style="font-size: 0.85rem; color: #4b5563; margin-bottom: 4px;">
                                                    <i class="bi bi-geo-alt me-1"></i> {{ $detail->branch_name ?? 'Cabang -' }}
                                                </div>
                                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">
                                                    {{ $detail->bank_address ?? 'Alamat tidak tersedia' }}
                                                </div>
                                            </div>
                                            <div style="font-weight: 700; color: #059669; font-size: 1rem; text-align: right; margin-top: 8px;">
                                                Rp {{ number_format($detail->nominal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3">Tidak ada data rincian bank.</div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <div style="font-weight: 700; color: #6b7280;">TOTAL NILAI BG DISERAHKAN</div>
                                <div style="font-size: 1.1rem; font-weight: 700; color: #111827;">Rp {{ number_format($bg->bg_nominal ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; margin-bottom: 24px; overflow: hidden; position: sticky; top: 20px;">
                        
                        <div style="padding: 25px; text-align: center; color: white; background: {{ $action == 'reject' ? 'linear-gradient(135deg, #ef4444, #b91c1c)' : 'linear-gradient(135deg, #f59e0b, #d97706)' }};">
                            @if($action == 'reject')
                                <i class="bi bi-x-octagon-fill" style="font-size: 2.5rem; margin-bottom: 10px; display: block; opacity: 0.9;"></i>
                                <div style="font-size: 1.25rem; font-weight: 700; margin-bottom: 5px;">Reject Submission</div>
                                <div style="font-size: 0.9rem; opacity: 0.9;">Dokumen akan dikembalikan ke Admin</div>
                            @else
                                <i class="bi bi-pencil-square" style="font-size: 2.5rem; margin-bottom: 10px; display: block; opacity: 0.9;"></i>
                                <div style="font-size: 1.25rem; font-weight: 700; margin-bottom: 5px;">Review with Notes</div>
                                <div style="font-size: 0.9rem; opacity: 0.9;">Setujui dokumen dengan catatan revisi</div>
                            @endif
                        </div>

                        <div style="padding: 24px;">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    Catatan / Pesan <span class="text-danger">*</span>
                                </label>
                                <textarea id="noteTextarea" name="notes" class="form-control" rows="8" placeholder="Tuliskan catatan revisi atau alasan penolakan secara detail..." style="font-size: 0.95rem; border-radius: 8px; border-color: #d1d5db; padding: 12px;" required></textarea>
                                <div class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> Catatan ini akan dikirimkan melalui email.
                                </div>
                            </div>

                            <div class="d-grid gap-3">
                                <button type="submit" id="btnSubmit" style="padding: 14px; font-weight: 600; border-radius: 8px; font-size: 1rem; transition: all 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; border: none; cursor: pointer; color: white; background: {{ $action == 'reject' ? '#ef4444' : '#f59e0b' }};">
                                    <i class="bi bi-paperplane-fill me-2"></i> Kirim Keputusan
                                </button>
                                <a href="#" onclick="window.close()" style="padding: 14px; font-weight: 600; border-radius: 8px; font-size: 1rem; transition: all 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; display: block; text-align: center; border: 1px solid #d1d5db; color: #4b5563; background: white; text-decoration: none;">
                                    Batal & Tutup
                                </a>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="text-center mt-3 text-muted small">
                        &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        document.getElementById('approvalForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Stop submit otomatis

            let noteValue = document.getElementById('noteTextarea').value.trim();
            
            if (!noteValue) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Catatan tidak boleh kosong!',
                });
                return;
            }

            let hasLetters = /[a-zA-Z]/.test(noteValue);
            
            if (!hasLetters) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Tidak Valid',
                    text: 'Mohon tuliskan catatan yang jelas menggunakan huruf/kata. Jangan hanya angka atau simbol.',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan catatan revisi/penolakan yang Anda tulis sudah sesuai.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '{{ $action == "reject" ? "#ef4444" : "#f59e0b" }}',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim Sekarang',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik Ya, baru submit form secara manual
                    this.submit();
                }
            });
        });
    </script>

</body>
</html>