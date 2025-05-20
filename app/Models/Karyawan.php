<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'kd_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_karyawan',
        'name',
        'email',
        'no_telp',
        'status',
        'email_verified_at',
        'password',
        'otp',
        'otp_expires_at',
        'id_role',
        'remember_token'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }
}
