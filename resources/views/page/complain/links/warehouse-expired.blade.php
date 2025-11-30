<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Approval Link Expired - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgb(192, 127, 0) 0%, rgb(170, 110, 0) 50%, rgb(140, 90, 0) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .expired-card {
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
        
        .expired-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fdcb6e, #e17055);
        }
        
        .expired-icon {
            width: 80px;
            height: 80px;
            background: #fdcb6e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .expired-icon::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border: 3px solid #fdcb6e;
            border-radius: 50%;
            opacity: 0.3;
            animation: pulse 2s infinite;
        }
        
        .expired-icon i {
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
        
        .expired-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .expired-message {
            color: #7f8c8d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .expired-details {
            background: #f8f9fa;
            border-left: 4px solid #fdcb6e;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 0 8px 8px 0;
        }
        
        .expired-details strong {
            color: #2c3e50;
        }
        
        .processing-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .processing-info strong {
            color: #856404;
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
        
        .contact-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="expired-card">
        <!-- Expired Icon -->
        <div class="expired-icon">
            <i class="fas fa-history"></i>
        </div>
        
        <!-- Expired Title -->
        <h1 class="expired-title">Warehouse Link Already Processed</h1>
        
        <!-- Expired Message -->
        <p class="expired-message">
            This warehouse approval link has already been used and processed. No further action is required.
        </p>
        
        <!-- Requisition Details -->
        <div class="expired-details">
            <strong>Requisition Information:</strong><br>
            <i class="fas fa-file-alt text-primary"></i> 
            <strong>Number:</strong> {{ $requisition->no_srs ?? 'N/A' }}<br>
            <i class="fas fa-building text-info"></i> 
            <strong>Customer:</strong> {{ $requisition->customer->name ?? 'N/A' }}<br>
            <i class="fas fa-flag text-warning"></i> 
            <strong>Current Status:</strong> 
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

        <!-- Processing Details -->
        @if(isset($tracking))
        <div class="processing-info">
            <strong>Processing Details:</strong><br>
            <i class="fas fa-warehouse text-success"></i> 
            <strong>Position:</strong> {{ $tracking->current_position }}<br>
            <i class="fas fa-clock text-info"></i> 
            <strong>Processed At:</strong> {{ $tracking->last_updated ? \Carbon\Carbon::parse($tracking->last_updated)->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB' : 'N/A' }}<br>
            @if($tracking->notes)
                <i class="fas fa-sticky-note text-warning"></i> 
                <strong>Notes:</strong> {{ $tracking->notes }}
            @endif
        </div>
        @endif

        <!-- Contact Information -->
        <div class="contact-info">
            <i class="fas fa-info-circle text-info"></i>
            <strong>Need Help?</strong><br>
            If you believe this is an error, please contact the system administrator or the warehouse supervisor who sent you this approval request.
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
                if (e.target.closest('.btn-cancel')) return; // Don't trigger for cancel button
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