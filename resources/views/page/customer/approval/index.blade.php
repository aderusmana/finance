<x-app-layout>
    @section('title', 'Customer Approvals List')

    @include('components.sample-table-styles')

    {{-- Custom Approval Decision & Beautiful Dialog Styling --}}
    <style>
        .decision-btn[data-select-action="approve"]:hover {
            background: #f0fdf4 !important;
            border-color: #a7f3d0 !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.12) !important;
        }
        .decision-btn[data-select-action="review"]:hover {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.12) !important;
        }
        .decision-btn[data-select-action="reject"]:hover {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.12) !important;
        }

        .decision-btn[data-select-action="approve"].active {
            background: #ecfdf5 !important;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
        }
        .decision-btn[data-select-action="review"].active {
            background: #eff6ff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        }
        .decision-btn[data-select-action="reject"].active {
            background: #fef2f2 !important;
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }

        /* Beautiful SweetAlert Custom Popup */
        .swal2-custom-approval-popup {
            border-radius: 1.5rem !important;
            padding: 2rem 1.75rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.18) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        .swal2-custom-approval-popup .swal2-html-container {
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }
        .swal2-custom-approval-popup .swal2-actions {
            margin-top: 1.5rem !important;
            gap: 12px !important;
        }
        .btn-swal-confirm {
            padding: 0.65rem 1.75rem !important;
            border-radius: 50rem !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            border: none !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .btn-swal-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2) !important;
        }
        .btn-swal-cancel {
            padding: 0.65rem 1.5rem !important;
            border-radius: 50rem !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .btn-swal-cancel:hover {
            background: #e2e8f0 !important;
            color: #1e293b !important;
        }
    </style>

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
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between control-bar-container" style="background: #ffffff; border-radius: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; padding: 1.25rem 1.5rem; z-index: 2; position: relative;">

                    <div class="d-flex align-items-center gap-2 gap-md-3 flex-wrap filter-container-responsive">
                        <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1 border">
                            <i class="ph-bold ph-funnel text-primary"></i>
                            <span class="text-muted fw-bold" style="font-size: 0.85rem;">FILTER</span>
                        </div>
                        <select id="levelFilter" class="form-select select2" style="width: 160px;">
                            <option value="all">All Levels</option>
                            @foreach($levels as $level)
                                <option value="{{ $level }}">Level {{ $level }}</option>
                            @endforeach
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

                    <div class="customer-approval-stats d-flex gap-2 gap-md-3 mt-3 mt-md-0 w-100 w-md-auto">
                        <div class="stat-badge-card d-flex align-items-center gap-2 px-2 px-md-3 py-2 flex-fill" style="background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 1rem; box-shadow: 0 2px 5px rgba(217, 119, 6, 0.1);">
                            <div class="stat-icon-box" style="width: 30px; height: 30px; background: #f59e0b; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                <i class="ph-bold ph-hourglass-high" style="font-size: 0.85rem;"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm overflow-hidden">
                                <span class="stat-title" style="font-size: 0.65rem; color: #b45309; font-weight: 700; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pending</span>
                                <span class="stat-val" style="font-size: 1.05rem; color: #92400e; font-weight: 800; line-height: 1;">{{ $pendingCount }}</span>
                            </div>
                        </div>
                        <div class="stat-badge-card d-flex align-items-center gap-2 px-2 px-md-3 py-2 flex-fill" style="background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; border-radius: 1rem; box-shadow: 0 2px 5px rgba(37, 99, 235, 0.1);">
                            <div class="stat-icon-box" style="width: 30px; height: 30px; background: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                <i class="ph-bold ph-spinner" style="font-size: 0.85rem;"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm overflow-hidden">
                                <span class="stat-title" style="font-size: 0.65rem; color: #1d4ed8; font-weight: 700; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Processing</span>
                                <span class="stat-val" style="font-size: 1.05rem; color: #1e3a8a; font-weight: 800; line-height: 1;">{{ $processingCount }}</span>
                            </div>
                        </div>
                        <div class="stat-badge-card d-flex align-items-center gap-2 px-2 px-md-3 py-2 flex-fill" style="background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 1rem; box-shadow: 0 2px 5px rgba(22, 163, 74, 0.1);">
                            <div class="stat-icon-box" style="width: 30px; height: 30px; background: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                <i class="ph-bold ph-seal-check" style="font-size: 0.85rem;"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm overflow-hidden">
                                <span class="stat-title" style="font-size: 0.65rem; color: #15803d; font-weight: 700; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Approved</span>
                                <span class="stat-val" style="font-size: 1.05rem; color: #166534; font-weight: 800; line-height: 1;">{{ $approvedCount }}</span>
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

                                <div class="text-start ms-auto" style="min-width: 180px;">

                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                                        <span style="font-weight: 600; margin-right: 4px;">No Rev:</span>
                                        <span id="view_modal_rev_number" style="font-weight: 700; color: #3b82f6; font-size: 13px;">-</span>
                                    </div>

                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                                        <span style="font-weight: 600; margin-right: 4px;">Revision:</span>
                                        <span id="view_modal_rev_count" style="font-weight: 700; color: #1e293b; font-size: 13px;">0</span>
                                    </div>

                                    <div style="font-size: 12px; color: #64748b;">
                                        <span style="font-weight: 600; margin-right: 4px;">Date:</span>
                                        <span id="view_modal_rev_date" style="font-weight: 700; color: #1e293b; font-size: 13px;">-</span>
                                    </div>

                                </div>

                            </div>
                        </div>

                        {{-- General Info --}}
                        <h5 class="fw-bolder mb-3" style="color: #1e3a8a;"><i class="ph-fill ph-info me-2 text-primary"></i> General Information</h5>
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 2rem;">
                            <div class="row g-4">
                                {{-- UBAH JADI COL-MD-7 --}}
                                <div class="col-md-7" style="border-right: 1px dashed #e2e8f0;">
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
                                        
                                        {{-- TAMBAHAN: GROUP, TYPE, CLASS --}}
                                        <div class="col-4">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Group</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.9rem;" id="view_account_group">-</div>
                                        </div>
                                        <div class="col-4">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Type</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.9rem;" id="view_customer_type">-</div>
                                        </div>
                                        <div class="col-4">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Class</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.9rem;" id="view_customer_class">-</div>
                                        </div>

                                        <div class="col-12">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Person in Charge (PIC)</label>
                                            <div class="fw-bold" style="color: #334155; font-size: 0.95rem;" id="view_pic">-</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-3 mt-1 mb-1 rounded d-flex align-items-center" style="background-color: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.2);">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 36px; height: 36px; background-color: #2563eb; color: white;">
                                                    <i class="ph-fill ph-user-circle" style="font-size: 1.25rem;"></i>
                                                </div>
                                                <div>
                                                    <label style="color: #2563eb; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 0.5px;">Sales Representative</label>
                                                    <div class="fw-bolder" style="color: #0f172a; font-size: 1rem;" id="view_sales">-</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Email address</label>
                                            <div class="fw-bold" style="color: #2563eb; font-size: 0.95rem;" id="view_email">-</div>
                                        </div>
                                    </div>
                                </div>
                                {{-- UBAH JADI COL-MD-5 AGAR PAS --}}
                                <div class="col-md-5 ps-md-4">
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
                                    <div class="d-flex justify-content-between mt-3 pt-3" style="border-top: 1px dashed #e2e8f0;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Currency</span>
                                        <span class="fw-bolder text-uppercase" style="color: #1e3a8a; font-size: 0.9rem;" id="view_ccar">-</span>
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
                            d.level = $('#levelFilter').val();
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

                $('#levelFilter, #approvalStatusFilter').on('change', function() { table.ajax.reload(); });
                $('#resetFilters').on('click', function() {
                    $('#levelFilter').val('all').trigger('change');
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

                    // --- MAPPING GENERAL INFO BARU ---
                    let accountGroupName = data.account_group?.name_account_group || data.account_group || '-';
                    let customerClassName = data.customer_class?.name_class || data.customer_class || '-';
                    
                    $('#view_account_group').text(accountGroupName);
                    $('#view_customer_type').text(data.customer_type || '-');
                    $('#view_customer_class').text(customerClassName);
                    $('#view_sales').text(salesName);

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
                    $('#view_term_of_payment').text(data.term_of_payment || '-');

                    // --- FINANCE SECTION ---
                    const limit = parseFloat(data.credit_limit) || 0;
                    const formattedLimit = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(limit);

                    $('#container_top, #container_lead_time, #container_credit_limit, #container_npwp, #finance_schedule_container').empty();
                    $('#finance_schedule_container').hide();
                    $('#calc_badge').hide();

                    if (data.can_adjust_finance) {
                        
                        // Buat inputan Approved Credit Limit jika BG Yes / TOP CBD
                        let apprvInputHtml = '';
                        if (data.bank_garansi === 'YA' || String(data.term_of_payment).toUpperCase() === 'CBD') {
                            const apprvVal = data.approved_credit_limit || '';
                            apprvInputHtml = `
                                <div class="mt-3" style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 12px;">
                                    <label style="color: #a7f3d0; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Approved Credit Limit</label>
                                    <input type="number" name="update_approved_credit_limit" class="form-control form-control-sm fw-bold mt-1 shadow-none" 
                                           value="${apprvVal}" placeholder="Opsional: Input Limit" form="modalResponseForm" 
                                           style="border: 1px solid rgba(16, 185, 129, 0.6); background: rgba(16, 185, 129, 0.15); color: #a7f3d0;">
                                </div>
                            `;
                        }

                        $('#container_credit_limit').html(`
                            <h3 class="mb-0 fw-bolder mt-1 text-white" id="view_credit_limit">${formattedLimit}</h3>
                            <input type="hidden" name="update_credit_limit_value" id="hidden_credit_limit" value="${limit}" form="modalResponseForm">
                            <span id="calc_badge" style="display:none; background: rgba(255,255,255,0.2); border-radius: 4px; padding: 2px 6px; font-size: 0.7rem; margin-top: 8px; display: inline-block;">
                                <i class="ph-fill ph-calculator me-1"></i> Auto-Calculated
                            </span>
                            ${apprvInputHtml}
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
                        // Tampilan untuk Non-Finance (Badge Approved Limit)
                        let apprvBadgeHtml = '';
                        const apprvCl = parseFloat(data.approved_credit_limit);
                        
                        if ((data.bank_garansi === 'YA' || String(data.term_of_payment).toUpperCase() === 'CBD') && !isNaN(apprvCl)) {
                            const fmtApprv = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(apprvCl);
                            apprvBadgeHtml = `
                                <div id="view_approved_credit_limit_wrapper" class="mt-2">
                                    <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #a7f3d0; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700;">
                                        <i class="ph-bold ph-check-circle me-1"></i>Apprv: <span>${fmtApprv}</span>
                                    </span>
                                </div>
                            `;
                        }

                        $('#container_credit_limit').html(`
                            <h3 class="mb-0 fw-bolder mt-1 text-white" id="view_credit_limit">${formattedLimit}</h3>
                            ${apprvBadgeHtml}
                        `);
                        $('#container_top').html(`<span class="fw-bolder" style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 6px; font-size: 0.9rem;">${data.term_of_payment || '-'}</span>`);
                        $('#container_lead_time').html(`<span class="fw-bolder" style="font-size: 0.9rem;">${data.lead_time || 0} Days</span>`);
                        $('#container_npwp').html(`<span class="fw-bolder mt-1" style="color: #0f172a; font-size: 1rem;" id="view_npwp">${data.npwp || '-'}</span>`);
                        $('#finance_schedule_container').hide();
                    }

                    // --- TAX & BILLING ---
                    $('#view_tanggal_npwp').text(data.tanggal_npwp || '-');
                    $('#view_nppkp').text(data.nppkp || '-');
                    $('#view_output_tax').text(data.output_tax || '-');
                    $('#view_ccar').text(data.ccar || '-');

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
                    const customerName = button.data('name') || '';
                    window.currentApprovalCustomerName = customerName;
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
                                                <input type="hidden" name="notes" id="modal_notes" value="">

                                                <label style="color: #475569; font-size: 0.85rem; font-weight: 700; margin-bottom: 12px; display: block;">Choose Your Decision <span class="text-danger">*</span></label>
                                                <div class="d-flex flex-column flex-md-row gap-3 mb-2">
                                                    <div class="decision-btn flex-fill" data-select-action="approve" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center;">
                                                        <div style="width: 40px; height: 40px; background: #10b981; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(16,185,129,0.2); flex-shrink: 0;">
                                                            <i class="ph-bold ph-check-circle fs-4"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bolder" style="color: #1e293b; font-size: 0.95rem;">Approve<br><span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">No Notes</span></h6>
                                                    </div>

                                                    <div class="decision-btn flex-fill" data-select-action="review" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center;">
                                                        <div style="width: 40px; height: 40px; background: #3b82f6; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(59,130,246,0.2); flex-shrink: 0;">
                                                            <i class="ph-bold ph-note-pencil fs-4"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bolder" style="color: #1e293b; font-size: 0.95rem;">Approve<br><span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">With Notes</span></h6>
                                                    </div>

                                                    <div class="decision-btn flex-fill" data-select-action="reject" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center;">
                                                        <div style="width: 40px; height: 40px; background: #ef4444; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(239,68,68,0.2); flex-shrink: 0;">
                                                            <i class="ph-bold ph-x-circle fs-4"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bolder" style="color: #1e293b; font-size: 0.95rem;">Reject<br><span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">With Notes</span></h6>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                `;

                                submitBtnHtml = `
                                    <button type="button" id="final_submit_btn" class="btn rounded-pill px-5 py-2 fw-bold shadow-sm" style="display: none; border: none; color: white;">
                                        Submit Decision
                                    </button>
                                `;
                            }

                            const submitUrl = "{{ route('customers.approval_action', ':id') }}".replace(':id', customerId);
                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalActionFormContainer form').attr('action', submitUrl);

                            const REV_MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

                            function pad2(n) {
                                const num = Number(n);
                                return num < 10 ? '0' + num : String(num);
                            }

                            function formatRevisionDate(value) {
                                if (value === undefined || value === null) return '-';
                                const s = String(value).trim();
                                if (!s || s === '-' || s.toLowerCase() === 'null') return '-';

                                // Prefer manual parsing for YYYY-MM-DD to avoid timezone shifts
                                const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
                                if (m) {
                                    const year = Number(m[1]);
                                    const month = Number(m[2]);
                                    const day = Number(m[3]);
                                    if (month >= 1 && month <= 12 && day >= 1 && day <= 31) {
                                        return `${pad2(day)}-${REV_MONTHS[month - 1]}-${String(year).slice(-2)}`;
                                    }
                                }

                                const d = new Date(s);
                                if (!Number.isNaN(d.getTime())) {
                                    const day = d.getDate();
                                    const month = d.getMonth() + 1;
                                    const year = d.getFullYear();
                                    return `${pad2(day)}-${REV_MONTHS[month - 1]}-${String(year).slice(-2)}`;
                                }

                                return s;
                            }

                            if (response.latest_revision) {
                                $('#view_modal_rev_number').text(response.latest_revision.revision_number);
                                $('#view_modal_rev_count').text(response.latest_revision.revision_count);
                                $('#view_modal_rev_date')
                                    .text(formatRevisionDate(response.latest_revision.revision_date))
                                    .attr('title', response.latest_revision.revision_date ?? '-');
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

                // Function to execute dynamic decision flow with SweetAlert confirmation & dialog prompt
                function executeDecisionFlow(selectedAction) {
                    const form = $('#modalResponseForm');
                    const customerName = window.currentApprovalCustomerName || 'Customer';
                    const topInput = $('#input_top');
                    const isFinanceForm = topInput.length > 0;
                    const isTopChanged = isFinanceForm && (String(topInput.val() || '').trim() !== String(topInput.attr('data-original') || '').trim());

                    $('#final_action').val(selectedAction);

                    let popupBg = '';
                    let iconHtml = '';
                    let titleHtml = '';
                    let badgeHtml = '';
                    let messageHtml = '';
                    let confirmBtnHtml = '';
                    let confirmBtnBg = '';

                    if (selectedAction === 'approve') {
                        popupBg = 'linear-gradient(180deg, #ecfdf5 0%, #f7fefb 110px, #ffffff 180px)';
                        iconHtml = `
                            <div style="width: 64px; height: 64px; background: #d1fae5; border: 2.5px solid #a7f3d0; border-radius: 50%; color: #059669; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; box-shadow: 0 8px 20px rgba(16,185,129,0.2);">
                                <i class="ph-bold ph-check-circle" style="font-size: 2.25rem;"></i>
                            </div>
                        `;
                        titleHtml = `<h4 class="fw-bolder mb-1" style="color: #065f46;">Konfirmasi Approval</h4>`;
                        badgeHtml = `
                            <div class="mb-3">
                                <span style="background: rgba(16,185,129,0.12); color: #047857; padding: 5px 14px; border-radius: 50rem; font-size: 0.85rem; font-weight: 700; display: inline-block;">
                                    <i class="ph-bold ph-user me-1"></i>${customerName}
                                </span>
                            </div>
                        `;
                        messageHtml = `<p style="color: #475569; font-size: 0.95rem; margin-bottom: 0;">Apakah Anda yakin ingin <strong>menyetujui</strong> pengajuan customer ini <span class="text-success fw-bold">tanpa catatan</span>?</p>`;
                        confirmBtnHtml = '<i class="ph-bold ph-check-circle me-1"></i> Ya, Setujui Sekarang!';
                        confirmBtnBg = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                    } else if (selectedAction === 'review') {
                        popupBg = 'linear-gradient(180deg, #eff6ff 0%, #f8faff 110px, #ffffff 180px)';
                        iconHtml = `
                            <div style="width: 64px; height: 64px; background: #dbeafe; border: 2.5px solid #bfdbfe; border-radius: 50%; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; box-shadow: 0 8px 20px rgba(37,99,235,0.2);">
                                <i class="ph-bold ph-note-pencil" style="font-size: 2.25rem;"></i>
                            </div>
                        `;
                        titleHtml = `<h4 class="fw-bolder mb-1" style="color: #1e40af;">Konfirmasi Approve dengan Catatan</h4>`;
                        badgeHtml = `
                            <div class="mb-3">
                                <span style="background: rgba(59,130,246,0.12); color: #1d4ed8; padding: 5px 14px; border-radius: 50rem; font-size: 0.85rem; font-weight: 700; display: inline-block;">
                                    <i class="ph-bold ph-user me-1"></i>${customerName}
                                </span>
                            </div>
                        `;
                        messageHtml = `<p style="color: #475569; font-size: 0.95rem; margin-bottom: 0;">Apakah Anda yakin ingin menyetujui pengajuan customer ini dengan <strong>menyertakan catatan</strong>?</p>`;
                        confirmBtnHtml = '<i class="ph-bold ph-arrow-right me-1"></i> Ya, Lanjutkan Isi Catatan';
                        confirmBtnBg = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
                    } else if (selectedAction === 'reject') {
                        popupBg = 'linear-gradient(180deg, #fef2f2 0%, #fff5f5 110px, #ffffff 180px)';
                        iconHtml = `
                            <div style="width: 64px; height: 64px; background: #fee2e2; border: 2.5px solid #fecaca; border-radius: 50%; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; box-shadow: 0 8px 20px rgba(239,68,68,0.2);">
                                <i class="ph-bold ph-x-circle" style="font-size: 2.25rem;"></i>
                            </div>
                        `;
                        titleHtml = `<h4 class="fw-bolder mb-1" style="color: #991b1b;">Konfirmasi Penolakan</h4>`;
                        badgeHtml = `
                            <div class="mb-3">
                                <span style="background: rgba(239,68,68,0.12); color: #b91c1c; padding: 5px 14px; border-radius: 50rem; font-size: 0.85rem; font-weight: 700; display: inline-block;">
                                    <i class="ph-bold ph-user me-1"></i>${customerName}
                                </span>
                            </div>
                        `;
                        messageHtml = `<p style="color: #475569; font-size: 0.95rem; margin-bottom: 0;">Apakah Anda yakin ingin <strong>menolak</strong> pengajuan customer ini? Alasan penolakan wajib disertakan.</p>`;
                        confirmBtnHtml = '<i class="ph-bold ph-arrow-right me-1"></i> Ya, Lanjutkan Tolak';
                        confirmBtnBg = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                    }

                    const step1Html = `
                        <div class="text-center px-2 py-1">
                            ${iconHtml}
                            ${titleHtml}
                            ${badgeHtml}
                            ${messageHtml}
                        </div>
                    `;

                    // Step 1: Dialog Konfirmasi Awal dengan visual elegan & dinamis
                    Swal.fire({
                        html: step1Html,
                        background: popupBg,
                        showCancelButton: true,
                        confirmButtonText: confirmBtnHtml,
                        cancelButtonText: 'Batal',
                        target: document.getElementById('viewModal'),
                        customClass: {
                            popup: 'swal2-custom-approval-popup',
                            confirmButton: 'btn-swal-confirm',
                            cancelButton: 'btn-swal-cancel'
                        },
                        didOpen: (popup) => {
                            const confirmBtn = popup.querySelector('.btn-swal-confirm');
                            if (confirmBtn) confirmBtn.style.background = confirmBtnBg;
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            $('.decision-btn').removeClass('active');
                            $('#final_action').val('');
                            return;
                        }

                        // Jika Approve langsung tanpa catatan:
                        if (selectedAction === 'approve') {
                            if (isTopChanged) {
                                Swal.fire({
                                    title: 'Catatan Wajib Diisi',
                                    text: 'Anda telah mengubah Term of Payment (TOP), sehingga wajib menyertakan catatan approval.',
                                    icon: 'warning',
                                    target: document.getElementById('viewModal')
                                }).then(() => {
                                    executeDecisionFlow('review');
                                });
                                return;
                            }
                            $('#modal_notes').val('');
                            processApprovalAjax(form);
                            return;
                        }

                        // Step 2: Dialog Isi Catatan / Alasan (Disesuaikan background & tampilannya dengan pilihan)
                        let step2Bg = '';
                        let step2IconHtml = '';
                        let step2Title = '';
                        let step2BadgeColor = '';
                        let step2TextareaBg = '';
                        let step2BorderColor = '';
                        let step2FocusBorder = '';
                        let step2FocusGlow = '';
                        let step2Placeholder = '';
                        let step2Label = '';
                        let step2SubmitText = '';
                        let step2SubmitBg = '';
                        let step2NoticeHtml = '';

                        if (selectedAction === 'review') {
                            step2Bg = 'linear-gradient(180deg, #eff6ff 0%, #f8faff 130px, #ffffff 100%)';
                            step2IconHtml = `
                                <div style="width: 58px; height: 58px; background: #dbeafe; border: 2.5px solid #bfdbfe; border-radius: 16px; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; box-shadow: 0 8px 18px rgba(37,99,235,0.18);">
                                    <i class="ph-bold ph-note-pencil" style="font-size: 2rem;"></i>
                                </div>
                            `;
                            step2Title = `<h4 class="fw-bolder mb-1" style="color: #1e40af;">Catatan Approval</h4>`;
                            step2BadgeColor = 'background: rgba(59,130,246,0.12); color: #1d4ed8;';
                            step2TextareaBg = '#f8faff';
                            step2BorderColor = '#bfdbfe';
                            step2FocusBorder = '#3b82f6';
                            step2FocusGlow = 'rgba(59, 130, 246, 0.2)';
                            step2Label = 'Catatan / Rekomendasi Approval';
                            step2Placeholder = 'Tuliskan catatan approval atau rekomendasi Anda secara jelas di sini...';
                            step2SubmitText = '<i class="ph-bold ph-check-circle me-1"></i> Simpan & Setujui';
                            step2SubmitBg = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';

                            if (isTopChanged) {
                                step2NoticeHtml = `
                                    <div class="alert alert-warning py-2 px-3 mb-3 text-start d-flex align-items-center gap-2" style="font-size: 0.85rem; border-radius: 0.75rem; border: 1px solid #fde68a; background: #fffbeb;">
                                        <i class="ph-bold ph-warning-circle fs-5 text-warning flex-shrink-0"></i>
                                        <span>Anda telah mengubah Term of Payment (TOP). Mohon jelaskan alasan perubahan pada catatan di bawah.</span>
                                    </div>
                                `;
                            }
                        } else if (selectedAction === 'reject') {
                            step2Bg = 'linear-gradient(180deg, #fef2f2 0%, #fffbfa 130px, #ffffff 100%)';
                            step2IconHtml = `
                                <div style="width: 58px; height: 58px; background: #fee2e2; border: 2.5px solid #fecaca; border-radius: 16px; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; box-shadow: 0 8px 18px rgba(220,38,38,0.18);">
                                    <i class="ph-bold ph-warning-circle" style="font-size: 2rem;"></i>
                                </div>
                            `;
                            step2Title = `<h4 class="fw-bolder mb-1" style="color: #991b1b;">Alasan Penolakan</h4>`;
                            step2BadgeColor = 'background: rgba(239,68,68,0.12); color: #b91c1c;';
                            step2TextareaBg = '#fffbfa';
                            step2BorderColor = '#fecaca';
                            step2FocusBorder = '#ef4444';
                            step2FocusGlow = 'rgba(239, 68, 68, 0.2)';
                            step2Label = 'Alasan Penolakan';
                            step2Placeholder = 'Tuliskan alasan penolakan secara jelas agar dapat ditindaklanjuti...';
                            step2SubmitText = '<i class="ph-bold ph-x-circle me-1"></i> Kirim Penolakan';
                            step2SubmitBg = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                        }

                        const step2Html = `
                            <div class="text-center px-1">
                                ${step2IconHtml}
                                ${step2Title}
                                <div class="mb-3">
                                    <span style="${step2BadgeColor} padding: 4px 14px; border-radius: 50rem; font-size: 0.825rem; font-weight: 700; display: inline-block;">
                                        <i class="ph-bold ph-user me-1"></i>${customerName}
                                    </span>
                                </div>
                                ${step2NoticeHtml}
                                <div class="text-start mt-2">
                                    <label class="fw-bold mb-1" style="font-size: 0.775rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">${step2Label} <span class="text-danger">*</span></label>
                                    <textarea id="swal_custom_notes" class="form-control" rows="4" placeholder="${step2Placeholder}" 
                                              style="border: 2px solid ${step2BorderColor}; border-radius: 0.85rem; background: ${step2TextareaBg}; font-size: 0.9rem; padding: 0.85rem; resize: none; transition: all 0.2s;" 
                                              onfocus="this.style.borderColor='${step2FocusBorder}'; this.style.boxShadow='0 0 0 4px ${step2FocusGlow}';" 
                                              onblur="this.style.borderColor='${step2BorderColor}'; this.style.boxShadow='none';"></textarea>
                                </div>
                            </div>
                        `;

                        Swal.fire({
                            html: step2Html,
                            background: step2Bg,
                            showCancelButton: true,
                            confirmButtonText: step2SubmitText,
                            cancelButtonText: 'Batal',
                            target: document.getElementById('viewModal'),
                            customClass: {
                                popup: 'swal2-custom-approval-popup',
                                confirmButton: 'btn-swal-confirm',
                                cancelButton: 'btn-swal-cancel'
                            },
                            didOpen: (popup) => {
                                const confirmBtn = popup.querySelector('.btn-swal-confirm');
                                if (confirmBtn) confirmBtn.style.background = step2SubmitBg;
                                const textarea = popup.querySelector('#swal_custom_notes');
                                if (textarea) textarea.focus();
                            },
                            buttonsStyling: false,
                            preConfirm: () => {
                                const noteInput = document.getElementById('swal_custom_notes');
                                const val = noteInput ? noteInput.value.trim() : '';
                                if (!val) {
                                    Swal.showValidationMessage(selectedAction === 'reject' ? 'Alasan penolakan wajib diisi!' : 'Catatan approval wajib diisi!');
                                    return false;
                                }
                                if (val.length < 3) {
                                    Swal.showValidationMessage('Alasan/catatan terlalu singkat. Mohon jelaskan lebih rinci.');
                                    return false;
                                }
                                return val;
                            }
                        }).then((inputResult) => {
                            if (inputResult.isConfirmed) {
                                const finalNote = inputResult.value || '';
                                $('#modal_notes').val(finalNote);
                                processApprovalAjax(form);
                            } else {
                                $('.decision-btn').removeClass('active');
                                $('#final_action').val('');
                            }
                        });
                    });
                }

                $(document).on('click', '.decision-btn', function() {
                    const selectedAction = $(this).data('select-action');
                    if (!selectedAction) return;

                    $('.decision-btn').removeClass('active');
                    $(this).addClass('active');

                    executeDecisionFlow(selectedAction);
                });

                $(document).on('click', '#final_submit_btn', function(e) {
                    e.preventDefault();
                    const action = $('#final_action').val();
                    if (!action) {
                        Swal.fire({
                            title: 'Pilih Keputusan',
                            text: 'Silakan pilih keputusan terlebih dahulu (Approve / Review / Reject).',
                            icon: 'warning',
                            target: document.getElementById('viewModal')
                        });
                        return;
                    }
                    executeDecisionFlow(action);
                });

                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const customerCodeInput = $('#it_update_code');
                    const isITForm = customerCodeInput.length > 0;

                    if (isITForm) {
                        const inputCode = customerCodeInput.val().trim();
                        if (!inputCode) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Customer Code is required!',
                                icon: 'error',
                                target: document.getElementById('viewModal')
                            });
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
                            target: document.getElementById('viewModal'),
                            customClass: {
                                confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm text-white',
                                cancelButton: 'btn btn-light rounded-pill px-4 fw-bold shadow-sm border'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) processApprovalAjax(form);
                        });
                        return;
                    }

                    const action = $('#final_action').val();
                    if (!action) {
                        Swal.fire({
                            title: 'Pilih Keputusan',
                            text: 'Silakan klik salah satu opsi keputusan (Approve / Review / Reject) terlebih dahulu.',
                            icon: 'warning',
                            target: document.getElementById('viewModal')
                        });
                        return;
                    }

                    executeDecisionFlow(action);
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
                            let errMsg = xhr.responseJSON?.message || 'An error occurred while processing the request.';
                            if (xhr.responseJSON?.errors) {
                                errMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            }
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
