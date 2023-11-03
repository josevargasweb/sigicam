<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiesgoUlceras extends Model
{
    public $table = "formulario_riesgo_ulceras";
    protected $primaryKey = 'id';
    protected $fillable = [];

    public $timestamps = false;
    
    public static function dataHistorial($caso){
        $datos = RiesgoUlceras::where("formulario_riesgo_ulceras.caso",$caso)
        ->select("formulario_riesgo_ulceras.id",
        "formulario_riesgo_ulceras.fecha_creacion",
        "formulario_riesgo_ulceras.percepcion_sensorial",
        "formulario_riesgo_ulceras.exposicion_humedad",
        "formulario_riesgo_ulceras.actividad",
        "formulario_riesgo_ulceras.movilidad",
        "formulario_riesgo_ulceras.nutricion",
        "formulario_riesgo_ulceras.peligro_lesiones",
        "u.nombres","u.apellido_paterno","u.apellido_materno")
        ->join("usuarios as u", "u.id","=","formulario_riesgo_ulceras.usuario_ingresa")
        ->where("formulario_riesgo_ulceras.visible",true)->get();
        $response = [];
        foreach($datos as $d){
        $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$d->id.")'>Ver/Editar</button>";
        $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
        $total = 0;
        $total += $d->percepcion_sensorial;
        $total += $d->exposicion_humedad;
        $total += $d->actividad;
        $total += $d->movilidad;
        $total += $d->nutricion;
        $total += $d->peligro_lesiones;
        $detalle = '';
        if($total <= 12){
            $detalle = "Alto";
        }
        if($total >= 13 && $total <= 15){
            $detalle = "Medio";
        }
        if($total >= 16){
            $detalle = "Bajo";
        }
        $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);

        $response[] = [$opciones,$usuario_aplica, $fecha, $total, $detalle, $d->percepcion_sensorial, $d->exposicion_humedad, $d->actividad, $d->movilidad, $d->nutricion, $d->peligro_lesiones];
        }
		return $response;
	}
}