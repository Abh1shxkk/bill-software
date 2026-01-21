<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .otp-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 10px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Email Verification</h1>
            <p style="margin: 10px 0 0 0; color: #6c757d;">One-Time Password for Transaction Email</p>
        </div>

        <p>Hello <strong>{{ $userName }}</strong>,</p>
        
        <p>You requested to send a transaction email. Please use the following OTP to verify your email address and proceed:</p>

        <div class="otp-box">
            <p style="margin: 0; font-size: 16px;">Your OTP Code</p>
            <div class="otp-code">{{ $otp }}</div>
            <p style="margin: 0; font-size: 14px;">Valid for 10 minutes</p>
        </div>

        <div class="info-box">
            <strong style="color: #856404;">⚠️ Security Notice:</strong>
            <p style="margin: 5px 0 0 0; color: #856404;">
                • This OTP is valid for 10 minutes only<br>
                • Do not share this code with anyone<br>
                • If you didn't request this, please ignore this email
            </p>
        </div>

        <p style="margin-top: 30px;">
            Enter this OTP in the verification popup to send your transaction email.
        </p>

        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p style="margin: 5px 0;">This is an automated email. Please do not reply to this message.</p>
            <p style="margin: 5px 0; font-size: 12px;">Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
