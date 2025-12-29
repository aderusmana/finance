<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Rincian Bank Garansi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body style="background-color: #f0f2f5; font-family: 'Inter', sans-serif; color: #344767;">

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            {{-- Main Card --}}
            <div class="main-card" style="border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); background: #fff; overflow: hidden;">

                {{-- Header Brand --}}
                <div class="header-brand" style="background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); color: white; padding: 30px;">
                    <h3 class="fw-bold mb-1">Formulir Bank Garansi</h3>
                    <p class="mb-0 opacity-75"><i class="bi bi-building me-2"></i>{{ $rec->customer->name }}</p>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <div>Mohon periksa kembali inputan Anda.</div>
                        </div>
                    @endif

                    {{-- Info Box --}}
                    <div class="info-box" style="background-color: #e7f1ff; border-left: 5px solid #0d6efd; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <small class="text-uppercase text-muted fw-bold ls-1">Limit Disetujui</small>
                                <h2 class="text-primary fw-bold mb-0">Rp {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}</h2>
                            </div>
                            <div class="text-end mt-2 mt-md-0">
                                <span class="badge bg-white text-primary border border-primary px-3 py-2 rounded-pill">
                                    <i class="bi bi-clock-history me-1"></i> Form Expiring Soon
                                </span>
                            </div>
                        </div>
                        <hr class="my-3 text-primary opacity-25">
                        <small class="text-muted">Mohon masukkan rincian pembagian bank, alamat cabang, dan PIC yang dapat dihubungi.</small>
                    </div>

                    <form action="{{ route('customer.portal.store-input', $token) }}" method="POST" id="bgForm">
                        @csrf

                        <div id="bank-rows">
                            @php
                                $details = old('details', [
                                    [
                                        'bank_name' => '',
                                        'branch_name' => '',
                                        'bank_address' => '',
                                        'contact_person' => '',
                                        'nominal' => ''
                                    ]
                                ]);
                            @endphp

                            @foreach($details as $index => $detail)
                            {{-- Bank Row --}}
                            <div class="bank-row fade-in" style="background-color: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 25px; margin-bottom: 20px; position: relative;">

                                @if($index > 0)
                                    {{-- Btn Remove --}}
                                    <div class="btn-remove remove-row" title="Hapus Bank" style="position: absolute; top: 15px; right: 15px; color: #dc3545; cursor: pointer; transition: 0.2s; background: #fff0f0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="bi bi-trash"></i>
                                    </div>
                                @endif

                                <h6 class="text-primary mb-3 fw-bold"><i class="bi bi-bank me-2"></i>Bank {{ $index + 1 }}</h6>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Nama Bank <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="details[{{ $index }}][bank_name]"
                                               class="form-control @error('details.'.$index.'.bank_name') is-invalid @enderror"
                                               style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;"
                                               placeholder="Contoh: BCA / Mandiri"
                                               value="{{ old('details.'.$index.'.bank_name', $detail['bank_name']) }}"
                                               required>
                                        @error('details.'.$index.'.bank_name')
                                            <div class="invalid-feedback" style="display: block; font-size: 0.8em; margin-top: 5px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Cabang Bank</label>
                                        <input type="text"
                                               name="details[{{ $index }}][branch_name]"
                                               class="form-control"
                                               style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;"
                                               placeholder="Contoh: KCU Sudirman"
                                               value="{{ old('details.'.$index.'.branch_name', $detail['branch_name'] ?? '') }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Alamat Bank</label>
                                        <textarea name="details[{{ $index }}][bank_address]"
                                                  class="form-control"
                                                  rows="2"
                                                  style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;"
                                                  placeholder="Alamat lengkap bank penerbit">{{ old('details.'.$index.'.bank_address', $detail['bank_address'] ?? '') }}</textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Contact Person (PIC)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                            <input type="text"
                                                   name="details[{{ $index }}][contact_person]"
                                                   class="form-control"
                                                   style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;"
                                                   placeholder="Nama PIC Bank"
                                                   value="{{ old('details.'.$index.'.contact_person', $detail['contact_person'] ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Nominal Pengajuan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp</span>
                                            <input type="text"
                                                name="details[{{ $index }}][nominal]"
                                                class="form-control nominal-input rupiah-format fw-bold text-end @error('details.'.$index.'.nominal') is-invalid @enderror"
                                                style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;"
                                                placeholder="0"
                                                value="{{ old('details.'.$index.'.nominal', $detail['nominal']) }}"
                                                required>
                                            @error('details.'.$index.'.nominal')
                                                <div class="invalid-feedback" style="display: block; font-size: 0.8em; margin-top: 5px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Btn Add Row --}}
                        <button type="button" class="btn w-100 py-3 mb-5" id="addBankRow" style="border-style: dashed; border-width: 2px; color: #0d6efd; font-weight: 600;">
                            <i class="bi bi-plus-circle-dotted me-2"></i> Tambah Bank Lain
                        </button>

                        {{-- Sticky Footer --}}
                        <div class="sticky-footer rounded-bottom" style="background: white; padding: 20px; border-top: 1px solid #eee; position: sticky; bottom: 0; z-index: 10; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-bold">Total Pengajuan</span>
                                <h4 class="fw-bold text-primary mb-0" id="totalNominalDisplay">Rp 0</h4>
                            </div>
                            <button type="button" id="btnSubmit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                                <i class="bi bi-send-check me-2"></i> Simpan & Lanjutkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-4 small">&copy; {{ date('Y') }} PT Sinar Meadow International Indonesia</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const limitApproved = Math.floor({{ $rec->credit_limit_updated ?? $rec->recommended_credit_limit ?? 0 }});
        let rowCount = document.querySelectorAll('.bank-row').length;

        const container = document.getElementById('bank-rows');
        const btnAdd = document.getElementById('addBankRow');
        const totalDisplay = document.getElementById('totalNominalDisplay');
        const form = document.getElementById('bgForm');
        const btnSubmit = document.getElementById('btnSubmit');

        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        function cleanNumber(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(/\./g, '')) || 0;
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.nominal-input').forEach(input => {
                total += cleanNumber(input.value);
            });

            totalDisplay.innerText = formatter.format(total);
            totalDisplay.classList.remove('text-primary', 'text-warning', 'text-danger');

            if (total < limitApproved) {
                totalDisplay.classList.add('text-danger');
            } else if (total > limitApproved) {
                totalDisplay.classList.add('text-danger');
            } else {
                totalDisplay.classList.add('text-primary');
            }
            return total;
        }

        document.querySelectorAll('.rupiah-format').forEach(input => {
            if(input.value) input.value = formatRupiah(input.value);
        });
        updateTotal();

        container.addEventListener('input', function(e) {
            if (e.target.classList.contains('nominal-input')) {
                e.target.value = formatRupiah(e.target.value);
                updateTotal();
            }
        });

        // --- UPDATE PADA JS: Style Inline dimasukkan juga ke string HTML ---
        btnAdd.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('bank-row', 'fade-in', 'mt-3');

            // Inject Inline Style untuk Row
            newRow.style.cssText = "background-color: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 25px; margin-bottom: 20px; position: relative;";

            newRow.innerHTML = `
                <div class="btn-remove remove-row" title="Hapus Bank" style="position: absolute; top: 15px; right: 15px; color: #dc3545; cursor: pointer; transition: 0.2s; background: #fff0f0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%;"><i class="bi bi-trash"></i></div>
                <h6 class="text-primary mb-3 fw-bold"><i class="bi bi-bank me-2"></i>Bank ${rowCount + 1}</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Nama Bank <span class="text-danger">*</span></label>
                        <input type="text" name="details[${rowCount}][bank_name]" class="form-control" style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;" placeholder="Contoh: BCA / Mandiri" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Cabang Bank</label>
                        <input type="text" name="details[${rowCount}][branch_name]" class="form-control" style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;" placeholder="Contoh: KCU Sudirman">
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Alamat Bank</label>
                        <textarea name="details[${rowCount}][bank_address]" class="form-control" rows="2" style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;" placeholder="Alamat lengkap bank penerbit"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Contact Person (PIC)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="details[${rowCount}][contact_person]" class="form-control" style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;" placeholder="Nama PIC Bank">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 500; font-size: 0.9rem; color: #495057; margin-bottom: 8px;">Nominal Pengajuan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="text" name="details[${rowCount}][nominal]" class="form-control nominal-input rupiah-format fw-bold text-end" style="padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.95rem;" placeholder="0" required>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            rowCount++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.bank-row').remove();
                updateTotal();
            }
        });

        btnSubmit.addEventListener('click', function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let currentTotal = updateTotal();
            let formattedTotal = formatter.format(currentTotal);

            Swal.fire({
                title: 'Konfirmasi Simpan',
                html: `
                    <p>Total Pengajuan: <strong>${formattedTotal}</strong></p>
                    <p class="mb-0">Apakah data bank dan nominal yang Anda masukkan sudah benar?</p>
                    <small class="text-muted">Pastikan tidak ada kesalahan pengetikan.</small>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) submitForm();
            });
        });

        function submitForm() {
            Swal.fire({
                title: 'Memproses...', text: 'Mohon tunggu sebentar',
                allowOutsideClick: false, didOpen: () => Swal.showLoading()
            });

            // Tetap bersihkan titik sebelum kirim ke server
            document.querySelectorAll('.nominal-input').forEach(input => {
                input.value = cleanNumber(input.value);
            });

            form.submit();
        }
    });
</script>
</body>
</html>
