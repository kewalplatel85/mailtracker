{{-- resources/views/admin/bookings.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">All Bookings</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.reports.export', ['date_from' => now()->subDays(30)->toDateString(), 'date_to' => now()->toDateString()]) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                Export Last 30 Days
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="mt-1 w-full border rounded px-3 py-2">
                    <option value="">All Types</option>
                    <option value="walk_in" {{ request('type') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                    <option value="appointment" {{ request('type') == 'appointment' ? 'selected' : '' }}>Appointment</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Bookings Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time/Queue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $booking->booking_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($booking->type == 'appointment')
                                <span class="font-mono">{{ date('h:i A', strtotime($booking->time_slot)) }}</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                    Queue #{{ $booking->queue_number }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->name }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->email }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->organization }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($booking->type == 'walk_in') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ \App\Models\Booking::getServices()[$booking->service] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                        class="text-xs border rounded px-2 py-1
                                        @if($booking->status == 'pending') bg-yellow-50
                                        @elseif($booking->status == 'checked_in') bg-blue-50
                                        @elseif($booking->status == 'completed') bg-green-50
                                        @elseif($booking->status == 'no_show') bg-red-50
                                        @else bg-gray-50 @endif">
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="no_show" {{ $booking->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="mailto:{{ $booking->email }}" class="text-blue-600 hover:text-blue-900">Email</a>
                            @if($booking->bookingEvent)
                                <a href="{{ route('admin.booking-events.show', $booking->bookingEvent) }}"
                                   class="ml-2 text-gray-600 hover:text-gray-900">View Event</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No bookings found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
