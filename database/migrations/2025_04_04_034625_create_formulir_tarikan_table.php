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
        Schema::create('formulir_tarikan', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedSmallInteger('id_kelompok');
            $table->decimal('total', 15, 2);
            $table->date('jadwal_pembayaran');
            $table->enum('status', ['belum lunas', 'lunas']);
            $table->date('tgl_bayar')->nullable();
            $table->string('bukti', 100)->nullable();
            $table->unsignedBigInteger('id_setoran')->nullable();
            $table->year('tahun')->nullable();
            $table->timestamps();

            $table->foreign('id_kelompok')->references('id')->on('kelompok');
            $table->foreign('id_setoran')->references('id')->on('setoran_pbb');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulir_tarikan');
    }
};
