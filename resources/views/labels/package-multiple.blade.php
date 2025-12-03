@extends('layouts.app')
@section('title', 'Print Package Labels')
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

        .label-item + .label-item {
            page-break-before: always !important;
        }

        .tracking-section {
            width: 100%;
            margin-bottom: 12pt;
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
        .barcode-section svg {
            width: 1.8in !important;
            height: 0.4in !important;
            max-width: 75% !important;
            print-color-adjust: exact !important;
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
            font-size: 36pt !important;
            font-weight: bold !important;
            color: #000 !important;
            margin-bottom: 4pt !important;
        }

        .phone-info {
            font-size: 14pt;
            color: #000;
            font-family: monospace;
        }

        .tracking-number {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            font-family: monospace;
            border: 1px solid #000;
            padding: 6pt 12pt;
            background: #f9f9f9;
            word-break: break-all;
        }
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

    <div class="print-section">
        @foreach($packages as $package)
            <div class="label-item w-full h-[37.5rem] flex flex-col justify-between items-center border border-black p-4 text-center bg-white mx-auto mb-5">
                <!-- Header -->
                <div class="package-header w-full text-center border-b border-black pb-2 mb-3">
                    <div class="text-2xl font-bold mb-1 text-black">
                        @if(isset($package->company) && $package->company)
                            {{ $package->company }}
                        @else
                            Mail All Center
                        @endif
                    </div>
                    <div class="text-lg font-semibold text-black">Package Details</div>
                </div>

                <!-- Tracking Number Section -->
                <div class="tracking-section">
                    <div class="text-base font-semibold mb-1 text-black">Tracking Number:</div>
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
                <div class="barcode-section my-5 flex justify-center items-center w-full">
                    @if(class_exists('Milon\Barcode\Facades\DNS1DFacade'))
                        <div class="w-45 h-10 max-w-3/4">
                            {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML($package->tracking_number, 'C128', 1.5, 60) !!}
                        </div>
                    @else
                        <div class="h-15 border-2 border-black flex items-center justify-center font-mono text-xl bg-white text-black">
                            *{{ $package->tracking_number }}*
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</main>

@endsection
