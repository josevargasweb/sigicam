<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use TipoUsuario;
use Session;
use App\Models\Establecimiento;
use App\Models\ListaEspera;
use App\Models\ListaTransito;
use App\Models\Riesgo;
use App\Models\Caso;
use App\Models\Examen;
use Consultas;
use DB;
use App\Models\UnidadEnEstablecimiento;
use App\Models\Derivacion;
use App\Models\HistorialBloqueo;
use View;
use URL;
use Excel;
use App\Models\Paciente;
use App\Models\HistorialDiagnostico;
use Carbon\Carbon;
use Log;
use App\Models\THistorialOcupaciones;
use App\Models\AreaFuncional;
use App\Models\Usuario;
use App\Models\Dotacion;

class IndexController extends Controller {

	private $totalLibres=0;
	private $totalReservadas=0;
	private $totalOcupadas=0;
	private $totalBloqueadas=0;
	private $totalReconvertidas=0;
	private $tipoUser=0;

	public static function menuOpciones(){

        /* revisar areas funcionales  y servicios visibles*/
        $establecimiento = Auth::user()->establecimiento;
        $areas_funcionales = DB::select("select
		u.id as id_unidad, u.alias as nombre_unidad, u.url as url_unidad,
		a.id_area_funcional as id_area, a.nombre as nombre_area,
		t.nombre as tipo_unidad, t.descripcion descripcion_unidad, a.orden AS orden_area
		from area_funcional a
		join unidades_en_establecimientos u on a.id_area_funcional = u.id_area_funcional
		join salas_con_camas s on s.establecimiento = u.id
		left join tipos_unidad t on t.id = u.tipo_unidad
		where u.establecimiento= $establecimiento 
		and u.visible is true 
		and s.visible is true
		group by u.id, u.alias, u.url, a.id_area_funcional, a.nombre, t.nombre, t.descripcion
		order by a.orden asc
        ");

		/* Si necesitan ingresar categoria de tipo_unidad a algunos deberan agregar a la lista  de ids de unidad y ser añadidos manualmente */
		$listaDeseados = [202,189];

        $response = [];
        foreach ($areas_funcionales as $key => $area) {
			if(Consultas::restriccionPersonal($area->id_unidad) != true){
				if(!array_key_exists($area->orden_area, $response)){

					$response[$area->orden_area] []= [$area->nombre_area];
					$response[$area->orden_area] []= array(
						/* "nombre_unidad" => */ (in_array($area->id_unidad, $listaDeseados))?$area->nombre_unidad." <b>".$area->descripcion_unidad."</b>":$area->nombre_unidad,
						/* "id_unidad" => */ $area->id_unidad,
						/* "url_unidad" => */ $area->url_unidad,
						$area->descripcion_unidad
					);
				}else{
					$response[$area->orden_area] [] = [
						/* "nombre_unidad" => */ (in_array($area->id_unidad, $listaDeseados))?$area->nombre_unidad." <b>".$area->descripcion_unidad."</b>":$area->nombre_unidad,
						/* "id_unidad" => */ $area->id_unidad,
						/* "url_unidad" => */ $area->url_unidad,
						$area->descripcion_unidad
					];
				}
			}
		}
		$cambios = [];
		/* comprobar si tienen mismos nombre los servicios */
		foreach($response as $key => $respo){
			foreach($respo as $key1 => $r){
				if($key1 != 0){
					foreach($response as $key2 => $respo2){
						foreach($respo2 as $key3 => $r2){
							if ($key3 != 0) {
								if($r[0] == $r2[0] && $r[1] != $r2[1]  && !in_array($r2[1], $cambios)){
									$response[$key][$key1][0] .= " <b>".$r[3]."</b>";
									$response[$key2][$key3][0] .= " <b>".$r2[3]."</b>";
									array_push($cambios,$r[1]);
									array_push($cambios,$r2[1]);
								}
							}
						}
					}
				}
			}
		}

        return response()->json($response);
    }

	public function alertaPacienteEspera(){
		//return "hola";
		$hoy = Carbon::now()->subHours(12);

		$lista_espera = DB::table("lista_espera as l")
						->join("casos as c", "c.id","=","l.caso")
						->where("c.establecimiento", Auth::user()->establecimiento)
						->where("c.fecha_ingreso2","<=",$hoy)
						->whereNull("l.fecha_termino")
						->count();


		$lista_transito = DB::table("lista_transito as l")
						->join("casos as c", "c.id","=","l.caso")
						->where("c.establecimiento", Auth::user()->establecimiento)
						->where("c.fecha_ingreso2","<=",$hoy)
						->whereNull("l.fecha_termino")
						->count();

		$cant_sin_categorizar = 0;
			$riesgo = DB::select("select count(*) from
				(select caso, cama, fecha_liberacion, fecha_ingreso_real from t_historial_ocupaciones_vista_aux where id_establecimiento=8) h
				left join lista_espera l on l.caso=h.caso
				left join ultimos_estados_pacientes u on u.caso=h.caso
				where
				h.caso not in (select caso from lista_transito where fecha_termino is null) and
				h.fecha_ingreso_real is not null and
				h.fecha_liberacion is null and
				h.cama in (select id from camas_vigentes_vista)
				and u.riesgo is null");

			//dd($riesgo[0]->count);
			$cant_sin_categorizar = $riesgo[0]->count;

		if($cant_sin_categorizar == 0){
			$sin_categorizar = "No existen pacientes sin categorizar";
		}else if(Auth::user()->tipo == "gestion_clinica" || Auth::user()->tipo == "enfermeraP" || Auth::user()->tipo == "master" || Auth::user()->tipo == "master_ss" || Auth::user()->tipo == "admin" || Auth::user()->tipo == "supervisora_de_servicio"){
			$sin_categorizar = "Existen " . $cant_sin_categorizar . " pacientes sin categorizar <a href='urgencia/listaCategorizados'> Ver lista</a>";
		}
		else{
			$sin_categorizar = "Existen " . $cant_sin_categorizar . " pacientes sin categorizar";
		}
		return response()->json(array("espera"=>$lista_espera,"transito"=>$lista_transito, "categorizar"=>$sin_categorizar));

	}

	public function index(){

		$this->tipoUser=Auth::user()->tipo;

		if($this->tipoUser != TipoUsuario::ADMINSS && $this->tipoUser != TipoUsuario::MONITOREO_SSVQ && $this->tipoUser != TipoUsuario::ADMINIAAS){

			$resumen = $this->resumenCamasEstablecimiento(Session::get("idEstablecimiento"));
			//return $resumen;
		}
		else {
			$resumen = $this->resumenCamasTotal();
		}




		//$resumen = "";
		// MOSTRAR MENSAJES DE DERIVACION
		$mensajes="";
		$esta=Session::get('idEstablecimiento');
		if($esta!=null){
			$alerta=DB::table( DB::raw(
	             "(select d.id,to_char(d.fecha,'DD-MM-YYYY') as fecha,p.nombre,p.apellido_paterno,p.apellido_materno,p.rut,p.dv,d.establecimiento from derivaciones as d,unidades_en_establecimientos as u,establecimientos as e, casos as c,pacientes as p
					where d.destino=u.id and e.id=$esta and u.establecimiento=e.id and p.id=c.paciente and c.id=d.caso
					and fecha_cierre is null and revisada='no') as ra"
	         ))
			->get();

			foreach ($alerta as $alert)
			{
				$establecimiento=Establecimiento::getNombre($alert->establecimiento);
				$mensajes.="&#x25b6 $alert->fecha $establecimiento, $alert->nombre $alert->apellido_paterno $alert->apellido_materno<br><br>";
			}
		}
		// fin mostrar mensajes de derivacion

		$total=array(
			"totalLibres"			=> $this->totalLibres,
			"totalReservadas"		=> $this->totalReservadas,
			"totalOcupadas"			=> $this->totalOcupadas,
			"totalBloqueadas"		=> $this->totalBloqueadas,
			"totalReconvertidas"	=> $this->totalReconvertidas,

		);



		try{
			return response()->json(["resumen"=>$resumen, "total"=>$total,"usuario"=>Session::get('usuario')->tipo]);
		}
		catch(Exception $e){
			return response()->json(["asd"=>$e]);
		}

	}



public function Alerta(){
		$total=Derivacion::getDerivacionMayorUnaHora();
		$mensajesOcupadas=array();
		$mensajesPromedio=array();
		$mensajes="";
		$this->tipoUser=Auth::user()->tipo;
		if($this->tipoUser != TipoUsuario::ADMINSS && $this->tipoUser != TipoUsuario::MONITOREO_SSVQ && $this->tipoUser != TipoUsuario::ADMINIAAS){
			foreach(Session::get("unidades") as $unidad){
				$u = UnidadEnEstablecimiento::find($unidad->id);
				$porcentaje= $u->porcentajeOcupacion();
				if($porcentaje >= 90) $mensajesOcupadas[]="En la unidad ".$unidad->nombre." se encuentran ocupadas más del 90% de las camas.";

				$ocupaciones = $u->ocupacionesMayoresAlPromedio();
				if( count($ocupaciones) > 0) $mensajesPromedio[] = "En la unidad {$unidad->nombre} hay camas con estadía mayor al promedio.";
			}
			if($total != 0) $mensajes.="Hay solicitudes de traslado externo con más de una hora.<br>";
			if(count($mensajesOcupadas) != 0) $mensajes.=implode($mensajesOcupadas, "<br>");
			if(count($mensajesOcupadas) != 0) $mensajes.="<br>".implode($mensajesPromedio, "<br>");
			$resumen = $this->resumenCamasEstablecimiento(Session::get("idEstablecimiento"));
		}

		else {
			$resumen = $this->resumenCamasTotal();
		}


		$total=array(
			"totalLibres"			=> $this->totalLibres,
			"totalOcupadas"			=> $this->totalOcupadas,
			"totalBloqueadas"		=> $this->totalBloqueadas,
			"totalReconvertidas"	=> $this->totalReconvertidas
		);
		return View::make("Administracion/CosaAzul", ["mensajes" => $mensajes, "resumen" => $resumen, "total" => $total]);
	}

	private function subirNivel($a, $campo, $cont){
		return $a->each(function($i) use ($campo, $cont){
			$i->{$campo} = new \Illuminate\Database\Eloquent\Collection();
			$i->{$cont}->each(function($ii) use (&$i, $campo) {
				$ii->{$campo}->each(function($cama) use($i, $campo){
					$copy = clone $cama;
					$i->{$campo}->add($copy);
					unset($cama);
				});
				unset($ii->{$campo});
			});
			unset($i->{$cont});
		});
	}

	private function aplanar(&$ests, $campo){
		foreach($ests as &$est){
			$est->{$campo} = new \Illuminate\Database\Eloquent\Collection();
			foreach($est->unidades as $unidad){
				foreach($unidad->{$campo} as $cama){
					$est->{$campo}[] = $cama;
				}
			}
		}
	}

	public function resumenCamasTotal(){


		$establecimientos = Establecimiento::whereHas("unidades", function($q){
			$q->where("visible", true)->whereHas("camas", function($qq){
				$qq->vigentes();
			});
		})
        ->with(["unidades" => function($q){
            $q->where("visible", true)
            ->where('id','<>',21)   // where para que no sume las camas de Emergencia Adultos
            ->where('id','<>',25);   // where para que no sume las camas de Pabellon Quirurjico
            //->with("camasLibres")
            //->with("camasBloqueadas")
            //->with("camasReservadas")
            //->with("camasOcupadas")
            //->with("camasReconvertidas");
        }])
        ->orderBy("nombre", "asc")
        ->get();





        //return $establecimientos;
		//$this->aplanar($establecimientos, "camasLibres");
		//$this->aplanar($establecimientos, "camasBloqueadas");
		//$this->aplanar($establecimientos, "camasReservadas");
		//$this->aplanar($establecimientos, "camasOcupadas");
		//$this->aplanar($establecimientos, "camasReconvertidas");
		$res = array();

		//return $establecimientos;
		$x=0;
		foreach($establecimientos as $obj){

				$camaAmarillo =0;
				$camaRoja     =0;
				$camaNegra    =0;
				$camaAzul     =0;
				$camaVerde    =0;

			$res[$obj->nombre]["nombre"] = $obj->nombre;

			$unidadesArray = array();
			foreach ($obj->unidades as $unidad) {

				array_push($unidadesArray, $unidad->alias);


				$consulta = Consultas::ultimoEstadoCamas();
				$consulta = Consultas::addTiempoBloqueo($consulta);
				$consulta = Consultas::addTiempoReserva($consulta);
				$consulta = Consultas::addTiemposOcupaciones($consulta);

				//return $consulta;
				$consulta = $consulta->where("ue.alias","=",$unidad->alias);
				$consulta = $consulta->addSelect("s.visible");
				$consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
				->where("est.id", "=", $obj->id)
				->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
				->whereNotNull("id_sala")
				->where("s.visible", true)
				->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
				->orderBy("s.nombre", "asc");
				$ocupacionesCamaSala = $consulta->get();




				$res[$obj->nombre]["unidadades"] = $unidadesArray;


			$infectado = 0;
			foreach($ocupacionesCamaSala as $ocupacion){

			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$camaAmarillo++;
				}
				elseif($ocupacion->bloqueado !== null){
					$camaNegra++;


				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$imagen = "camaAzul.png";
					$camaAzul++;
				}
				else{
					$camaVerde++;

				}
				//continue;
			}
			else{
				if($ocupacion->ocupado !== null){
					$camaRoja++;
					$infeccion=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$ocupacion->id_caso and i.caso=$ocupacion->id_caso and i.fecha_termino is null) as re"
					 ))->get();
					if(count($infeccion)){
						$infectado = $infectado + count($infeccion);
					}
				}

				elseif($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";

				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "camaNegra.png";
					$camaNegra++;
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$totalAzul++;
				}
				//continue;
			}

