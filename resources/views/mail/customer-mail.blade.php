<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Customer Approval</title>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
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
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
    </style>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, th, p, a, li, span, h1, h2, h3, h4, h5, h6 {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>

<body style="height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6; color: #1f2937;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 25px 15px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="750">
                <tr>
                <td align="center" valign="top" width="750">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="border-collapse: separate; max-width: 800px; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    <tr>
                        <td bgcolor="#1e3a8a" style="background-color: #1e3a8a; padding: 30px 40px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td align="left" valign="middle" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <h1 class="header-text" style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 28px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #ffffff; line-height: 1.2;">
                                            Customer Approval
                                        </h1>
                                        <p style="margin: 8px 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #bfdbfe; line-height: 1.4;">
                                            Request #{{ $customer->id }} &bull; {{ date('d M Y') }}
                                        </p>
                                    </td>
                                    <td align="right" valign="middle" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td bgcolor="#2e4ca0" style="background-color: #2e4ca0; padding: 8px 16px; border-radius: 4px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-align: center;">
                                                    {{ strtoupper($customer->status_approval ?? 'PENDING') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="mobile-padding" style="padding: 40px; background-color: #ffffff; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 35px; background-color: #eff6ff; border-left: 6px solid #3b82f6; border-radius: 0 4px 4px 0; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 20px; font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #1f2937; line-height: 1.6;">
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
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 35px 0 20px;">
                                🏢 General Information
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        Customer Name
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-weight: bold; font-size: 18px; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Code / Sort
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->code ?? '-(Pending)-' }} / {{ $customer->sort_name ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Group / Class
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->customer_type ?? '-' }} /
                                        {{ $customer->accountGroup->name_account_group ?? $customer->account_group }} /
                                        {{ $customer->customerClass->name_class ?? $customer->customer_class }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        Full Address
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->address1 }}<br>
                                        @if($customer->address2) {{ $customer->address2 }}<br> @endif
                                        @if($customer->address3) {{ $customer->address3 }}<br> @endif
                                        {{ $customer->city }}, {{ $customer->postal_code }}<br>
                                        {{ $customer->country }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Email / Area
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->email }} / {{ $customer->area }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Requester
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->user->name ?? $customer->created_by }}
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 35px 0 20px;">
                                💰 Financial & Tax Terms
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Credit Limit
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #15803d; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">
                                        IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}
                                        @if(($customer->bank_garansi === 'YA' || strtoupper($customer->term_of_payment) === 'CBD') && $customer->approved_credit_limit)
                                            <br><span style="font-size: 14px; color: #166534;">Apprv: IDR {{ number_format((float)$customer->approved_credit_limit, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        TOP / Lead Time
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->term_of_payment }}
                                        <span style="color: #9ca3af; margin: 0 8px;">|</span>
                                        {{ $customer->lead_time ?? 0 }} Days
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Output Tax
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->output_tax }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Bank Garansi / Currency
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->bank_garansi }} / {{ $customer->ccar }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        NPWP
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->npwp }} <br>
                                        <span style="font-size: 13px; color: #6b7280;">Date: {{ $customer->tanggal_npwp ? \Carbon\Carbon::parse($customer->tanggal_npwp)->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        NPPKP
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->nppkp ?? '-' }} <br>
                                        <span style="font-size: 13px; color: #6b7280;">Date: {{ $customer->tanggal_nppkp ? \Carbon\Carbon::parse($customer->tanggal_nppkp)->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 35px 0 20px;">
                                🚚 Shipping & Billing
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        Shipping To
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                        <strong>{{ $customer->shipping_to_name }}</strong><br>
                                        {{ $customer->shipping_to_address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        Billing (Penagihan)
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                        <strong>CP: {{ $customer->penagihan_nama_kontak }}</strong> ({{ $customer->penagihan_telepon }})<br>
                                        {{ $customer->penagihan_address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; vertical-align: top; font-family: Arial, Helvetica, sans-serif;">
                                        Correspondence
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->surat_menyurat_address ?? '-' }}
                                    </td>
                                </tr>
                            </table>

                            <p class="section-title" style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 35px 0 20px;">
                                👥 Key Personnel
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td width="35%" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Purchasing Mgr
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->purchasing_manager_name }} <br>
                                        <span style="font-size: 13px; color: #6b7280;">{{ $customer->purchasing_manager_email }}</span> | <span style="font-size: 13px; color: #6b7280;">{{ $customer->purchasing_manager_telepon }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Finance Mgr
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->finance_manager_name }} <br>
                                        <span style="font-size: 13px; color: #6b7280;">{{ $customer->finance_manager_email }}</span> | <span style="font-size: 13px; color: #6b7280;">{{ $customer->finance_manager_telepon }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
                                        Tax Contact
                                    </td>
                                    <td class="data-text" style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                                        {{ $customer->tax_contact_name }} <br>
                                        <span style="font-size: 13px; color: #6b7280;">{{ $customer->tax_contact_email }} | {{ $customer->tax_contact_phone }}</span>
                                    </td>
                                </tr>
                            </table>

                            @if($customer->items && $customer->items->count() > 0)
                            <p class="section-title" style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; border-bottom: 3px solid #e5e7eb; padding-bottom: 10px; margin: 35px 0 20px;">
                                📦 Calculation Items
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <thead>
                                    <tr>
                                        <th align="left" bgcolor="#f9fafb" style="padding: 10px; border: 1px solid #e5e7eb; color: #374151; font-family: Arial, Helvetica, sans-serif;">Product</th>
                                        <th align="right" bgcolor="#f9fafb" style="padding: 10px; border: 1px solid #e5e7eb; color: #374151; font-family: Arial, Helvetica, sans-serif;">Qty</th>
                                        <th align="right" bgcolor="#f9fafb" style="padding: 10px; border: 1px solid #e5e7eb; color: #374151; font-family: Arial, Helvetica, sans-serif;">Price</th>
                                        <th align="right" bgcolor="#f9fafb" style="padding: 10px; border: 1px solid #e5e7eb; color: #374151; font-family: Arial, Helvetica, sans-serif;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->items as $item)
                                    <tr>
                                        <td style="padding: 10px; border: 1px solid #e5e7eb; color: #111827; font-family: Arial, Helvetica, sans-serif;">
                                            {{ $item->item_name }}
                                        </td>
                                        <td align="right" style="padding: 10px; border: 1px solid #e5e7eb; color: #111827; font-family: Arial, Helvetica, sans-serif;">
                                            {{ number_format($item->quantity) }}
                                        </td>
                                        <td align="right" style="padding: 10px; border: 1px solid #e5e7eb; color: #111827; font-family: Arial, Helvetica, sans-serif;">
                                            {{ number_format($item->price) }}
                                        </td>
                                        <td align="right" style="padding: 10px; border: 1px solid #e5e7eb; color: #111827; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">
                                            {{ number_format($item->quantity * $item->price) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            @if(isset($mail_type) && $mail_type == 'approval' && !empty($token))
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 40px; background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 25px; text-align: center; font-family: Arial, Helvetica, sans-serif;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 25px; border-collapse: separate;">
                                            <tr>
                                                <td style="padding: 16px 20px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #475569; text-align: left; line-height: 1.6;">
                                                    <strong>Panduan Keputusan:</strong><br>
                                                    <span style="color: #2563eb;">📝 <strong>Review Customer:</strong></span> Setujui dengan catatan. <span style="color: #d97706;">(Khusus Manager Finance & Dept Head Finance: Dapat mengubah TOP & Credit Limit)</span>.
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="margin: 0 0 20px; font-family: Arial, Helvetica, sans-serif; font-size: 17px; font-weight: bold; color: #334155;">
                                            Silahkan pilih keputusan Anda:
                                        </p>

                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                            <tr>
                                                <td align="center" bgcolor="#3b82f6" style="border-radius: 6px; background-color: #3b82f6;">
                                                    <a href="{{ route('customers.view_approval', ['token' => $token, 'pre_action' => 'review']) }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 15px 30px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #3b82f6; border-radius: 6px; line-height: 1.2;">
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
                                </tr>
                            </table>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#1f2937" style="background-color: #1f2937; padding: 25px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #9ca3af; line-height: 1.5; border-top: 1px solid #374151;">
                            &copy; {{ date('Y') }} Automated Approval System.<br>
                            PT. Sinar Meadow International Indonesia
                        </td>
                    </tr>
                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
