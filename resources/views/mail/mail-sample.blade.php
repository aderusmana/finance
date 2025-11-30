<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Notification</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; color: #333; line-height: 1.6; }
        .email-container { max-width: 800px; margin: 20px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); }
        .email-header {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 25px 40px;
            display: flex;
            align-items: center;
            text-align: center;
            justify-content: center;
        }
        .logo-container { padding-right: 20px; }
        .company-logo { max-height: 50px; width: auto; }
        .header-text .company-name { font-size: 20px; font-weight: 700; margin: 0; }
        .header-text .email-title { font-size: 24px; font-weight: 600; margin: 0; }
        .email-content { padding: 40px; }
        .greeting { font-size: 18px; color: #2c3e50; margin-bottom: 25px; padding: 20px; background: #fef8e7; border-radius: 8px; border-left: 4px solid #cc982f; }
        .info-section { margin-bottom: 30px; }
        .section-title { background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%); color: white; padding: 12px 20px; margin: 0 0 15px 0; border-radius: 8px 8px 0 0; font-weight: 600; font-size: 16px; }
        .info-category { background: #fff3cd; color: #856404; font-size: 15px; word-break: break-word; }
        .info-subcategory { background: #cdeaffff; color: #2927a5ff; font-size: 15px; word-break: break-word; }
        .info-grid { width: 100%; background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none; }
        .info-grid table { width: 100%; border-collapse: collapse; }
        .info-grid td { width: 50%; vertical-align: top; padding: 7px; }
        .info-item { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; height: 100%; }
        .info-label { font-weight: 600; color: #495057; font-size: 14px; margin-bottom: 5px; }
        .info-value { color: #2c3e50; font-size: 15px; word-break: break-word; }
        .status-badge { display: inline-block; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce7ff; color: #0066cc; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9ecef
        }

        .product-table th {
            background: #343a40;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px
        }

        .product-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px
        }
        .action-section { background: #fef8e7; padding: 30px; border-radius: 12px; text-align: center; margin: 30px 0; border: 1px solid #cc982f; }
        .action-title { font-size: 20px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; }
        .action-subtitle { color: #6c757d; margin-bottom: 25px; font-size: 16px; }

        .btn { display: inline-block; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; text-align: center; color: white !important; min-width: 140px; transition: all .3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-approve { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .btn-reject { background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%); }
        .btn-review { background: linear-gradient(135deg, #007bff 0%, #0d6efd 100%); }
        .btn-qa-form { background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }
        .email-footer { background: #343a40; color: white; padding: 30px 40px; text-align: center; }
        .copyright { font-size: 12px; opacity: .7; margin-top: 15px; }
        /* Atur tabel utama untuk tombol */
        .button-group {
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        /* Beri jarak antar sel tombol di tampilan desktop */
        .button-cell {
            padding: 5px;
        }

        /* Pastikan tombol mengisi selnya */
        .button-group .btn {
            width: 100%; /* Tombol akan mengisi lebar sel */
            box-sizing: border-box; /* Padding tidak akan menambah lebar */
        }


        /* --- INI BAGIAN PENTING UNTUK RESPONSIVE --- */
        @media screen and (max-width: 600px) {
            /* Ubah sel tabel (td) menjadi block element */
            .button-group .button-cell {
                display: block;
                width: 100% !important;
                padding: 8px 0; /* Beri jarak vertikal antar tombol */
            }

            /* Atur lebar tombol di mobile agar tidak terlalu mepet ke tepi */
            .button-group .btn {
                width: 90% !important;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="header-content">
                <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="{{ config('app.name') }}" class="company-logo">
                <h1 class="email-title">Requisition Slip Request</h1>
                <p class="email-subtitle">{{ $requisition->no_srs }} - Sample Request</p>
            </div>
        </div>

        <div class="email-content">
            {{-- ===================== BAGIAN GREETING ===================== --}}
            <div class="greeting">
                <strong>Hello {{ $recipient->name }},</strong><br>
                @if(isset($mail_type) && $mail_type === 'qa_form_notification')
                    Requisition <strong>{{ $requisition->no_srs }}</strong> requires your action to complete the QA/QM HSE form. Please click the button below.
                @elseif(isset($mail_type) && $mail_type === 'warehouse_process')
                    Requisition <strong>{{ $requisition->no_srs }}</strong> has been fully approved and now requires your action for the process: <strong>{{ $process_step ?? 'N/A' }}</strong>.
                @elseif(isset($mail_type) && $mail_type === 'completed_notification')
                    Kabar baik! Sample Requisition Anda dengan nomor <strong>{{ $requisition->no_srs }}</strong> telah selesai diproses dan siap untuk langkah selanjutnya.
                @elseif(isset($mail_type) && $mail_type === 'rejection_notification')
                    Mohon maaf, Sample Requisition Anda dengan nomor <strong>{{ $requisition->no_srs }}</strong> telah ditolak.
                @elseif(isset($mail_type) && $mail_type === 'recallation_notification')
                    This is a notification to inform you that the sample requisition <strong>{{ $requisition->no_srs }}</strong> from <strong>{{ $requisition->requester->name ?? 'N/A' }}</strong> has been **RECALLED** by the requester.
                    <br><br>
                    No further action is required from you for this request.
                @else
                    A new sample requisition requires your review and approval. Please check the details and choose an action.
                @endif
            </div>

            <div class="info-section">
                <h3 class="section-title">📄 Request Information</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Category</div>
                                    <div class="info-value">{{ $requisition->category }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Sub Category</div>
                                    <div class="info-value">{{ $requisition->sub_category }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Request Number</div>
                                    <div class="info-value">{{ $requisition->no_srs }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Request Date</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Requester</div>
                                    <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Current Status</div>
                                    <div class="info-value">
                                        {{-- JIKA INI ADALAH EMAIL UNTUK PROSES GUDANG, TAMPILKAN "APPROVED" --}}
                                        @if(isset($mail_type) && $mail_type === 'warehouse_process')
                                            <span class="status-badge status-approved">Approved</span>

                                        {{-- JIKA BUKAN, GUNAKAN LOGIKA STATUS SEPERTI BIASA --}}
                                        @elseif($requisition->status == 'Pending')
                                            <span class="status-badge status-pending">Pending</span>
                                        @elseif($requisition->status == 'In Progress' || $requisition->status == 'Processing')
                                            <span class="status-badge status-processing">In Progress</span>
                                        @elseif($requisition->status == 'Approved')
                                            <span class="status-badge status-approved">Approved</span>
                                        @elseif($requisition->status == 'Rejected')
                                            <span class="status-badge status-rejected">Rejected</span>
                                        @elseif($requisition->status == 'Completed')
                                            <span class="status-badge status-approved">Completed</span>
                                        @elseif($requisition->status == 'Canceled')
                                            <span class="status-badge status-rejected">Canceled</span>
                                        @else
                                            <span>{{ $requisition->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Objective</div>
                                    <div class="info-value">{{ $requisition->objectives }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Estimated Potential</div>
                                    <div class="info-value">{{ $requisition->estimated_potential ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Detail Item --}}
            @if($requisition->requisitionItems->count() > 0)
            <div class="info-section">
                <h3 class="section-title">📦 Requested Item List</h3>
                <div
                    style="background: #f8f9fa; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <table class="product-table">
                        <thead>
                            <tr>
                                @if($requisition->sub_category == 'Packaging')
                                <th>Material Type</th>
                                @endif
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Unit</th>
                                <th>Qty Required</th>
                                <th>Qty Issued</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->requisitionItems as $item)
                            <tr>
                                @if($requisition->sub_category == 'Packaging')
                                <td>{{ $item->material_type ?? '-' }}</td>
                                <td>{{ $item->itemDetail->item_detail_code ?? '-' }}</td>
                                <td>{{ $item->itemDetail->item_detail_name ?? '-' }}</td>
                                <td>{{ $item->itemDetail->unit ?? '-' }}</td>
                                @else
                                <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                <td>{{ $item->itemMaster->unit ?? '-' }}</td>
                                @endif
                                <td style="text-align: center; font-weight: 600;">{{ $item->quantity_required ?? 0 }}</td>
                                <td style="text-align: center; font-weight: 600;">{{ $item->quantity_issued ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="action-section">
                @if(isset($mail_type) && $mail_type == 'qa_form_notification')
                    <h3 class="action-title">📝 Complete QA Form</h3>
                    <p class="action-subtitle">Please click the button below to open the form and complete the required fields.</p>
                    <div>
                        <tr><td><a href="{{ $form_url }}" class="btn btn-qa-form">Open QA Form</a></td></tr>
                    </div>
                @elseif(isset($mail_type) && $mail_type == 'warehouse_process')
                    <h3 class="action-title">📦 Warehouse Action Required</h3>
                    <p class="action-subtitle">Process step: <strong>{{ $process_step ?? 'N/A' }}</strong>. Please choose an action.</p>
                    <table class="button-group" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="button-cell">
                                <a href="{{ $submit_url }}" class="btn btn-approve">✅ Submit Process</a>
                            </td>
                            <td class="button-cell">
                                <a href="{{ $review_url }}" class="btn btn-review">📝 Submit with Notes</a>
                            </td>
                        </tr>
                    </table>
                @elseif(isset($mail_type) && $mail_type === 'completed_notification')
                    <h3 class="action-title" style="color: #28a745;">✅ Process Completed</h3>
                    <p class="action-subtitle">Tidak ada tindakan lebih lanjut yang diperlukan dari Anda untuk email ini. Terima kasih.</p>

                @elseif(isset($mail_type) && $mail_type === 'recallation_notification')
                    <h3 class="action-title" style="color: #6c757d;">🚫 Requisition Recalled</h3>
                    <p class="action-subtitle">This requisition has been recalled by the requester <strong>{{ $approver_name ?? 'Requester' }}</strong>
                    <br>
                        Alasan: <i>"{{ $rejection_notes ?? 'Tidak ada alasan yang diberikan.' }}"</i>
                    </p>

                @elseif(isset($mail_type) && $mail_type === 'rejection_notification')
                    <h3 class="action-title" style="color: #dc3545;">❌ Requisition Rejected</h3>
                    <p class="action-subtitle">
                        Ditolak oleh: <strong>{{ $approver_name ?? 'Approver' }}</strong>
                        <br>
                        Alasan: <i>"{{ $rejection_notes ?? 'Tidak ada alasan yang diberikan.' }}"</i>
                    </p>

                {{-- [PERBAIKAN] Blok 'else' ini sekarang hanya akan berjalan untuk email approval biasa --}}
                @else
                    {{-- Pastikan variabel URL ada sebelum menampilkan tombol --}}
                    @if(isset($approve_url) && isset($review_url) && isset($reject_url))
                        <h3 class="action-title">⚡ Take Action</h3>
                        <p class="action-subtitle">Please review the request above and choose your action below</p>
                        <table class="button-group" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="button-cell">
                                    <a href="{{ $approve_url }}" class="btn btn-approve">✅ Quick Approve</a>
                                </td>
                                <td class="button-cell">
                                    <a href="{{ $review_url }}" class="btn btn-review">📝 Review with Notes</a>
                                </td>
                                <td class="button-cell">
                                    <a href="{{ $reject_url }}" class="btn btn-reject">❌ Quick Reject</a>
                                </td>
                            </tr>
                        </table>
                    @else
                        <h3 class="action-title">ℹ️ Notification Only</h3>
                        <p class="action-subtitle">This is a notification email. No action is required from you.</p>
                    @endif
                @endif
            </div>
        </div>

        <div class="email-footer">
            <div class="copyright">
                © {{ date('Y') }} PT. Sinar Meadow International Indonesia. All rights reserved.<br>
                This is an automated message, please do not reply.
            </div>
        </div>
    </div>
</body>
</html>
