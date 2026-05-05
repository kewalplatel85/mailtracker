{{-- resources/views/booking/confirmation.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6 mt-20">
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <div class="mb-4">
                <svg class="mx-auto size-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>

            @if(isset($booking))
                <div class="bg-gray-50 rounded-lg p-4 mt-4 text-left space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-medium">{{ $booking['name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $booking['email'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Contact:</span>
                        <span class="font-medium">{{ $booking['contact_number'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Organization:</span>
                        <span class="font-medium">{{ $booking['organization'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium">{{ $service_name ?? 'N/A' }}</span>
                    </div>

                    @if(isset($booking['type']) && $booking['type'] == 'walk_in')
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="text-gray-600">Queue Number:</span>
                            <span class="text-xl font-bold text-blue-600">#{{ $booking['queue_number'] ?? 'N/A' }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2 text-center">
                            Please wait for your queue number to be called.
                        </p>
                    @endif

                    @if(isset($event))
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $event->event_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">{{ date('h:i A', strtotime($booking['time_slot'])) }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2 text-center">
                            Please arrive 5 minutes before your appointment.
                        </p>
                    @endif
                </div>
            @endif

            <a href="{{ route('booking.walk-in') }}" class="inline-block mt-6 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Register Another
            </a>
        </div>
    </div>
</body>
</html>
