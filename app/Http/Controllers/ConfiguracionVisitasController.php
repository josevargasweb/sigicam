<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracionVisitas;
use DB;
use Log;

class ConfiguracionVisitasController extends Controller{
	
	public function guardar(Request $request){
		try{
			
			$request->recibe_visitas_ = $request->recibe_visitas_ === "true" ? true : ($request->recibe_visitas_ === "false" ? false : null);
			$request->comentario_visitas_ = ($request->comentario_visitas_) ? $request->comentario_visitas_ : null;
			$request->cantidad_personas_ = ($request->recibe_visitas_) ? ($request->cantidad_personas_ ? $request->cantidad_personas_ : null) : null;
			$request->cantidad_horas_ = ($request->recibe_visitas_) ? ($request->cantidad_horas_ ? $request->cantidad_horas_ : null) : null;
			DB::beginTransaction();
			
			$cv = new ConfiguracionVisitas();
			$cv->ocultarRegistros($request->id_caso_);
			$cv->guardar($request);

			DB::commit();
			
			return response()->json(["exito" => true]);
		} catch (\Exception $ex) {
			Log::info($ex);
			DB::rollback();
			return response()->json(["exito" => false]);
		}
	}
}
