<?php

namespace App\Models;
use App\Models\PlanificacionCuidadoAtencionEnfermeria;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaCuidadoEnfermeriaAtencion extends Model
{
    public $table = "formulario_hoja_enfermeria_cuidado_enfermeria_atencion";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;


    public function planificacion()
    {
        return $this->belongsTo(PlanificacionCuidadoAtencionEnfermeria::class, "id_atencion");
    }

}
