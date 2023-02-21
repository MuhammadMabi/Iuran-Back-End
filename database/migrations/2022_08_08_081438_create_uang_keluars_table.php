<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUangKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->index();
            $table->text('jenis_pengeluaran', 50);
            $table->integer('total');
            $table->text('keterangan');
            $table->date('tanggal_pengeluaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uang_keluars');
    }
}
