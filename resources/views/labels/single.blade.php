@extends('layouts.app')
@section('title', 'Print Single Storage Label')
@section('content')

<style>
    @page {
        size: 4in 6in;
        margin: 0.1in;
    }

    @media print {
        html, body {
            width: 4in;
            height: 6in;
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
            width: 3.8in;
            height: 5.8in;
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
            width: 3.8in;
            height: 5.8in;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 2px solid #000;
            padding: 0.2in;
            text-align: center;
            background: white;
            box-sizing: border-box;
            position: relative;
        }

        .label-number {
            font-size: 72pt;
            font-weight: bold;
            margin-bottom: 24pt;
            color: #000;
            line-height: 1;
        }

        .label-customer {
            font-size: 28pt;
            font-weight: 600;
            margin-bottom: 18pt;
            color: #000;
            line-height: 1.1;
            word-wrap: break-word;
            max-width: 100%;
        }

        .label-phone {
            font-size: 24pt;
            margin-bottom: 0;
            color: #000;
            line-height: 1.1;
        }

        .label-expiry {
            font-size: 12pt;
            color: #333;
            position: absolute;
            bottom: 12pt;
            line-height: 1.1;
        }
    }

        .label-number {
            font-size: 72px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #000;
            line-height: 1;
        }

        .label-customer {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #000;
            line-height: 1.1;
        }

        .label-phone {
            font-size: 30px;
            margin-bottom: 20px;
            color: #000;
            line-height: 1.1;
        }

        .label-expiry {
            font-size: 18px;
            color: #666;
            margin-top: 15px;
            line-height: 1.2;
        }
    }
</style>

<main class="max-w-4xl mx-auto py-6">
    <!-- Navigation (Hidden during print) -->
    <div class="no-print mb-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('labels.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Back to All Labels
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                Print Storage Label
            </button>
        </div>
    </div>

    <!-- Printable Single Label Section -->
    <div class="print-section">
        <div class="single-label">
            <div class="label-item">
                <div class="label-number">{{ $package->mailbox_number ?: '' }}</div>
                <div class="label-customer">{{ $package->customer_name }}</div>
                <div class="label-phone">{{ $package->phone_number }}</div>
                <div class="label-expiry">
                    Expires: {{ $package->created_at->addDays(30)->format('n/j/Y') }}
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
