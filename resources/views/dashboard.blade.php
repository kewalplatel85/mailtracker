@extends('layouts.app')
@section('title','Dashboard')

@section('content')

@section('content')
<div class="min-h-screen bg-gray-50 py-2 sm:py-4">
    <div class="max-w-full mx-auto px-3 sm:px-6 md:px-8 lg:px-12">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">📬</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Mailboxes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_mailboxes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">📦</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">With Packages</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['mailboxes_with_packages'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">🚚</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Packages</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_packages'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: 2 Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-3 sm:gap-6">

            <!-- Left Column: Package Entry Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <!-- Form Header -->
                    <div class="px-2 sm:px-3 py-2 border-b border-gray-200">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">📦 Package Entry</h3>
                        <p class="text-xs sm:text-sm text-gray-500">Scan and register new packages</p>
                    </div>

                    <!-- CSV Upload Section -->
                    <div class="px-2 sm:px-3 py-2 bg-gray-50 border-b border-gray-200">
                        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                <input type="file" name="file" accept=".csv,.xlsx" required
                                       class="block w-full text-xs sm:text-sm text-gray-500 file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="submit" class="px-3 sm:px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 whitespace-nowrap">
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Package Entry Form -->
                    <div class="p-2 sm:p-3">
                        <form id="packageForm" class="space-y-2 sm:space-y-3">
                            @csrf
                            <!-- Tab Selection -->
                            <div class="grid grid-cols-2 gap-1 sm:gap-2">
                                <button type="button" class="tab-btn active px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium rounded-md bg-blue-600 text-white">
                                    Current Clients
                                </button>
                                <button type="button" class="tab-btn px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium rounded-md bg-gray-200 text-gray-700">
                                    New Clients
                                </button>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Mailbox #</label>
                                <input type="text" name="mailbox_number" required
                                       class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                    <option value="Picked Up">Picked Up</option>
                                </select>
                            </div>

                            <!-- Tracking Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                                <textarea name="tracking_number" id="trackingInput" rows="2" placeholder="Enter tracking numbers (one per line)"
                                          class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                <div id="trackingErrors" class="hidden mt-1 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-600"></div>
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
                    <div class="px-2 sm:px-3 py-2 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0">
                        <div>
                            <h3 class="text-base sm:text-lg font-medium text-gray-900">📬 Mailbox Grid</h3>
                            <p class="text-xs sm:text-sm text-gray-500">Visual mailbox management</p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                            <!-- Search -->
                            <div class="relative w-full sm:w-auto">
                                <input type="text" id="searchMailbox" placeholder="Search mailbox..."
                                       class="pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                                <div class="absolute left-2 sm:left-3 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Expiration Filter -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                                <select id="expirationFilter" class="px-2 sm:px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm w-full sm:w-auto">
                                    <option value="">All Expirations</option>
                                    <option value="no-due-date">No Due Date</option>
                                    <option value="expired">Expired</option>
                                    <option value="expiring-soon">Expiring This Month</option>
                                    <option value="expiring-next">Expiring Next Month</option>
                                    <optgroup label="Recent Months">
                                        @php
                                            $currentYear = date('Y');
                                            $currentMonth = date('n'); // 1-12
                                            $months = [
                                                'January', 'February', 'March', 'April', 'May', 'June',
                                                'July', 'August', 'September', 'October', 'November', 'December'
                                            ];

                                            // Show previous 2 months, current month, and next 3 months
                                            for ($i = -2; $i <= 3; $i++) {
                                                $targetMonth = $currentMonth + $i;
                                                $targetYear = $currentYear;

                                                if ($targetMonth <= 0) {
                                                    $targetMonth += 12;
                                                    $targetYear--;
                                                } elseif ($targetMonth > 12) {
                                                    $targetMonth -= 12;
                                                    $targetYear++;
                                                }

                                                $monthName = $months[$targetMonth - 1];
                                                $monthValue = sprintf('%04d-%02d', $targetYear, $targetMonth);
                                                echo "<option value=\"{$monthValue}\">{$monthName} {$targetYear}</option>";
                                            }
                                        @endphp
                                    </optgroup>
                                    <optgroup label="By Year">
                                        <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                                        <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                                        <option value="{{ $currentYear + 1 }}">{{ $currentYear + 1 }}</option>
                                    </optgroup>
                                </select>
                                <button id="clearFilters" class="px-2 sm:px-3 py-2 bg-gray-500 text-white text-xs sm:text-sm rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Clear
                                </button>
                            </div>
                            <!-- View Toggle -->
                            <div class="flex bg-gray-200 rounded-md w-full sm:w-auto">
                                <button class="view-toggle active px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium rounded-l-md bg-blue-600 text-white flex-1 sm:flex-none" data-view="grid">
                                    Grid
                                </button>
                                <button class="view-toggle px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium rounded-r-md text-gray-700 flex-1 sm:flex-none" data-view="table">
                                    Table
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mailbox Grid Content -->
                    <div class="p-2 sm:p-3">
                        <!-- Pagination Info -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-2 space-y-2 sm:space-y-0">
                            <div class="text-xs sm:text-sm text-gray-500">
                                Showing <span id="currentStart">1</span>-<span id="currentEnd">40</span> of <span id="totalMailboxes">345</span> mailboxes
                            </div>
                            <div class="flex items-center space-x-1 sm:space-x-2">
                                <button id="prevPage" class="px-2 sm:px-3 py-1 border border-gray-300 rounded-md text-xs sm:text-sm hover:bg-gray-50 disabled:opacity-50">
                                    Prev
                                </button>
                                <span id="pageInfo" class="text-xs sm:text-sm text-gray-600">Page 1 of 9</span>
                                <button id="nextPage" class="px-2 sm:px-3 py-1 border border-gray-300 rounded-md text-xs sm:text-sm hover:bg-gray-50 disabled:opacity-50">
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
                                            // CRITICAL: Filter by company to prevent cross-company data leakage
                                            $currentCompanyId = session('current_company_id') ?? auth()->user()->company_id;
                                            $packageCount = \App\Models\Package::where('mailbox_number', $mailboxNumber)
                                                ->where('status', 'Ready for Pickup')
                                                ->when($currentCompanyId, function($query, $companyId) {
                                                    return $query->where('company_id', $companyId);
                                                })
                                                ->count();
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

