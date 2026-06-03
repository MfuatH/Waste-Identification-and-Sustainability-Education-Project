@extends('layouts.app')

@section('content')

<h1 class="text-4xl font-black mb-8">
    Recycling Education
</h1>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">

    @foreach([
        ['🍃','Organic Waste'],
        ['🧴','Plastic Waste'],
        ['🍾','Glass Waste'],
        ['🥫','Metal Waste'],
        ['💻','Electronic Waste'],
        ['♻️','General Recycling']
    ] as $item)

    <div class="bg-white border rounded-3xl p-8 hover:shadow-xl transition">

        <div class="text-6xl">
            {{ $item[0] }}
        </div>

        <h3 class="text-2xl font-black mt-6">
            {{ $item[1] }}
        </h3>

        <p class="text-slate-500 mt-4">
            Learn proper handling and recycling methods.
        </p>

        <button
            class="mt-6 bg-lime-500 text-white px-6 py-3 rounded-xl">

            Learn More

        </button>

    </div>

    @endforeach

</div>

@endsection