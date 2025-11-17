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
        Schema::create('wajib_pajak', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama_wp', 50);
            $table->string('alamat_wp', 80);
            $table->unsignedSmallInteger('id_kelompok')->nullable();
            $table->unsignedBigInteger('id_petugas')->nullable();
            $table->enum('bagian', ['RT001', 'RT002', 'RT003', 'RT004', 'RT005', 'Luar Desa'])->nullable();
            $table->boolean('kepala_keluarga')->default(false);
            $table->timestamps();

            $table->foreign('id_kelompok')->references('id')->on('kelompok');
            $table->foreign('id_petugas')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wajib_pajak');
    }
};
