{{-- resources/views/booking/appointment.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment - {{ $event->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto p-6 mt-10">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h1 class="text-xl font-bold">{{ $event->title }}</h1>
            <p class="text-gray-600">
                {{ $event->event_date->format('F d, Y') }} |
                {{ date('h:i A', strtotime($event->start_time)) }} -
                {{ date('h:i A', strtotime($event->end_time)) }}
                | {{ $event->interval_minutes }} min intervals
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form id="bookingForm" action="{{ route('booking.appointment.store', $event->unique_link) }}" method="POST"
              class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf

            {{-- Hidden token to prevent double submission --}}
            <input type="hidden" name="booking_token" value="{{ $bookingToken }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium text-sm">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm">Contact Number *</label>
                    <input type="tel" name="contact_number" value="{{ old('contact_number') }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium text-sm">Organization *</label>
                    <input type="text" name="organization" value="{{ old('organization') }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block font-medium text-sm">Service *</label>
                <select name="service" required class="w-full border rounded px-3 py-2">
                    <option value="">Select service...</option>
                    @foreach($services as $key => $name)
                        <option value="{{ $key }}" {{ old('service') == $key ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-sm mb-2">Select Time Slot *</label>
                <div class="grid grid-cols-4 gap-2" id="slotContainer">
                    @foreach($timeSlots as $slot)
                        <button type="button"
                                onclick="selectSlot('{{ $slot['time'] }}', this)"
                                class="slot-btn p-2 text-center border rounded text-sm
                                    {{ $slot['available'] ? 'bg-green-50 border-green-300 hover:bg-green-100 cursor-pointer' : 'bg-red-50 border-red-200 cursor-not-allowed opacity-50' }}"
                                {{ $slot['available'] ? '' : 'disabled' }}
                                data-time="{{ $slot['time'] }}">
                            {{ $slot['display'] }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="time_slot" id="time_slot_input" required>
                <p id="selected_slot_display" class="text-sm text-gray-500 mt-1"></p>
            </div>

            <button type="submit" id="submitBtn"
                    class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Confirm Appointment
            </button>
        </form>
    </div>

    <script>
        let selectedSlot = null;
        let isSubmitting = false;

        function selectSlot(time, element) {
            selectedSlot = time;
            document.getElementById('time_slot_input').value = time;
            document.getElementById('selected_slot_display').textContent =
                'Selected: ' + element.textContent.trim();

            document.querySelectorAll('.slot-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-blue-500');
            });
            element.classList.add('ring-2', 'ring-blue-500');
        }

        // Prevent double submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                alert('Your booking is being processed. Please wait...');
                return false;
            }

            if (!selectedSlot) {
                e.preventDefault();
                alert('Please select a time slot.');
                return false;
            }

            isSubmitting = true;
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            return true;
        });

        // Refresh available slots every 30 seconds
        setInterval(function() {
            if (isSubmitting) return;

            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newSlots = doc.getElementById('slotContainer');
                    if (newSlots) {
                        document.getElementById('slotContainer').innerHTML = newSlots.innerHTML;
                    }
                })
                .catch(err => console.log('Failed to refresh slots'));
        }, 30000);
    </script>
</body>
</html>
