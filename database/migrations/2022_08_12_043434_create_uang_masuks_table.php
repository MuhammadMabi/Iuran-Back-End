<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUangMasuksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uang_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 20)->index();
            $table->integer('jumblah_bayar');
            $table->date('tanggal_setoran');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->timestamps();
            // $table->foreign('id_iuran')->references('id')->on('jenis_iurans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uang_masuks');
    }
}
