@extends('layouts.app')
@section('title', 'Login Admin')
@section('content')
<section class="mx-auto max-w-md px-6 py-16"><div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm"><p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">AKSES TERBATAS</p><h1 class="mt-2 text-3xl font-black">Login admin</h1><p class="mt-2 text-sm text-slate-500">Masuk untuk mengelola pengajuan KTM.</p>
@if($errors->any())<div class="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">@csrf<label class="block text-sm font-semibold">Email<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></label><label class="block text-sm font-semibold">Kata sandi<input type="password" name="password" required autocomplete="current-password" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></label><label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded border-slate-300"> Ingat saya</label><button class="w-full rounded-xl bg-[#174b9b] px-5 py-3 font-bold text-white hover:bg-[#123b7d]">Masuk ke dashboard</button></form>
</div></section>
@endsection
