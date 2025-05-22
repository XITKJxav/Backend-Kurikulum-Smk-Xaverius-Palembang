<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id_mata_pelajaran';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_mata_pelajaran',
        'nama',
        'status',
    ];
}
