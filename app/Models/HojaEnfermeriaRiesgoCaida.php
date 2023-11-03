<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class HojaEnfermeriaRiesgoCaida extends Model
{
    public $table = "formulario_hoja_enfermeria_riesgo_caida";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id','caso','usuario_ingresa','fecha_creacion','usuario_modifica','fecha_modificacion','visible','fecha_eliminacion','horario','criterio_edad','criterio_compr_conciencia','criterio_agi_psicomotora','criterio_lim_sensorial','criterio_lim_motora','total','id_anterior','tipo_modificacion','procedencia','medicamentos','caidas_previas','deficits_sensoriales','estado_mental','deambulacion','edad','antecedentes','tipo'
    ];

    public $timestamps = false;

    public static function dataHistorialMacdems($caso){
        $datos = HojaEnfermeriaRiesgoCaida::where("formulario_hoja_enfermeria_riesgo_caida.caso",$caso)
                    ->select("formulario_hoja_enfermeria_riesgo_caida.id","formulario_hoja_enfermeria_riesgo_caida.fecha_creacion","formulario_hoja_enfermeria_riesgo_caida.edad","formulario_hoja_enfermeria_riesgo_caida.antecedentes","formulario_hoja_enfermeria_riesgo_caida.caidas_previas","formulario_hoja_enfermeria_riesgo_caida.criterio_compr_conciencia","u.nombres","u.apellido_paterno","u.apellido_materno")
                    ->join("usuarios as u", "u.id","=","formulario_hoja_enfermeria_riesgo_caida.usuario_ingresa")
                    ->where("formulario_hoja_enfermeria_riesgo_caida.procedencia", "=","Macdems")
                    ->where("formulario_hoja_enfermeria_riesgo_caida.visible",true)->get();
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
                $total += 3;
                $edad = 3;
            }else{
                $total += 2;
                $edad = 2;
            }
            $antecedentes = ($d->antecedentes == 0)?0:1;
            $total += ($d->caidas_previas == 0)?0:1;
            $total += ($d->antecedentes == 0)?0:1;
            $total += ($d->criterio_compr_conciencia == false)?0:1;
            $detalle = '';
            if($total <= 1){
                $detalle = "Bajo Riesgo";
            }
            if($total >= 2 && $total <= 3){
                $detalle = "Mediano Riesgo";
            }
            if($total >= 4){
                $detalle = "Alto Riesgo";
            }
            $compromiso_conciencia = ($d->criterio_compr_conciencia == true) ? 1 : 0; 
            $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);
            
            $response[] = [$opciones, $usuario_aplica, $fecha, $total, $detalle, $edad, $d->caidas_previas, $antecedentes, $compromiso_conciencia ];
        }
		return $response;
    }
    
    public static function dataHistorialRiesgoCaida($caso){
        $datos = HojaEnfermeriaRiesgoCaida::where("formulario_hoja_enfermeria_riesgo_caida.caso",$caso)
            ->select("formulario_hoja_enfermeria_riesgo_caida.id",
            "formulario_hoja_enfermeria_riesgo_caida.fecha_creacion",
            "formulario_hoja_enfermeria_riesgo_caida.caidas_previas",
            "formulario_hoja_enfermeria_riesgo_caida.deficits_sensoriales",
            "formulario_hoja_enfermeria_riesgo_caida.medicamentos",
            "formulario_hoja_enfermeria_riesgo_caida.estado_mental",
            "formulario_hoja_enfermeria_riesgo_caida.deambulacion",
            "formulario_hoja_enfermeria_riesgo_caida.tipo",
            "u.nombres","u.apellido_paterno","u.apellido_materno")
            ->join("usuarios as u", "u.id","=","formulario_hoja_enfermeria_riesgo_caida.usuario_ingresa")
            ->where("formulario_hoja_enfermeria_riesgo_caida.procedencia", "=","Formulario1")
            ->where("formulario_hoja_enfermeria_riesgo_caida.visible",true)
            ->get();

        Log::info($datos);

        $response = [];
        foreach($datos as $d){
            $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$d->id.")'>Ver/Editar</button>";
            $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
            $total = 0;
            $total += ($d->caidas_previas == 0)?0:1;
            $total += ($d->deficits_sensoriales == 0)?0:1;
            if(strpos($d->medicamentos, ',')) {
                $total +=  count(explode( ',', $d->medicamentos ));
           }else{
               $total += ($d->medicamentos == 0)?0:1;
           }
            $total += ($d->estado_mental == 0)?0:1;
            $total += ($d->deambulacion == 0)?0:1;
            $detalle = '';
            $tipo = ($d->tipo != null)?$d->tipo: "No posee";
            $detalle = "Alto Riesgo - ".$tipo;
            if($total <= 1){
                $detalle = "Bajo Riesgo - ".$tipo;
            }
            
            $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);

            $response[] = [$opciones,$usuario_aplica, $fecha, $total, $detalle, $d->caidas_previas, $d->medicamentos, $d->deficits_sensoriales, $d->estado_mental, $d->deambulacion];
        }
		return $response;
	}
}