			//$camaVerde =0;



		$res[$obj->nombre]["bloqueadas"] = $camaNegra;
		$res[$obj->nombre]["reconvertidas"] = $camaAzul;
		$res[$obj->nombre]["ocupadas"] = $camaRoja;
		$res[$obj->nombre]["libres"] = $camaVerde;
		$res[$obj->nombre]["id"] = $obj->id;
		$res[$obj->nombre]["cantidad_infectados"] = $infectado;

		} // fin ocupacion




			}  //foreach unidad













		}


		//return $ocupacionesCamaSala;
		return $res;

	}






	public function resumenCamasEstablecimiento($idEst){

		$tiene=false;
		$camas=array();
		$nombres = array();



		//return $ocupacionesCamaSala;


		$unidad = UnidadEnEstablecimiento::where("visible", true)
		/*->whereHas("camas", function($q){

			//
			$q->vigentes();
		})
		*/
		->where("establecimiento", $idEst)
		//->with("camasLibres")
		//->with("camasBloqueadas")
		//->with("camasReservadas")
		//->with("camasOcupadas")
		//->with("camasReconvertidas")
		->get();

		//return response()->json($unidad);

		//return $unidad;
		$res = array();
		$lista_camas_no_asignadas = [];

		$camas_no_asignadas = DB::table("lista_transito as l")
		->join("casos as c", "c.id", "=", "l.caso")
		->join("t_historial_ocupaciones as t", "t.caso","=", "l.caso")
		->join("camas as ca", "ca.id", "=", "t.cama")
		->join("salas as s", "s.id", "=","ca.sala")
		->join("unidades_en_establecimientos as un","un.id","=","s.establecimiento")
		->join("area_funcional as a", "a.id_area_funcional", "=","un.id_area_funcional")
        ->join("pacientes as p", "p.id", "=", "c.paciente")
        ->join("usuarios as u", "l.id_usuario_ingresa", "=", "u.id")
        ->whereNull("l.fecha_termino")
		->where("u.establecimiento", $idEst)
		->whereNull("t.fecha_liberacion")
        ->select("p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			"p.rut as rut",
			"p.dv as dv",
			"l.fecha as fecha",
			"c.id as idCaso",
			"l.id_lista_transito as idLista",
			"c.id_unidad",
			"c.ficha_clinica",
			"p.id as id_paciente",
			"t.cama",
			"ca.id_cama as n_cama",
			"s.nombre as n_sala",
			"un.alias as n_unidad",
			"un.tooltip",
			"a.nombre as n_area")->get();

		foreach ($camas_no_asignadas as $key => $cama_b) {
			$lista_camas_no_asignadas [] = $cama_b->cama;
		}

		foreach($unidad as $obj){

			/*
			$res[$obj->url] = [];
			$res[$obj->url]["nombre"] = $obj->alias;
			$res[$obj->url]["libres"] = $obj->camasLibres->count();
			$res[$obj->url]["bloqueadas"] = $obj->camasBloqueadas->count();
			$res[$obj->url]["reservadas"] = $obj->camasReservadas->count();
			$res[$obj->url]["ocupadas"] = $obj->camasOcupadas->count();
			$res[$obj->url]["reconvertidas"] = $obj->camasReconvertidas->count();
			$this->totalLibres += $res[$obj->url]["libres"];
			$this->totalBloqueadas += $res[$obj->url]["bloqueadas"];
			$this->totalReservadas += $res[$obj->url]["reservadas"];
			$this->totalOcupadas += $res[$obj->url]["ocupadas"];
			$this->totalReconvertidas += $res[$obj->url]["reconvertidas"];
			*/
		$res[$obj->url]["nombre"] = $obj->alias;
		$consulta = Consultas::ultimoEstadoCamas();
		$consulta = Consultas::addTiempoBloqueo($consulta);
		$consulta = Consultas::addTiempoReserva($consulta);
		$consulta = Consultas::addTiemposOcupaciones($consulta);
		//return $consulta;
		$consulta = $consulta->where("ue.alias","=",$obj->alias);
		$consulta = $consulta->addSelect("s.visible");
		$consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
		->where("est.id", "=", $idEst)
		->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
		->whereNotNull("id_sala")
		->where("s.visible", true)
		->whereNotIn("cm.id", $lista_camas_no_asignadas)
		->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
		->orderBy("s.nombre", "asc");
		$ocupacionesCamaSala = $consulta->get();

		$camaAmarillo =0;
		$camaRoja     =0;
		$camaNegra    =0;
		$camaAzul     =0;
		$camaVerde    =0;

		//return $ocupacionesCamaSala;

		$infectado = 0;
		foreach($ocupacionesCamaSala as $ocupacion){
			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$camaAmarillo++;
				}
				elseif($ocupacion->bloqueado !== null){
					$camaNegra++;


				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$imagen = "camaAzul.png";
					$camaAzul++;
				}
				else{
					$camaVerde++;
				}
				continue;
			}
			else{
				if($ocupacion->ocupado !== null){
					$camaRoja++;
					//$infeccion=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$ocupacion->id_caso and i.caso=$ocupacion->id_caso and i.fecha_termino is null) as re"
					 //))->get();

					/*if(count($infeccion)){
						//$infectado = $infectado + count($infeccion);
					}*/
				}

				elseif($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";

				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "camaNegra.png";
					$camaNegra++;
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$totalAzul++;
				}
				continue;
			}

			$camaVerde =0;
		}


		$res[$obj->url]["bloqueadas"] = $camaNegra;
		$res[$obj->url]["reconvertidas"] = $camaAzul;
		$res[$obj->url]["ocupadas"] = $camaRoja;
		$res[$obj->url]["libres"] = $camaVerde;
		$res[$obj->url]["cantidad_infectados"] = $infectado;
		$res[$obj->url]["id"] = $obj->id;
		$res[$obj->url]["url"] = $obj->url;
		$res[$obj->url]["id_area_funcional"] = $obj->id_area_funcional;
		$res[$obj->url]["tooltip"] = $obj->tooltip;
		}

		//$res["asignadas"] = $camas_no_asignadas;
		return $res;

	}

	private function getQuery($data){
		$ocupacionesCamaSala = Consultas::ultimoEstadoCamas();
		if($this->tipoUser == TipoUsuario::ADMINSS || $this->tipoUser == TipoUsuario::MONITOREO_SSVQ || $this->tipoUser == TipoUsuario::ADMINIAAS) $ocupacionesCamaSala=$ocupacionesCamaSala->where("est.id", "=", $data["id"]);
		else $ocupacionesCamaSala=$ocupacionesCamaSala->where("ue.url", "=", $data)->where("est.id", "=", Session::get("idEstablecimiento"));
		return $ocupacionesCamaSala->get();
	}


	public function camas($id){
		return View::make("Index/Cama", ["id" => $id, "nombre" => Establecimiento::getNombre($id)]);
	}

	public function getCamas(Request $request){
		$url = URL::to('/');
		$unidad=$request->input("unidad");
		$idEstablecimiento=$request->input("id");
		$tiene=false;
		$camas=array();
		$nombres = array();
		$consulta = Consultas::ultimoEstadoCamas();
		$consulta = Consultas::addTiempoBloqueo($consulta);
		$consulta = Consultas::addTiempoReserva($consulta);
		$consulta = Consultas::addTiemposOcupaciones($consulta);
		$consulta = $consulta->addSelect("s.visible");
		// $consulta->where("est.id", "=", $idEstablecimiento)
		// 	->where("ue.url", "=", $unidad)
		// 	->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
		// 	->whereNotNull("id_sala")
		// 	->where("s.visible", true)
		// 	->orderBy("ue.alias")
		// 	->orderBy("s.nombre")
		// 	->orderBy("cm.id_cama");
		$consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
		->where("est.id", "=", $idEstablecimiento)
		->where("ue.url", "=", $unidad)
		->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
		->whereNotNull("id_sala")
		->where("s.visible", true)
		->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
		->orderBy("s.nombre", "asc");
		$ocupacionesCamaSala = $consulta->get();

		foreach($ocupacionesCamaSala as $ocupacion){
			$nombre_sala = empty($ocupacion->nombre_sala) ? "Sala sin nombre ({$ocupacion->id_sala})" : $ocupacion->nombre_sala;
			$nombres[$ocupacion->id_sala] = $nombre_sala;
			$imagen="SIN_PACIENTE.png";

			$reconvertida = "nada.png";
			$sexo = "nada.png";
			$estadia_promedio = "nada.png";
			$alta_clinica = "nada.png";
			$iaas_img = "nada.png";

			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$horas=Consultas::formatTiempoReserva($ocupacion->reserva_queda);
					if(empty($horas)) $horas="<br><br>";
					$renovada=($ocupacion->renovada) ? 1 : 0;
					$camas[$ocupacion->id_sala][]=array("img" => "<a><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "cama_bloqueada.png";
					$horas=Consultas::formatTiempoBloqueo($ocupacion->fecha_bloqueo);
					if(empty($horas)) $horas="<br><br>";
					$click=(Auth::user()->tipo == TipoUsuario::ADMINSS) ? "onclick='abrirDesbloquear(\"$ocupacion->id_cama_unq\");'" : "";
					$class=(Auth::user()->tipo == TipoUsuario::ADMINSS) ? "class='cursor'" : "";
					if(Auth::user()->tipo != TipoUsuario::$USUARIO){
						$camas[$ocupacion->id_sala][]=array("img" => "<a $click $class><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
					}
					else{
						$camas[$ocupacion->id_sala][]=array("img" => "<a $click $class><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
					}
				}
				//cama reconvertida
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$imagen = "SIN_PACIENTE.png";
					$reconvertida = "reconvertida.png";
					$nombre=UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual) ." - ". $ocupacion->id_cama;
					$camas[$ocupacion->id_sala][]=array("img" => "<a><figure> <img src='$url/img/$imagen' class='imgCama' /><figcaption>
					<img src='$url/img/$sexo' class='imgPunto' />
					<img src='$url/img/$reconvertida' class='imgPunto' />
					<img src='$url/img/$estadia_promedio' class='imgPunto' />
					<img src='$url/img/$alta_clinica' class='imgPunto' />
					<img src='$url/img/$iaas_img' class='imgPunto' />
					<br>
					$nombre</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
				}
				else{
					$tiene=true;
					$camas[$ocupacion->id_sala][]=array("img" => "<a><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
				}
				continue;
			}
			else{
				if($ocupacion->sexo == "masculino"){
					$sexo = "hombre.png";
				}
				if($ocupacion->sexo == "femenino"){
					$sexo = "mujer.png";
				}
				if($ocupacion->sexo == "indefinido"){
					$sexo = "indefinido.png";
				}
				if($ocupacion->ocupado !== null){
					$imagen = "camaRoja.png";

					$infeccion=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$ocupacion->id_caso and i.caso=$ocupacion->id_caso and i.fecha_termino is null) as re"
         			))->get();

					if(count($infeccion))
						{
							//$imagen = "camaRojaInfec.png";
							$iaas_img = "iaas.png";
						}

						if($ocupacion->fecha_liberacion == null && $ocupacion->fecha_alta != null){
							$imagen = "camaRojaAlta.png";
						}



						if($ocupacion->riesgo == null){
							$imagen = "SIN_CATEGORIZACION.png";
						}

						//** fix color camas */
						//cama ANARILLA
					elseif($ocupacion->riesgo == "B3" || $ocupacion->riesgo == "C1" || $ocupacion->riesgo == "C2"){
						$imagen = "RIESGO_B.png";
					}

					//Cama VERDE
					elseif($ocupacion->riesgo[0]=="D" || $ocupacion->riesgo == "C3"){
						$imagen = "RIESGO_D.png";
					}

					//Cama ROJA
					elseif($ocupacion->riesgo[0]=="A" ||$ocupacion->riesgo == "B1" || $ocupacion->riesgo == "B2"){
						$imagen = "RIESGO_A.png";
					}

					//cama naranja
					if($ocupacion->fecha_ingreso_real == null){
						$imagen = "cama_reservada.png";
					}
					//** fix color camas */

						// //aCma amarilla
						// elseif($ocupacion->riesgo[0] == "D"){
						// 	$imagen = "RIESGO_D.png";
						// }
						// //Cama azul
						// elseif($ocupacion->riesgo[0]=="C"){
						// 	$imagen = "RIESGO_C.png";
						// }
						// //Cama verde
						// elseif($ocupacion->riesgo[0]=="B"){
						// 	$imagen = "RIESGO_B.png";
						// }

						// //Cama roja
						// elseif($ocupacion->riesgo[0]=="A"){
						// 	$imagen = "RIESGO_A.png";
						// }

					//$horas= Consultas::formatTiempoOcupacion($ocupacion->fecha_ingreso, $ocupacion->fecha_liberacion);
					$horas= Consultas::formatTiempoOcupacion($ocupacion->fecha_ingreso_real, $ocupacion->fecha_liberacion);
					$caption = (empty($ocupacion->riesgo)) ? $horas : $ocupacion->riesgo ." - ". $horas;
					$nombre=UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual);
					$caption=$caption." - ".$ocupacion->id_cama;
					$click="getPaciente(\"$ocupacion->id_paciente\", \"$nombre\",\"$ocupacion->id_caso\",\"$idEstablecimiento\")";
					$camas[$ocupacion->id_sala][]=array("img" => "<a class='cursor' onclick='$click'><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>
					<img src='$url/img/$sexo' class='imgPunto' />
					<img src='$url/img/$reconvertida' class='imgPunto' />
					<img src='$url/img/$estadia_promedio' class='imgPunto' />
					<img src='$url/img/$alta_clinica' class='imgPunto' />
					<img src='$url/img/$iaas_img' class='imgPunto' />
						<br>
						$caption
						</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
				}

				elseif($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$horas=Consultas::formatTiempoReserva($ocupacion->reserva_queda);
					if(empty($horas)) $horas="<br><br>";
					$renovada=($ocupacion->renovada) ? 1 : 0;
					$camas[$ocupacion->id_sala][]=array("img" => "<a><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "camaNegra.png";
					$horas=Consultas::formatTiempoBloqueo($ocupacion->fecha_bloqueo);
					$click=(Auth::user()->tipo == TipoUsuario::ADMINSS) ? "onclick='abrirDesbloquear(\"$ocupacion->id_cama_unq\");'" : "";
					$class=(Auth::user()->tipo == TipoUsuario::ADMINSS) ? "class='cursor'" : "";
					if(empty($horas)) $horas="<br><br>";
					if(Auth::user()->tipo != TipoUsuario::$USUARIO){
						$camas[$ocupacion->id_sala][]=array("img" => "<a $click $class><figcaption>$horas - $ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
					}
					else{
						$camas[$ocupacion->id_sala][]=array("img" => "<a $click $class><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
					}
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$imagen = "camaAzul.png";
					$reconvertida = "reconvertida.png";
					$nombre=UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual) ." - ". $ocupacion->id_cama;
					$camas[$ocupacion->id_sala][]=array("img" => "<a><figure> <img src='$url/img/$imagen' class='imgCama' /><figcaption>
					<img src='$url/img/$sexo' class='imgPunto' />
					<img src='$url/img/$reconvertida' class='imgPunto' />
					<img src='$url/img/$estadia_promedio' class='imgPunto' />
					<img src='$url/img/$alta_clinica' class='imgPunto' />
					<img src='$url/img/$iaas_img' class='imgPunto' />
						<br>
						$nombre</figcaption> </figure></a>", "sala" => $ocupacion->id_sala);
				}
				continue;
			}

		}

		$response=array("nombres" => $nombres, "salas" => $camas, "tiene"=>$tiene);

		return response()->json($response);
	}

	public function obtenerMensajeBloqueo(Request $request){
    	$idCama=$request->input("idCama");
    	$historial=HistorialBloqueo::where("cama", "=", $idCama)->orderBy("fecha", "desc")->first();
    	if($historial == null) return response()->json(["motivo" => ""]);
    	return response()->json(["motivo" => ucwords($historial->motivo)]);
    }

    public function obtenerCamasLista($unidad,$est){
		$response = array();
		$unidad = ($unidad === null) ? $request->input("unidad") : $unidad;
		$est =$est;
		$ocupacionesCamaSala = Consultas::ultimoEstadoCamas();
		$ocupacionesCamaSala = Consultas::addTiempoBloqueo($ocupacionesCamaSala);
		$ocupacionesCamaSala = Consultas::addTiempoReserva($ocupacionesCamaSala);
		$ocupacionesCamaSala = Consultas::addTiemposOcupaciones($ocupacionesCamaSala);
		$ocupacionesCamaSala = $ocupacionesCamaSala
			->leftJoin("tipos_cama as ttc", "ttc.id", "=", "cm.tipo")
			->addSelect("s.visible")
			->addSelect("cs.fecha_ingreso", "ttc.nombre as tipo")
			->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
			->where("est.id", "=", $est)
			->where("ue.url", "=", $unidad)
			->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
			->whereNotNull("id_sala")
			->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
			->get();
			//return $ocupacionesCamaSala;
		foreach($ocupacionesCamaSala as $ocupacion){
			$nombre_sala = empty($ocupacion->nombre_sala) ? "Sala sin nombre ({$ocupacion->id_sala})" : $ocupacion->nombre_sala;
			$estado = "";
			$nombre = $ocupacion->nombrePaciente;
			$rut = (empty($ocupacion->rut)) ? "" : Paciente::formatearRut($ocupacion->rut, $ocupacion->dv);
			//$diagnostico = $ocupacion->diagnostico;
			//$diagnostico = (empty($ocupacion->diagnostico)) ? "" : $ocupacion->diagnostico;
			$hd = HistorialDiagnostico::select("diagnostico")->where("caso","=",$ocupacion->id_caso)->orderBy("id","desc")->first();
			$diagnostico = $hd["diagnostico"];
			$horas=Consultas::formatTiempoReserva($ocupacion->reserva_queda);
			$segundos = $this->tiempoEstada($ocupacion->reserva_queda);
			$cama = $ocupacion->id_cama;
			$nombreUnidad = $ocupacion->unidad;
			$ingreso = '';
			$n_cama = $ocupacion->xx;
			$tipo_cama = $ocupacion->tipo;
			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$estado = "Reservada";
				}
				elseif($ocupacion->bloqueado !== null){
					$estado = "Bloqueada";
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$estado = "Reconvertida";
				}
				else{
					$estado = "Libre";
				}
			}
			else{
				if($ocupacion->ocupado !== null){
					$estado = "Ocupada";
					$horas=Consultas::formatTiempoOcupacion($ocupacion->fecha_ingreso, $ocupacion->fecha_liberacion);
					$segundos = $this->tiempoEstada($ocupacion->fecha_ingreso, $ocupacion->fecha_liberacion);
					$ingreso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ocupacion->fecha_ingreso)->format("d-m-Y H:i:s");
				}

				elseif($ocupacion->reservado !== null){
					$estado = "Reservada";
				}
				elseif($ocupacion->bloqueado !== null){
					$estado = "Bloqueada";
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$estado = "Reconvertida";
				}
			}
			$response[]=array($nombreUnidad, $nombre_sala, $cama, $tipo_cama, $diagnostico, $nombre, $rut, $ingreso, $estado, $horas, $segundos, $n_cama );
		}

		return response()->json($response);
	}

	private function tiempoEstada($ingreso, $liberacion = null){
		if(is_null($ingreso)) return "";
		if(is_null($liberacion)) $f_liberacion = \Carbon\Carbon::now();
		else $f_liberacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $liberacion);
		$f_ingreso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ingreso);
		/* @var $diff \Carbon\Carbon */
		return $f_liberacion->diffInSeconds($f_ingreso);
	}

    public function exportar($id){
		$this->establecimiento=$id;
		/* $url=DB::table( DB::raw(
			"(select url,visible from unidades_en_establecimientos where establecimiento=$this->establecimiento and visible=true order by url) as ra"
		))->get();
		return $camas2=json_decode($this->obtenerCamasLista($url[0]->url,$this->establecimiento)); */

		Excel::create('Camas', function($excel) {
			$excel->sheet('Camas', function($sheet) {


		$url=DB::table( DB::raw(
             "(select url,visible from unidades_en_establecimientos where establecimiento=$this->establecimiento and visible=true order by url) as ra"
         ))->get();
				$i=0;
				foreach ($url as $urls)
				{   $ur=$urls->url;
					$camas2=json_decode($this->obtenerCamasLista($ur,$this->establecimiento)->getContent());
					$camas3[]=$camas2;
					$i++;

				}
				$j=0;
				foreach ($camas3 as $camita) {
					$camas2=array_merge($camas2, $camita);
					$j++;
					if($j+1==$i)break;
				}
				$sheet->loadView('Gestion.ListaCamas', ["camas" => $camas2]);
			});
		})->download('xls');
	}

	/* public function categorizacion(){
		$resultados = DB::select(DB::raw("select riesgo::varchar, ((100*count(riesgo))/(select count(*)
			from casos
			inner join ultimas_ocupaciones_vista u on u.caso=casos.id
			inner join establecimientos on casos.establecimiento = establecimientos.id
			where
			establecimientos.id = ".Session::get('idEstablecimiento')." and
			fecha <= now())::numeric)::numeric(4,1) as porcentaje, count(riesgo) as numero_pacientes
			from
			(select casos.id, max(tec.fecha) as fecha, tec.riesgo as riesgo
			from casos
			inner join ultimas_ocupaciones_vista u on u.caso=casos.id
			inner join (select distinct(caso), max(fecha) as fec from t_evolucion_casos group by caso) as t on t.caso = casos.id
			inner join t_evolucion_casos as tec on tec.caso = casos.id
			inner join establecimientos on casos.establecimiento = establecimientos.id
			where
			establecimientos.id = ".Session::get('idEstablecimiento')." and
			tec.fecha <=  now() AND
			tec.riesgo is not null and
			t.fec=tec.fecha
			group by (casos.id, tec.riesgo))tab
			group by (riesgo)
			order by (riesgo)
		"));

		$sinCat = DB::select(DB::raw("select 'Sin categorizar' as riesgo, 100- 0 as porcentaje, count(*) as numero_pacientes
			from casos
			inner join ultimas_ocupaciones_vista u on u.caso=casos.id
			inner join establecimientos on casos.establecimiento = establecimientos.id
			where
			establecimientos.id = ".Session::get('idEstablecimiento')." and
			fecha <= now()
		"));

		$cat = array("A1"=>array("riesgo"=>"A1", "porcentaje"=>"0", "numero_pacientes"=>0),
				"A2"=>array("riesgo"=>"A2", "porcentaje"=>"0", "numero_pacientes"=>0),
				"A3"=>array("riesgo"=>"A3", "porcentaje"=>"0", "numero_pacientes"=>0),
				"B1"=>array("riesgo"=>"B1", "porcentaje"=>"0", "numero_pacientes"=>0),
				"B2"=>array("riesgo"=>"B2", "porcentaje"=>"0", "numero_pacientes"=>0),
				"B3"=>array("riesgo"=>"B3", "porcentaje"=>"0", "numero_pacientes"=>0),
				"C1"=>array("riesgo"=>"C1", "porcentaje"=>"0", "numero_pacientes"=>0),
				"C2"=>array("riesgo"=>"C2", "porcentaje"=>"0", "numero_pacientes"=>0),
				"C3"=>array("riesgo"=>"C3", "porcentaje"=>"0", "numero_pacientes"=>0),
				"D1"=>array("riesgo"=>"D1", "porcentaje"=>"0", "numero_pacientes"=>0),
				"D2"=>array("riesgo"=>"D2", "porcentaje"=>"0", "numero_pacientes"=>0),
				"D3"=>array("riesgo"=>"D3", "porcentaje"=>"0", "numero_pacientes"=>0)
		);

		$porcentaje = 100;
		$total_pac = $sinCat[0]->numero_pacientes;
		foreach($resultados as $resultado){
			if(array_key_exists($resultado->riesgo, $cat)){
				$cat[$resultado->riesgo] = array("riesgo"=>$resultado->riesgo,
												"porcentaje"=>$resultado->porcentaje,
												"numero_pacientes"=>$resultado->numero_pacientes);
				$porcentaje = $porcentaje - $resultado->porcentaje;
				$total_pac = $total_pac - $resultado->numero_pacientes;
			}
		}

		$sin = array("riesgo"=>$sinCat[0]->riesgo, "porcentaje"=>$porcentaje, "numero_pacientes"=>$total_pac);

		return response()->json(array("cat"=>$cat, "sin"=>$sin));

	} */


	public function reporteUsoDeCamas(){

		$user = Auth::user();
		$idEstablecimiento=$user->establecimiento;


		//if(Session::get("idEstablecimiento")){
		if($idEstablecimiento){
		    //$whereEstablecimiento = "tv.id_establecimiento=".Session::get("idEstablecimiento");
			$whereEstablecimiento = "tv.id_establecimiento=".$idEstablecimiento;
			//$whereEstablecimiento2 = "h.id_establecimiento=".Session::get("idEstablecimiento");
			$whereEstablecimiento2 = "h.id_establecimiento=".$idEstablecimiento;
		}
		else{
			$whereEstablecimiento = "TRUE";
			$whereEstablecimiento2 = "TRUE";
		}

		$usoDeCamas = DB::select("
		SELECT    tv.total_camas,
          tab.n_camas_ocupadas,
          tv.tipo_cama,
          tv.tipo AS id_tipo,
          ((tab.n_camas_ocupadas*100)/tv.total_camas)
                    ||'%' AS porcentaje_ocupadas
		FROM     tipos_camas_hospital_vista tv
		LEFT JOIN  (
			SELECT    Count(*) AS n_camas_ocupadas,
					c.tipo   AS id_tipo_cama,
					t.nombre AS tipo_cama
			FROM      camas c
			join salas s on s.id = c.sala
			join unidades_en_establecimientos u on u.id = s.establecimiento
			LEFT JOIN t_historial_ocupaciones_vista_aux h
			ON        h.cama=c.id
			LEFT JOIN lista_espera l
			ON        l.caso=h.caso
			LEFT JOIN tipos_cama t
			ON        t.id=c.tipo
			WHERE     $whereEstablecimiento2
			AND       h.fecha_ingreso_real IS NOT NULL
			AND       h.cama IN
					(
							SELECT id
							FROM   camas_vigentes_vista)
			AND       c.tipo IS NOT NULL
			AND       h.created_at < (Now()::date + interval '1 day')
			AND       (
								h.fecha_liberacion IS NULL
					OR        h.fecha_liberacion > (now()::date + interval '1 day'))
			AND       h.caso NOT IN
					(
							SELECT caso
							FROM   lista_transito
							WHERE  fecha_termino IS NULL)
			GROUP BY  c.tipo,
					t.nombre) tab
		ON        tv.tipo=tab.id_tipo_cama
		WHERE     $whereEstablecimiento");

		$camas [] = [];
		foreach($usoDeCamas as $uso){
			$camas[$uso->tipo_cama] = [
				"total_camas" => $uso->total_camas,
				"n_camas_ocupadas" => ($uso->n_camas_ocupadas != NULL)?$uso->n_camas_ocupadas:0,
				"porcentaje_ocupadas" => ($uso->porcentaje_ocupadas != NULL)?$uso->porcentaje_ocupadas:'0%',
				"tipo_cama" => $uso->tipo_cama
			];
		}

		$total_listaEspera =  ListaEspera::cantidadPacientes();
		$total_listaTransito =  ListaTransito::cantidadPacientes();
		$total_categorizacionD2yD3 = Riesgo::PacientesD2yD3();
	    $total_categorizados = Riesgo::categorizadosHoy();
		$total_egresos = Caso::egresos();
		$estada_promedio = Caso::estada();
		$examenes_pendientes = Examen::cantidadDeExpamenesPendientes();
		if(!array_key_exists ("CRITICA", $camas)){
			$camas['CRITICA'] = array(
				"tipo_cama"=> 'CRITICA',
				"porcentaje_ocupadas"=> '0%',
				"total_camas"=> '0',
				"n_camas_ocupadas"=>'0'
			);
		}
		if(!array_key_exists ("MEDIA", $camas)){
			$camas['MEDIA'] = array(
				"tipo_cama"=> 'MEDIA',
				"porcentaje_ocupadas"=> '0%',
				"total_camas"=> '0',
				"n_camas_ocupadas"=>'0'
			);
		}
		if(!array_key_exists ("BASICA", $camas)){
			$camas['BASICA'] = array(
				"tipo_cama"=> 'BASICA',
				"porcentaje_ocupadas"=> '0%',
				"total_camas"=> '0',
				"n_camas_ocupadas"=>'0'
			);
		}


		$data = array(
				"0"=>array("tipo"=>$camas['CRITICA']["tipo_cama"], "porcentaje"=>$camas['CRITICA']["porcentaje_ocupadas"], "total"=>$camas['CRITICA']["total_camas"], "ocupadas"=>$camas['CRITICA']["n_camas_ocupadas"]),
				"1"=>array("tipo"=>$camas['BASICA']["tipo_cama"], "porcentaje"=>$camas['BASICA']["porcentaje_ocupadas"], "total"=>$camas['BASICA']["total_camas"], "ocupadas"=>$camas['BASICA']["n_camas_ocupadas"]),
				"2"=>array("tipo"=>$camas['MEDIA']["tipo_cama"], "porcentaje"=>$camas['MEDIA']["porcentaje_ocupadas"], "total"=>$camas['MEDIA']["total_camas"], "ocupadas"=>$camas['MEDIA']["n_camas_ocupadas"]),
				"TotalListaEspera"		=> $total_listaEspera[0]->total_espera,
				"TotalListaTransito"		=> $total_listaTransito[0]->total_transito,
				"total_categorizacionD2yD3"	=> count($total_categorizacionD2yD3),
				"total_categorizados"	=> count($total_categorizados),
				"total_egresos"	=> count($total_egresos),
				"estada_promedio"	=> $estada_promedio,
				"examenes" => $examenes_pendientes[0]->count


		);

		//return $usoDeCamas;
		return response()->json(array("data"=>$data));
	}

	public function resumenCamasIndexPadre(){

		$id_establecimiento = Session::get("idEstablecimiento");

		/*Ocupadas*/
		/*Esta linea dentro del where inhabilita las camas ocupadas en la serena y no tiene sentido
		 (h.fecha_ingreso_real is null or h.caso in (SELECT caso from lista_transito where fecha_termino is not null)) and */
		$resumen = DB::select("SELECT distinct id_servicio as id, alias as nombre, sum (count) as ocupadas from(
		  select h.id_servicio, h.nombre_servicio as alias, count(*)
		  from
		    (select caso, cama, fecha_liberacion, fecha_ingreso_real, nombre_servicio, id_servicio
			from t_historial_ocupaciones_vista_aux th
			join camas as c on c.id = th.cama
			join salas as s on s.id = c.sala
			join unidades_en_establecimientos as u on u.id = s.establecimiento
			where id_establecimiento= :id_establecimiento
			and s.visible is true
			and u.visible is true) h
		  where
		   h.fecha_liberacion is null and
		   h.cama in (select id from camas_vigentes_vista)
		  group by h.id_servicio, h.nombre_servicio
		  union
		   select uee.id, uee.alias, 0 as count from unidades_en_establecimientos uee join salas_con_camas s on s.establecimiento = uee.id and uee.establecimiento= :id_establecimiento and uee.visible is true and s.visible is true
			)tab
			group by id_servicio,alias order by alias asc
			", ['id_establecimiento'=>$id_establecimiento]);

		/*Bloqueadas*/
		foreach ($resumen as $res) {
			$tooltip = DB::select("SELECT distinct id_servicio as id, alias as nombre, sum (count) as bloqueadas from(
			  select h.id_servicio, h.nombre_servicio as alias, count(*)
			  from
			    historial_bloqueo_camas_vista h

			  join camas as c on c.id = h.cama
			  join salas as s on s.id = c.sala
			  join unidades_en_establecimientos as u on u.id = s.establecimiento

			  where
			   h.id_establecimiento= :id_establecimiento and
			   h.fecha_habilitacion is null and
			   s.visible is true and
			   u.visible is true
			  group by h.id_servicio, h.nombre_servicio
			  union
			   select id, alias, 0 as count from unidades_en_establecimientos where establecimiento= :id_establecimiento
			)tab
			where id_servicio = :id_servicio
			group by id_servicio,alias", ['id_establecimiento'=>$id_establecimiento, 'id_servicio'=>$res->id]);
			$res->bloqueadas =$tooltip[0]->bloqueadas;
		}

		/*Libres*/
		$total = 100;
		$total = $total - ($res->ocupadas + $res->bloqueadas);
		$res->libres = $total;


		/*url, tooltip, id_area_funcional*/
		foreach ($resumen as $res) {
			$consulta = DB::select("select u.url, u.tooltip, u.id_area_funcional, a.nombre as nombre_area from unidades_en_establecimientos as u inner join area_funcional as a on u.id_area_funcional = a.id_area_funcional where id=?", [$res->id]);

			$res->url = $consulta[0]->url;
			$res->tooltip = $consulta[0]->tooltip;
			$res->id_area_funcional = $consulta[0]->id_area_funcional;
			$res->nombre_area = $consulta[0]->nombre_area;
		}

		/*Total camas*/
		foreach ($resumen as $res) {
			$consulta = DB::select("SELECT distinct id_servicio, alias, sum (count) as total_camas from(
			  select t.id_servicio, t.nombre_servicio as alias, count(*)
			  from
			    (select id_servicio, nombre_servicio
			     from camas_vigentes_vista
			     where
			     id_establecimiento = :id_establecimiento
				 and sala not in (select id from salas where visible is false)
				 ) t
			  group by t.id_servicio, t.nombre_servicio

			  union
			   select id, alias, 0 as count from unidades_en_establecimientos where establecimiento= :id_establecimiento
			)tab
			where id_servicio = :id_servicio
			group by id_servicio,alias
			", ['id_establecimiento'=>$id_establecimiento, 'id_servicio'=>$res->id]);

			$res->libres = $consulta[0]->total_camas - ($res->ocupadas + $res->bloqueadas);
			$res->dotacion = $consulta[0]->total_camas;

		}

		foreach ($resumen as $res){
			$consulta = DB::select(DB::Raw("SELECT id_servicio, dotacion from dotacion_cama where id_servicio = $res->id and visible = true"));
			if(isset($consulta[0]->dotacion)){
				$res->dotacion = $consulta[0]->dotacion;
			}
		}
		Log::info($resumen);
		return $resumen;
	}

	public function resumenCamasIndex(){
		$resumen = $this->resumenCamasIndexPadre();
		return response()->json(["resumen"=>$resumen]);
	}

	public function descargarExcelResumencamas(){
		$datos = [];
		$informacion = $this->resumenCamasIndexPadre();

		foreach ($informacion as $info){
			$nombre_area = $info->nombre_area;
			$servicio = $info->nombre;
			$dotacion = $info->dotacion;
			$camaLibre = $info->libres;
			$camaOcupada = $info->ocupadas;
			$camaBloqueada = $info->bloqueadas;
			$datos[] = [
				$nombre_area,
				$servicio,
				$dotacion,
				$camaLibre,
				$camaOcupada,
				$camaBloqueada

			];
		}
		try {

			$html = [
				"informacion" => $datos,
			];

			Excel::create('Resumencamas', function ($excel) use ($html){
				$excel->sheet('Resumencamas', function ($sheet) use ($html){

					$sheet->mergeCells('A1:H1');
					$sheet->setAutoSize(true);

					$sheet->setHeight(1, 50);
					$sheet->row(1, function ($row) {

						// call cell manipulation methods
						$row->setBackground('#1E9966');
						$row->setFontColor("#FFFFFF");
						$row->setAlignment("center");

					});
					#

					$sheet->loadView('NuevasEstadisticas.ExcelResumencamas', ["html" => $html]);
				});
			})->download('xls');
		}catch (Exception $e) {
		 	return response()->json($e->getMessage());
		}

	}


}
