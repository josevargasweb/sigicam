<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

class PacientesCategorizados extends Controller
{
	public function calcularPorcentaje($mes, $anno, $categorizacion){

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}
		
		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$cantidad = [];
		
		for($i=1; $i<=$cant_dias; $i++){
			$fecha = $anno."-".$mes."-".$i;
			
			$resultado=  DB::select(DB::Raw("select coalesce(count(riesgo), 0) as numero_pacientes 
			from
			(select casos.id, max(tec.fecha) as fecha, tec.riesgo as riesgo
				from casos
				inner join historial_ocupaciones_vista u on u.caso=casos.id
				inner join (select distinct(caso), fecha as fec from t_evolucion_casos where riesgo is not null and urgencia is not true) as t on t.caso = casos.id
				inner join t_evolucion_casos as tec on tec.caso = casos.id 
				inner join establecimientos on casos.establecimiento = establecimientos.id 
				where 
				establecimientos.id = ".Auth::user()->establecimiento." 
				and (tec.fecha <= u.fecha_liberacion or u.fecha_liberacion is null)
				and (tec.fecha::date) = '$fecha'::date 
				AND tec.riesgo is not null 
				and t.fec=tec.fecha 
				and riesgo in ($categorizacion)
				group by (casos.id, tec.riesgo))tab
				group by (riesgo);"));  
			$cantidad[] = (!empty($resultado))?intval($resultado[0]->numero_pacientes):0;
		}

		return ["cantidad"=>$cantidad];
	}

    public function graficoCategorizados(Request $request){

		$meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Spetiembre","Octubre","Noviembre","Diciembre"];
		$mes = $request->input('mes');
		$anno = $request->input('anno');

		if($mes == 0 && $anno == 0){
			$fecha = $meses[Carbon::now()->format("m")-1]." del ".Carbon::now()->format("Y");
		}else{
			$fecha = $meses[$mes-1]." del ".$anno;
		}

		$resultados = [];
		$cantidad = [];

		if (empty($request->categorizacion)) {
			return response()->json(array(
				"resultados"=>$resultados,  
				"cantidad"=>$cantidad,
				"fecha" => $fecha
			));
		}
		
		foreach ($request->categorizacion as $categ) {
			$respuesta  = $this->calcularPorcentaje($mes,$anno,"'$categ'");
			$cantidad [$categ] = $respuesta["cantidad"];
		}
		
		return response()->json(array(
			"resultados"=>$resultados,  
			"cantidad"=>$cantidad,
			"fecha" => $fecha
		));
	}
}
