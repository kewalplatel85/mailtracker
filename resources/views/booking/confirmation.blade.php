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

                    @if(isset($booking['type']))
                        {{-- SMS Status Section --}}
                        <div class="border-t pt-2 mt-2">
                            @if(isset($sms_sent) && $sms_sent)
                                <div class="flex items-center text-green-600 text-sm">
                                    <svg class="size-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    📱 Confirmation SMS sent to {{ $booking['contact_number'] }}
                                </div>

                                @if($booking['type'] == 'walk_in')
                                    <p class="text-xs text-gray-500 mt-1">
                                        You'll receive your queue number via SMS
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">
                                        You'll receive appointment details via SMS
                                    </p>
                                @endif

                            @else
                                <div class="flex items-center text-amber-600 text-sm">
                                    <svg class="size-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    SMS delivery pending
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Please save your booking details
                                </p>
                            @endif
                        </div>
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
