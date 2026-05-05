<?php
// app/Http/Controllers/AdminBookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('bookingEvent');

        // Filter by date
        if ($request->filled('date')) {
            $query->where('booking_date', $request->date);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.bookings', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,checked_in,completed,no_show,cancelled'
        ]);

        $oldStatus = $booking->status;
        $newStatus = $request->status;

        $booking->update(['status' => $newStatus]);

        // Clear the event's slot cache when cancelling
        if ($newStatus === 'cancelled' && $booking->bookingEvent) {
            $booking->bookingEvent->clearSlotCache();
        }

        // Send SMS if checking in
        if ($newStatus === 'checked_in') {
            // Optional: Send "you're checked in" SMS
        }

        $message = match($newStatus) {
            'cancelled' => 'Booking cancelled. Time slot is now available for others.',
            'checked_in' => 'Client checked in successfully.',
            'completed' => 'Booking marked as completed.',
            'no_show' => 'Client marked as no-show.',
            default => 'Booking status updated.'
        };

        return back()->with('success', $message);
    }
}
