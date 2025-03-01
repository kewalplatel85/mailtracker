<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white p-8 border border-gray-300 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-6 text-center">Forgot Password</h2>

        @if (session('status'))
            <p class="text-green-600 mb-4">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="text-red-500 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700">
                Send Password Reset Link
            </button>

            <p class="mt-4 text-sm text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to Login</a>
            </p>
        </form>
    </div>
</body>

</html>
