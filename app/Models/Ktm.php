<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama', 'nim', 'program_studi', 'angkatan', 'foto_path', 'kode_pengajuan', 'token_verifikasi', 'status', 'catatan', 'approved_at'])]
class Ktm extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return ['angkatan' => 'integer', 'approved_at' => 'datetime'];
    }
}
