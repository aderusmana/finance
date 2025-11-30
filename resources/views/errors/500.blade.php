<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
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
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

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

        .icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
        }

        .icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            animation: shake 3s infinite;
            box-shadow: 0 10px 30px rgba(250, 112, 154, 0.4);
        }

        .icon i {
            font-size: 60px;
            color: white;
        }

        @keyframes shake {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-5deg);
            }
            75% {
                transform: rotate(5deg);
            }
        }

        .error-code {
            font-size: 80px;
            font-weight: 700;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
            border-left: 4px solid #fa709a;
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
            color: #fa709a;
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
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(250, 112, 154, 0.6);
        }

        .btn-secondary {
            background: white;
            color: #fa709a;
            border: 2px solid #fa709a;
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
            color: #fa709a;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .help-text a:hover {
            color: #fee140;
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
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="error-container">
        <div class="icon-wrapper">
            <div class="icon">
                <i class="ph-fill ph-warning-circle"></i>
            </div>
        </div>

        <div class="error-code">500</div>
        <h1 class="error-title">Internal Server Error</h1>

        <p class="error-message">
            Oops! Something went wrong on our end. We're working to fix the problem. Please try again in a few moments.
        </p>

        <div class="error-details">
            <div class="error-details-title">
                <i class="ph-duotone ph-wrench"></i>
                What's Happening
            </div>
            <ul class="error-details-list">
                <li>
                    <i class="ph-duotone ph-gear"></i>
                    Our server encountered an unexpected error
                </li>
                <li>
                    <i class="ph-duotone ph-clock"></i>
                    The issue has been logged for investigation
                </li>
                <li>
                    <i class="ph-duotone ph-users-three"></i>
                    Our team is working to resolve it quickly
                </li>
            </ul>
        </div>

        <div class="button-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="ph-bold ph-house"></i>
                Go Home
            </a>
            <a href="javascript:window.location.reload()" class="btn btn-secondary">
                <i class="ph-bold ph-arrow-clockwise"></i>
                Try Again
            </a>
        </div>

        <div class="help-text">
            Problem persists? <a href="mailto:support@sinarmeadow.com">Report this issue</a>
        </div>
    </div>
</body>
</html>