<!-- Quick Message Modal -->
<div id="quickMessageModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Modal Backdrop -->
    <div class="fixed inset-0" style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(6px);" onclick="closeQuickMessageModal()"></div>
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 relative z-10">
        <div class="relative rounded-xl max-w-md w-full p-6 shadow-2xl border border-gray-300" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">📱 Quick Message</h3>
                <button onclick="closeQuickMessageModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="quickMessageForm" class="space-y-4">
                <!-- Customer Info -->
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">Customer:</div>
                    <div id="qmCustomerName" class="font-medium text-gray-900"></div>
                    <div class="text-sm text-gray-500">
                        <span>Mailbox: </span><span id="qmMailboxNumber"></span> |
                        <span>Phone: </span><span id="qmPhoneNumber"></span> |
                        <span>Email: </span><span id="qmEmail"></span>
                    </div>
                </div>

                <!-- Delivery Method Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📤 Delivery Method</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="sendSMS" name="delivery_method" value="sms" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                            <label for="sendSMS" class="ml-2 text-sm text-gray-700 flex items-center">
                                📱 SMS Message
                                <span id="smsStatus" class="ml-2 text-xs text-gray-500"></span>
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="sendEmail" name="delivery_method" value="email" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="sendEmail" class="ml-2 text-sm text-gray-700 flex items-center">
                                📧 Email Message
                                <span id="emailStatus" class="ml-2 text-xs text-gray-500"></span>
                            </label>
                        </div>
                    </div>
                    <div id="deliveryWarning" class="hidden mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                        ⚠️ Select at least one delivery method
                    </div>
                </div>

                <!-- Message Templates -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💬 Message Template</label>
                    <select id="messageTemplate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Select a template...</option>
                        <option value="package_ready">📦 Package Ready for Pickup</option>
                        <option value="mail_notification">📬 Mail Notification</option>
                        <option value="payment_reminder">💰 Payment Reminder</option>
                        <option value="account_update">📋 Account Update Required</option>
                        <option value="office_hours">🕒 Office Hours Notice</option>
                        <option value="custom">✏️ Custom Message</option>
                    </select>
                </div>

                <!-- Message Content -->
                <div>
                    <label for="messageContent" class="block text-sm font-medium text-gray-700 mb-2">✉️ Message</label>
                    <textarea id="messageContent" name="message" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                              placeholder="Enter your message here..."></textarea>
                    <div class="flex justify-between mt-1">
                        <div class="text-xs text-gray-500">
                            <span id="charCount">0</span>/160 characters
                        </div>
                        <div class="text-xs text-gray-500">
                            <span id="smsCount">1</span> SMS
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeQuickMessageModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        📤 Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Renewal Reminder Modal -->
<div id="renewalReminderModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Modal Backdrop -->
    <div class="fixed inset-0" style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(6px);" onclick="closeRenewalReminderModal()"></div>
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 relative z-10">
        <div class="relative rounded-xl max-w-md w-full p-6 shadow-2xl border border-gray-300" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">🔔 Renewal Reminder</h3>
                <button onclick="closeRenewalReminderModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="renewalReminderForm" class="space-y-4">
                <!-- Customer Info -->
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">Customer:</div>
                    <div id="rrCustomerName" class="font-medium text-gray-900"></div>
                    <div class="text-sm text-gray-500">
                        <span>Mailbox: </span><span id="rrMailboxNumber"></span> |
                        <span>Due Date: </span><span id="rrDueDate" class="font-medium text-red-600"></span>
                        <span id="rrPhoneNumber" style="display: none;"></span> <!-- Hidden phone storage -->
                    </div>
                </div>

                <!-- Delivery Method Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📤 Delivery Method</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="rrSendSMS" name="rr_delivery_method" value="sms" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                            <label for="rrSendSMS" class="ml-2 text-sm text-gray-700">📱 SMS Reminder</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="rrSendEmail" name="rr_delivery_method" value="email" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="rrSendEmail" class="ml-2 text-sm text-gray-700">📧 Email Reminder</label>
                        </div>
                    </div>
                </div>

                <!-- Reminder Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">⏰ Reminder Type</label>
                    <select id="reminderType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="gentle">🙂 Gentle Reminder (Friendly)</option>
                        <option value="standard">📋 Standard Notice</option>
                        <option value="urgent">⚠️ Urgent - Due Soon</option>
                        <option value="final">🚨 Final Notice</option>
                        <option value="custom">✏️ Custom Message</option>
                    </select>
                </div>

                <!-- Message Content -->
                <div>
                    <label for="renewalMessage" class="block text-sm font-medium text-gray-700 mb-2">✉️ Message</label>
                    <textarea id="renewalMessage" name="message" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                              placeholder="Renewal reminder message will appear here..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeRenewalReminderModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
                        🔔 Send Reminder
                    </button>
                </div>
            </form>
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
    // Clear search and expiration filters
    $('#searchMailbox').val('');
    $('#expirationFilter').val('');

    // Reset to all mailboxes
    filteredMailboxes = allMailboxes;
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
        applyFilters();
    });

    // Expiration filter functionality
    $('#expirationFilter').on('change', function() {
        applyFilters();
    });

    // Clear filters functionality
    $('#clearFilters').on('click', function() {
        $('#searchMailbox').val('');
        $('#expirationFilter').val('');
        applyFilters();
    });

    // Combined filter function
    function applyFilters() {
        const searchQuery = $('#searchMailbox').val().toLowerCase();
        const expirationFilter = $('#expirationFilter').val();

        filteredMailboxes = allMailboxes.filter(item => {
            const mailbox = $(item).data('mailbox').toString().toLowerCase();
            const customer = $(item).data('customer').toString().toLowerCase();
            const dueDate = $(item).data('due-date');

            // Search filter
            let matchesSearch = true;
            if (searchQuery) {
                matchesSearch = mailbox.includes(searchQuery) || customer.includes(searchQuery);
            }

            // Expiration filter
            let matchesExpiration = true;
            if (expirationFilter) {
                const hasNoDueDate = !dueDate || dueDate === 'N/A' || dueDate === '' || dueDate === 'null';

                if (expirationFilter === 'no-due-date') {
                    // Show only mailboxes with no due date
                    matchesExpiration = hasNoDueDate;
                } else {
                    // For other filters, exclude mailboxes with no due date
                    if (hasNoDueDate) {
                        matchesExpiration = false;
                    } else {
                        const expDate = new Date(dueDate);
                        const today = new Date();
                        const currentMonth = today.getMonth();
                        const currentYear = today.getFullYear();

                        // Skip invalid dates
                        if (isNaN(expDate.getTime())) {
                            matchesExpiration = false;
                        } else {
                            switch (expirationFilter) {
                                case 'expired':
                                    matchesExpiration = expDate < today;
                                    break;
                                case 'expiring-soon':
                                    const endOfMonth = new Date(currentYear, currentMonth + 1, 0);
                                    matchesExpiration = expDate >= today && expDate <= endOfMonth;
                                    break;
                                case 'expiring-next':
                                    const nextMonthStart = new Date(currentYear, currentMonth + 1, 1);
                                    const nextMonthEnd = new Date(currentYear, currentMonth + 2, 0);
                                    matchesExpiration = expDate >= nextMonthStart && expDate <= nextMonthEnd;
                                    break;
                                default:
                                    if (expirationFilter.includes('-')) {
                                        // Month filter (YYYY-MM)
                                        const [filterYear, filterMonth] = expirationFilter.split('-').map(Number);
                                        matchesExpiration = expDate.getFullYear() === filterYear &&
                                                           expDate.getMonth() === (filterMonth - 1);
                                    } else {
                                        // Year filter
                                        const filterYear = parseInt(expirationFilter);
                                        matchesExpiration = expDate.getFullYear() === filterYear;
                                    }
                                    break;
                            }
                        }
                    }
                }
            }

            return matchesSearch && matchesExpiration;
        });

        currentPage = 1;
        updatePagination();
    }    // View toggle
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${$item.data('customer') || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${$item.data('phone') || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${$item.data('packages') > 0 ? `<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">${$item.data('packages')} packages</span>` : '<span class="text-gray-400">No packages</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 mailbox-details"
                                data-mailbox="${$item.data('mailbox')}"
                                data-customer="${$item.data('customer') || ''}"
                                data-phone="${$item.data('phone') || ''}"
                                data-size-type="${$item.data('size-type') || ''}"
                                data-status="${$item.data('status') || ''}"
                                data-email="${$item.data('email') || ''}"
                                data-date-close="${$item.data('date-close') || ''}"
                                data-term="${$item.data('term') || ''}"
                                data-due-date="${$item.data('due-date') || ''}"
                                data-packages="${$item.data('packages') || 0}">View</button>
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

        // Handle undefined/null values with better fallbacks
        const safeCustomer = customer && customer !== 'undefined' ? customer : 'N/A';
        const safePhone = phone && phone !== 'undefined' ? phone : 'N/A';
        const safeEmail = email && email !== 'undefined' ? email : 'N/A';
        const safeSizeType = sizeType && sizeType !== 'undefined' ? sizeType : 'N/A';
        const safeStatus = status && status !== 'undefined' ? status : 'N/A';
        const safeDateClose = dateClose && dateClose !== 'undefined' ? dateClose : 'N/A';
        const safeTerm = term && term !== 'undefined' ? term : 'N/A';
        const safeDueDate = dueDate && dueDate !== 'undefined' ? dueDate : 'N/A';
        const safePackages = packages !== undefined && packages !== null ? packages : 0;        $('#modalTitle').text(`Mailbox ${mailbox} - Client Information`);
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
                            <span class="text-gray-900 font-medium">${safeCustomer}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Phone Number:</span>
                            <span class="text-gray-900 font-medium">${safePhone}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Email:</span>
                            <span class="text-gray-900 font-medium">${safeEmail}</span>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-1">Account Status</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Size/Type:</span>
                            <span class="text-gray-900 font-medium">${safeSizeType}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Status:</span>
                            ${safeStatus !== 'N/A' ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">${safeStatus}</span>` : '<span class="text-gray-900 font-medium">N/A</span>'}
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Term:</span>
                            <span class="text-gray-900 font-medium">${safeTerm}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Due Date:</span>
                            <span class="text-gray-900 font-medium">${safeDueDate}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Date Close:</span>
                            <span class="text-gray-900 font-medium">${safeDateClose}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Summary -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Package Summary</h4>
                    ${safePackages > 0 ?
                        `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">${safePackages} package(s)</span>` :
                        '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">No packages</span>'
                    }
                </div>
                ${safePackages > 0 ? `
                <div class="mt-4">
                    <button id="packageBtn-${mailbox}" onclick="togglePackageDetails('${mailbox}')" class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                        📦 View Package Details
                    </button>
                </div>` : ''}

                <!-- Action Buttons -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <button onclick="quickMessage('${mailbox}', '${phone}', '${customer}', '${email}')" class="bg-green-600 text-white py-2.5 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center">
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

        // Validate mailbox requirement for all operations
        if (!mailboxNumber || mailboxNumber.trim() === '') {
            alert('Mailbox number is required.');
            return;
        }

        // Additional validation for Incoming status
        if (status === 'Incoming') {
            if (!trackingNumber || trackingNumber.trim() === '') {
                alert('At least one tracking number is required for incoming packages.');
                return;
            }

            // Check for duplicate tracking numbers before saving
            const trackingNumbers = trackingNumber.split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0);

            if (trackingNumbers.length === 0) {
                alert('At least one valid tracking number is required.');
                return;
            }

            // Verify no tracking numbers already exist
            checkDuplicateTrackings(trackingNumbers, () => {
                proceedWithIncomingSave(formData, mailboxNumber, customerName, packageCount, trackingNumber, smsMessage);
            });
            return;
        }

        // Handle status logic: Incoming saves as Ready for Pickup, Picked Up processes pickup
        if (status === 'Incoming') {
            // Save as Ready for Pickup when Incoming is selected
            formData.append('mailbox_number', mailboxNumber);
            formData.append('customer_name', customerName);
            formData.append('package_count', packageCount);
            formData.append('status', 'Ready for Pickup');
            formData.append('tracking_number', trackingNumber);
            formData.append('sms_message', smsMessage);
        } else if (status === 'Picked Up') {
            // Process pickup logic - verify and mark as picked up
            processPickup(trackingNumber, mailboxNumber);
            return;
        }

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

                // Show SMS sending result only
                if (response.sms_sent) {
                    showToast('📱 SMS notification sent successfully!', 'success');
                } else if (response.sms_message && response.sms_message !== 'No SMS message provided') {
                    showToast(`📱 SMS not sent: ${response.sms_message}`, 'warning');
                }

                // Reset form and prepare for next entry
                $('#packageForm')[0].reset();

                // Reset package count to 1
                $('input[name="package_count"]').val(1);

                // Clear tracking preview
                $('#trackingPreview').addClass('hidden');
                $('#previewList').html('');

                // Clear any mailbox highlighting and reset to default view
                $('.mailbox-item').removeClass('mailbox-highlighted border-green-500 bg-green-50 ring-2 ring-green-200');

                // Reset search and filter to default view
                if ($('#searchMailbox').length) {
                    $('#searchMailbox').val('');
                }

                // Reset mailbox filter to show default grid
                filteredMailboxes = allMailboxes;
                currentPage = 1; // Reset to first page
                updatePagination();

                // Update the package count for the specific mailbox
                const mailboxNumber = response.mailbox_number || $('input[name="mailbox_number"]').val();
                if (mailboxNumber) {
                    updateMailboxPackageCount(mailboxNumber);
                }
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

    // Clear tracking field when status changes
    $('select[name="status"]').on('change', function() {
        $('textarea[name="tracking_number"]').val('');
        $('#trackingPreview').addClass('hidden');
        $('#previewList').html('');
        hideInlineError();
    });

    // Add package count manual override capability
    $('input[name="package_count"]').on('input', function() {
        const packageCount = parseInt($(this).val()) || 1;
        const trackingText = $('textarea[name="tracking_number"]').val().trim();

        if (trackingText) {
            const trackingNumbers = trackingText.split('\\n')
                .map(line => line.trim())
                .filter(line => line.length > 0);

            if (trackingNumbers.length > packageCount) {
                showToast(`Package count is less than tracking numbers (${trackingNumbers.length}). Consider updating.`, 'warning');
            }
        }
    });

    // Quick Message Modal Event Handlers
    $('#messageTemplate').on('change', updateMessageTemplate);
    $('#messageContent').on('input', updateCharacterCount);

    // Renewal Reminder Modal Event Handlers
    $('#reminderType').on('change', updateRenewalMessage);

    // Form submissions
    $('#quickMessageForm').on('submit', function(e) {
        e.preventDefault();

        // Validate delivery methods
        const smsChecked = $('#sendSMS').is(':checked') && !$('#sendSMS').is(':disabled');
        const emailChecked = $('#sendEmail').is(':checked') && !$('#sendEmail').is(':disabled');

        if (!smsChecked && !emailChecked) {
            $('#deliveryWarning').removeClass('hidden');
            showToast('Please select at least one delivery method', 'warning');
            return;
        }

        $('#deliveryWarning').addClass('hidden');

        // Get values and validate required fields
        const mailboxNumber = $('#qmMailboxNumber').text().trim();
        const customerName = $('#qmCustomerName').text().trim();
        const phoneNumber = $('#qmPhoneNumber').text().trim();
        const email = $('#qmEmail').text().trim();
        const message = $('#messageContent').val().trim();

        // Validate required fields
        if (!mailboxNumber) {
            showToast('Mailbox number is required', 'error');
            return;
        }

        if (!customerName || customerName === 'N/A') {
            showToast('Customer name is required', 'error');
            return;
        }

        if (!message) {
            showToast('Message content is required', 'error');
            return;
        }

        const formData = {
            mailbox_number: mailboxNumber,
            customer_name: customerName,
            phone_number: (phoneNumber && phoneNumber !== 'N/A') ? phoneNumber : null,
            email: (email && email !== 'N/A') ? email : null,
            message: message,
            send_sms: smsChecked ? 1 : 0,
            send_email: emailChecked ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Debug log (remove in production)
        console.log('Quick message form data:', formData);

        // Send the message
        $.ajax({
            url: '/send-quick-message',
            method: 'POST',
            data: formData,
            success: function(response) {
                showToast(response.message || 'Message sent successfully!', 'success');
                closeQuickMessageModal();
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                console.log('Quick message error:', xhr.responseJSON);

                if (error && error.errors) {
                    // Show validation errors
                    Object.keys(error.errors).forEach(key => {
                        error.errors[key].forEach(msg => {
                            showToast(`${key}: ${msg}`, 'error');
                        });
                    });
                } else {
                    showToast(error?.message || 'Error sending message', 'error');
                }
            }
        });
    });

    $('#renewalReminderForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            mailbox_number: $('#rrMailboxNumber').text(),
            customer_name: $('#rrCustomerName').text(),
            due_date: $('#rrDueDate').text(),
            message: $('#renewalMessage').val(),
            reminder_type: $('#reminderType').val(),
            phone_number: $('#rrPhoneNumber').text(),  // Include phone number
            send_sms: $('#rrSendSMS').is(':checked') ? 1 : 0,
            send_email: $('#rrSendEmail').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Send the reminder
        $.ajax({
            url: '/send-renewal-reminder',
            method: 'POST',
            data: formData,
            success: function(response) {
                showToast(response.message || 'Renewal reminder sent successfully!', 'success');
                closeRenewalReminderModal();
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                showToast(error?.message || 'Error sending reminder', 'error');
            }
        });
    });
});

// Real-time tracking number preview
function setupTrackingPreview() {
    const trackingInput = $('#trackingInput');
    const trackingPreview = $('#trackingPreview');
    const previewList = $('#previewList');
    const clearPreviewBtn = $('#clearPreview');
    const trackingErrors = $('#trackingErrors');
    let validationTimeout;

    // Handle input events with debouncing to prevent multiple validations
    trackingInput.on('input paste', function(e) {
        // Clear any existing timeout
        if (validationTimeout) {
            clearTimeout(validationTimeout);
        }

        // Set new timeout for validation
        validationTimeout = setTimeout(() => {
            processTrackingInput();
        }, e.type === 'paste' ? 100 : 500); // Shorter delay for paste, longer for typing
    });

    // Handle Enter key for new tracking numbers
    trackingInput.on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const currentValue = $(this).val();
            if (currentValue.trim() && !currentValue.endsWith('\n')) {
                $(this).val(currentValue + '\n');
                // Clear timeout and process immediately
                if (validationTimeout) {
                    clearTimeout(validationTimeout);
                }
                processTrackingInput();
            }
        }
    });

    function processTrackingInput() {
        const trackingText = trackingInput.val().trim();
        const status = $('select[name="status"]').val();

        if (trackingText) {
            const trackingNumbers = trackingText.split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0);

            if (trackingNumbers.length > 0) {
                // Validate tracking numbers based on status
                if (status === 'Incoming') {
                    validateIncomingTrackings(trackingNumbers);
                } else if (status === 'Picked Up') {
                    validatePickupTrackings(trackingNumbers);
                } else {
                    // Default behavior for other statuses
                    updateTrackingPreviewWithValidation(trackingNumbers, []);
                }
            } else {
                trackingPreview.addClass('hidden');
            }
        } else {
            trackingPreview.addClass('hidden');
            $('input[name="package_count"]').val(1);
        }
    }

    clearPreviewBtn.on('click', function() {
        trackingInput.val('');
        trackingPreview.addClass('hidden');
        previewList.html('');
        hideInlineError();
    });
}

