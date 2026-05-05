<?php
// app/Http/Controllers/BookingEventController.php

namespace App\Http\Controllers;

use App\Models\BookingEvent;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingEventController extends Controller
{
    public function index()
    {
        $events = BookingEvent::withCount(['bookings as booked_count' => function ($q) {
            $q->where('type', 'appointment');
        }])->orderBy('created_at', 'desc')->paginate(20);

        return view('booking-events.index', compact('events'));
    }

    public function create()
    {
        return view('booking-events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date|after:yesterday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval_minutes' => 'required|integer|min:5|max:60',
        ]);

        $event = BookingEvent::create($validated);
        $event->generateQrCode();

        return redirect()->route('booking-events.show', $event)
            ->with('success', 'Booking event created successfully.');
    }

    public function show(BookingEvent $bookingEvent)
    {
        $timeSlots = $bookingEvent->generateTimeSlots();
        $bookings = $bookingEvent->bookings()
            ->where('type', 'appointment')
            ->orderBy('time_slot')
            ->get();

        return view('booking-events.show', compact('bookingEvent', 'timeSlots', 'bookings'));
    }

    public function destroy(BookingEvent $bookingEvent)
    {
        $bookingEvent->update(['status' => 'cancelled']);
        return back()->with('success', 'Event cancelled.');
    }

    /**
     * Get the static walk-in QR code
     */
    public function walkInQr()
    {
        $qrPath = 'qr-codes/walk-in-static.svg';

        if (!file_exists(storage_path('app/public/' . $qrPath))) {
            QrCode::size(300)
                ->generate(route('booking.walk-in'), storage_path('app/public/' . $qrPath));
        }

        return response()->file(storage_path('app/public/' . $qrPath));
    }
}
