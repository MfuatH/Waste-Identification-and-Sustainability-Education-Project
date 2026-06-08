<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>WISE Login</title>

<script src="https://cdn.tailwindcss.com"></script>

<script defer
src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
</script>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>

body{
    font-family:'Nunito',sans-serif;
}

</style>

</head>
<body>

<div
x-data="{
register:false,
forgot:false
}"
class="min-h-screen bg-gradient-to-br from-lime-50 via-white to-green-100">

<div class="max-w-7xl mx-auto px-6 min-h-screen">

<div class="grid lg:grid-cols-2 min-h-screen">

<!-- LEFT SIDE -->

<div class="hidden lg:flex flex-col justify-center">

<div>

<div class="text-8xl mb-4">
♻️
</div>

<h1 class="text-6xl font-black text-lime-600">
WISE
</h1>

<h2 class="text-4xl font-black mt-8 text-slate-800">
Waste Identification and Sustainability Education
</h2>

<p class="mt-6 text-xl text-slate-500 max-w-xl leading-relaxed">
Upload waste images, classify waste automatically using AI,
learn recycling methods and contribute to a cleaner environment.
</p>

<div class="mt-10 grid grid-cols-2 gap-4 max-w-lg">

<div class="bg-white p-5 rounded-3xl shadow">
<h3 class="text-3xl">📸</h3>
<p class="font-bold mt-3">AI Scanner</p>
</div>

<div class="bg-white p-5 rounded-3xl shadow">
<h3 class="text-3xl">🤖</h3>
<p class="font-bold mt-3">AI Chatbot</p>
</div>

<div class="bg-white p-5 rounded-3xl shadow">
<h3 class="text-3xl">📚</h3>
<p class="font-bold mt-3">Education</p>
</div>

<div class="bg-white p-5 rounded-3xl shadow">
<h3 class="text-3xl">📊</h3>
<p class="font-bold mt-3">Analytics</p>
</div>

</div>

</div>

</div>

<!-- LOGIN CARD -->

<div class="flex items-center justify-center">

<div class="bg-white shadow-2xl rounded-[40px] p-10 w-full max-w-md animate-card-enter">

<h2 class="text-4xl font-black text-center">
Welcome Back
</h2>

<p class="text-center text-slate-500 mt-3">
Sign in to continue
</p>

@if ($errors->any())

<div class="mt-5 bg-red-100 border border-red-300 text-red-700 p-4 rounded-xl">

{{ $errors->first() }}

</div>

@endif

<form
method="POST"
action="{{ route('login.post') }}"
class="space-y-5 mt-8">

@csrf

<input
type="email"
name="email"
value="{{ old('email') }}"
placeholder="Email Address"
required
class="w-full border rounded-2xl p-4">

<input
type="password"
name="password"
placeholder="Password"
required
class="w-full border rounded-2xl p-4">

<div class="flex justify-between text-sm">

<label class="flex items-center gap-2">

<input
type="checkbox"
name="remember">

Remember Me

</label>

<button
type="button"
@click="forgot=true"
class="text-lime-600 font-bold">

Forgot Password?

</button>

</div>

<button
type="submit"
class="w-full bg-lime-500 hover:bg-lime-600 transition text-white py-4 rounded-2xl font-bold">

Login

</button>

</form>

<div class="mt-8 text-center">

<p class="text-slate-500">

Don't have an account?

<button
@click="register=true"
class="text-lime-600 font-bold">

Create Account

</button>

</p>

</div>

</div>

</div>

</div>

</div>

<!-- REGISTER MODAL -->

<div
x-show="register"
x-transition
class="fixed inset-0 bg-black/60 flex items-center justify-center p-6 z-50">

<div
@click.away="register=false"
class="bg-white rounded-[32px] p-8 w-full max-w-lg">

<h3 class="text-3xl font-black mb-6">
Create Account
</h3>

<form
method="POST"
action="{{ route('register') }}"
class="space-y-4">

@csrf

<input
type="text"
name="name"
placeholder="Full Name"
required
class="w-full border rounded-xl p-4">

<input
type="email"
name="email"
placeholder="Email Address"
required
class="w-full border rounded-xl p-4">

<input
type="password"
name="password"
placeholder="Password"
required
class="w-full border rounded-xl p-4">

<input
type="password"
name="password_confirmation"
placeholder="Confirm Password"
required
class="w-full border rounded-xl p-4">

<button
type="submit"
class="w-full bg-lime-500 text-white py-4 rounded-xl font-bold">

Register

</button>

</form>

<button
@click="register=false"
class="w-full mt-4 text-slate-500">

Close

</button>

</div>

</div>

<!-- FORGOT PASSWORD -->

<div
x-show="forgot"
x-transition
class="fixed inset-0 bg-black/60 flex items-center justify-center p-6 z-50">

<div
@click.away="forgot=false"
class="bg-white rounded-[32px] p-8 w-full max-w-md">

<h3 class="text-3xl font-black mb-6">
Reset Password
</h3>

<form>

<input
type="email"
placeholder="Email Address"
class="w-full border rounded-xl p-4">

<button
type="button"
class="w-full mt-4 bg-lime-500 text-white py-4 rounded-xl font-bold">

Send Reset Link

</button>

</form>

<button
@click="forgot=false"
class="w-full mt-4 text-slate-500">

Close

</button>

</div>

</div>

</div>

</body>
</html>