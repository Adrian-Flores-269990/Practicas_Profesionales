<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;

    protected $table = 'version';
    protected $primaryKey = 'Id_Version';
    public $timestamps = false;

    protected $fillable = [
        'Id_Carrera_I',
        'Vigente',
        'Num_Version'
    ];
}
