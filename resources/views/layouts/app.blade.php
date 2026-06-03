<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SmartWaste AI') }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body{
            font-family:'Nunito',sans-serif;
        }

        .sidebar-scroll::-webkit-scrollbar{
            width:6px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb{
            background:#d1d5db;
            border-radius:10px;
        }
    </style>
</head>
<body class="bg-slate-50">

<div class="flex min-h-screen">

    <x-sidebar />

    <div class="flex-1 flex flex-col">

        <x-navbar />

        <main class="p-6">
            {{ $slot ?? '' }}

            @yield('content')
        </main>

    </div>

</div>

</body>
</html>