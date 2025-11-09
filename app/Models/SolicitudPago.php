<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudPago extends Model
{
    protected $table = 'solicitud_pago';
    public $timestamps = false;

    protected $primaryKey = 'Id_Solicitud_Pago'; 

    protected $fillable = [
        'Id_Expediente',
        'Fecha_Solicitud',
        'Fecha_Inicio_Pago',
        'Fecha_Termino_Pago',
        'Salario',
        'Nombre_Persona_Autoriza',
        'Cargo_Persona_Autoriza',
        'Fecha_Entrega',
    ];
}