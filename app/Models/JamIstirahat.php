<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamIstirahat extends Model
{
    protected $table = 'jam_istirahat';

    protected $fillable = [
        'id',
        'durasi',
    ];
}
