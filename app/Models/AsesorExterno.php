<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsesorExterno extends Model
{
    use HasFactory;

    protected $table = 'asesor_externo';
    protected $primaryKey = 'Id_Asesor_Externo';
    public $timestamps = false;

    protected $fillable = [
        'Nombre', 'Apellido_Paterno', 'Apellido_Materno',
        'Area', 'Puesto', 'Correo', 'Telefono'
    ];
}
