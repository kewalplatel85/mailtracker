{{-- resources/views/booking/fully-booked.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Fully Booked - {{ $event->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6 mt-20 text-center">
        <div class="mb-6">
            <span class="text-6xl">😔</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Fully Booked!</h1>
        <p class="text-gray-600 mb-6">
            All time slots for <strong>{{ $event->title }}</strong> on
            {{ $event->event_date->format('F d, Y') }} have been booked.
        </p>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>What can you do?</strong><br>
                • Check back later for cancellations<br>
                • Visit us as a walk-in during business hours<br>
                • Contact us for alternative arrangements
            </p>
        </div>
        {{-- Show walk-in QR option --}}
        <div class="mt-6">
            <p class="text-sm text-gray-500 mb-3">Or visit us as a walk-in:</p>
            <a href="{{ route('booking.walk-in') }}"
               class="inline-block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                Walk-in Registration
            </a>
        </div>
    </div>
</body>
</html>
