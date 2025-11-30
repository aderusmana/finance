<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        .email-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="20" fill="url(%23dots)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .company-logo {
            max-height: 50px;
            width: auto;
            margin-bottom: 15px;
        }
        .email-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-subtitle {
            font-size: 16px;
            margin: 10px 0 0 0;
            opacity: 0.9;
        }

        /* Content */
        .email-content {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border-left: 4px solid #cc982f;
        }

        /* Info Cards */
        .info-section {
            margin-bottom: 30px;
        }
        .section-title {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 12px 20px;
            margin: 0 0 15px 0;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            font-size: 16px;
        }
        .info-grid {
            width: 100%;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e9ecef;
            border-top: none;
        }
        .info-grid table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 7px;
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #2c3e50;
            font-size: 15px;
            word-break: break-word;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-progress {
            background: #cce7ff;
            color: #0066cc;
            border: 1px solid #99d6ff;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1b0b7;
        }

        /* Product Table */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
            overflow-x: scroll;
            border: 1px solid #e9ecef;
        }
        .product-table th {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        .product-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        .product-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .product-table tr:hover {
            background-color: #e9ecef;
        }

        /* Action Buttons */
        .action-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            border: 2px solid #cc982f;
        }
        .action-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .action-subtitle {
            color: #6c757d;
            margin-bottom: 25px;
            font-size: 16px;
        }
        .button-group {
            text-align: center;
        }
        .button-group table {
            margin: 0 auto;
            border-collapse: collapse;
        }
        .button-group td {
            padding: 7px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            min-width: 140px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .btn-approve {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
        }
        .btn-review {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        /* Footer */
        .email-footer {
            background: #2c3e50;
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }
        .company-info {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .company-tagline {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 20px;
        }
        .contact-info {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.8;
        }
        .footer-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 20px 0;
        }
        .copyright {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 15px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px !important;
                border-radius: 8px !important;
            }
            .email-content, .email-header, .email-footer {
                padding: 20px !important;
            }
            .info-grid td {
                width: 100% !important;
                display: block !important;
            }
            .button-group td {
                display: block !important;
                width: 100% !important;
            }
            .btn {
                width: 100% !important;
            }
            .product-table {
                font-size: 12px !important;
            }
            .product-table th, .product-table td {
                padding: 8px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="header-content">
                <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}" class="company-logo">
                <h1 class="email-title">Requisition Approval Request</h1>
                <p class="email-subtitle">Sales & Marketing - Packaging Replacement Request</p>
            </div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <strong>Hello {{ $approver->name }},</strong><br>
                You have received a new requisition approval request. Please review the details below and provide your decision.
            </div>

            <!-- Basic Information -->
            <div class="info-section">
                <h3 class="section-title">📄 Request Information</h3>
                <div class="info-grid">
                    <table>
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
                                        @if($requisition->status == 'Pending')
                                            <span class="status-badge status-pending">⏳ Pending</span>
                                        @elseif($requisition->status == 'In Progress')
                                            <span class="status-badge status-progress">🔄 In Progress</span>
                                        @elseif($requisition->status == 'Approved')
                                            <span class="status-badge status-approved">✅ Approved</span>
                                        @elseif($requisition->status == 'Rejected')
                                            <span class="status-badge status-rejected">❌ Rejected</span>
                                        @else
                                            <span class="status-badge">{{ $requisition->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Information -->
            @if($requisition->customer)
            <div class="info-section">
                <h3 class="section-title">👤 Customer Information</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Customer Name</div>
                                    <div class="info-value">{{ $requisition->customer->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Customer Address</div>
                                    <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Account</div>
                                    <div class="info-value">{{ $requisition->account ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Cost Center</div>
                                    <div class="info-value">{{ $requisition->cost_center }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            <!-- Objectives -->
            @if($requisition->objectives)
            <div class="info-section">
                <h3 class="section-title">🎯 Objectives</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <p style="margin: 0; color: #2c3e50; line-height: 1.6;">{{ $requisition->objectives }}</p>
                </div>
            </div>
            @endif

            <!-- Product Details -->
            @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
            <div class="info-section">
                <h3 class="section-title">📦 Product Details</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Material Type</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Unit</th>
                                <th>QTY Required</th>
                                <th>QTY Issued</th>
                                <th>Batch Number</th>
                                <th>remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->requisitionItems as $item)
                                @php
                                    $detail = $item->itemMaster->ItemDetails->firstWhere('id', $item->item_detail_id);
                                @endphp
                                @if($detail)
                                <tr>
                                    <td>{{ $detail->material_type ?? '-' }}</td>
                                    <td>{{ $detail->item_detail_code ?? '-' }}</td>
                                    <td>{{ $detail->item_detail_name ?? '-' }}</td>
                                    <td>{{ $detail->unit ?? '-' }}</td>
                                    <td style="text-align: center; font-weight: 600;">{{ $item->quantity_required }}</td>
                                    <td style="text-align: center; font-weight: 600;">{{ $item->quantity_issued }}</td>
                                    <td style="text-align: center; font-weight: 600;">{{ $item->batch_number ?? '-' }}</td>
                                    <td style="text-align: center; font-weight: 600;">{{ $item->remarks ?? '-' }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-section">
                <h3 class="action-title">⚡ Take Action</h3>
                <p class="action-subtitle">Please review the request above and choose your action below</p>
                <div class="button-group">
                    <table>
                        <tr>
                            <td>
                                <a href="{{ $approveLink }}" class="btn btn-approve">
                                    ✅ Quick Approve
                                </a>
                            </td>
                            <td>
                                <a href="{{ $rejectLink }}" class="btn btn-reject">
                                    ❌ Quick Reject
                                </a>
                            </td>
                            <td>
                                <a href="{{ $approveWithReviewLink }}" class="btn btn-review">
                                    📝 Review with Notes
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Important Note -->
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h4 style="color: #856404; margin: 0 0 10px 0;">⚠️ Important Notice</h4>
                <ul style="color: #856404; margin: 0; padding-left: 20px;">
                    <li>This approval request is time-sensitive</li>
                    <li>Quick actions (Approve/Reject) will be processed immediately</li>
                    <li>Use "Review with Notes" if you need to add comments</li>
                    <li>Contact the requester directly if you need additional information</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-content">
                <div class="company-info">{{ config('app.name') }}</div>
                <div class="company-tagline">Sales & Marketing Department</div>
                <div class="footer-divider"></div>
                <div class="contact-info">
                    <strong>Need Help?</strong><br>
                    Contact IT Support: support@company.com<br>
                    Internal Extension: 1234
                </div>
                <div class="copyright">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                    This is an automated message, please do not reply directly to this email.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
