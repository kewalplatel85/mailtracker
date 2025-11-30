@extends('layouts.app')
@section('title', 'Print Package Labels')
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
            margin: 0 !important;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }

        .no-print {
            display: none !important;
        }

        nav, header, .navbar, .header, .navigation {
            display: none !important;
        }

        .label-grid {
            display: block !important;
        }

        .label-page {
            width: 4in !important;
            height: 6in !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .label-page + .label-page {
            page-break-before: always !important;
        }

        .label-item {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0.25in !important;
            border: none !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            align-items: center !important;
            text-align: center !important;
            box-shadow: none !important;
            background: white !important;
            box-sizing: border-box !important;
        }

        .label-item + .label-item {
            page-break-before: always !important;
        }

        .package-header {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 6pt;
            margin-bottom: 8pt;
            flex-shrink: 0;
        }

        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 3pt;
            color: #000;
        }

        .package-title {
            font-size: 12pt;
            font-weight: 600;
            color: #000;
        }

        .tracking-section {
            width: 100%;
            margin-bottom: 8pt;
            flex-shrink: 0;
        }

        .tracking-label {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 4pt;
            color: #000;
        }

        .tracking-number {
            font-size: 16pt;
            font-weight: bold;
            color: #000;
            font-family: monospace;
            border: 1px solid #000;
            padding: 3pt 6pt;
            background: #f9f9f9;
            word-break: break-all;
        }

        .customer-section {
            width: 100%;
            margin-bottom: 8pt;
            flex-shrink: 0;
        }

        .customer-name {
            font-size: 18pt;
            font-weight: 600;
            margin-bottom: 4pt;
            color: #000;
            line-height: 1.0;
        }

        .mailbox-info {
            font-size: 14pt;
            color: #000;
            margin-bottom: 3pt;
        }

        .phone-info {
            font-size: 12pt;
            color: #000;
            font-family: monospace;
        }

        .barcode-section {
            width: 100%;
            margin: 6pt 0 0 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            max-height: 1.5in;
        }

        .barcode-section svg {
            width: 2.8in !important;
            height: 0.7in !important;
            max-width: 100% !important;
            max-height: 100% !important;
            print-color-adjust: exact !important;
        }

        .package-header {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 8pt;
            margin-bottom: 8pt;
        }

        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 4pt;
            color: #000;
        }

        .package-title {
            font-size: 12pt;
            font-weight: 600;
            color: #000;
        }

        .tracking-section {
            width: 100%;
            margin-bottom: 8pt;
        }

        .tracking-label {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 3pt;
            color: #000;
        }

        .tracking-number {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            font-family: monospace;
            border: 1px solid #000;
            padding: 3pt 6pt;
            background: #f9f9f9;
            word-break: break-all;
        }

        .customer-section {
            width: 100%;
            margin-bottom: 8pt;
        }

        .customer-name {
            font-size: 20pt;
            font-weight: 600;
            margin-bottom: 4pt;
            color: #000;
            line-height: 1.0;
        }

        .mailbox-info {
            font-size: 14pt;
            color: #000;
            margin-bottom: 3pt;
        }

        .phone-info {
            font-size: 12pt;
            color: #000;
            font-family: monospace;
        }

        .barcode-section {
            width: 100%;
            margin: 5pt 0 8pt 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }

        .barcode-section svg {
            width: 2.8in !important;
            height: 0.8in !important;
            max-width: 100% !important;
            print-color-adjust: exact !important;
        }
    }

    /* Screen styles - completely hidden during print */
    @media screen {
        .label-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px 0;
        }

        .label-item {
            border: 2px solid #000;
            padding: 30px;
            text-align: center;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }
    }

    @media print {
        /* Hide all screen elements completely */
        .label-grid {
            display: none !important;
        }
    }

    .package-header {
        width: 100%;
        text-align: center;
        border-bottom: 1px solid #000;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .company-name {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 4px;
        color: #000;
    }

    .package-title {
        font-size: 14px;
        font-weight: 600;
        color: #000;
    }

    .tracking-section {
        width: 100%;
        margin-bottom: 12px;
    }

    .tracking-label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
        color: #000;
    }

    .tracking-number {
        font-size: 20px;
        font-weight: bold;
        color: #000;
        font-family: monospace;
        border: 1px solid #000;
        padding: 6px 8px;
        background: #f9f9f9;
        word-break: break-all;
    }

    .customer-section {
        width: 100%;
        margin-bottom: 12px;
    }

    .customer-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #000;
        line-height: 1.1;
    }

    .mailbox-info {
        font-size: 16px;
        color: #000;
        margin-bottom: 4px;
    }

    .phone-info {
        font-size: 14px;
        color: #000;
        font-family: monospace;
    }

    .barcode-section {
        width: 100%;
        margin: 15px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
    }

    .barcode-section svg {
        width: 280px;
        height: 60px;
        max-width: 100%;
    }
</style>

<main class="max-w-6xl mx-auto py-6">
    <div class="no-print mb-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('labels.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Back to All Labels
            </a>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-2 border border-orange-300 text-sm font-medium rounded-md text-orange-700 bg-orange-50">
                    Preview Package Labels ({{ count($packages) }})
                </span>
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Print All Package Labels
                </button>
            </div>
        </div>
    </div>

    <div class="print-section">
        @foreach($packages as $package)
            <div class="label-page">
                <div class="label-item">
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

                    <div class="tracking-section">
                        <div class="tracking-label">Tracking Number:</div>
                        <div class="tracking-number">{{ $package->tracking_number }}</div>
                    </div>

                    <div class="customer-section">
                        <div class="customer-name">{{ $package->customer_name }}</div>
                        @if($package->mailbox_number && $package->mailbox_number !== 'N/A')
                            <div class="mailbox-info">Mailbox: {{ $package->mailbox_number }}</div>
                        @endif
                        @if($package->phone_number)
                            <div class="phone-info">{{ $package->phone_number }}</div>
                        @endif
                    </div>

                    <div class="barcode-section">
                        @if(class_exists('Milon\Barcode\Facades\DNS1DFacade'))
                            {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML($package->tracking_number, 'C128', 2.5, 80) !!}
                        @else
                            <div style="height: 80px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-family: monospace; font-size: 24px; background: #fff; color: #000;">
                                *{{ $package->tracking_number }}*
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</main>

@endsection
