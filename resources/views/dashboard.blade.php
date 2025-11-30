@extends('layouts.app')
@section('title','Dashboard')

@section('content')

@section('content')
<div class="min-h-screen bg-gray-50 py-4">
    <div class="max-w-full mx-auto px-6 sm:px-8 lg:px-12">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">📬</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Mailboxes</p>
                        <p class="text-2xl font-semibold text-gray-900">345</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">📦</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">With Packages</p>
                        <p class="text-2xl font-semibold text-gray-900">42</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">🚚</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Packages</p>
                        <p class="text-2xl font-semibold text-gray-900">89</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: 2 Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">

            <!-- Left Column: Package Entry Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <!-- Form Header -->
                    <div class="px-3 py-2 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">📦 Package Entry</h3>
                        <p class="text-sm text-gray-500">Scan and register new packages</p>
                    </div>

                    <!-- CSV Upload Section -->
                    <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center space-x-3">
                                <input type="file" name="file" accept=".csv,.xlsx" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 whitespace-nowrap">
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Package Entry Form -->
                    <div class="p-3">
                        <form id="packageForm" class="space-y-2">
                            @csrf
                            <!-- Tab Selection -->
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" class="tab-btn active px-3 py-1 text-sm font-medium rounded-md bg-blue-600 text-white">
                                    Current Clients
                                </button>
                                <button type="button" class="tab-btn px-3 py-1 text-sm font-medium rounded-md bg-gray-200 text-gray-700">
                                    New Clients
                                </button>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mailbox #</label>
                                <input type="text" name="mailbox_number" required
                                       class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Customer Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                <input type="text" name="customer_name" required
                                       class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Package Count -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Package Count</label>
                                <input type="number" name="package_count" value="1" min="1" required
                                       class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Incoming">Incoming</option>
                                    <option value="Ready for Pickup">Ready for Pickup</option>
                                    <option value="Picked Up">Picked Up</option>
                                </select>
                            </div>

                            <!-- Tracking Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                                <textarea name="tracking_number" id="trackingInput" rows="2" placeholder="Enter tracking numbers (one per line)"
                                          class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Enter one tracking number per line</p>
                            </div>

                            <!-- Real-time Tracking Preview -->
                            <div id="trackingPreview" class="hidden">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Package Preview</label>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" onclick="printAllPreviewLabels()" class="text-xs px-2 py-1 bg-purple-600 text-white rounded hover:bg-purple-700">Print All Labels</button>
                                        <button type="button" id="clearPreview" class="text-xs text-red-600 hover:text-red-800">Clear All</button>
                                    </div>
                                </div>
                                <div id="previewList" class="space-y-2 max-h-32 overflow-y-auto"></div>
                            </div>

                            <!-- SMS Message -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SMS Message</label>
                                <textarea name="sms_message" rows="2"
                                          placeholder="Hi, this is Mail All Center. You have a package ready for pickup. Thanks!"
                                          class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            <!-- Package Images -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Package Images</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center">
                                    <input type="file" id="package_images" name="package_images[]" multiple accept="image/*" class="hidden">
                                    <div class="text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="mt-4 space-x-2">
                                            <button type="button" onclick="document.getElementById('package_images').click()"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                📁 Upload Files
                                            </button>
                                            <button type="button" id="cameraBtn"
                                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                                📷 Use Camera
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="imagePreview" class="mt-4 grid grid-cols-3 gap-2"></div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                💾 Save Package & Send SMS
                            </button>

                            <!-- Tracking Numbers Display (After Submission) -->
                            <div id="trackingDisplay" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-green-800">✅ Packages Saved Successfully!</h4>
                                    <div class="space-x-2">
                                        <button type="button" id="printAllLabels" class="hidden px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            🖨️ Print All Labels
                                        </button>
                                        <button type="button" id="clearTracking" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                            🗑️ Clear
                                        </button>
                                    </div>
                                </div>
                                <div id="trackingList" class="space-y-2"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Mailbox Grid -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-lg shadow">
                    <!-- Grid Header -->
                    <div class="px-3 py-2 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">📬 Mailbox Grid</h3>
                            <p class="text-sm text-gray-500">Visual mailbox management</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" id="searchMailbox" placeholder="Search mailbox..."
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- View Toggle -->
                            <div class="flex bg-gray-200 rounded-md">
                                <button class="view-toggle active px-3 py-2 text-sm font-medium rounded-l-md bg-blue-600 text-white" data-view="grid">
                                    Grid
                                </button>
                                <button class="view-toggle px-3 py-2 text-sm font-medium rounded-r-md text-gray-700" data-view="table">
                                    Table
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mailbox Grid Content -->
                    <div class="p-3">
                        <!-- Pagination Info -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm text-gray-500">
                                Showing <span id="currentStart">1</span>-<span id="currentEnd">40</span> of <span id="totalMailboxes">345</span> mailboxes
                            </div>
                            <div class="flex items-center space-x-2">
                                <button id="prevPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50">
                                    Previous
                                </button>
                                <span id="pageInfo" class="text-sm text-gray-600">Page 1 of 9</span>
                                <button id="nextPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50">
                                    Next
                                </button>
                            </div>
                        </div>

                        <!-- Grid View -->
                        <div id="gridView" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-2 min-h-[650px] overflow-hidden min-w-0">
                            @if(!empty($data) && count($data) > 7)
                                @foreach(array_slice($data, 7) as $index => $row)
                                    @if(!empty($row[0]))
                                        @php
                                            $mailboxNumber = trim($row[0]);
                                            $customerName = isset($row[3]) ? trim($row[3]) : '';
                                            $phoneNumber = isset($row[4]) ? trim($row[4]) : '';
                                            $sizeType = isset($row[1]) ? trim($row[1]) : '';
                                            $status = isset($row[2]) ? trim($row[2]) : '';
                                            $email = isset($row[8]) ? trim($row[8]) : '';
                                            $dateClose = isset($row[5]) ? trim($row[5]) : '';
                                            $term = isset($row[6]) ? trim($row[6]) : '';
                                            $dueDate = isset($row[7]) ? trim($row[7]) : '';
                                            $packageCount = \App\Models\Package::where('mailbox_number', $mailboxNumber)->where('status', 'Ready for Pickup')->count();
                                        @endphp
                                        <div class="mailbox-item group relative aspect-square bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:shadow-md transition-all cursor-pointer min-w-0"
                                             data-mailbox="{{ $mailboxNumber }}"
                                             data-customer="{{ $customerName }}"
                                             data-phone="{{ $phoneNumber }}"
                                             data-size-type="{{ $sizeType }}"
                                             data-status="{{ $status }}"
                                             data-email="{{ $email }}"
                                             data-date-close="{{ $dateClose }}"
                                             data-term="{{ $term }}"
                                             data-due-date="{{ $dueDate }}"
                                             data-packages="{{ $packageCount }}">

                                            @if($packageCount > 0)
                                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center z-10 shadow-md">
                                                    {{ $packageCount }}
                                                </div>
                                            @endif

                                            <div class="h-full flex flex-col items-center justify-center p-2 text-center min-w-0 {{ $packageCount > 0 ? 'bg-blue-50 border-blue-500' : '' }}">
                                                <div class="text-lg font-bold text-gray-900 mb-1">{{ $mailboxNumber }}</div>
                                                <div class="text-xs text-gray-600 leading-tight truncate w-full">{{ Str::limit($customerName, 12) }}</div>
                                                @if($packageCount > 0)
                                                    <div class="text-xs text-blue-600 font-semibold mt-1">📦 {{ $packageCount }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No customer data found</h3>
                                        <p class="text-gray-500">Upload a CSV file to see mailbox information</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Table View (Hidden by default) -->
                        <div id="tableView" class="hidden max-h-[650px] overflow-y-auto">
                            <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mailbox</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packages</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Table rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mailbox Details Modal -->
<div id="mailboxModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Modal Backdrop -->
    <div id="modalBackdrop" class="fixed inset-0" style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(6px);"></div>
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 relative z-10">
        <div class="relative rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-300" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px);">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Mailbox Details</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="space-y-4">
                <!-- Modal content will be populated by JavaScript -->
            </div>
            <div id="packageDetails" class="hidden mt-6">
                <h4 class="font-medium text-gray-900 mb-3">Package Details:</h4>
                <div id="packageList" class="space-y-2">
                    <!-- Package details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Toast notification function
