<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudFPP01 extends Model
{
    use HasFactory;

    protected $table = 'solicitud_fpp01';
    protected $primaryKey = 'Id_Solicitud_FPP01';
    public $timestamps = false;

    protected $fillable = [
        'Fecha_Solicitud', 'Numero_Creditos', 'Clave_Alumno',
        'Fecha_Inicio', 'Fecha_Termino', 'Nombre_Proyecto',
        'Actividades', 'Horario_Mat_Ves', 'Apoyo_Economico'
    ];

    public function dependenciaEmpresas()
    {
        return $this->hasMany(DependenciaEmpresaSolicitud::class, 'Id_Solicitud_FPP01');
    }
}
