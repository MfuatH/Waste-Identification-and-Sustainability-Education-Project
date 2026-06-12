<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SmartWaste AI') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-page-enter {
            animation: pageEnter 0.4s ease-out forwards;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-50 overflow-x-hidden">

<div class="min-h-screen">
    <div class="flex flex-col lg:flex-row">

        <!-- Sidebar -->
        <div
            id="mobileSidebar"
            class="fixed inset-y-0 left-0 z-50 w-full max-w-xs transform -translate-x-full transition-transform duration-300 ease-in-out"
        >
            <x-sidebar />
        </div>

        <!-- Backdrop -->
        <div
            id="sidebarBackdrop"
            class="fixed inset-0 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-40"
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:overflow-hidden">

            <x-navbar />

            <main class="p-6 min-h-[calc(100vh-5rem)] animate-page-enter">
                @isset($slot)
                    {{ $slot }}
                @endisset

                @yield('content')
            </main>

        </div>

    </div>
</div>

@stack('scripts')



</body>
</html>
