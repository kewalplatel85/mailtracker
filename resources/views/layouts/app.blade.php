<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My App')</title>
    
    @yield('head')

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

        /* SMS Inbox Styles */
        #sms-inbox {
            font-family: inherit;
        }

        #inbox-panel {
            width: 400px;
            right: 0;
            bottom: 100px;
            max-width: 90vw;
        }

        .tab-link.text-blue-600 {
            border-bottom: 2px solid #2563eb;
            color: #2563eb !important;
        }

        .autocomplete-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }

        .autocomplete-suggestions .suggestion-item {
            padding: 0.5rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }

        .autocomplete-suggestions .suggestion-item:hover {
            background-color: #f9fafb;
        }

        .autocomplete-suggestions .suggestion-item:last-child {
            border-bottom: none;
        }

        @media (max-width: 640px) {
            #inbox-panel {
                width: 350px;
                min-h-[600px];
                max-h-[600px];
            }
        }
    </style>

    @vite(['resources/css/app.css','resources/js/app.js','resources/js/navi.js','resources/js/sms-inbox.js'])
</head>

<body class="h-full">
    <div class="min-h-full">

        @include('partials.navi') <!-- Navbar partial -->

        <main>
            @yield('content')
        </main>

        {{-- @include('partials.footer') <!-- Footer partial --> --}}

        <!-- Global SMS Inbox Component -->
        @auth
            @include('components.sms-inbox')
        @endauth
    </div>
</body>
</html>