function showToast(message, type = 'success') {
    const toast = $(`
        <div class="toast animate-fade-in-down bg-white shadow-lg rounded-lg border-l-4 ${
            type === 'success' ? 'border-green-500' :
            type === 'error' ? 'border-red-500' :
            type === 'warning' ? 'border-yellow-500' : 'border-blue-500'
        } p-4 mb-3 max-w-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-lg">${
                        type === 'success' ? '✅' :
                        type === 'error' ? '❌' :
                        type === 'warning' ? '⚠️' : 'ℹ️'
                    }</span>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <button class="ml-4 text-gray-400 hover:text-gray-600" onclick="$(this).closest('.toast').remove()">
                    <span class="sr-only">Close</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `);

    $('#toast-container').append(toast);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        toast.fadeOut(300, () => toast.remove());
    }, 5000);
}

// Global variables for mailbox filtering
let allMailboxes = [];
let filteredMailboxes = [];
let currentPage = 1;
let itemsPerPage = 40;

// Global filter functions
function filterToMailbox(mailboxNumber) {
    filteredMailboxes = allMailboxes.filter(item => {
        return $(item).data('mailbox').toString() === mailboxNumber;
    });
    currentPage = 1;
    updatePagination();
}

function resetMailboxFilter() {
    const searchQuery = $('#searchMailbox').val().toLowerCase();
    if (searchQuery) {
        // Keep search filter if active
        filteredMailboxes = allMailboxes.filter(item => {
            const mailbox = $(item).data('mailbox').toString().toLowerCase();
            const customer = $(item).data('customer').toString().toLowerCase();
            return mailbox.includes(searchQuery) || customer.includes(searchQuery);
        });
    } else {
        // Show all mailboxes
        filteredMailboxes = allMailboxes;
    }
    currentPage = 1;
    updatePagination();
}

