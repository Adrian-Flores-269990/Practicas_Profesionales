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
        'Fecha_Solicitud',
        'Numero_Creditos',
        'Induccion_Placticas',
        'Clave_Alumno',
        'Tipo_Seguro',
        'NSF',
        'Situacion_Alunmno_Pasante',
        'Estadistica_General',
        'Constancia_Vig_Der',
        'Carta_Pasante',
        'Egresado_Sit_Esp',
        'Archivo_CVD',
        'Fecha_Inicio',
        'Fecha_Termino',
        'Clave_Encargado',
        'Clave_Asesor_Externo',
        'Datos_Asesor_Externo',
        'Productos_Servicios_Emp',
        'Datos_Empresa',
        'Nombre_Proyecto',
        'Actividades',
        'Horario_Mat_Ves',
        'Horario_Entrada',
        'Horario_Salida',
        'Dias_Semana',
        'Validacion_Creditos',
        'Apoyo_Economico',
        'Extension_Practicas',
        'Expedicion_Recibos',
        'Autorizacion',
        'Propuso_Empresa',
        'Evaluacion',
        'Cancelar',
        'Estado_Encargado',
        'Estado_Departamento'
    ];

    public function autorizacion()
    {
        return $this->hasOne(\App\Models\AutorizacionSolicitud::class, 'Id_Solicitud_FPP01', 'Id_Solicitud_FPP01');
    }

    public function alumno()
    {
        return $this->belongsTo(\App\Models\Alumno::class, 'Clave_Alumno', 'Clave_Alumno');
    }

    public function dependenciaEmpresas()
    {
        return $this->hasMany(DependenciaMercadoSolicitud::class, 'Id_Solicitud_FPP01');
    }

    public function dependenciaMercadoSolicitud()
    {
        return $this->hasOne(DependenciaMercadoSolicitud::class, 'Id_Solicitud_FPP01', 'Id_Solicitud_FPP01');
    }
}
