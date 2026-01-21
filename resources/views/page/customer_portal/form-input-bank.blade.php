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
    <style>
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; color: #344767; }
        .main-card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); background: #fff; overflow: hidden; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15); border-color: #0d6efd; }
        .form-control[readonly] { background-color: #f8f9fa; color: #6c757d; border-color: #e9ecef; cursor: not-allowed; }
        .nominal-input { font-size: 1.1rem; letter-spacing: 0.5px; }
        .fade-in { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            @php
                $action = $action ?? 'new';
                $isExisting  = ($action === 'existing');
                $isExtension = ($action === 'extension');

                if ($isExisting && isset($existingBg)) {
                    $firstDetail = $existingBg->details->first();
                    $details = [
                        [
                            'bank_name'      => $firstDetail->bank_name ?? '',
                            'branch_name'    => $firstDetail->branch_name ?? '',
                            'bank_address'   => $firstDetail->bank_address ?? '',
                            'contact_person' => $firstDetail->contact_person ?? '',
                            'nominal'        => $existingBg->bg_nominal
                        ]
                    ];
                } else {
                    $details = old('details', [
                        [
                            'bank_name' => '',
                            'branch_name' => '',
                            'bank_address' => '',
                            'contact_person' => '',
                            'nominal' => ''
                        ]
                    ]);
                }
            @endphp

            <div class="main-card">

                <div class="header-brand" style="background: linear-gradient(135deg, {{ $isExisting ? '#6366f1 0%, #4338ca' : ($isExtension ? '#10b981 0%, #059669' : '#0d6efd 0%, #0043a8') }} 100%); color: white; padding: 30px;">
                    @if($isExisting)
                        <h3 class="fw-bold mb-1"><i class="bi bi-arrow-repeat me-2"></i>Update Bank Garansi (Existing)</h3>
                        <p class="mb-0 opacity-75">Perbarui Nominal untuk BG: <strong>{{ $existingBg->bg_number ?? '-' }}</strong></p>
                    @elseif($isExtension)
                        <h3 class="fw-bold mb-1"><i class="bi bi-plus-square me-2"></i>Pengajuan Tambahan (Extension)</h3>
                        <p class="mb-0 opacity-75"><i class="bi bi-building me-2"></i>{{ $rec->customer->name }}</p>
                    @else
                        <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-text me-2"></i>Formulir Bank Garansi Baru</h3>
                        <p class="mb-0 opacity-75"><i class="bi bi-building me-2"></i>{{ $rec->customer->name }}</p>
                    @endif
                </div>

                <div class="card-body p-4">
                    {{-- Alerts --}}
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center shadow-sm" role="alert">
                            <i class="bi bi-exclamation-circle me-3 fs-4"></i>
                            <div>Mohon periksa kembali inputan Anda yang berwarna merah.</div>
                        </div>
                    @endif

                    {{-- Info Box (Limit) --}}
                    @if(!$isExisting)
                    <div class="info-box" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Credit Limit Updated</small>
                                <div class="text-dark fw-bold fs-5">Rp {{ number_format($rec->credit_limit_updated ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                <i class="bi bi-info-circle me-1"></i> {{ $isExtension ? 'Extension Mode' : 'New Submission' }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('customer.portal.store-input', $token) }}" method="POST" id="bgForm">
                        @csrf

                        <div id="bank-rows">
                            @foreach($details as $index => $detail)
                            {{-- Bank Row --}}
                            <div class="bank-row fade-in" style="background-color: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 25px; margin-bottom: 20px; position: relative;">

                                {{-- Logic Hapus Row: Hanya muncul jika BUKAN existing DAN index > 0 --}}
                                @if(!$isExisting && $index > 0)
                                    <div class="btn-remove remove-row" title="Hapus Bank" style="position: absolute; top: 15px; right: 15px; color: #dc3545; cursor: pointer; transition: 0.2s; background: #fff0f0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="bi bi-trash"></i>
                                    </div>
                                @endif

                                <h6 class="text-primary mb-3 fw-bold">
                                    <i class="bi bi-bank me-2"></i>
                                    {{ $isExisting ? 'Data Bank Garansi' : 'Bank ' . ($index + 1) }}
                                </h6>

                                <div class="row g-3">
                                    {{-- NAMA BANK --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Nama Bank <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="details[{{ $index }}][bank_name]"
                                               class="form-control @error('details.'.$index.'.bank_name') is-invalid @enderror"
                                               placeholder="Contoh: BCA / Mandiri"
                                               value="{{ old('details.'.$index.'.bank_name', $detail['bank_name']) }}"
                                               {{ $isExisting ? 'readonly' : '' }} {{-- READONLY JIKA EXISTING --}}
                                               required>
                                        @error('details.'.$index.'.bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- CABANG --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Cabang Bank</label>
                                        <input type="text"
                                               name="details[{{ $index }}][branch_name]"
                                               class="form-control"
                                               placeholder="Contoh: KCU Sudirman"
                                               value="{{ old('details.'.$index.'.branch_name', $detail['branch_name'] ?? '') }}"
                                               {{ $isExisting ? 'readonly' : '' }}>
                                    </div>

                                    {{-- ALAMAT --}}
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Alamat Bank</label>
                                        <textarea name="details[{{ $index }}][bank_address]"
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Alamat lengkap bank penerbit"
                                                  {{ $isExisting ? 'readonly' : '' }}>{{ old('details.'.$index.'.bank_address', $detail['bank_address'] ?? '') }}</textarea>
                                    </div>

                                    {{-- PIC --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Contact Person (PIC)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                            <input type="text"
                                                   name="details[{{ $index }}][contact_person]"
                                                   class="form-control"
                                                   placeholder="Nama PIC Bank"
                                                   value="{{ old('details.'.$index.'.contact_person', $detail['contact_person'] ?? '') }}"
                                                   {{ $isExisting ? 'readonly' : '' }}>
                                        </div>
                                    </div>

                                    {{-- NOMINAL (SELALU EDITABLE) --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Nominal Pengajuan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text {{ $isExisting ? 'bg-indigo text-white' : 'bg-light fw-bold' }}" style="{{ $isExisting ? 'background-color: #4338ca;' : '' }}">Rp</span>
                                            <input type="text"
                                                name="details[{{ $index }}][nominal]"
                                                class="form-control nominal-input rupiah-format fw-bold text-end @error('details.'.$index.'.nominal') is-invalid @enderror"
                                                placeholder="0"
                                                value="{{ old('details.'.$index.'.nominal', number_format((float)$detail['nominal'], 0, '', '')) }}"
                                                required>
                                            @error('details.'.$index.'.nominal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if($isExisting)
                                            <div class="form-text text-primary"><i class="bi bi-info-circle me-1"></i> Silakan ubah nominal ini sesuai kebutuhan.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Btn Add Row: HANYA MUNCUL JIKA TIDAK EXISTING --}}
                        @if(!$isExisting)
                        <button type="button" class="btn w-100 py-3 mb-5 btn-outline-primary border-2 fw-bold" id="addBankRow" style="border-style: dashed;">
                            <i class="bi bi-plus-circle-dotted me-2"></i> Tambah Bank Lain
                        </button>
                        @endif

                        {{-- Sticky Footer --}}
                        <div class="sticky-footer rounded-bottom" style="background: white; padding: 20px; border-top: 1px solid #eee; position: sticky; bottom: 0; z-index: 10; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-bold">Total Pengajuan</span>
                                <h4 class="fw-bold text-primary mb-0" id="totalNominalDisplay">Rp 0</h4>
                            </div>
                            <button type="button" id="btnSubmit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="background: {{ $isExisting ? '#4338ca' : ($isExtension ? '#059669' : '#0d6efd') }}; border:none;">
                                <i class="bi bi-send-check me-2"></i>
                                @if($isExisting) Update Nominal @elseif($isExtension) Ajukan Extension @else Simpan & Lanjutkan @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-4 small">&copy; {{ date('Y') }} Financial System</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isExisting = @json($isExisting);
        const container = document.getElementById('bank-rows');
        const btnAdd = document.getElementById('addBankRow');
        const totalDisplay = document.getElementById('totalNominalDisplay');
        const form = document.getElementById('bgForm');
        const btnSubmit = document.getElementById('btnSubmit');

        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0
        });

        function updateRowNumbers() {
            if(isExisting) return; // Tidak perlu update nomor jika existing
            const rows = document.querySelectorAll('.bank-row');
            rows.forEach((row, index) => {
                const title = row.querySelector('h6');
                if(title) {
                    title.innerHTML = `<i class="bi bi-bank me-2"></i>Bank ${index + 1}`;
                }
                // Update attribute name untuk array laravel
                const inputs = row.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    let name = input.getAttribute('name');
                    if (name) {
                        let newName = name.replace(/details\[\d+\]/, `details[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        function formatRupiah(angka) {
            if(!angka) return '';
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
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
            return parseFloat(value.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.nominal-input').forEach(input => {
                total += cleanNumber(input.value);
            });
            totalDisplay.innerText = formatter.format(total);
        }

        // Init Formatting
        document.querySelectorAll('.rupiah-format').forEach(input => {
            if(input.value) input.value = formatRupiah(input.value);
        });
        updateTotal();

        // Event Listener Input Nominal
        container.addEventListener('input', function(e) {
            if (e.target.classList.contains('nominal-input')) {
                e.target.value = formatRupiah(e.target.value);
                updateTotal();
            }
        });

        // Event Listener Add Row (Hanya jika tombol ada)
        if(btnAdd) {
            btnAdd.addEventListener('click', function() {
                const tempIndex = document.querySelectorAll('.bank-row').length;

                const newRow = document.createElement('div');
                newRow.classList.add('bank-row', 'fade-in', 'mt-3');
                newRow.style.cssText = "background-color: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 25px; margin-bottom: 20px; position: relative;";

                newRow.innerHTML = `
                    <div class="btn-remove remove-row" title="Hapus Bank" style="position: absolute; top: 15px; right: 15px; color: #dc3545; cursor: pointer; transition: 0.2s; background: #fff0f0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%;"><i class="bi bi-trash"></i></div>

                    <h6 class="text-primary mb-3 fw-bold"><i class="bi bi-bank me-2"></i>Bank ${tempIndex + 1}</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" name="details[${tempIndex}][bank_name]" class="form-control" placeholder="Contoh: BCA / Mandiri" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Cabang Bank</label>
                            <input type="text" name="details[${tempIndex}][branch_name]" class="form-control" placeholder="Contoh: KCU Sudirman">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Alamat Bank</label>
                            <textarea name="details[${tempIndex}][bank_address]" class="form-control" rows="2" placeholder="Alamat lengkap bank penerbit"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Contact Person (PIC)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="details[${tempIndex}][contact_person]" class="form-control" placeholder="Nama PIC Bank">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nominal Pengajuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" name="details[${tempIndex}][nominal]" class="form-control nominal-input rupiah-format fw-bold text-end" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(newRow);
                updateRowNumbers();
            });
        }

        // Event Listener Delete Row
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.bank-row').remove();
                updateTotal();
                updateRowNumbers();
            }
        });

        // Submit Handler
        btnSubmit.addEventListener('click', function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let currentTotal = updateTotal(); // ensure calc is fresh
            let totalStr = totalDisplay.innerText;

            let titleText = isExisting ? 'Update Nominal?' : 'Konfirmasi Simpan';
            let msgText = isExisting
                ? 'Anda akan memperbarui nominal untuk Bank Garansi ini.'
                : 'Apakah data bank dan nominal yang Anda masukkan sudah benar?';

            Swal.fire({
                title: titleText,
                html: `
                    <p class="mb-2">${msgText}</p>
                    <h3 class="text-primary fw-bold">${totalStr}</h3>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isExisting ? '#4338ca' : '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Proses',
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

            // Clean number inputs before submit
            document.querySelectorAll('.nominal-input').forEach(input => {
                input.value = cleanNumber(input.value);
            });

            form.submit();
        }
    });
</script>
</body>
</html>
