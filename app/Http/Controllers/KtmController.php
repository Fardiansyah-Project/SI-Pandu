<?php

namespace App\Http\Controllers;

use App\Models\Ktm;
use App\Services\DownloadCardServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KtmController extends Controller
{
    public function create(): View
    {
        return view('ktm.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nim' => ['required', 'string', 'max:30', 'regex:/^[0-9A-Za-z.-]+$/', 'unique:ktms,nim'],
            'program_studi' => ['required', 'string', 'max:120'],
            'angkatan' => ['required', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'persetujuan' => ['accepted'],
        ], [
            'nama.required' => 'Nama lengkap harus diisi.',
            'nama.string' => 'Nama lengkap harus berupa teks.',
            'nama.max' => 'Nama lengkap tidak boleh lebih dari 120 karakter.',
            'nim.required' => 'NIM harus diisi.',
            'nim.string' => 'NIM harus berupa teks.',
            'nim.max' => 'NIM tidak boleh lebih dari 30 karakter.',
            'nim.regex' => 'NIM hanya boleh berisi huruf, angka, titik, dan tanda hubung.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'program_studi.required' => 'Program studi harus diisi.',
            'program_studi.string' => 'Program studi tidak valid.',
            'program_studi.max' => 'Program studi tidak boleh lebih dari 120 karakter.',
            'angkatan.required' => 'Angkatan harus diisi.',
            'angkatan.integer' => 'Angkatan harus berupa angka.',
            'angkatan.min' => 'Angkatan tidak boleh kurang dari 2000.',
            'angkatan.max' => 'Angkatan tidak boleh lebih dari '.(now()->year + 1).'.',
            'foto.required' => 'Pas foto harus diunggah.',
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'File yang diunggah harus berupa JPG atau PNG.',
            'foto.max' => 'Ukuran file pas foto tidak boleh lebih dari 5 MB.',
            'persetujuan.accepted' => 'Anda harus menyetujui penggunaan data dan foto untuk keperluan penerbitan KTM serta verifikasi akademik.',
        ]);

        $fotoPath = $request->file('foto')->store('foto-ktm', 'public');
        $ktm = Ktm::create([
            'nama' => $data['nama'],
            'nim' => $data['nim'],
            'program_studi' => $data['program_studi'],
            'angkatan' => $data['angkatan'],
            'foto_path' => $fotoPath,
            'kode_pengajuan' => 'KTM-'.strtoupper(Str::random(8)),
            'token_verifikasi' => Str::uuid()->toString(),
        ]);

        return redirect()->route('ktm.success', $ktm->kode_pengajuan);
    }

    public function success(string $code): View
    {
        return view('ktm.success', ['ktm' => Ktm::where('kode_pengajuan', $code)->firstOrFail()]);
    }

    public function verify(string $token): View
    {
        return view('ktm.verify', ['ktm' => Ktm::where('token_verifikasi', $token)->first()]);
    }

    public function submittedPhoto(string $code)
    {
        $ktm = Ktm::where('kode_pengajuan', $code)->firstOrFail();
        abort_unless(Storage::disk('public')->exists($ktm->foto_path), 404);

        return Storage::disk('public')->download($ktm->foto_path, 'foto-'.$ktm->nim.'.jpg');
    }

    public function photo(Ktm $ktm)
    {
        abort_unless(Storage::disk('public')->exists($ktm->foto_path), 404);

        return Storage::disk('public')->response($ktm->foto_path, 'foto-'.$ktm->nim.'.jpg', [
            'Content-Type' => Storage::disk('public')->mimeType($ktm->foto_path) ?: 'application/octet-stream',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function card(Ktm $ktm, DownloadCardServices $downloadCardServices): View
    {
        abort_unless($ktm->status === 'approved', 403, 'KTM harus disetujui sebelum dicetak.');

        return view('ktm.card', [
            'ktm' => $ktm,
            'verificationQr' => 'data:image/png;base64,'.base64_encode($downloadCardServices->verificationQr($ktm)),
        ]);
    }

    public function downloadCard(Ktm $ktm, string $side, DownloadCardServices $downloadCardServices): StreamedResponse
    {
        return $downloadCardServices->downloadCard($ktm, $side);
    }

    public function index(Request $request): View
    {
        $query = Ktm::query();
        if ($search = $request->string('q')->toString()) {
            $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.ktm.index', [
            'ktms' => $query->latest()->paginate(12)->withQueryString(),
            'total' => Ktm::count(),
            'pending' => Ktm::where('status', 'pending')->count(),
            'approved' => Ktm::where('status', 'approved')->count(),
            'rejected' => Ktm::where('status', 'rejected')->count(),
        ]);
    }

    public function update(Request $request, Ktm $ktm): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);
        $ktm->update(['status' => $data['status'], 'catatan' => $data['catatan'] ?? null, 'approved_at' => $data['status'] === 'approved' ? now() : null]);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function destroy(Ktm $ktm): RedirectResponse
    {
        $ktm->delete();

        return back()->with('success', 'Pengajuan berhasil dihapus.');
    }
}
