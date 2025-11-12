<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reporte';
    protected $primaryKey = 'Id_Reporte';
    public $timestamps = false;

    protected $fillable = [
        'Id_Expediente',
        'Periodo_Ini',
        'Periodo_Fin',
        'Resumen_Actividad',
        'Numero_Reporte',
        'Reporte_Final',
        'Archivo_Agregado',
        'Calificacion',
        'Observaciones',
        'Nombre_Archivo'
    ];
}
