{{-- resources/views/booking-events/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $bookingEvent->title }}</h1>
            <p class="text-gray-600">
                {{ $bookingEvent->event_date->format('F d, Y') }} |
                {{ date('h:i A', strtotime($bookingEvent->start_time)) }} -
                {{ date('h:i A', strtotime($bookingEvent->end_time)) }}
                | {{ $bookingEvent->interval_minutes }} min intervals
            </p>
        </div>
        <span class="px-3 py-1 rounded {{ $bookingEvent->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ ucfirst($bookingEvent->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- QR Code --}}
        <div class="border rounded-lg p-6 text-center">
            <h2 class="font-semibold mb-4">Scan to Book Appointment</h2>
            @if($bookingEvent->qr_code_path)
                <img src="{{ asset('storage/' . $bookingEvent->qr_code_path) }}"
                     alt="QR Code" class="mx-auto w-64 h-64">
            @endif
            <div class="mt-4">
                <p class="text-sm text-gray-500">Or share this link:</p>
                <input type="text" value="{{ $bookingEvent->getBookingLink() }}" readonly
                       class="w-full border rounded px-3 py-1 text-sm mt-1 bg-gray-50"
                       onclick="this.select()">
            </div>
        </div>

        {{-- Time Slots --}}
        <div class="border rounded-lg p-6">
            <h2 class="font-semibold mb-4">Time Slots ({{ count($timeSlots) }} total)</h2>
            <div class="max-h-64 overflow-y-auto space-y-2">
                @foreach($timeSlots as $slot)
                    <div class="flex justify-between items-center p-2 rounded {{ $slot['available'] ? 'bg-green-50' : 'bg-red-50' }}">
                        <span class="font-mono">{{ $slot['display'] }}</span>
                        <span class="text-sm {{ $slot['available'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $slot['available'] ? 'Available' : 'Booked' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bookings List --}}
    <div class="border rounded-lg p-6">
        <h2 class="font-semibold mb-4">Current Bookings ({{ $bookings->count() }})</h2>
        <table class="w-full">
            <thead>
                <tr class="text-left border-b">
                    <th class="pb-2">Time</th>
                    <th class="pb-2">Name</th>
                    <th class="pb-2">Organization</th>
                    <th class="pb-2">Service</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr class="border-b">
                        <td class="py-2">{{ date('h:i A', strtotime($booking->time_slot)) }}</td>
                        <td>{{ $booking->name }}</td>
                        <td>{{ $booking->organization }}</td>
                        <td>{{ \App\Models\Booking::getServices()[$booking->service] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
