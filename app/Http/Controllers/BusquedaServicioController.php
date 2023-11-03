<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \App\Models\Unidad;
use View;
use \App\Models\UnidadEnEstablecimiento;



class BusquedaServicioController extends Controller{

	public function viewBusqueda(){
		$response=array();
		$servicios=Unidad::orderBy('nombre', 'asc')->get();
		foreach($servicios as $servicio) $response[$servicio->id]=$servicio->nombre;
		return View::make("Busqueda/Servicio", ["servicios" => $response]);
	}

	public function getCuposEstablecimiento(Request $request){
		$a = $request->input('servicio');

		$servicios = UnidadEnEstablecimiento::whereHas('serviciosOfrecidos', function($q) use ($a) {
			$q->where('unidad', $a);
		})->with("camasLibres")->with("establecimientos")->get();

		$camas = [];
		foreach($servicios as $servicio){
			$camas[] = [$servicio->establecimientos->nombre, $servicio->alias, $servicio->camasLibres->count() ];
		}
		return response()->json($camas);

	}

}
