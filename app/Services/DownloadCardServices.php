<?php

namespace App\Services;

use App\Models\Ktm;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class DownloadCardServices
{
    public function downloadCard(Ktm $ktm, string $side): StreamedResponse
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

    private function downloadFrontCard(Ktm $ktm): StreamedResponse
    {
        $temporaryDirectory = $this->createTemporaryDirectory();
        $keepTemporaryDirectory = false;

        try {
            $browserProfile = $temporaryDirectory.DIRECTORY_SEPARATOR.'profile';
            $htmlPath = $temporaryDirectory.DIRECTORY_SEPARATOR.'card.html';
            $pngPath = $temporaryDirectory.DIRECTORY_SEPARATOR.'card.png';

            if (! mkdir($browserProfile, 0700)) {
                throw new RuntimeException('Tidak dapat membuat direktori sementara untuk Chromium.');
            }

            $logoPath = public_path('assets/logo/logo.png');
            $photoPath = Storage::disk('public')->path($ktm->foto_path);
            $topWavePath = public_path('assets/partials/top_wave.png');
            $bottomWaveFilename = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi'
                ? 'bottom_wave_SI.png'
                : 'bottom_wave_TI.png';
            $bottomWavePath = public_path('assets/partials/'.$bottomWaveFilename);
            abort_unless(is_file($topWavePath) && is_file($bottomWavePath), 503, 'Aset gelombang kartu tidak ditemukan.');

            $view = view('ktm.download-card-front', [
                'ktm' => $ktm,
                'verificationQr' => 'data:image/png;base64,'.base64_encode($this->verificationQr($ktm)),
                'logoSource' => $this->imageDataUri($logoPath, 'image/png'),
                'photoSource' => $this->imageDataUri($photoPath, 'image/png'),
                'topWaveSource' => $this->imageDataUri($topWavePath, 'image/png'),
                'bottomWaveSource' => $this->imageDataUri($bottomWavePath, 'image/png'),
                'cardStyles' => $this->cardStyles(),
            ])->render();

            if (file_put_contents($htmlPath, $view) === false) {
                throw new RuntimeException('Tidak dapat menyiapkan tampilan kartu untuk diunduh.');
            }

            $process = new Process([
                $this->browserExecutable(),
                '--headless',
                '--disable-gpu',
                '--disable-crash-reporter',
                '--disable-breakpad',
                '--hide-scrollbars',
                '--no-first-run',
                '--no-default-browser-check',
                '--allow-file-access-from-files',
                '--run-all-compositor-stages-before-draw',
                '--virtual-time-budget=3000',
                '--force-device-scale-factor=1',
                '--disable-dev-shm-usage',
                '--user-data-dir='.$browserProfile,
                '--window-size=1011,639',
                '--screenshot='.$pngPath,
                $this->fileUrl($htmlPath),
            ], timeout: 45);

            try {
                $process->mustRun();
            } catch (ProcessTimedOutException $exception) {
                report($exception);
                abort(503, 'Proses rendering kartu depan melewati batas waktu.');
            } catch (ProcessFailedException $exception) {
                report($exception);
                abort(503, 'Gagal merender kartu. Pastikan Chromium atau Chrome dapat dijalankan.');
            }

            abort_unless(is_file($pngPath) && filesize($pngPath) > 0, 503, 'PNG kartu depan gagal dibuat.');

            unlink($htmlPath);
            $keepTemporaryDirectory = true;

            return response()->streamDownload(function () use ($pngPath, $temporaryDirectory): void {
                try {
                    if (readfile($pngPath) === false) {
                        throw new RuntimeException('Tidak dapat membaca PNG kartu yang telah dibuat.');
                    }
                } finally {
                    $this->removeTemporaryDirectory($temporaryDirectory);
                }
            }, 'KTM-'.$ktm->nim.'-depan.png', ['Content-Type' => 'image/png']);
        } finally {
            if (! $keepTemporaryDirectory) {
                $this->removeTemporaryDirectory($temporaryDirectory);
            }
        }
    }

    private function downloadBackCard(Ktm $ktm): StreamedResponse
    {
        $isSistemInformasi = mb_strtolower(trim($ktm->program_studi)) === 'sistem informasi';
        $template = $isSistemInformasi ? 'card-back_SI.png' : 'card-back_TI.png';
        $templatePath = public_path('assets/partials/'.$template);
        abort_unless(is_file($templatePath), 503, 'Template kartu belakang tidak ditemukan.');

        return response()->streamDownload(function () use ($templatePath): void {
            if (readfile($templatePath) === false) {
                throw new RuntimeException('Tidak dapat membaca template kartu belakang.');
            }
        }, 'KTM-'.$ktm->nim.'-belakang.png', ['Content-Type' => 'image/png']);
    }

    private function cardStyles(): string
    {
        $manifestPath = public_path('build/manifest.json');
        abort_unless(is_file($manifestPath), 503, 'Aset frontend belum dibangun.');

        $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        $stylePaths = [
            public_path('build/'.($manifest['resources/css/app.css']['file'] ?? '')),
        ];
        $fontManifestPath = public_path('build/fonts-manifest.json');

        if (is_file($fontManifestPath)) {
            $fontManifest = json_decode((string) file_get_contents($fontManifestPath), true, flags: JSON_THROW_ON_ERROR);
            if (isset($fontManifest['style']['file'])) {
                $stylePaths[] = public_path('build/'.$fontManifest['style']['file']);
            }
        }

        $styles = [];
        foreach ($stylePaths as $stylePath) {
            abort_unless(is_file($stylePath), 503, 'Aset CSS kartu tidak ditemukan.');
            $style = file_get_contents($stylePath);
            if ($style === false) {
                throw new RuntimeException('Tidak dapat membaca aset CSS kartu.');
            }

            $styles[] = $this->inlineStyleAssets($style);
        }

        return implode("\n", $styles);
    }

    private function inlineStyleAssets(string $styles): string
    {
        $result = preg_replace_callback(
            '~url\("(/build/assets/[^"]+)"\)|url\((/build/assets/[^)]+)\)~',
            function (array $matches): string {
                $assetUrl = $matches[1] !== '' ? $matches[1] : $matches[2];
                $assetPath = public_path(ltrim($assetUrl, '/'));
                abort_unless(is_file($assetPath), 503, 'Font atau aset kartu tidak ditemukan.');

                $contents = file_get_contents($assetPath);
                if ($contents === false) {
                    throw new RuntimeException('Tidak dapat membaca aset font kartu.');
                }

                $mimeType = match (strtolower(pathinfo($assetPath, PATHINFO_EXTENSION))) {
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2',
                    default => 'application/octet-stream',
                };

                return 'url("data:'.$mimeType.';base64,'.base64_encode($contents).'")';
            },
            $styles,
        );

        if ($result === null) {
            throw new RuntimeException('Tidak dapat menyiapkan font dan aset kartu.');
        }

        return $result;
    }

    private function imageDataUri(string $path, string $fallbackMimeType): string
    {
        if (! is_file($path)) {
            return 'data:image/png;base64,';
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Tidak dapat membaca gambar kartu.');
        }

        $mimeType = mime_content_type($path) ?: $fallbackMimeType;

        return 'data:'.$mimeType.';base64,'.base64_encode($contents);
    }

    private function browserExecutable(): string
    {
        $configuredPath = config('services.ktm_chrome_path');
        if (is_string($configuredPath) && $configuredPath !== '') {
            abort_unless(is_file($configuredPath), 503, 'Lokasi browser di KTM_CHROME_PATH tidak ditemukan.');

            return $configuredPath;
        }

        $finder = new ExecutableFinder;
        foreach (['chrome', 'msedge', 'chromium', 'chromium-browser', 'google-chrome', 'google-chrome-stable'] as $name) {
            if ($executable = $finder->find($name)) {
                return $executable;
            }
        }

        $programFiles = getenv('ProgramFiles') ?: 'C:\\Program Files';
        $programFilesX86 = getenv('ProgramFiles(x86)') ?: 'C:\\Program Files (x86)';
        $localAppData = getenv('LOCALAPPDATA') ?: '';
        $commonPaths = [
            $programFiles.'\\Google\\Chrome\\Application\\chrome.exe',
            $programFilesX86.'\\Google\\Chrome\\Application\\chrome.exe',
            $programFiles.'\\Microsoft\\Edge\\Application\\msedge.exe',
            $programFilesX86.'\\Microsoft\\Edge\\Application\\msedge.exe',
            $localAppData.'\\Google\\Chrome\\Application\\chrome.exe',
        ];

        foreach ($commonPaths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        abort(503, 'Browser Chromium tidak ditemukan. Atur KTM_CHROME_PATH ke lokasi Chrome atau Edge.');
    }

    private function fileUrl(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        if (preg_match('/^[A-Za-z]:\//', $path) === 1) {
            $path = '/'.$path;
        }

        $segments = array_map('rawurlencode', explode('/', $path));

        return 'file://'.str_replace('%3A', ':', implode('/', $segments));
    }

    private function createTemporaryDirectory(): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'ktm-card-');
        if ($temporaryFile === false) {
            throw new RuntimeException('Tidak dapat membuat lokasi sementara untuk PNG kartu.');
        }

        if (! unlink($temporaryFile) || ! mkdir($temporaryFile, 0700)) {
            throw new RuntimeException('Tidak dapat menyiapkan lokasi sementara untuk PNG kartu.');
        }

        return $temporaryFile;
    }

    private function removeTemporaryDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS) as $entry) {
            $path = $entry->getPathname();

            if ($entry->isDir() && ! $entry->isLink()) {
                $this->removeTemporaryDirectory($path);
            } else {
                $this->removeTemporaryFile($path);
            }
        }

        if (! rmdir($directory)) {
            throw new RuntimeException('Tidak dapat menghapus direktori sementara kartu.');
        }
    }

    private function removeTemporaryFile(string $path): void
    {
        $attempts = PHP_OS_FAMILY === 'Windows' ? 30 : 1;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                if (unlink($path)) {
                    return;
                }
            } catch (\ErrorException $exception) {
                $message = $exception->getMessage();
                $isFileLocked = str_contains($message, 'Permission denied')
                    || str_contains($message, 'Resource temporarily unavailable');

                if (! $isFileLocked || $attempt === $attempts) {
                    throw $exception;
                }
            }

            if ($attempt < $attempts) {
                usleep(100_000);
            }
        }

        throw new RuntimeException('Tidak dapat menghapus file sementara kartu: '.$path);
    }
}
