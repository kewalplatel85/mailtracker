<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'booking_event_id',
        'name',
        'email',
        'contact_number',
        'organization',
        'service',
        'type',
        'booking_date',
        'time_slot',
        'queue_number',
        'status'
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function bookingEvent(): BelongsTo
    {
        return $this->belongsTo(BookingEvent::class);
    }

    public static function getServices(): array
    {
        return [
            'live_scan' => 'Live Scan',
            'fd_258' => 'FD 258 Out of State Fingerprint',
            'notary_public' => 'Notary Public',
            'passport_photo' => 'Passport Photo',
            'tsa_pre_check' => 'TSA Pre Check',
            'hazmat' => 'Hazmat',
            'twic' => 'TWIC',
        ];
    }

    /**
     * Generate queue number for walk-in clients
     */
    public static function generateQueueNumber(string $date): int
    {
        $lastQueue = static::where('booking_date', $date)
            ->where('type', 'walk_in')
            ->max('queue_number');

        return ($lastQueue ?? 0) + 1;
    }
}
