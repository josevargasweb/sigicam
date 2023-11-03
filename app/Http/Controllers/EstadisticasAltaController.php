<?php
namespace App\Http\Controllers;

use \App\Models\Establecimiento;
use \App\Models\AreaFuncional;
use \App\Models\Caso;
use \App\Models\THistorialOcupaciones;
use \App\Models\UnidadEnEstablecimiento;
use \App\Models\ListaTransito;
use \App\Models\ListaEspera;
use \App\Models\Paciente;
use App\Models\Consultas;
use App\Models\HistorialDiagnostico;
use App\Helpers\EstadosPacientesHelper;

use View;
use DB;
use Session;
use PDF;
use Auth;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Excel;

class EstadisticasAltaController extends Controller{


	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		$estab = Session::get("idEstablecimiento");
		return View::make("Estadisticas/ReporteAlta", ["establecimiento" => $establecimiento, "fecha" => $fecha, "estab"=>$estab]);
	}
	
	public function reporteIngresos($fecha_inicio,$fecha,$est=null){	

		$casosIngresos = Caso::select("casos.id")
			->join("t_historial_ocupaciones as t", "t.caso","casos.id")
			->whereNotNull("t.fecha_ingreso_real")
			->whereRaw("t.fecha_ingreso_real >= '$fecha_inicio' AND t.fecha_ingreso_real <= '$fecha'")
			->where("establecimiento","=",$est)
			->groupBy("casos.id")
			->get();
			
		//buscar ultima ubicacion del caso 
		$ingresos = [];
		foreach($casosIngresos as $key => $casos){

			$primeraOcupacion = THistorialOcupaciones::where("caso",$casos->id)->whereNotNull("fecha_ingreso_real")->orderby("id","asc")->first();

			if($primeraOcupacion){
				$Ubicacion = UnidadEnEstablecimiento::select("unidades_en_establecimientos.alias", "unidades_en_establecimientos.id","tu.descripcion")
				->join("salas","salas.establecimiento","=","unidades_en_establecimientos.id")
				->join("camas","camas.sala","=","salas.id")
				->join("t_historial_ocupaciones as t","t.cama","=","camas.id")
				->join("tipos_unidad as tu","tu.id","=","unidades_en_establecimientos.tipo_unidad")
				->where("t.id",$primeraOcupacion->id)
				->where("unidades_en_establecimientos.visible", true)
				->first();
				
				if(isset($ingresos[$Ubicacion->id])){
					$ingresos[$Ubicacion->id]["cantidad"] += 1;
				}else{
					$ingresos[$Ubicacion->id]["cantidad"] = 1;
					$ingresos[$Ubicacion->id]["alias"] = $Ubicacion->alias;
					$ingresos[$Ubicacion->id]["descripcion"] = $Ubicacion->descripcion;
				}
								
			}
		}

		$todas_unidades = DB::table("unidades_en_establecimientos as u")
							->leftjoin("tipos_unidad as cu","cu.id","=","u.tipo_unidad")
							->select("u.alias","u.id","cu.descripcion")
							->where("u.visible", true)
							->where("u.establecimiento","=",$est)
							->orderBy("u.alias","asc")
							->get();
		//aqui van los id de las unidades que si o si quieres añadir con su descripcion
		$listaDeseados = [];
		//Falta escribir el nombre del area, si es adulto, neo o ped
		$servicios_modificados = [];
        $cambios = [];
        //Se busca si existen mismos nombres
        foreach($todas_unidades as $key => $respo){
            if(!in_array($respo->id, $cambios)){
                foreach($ingresos as $key2 => $respo2){
                    if($respo->alias == $respo2["alias"] && !in_array($key2, $cambios) && $respo->id != $key2){    
                        $servicios_modificados[$key2] = [
							$respo2["alias"]." ".$respo2["descripcion"],
							$respo2["cantidad"]
						];
                        array_push($cambios,$key2);
                        array_push($cambios,$respo->id);
                    }elseif(!in_array($key2, $cambios)){
                        $servicios_modificados[$key2] = (in_array($key2, $listaDeseados))?[$respo2["alias"]." ".$respo2->descripcion,$respo2["cantidad"]]:[$respo2["alias"],$respo2["cantidad"]];
                    }
				}
            }
		}
		return json_encode(array("especialidades"=>$servicios_modificados, "fechainicio"=>$fecha));
	}

	public function reporte($fecha_inicio,$fecha,$est=null){
		//corregir. Manda valores incorrectos

		$casosEgresos = Caso::select("casos.id")
			->whereRaw("fecha_termino >= '$fecha_inicio' AND fecha_termino <= '$fecha'")
			->where("establecimiento","=",$est)
			->groupBy("casos.id")
			->get();

		//buscar ultima ubicacion del caso
		$egresos = [];
		foreach($casosEgresos as $casos){
			$ultimaOcupacion = THistorialOcupaciones::where("caso",$casos->id)
			->whereRaw("(motivo <> 'traslado interno' or motivo is null)")
			->whereNotNull("fecha_ingreso_real")
			->whereNotNull("fecha_liberacion")
			->where("motivo","<>","corrección cama")
			->orderby("id","desc")->first();

			if($ultimaOcupacion){
				$Ubicacion = UnidadEnEstablecimiento::select("unidades_en_establecimientos.alias", "unidades_en_establecimientos.id","tu.descripcion")
				->join("salas","salas.establecimiento","=","unidades_en_establecimientos.id")
				->join("camas","camas.sala","=","salas.id")
				->join("t_historial_ocupaciones as t","t.cama","=","camas.id")
				->join("tipos_unidad as tu","tu.id","=","unidades_en_establecimientos.tipo_unidad")
				->where("t.id",$ultimaOcupacion->id)
				->where("unidades_en_establecimientos.visible", true)
				->first();

				if(isset($egresos[$Ubicacion->id])){
					$egresos[$Ubicacion->id]["cantidad"] += 1;
				}else{
					$egresos[$Ubicacion->id]["cantidad"] = 1;
					$egresos[$Ubicacion->id]["alias"] = $Ubicacion->alias;
					$egresos[$Ubicacion->id]["descripcion"] = $Ubicacion->descripcion;
				}

			}

		}
		
		$todas_unidades = DB::table("unidades_en_establecimientos as u")
							->leftjoin("tipos_unidad as cu","cu.id","=","u.tipo_unidad")
							->select("u.alias","u.id","cu.descripcion")
							->where("u.visible", true)
							->where("u.establecimiento","=",$est)
							->orderBy("u.alias","asc")
							->get();
		//aqui van los id de las unidades que si o si quieres añadir con su descripcion
		$listaDeseados = [];
		//Falta escribir el nombre del area, si es adulto, neo o ped
		$servicios_modificados = [];
        $cambios = [];
        //Se busca si existen mismos nombres
        foreach($todas_unidades as $respo){
            if(!in_array($respo->id, $cambios)){
                foreach($egresos as $key2 => $respo2){
                    if($respo->alias == $respo2["alias"] && !in_array($key2, $cambios) && $respo->id != $key2){    
                        $servicios_modificados[$key2] = [
							$respo2["alias"]." ".$respo2["descripcion"],
							$respo2["cantidad"]
						];
                        array_push($cambios,$key2);
                        array_push($cambios,$respo->id);
                    }elseif(!in_array($key2, $cambios)){
                        $servicios_modificados[$key2] = (in_array($key2, $listaDeseados))?[$respo2["alias"]." ".$respo2->descripcion,$respo2["cantidad"]]:[$respo2["alias"],$respo2["cantidad"]];
                    }
				}
            }
		}




		return json_encode(array("especialidades"=>$servicios_modificados, "fechainicio"=>$fecha));
	}

	public function reporteIngresosAdminSS($fecha_inicio,$fecha){

		$especialidades = DB::table("establecimientos")
		->join("unidades_en_establecimientos AS unidades","unidades.establecimiento", "=", "establecimientos.id")
		->join("salas AS s", "s.establecimiento", "=", "unidades.id")
		->join("camas AS cm", "cm.sala", "=", "s.id")
		->join("historial_ocupaciones AS ho","ho.cama","=","cm.id")
		->select("establecimientos.nombre AS alias",DB::raw("count(*)"))
		->whereRaw("s.id IS NOT NULL")
		->whereRaw("cm.id IS NOT NULL")
		->whereRaw("fecha_liberacion >= '".$fecha_inicio."' AND fecha_liberacion <= '".$fecha."'")
		->groupBy("establecimientos.id")
		->get();

		return json_encode(array("especialidades"=>$especialidades));
	}


	public function reporteAdminSS($fecha_inicio,$fecha){

		$especialidades = DB::table("establecimientos")
		->join("unidades_en_establecimientos AS unidades","unidades.establecimiento", "=", "establecimientos.id")
		->join("salas AS s", "s.establecimiento", "=", "unidades.id")
		->join("camas AS cm", "cm.sala", "=", "s.id")
		->join("historial_ocupaciones AS ho","ho.cama","=","cm.id")
		//->select("alias as categoria")->distinct()
		->select("establecimientos.nombre AS alias",DB::raw("count(*)"))
		//->whereRaw("unidades.establecimiento = 8" )
		->whereRaw("s.id IS NOT NULL")
		->whereRaw("cm.id IS NOT NULL")
		//->whereRaw("(motivo ='alta' OR motivo = 'fallecimiento' OR motivo='otro' OR motivo='traslado extra sistema' OR motivo = 'hospitalización domiciliaria' )")
		->whereNotNull("ho.motivo")
		->whereRaw("fecha_liberacion >= '".$fecha_inicio."' AND fecha_liberacion <= '".$fecha."'")
		->groupBy("establecimientos.id")
		->get();
		// OR motivo='traslado externo' se sacó esto porque ya no está en el enum


			return json_encode(array("especialidades"=>$especialidades));
	}


	function compare_lastname($a, $b)
	{
	  return strnatcmp($a['id'], $b['id']);
	}

	public function pdfInformeIngresos($fecha_inicio,$fecha, $estab = null){

		//Caoss ingresados se toman como pacientes hospitalizados dentro de esas fechas o con fecha_ingreso_real
		$casosIngresos = Caso::select("casos.id")
				->join("t_historial_ocupaciones as t", "t.caso","casos.id")
				->whereNotNull("t.fecha_ingreso_real")
				->whereRaw("t.fecha_ingreso_real >= '$fecha_inicio' AND t.fecha_ingreso_real <= '$fecha 23:59:59'")
				->where("establecimiento","=",$estab)
				->groupBy("casos.id")
				->get();

		$ingresos = [];

		foreach($casosIngresos as $key => $casos){
			//se debe considerar la primera ubicacion del paciente
			$primeraOcupacion = THistorialOcupaciones::where("caso",$casos->id)->whereNotNull("fecha_ingreso_real")->orderby("id","asc")->first();

			if($primeraOcupacion){
				$casosIngresados = UnidadEnEstablecimiento::select("t.caso as caso","unidades_en_establecimientos.id_area_funcional","area_funcional.nombre as nombre_area","t.fecha_ingreso_real","c.fecha_termino","c.motivo_termino","c.id", "p.nombre","p.apellido_paterno","p.apellido_materno", "c.dau","c.ficha_clinica")
				->join("area_funcional","area_funcional.id_area_funcional","=","unidades_en_establecimientos.id_area_funcional")
				->join("salas","salas.establecimiento","=","unidades_en_establecimientos.id")
				->join("camas","camas.sala","=","salas.id")
				->join("t_historial_ocupaciones as t","t.cama","=","camas.id")
				->join("casos as c","c.id","=","t.caso")
				->join("pacientes as p","p.id","=","c.paciente")
				//->where("t.cama","=",$ultimaOcupacion->cama)
				//->where("c.id","=",$casos->id)
				->where("t.id",$primeraOcupacion->id)
				->where("unidades_en_establecimientos.visible", true)
				//->orderBy("area_funcional.nombre", "asc")
				->groupBy("t.caso","unidades_en_establecimientos.id_area_funcional","area_funcional.nombre","t.fecha_ingreso_real","c.fecha_termino","c.motivo_termino","c.id", "p.nombre","p.apellido_paterno","p.apellido_materno", "c.dau","c.ficha_clinica")
				->first();

				//este debe rescatar la fecha_egreso del ultimo historial para calcular su diferencia de tiempo que lleva el paciente dentro
				$ultimaOcupacion = Caso::where("id",$casos->id)->first();
				$casosIngresados->fecha_termino = $ultimaOcupacion->fecha_termino;
				$ingresos[] = $casosIngresados;
			}
		}
		
		//mostrar areas fucionales disponibles
		$area_func = UnidadEnEstablecimiento::select("a.nombre","a.id_area_funcional")
					->join("area_funcional as a","a.id_area_funcional","unidades_en_establecimientos.id_area_funcional")
					->where("unidades_en_establecimientos.visible",true)
					->where("unidades_en_establecimientos.establecimiento",Auth::user()->establecimiento)
					->groupBy("a.nombre","a.id_area_funcional")
					->orderBy("a.nombre", "asc")
					->get();


		$datos = [];

		foreach ($area_func as $area) {
			$casos2=array();
			$diff = array();
			$estadoAlta = array();
			$carbonEgreso ="";
			$carbonIngreso="";

			if(isset($ingresos)){
				foreach($ingresos as $ingreso){
					if($area->id_area_funcional == $ingreso->id_area_funcional){
						$ingreso["nombre"] = Str::upper($ingreso["nombre"]);
						$ingreso["apellido_paterno"] = Str::upper($ingreso["apellido_paterno"]);
						$ingreso["apellido_materno"] = Str::upper($ingreso["apellido_materno"]);
						$casos2[] = $ingreso;
						$carbonIngreso = Carbon::parse($ingreso->fecha_ingreso_real);
						$carbonEgreso = ($ingreso->fecha_termino)?Carbon::parse($ingreso->fecha_termino):Carbon::now();
						$diff[] = $carbonEgreso->diffInDays($carbonIngreso);

						if($ingreso->motivo_termino == "fallecimiento"){
							$estadoAlta[] = "Fallecido";
						}
						else{
							$estadoAlta[] = "Vivo";
						}
					}

				}
			}
			$datos[] = Array("area"=>$area->nombre,"casos"=>$casos2, "nDias"=>$diff,"estadoAlta"=>$estadoAlta,"fecha_inicio"=>$fecha_inicio, "fecha"=>$fecha);
		}

		$html = PDF::loadView("Estadisticas/IngresosEgresos/pdfInformeIngresos", [
			"datos" => $datos
		]);
		return $html->setPaper('legal', 'portrait')->download('InformeDeIngresos'.$fecha.'.pdf');

	}
	/* select *
	from casos
	where motivo_termino is not null
	and fecha_termino>= '2020-08-01 00:00:00' AND fecha_termino<= '2020-08-30 23:59:59'
	and establecimiento= 8
	group by  */

	public function excelInformeIngresos($fecha_inicio,$fecha, $estab = null){

		$motivos = EstadosPacientesHelper::motivoPacienteEgresadoYHospitalizado();

		//Caoss ingresados se toman como pacientes hospitalizados dentro de esas fechas o con fecha_ingreso_real
		$casosIngresos = Caso::select("casos.id")
				->join("t_historial_ocupaciones as t", "t.caso","casos.id")
				->whereNotNull("t.fecha_ingreso_real")
				->whereRaw("t.fecha_ingreso_real >= '$fecha_inicio' AND t.fecha_ingreso_real <= '$fecha 23:59:59'")
				->where("establecimiento","=",$estab)
				->where(function($q){
					$q->whereIn("t.motivo", [
							'alta',
							'fallecimiento',
							'derivación',
							'otro',
							'traslado extra sistema',
							'hospitalización domiciliaria',
							'Liberación de responsabilidad',
							'derivacion otra institucion'
						])
						->orWhereNull("t.motivo");
				})
				//->whereRaw('"casos"."motivo_termino" in (?)', $motivos)
				->groupBy("casos.id")
				->get();

		$ingresos = [];

		
		foreach($casosIngresos as $key => $caso){
			//Buscar fecha de hsopitalizacion
			$primeraOcupacion = THistorialOcupaciones::where("caso",$caso->id)
					->whereNotNull("fecha_ingreso_real")
					->where(function($q){
						$q->whereNotIn("motivo", [
								'corrección cama'
							])
							->orWhereNull("motivo");
					})->orderby("id","asc")->first();

			//Buscar fecha de asignacion
			$fecha_asignacion = ListaTransito::where("caso",$caso->id)->first();

			//Buscar fecha de solicitud
			$caso_info = Caso::where("id",$caso->id)->first();

			//Datos del paciente
			$paciente = Paciente::where("id",$caso_info->paciente)->first();

			//Diagnostico de ingreso
			$diagnostico = HistorialDiagnostico::where("caso", $caso->id)
					->where(function($q){
						$q->where("id_tipo_diagnostico",4)
							->orWhereNull("id_tipo_diagnostico");
					})->first();

			$run ="";
			if (isset($paciente->rut)) {
				$dv = ($paciente->dv == 10)?"K":$paciente->dv;
				$run = $paciente->rut."-". $dv;
			}
			
			$ingresos [] = [
				"nombre" => Str::upper($paciente->nombre)." ".Str::upper($paciente->apellido_paterno)." ".Str::upper($paciente->apellido_materno),
				"run" => $run,
				"fecha_hospitalizacion" => Carbon::parse($primeraOcupacion->fecha_ingreso_real)->format("d-m-Y H:i:s"),
				"fecha_asignacion" => Carbon::parse($fecha_asignacion->fecha)->format("d-m-Y H:i:s"),
				"fecha_solicitud" => Carbon::parse($caso_info->fecha_ingreso2)->format("d-m-Y H:i:s"),
				"diagnostico" => $diagnostico->diagnostico,
				"diagnostico_comentario" => $diagnostico->comentario
			];
		}

		$inicio = Carbon::parse($fecha_inicio)->format("d-m-Y");
		$fin = Carbon::parse($fecha)->format("d-m-Y");
		$inicio_titulo = Carbon::parse($fecha_inicio)->format("dmY");
		$fin_titulo = Carbon::parse($fecha)->format("dmY");

		Excel::create('pacientesIngresados_'.$inicio_titulo."_".$fin_titulo, function($excel) use ($ingresos, $inicio, $fin) {
			$excel->sheet('pacientesIngresados', function($sheet) use ($ingresos, $inicio, $fin) {

				$sheet->mergeCells('A1:H1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});

				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$hoy = Carbon::now();

				$sheet->loadView('Estadisticas.IngresosEgresos.excelInformeIngresos', [
					"datos" => $ingresos,
					"establecimiento" => $nombreEstablecimiento,
					"inicio" => $inicio,
					"fin" => $fin
					]
				);
			});
		})->download('xls');

	}
	

	public function informeEgreso($fecha_inicio,$fecha, $reporte, $estab = null){

		$casosEgresadosPadre = Caso::select("casos.id")
		//->whereIn("motivo_termino",array("alta","fallecimiento","derivación","otro","traslado extra sistema","hospitalización domiciliaria","alta sin liberar cama","Fuga","Liberación de responsabilidad"))
		//->whereNotNull("motivo_termino")
		->whereRaw("fecha_termino >= '$fecha_inicio' AND fecha_termino <= '$fecha 23:59:59'")
		->where("establecimiento","=",$estab)
		->groupBy("casos.id")
		->get();
		$egresos = [];
		foreach($casosEgresadosPadre as $casos){
			$casosEgresados ="";
			$ultimaOcupacion = THistorialOcupaciones::where("caso",$casos->id)
			->whereRaw("(motivo <> 'traslado interno' or motivo is null)")
			->whereNotNull("fecha_ingreso_real")
			->whereNotNull("fecha_liberacion")
			->where("motivo","<>","corrección cama")
			->orderby("id","desc")->first();
			
			if($ultimaOcupacion){
				$casosEgresados = UnidadEnEstablecimiento::select("t.caso as caso","unidades_en_establecimientos.id_area_funcional","area_funcional.nombre as nombre_area","t.fecha_ingreso_real","c.fecha_termino","c.motivo_termino","c.id", "p.nombre","p.apellido_paterno","p.apellido_materno","p.rut","p.dv", "c.prevision", "d.diagnostico", "d.id_cie_10", "d.comentario", "c.dau","c.ficha_clinica")
				// $casosEgresados = UnidadEnEstablecimiento::select(DB::raw('t.caso as caso, unidades_en_establecimientos.id_area_funcional,area_funcional.nombre as nombre_area,t.fecha_ingreso_real,c.fecha_termino,c.motivo_termino,c.id, upper(p.nombre),upper(p.apellido_paterno),upper(p.apellido_materno),c.dau,c.ficha_clinica'))
				->join("area_funcional","area_funcional.id_area_funcional","=","unidades_en_establecimientos.id_area_funcional")
				->join("salas","salas.establecimiento","=","unidades_en_establecimientos.id")
				->join("camas","camas.sala","=","salas.id")
				->join("t_historial_ocupaciones as t","t.cama","=","camas.id")
				->join("casos as c","c.id","=","t.caso")
				->join("pacientes as p","p.id","=","c.paciente")
				->join("diagnosticos as d","d.caso","=", "c.id")
				//->where("t.cama","=",$ultimaOcupacion->cama)
				//->where("c.id","=",$casos->id)
				->where("t.id",$ultimaOcupacion->id)
				->where("unidades_en_establecimientos.visible", true)
				->orderBy("area_funcional.nombre", "asc")
				// ->groupBy("t.caso","unidades_en_establecimientos.id_area_funcional","area_funcional.nombre","t.fecha_ingreso_real","c.fecha_termino","c.motivo_termino","c.id", "p.nombre","p.apellido_paterno","p.apellido_materno", "c.dau","c.ficha_clinica")
				->first();

			}else{
				//revisar si tenia lista de transito
				$esperaDeHospitalizacion = ListaTransito::where("caso",$casos->id)->first();
				$tmp = DB::table("casos as c")
					->join("pacientes as p", "p.id","c.paciente")
					->join("diagnosticos as d","d.caso","=", "c.id")
					->where("c.id",$casos->id)
					->first();

				$rut = ($tmp->rut) ? $tmp->rut : '';
				$dv = ($tmp->dv) ? $tmp->dv : '';

				if ($esperaDeHospitalizacion) {
					$casosEgresados = [
						"fecha_ingreso_real" => $tmp->fecha_ingreso,
						"fecha_termino" => $tmp->fecha_termino,
						"dau" => $tmp->dau,
						"ficha_clinica" => $tmp->ficha_clinica,
						"nombre" => $tmp->nombre,
						"apellido_paterno" => $tmp->apellido_paterno,
						"apellido_materno" => $tmp->apellido_materno,
						"rut" => $rut,
						"dv" => $dv,
						"prevision" => $tmp->prevision,
						"id_cie_10" => $tmp->id_cie_10,
						"diagnostico" => $tmp->diagnostico,
						"comentario" => $tmp->comentario,
						"motivo_termino" => $tmp->motivo_termino,
						"id_area_funcional" => "ListaHospitalizacion",
						"motivo_termino" => $tmp->motivo_termino
					];
				}else{
					//revisar si tenia lista de espera
					$esperaDeCama = ListaEspera::where("caso",$casos->id)->first();
					if ($esperaDeCama) {
						$casosEgresados = [
							"fecha_ingreso_real" => $tmp->fecha_ingreso,
							"fecha_termino" => $tmp->fecha_termino,
							"dau" => $tmp->dau,
							"ficha_clinica" => $tmp->ficha_clinica,
							"nombre" => $tmp->nombre,
							"apellido_paterno" => $tmp->apellido_paterno,
							"apellido_materno" => $tmp->apellido_materno,
							"rut" => $rut,
							"dv" => $dv,
							"prevision" => $tmp->prevision,
							"id_cie_10" => $tmp->id_cie_10,
							"diagnostico" => $tmp->diagnostico,
							"comentario" => $tmp->comentario,
							"motivo_termino" => $tmp->motivo_termino,
							"id_area_funcional" => "ListaEspera",
							"motivo_termino" => $tmp->motivo_termino
						];
					}
				}
			}
			if($casosEgresados != ""){
				$egresos[] = $casosEgresados;
			}

		}

		//mostrar areas fucionales disponibles
		$area_func = UnidadEnEstablecimiento::select("a.nombre","a.id_area_funcional")
					->join("area_funcional as a","a.id_area_funcional","unidades_en_establecimientos.id_area_funcional")
					->where("unidades_en_establecimientos.visible",true)
					->where("unidades_en_establecimientos.establecimiento",Auth::user()->establecimiento)
					->groupBy("a.nombre","a.id_area_funcional")
					->orderBy("a.nombre", "asc")
					->get();
		$area_func->push(["nombre" => "Lista espera de cama","id_area_funcional" => "ListaEspera"]);
		$area_func->push(["nombre" => "Lista espera de hospitalización","id_area_funcional" => "ListaHospitalizacion"]);
		
		$datos = [];

		foreach ($area_func as $area) {
			$casos2=[];
			$diff = [];
			$estadoAlta = [];
			$carbonEgreso ="";
			$carbonIngreso="";

			if(isset($egresos)){
				foreach($egresos as $egreso){
					if($area["id_area_funcional"] == $egreso["id_area_funcional"]){
						$egreso["nombre"] = Str::upper($egreso["nombre"]);
						$egreso["apellido_paterno"] = Str::upper($egreso["apellido_paterno"]);
						$egreso["apellido_materno"] = Str::upper($egreso["apellido_materno"]);
						if($egreso["motivo_termino"] == "fallecimiento"){
							$estadoAlta[] = "Fallecido";
						}
						else{
							$estadoAlta[] = "Vivo";
						}
						$egreso["motivo_termino"] = Consultas::traduccionDestinoEgreso($egreso["motivo_termino"]);
						$casos2[] = $egreso;
						$carbonIngreso = Carbon::parse($egreso["fecha_ingreso_real"]);
						$carbonEgreso = Carbon::parse($egreso["fecha_termino"]);
						$diff[] = $carbonEgreso->diffInDays($carbonIngreso);
					}
				}
			}
			$datos[] = Array("area"=>$area["nombre"],"casos"=>$casos2, "nDias"=>$diff,"estadoAlta"=>$estadoAlta,"fecha_inicio"=>$fecha_inicio, "fecha"=>$fecha);
		}

		if ($reporte == 'pdf') {
			$html = PDF::loadView("Estadisticas/IngresosEgresos/pdfInformeEgresos", [
				"datos" => $datos
			]);
			return $html->setPaper('legal', 'landscape')->download('InformeDeEgreso'.$fecha.'.pdf');
		}else if($reporte == 'excel'){
			Excel::create('PacientesEgresados', function($excel) use ($datos) {
				$excel->sheet('PacientesEgresados', function($sheet) use ($datos) {
	
					$sheet->mergeCells('A1:J1');
					$sheet->setAutoSize(true);
					$sheet->setHeight(1,50);
					$sheet->row(1, function($row) {
	
						$row->setBackground('#1E9966');
						$row->setFontColor("#FFFFFF");
						$row->setAlignment("center");
	
					});
					$idEstablecimiento = Auth::user()->establecimiento;
					$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
					$hoy = Carbon::now();
	
					$sheet->loadView('Estadisticas.ExcelInformeEgresos', [
						"datos" => $datos,
						"establecimiento" => $nombreEstablecimiento,
						"hoy" => $hoy
						]
					);
				});
			})->download('xls');
		}
	}

	public function pdfInformeFoliados($fecha_inicio, $fecha, $estab = null, $folio){


		$casosEgresadosPadre = Caso::select("id")
		//->whereNotNull("motivo_termino")
		//whereIn("motivo_termino",array("alta","fallecimiento","derivación","otro","traslado extra sistema","hospitalización domiciliaria","alta sin liberar cama","Fuga","Liberación de responsabilidad"))
		->whereRaw("fecha_termino >= '$fecha_inicio' AND fecha_termino <= '$fecha 23:59:59'")
		->where("establecimiento","=",$estab)
		->groupBy("id")
		->get();

		$totalPacientes = count($casosEgresadosPadre);
		$sumaDias = 0;
		$datos = array();
		foreach($casosEgresadosPadre as $casos){
			$casosEgresados ="";
			$ultimaOcupacion = THistorialOcupaciones::where("caso",$casos->id)
			->whereRaw("(motivo <> 'traslado interno' or motivo is null)")
			->whereNotNull("fecha_ingreso_real")
			->whereNotNull("fecha_liberacion")
			->where("motivo","<>","corrección cama")
			->orderby("id","desc")->first();
			
			if($ultimaOcupacion){
				$casosEgresados = UnidadEnEstablecimiento::
				//select("t_historial_ocupaciones.caso as caso","unidades_en_establecimientos.id_area_funcional","area_funcional.nombre")
				join("area_funcional","area_funcional.id_area_funcional","=","unidades_en_establecimientos.id_area_funcional")
				->join("salas","salas.establecimiento","=","unidades_en_establecimientos.id")
				->join("camas","camas.sala","=","salas.id")
				->join("t_historial_ocupaciones","t_historial_ocupaciones.cama","=","camas.id")
				->join("casos","casos.id","=","t_historial_ocupaciones.caso")
				->join("pacientes","pacientes.id","=","casos.paciente")
				->where("t_historial_ocupaciones.cama","=",$ultimaOcupacion->cama)
				->where("casos.id","=",$casos->id)
				->where("casos.establecimiento","=",$estab)
				->first();
			}else{
				//revisar si tenia lista de transito
				$esperaDeHospitalizacion = ListaTransito::where("caso",$casos->id)->first();
				$tmp = DB::table("casos as c")
					->join("pacientes as p", "p.id","c.paciente")
					->where("c.id",$casos->id)
					->first();

				if ($esperaDeHospitalizacion) {
					$casosEgresados = [
						"fecha_ingreso_real" => $tmp->fecha_ingreso,
						"fecha_termino" => $tmp->fecha_termino,
						"dau" => $tmp->dau,
						"ficha_clinica" => $tmp->ficha_clinica,
						"nombre" => $tmp->nombre,
						"apellido_paterno" => $tmp->apellido_paterno,
						"apellido_materno" => $tmp->apellido_materno,
						"alias" => "Espera de hospitalizacion",
						"motivo_termino" => $tmp->motivo_termino
					];
				}else{
					//revisar si tenia lista de espera
					$esperaDeCama = ListaEspera::where("caso",$casos->id)->first();
					if ($esperaDeCama) {
						$casosEgresados = [
							"fecha_ingreso_real" => $tmp->fecha_ingreso,
							"fecha_termino" => $tmp->fecha_termino,
							"dau" => $tmp->dau,
							"ficha_clinica" => $tmp->ficha_clinica,
							"nombre" => $tmp->nombre,
							"apellido_paterno" => $tmp->apellido_paterno,
							"apellido_materno" => $tmp->apellido_materno,
							"alias" => "Espera de cama",
							"motivo_termino" => $tmp->motivo_termino
						];
					}
				}

			}

			if($casosEgresados != ""){
				$carbonIngreso = Carbon::parse($casosEgresados["fecha_ingreso_real"]);
				$carbonEgreso = Carbon::parse($casosEgresados["fecha_termino"]);
				$diff = $carbonEgreso->diffInDays($carbonIngreso);
				//si estan en lista de espera o hospitalizacion, este no se cuenta como dias de total
				if($casosEgresados["alias"] != "Espera de hospitalizacion" && $casosEgresados["alias"] != "Espera de cama"){
					$sumaDias = $sumaDias + $diff;
				}

				if($casosEgresados["motivo_termino"] == "fallecimiento"){
					$estadoAlta = "Fallecido";
				}
				else{
					$estadoAlta = "Vivo";
				}
				$casosEgresados["nombre"] = Str::upper($casosEgresados["nombre"]);
				$casosEgresados["apellido_paterno"] = Str::upper($casosEgresados["apellido_paterno"]);
				$casosEgresados["apellido_materno"] = Str::upper($casosEgresados["apellido_materno"]);
				$datos[] = array("casos"=>$casosEgresados, "folio"=>$folio, "diff"=>$diff,"estadoAlta"=>$estadoAlta);
				$folio++;
			}
			
		}



		$pdf = PDF::loadView('Estadisticas.IngresosEgresos.pdfInformeFoliados', array("datos"=>$datos,"fecha_inicio"=>$fecha_inicio, "fecha"=>$fecha, "totalPacientes"=>$totalPacientes,"sumaDias"=>$sumaDias));
		return $pdf->download('Informe de egresos foliado.pdf');
	}
}
