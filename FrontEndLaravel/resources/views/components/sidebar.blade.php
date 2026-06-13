<aside class="w-full max-w-xs lg:w-72 lg:flex-none bg-white border-r border-slate-200 min-h-screen lg:sticky top-0 shadow-sm">

    <div class="h-20 border-b border-slate-300 flex items-center px-6">

        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-2xl bg-lime-500 flex items-center justify-center text-white shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7l9 5 9-5-9-5z" />
                    <path d="M3 17l9 5 9-5" />
                    <path d="M3 12l9 5 9-5" />
                </svg>
            </div>

            <div>
                <h2 class="font-black text-xl text-slate-900">
                    WISE
                </h2>

                <p class="text-xs text-slate-500 leading-tight max-w-[12rem]">
                    Waste Identification & Sustainability
                </p>
            </div>

        </div>

    </div>

    <div class="p-4 sidebar-scroll overflow-y-auto min-h-[calc(100vh-6rem)]">

        <p class="text-xs uppercase font-bold text-slate-400 px-3 mb-3">
            Main Menu
        </p>

        <nav class="space-y-2">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9.5L12 3l9 6.5v11a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1V15H10v6.5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-11z" />
                    </svg>
                </span>
                <span class="font-semibold">Dashboard</span>
            </a>

            <a href="{{ route('scanner') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="7" width="18" height="13" rx="2" ry="2" />
                        <path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z" />
                        <path d="M12 11v6" />
                    </svg>
                </span>
                <span class="font-semibold">AI Scanner</span>
            </a>

            <a href="{{ route('scanner.history') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19h16M4 12h16M4 5h16" />
                    </svg>
                </span>
                <span class="font-semibold">History Scan</span>
            </a>

            <a href="{{ route('education') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H18" />
                        <path d="M18 6.5A2.5 2.5 0 0 0 15.5 4H6.5A2.5 2.5 0 0 0 4 6.5v11A2.5 2.5 0 0 0 6.5 20H18" />
                        <path d="M8 4v16" />
                    </svg>
                </span>
                <span class="font-semibold">Education</span>
            </a>

            <a href="{{ route('chatbot') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <span class="font-semibold">AI Chatbot</span>
            </a>

            {{-- <a href="{{ route('analytics') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19h16" />
                        <path d="M7 15v4" />
                        <path d="M12 11v8" />
                        <path d="M17 7v12" />
                    </svg>
                </span>
                <span class="font-semibold">Analytics</span>
            </a> --}}

            <a href="{{ route('profile') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600 transition-colors duration-200">

                <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </span>
                <span class="font-semibold">Profile</span>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="w-full text-left flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50 transition-colors duration-200">

                    <span class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </span>
                    <span class="font-semibold">Logout</span>
                </button>
            </form>

        </nav>

    </div>

</aside>
