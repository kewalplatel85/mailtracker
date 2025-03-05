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
                          <div class="relative mt-2 w-full md:w-1/2">
                            <!-- Hidden Native Select -->
                            <select id="packageStat" name="packageStat" class="hidden">
                                <option selected data-route="Incoming">Incoming</option>
                                <option data-route="Outgoing">Outgoing</option>
                            </select>

                            <!-- Custom Dropdown Trigger -->
                            <button id="custom-dropdown-btn" data-stat="Incoming" class=" w-full rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:outline-indigo-600 sm:text-sm/6">
                                Incoming
                            </button>

                            <!-- Dropdown Arrow -->
                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 size-5 text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>

                            <!-- Custom Dropdown -->
                            <ul id="custom-dropdown" class="package_Logstat absolute z-10 mt-2 hidden w-full rounded-md bg-white shadow-lg transition-all duration-300 ease-in-out">
                                <li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">Incoming</li>
                                <li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">Outgoing</li>
                            </ul>
                        </div>
                        </div>
                        <div class="mt-2 flow-root">
                          <div class="-mx-4 mt-2 px-2 overflow-x-auto sm:-mx-6 lg:-mx-8 bg-gray-900 rounded-sm">
                            <div class="inline-block min-w-full align-middle sm:px-6 lg:px-8 max-h-screen overflow-y-auto">
                              @if(!empty($packageLogs))
                                  <table class="min-w-full divide-y divide-gray-700" id="packageLogs">
                                      <thead class="sticky top-0">
                                          <tr>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Mailbox #</th>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Customer</th>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Phone Number</th>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Number of packages</th>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Tracking numbers</th>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Status</th>
                                            <th scope="col" class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">Date Recieved</th>
                                          </tr>
                                      </thead>
                                      <tbody class="divide-y divide-gray-800">
                                          @foreach ($packageLogs->groupBy('mailbox_number') as $mailboxNumber =>$package)
                                          @php
                                            $firstPackage = $package->first(); // Accessing first record for static fields
                                          @endphp
                                            <tr>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $mailboxNumber }}</td>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $firstPackage->customer_name }}</td>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $firstPackage->phone_number }}</td>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $package->count() }}</td>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{!! $package->pluck('tracking_number')->implode(', <br>') !!}</td>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $firstPackage->first()->status }}</td>
                                                <td class="mailbox py-3 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $firstPackage->first()->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                          @endforeach
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
            @include('sms.inbox', ['messages' => $showMessages['messages']])
        </main>
    @endsection
