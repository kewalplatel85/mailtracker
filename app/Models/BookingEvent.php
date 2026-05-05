<?php
// app/Models/BookingEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingEvent extends Model
{
    protected $fillable = [
        'title',
        'event_date',
        'start_time',
        'end_time',
        'interval_minutes',
        'unique_link',
        'qr_code_path',
        'status'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    // In-memory cache for slots (lasts only during the request lifecycle)
    private $cachedSlots = null;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            $event->unique_link = Str::random(12);
        });
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Generate all possible time slots for this event
     * Uses in-memory caching to prevent multiple queries in same request
     */
    public function generateTimeSlots(): array
    {
        // Return cached slots if available (per request cache)
        if ($this->cachedSlots !== null) {
            return $this->cachedSlots;
        }

        $slots = [];
        $startTime = $this->start_time;
        $endTime = $this->end_time;

        // Convert to timestamps
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $interval = $this->interval_minutes * 60;

        // Get all booked slots in ONE query
        $bookedSlots = $this->bookings()
            ->where('booking_date', $this->event_date)
            ->where('status', '!=', 'cancelled')
            ->pluck('time_slot')
            ->map(function ($time) {
                return substr($time, 0, 5); // Normalize to HH:MM
            })
            ->toArray();

        for ($time = $start; $time < $end; $time += $interval) {
            $timeString = date('H:i', $time);
            $slots[] = [
                'time' => $timeString,
                'display' => date('h:i A', $time),
                'available' => !in_array($timeString, $bookedSlots)
            ];
        }

        // Cache in memory for this request
        $this->cachedSlots = $slots;

        return $slots;
    }

    /**
     * Clear the in-memory slot cache
     */
    public function clearSlotCache(): void
    {
        $this->cachedSlots = null;
    }

    /**
     * Get booking link for this event
     */
    public function getBookingLink(): string
    {
        return route('booking.appointment', ['link' => $this->unique_link]);
    }

    /**
     * Generate QR code for this event
     */
    public function generateQrCode(): string
    {
        $url = $this->getBookingLink();
        $filename = 'qr-codes/event-' . $this->id . '.svg';

        QrCode::size(300)->generate($url, storage_path('app/public/' . $filename));

        $this->qr_code_path = $filename;
        $this->save();

        return $filename;
    }
}
