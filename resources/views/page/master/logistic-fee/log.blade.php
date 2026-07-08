<x-app-layout>
    @section('title', 'Logistic Fee History Logs')
    @include('components.sample-table-styles')

    <div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem;">

        {{-- 1. HEADER BANNER MEWAH (TEMA VIOLET/AMETHYST) --}}
        <div class="row m-2 mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 1.25rem; padding: 2rem 2.5rem; color: white; box-shadow: 0 10px 25px rgba(126, 34, 206, 0.2); position: relative; overflow: hidden; margin-bottom: -1rem; z-index: 1;">
                    <div>
                        <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Logistic Fee History Logs</h3>
                        <p class="mb-0" style="color: #f7f7f7; font-size: 0.95rem;">Monitor all audit trails, requests, and approvals for Logistic Fee changes transparently.</p>
                    </div>
                    <!-- <div class="flex-shrink-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="background: rgba(255,255,255,0.15); padding: 0.5rem 1.2rem; border-radius: 2rem; display: inline-flex; flex-wrap: nowrap;">
                                <li class="breadcrumb-item"><a href="#" class="text-white text-decoration-none"><i class="ph-fill ph-clock-counter-clockwise me-1"></i> Master Data</a></li>
                                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Fee History</li>
                            </ol>
                        </nav>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- 2. TABEL DATA LOG --}}
        <div class="row m-2">
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: none; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); overflow: hidden; z-index: 2; position: relative;">
                    <div class="card-header bg-white pt-4 pb-0 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 0;">
                        <h5 class="fw-bolder mb-0" style="color: #1e293b;"><i class="ph-fill ph-list-magnifying-glass me-2" style="color: #2563eb;"></i>Audit Trail Log</h5>
                        <button class="btn btn-sm btn-light border fw-bold rounded-pill px-3" style="color: #475569;" onclick="table.ajax.reload()"><i class="ph-bold ph-arrows-clockwise me-1"></i> Refresh</button>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table w-100" id="sampleTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 5%;">No</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Date</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Customer</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Distributor</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Old Price</th>
                                        <th style="background-color: #f8fafc; color: #7e22ce; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;"><i class="ph-bold ph-tag me-1"></i> New Price</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Status</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Actor</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 20%;">Note</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        let table;
        $(document).ready(function() {
            table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('logistic-fees.log') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'date', name: 'created_at' },
                    { data: 'customer', name: 'distributorCustomer.customer.name' },
                    { data: 'distributor', name: 'distributorCustomer.distributor.name' },
                    { data: 'old_fee', name: 'old_fee' },
                    { data: 'new_fee', name: 'new_fee' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'action_by', name: 'action_by' },
                    { data: 'notes', name: 'notes', orderable: false }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 Search log history...",
                    lengthMenu: "Show _MENU_ rows",
                    info: "Showing _START_ to _END_ of _TOTAL_ log entries"
                },
                drawCallback: function(settings) {
                    $('#sampleTable tbody td').css({
                        'padding': '1rem 1rem',
                        'vertical-align': 'middle',
                        'border-bottom': '1px solid #f1f5f9'
                    });
                }
            });

            $('.dataTables_filter input').css({
                'width': '250px',
                'margin-left': '10px',
                'border-radius': '50rem',
                'border': '1px solid #cbd5e1',
                'padding': '0.4rem 1rem',
                'background-color': '#ffffff'
            });
        });
    </script>
    @endpush
</x-app-layout>
