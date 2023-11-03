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


class RegistroClinico extends Model
{
    protected $table = 'registro_clinico';
	public $timestamps = false;
	protected $primaryKey = 'id';


	public static function dataHistorialRegistroClinico($caso){
        $datos = RegistroClinico::where("registro_clinico.caso",$caso)
                    ->select("registro_clinico.id","registro_clinico.fecha_creacion","registro_clinico.registro","u.nombres","u.apellido_paterno","u.apellido_materno")
                    ->join("usuarios as u", "u.id","=","registro_clinico.usuario")
                    ->where("registro_clinico.visible",true)->get();
        $response = [];
        foreach($datos as $d){
            $opciones = "<button class='btn btn-primary' type='button' onclick='editarRegistroClinico(".$d->id.")'>Ver/Editar</button> <br><br>
            <button type='button' class='btn btn-danger' onclick='eliminarRegistroClinico(" . $d->id . ")'>Eliminar</button>";
            // $fecha=date("d-m-Y H:i", strtotime($d->fecha_creacion));
            $registro = $d->registro;
           
            $usuario_aplica  = strtoupper($d->nombres)." ".strtoupper($d->apellido_paterno)." ".strtoupper($d->apellido_materno);
            
            $response[] = [$usuario_aplica, $registro,$opciones];
        }
		return $response;
    }

}
