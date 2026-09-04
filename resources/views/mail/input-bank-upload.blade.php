<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Garansi Notification</title>
</head>
<body style="margin: 0; padding: 40px 0; background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased; color: #334155;">

    @php
        $isUploadContext = isset($submission);
        $targetBg = null;
        $rec = null;
        $bankDetails = [];
        $token = '#';
        $customer = null;

        $isAdminNotif = isset($isUploadAdminNotif) && $isUploadAdminNotif;

        // 1. CEK KONTEKS & INISIALISASI VARIABEL
        if ($isAdminNotif) {
            // Konteks: Email Notifikasi ke Admin (Super Admin / RTM) saat Upload
            $rec = $recommendation;
            $customer = $rec->customer;

            $pageTitle = 'Document Uploaded by Customer';
            $refNumber = 'Ref ID: #' . substr($rec->id, 0, 8);

            // Setup Tombol & Link untuk Approval
            if (isset($submission) && $submission->token) {
                $actionUrl = route('customer.portal.review-upload', ['token' => $submission->token]);
                $downloadUrl = route('customer.portal.download-submission-pdf', ['token' => $submission->token]);
            } else {
                $actionUrl = route('bg-approvals.index');
                $downloadUrl = '#'; 
            }
            $btnColor = '#3b82f6';
            $btnShadow = 'rgba(59, 130, 246, 0.3)';
            $btnText = 'Review Uploaded Document';
        } elseif ($isUploadContext) {
            // Konteks: Email Konfirmasi Upload (Submission) ke Customer
            $rec = $submission->recommendation;
            $token = $submission->token;
            $customer = $rec->customer;

            $pageTitle = 'Confirmation of BG Application';
            $refNumber = 'Form Code: #' . $submission->form_code;

            // Logika pencarian BG Spesifik (Existing/New)
            $submissionTime = \Carbon\Carbon::parse($submission->created_at);
            $startTime = $submissionTime->copy()->subMinutes(2);
            $endTime   = $submissionTime->copy()->addMinutes(2);
            $customerId = $rec ? $rec->customer_id : null;

            if ($customerId) {
                $candidateBgs = \App\Models\BG\BankGaransi::where('customer_id', $customerId)
                    ->whereBetween('created_at', [$startTime, $endTime])
                    ->with('details')
                    ->orderBy('id', 'asc')
                    ->get();

                if ($candidateBgs->isEmpty()) {
                    // Fallback cek updated_at untuk existing
                    $candidateBgs = \App\Models\BG\BankGaransi::where('customer_id', $customerId)
                        ->whereBetween('updated_at', [$startTime, $endTime])
                        ->with('details')
                        ->orderBy('id', 'asc')
                        ->get();
                }

                $siblings = \App\Models\BG\BgSubmission::where('bg_recommendation_id', $rec->id)
                    ->whereBetween('created_at', [$startTime, $endTime])
                    ->orderBy('id', 'asc')
                    ->pluck('id')
                    ->toArray();

                $myIndex = array_search($submission->id, $siblings);

                if ($myIndex !== false && isset($candidateBgs[$myIndex])) {
                    $targetBg = $candidateBgs[$myIndex];
                } else {
                    $targetBg = $candidateBgs->first();
                }
            }

            // Setup Tombol & Link untuk Upload
            $actionUrl = route('customer.portal.upload-form', ['token' => $token]);
            $downloadUrl = route('customer.portal.download-pdf', ['token' => $token]);
            $btnColor = '#2563eb';
            $btnShadow = 'rgba(37, 99, 235, 0.3)';
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
            $btnShadow = 'rgba(16, 185, 129, 0.3)';
            $btnText = 'Fill in the Bank Guarantee Form';

        } else {
            // Fallback Error Prevention
            $pageTitle = 'System Notification';
            $refNumber = '-';
            $actionUrl = '#';
            $downloadUrl = '#';
            $btnColor = '#64748b';
            $btnShadow = 'none';
            $btnText = 'Invalid Link';
        }
    @endphp

    <div style="max-width: 680px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">

        {{-- HEADER --}}
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 40px 30px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">{{ $pageTitle }}</h1>
            <p style="margin: 8px 0 0; opacity: 0.9; font-size: 14px;">{{ $refNumber }}</p>
        </div>

        <div style="padding: 40px 35px;">

            {{-- GREETING --}}
            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin-bottom: 25px; margin-top: 0;">
                Dear <strong>{{ $customer->name ?? 'Business Partner' }}</strong>,<br><br>

                @if($isAdminNotif)
                    Customer <strong>{{ $customer->name ?? 'Business Partner' }}</strong> has successfully uploaded their signed Bank Guarantee document. 
                    Please review the submission and verify the document through the Approval Inbox.
                @elseif($isUploadContext)
                    Thank you, we have successfully received your digital form data.
                    To legally validate this submission, we require the physical documents to be signed.
                @else
                    Based on the latest sales performance evaluation and risk management policies, we have approved the update to your Bank Guarantee facility. Here are the final management decisions:
                @endif
            </p>

            {{-- INFO BANK GARANSI SPESIFIK (HANYA MUNCUL DI EMAIL UPLOAD) --}}
            @if($isUploadContext && $targetBg && $targetBg->details->first())
                <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td colspan="2" style="padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700;">
                                Document Details
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0 5px; color: #64748b; font-size: 13px;">Bank Name</td>
                            <td style="padding: 10px 0 5px; text-align: right; color: #1e293b; font-weight: 700; font-size: 14px;">
                                {{ $targetBg->details->first()->bank_name ?? '-' }}
                                @if($targetBg->details->first()->branch_name)
                                    <span style="color: #64748b; font-weight: normal; font-size: 12px;">({{ $targetBg->details->first()->branch_name }})</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; color: #64748b; font-size: 13px;">Nominal</td>
                            <td style="padding: 5px 0; text-align: right; color: #15803d; font-weight: 700; font-size: 14px;">
                                Rp {{ number_format($targetBg->bg_nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

            {{-- HERO SECTION (ACTION) --}}
            @if($isAdminNotif)
                <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 25px; margin-bottom: 35px; text-align: center;">
                    <h3 style="margin: 0 0 10px; color: #1e40af; font-size: 16px; font-weight: 700;">
                        Document Ready for Review
                    </h3>
                    <p style="margin: 0; font-size: 14px; color: #1e3a8a;">
                        The uploaded document is waiting for your review and approval.
                    </p>
                </div>
            @elseif($isUploadContext)
                <div style="background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 25px; margin-bottom: 35px;">
                    <h3 style="margin: 0 0 15px; color: #9a3412; font-size: 16px; font-weight: 700;">
                        ⚠️ Required Action: Download, Sign & Upload
                    </h3>
                    <ol style="margin: 0; padding-left: 20px; font-size: 14px; color: #9a3412; line-height: 1.6;">
                        <li style="margin-bottom: 8px;"><strong>Download</strong> PDF form.</li>
                        <li style="margin-bottom: 8px;"><strong>Print & Sign</strong> (Wet signature + Stamp).</li>
                        <li style="margin-bottom: 8px;"><strong>Scan</strong> the document into a PDF file.</li>
                        <li><strong>Upload</strong> the document through the Upload button below.</li>
                    </ol>
                </div>
            @else
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 30px; text-align: center; margin-bottom: 35px;">
                    <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #15803d; font-weight: 700;">
                        SET BG (Approved Nominal)
                    </p>
                    <h1 style="margin: 10px 0 10px; font-size: 38px; color: #15803d; letter-spacing: -1px; font-weight: 800;">
                        Rp {{ number_format($rec->set_bg ?? 0, 0, ',', '.') }}
                    </h1>
                </div>
            @endif

            {{-- DATA TABLES --}}
            @if($rec)
            <div style="margin-bottom: 15px; border-left: 4px solid #3b82f6; padding-left: 12px;">
                <h3 style="margin: 0; color: #1e3a8a; font-size: 16px; font-weight: 700;">Analysis Details & Decision</h3>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 40px;">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">Approved BG Nominal (Set BG)</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #1e3a8a; font-weight: 700;">
                        Rp {{ number_format($rec->set_bg ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">Updated Credit Limit</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155; font-weight: 600;">
                        Rp {{ number_format($rec->credit_limit_updated ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">Average Sales</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155;">
                        Rp {{ number_format($rec->average ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">System Recommended Limit</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155;">
                        Rp {{ number_format($rec->recommended_credit_limit ?? 0, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Parameter Teknis --}}
                <tr>
                    <td colspan="2" style="padding: 20px 0 5px; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        Calculation Parameters
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px;">TOP / Lead Time</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px;">
                        {{ $rec->top ?? 0 }} Days / {{ $rec->lead_time ?? 0 }} Days
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px;">Inflation / Tax</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px;">
                        {{ $rec->inflation ?? 0 }}% / {{ ($rec->tax ? $rec->tax->value * 100 : 11) }}%
                    </td>
                </tr>
            </table>

            {{-- PERIODS TABLE --}}
            <div style="margin-bottom: 15px; border-left: 4px solid #3b82f6; padding-left: 12px;">
                <h3 style="margin: 0; color: #1e3a8a; font-size: 16px; font-weight: 700;">Sales History</h3>
            </div>

            <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 40px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th style="padding: 10px 15px; text-align: left; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Period</th>
                            <th style="padding: 10px 15px; text-align: right; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($rec->periods)
                            @forelse($rec->periods as $period)
                            <tr>
                                <td style="padding: 8px 15px; border-bottom: 1px solid #f1f5f9; color: #334155;">
                                    {{ \Carbon\Carbon::parse($period->period_date)->locale('id')->isoFormat('MMMM Y') }}
                                </td>
                                <td style="padding: 8px 15px; border-bottom: 1px solid #f1f5f9; text-align: right; font-family: Consolas, monospace; color: #334155;">
                                    Rp {{ number_format($period->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" style="padding: 15px; text-align: center; color: #94a3b8; font-style: italic;">
                                    No period details available.
                                </td>
                            </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="2" style="padding: 15px; text-align: center; color: #94a3b8; font-style: italic;">
                                    No period data available.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif

            {{-- CTA SECTION --}}
            <div style="text-align: center; padding: 35px 20px; background-color: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <p style="font-size: 14px; margin: 0 0 25px; color: #475569; line-height: 1.5;">
                    @if($isAdminNotif)
                        Click the button below to access the Approval Inbox:
                    @elseif($isUploadContext)
                        Please download the form for <strong>{{ $targetBg->details->first()->bank_name ?? 'Bank' }}</strong>, then upload it back:
                    @else
                        To proceed with issuing a Bank Guarantee worth <strong>Rp {{ number_format($rec->set_bg ?? 0, 0, ',', '.') }}</strong>, please complete the guarantee bank details:
                    @endif
                </p>

                @if($isUploadContext)
                    <a href="{{ $downloadUrl }}"
                       style="display: inline-block; background-color: #ffffff; color: #475569; padding: 12px 25px; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 50px; border: 1px solid #cbd5e1; margin-bottom: 15px; margin-right: 10px;">
                        @if($isAdminNotif)
                            ⬇️ Download Uploaded Document
                        @else
                            ⬇️ Download PDF Form
                        @endif
                    </a>
                @endif

                <a href="{{ $actionUrl }}"
                   style="display: inline-block; background-color: {{ $btnColor }}; color: #ffffff; padding: 14px 35px; font-size: 16px; font-weight: 700; text-decoration: none; border-radius: 50px; box-shadow: 0 4px 10px {{ $btnShadow }};">
                    {!! $btnText !!}
                </a>

                <p style="font-size: 12px; color: #94a3b8; margin: 20px 0 0;">
                    <em>*This link is confidential and specific to this application.</em>
                </p>
            </div>

        </div>

        {{-- FOOTER --}}
        <div style="background-color: #1e293b; color: #94a3b8; padding: 30px; text-align: center; font-size: 12px; line-height: 1.6;">
            <p style="margin: 0 0 10px;">This email was sent automatically by the Customer Portal.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} <strong>PT. Sinar Meadow International Indonesia</strong>.<br>Automated System Notification.</p>
        </div>

    </div>

</body>
</html>
