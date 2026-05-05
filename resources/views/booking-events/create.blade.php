{{-- resources/views/booking-events/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Create Booking Event</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.booking-events.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Event Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border rounded px-3 py-2" placeholder="e.g., May 6 Appointment Slots">
        </div>

        <div>
            <label class="block font-medium">Date</label>
            <input type="date" name="event_date" value="{{ old('event_date') }}" required
                   class="w-full border rounded px-3 py-2">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Start Time</label>
                <input type="time" name="start_time" value="{{ old('start_time', '16:00') }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">End Time</label>
                <input type="time" name="end_time" value="{{ old('end_time', '18:00') }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block font-medium">Interval Per Person (minutes)</label>
            <select name="interval_minutes" class="w-full border rounded px-3 py-2">
                <option value="5">5 minutes (12 per hour)</option>
                <option value="10" selected>10 minutes (6 per hour)</option>
                <option value="15">15 minutes (4 per hour)</option>
                <option value="20">20 minutes (3 per hour)</option>
                <option value="30">30 minutes (2 per hour)</option>
            </select>
            <p class="text-sm text-gray-500 mt-1">
                If interval is 5 min, 12 people can book per hour.
            </p>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Create Event & Generate QR Code
        </button>
    </form>
</div>
@endsection
