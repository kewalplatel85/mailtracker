@extends('layouts.app')
@section('title', 'Print Storage Labels')
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

        /* Each label fills the 3.5x5 inch page */
        .label-grid {
            display: block !important;
        }

        .label-item {
            page-break-after: always;
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

        .label-item:last-child {
            page-break-after: auto;
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

    /* Screen styles */
    .label-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .label-item {
        border: 2px solid #000;
        padding: 30px;
        text-align: center;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
    }

    .label-item.selected {
        background-color: #e3f2fd;
        border-color: #1976d2;
    }

    .label-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        transform: scale(1.5);
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
        margin-bottom: 12px;
        color: #000;
        line-height: 1.1;
    }

    .label-expiry {
        font-size: 14px;
        color: #666;
        margin-top: 10px;
        line-height: 1.2;
    }

    .print-options {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<main class="max-w-7xl mx-auto py-6">
    <!-- Filter Section -->
    <div class="no-print mb-6">
        <!-- Print Instructions Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Print Setup Instructions</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p><strong>When printing:</strong> In your browser's print dialog, click <strong>"More settings"</strong> → <strong>"Paper size"</strong> → Select <strong>"Custom"</strong> → Enter <strong>4 inches × 6 inches</strong></p>
                        <p class="mt-1">Set <strong>Margins</strong> to <strong>"Minimum"</strong> and <strong>Scale</strong> to <strong>"100%"</strong> for perfect label printing.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Storage Box Label Printing</h1>

            <form method="GET" action="{{ route('labels.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="mailbox_number" class="block text-sm font-medium text-gray-700">Mailbox Number</label>
                    <input type="text" name="mailbox_number" id="mailbox_number"
                           value="{{ request('mailbox_number') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Enter mailbox number">
                </div>

                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name"
                           value="{{ request('customer_name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Enter customer name">
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number"
                           value="{{ request('phone_number') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Enter phone number">
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Filter Packages
                    </button>
                    <a href="{{ route('labels.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Clear Filters
                    </a>
                </div>
            </form>

            <!-- Print Options -->
            @if($packages->count() > 0)
            <div class="print-options no-print mt-6">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="selectAll" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="selectAll" class="text-sm font-medium text-gray-700">Select All</label>
                    </div>

                    <button type="button" onclick="printSelectedLabels()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        Print Selected Labels (Perfect 4" × 6")
                    </button>

                    <span class="text-sm text-gray-600">
                        Perfect 4" × 6" label printing with step-by-step instructions
                    </span>
                </div>
            </div>
            @endif

            <div class="mt-4 text-sm text-gray-600">
                <strong>{{ $packages->count() }}</strong> packages found
                @if(request()->hasAny(['mailbox_number', 'customer_name', 'phone_number']))
                    (filtered)
                @endif
            </div>
        </div>
    </div>

    <!-- Printable Labels Section -->
    <div class="print-section">
        @if($packages->count() > 0)
            <div class="label-grid" id="labelGrid">
                @foreach($packages as $package)
                    <div class="label-item" data-package-id="{{ $package->id }}">
                        <input type="checkbox" class="label-checkbox no-print" value="{{ $package->id }}">
                        <div class="label-number">{{ $package->mailbox_number ?: '' }}</div>
                        <div class="label-customer">{{ $package->customer_name }}</div>
                        <div class="label-phone">{{ $package->phone_number }}</div>
                        <div class="label-expiry">
                            Expires: {{ $package->created_at->addDays(30)->format('n/j/Y') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg">No packages found matching your criteria.</div>
                <a href="{{ route('labels.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                    View All Packages
                </a>
            </div>
        @endif
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const labelCheckboxes = document.querySelectorAll('.label-checkbox');

    // Select all functionality
    selectAllCheckbox?.addEventListener('change', function() {
        labelCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            updateLabelSelection(checkbox);
        });
    });

    // Individual checkbox functionality
    labelCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateLabelSelection(this);
            updateSelectAllState();
        });
    });

    function updateLabelSelection(checkbox) {
        const labelItem = checkbox.closest('.label-item');
        if (checkbox.checked) {
            labelItem.classList.add('selected');
        } else {
            labelItem.classList.remove('selected');
        }
    }

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.label-checkbox:checked').length;
        const totalCount = labelCheckboxes.length;

        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }
});

function printSelectedLabels() {
    const selectedCheckboxes = document.querySelectorAll('.label-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one label to print.');
        return;
    }

    // Create print window with proper sizing
    const printWindow = window.open('', '_blank', 'width=400,height=600');

    let labelHtml = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Storage Labels - 4" x 6"</title>
        <style>
            @page {
                size: 4in 6in;
                margin: 0;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                width: 4in;
                height: 6in;
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                background: white;
            }

            @media print {
                html, body {
                    width: 4in !important;
                    height: 6in !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }

                .no-print {
                    display: none !important;
                }
            }

            .label-item {
                width: 4in;
                height: 6in;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                border: 2px solid #000;
                padding: 0.3in;
                box-sizing: border-box;
                position: relative;
                background: white;
                page-break-after: always;
            }

            .label-item:last-child {
                page-break-after: avoid;
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
                text-align: center;
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

            .print-instructions {
                position: fixed;
                top: 10px;
                left: 10px;
                right: 10px;
                background: #e3f2fd;
                border: 2px solid #1976d2;
                padding: 15px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 1000;
            }

            @media print {
                .print-instructions {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="print-instructions no-print">
            <h3>🎯 Perfect Print Setup:</h3>
            <p><strong>1.</strong> Press <strong>Ctrl+P</strong> to open print dialog</p>
            <p><strong>2.</strong> Click <strong>"More settings"</strong> → <strong>"Paper size"</strong></p>
            <p><strong>3.</strong> Select <strong>"Custom"</strong> → Enter <strong>"4" x "6"</strong> inches</p>
            <p><strong>4.</strong> Set <strong>Margins</strong> to <strong>"None"</strong> or <strong>"Minimum"</strong></p>
            <p><strong>5.</strong> Set <strong>Scale</strong> to <strong>"100%"</strong></p>
            <p><strong>6.</strong> Click <strong>"Print"</strong> - Perfect labels every time!</p>
            <button onclick="window.print()" style="background: #1976d2; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin-top: 10px; cursor: pointer;">🖨️ Start Printing</button>
        </div>
    `;

    // Add selected labels
    selectedCheckboxes.forEach((checkbox) => {
        const labelItem = checkbox.closest('.label-item');
        const number = labelItem.querySelector('.label-number').textContent.trim();
        const customer = labelItem.querySelector('.label-customer').textContent.trim();
        const phone = labelItem.querySelector('.label-phone').textContent.trim();
        const expiry = labelItem.querySelector('.label-expiry').textContent.trim();

        labelHtml += `
        <div class="label-item">
            <div class="mailbox-number">${number}</div>
            <div class="customer-name">${customer}</div>
            <div class="phone-number">${phone}</div>
            <div class="expiry-date">${expiry}</div>
        </div>
        `;
    });

    labelHtml += `
    </body>
    </html>
    `;

    printWindow.document.write(labelHtml);
    printWindow.document.close();
    printWindow.focus();
}
</script>

@endsection
