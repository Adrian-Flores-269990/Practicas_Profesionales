<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expediente';
    protected $primaryKey = 'Id_Expediente';
    public $timestamps = false;

    protected $fillable = [
        'Id_Solicitud_FPP01',
        'Solicitud_FPP01_Firmada',
        'Id_Solicitud_FPP02',
        'Solicitud_FPP02_Firmada',
        'Id_Carta_Presentacion',
        'Carta_Presentacion_Firmada',
        'Carta_Aceptacion',
        'Carta_Desglose_Percepciones',
        'Carta_Termino',
        'Carta_Validacion',
        'Constancia',
        'Contador_Reportes',
        // Agregados por Marián Sánchez
        'Carta_Presentacion',
        'Autorizacion_Presentacion',
        'Autorizacion_Aceptacion',
        'Fecha_Autorizacion_Presentacion',
        'Fecha_Autorizacion_Aceptacion',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudFPP01::class, 'Id_Solicitud_FPP01', 'Id_Solicitud_FPP01');
    }

    public function solicitudFPP01()
    {
        return $this->belongsTo(SolicitudFPP01::class, 'Id_Solicitud_FPP01', 'Id_Solicitud_FPP01');
    }

    public function registro()
    {
        return $this->belongsTo(SolicitudFPP02::class, 'Id_Solicitud_FPP02', 'Id_Solicitud_FPP02');
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class, 'Id_Expediente', 'Id_Expediente');
    }
}