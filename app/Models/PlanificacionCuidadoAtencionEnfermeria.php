<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionCuidadoAtencionEnfermeria extends Model
{
    public $table = "formulario_planificacion_cuidados_atencion_enfermeria";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;

    public static function obtenerHorasPlanificacion($tipo, $caso, $responsable){
		$horasP=PlanificacionCuidadoAtencionEnfermeria::select('horario')->where('tipo',$tipo)->where('caso',$caso)->where('resp_atencion', $responsable)->whereNull('fecha_modificacion')->whereNotNull('horario')->orderBy('horario')->get();
        $horas = [];
        foreach($horasP as $hora){
            $horas[] = $hora->horario;
        }
        return $horas;
	}
    
}