// Show inline error message
function showInlineError(message) {
    const trackingErrors = $('#trackingErrors');
    trackingErrors.html(message).removeClass('hidden');
}

// Hide inline error message
function hideInlineError() {
    const trackingErrors = $('#trackingErrors');
    trackingErrors.addClass('hidden').html('');
}

// Validate tracking numbers for Incoming status
function validateIncomingTrackings(trackingNumbers) {
    Promise.all(trackingNumbers.map(tracking =>
        fetch('/check-tracking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ tracking_number: tracking })
        }).then(response => response.json())
    )).then(responses => {
        const validTrackings = [];
        const invalidTrackings = [];

        responses.forEach((response, index) => {
            const tracking = trackingNumbers[index];
            if (response.exists) {
                invalidTrackings.push(tracking);
            } else {
                validTrackings.push(tracking);
            }
        });

        // Show consolidated error message
        if (invalidTrackings.length > 0) {
            showInlineError(`Tracking number(s) already exist: ${invalidTrackings.join(', ')}`);
        } else {
            hideInlineError();
        }

        updateTrackingPreviewWithValidation(validTrackings, invalidTrackings);
    }).catch(error => {
        console.error('Validation error:', error);
        showInlineError('Error validating tracking numbers');
    });
}

// Validate tracking numbers for Picked Up status
function validatePickupTrackings(trackingNumbers) {
    const currentMailbox = $('input[name="mailbox_number"]').val().trim();

    Promise.all(trackingNumbers.map(tracking =>
        fetch('/check-tracking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ tracking_number: tracking })
        }).then(response => response.json())
    )).then(responses => {
        const validTrackings = [];
        const invalidTrackings = [];
        const errorMessages = [];
        let firstValidResponse = null;

        responses.forEach((response, index) => {
            const tracking = trackingNumbers[index];

            if (!response.exists) {
                invalidTrackings.push(tracking);
                errorMessages.push(`${tracking}: not found`);
            } else if (response.status !== 'Ready for Pickup') {
                invalidTrackings.push(tracking);
                errorMessages.push(`${tracking}: status is ${response.status}`);
            } else {
                // Check mailbox matching logic
                if (!currentMailbox) {
                    // No mailbox set, auto-fill from first valid tracking
                    if (!firstValidResponse) {
                        firstValidResponse = response;
                        $('input[name="mailbox_number"]').val(response.mailbox_number);
                        $('input[name="customer_name"]').val(response.customer_name);
                        validTrackings.push(tracking);
                    } else if (String(response.mailbox_number).trim() === String(firstValidResponse.mailbox_number).trim()) {
                        validTrackings.push(tracking);
                    } else {
                        invalidTrackings.push(tracking);
                        errorMessages.push(`${tracking}: different mailbox (${response.mailbox_number})`);
                    }
                } else if (String(response.mailbox_number).trim() === String(currentMailbox).trim()) {
                    validTrackings.push(tracking);
                } else {
                    invalidTrackings.push(tracking);
                    errorMessages.push(`${tracking}: belongs to mailbox ${response.mailbox_number}`);
                }
            }
        });

        // Show consolidated error message
        if (errorMessages.length > 0) {
            showInlineError(errorMessages.join('; '));
            if (firstValidResponse && errorMessages.some(msg => msg.includes('different mailbox'))) {
                showToast(`Complete pickup for mailbox ${firstValidResponse.mailbox_number} first`, 'warning');
            }
        } else {
            hideInlineError();
        }

        updateTrackingPreviewWithValidation(validTrackings, invalidTrackings);
    }).catch(error => {
        console.error('Validation error:', error);
        showInlineError('Error validating tracking numbers');
    });
}

