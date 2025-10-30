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

    public function dependenciaEmpresa()
    {
        return $this->belongsTo(\App\Models\DependenciaEmpresa::class, 'Id_Depend_Emp', 'Id_Depn_Emp');
    }

    public function sectorPrivado()
    {
        return $this->belongsTo(\App\Models\SectorPrivado::class, 'Id_Privado', 'Id_Privado');
    }

    public function sectorPublico()
    {
        return $this->belongsTo(\App\Models\SectorPublico::class, 'Id_Publico', 'Id_Publico');
    }

    public function sectorUaslp()
    {
        return $this->belongsTo(\App\Models\SectorUaslp::class, 'Id_UASLP', 'Id_UASLP');
    }

}
