{{-- resources/views/booking-events/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Booking Events</h1>
            <p class="text-gray-600 mt-1">Manage time slots and generate QR codes for appointments</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.walk-in.qr') }}" target="_blank"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                Download Walk-in QR
            </a>
            <a href="{{ route('admin.booking-events.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                + Create New Event
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-sm text-gray-500">Total Events</p>
            <p class="text-2xl font-bold">{{ $events->total() }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-sm text-gray-500">Active Events</p>
            <p class="text-2xl font-bold text-green-600">{{ $events->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-sm text-gray-500">Completed</p>
            <p class="text-2xl font-bold text-blue-600">{{ $events->where('status', 'completed')->count() }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-sm text-gray-500">Cancelled</p>
            <p class="text-2xl font-bold text-red-600">{{ $events->where('status', 'cancelled')->count() }}</p>
        </div>
    </div>

    {{-- Events Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interval</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($event->qr_code_path)
                                    <img src="{{ asset('storage/' . $event->qr_code_path) }}"
                                         class="size-8 mr-3" alt="QR">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                    <div class="text-xs text-gray-500">
                                        Link: {{ $event->getBookingLink() }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $event->event_date->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ date('h:i A', strtotime($event->start_time)) }} -
                                {{ date('h:i A', strtotime($event->end_time)) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $event->interval_minutes }} mins
                            <span class="text-xs text-gray-400 block">
                                ({{ floor(60 / $event->interval_minutes) }}/hr)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $totalSlots = count($event->generateTimeSlots());
                                $bookedSlots = $totalSlots - collect($event->generateTimeSlots())
                                    ->where('available', true)->count();
                            @endphp
                            <div class="text-sm font-medium text-gray-900">
                                {{ $bookedSlots }} / {{ $totalSlots }}
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-blue-600 h-2 rounded-full"
                                     style="width: {{ $totalSlots > 0 ? ($bookedSlots / $totalSlots) * 100 : 0 }}%">
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($event->status === 'active') bg-green-100 text-green-800
                                @elseif($event->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('admin.booking-events.show', $event) }}"
                               class="text-blue-600 hover:text-blue-900">View</a>

                            <button onclick="copyLink('{{ $event->getBookingLink() }}')"
                                    class="text-gray-600 hover:text-gray-900">Copy Link</button>

                            <a href="{{ $event->getBookingLink() }}" target="_blank"
                               class="text-green-600 hover:text-green-900">Open Form ↗</a>

                            @if($event->status === 'active')
                                <form action="{{ route('admin.booking-events.destroy', $event) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to cancel this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto size-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <p class="text-lg font-medium">No booking events yet</p>
                            <p class="text-sm mt-1">Create your first event to generate QR codes and booking links</p>
                            <a href="{{ route('admin.booking-events.create') }}"
                               class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                Create New Event
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>

<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Create a temporary toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50';
        toast.textContent = 'Link copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection
