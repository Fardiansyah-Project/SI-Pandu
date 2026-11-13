<div
    class="relative w-full aspect-[1011/639] [container-type:inline-size] print:w-[1011px] print:h-[639px] print:aspect-auto">
    <div
        class="relative w-[1011px] h-[639px] overflow-hidden bg-white rounded-3xl shadow-2xl
        scale-[var(--scale)] origin-top-left print:scale-100 print:shadow-none print:rounded-none"
        style="--scale: {{ $cardScale ?? 'min(1, calc(100cqw / 1011px))' }}">

        <!-- Latar belakang gelombang biru (kanan atas & kiri bawah) -->
        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 1011 639" preserveAspectRatio="none"
            aria-hidden="true">
            <defs>
                <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#1fa3dc" />
                    <stop offset="1" stop-color="#0a6fc2" />
                </linearGradient>
                <linearGradient id="g2" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#8ed3ee" />
                    <stop offset="1" stop-color="#c9e9f6" />
                </linearGradient>
                <linearGradient id="g3" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#9db4d6" />
                    <stop offset="1" stop-color="#b9a9c4" />
                </linearGradient>
                <linearGradient id="g4" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#2cb1e8" />
                    <stop offset="1" stop-color="#0a8fd0" />
                </linearGradient>
            </defs>
            <!-- kanan atas -->
            <path d="M590 0 H1011 V450 C990 330 900 220 800 150 C720 100 650 50 590 0 Z" fill="#cfe9f6" />
            <path d="M590 0 H680 C740 90 790 135 860 175 C760 150 650 80 590 0 Z" fill="url(#g3)" />
            <path d="M690 0 H1011 V340 C960 200 860 110 760 50 C735 35 705 15 690 0 Z" fill="url(#g2)"
                opacity=".9" />
            <path d="M760 0 H1011 V300 C960 190 880 90 800 40 C785 30 770 12 760 0 Z" fill="url(#g1)"
                opacity=".85" />
            <path d="M870 0 H1011 V170 C970 90 925 35 870 0 Z" fill="url(#g4)" />
            <path d="M890 110 C945 190 990 290 1011 400 V510 C985 380 940 260 890 110 Z" fill="#e58a8a"
                opacity=".75" />
            <path d="M1011 450 C990 540 955 600 930 639 H1011 Z" fill="#8ed3ee" opacity=".8" />
            <path d="M1011 520 C1000 570 985 610 975 639 H1011 Z" fill="#0a6fc2" opacity=".8" />
            <!-- kiri bawah -->
            <path d="M0 360 C60 440 150 530 270 639 H0 Z" fill="url(#g1)" />
            <path d="M0 440 C90 480 180 560 270 639 H130 C85 565 40 500 0 440 Z" fill="#8ed3ee"
                opacity=".8" />
            <path d="M0 520 C80 550 150 595 215 639" stroke="#e58a8a" stroke-width="3" fill="none"
                opacity=".8" />
            <path d="M0 480 C100 520 190 580 250 639" stroke="#ffffff" stroke-width="2" fill="none"
                opacity=".5" />
        </svg>

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
