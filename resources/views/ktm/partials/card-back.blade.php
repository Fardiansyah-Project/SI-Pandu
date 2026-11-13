@php
    $isSistemInformasi = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi';
    $accentStart = $isSistemInformasi ? '#facc15' : '#dc2626';
    $accentEnd = $isSistemInformasi ? '#eab308' : '#b91c1c';
@endphp
<div
    class="relative w-full aspect-[1011/639] [container-type:inline-size] print:w-[1011px] print:h-[639px] print:aspect-auto">
    <div
        class="relative w-[1011px] h-[639px] overflow-hidden bg-white rounded-3xl shadow-2xl
        scale-[var(--scale)] origin-top-left print:scale-100 print:shadow-none print:rounded-none"
        style="--scale: {{ $cardScale ?? 'min(1, calc(100cqw / 1011px))' }}">

        <div class="">
            <img src="{{ asset('assets/partials/top_wave.png') }}" alt="" class="absolute top-0 left-0 w-full h-auto">
        </div>

        <!-- Header kampus (kiri atas) -->
        <div class="absolute top-[16px] mt-5 left-12 flex items-center gap-2.5 text-navy">
            <div class="w-[76px] h-[76px] bg-white grid place-items-center overflow-hidden">
                <img src="{{ $logoSource ?? asset('assets/logo/logo.png') }}" alt=""
                    class="w-full h-full object-contain" onerror="this.remove()">
            </div>
            <div>
                <small class="block text-[15px] font-normal tracking-wide">SEKOLAH TINGGI MANAJEMEN
                    INFORMATIKA DAN KOMPUTER</small>
                <strong class="block text-[29px] font-bold leading-tight">STMIK ADHI GUNA</strong>
            </div>
        </div>

        <!-- Foto mahasiswa -->
        <div
            class="absolute left-12 top-[164px] w-[224px] h-[258px] rounded-[22px] overflow-hidden bg-[#e7e1da]">
            <img src="{{ $photoSource ?? route('admin.ktm.photo', $ktm) }}" alt="Foto mahasiswa"
                class="w-full h-full object-cover block" onerror="this.remove()">
        </div>

        <!-- Data mahasiswa -->
        <div class="absolute left-[302px] top-[196px] text-navy">
            <div class="text-[22px] tracking-[3.5px] uppercase">Kartu Mahasiswa</div>
            <div id="nama" class="mt-3.5 max-w-[640px] text-[54px] font-bold leading-[1.15] uppercase"
                title="{{ mb_strtoupper($ktm->nama) }}">
                {{ mb_strtoupper($ktm->nama) }}
            </div>
            <div id="nim" class="mt-1.5 text-[50px] font-bold leading-tight" title="{{ $ktm->nim }}">
                {{ $ktm->nim }}
            </div>
            <div id="prodi" class="mt-3 text-[23px] tracking-[3.5px] uppercase"
                title="{{ mb_strtoupper($ktm->program_studi) }}">
                {{ mb_strtoupper($ktm->program_studi) }}
            </div>
        </div>

        <!-- QR Code (kanan bawah) -->
        <div class="absolute right-6 bottom-5 w-28 h-28 bg-white p-1">
            <img src="{{ $verificationQr }}" alt="QR verifikasi KTM {{ $ktm->nama }}"
                class="w-full h-full object-contain">
        </div>
    </div>
</div>
