<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionCuidadoIndicacionMedica extends Model
{
    public $table = "formulario_planificacion_cuidados_indicaciones_medicas";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = true;

    public static function indicacionMedica($id){
      return PlanificacionCuidadoIndicacionMedica::where('id',$id)->where('visible',true)->get();
    }
}