function updatePagination() {
    const totalPages = Math.ceil(filteredMailboxes.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(currentPage * itemsPerPage, filteredMailboxes.length);

    $('#currentStart').text(start);
    $('#currentEnd').text(end);
    $('#totalMailboxes').text(filteredMailboxes.length);
    $('#pageInfo').text(`Page ${currentPage} of ${totalPages}`);

    $('#prevPage').prop('disabled', currentPage === 1);
    $('#nextPage').prop('disabled', currentPage === totalPages);

    showPage();
}

function showPage() {
    $('.mailbox-item').hide();
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    filteredMailboxes.slice(start, end).forEach(item => {
        $(item).show();
    });
}

// Confirmation toast function
function showConfirmationToast(title, message, onConfirm, onCancel = null) {
    const confirmToast = $(`
        <div class="toast animate-fade-in-down bg-white shadow-xl rounded-lg border border-gray-300 p-4 mb-3 max-w-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-lg">❓</span>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-semibold text-gray-900 mb-1">${title}</p>
                    <p class="text-xs text-gray-600 mb-3">${message}</p>
                    <div class="flex space-x-2">
                        <button class="confirm-yes px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 font-medium">
                            ✅ Yes
                        </button>
                        <button class="confirm-no px-3 py-1 bg-gray-400 text-white text-xs rounded hover:bg-gray-500 font-medium">
                            ❌ Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('#toast-container').append(confirmToast);

    // Handle confirmation
    confirmToast.find('.confirm-yes').on('click', function() {
        confirmToast.remove();
        if (onConfirm) onConfirm();
    });

    // Handle cancellation
    confirmToast.find('.confirm-no').on('click', function() {
        confirmToast.remove();
        if (onCancel) onCancel();
    });

    // Auto-remove after 10 seconds if no action taken
    setTimeout(() => {
        if (confirmToast.length) {
            confirmToast.fadeOut(300, () => confirmToast.remove());
            if (onCancel) onCancel();
        }
    }, 10000);
}

$(document).ready(function() {
    // Initialize global variables
    allMailboxes = $('.mailbox-item').toArray();
    filteredMailboxes = allMailboxes;

    // Initialize pagination
    updatePagination();

    // Pagination controls
    $('#prevPage').click(() => {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    });

    $('#nextPage').click(() => {
        const totalPages = Math.ceil(filteredMailboxes.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    });

    // Search functionality
    $('#searchMailbox').on('input', function() {
        const query = $(this).val().toLowerCase();
        filteredMailboxes = allMailboxes.filter(item => {
            const mailbox = $(item).data('mailbox').toString().toLowerCase();
            const customer = $(item).data('customer').toString().toLowerCase();
            return mailbox.includes(query) || customer.includes(query);
        });
        currentPage = 1;
        updatePagination();
    });

    // View toggle
    $('.view-toggle').click(function() {
        $('.view-toggle').removeClass('active bg-blue-600 text-white').addClass('text-gray-700');
        $(this).addClass('active bg-blue-600 text-white').removeClass('text-gray-700');

        if ($(this).data('view') === 'grid') {
            $('#gridView').show();
            $('#tableView').hide();
        } else {
            $('#gridView').hide();
            $('#tableView').show();
            populateTable();
        }
    });

    // Populate table view
    function populateTable() {
        const tbody = $('#tableView tbody');
        tbody.empty();

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        filteredMailboxes.slice(start, end).forEach(item => {
            const $item = $(item);
            const row = `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${$item.data('mailbox')}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${$item.data('customer')}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${$item.data('phone')}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${$item.data('packages') > 0 ? `<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">${$item.data('packages')} packages</span>` : '<span class="text-gray-400">No packages</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 mailbox-details" data-mailbox="${$item.data('mailbox')}">View</button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Mailbox click handler
    $(document).on('click', '.mailbox-item, .mailbox-details', function(e) {
        e.preventDefault();
        const mailbox = $(this).data('mailbox');
        const customer = $(this).data('customer');
        const phone = $(this).data('phone');
        const sizeType = $(this).data('size-type');
        const status = $(this).data('status');
        const email = $(this).data('email');
        const dateClose = $(this).data('date-close');
        const term = $(this).data('term');
        const dueDate = $(this).data('due-date');
        const packages = $(this).data('packages');

        $('#modalTitle').text(`Mailbox ${mailbox} - Client Information`);
        $('#modalContent').html(`
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Information -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-1">Client Details</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Mailbox Number:</span>
                            <span class="text-gray-900 font-medium">${mailbox}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Customer Name:</span>
                            <span class="text-gray-900 font-medium">${customer || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Phone Number:</span>
                            <span class="text-gray-900 font-medium">${phone || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Email:</span>
                            <span class="text-gray-900 font-medium">${email || 'N/A'}</span>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-1">Account Status</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Size/Type:</span>
                            <span class="text-gray-900 font-medium">${sizeType || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Status:</span>
                            ${status ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">${status}</span>` : '<span class="text-gray-900 font-medium">N/A</span>'}
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Term:</span>
                            <span class="text-gray-900 font-medium">${term || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Due Date:</span>
                            <span class="text-gray-900 font-medium">${dueDate || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Date Close:</span>
                            <span class="text-gray-900 font-medium">${dateClose || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Summary -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Package Summary</h4>
                    ${packages > 0 ?
                        `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">${packages} package(s)</span>` :
                        '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">No packages</span>'
                    }
                </div>
                ${packages > 0 ? `
                <div class="mt-4">
                    <button id="packageBtn-${mailbox}" onclick="togglePackageDetails('${mailbox}')" class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                        📦 View Package Details
                    </button>
                </div>` : ''}

                <!-- Action Buttons -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <button onclick="quickMessage('${mailbox}', '${phone}', '${customer}')" class="bg-green-600 text-white py-2.5 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center">
                        💬 Quick Message
                    </button>
                    <button onclick="addPackageToMailbox('${mailbox}', '${customer}')" class="bg-purple-600 text-white py-2.5 px-4 rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm flex items-center justify-center">
                        📦 Add Package
                    </button>
                    <button onclick="sendRenewalReminder('${mailbox}', '${phone}', '${customer}', '${dueDate}')" class="bg-orange-600 text-white py-2.5 px-4 rounded-lg hover:bg-orange-700 transition-colors font-medium text-sm flex items-center justify-center">
                        🔔 Renewal Reminder
                    </button>
                </div>
            </div>
        `);

        $('#packageDetails').addClass('hidden');
        $('#mailboxModal').removeClass('hidden');
    });

    // Close modal
    $('#closeModal').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#mailboxModal').addClass('hidden');
    });

    // Close modal when clicking background or close button
    $('#mailboxModal').on('click', function(e) {
        // Close if clicking on the modal backdrop or the modal container itself
        if (e.target.id === 'modalBackdrop' ||
            e.target.id === 'mailboxModal' ||
            $(e.target).hasClass('flex')) {
            $('#mailboxModal').addClass('hidden');
        }
    });

    // Direct backdrop click handler
    $('#modalBackdrop').on('click', function() {
        $('#mailboxModal').addClass('hidden');
    });

    // Close modal button
    $('#closeModal').click(function() {
        $('#mailboxModal').addClass('hidden');
    });

    // Close modal on escape key
    $(document).keydown(function(e) {
        if (e.keyCode === 27 && !$('#mailboxModal').hasClass('hidden')) {
            $('#mailboxModal').addClass('hidden');
        }
    });

    // Tab switching
    $('.tab-btn').click(function() {
        $('.tab-btn').removeClass('active bg-blue-600 text-white').addClass('bg-gray-200 text-gray-700');
        $(this).addClass('active bg-blue-600 text-white').removeClass('bg-gray-200 text-gray-700');
    });

    // Auto-fill customer name when mailbox number is entered
    let searchTimeout;
    $('input[name="mailbox_number"]').on('input', function() {
        const mailboxNumber = $(this).val().trim();
        const customerNameField = $('input[name="customer_name"]');

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Clear previous highlights
        $('.mailbox-item').removeClass('mailbox-highlighted border-green-500 bg-green-50 ring-2 ring-green-200');

        if (mailboxNumber) {
            // Find matching mailbox in the grid
            const matchingMailbox = $('.mailbox-item').filter(function() {
                return $(this).data('mailbox').toString() === mailboxNumber;
            });

            if (matchingMailbox.length > 0) {
                // Auto-fill customer name from matching mailbox
                const customerName = matchingMailbox.data('customer');
                if (customerName && customerName.trim() !== '') {
                    customerNameField.val(customerName);
                    // Visual feedback that auto-fill worked
                    customerNameField.addClass('bg-green-50 border-green-300');
                    setTimeout(() => {
                        customerNameField.removeClass('bg-green-50 border-green-300');
                    }, 1500);
                }

                // Highlight and scroll to matching mailbox
                matchingMailbox.addClass('mailbox-highlighted border-green-500 bg-green-50 ring-2 ring-green-200');

                // Filter to show only the matching mailbox
                filterToMailbox(mailboxNumber);

                // Scroll the highlighted mailbox into view
                setTimeout(() => {
                    matchingMailbox[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'center'
                    });
                }, 200);

                // Delayed notification to prevent spam while typing
                searchTimeout = setTimeout(() => {
                    showToast(`Found mailbox ${mailboxNumber} - ${customerName}`, 'success');
                }, 2000);
            } else {
                // Clear customer name if mailbox not found
                customerNameField.val('');
                // Reset filter to show all mailboxes
                resetMailboxFilter();

                // Delayed notification to prevent spam while typing
                searchTimeout = setTimeout(() => {
                    showToast(`Mailbox ${mailboxNumber} not found`, 'warning');
                }, 1500);
            }
        } else {
            // Clear customer name if mailbox number is empty
            customerNameField.val('');
            // Reset filter to show all mailboxes
            resetMailboxFilter();
        }
    });

    // Form submission
    $('#packageForm').submit(function(e) {
        e.preventDefault();

        // Collect form data
        const formData = new FormData();
        const mailboxNumber = $('input[name="mailbox_number"]').val().trim();
        const customerName = $('input[name="customer_name"]').val().trim();
        const packageCount = $('input[name="package_count"]').val() || 1;
        const status = $('select[name="status"]').val();
        const trackingNumber = $('textarea[name="tracking_number"]').val().trim();
        const smsMessage = $('textarea[name="sms_message"]').val().trim();

        // Validation
        if (!customerName) {
            alert('Customer name is required.');
            return;
        }

        if (!status) {
            alert('Status is required.');
            return;
        }

        // Add form data
        formData.append('mailbox_number', mailboxNumber);
        formData.append('customer_name', customerName);
        formData.append('package_count', packageCount);
        formData.append('status', 'Ready for Pickup'); // Always save as Ready for Pickup
        formData.append('tracking_number', trackingNumber);
        formData.append('sms_message', smsMessage);

        // Add CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Add images if any
        const fileInput = $('input[name="package_images[]"]')[0];
        if (fileInput && fileInput.files) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('package_images[]', fileInput.files[i]);
            }
        }

        // Submit form
        $.ajax({
            url: '/saveAndNotify',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                showToast(response.message || 'Package saved successfully!', 'success');

                // Show additional info if available
                if (response.packages_created > 1) {
                    showToast(`Created ${response.packages_created} packages`, 'info');
                }

                if (response.phone_found) {
                    showToast('Customer phone number found in records', 'info');
                } else if (mailboxNumber) {
                    showToast('No phone number found for this mailbox', 'warning');
                }

                // Display tracking numbers with label printing options
                if (response.packages && response.packages.length > 0) {
                    displayTrackingNumbers(response.packages);
                }

                // Reset form but keep tracking display
                $('#packageForm')[0].reset();

                // Don't auto-reload to keep tracking numbers visible
                // setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                if (error && error.errors) {
                    Object.keys(error.errors).forEach(key => {
                        error.errors[key].forEach(msg => {
                            showToast(`${key}: ${msg}`, 'error');
                        });
                    });
                } else {
                    showToast(error?.message || 'Error saving package. Please try again.', 'error');
                }
            }
        });
    });

    // Initialize
    updatePagination();
    setupTrackingPreview();
});

// Real-time tracking number preview
function setupTrackingPreview() {
    const trackingInput = $('#trackingInput');
    const trackingPreview = $('#trackingPreview');
    const previewList = $('#previewList');
    const clearPreviewBtn = $('#clearPreview');

    // Handle input events with proper scanning support
    trackingInput.on('input paste', function(e) {
        // For paste events, allow time for content to be inserted
        if (e.type === 'paste') {
            setTimeout(() => {
                processTrackingInput();
            }, 10);
        } else {
            processTrackingInput();
        }
    });

    // Handle Enter key for new tracking numbers
    trackingInput.on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const currentValue = $(this).val();
            if (currentValue.trim() && !currentValue.endsWith('\n')) {
                $(this).val(currentValue + '\n');
                processTrackingInput();
            }
        }
    });

    function processTrackingInput() {
        const trackingText = trackingInput.val().trim();

        if (trackingText) {
            const trackingNumbers = trackingText.split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0);

            if (trackingNumbers.length > 0) {
                updateTrackingPreview(trackingNumbers);
                trackingPreview.removeClass('hidden');
            } else {
                trackingPreview.addClass('hidden');
            }
        } else {
            trackingPreview.addClass('hidden');
        }
    }

    clearPreviewBtn.on('click', function() {
        trackingInput.val('');
        trackingPreview.addClass('hidden');
        previewList.html('');
    });
}

// Update tracking preview display
function updateTrackingPreview(trackingNumbers) {
    const previewList = $('#previewList');
    const mailboxNumber = $('input[name="mailbox_number"]').val().trim();
    const customerName = $('input[name="customer_name"]').val().trim();

    let previewHtml = '';
    trackingNumbers.forEach((tracking, index) => {
        if (tracking) {
            previewHtml += `
                <div class="p-2 bg-white rounded border">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-sm font-medium text-gray-900">Package #${index + 1}</span>
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded font-mono break-all">${tracking}</span>
                    </div>
                    <div class="text-xs text-gray-500 mb-2">
                        ${customerName ? customerName : 'Customer'} ${mailboxNumber ? `(${mailboxNumber})` : ''}
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-orange-600 font-medium">Incoming</span>
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); printTrackingLabel('${tracking}'); return false;" class="px-2 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 flex items-center space-x-1">
                            <span>🏷️</span>
                            <span>Print Label</span>
                        </button>
                    </div>
                </div>
            `;
        }
    });

    previewList.html(previewHtml);
}

// Display tracking numbers with label printing options
function displayTrackingNumbers(packages) {
    const trackingDisplay = $('#trackingDisplay');
    const trackingList = $('#trackingList');
    const printAllBtn = $('#printAllLabels');
    const trackingPreview = $('#trackingPreview');

    // Hide preview and show saved packages
    trackingPreview.addClass('hidden');
    trackingDisplay.removeClass('hidden');
    printAllBtn.removeClass('hidden');

    let trackingHtml = '';
    packages.forEach((pkg, index) => {
        trackingHtml += `
            <div class="flex items-center justify-between p-3 bg-white rounded border border-green-200">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <span class="font-medium text-gray-900">Package #${index + 1}</span>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-mono">${pkg.tracking_number}</span>
                        <span class="text-sm text-gray-600">${pkg.customer_name} (${pkg.mailbox_number})</span>
                    </div>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-xs text-gray-500">Status: ${pkg.status || 'Incoming'}</span>
                        <span class="text-xs text-green-600">✓ Saved</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="printSingleLabel(${pkg.id})" class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 flex items-center space-x-1">
                        <span>🏷️</span>
                        <span>Print Label</span>
                    </button>
                    <button onclick="viewPackageDetails(${pkg.id})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 flex items-center space-x-1">
                        <span>👁️</span>
                        <span>View</span>
                    </button>
                </div>
            </div>
        `;
    });

    trackingList.html(trackingHtml);

    // Scroll to tracking display
    setTimeout(() => {
        trackingDisplay[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
}

// Print single package label
function printSingleLabel(packageId) {
    const printWindow = window.open(`/labels/single/${packageId}`, '_blank', 'width=800,height=600');
    printWindow.focus();

    // Auto-print when window loads
    printWindow.onload = function() {
        printWindow.print();
    };

    showToast('Opening label for printing...', 'info');
}

// Print label for tracking number from preview (before saving)
function printTrackingLabel(trackingNumber) {
    const mailboxNumber = $('input[name="mailbox_number"]').val() || '';
    const customerName = $('input[name="customer_name"]').val() || '';
    const phoneNumber = $('input[name="phone_number"]').val() || '';

    if (!customerName.trim()) {
        showToast('Please enter customer name before printing label', 'warning');
        return;
    }

    // Create a temporary package object for label printing
    const tempPackage = {
        tracking_number: trackingNumber,
        customer_name: customerName.trim(),
        mailbox_number: mailboxNumber.trim() || 'N/A',
        phone_number: phoneNumber.trim() || '',
        status: 'incoming',
        created_at: new Date().toISOString()
    };

    // Use AJAX to get label content and print directly
    $.ajax({
        url: '/labels/preview',
        method: 'POST',
        data: tempPackage,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Create a hidden popup window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
            printWindow.document.write(response);
            printWindow.document.close();

            // Wait for content to load then print
            printWindow.onload = function() {
                printWindow.print();
                // Close the window after printing
                setTimeout(function() {
                    printWindow.close();
                }, 1000);
            };
        },
        error: function(xhr, status, error) {
            console.error('Error generating label:', error);
            showToast('Error generating label. Please try again.', 'error');
        }
    });

    showToast(`Generating label for ${trackingNumber}...`, 'info');
}

// Print all preview labels (before saving)
function printAllPreviewLabels() {
    const trackingInput = $('textarea[name="tracking_number"]').val();
    if (!trackingInput || !trackingInput.trim()) {
        showToast('No tracking numbers to print', 'warning');
        return;
    }

    const customerName = $('input[name="customer_name"]').val();
    if (!customerName || !customerName.trim()) {
        showToast('Please enter customer name before printing labels', 'warning');
        return;
    }

    const trackingNumbers = trackingInput.split('\n')
        .map(line => line.trim())
        .filter(line => line.length > 0)
        .filter((value, index, self) => self.indexOf(value) === index); // Remove duplicates

    if (trackingNumbers.length === 0) {
        showToast('No valid tracking numbers found', 'warning');
        return;
    }

    const mailboxNumber = $('input[name="mailbox_number"]').val() || '';
    const phoneNumber = $('input[name="phone_number"]').val() || '';

    // Create package data for multiple labels
    const packagesData = {
        packages: trackingNumbers.map(tracking => ({
            tracking_number: tracking,
            customer_name: customerName.trim(),
            mailbox_number: mailboxNumber.trim() || 'N/A',
            phone_number: phoneNumber.trim() || '',
            status: 'incoming',
            created_at: new Date().toISOString()
        }))
    };

    // Use AJAX to get multiple labels content and print directly

    $.ajax({
        url: '/labels/preview-multiple',
        method: 'POST',
        data: packagesData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Create a new window for printing with proper settings
            const printWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');

            if (!printWindow) {
                showToast('Please allow popups for this site to print labels', 'error');
                return;
            }

            printWindow.document.write(response);
            printWindow.document.close();

            // Wait for content to load then manually trigger print
            setTimeout(function() {
                printWindow.focus();
                printWindow.print();
            }, 1500);
        },
        error: function(xhr, status, error) {
            console.error('Error response:', xhr.responseText);
            console.error('Status:', status);
            console.error('Error:', error);

            try {
                const errorData = JSON.parse(xhr.responseText);
                showToast('Error: ' + (errorData.error || 'Unknown error'), 'error');
                console.error('Parsed error:', errorData);
            } catch (e) {
                showToast('Error generating labels. Please try again.', 'error');
            }
        }
    });

    showToast(`Generating ${trackingNumbers.length} labels for printing...`, 'success');
}

// Print all labels
function printAllLabels() {
    const trackingItems = $('#trackingList .flex');
    const packageIds = [];

    trackingItems.each(function() {
        const printBtn = $(this).find('button[onclick*="printSingleLabel"]');
        const onclick = printBtn.attr('onclick');
        if (onclick) {
            const match = onclick.match(/printSingleLabel\((\d+)\)/);
            if (match) {
                packageIds.push(match[1]);
            }
        }
    });

    if (packageIds.length === 0) {
        showToast('No packages available for printing', 'warning');
        return;
    }

    // Print each label in sequence with delay to prevent blocking
    packageIds.forEach((id, index) => {
        setTimeout(() => {
            printSingleLabel(id);
        }, index * 1000); // 1 second delay between each print
    });

    showToast(`Printing ${packageIds.length} labels...`, 'success');
}

// View package details
function viewPackageDetails(packageId) {
    // You can implement this to show more package details
    showToast('Package details feature coming soon!', 'info');
}

// Clear tracking display
function clearTrackingDisplay() {
    $('#trackingDisplay').addClass('hidden');
    $('#trackingPreview').addClass('hidden');
    $('#trackingList').html('');
    $('#previewList').html('');
    $('#printAllLabels').addClass('hidden');
    $('#trackingInput').val('');
}

// Event handlers
$(document).ready(function() {
    // Clear tracking button
    $('#clearTracking').click(function() {
        clearTrackingDisplay();
    });

    // Print all labels button
    $('#printAllLabels').click(function() {
        printAllLabels();
    });
});

// Load package details function
function togglePackageDetails(mailboxNumber) {
    const packageDetails = $('#packageDetails');
    const button = $(`#packageBtn-${mailboxNumber}`);

    if (packageDetails.hasClass('hidden')) {
        // Show package details
        button.text('📦 Hide Package Details');
        packageDetails.removeClass('hidden');
        $('#packageList').html('<div class="text-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div><p class="text-sm text-gray-500 mt-2">Loading packages...</p></div>');

        // Fetch real package data from API
        fetch(`/get-packages-by-mailbox/${mailboxNumber}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(packages => {
                // Check if it's an error response
                if (packages.error) {
                    throw new Error(packages.message);
                }

                if (!Array.isArray(packages) || packages.length === 0) {
                    $('#packageList').html('<div class="text-center py-4 text-gray-500">No packages found for this mailbox.</div>');
                    return;
                }

                let packagesHtml = '';
                packages.forEach((pkg, index) => {
                    const statusColor = pkg.status === 'Incoming' ? 'blue' :
                                      pkg.status === 'Ready for Pickup' ? 'yellow' :
                                      pkg.status === 'Picked Up' ? 'green' : 'gray';

                    // Build workflow timeline
                    let workflowHtml = '<div class="text-xs text-gray-500 mt-2">';
                    if (pkg.received_at) {
                        workflowHtml += `<div>📥 Received: ${pkg.received_at}</div>`;
                    }
                    if (pkg.ready_at) {
                        workflowHtml += `<div>✅ Ready: ${pkg.ready_at}</div>`;
                    }
                    if (pkg.picked_up_at) {
                        workflowHtml += `<div>📦 Picked up: ${pkg.picked_up_at}</div>`;
                    }
                    if (pkg.age_days > 0) {
                        workflowHtml += `<div class="font-medium ${pkg.age_days > 7 ? 'text-red-600' : 'text-blue-600'}">Age: ${pkg.age_days} days</div>`;
                    }
                    workflowHtml += '</div>';

                    // Build action buttons based on status
                    let actionButtonsHtml = '';
                    if (pkg.status === 'Ready for Pickup') {
                        actionButtonsHtml = `
                            <div class="mt-2">
                                <button onclick="markAsPickedUp(${pkg.id}, '${pkg.tracking_number}')"
                                        class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 flex items-center space-x-1">
                                    <span>✅</span>
                                    <span>Mark as Picked Up</span>
                                </button>
                            </div>
                        `;
                    }

                    packagesHtml += `
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-${statusColor}-500 mb-3" data-package-id="${pkg.id}" data-package-status="${pkg.status}">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        ${pkg.status === 'Ready for Pickup' ? `<input type="checkbox" class="package-checkbox rounded" data-package-id="${pkg.id}" data-tracking="${pkg.tracking_number}">` : ''}
                                        <span class="font-medium text-gray-900">Package #${index + 1}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Tracking: ${pkg.tracking_number}</p>
                                    ${workflowHtml}
                                </div>
                                <div class="text-right">
                                    <span class="bg-${statusColor}-100 text-${statusColor}-800 text-xs font-medium px-2 py-1 rounded-full">${pkg.status}</span>
                                    ${actionButtonsHtml}
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Add bulk action controls if there are Ready for Pickup packages
                const readyPackages = packages.filter(pkg => pkg.status === 'Ready for Pickup');
                let finalHtml = '';

                if (readyPackages.length > 0) {
                    const bulkControlsHtml = `
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="selectAllPackages" class="rounded">
                                    <label for="selectAllPackages" class="text-sm font-medium text-gray-700">Select All (${readyPackages.length} packages)</label>
                                </div>
                                <div class="flex space-x-2">
                                    <button id="bulkMarkPickedUp" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 disabled:opacity-50" disabled>
                                        ✅ Mark Selected as Picked Up
                                    </button>
                                    <button id="bulkMarkAllPickedUp" class="px-3 py-2 bg-orange-600 text-white text-sm rounded hover:bg-orange-700">
                                        📦 Mark ALL as Picked Up
                                    </button>
                                </div>
                            </div>
                            <div id="bulkActionStatus" class="text-sm text-gray-600"></div>
                        </div>
                    `;
                    finalHtml = bulkControlsHtml + packagesHtml;
                } else {
                    finalHtml = packagesHtml;
                }

                $('#packageList').html(finalHtml);

                if (readyPackages.length > 0) {
                    // Setup bulk action handlers
                    setupBulkActions(mailboxNumber, readyPackages);
                }
            })
            .catch(error => {
                console.error('Error fetching packages:', error);
                $('#packageList').html(`<div class="text-center py-4 text-red-500">Error: ${error.message || 'Please try again.'}</div>`);
            });
    } else {
        // Hide package details
        button.text('📦 View Package Details');
        packageDetails.addClass('hidden');
    }
}

// Individual package mark as picked up function
function markAsPickedUp(packageId, trackingNumber) {
    showConfirmationToast(
        `Mark package ${trackingNumber} as picked up?`,
        'Are you sure you want to mark this package as picked up?',
        () => {
            // Confirmed - proceed with the action
            $.ajax({
                url: '/packages/mark-picked-up',
                method: 'POST',
                data: {
                    package_id: packageId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showToast(`Package ${trackingNumber} marked as picked up!`, 'success');
                    // Refresh the package details
                    const mailboxNumber = $('#modalTitle').text().match(/Mailbox (\d+)/)[1];
                    if (mailboxNumber) {
                        setTimeout(() => {
                            togglePackageDetails(mailboxNumber);
                            togglePackageDetails(mailboxNumber); // Call twice to refresh
                        }, 500);
                    }
                    // Update mailbox count in grid
                    updateMailboxPackageCount(mailboxNumber);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    showToast(error?.message || 'Error marking package as picked up', 'error');
                }
            });
        }
    );
}

// Setup bulk actions
function setupBulkActions(mailboxNumber, packages) {
    // Select all checkbox
    $('#selectAllPackages').off('change').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.package-checkbox').prop('checked', isChecked);
        updateBulkButtonState();
    });

    // Individual checkboxes
    $(document).off('change', '.package-checkbox').on('change', '.package-checkbox', function() {
        updateBulkButtonState();
        // Update select all state
        const totalCheckboxes = $('.package-checkbox').length;
        const checkedCheckboxes = $('.package-checkbox:checked').length;
        $('#selectAllPackages').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Bulk mark selected as picked up
    $('#bulkMarkPickedUp').off('click').on('click', function() {
        const selectedPackages = $('.package-checkbox:checked');
        if (selectedPackages.length === 0) {
            showToast('Please select packages to mark as picked up', 'warning');
            return;
        }

        const packageIds = [];
        const trackingNumbers = [];
        selectedPackages.each(function() {
            packageIds.push($(this).data('package-id'));
            trackingNumbers.push($(this).data('tracking'));
        });

        showConfirmationToast(
            `Mark ${packageIds.length} selected packages as picked up?`,
            'This action will mark all selected packages as picked up.',
            () => {
                bulkMarkAsPickedUp(packageIds, trackingNumbers, mailboxNumber);
            }
        );
    });

    // Bulk mark ALL as picked up
    $('#bulkMarkAllPickedUp').off('click').on('click', function() {
        const allReadyPackages = packages.filter(pkg => pkg.status === 'Ready for Pickup');
        if (allReadyPackages.length === 0) {
            showToast('No packages available to mark as picked up', 'warning');
            return;
        }

        showConfirmationToast(
            `Mark ALL ${allReadyPackages.length} packages as picked up?`,
            'This action will mark ALL packages in this mailbox as picked up.',
            () => {
                const packageIds = allReadyPackages.map(pkg => pkg.id);
                const trackingNumbers = allReadyPackages.map(pkg => pkg.tracking_number);
                bulkMarkAsPickedUp(packageIds, trackingNumbers, mailboxNumber);
            }
        );
    });
}

// Update bulk button state
function updateBulkButtonState() {
    const selectedCount = $('.package-checkbox:checked').length;
    const bulkButton = $('#bulkMarkPickedUp');

    if (selectedCount > 0) {
        bulkButton.prop('disabled', false).text(`✅ Mark Selected (${selectedCount}) as Picked Up`);
        $('#bulkActionStatus').text(`${selectedCount} packages selected`);
    } else {
        bulkButton.prop('disabled', true).text('✅ Mark Selected as Picked Up');
        $('#bulkActionStatus').text('');
    }
}

// Bulk mark as picked up function
function bulkMarkAsPickedUp(packageIds, trackingNumbers, mailboxNumber) {
    $.ajax({
        url: '/packages/bulk-mark-picked-up',
        method: 'POST',
        data: {
            package_ids: packageIds,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showToast(`${packageIds.length} packages marked as picked up!`, 'success');
            // Refresh the package details
            setTimeout(() => {
                togglePackageDetails(mailboxNumber);
                togglePackageDetails(mailboxNumber); // Call twice to refresh
            }, 500);
            // Update mailbox count in grid
            updateMailboxPackageCount(mailboxNumber);
        },
        error: function(xhr) {
            const error = xhr.responseJSON;
            showToast(error?.message || 'Error marking packages as picked up', 'error');
        }
    });
}

// Update mailbox package count in the grid
function updateMailboxPackageCount(mailboxNumber) {
    $.ajax({
        url: `/get-packages-by-mailbox/${mailboxNumber}`,
        method: 'GET',
        success: function(packages) {
            const readyPackages = packages.filter(pkg => pkg.status === 'Ready for Pickup');
            const mailboxElement = $(`.mailbox-item[data-mailbox="${mailboxNumber}"]`);
            const packageCountBadge = mailboxElement.find('.absolute.-top-1.-right-1');

            if (readyPackages.length > 0) {
                if (packageCountBadge.length > 0) {
                    packageCountBadge.text(readyPackages.length);
                } else {
                    mailboxElement.prepend(`<div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center z-10 shadow-md">${readyPackages.length}</div>`);
                }
                mailboxElement.find('.h-full').addClass('bg-blue-50 border-blue-500');
            } else {
                packageCountBadge.remove();
                mailboxElement.find('.h-full').removeClass('bg-blue-50 border-blue-500');
            }

            // Update data attribute
            mailboxElement.attr('data-packages', readyPackages.length);
        },
        error: function() {
            // Silently handle error
        }
    });
}

// Add package to mailbox function
function addPackageToMailbox(mailboxNumber, customerName) {
    // Close the modal
    $('#mailboxModal').addClass('hidden');

    // Fill the form fields
    $('input[name="mailbox_number"]').val(mailboxNumber);
    $('input[name="customer_name"]').val(customerName || '');

    // Clear any previous tracking numbers
    $('textarea[name="tracking_number"]').val('');

    // Hide any preview displays
    $('#trackingPreview').addClass('hidden');
    $('#trackingDisplay').addClass('hidden');

    // Clear previous highlights
    $('.mailbox-item').removeClass('mailbox-highlighted border-green-500 bg-green-50 ring-2 ring-green-200');

    // Find and highlight the matching mailbox
    const matchingMailbox = $('.mailbox-item').filter(function() {
        return $(this).data('mailbox').toString() === mailboxNumber.toString();
    });

    if (matchingMailbox.length > 0) {
        // Highlight the mailbox
        matchingMailbox.addClass('mailbox-highlighted border-green-500 bg-green-50 ring-2 ring-green-200');

        // Filter to show only the matching mailbox
        filterToMailbox(mailboxNumber.toString());

        // Scroll the highlighted mailbox into view
        setTimeout(() => {
            matchingMailbox[0].scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'center'
            });
        }, 200);
    }

    // Focus on tracking number field
    setTimeout(() => {
        $('textarea[name="tracking_number"]').focus();

        // Scroll to the package entry form
        const packageForm = $('#packageForm');
        if (packageForm.length) {
            packageForm[0].scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Show success feedback
        showToast(`Ready to add package for ${customerName} (Mailbox ${mailboxNumber})`, 'info');
    }, 300);
}

// Quick message function (placeholder)
function quickMessage(mailboxNumber, phoneNumber, customerName) {
    showToast('Quick message feature coming soon!', 'info');
}

// Renewal reminder function (placeholder)
function sendRenewalReminder(mailboxNumber, phoneNumber, customerName, dueDate) {
    showToast('Renewal reminder feature coming soon!', 'info');
}
</script>

@endsection
