@extends('layouts.app')
@section('title','Package Logs')
@section('content')
<main>
    <div class="place-items-center space-y-2 md:block">
        <div class="bg-white w-full">
            <div class="mx-auto max-w-7xl">
                <div class="bg-white rounded-sm py-2 border border-gray-300">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold text-gray-900">Mail All Center</h1>
                                <p class="mt-2 text-sm text-gray-700">Clients Information</p>
                            </div>
                            <div class="relative mt-2 w-full md:w-1/3">
                                <input type="text" id="searchInput" placeholder="Search by Mailbox #, Customer, or Package ID" class="w-full rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:outline-indigo-600 sm:text-sm">
                            </div>
                            <div class="relative mt-2 w-full md:w-1/2">
                                <select id="packageStat" name="packageStat" class="hidden">
                                    <option selected data-route="Incoming">Incoming</option>
                                    <option data-route="Outgoing">Outgoing</option>
                                </select>

                                <button id="custom-dropdown-btn" data-stat="Incoming" class="w-full rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:outline-indigo-600 sm:text-sm/6">
                                    Incoming
                                </button>

                                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 size-5 text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                </svg>

                                <ul id="custom-dropdown" class="package_Logstat absolute z-10 mt-2 hidden w-full rounded-md bg-white shadow-lg transition-all duration-300 ease-in-out">
                                    <li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">Incoming</li>
                                    <li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">Outgoing</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-2 flow-root">
                            <div class="-mx-4 mt-2 px-2 overflow-x-auto sm:-mx-6 lg:-mx-8 bg-gray-900 rounded-sm">
                                <div class="inline-block min-w-full align-middle sm:px-6 lg:px-8 max-h-screen overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-700" id="packageLogs">
                                        <thead class="sticky top-0 bg-gray-900">
                                            <tr>
                                                <th class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white whitespace-nowrap">Mailbox #</th>
                                                <th class="py-3 pr-1 pl-1 text-left text-sm font-semibold text-white">Customer</th>
                                                <th class="py-3 pr-1 pl-1 text-left text-sm font-semibold text-white whitespace-nowrap">Phone Number</th>
                                                <th class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white whitespace-nowrap"># Of Packages</th>
                                                <th class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">Tracking Numbers</th>
                                                <th class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">Status</th>
                                                <th class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white whitespace-nowrap">Date Received</th>
                                                <th class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white whitespace-nowrap">Package ID #</th>
                                                <th id="actionsColumn" class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">
                                                    <span id="actionsText">Actions</span>
                                                    <button id="deleteAllBtn" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 whitespace-nowrap hidden">
                                                        Delete All
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-800" id="tableBody">
                                            @foreach ($packages as $packageGroup)
                                            <tr class="package-row">
                                                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white ">{{ $packageGroup->mailbox_number }}</td>
                                                <td class="py-3 pr-1 pl-1 text-left text-sm font-semibold text-white">{{ $packageGroup->customer_name }}</td>
                                                <td class="py-3 pr-1 pl-1 text-left text-sm font-semibold text-white">{{ $packageGroup->phone_number }}</td>
                                                <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">{{ $packageGroup->package_count }}</td>
                                                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">{!! implode('<br>', $packageGroup->tracking_numbers) !!}</td>
                                                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">{{ $packageGroup->status }}</td>
                                                <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">{{ \Carbon\Carbon::parse($packageGroup->date_received)->format('d/m/Y') }}</td>
                                                <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">{!! implode('<br>', $packageGroup->id) !!}</td>
                                                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">
                                                    @foreach ($packageGroup->tracking_numbers as $index => $trackingNumber)
                                                        @if ($packageGroup->status !== 'Outgoing')
                                                            {{-- Claim Package Button --}}
                                                            <button class="update-status-btn rounded-sm text-white border-blue-950 bg-blue-800 px-0.5 hover:bg-blue-900 hover:text-gray-500 whitespace-nowrap"
                                                                data-id="{{ $packageGroup->id[$index] }}" data-tracking="{{ $trackingNumber }}">
                                                                claim-package
                                                            </button><br>
                                                        @else
                                                            {{-- Delete Button when status is Outgoing --}}
                                                            <button type="button" class="delete-btn text-red-600 hover:text-red-900"
                                                                data-id="{{ $packageGroup->id[$index] }}">
                                                                Delete
                                                            </button><br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('sms.inbox', ['receivedMessages' => $receivedMessages, 'sentMessages' => $sentMessages])
            </div>
        </div>
    </div>
</main>
@endsection
