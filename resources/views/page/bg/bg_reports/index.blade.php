<x-app-layout>
    @section('title', 'Document Center')
    @include('components.sample-table-styles')

    {{-- HEADER & BREADCRUMB --}}
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Document Center</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Reports & Documents</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- INFO BANNER --}}
            <div class="alert alert-info d-flex align-items-center mb-4 border-0 shadow-sm" style="background-color: #e3f2fd; color: #0d47a1;">
                <i class="ph-fill ph-info me-3 fs-3"></i>
                <div>
                    <strong>Pusat Kendali Dokumen</strong>
                    <div class="small mt-1">
                        Halaman ini berfungsi sebagai cadangan (backup) untuk mencetak dokumen PDF secara manual jika email gagal terkirim atau file fisik hilang.
                        Dokumen yang didownload dari sini akan selalu menggunakan <b>data terbaru</b> dari database.
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-transparent">
                <div class="card-body p-0">

                    {{-- TAB NAVIGATION --}}
                    <ul class="nav nav-pills nav-pills-custom mb-4" id="reportTabs" role="tablist">

                        {{-- TOMBOL TAB 1: TRANSACTION --}}
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link active w-100" id="trans-tab" data-bs-toggle="pill" data-bs-target="#trans" type="button">
                                <i class="ph-bold ph-files"></i>
                                <div>
                                    <div class="fw-bold fs-6">Transaction Documents</div>
                                    <div class="small text-muted">Lampiran D & Formulir Pengajuan (Per Transaksi)</div>
                                </div>
                            </button>
                        </li>

                        {{-- TOMBOL TAB 2: LETTERS --}}
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="letters-tab" data-bs-toggle="pill" data-bs-target="#letters" type="button">
                                <i class="ph-bold ph-envelope-open"></i>
                                <div>
                                    <div class="fw-bold fs-6">Expiring Letters</div>
                                    <div class="small text-muted">Surat Pemberitahuan Jatuh Tempo (Per BG Aktif)</div>
                                </div>
                            </button>
                        </li>

                    </ul>

                    {{-- ISI KONTEN --}}
                    <div class="tab-content bg-white p-4 rounded shadow-sm border" id="reportTabContent">

                        {{-- KONTEN TAB 1: TRANSACTION LIST --}}
                        <div class="tab-pane fade show active" id="trans" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="ph-bold ph-list me-2 text-primary"></i>Daftar Dokumen Transaksi
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="w-100 display align-middle" id="sampleTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Date</th>
                                            <th>Reference Code</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        {{-- KONTEN TAB 2: LETTERS LIST --}}
                        <div class="tab-pane fade" id="letters" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="ph-bold ph-alarm me-2 text-warning"></i>Daftar BG Jatuh Tempo
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="w-100 display align-middle" id="historyTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>No</th>
                                            <th>BG Number</th>
                                            <th>Customer</th>
                                            <th>Exp. Date</th>
                                            <th>Nominal</th>
                                            <th class="text-center">Download</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {

            // --- TABLE 1: TRANSACTIONS ---
            var transTable = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('bg-reports.index') }}", data: { type: 'transactions' } },
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center'},
                    {data: 'date', name: 'created_at'},
                    {data: 'form_code', name: 'form_code'},
                    {data: 'customer', name: 'recommendation.customer.name'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ]
            });

            // --- TABLE 2: LETTERS ---
            var lettersTable = $('#historyTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('bg-reports.index') }}", data: { type: 'expiring' } },
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center'},
                    {data: 'bg_number', name: 'bg_number'},
                    {data: 'customer', name: 'customer.name'},
                    {data: 'exp_date', name: 'exp_date'},
                    {data: 'nominal', name: 'bg_nominal'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ]
            });

            // Reload table saat tab diklik agar layout pas
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                transTable.columns.adjust().draw();
                lettersTable.columns.adjust().draw();
            });
        });
    </script>
    @endpush
</x-app-layout>
