<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Approval Request</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f8; color: #333; }
        .email-wrapper { width: 100%; background-color: #f4f6f8; padding: 40px 0; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .email-header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 30px; text-align: center; color: #ffffff; }
        .email-header h1 { margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
        .email-body { padding: 35px; }
        .greeting { font-size: 16px; color: #4b5563; margin-bottom: 25px; line-height: 1.6; }
        .summary-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .data-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #cbd5e1; }
        .data-row:last-child { border-bottom: none; }
        .label { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .value { font-size: 14px; color: #1e293b; font-weight: 600; text-align: right; }
        .highlight-value { color: #1e3a8a; font-weight: 700; }

        /* BUTTONS */
        .action-container { text-align: center; margin-top: 20px; }
        .btn { display: block; width: 100%; max-width: 250px; margin: 10px auto; padding: 12px 0; text-align: center; color: #ffffff !important; text-decoration: none; font-weight: bold; border-radius: 50px; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        .btn-approve { background-color: #10b981; border: 1px solid #059669; } /* Green */
        .btn-review { background-color: #3b82f6; border: 1px solid #2563eb; } /* Blue */
        .btn-reject { background-color: #ef4444; border: 1px solid #dc2626; } /* Red */

        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .note-text { font-size: 12px; color: #9ca3af; margin-top: 20px; text-align: center; }

        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>Customer Approval</h1>
                <p>New Request Notification</p>
            </div>

            <div class="email-body">
                <div class="greeting">
                    <strong>Dear {{ $approver_name ?? 'Approver' }},</strong><br>
                    Permintaan pembuatan Customer baru memerlukan keputusan Anda.
                </div>

                <div class="summary-card">
                    <div class="data-row">
                        <span class="label">Customer Name</span>
                        <span class="value highlight-value">{{ $customer->name ?? '-' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Term of Payment</span>
                        <span class="value">{{ $customer->term_of_payment ?? '-' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Credit Limit</span>
                        <span class="value highlight-value">IDR {{ number_format($customer->credit_limit ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Requested By</span>
                        <span class="value">{{ $customer->user->name ?? $customer->created_by }}</span>
                    </div>
                </div>

                <div class="action-container">
                    @if(isset($mail_type) && $mail_type == 'approval' && !empty($token))
                        <p style="margin-bottom: 15px; color: #64748b; font-size: 14px;">Pilih keputusan Anda untuk melanjutkan ke aplikasi:</p>

                        <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'approve']) }}" class="btn btn-approve">
                            ✅ Approve Request
                        </a>
                        <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'review']) }}" class="btn btn-review">
                            📝 Review with Notes
                        </a>
                        <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'reject']) }}" class="btn btn-reject">
                            ⛔ Reject Request
                        </a>
                    @else
                        <div style="color: #16a34a; font-weight: bold; padding: 15px; background: #dcfce7; border-radius: 6px;">
                            ✅ Notification Only (No Action Required)
                        </div>
                    @endif
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia.<br>
                Automated System Notification.
            </div>
        </div>
    </div>
</body>
</html>
