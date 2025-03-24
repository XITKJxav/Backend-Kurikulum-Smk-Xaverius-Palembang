<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusans'; 
    protected $primaryKey = 'kd_jurusan';  
    protected $keyType = 'string'; 
    
    protected $fillable = [
        'kd_jurusan', 
        'nama_jurusan',
        'status'  
    ];
}
