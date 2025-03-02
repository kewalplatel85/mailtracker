    @extends('layouts.app')
    @section('title','Dashboard')

    @section('content')
        <main>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="px-4 py-5 sm:px-6">
                      <h2 class="text-lg "> Mail All Center</h2>
                      <h4> Package Information</h4>
                      <!-- We use less vertical padding on card headers on desktop than on body sections -->
                    </div>
                    <div class="px-4 py-5 sm:p-6 place-items-center space-y-2">
                        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="w-full place-items-center">
                            @csrf
                            <div class="w-full md:w-1/2 flex space-x-2">
                                <div class="rounded-md flex-auto bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                    <input type="file" name="file" accept=".csv,.xlsx" required class="block w-full text-red-600 file:text-blue-600 file:bg-gray-100 file:rounded-md file:px-5 placeholder:text-gray-400 focus:outline-none sm:text-lg/8">
                                </div>
                                <div class="flex-none my-1">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Upload</button>
                                </div>
                            </div>
                        </form>

                        {{-- <div class="relative mt-2">
                            <button type="button" class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label">
                            <span class="col-start-1 row-start-1 flex items-center gap-3 pr-6">
                                <span aria-label="Online" class="inline-block size-2 shrink-0 rounded-full border border-transparent"></span>
                                <span class="block truncate">Tom Cook</span>
                            </span>
                            <svg class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M5.22 10.22a.75.75 0 0 1 1.06 0L8 11.94l1.72-1.72a.75.75 0 1 1 1.06 1.06l-2.25 2.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 0 1 0-1.06ZM10.78 5.78a.75.75 0 0 1-1.06 0L8 4.06 6.28 5.78a.75.75 0 0 1-1.06-1.06l2.25-2.25a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                            </svg>
                            </button>

                            <!--
                            Select popover, show/hide based on select state.

                            Entering: ""
                                From: ""
                                To: ""
                            Leaving: "transition ease-in duration-100"
                                From: "opacity-100"
                                To: "opacity-0"
                            -->
                            <ul class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base ring-1 shadow-lg ring-black/5 focus:outline-hidden sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-3">
                            <!--
                                Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation.

                                Highlighted: "bg-indigo-600 text-white outline-hidden", Not Highlighted: "text-gray-900"
                            -->
                            <li class="relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none" id="listbox-option-0" role="option">
                                <div class="flex items-center">
                                <!-- Online: "bg-green-400 forced-colors:bg-[Highlight]", Not Online: "bg-gray-200" -->
                                <span class="inline-block size-2 shrink-0 rounded-full border border-transparent" aria-hidden="true"></span>
                                <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                                <span class="ml-3 block truncate font-normal">
                                    Wade Cooper
                                    <span class="sr-only"> is online</span>
                                </span>
                                </div>

                                <!--
                                Checkmark, only display for selected option.

                                Highlighted: "text-white", Not Highlighted: "text-indigo-600"
                                -->
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </li>

                            <!-- More items... -->
                            </ul>
                        </div> --}}

                        <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <label for="mailbox" class="block text-xs font-medium text-gray-900">Mailbox #</label>
                            <input type="text" name="mailbox" id="mailbox" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="0000" oninput="this.value=this.value.replace(/\D/g,'')" list="mailbox-suggestions">
                        </div>

                        <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <label for="pcounter" class="block text-xs font-medium text-gray-900">Number of Packages</label>
                            <input type="text" name="pcounter" id="pcounter" min="1" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="0" oninput="this.value=this.value.replace(/\D/g,'')">
                        </div>
                        <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <label for="track-number" class="block text-xs font-medium text-gray-900">Tracking number #</label>
                            <input type="text" name="track_number" id="track_number" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="000000000000">
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
                    </div>

                    <div class="place-items-center space-y-2 hidden md:block">
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
                                            <datalist id="mailbox-suggestions">
                                                @foreach(array_slice($data,1) as $row)
                                                        <option value="{{ $row[0] }}"></option>
                                                @endforeach
                                            </datalist>
                                        @endif
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                    </div>

                </div>
            </div>
        </main>
    @endsection