// Update preview with validation results
function updateTrackingPreviewWithValidation(validTrackings, invalidTrackings) {
    const packageCountInput = $('input[name="package_count"]');
    const currentCount = parseInt(packageCountInput.val()) || 1;
    const validCount = validTrackings.length;

    if (validCount > 0) {
        // Update package count to match valid tracking numbers
        const newCount = Math.max(currentCount, validCount);
        packageCountInput.val(newCount);

        updateTrackingPreview(validTrackings);
        $('#trackingPreview').removeClass('hidden');

        // Only update textarea content if there are invalid trackings to remove
        // This prevents cursor position reset when user is typing valid trackings
        if (invalidTrackings.length > 0) {
            const textarea = $('textarea[name="tracking_number"]')[0];
            const cursorPosition = textarea.selectionStart;
            textarea.value = validTrackings.join('\n');
            // Restore cursor position or put it at the end
            const newPosition = Math.min(cursorPosition, textarea.value.length);
            textarea.setSelectionRange(newPosition, newPosition);
        }

        // Hide errors if we have valid trackings and no invalid ones
        if (invalidTrackings.length === 0) {
            hideInlineError();
        }
    } else {
        $('#trackingPreview').addClass('hidden');
        $('input[name="package_count"]').val(1);

        // Only clear textarea if ALL trackings were invalid
        $('textarea[name="tracking_number"]').val('');
    }
}

