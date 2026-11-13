@extends('layouts.app')
@section('title', 'Pratinjau KTM')
@section('content')
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">KARTU TERVERIFIKASI</p>
                <h1 class="mt-1 text-3xl font-black">Pratinjau kartu KTM</h1>
                <p class="mt-2 text-slate-500">Unduh bagian depan dan belakang dalam format PNG.</p>
            </div><a href="{{ route('admin.ktm.index') }}"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold">← Kembali ke dashboard</a>
        </div>
        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold">Sisi depan</h2><a href="{{ route('admin.ktm.download', [$ktm, 'depan']) }}"
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Unduh PNG</a>
                </div>
                @include('ktm.partials.card-front')
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold">Sisi belakang</h2><a
                        href="{{ route('admin.ktm.download', [$ktm, 'belakang']) }}"
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Unduh PNG</a>
                </div>
                <div class="overflow-hidden rounded-2xl bg-[#e7f1ff] shadow-lg">
                    <div class="border-b-4 border-red-600 bg-[#102b5c] p-5 text-white">
                        <p class="text-2xl font-black">INFORMASI KARTU</p>
                    </div>
                    <div class="grid min-h-64 gap-4 p-6">
                        <div>
                            <p class="font-black tracking-widest text-blue-900">PERHATIAN</p>
                            <ol class="mt-3 list-inside list-decimal space-y-2 text-sm">
                                <li>Kartu ini adalah tanda pengenal resmi mahasiswa.</li>
                                <li>Kartu wajib dibawa selama berada di lingkungan kampus.</li>
                                <li>Jika kartu hilang, segera laporkan ke bagian akademik.</li>
                            </ol>
                        </div>
                        <div class="mt-2 flex items-end justify-between">
                            <div class="max-w-[60%] break-all text-xs text-blue-800">Verifikasi:
                                {{ route('ktm.verify', $ktm->token_verifikasi) }}</div>
                            <div class="text-right text-xs">
                                <p>KETUA</p>
                                <p class="mt-5 font-bold">Hj. Mus Aidah, S.Pd., MM.</p>
                                <p>NIDN 0918127802</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
    {{-- <section class="mx-auto max-w-6xl px-6 py-10"><div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">KARTU TERVERIFIKASI</p><h1 class="mt-1 text-3xl font-black">Pratinjau kartu KTM</h1><p class="mt-2 text-slate-500">Unduh bagian depan dan belakang dalam format PNG.</p></div><a href="{{ route('admin.ktm.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold">← Kembali ke dashboard</a></div>
<div class="mt-8 grid gap-8 lg:grid-cols-2">
<article class="rounded-2xl border border-slate-200 bg-white p-5"><div class="mb-4 flex items-center justify-between"><h2 class="font-bold">Sisi depan</h2><a href="{{ route('admin.ktm.download', [$ktm, 'depan']) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Unduh PNG</a></div>
<div class="ktm-front-design relative isolate overflow-hidden rounded-xl bg-white shadow-lg">
    <style>
        .ktm-front-design{aspect-ratio:1011/638;container-type:inline-size;color:#123d7a}
        .ktm-front-campus{position:absolute;top:3%;right:10.5%;text-align:right;font-size:1.42cqw;line-height:1.15}
        .ktm-front-campus strong{display:block;font-size:2.9cqw;line-height:1.1}
        .ktm-front-seal{position:absolute;top:2%;right:1.8%;width:7.7%;height:auto}
        .ktm-front-photo{position:absolute;left:4%;top:25.7%;width:22.2%;height:40.4%;border-radius:8%;object-fit:cover}
        .ktm-front-label{position:absolute;left:29.8%;top:30.5%;font-size:2.2cqw;letter-spacing:.18em;line-height:1.2}
        .ktm-front-name{position:absolute;left:29.8%;top:37.5%;width:67%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-size:clamp(16px,min(5.15cqw,{{ number_format(109 / max(1, mb_strlen($ktm->nama)), 2, '.', '') }}cqw),52px);font-weight:800;line-height:1.08}
        .ktm-front-nim{position:absolute;left:29.8%;top:49.1%;width:67%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-size:clamp(16px,min(5.05cqw,{{ number_format(109 / max(1, mb_strlen($ktm->nim)), 2, '.', '') }}cqw),51px);font-weight:800;line-height:1.08}
        .ktm-front-program{position:absolute;left:29.8%;top:59.8%;width:67%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-size:clamp(11px,min(2.5cqw,{{ number_format(110 / max(1, mb_strlen($ktm->program_studi)), 2, '.', '') }}cqw),26px);letter-spacing:.12em;line-height:1.2}
        .ktm-front-qr{position:absolute;left:1.8%;bottom:2.2%;width:11%;background:#fff;padding:.55%}
    </style>
    <svg class="absolute inset-0 -z-10 h-full w-full" viewBox="0 0 1011 638" preserveAspectRatio="none" aria-hidden="true">
        <path fill="#2eaecf" d="M0 0h318C265 72 197 130 124 157L0 294z"/>
        <path fill="#68c6e2" d="M0 0h245l-78 78-97 87-70 8z"/>
        <path fill="#bee4ef" d="M0 0h218l-90 132L0 174z"/>
        <path fill="#aeb8c5" d="M186 0h320L355 93l-128 71H119z"/>
        <path fill="#68c6e2" d="M0 484l83 154H0z"/>
        <path fill="#2eaecf" d="M0 540l53 98H0z"/>
        <path fill="#bee4ef" d="M50 638h89L0 385z"/>
        <path fill="#174b9b" d="M70 638h101L0 453z"/>
        <path fill="#cd313d" d="M76 638h18L0 350z"/>
        <path fill="#bee4ef" d="M780 638l231-242v242z"/>
        <path fill="#2eaecf" d="M842 638l169-164v164z"/>
        <path fill="#cd313d" d="M760 638l251-102v32z"/>
        <path fill="#174b9b" d="M902 638l109-26v26z"/>
    </svg>
    <div class="ktm-front-campus">SEKOLAH TINGGI MANAJEMEN INFORMATIKA DAN KOMPUTER<strong>STMIK ADHI GUNA</strong></div>
    <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="ktm-front-seal">
    <img src="{{ route('admin.ktm.photo', $ktm) }}" alt="Foto {{ $ktm->nama }}" class="ktm-front-photo">
    <p class="ktm-front-label">KARTU MAHASISWA</p>
    <p class="ktm-front-name" title="{{ mb_strtoupper($ktm->nama) }}">{{ mb_strtoupper($ktm->nama) }}</p>
    <p class="ktm-front-nim" title="{{ $ktm->nim }}">{{ $ktm->nim }}</p>
    <p class="ktm-front-program" title="{{ mb_strtoupper($ktm->program_studi) }}">{{ mb_strtoupper($ktm->program_studi) }}</p>
    <img src="{{ $verificationQr }}" alt="QR verifikasi KTM {{ $ktm->nama }}" class="ktm-front-qr">
</div></article>
<article class="rounded-2xl border border-slate-200 bg-white p-5"><div class="mb-4 flex items-center justify-between"><h2 class="font-bold">Sisi belakang</h2><a href="{{ route('admin.ktm.download', [$ktm, 'belakang']) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Unduh PNG</a></div><div class="overflow-hidden rounded-2xl bg-[#e7f1ff] shadow-lg"><div class="border-b-4 border-red-600 bg-[#102b5c] p-5 text-white"><p class="text-2xl font-black">INFORMASI KARTU</p></div><div class="grid min-h-64 gap-4 p-6"><div><p class="font-black tracking-widest text-blue-900">PERHATIAN</p><ol class="mt-3 list-inside list-decimal space-y-2 text-sm"><li>Kartu ini adalah tanda pengenal resmi mahasiswa.</li><li>Kartu wajib dibawa selama berada di lingkungan kampus.</li><li>Jika kartu hilang, segera laporkan ke bagian akademik.</li></ol></div><div class="mt-2 flex items-end justify-between"><div class="max-w-[60%] break-all text-xs text-blue-800">Verifikasi: {{ route('ktm.verify', $ktm->token_verifikasi) }}</div><div class="text-right text-xs"><p>KETUA</p><p class="mt-5 font-bold">Hj. Mus Aidah, S.Pd., MM.</p><p>NIDN 0918127802</p></div></div></div></div></article>
</div></section> --}}

@endsection
