<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    protected $table = 'pengajar';
    protected $primaryKey = 'id_pengajar';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "id_pengajar",
        "kd_karyawan",
        "id_mata_pelajaran",
        "status"
    ];
}
