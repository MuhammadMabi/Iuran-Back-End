<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wargas', function (Blueprint $table) {
            $table->string('nik', 20)->primary();
            $table->string('no_kk', 20)->index();
            $table->string('nama', 50);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin',['Laki-laki', 'Perempuan'])->default('Laki-laki');
            $table->string('password', 100);
            $table->enum('role',['Admin', 'Petugas', 'Warga']);
            $table->rememberToken();
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
        Schema::dropIfExists('wargas');
    }
}
