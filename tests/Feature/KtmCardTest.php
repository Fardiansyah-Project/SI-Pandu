<?php

namespace Tests\Feature;

use App\Http\Controllers\KtmController;
use App\Models\Ktm;
use App\Services\DownloadCardServices;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        $response = $controller->downloadCard($ktm, 'depan', $downloadCardServices);
        ob_start();
        $response->sendContent();
        $png = ob_get_clean();

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
        ob_start();
        $response->sendContent();
        $png = ob_get_clean();

        $image = imagecreatefromstring($png);
        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertSame(1011, imagesx($image));
        $this->assertSame(639, imagesy($image));

        imagedestroy($image);
    }
}
