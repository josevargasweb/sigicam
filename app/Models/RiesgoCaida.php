<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiesgoCaida extends Model
{
    protected $table = 'formulario_riesgo_caida';
	public $timestamps = false;
	protected $primaryKey = 'id_formulario_riesgo_caida';

	public static function dataHistorialMacdems($caso){
		$datos = HojaEnfermeriaRiesgoCaida::where("caso",$caso)
                    ->where("procedencia", "=","Macdems")
                    ->where("visible",true)->get();
        $response = [];
        foreach($datos as $d){
            $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$d->id.")'>Ver/Editar</button>";
            $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
            $total = 0;
            $edad = 0;

            if($d->edad == 0){
                $total += 1;
                $edad = 1;
            }else if($d->edad  == 1 || $d->edad == 2){
                $total += 2;
                $edad = 2;
            }else{
                $total += 3;
                $edad = 3;
            }
            $antecedentes = ($d->antecedentes == 0)?0:1;
            $total += ($d->caidas_previas == 0)?0:1;
            $total += ($d->antecedentes == 0)?0:1;
            $total += ($d->criterio_compr_conciencia == false)?0:1;
            $detalle = '';
            if($total <= 1){
                $detalle = "Bajo Riesgo";
            }else if($total >= 2 && $total <= 3){
                $detalle = "Mediano Riesgo";
            }else{
                $detalle = "Alto Riesgo";
            }
            $compromiso_conciencia = ($d->criterio_compr_conciencia == true) ? 1 : 0; 
            $total = $total ." - ".$detalle;
            $response[] = [$opciones,$fecha, $edad, $d->caidas_previas, $antecedentes, $compromiso_conciencia, $total];
        }
		return $response;
	}
}
?>