<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamBelajar extends Model
{
    protected $table = 'jam_belajar';

    protected $fillable = [
        'id',
        'jam_mulai',
        'jam_selesai'
    ];
}
