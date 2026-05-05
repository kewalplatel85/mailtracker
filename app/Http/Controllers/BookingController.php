<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use App\Services\TwilioSMSService;
class BookingController extends Controller
{
    protected $smsService;

    public function __construct(TwilioSMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show walk-in registration form
     */
    public function walkInForm()
    {
        $services = Booking::getServices();
        return view('booking.walk-in', compact('services'));
    }

    /**
     * Store walk-in booking
     */
    public function storeWalkIn(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'contact_number' => 'required|string|max:20',
            'organization' => 'required|string|max:255',
            'service' => 'required|in:' . implode(',', array_keys(Booking::getServices())),
        ]);

        $validated['type'] = 'walk_in';
        $validated['booking_date'] = now()->toDateString();
        $validated['queue_number'] = Booking::generateQueueNumber(now()->toDateString());
        $validated['status'] = 'pending';

        $booking = Booking::create($validated);

        // Send SMS with queue number
        $smsSent = $this->smsService->sendQueueNotification($booking);

        // Pass the model directly (not toArray()) so the view can handle both
        return view('booking.confirmation', [
            'booking' => $booking,  // Pass the model object
            'service_name' => Booking::getServices()[$validated['service']],
            'sms_sent' => $smsSent,
        ]);
    }

      /**
        * Send SMS notification to walk-in client
     */
    private function sendWalkInSMS($booking)
    {
        try {
            // Format phone number (remove any non-numeric characters)
            $phoneNumber = preg_replace('/[^0-9]/', '', $booking->contact_number);

            // Add country code if not present (assuming US +1)
            if (strlen($phoneNumber) == 10) {
                $phoneNumber = '+1' . $phoneNumber;
            } elseif (strlen($phoneNumber) > 10 && substr($phoneNumber, 0, 1) != '+') {
                $phoneNumber = '+' . $phoneNumber;
            }

            $twilio = new Client(
                env('TWILIO_SID'),
                env('TWILIO_AUTH_TOKEN')
            );

            $message = "Hi {$booking->name}, your walk-in queue number is #{$booking->queue_number}. ";
            $message .= "Service: " . Booking::getServices()[$booking->service] . ". ";
            $message .= "Date: " . now()->format('M d, Y') . ". ";
            $message .= "Please wait for your number to be called. Thank you!";

            $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $message
                ]
            );

            Log::info("SMS sent to {$booking->name} for queue #{$booking->queue_number}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send SMS to {$booking->name}: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Show appointment booking form
     */
    public function appointmentForm($link)
    {
        $event = BookingEvent::where('unique_link', $link)
            ->where('status', 'active')
            ->firstOrFail();

        // Check if event is in the past
        if ($event->event_date < now()->startOfDay()) {
            return view('booking.event-expired', compact('event'));
        }

        $timeSlots = $event->generateTimeSlots();
        $services = Booking::getServices();

        // Check if any slots are available
        $availableSlots = collect($timeSlots)->where('available', true);
        if ($availableSlots->isEmpty()) {
            return view('booking.fully-booked', compact('event'));
        }

        // Generate a unique booking token to prevent double submission
        $bookingToken = md5(uniqid(rand(), true));

        return view('booking.appointment', compact('event', 'timeSlots', 'services', 'bookingToken'));
    }

    /**
     * Store appointment booking with race condition protection
     */
    public function storeAppointment(Request $request, $link)
    {
        $event = BookingEvent::where('unique_link', $link)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'contact_number' => 'required|string|max:20',
            'organization' => 'required|string|max:255',
            'service' => 'required|in:' . implode(',', array_keys(Booking::getServices())),
            'time_slot' => 'required|date_format:H:i',
        ]);

        try {
            $booking = DB::transaction(function () use ($event, $validated) {
                // Lock event row to prevent race conditions
                $lockedEvent = BookingEvent::where('id', $event->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Check event is still active
                if ($lockedEvent->status !== 'active') {
                    throw new \Exception('event_inactive');
                }

                // Check if slot is available
                $existingBooking = Booking::where('booking_event_id', $lockedEvent->id)
                    ->where('booking_date', $lockedEvent->event_date)
                    ->where('time_slot', $validated['time_slot'])
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->exists();

                if ($existingBooking) {
                    throw new \Exception('slot_taken');
                }

                // Create the booking
                return $lockedEvent->bookings()->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'contact_number' => $validated['contact_number'],
                    'organization' => $validated['organization'],
                    'service' => $validated['service'],
                    'type' => 'appointment',
                    'booking_date' => $lockedEvent->event_date,
                    'time_slot' => $validated['time_slot'],
                    'status' => 'pending',
                ]);
            }, 3);

            // Success - Send SMS confirmation for appointment
            $smsSent = $this->smsService->sendAppointmentConfirmation($booking);

            // Success - show confirmation
            return view('booking.confirmation', [
                'booking' => $booking->toArray(),
                'service_name' => Booking::getServices()[$validated['service']],
                'event' => $event,
                'sms_sent' => $smsSent,
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // Duplicate entry - someone booked milliseconds faster
                return $this->handleBookingConflict($event, $validated);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($e->getMessage() === 'slot_taken') {
                return $this->handleBookingConflict($event, $validated);
            }
            if ($e->getMessage() === 'event_inactive') {
                return view('booking.event-expired', compact('event'));
            }

            return back()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Handle booking conflict gracefully
     */
    private function handleBookingConflict($event, $validated)
    {
        // Refresh available slots
        $timeSlots = $event->generateTimeSlots();
        $availableSlots = collect($timeSlots)->where('available', true);
        $services = Booking::getServices();

        // Get alternative suggestions (next 3 available slots)
        $suggestions = $availableSlots->take(3)->map(function ($slot) {
            return [
                'time' => $slot['time'],
                'display' => $slot['display']
            ];
        });

        return view('booking.slot-conflict', [
            'event' => $event,
            'timeSlots' => $timeSlots,
            'services' => $services,
            'attemptedSlot' => $validated['time_slot'],
            'attemptedSlotDisplay' => date('h:i A', strtotime($validated['time_slot'])),
            'suggestions' => $suggestions,
            'availableCount' => $availableSlots->count(),
        ]);
    }
}
