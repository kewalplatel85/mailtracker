{{-- resources/views/booking/walk-in.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Walk-in Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6 mt-10">
        <h1 class="text-2xl font-bold mb-2">Walk-in Registration</h1>
        <p class="text-gray-600 mb-6">Please fill in your details to join the queue.</p>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form action="{{ route('booking.walk-in') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            <div>
                <label class="block font-medium text-sm">Full Name *</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium text-sm">Email *</label>
                <input type="email" name="email" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium text-sm">Contact Number *</label>
                <input type="tel" name="contact_number" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium text-sm">Organization *</label>
                <input type="text" name="organization" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium text-sm">Reason for Visit *</label>
                <select name="service" required class="w-full border rounded px-3 py-2">
                    <option value="">Select service...</option>
                    @foreach($services as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Join Queue
            </button>
        </form>
    </div>
</body>
</html>
