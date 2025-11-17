<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('formulir_pbb', function (Blueprint $table) {
            $table->id();
            $table->string('id_formulir'); // FK ke formulir_tarikan.id
            $table->unsignedBigInteger('id_pbb'); // FK ke pbb.id
            $table->year('tahun');
            $table->timestamps();

            $table->foreign('id_formulir')->references('id')->on('formulir_tarikan')->onDelete('cascade');
            $table->foreign('id_pbb')->references('id')->on('pbb')->onDelete('cascade');
            $table->index(['id_formulir', 'id_pbb', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulir_pbb');
    }
};
