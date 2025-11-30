<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Rejected</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
            border-left: 4px solid #dc3545;
        }
        
        /* Info Cards */
        .info-section {
            margin-bottom: 30px;
        }
        .section-title {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1b0b7;
        }
        
        /* Rejection Alert */
        .rejection-alert {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            border: 2px solid #dc3545;
        }
        .rejection-title {
            font-size: 20px;
            font-weight: 700;
            color: #721c24;
            margin-bottom: 15px;
        }
        .rejection-subtitle {
            color: #721c24;
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
                <h1 class="email-title">Requisition Rejected</h1>
                <p class="email-subtitle">{{ $rejectionType === 'warehouse' ? 'Warehouse Approval' : 'Standard Approval' }} - Request Rejected</p>
            </div>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <strong>Hello {{ $requester->name ?? 'User' }},</strong><br>
                Your requisition approval request has been rejected. Please review the details below and take appropriate action.
            </div>

            <!-- Rejection Alert -->
            <div class="rejection-alert">
                <h3 class="rejection-title">❌ Requisition Rejected</h3>
                <p class="rejection-subtitle">Your requisition <strong>{{ $requisition->no_srs }}</strong> has been rejected during the {{ $rejectionType === 'warehouse' ? 'warehouse approval' : 'approval' }} process.</p>
            </div>
            
            <!-- Basic Information -->
            <div class="info-section">
                <h3 class="section-title">� Request Information</h3>
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
                                    <div class="info-label">Current Status</div>
                                    <div class="info-value">
                                        <span class="status-badge status-rejected">Rejected</span>
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
            
            <!-- Rejection Details -->
            <div class="info-section">
                <h3 class="section-title">🚫 Rejection Information</h3>
                <div class="info-grid">
                    <table>
                        <tr>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Rejected By</div>
                                    <div class="info-value">{{ $rejectedBy->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="info-item">
                                    <div class="info-label">Rejection Date</div>
                                    <div class="info-value">{{ $formattedRejectionDate }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="info-item">
                                    <div class="info-label">Rejection Type</div>
                                    <div class="info-value">{{ $rejectionType === 'warehouse' ? 'Warehouse Approval' : 'Manager Approval' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($rejectionReason)
            <div class="info-section">
                <h3 class="section-title">💬 Reason for Rejection</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <p style="margin: 0; color: #2c3e50; line-height: 1.6; font-style: italic; background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef;">{{ $rejectionReason }}</p>
                </div>
            </div>
            @endif

            <!-- Next Steps -->
            <div class="info-section">
                <h3 class="section-title">📢 What happens next?</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #e9ecef; border-top: none;">
                    <p style="margin: 0 0 15px 0; color: #2c3e50; line-height: 1.6;">
                        Your requisition has been rejected and the process has been stopped. 
                        @if($rejectionType === 'warehouse')
                            Please review the warehouse requirements and resubmit if necessary.
                        @else
                            Please review the requirements and resubmit if necessary.
                        @endif
                    </p>
                    <p style="margin: 0; color: #2c3e50; line-height: 1.6; font-weight: 600;">
                        If you have any questions about this rejection, please contact the person who rejected your request or your supervisor.
                    </p>
                </div>
            </div>

            <!-- Important Note -->
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h4 style="color: #721c24; margin: 0 0 10px 0;">⚠️ Important Notice</h4>
                <ul style="color: #721c24; margin: 0; padding-left: 20px;">
                    <li>This requisition has been permanently rejected</li>
                    <li>You will need to create a new requisition if you wish to proceed</li>
                    <li>Please address the rejection reasons before resubmitting</li>
                    <li>Contact the rejector directly if clarification is needed</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-content">
                <div class="company-info">{{ config('app.name') }}</div>
                <div class="company-tagline">Requisition Management System</div>
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