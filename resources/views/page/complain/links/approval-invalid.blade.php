<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Link - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgb(192, 127, 0) 0%, rgb(160, 100, 0) 50%, rgb(128, 80, 0) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-card {
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
        
        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #ee5a24);
        }
        
        .chain-icon {
            width: 80px;
            height: 80px;
            background: #ff6b6b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .chain-icon::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border: 3px solid #ff6b6b;
            border-radius: 50%;
            opacity: 0.3;
            animation: pulse 2s ease-out infinite;
        }
        
        .chain-icon i {
            color: white;
            font-size: 35px;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 0.4;
            }
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }
        
        .error-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #7f8c8d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 0 8px 8px 0;
        }
        
        .error-details strong {
            color: #2c3e50;
        }
        
        .last-action {
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .last-action .icon {
            color: #17a2b8;
            margin-right: 8px;
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
        
        .suggestions {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .suggestions h6 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .suggestion-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #6c757d;
        }
        
        .suggestion-item i {
            margin-right: 10px;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <!-- Chain Icon -->
        <div class="chain-icon">
            <i class="fas fa-unlink"></i>
        </div>
        
        <!-- Error Title -->
        <h1 class="error-title">Invalid Link</h1>
        
        <!-- Error Message -->
        <p class="error-message">
            Unfortunately, this approval link is no longer valid or has already been processed.
        </p>
        
        <!-- Error Details -->
        <div class="error-details">
            <strong>What happened?</strong><br>
            @if(isset($errorType))
                @if($errorType === 'missing_params')
                    <i class="fas fa-exclamation-triangle text-warning"></i> The approval link is incomplete or corrupted. Essential parameters are missing.
                @elseif($errorType === 'invalid_link')
                    <i class="fas fa-question-circle text-danger"></i> The approval link references a requisition that doesn't exist in our system.
                @elseif($errorType === 'token_not_found' || $errorType === 'token_expired')
                    <i class="fas fa-history text-info"></i> This approval link has already been used or has expired for security reasons.
                @else
                    <i class="fas fa-exclamation-circle text-secondary"></i> An unexpected error occurred while processing this approval link.
                @endif
            @else
                <i class="fas fa-exclamation-circle text-secondary"></i> This approval link is no longer valid.
            @endif
        </div>
        
        <!-- Last Action Information -->
        @if(isset($lastActionDate) && $lastActionDate !== 'Unknown')
        <div class="last-action">
            <i class="fas fa-clock icon"></i>
            <strong>Last action performed:</strong><br>
            <span class="text-muted">{{ $lastActionDate }} WIB</span>
        </div>
        @endif
        
        <!-- Suggestions -->
        <div class="suggestions">
            <h6><i class="fas fa-lightbulb text-warning"></i> What can you do?</h6>
            <div class="suggestion-item">
                <i class="fas fa-envelope text-primary"></i>
                <span>Check your email for the correct approval link</span>
            </div>
            <!-- <div class="suggestion-item"> -->
                <!-- <i class="fas fa-redo text-success"></i> -->
                <!-- <span>Request a new approval link if needed</span> -->
            <!-- </div> -->
            <div class="suggestion-item">
                <i class="fas fa-phone text-info"></i>
                <span>Contact the sender for assistance</span>
            </div>
        </div>
        
        <!-- Countdown Section - Moved to Bottom -->
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
                countdownSection.className = 'countdown-section';
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