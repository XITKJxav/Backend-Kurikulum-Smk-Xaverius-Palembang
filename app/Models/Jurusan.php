<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RuanganKelas;

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

    public function ruanganKelas()
    {
        return $this->hasMany(RuanganKelas::class, 'kd_jurusan');
    }
}
