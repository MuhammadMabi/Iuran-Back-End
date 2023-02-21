<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UangKeluar extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'uang_keluars';
    protected $fillable = ['nik','jenis_pengeluaran','total','keterangan','tanggal_pengeluaran'];
}
