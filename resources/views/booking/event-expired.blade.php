{{-- resources/views/booking/event-expired.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Event Expired</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6 mt-20 text-center">
        <div class="mb-6">
            <span class="text-6xl">⏰</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Event Expired</h1>
        <p class="text-gray-600 mb-6">
            This booking event is no longer active.
        </p>
        <a href="{{ route('booking.walk-in') }}"
           class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Visit as Walk-in Instead
        </a>
    </div>
</body>
</html>
