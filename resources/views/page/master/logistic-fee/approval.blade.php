<x-app-layout>
    @section('title', 'Logistic Fee Approvals')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Fee Approvals</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph ph-check-square-offset f-s-16"></i> Tasks</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Fee Approvals</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tgl Pengajuan</th>
                                    <th>Customer</th>
                                    <th>Distributor</th>
                                    <th>Harga Lama</th>
                                    <th>Pengajuan Baru</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TINJAU APPROVAL (DESAIN NGEPRESS) --}}
    <div class="modal fade" id="modalApproval" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-3" style="width: 40px; height: 40px;">
                            <i class="ph-bold ph-check-circle fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Tinjau Perubahan Harga</h5>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                
                <form id="approvalForm">
                    <div class="modal-body p-3 bg-light">
                        <input type="hidden" id="log_id">
                        
                        <div class="bg-white border rounded-3 p-3 shadow-sm mb-3">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="text-muted" width="40%">Distributor</td><td class="fw-bold text-dark" id="txt_distributor">: -</td></tr>
                                <tr><td class="text-muted">Customer</td><td class="fw-bold text-dark" id="txt_customer">: -</td></tr>
                            </table>
                        </div>

                        <div class="d-flex align-items-center justify-content-between bg-white border rounded-3 p-3 shadow-sm mb-3">
                            <div class="text-center w-100">
                                <small class="text-muted d-block fw-bold">Harga Saat Ini</small>
                                <span class="fs-5 text-secondary text-decoration-line-through" id="txt_old_fee">-</span>
                            </div>
                            <i class="ph-bold ph-arrow-right text-primary fs-3 px-3"></i>
                            <div class="text-center w-100">
                                <small class="text-muted d-block fw-bold">Pengajuan Baru</small>
                                <span class="fs-5 fw-bold text-primary" id="txt_new_fee">-</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="fw-semibold text-secondary small mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea id="notes" class="form-control form-control-sm" rows="2" placeholder="Ketik alasan jika ditolak / catatan tambahan jika disetujui..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-danger px-4 rounded-pill fw-semibold" onclick="submitApproval('reject')"><i class="ph-bold ph-x-circle me-1"></i> Tolak</button>
                        <button type="button" class="btn btn-primary px-4 rounded-pill fw-semibold" onclick="submitApproval('approve')"><i class="ph-bold ph-check-circle me-1"></i> Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let table;
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            table = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('logistic-fees.approval.list') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'date', name: 'created_at' },
                    { data: 'customer', name: 'customer' },
                    { data: 'distributor', name: 'distributor' },
                    { data: 'old_fee', name: 'old_fee' },
                    { data: 'new_fee', name: 'new_fee' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // Tinjau Button
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                Swal.fire({ title: 'Memuat...', didOpen: () => Swal.showLoading() });
                $.get("{{ url('/logistic-fees-approval') }}/" + id, function(data) {
                    Swal.close();
                    $('#log_id').val(data.log_id);
                    $('#txt_distributor').text(': ' + data.distributor);
                    $('#txt_customer').text(': ' + data.customer);
                    $('#txt_old_fee').text(data.old_fee);
                    $('#txt_new_fee').text(data.new_fee);
                    $('#notes').val('');
                    $('#modalApproval').modal('show');
                });
            });

            // Resend Email Button
            $(document).on('click', '.btn-resend', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Kirim Ulang Email?', text: "Email notifikasi approval akan dikirim ulang ke email Anda.",
                    icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Kirim'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Mengirim...', didOpen: () => Swal.showLoading() });
                        $.post("{{ url('/logistic-fees-approval/resend') }}/" + id, function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                        }).fail(function() { Swal.fire('Error', 'Gagal mengirim email.', 'error'); });
                    }
                });
            });
        });

        function submitApproval(action) {
            let id = $('#log_id').val();
            let notes = $('#notes').val();
            let actText = action === 'approve' ? 'Menyetujui' : 'Menolak';

            // --- TAMBAHAN VALIDASI REJECT ---
            if (action === 'reject' && notes.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Diperlukan!',
                    text: 'Anda wajib mengisi Catatan Tambahan (alasan) apabila menolak pengajuan ini.'
                });
                
                // Ubah border textarea jadi merah sebentar dan fokuskan kursor
                $('#notes').addClass('is-invalid').focus();
                setTimeout(() => $('#notes').removeClass('is-invalid'), 3000);
                
                return; // Hentikan eksekusi, jangan kirim ke server
            }
            // --------------------------------

            Swal.fire({
                title: actText + ' Pengajuan?', 
                icon: 'warning',
                showCancelButton: true, 
                confirmButtonText: 'Ya, Proses',
                confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    $.post("{{ url('/logistic-fees-approval/process') }}/" + id, { action: action, notes: notes }, function(res) {
                        $('#modalApproval').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Selesai!', res.message, 'success');
                    }).fail(function(err) {
                        let errMsg = 'Terjadi kesalahan sistem.';
                        // Tangkap error validasi dari backend jika ada
                        if(err.responseJSON && err.responseJSON.errors && err.responseJSON.errors.notes) {
                            errMsg = err.responseJSON.errors.notes[0];
                        }
                        Swal.fire('Gagal', errMsg, 'error');
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>