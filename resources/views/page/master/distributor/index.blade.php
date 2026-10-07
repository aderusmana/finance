<x-app-layout>
    @section('title', 'Master Distributor')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 42px;
            padding: 4px 6px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 6px;
            margin: 0;
            display: inline-flex;
            align-items: center;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #1d4ed8 !important;
            margin-right: 5px;
            font-weight: bold;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #b91c1c !important;
        }
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection {
            background-color: #f1f5f9 !important;
            cursor: not-allowed !important;
            border-color: #cbd5e1 !important;
        }
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection__choice {
            background-color: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection__choice__remove {
            display: none !important;
        }
        .select2-container--bootstrap-5 .select2-search--inline .select2-search__field {
            margin-top: 3px;
        }
    </style>

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Master Distributor</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="ph-bold ph-plus"></i> Tambah Distributor
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Distributor</th>
                                    <th>Nama Distributor</th>
                                    <th>Email</th>
                                    <th>Email BuPot</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM --}}
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Distributor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">
                    <div class="modal-body">
                        {{-- OPSI LINK CUSTOMER --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih dari Master Customer <span class="text-muted fw-normal">(Opsional)</span></label>
                            <select name="customer_id" id="customer_id" class="form-select select2-customer" style="width: 100%;">
                                <option value="">-- Input Manual / Non-Customer --</option>
                                @if(isset($customers))
                                    @foreach ($customers as $c)
                                        @php
                                            $custEmails = array_values(array_unique(array_filter([
                                                $c->email,
                                                $c->purchasing_manager_email,
                                                $c->finance_manager_email
                                            ])));
                                        @endphp
                                        <option value="{{ $c->id }}" 
                                                data-code="{{ $c->code }}" 
                                                data-name="{{ $c->name }}" 
                                                data-email="{{ implode(', ', $custEmails) }}"
                                                data-emails='@json($custEmails)'>
                                            {{ $c->code }} - {{ $c->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text" style="font-size: 0.78rem;">
                                <i class="ph-bold ph-info"></i> Pilih customer jika ingin data Kode, Nama, dan Email terisi otomatis. Kosongkan jika ingin input manual.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Distributor <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: ID3455" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Distributor <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: PT. CITRA BHOGA JAYA" required>
                        </div>
                        <div class="mb-3">
                            @if(!empty($isFinance))
                                <label class="form-label fw-semibold">
                                    Email Distributor 
                                    <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.72rem;">
                                        <i class="ph-bold ph-lock"></i> Read Only (Sales)
                                    </span>
                                </label>
                                <select name="email[]" id="email" class="form-select select2-email" multiple="multiple" style="width: 100%;" disabled>
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.78rem;">
                                    <i class="ph-bold ph-lock me-1"></i> Email distributor hanya dapat diisi/diubah oleh tim Sales atau terisi otomatis dari Customer.
                                </div>
                            @else
                                <label class="form-label fw-semibold">
                                    Email Distributor <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <select name="email[]" id="email" class="form-select select2-email" multiple="multiple" style="width: 100%;">
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.78rem;">
                                    <i class="ph-bold ph-info"></i> Ketik email lalu tekan <kbd class="bg-light text-dark border">Enter</kbd> atau <kbd class="bg-light text-dark border">,</kbd> (koma). Bisa memasukkan lebih dari 1 email.
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            @if(!empty($isSales))
                                <label class="form-label fw-semibold">
                                    Email Bukti Potong (BuPot) 
                                    <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.72rem;">
                                        <i class="ph-bold ph-lock"></i> Read Only (Finance)
                                    </span>
                                </label>
                                <select name="bupot_email[]" id="bupot_email" class="form-select select2-bupot-email" multiple="multiple" style="width: 100%;" disabled>
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.78rem;">
                                    <i class="ph-bold ph-lock me-1"></i> Email BuPot hanya dapat diisi/diubah oleh tim Finance.
                                </div>
                            @else
                                <label class="form-label fw-semibold">
                                    Email Bukti Potong (BuPot) <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <select name="bupot_email[]" id="bupot_email" class="form-select select2-bupot-email" multiple="multiple" style="width: 100%;">
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.78rem;">
                                    <i class="ph-bold ph-info"></i> Ketik email lalu tekan <kbd class="bg-light text-dark border">Enter</kbd> atau <kbd class="bg-light text-dark border">,</kbd> (koma). Bisa memasukkan lebih dari 1 email untuk penerima BuPot.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="ph-bold ph-floppy-disk me-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const isSales = {{ !empty($isSales) ? 'true' : 'false' }};
        const isFinance = {{ !empty($isFinance) ? 'true' : 'false' }};
        let table;
        $(document).ready(function() {
            $('#customer_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                placeholder: '-- Input Manual / Non-Customer --',
                allowClear: true
            });

            $('#email').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                tags: true,
                tokenSeparators: [',', ';', ' '],
                placeholder: 'Ketik email lalu tekan Enter...',
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $('#bupot_email').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                tags: true,
                tokenSeparators: [',', ';', ' '],
                placeholder: 'Ketik email BuPot lalu tekan Enter...',
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $('#customer_id').on('change', function() {
                let selected = $(this).find('option:selected');
                let code = selected.data('code');
                let name = selected.data('name');
                let emails = selected.data('emails');

                if (code) {
                    $('#code').val(code);
                    $('#name').val(name);

                    $('#email').empty();
                    if (Array.isArray(emails) && emails.length > 0) {
                        emails.forEach(function(em) {
                            if (em) {
                                let newOption = new Option(em, em, true, true);
                                $('#email').append(newOption);
                            }
                        });
                    } else if (selected.data('email')) {
                        let splitEmails = String(selected.data('email')).split(/[,;]+/).map(s => s.trim()).filter(s => s.length > 0);
                        splitEmails.forEach(function(em) {
                            let newOption = new Option(em, em, true, true);
                            $('#email').append(newOption);
                        });
                    }
                    $('#email').trigger('change');
                }
            });

            table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('distributors.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'bupot_email', name: 'bupot_email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#mainForm').on('submit', function(e){
                e.preventDefault();

                let id = $('#dataId').val();
                let url = "{{ route('distributors.store') }}";
                let method = "POST";

                if(id) {
                    url = "{{ url('/distributors') }}/" + id;
                    method = "PUT";
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(err) {
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal menyimpan data';
                        if (err.responseJSON && err.responseJSON.errors) {
                            let errs = Object.values(err.responseJSON.errors).flat();
                            msg = errs.join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: msg
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('/distributors') }}/" + id, function(data) {
                    $('#dataId').val(data.id);
                    $('#customer_id').val(data.customer_id || '').trigger('change.select2');
                    $('#code').val(data.code);
                    $('#name').val(data.name);

                    $('#email').empty();
                    let emailList = data.email_list;
                    if (!emailList && data.email) {
                        emailList = data.email.split(/[,;]+/).map(s => s.trim()).filter(s => s.length > 0);
                    }
                    if (Array.isArray(emailList)) {
                        emailList.forEach(function(em) {
                            if (em) {
                                let newOption = new Option(em, em, true, true);
                                $('#email').append(newOption);
                            }
                        });
                    }
                    $('#email').trigger('change');

                    $('#bupot_email').empty();
                    let bupotEmailList = data.bupot_email_list;
                    if (!bupotEmailList && data.bupot_email) {
                        bupotEmailList = data.bupot_email.split(/[,;]+/).map(s => s.trim()).filter(s => s.length > 0);
                    }
                    if (Array.isArray(bupotEmailList)) {
                        bupotEmailList.forEach(function(em) {
                            if (em) {
                                let newOption = new Option(em, em, true, true);
                                $('#bupot_email').append(newOption);
                            }
                        });
                    }
                    $('#bupot_email').trigger('change');

                    if (isFinance) {
                        $('#email').prop('disabled', true);
                    }
                    if (isSales) {
                        $('#bupot_email').prop('disabled', true);
                    }

                    $('#modalTitle').text('Edit Distributor');
                    $('#modalForm').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/distributors') }}/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', res.message, 'success');
                            },
                            error: function(err) {
                                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal menghapus data';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });

        function openModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');
            $('#customer_id').val('').trigger('change.select2');
            $('#email').empty().trigger('change');
            $('#bupot_email').empty().trigger('change');
            if (isFinance) {
                $('#email').prop('disabled', true);
            }
            if (isSales) {
                $('#bupot_email').prop('disabled', true);
            }
            $('#modalTitle').text('Tambah Distributor');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
