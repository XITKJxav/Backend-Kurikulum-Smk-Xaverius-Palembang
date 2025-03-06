<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileUpload extends Model
{
    use HasFactory;

    protected $table = 'file_uploads'; 
    protected $primaryKey = 'url_id'; 
    public $incrementing = false; 
    protected $keyType = 'string';
    public $timestamps = false; 

    protected $fillable = [
        'url_id', 
        'name',   
    ];
}

