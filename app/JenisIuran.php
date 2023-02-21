<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisIuran extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'jenis_iurans';
    protected $fillable = ['nama','biaya'];
}
