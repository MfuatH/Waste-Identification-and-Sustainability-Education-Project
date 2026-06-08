@extends('layouts.guest')

@section('content')

<nav class="fixed top-0 w-full bg-white border-b z-50">
    <div class="max-w-7xl mx-auto px-6">

        <div class="h-20 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <div class="w-12 h-12 bg-lime-500 rounded-2xl flex items-center justify-center text-white text-xl font-black">
                    ♻
                </div>

                <div>
                    <h1 class="font-black text-xl text-lime-600">
                        WISE
                    </h1>

                    <p class="text-xs text-slate-500">
                        Waste Identification and Sustainability Education
                    </p>
                </div>

            </div>

            <div class="hidden md:flex gap-8 font-bold text-slate-600">

                <a href="#features">Features</a>
                <a href="#how">How It Works</a>
                <a href="#education">Education</a>
                <a href="#ai">AI Assistant</a>

            </div>

            <div class="flex gap-3 items-center">

                <a href="{{ route('login') }}"
                   class="hidden md:inline-flex px-5 py-3 rounded-xl border font-bold">
                    Login
                </a>

                <a href="{{ route('login') }}"
                   class="hidden md:inline-flex px-5 py-3 rounded-xl bg-lime-500 text-white font-bold">
                    Get Started
                </a>

                <button id="mobileMenuToggle" class="md:hidden p-3 rounded-2xl bg-slate-100 text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

            </div>
        </div>

        <div id="mobileMenu" class="md:hidden hidden bg-white border-b border-slate-200">
            <div class="px-6 pb-4 pt-4 space-y-3 font-bold text-slate-700">
                <a href="#features" class="block">Features</a>
                <a href="#how" class="block">How It Works</a>
                <a href="#education" class="block">Education</a>
                <a href="#ai" class="block">AI Assistant</a>
                <a href="{{ route('login') }}" class="block rounded-2xl border px-4 py-3">Login</a>
                <a href="{{ route('login') }}" class="block rounded-2xl bg-lime-500 text-white px-4 py-3">Get Started</a>
            </div>
        </div>

        </div>

    </div>
</nav>

<section class="pt-40 pb-28 bg-gradient-to-b from-lime-50 to-white">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <div>

                <div class="inline-flex px-4 py-2 rounded-full bg-lime-100 text-lime-600 font-bold text-sm mb-6">
                    🌱 AI Powered Environmental Platform
                </div>

                <h1 class="text-6xl font-black text-slate-900 leading-tight">
                    WISE
                    Intelligence
                    for Waste
                </h1>

                <p class="text-xl text-slate-500 mt-8 leading-relaxed">
                    Identify waste types instantly using Artificial Intelligence,
                    learn recycling methods, and contribute to a cleaner future.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">

                    <a href="{{ route('login') }}"
                       class="px-8 py-4 rounded-2xl bg-lime-500 text-white font-bold">
                        Start Scanning
                    </a>

                    <a href="#features"
                       class="px-8 py-4 rounded-2xl border font-bold">
                        Learn More
                    </a>

                </div>

            </div>

            <div>

                <div class="bg-white rounded-[40px] shadow-xl p-10 border">

                    <div class="grid grid-cols-2 gap-5">

                        <div class="bg-green-100 rounded-3xl p-6">
                            <h3 class="text-4xl">🍃</h3>
                            <p class="font-black mt-4">
                                Organic
                            </p>
                        </div>

                        <div class="bg-blue-100 rounded-3xl p-6">
                            <h3 class="text-4xl">🧴</h3>
                            <p class="font-black mt-4">
                                Plastic
                            </p>
                        </div>

                        <div class="bg-yellow-100 rounded-3xl p-6">
                            <h3 class="text-4xl">🥫</h3>
                            <p class="font-black mt-4">
                                Metal
                            </p>
                        </div>

                        <div class="bg-purple-100 rounded-3xl p-6">
                            <h3 class="text-4xl">💻</h3>
                            <p class="font-black mt-4">
                                E-Waste
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="py-24">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid md:grid-cols-4 gap-6">

            <div class="bg-white border rounded-3xl p-8 text-center">
                <h2 class="text-5xl font-black text-lime-500">95%</h2>
                <p class="mt-3 text-slate-500">AI Accuracy</p>
            </div>

            <div class="bg-white border rounded-3xl p-8 text-center">
                <h2 class="text-5xl font-black text-blue-500">10K+</h2>
                <p class="mt-3 text-slate-500">Images Trained</p>
            </div>

            <div class="bg-white border rounded-3xl p-8 text-center">
                <h2 class="text-5xl font-black text-orange-500">5</h2>
                <p class="mt-3 text-slate-500">Waste Categories</p>
            </div>

            <div class="bg-white border rounded-3xl p-8 text-center">
                <h2 class="text-5xl font-black text-purple-500">24/7</h2>
                <p class="mt-3 text-slate-500">AI Assistant</p>
            </div>

        </div>

    </div>

</section>

<section id="features" class="py-24 bg-slate-50">

<div class="max-w-7xl mx-auto px-6">

<h2 class="text-center text-5xl font-black mb-16">
Core Features
</h2>

<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

<div class="bg-white p-8 rounded-3xl border">
<h3 class="text-5xl mb-4">📸</h3>
<h4 class="font-black text-xl">AI Scanner</h4>
<p class="mt-4 text-slate-500">
Classify waste images automatically.
</p>
</div>

<div class="bg-white p-8 rounded-3xl border">
<h3 class="text-5xl mb-4">🤖</h3>
<h4 class="font-black text-xl">Gemma Chatbot</h4>
<p class="mt-4 text-slate-500">
Waste education assistant.
</p>
</div>

<div class="bg-white p-8 rounded-3xl border">
<h3 class="text-5xl mb-4">📚</h3>
<h4 class="font-black text-xl">Education</h4>
<p class="mt-4 text-slate-500">
Interactive recycling guides.
</p>
</div>

<div class="bg-white p-8 rounded-3xl border">
<h3 class="text-5xl mb-4">📊</h3>
<h4 class="font-black text-xl">Analytics</h4>
<p class="mt-4 text-slate-500">
Classification statistics.
</p>
</div>

</div>

</div>

</section>

<footer class="bg-slate-900 text-white py-20">

<div class="max-w-7xl mx-auto px-6 text-center">

<h2 class="text-4xl font-black">
Smart Waste AI Classification
</h2>

<p class="mt-6 text-slate-400">
        WISE — Waste Identification and Sustainability Education

</div>

</footer>

@endsection