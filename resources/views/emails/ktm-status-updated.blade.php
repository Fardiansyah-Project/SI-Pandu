<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengajuan KTM</title>
</head>

<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f1f5f9;padding:40px 16px">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06)">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#174b9b;padding:32px 32px 24px;text-align:center">
                            <h1 style="margin:0;font-size:20px;font-weight:800;color:#ffffff;letter-spacing:.5px">
                                SIPEKA — KTM Digital
                            </h1>
                            <p style="margin:6px 0 0;font-size:13px;color:#bfdbfe">
                                Pembaruan Status Pengajuan
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px">
                            <p style="margin:0;font-size:15px;color:#334155;line-height:1.6">
                                Halo, <strong>{{ $ktm->nama }}</strong>!
                            </p>

                            <p style="margin:16px 0 0;font-size:15px;color:#334155;line-height:1.6">
                                Pengajuan KTM Anda dengan kode
                                <strong style="color:#174b9b">{{ $ktm->kode_pengajuan }}</strong>
                                telah diperbarui.
                            </p>

                            {{-- Status badge --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0">
                                <tr>
                                    <td
                                        style="padding:10px 20px;border-radius:10px;font-size:14px;font-weight:700;
                                        @if ($ktm->status === 'approved') background-color:#ecfdf5;color:#047857
                                        @elseif($ktm->status === 'rejected')
                                            background-color:#fef2f2;color:#b91c1c
                                        @else
                                            background-color:#fffbeb;color:#92400e @endif
                                    ">
                                        Status:
                                        {{ match ($ktm->status) {
                                            'approved' => '✅ Disetujui',
                                            'rejected' => '❌ Ditolak',
                                            default => '⏳ Menunggu Verifikasi',
                                        } }}
                                    </td>
                                </tr>
                            </table>

                            @if ($ktm->status === 'approved')
                                <p style="margin:0 0 16px;font-size:14px;color:#334155;line-height:1.6">
                                    Selamat! Pengajuan KTM Anda telah <strong>disetujui</strong>. Kartu mahasiswa
                                    digital
                                    Anda sudah siap dan dapat diverifikasi menggunakan kode pengajuan atau link
                                    verifikasi.
                                </p>
                            @elseif($ktm->status === 'rejected')
                                <p style="margin:0 0 8px;font-size:14px;color:#334155;line-height:1.6">
                                    Mohon maaf, pengajuan KTM Anda <strong>ditolak</strong>.
                                </p>
                                @if ($ktm->catatan)
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                        style="margin:0 0 16px">
                                        <tr>
                                            <td
                                                style="padding:12px 16px;background-color:#fef2f2;border-left:4px solid #ef4444;border-radius:0 8px 8px 0;font-size:13px;color:#991b1b;line-height:1.5">
                                                <strong>Catatan:</strong> {{ $ktm->catatan }}
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                                <p style="margin:0 0 16px;font-size:14px;color:#334155;line-height:1.6">
                                    Silakan periksa dan ajukan kembali pengajuan Anda jika diperlukan.
                                </p>
                            @endif

                            {{-- Info table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin:24px 0">
                                <tr style="background-color:#f8fafc">
                                    <td
                                        style="padding:10px 16px;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;width:140px">
                                        Kode Pengajuan</td>
                                    <td
                                        style="padding:10px 16px;font-size:13px;font-weight:700;color:#174b9b;border-bottom:1px solid #e2e8f0">
                                        {{ $ktm->kode_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 16px;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0">
                                        NIM</td>
                                    <td
                                        style="padding:10px 16px;font-size:13px;color:#334155;border-bottom:1px solid #e2e8f0">
                                        {{ $ktm->nim }}</td>
                                </tr>
                                <tr style="background-color:#f8fafc">
                                    <td
                                        style="padding:10px 16px;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0">
                                        Program Studi</td>
                                    <td
                                        style="padding:10px 16px;font-size:13px;color:#334155;border-bottom:1px solid #e2e8f0">
                                        {{ $ktm->program_studi }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;font-size:13px;color:#64748b">Angkatan</td>
                                    <td style="padding:10px 16px;font-size:13px;color:#334155">{{ $ktm->angkatan }}
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:8px 0 0">
                                <tr>
                                    <td style="border-radius:10px;background-color:#174b9b">
                                        <a href="{{ url('/verifikasi/' . $ktm->token_verifikasi) }}"
                                            style="display:inline-block;padding:12px 28px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none">
                                            Lihat Status Pengajuan →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="padding:20px 32px;background-color:#f8fafc;border-top:1px solid #e2e8f0;text-align:center">
                            <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.5">
                                Email ini dikirim otomatis oleh Sistem Informasi Pengajuan Kartu Mahasiswa (SIPEKA).<br>
                                Jika Anda tidak merasa mengajukan KTM, abaikan email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
