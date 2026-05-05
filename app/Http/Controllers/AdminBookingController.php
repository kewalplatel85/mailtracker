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

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Booking status updated successfully.');
    }
}
