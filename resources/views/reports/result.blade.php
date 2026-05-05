{{-- resources/views/reports/result.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Booking Report</h1>
        <a href="{{ route('admin.reports.export', request()->all()) }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Export CSV
        </a>
    </div>

    <p class="text-gray-600 mb-6">{{ $dateFrom }} to {{ $dateTo }}</p>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Total Visits</p>
            <p class="text-3xl font-bold">{{ $summary['total_visits'] }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Walk-ins</p>
            <p class="text-3xl font-bold">{{ $summary['walk_ins'] }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Appointments</p>
            <p class="text-3xl font-bold">{{ $summary['appointments'] }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p class="text-sm text-gray-500">Organizations</p>
            <p class="text-3xl font-bold">{{ count($summary['by_organization']) }}</p>
        </div>
    </div>

    {{-- By Service Breakdown --}}
    <div class="bg-white shadow rounded p-6 mb-8">
        <h2 class="font-semibold text-lg mb-4">By Service</h2>
        <table class="w-full">
            <thead>
                <tr class="text-left border-b">
                    <th class="pb-2">Service</th>
                    <th class="pb-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['by_service'] as $service => $count)
                    <tr class="border-b">
                        <td class="py-2">{{ $service }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Daily Breakdown --}}
    <div class="bg-white shadow rounded p-6 mb-8">
        <h2 class="font-semibold text-lg mb-4">Daily Breakdown</h2>
        <table class="w-full">
            <thead>
                <tr class="text-left border-b">
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Total</th>
                    <th class="pb-2">Walk-ins</th>
                    <th class="pb-2">Appointments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['daily_breakdown'] as $date => $data)
                    <tr class="border-b">
                        <td class="py-2">{{ $date }}</td>
                        <td>{{ $data['total'] }}</td>
                        <td>{{ $data['walk_ins'] }}</td>
                        <td>{{ $data['appointments'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
