@extends('layouts.app')
@section('title', 'Print Package Labels')
@section('content')

<style>
    @page {
        size: 4in 6in;
        margin: 0;
    }

    @media print {
        @page {
            size: 4in 6in !important;
            margin: 0 !important;
        }
        
        html, body {
            width: 4in !important;
            height: 6in !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }

        .no-print {
            display: none !important;
        }

        .label-container {
            width: 4in;
            height: 6in;
            padding: 0.2in;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            background: white;
            box-sizing: border-box;
            page-break-after: always;
        }

        .label-container:last-child {
            page-break-after: avoid;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }

        .company {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 4pt;
        }

        .subtitle {
            font-size: 14pt;
            font-weight: 600;
        }

        .tracking {
            margin-bottom: 12pt;
        }

        .tracking-label {
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 4pt;
        }

        .tracking-value {
            font-size: 18pt;
            font-weight: bold;
            font-family: monospace;
            border: 2px solid #000;
            padding: 6pt;
            background: #f9f9f9;
            word-break: break-all;
        }

        .customer {
            margin-bottom: 12pt;
        }

        .customer-name {
            font-size: 24pt;
            font-weight: 600;
            margin-bottom: 8pt;
            line-height: 1.1;
        }

        .mailbox {
            font-size: 36pt;
            font-weight: bold;
            margin-bottom: 6pt;
        }

        .phone {
            font-size: 14pt;
            font-family: monospace;
        }

        .barcode {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8pt;
        }

        .barcode svg {
            width: 1.8in !important;
            height: 0.5in !important;
            max-width: 90% !important;
        }
    }

    /* Screen styles */
    .label-container {
        width: 400px;
        height: 600px;
        padding: 20px;
        margin: 20px auto;
        border: 2px solid #000;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: center;
        background: white;
        box-sizing: border-box;
    }

    .header {
        border-bottom: 2px solid #000;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .company {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .subtitle {
        font-size: 18px;
        font-weight: 600;
    }

    .tracking {
        margin-bottom: 16px;
    }

    .tracking-label {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .tracking-value {
        font-size: 24px;
        font-weight: bold;
        font-family: monospace;
        border: 2px solid #000;
        padding: 8px 12px;
        background: #f9f9f9;
        word-break: break-all;
    }

    .customer {
        margin-bottom: 16px;
    }

    .customer-name {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 12px;
        line-height: 1.1;
    }

    .mailbox {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .phone {
        font-size: 18px;
        font-family: monospace;
    }

    .barcode {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
    }

    .barcode svg {
        width: 180px;
        height: 50px;
        max-width: 90%;
    }
</style>

<main class="max-w-6xl mx-auto py-6">
    <div class="no-print mb-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('labels.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Back to All Labels
            </a>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50">
                    {{ count($packages) }} Package{{ count($packages) > 1 ? 's' : '' }} Selected
                </span>
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Print All Labels
                </button>
            </div>
        </div>
    </div>

    @foreach($packages as $package)
        <div class="label-container">
            <div class="header">
                <div class="company">
                    @if(isset($package->company) && $package->company)
                        {{ $package->company }}
                    @else
                        Mail All Center
                    @endif
                </div>
                <div class="subtitle">Package Details</div>
            </div>

            <div class="tracking">
                <div class="tracking-label">Tracking Number:</div>
                <div class="tracking-value">{{ $package->tracking_number }}</div>
            </div>

            <div class="customer">
                <div class="customer-name">{{ $package->customer_name }}</div>
                @if($package->mailbox_number && $package->mailbox_number !== 'N/A')
                    <div class="mailbox">Mailbox: {{ $package->mailbox_number }}</div>
                @endif
                @if($package->phone_number)
                    <div class="phone">{{ $package->phone_number }}</div>
                @endif
            </div>

            <div class="barcode">
                @if(class_exists('Milon\Barcode\Facades\DNS1DFacade'))
                    {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML($package->tracking_number, 'C128', 1.5, 60) !!}
                @else
                    <div style="height: 60px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-family: monospace; font-size: 20px; background: #fff; color: #000;">
                        *{{ $package->tracking_number }}*
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</main>

@endsection