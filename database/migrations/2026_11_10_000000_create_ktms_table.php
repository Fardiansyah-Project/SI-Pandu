<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ktms', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nim', 30)->unique();
            $table->string('program_studi', 120);
            $table->unsignedSmallInteger('angkatan');
            $table->string('foto_path');
            $table->string('kode_pengajuan', 20)->unique();
            $table->uuid('token_verifikasi')->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->text('catatan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ktms');
    }
};
