@extends('layouts.app')
@section('title', 'Sipandu')
@section('content')
    <section class="relative overflow-hidden bg-[#102b5c] text-white">
        <div class="absolute -right-24 -top-40 h-96 w-96 rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="mx-auto grid max-w-7xl gap-10 px-6 py-16 lg:grid-cols-[1fr_.8fr] lg:items-center lg:py-24">
            <div class="relative"><span
                    class="inline-flex items-center gap-2 rounded-full border border-blue-300/30 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-100">✦
                    LAYANAN AKADEMIK DIGITAL</span>
                <h1 class="mt-6 max-w-2xl text-4xl font-black leading-tight tracking-tight sm:text-5xl">Kartu
                    Mahasiswa,<br><span class="text-blue-300">selangkah lebih mudah.</span></h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-blue-100/80">Ajukan KTM resmi STMIK Adhi Guna secara
                    online. Lengkapi data, unggah pas foto, lalu pantau proses pengajuan Anda.</p>
                <div class="mt-9 grid max-w-lg grid-cols-3 gap-4 border-t border-white/15 pt-6">
                    <div><strong class="block text-xl">01</strong><span class="text-xs text-blue-200">Isi formulir</span>
                    </div>
                    <div><strong class="block text-xl">02</strong><span class="text-xs text-blue-200">Verifikasi
                            admin</span></div>
                    <div><strong class="block text-xl">03</strong><span class="text-xs text-blue-200">KTM siap</span></div>
                </div>
            </div>
            <div
                class="relative rounded-3xl border border-white/15 bg-white p-6 text-slate-900 shadow-2xl shadow-blue-950/30 sm:p-8">
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">FORMULIR ONLINE</p>
                        <h2 class="mt-1 text-2xl font-extrabold">Ajukan KTM</h2>
                        <p class="mt-1 text-sm text-slate-500">Isi data sesuai identitas akademik.</p>
                    </div><span class="rounded-xl bg-blue-50 p-3 text-xl">▤</span>
                </div>
                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">Periksa kembali data yang Anda masukkan.
                        {{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('ktm.store') }}" enctype="multipart/form-data" class="space-y-4">@csrf
                    <label class="block text-sm font-semibold">Nama lengkap<input name="nama" value="{{ old('nama') }}"
                            required maxlength="120" placeholder="Nama sesuai data akademik"
                            class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></label>
                    <div class="grid gap-4 sm:grid-cols-2"><label class="block text-sm font-semibold">NIM<input
                                name="nim" value="{{ old('nim') }}" required placeholder="Nomor induk mahasiswa"
                                class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></label><label
                            class="block text-sm font-semibold">Angkatan<input name="angkatan" id="angkatan" type="number"
                                min="2000" max="{{ now()->year + 1 }}" value="{{ old('angkatan') }}" required
                                placeholder="Contoh: 2023"
                                class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></label>
                    </div>
                    <label class="block text-sm font-semibold">Program studi<select name="program_studi" required
                            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 font-normal outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                            <option value="">Pilih program studi</option>
                            @foreach (['Teknik Informatika', 'Sistem Informasi'] as $prodi)
                                <option value="{{ $prodi }}" @selected(old('program_studi') === $prodi)>{{ $prodi }}
                                </option>
                            @endforeach
                        </select></label>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><label for="foto-input"
                            class="block text-sm font-semibold">Pas foto <span class="font-normal text-slate-500">(JPG/PNG,
                                maksimal 5 MB)</span></label><input id="foto-input" type="file" name="foto"
                            accept="image/jpeg,image/png" required
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-semibold file:text-blue-700">
                        <p class="mt-2 text-xs text-slate-500">Pilih foto, lalu geser gambar di area pratinjau dan atur
                            zoom. Potongan otomatis berbentuk pas foto 3:4.</p>
                        <div id="crop-tools" class="mt-4 hidden">
                            <div
                                class="mx-auto max-w-sm overflow-hidden rounded-xl border-4 border-white bg-slate-200 shadow-md">
                                <canvas id="foto-canvas" width="600" height="800"
                                    class="block aspect-[3/4] w-full touch-none cursor-grab active:cursor-grabbing"></canvas>
                            </div>
                            <div class="mx-auto mt-4 flex max-w-sm items-center gap-3"><label for="foto-zoom"
                                    class="text-xs font-semibold text-slate-600">Zoom</label><input id="foto-zoom"
                                    type="range" min="1" max="3" step="0.01" value="1"
                                    class="w-full accent-blue-700"><button type="button" id="foto-reset"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700">Atur
                                    ulang</button></div>
                            <p id="crop-status" class="mt-2 text-center text-xs font-medium text-emerald-700">Geser foto
                                untuk menentukan area potong.</p>
                        </div>
                    </div>
                    <label
                        class="flex items-start gap-3 rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-600"><input
                            type="checkbox" name="persetujuan" value="1" required
                            class="mt-0.5 rounded border-slate-300 text-blue-700 focus:ring-blue-600"><span>Saya menyetujui
                            penggunaan data dan foto untuk keperluan penerbitan KTM serta verifikasi
                            akademik.</span></label>
                    <button
                        class="w-full rounded-xl bg-[#174b9b] px-5 py-3.5 font-bold text-white shadow-lg shadow-blue-900/15 transition hover:bg-[#123b7d]">Kirim
                        Pengajuan <span aria-hidden="true">→</span></button>
                    <p class="text-center text-xs text-slate-400">Data Anda dikirim dengan aman untuk ditinjau oleh bagian
                        akademik.</p>
                </form>
            </div>
        </div>
    </section>
    <section class="mx-auto grid max-w-7xl gap-4 px-6 py-12 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="text-sm font-bold text-blue-700">01 ·
                LENGKAPI</span>
            <h3 class="mt-2 font-bold">Isi data akademik</h3>
            <p class="mt-1 text-sm text-slate-500">Pastikan nama dan NIM sesuai data kampus.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="text-sm font-bold text-blue-700">02 ·
                TUNGGU</span>
            <h3 class="mt-2 font-bold">Proses verifikasi</h3>
            <p class="mt-1 text-sm text-slate-500">Tim akademik akan memeriksa pengajuan Anda.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="text-sm font-bold text-blue-700">03 ·
                SELESAI</span>
            <h3 class="mt-2 font-bold">Kartu terverifikasi</h3>
            <p class="mt-1 text-sm text-slate-500">Simpan kode pengajuan untuk referensi.</p>
        </div>
    </section>
    <script>
        const nimInput = document.querySelector('[name="nim"]');
        nimInput.addEventListener('input', e => {
            const digits = e.target.value.replace(/\D/g, '');
            if (digits.length >= 7) {
                const year = Number(`20${digits.slice(5,7)}`);
                const input = document.getElementById('angkatan');
                if (year >= 2000 && year <= new Date().getFullYear() + 1 && !input.value) input.value = year
            }
        });
        const fotoInput = document.getElementById('foto-input'),
            cropTools = document.getElementById('crop-tools'),
            canvas = document.getElementById('foto-canvas'),
            ctx = canvas.getContext('2d'),
            zoom = document.getElementById('foto-zoom'),
            form = canvas.closest('form');
        let sourceImage = null,
            scale = 1,
            offsetX = 0,
            offsetY = 0,
            dragStart = null,
            pointerId = null;

        function drawCrop() {
            if (!sourceImage) return;
            const base = Math.max(canvas.width / sourceImage.width, canvas.height / sourceImage.height);
            const width = sourceImage.width * base * scale,
                height = sourceImage.height * base * scale;
            const minX = canvas.width - width,
                minY = canvas.height - height;
            offsetX = Math.min(0, Math.max(minX, offsetX));
            offsetY = Math.min(0, Math.max(minY, offsetY));
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(sourceImage, offsetX, offsetY, width, height)
        }
        fotoInput.addEventListener('change', () => {
            const file = fotoInput.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                fotoInput.setCustomValidity('Pilih berkas gambar JPG atau PNG.');
                fotoInput.reportValidity();
                return
            }
            fotoInput.setCustomValidity('');
            const url = URL.createObjectURL(file);
            const image = new Image();
            image.onload = () => {
                if (sourceImage?.src?.startsWith('blob:')) URL.revokeObjectURL(sourceImage.src);
                sourceImage = image;
                scale = 1;
                offsetX = (canvas.width - image.width * Math.max(canvas.width / image.width, canvas.height /
                    image.height)) / 2;
                offsetY = (canvas.height - image.height * Math.max(canvas.width / image.width, canvas.height /
                    image.height)) / 2;
                zoom.value = '1';
                cropTools.classList.remove('hidden');
                drawCrop();
            };
            image.onerror = () => {
                fotoInput.setCustomValidity('Foto tidak dapat dibaca. Silakan pilih file JPG atau PNG lain.');
                fotoInput.reportValidity();
                URL.revokeObjectURL(url)
            };
            image.src = url;
        });
        zoom.addEventListener('input', () => {
            if (!sourceImage) return;
            const old = scale;
            scale = Number(zoom.value);
            const factor = scale / old;
            offsetX = canvas.width / 2 - (canvas.width / 2 - offsetX) * factor;
            offsetY = canvas.height / 2 - (canvas.height / 2 - offsetY) * factor;
            drawCrop()
        });
        canvas.addEventListener('pointerdown', e => {
            if (!sourceImage) return;
            dragStart = {
                x: e.clientX,
                y: e.clientY,
                offsetX,
                offsetY
            };
            pointerId = e.pointerId;
            canvas.setPointerCapture(pointerId)
        });
        canvas.addEventListener('pointermove', e => {
            if (!dragStart || e.pointerId !== pointerId) return;
            const rect = canvas.getBoundingClientRect();
            const factor = canvas.width / rect.width;
            offsetX = dragStart.offsetX + (e.clientX - dragStart.x) * factor;
            offsetY = dragStart.offsetY + (e.clientY - dragStart.y) * factor;
            drawCrop()
        });

        function stopDrag() {
            dragStart = null;
            pointerId = null
        }
        canvas.addEventListener('pointerup', stopDrag);
        canvas.addEventListener('pointercancel', stopDrag);
        document.getElementById('foto-reset').addEventListener('click', () => {
            if (!sourceImage) return;
            scale = 1;
            zoom.value = '1';
            const base = Math.max(canvas.width / sourceImage.width, canvas.height / sourceImage.height);
            offsetX = (canvas.width - sourceImage.width * base) / 2;
            offsetY = (canvas.height - sourceImage.height * base) / 2;
            drawCrop()
        });
        form.addEventListener('submit', event => {
            if (!sourceImage) {
                event.preventDefault();
                fotoInput.setCustomValidity('Pilih foto dan tunggu pratinjau crop selesai dimuat.');
                fotoInput.reportValidity();
                return
            }
            event.preventDefault();
            canvas.toBlob(blob => {
                if (!blob) {
                    fotoInput.setCustomValidity('Gagal memproses hasil crop. Silakan pilih foto kembali.');
                    fotoInput.reportValidity();
                    return
                }
                const cropped = new File([blob], 'foto-ktm.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });
                const transfer = new DataTransfer();
                transfer.items.add(cropped);
                fotoInput.files = transfer.files;
                HTMLFormElement.prototype.submit.call(form)
            }, 'image/jpeg', 0.92)
        });
    </script>
@endsection
