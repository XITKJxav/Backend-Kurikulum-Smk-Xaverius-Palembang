<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusAgendaUpacara extends Model
{
    protected $table = 'statusagendaupacara';
    protected $primaryKey = 'id_status_upacara';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_status_upacara',
        'nama',
    ];
}
