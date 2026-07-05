<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
        }
        .otp-box {
            background-color: #3498db;
            color: white;
            font-size: 36px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            letter-spacing: 10px;
            margin: 30px 0;
        }
        .message {
            color: #666;
            font-size: 14px;
            text-align: center;
            line-height: 1.6;
        }
        .warning {
            color: #e74c3c;
            font-size: 13px;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Clinic Management System</h1>
        </div>

        <p class="message">Hello <strong>{{ $userName }}</strong>,</p>
        <p class="message">Your OTP verification code is:</p>

        <div class="otp-box">
            {{ $otp }}
        </div>

        <p class="message">This code is valid for <strong>10 minutes</strong> only.</p>

        <p class="warning">
            ⚠️ If you did not request this code, please ignore this email.
        </p>

        <div class="footer">
            <p>© 2026 Clinic Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
