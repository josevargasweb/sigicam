<?php 


namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Session;

class Riesgo extends Model{

	protected $table = "riesgos";
	protected $fillable = [	"dependencia1", 
							"dependencia2",
							"dependencia3",
							"dependencia4",
							"dependencia5",
							"dependencia6",
							"riesgo1",
							"riesgo2",
							"riesgo3",
							"riesgo4",
							"riesgo5",
							"riesgo6",
							"riesgo7",
							"riesgo8",
							"categoria"
							];
	protected $primaryKey = "id";

	public $timestamps = false;

	public static function getRiesgos(){
		return array(
			'A1' => 'A1',
			'A2' => 'A2',
			'A3' => 'A3',
			'B1' => 'B1',
			'B2' => 'B2',
			'B3' => 'B3',
			'C1' => 'C1',
			'C2' => 'C2',
			'C3' => 'C3',
			'D1' => 'D1',
			'D2' => 'D2',
			'D3' => 'D3',
			'sin riesgo' => 'sin riesgo');
	}

	public static function PacientesD2yD3(){

		$estab = Auth::user()->establecimiento;
		if(Session::get('usuario')->tipo != 'admin_ss'){
			//$estab = "establecimientos.id = ".Session::get("idEstablecimiento");
			$estab = "establecimientos.id = ".$estab;
		}else{
			$estab = "TRUE";
		}

		$fecha = date("Y-m-d");
		///////////////////////////////////////////////////////////////////////////////
		//SE AÑADIO CASOS CON FECHA_TERMINO IS NULL PORQUE SE CONTABAN ESOS CASOS TMB//
		///////////////////////////////////////////////////////////////////////////////
		
		$pacientes_servicios=DB::select(DB::raw("select distinct(tab.id), tab.fecha, tab.riesgo, tab.comentario
			from
			(select casos.id, max(tec.fecha) as fecha, tec.riesgo as riesgo, tec.comentario as comentario
			from casos
			inner join historial_ocupaciones_vista u on u.caso=casos.id
			inner join (select distinct(caso), fecha as fec from t_evolucion_casos where riesgo is not null and urgencia is not true) as t on t.caso = casos.id
			inner join t_evolucion_casos as tec on tec.caso = casos.id
			inner join establecimientos on casos.establecimiento = establecimientos.id
			where ".$estab." and
			(tec.fecha::date) = '"."$fecha"."' AND
			tec.riesgo is not null AND
			casos.fecha_termino is null AND
			t.fec=tec.fecha AND
			riesgo in ('D2','D3')
			group by (casos.id, tec.riesgo, tec.comentario))tab
			order by tab.id;"));

		return $pacientes_servicios;

	}

	public static function categorizadosHoy(){

		$estab = Auth::user()->establecimiento;
		
		if(Session::get('usuario')->tipo != 'admin_ss'){
			//$estab = "establecimientos.id = ".Session::get("idEstablecimiento");
			$estab = "establecimientos.id = ".$estab;
		}else{
			$estab = "TRUE";
		}

		$fecha = date("Y-m-d");

		$pacientes_servicios=DB::select(DB::raw("select distinct(tab.id), tab.fecha, tab.riesgo, tab.comentario
			from
			(select casos.id, max(tec.fecha) as fecha, tec.riesgo as riesgo, tec.comentario as comentario
			from casos
			inner join historial_ocupaciones_vista u on u.caso=casos.id
			inner join (select distinct(caso), fecha as fec from t_evolucion_casos where riesgo is not null and urgencia is not true) as t on t.caso = casos.id
			inner join t_evolucion_casos as tec on tec.caso = casos.id
			inner join establecimientos on casos.establecimiento = establecimientos.id
			where
			".$estab." and
			(tec.fecha::date) = '"."$fecha"."' AND
			tec.riesgo is not null and
			t.fec=tec.fecha
			group by (casos.id, tec.riesgo, tec.comentario))tab
			order by tab.id;"));

		return $pacientes_servicios;

	}
	
}

?>