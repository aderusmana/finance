<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Bank Garansi Notification</title>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    </style>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, th, p, a, li, span, h1, h2, h3, h4, h5, h6 {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', Arial, sans-serif; color: #334155;">

    @php
        $isUploadContext = isset($submission);
        $targetBg = null;
        $rec = null;
        $bankDetails = [];
        $token = '#';
        $customer = null;
        $candidateBgs = collect();

        $isAdminNotif = isset($isUploadAdminNotif) && $isUploadAdminNotif;

        // 1. CEK KONTEKS & INISIALISASI VARIABEL
        if ($isAdminNotif) {
            // Konteks: Email Notifikasi ke Admin (Super Admin / RTM) saat Upload
            $rec = isset($submission) && $submission->recommendation ? $submission->recommendation : ($recommendation ?? null);
            $customer = $rec ? $rec->customer : null;

            $pageTitle = 'Document Uploaded by Customer';
            $refNumber = isset($submission) ? 'Form Code: #' . $submission->form_code : ('Ref ID: #' . ($rec ? substr($rec->id, 0, 8) : '-'));

            // Setup Tombol & Link untuk Admin RTM
            $actionUrl = route('bg-submissions.index');
            $downloadUrl = (isset($submission) && $submission->token) 
                ? route('customer.portal.download-submission-pdf', ['token' => $submission->token]) 
                : '#';
            $btnColor = '#3b82f6';
            $btnText = 'Review Submission & Complete BG';
        } elseif ($isUploadContext) {
            // Konteks: Email Konfirmasi Upload (Submission) ke Customer
            $rec = $submission->recommendation;
            $token = $submission->token;
            $customer = $rec ? $rec->customer : null;

            $pageTitle = 'Confirmation of BG Application';
            $refNumber = 'Form Code: #' . $submission->form_code;

            // Setup Tombol & Link untuk Upload
            $actionUrl = route('customer.portal.upload-form', ['token' => $token]);
            $downloadUrl = route('customer.portal.download-pdf', ['token' => $token]);
            $btnColor = '#2563eb';
            $btnText = '⬆️ Upload Signed Documents';

        } elseif (isset($recommendation)) {
            // Konteks: Email Notifikasi Awal (CustomerFillFormNotification)
            $rec = $recommendation;
            $token = $rec->token;
            $customer = $rec->customer;

            $pageTitle = 'Confirmation of BG Application';
            $refNumber = 'Ref ID: #' . substr($rec->id, 0, 8);

            // Setup Tombol & Link untuk Input
            $actionUrl = route('customer.portal.input-form', ['token' => $token]);
            $downloadUrl = '#'; // Belum ada download di tahap ini
            $btnColor = '#10b981'; // Hijau
            $btnText = 'Fill in the Bank Guarantee Form';

        } else {
            // Fallback Error Prevention
            $pageTitle = 'System Notification';
            $refNumber = '-';
            $actionUrl = '#';
            $downloadUrl = '#';
            $btnColor = '#64748b';
            $btnText = 'Invalid Link';
        }

        // Ambil Data Seluruh Bank Garansi jika ada submission
        if (isset($submission) && $submission && $rec) {
            $submissionTime = \Carbon\Carbon::parse($submission->created_at);
            $startTime = $submissionTime->copy()->subMinutes(5);
            $endTime   = $submissionTime->copy()->addMinutes(5);
            $customerId = $rec->customer_id;

            if ($customerId) {
                $candidateBgs = \App\Models\BG\BankGaransi::where('customer_id', $customerId)
                    ->whereBetween('created_at', [$startTime, $endTime])
                    ->with('details')
                    ->orderBy('id', 'asc')
                    ->get();

                if ($candidateBgs->isEmpty()) {
                    $candidateBgs = \App\Models\BG\BankGaransi::where('customer_id', $customerId)
                        ->where('status', 'draft')
                        ->with('details')
                        ->latest()
                        ->get();
                }

                if ($candidateBgs->isEmpty()) {
                    $candidateBgs = \App\Models\BG\BankGaransi::where('customer_id', $customerId)
                        ->with('details')
                        ->latest()
                        ->take(3)
                        ->get();
                }

                $targetBg = $candidateBgs->first();
            }
        }
    @endphp

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7f6; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 30px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="650">
                <tr>
                <td align="center" valign="top" width="650">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 650px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    {{-- HEADER --}}
                    <tr>
                        <td bgcolor="#1e3a8a" style="background-color: #1e3a8a; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 35px 30px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; color: #ffffff; line-height: 1.3;">{{ $pageTitle }}</h1>
                            <p style="margin: 8px 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #bfdbfe; line-height: 1.4;">{{ $refNumber }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px; background-color: #ffffff;">

                            {{-- GREETING --}}
                            <p style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #334155; margin: 0 0 25px 0;">
                                @if($isAdminNotif)
                                    Halo <strong>Tim Admin-RTM / Sales</strong>,<br><br>
                                    Customer <strong>{{ $customer->name ?? 'Distributor' }}</strong> telah berhasil mengunggah dokumen konfirmasi Bank Garansi yang telah ditandatangani dan dicap perusahaan (Form Code: <strong>{{ isset($submission) ? $submission->form_code : '' }}</strong>).<br><br>
                                    Silakan review dokumen tersebut dan lengkapi <em>Nomor Resmi Bank Garansi</em>, <em>Tanggal Jatuh Tempo</em>, dan <em>Scan Dokumen Bank Garansi Asli</em> sebelum diteruskan untuk validasi Finance (Bu Rita).
                                @elseif($isUploadContext)
                                    Dear <strong>{{ $customer->name ?? 'Business Partner' }}</strong>,<br><br>
                                    Thank you, we have successfully received your digital form data.
                                    To legally validate this submission, we require the physical documents to be signed.
                                @else
                                    Dear <strong>{{ $customer->name ?? 'Business Partner' }}</strong>,<br><br>
                                    Based on the latest sales performance evaluation and risk management policies, we have approved the update to your Bank Guarantee facility. Here are the final management decisions:
                                @endif
                            </p>

                            {{-- INFO BANK GARANSI (MULTI-BANK SUPPORT) --}}
                            @if(($isUploadContext || $isAdminNotif) && isset($candidateBgs) && $candidateBgs->count() > 0)
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 25px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 16px 18px;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                                <tr>
                                                    <td colspan="2" style="padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">
                                                        Document Details @if($candidateBgs->count() > 1) (Multi-Bank: {{ $candidateBgs->count() }} Bank) @endif
                                                    </td>
                                                </tr>
                                                @foreach($candidateBgs as $idx => $bgItem)
                                                    @php
                                                        $dFirst = $bgItem->details ? $bgItem->details->first() : null;
                                                        $bName = $dFirst && $dFirst->bank_name ? $dFirst->bank_name : ($bgItem->bank_name ?? 'Bank');
                                                        $bBranch = $dFirst && $dFirst->branch_name ? ' ('.$dFirst->branch_name.')' : '';
                                                    @endphp
                                                    <tr>
                                                        <td style="padding: 10px 0; color: #64748b; font-size: 13px; font-family: 'Segoe UI', Arial, sans-serif; {{ !$loop->last ? 'border-bottom: 1px dashed #e2e8f0;' : '' }}">
                                                            @if($candidateBgs->count() > 1)
                                                                <span style="background-color: #eff6ff; color: #2563eb; font-weight: 700; font-size: 11px; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">Bank {{ $idx + 1 }}</span>
                                                            @else
                                                                Bank Name:
                                                            @endif
                                                            <strong style="color: #1e293b; font-size: 14px;">{{ $bName }}</strong>
                                                            @if($bBranch)
                                                                <span style="color: #64748b; font-size: 12px;">{{ $bBranch }}</span>
                                                            @endif
                                                        </td>
                                                        <td style="padding: 10px 0; text-align: right; color: #15803d; font-weight: 700; font-size: 14px; font-family: Consolas, monospace, Arial; {{ !$loop->last ? 'border-bottom: 1px dashed #e2e8f0;' : '' }}">
                                                            Rp {{ number_format($bgItem->bg_nominal, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if($candidateBgs->count() > 1)
                                                    <tr>
                                                        <td style="padding: 12px 0 5px; color: #1e293b; font-size: 13px; font-weight: 700; border-top: 2px solid #cbd5e1; font-family: 'Segoe UI', Arial, sans-serif;">
                                                            Total Nominal Bank Garansi
                                                        </td>
                                                        <td style="padding: 12px 0 5px; text-align: right; color: #15803d; font-weight: 800; font-size: 15px; font-family: Consolas, monospace, Arial; border-top: 2px solid #cbd5e1;">
                                                            Rp {{ number_format($candidateBgs->sum('bg_nominal'), 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- HERO SECTION (ACTION BOX) --}}
                            @if($isAdminNotif)
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; margin-bottom: 30px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 20px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif;">
                                            <h3 style="margin: 0 0 8px; color: #1e40af; font-size: 16px; font-weight: 700;">
                                                Document Ready for Review
                                            </h3>
                                            <p style="margin: 0; font-size: 14px; color: #1e3a8a;">
                                                The uploaded document is waiting for your review and approval.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @elseif($isUploadContext)
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; margin-bottom: 30px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 20px; font-family: 'Segoe UI', Arial, sans-serif;">
                                            <h3 style="margin: 0 0 12px; color: #9a3412; font-size: 16px; font-weight: 700;">
                                                ⚠️ Required Action: Download, Sign & Upload
                                            </h3>
                                            <ol style="margin: 0; padding-left: 20px; font-size: 14px; color: #9a3412; line-height: 1.6;">
                                                <li style="margin-bottom: 6px;"><strong>Download</strong> PDF form.</li>
                                                <li style="margin-bottom: 6px;"><strong>Print & Sign</strong> (Wet signature + Stamp).</li>
                                                <li style="margin-bottom: 6px;"><strong>Scan</strong> the document into a PDF file.</li>
                                                <li><strong>Upload</strong> the document through the Upload button below.</li>
                                            </ol>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin-bottom: 30px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 25px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif;">
                                            <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #15803d; font-weight: 700;">
                                                SET BG (Approved Nominal)
                                            </p>
                                            <h1 style="margin: 8px 0 0; font-size: 34px; color: #15803d; letter-spacing: -1px; font-weight: 800; font-family: Arial, sans-serif;">
                                                Rp {{ number_format($rec->set_bg ?? 0, 0, ',', '.') }}
                                            </h1>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- DATA TABLES --}}
                            @if($rec)
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 700; color: #1e3a8a; border-left: 4px solid #3b82f6; padding-left: 10px; margin-bottom: 12px;">
                                Analysis Details & Decision
                            </div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 30px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; color: #475569; font-family: 'Segoe UI', Arial, sans-serif;">Approved BG Nominal (Set BG)</td>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #1e3a8a; font-weight: 700; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($rec->set_bg ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; color: #475569; font-family: 'Segoe UI', Arial, sans-serif;">Updated Credit Limit</td>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155; font-weight: 600; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($rec->credit_limit_updated ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; color: #475569; font-family: 'Segoe UI', Arial, sans-serif;">Average Sales</td>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($rec->average ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; color: #475569; font-family: 'Segoe UI', Arial, sans-serif;">System Recommended Limit</td>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($rec->recommended_credit_limit ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                {{-- Parameter Teknis --}}
                                <tr>
                                    <td colspan="2" style="padding: 16px 0 6px; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Calculation Parameters
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px; font-family: 'Segoe UI', Arial, sans-serif;">TOP / Lead Time</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        {{ $rec->top ?? 0 }} Days / {{ $rec->lead_time ?? 0 }} Days
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px; font-family: 'Segoe UI', Arial, sans-serif;">Inflation / Tax</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        {{ $rec->inflation ?? 0 }}% / {{ ($rec->tax ? $rec->tax->value * 100 : 11) }}%
                                    </td>
                                </tr>
                            </table>

                            {{-- PERIODS TABLE --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 700; color: #1e3a8a; border-left: 4px solid #3b82f6; padding-left: 10px; margin-bottom: 12px;">
                                Sales History
                            </div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 35px; border-collapse: collapse; font-size: 13px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <thead>
                                    <tr bgcolor="#f8fafc" style="background-color: #f8fafc;">
                                        <th style="padding: 10px 15px; text-align: left; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Period</th>
                                        <th style="padding: 10px 15px; text-align: right; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Nominal (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($rec->periods)
                                        @forelse($rec->periods as $period)
                                        <tr>
                                            <td style="padding: 8px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-family: 'Segoe UI', Arial, sans-serif;">
                                                {{ \Carbon\Carbon::parse($period->period_date)->locale('id')->isoFormat('MMMM Y') }}
                                            </td>
                                            <td style="padding: 8px 15px; border-bottom: 1px solid #f1f5f9; text-align: right; font-family: Consolas, monospace, Arial; color: #334155;">
                                                Rp {{ number_format($period->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" style="padding: 15px; text-align: center; color: #94a3b8; font-style: italic; font-family: 'Segoe UI', Arial, sans-serif;">
                                                No period details available.
                                            </td>
                                        </tr>
                                        @endforelse
                                    @else
                                        <tr>
                                            <td colspan="2" style="padding: 15px; text-align: center; color: #94a3b8; font-style: italic; font-family: 'Segoe UI', Arial, sans-serif;">
                                                No period data available.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            @endif

                            {{-- CTA SECTION (Bulletproof Table-Cell Buttons) --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 30px 20px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif;">
                                        <p style="font-size: 14px; margin: 0 0 20px 0; color: #475569; line-height: 1.5;">
                                            @if($isAdminNotif)
                                                Click the button below to access the Approval Inbox:
                                            @elseif($isUploadContext)
                                                Please download the form for <strong>{{ $targetBg->details->first()->bank_name ?? 'Bank' }}</strong>, then upload it back:
                                            @else
                                                To proceed with issuing a Bank Guarantee worth <strong>Rp {{ number_format($rec->set_bg ?? 0, 0, ',', '.') }}</strong>, please complete the guarantee bank details:
                                            @endif
                                        </p>

                                        <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto; border-collapse: separate;">
                                            @if($isUploadContext)
                                            <tr>
                                                <td align="center" bgcolor="#ffffff" style="border-radius: 50px; background-color: #ffffff; border: 1px solid #cbd5e1;">
                                                    <a href="{{ $downloadUrl }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 12px 26px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: 600; color: #475569; text-decoration: none; border-radius: 50px; line-height: 1.2;">
                                                        @if($isAdminNotif)
                                                            ⬇️ Download Uploaded Document
                                                        @else
                                                            ⬇️ Download PDF Form
                                                        @endif
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="12" style="height: 12px; font-size: 12px; line-height: 12px;">&nbsp;</td>
                                            </tr>
                                            @endif

                                            <tr>
                                                <td align="center" bgcolor="{{ $btnColor }}" style="border-radius: 50px; background-color: {{ $btnColor }};">
                                                    <a href="{{ $actionUrl }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 14px 32px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid {{ $btnColor }}; line-height: 1.2;">
                                                        {!! $btnText !!}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="font-size: 12px; color: #94a3b8; margin: 20px 0 0 0;">
                                            <em>*This link is confidential and specific to this application.</em>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td bgcolor="#1e293b" style="background-color: #1e293b; color: #94a3b8; padding: 25px 30px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; line-height: 1.6; border-top: 1px solid #334155;">
                            <p style="margin: 0 0 6px 0;">This email was sent automatically by the Customer Portal.</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} <strong>PT. Sinar Meadow International Indonesia</strong>.<br>Automated System Notification.</p>
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
