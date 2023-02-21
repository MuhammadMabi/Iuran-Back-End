<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UangMasuk extends Model
{
    protected $primaryKey = 'id';
    protected $foreignKey = 'no_kk';
    protected $table = 'uang_masuks';
    protected $fillable = ['no_kk','jumblah_bayar','tanggal_setoran','bulan','tahun'];
}
