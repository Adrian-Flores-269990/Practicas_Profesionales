<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expediente';
    public $timestamps = false;

    protected $primaryKey = 'Id_Expediente'; 

    protected $fillable = [
        'Id_Solicitud_FPP01',
        'Carta_Desglose_Percepciones',
    ];
}
