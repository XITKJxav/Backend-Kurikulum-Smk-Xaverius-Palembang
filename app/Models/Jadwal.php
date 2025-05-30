<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = ['id_ruangan_kelas', 'kd_jam_pembelajaran', 'id_hari'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "id_mata_pelajaran",
        "id_pengajar",
        "kd_jam_pembelajaran",
        "id_hari",
        "kd_guru_piket",
        "id_ruangan_kelas"
    ];



    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mata_pelajaran', 'id_mata_pelajaran');
    }

    public function jamBelajar()
    {
        return $this->belongsTo(JamBelajar::class, 'kd_jam_pembelajaran', 'id');
    }

    public function hari()
    {
        return $this->belongsTo(Hari::class, 'id_hari', 'id');
    }

    public function guruPiket()
    {
        return $this->belongsTo(Karyawan::class, 'kd_guru_piket', 'kd_karyawan');
    }

    public function pengajar()
    {
        return $this->belongsTo(Karyawan::class, 'id_pengajar', 'kd_karyawan');
    }
}
