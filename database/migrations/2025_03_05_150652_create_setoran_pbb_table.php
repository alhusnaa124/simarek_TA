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
        Schema::create('setoran_pbb', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_petugas');
            $table->unsignedBigInteger('id_verifikator')->nullable();
            $table->date('tanggal_setor');
            $table->decimal('jumlah_setor', 15, 2);
            $table->enum('status', ['Di terima', 'Di Tolak'])->nullable();
            $table->string('catatan', 50)->nullable();
            $table->year('tahun');
            $table->timestamps();

            $table->foreign('id_petugas')->references('id')->on('users');
            $table->foreign('id_verifikator')->references('id')->on('users');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_pbb');
    }
};
