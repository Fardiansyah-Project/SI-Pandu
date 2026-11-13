@php
    $isSistemInformasi = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi';
    // $accentStart = $isSistemInformasi ? 'bottom_wave_SI' : 'bottom_wave_TI';
    // $accentEnd = $isSistemInformasi ? '#eab308' : '#b91c1c';
@endphp
<div
    class="relative w-full aspect-[1011/639] [container-type:inline-size] print:w-[1011px] print:h-[639px] print:aspect-auto">
    <div
        class="relative isolate w-[1011px] h-[639px] overflow-hidden bg-white rounded-3xl shadow-2xl
        scale-[var(--scale)] origin-top-left print:scale-100 print:shadow-none print:rounded-none"
        style="--scale: {{ $cardScale ?? 'min(1, calc(100cqw / 1011px))' }}">

        <!-- Latar belakang gelombang biru atas) -->
        <div class="">
            <img src="{{ asset('assets/partials/top_wave.png') }}" alt="" class="absolute top-0 left-0 w-full h-auto">
        </div>

        <!-- Header kampus (kiri atas) -->
        <div class="absolute top-[40px] left-14 flex items-center gap-3 text-navy">
            <div class="w-[112px] h-[112px] grid place-items-center overflow-hidden">
                <img src="{{ $logoSource ?? asset('assets/logo/logo.png') }}" alt=""
                    class="w-full h-full object-contain" style="filter: drop-shadow(0 0 3px #fff)"
                    onerror="this.remove()">
            </div>
            <div>
                <small class="block text-[24px] font-normal tracking-wide">SEKOLAH TINGGI MANAJEMEN
                    INFORMATIKA DAN KOMPUTER</small>
                <strong class="block text-[48px] font-bold leading-none">STMIK ADHI GUNA</strong>
            </div>
        </div>

        <!-- Foto mahasiswa -->
        <div
            class="absolute left-16 top-[221px] w-[202px] h-[268px] rounded-[18px] overflow-hidden bg-[#e7e1da]">
            <img src="{{ $photoSource ?? route('admin.ktm.photo', $ktm) }}" alt="Foto mahasiswa"
                class="w-full h-full object-cover block" onerror="this.remove()">
        </div>

        <!-- Data mahasiswa -->
        <div class="absolute left-[302px] top-[238px] text-navy">
            <div class="text-[22px] tracking-[5px] uppercase">Kartu Mahasiswa</div>
            <div id="nama" class="mt-3.5 max-w-[640px] text-[54px] font-bold leading-[1.15] uppercase"
                title="{{ mb_strtoupper($ktm->nama) }}">
                {{ mb_strtoupper($ktm->nama) }}
            </div>
            <div id="nim" class="mt-1.5 text-[50px] font-bold leading-tight" title="{{ $ktm->nim }}">
                {{ $ktm->nim }}
            </div>
            <div id="prodi" class="mt-3 text-[23px] tracking-[5px] uppercase"
                title="{{ mb_strtoupper($ktm->program_studi) }}">
                {{ mb_strtoupper($ktm->program_studi) }}
            </div>
        </div>

        <!-- QR Code (kanan bawah) -->
        <div class="absolute right-9 bottom-6 w-[110px] h-[110px] bg-white">
            <img src="{{ $verificationQr }}" alt="QR verifikasi KTM {{ $ktm->nama }}"
                class="w-full h-full object-contain">
        </div>

        {{-- Bottom Wave --}}
        <img src="{{ asset('assets/partials/' . ($isSistemInformasi ? 'bottom_wave_SI.png' : 'bottom_wave_TI.png')) }}"
            alt="" aria-hidden="true" class="absolute bottom-0 left-0 -z-10 w-full h-auto pointer-events-none">
    </div>
</div>
