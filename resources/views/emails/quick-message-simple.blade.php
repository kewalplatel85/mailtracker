<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message from {{ $companyName ?? 'Mail Center' }}</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;">
        <h2>{{ $companyName ?? 'Mail Center' }}</h2>

        <p><strong>Dear {{ $customerName ?? 'Customer' }},</strong></p>
        <p><small>Mailbox #{{ $mailboxNumber ?? 'N/A' }}</small></p>

        <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
            {!! nl2br(e($messageContent ?? 'No message content')) !!}
        </div>

        <hr style="margin: 20px 0;">
        <p style="font-size: 12px; color: #666;">
            This email was sent regarding your mailbox #{{ $mailboxNumber ?? 'N/A' }}.<br>
            If you have any questions, please contact us directly.
        </p>
    </div>
</body>
</html>
