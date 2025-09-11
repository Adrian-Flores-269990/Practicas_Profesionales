<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';  // Nombre exacto de tu tabla
    protected $primaryKey = 'clave_alumno'; // Si tu PK no es "id"
    public $timestamps = false;    // Si no tienes created_at / updated_at

    protected $fillable = [
        'nombre_alumno',
        'apellidop_alumno',
        'apellidom_alumno',
        'semestre',
        'carrera',
        'telfono_celular',
        'correo_electronico',
        'password',
        'cve_alumno',
        'rpe'
    ];
}
