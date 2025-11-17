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
        Schema::create('pbb', function (Blueprint $table) {
            $table->id();
            $table->string('nop', 19);
            $table->year('tahun');
            $table->unsignedSmallInteger('no_wp');
            $table->string('alamat_op', 100);
            $table->unsignedSmallInteger('luas_tnh');
            $table->unsignedSmallInteger('luas_bgn');
            $table->decimal('pajak_terhutang', 15, 2);
            $table->enum('jenis', ['darat', 'sawah']);
            $table->decimal('dharma_tirta', 15, 2);
            $table->enum('status', ['lunas','belum']);
            $table->timestamps();

            $table->foreign('no_wp')->references('id')->on('wajib_pajak')->onDelete('cascade');
            $table->unique(['nop', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbb');
    }
};
