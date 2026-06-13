@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <div>
        <h1 class="text-4xl font-black">Riwayat Scan</h1>
        <p class="text-slate-500 mt-2">Lihat semua foto dan hasil klasifikasi yang telah tersimpan.</p>
    </div>

    <div class="bg-white rounded-3xl border p-6">

        @if($histories->count())
            <div class="grid gap-6">
                @foreach($histories as $history)
                    <div class="border rounded-3xl overflow-hidden shadow-sm">
                        <div class="md:flex">
                            @php
                                $storagePath = storage_path('app/public/' . ($history->image ?? ''));
                                if ($history->image && file_exists($storagePath)) {
                                    $imgUrl = asset('storage/' . $history->image);
                                } else {
                                    $imgUrl = asset('images/placeholder.png');
                                }
                            @endphp
                            <img src="{{ $imgUrl }}" alt="Scan Image" class="w-full md:w-48 h-40 object-cover" />
                            <div class="p-6 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-xl font-black">{{ $history->category }}</h2>
                                        <p class="text-sm text-slate-500">{{ $history->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-lime-600">{{ number_format($history->confidence, 2) }}%</span>
                                </div>

                                <div class="text-slate-700 text-sm">
                                    <p class="font-semibold">Rekomendasi:</p>
                                    <p class="whitespace-pre-line">{{ $history->recommendation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $histories->links() }}
            </div>
        @else
            <div class="text-center py-20 text-slate-500">
                Belum ada data scan tersimpan. Silakan lakukan upload foto monitoring terlebih dahulu.
            </div>
        @endif

    </div>

</div>

@endsection