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


class UsoRestringido extends Model
{
    protected $table = 'formulario_uso_restringido';
	public $timestamps = false;
	protected $primaryKey = 'id';


	public static function dataHistorialUsoRestringido($caso){
        $datos = UsoRestringido::where("formulario_uso_restringido.caso",$caso)
                    ->select("formulario_uso_restringido.id","formulario_uso_restringido.fecha_creacion","u.nombres","u.apellido_paterno","u.apellido_materno")
                    ->join("usuarios as u", "u.id","=","formulario_uso_restringido.usuario")
                    ->where("formulario_uso_restringido.visible",true)->get();
        $response = [];
        foreach($datos as $d){
            $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$d->id.")'>Ver/Editar</button>";
            $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
           
            $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);
            
            $response[] = [$opciones, $usuario_aplica, $fecha];
        }
		return $response;
    }

}
