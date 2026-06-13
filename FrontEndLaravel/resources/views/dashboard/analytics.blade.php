@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <h1 class="text-4xl font-black">
        Analytics Dashboard
    </h1>

    <div class="grid md:grid-cols-4 gap-6">

        <x-stats-card
            title="Total Classifications"
            value="12,483"
            icon="📸"/>

        <x-stats-card
            title="Users"
            value="1,847"
            icon="👥"/>

        <x-stats-card
            title="AI Accuracy"
            value="95%"
            icon="🧠"/>

        <x-stats-card
            title="Categories"
            value="5"
            icon="♻️"/>

    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        <x-chart-card title="Monthly Classification">

            <div class="h-80 flex items-center justify-center text-slate-400">
                Line Chart
            </div>

        </x-chart-card>

        <x-chart-card title="Waste Distribution">

            <div class="h-80 flex items-center justify-center text-slate-400">
                Pie Chart
            </div>

        </x-chart-card>

    </div>

</div>

@endsection
