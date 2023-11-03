<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use Auth;
use Log;


class ExamenLaboratorio extends Model
{
    protected $table = 'formulario_examenes_laboratorio';
	public $timestamps = false;
	protected $primaryKey = 'id';


	public static function dataHistorialExamenLaboratorio($caso){
        $datos = ExamenLaboratorio::where("formulario_examenes_laboratorio.caso",$caso)
                    ->select("formulario_examenes_laboratorio.id","formulario_examenes_laboratorio.fecha_creacion","u.nombres","u.apellido_paterno","u.apellido_materno")
                    ->join("usuarios as u", "u.id","=","formulario_examenes_laboratorio.usuario")
                    ->where("formulario_examenes_laboratorio.visible",true)->get();
        $response = [];
        foreach($datos as $d){
			$opciones = "<div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-warning' onclick='editarExamenLaboratorio(".$d->id.")'>Modificar</button>
            </div>
            <br><br>
            <div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-danger' onclick='eliminarExamenLaboratorio(".$d->id.")'>Eliminar</button>
            </div>";	
            $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
           
            $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);
            
            $response[] = [$opciones, $usuario_aplica, $fecha];
        }
		return $response;
    }


}
