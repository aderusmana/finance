<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Proof Required</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f7fc;
            line-height: 1.6;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        .email-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            padding: 40px 30px;
            text-align: center;
            border-radius: 0;
        }
        
        .warning-icon {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .warning-icon svg {
            width: 40px;
            height: 40px;
            fill: #ffffff;
        }
        
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin: 10px 0 0 0;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .alert-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.1);
        }
        
        .alert-box h2 {
            color: #856404;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
        }
        
        .alert-box h2::before {
            content: "⚠️";
            margin-right: 10px;
            font-size: 20px;
        }
        
        .alert-box p {
            color: #856404;
            margin: 0;
            font-size: 15px;
        }
        
        .requisition-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 2px solid #2196f3;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);
        }
        
        .requisition-info h3 {
            color: #1565c0;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
        }
        
        .requisition-info h3::before {
            content: "📋";
            margin-right: 10px;
            font-size: 20px;
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid rgba(33, 150, 243, 0.2);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #1565c0;
            min-width: 150px;
            font-size: 14px;
        }
        
        .info-value {
            color: #0d47a1;
            font-size: 14px;
            font-weight: 500;
        }
        
        .action-required {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 2px dashed #6c757d;
        }
        
        .action-required h3 {
            color: #495057;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
        }
        
        .action-required h3::before {
            content: "✅";
            margin-right: 10px;
            font-size: 20px;
        }
        
        .action-steps {
            list-style: none;
            padding: 0;
            margin: 0;
            counter-reset: step-counter;
        }
        
        .action-steps li {
            position: relative;
            padding-left: 45px;
            margin-bottom: 15px;
            color: #495057;
            font-size: 15px;
            counter-increment: step-counter;
        }
        
        .action-steps li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .primary-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .primary-button:hover {
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            transform: translateY(-2px);
        }
        
        .note-box {
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 15px 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .note-box p {
            margin: 0;
            color: #495057;
            font-size: 14px;
        }
        
        .note-box strong {
            color: #0c5460;
        }
        
        .email-footer {
            background: #2c3e50;
            color: #ffffff;
            padding: 30px;
            text-align: center;
            border-radius: 0;
        }
        
        .email-footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #ecf0f1;
        }
        
        .email-footer .company-name {
            font-weight: 700;
            color: #ffffff;
            font-size: 16px;
        }
        
        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #dee2e6, transparent);
            margin: 30px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
            
            .requisition-info,
            .action-required {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="warning-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                </svg>
            </div>
            <h1>Payment Proof Required</h1>
            <p>Action Required for Your Requisition</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <!-- Alert Box -->
            <div class="alert-box">
                <h2>Important Notice</h2>
                <p>Your requisition has been reviewed and requires payment proof to proceed further.</p>
            </div>
            
            <!-- Requisition Information -->
            <div class="requisition-info">
                <h3>Requisition Details</h3>
                <div class="info-row">
                    <span class="info-label">Requisition No:</span>
                    <span class="info-value">{{ $requisition->no_srs }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Request Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Status:</span>
                    <span class="info-value" style="color: #dc3545; font-weight: 700;">Payment Proof Required</span>
                </div>
            </div>
            
            <!-- Action Required Section -->
            <div class="action-required">
                <h3>What You Need To Do</h3>
                <ul class="action-steps">
                    <li>Login to the Requisition Management System</li>
                    <li>Navigate to your requisition <strong>{{ $requisition->no_srs }}</strong></li>
                    <li>Upload your payment proof document (PDF, JPG, or PNG format)</li>
                    <li>Submit the document for review</li>
                </ul>
            </div>
            
            <!-- Button -->
            <div class="button-container">
                <a href="{{ url('/complain') }}" class="primary-button">
                    Upload Payment Proof Now
                </a>
            </div>
            
            <div class="divider"></div>
            
            <!-- Note -->
            <div class="note-box">
                <p>
                    <strong>📌 Important:</strong> Your requisition will remain on hold until the payment proof is provided and verified by our team.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p class="company-name">{{ config('app.name') }}</p>
            <p>Requisition Management System</p>
            <p style="margin-top: 15px; font-size: 12px; color: #95a5a6;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
