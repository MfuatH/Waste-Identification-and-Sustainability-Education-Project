@extends('layouts.app')

@section('content')

<div class="bg-white rounded-3xl border h-[80vh] flex flex-col">

    <div class="border-b p-6 flex items-center gap-4">

        <div class="w-14 h-14 rounded-2xl bg-lime-500 flex items-center justify-center text-white text-2xl">
            🤖
        </div>

        <div>

            <h2 class="font-black text-xl">
                WISE AI Assistant
            </h2>

            <p class="text-sm text-slate-500">
                Powered by Gemma + FastAPI
            </p>

        </div>

    </div>

    <div class="flex-1 overflow-y-auto p-8 space-y-4 bg-slate-50">

        <div class="bg-white border rounded-2xl p-4 max-w-xl">
            Hello 👋 How can I help you today?
        </div>

        <div class="bg-lime-500 text-white rounded-2xl p-4 ml-auto max-w-xl">
            How do I recycle plastic bottles?
        </div>

        <div class="bg-white border rounded-2xl p-4 max-w-xl">
            Plastic bottles should be cleaned before recycling...
        </div>

    </div>

    <div class="border-t p-5">

        <div class="flex gap-3">

            <input
                type="text"
                placeholder="Ask about recycling..."
                class="flex-1 border rounded-2xl px-5">

            <button
                class="bg-lime-500 text-white px-8 rounded-2xl">

                Send

            </button>

        </div>

    </div>

</div>

@endsection