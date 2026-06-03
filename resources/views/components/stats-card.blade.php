@props([
'title',
'value',
'icon',
'color' => 'lime'
])

<div class="bg-white rounded-3xl p-6 border shadow-sm">

    <div class="flex justify-between">

        <div>

            <p class="text-slate-500 text-sm">
                {{ $title }}
            </p>

            <h3 class="text-4xl font-black mt-3">
                {{ $value }}
            </h3>

        </div>

        <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-{{ $color }}-100 text-2xl">
            {{ $icon }}
        </div>

    </div>

</div>