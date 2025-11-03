<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'Id_Bitacora';
    public $timestamps = false;

    protected $fillable = [
        'Clave_Usuario',
        'Fecha',
        'Hora',
        'Movimiento',
        'IP'
    ];

    public function alumno()
    {
        return $this->belongsTo(\App\Models\Alumno::class, 'Clave_Usuario', 'Clave_Alumno');
    }

    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'Clave_Usuario', 'RPE');
    }

}
