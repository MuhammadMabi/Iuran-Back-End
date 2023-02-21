<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeteranganRumah extends Model
{
    protected $primaryKey = 'no_kk';
    protected $table = 'keterangan_rumahs';
    protected $fillable = ['no_kk','nama_kepala_keluarga','no_rumah','alamat'];
}
