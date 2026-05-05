{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Generate Report</h1>

    <form action="{{ route('admin.reports.generate') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">From Date</label>
                <input type="date" name="date_from" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">To Date</label>
                <input type="date" name="date_to" required class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block font-medium">Filter by Organization (optional)</label>
            <input type="text" name="organization" class="w-full border rounded px-3 py-2"
                   placeholder="Leave blank for all organizations">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Generate Report
        </button>
    </form>
</div>
@endsection
