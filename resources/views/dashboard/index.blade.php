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

            <div class="h-72">
                <canvas id="trendChart"></canvas>
            </div>

        </x-chart-card>

        <x-chart-card title="Waste Distribution">

            <div class="h-72">
                <canvas id="distributionChart"></canvas>
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
                @forelse($recentActivities as $activity)
                    <tr class="border-b">
                        <td class="py-4">
                            {{ $activity->created_at->format('d M Y H:i') }}
                        </td>

                        <td>
                            {{ $activity->category }}
                        </td>

                        <td>
                            {{ number_format($activity->confidence, 1) }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-slate-500">
                            No scan history available yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Scans per Day',
                data: @json($trendValues),
                borderColor: '#65a30d',
                backgroundColor: 'rgba(101, 163, 13, 0.15)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutBack',
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('distributionChart'), {
        type: 'pie',
        data: {
            labels: ['Plastic Waste', 'Organic Waste', 'E-Waste'],
            datasets: [{
                data: [{{ $plastic }}, {{ $organic }}, {{ $ewaste }}],
                backgroundColor: ['#3b82f6', '#22c55e', '#f59e0b'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutBack',
            },
        }
    });
</script>
@endpush

@endsection
