<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudFPP02 extends Model
{
    use HasFactory;

    protected $table = 'solicitud_fpp02';
    protected $primaryKey = 'Id_Solicitud_FPP02';
    public $timestamps = false;

    protected $fillable = [
        'Id_Solicitud_FPP02',
        'Asignacion_Oficial_DSSPP',
        'Fecha_Asignacion',
        'Servicio_Social',
        'Num_Meses',
        'Total_Horas',
        'Autorizacion',
        'Fecha_Autorizacion',
    ];
}
