<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Rol;

class Empleado extends Authenticatable
{
    protected $table = 'encargado';  // Nombre exacto de la tabla
    protected $primaryKey = 'Id_Encargado';
    public $timestamps = false;

    protected $fillable = [
        'Id_Rol','Nombre','RPE','Area','Carrera','Cargo','Correo','Telefono'
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'Id_Rol');
    }
}
