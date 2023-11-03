<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barthel extends Model
{
    //
    protected $table = "formulario_barthel";
	public $timestamps = false;
    protected $primaryKey = "id_formulario_barthel";
    protected $fillable = ['id_formulario_barthel','caso','usuario_responsable','fecha_creacion','comida','lavado','vestido','arreglo','deposicion','miccion','retrete','trasferencia','deambulacion','escaleras','fecha_modificacion','usuario_modifica','visible','id_anterior'];
    
    public static function dataHistorialBarthel($caso){

        $datos = Barthel::where("formulario_barthel.caso",$caso)
                ->select("formulario_barthel.id_formulario_barthel","formulario_barthel.fecha_creacion","formulario_barthel.comida","formulario_barthel.lavado","formulario_barthel.vestido","formulario_barthel.arreglo","formulario_barthel.deposicion","formulario_barthel.miccion","formulario_barthel.retrete","formulario_barthel.trasferencia","formulario_barthel.deambulacion","formulario_barthel.escaleras","u.nombres","u.apellido_paterno","u.apellido_materno","formulario_barthel.tipo")
                ->join("usuarios as u", "u.id","=","formulario_barthel.usuario_responsable")
                ->where("formulario_barthel.visible",true)->get();
        $response = [];
        foreach($datos as $d){
            $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$d->id_formulario_barthel.")'>Ver/Editar</button>";

            $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
            $total = $d->comida + $d->lavado + $d->vestido + $d->arreglo + $d->deposicion + $d->miccion + $d->retrete + $d->trasferencia + $d->deambulacion + $d->escaleras;
            $detalle = '';
            if($total < 20){
                $detalle = "Dependencia Total";
            }else if($total >= 20 && $total <= 35){
                $detalle = "Grave";
            }else if($total >= 40 && $total <= 55){
                $detalle = "Moderada";
            }else if($total >= 60 && $total <= 95){
                $detalle = "Leve";
            }else if($total = 100){
                $detalle = "Independiente";
            }

            $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);
            $tipo = ($d->tipo != null)?$d->tipo: "No posee";

            $response[]  = array($opciones,$usuario_aplica, $fecha, $total, $detalle." - ". $tipo, $d->comida, $d->lavado, $d->vestido, $d->arreglo, $d->deposicion, $d->miccion, $d->retrete, $d->trasferencia, $d->deambulacion, $d->escaleras);
        }
        return $response;
    }
}
