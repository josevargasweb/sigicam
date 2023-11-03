<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormularioExamenGinecoobstetrico;

class FormularioExamenGinecoobstetricoController extends Controller
{
    public function guardar(Request $request) {
		try{
			$vm = new FormularioExamenGinecoobstetrico();
			$vm->guardar($request);
			return response()->json(["error" => false]);
		}catch(\Exception $e){
			return response()->json(["error" => true,"msg" => $e->getMessage().",".$e->getLine().",".$e->getFile(),"e" => get_class($e)]);
		}
	}
	public function cargar(Request $request){
		try{
			$vm = new FormularioExamenGinecoobstetrico();
			$dato = $vm->cargar($request);
			return response()->json($dato);
		}catch(\Exception $e){
			return response()->json(["error" => true,"msg" => $e->getMessage().",".$e->getLine().",".$e->getFile(),"e" => get_class($e)]);
		}
	}
}