// Update tracking preview display
function updateTrackingPreview(trackingNumbers) {
    const previewList = $('#previewList');
    const mailboxNumber = $('input[name="mailbox_number"]').val().trim();
    const customerName = $('input[name="customer_name"]').val().trim();
    const status = $('select[name="status"]').val();

    // Determine status display and color
    let statusDisplay = status || 'Incoming';
    let statusColor = 'orange';
    if (status === 'Picked Up') {
        statusColor = 'green';
    }

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
                        <span class="text-xs text-${statusColor}-600 font-medium">${statusDisplay}</span>
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

// Print individual tracking label
function printTrackingLabel(trackingNumber) {
    if (!trackingNumber || trackingNumber.trim() === '') {
        showToast('No tracking number to print', 'warning');
        return;
    }

    // Create a new window for printing
    const printWindow = window.open('', '_blank', 'width=400,height=600');

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Tracking Label - ${trackingNumber}</title>
            <style>
                body {
                    font-family: 'Courier New', monospace;
                    margin: 20px;
                    line-height: 1.4;
                }
                .label {
                    border: 2px solid #000;
                    padding: 20px;
                    text-align: center;
                    max-width: 300px;
                    margin: 0 auto;
                }
                .title {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 15px;
                }
                .tracking {
                    font-size: 16px;
                    font-weight: bold;
                    background: #f0f0f0;
                    padding: 10px;
                    border: 1px solid #ccc;
                    margin: 15px 0;
                    word-break: break-all;
                }
                .info {
                    font-size: 12px;
                    margin: 10px 0;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="label">
                <div class="title">PACKAGE LABEL</div>
                <div class="info">Tracking Number:</div>
                <div class="tracking">${trackingNumber}</div>
                <div class="info">Date: ${new Date().toLocaleDateString()}</div>
                <div class="info">Status: Incoming</div>
            </div>
            <div class="no-print" style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">Print Label</button>
                <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">Close</button>
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();

    // Auto-focus the print window
    printWindow.focus();

    showToast(`Print label opened for ${trackingNumber}`, 'info');
}

// Preview section with package tracking display
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

// Check for duplicate tracking numbers
function checkDuplicateTrackings(trackingNumbers, callback) {
    Promise.all(trackingNumbers.map(tracking =>
        fetch('/check-tracking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ tracking_number: tracking })
        }).then(response => response.json())
    )).then(responses => {
        const duplicates = [];

        responses.forEach((response, index) => {
            if (response.exists) {
                duplicates.push(trackingNumbers[index]);
            }
        });

        if (duplicates.length > 0) {
            showToast(`Tracking number(s) already exist: ${duplicates.join(', ')}`, 'error');
            return;
        }

        // No duplicates, proceed with save
        callback();
    }).catch(error => {
        showToast('Error checking tracking numbers', 'error');
        console.error('Duplicate check error:', error);
    });
}

// Proceed with incoming package save after validation
function proceedWithIncomingSave(formData, mailboxNumber, customerName, packageCount, trackingNumber, smsMessage) {
    const formData2 = new FormData();
    formData2.append('mailbox_number', mailboxNumber);
    formData2.append('customer_name', customerName);
    formData2.append('package_count', packageCount);
    formData2.append('status', 'Ready for Pickup');
    formData2.append('tracking_number', trackingNumber);
    formData2.append('sms_message', smsMessage);
    formData2.append('_token', $('meta[name="csrf-token"]').attr('content'));

    // Add images if any
    const fileInput = $('input[name="package_images[]"]')[0];
    if (fileInput && fileInput.files) {
        for (let i = 0; i < fileInput.files.length; i++) {
            formData2.append('package_images[]', fileInput.files[i]);
        }
    }

    // Submit form
    $.ajax({
        url: '/saveAndNotify',
        type: 'POST',
        data: formData2,
        contentType: false,
        processData: false,
        success: function(response) {
            showToast(response.message || 'Package saved successfully!', 'success');

            // Show SMS sending result only
            if (response.sms_sent) {
                showToast('📱 SMS notification sent successfully!', 'success');
            } else if (response.sms_message && response.sms_message !== 'No SMS message provided') {
                showToast(`📱 SMS not sent: ${response.sms_message}`, 'warning');
            }

            // Reset form and prepare for next entry
            $('#packageForm')[0].reset();
            $('input[name="package_count"]').val(1);
            $('#trackingPreview').addClass('hidden');
            $('#previewList').html('');
            $('.mailbox-item').removeClass('mailbox-highlighted border-green-500 bg-green-50 ring-2 ring-green-200');

            if ($('#searchMailbox').length) {
                $('#searchMailbox').val('');
            }

            // Update mailbox badge
            if (response.mailbox_number) {
                updateMailboxBadge(response.mailbox_number);
            }

            // Show success packages
            if (response.packages && response.packages.length > 0) {
                displaySuccessPackages(response.packages);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving package:', error);
            const response = xhr.responseJSON;
            showToast(response?.message || 'Error saving package. Please try again.', 'error');
        }
    });
}

// Process pickup when Picked Up status is selected
function processPickup(trackingNumber, mailboxNumber) {
    if (!trackingNumber || trackingNumber.trim() === '') {
        showToast('Tracking number is required for pickup', 'error');
        return;
    }

    // Split tracking numbers if multiple
    const trackingNumbers = trackingNumber.split('\n')
        .map(line => line.trim())
        .filter(line => line.length > 0);

    if (trackingNumbers.length === 0) {
        showToast('No valid tracking numbers found', 'error');
        return;
    }

    // Verify each tracking number exists and is ready for pickup
    Promise.all(trackingNumbers.map(tracking =>
        fetch('/check-tracking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ tracking_number: tracking })
        }).then(response => response.json())
    )).then(responses => {
        const validPackages = [];
        const invalidTrackings = [];

        responses.forEach((response, index) => {
            const tracking = trackingNumbers[index];
            if (response.exists && response.status === 'Ready for Pickup') {
                // Verify mailbox if provided
                if (!mailboxNumber || response.mailbox_number == mailboxNumber) {
                    validPackages.push({
                        id: response.id,
                        tracking_number: tracking,
                        customer_name: response.customer_name,
                        mailbox_number: response.mailbox_number
                    });
                } else {
                    invalidTrackings.push(`${tracking} (wrong mailbox)`);
                }
            } else if (response.exists && response.status !== 'Ready for Pickup') {
                invalidTrackings.push(`${tracking} (status: ${response.status})`);
            } else {
                invalidTrackings.push(`${tracking} (not found)`);
            }
        });

        if (invalidTrackings.length > 0) {
            showToast(`Invalid tracking numbers: ${invalidTrackings.join(', ')}`, 'error');
            return;
        }

        if (validPackages.length === 0) {
            showToast('No valid packages found for pickup', 'error');
            return;
        }

        // Mark packages as picked up
        const packageIds = validPackages.map(pkg => pkg.id);

        $.ajax({
            url: '/packages/bulk-mark-picked-up',
            method: 'POST',
            data: {
                package_ids: packageIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showToast(`${validPackages.length} package${validPackages.length > 1 ? 's' : ''} marked as picked up!`, 'success');

                // Show SMS confirmation - the backend handles SMS automatically
                showToast('📱 SMS notification sent to customer', 'success');

                // Clear and reset entire form
                $('#packageForm')[0].reset();
                $('textarea[name="tracking_number"]').val('');
                $('input[name="mailbox_number"]').val('');
                $('input[name="customer_name"]').val('');
                $('input[name="package_count"]').val(1);

                // Clear preview and errors
                $('#trackingPreview').addClass('hidden');
                $('#previewList').html('');
                hideInlineError();

                // Reset focus to tracking input for next entry
                $('textarea[name="tracking_number"]').focus();

                // Update mailbox count if applicable
                if (mailboxNumber) {
                    updateMailboxPackageCount(mailboxNumber);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                showToast(error?.message || 'Error marking packages as picked up', 'error');
            }
        });
    }).catch(error => {
        showToast('Error verifying tracking numbers', 'error');
        console.error('Verification error:', error);
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

    // Reset package count to 1
    $('input[name="package_count"]').val(1);

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

// Quick message function
function quickMessage(mailboxNumber, phoneNumber, customerName, email = '') {
    // Populate modal with customer info
    $('#qmCustomerName').text(customerName || 'N/A');
    $('#qmMailboxNumber').text(mailboxNumber);
    $('#qmPhoneNumber').text(phoneNumber || 'N/A');
    $('#qmEmail').text(email || 'N/A');

    // Update delivery method availability
    if (!phoneNumber || phoneNumber === 'N/A') {
        $('#sendSMS').prop('checked', false).prop('disabled', true);
        $('#smsStatus').text('(No phone number)').addClass('text-red-500');
    } else {
        $('#sendSMS').prop('checked', true).prop('disabled', false);
        $('#smsStatus').text('').removeClass('text-red-500');
    }

    if (!email || email === 'N/A') {
        $('#sendEmail').prop('checked', false).prop('disabled', true);
        $('#emailStatus').text('(No email address)').addClass('text-red-500');
    } else {
        $('#sendEmail').prop('checked', true).prop('disabled', false);
        $('#emailStatus').text('').removeClass('text-red-500');
    }

    // Clear previous content
    $('#messageTemplate').val('');
    $('#messageContent').val('');
    updateCharacterCount();

    // Show the modal
    $('#quickMessageModal').removeClass('hidden');
}

// Renewal reminder function
function sendRenewalReminder(mailboxNumber, phoneNumber, customerName, dueDate) {
    // Populate modal with customer info
    $('#rrCustomerName').text(customerName || 'N/A');
    $('#rrMailboxNumber').text(mailboxNumber);
    $('#rrDueDate').text(dueDate || 'N/A');
    $('#rrPhoneNumber').text(phoneNumber || 'N/A');  // Store phone for form submission

    // Update delivery method availability
    if (!phoneNumber || phoneNumber === 'N/A') {
        $('#rrSendSMS').prop('checked', false).prop('disabled', true);
    } else {
        $('#rrSendSMS').prop('checked', true).prop('disabled', false);
    }

    // Clear and set default message
    $('#reminderType').val('gentle');
    updateRenewalMessage();

    // Show the modal
    $('#renewalReminderModal').removeClass('hidden');
}

// Close modals
function closeQuickMessageModal() {
    $('#quickMessageModal').addClass('hidden');
}

function closeRenewalReminderModal() {
    $('#renewalReminderModal').addClass('hidden');
}

// Update character count for SMS
function updateCharacterCount() {
    const message = $('#messageContent').val();
    const charCount = message.length;
    const smsCount = Math.ceil(charCount / 160) || 1;

    $('#charCount').text(charCount);
    $('#smsCount').text(smsCount);

    // Color coding for length
    if (charCount > 160) {
        $('#charCount').addClass('text-orange-600');
    } else {
        $('#charCount').removeClass('text-orange-600');
    }
}

// Update renewal message based on type
function updateRenewalMessage() {
    const type = $('#reminderType').val();
    const customerName = $('#rrCustomerName').text();
    const dueDate = $('#rrDueDate').text();

    let message = '';

    switch(type) {
        case 'gentle':
            message = `Hi ${customerName}, this is a friendly reminder that your mailbox rental is due on ${dueDate}. Please visit us at your convenience to renew. Thank you!`;
            break;
        case 'standard':
            message = `Dear ${customerName}, your mailbox rental payment is due on ${dueDate}. Please renew your service to avoid interruption. Thank you.`;
            break;
        case 'urgent':
            message = `URGENT: ${customerName}, your mailbox rental is due ${dueDate}. Please renew immediately to avoid service suspension. Contact us today.`;
            break;
        case 'final':
            message = `FINAL NOTICE: ${customerName}, your mailbox rental was due ${dueDate}. Service will be suspended if not renewed within 3 days. Please contact us immediately.`;
            break;
        case 'custom':
            message = '';
            break;
    }

    $('#renewalMessage').val(message);
}

// Message templates for quick message
function updateMessageTemplate() {
    const template = $('#messageTemplate').val();
    const customerName = $('#qmCustomerName').text();

    let message = '';

    switch(template) {
        case 'package_ready':
            message = `Hi ${customerName}, you have a package ready for pickup at Mail All Center. Please bring your ID. Thank you!`;
            break;
        case 'mail_notification':
            message = `Hi ${customerName}, you got a mail in your mailbox.`;
            break;
        case 'payment_reminder':
            message = `Hi ${customerName}, this is a reminder about your outstanding balance. Please visit us to update your account. Thank you.`;
            break;
        case 'account_update':
            message = `Hi ${customerName}, we need to update your account information. Please visit us at your earliest convenience. Thank you.`;
            break;
        case 'office_hours':
            message = `Hi ${customerName}, please note our office hours: Mon-Fri 9AM-6PM, Sat 9AM-3PM. We're closed Sundays. Thank you.`;
            break;
        case 'custom':
            message = '';
            break;
    }

    $('#messageContent').val(message);
    updateCharacterCount();
}

// Initialize customer data for SMS autocomplete
window.customersData = @json($data ?? []).slice(7).map(function(row) {
    // Skip header rows and only include valid mailbox entries
    if (row[0] && row[0].toString().trim() && !isNaN(row[0])) {
        return {
            mailbox: row[0] || '',
            customer: row[3] || '', // Customer name is in column 3 (index 3)
            phone: row[4] || '' // Phone number is in column 4 (index 4)
        };
    }
    return null;
}).filter(function(item) {
    return item !== null; // Remove null entries
});

// Initialize SMS autocomplete when page loads
$(document).ready(function() {
    if (typeof initializeSMSAutocomplete === 'function') {
        initializeSMSAutocomplete();
    }
});
</script>

@endsection
