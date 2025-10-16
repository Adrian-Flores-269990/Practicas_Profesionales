<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DependenciaMercadoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'dependenciamercadosolicitud';
    protected $primaryKey = 'Id_DPMS';
    public $timestamps = false;

    protected $fillable = [
        'Id_Solicitud_FPP01',
        'Id_Depend_Emp',
        'Id_Publico',
        'Id_Privado',
        'Id_UASLP',
        'Id_Mercado',
        'Porcentaje'
    ];
/*
    // Relación con la solicitud principal (FPP01)
    public function solicitud()
    {
        return $this->belongsTo(SolicitudFPP01::class, 'Id_Solicitud_FPP01', 'Id_Solicitud_FPP01');
    }

    // Relación con dependencia (empresa)
    public function dependenciaEmpresa()
    {
        return $this->belongsTo(DependenciaEmpresa::class, 'Id_Depn_Emp', 'Id_Depn_Emp');
    }

    // Relación con sector privado
    public function sectorPrivado()
    {
        return $this->belongsTo(SectorPrivado::class, 'Id_Privado', 'Id_Privado');
    }

    // Relación con sector público
    public function sectorPublico()
    {
        return $this->belongsTo(SectorPublico::class, 'Id_Publico', 'Id_Publico');
    }

    // Relación con sector UASLP
    public function sectorUaslp()
    {
        return $this->belongsTo(SectorUaslp::class, 'Id_UASLP', 'Id_UASLP');
    }

    // Relación con el tipo de mercado
    public function mercado()
    {
        return $this->belongsTo(Mercado::class, 'Id_Mercado', 'Id_Mercado');
    }*/
}
