@php
    $isSistemInformasi = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi';
    $background = $isSistemInformasi ? 'card-back_SI.png' : 'card-back_TI.png';
@endphp
<div
    class="relative w-full aspect-[1011/639] [container-type:inline-size] print:w-[1011px] print:h-[639px] print:aspect-auto">
    <div class="relative isolate w-[1011px] h-[639px] overflow-hidden bg-white rounded-3xl shadow-2xl
        scale-[var(--scale)] origin-top-left print:scale-100 print:shadow-none print:rounded-none"
        style="--scale: {{ $cardScale ?? 'min(1, calc(100cqw / 1011px))' }}">
        <img src="{{ asset('assets/partials/' . $background) }}" alt="" aria-hidden="true"
            class="absolute inset-0 w-full h-full object-cover">
        {{-- <div class="absolute left-[41px] top-[484px] z-10 h-[136px] w-[136px] bg-blue-950"></div>
        <img src="{{ $backQr }}" alt="QR situs resmi STMIK Adhi Guna"
            class="absolute left-[44px] top-[487px] z-20 h-[130px] w-[130px] object-contain"> --}}
    </div>
</div>
