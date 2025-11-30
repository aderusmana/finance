<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Requisition Rejected</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #ddd;
            overflow: hidden;
        }

        .header {
            background-color: #dc3545; /* Warna Reject (Merah) */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
            line-height: 1.6;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .details-table td {
            padding: 10px;
            border: 1px solid #eee;
        }

        .details-table td:first-child {
            background-color: #f9f9f9;
            font-weight: bold;
            width: 35%;
        }

        .notes-box {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545; /* Warna Reject (Merah) */
            padding: 15px;
            margin-top: 20px;
        }

        .footer {
            background-color: #f4f4f4;
            color: #777;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Your Requisition Has Been Rejected</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $requisition->requester->name }}</strong>,</p>
            <p>
                We regret to inform you that your Free Goods Requisition with the number
                <strong>{{ $requisition->no_srs }}</strong> has been rejected.
            </p>

            <div class="notes-box">
                <p><strong>Rejected by:</strong> {{ $rejectingApproverName }}</p>
                <p><strong>Reason:</strong><br><em>"{{ $notes }}"</em></p>
            </div>

            <p>
                Please review your request details and submit a new Requisition Slip if necessary.
            </p>
            <p>Thank you.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Requisition Slip App. All rights reserved.</p>
        </div>
    </div>
</body>

</html>