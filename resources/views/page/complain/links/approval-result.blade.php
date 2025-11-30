<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Confirmation - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgb(192, 127, 0) 0%, rgb(180, 115, 0) 50%, rgb(160, 100, 0) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .result-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .result-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00b894, #00cec9);
        }

        .result-card.rejected::before {
            background: linear-gradient(90deg, #ff6b6b, #ee5a24);
        }

        .result-icon {
            width: 80px;
            height: 80px;
            background: #00b894;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }

        .result-icon.rejected {
            background: #ff6b6b;
        }

        .result-icon::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border: 3px solid #00b894;
            border-radius: 50%;
            opacity: 0.3;
            animation: pulse 2s infinite;
        }

        .result-icon.rejected::after {
            border-color: #ff6b6b;
        }

        .result-icon i {
            color: white;
            font-size: 35px;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.1;
            }
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .result-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .result-message {
            color: #7f8c8d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .result-details {
            background: #f8f9fa;
            border-left: 4px solid #00b894;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 0 8px 8px 0;
        }

        .result-details.rejected {
            border-left-color: #ff6b6b;
        }

        .result-details strong {
            color: #2c3e50;
        }

        .status-info {
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .countdown-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0 0 0;
        }

        .countdown-text {
            color: #856404;
            font-weight: 400;
            font-size: 13px;
        }

        .countdown-number {
            font-size: 16px;
            font-weight: bold;
            color: #ff6b6b;
            margin: 0 3px;
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 400;
            font-size: 11px;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="result-card {{ $status === 'reject' ? 'rejected' : '' }}">
        <!-- Result Icon -->
        <div class="result-icon {{ $status === 'reject' ? 'rejected' : '' }}">
            @if($status === 'approve')
                <i class="fas fa-check"></i>
            @else
                <i class="fas fa-times"></i>
            @endif
        </div>

        <!-- Result Title -->
        <h1 class="result-title">
            @if($status === 'approve')
                Approval Successful!
            @else
                Rejection Successful!
            @endif
        </h1>

        <!-- Result Message -->
        <p class="result-message">
            @if($status === 'approve')
                The requisition has been approved successfully and forwarded to the next level.
            @else
                The requisition has been rejected successfully with your provided reason.
            @endif
        </p>

        <!-- Result Details -->
        <div class="result-details {{ $status === 'reject' ? 'rejected' : '' }}">
            <strong>Action Completed:</strong><br>
            <i class="fas fa-file-alt text-primary"></i>
            <strong>Requisition Number:</strong> {{ $requisition->no_srs ?? 'N/A' }}<br>
            <i class="fas fa-building text-info"></i>
            <strong>Customer:</strong> {{ $requisition->customer->name ?? 'N/A' }}<br>
            <i class="fas fa-flag text-warning"></i>
            <strong>Status:</strong>
            @if($requisition->status == 'Approved')
                <span class="badge bg-success">Approved</span>
            @elseif($requisition->status == 'Rejected')
                <span class="badge bg-danger">Rejected</span>
            @elseif($requisition->status == 'In Progress')
                <span class="badge bg-info">In Progress</span>
            @else
                <span class="badge bg-secondary">{{ $requisition->status }}</span>
            @endif
        </div>

        <!-- Status Information -->
        <div class="status-info">
            <i class="fas fa-info-circle text-info"></i>
            <strong>What happens next?</strong><br>
            <span class="text-muted">
                @if($status === 'approve')
                    This action has been recorded and the requisition will proceed to the next approval level or be marked as fully approved.
                @else
                    This action has been recorded and the requisition process has been stopped. The requester will be notified of the rejection.
                @endif
            </span>
        </div>

        <!-- Countdown Section -->
        <div class="countdown-section" id="countdown-section">
            <div class="countdown-text">
                <i class="fas fa-stopwatch"></i>
                Auto-close in
                <span class="countdown-number" id="countdown">5</span>
                sec
            </div>
            <button class="btn btn-cancel" onclick="cancelAutoClose()">
                <i class="fas fa-hand-paper"></i> Cancel
            </button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let countdown = 5;
        let countdownInterval;
        let autoCloseActive = true;

        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            const countdownSection = document.getElementById('countdown-section');

            countdownInterval = setInterval(() => {
                countdown--;

                if (countdown > 0) {
                    countdownElement.textContent = countdown;
                } else {
                    clearInterval(countdownInterval);
                    countdownSection.innerHTML = `
                        <div class="countdown-text">
                            <i class="fas fa-spinner fa-spin"></i> Closing page now...
                        </div>
                    `;

                    // Close the page after a short delay
                    setTimeout(() => {
                        if (window.opener) {
                            // If opened in popup, close it
                            window.close();
                        } else {
                            // Try to close the current tab/window
                            window.close();

                            // If window.close() doesn't work (some browsers block it),
                            // try alternative methods
                            setTimeout(() => {
                                if (window.history.length > 1) {
                                    window.history.back();
                                } else {
                                    // As last resort, redirect to a close instruction page
                                    document.body.innerHTML = `
                                        <div style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:Arial,sans-serif;text-align:center;background:#f8f9fa;">
                                            <div>
                                                <h3>Please close this tab manually</h3>
                                                <p style="color:#6c757d;">This window should have closed automatically.<br>Please close this tab manually.</p>
                                            </div>
                                        </div>
                                    `;
                                }
                            }, 500);
                        }
                    }, 1000);
                }
            }, 1000);
        }

        function cancelAutoClose() {
            if (autoCloseActive) {
                clearInterval(countdownInterval);
                autoCloseActive = false;

                const countdownSection = document.getElementById('countdown-section');
                countdownSection.style.background = '#d1ecf1';
                countdownSection.style.borderColor = '#bee5eb';
                countdownSection.innerHTML = `
                    <div class="countdown-text" style="color: #0c5460;">
                        <i class="fas fa-check-circle"></i> Auto-close cancelled
                    </div>
                `;
            }
        }

        // Start countdown when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();

            // Also cancel auto-close on any user interaction
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-cancel')) return;
                if (autoCloseActive) {
                    cancelAutoClose();
                }
            });

            document.addEventListener('keydown', function() {
                if (autoCloseActive) {
                    cancelAutoClose();
                }
            });
        });
    </script>
</body>
</html>
