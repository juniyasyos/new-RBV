<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_unit_kerja')) {
            Schema::create('user_unit_kerja', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
                $table->unsignedBigInteger('unit_kerja_id');
                $table->foreign('unit_kerja_id')->references('id')->on('unit_kerja')->cascadeOnDelete()->cascadeOnUpdate();
                $table->primary(['user_id', 'unit_kerja_id']);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_unit_kerja');
    }
};
