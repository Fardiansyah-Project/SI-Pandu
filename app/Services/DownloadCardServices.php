<?php

namespace App\Services;

use App\Models\Ktm;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadCardServices
{
    private const CARD_WIDTH = 1011;

    private const CARD_HEIGHT = 639;

    public function downloadCard(Ktm $ktm, string $side): BinaryFileResponse
    {
        abort_unless($ktm->status === 'approved', 403, 'KTM harus disetujui sebelum diunduh.');
        abort_unless(in_array($side, ['depan', 'belakang'], true), 404);

        return $side === 'depan'
            ? $this->downloadFrontCard($ktm)
            : $this->downloadBackCard($ktm);
    }

    public function verificationQr(Ktm $ktm, int $size = 128): string
    {
        $qrCode = QrCode::create(route('ktm.verify', $ktm->token_verifikasi))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize($size)
            ->setMargin(0);

        return (new PngWriter)->write($qrCode)->getString();
    }

    private function downloadFrontCard(Ktm $ktm): BinaryFileResponse
    {
        abort_unless(extension_loaded('gd') && function_exists('imagettftext'), 503, 'Ekstensi GD dengan dukungan FreeType harus diaktifkan untuk membuat kartu.');

        $fontPath = public_path('fonts/DejaVuSans.ttf');
        $boldFontPath = public_path('fonts/DejaVuSans-Bold.ttf');
        abort_unless(is_file($fontPath) && is_file($boldFontPath), 503, 'Font kartu tidak ditemukan.');

        $topWavePath = public_path('assets/partials/top_wave.png');
        $bottomWaveFilename = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi'
            ? 'bottom_wave_SI.png'
            : 'bottom_wave_TI.png';
        $bottomWavePath = public_path('assets/partials/'.$bottomWaveFilename);
        abort_unless(is_file($topWavePath) && is_file($bottomWavePath), 503, 'Aset gelombang kartu tidak ditemukan.');

        $pngPath = tempnam(sys_get_temp_dir(), 'ktm-card-');
        if ($pngPath === false) {
            throw new RuntimeException('Tidak dapat membuat file sementara untuk PNG kartu.');
        }

        $keepPng = false;
        $card = imagecreatetruecolor(self::CARD_WIDTH, self::CARD_HEIGHT);

        try {
            if ($card === false) {
                throw new RuntimeException('Tidak dapat menyiapkan gambar kartu.');
            }

            imagealphablending($card, true);
            $white = imagecolorallocate($card, 255, 255, 255);
            imagefill($card, 0, 0, $white);

            $this->drawScaledImage($card, $topWavePath, 0, 0, self::CARD_WIDTH, (int) round(95 * self::CARD_WIDTH / 324));
            $this->drawScaledImage($card, $bottomWavePath, 0, self::CARD_HEIGHT - (int) round(142 * self::CARD_WIDTH / 324), self::CARD_WIDTH, (int) round(142 * self::CARD_WIDTH / 324));
            $this->drawContainedImage($card, public_path('assets/logo/logo.png'), 56, 40, 112, 112);
            $this->drawStudentPhoto($card, Storage::disk('public')->path($ktm->foto_path));
            $this->drawVerificationQr($card, $ktm);
            $this->drawCardText($card, $ktm, $fontPath, $boldFontPath);

            if (! imagepng($card, $pngPath)) {
                throw new RuntimeException('PNG kartu depan gagal dibuat.');
            }

            $response = response()->download($pngPath, 'KTM-'.$ktm->nim.'-depan.png', ['Content-Type' => 'image/png']);
            $response->deleteFileAfterSend(true);
            $keepPng = true;

            return $response;
        } finally {
            if ($card instanceof \GdImage) {
                imagedestroy($card);
            }

            if (! $keepPng && is_file($pngPath) && ! unlink($pngPath)) {
                throw new RuntimeException('Tidak dapat menghapus PNG kartu sementara.');
            }
        }
    }

    private function downloadBackCard(Ktm $ktm): BinaryFileResponse
    {
        $isSistemInformasi = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi';
        $template = $isSistemInformasi ? 'card-back_SI.png' : 'card-back_TI.png';
        $templatePath = public_path('assets/partials/'.$template);
        abort_unless(is_file($templatePath), 503, 'Template kartu belakang tidak ditemukan.');

        return response()->download($templatePath, 'KTM-'.$ktm->nim.'-belakang.png', ['Content-Type' => 'image/png']);
    }

    private function drawScaledImage(\GdImage $canvas, string $path, int $x, int $y, int $width, int $height): void
    {
        $source = $this->readImage($path);

        try {
            imagecopyresampled($canvas, $source, $x, $y, 0, 0, $width, $height, imagesx($source), imagesy($source));
        } finally {
            imagedestroy($source);
        }
    }

    private function drawContainedImage(\GdImage $canvas, string $path, int $x, int $y, int $width, int $height): void
    {
        if (! is_file($path)) {
            return;
        }

        $source = $this->readImage($path);

        try {
            $scale = min($width / imagesx($source), $height / imagesy($source));
            $drawWidth = (int) round(imagesx($source) * $scale);
            $drawHeight = (int) round(imagesy($source) * $scale);

            imagecopyresampled(
                $canvas,
                $source,
                $x + (int) floor(($width - $drawWidth) / 2),
                $y + (int) floor(($height - $drawHeight) / 2),
                0,
                0,
                $drawWidth,
                $drawHeight,
                imagesx($source),
                imagesy($source),
            );
        } finally {
            imagedestroy($source);
        }
    }

    private function drawStudentPhoto(\GdImage $canvas, string $path): void
    {
        $background = imagecolorallocate($canvas, 231, 225, 218);
        imagefilledrectangle($canvas, 64, 221, 265, 488, $background);

        if (! is_file($path)) {
            return;
        }

        $source = $this->readImage($path);

        try {
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $targetRatio = 202 / 268;

            if ($sourceWidth / $sourceHeight > $targetRatio) {
                $cropWidth = $sourceHeight * $targetRatio;
                $sourceX = (int) floor(($sourceWidth - $cropWidth) / 2);
                $sourceY = 0;
            } else {
                $cropHeight = $sourceWidth / $targetRatio;
                $sourceX = 0;
                $sourceY = (int) floor(($sourceHeight - $cropHeight) / 2);
                $cropWidth = $sourceWidth;
            }

            $cropHeight = $sourceHeight - 2 * $sourceY;

            imagecopyresampled($canvas, $source, 64, 221, $sourceX, $sourceY, 202, 268, (int) round($cropWidth), (int) round($cropHeight));
        } finally {
            imagedestroy($source);
        }
    }

    private function drawVerificationQr(\GdImage $canvas, Ktm $ktm): void
    {
        $qrCode = imagecreatefromstring($this->verificationQr($ktm));
        if ($qrCode === false) {
            throw new RuntimeException('QR verifikasi kartu tidak dapat dibuat.');
        }

        try {
            $x = self::CARD_WIDTH - 36 - 111;
            $y = self::CARD_HEIGHT - 24 - 111;
            $background = imagecolorallocate($canvas, 23, 37, 84);
            imagefilledrectangle($canvas, $x, $y, $x + 110, $y + 110, $background);
            imagecopyresampled($canvas, $qrCode, $x, $y, 0, 0, 111, 111, imagesx($qrCode), imagesy($qrCode));
        } finally {
            imagedestroy($qrCode);
        }
    }

    private function drawCardText(\GdImage $canvas, Ktm $ktm, string $fontPath, string $boldFontPath): void
    {
        $navy = imagecolorallocate($canvas, 15, 35, 75);
        $this->drawText($canvas, 'SEKOLAH TINGGI MANAJEMEN INFORMATIKA DAN KOMPUTER', 24, 180, 68, $navy, $fontPath);
        $this->drawText($canvas, 'STMIK ADHI GUNA', 48, 180, 126, $navy, $boldFontPath);
        $this->drawTrackedText($canvas, 'KARTU MAHASISWA', 22, 302, 263, $navy, $fontPath, 5);

        $name = mb_strtoupper(trim(preg_replace('/\s+/', ' ', (string) $ktm->nama) ?? ''));
        $nameLength = max(1, mb_strlen($name));
        $nameFontSize = (int) round(min(54, max(20, 920 / $nameLength)));

        while ($nameFontSize > 20 && $this->textWidth($name, $boldFontPath, $nameFontSize) > 640) {
            $nameFontSize--;
        }

        $this->drawText($canvas, $name, $nameFontSize, 302, 323, $navy, $boldFontPath);
        $this->drawText($canvas, (string) $ktm->nim, 50, 302, 390, $navy, $boldFontPath);
        $this->drawTrackedText($canvas, mb_strtoupper(trim($ktm->program_studi)), 23, 302, 435, $navy, $fontPath, 5);
    }

    private function drawText(\GdImage $canvas, string $text, int $size, int $x, int $baseline, int $color, string $fontPath): void
    {
        if ($text !== '') {
            imagettftext($canvas, $size, 0, $x, $baseline, $color, $fontPath, $text);
        }
    }

    private function drawTrackedText(\GdImage $canvas, string $text, int $size, int $x, int $baseline, int $color, string $fontPath, int $spacing): void
    {
        foreach (mb_str_split($text) as $character) {
            imagettftext($canvas, $size, 0, $x, $baseline, $color, $fontPath, $character);
            $bounds = imagettfbbox($size, 0, $fontPath, $character);
            $x += $bounds[2] - $bounds[0] + $spacing;
        }
    }

    private function textWidth(string $text, string $fontPath, int $size): float
    {
        $bounds = imagettfbbox($size, 0, $fontPath, $text);

        return (float) ($bounds[2] - $bounds[0]);
    }

    private function readImage(string $path): \GdImage
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Tidak dapat membaca gambar kartu: '.$path);
        }

        $image = imagecreatefromstring($contents);
        if ($image === false) {
            throw new RuntimeException('Format gambar kartu tidak didukung: '.$path);
        }

        return $image;
    }
}
