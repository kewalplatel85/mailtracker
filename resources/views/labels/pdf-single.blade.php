<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Storage Label</title>
    <style>
        @page {
            size: 4in 6in;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: white;
        }

        .label-page {
            width: 4in;
            height: 6in;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 2px solid #000;
            box-sizing: border-box;
            padding: 0.3in;
            position: relative;
            background: white;
        }

        .mailbox-number {
            font-size: 72pt;
            font-weight: bold;
            margin-bottom: 24pt;
            color: #000;
            line-height: 0.9;
        }

        .customer-name {
            font-size: 28pt;
            font-weight: 600;
            margin-bottom: 18pt;
            color: #000;
            line-height: 1.0;
            word-wrap: break-word;
            max-width: 3.4in;
        }

        .phone-number {
            font-size: 24pt;
            margin-bottom: 0;
            color: #000;
            line-height: 1.0;
        }

        .expiry-date {
            font-size: 12pt;
            color: #333;
            position: absolute;
            bottom: 12pt;
            left: 50%;
            transform: translateX(-50%);
            line-height: 1.0;
        }
    </style>
</head>
<body>
    <div class="label-page">
        <div class="mailbox-number">{{ $package->mailbox_number ?: '' }}</div>
        <div class="customer-name">{{ $package->customer_name }}</div>
        <div class="phone-number">{{ $package->phone_number }}</div>
        <div class="expiry-date">
            Expires: {{ $package->created_at->addDays(30)->format('n/j/Y') }}
        </div>
    </div>
</body>
</html>
