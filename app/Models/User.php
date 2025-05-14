<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\RuanganKelas;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'users';
    protected $primaryKey = 'kd_kepengurusan_kelas';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kd_murid',
        'name',
        'email',
        'no_telp',
        'status',
        'password',
        'id_ruang_kelas',
        'otp',
        'otp_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable', 'tokenable_type', 'tokenable_id', 'kd_kepengurusan_kelas');
    }

    public function ruanganKelas()
    {
        return $this->belongsTo(RuanganKelas::class, 'id_ruang_kelas');
    }
}
