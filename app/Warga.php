<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $primaryKey = 'nik';
    protected $table = 'wargas';
    protected $fillable = ['nik','no_kk','nama','tanggal_lahir','jenis_kelamin','password','role'];
}
