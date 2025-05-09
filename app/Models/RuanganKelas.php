<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Jurusan;
use App\Models\User;

class RuanganKelas extends Model
{
    use HasFactory;
    protected $table = 'ruangankelas';

    protected $fillable = [
        'nama_ruangan',
        'status',
        'kd_jurusan'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'kd_jurusan');
    }

    public function pengurusKelas()
    {
        return $this->hasMany(User::class, 'kd_kepengurusan_kelas');
    }
}
