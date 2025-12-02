@extends('layouts.app')
@section('title', 'Print Package Label')
@section('content')

<style>
    @page {
        size: 4in 6in;
        margin: 0;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
    }

    @media print {
        @page {
            size: 4in 6in !important;
            margin: 0 !important;
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

        .single-label {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .label-item {
            margin: 0;
            width: 4in;
            height: 6in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #000;
            padding: 0.15in;
            text-align: center;
            background: white;
            box-sizing: border-box;
            position: relative;
        }

        .package-header {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 4pt;
            color: #000;
        }

        .package-title {
            font-size: 14pt;
            font-weight: 600;
            color: #000;
        }

        .tracking-section {
            width: 100%;
            margin-bottom: 12pt;
        }

        .tracking-label {
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 4pt;
            color: #000;
        }

        .tracking-number {
            font-size: 20pt;
            font-weight: bold;
            color: #000;
            font-family: monospace;
            border: 1px solid #000;
            padding: 4pt 8pt;
            background: #f9f9f9;
            word-break: break-all;
        }

        .customer-section {
            width: 100%;
            margin-bottom: 12pt;
        }

        .customer-name {
            font-size: 24pt;
            font-weight: 600;
            margin-bottom: 6pt;
            color: #000;
            line-height: 1.1;
        }

        .mailbox-info {
            font-size: 36pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 4pt;
        }

        .phone-info {
            font-size: 14pt;
            color: #000;
            font-family: monospace;
        }

        .package-status {
            font-size: 12pt;
            font-weight: 600;
            padding: 4pt 12pt;
            background: #e5f3ff;
            border: 1px solid #0066cc;
            color: #0066cc;
            border-radius: 4pt;
            margin-bottom: 8pt;
        }

        .label-footer {
            width: 100%;
            border-top: 1px solid #000;
            padding-top: 6pt;
            font-size: 10pt;
            color: #333;
            min-height: 20pt;
        }

        .barcode-section {
            margin: 2pt 0 8pt 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            flex-grow: 1;
        }

        .barcode-section svg {
            width: 1.8in !important;
            height: 0.4in !important;
            max-width: 75% !important;
            print-color-adjust: exact !important;
        }
    }

    /* Screen styles */
    .label-item {
        width: 400px;
        height: 600px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #000;
        padding: 15px;
        text-align: center;
        background: white;
        box-sizing: border-box;
        position: relative;
        margin: 0 auto;
    }

    .package-header {
        width: 100%;
        text-align: center;
        border-bottom: 1px solid #000;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 4px;
        color: #000;
    }

    .package-title {
        font-size: 18px;
        font-weight: 600;
        color: #000;
    }

    .tracking-section {
        width: 100%;
        margin-bottom: 12px;
    }

    .tracking-label {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
        color: #000;
    }

    .tracking-number {
        font-size: 24px;
        font-weight: bold;
        color: #000;
        font-family: monospace;
        border: 1px solid #000;
        padding: 6px 12px;
        background: #f9f9f9;
        word-break: break-all;
    }

    .customer-section {
        width: 100%;
        margin-bottom: 12px;
    }

    .customer-name {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #000;
        line-height: 1.1;
    }

    .mailbox-info {
        font-size: 48px;
        font-weight: bold;
        color: #000;
        margin-bottom: 6px;
    }

    .phone-info {
        font-size: 18px;
        color: #000;
        font-family: monospace;
    }

    .package-status {
        font-size: 16px;
        font-weight: 600;
        padding: 6px 16px;
        background: #e5f3ff;
        border: 1px solid #0066cc;
        color: #0066cc;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .label-footer {
        width: 100%;
        border-top: 1px solid #000;
        padding-top: 8px;
        font-size: 14px;
        color: #333;
        min-height: 30px;
    }

    .barcode-section {
        margin: 20px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .barcode-section svg {
        width: 180px;
        height: 40px;
        max-width: 75%;
    }
</style>

<main class="max-w-4xl mx-auto py-6">
    <!-- Navigation (Hidden during print) -->
    <div class="no-print mb-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('labels.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Back to All Labels
            </a>
            <div class="flex space-x-2">
                @if(isset($package->is_preview) && $package->is_preview)
                    <span class="inline-flex items-center px-3 py-2 border border-orange-300 text-sm font-medium rounded-md text-orange-700 bg-orange-50">
                        Preview Package Label
                    </span>
                @endif
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Print Package Label
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Package Label Section -->
    <div class="print-section">
        <div class="single-label">
            <div class="label-item">
                <!-- Header -->
                <div class="package-header">
                    <div class="company-name">
                        @if(isset($package->company) && $package->company)
                            {{ $package->company }}
                        @else
                            Mail All Center
                        @endif
                    </div>
                    <div class="package-title">Package Details</div>
                </div>

                <!-- Tracking Number Section -->
                <div class="tracking-section">
                    <div class="tracking-label">Tracking Number:</div>
                    <div class="tracking-number">{{ $package->tracking_number }}</div>
                </div>

                <!-- Customer Information -->
                <div class="customer-section">
                    <div class="customer-name">{{ $package->customer_name }}</div>
                    @if($package->mailbox_number && $package->mailbox_number !== 'N/A')
                        <div class="mailbox-info">Mailbox: {{ $package->mailbox_number }}</div>
                    @endif
                    @if($package->phone_number)
                        <div class="phone-info">{{ $package->phone_number }}</div>
                    @endif
                </div>

                <!-- Barcode Section -->
                <div class="barcode-section">
                    @if(class_exists('Milon\Barcode\Facades\DNS1DFacade'))
                        {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML($package->tracking_number, 'C128', 1.5, 60) !!}
                    @else
                        <div style="height: 60px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-family: monospace; font-size: 20px; background: #fff; color: #000;">
                            *{{ $package->tracking_number }}*
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
