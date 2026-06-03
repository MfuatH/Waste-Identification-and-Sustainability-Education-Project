<header class="bg-white border-b sticky top-0 z-40">

    <div class="h-20 px-6 flex items-center justify-between">

        <div>
            <h1 class="font-black text-2xl text-slate-800">
                Smart Waste AI
            </h1>

            <p class="text-sm text-slate-500">
                Environmental Intelligence Platform
            </p>
        </div>

        <div class="flex items-center gap-4">

            <button class="relative">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6 text-slate-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                </svg>

                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
            </button>

            <div class="flex items-center gap-3">

                <img
                    src="https://ui-avatars.com/api/?name=User"
                    class="w-11 h-11 rounded-full">

                <div>
                    <p class="font-bold text-slate-800">
                        {{ Auth::user()->name ?? 'Guest' }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Member
                    </p>
                </div>

            </div>

        </div>

    </div>

</header>