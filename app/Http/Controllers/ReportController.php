<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'organization' => 'nullable|string',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $bookings = Booking::whereBetween('booking_date', [$dateFrom, $dateTo])
            ->when($request->organization, function ($query, $org) {
                $query->where('organization', $org);
            })
            ->get();

        // Summary statistics
        $summary = [
            'total_visits' => $bookings->count(),
            'walk_ins' => $bookings->where('type', 'walk_in')->count(),
            'appointments' => $bookings->where('type', 'appointment')->count(),
            'by_service' => $bookings->groupBy('service')
                ->map(fn($group) => $group->count())
                ->mapWithKeys(fn($count, $service) => [Booking::getServices()[$service] ?? $service => $count]),
            'by_status' => $bookings->groupBy('status')
                ->map(fn($group) => $group->count()),
            'by_organization' => $bookings->groupBy('organization')
                ->map(fn($group) => $group->count()),
            'daily_breakdown' => $bookings->groupBy('booking_date')
                ->map(function ($group) {
                    return [
                        'total' => $group->count(),
                        'walk_ins' => $group->where('type', 'walk_in')->count(),
                        'appointments' => $group->where('type', 'appointment')->count(),
                    ];
                }),
        ];

        return view('reports.result', compact('summary', 'dateFrom', 'dateTo', 'bookings'));
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $bookings = Booking::whereBetween('booking_date', [$request->date_from, $request->date_to])
            ->when($request->organization, function ($query, $org) {
                $query->where('organization', $org);
            })
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="booking-report.csv"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Time', 'Type', 'Service', 'Name', 'Email', 'Contact', 'Organization', 'Queue #', 'Status']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_date,
                    $booking->time_slot ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $booking->type)),
                    Booking::getServices()[$booking->service] ?? $booking->service,
                    $booking->name,
                    $booking->email,
                    $booking->contact_number,
                    $booking->organization,
                    $booking->queue_number ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $booking->status)),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
