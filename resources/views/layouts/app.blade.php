<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My App')</title>

    <!-- Toast Animation Styles -->
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-in-out;
        }

        .toast {
            transition: all 0.3s ease-in-out;
        }

        .toast:hover {
            transform: translateX(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>

    @vite(['resources/css/app.css','resources/js/app.js','resources/js/navi.js'])
</head>

<body class="h-full">
    <div class="min-h-full">

        @include('partials.navi') <!-- Navbar partial -->

        <main>
            @yield('content')
        </main>

        {{-- @include('partials.footer') <!-- Footer partial --> --}}
    </div>
</body>
</html>
