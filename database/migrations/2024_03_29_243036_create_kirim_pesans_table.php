<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKirimPesansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kirim_pesans', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_berhasil')->nullable();
            $table->timestamps();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('pegawai_id');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->restrictOnDelete();
            $table->foreignId('proses_id');
            $table->foreign('proses_id')->references('id')->on('proses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kirim_pesans');
    }
}
