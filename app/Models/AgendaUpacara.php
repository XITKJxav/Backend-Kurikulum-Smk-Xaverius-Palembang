<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaUpacara extends Model
{
    protected $table = 'agendaupacara';
    protected $primaryKey = 'kd_agendaupacara';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kd_agendaupacara',
        'tanggal_upacara',
        'id_status_upacara'
    ];

    public function statusAgendaUpacara()
    {
        return $this->belongsTo(StatusAgendaUpacara::class, 'id_status_upacara');
    }
}
