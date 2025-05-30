<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $primaryKey = "id_role";
    protected $autoIncrement = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name'
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_role', 'id_role');
    }
}
