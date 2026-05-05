{{-- resources/views/booking/slot-conflict.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Time Slot Unavailable - {{ $event->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto p-6 mt-10">

        {{-- Alert Banner --}}
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">
                        Sorry, this time slot was just taken
                    </h3>
                    <div class="mt-2 text-sm text-amber-700">
                        <p>
                            <strong>{{ $attemptedSlotDisplay }}</strong> was booked by another client just moments before you.
                            Don't worry - there {{ $availableCount === 1 ? 'is' : 'are' }} still
                            <strong>{{ $availableCount }} other time {{ $availableCount === 1 ? 'slot' : 'slots' }}</strong> available.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Event Info --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h1 class="text-xl font-bold text-blue-900">{{ $event->title }}</h1>
            <p class="text-blue-700">
                {{ $event->event_date->format('F d, Y') }} |
                {{ date('h:i A', strtotime($event->start_time)) }} -
                {{ date('h:i A', strtotime($event->end_time)) }}
            </p>
        </div>

        {{-- Quick Suggestions --}}
        @if($suggestions->isNotEmpty())
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-gray-900 mb-3">
                ⚡ Quick Pick - Similar Time Slots
            </h2>
            <p class="text-sm text-gray-600 mb-4">
                Click one to select it, then fill the form below:
            </p>
            <div class="grid grid-cols-3 gap-3">
                @foreach($suggestions as $slot)
                    <button type="button"
                            onclick="quickSelectSlot('{{ $slot['time'] }}')"
                            class="bg-green-50 border-2 border-green-300 text-green-700 px-4 py-2 rounded-lg hover:bg-green-100 font-medium transition">
                        {{ $slot['display'] }}
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Booking Form (Pre-filled with their info) --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Select Another Time</h2>

            <form id="bookingForm" action="{{ route('booking.appointment.store', $event->unique_link) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Contact Number *</label>
                        <input type="tel" name="contact_number" value="{{ old('contact_number') }}" required
                               class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Organization *</label>
                        <input type="text" name="organization" value="{{ old('organization') }}" required
                               class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-sm text-gray-700">Service *</label>
                    <select name="service" required
                            class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select service...</option>
                        @foreach($services as $key => $name)
                            <option value="{{ $key }}" {{ old('service') == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block font-medium text-sm text-gray-700">Available Time Slots *</label>
                        <span class="text-xs text-gray-500">
                            Refreshes automatically every 30 seconds
                        </span>
                    </div>

                    <div class="grid grid-cols-4 gap-2" id="slotContainer">
                        @foreach($timeSlots as $slot)
                            <button type="button"
                                    onclick="selectSlot('{{ $slot['time'] }}', this)"
                                    class="slot-btn p-2 text-center border rounded text-sm
                                        {{ $slot['available'] ? 'bg-green-50 border-green-300 hover:bg-green-100 cursor-pointer text-gray-900' : 'bg-gray-100 border-gray-200 cursor-not-allowed text-gray-400 line-through' }}"
                                    {{ $slot['available'] ? '' : 'disabled' }}
                                    data-time="{{ $slot['time'] }}">
                                {{ $slot['display'] }}
                                @if(!$slot['available'])
                                    <span class="block text-xs">Taken</span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="time_slot" id="time_slot_input" required>

                    <div id="selected_slot_display" class="mt-2 text-sm">
                        @if(old('time_slot'))
                            <span class="text-gray-500">
                                Previously selected:
                                <span class="line-through">{{ date('h:i A', strtotime(old('time_slot'))) }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Confirm Appointment
                </button>
            </form>
        </div>

        {{-- Help Text --}}
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>💡 Tip: Popular time slots fill up quickly. Book early to secure your preferred time!</p>
        </div>
    </div>

    <script>
        let selectedSlot = null;
        let isSubmitting = false;

        function selectSlot(time, element) {
            // Only allow selection of available slots
            if (element.disabled) return;

            selectedSlot = time;
            document.getElementById('time_slot_input').value = time;

            const display = element.textContent.trim().split('\n')[0];
            document.getElementById('selected_slot_display').innerHTML =
                '<span class="text-green-600 font-medium">✓ Selected: ' + display + '</span>';

            // Update button styling
            document.querySelectorAll('.slot-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
            });
            element.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
        }

        function quickSelectSlot(time) {
            // Find the slot button and click it programmatically
            const buttons = document.querySelectorAll('.slot-btn');
            buttons.forEach(btn => {
                if (btn.dataset.time === time && !btn.disabled) {
                    selectSlot(time, btn);
                    btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }

        // Prevent double submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            if (!selectedSlot) {
                e.preventDefault();
                alert('Please select an available time slot.');
                return false;
            }

            isSubmitting = true;
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="inline-flex items-center">' +
                '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">' +
                '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                '</svg>' +
                'Processing...' +
                '</span>';

            return true;
        });

        // Auto-refresh available slots every 30 seconds
        let refreshInterval;
        function startAutoRefresh() {
            refreshInterval = setInterval(refreshSlots, 30000);
        }

        async function refreshSlots() {
            if (isSubmitting) return;

            try {
                const response = await fetch(window.location.href);
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newSlotContainer = doc.getElementById('slotContainer');
                if (newSlotContainer) {
                    const oldContainer = document.getElementById('slotContainer');
                    oldContainer.innerHTML = newSlotContainer.innerHTML;

                    // Re-attach event listeners to new buttons
                    oldContainer.querySelectorAll('.slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            selectSlot(this.dataset.time, this);
                        });
                    });
                }
            } catch (err) {
                console.log('Failed to refresh slots');
            }
        }

        // Start auto-refresh
        startAutoRefresh();

        // Clean up on page leave
        window.addEventListener('beforeunload', () => {
            clearInterval(refreshInterval);
        });
    </script>
</body>
</html>
