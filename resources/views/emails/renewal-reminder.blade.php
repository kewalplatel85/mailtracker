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
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header.low { border-bottom: 3px solid #28a745; }
        .header.medium { border-bottom: 3px solid #ffc107; }
        .header.high { border-bottom: 3px solid #dc3545; }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .urgency-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .urgency-low { background: #d4edda; color: #155724; }
        .urgency-medium { background: #fff3cd; color: #856404; }
        .urgency-high { background: #f8d7da; color: #721c24; }

        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .due-date-box {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
        }
        .due-date-low { background: #d4edda; color: #155724; }
        .due-date-medium { background: #fff3cd; color: #856404; }
        .due-date-high { background: #f8d7da; color: #721c24; }

        .message-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .renewal-info {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: 600;
            text-align: center;
        }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-danger { background: #dc3545; }

        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $urgencyLevel }}">
            <div class="logo">{{ $companyName }}</div>
            <div class="urgency-badge urgency-{{ $urgencyLevel }}">
                @switch($reminderType)
                    @case('gentle')
                        🙂 Friendly Reminder
                        @break
                    @case('standard')
                        📋 Renewal Notice
                        @break
                    @case('urgent')
                        ⚠️ URGENT: Due Soon
                        @break
                    @case('final')
                        🚨 FINAL NOTICE
                        @break
                    @default
                        🔔 Renewal Reminder
                @endswitch
            </div>
        </div>

        <div class="customer-info">
            <strong>Dear {{ $customerName }},</strong><br>
            <small>Mailbox #{{ $mailboxNumber }}</small>
        </div>

        <div class="due-date-box due-date-{{ $urgencyLevel }}">
            @switch($reminderType)
                @case('final')
                    🚨 Your mailbox rental was due: {{ $dueDate }}
                    @break
                @case('urgent')
                    ⚠️ Your mailbox rental is due: {{ $dueDate }}
                    @break
                @default
                    📅 Your mailbox rental is due: {{ $dueDate }}
            @endswitch
        </div>

        <div class="message-content">
            {!! nl2br(e($message)) !!}
        </div>

        <div class="renewal-info">
            <h3 style="color: #007bff; margin-top: 0;">💳 How to Renew Your Mailbox</h3>

            <p><strong>📍 Visit Us In Person:</strong><br>
            [Your Business Address]<br>
            Mon-Fri 9AM-6PM, Sat 9AM-3PM</p>

            <p><strong>📞 Call Us:</strong><br>
            [Your Phone Number]</p>

            <p><strong>💳 Payment Methods:</strong><br>
            • Cash, Credit Cards, Debit Cards<br>
            • Personal Checks<br>
            • Online Payment Portal: [URL]</p>

            @if($urgencyLevel === 'high')
                <div style="background: #f8d7da; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #dc3545;">
                    <strong>⚠️ Important:</strong> Service may be suspended if payment is not received promptly.
                    All packages will be held securely, but access to your mailbox will be restricted.
                </div>
            @endif
        </div>

        <div style="text-align: center; margin: 20px 0;">
            @switch($urgencyLevel)
                @case('high')
                    <a href="tel:[YOUR_PHONE]" class="btn btn-danger">🚨 Call Now</a>
                    <a href="[PAYMENT_PORTAL_URL]" class="btn btn-warning">💳 Pay Online</a>
                    @break
                @case('medium')
                    <a href="[PAYMENT_PORTAL_URL]" class="btn btn-warning">💳 Renew Online</a>
                    <a href="tel:[YOUR_PHONE]" class="btn btn-success">📞 Call Us</a>
                    @break
                @default
                    <a href="[PAYMENT_PORTAL_URL]" class="btn btn-success">💳 Renew Online</a>
            @endswitch
        </div>

        <div class="footer">
            <p><strong>{{ $companyName }}</strong></p>
            <p>Professional Mailbox & Package Services</p>
            <p>
                📍 [Your Address] | 📞 [Your Phone] | 🌐 [Your Website]<br>
                📧 [Your Email] | 🕒 Mon-Fri 9AM-6PM, Sat 9AM-3PM
            </p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e9ecef;">
            <p style="font-size: 12px; color: #868e96;">
                This renewal reminder was sent for mailbox #{{ $mailboxNumber }}.<br>
                If you have already renewed, please disregard this message.<br>
                Questions? Contact us directly at [Your Phone] or [Your Email].
            </p>
        </div>
    </div>
</body>
</html>
