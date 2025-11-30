<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .message-type {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .package-ready { background: #d4edda; color: #155724; }
        .payment-reminder { background: #fff3cd; color: #856404; }
        .account-update { background: #d1ecf1; color: #0c5460; }
        .office-hours { background: #e2e3e5; color: #383d41; }
        .general { background: #e7f3ff; color: #004085; }

        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .message-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $companyName }}</div>
            <div class="message-type {{ $messageType }}">
                @switch($messageType)
                    @case('package_ready')
                        📦 Package Ready for Pickup
                        @break
                    @case('payment_reminder')
                        💰 Payment Reminder
                        @break
                    @case('account_update')
                        📋 Account Update Required
                        @break
                    @case('office_hours')
                        🕒 Office Hours Notice
                        @break
                    @default
                        📧 Message from {{ $companyName }}
                @endswitch
            </div>
        </div>

        <div class="customer-info">
            <strong>Dear {{ $customerName }},</strong><br>
            <small>Mailbox #{{ $mailboxNumber }}</small>
        </div>

        <div class="message-content">
            {!! nl2br(e($message)) !!}
        </div>

        @if($messageType === 'package_ready')
            <div class="contact-info">
                <strong>📍 Pickup Information:</strong><br>
                • Please bring a valid ID<br>
                • Our address: [Your Business Address]<br>
                • Office Hours: Mon-Fri 9AM-6PM, Sat 9AM-3PM<br>
                • Phone: [Your Phone Number]
            </div>
        @endif

        @if($messageType === 'payment_reminder')
            <div class="contact-info">
                <strong>💳 Payment Options:</strong><br>
                • Visit us in person<br>
                • Call us at [Your Phone Number]<br>
                • Online payment: [Payment Portal URL]<br>
                • We accept cash, cards, and checks
            </div>
        @endif

        <div class="footer">
            <p><strong>{{ $companyName }}</strong></p>
            <p>Professional Mailbox & Package Services</p>
            <p>
                📍 [Your Address] | 📞 [Your Phone] | 🌐 [Your Website]<br>
                📧 [Your Email] | 🕒 Mon-Fri 9AM-6PM, Sat 9AM-3PM
            </p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e9ecef;">
            <p style="font-size: 12px; color: #868e96;">
                This email was sent to you regarding your mailbox #{{ $mailboxNumber }}.<br>
                If you have any questions, please contact us directly.
            </p>
        </div>
    </div>
</body>
</html>
