<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionSolicitud extends Model
{
    use HasFactory;

    protected $table = 'autorizacion_solicitud';
    protected $primaryKey = 'Id_Autorizacion_Solicitud';
    public $timestamps = false;

    protected $fillable = [
        'Autorizo_Empleado', 'Comentario_Encargado', 'Fecha_As', 'Id_Solicitud_FPP01'
    ];
}
