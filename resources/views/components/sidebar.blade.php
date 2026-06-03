<aside class="w-72 bg-white border-r min-h-screen sticky top-0">

    <div class="h-20 border-b flex items-center px-6">

        <div class="flex items-center gap-3">

            <div class="w-12 h-12 rounded-xl bg-lime-500 flex items-center justify-center text-white font-black text-xl">
                ♻
            </div>

            <div>
                <h2 class="font-black text-xl text-lime-600">
                    SmartWaste
                </h2>

                <p class="text-xs text-slate-500">
                    AI Classification
                </p>
            </div>

        </div>

    </div>

    <div class="p-4 sidebar-scroll overflow-y-auto">

        <p class="text-xs uppercase font-bold text-slate-400 px-3 mb-3">
            Main Menu
        </p>

        <nav class="space-y-2">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600">

                🏠 Dashboard
            </a>

            <a href="{{ route('scanner') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600">

                📸 AI Scanner
            </a>

            <a href="{{ route('education') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600">

                📚 Education
            </a>

            <a href="{{ route('chatbot') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600">

                🤖 AI Chatbot
            </a>

            <a href="{{ route('analytics') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600">

                📊 Analytics
            </a>

            <a href="{{ route('profile') }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-lime-50 hover:text-lime-600">

                👤 Profile
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    class="w-full text-left flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50">

                    🚪 Logout
                </button>
            </form>

        </nav>

    </div>

</aside>