<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class KtmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'nim' => ['required', 'string', 'max:30', 'regex:/^[0-9A-Za-z.-]+$/', 'unique:ktms,nim'],
            'program_studi' => ['required', 'string', 'max:120'],
            'angkatan' => ['required', 'integer', 'min:2000', 'max:' . (now()->year)],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'persetujuan' => ['accepted'],
        ];
    }

    #[Override]
    public function messages()
    {
        return [
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
            'angkatan.max' => 'Angkatan tidak boleh lebih dari ' . (now()->year) . '.',
            'foto.required' => 'Pas foto harus diunggah.',
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'File yang diunggah harus berupa JPG atau PNG.',
            'foto.max' => 'Ukuran file pas foto tidak boleh lebih dari 5 MB.',
            'persetujuan.accepted' => 'Anda harus menyetujui penggunaan data dan foto untuk keperluan penerbitan KTM serta verifikasi akademik.',
        ];
    }
}
