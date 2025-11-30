@extends('layouts.app')
@section('title', 'Print Package Label')
@section('content')

<style>
    @page {
        size: 4in 6in;
        margin: 0.1in;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
    }

    @media print {
        @page {
            size: 4in 6in !important;
            margin: 0.1in !important;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            width: 4in !important;
            height: 6in !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden;
            background: white !important;
        }

        body * {
            visibility: hidden;
        }
        .print-section, .print-section * {
            visibility: visible;
        }
        .print-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 4in;
            height: 6in;
            overflow: hidden;
        }
        .no-print {
            display: none !important;
        }

        .label-item {
            margin: 0;
            width: 3.8in;
            height: 5.8in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            border: 2px solid #000;
            padding: 0.15in;
            text-align: center;
            background: white;
            box-sizing: border-box;
            position: relative;
        }

        .package-header {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 8pt;
            color: #000;
            line-height: 1;
        }

        .package-subtitle {
            font-size: 12pt;
            margin-bottom: 15pt;
            color: #000;
            line-height: 1;
        }

        .tracking-section {
            margin-bottom: 15pt;
            text-align: center;
        }

        .tracking-label {
            font-size: 10pt;
            margin-bottom: 5pt;
            color: #000;
        }

        .tracking-number {
            font-size: 20pt;
            font-weight: bold;
            color: #000;
            font-family: monospace;
            border: 1px solid #000;
            padding: 5pt;
            background: #f9f9f9;
            margin-bottom: 15pt;
        }

        .customer-name {
            font-size: 24pt;
            font-weight: 600;
            margin-bottom: 10pt;
            color: #000;
            line-height: 1.0;
        }

        .mailbox-info {
            font-size: 18pt;
            margin-bottom: 15pt;
            color: #000;
            line-height: 1.0;
        }

        .barcode-section {
            margin: 5pt 0 15pt 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            flex-grow: 1;
        }

        .barcode-section svg {
            width: 2.8in !important;
            height: 0.8in !important;
            max-width: 100% !important;
        }
    }

    /* Screen styles */
    .label-item {
        border: 2px solid #000;
        padding: 30px;
        text-align: center;
        min-height: 500px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        margin: 0 auto;
        max-width: 400px;
    }

    .package-header {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #000;
        line-height: 1;
    }

    .package-subtitle {
        font-size: 14px;
        margin-bottom: 15px;
        color: #000;
        line-height: 1;
    }

    .tracking-section {
        margin-bottom: 15px;
        text-align: center;
    }

    .tracking-label {
        font-size: 12px;
        margin-bottom: 5px;
        color: #000;
    }

    .tracking-number {
        font-size: 24px;
        font-weight: bold;
        color: #000;
        font-family: monospace;
        border: 1px solid #000;
        padding: 8px;
        background: #f9f9f9;
        margin-bottom: 15px;
        word-break: break-all;
    }

    .customer-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #000;
        line-height: 1.1;
    }

    .mailbox-info {
        font-size: 18px;
        margin-bottom: 12px;
        color: #000;
        line-height: 1.1;
    }

    .barcode-section {
        margin: 20px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .barcode-section svg {
        width: 280px;
        height: 60px;
        max-width: 100%;
    }
</style>

<main class="max-w-4xl mx-auto py-6">
    <!-- Navigation -->
    <div class="no-print mb-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('labels.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Back to All Labels
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                Print Package Label
            </button>
        </div>
    </div>

    <!-- Printable Package Label -->
    <div class="print-section">
        <div class="label-item">
            <div class="package-header">Mail All Center</div>
            <div class="package-subtitle">Package Details</div>

            <div class="tracking-section">
                <div class="tracking-label">Tracking Number:</div>
                <div class="tracking-number">TEST12345</div>
            </div>

            <div class="customer-name">John Doe</div>
            <div class="mailbox-info">Mailbox: 123</div>
            <div class="mailbox-info">(555) 123-4567</div>

            <div class="barcode-section">
                {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML('TEST12345', 'C128', 2.5, 80) !!}
            </div>
        </div>
    </div>
</main>

@endsection
