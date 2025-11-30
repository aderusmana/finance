<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Unauthorized Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Animated background circles */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: -100px;
            animation-delay: 2s;
        }

        .bg-circle:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 10%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-30px) scale(1.1);
            }
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lock-icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
        }

        .lock-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .lock-icon i {
            font-size: 60px;
            color: white;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
            }
        }

        .error-code {
            font-size: 80px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -2px;
        }

        .error-title {
            font-size: 32px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 16px;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-details {
            background: #f7fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .error-details-title {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .error-details-list {
            list-style: none;
            padding: 0;
            text-align: left;
        }

        .error-details-list li {
            font-size: 14px;
            color: #718096;
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-details-list li i {
            color: #667eea;
            flex-shrink: 0;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f7fafc;
            transform: translateY(-2px);
        }

        .help-text {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #a0aec0;
        }

        .help-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .help-text a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .error-container {
                padding: 40px 24px;
            }

            .error-code {
                font-size: 60px;
            }

            .error-title {
                font-size: 24px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background circles -->
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="error-container">
        <!-- Lock Icon -->
        <div class="lock-icon-wrapper">
            <div class="lock-icon">
                <i class="ph-fill ph-lock"></i>
            </div>
        </div>

        <!-- Error Code -->
        <div class="error-code">401</div>

        <!-- Error Title -->
        <h1 class="error-title">Unauthorized Access</h1>

        <!-- Error Message -->
        <p class="error-message">
            Sorry, you don't have permission to access this page. This area is restricted and requires proper authentication.
        </p>

        <!-- Error Details -->
        <div class="error-details">
            <div class="error-details-title">
                <i class="ph-duotone ph-warning-circle"></i>
                Possible Reasons
            </div>
            <ul class="error-details-list">
                <li>
                    <i class="ph-duotone ph-user-circle"></i>
                    You are not logged in or your session has expired
                </li>
                <li>
                    <i class="ph-duotone ph-shield-warning"></i>
                    You don't have the required permissions
                </li>
                <li>
                    <i class="ph-duotone ph-key"></i>
                    Invalid or expired authentication token
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="button-group">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="ph-bold ph-sign-in"></i>
                Login Again
            </a>
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <i class="ph-bold ph-house"></i>
                Go Home
            </a>
        </div>

        <!-- Help Text -->
        <div class="help-text">
            Need help? Contact your <a href="mailto:support@sinarmeadow.com">system administrator</a>
        </div>
    </div>
</body>
</html>
