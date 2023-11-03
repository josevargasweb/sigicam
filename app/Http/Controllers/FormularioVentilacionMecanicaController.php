<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormularioVentilacionMecanica;

class FormularioVentilacionMecanicaController extends Controller
{
    public function guardar(Request $request) {
		try{
			$vm = new FormularioVentilacionMecanica();
			$vm->guardar($request);
			return response()->json(["error" => false]);
		}catch(\Exception $e){
			return response()->json(["error" => true,"msg" => $e->getMessage().",".$e->getLine().",".$e->getFile(),"e" => get_class($e)]);
		}
	}
	public function lista(Request $request){
		try{
			$vm = new FormularioVentilacionMecanica();
			return $vm->listar($request);
		} catch (\Exception $ex) {
			return $ex->getMessage();
		}
	}
	public function eliminar(Request $request){
		try{
			$fvm = new FormularioVentilacionMecanica();
			$fvm->eliminar($request);
			
			return response()->json(["error" => false]);
		} catch (\Exception $ex) {
			return response()->json(["error" => true,"msg" => $ex->getMessage()]);
		}
	}
}
