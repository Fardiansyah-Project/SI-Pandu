<?php

namespace Tests\Feature;

use App\Http\Controllers\KtmController;
use App\Models\Ktm;
use App\Services\DownloadCardServices;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

class KtmCardTest extends TestCase
{
    public function test_approved_card_preview_and_front_download_include_the_design(): void
    {
        $ktm = new Ktm([
            'nama' => 'Alisya Felisya',
            'nim' => '55201230XX',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => 2023,
            'foto_path' => 'foto-ktm/test-student.png',
            'kode_pengajuan' => 'KTM-TEST123',
            'token_verifikasi' => Str::uuid()->toString(),
            'status' => 'approved',
        ]);
        $ktm->setAttribute('id', 1);
        Storage::fake('public');

        $photo = imagecreatetruecolor(100, 200);
        $photoColor = imagecolorallocate($photo, 200, 40, 120);
        imagefill($photo, 0, 0, $photoColor);
        ob_start();
        imagepng($photo);
        $photoPng = ob_get_clean();
        imagedestroy($photo);
        Storage::disk('public')->put($ktm->foto_path, $photoPng);

        $controller = app(KtmController::class);
        $downloadCardServices = app(DownloadCardServices::class);

        $preview = $controller->card($ktm, $downloadCardServices)->render();
        $this->assertStringContainsString('uppercase">Kartu Mahasiswa</div>', $preview);
        $this->assertStringContainsString('aspect-[1011/639]', $preview);
        $this->assertStringContainsString('100cqw / 1011px', $preview);
        $this->assertStringContainsString('card-back_TI.png', $preview);
        $this->assertStringContainsString('text-[45px] font-bold leading-tight', $preview);

        preg_match('/id="nama".*?font-size:\s*(\d+)px;/s', $preview, $defaultNameFontSize);
        $this->assertSame('45', $defaultNameFontSize[1] ?? null);

        $longNameKtm = clone $ktm;
        $longNameKtm->nama = 'Karina Aespa L.Lamajido';
        $longNamePreview = $controller->card($longNameKtm, $downloadCardServices)->render();
        preg_match('/id="nama".*?font-size:\s*(\d+)px;/s', $longNamePreview, $longNameFontSize);
        $this->assertLessThan(45, (int) ($longNameFontSize[1] ?? 45));

        $response = $controller->downloadCard($ktm, 'depan', $downloadCardServices);
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KTM-'.$ktm->nim.'-depan.png', $response->headers->get('Content-Disposition'));
        $expectedBytes = $response->getFile()->getSize();
        $response->prepare(Request::create('/download'));
        $this->assertSame((string) $expectedBytes, $response->headers->get('Content-Length'));

        ob_start();
        $response->sendContent();
        $png = ob_get_clean();

        $this->assertSame($expectedBytes, strlen($png));

        $image = imagecreatefromstring($png);
        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertSame(1011, imagesx($image));
        $this->assertSame(639, imagesy($image));

        $waveColor = imagecolorsforindex($image, imagecolorat($image, 20, 600));
        $this->assertGreaterThan($waveColor['red'], $waveColor['blue']);
        $this->assertGreaterThan($waveColor['green'], $waveColor['blue']);

        $leftMargin = imagecolorsforindex($image, imagecolorat($image, 20, 293));
        $this->assertSame(255, $leftMargin['red']);
        $this->assertSame(255, $leftMargin['green']);
        $this->assertSame(255, $leftMargin['blue']);

        $studentPhoto = imagecolorsforindex($image, imagecolorat($image, 100, 300));
        $this->assertSame(200, $studentPhoto['red']);
        $this->assertSame(40, $studentPhoto['green']);
        $this->assertSame(120, $studentPhoto['blue']);

        $roundedPhotoCorner = imagecolorsforindex($image, imagecolorat($image, 64, 221));
        $this->assertSame(231, $roundedPhotoCorner['red']);
        $this->assertSame(225, $roundedPhotoCorner['green']);
        $this->assertSame(218, $roundedPhotoCorner['blue']);

        $headerOverflowsCard = false;
        for ($y = 40; $y <= 70; $y++) {
            for ($x = 956; $x < 1011; $x++) {
                $headerPixel = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                if ($headerPixel['red'] < 80 && $headerPixel['green'] < 80 && $headerPixel['blue'] < 110) {
                    $headerOverflowsCard = true;
                    break 2;
                }
            }
        }
        $this->assertFalse($headerOverflowsCard);

        $hasWebsiteTextColor = false;
        for ($y = 196; $y <= 375; $y++) {
            for ($x = 302; $x < 942; $x++) {
                $textColor = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                if ($textColor['red'] < 80 && $textColor['green'] < 80 && $textColor['blue'] < 80) {
                    $hasWebsiteTextColor = true;
                    break 2;
                }
            }
        }
        $this->assertTrue($hasWebsiteTextColor);

        imagedestroy($image);
    }

    public function test_back_card_uses_the_matching_template_and_downloads_as_png(): void
    {
        $ktm = new Ktm([
            'nama' => 'Alisya Felisya',
            'nim' => '55201230XX',
            'program_studi' => 'Sistem Informasi',
            'angkatan' => 2023,
            'foto_path' => 'foto-ktm/test-student.png',
            'kode_pengajuan' => 'KTM-TEST456',
            'token_verifikasi' => Str::uuid()->toString(),
            'status' => 'approved',
        ]);
        $ktm->setAttribute('id', 2);

        $controller = app(KtmController::class);
        $downloadCardServices = app(DownloadCardServices::class);
        $preview = $controller->card($ktm, $downloadCardServices)->render();
        $this->assertStringContainsString('card-back_SI.png', $preview);

        $response = $controller->downloadCard($ktm, 'belakang', $downloadCardServices);
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KTM-'.$ktm->nim.'-belakang.png', $response->headers->get('Content-Disposition'));
        $expectedBytes = $response->getFile()->getSize();
        $response->prepare(Request::create('/download'));
        $this->assertSame((string) $expectedBytes, $response->headers->get('Content-Length'));

        ob_start();
        $response->sendContent();
        $png = ob_get_clean();

        $this->assertSame($expectedBytes, strlen($png));

        $template = file_get_contents(public_path('assets/partials/card-back_SI.png'));
        $this->assertNotFalse($template);
        $this->assertSame($template, $png);
    }
}
