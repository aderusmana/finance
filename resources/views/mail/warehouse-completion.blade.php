<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Approval Completed</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-left: 4px solid #28a745;
        }
        
        /* Info Cards */
        .info-section {
            margin-bottom: 30px;
        }
        .section-title {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Success Alert */
        .success-alert {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            border: 2px solid #28a745;
        }
        .success-title {
            font-size: 20px;
            font-weight: 700;
            color: #155724;
            margin-bottom: 15px;
        }
        .success-subtitle {
            color: #155724;
            margin-bottom: 25px;
            font-size: 16px;
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="header-content">
                <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}" class="company-logo">
                <h1 class="email-title">Warehouse Approval Completed</h1>
                <p class="email-subtitle">Warehouse Department - Approval Successfully Completed</p>
            </div>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <strong>Hello {{ $requester->name ?? 'User' }},</strong><br>
                Great news! Your requisition approval has been successfully completed. Please review the details below.
            </div>

            <!-- Success Alert -->
            <div class="success-alert">
                <h3 class="success-title">🎉 Approval Process Complete!</h3>
                <p class="success-subtitle">Your requisition <strong>{{ $requisition->no_srs }}</strong> has successfully completed all warehouse approval processes and is now fully approved.</p>
            </div>
            
            <!-- Basic Information -->
            <div class="info-section">
                <h3 class="section-title">📄 Request Information</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">No. SRS</div>
                                    <div class="info-value">{{ $requisition->no_srs }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Category</div>
                                    <div class="info-value">{{ $requisition->category }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Final Status</div>
                                    <div class="info-value">
                                        <span class="status-badge status-approved">Approved</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Request Date</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}
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
                                    <div class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Customer Address</div>
                                    <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            <!-- Objectives -->
            @if($requisition->reason_for_replacement)
            <div class="info-section">
                <h3 class="section-title">🎯 Reason for Replacement</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <p style="margin: 0; color: #2c3e50; line-height: 1.6;">{{ $requisition->reason_for_replacement }}</p>
                </div>
            </div>
            @endif
            
            <!-- Completion Details -->
            <div class="info-section">
                <h3 class="section-title">✅ Completion Information</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Completed Date</div>
                                    <div class="info-value">{{ $formattedCompletionDate }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Process Type</div>
                                    <div class="info-value">Warehouse Approval Process</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            @if($completedBy)
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Final Approver</div>
                                    <div class="info-value">{{ $completedBy->name }}</div>
                                </div>
                            </td>
                            @endif
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Current Route</div>
                                    <div class="info-value">{{ $requisition->route_to ?? 'Completed' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Timeline Summary -->
            <div class="info-section">
                <h3 class="section-title">⏱️ Process Summary</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Request Date</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Completion Date</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($completionDate)->format('d M Y') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="info-item">
                                    <div class="info-label">Total Process Time</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($requisition->request_date)->diffInDays(\Carbon\Carbon::parse($completionDate)) }} days
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-content">
                <div class="company-info">{{ config('app.name') }}</div>
                <div class="company-tagline">Warehouse Department</div>
                <div class="footer-divider"></div>
                <div class="contact-info">
                    <strong>Need Help?</strong><br>
                    Contact IT Support:<br>
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