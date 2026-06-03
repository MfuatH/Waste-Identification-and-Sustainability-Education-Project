@extends('layouts.app')

@section('content')

<div class="max-w-4xl">

    <h1 class="text-4xl font-black mb-8">
        Profile Settings
    </h1>

    <div class="bg-white rounded-3xl border p-8">

        <div class="flex items-center gap-6 mb-10">

            <img
                src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}"
                class="w-28 h-28 rounded-full">

            <div>

                <h2 class="text-3xl font-black">
                    {{ Auth::user()->name ?? 'User' }}
                </h2>

                <p class="text-slate-500">
                    {{ Auth::user()->email ?? 'email@example.com' }}
                </p>

            </div>

        </div>

        <form class="space-y-6">

            <div>

                <label class="font-bold">
                    Full Name
                </label>

                <input
                    type="text"
                    class="w-full border rounded-xl mt-2 p-4">

            </div>

            <div>

                <label class="font-bold">
                    Email Address
                </label>

                <input
                    type="email"
                    class="w-full border rounded-xl mt-2 p-4">

            </div>

            <div>

                <label class="font-bold">
                    New Password
                </label>

                <input
                    type="password"
                    class="w-full border rounded-xl mt-2 p-4">

            </div>

            <button
                class="bg-lime-500 text-white px-8 py-4 rounded-xl font-bold">

                Save Changes

            </button>

        </form>

    </div>

</div>

@endsection
