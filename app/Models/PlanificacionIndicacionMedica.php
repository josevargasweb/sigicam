<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionIndicacionMedica extends Model
{
    public $table = "formulario_planificacion_indicaciones_medicas";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;

    public static function indicacionMedica($id){
      return PlanificacionIndicacionMedica::where('id',$id)->where('visible',true)->get();
    }
}
