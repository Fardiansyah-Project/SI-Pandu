<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#102b5c">
    <title>@yield('title', 'KTM Generator') — STMIK Adhi Guna</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body {
                font-family: Inter, ui-sans-serif, system-ui, sans-serif
            }

            .bg-slate-50 {
                background: #f8fafc
            }

            .text-slate-900 {
                color: #0f172a
            }
        </style>
    @endif
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-white/10 bg-[#102b5c] text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="{{ route('ktm.create') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/logo/logo.png') }}"
                    class="grid h-11 w-11 place-items-center rounded-full bg-white" />
                <div class="gap-1">
                    <div class="text-sm font-extrabold tracking-wide">
                        SIPANDU
                    </div>
                    <div class="text-xs text-blue-200">
                        Sistem Informasi Layanan Terpadu
                    </div>
                </div>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('admin.ktm.index') }}"
                        class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button
                            class="rounded-lg px-3 py-2 text-sm font-semibold text-blue-100 hover:bg-white/10">Keluar</button>
                    </form>
                    {{-- <a href="{{ route('login') }}" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10">Login admin</a> --}}
                @endauth
            </div>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer class="mt-20 border-t border-slate-200 bg-white">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-7 text-sm text-slate-500 sm:flex-row sm:justify-between">
            <span>© {{ now()->year }} STMIK Adhi Guna · Sistem pengajuan KTM</span><span>Data Anda hanya digunakan
                untuk penerbitan kartu mahasiswa.</span>
        </div>
    </footer>
</body>

</html>
