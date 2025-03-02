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

                        <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <label for="mailbox" class="block text-xs font-medium text-gray-900">Mailbox #</label>
                            <input type="text" name="mailbox" id="mailbox" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="0000">
                        </div>

                        <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <label for="pcounter" class="block text-xs font-medium text-gray-900">Number of Packages</label>
                            <input type="text" name="pcounter" id="pcounter" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="1">
                        </div>
                        <div class="rounded-md bg-white px-3 pt-2.5 w-full md:w-1/2 pb-1 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                            <label for="pcounter" class="block text-xs font-medium text-gray-900">Tracking number #</label>
                            <input type="text" name="pcounter" id="pcounter" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-lg/8" placeholder="000000000000">
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6 place-items-center space-y-2">
                        track items
                        @if(!empty($data))
                            <h2 class="text-xl font-semibold mb-4">Uploaded Data:</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-gray-300">
                                    @foreach($data as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                                <td class="border p-2">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endif
                        @if(isset($data) && count($data)>0)
                            <h2 class="text-xl font-semibold mb-4">Uploaded Data:</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-gray-300">
                                    @foreach($data as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                                <td class="border p-2">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endif
                    </div>
                  </div>

              </div>
        </main>
    @endsection
