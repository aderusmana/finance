<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Request</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            text-align: center;
            max-width: 550px;
            width: 100%;
            border-top: 5px solid #e53e3e;
        }
        .icon {
            font-size: 64px;
            color: #e53e3e;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin: 0 0 15px 0;
            font-size: 2.2rem;
            font-weight: 600;
        }
        .message-box {
            background-color: #fff3f3;
            border: 1px solid #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            margin-bottom: 25px;
        }
        .message-box p {
            margin: 0;
            font-weight: 500;
            font-size: 1rem;
        }
        .explanation {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .button {
            background-color: #004a99;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 30px;
        }
        .button:hover {
            background-color: #003b7a;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 74, 153, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Request Cannot Be Processed</h1>
        <p class="explanation">
            We're sorry, but the approval link you used is no longer valid.
            This may happen if the request has already been approved, rejected, or canceled by another user.
        </p>
        <div class="message-box">
            <p>{{ $message }}</p>
        </div>
        <p class="explanation">
            Please check your application dashboard for the latest status. You can now safely close this page.
        </p>
        <button id="closeTabBtn" class="button">Close Tab</button>
    </div>

    <script>
        document.getElementById('closeTabBtn').addEventListener('click', function() {
            window.close();
        });
    </script>
</body>
</html>
