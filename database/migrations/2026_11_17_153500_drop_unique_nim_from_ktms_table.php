<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ktms', function (Blueprint $table) {
            $table->dropUnique('ktms_nim_unique');
            $table->index('nim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ktms', function (Blueprint $table) {
            $table->dropIndex(['nim']);
            $table->unique('nim');
        });
    }
};
