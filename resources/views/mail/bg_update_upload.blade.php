<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document {{ ucfirst($type) }}</title>
</head>
<body style="margin: 0; padding: 40px 0; background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">

        {{-- HEADER --}}
        <div style="background: linear-gradient(135deg, {{ $type == 'existing' ? '#4f46e5 0%, #4338ca' : '#059669 0%, #047857' }} 100%); padding: 35px 30px; text-align: center; color: #ffffff;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 700;">Document Confirmation {{ ucfirst($type) }}</h2>
            <p style="margin: 8px 0 0; opacity: 0.9; font-size: 14px;">Form Code: #{{ $submission->form_code }}</p>
        </div>

        <div style="padding: 40px 30px;">
            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin-bottom: 25px;">
                Dear <strong>{{ $submission->recommendation->customer->name ?? 'Business Partner' }}</strong>,<br><br>

                @if($type == 'existing')
                    Your Bank Guarantee nominal update data has been saved.
                @else
                    Your Bank Guarantee extension request data has been saved.
                @endif
                Please download the form below, sign it, and then re-upload it.
            </p>

            {{-- INSTRUKSI BOX --}}
            <div style="background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                <h3 style="margin: 0 0 10px; color: #9a3412; font-size: 16px;">⚠️ Steps:</h3>
                <ol style="margin: 0; padding-left: 20px; font-size: 14px; color: #9a3412; line-height: 1.6;">
                    <li style="margin-bottom: 5px;">Click the <strong>Download Form</strong> button below.</li>
                    <li style="margin-bottom: 5px;"><strong>Print & Sign</strong> (Wet Signature + Stamp).</li>
                    <li style="margin-bottom: 5px;"><strong>Scan</strong> the document into a PDF.</li>
                    <li>Click the <strong>Upload Document</strong> button to send it back.</li>
                </ol>
            </div>

            {{-- AREA TOMBOL ACTION --}}
            <div style="text-align: center; margin-bottom: 20px;">

                {{-- 1. TOMBOL DOWNLOAD (BARU) --}}
                <a href="{{ route('customer.portal.download-pdf', ['token' => $submission->token]) }}"
                   style="display: inline-block; background-color: #ffffff; color: {{ $type == 'existing' ? '#4f46e5' : '#059669' }}; padding: 12px 28px; font-size: 14px; font-weight: 700; text-decoration: none; border-radius: 50px; border: 2px solid {{ $type == 'existing' ? '#4f46e5' : '#059669' }}; margin-bottom: 15px;">
                    ⬇️ Download PDF Form
                </a>

                <br>

                {{-- 2. TOMBOL UPLOAD --}}
                <a href="{{ route('customer.portal.upload-form', ['token' => $submission->token]) }}"
                   style="display: inline-block; background-color: {{ $type == 'existing' ? '#4f46e5' : '#059669' }}; color: #ffffff; padding: 14px 32px; font-size: 16px; font-weight: 700; text-decoration: none; border-radius: 50px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    ⬆️ Upload Signed Document
                </a>

            </div>

            <p style="text-align: center; font-size: 12px; color: #94a3b8; margin-top: 30px;">
                <em>This link is secure and exclusively for submission code: {{ $submission->form_code }}</em>
            </p>
        </div>

        <div style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
             <p style="color: #94a3b8; font-size: 12px; margin: 0;">&copy; {{ date('Y') }} Financial System.</p>
        </div>
    </div>
</body>
</html>
