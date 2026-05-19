<x-app-layout>
    @section('title', 'Customer Approvals List')

    @include('components.sample-table-styles')

    {{-- Loading Overlay (Glassmorphism) --}}
    <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(5px); z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center;">
        <div class="spinner-border" style="width: 4rem; height: 4rem; color: #2563eb; border-width: 0.3rem;" role="status"></div>
        <h4 class="mt-4 fw-bolder" style="color: #1e3a8a; letter-spacing: 0.5px;">Processing Data...</h4>
        <p style="color: #64748b; font-weight: 500;">Please wait a moment, the system is synchronizing.</p>
    </div>

    <div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem;">

        {{-- 1. HEADER BANNER MEWAH (TEMA ROYAL BLUE) --}}
        <div class="row m-2 mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 1.25rem; padding: 2rem 2.5rem; color: white; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25); position: relative; overflow: hidden; margin-bottom: -1rem; z-index: 1;">
                    <div>
                        <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Customer Approvals</h3>
                        <p class="mb-0" style="color: #bfdbfe; font-size: 0.95rem;">Review customer approval requests, check credit limits, and manage account activations.</p>
                    </div>
                    <!-- <div class="flex-shrink-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="background: rgba(255,255,255,0.15); padding: 0.6rem 1.2rem; border-radius: 50rem; display: inline-flex; flex-wrap: nowrap; box-shadow: inset 0 2px 4px rgba(255,255,255,0.1);">
                                <li class="breadcrumb-item"><a href="/" class="text-white text-decoration-none"><i class="ph-fill ph-house me-1"></i> Home</a></li>
                                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Approvals List</li>
                            </ol>
                        </nav>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- 2. CONTROL BAR (FILTER & STATS) --}}
        <div class="row m-2 mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between" style="background: #ffffff; border-radius: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; padding: 1.25rem 1.5rem; z-index: 2; position: relative;">

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1 border">
                            <i class="ph-bold ph-funnel text-primary"></i>
                            <span class="text-muted fw-bold" style="font-size: 0.85rem;">FILTER</span>
                        </div>
                        <select id="statusFilter" class="form-select select2" style="width: 160px;">
                            <option value="all">All Accounts</option>
                            <option value="Active">Active (BG)</option>
                            <option value="Inactive">Inactive (BG)</option>
                        </select>
                        <select id="approvalStatusFilter" class="form-select select2" style="width: 180px;">
                            <option value="all">All Statuses</option>
                            @foreach($approvalStatuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <button id="resetFilters" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; color: #475569;" title="Reset Filters">
                            <i class="ph-bold ph-arrows-clockwise fs-5"></i>
                        </button>
                    </div>

                    <div class="d-flex gap-3 mt-3 mt-md-0">
                        <div class="d-flex align-items-center gap-2 px-3 py-2" style="background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 1rem; box-shadow: 0 2px 5px rgba(217, 119, 6, 0.1);">
                            <div style="width: 32px; height: 32px; background: #f59e0b; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="ph-bold ph-hourglass-high fs-6"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span style="font-size: 0.7rem; color: #b45309; font-weight: 700; text-transform: uppercase;">Pending</span>
                                <span style="font-size: 1.1rem; color: #92400e; font-weight: 800;">{{ $pendingCount }}</span>
                            </div>
                        </div>
                         <div class="d-flex align-items-center gap-2 px-3 py-2" style="background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 1rem; box-shadow: 0 2px 5px rgba(22, 163, 74, 0.1);">
                            <div style="width: 32px; height: 32px; background: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="ph-bold ph-seal-check fs-6"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span style="font-size: 0.7rem; color: #15803d; font-weight: 700; text-transform: uppercase;">Approved</span>
                                <span style="font-size: 1.1rem; color: #166534; font-weight: 800;">{{ $approvedCount }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. TABEL DATA UTAMA --}}
        <div class="row m-2">
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: none; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); overflow: hidden; z-index: 2; position: relative;">
                    <div class="card-header bg-white pt-4 pb-0 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 0;">
                        <h5 class="fw-bolder mb-0" style="color: #1e293b;"><i class="ph-fill ph-users-three me-2" style="color: #2563eb;"></i>Customer Approval Queue</h5>
                        <button class="btn btn-sm btn-light border fw-bold rounded-pill px-3" style="color: #475569;" onclick="table.ajax.reload()"><i class="ph-bold ph-arrows-clockwise me-1"></i> Refresh Data</button>
                    </div>

                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table w-100 display" id="sampleTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 5%;">No</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 12%;">Approver NIK</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Customer</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 8%;">Level</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Status</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Route To</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 15%;">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. MODAL VIEW DETAIL MEWAH --}}
        <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content" style="border: none; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">

                    {{-- Header Modal --}}
                    <div class="modal-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 1.5rem 2rem; border-bottom: none;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 55px; height: 55px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
                                <i class="ph-fill ph-storefront text-white" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <h4 class="modal-title mb-0 fw-bolder text-white" id="view_header_name" style="letter-spacing: -0.5px;">Customer Name</h4>
                                <div style="color: #bfdbfe; font-size: 0.85rem; font-weight: 600; margin-top: 2px;"><i class="ph-bold ph-hash me-1"></i><span id="view_header_code">CODE-001</span></div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body" style="background-color: #f8fafc; padding: 2rem; max-height: 80vh; overflow-y: auto;">

                        {{-- Status Banner --}}
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 2rem;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                                
                                <div class="d-flex flex-wrap align-items-center gap-4">
                                    <div class="d-flex flex-column">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Status Akun</label>
                                        <div><span id="view_status_badge" class="badge rounded-pill" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 16px; font-weight: 700; font-size: 0.8rem;">-</span></div>
                                    </div>
                                    <div style="width: 1px; height: 40px; background: #e2e8f0;" class="d-none d-md-block"></div>
                                    
                                    <div class="d-flex flex-column">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Progress Approval</label>
                                        <div id="view_approval_badge" class="fw-bolder" style="font-size: 1.1rem; color: #1e293b;">Pending</div>
                                    </div>
                                    <div style="width: 1px; height: 40px; background: #e2e8f0;" class="d-none d-md-block"></div>
                                    
                                    <div class="d-flex flex-column">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Sales Person</label>
                                        <div id="view_user_name" class="fw-bolder" style="font-size: 1.1rem; color: #1e293b;">-</div>
                                    </div>
                                </div>

                                <div class="text-end" style="background: #f8fafc; padding: 10px 16px; border-radius: 8px; border: 1px solid #e2e8f0; min-width: 180px;">
                                    <label style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block;">
                                        <i class="ph-bold ph-file-text" style="color: #3b82f6; margin-right: 4px;"></i>Doc Revision
                                    </label>
                                    <div style="font-size: 0.9rem; font-weight: 700; color: #1e293b; margin-bottom: 2px;">
                                        No: <span id="view_modal_rev_number" style="color: #3b82f6;">-</span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748b;">
                                        Rev: <span id="view_modal_rev_count" style="font-weight: 600; color: #1e293b;">0</span> 
                                        <span style="margin: 0 4px;">|</span> 
                                        Date: <span id="view_modal_rev_date">-</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- General Info --}}
                        <h5 class="fw-bolder mb-3" style="color: #1e3a8a;"><i class="ph-fill ph-info me-2 text-primary"></i> General Information</h5>
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 2rem;">
                            <div class="row g-4">
                                <div class="col-md-6" style="border-right: 1px dashed #e2e8f0;">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Customer Name</label>
                                            <div class="fw-bolder" style="color: #0f172a; font-size: 1.1rem;" id="view_name">-</div>
                                        </div>
                                        <div class="col-6">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Short Name</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_sort_name">-</div>
                                        </div>
                                        <div class="col-6">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">No. PKD</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_no_pkd">-</div>
                                        </div>
                                        <div class="col-12">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Person in Charge (PIC)</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_pic">-</div>
                                        </div>
                                        <div class="col-12">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Email address</label>
                                            <div class="fw-bold" style="color: #2563eb; font-size: 0.95rem;" id="view_email">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Main Address</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem; line-height: 1.5;" id="view_full_address">-</div>
                                        </div>
                                        <div class="col-4">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">City</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_city">-</div>
                                        </div>
                                        <div class="col-4">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Area</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_area">-</div>
                                        </div>
                                        <div class="col-4">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Postal code</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_postal_code">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Financial & Tax --}}
                        <h5 class="fw-bolder mb-3" style="color: #1e3a8a;"><i class="ph-fill ph-currency-circle-dollar me-2 text-primary"></i> Financial  & Tax</h5>
                        <div class="row g-3 mb-4">

                            {{-- Card 1: Credit Limit (Gradient Indigo) --}}
                            <div class="col-md-4">
                                <div style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); border-radius: 1.25rem; padding: 1.5rem; color: white; height: 100%; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2); position: relative; overflow: hidden;">
                                    <i class="ph-duotone ph-wallet" style="position: absolute; right: -15px; top: -15px; font-size: 8rem; color: rgba(255,255,255,0.1); transform: rotate(-15deg);"></i>
                                    <div style="position: relative; z-index: 2;">
                                        <label style="color: #c7d2fe; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Credit Limit</label>
                                        <div id="container_credit_limit" class="mt-2 mb-4">
                                            <h3 class="fw-bolder mb-0 text-white" id="view_credit_limit">IDR 0</h3>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center" style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 1rem; margin-bottom: 0.75rem;">
                                            <span style="color: #a5b4fc; font-size: 0.85rem; font-weight: 600;">Term of Payment</span>
                                            <div id="container_top">
                                                <span class="fw-bolder" style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 6px; font-size: 0.9rem;" id="view_term_of_payment">-</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #a5b4fc; font-size: 0.85rem; font-weight: 600;">Lead Time</span>
                                            <div id="container_lead_time">
                                                <span class="fw-bolder" style="font-size: 0.9rem;"><span id="view_lead_time">0</span> Days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 2: Tax Information --}}
                            <div class="col-md-4">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; height: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                                    <h6 class="fw-bolder mb-3 pb-3" style="color: #1e293b; border-bottom: 1px dashed #e2e8f0;">Tax Data</h6>
                                    <div class="mb-3">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">No NPWP</label>
                                        <div id="container_npwp" class="mt-1">
                                            <span class="fw-bolder" style="color: #0f172a; font-size: 1rem;" id="view_npwp">-</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">NPWP Date</span>
                                        <span class="fw-bold" style="color: #334155; font-size: 0.85rem;" id="view_tanggal_npwp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">NPPKP</span>
                                        <span class="fw-bold" style="color: #334155; font-size: 0.85rem;" id="view_nppkp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Output Tax</span>
                                        <span class="fw-bold" style="color: #334155; font-size: 0.85rem;" id="view_output_tax">-</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 3: Billing Contact --}}
                            <div class="col-md-4">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; height: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                                    <h6 class="fw-bolder mb-3 pb-3" style="color: #1e293b; border-bottom: 1px dashed #e2e8f0;">Billing Contact</h6>
                                    <div class="mb-3">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Contact Name</label>
                                        <div class="fw-bolder mt-1" style="color: #0f172a; font-size: 0.95rem;" id="view_penagihan_nama_kontak">-</div>
                                    </div>
                                    <div class="mb-3">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Phone number</label>
                                        <div class="fw-bolder mt-1" style="color: #0f172a; font-size: 0.95rem;" id="view_penagihan_telepon">-</div>
                                    </div>
                                    <div>
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Billing Address</label>
                                        <div class="fw-bold mt-1" style="color: #334155; font-size: 0.85rem; line-height: 1.4;" id="view_penagihan_address">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Container Khusus Schedule (Hanya Muncul Jika Finance) --}}
                        <div id="finance_schedule_container" style="display: none; margin-bottom: 2rem;"></div>

                        {{-- Management & Logistics --}}
                        <h5 class="fw-bolder mb-3 mt-4" style="color: #1e3a8a;"><i class="ph-fill ph-users-three me-2 text-primary"></i> Management & Logistics</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.02); height: 100%;">
                                    <table class="table mb-0 align-middle">
                                        <thead style="background: #f8fafc;">
                                            <tr>
                                                <th class="ps-4 py-3 fw-bold text-secondary text-uppercase f-s-12">Position Role</th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Full Name</th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Email Address</th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Phone Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Purchasing Mgr</td>
                                                <td class="fw-bold text-dark" id="view_purchasing_manager_name">-</td>
                                                <td class="text-dark" id="view_purchasing_manager_email">-</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Finance Mgr</td>
                                                <td class="fw-bold text-dark" id="view_finance_manager_name">-</td>
                                                <td class="text-dark" id="view_finance_manager_email">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fcd34d; border-radius: 1.25rem; padding: 1.5rem; height: 100%; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.05);">
                                    <h6 class="fw-bolder mb-3 pb-3" style="color: #92400e; border-bottom: 1px dashed #fcd34d;">
                                        <i class="ph-fill ph-truck me-2 text-warning"></i>Tujuan Pengiriman
                                    </h6>
                                    <div class="mb-3">
                                        <label style="color: #b45309; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Recipient's name</label>
                                        <div class="fw-bolder mt-1" style="color: #78350f; font-size: 1.05rem;" id="view_shipping_to_name">-</div>
                                    </div>
                                    <div>
                                        <label style="color: #b45309; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Shipping address</label>
                                        <div class="fw-bold mt-1" style="color: #92400e; font-size: 0.85rem; line-height: 1.5;" id="view_shipping_to_address">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Documents --}}
                        <h5 class="fw-bolder mb-3 mt-4" style="color: #1e3a8a;"><i class="ph-fill ph-folder-open me-2 text-primary"></i> Document Attachments</h5>
                        <div class="row g-3" id="document_grid"></div>

                        <div id="no_documents" class="text-center py-5" style="display:none; background: #ffffff; border: 2px dashed #cbd5e1; border-radius: 1.25rem;">
                            <i class="ph-duotone ph-files f-s-48 mb-2" style="color: #94a3b8;"></i>
                            <p class="mb-0 fw-bold" style="color: #64748b;">No documents attached.</p>
                        </div>

                        {{-- Action Form Container (Injected by JS) --}}
                        <div id="viewModalActionFormContainer" class="mt-4"></div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer d-flex justify-content-between align-items-center" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem;" id="viewModalFooter">
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-bold border shadow-sm" style="color: #475569;" data-bs-dismiss="modal">Close Details</button>
                        {{-- JS will append Submit button here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Preview File --}}
        <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 1.5rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                    <div class="modal-header border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="modal-title fw-bolder mb-0 text-white" id="filePreviewModalLabel" style="letter-spacing: 0.5px;">FILE PREVIEW</h6>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height: 500px;">
                        <img id="previewImageContent" src="" class="img-fluid" style="max-height: 70vh; max-width: 100%; display: none; border-radius: 0.5rem;" alt="File Preview">
                        <iframe id="previewPdfContent" src="" style="width: 100%; height: 75vh; border: none; display: none; background: #fff;"></iframe>

                        <div id="previewErrorMessage" class="text-white p-5 text-center" style="display: none;">
                            <div style="width: 80px; height: 80px; background: rgba(239,68,68,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                <i class="ph-bold ph-file-x" style="font-size: 2.5rem; color: #ef4444;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Unsupported Format</h5>
                            <p style="color: #94a3b8; margin-bottom: 1.5rem;">This file cannot be displayed directly in the browser.</p>
                            <a href="#" id="downloadFallbackLink" target="_blank" class="btn rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none;">Download File</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL KHUSUS: VERIFY & EDIT NPWP --}}
        <div class="modal fade" id="modalVerifyNpwpSystem" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content" style="border: none; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);">
                    <div class="modal-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-bottom: none; padding: 1.25rem 2rem;">
                        <h5 class="modal-title fw-bolder text-white mb-0"><i class="ph-bold ph-scan me-2 text-primary"></i> NPWP Data Verification</h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <div class="col-lg-8 d-flex align-items-center justify-content-center" style="background-color: #e2e8f0; min-height: 500px; max-height: 600px; position: relative;">
                                <div id="npwp_preview_container" class="w-100 h-100 d-flex align-items-center justify-content-center p-3"></div>
                            </div>

                            <div class="col-lg-4 p-4 bg-white d-flex flex-column justify-content-center" style="box-shadow: -5px 0 15px rgba(0,0,0,0.05); z-index: 2;">
                                <div style="width: 48px; height: 48px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                    <i class="ph-fill ph-identification-card fs-3"></i>
                                </div>
                                <h5 class="fw-bolder text-dark mb-2">Input NPWP System</h5>
                                <p style="color: #64748b; font-size: 0.85rem; line-height: 1.5; margin-bottom: 1.5rem;">
                                    Match the NPWP number on the document on the left with the input field below. Make sure it's spelled correctly.
                                </p>

                                <div class="mb-4">
                                    <label class="fw-bold" style="color: #475569; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px;">No NPWP</label>
                                    <input type="text" id="input_npwp_verification" class="form-control form-control-lg fw-bolder" style="border: 2px solid #cbd5e1; border-radius: 0.75rem; color: #0f172a;" placeholder="Type in your NPWP number...">
                                </div>

                                <div class="d-flex flex-column gap-2 mt-auto">
                                    <button type="button" class="btn rounded-pill py-3 fw-bold w-100 shadow-sm" id="btn_save_npwp_verification" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                                        <i class="ph-bold ph-check-circle me-2"></i> Save Verification Results
                                    </button>
                                    <button type="button" class="btn btn-light rounded-pill py-3 fw-bold w-100 border shadow-sm" style="color: #475569;" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $('.select2').select2({ theme: 'bootstrap-5', minimumResultsForSearch: 10 });

            // --- GLOBAL FUNCTION: Toggle Schedule ---
            window.toggleSchedule = function(btn, type) {
                const button = $(btn);
                const container = $('#' + type + '_container_modal');
                const inputContainer = $('#' + type + '_inputs_modal');
                const value = button.data('val');
                const isAll = value === 'All';

                const colorClass = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                const dateColor = 'btn-info';
                const isDateBtn = button.hasClass('btn-date-schedule');

                if (isAll) {
                    const isActive = button.hasClass('active');
                    if (!isActive) {
                        button.addClass('active btn-dark').removeClass('btn-outline-dark');
                        container.find('button:not([data-val="All"])').each(function() {
                            const childIsDate = $(this).hasClass('btn-date-schedule');
                            $(this).addClass('active text-white').removeClass('btn-outline-secondary btn-outline-primary btn-outline-success');
                            if (childIsDate) $(this).addClass(dateColor); else $(this).addClass(colorClass);
                        });
                    } else {
                        button.removeClass('active btn-dark').addClass('btn-outline-dark');
                        container.find('button').each(function() {
                            const childIsDate = $(this).hasClass('btn-date-schedule');
                            $(this).removeClass('active text-white ' + colorClass + ' ' + dateColor);
                            if (childIsDate) $(this).addClass('btn-outline-secondary');
                            else if ($(this).data('val') !== 'All') {
                                $(this).addClass(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                            }
                        });
                    }
                } else {
                    const allBtn = container.find('button[data-val="All"]');
                    allBtn.removeClass('active btn-dark').addClass('btn-outline-dark');
                    button.toggleClass('active');
                    if (button.hasClass('active')) {
                        button.addClass('text-white');
                        button.removeClass(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                        if (isDateBtn) button.addClass(dateColor).removeClass('btn-outline-secondary');
                        else button.addClass(colorClass);
                    } else {
                        button.removeClass('text-white ' + colorClass + ' ' + dateColor);
                        if (isDateBtn) button.addClass('btn-outline-secondary');
                        else button.addClass(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                    }
                }

                inputContainer.empty();
                if (container.find('button[data-val="All"]').hasClass('active')) {
                    inputContainer.append(`<input type="hidden" name="update_${type}[]" value="All" form="modalResponseForm">`);
                } else {
                    container.find('button.active:not([data-val="All"])').each(function() {
                        inputContainer.append(`<input type="hidden" name="update_${type}[]" value="${$(this).data('val')}" form="modalResponseForm">`);
                    });
                }
            };

            $(document).ready(function() {
                 const table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customers.approval.data') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                            d.approval_status = $('#approvalStatusFilter').val();
                        }
                    },
                    order: [[7, 'desc']],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'approver_nik', name: 'approver_nik' },
                        { data: 'customer_name', name: 'customers.name' },
                        { data: 'level', name: 'approval_logs.level', className: 'text-center' },
                        { data: 'status_approval', name: 'customers.status_approval', className: 'text-center' },
                        { data: 'route_to', name: 'customers.route_to', className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'updated_at', name: 'approval_logs.updated_at', visible: false, searchable: false }
                    ],
                    autoWidth: false,
                    language: {
                        search: "",
                        searchPlaceholder: "🔍 Search data...",
                        lengthMenu: "Tampilkan _MENU_ baris",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data"
                    },
                    drawCallback: function(settings) {
                        $('#sampleTable tbody td').css({
                            'padding': '1.25rem 1rem',
                            'vertical-align': 'middle',
                            'border-bottom': '1px solid #f1f5f9'
                        });
                    }
                });

                // Style Search Box DataTables
                $('.dataTables_filter input').css({
                    'width': '250px',
                    'margin-left': '10px',
                    'border-radius': '50rem',
                    'border': '1px solid #cbd5e1',
                    'padding': '0.4rem 1rem',
                    'background-color': '#ffffff',
                    'box-shadow': 'inset 0 1px 2px rgba(0,0,0,0.02)'
                });

                $('#statusFilter, #approvalStatusFilter').on('change', function() { table.ajax.reload(); });
                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all').trigger('change');
                    $('#approvalStatusFilter').val('all').trigger('change');
                });

                // Resend approval email handler
                $(document).on('click', '.btn-resend-email', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    const token = button.data('token');
                    const approverName = button.data('approver-name') || 'Approver';

                    Swal.fire({
                        title: 'Resend Notification?',
                        html: `<p style="font-size:0.9rem; color:#64748b;">The approval notification email will be resent to <b>${approverName}</b>.</p>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ph-bold ph-paper-plane-right me-1"></i> Ya, Kirim',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm text-white',
                            cancelButton: 'btn btn-light rounded-pill px-4 fw-bold shadow-sm border'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        const originalHtml = button.html();
                        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                        const url = "{{ route('approvals.resend', ':token') }}".replace(':token', token);
                        const csrf = $('meta[name="csrf-token"]').attr('content');

                        $.ajax({
                            url: url,
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf },
                            data: {},
                            success: function(res) {
                                Swal.fire({
                                    title: 'Terkirim', html: res.message || 'Approval email resent.', icon: 'success',
                                    customClass: { confirmButton: 'btn btn-success rounded-pill px-4 fw-bold shadow-sm text-white' }, buttonsStyling: false
                                });
                                if (typeof table !== 'undefined' && table.ajax) table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message || 'Failed to resend approval email.';
                                Swal.fire('Error', msg, 'error');
                            },
                            complete: function() {
                                button.prop('disabled', false).html(originalHtml);
                            }
                        });
                    });
                });

                window.populateViewForm = function(data) {
                    $('#view_header_name').text(data.name || 'Unknown Customer');
                    $('#view_header_code').text(data.code || 'DRAFT (Waiting for Code)');

                    $('#view_status_badge').text(data.status || '-');

                    const status = data.status_approval || 'Pending';
                    let badgeClass = 'text-warning';
                    if(status === 'Approved' || status === 'Completed') badgeClass = 'text-success';
                    if(status === 'Rejected') badgeClass = 'text-danger';
                    if(status === 'Processing') badgeClass = 'text-primary';
                    $('#view_approval_badge').removeClass().addClass('fw-bolder ' + badgeClass).css('font-size', '1.1rem').text(status.toUpperCase());

                    let salesName = '-';
                    if (data.sales && data.sales.user) salesName = data.sales.user.name;
                    else if (data.user) salesName = data.user.name;
                    $('#view_user_name').text(salesName);

                    $('#view_name').text(data.name);
                    $('#view_sort_name').text(data.sort_name || '-');
                    $('#view_no_pkd').text(data.no_pkd || '-');
                    $('#view_pic').text(data.pic || '-');
                    $('#view_email').text(data.email || '-');
                    const fullAddr = [data.address1, data.address2, data.address3].filter(Boolean).join(', ');
                    $('#view_full_address').text(fullAddr || '-');
                    $('#view_city').text(data.city || '-');
                    $('#view_area').text(data.area || '-');
                    $('#view_postal_code').text(data.postal_code || '-');

                    // --- FINANCE SECTION ---
                    const limit = parseFloat(data.credit_limit) || 0;
                    const formattedLimit = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(limit);

                    $('#container_top, #container_lead_time, #container_credit_limit, #container_npwp, #finance_schedule_container').empty();
                    $('#finance_schedule_container').hide();
                    $('#calc_badge').hide();

                    if (data.can_adjust_finance) {
                        $('#container_credit_limit').html(`
                            <h3 class="mb-0 fw-bolder mt-1 text-white" id="view_credit_limit">${formattedLimit}</h3>
                            <input type="hidden" name="update_credit_limit_value" id="hidden_credit_limit" value="${limit}" form="modalResponseForm">
                            <span id="calc_badge" style="display:none; background: rgba(255,255,255,0.2); border-radius: 4px; padding: 2px 6px; font-size: 0.7rem; margin-top: 8px; display: inline-block;">
                                <i class="ph-fill ph-calculator me-1"></i> Auto-Calculated
                            </span>
                        `);

                        let currentTop = data.term_of_payment || '30';

                        let topOptions = `
                            <option value="7">Net 7 Days</option>
                            <option value="14">Net 14 Days</option>
                            <option value="30">Net 30 Days</option>
                            <option value="45">Net 45 Days</option>
                            <option value="CBD">Cash Before Delivery</option>
                        `;

                        $('#container_top').html(`
                            <select class="form-select form-select-sm fw-bold"
                                    name="update_top"
                                    id="input_top"
                                    data-original="${currentTop}"
                                    form="modalResponseForm"
                                    style="border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white;">
                                ${topOptions}
                            </select>
                        `);
                        $('#input_top').val(currentTop);

                        let leadTimeValue = (data.lead_time && data.lead_time != 0) ? data.lead_time : '';
                        $('#container_lead_time').html(`
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <input type="number" class="form-control fw-bold"
                                    name="update_lead_time" id="input_lead_time"
                                    value="${leadTimeValue}"
                                    placeholder="0"
                                    form="modalResponseForm"
                                    style="border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white;">
                                <span class="input-group-text" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">Hari</span>
                            </div>
                        `);

                        let npwpUrl = null;
                        if (data.files && data.files.length > 0) {
                            const file = data.files.find(f => f.npwp_file);
                            if (file && file.npwp_file) npwpUrl = "{{ asset('storage') }}/" + file.npwp_file;
                        }
                        if (!npwpUrl && data.file_npwp_path) npwpUrl = data.file_npwp_path;

                        $('#container_npwp').html(`
                            <div class="input-group mt-1">
                                <input type="text" class="form-control fw-bolder"
                                    id="display_npwp_main" value="${data.npwp || ''}" readonly style="border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a;">
                                <button type="button" class="btn fw-bold shadow-sm" style="background: #2563eb; color: white;"
                                        onclick="openNpwpVerificationModal('${npwpUrl}', '${data.npwp || ''}')">
                                    <i class="ph-bold ph-pencil-simple me-1"></i> Verify
                                </button>
                                <input type="hidden" name="update_npwp" id="real_update_npwp"
                                    value="${data.npwp || ''}" form="modalResponseForm">
                            </div>
                        `);

                        const genBtn = (type, val, label, isDate = false) => {
                            const activeArr = data[type] || [];
                            let activeClass = '';

                            const color = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                            const dateColor = 'btn-info';

                            let isActive = false;
                            if (activeArr.includes('All') || activeArr.includes(String(val))) isActive = true;

                            if (isActive) {
                                if(val === 'All') activeClass = 'active btn-dark';
                                else activeClass = `active text-white ${isDate ? dateColor : color}`;
                            } else {
                                if(val === 'All') activeClass = 'btn-outline-dark';
                                else activeClass = isDate ? 'btn-outline-secondary' : (type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                            }

                            let style = 'font-size: 0.75rem !important; font-weight: 600; border-radius: 6px;';
                            if(isDate) style += 'width: 32px !important; height: 32px !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; line-height: 1 !important;';
                            else style += 'padding: 4px 12px !important;';

                            const identifierClass = isDate ? 'btn-date-schedule' : 'btn-day-schedule';
                            return `<button type="button" class="btn btn-sm ${activeClass} mb-1 me-1 ${identifierClass}" style="${style}" data-val="${val}" onclick="toggleSchedule(this, '${type}')">${label}</button>`;
                        };

                        let payDays = genBtn('payment_days', 'All', 'All Days');
                        ['Monday','Tuesday','Wednesday','Thursday','Friday'].forEach(d => payDays += genBtn('payment_days', d, d));

                        let payDates = genBtn('payment_date', 'All', 'All Dates');
                        payDates += '<div class="d-flex flex-wrap gap-1 mt-2">';
                        for(let i=1; i<=31; i++) payDates += genBtn('payment_date', i, i, true);
                        payDates += '</div>';

                        let fakDays = genBtn('faktur_days', 'All', 'All Days');
                        ['Monday','Tuesday','Wednesday','Thursday','Friday'].forEach(d => fakDays += genBtn('faktur_days', d, d));

                        let fakDates = genBtn('faktur_date', 'All', 'All Dates');
                        fakDates += '<div class="d-flex flex-wrap gap-1 mt-2">';
                        for(let i=1; i<=31; i++) fakDates += genBtn('faktur_date', i, i, true);
                        fakDates += '</div>';

                        $('#finance_schedule_container').html(`
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-top: 1rem;">
                                <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                                    <h6 class="fw-bolder mb-0" style="color: #0f172a;"><i class="ph-bold ph-calendar-check me-2 text-primary"></i>Payment & Faktur Schedule (Finance)</h6>
                                </div>
                                <div style="padding: 1.5rem;">
                                    <div class="mb-4">
                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Nomor Virtual Account</label>
                                        <input type="text" class="form-control fw-bold" name="update_va" value="${data.virtual_account || ''}" placeholder="Type VA Number..." form="modalResponseForm" style="border: 2px solid #e2e8f0; border-radius: 0.75rem;">
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6" style="border-right: 1px dashed #cbd5e1;">
                                            <h6 class="fw-bolder mb-3" style="color: #2563eb;">Payment Schedule</h6>
                                            <div class="mb-3">
                                                <label style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; margin-bottom: 6px; display: block;">By Day</label>
                                                <div id="payment_days_container_modal">${payDays}</div>
                                                <div id="payment_days_inputs_modal"></div>
                                            </div>
                                            <div>
                                                <label style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; margin-bottom: 6px; display: block;">By Date</label>
                                                <div id="payment_date_container_modal">${payDates}</div>
                                                <div id="payment_date_inputs_modal"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ps-md-4">
                                            <h6 class="fw-bolder mb-3" style="color: #059669;">Faktur Schedule (Faktur)</h6>
                                            <div class="mb-3">
                                                <label style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; margin-bottom: 6px; display: block;">By Day</label>
                                                <div id="faktur_days_container_modal">${fakDays}</div>
                                                <div id="faktur_days_inputs_modal"></div>
                                            </div>
                                            <div>
                                                <label style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; margin-bottom: 6px; display: block;">By Date</label>
                                                <div id="faktur_date_container_modal">${fakDates}</div>
                                                <div id="faktur_date_inputs_modal"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        $('#finance_schedule_container').show();

                        ['payment_days', 'payment_date', 'faktur_days', 'faktur_date'].forEach(type => {
                            const arr = data[type] || [];
                            const inputDiv = $('#' + type + '_inputs_modal');
                            inputDiv.empty();
                            if(arr.includes('All')) {
                                inputDiv.append(`<input type="hidden" name="update_${type}[]" value="All" form="modalResponseForm">`);
                            } else {
                                arr.forEach(val => { inputDiv.append(`<input type="hidden" name="update_${type}[]" value="${val}" form="modalResponseForm">`); });
                            }
                        });

                        const baseAmount = parseFloat(data.base_total_amount) || 0;
                        function calculateFinanceLimit() {
                            const topStr = $('#input_top').val();
                            const lt = parseFloat($('#input_lead_time').val()) || 0;
                            let topDays = 0, divider = 30;

                            if (topStr === 'CBD') { topDays = 0; divider = 30; }
                            else { topDays = parseInt(topStr) || 0; divider = topDays > 0 ? topDays : 30; }

                            if (topStr === '7') divider = 7.5;
                            if (topStr === '14') divider = 15;

                            let result = ((topDays + lt) * baseAmount) / divider;
                            if (topStr === 'CBD') result = 0;

                            const rounded = Math.round(result);
                            $('#view_credit_limit').text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(rounded));
                            $('#hidden_credit_limit').val(rounded);
                            $('#calc_badge').show();
                        }
                        $('#input_top, #input_lead_time').on('change input', calculateFinanceLimit);

                    } else {
                        $('#container_credit_limit').html(`<h3 class="mb-0 fw-bolder mt-1 text-white" id="view_credit_limit">${formattedLimit}</h3>`);
                        $('#container_top').html(`<span class="fw-bolder" style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 6px; font-size: 0.9rem;">${data.term_of_payment || '-'}</span>`);
                        $('#container_lead_time').html(`<span class="fw-bolder" style="font-size: 0.9rem;">${data.lead_time || 0} Days</span>`);
                        $('#container_npwp').html(`<span class="fw-bolder mt-1" style="color: #0f172a; font-size: 1rem;" id="view_npwp">${data.npwp || '-'}</span>`);
                        $('#finance_schedule_container').hide();
                    }

                    $('#view_tanggal_npwp').text(data.tanggal_npwp || '-');
                    $('#view_nppkp').text(data.nppkp || '-');
                    $('#view_output_tax').text(data.output_tax || '-');
                    $('#view_penagihan_nama_kontak').text(data.penagihan_nama_kontak || '-');
                    $('#view_penagihan_telepon').text(data.penagihan_telepon || '-');
                    $('#view_penagihan_address').text(data.penagihan_address || '-');
                    $('#view_purchasing_manager_name').text(data.purchasing_manager_name || '-');
                    $('#view_purchasing_manager_email').text(data.purchasing_manager_email || '-');
                    $('#view_purchasing_manager_phone').text(data.purchasing_manager_telepon || data.purchasing_manager_phone || '-');
                    $('#view_finance_manager_name').text(data.finance_manager_name || '-');
                    $('#view_finance_manager_email').text(data.finance_manager_email || '-');
                    $('#view_finance_manager_phone').text(data.finance_manager_telepon || data.finance_manager_phone || '-');
                    $('#view_shipping_to_name').text(data.shipping_to_name || '-');
                    $('#view_shipping_to_address').text(data.shipping_to_address || '-');

                    const gridContainer = $('#document_grid');
                    gridContainer.empty();
                    $('#no_documents').hide();
                    let fileCount = 0;
                    const storageBase = "{{ asset('storage') }}";

                    function appendFileCard(label, filename) {
                        if(!filename) return;
                        fileCount++;
                        const cleanFileName = filename.startsWith('/') ? filename.substring(1) : filename;
                        const fullUrl = `${storageBase}/${cleanFileName}`;
                        const ext = cleanFileName.split('.').pop().toLowerCase();
                        let icon = 'ph-file-text';
                        let iconColor = '#3b82f6';

                        if(['jpg','jpeg','png'].includes(ext)) { icon = 'ph-image'; iconColor = '#10b981'; }
                        if(ext === 'pdf') { icon = 'ph-file-pdf'; iconColor = '#ef4444'; }

                        const html = `
                            <div class="col-md-3">
                                <div class="card h-100 btn-preview-file"
                                     data-url="${fullUrl}" data-filename="${cleanFileName}" data-title="${label}" data-customer-name="${data.name}"
                                     style="border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.02); cursor: pointer; transition: all 0.2s;"
                                     onmouseover="this.style.borderColor='#3b82f6'; this.style.transform='translateY(-2px)';"
                                     onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)';">
                                    <div class="card-body p-3 text-center">
                                        <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                            <i class="ph-fill ${icon}" style="font-size: 1.5rem; color: ${iconColor};"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="color: #1e293b; font-size: 0.85rem;">${label}</h6>
                                        <span style="color: #94a3b8; font-size: 0.75rem;">Click to view</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        gridContainer.append(html);
                    }
                    if(data.files && data.files.length > 0) {
                        const f = data.files[0];
                        appendFileCard('NPWP Document', f.npwp_file);
                        appendFileCard('NIB/SIUP Document', f.nib_siup_file);
                        appendFileCard('KTP Document', f.ktp_file);
                        appendFileCard('Akte Pendirian', f.akte_file);
                        appendFileCard('Company Profile', f.company_profile_file);
                    } else {
                        appendFileCard('NPWP Document', data.file_npwp);
                        appendFileCard('NIB/SIUP Document', data.file_nib);
                        appendFileCard('KTP Document', data.file_ktp);
                        appendFileCard('Akte Pendirian', data.file_akte);
                        appendFileCard('Company Profile', data.file_company_profile);
                    }
                    if(fileCount === 0) $('#no_documents').show();
                };

                window.openNpwpVerificationModal = function(fileUrl, currentNpwp) {
                    $('#input_npwp_verification').val(currentNpwp);
                    const container = $('#npwp_preview_container');
                    container.empty();

                    if (fileUrl) {
                        const ext = fileUrl.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png', 'webp', 'bmp'].includes(ext)) {
                            container.html(`<img src="${fileUrl}" class="img-fluid shadow-lg" style="max-height: 500px; max-width: 100%; border-radius: 0.75rem;">`);
                        } else if (ext === 'pdf') {
                            container.html(`<iframe src="${fileUrl}" width="100%" height="100%" style="min-height: 500px; border:none; border-radius: 0.75rem;" class="shadow-lg"></iframe>`);
                        } else {
                            container.html(`
                                <div class="text-center text-white">
                                    <i class="ph-duotone ph-file-x" style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;"></i>
                                    <p class="mb-3">Preview tidak tersedia untuk format file ini.</p>
                                    <a href="${fileUrl}" target="_blank" class="btn rounded-pill px-4 fw-bold shadow-sm" style="background: #3b82f6; color: white; border: none;">Download File</a>
                                </div>
                            `);
                        }
                    } else {
                        container.html(`
                            <div class="text-center" style="color: rgba(255,255,255,0.5);">
                                <i class="ph-duotone ph-file-dashed" style="font-size: 5rem; margin-bottom: 1rem;"></i>
                                <h5>There is no NPWP file yet</h5>
                            </div>
                        `);
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalVerifyNpwpSystem'));
                    modal.show();
                };

                $(document).on('click', '#btn_save_npwp_verification', function() {
                    const newVal = $('#input_npwp_verification').val();
                    $('#display_npwp_main').val(newVal);
                    $('#real_update_npwp').val(newVal);

                    const modalEl = document.getElementById('modalVerifyNpwpSystem');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    $('#display_npwp_main').css({'background': '#fef9c3', 'border-color': '#eab308'}).focus();
                    setTimeout(() => $('#display_npwp_main').css({'background': '#f8fafc', 'border-color': '#cbd5e1'}), 1500);
                });

                $(document).on('click', '.btn-preview-file', function() {
                    const url = $(this).data('url');
                    const filename = $(this).data('filename');
                    const title = $(this).data('title');
                    const customerName = $(this).data('customer-name');
                    $('#previewImageContent').hide();
                    $('#previewPdfContent').hide();
                    $('#previewErrorMessage').hide();
                    const headerTitle = `<i class="ph-bold ph-image me-2 text-primary"></i> <span style="letter-spacing: 0.5px;">${title}</span> <span style="color: rgba(255,255,255,0.2); margin: 0 10px;">|</span> <span class="fw-light" style="color: #94a3b8;">${customerName}</span>`;
                    $('#filePreviewModalLabel').html(headerTitle);
                    if (!url) return;
                    const extension = filename.split('.').pop().toLowerCase();
                    if (['jpg', 'jpeg', 'png', 'bmp', 'webp'].includes(extension)) {
                        $('#previewImageContent').attr('src', url).show();
                    } else if (extension === 'pdf') {
                        $('#previewPdfContent').attr('src', url).show();
                    } else {
                        $('#downloadFallbackLink').attr('href', url);
                        $('#previewErrorMessage').show();
                    }
                    const fileModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                    fileModal.show();
                });

                $(document).on('click', '.action-btn-modal', function() {
                    const button = $(this);
                    const customerId = button.data('id');
                    const token = button.data('token');
                    const customerName = button.data('name');
                    const btnTitle = button.attr('title') || '';
                    const isITInput = btnTitle.includes('Input Code');

                    const originalIcon = button.html();
                    button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

                    $.ajax({
                        url: `/customers/${customerId}`,
                        type: 'GET',
                        success: function(response) {
                            populateViewForm(response);

                            let actionFormHtml = '';
                            let submitBtnHtml = '';

                            if (isITInput) {
                                let today = new Date().toISOString().split('T')[0];
                                let joinVal = response.join_date ? response.join_date.split(' ')[0] : today;
                                let codeVal = response.code || '';

                                actionFormHtml = `
                                    <div style="background: #ffffff; border: 1px solid #bae6fd; border-radius: 1.25rem; overflow: hidden; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.05); margin-top: 1rem;">
                                        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 1rem 1.5rem; border-bottom: 1px solid #bae6fd;">
                                            <h6 class="mb-0 fw-bolder" style="color: #0369a1;"><i class="ph-bold ph-pencil-simple me-2"></i>IT ACTIVATION: SET CUSTOMER CODE</h6>
                                        </div>
                                        <div style="padding: 1.5rem;">
                                            <form id="modalResponseForm" action="{{ route('customers.approval_action', ':id') }}".replace(':id', customerId) method="POST">
                                                @csrf
                                                <input type="hidden" name="token" value="${token}">
                                                <input type="hidden" name="action" id="final_action" value="review">
                                                <div class="row g-4 mb-3">
                                                    <div class="col-md-6">
                                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Customer Code <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control form-control-lg fw-bold" id="it_update_code" name="update_code" value="${codeVal}" placeholder="e.g. CUST-001" style="border: 2px solid #93c5fd; border-radius: 0.75rem; color: #0f172a;" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Join Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control form-control-lg fw-bold" name="update_join_date" value="${joinVal}" style="border: 2px solid #e2e8f0; border-radius: 0.75rem; color: #334155;" required>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Notes (Optional)</label>
                                                    <textarea class="form-control" id="modal_notes" name="notes" rows="2" placeholder="Enter notes here..." style="border: 1px solid #cbd5e1; border-radius: 0.75rem; background: #f8fafc; resize: none;"></textarea>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                `;

                                submitBtnHtml = `
                                    <button type="submit" form="modalResponseForm" id="final_submit_btn" class="btn rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                                        <i class="ph-bold ph-check-circle me-2"></i> Save & Activate
                                    </button>
                                `;

                            } else {
                                actionFormHtml = `
                                    <div style="background: #ffffff; border: 1px solid #c7d2fe; border-radius: 1.25rem; overflow: hidden; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.05); margin-top: 1rem;">
                                        <div style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); padding: 1rem 1.5rem; border-bottom: 1px solid #c7d2fe;">
                                            <h6 class="mb-0 fw-bolder" style="color: #3730a3;"><i class="ph-bold ph-gavel me-2"></i>DECISION: APPROVAL REVIEW</h6>
                                        </div>
                                        <div style="padding: 1.5rem;">
                                            <form id="modalResponseForm" action="{{ route('customers.approval_action', ':id') }}".replace(':id', customerId) method="POST">
                                                @csrf
                                                <input type="hidden" name="token" value="${token}">
                                                <input type="hidden" name="action" id="final_action" value="">

                                                <label style="color: #475569; font-size: 0.85rem; font-weight: 700; margin-bottom: 12px; display: block;">Choose Your Decision <span class="text-danger">*</span></label>
                                                <div class="d-flex flex-column flex-md-row gap-3 mb-4">
                                                    <div class="decision-btn flex-fill" data-select-action="approve" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center;">
                                                        <div style="width: 40px; height: 40px; background: #10b981; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(16,185,129,0.2);">
                                                            <i class="ph-bold ph-check-circle fs-4"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bolder" style="color: #1e293b; font-size: 0.95rem;">Approve<br><span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">No Notes</span></h6>
                                                    </div>

                                                    <div class="decision-btn flex-fill" data-select-action="review" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center;">
                                                        <div style="width: 40px; height: 40px; background: #3b82f6; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(59,130,246,0.2);">
                                                            <i class="ph-bold ph-note-pencil fs-4"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bolder" style="color: #1e293b; font-size: 0.95rem;">Approve<br><span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">With Notes</span></h6>
                                                    </div>

                                                    <div class="decision-btn flex-fill" data-select-action="reject" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center;">
                                                        <div style="width: 40px; height: 40px; background: #ef4444; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(239,68,68,0.2);">
                                                            <i class="ph-bold ph-x-circle fs-4"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bolder" style="color: #1e293b; font-size: 0.95rem;">Reject<br><span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">With Notes</span></h6>
                                                    </div>
                                                </div>
                                                <div id="notes_container" style="display: none;">
                                                    <label for="modal_notes" style="color: #475569; font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; display: block;">Notes / Reasons <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="modal_notes" name="notes" rows="3" placeholder="Type your reason or note here..." style="border: 2px solid #cbd5e1; border-radius: 0.75rem; background: #f8fafc; resize: none;"></textarea>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                `;

                                submitBtnHtml = `
                                    <button type="submit" form="modalResponseForm" id="final_submit_btn" class="btn rounded-pill px-5 py-2 fw-bold shadow-sm" style="display: none; border: none; color: white;">
                                        Submit Decision
                                    </button>
                                `;
                            }

                            const submitUrl = "{{ route('customers.approval_action', ':id') }}".replace(':id', customerId);
                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalActionFormContainer form').attr('action', submitUrl);

                            if (response.latest_revision) {
                                $('#view_modal_rev_number').text(response.latest_revision.revision_number);
                                $('#view_modal_rev_count').text(response.latest_revision.revision_count);
                                $('#view_modal_rev_date').text(response.latest_revision.revision_date);
                            } else {
                                $('#view_modal_rev_number').text('-');
                                $('#view_modal_rev_count').text('0');
                                $('#view_modal_rev_date').text('-');
                            }

                            $('#viewModalFooter button[type="submit"]').remove();
                            $('#viewModalFooter').prepend(submitBtnHtml);
                            $('#viewModal').modal('show');
                        },
                        error: function() { Swal.fire('Error', 'Failed to fetch data.', 'error'); },
                        complete: function() { button.html(originalIcon).prop('disabled', false); }
                    });
                });

                $(document).on('click', '.decision-btn', function() {
                    // Reset styling
                    $('.decision-btn').css({'border': '1px solid #e2e8f0', 'background': '#ffffff', 'opacity': '0.6', 'box-shadow': 'none'});
                    $('.decision-btn').find('h6').css('color', '#1e293b');

                    // Apply Active Styling
                    $(this).css({'border': '2px solid transparent', 'opacity': '1', 'box-shadow': '0 4px 15px rgba(0,0,0,0.05)'});

                    const selectedAction = $(this).data('select-action');
                    $('#final_action').val(selectedAction);

                    if (selectedAction === 'approve') {
                        $(this).css({'border-color': '#10b981', 'background': '#f0fdf4'});
                        $(this).find('h6').css('color', '#047857');
                    } else if (selectedAction === 'review') {
                        $(this).css({'border-color': '#3b82f6', 'background': '#eff6ff'});
                        $(this).find('h6').css('color', '#1d4ed8');
                    } else if (selectedAction === 'reject') {
                        $(this).css({'border-color': '#ef4444', 'background': '#fef2f2'});
                        $(this).find('h6').css('color', '#b91c1c');
                    }

                    const notesContainer = $('#notes_container');
                    const notesInput = $('#modal_notes');
                    const submitBtn = $('#final_submit_btn');

                    if(selectedAction === 'approve') {
                        notesContainer.slideUp();
                        notesInput.removeAttr('required').val('');
                        submitBtn.css('background', 'linear-gradient(135deg, #10b981 0%, #059669 100%)')
                                .html('<i class="ph-bold ph-check-circle me-2"></i> Submit Approve').fadeIn();
                    } else if (selectedAction === 'review') {
                        notesContainer.slideDown();
                        notesInput.attr('required', 'required');
                        submitBtn.css('background', 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)')
                                .html('<i class="ph-bold ph-paper-plane-tilt me-2"></i> Approve with Notes').fadeIn();
                    } else if (selectedAction === 'reject') {
                        notesContainer.slideDown();
                        notesInput.attr('required', 'required');
                        submitBtn.css('background', 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)')
                                .html('<i class="ph-bold ph-x-circle me-2"></i> Submit Reject').fadeIn();
                    }
                });

                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const action = $('#final_action').val();
                    const notesValue = $('#modal_notes').val() ? $('#modal_notes').val().trim() : '';

                    const customerCodeInput = $('#it_update_code');
                    const isITForm = customerCodeInput.length > 0;

                    const topInput = $('#input_top');
                    const isFinanceForm = topInput.length > 0;

                    if (isITForm) {
                        const inputCode = customerCodeInput.val().trim();
                        if (!inputCode) {
                            Swal.fire('Error', 'Customer Code is required!', 'error');
                            return;
                        }

                        Swal.fire({
                            title: 'Confirm Activation?',
                            html: `Please confirm that the Customer Code is correct:<br><br><h2 class="text-primary fw-bold mb-0">${inputCode}</h2>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#059669',
                            confirmButtonText: 'Yes, Activate Now!',
                            cancelButtonText: 'Batal',
                            target: document.getElementById('viewModal')
                        }).then((result) => {
                            if (result.isConfirmed) processApprovalAjax(form);
                        });
                    }
                    else {
                        if (!action) {
                            Swal.fire('Warning', 'Please select your decision (Approve / Reject) first.', 'warning');
                            return;
                        }

                        const isReject = action === 'reject';
                        const isApprove = action === 'approve';

                        if (isReject) {
                            if (!notesValue || !/[a-zA-Z]/.test(notesValue)) {
                                Swal.fire('Warning', 'Reason for rejection is required and must be clear.', 'warning');
                                return;
                            }
                        }
                        else if (action === 'review') {
                            if (isFinanceForm) {
                                const currentTop = String(topInput.val() || '').trim();
                                const originalTop = String(topInput.attr('data-original') || '').trim();

                                if (currentTop !== originalTop) {
                                    if (!notesValue) {
                                        Swal.fire('Warning', 'Notes are required because you have changed the Term of Payment (TOP).', 'warning');
                                        return;
                                    }
                                }
                            }
                            else {
                                if (!notesValue) {
                                    Swal.fire('Warning', 'Notes are required for this approval.', 'warning');
                                    return;
                                }
                            }
                        }

                        let title = 'Confirm Action?';
                        let text = 'Proceed with this decision?';
                        let confirmColor = '#3b82f6';
                        let icon = 'question';

                        if (isReject) {
                            title = 'Confirm Rejection?';
                            text = "This application will be rejected and returned.";
                            icon = 'warning';
                            confirmColor = '#ef4444';
                        } else if (isApprove) {
                            title = 'Approve without Notes?';
                            text = "You will approve this application without providing any notes.";
                            confirmColor = '#10b981';
                        } else {
                            title = 'Submit Approval?';
                            text = "Submit the approval along with any notes or data changes?";
                        }

                        Swal.fire({
                            title: title,
                            text: text,
                            icon: icon,
                            showCancelButton: true,
                            confirmButtonColor: confirmColor,
                            confirmButtonText: 'Yes, Submit!',
                            cancelButtonText: 'Cancel',
                            target: document.getElementById('viewModal'),
                            customClass: {
                                confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm text-white',
                                cancelButton: 'btn btn-light rounded-pill px-4 fw-bold shadow-sm border'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) processApprovalAjax(form);
                        });
                    }
                });

                function processApprovalAjax(form) {
                    $('#loading-overlay').css('display', 'flex').hide().fadeIn('fast');
                    $('#viewModal').modal('hide');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function(res) {
                            $('#loading-overlay').fadeOut('fast');
                            Swal.fire({
                                title: 'Success!', html: res.message, icon: 'success',
                                customClass: { confirmButton: 'btn btn-success rounded-pill px-4 fw-bold shadow-sm text-white' }, buttonsStyling: false
                            });
                            if (typeof table !== 'undefined') table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            $('#loading-overlay').fadeOut('fast');
                            const errMsg = xhr.responseJSON?.message || 'An error occurred while processing the request.';
                            Swal.fire('Error!', errMsg, 'error');
                        }
                    });
                }

                $('#viewModal').on('hidden.bs.modal', function () {
                    $('#viewModalActionFormContainer').empty();
                    $('#viewModalFooter button[type="submit"]').remove();
                });
            });
        </script>
    @endpush
</x-app-layout>
