@props([
    'title',
    'showAction' => true,
])

<div class="bg-white rounded-3xl border p-6 shadow-sm animate-chart-appear">

    <div class="flex justify-between items-center mb-5">

        <h3 class="text-xl font-black">
            {{ $title }}
        </h3>

        @if($showAction)
            <button class="text-lime-600 text-sm font-bold">
                View More
            </button>
        @endif

    </div>

    {{ $slot }}

</div>