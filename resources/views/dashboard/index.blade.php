@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <!-- Welcome Banner -->

    <div class="bg-gradient-to-r from-lime-500 to-green-600 rounded-[32px] p-10 text-white">

        <div class="flex flex-col lg:flex-row justify-between items-center">

            <div>

                <h1 class="text-4xl font-black">
                    Welcome Back, {{ Auth::user()->name ?? 'User' }}!
                </h1>

                <p class="mt-4 text-lg opacity-90">
                    Let's make the environment cleaner today with AI-powered waste classification.
                </p>

            </div>

            <div class="text-8xl mt-6 lg:mt-0">
                ♻️
            </div>

        </div>

    </div>

    <!-- Stats -->

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <x-stats-card
            title="Total Scans"
            value="{{ number_format($totalScans) }}"
            icon="📸"/>

        <x-stats-card
            title="Plastic Waste"
            value="{{ number_format($plastic) }}"
            icon="🧴"/>

        <x-stats-card
            title="Organic Waste"
            value="{{ number_format($organic) }}"
            icon="🍃"/>

        <x-stats-card
            title="E-Waste"
            value="{{ number_format($ewaste) }}"
            icon="💻"/>

    </div>

    <!-- Charts -->

    <div class="grid lg:grid-cols-2 gap-6">

        <x-chart-card title="Classification Trend">

            <div class="h-72 flex items-center justify-center text-slate-400">
                Chart.js Line Chart Here
            </div>

        </x-chart-card>

        <x-chart-card title="Waste Distribution">

            <div class="h-72 flex items-center justify-center text-slate-400">
                Pie Chart Here
            </div>

        </x-chart-card>

    </div>

    <!-- Recent Activity -->

    <div class="bg-white rounded-3xl border p-6">

        <h3 class="text-2xl font-black mb-5">
            Recent Activities
        </h3>

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="text-left py-3">Time</th>
                    <th class="text-left py-3">Waste Type</th>
                    <th class="text-left py-3">Confidence</th>

                </tr>

            </thead>

            <tbody>

                <tr class="border-b">
                    <td class="py-4">09:12</td>
                    <td>Plastic Bottle</td>
                    <td>96.7%</td>
                </tr>

                <tr class="border-b">
                    <td class="py-4">10:22</td>
                    <td>Banana Peel</td>
                    <td>98.3%</td>
                </tr>

                <tr>
                    <td class="py-4">11:54</td>
                    <td>Electronic Waste</td>
                    <td>94.1%</td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection
