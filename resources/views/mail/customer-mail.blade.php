<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Approval</title>
    <style type="text/css">
        /* MEDIA QUERIES */
        @media screen and (max-width: 850px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .mobile-padding {
                padding: 20px !important;
            }
            /* Penyesuaian font di mobile */
            .header-text {
                font-size: 24px !important;
            }
            .section-title {
                font-size: 20px !important;
            }
            .data-text {
                font-size: 16px !important;
            }
        }

        /* Client-specific resets */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        img { -ms-interpolation-mode: bicubic; }
    </style>
</head>

<body style="height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 20px 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="border-collapse: collapse; max-width: 850px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    <tr>
                        <td bgcolor="#1e3a8a" style="padding: 30px 40px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td align="left" valign="middle" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <h1 class="header-text" style="margin: 0; font-size: 30px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #ffffff;">
                                            Customer Approval
                                        </h1>
                                        <p style="margin: 8px 0 0; font-size: 16px; opacity: 0.9; color: #ffffff;">
                                            Request #{{ $customer->id }} &bull; {{ date('d M Y') }}
                                        </p>
                                    </td>
                                    <td align="right" valign="middle" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <span style="background-color: rgba(255, 255, 255, 0.2); padding: 10px 18px; border-radius: 4px; font-size: 15px; font-weight: bold; color: #ffffff;">
                                            {{ strtoupper($customer->status_approval ?? 'PENDING') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="mobile-padding" style="padding: 40px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <div style="font-size: 18px; margin-bottom: 35px; border-left: 6px solid #3b82f6; padding: 20px; background-color: #eff6ff; border-radius: 0 4px 4px 0; color: #1f2937; line-height: 1.6;">
                                <strong>Dear {{ $approver_name ?? 'User' }},</strong><br>
                                @if(isset($mail_type) && $mail_type == 'approval')
                                    Mohon tinjau data customer baru berikut.
                                @elseif(isset($mail_type) && $mail_type == 'completed')
                                    Proses Approval Selesai. Data Customer telah aktif.
                                @elseif(isset($mail_type) && $mail_type == 'rejected')
                                    Permintaan pembuatan Customer ditolak.
                                @else
                                    Mohon tinjau data customer berikut.
                                @endif
                            </div>

                            <p class="section-title" style="font-size: 22px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 40px 0 20px;">
                                🏢 General Information
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 18px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        Customer Name
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-weight: bold; font-size: 20px;">
                                        {{ $customer->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Code / Sort
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->code ?? '-(Pending)-' }} / {{ $customer->sort_name ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Group / Class
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->accountGroup->name_account_group ?? $customer->account_group }} /
                                        {{ $customer->customerClass->name_class ?? $customer->customer_class }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        Full Address
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6;">
                                        {{ $customer->address1 }}<br>
                                        @if($customer->address2) {{ $customer->address2 }}<br> @endif
                                        @if($customer->address3) {{ $customer->address3 }}<br> @endif
                                        {{ $customer->city }}, {{ $customer->postal_code }}<br>
                                        {{ $customer->country }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Email / Area
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->email }} / {{ $customer->area }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Requester
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->user->name ?? $customer->created_by }}
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-size: 22px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 40px 0 20px;">
                                💰 Financial & Tax Terms
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 18px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Credit Limit
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #15803d; font-weight: bold;">
                                        IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}
                                        @if(($customer->bank_garansi === 'YA' || strtoupper($customer->term_of_payment) === 'CBD') && $customer->approved_credit_limit)
                                            <br><span style="font-size: 14px; color: #166534;">Apprv: IDR {{ number_format((float)$customer->approved_credit_limit, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        TOP / Lead Time
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->term_of_payment }}
                                        <span style="color: #9ca3af; margin: 0 8px;">|</span>
                                        {{ $customer->lead_time ?? 0 }} Days
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Output Tax
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->output_tax }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Bank Garansi / CCAR
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->bank_garansi }} / {{ $customer->ccar }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        NPWP
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->npwp }} <br>
                                        <span style="font-size: 14px; color: #6b7280;">Date: {{ $customer->tanggal_npwp ? \Carbon\Carbon::parse($customer->tanggal_npwp)->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        NPPKP
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->nppkp ?? '-' }} <br>
                                        <span style="font-size: 14px; color: #6b7280;">Date: {{ $customer->tanggal_nppkp ? \Carbon\Carbon::parse($customer->tanggal_nppkp)->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-size: 22px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 40px 0 20px;">
                                🚚 Shipping & Billing
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 18px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        Shipping To
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6;">
                                        <strong>{{ $customer->shipping_to_name }}</strong><br>
                                        {{ $customer->shipping_to_address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        Billing (Penagihan)
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6;">
                                        <strong>CP: {{ $customer->penagihan_nama_kontak }}</strong> ({{ $customer->penagihan_telepon }})<br>
                                        {{ $customer->penagihan_address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top;">
                                        Correspondence
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6;">
                                        {{ $customer->surat_menyurat_address ?? '-' }}
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-size: 22px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 40px 0 20px;">
                                👥 Key Personnel
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 18px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Purchasing Mgr
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->purchasing_manager_name }} <br>
                                        <span style="font-size: 14px; color: #6b7280;">{{ $customer->purchasing_manager_email }}</span> | <span style="font-size: 14px; color: #6b7280;">{{ $customer->purchasing_manager_telepon }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Finance Mgr
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->finance_manager_name }} <br>
                                        <span style="font-size: 14px; color: #6b7280;">{{ $customer->finance_manager_email }}</span> | <span style="font-size: 14px; color: #6b7280;">{{ $customer->finance_manager_telepon }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600;">
                                        Tax Contact
                                    </td>
                                    <td class="data-text" style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; color: #000000;">
                                        {{ $customer->tax_contact_name }} <br>
                                        <span style="font-size: 14px; color: #6b7280;">{{ $customer->tax_contact_email }} | {{ $customer->tax_contact_phone }}</span>
                                    </td>
                                </tr>
                            </table>

                            @if($customer->items && $customer->items->count() > 0)
                            <p class="section-title" style="font-size: 22px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 40px 0 20px;">
                                📦 Calculation Items
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 16px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <thead>
                                    <tr>
                                        <th align="left" bgcolor="#f9fafb" style="padding: 12px; border: 1px solid #e5e7eb; color: #374151;">Product</th>
                                        <th align="right" bgcolor="#f9fafb" style="padding: 12px; border: 1px solid #e5e7eb; color: #374151;">Qty</th>
                                        <th align="right" bgcolor="#f9fafb" style="padding: 12px; border: 1px solid #e5e7eb; color: #374151;">Price</th>
                                        <th align="right" bgcolor="#f9fafb" style="padding: 12px; border: 1px solid #e5e7eb; color: #374151;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->items as $item)
                                    <tr>
                                        <td style="padding: 12px; border: 1px solid #e5e7eb; color: #111827;">
                                            {{ $item->item_name }}
                                        </td>
                                        <td align="right" style="padding: 12px; border: 1px solid #e5e7eb; color: #111827;">
                                            {{ number_format($item->quantity) }}
                                        </td>
                                        <td align="right" style="padding: 12px; border: 1px solid #e5e7eb; color: #111827;">
                                            {{ number_format($item->price) }}
                                        </td>
                                        <td align="right" style="padding: 12px; border: 1px solid #e5e7eb; color: #111827; font-weight: bold;">
                                            {{ number_format($item->quantity * $item->price) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            @if(isset($mail_type) && $mail_type == 'approval' && !empty($token))
                            <div style="margin-top: 50px; background-color: #f8fafc; padding: 30px; border-radius: 8px; border: 2px dashed #cbd5e1; text-align: center;">

                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 30px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                    <tr>
                                        <td style="padding: 20px; font-size: 16px; color: #475569; text-align: left; line-height: 1.6; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                            <strong>Panduan Keputusan:</strong><br>
                                            {{-- <span style="color: #059669;">✅ <strong>Approve not Review:</strong></span> Setujui permintaan langsung.<br> --}}
                                            <span style="color: #2563eb;">📝 <strong>Review Customer:</strong></span> Setujui dengan catatan. <span style="color: #d97706;">(Khusus Manager Finance & Dept Head Finaance: Dapat mengubah TOP & Credit Limit)</span>.<br>
                                            {{-- <span style="color: #dc2626;">⛔ <strong>Reject:</strong></span> Tolak permintaan (Wajib sertakan alasan). --}}
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 0 0 25px; font-size: 18px; font-weight: bold; color: #334155;">
                                    Silahkan pilih keputusan Anda:
                                </p>

                                <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                    <tr>
                                            {{-- @if(empty($is_it))
                                            <td style="padding: 0 10px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                    <tr>
                                                        <td align="center" bgcolor="#10b981" style="border-radius: 6px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                            <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'approve']) }}" style="display: inline-block; padding: 16px 28px; font-family: Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #10b981; border-radius: 6px;">
                                                                ✅ Approve not Review
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            @endif --}}

                                        <td style="padding: 0 10px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                <tr>
                                                    <td align="center" bgcolor="#3b82f6" style="border-radius: 6px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                         <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'review']) }}" style="display: inline-block; padding: 16px 28px; font-family: Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #3b82f6; border-radius: 6px;">
                                                            @if(!empty($is_it))
                                                                ⌨️ Input Customer Code
                                                            @else
                                                                📝 Review Customer
                                                            @endif
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>

                                        {{-- @if(empty($is_it))
                                        <td style="padding: 0 10px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                <tr>
                                                    <td align="center" bgcolor="#ef4444" style="border-radius: 6px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                         <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'reject']) }}" style="display: inline-block; padding: 16px 28px; font-family: Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #ef4444; border-radius: 6px;">
                                                            ⛔ Reject
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        @endif --}}
                                    </tr>
                                </table>
                            </div>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#1f2937" style="padding: 30px; font-size: 15px; color: #9ca3af; mso-table-lspace: 0pt; mso-table-rspace: 0pt; line-height: 1.5;">
                            &copy; {{ date('Y') }} Automated Approval System.<br>
                            PT. Sinar Meadow International Indonesia
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
