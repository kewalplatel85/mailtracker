@extends('layouts.app')
@section('title', 'Print Multiple Storage Labels')
@section('content')

<style>
    @page {
        size: 8.5in 11in;
        margin: 0.25in;
    }

    @media print {
        html, body {
            width: 8.5in;
            height: 11in;
            margin: 0;
            padding: 0;
            overflow: hidden;
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
            width: 8in;
            height: 10.5in;
        }
        .no-print {
            display: none !important;
        }

        .labels-grid {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .label-item {
            width: 3.8in;
            height: 5.2in;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 2px solid #000;
            margin: 0.1in;
            padding: 0.2in;
            text-align: center;
            background: white;
            box-sizing: border-box;
            position: relative;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .label-number {
            font-size: 64pt;
            font-weight: bold;
            margin-bottom: 20pt;
            color: #000;
            line-height: 1;
        }

        .label-customer {
            font-size: 24pt;
            font-weight: 600;
            margin-bottom: 16pt;
            color: #000;
            line-height: 1.1;
            word-wrap: break-word;
            max-width: 100%;
        }

        .label-phone {
            font-size: 20pt;
            margin-bottom: 0;
            color: #000;
            line-height: 1.1;
        }

        .label-expiry {
            font-size: 10pt;
            color: #333;
            position: absolute;
            bottom: 8pt;
            line-height: 1.1;
        }
    }

    /* Screen styles */
    .labels-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        width: 100%;
    }

    .label-item {
        width: 100%;
        height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border: 2px solid #ddd;
        padding: 20px;
        text-align: center;
        background: white;
        box-sizing: border-box;
        position: relative;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .label-number {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #000;
        line-height: 1;
    }

    .label-customer {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #000;
        line-height: 1.1;
    }

    .label-phone {
        font-size: 20px;
        margin-bottom: 15px;
        color: #000;
        line-height: 1.1;
    }

    .label-expiry {
        font-size: 14px;
        color: #666;
        margin-top: auto;
        line-height: 1.1;
    }
</style>

<main class="max-w-6xl mx-auto py-6">
    <!-- Navigation (Hidden during print) -->
    <div class="no-print mb-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('labels.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Back to All Labels
            </a>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-2 border border-orange-300 text-sm font-medium rounded-md text-orange-700 bg-orange-50">
                    Preview Labels ({{ count($packages) }})
                </span>
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Print All Labels
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Multiple Labels Section -->
    <div class="print-section">
        <div class="labels-grid">
            @foreach($packages as $package)
                <div class="label-item">
                    <div class="label-number">{{ $package->mailbox_number ?: 'N/A' }}</div>
                    <div class="label-customer">{{ $package->customer_name }}</div>
                    @if(isset($package->phone_number) && $package->phone_number)
                        <div class="label-phone">{{ $package->phone_number }}</div>
                    @elseif(isset($package->tracking_number) && $package->tracking_number)
                        <div class="label-phone">{{ $package->tracking_number }}</div>
                    @endif
                    <div class="label-expiry">
                        @if(isset($package->is_preview) && $package->is_preview)
                            Preview Label - {{ now()->format('n/j/Y') }}
                        @elseif(isset($package->due_date) && $package->due_date && $package->due_date !== 'N/A')
                            Expires: {{ \Carbon\Carbon::parse($package->due_date)->format('n/j/Y') }}
                        @else
                            Expires: {{ is_string($package->created_at) ? \Carbon\Carbon::parse($package->created_at)->addDays(30)->format('n/j/Y') : $package->created_at->addDays(30)->format('n/j/Y') }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>

@endsection
