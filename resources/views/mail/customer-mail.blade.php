<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Approval</title>
    <style type="text/css">
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
        }

        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .mobile-padding {
                padding: 20px !important;
            }
        }

    </style>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 700px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);"
                    class="email-container">

                    <tr>
                        <td bgcolor="#1e3a8a" style="padding: 20px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left" valign="middle">
                                        <h1
                                            style="margin: 0; font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #ffffff;">
                                            Customer Approval</h1>
                                        <p style="margin: 5px 0 0; font-size: 11px; opacity: 0.8; color: #ffffff;">
                                            Request #{{ $customer->id }} &bull; {{ date('d M Y') }}</p>
                                    </td>
                                    <td align="right" valign="middle">
                                        <span
                                            style="background-color: rgba(255, 255, 255, 0.2); padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; color: #ffffff;">
                                            {{ strtoupper($customer->status_approval ?? 'PENDING') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px;" class="mobile-padding">

                            <div
                                style="font-size: 14px; margin-bottom: 20px; border-left: 4px solid #3b82f6; padding: 15px; background-color: #eff6ff; border-radius: 0 4px 4px 0; color: #1f2937;">
                                <strong>Hi {{ $approver_name ?? 'User' }},</strong><br>
                                @if($mail_type == 'approval')
                                Mohon tinjau data customer baru berikut.
                                @elseif($mail_type == 'completed')
                                Proses Approval Selesai. Data Customer telah aktif.
                                @elseif($mail_type == 'rejected')
                                Permintaan pembuatan Customer ditolak.
                                @endif
                            </div>

                            <p
                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 25px 0 10px;">
                                🏢 General Information</p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="font-size: 12px; border-collapse: collapse;">
                                <tr>
                                    <td width="35%"
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Customer Name</td>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827; font-weight: bold;">
                                        {{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Code / Sort</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->code ?? '-(Pending)-' }} / {{ $customer->sort_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Group / Class</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->accountGroup->name_account_group ?? $customer->account_group }} /
                                        {{ $customer->customerClass->name_class ?? $customer->customer_class }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600; vertical-align: top;">
                                        Full Address</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->address1 }}<br>
                                        @if($customer->address2) {{ $customer->address2 }}<br> @endif
                                        @if($customer->address3) {{ $customer->address3 }}<br> @endif
                                        {{ $customer->city }}, {{ $customer->postal_code }}<br>
                                        {{ $customer->country }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Email / Area</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->email }} / {{ $customer->area }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Requester</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->user->name ?? $customer->created_by }}</td>
                                </tr>
                            </table>

                            <p
                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 25px 0 10px;">
                                💰 Financial & Tax Terms</p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="font-size: 12px; border-collapse: collapse;">
                                <tr>
                                    <td width="35%"
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Credit Limit</td>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #15803d; font-weight: bold;">
                                        IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        TOP / Lead Time</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->term_of_payment }}
                                        <span style="color: #9ca3af; margin: 0 5px;">|</span>
                                        {{ $customer->lead_time ?? 0 }} Days
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Output Tax</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->output_tax }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Bank Garansi / CCAR</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->bank_garansi }} / {{ $customer->ccar }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        NPWP / Date</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->npwp }} <br>
                                        <span style="font-size: 11px; color: #6b7280;">Date:
                                            {{ $customer->tanggal_npwp ? \Carbon\Carbon::parse($customer->tanggal_npwp)->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        NPPKP / Date</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->nppkp ?? '-' }} <br>
                                        <span style="font-size: 11px; color: #6b7280;">Date:
                                            {{ $customer->tanggal_nppkp ? \Carbon\Carbon::parse($customer->tanggal_nppkp)->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 25px 0 10px;">
                                🚚 Shipping & Billing</p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="font-size: 12px; border-collapse: collapse;">
                                <tr>
                                    <td width="35%"
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600; vertical-align: top;">
                                        Shipping To</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        <strong>{{ $customer->shipping_to_name }}</strong><br>
                                        {{ $customer->shipping_to_address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600; vertical-align: top;">
                                        Billing (Penagihan)</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        <strong>CP: {{ $customer->penagihan_nama_kontak }}</strong>
                                        ({{ $customer->penagihan_telepon }})<br>
                                        {{ $customer->penagihan_address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600; vertical-align: top;">
                                        Correspondence</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->surat_menyurat_address ?? '-' }}
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 25px 0 10px;">
                                👥 Key Personnel</p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="font-size: 12px; border-collapse: collapse;">
                                <tr>
                                    <td width="35%"
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Purchasing Mgr</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->purchasing_manager_name }} <br>
                                        <span
                                            style="font-size: 11px; color: #6b7280;">{{ $customer->purchasing_manager_email }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Finance Mgr</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->finance_manager_name }} <br>
                                        <span
                                            style="font-size: 11px; color: #6b7280;">{{ $customer->finance_manager_email }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; font-weight: 600;">
                                        Tax Contact</td>
                                    <td style="padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #111827;">
                                        {{ $customer->tax_contact_name }} <br>
                                        <span
                                            style="font-size: 11px; color: #6b7280;">{{ $customer->tax_contact_email }}
                                            | {{ $customer->tax_contact_phone }}</span>
                                    </td>
                                </tr>
                            </table>

                            @if($customer->items && $customer->items->count() > 0)
                            <p
                                style="font-size: 12px; font-weight: 700; color: #4b5563; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin: 25px 0 10px;">
                                📦 Calculation Items</p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="font-size: 11px; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th align="left" bgcolor="#f9fafb"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #374151;">Product
                                        </th>
                                        <th align="right" bgcolor="#f9fafb"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #374151;">Qty</th>
                                        <th align="right" bgcolor="#f9fafb"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #374151;">Price</th>
                                        <th align="right" bgcolor="#f9fafb"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #374151;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->items as $item)
                                    <tr>
                                        <td style="padding: 8px; border: 1px solid #e5e7eb; color: #111827;">
                                            {{ $item->item_name }}</td>
                                        <td align="right"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #111827;">
                                            {{ number_format($item->quantity) }}</td>
                                        <td align="right"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #111827;">
                                            {{ number_format($item->price) }}</td>
                                        <td align="right"
                                            style="padding: 8px; border: 1px solid #e5e7eb; color: #111827; font-weight: bold;">
                                            {{ number_format($item->quantity * $item->price) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            @if(isset($mail_type) && $mail_type == 'approval' && !empty($token))
                            <div
                                style="margin-top: 30px; background-color: #f8fafc; padding: 20px; border-radius: 6px; border: 1px dashed #cbd5e1; text-align: center;">

                                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                    style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 20px;">
                                    <tr>
                                        <td
                                            style="padding: 10px; font-size: 11px; color: #475569; text-align: left; line-height: 1.6;">
                                            <strong>Panduan Keputusan:</strong><br>
                                            <span style="color: #059669;">✅ <strong>Approve:</strong></span> Setujui
                                            permintaan langsung.<br>
                                            <span style="color: #2563eb;">📝 <strong>Review:</strong></span> Setujui
                                            dengan catatan. <span style="color: #d97706;">(Khusus Manager Finance & Dept
                                                Head: Dapat mengubah TOP & Credit Limit)</span>.<br>
                                            <span style="color: #dc2626;">⛔ <strong>Reject:</strong></span> Tolak
                                            permintaan (Wajib sertakan alasan).
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 0 0 15px; font-size: 12px; font-weight: bold; color: #334155;">
                                    Silahkan pilih keputusan Anda:</p>

                                <table align="center" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 0 5px;">
                                            <table border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td align="center" bgcolor="#10b981" style="border-radius: 4px;">
                                                        <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'approve']) }}"
                                                            style="display: inline-block; padding: 10px 18px; font-family: Arial, sans-serif; font-size: 12px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #10b981; border-radius: 4px;">✅
                                                            Approve not Review</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="padding: 0 5px;">
                                            <table border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td align="center" bgcolor="#3b82f6" style="border-radius: 4px;">
                                                        <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'review']) }}"
                                                            style="display: inline-block; padding: 10px 18px; font-family: Arial, sans-serif; font-size: 12px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #3b82f6; border-radius: 4px;">📝
                                                            Approve with Review</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="padding: 0 5px;">
                                            <table border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td align="center" bgcolor="#ef4444" style="border-radius: 4px;">
                                                        <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'reject']) }}"
                                                            style="display: inline-block; padding: 10px 18px; font-family: Arial, sans-serif; font-size: 12px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #ef4444; border-radius: 4px;">⛔
                                                            Reject</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#1f2937" style="padding: 20px; font-size: 11px; color: #9ca3af;">
                            &copy; {{ date('Y') }} Automated Approval System.<br>PT. Sinar Meadow International
                            Indonesia
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
