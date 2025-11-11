<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expediente';
    protected $primaryKey = 'Id_Expediente';
    public $timestamps = false;

    protected $fillable = [
        'Id_Solicitud_FPP01',
        'Solicitud_FPP01_Firmada',
        'Id_Solicitud_FPP02',
        'Solicitud_FPP02_Firmada',
        'Id_Carta_Presentacion',
        'Carta_Presentacion_Firmada',
        'Carta_Aceptacion',
        'Carta_Desglose_Percepciones',
        'Carta_Termino',
        'Carta_Validacion',
        'Constancia',
        'Contador_Reportes',
    ];
}
