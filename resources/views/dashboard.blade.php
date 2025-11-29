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
                                <textarea name="tracking_number" rows="2" placeholder="Enter tracking numbers (one per line)"
                                          class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
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

                            <!-- Tracking Numbers Display -->
                            <div id="trackingDisplay" class="hidden mt-4 p-4 bg-gray-50 rounded-md">
                                <h4 class="font-medium text-gray-900 mb-2">Tracking Numbers</h4>
                                <div id="trackingList" class="space-y-1"></div>
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
                                            $packageCount = \App\Models\Package::where('mailbox_number', $mailboxNumber)->where('status', 'Incoming')->count();
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

$(document).ready(function() {
    let currentPage = 1;
    let itemsPerPage = 40;
    let allMailboxes = $('.mailbox-item').toArray();
    let filteredMailboxes = allMailboxes;

    // Initialize pagination
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

    // Show current page
    function showPage() {
        $('.mailbox-item').hide();
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        filteredMailboxes.slice(start, end).forEach(item => {
            $(item).show();
        });
    }

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
    $('input[name="mailbox_number"]').on('input', function() {
        const mailboxNumber = $(this).val().trim();
        const customerNameField = $('input[name="customer_name"]');

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
            } else {
                // Clear customer name if mailbox not found
                customerNameField.val('');
            }
        } else {
            // Clear customer name if mailbox number is empty
            customerNameField.val('');
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
        formData.append('status', status);
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

                // Reset form
                $('#packageForm')[0].reset();
                // Optionally refresh page or update UI
                setTimeout(() => location.reload(), 2000);
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
                console.log('Packages received:', packages);

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

                    packagesHtml += `
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-${statusColor}-500 mb-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-900">Package #${index + 1}</p>
                                    <p class="text-sm text-gray-600">Tracking: ${pkg.tracking_number}</p>
                                    <p class="text-sm text-gray-500">Created: ${pkg.created_at}</p>
                                    ${workflowHtml}
                                </div>
                                <div class="text-right">
                                    <span class="bg-${statusColor}-100 text-${statusColor}-800 text-xs font-medium px-2 py-1 rounded-full">${pkg.status}</span>
                                    ${pkg.age_days > 7 && pkg.status === 'Ready for Pickup' ? '<div class="text-xs text-red-600 mt-1">⚠️ Aging</div>' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#packageList').html(packagesHtml);
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
</script>

@endsection
                            </div>

                            <div class="relative w-full">
                                <!-- Hidden Native Select -->
                                <select id="packageStat" name="packageStat" class="hidden">
                                    <option selected data-route="Incoming">Incoming</option>
                                    <option data-route="Outgoing">Outgoing</option>
                                </select>

                                <!-- Custom Dropdown Trigger -->
                                <button id="custom-dropdown-btn" data-stat="Incoming" class="package_stat w-full rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:outline-indigo-600 sm:text-sm/6">
                                    Incoming
                                </button>

                                <!-- Dropdown Arrow -->
                                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 size-5 text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>

                                <!-- Custom Dropdown -->
                                <ul id="custom-dropdown" class="scanStat absolute z-10 mt-2 hidden w-full rounded-md bg-white shadow-lg transition-all duration-300 ease-in-out">
                                    <li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">Incoming</li>
                                    <li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">Outgoing</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6 place-items-center space-y-2">
                        <form id="packageForm" class="w-full md:1/2 place-items-center">
                            @csrf
                            <div class="rounded-md flex justify-between divide-x divide-gray-300 bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                <div>
                                    <label for="mailbox" class="block text-xs font-medium text-gray-900">Mailbox #</label>
                                    <input type="text" name="mailbox" id="mailbox" data-mc="0" data-mb="0" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="0000" oninput="this.value=this.value.replace(/\D/g,'')">
                                    <small id="mailbox-error" style="color: red;"></small>
                                </div>
                                <div class="flex-auto px-2">
                                    <label for="mailbox" class="block text-xs font-medium text-gray-900">Customer</label>
                                    <input type="text" name="customer" id="customer" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="Customer Name">
                                    <small id="customer-error" style="color: red;"></small>
                                </div>
                            </div>
                            <div class="contact-div rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600" style="display: none">
                                <label for="cnumber" class="block text-xs font-medium text-gray-900">Contact Number</label>
                                <input type="text" name="cnumber" id="cnumber" min="1" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="000-0000-0000" oninput="this.value=this.value.replace(/\D/g,'')">
                            </div>
                            <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                <label for="pcounter" class="block text-xs font-medium text-gray-900">Number of Packages</label>
                                <input type="text" name="pcounter" id="pcounter" min="1" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="0" oninput="this.value=this.value.replace(/\D/g,'')" value="0">
                            </div>
                            <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                <label for="track-number" class="block text-xs font-medium text-gray-900">Tracking number #</label>
                                <input type="text" name="track_number" id="track_number" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="000000000000">
                            </div>
                            <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 mb-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                <label for="sms" class="block text-xs font-medium text-gray-900">Custom SMS #</label>
                                <textarea rows="2" name="sms" id="sms" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8"
                                    placeholder="Hi, This is Mail All Center, Mountain View. You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!">{{old('description')}}</textarea>
                            </div>
                            <div class="lbl-div rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 mb-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600" style="display: none">
                                <label for="custom-lbl" class="block text-xs font-medium text-gray-900">Custom Package Label #</label>
                                <textarea rows="2" name="lbl" id="lbl" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8"
                                    placeholder="Rent a Mailbox for $15/ month, Avoid Porch Pirates, We accept all packages">{{old('description')}}</textarea>
                            </div>

                            <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 mb-3 outline-1 outline-gray-300">
                                <label class="block text-xs font-medium text-gray-900">Attach or Capture Image</label>

                                <!-- File input for uploads -->
                                <input type="file" accept="image/*" name="package_images[]" id="package_image" class="mb-2" multiple>

                                <!-- Video stream + canvas -->
                                <video id="cameraStream" autoplay playsinline class="w-full mb-2 hidden rounded shadow"></video>
                                <canvas id="snapshot" class="hidden"></canvas>

                                <!-- Control buttons -->
                                <div class="flex gap-2 mb-2">
                                    <button type="button" id="startCamera" class="bg-blue-500 text-white px-3 py-1 rounded">📷 Start Camera</button>
                                    <button type="button" id="captureImage" class="bg-green-500 text-white px-3 py-1 rounded hidden">📸 Capture</button>
                                    <button type="button" id="cancelCamera" class="bg-red-500 text-white px-3 py-1 rounded hidden">✖ Cancel</button>
                                </div>

                                <!-- Preview area -->
                                <div id="imagePreview" class="flex gap-2 mt-2 flex-wrap"></div>
                            </div>

                            <button type="submit" class="block px-3 pt-2.5 w-full md:w-1/2 pb-1 bg-blue-600 text-white rounded-md py-2.5 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-blue-600">Save and Send SMS</button>
                            <div class="p-1 bg-gray-900 rounded-md shadow-lg w-full md:w-1/2">
                                <table class="min-w-full rounded-md divide-y text-white" id="tracking_table">
                                    <thead>
                                        <tr>
                                            <th>Tracking Number</th>
                                        </tr>
                                    </thead>
                                    <tbody class="trn_count divide-y divide-gray-600" data-total="0">

                                    </tbody>
                                </table>
                            </div>
                        </form>
                            <div id="loadingScreen" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden flex items-center justify-center">
                                <div class="flex flex-col items-center">
                                <div class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin mb-4"></div>
                                <p class="text-white text-lg font-medium">Processing, please wait...</p>
                                </div>
                            </div>
                    </div>

                    <div class="place-items-center space-y-2 md:block">
                          <div class="bg-gray-900 w-full rounded-md">
                            <div class="mx-auto max-w-7xl">
                              <div class="bg-gray-900 py-6 rounded-md">
                                <div class="px-4 sm:px-6 lg:px-8">
                                  <div class="sm:flex sm:items-center">
                                    <div class="sm:flex-auto">
                                      <h1 class="text-base font-semibold text-white">Mail All Center</h1>
                                      <p class="my-2 text-sm text-gray-300">Clients Information</p>
                                    </div>
                                  </div>
                                  <div class="mt-2 flow-root">
                                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                      <div class="inline-block min-w-full align-middle sm:px-6 lg:px-8 max-h-screen overflow-y-auto">
                                        @if(!empty($data))
                                            <table class="min-w-full divide-y divide-gray-700" id="clientTable">
                                                <thead class="sticky top-0">
                                                    <tr>
                                                    @foreach($data[6] as $header => $value)
                                                        <th scope="col" class="pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">{{ ucfirst($value) }}</th>
                                                    @endforeach
                                                        <th scope="col" class="pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-800">
                                                    @foreach(array_slice($data,7) as $row)
                                                        <tr>
                                                        @foreach($row as $index => $cell)
                                                            <td class="mailbox py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">
                                                                @php $disabled = $row[0] ? 'disabled' : '';
                                                                  switch ($index) {
                                                                    case 4:
                                                                        $inputType = 'tel'; // contact number
                                                                        break;
                                                                    case 5:
                                                                    case 7:
                                                                        $inputType = 'date'; // specific date fields
                                                                        break;
                                                                    case 8:
                                                                        $inputType = 'email'; // email
                                                                        break;
                                                                    default:
                                                                        $inputType = 'text'; // all others
                                                                 }
                                                                 if ($inputType === 'date') {
                                                                        $timestamp = strtotime($cell);
                                                                        $inputValue = ($timestamp && trim($cell) !== '') ? date('Y-m-d', $timestamp) : '';
                                                                    } else {
                                                                        $inputValue = $cell;
                                                                }
                                                                @endphp
                                                                <input type="{{ $inputType }}" value="{{ $inputValue }}" class="edit-info w-full border-0 rounded-sm z-50" {{ $disabled }} data-index="{{ $index }}" data-raw="{{ $cell }}">
                                                            </td>
                                                        @endforeach
                                                            <td class="mailbox py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0 flex space-x-2">
                                                                <a href="#" class="edit-{{ $row[0] }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hover:text-blue-500 cursor-pointer">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                    </svg>
                                                                </a>
                                                                <a href="#" class="save-edit" hidden>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hover:text-blue-500 cursor-pointer text-green-500">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                    </svg>
                                                                </a>
                                                                <a href="#" class="cancel-edit" hidden>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hover:text-blue-500 cursor-pointer text-red-500" >
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                      </svg>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                            <!-- More people... -->
                                                </tbody>
                                            </table>
                                        @endif
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                    </div>
                </form>
                </div>
            </div>
            {{-- @include('sms.inbox') --}}
            @include('sms.inbox', ['receivedMessages' => $receivedMessages, 'sentMessages' => $sentMessages])
        </main>
    @endsection
