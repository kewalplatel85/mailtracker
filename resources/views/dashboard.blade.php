    @extends('layouts.app')
    @section('title','Dashboard')

    @section('content')
        <main>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="px-4 py-5 sm:px-6">
                      <h2 class="text-lg "> Mail All Center</h2>
                      <h4> Package Information</h4>
                      <!-- We use less vertical padding on card headers on desktop than on body sections -->
                    </div>
                    <div class="px-4 pb-1 sm:px-6 place-items-center">
                        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="w-full place-items-center">
                            @csrf
                            <div class="w-full md:w-1/2 flex space-x-2">
                                <div class="rounded-md flex-auto bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                    <input type="file" name="file" accept=".csv,.xlsx" required class="block w-full text-red-600 file:text-blue-600 file:bg-gray-100 file:rounded-md file:px-5 placeholder:text-gray-400 focus:outline-none sm:text-lg/8">
                                </div>
                                <div class="flex-none my-1">
                                    <button type="submit" class="upload px-4 py-2 bg-blue-600 text-white rounded-lg">Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="px-4 sm:px-6 place-items-center">
                        <div class="w-full md:w-1/2 grid grid-flow-col grid-cols-2">
                            <div class="relative w-full">
                                <!-- Hidden Native Select -->
                                <select id="custTab1" name="" class="hidden">
                                    <option selected data-route="Current">Current Clients</option>
                                    <option data-route="New">New Clients</option>
                                </select>

                                <!-- Custom Dropdown Trigger -->
                                <button id="custTab1-dropdown-btn" data-stat="Current" class="custTab1 w-full rounded-md bg-blue-400 py-1.5 pr-8 pl-3 text-base font-bold text-white outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:outline-indigo-600 sm:text-sm/6">
                                    Current Clients
                                </button>

                                <!-- Dropdown Arrow -->
                                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 size-5 text-white sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>

                                <!-- Custom Dropdown -->
                                <ul id="custTab1-dropdown" class="custTab-ul absolute z-10 mt-2 hidden w-full rounded-md bg-blue-300 shadow-lg transition-all duration-300 ease-in-out">
                                    <li class="dropdown-item cursor-pointer rounded-sm px-4 py-2 hover:bg-blue-400 hover:text-white text-white">Current Clients</li>
                                    <li class="dropdown-item cursor-pointer rounded-sm px-4 py-2 hover:bg-blue-400 hover:text-white text-white">New Clients</li>
                                </ul>
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
                    </div>

                    <div class="place-items-center space-y-2 md:block">
                          <div class="bg-gray-900 w-full rounded-md">
                            <div class="mx-auto max-w-7xl">
                              <div class="bg-gray-900 py-6 rounded-md">
                                <div class="px-4 sm:px-6 lg:px-8">
                                  <div class="sm:flex sm:items-center">
                                    <div class="sm:flex-auto">
                                      <h1 class="text-base font-semibold text-white">Mail All Center</h1>
                                      <p class="mt-2 text-sm text-gray-300">Clients Information</p>
                                    </div>
                                  </div>
                                  <div class="mt-2 flow-root">
                                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                      <div class="inline-block min-w-full align-middle sm:px-6 lg:px-8 max-h-screen overflow-y-auto">
                                        @if(!empty($data))
                                            <table class="min-w-full divide-y divide-gray-700" id="clientTable">
                                                <thead class="sticky top-0">
                                                    <tr>
                                                    @foreach($data[0] as $header => $value)
                                                        <th scope="col" class="pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">{{ ucfirst($value) }}</th>
                                                    @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-800">
                                                    @foreach(array_slice($data,1) as $row)
                                                        <tr>
                                                        @foreach($row as $cell)
                                                            <td class="mailbox py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-0">{{ $cell }}</td>
                                                        @endforeach
                                                        </tr>
                                                    @endforeach
                                            <!-- More people... -->
                                                </tbody>
                                            </table>
                                            {{-- <datalist id="mailbox-suggestions" limit="10">
                                                @foreach(array_slice($data,1) as $row)
                                                        <option value="{{ $row[0] }}"></option>
                                                @endforeach
                                            </datalist>
                                            <datalist id="customer-suggestions" limit="10">
                                                @foreach(array_slice($data,1) as $row)
                                                        <option value="{{ $row[3] }}"></option>
                                                @endforeach
                                            </datalist> --}}
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
