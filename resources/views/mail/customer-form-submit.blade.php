<!DOCTYPE html>
<html>
<head>
    <title>Pemberitahuan Bank Garansi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer { margin-top: 30px; font-size: 0.8em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Yth. {{ $recommendation->customer->name ?? 'Bapak/Ibu' }},</h2>

        <p>Berdasarkan evaluasi terbaru, kami merekomendasikan penyesuaian limit fasilitas Bank Garansi Anda.</p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Recommended Limit:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">
                    Rp {{ number_format($recommendation->recommended_credit_limit, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Periode:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ date('Y') }}</td>
            </tr>
        </table>

        <p>Untuk melanjutkan proses penerbitan Bank Garansi, mohon lengkapi detail bank (Nama Bank, Cabang, Nominal, dll) melalui tautan di bawah ini:</p>

        {{-- Pastikan Anda sudah membuat route 'customer.bg.form' di web.php --}}
        <a href="{{ route('customer.bg.form', ['id' => $recommendation->id]) }}" class="btn">
            Isi Form Detail Bank Garansi
        </a>

        <p style="margin-top: 20px;">
            Setelah Anda mengisi form tersebut, sistem kami akan otomatis mengirimkan dokumen PDF yang siap untuk dicetak dan ditandatangani.
        </p>

        <div class="footer">
            <p>Terima kasih,<br>Tim Finance & Admin</p>
        </div>
    </div>
</body>
</html>
