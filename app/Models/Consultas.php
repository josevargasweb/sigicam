<?php namespace App\Models{

use App\Http\Controllers\GestionController;
use App\Http\Controllers\EvolucionController;

use DB;
use TipoUsuario;
use Session;
use Log;
use Auth;
use Exception;

use App\Models\EvolucionCaso;
use App\Models\Restriccion;
use App\Models\Caso;
use App\Models\Medicamento;
use App\Models\THistorialOcupaciones;
use Carbon\Carbon;


class Consultas{

	public static function restriccionCategorizacionCama($idCaso){
		//se pidio cancelar la restriccion
		$sin_categorizar = true;
		$restriccion_tiempo = false;
		$imagen = "SIN_CATEGORIZACION.png";
		return response()->json(["restriccion" => $restriccion_tiempo, "imagen" => $imagen, "sin_categorizar" => $sin_categorizar]);
	
		$ocupacion = THistorialOcupaciones::where('t_historial_ocupaciones.caso',$idCaso)
					->whereNull('t_historial_ocupaciones.fecha_liberacion')
					->first();

		/* $restriccion_tiempo=false;
		$imagen = "SIN_CATEGORIZACION.png"; */

		$imagen = "SIN_CATEGORIZAR_candado.png";
		$restriccion_tiempo = true;

		$fecha_ingreso_real = Carbon::parse($ocupacion->fecha_ingreso_real);
		$fecha_ingreso_real_hr = $fecha_ingreso_real->format("H");
		$dia_actual = Carbon::now()->format("Y-m-d");
		$tiempo_estadia = $fecha_ingreso_real->diffInHours(Carbon::now());
		$dia_hospitalizacion_mas_2 = $fecha_ingreso_real->addDays(2)->format("Y-m-d");

		$hr_actual = Carbon::now()->format("H:i");
		$hr_max = Carbon::parse("14:00")->format("H:i");
		$hr_min = Carbon::parse("00:00")->format("H:i");

		/* if($tiempo_estadia <= 8){
			$imagen = "SIN_CATEGORIZAR_candado.png";
			$restriccion_tiempo=true;
		}

		if($fecha_ingreso_real_hr >= 16 && ($tiempo_estadia >= 8)){
			$imagen = "SIN_CATEGORIZAR_candado.png";
			$restriccion_tiempo=true;

			if($dia_actual >= $dia_hospitalizacion_mas_2){
				$imagen = "SIN_CATEGORIZACION.png";
				$restriccion_tiempo=false;
			}
		} */

		$hora = (new EvolucionController)->consultarHora();

		$sin_categorizar = false;
		if ( $tiempo_estadia >= 8) {
			
			$imagen = "SIN_CATEGORIZACION.png";
			$sin_categorizar = true;

			if(isset($hora->original['exito']) == true){
				$restriccion_tiempo = false;
			}
		}

		/* return response()->json(["restriccion" => $restriccion_tiempo, "imagen" => $imagen]); */

		return response()->json(["restriccion" => $restriccion_tiempo, "imagen" => $imagen, "sin_categorizar" => $sin_categorizar]);
	}

	public static function getRegionPaciente($comuna){
		return DB::table('comuna')->select('id_region')->where('id_comuna', $comuna)->first();
	}

	public static function restriccionPersonal($unidad){
		$tiene = Restriccion::where([["id_unidad", $unidad], ["id_usuario",Auth::user()->id]])->first();
		if($tiene){
			return true;
		}else{
			return false;
		}
		//return $undiad;
	}

	public static function getRiesgoDependencia($idCaso){

		$riesgo = EvolucionCaso::where("caso","=",$idCaso)
						->select("r.dependencia1","r.dependencia2","r.dependencia3","r.dependencia4","r.dependencia5","r.dependencia6","r.riesgo1","r.riesgo2","r.riesgo3","r.riesgo4","r.riesgo5","r.riesgo6","r.riesgo7","r.riesgo8")
						->leftjoin("riesgos as r", "t_evolucion_casos.riesgo_id", "=", "r.id")
						->orderby("fecha","desc")
						->first();

		$paciente = Caso::leftjoin("pacientes as p", "p.id", "=", "casos.paciente")
						->where("casos.id", $idCaso)
						->first();
		$nombre_paciente = strtoupper($paciente->nombre)." ".strtoupper($paciente->apellido_paterno)." ".strtoupper($paciente->apellido_materno);
		if(isset($paciente->nombre_social)){
			$nombre_paciente .= " (".strtoupper($paciente->nombre_social).")";
		}

		return response()->json(["riesgo" => $riesgo, "paciente" => $nombre_paciente]); 
	}

	public static function joinCamaEstablecimiento(){
		return DB::table("camas as cm")
		->join('salas_con_camas as s', 'cm.sala', '=', 's.id')
		->join('ultimas_camas_unidades AS usc', "cm.id", "=", "usc.cama")
		->join('unidades_en_establecimientos AS ueactual', 'usc.unidad', "=", "ueactual.id")
		->rightJoin('unidades_en_establecimientos as ue', 's.establecimiento', '=', 'ue.id')
		->leftJoin('servicios_ofrecidos as srv', 'srv.unidad_en_establecimiento', '=', 'ue.id')
		->leftJoin('unidades AS u', "srv.unidad", "=", "u.id")
		->join("establecimientos AS est", "ue.establecimiento", "=", "est.id");
	}

	public static function joinUltimoEstadoCamas(){
		return self::joinCamaEstablecimiento()
		->leftJoin("ultimas_ocupaciones as uev", "uev.cama", "=", "cm.id")
		->leftJoin("ultimas_reservas as ur", "ur.cama", "=", "cm.id")
		->leftJoin("ultimos_bloqueos_camas as ub", "ub.cama", "=", "cm.id")
		->leftJoin("casos as cs", function($join){
			$join->on("cs.id", "=", "uev.caso")->orOn("cs.id", "=", "ur.caso");
		})
		/* ARREGLO KATHY DE 22.31s A 1.87s */ 
		->join(DB::raw("(SELECT p.* from casos c, pacientes p where c.paciente=p.id and fecha_termino is null) as pac"), "pac.id", "=", "cs.paciente", "full outer")
		/* ANTIGO CONSULTA */
		/* ->join("pacientes as pac", "pac.id", "=", "cs.paciente", "full outer") */
		->leftJoin("ultimos_estados_pacientes as uep", "uep.caso", "=", "cs.id")
		->leftJoin("ultimas_dietas_pacientes as udt", "udt.caso", "=", "cs.id");
	}
	public static function joinHistorialCamas(){
		return self::joinCamaEstablecimiento()
		->join("historial_ocupaciones as uev", "uev.cama", "=", "cm.id")
		->leftJoin("ultimos_bloqueos_camas as ub", "ub.cama", "=", "cm.id")
		->leftJoin("ultimas_reservas as ur", "ur.cama", "=", "cm.id")
		->leftJoin("casos as cs", "cs.id", "=", "uev.caso")
		->leftJoin("pacientes as pac", "pac.id", "=", "cs.paciente");
	}

	public static function historialCamasPorUnidad(){
		return self::joinHistorialCamas()
		->groupBy("u.nombre")->groupBy("cs.id")->groupBy("pac.rut")->groupBy("pac.id")
		->orderBy("cs.id", "desc");
	}

	public static function tiemposOcupaciones(){
		$sub = self::historialCamasPorUnidad()
		->select( DB::raw("cs.id as caso, u.nombre, max(cm.id) as cama, pac.id as id_paciente, pac.rut,min(uev.fecha) as fecha, min(uev.fecha_liberacion) as liberacion") );
		return DB::table( DB::raw("({$sub->toSql()}) as s") )
		->mergeBindings( $sub )
		->select( DB::raw("*, to_char(date_trunc('second', ( case WHEN s.liberacion is null THEN now()::timestamp without time zone ELSE s.liberacion END )) - s.fecha , 'FMdd \"días\", FMHH24 \"horas\"' ) as tiempo")  );
	}

	public static function addTiemposOcupaciones($consulta){
		return $consulta->addSelect("uev.fecha AS fecha_ocupacion", "uev.fecha_liberacion");
	}

	public static function formatTiempoOcupacion($ingreso, $liberacion = null){
		if(is_null($ingreso)) return "";
		if(is_null($liberacion)) $f_liberacion = \Carbon\Carbon::now();
		else $f_liberacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $liberacion);
		$f_ingreso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ingreso);
		$diff_dias = $f_liberacion->diffInDays($f_ingreso);
		$diff = $f_liberacion->diff($f_ingreso);
		return "{$diff_dias} días, {$diff->h} horas";
	}

	public static function addTiempoBloqueo($consulta){
		return $consulta->addSelect("ub.fecha as fecha_bloqueo");
	}

	public static function formatTiempoBloqueo($historial){
		if(is_null($historial)) return "";
		$now = \Carbon\Carbon::now();
		$fecha = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $historial);
		$diff = $now->diff($fecha);
		$diff_dias = $now->diffInDays($fecha);
		return $diff_dias. " días, ".$diff->h." horas";
	}

	public static function addTiempoReserva($consulta){
		return $consulta->addSelect("ur.queda as reserva_queda");
	}
	public static function formatTiempoReserva($reserva){
		if(is_null($reserva)) return "";
		$queda=$reserva;
		$dia=date('H', strtotime($queda));
		$minutos=date('i', strtotime($queda));
		return "Queda: ".$dia." horas, ".$minutos." minutos";
	}

	public static function ultimoEstadoCamas(){
		return self::joinUltimoEstadoCamas()->distinct()
		->select(
			"est.id as id_est",
			"s.id as id_sala_unq",
			"s.id_sala as id_sala",
			"s.nombre as nombre_sala",
			"s.created_at as fecha_creacion_sala",
			"s.descripcion as descripcion_sala",
			"cm.id as id_cama_unq",
			"cm.id_cama as id_cama",
			"cm.descripcion as cama_descripcion",
			"ue.url as alias_unidad",
			"ue.id as id_unidad",
			"ue.alias as unidad",
			"uev.fecha",
			"cs.fecha_ingreso",
			"pac.id as id_paciente",
			"pac.rut",
			"pac.dv",
			"pac.nombre as nombrePaciente",
			"pac.apellido_paterno as apellidoPaterno",
			"pac.apellido_materno as apellidoMaterno",
			"pac.fecha_nacimiento",
			"pac.sexo",
			"uep.riesgo",
			"uep.id_usuario",
			"uev.cama as ocupado",
			"ur.cama as reservado",
			"ub.cama as bloqueado",
			"est.nombre",
			"cs.id as id_caso",
			"ur.renovada as renovada",
			"ueactual.id as id_unidad_actual",
			"ueactual.alias as unidad_actual",
			"udt.dieta AS dieta_actual",
			"cs.especialidad",
			"uev.fecha_alta",
			"uev.fecha_ingreso_real",
			"uev.id_usuario_ingresa"
		);
	}

	public static function getIdReservaPorCaso($idCaso){
		return DB::table("ultimas_reservas as u")
		->select("id")->where("caso", "=", $idCaso)->first()->id;
	}

	public static function cuposCamas(){
		return self::joinUltimoEstadoCamas()
		//->join("camas_habilitadas as cmh", "cm.id", "=", "cmh.id")
		->select("est.id as id_est", "est.nombre", "ue.id as destino", "ue.alias", DB::raw('count(distinct cm.id) as cantidad'))
		->where("uev.cama", "=", NULL)
		->where("ur.cama", "=", NULL)
		->groupBy("est.id")
		->groupBy("est.nombre")
		->groupBy("ue.alias")
			->groupBy("ue.id");
	}

	public static function cuposPorUnidad($unidad){
		return self::cuposCamas()
		->where("ue.id", "=", $unidad);
	}

	public static function establecimientoConCamas($unidad){
		return self::cuposPorUnidad($unidad)
		->where("est.id", "!=", Session::get("idEstablecimiento"));
	}

	public static function getEstablecimientoConCamas($unidad){
		return self::establecimientoConCamas($unidad)->get();
	}

	public static function unidadesEnEstablecimiento(){
		return DB::table("unidades_en_establecimientos AS ue")
		->join('establecimientos AS est', "ue.establecimiento", "=", "est.id")
		//->join('unidades AS u', "ue.unidad", "=", "u.id")
		->select('ue.id as id', 'ue.alias as nombre', 'ue.url as alias', 'ue.establecimiento as establecimiento');
	}

	public static function getRiesgos($excepto = null){
		$response[0]="Sin riesgo";
		$riesgos=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", "riesgo");
		if($excepto){
			$riesgos->where("e.enumlabel", "<>", $excepto);
		}
		$riesgos = $riesgos->get();
		foreach ($riesgos as $riesgo) {
			$response[$riesgo->enumlabel]=$riesgo->enumlabel;
		}
		return $response;
	}

	public static function getRegion(){
		$regiones=DB::table("region")
			->pluck('nombre_region','id_region');
		return $regiones;
	}

	public static function getTipoUsuario(){
		$response=array();
		$tipos=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", "tipo_usuario")
		//->where("e.enumlabel", "<>","iaas")
		//->where("e.enumlabel", "<>","gestion_clinica")
		/* ->where("e.enumlabel", "<>","usuario") */
		->where("e.enumlabel", "<>","monitoreo_ssvq")
		->where("e.enumlabel", "<>","master")
		//->where("e.enumlabel", "<>","admin_iaas")
		->get();
		foreach ($tipos as $tipo) {
			$response[$tipo->enumlabel]=TipoUsuario::getNombre($tipo->enumlabel);
		}
		return $response;
	}

	public static function getMotivosLiberacion(){
		$response=array();
		$motivos=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", "motivos_liberacion")
		//->where('e.enumlabel', '<>', "hospitalización domiciliaria")
		->where('e.enumlabel', '<>', "corrección cama")
		->orderby("e.enumlabel")->get();
		foreach ($motivos as $key => $motivo) {
			if ($motivo->enumlabel == "otro") {
				$ultimo =ucwords($motivo->enumlabel);
			}else{
				if($motivo->enumlabel == "alta"){
					$response[$motivo->enumlabel]="1. Domicilio";
				}elseif($motivo->enumlabel == "derivación"){
					$response[$motivo->enumlabel]="2. Derivación a otro establecimiento de la red pública";
				}elseif($motivo->enumlabel == "traslado extra sistema"){
					$response[$motivo->enumlabel]="3. Derivación a institución privada";
				}elseif($motivo->enumlabel == "derivacion otra institucion"){
					$response[$motivo->enumlabel]="4. Derivación a otros centros u otra institución";
				}elseif($motivo->enumlabel == "Liberación de responsabilidad"){
					$response[$motivo->enumlabel]="5. Alta voluntaria";
				}elseif($motivo->enumlabel == "Fuga"){
					$response[$motivo->enumlabel]="6. Fuga del paciente";
				}elseif($motivo->enumlabel == "hospitalización domiciliaria"){
					$response[$motivo->enumlabel]="7. Hospitalización domiciliaria";
				}elseif($motivo->enumlabel == "alta sin liberar cama"){
					$response[$motivo->enumlabel]="8. Alta sin liberar cama";
				}elseif($motivo->enumlabel == "fallecimiento"){
					$response[$motivo->enumlabel]="9. Fallecimiento";
				}
				else{	
					$response[$motivo->enumlabel]=ucwords($motivo->enumlabel);
				}					
			}
		}
		
		asort($response);
		$response[$ultimo]=ucwords($ultimo);
		return $response;
	}

	public static function getMotivosLiberacion2(){
		$response=array();
		$motivos=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", "motivo_salida_urgencia")
		->where('e.enumlabel', '<>', "hospitalización domiciliaria")
		->where('e.enumlabel', '<>', "rechaza atencion")
		->where('e.enumlabel', '<>', "rehospitalizar")
		->orderby("e.enumlabel")->get();
		foreach ($motivos as $motivo) {

			if($motivo->enumlabel == "alta"){
				$response[$motivo->enumlabel]="1. Domicilio";
			}elseif($motivo->enumlabel == "derivación"){
				$response[$motivo->enumlabel]="2. Derivación a otro establecimiento de la red pública";
			}elseif($motivo->enumlabel == "traslado extra sistema"){
				$response[$motivo->enumlabel]="3. Derivación a institución privada";
			}elseif($motivo->enumlabel == "derivacion otra institucion"){
				$response[$motivo->enumlabel]="4. Derivación a otros centros u otra institución";
			}elseif($motivo->enumlabel == "liberación de responsabilidad"){
				$response[$motivo->enumlabel]="5. Alta voluntaria";
			}elseif($motivo->enumlabel == "fuga"){
				$response[$motivo->enumlabel]="6. Fuga del paciente";
			}elseif($motivo->enumlabel == "hospitalización"){
				$response[$motivo->enumlabel]="7. Hospitalización domiciliaria";
			}elseif($motivo->enumlabel == "fallecimiento"){
				$response[$motivo->enumlabel]="8. Fallecimiento";
			}elseif($motivo->enumlabel == "otro"){
				$response[$motivo->enumlabel]="9. Otro";
			}else{	
				$response[$motivo->enumlabel]=ucwords($motivo->enumlabel);
			}		
			asort($response);
		}
		return $response;
	}

	public static function getMotivosBloqueo(){
		$response=array();
		$motivos=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("e.enumlabel", "!=", "procedimiento")
		->where("t.typname", "=", "motivos_bloqueo")
		->orderBy('e.enumlabel')->get();
		foreach ($motivos as $motivo) {
			if($motivo->enumlabel == 'otros'){
				$dejar_ultimo = $motivo->enumlabel;
			}else{
				$response[$motivo->enumlabel]=ucwords($motivo->enumlabel);
			}
		}
		$response[$dejar_ultimo]=ucwords($dejar_ultimo);
		return $response;
	}

	public static function obtenerEnum($nombre){
		$response=array();
		$enums=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", $nombre)
		->orderBy("e.enumlabel")->get();
		foreach ($enums as $enum) {
			$response[$enum->enumlabel]=ucwords($enum->enumlabel);
			// if($enum->enumlabel == "alta"){
			// 	$response[$enum->enumlabel]="1. Alta";
			// }elseif($enum->enumlabel == "fallecimiento"){
			// 	$response[$enum->enumlabel]="2. Fallecimiento";
			// }
			// elseif($enum->enumlabel == "otro"){
			// 	$response[$enum->enumlabel]="3. Otro";
			// }
		}
		return $response;
	}

	public static function getUnidadesEstablecimiento($id){
		$response=array();
        $unidades = UnidadEnEstablecimiento::conCamas()->where("visible", true)->where("establecimiento", $id)->get();
		foreach($unidades as $unidad){
			$ocupacionesCamaSala = Consultas::ultimoEstadoCamas()
			->where("est.id", "=", $id)
			->where("ue.url", "=", $unidad->url)
			->whereNull("ur.cama")
			->whereNull("uev.fecha")->get();
			if(count($ocupacionesCamaSala) != 0) $response[]=array("id" => $unidad->id, "nombre" => ucwords($unidad->alias), "alias" => $unidad->url);
		}
		return $response;
	}

	public static function getHospitalizacionTotal($inicio, $fin){
		
		return $inicio;
	}

	public static function traduccionDestinoEgreso($destino){
		if ($destino == "otro") {
			$resp =ucwords($destino);
		}else{
			if($destino == "alta"){
				$resp = "Domicilio";
			}elseif($destino == "derivación"){
				$resp = "Derivación a otro establecimiento de la red pública";
			}elseif($destino == "traslado extra sistema"){
				$resp ="Derivación a institución privada";
			}elseif($destino == "derivacion otra institucion"){
				$resp = "Derivación a otros centros u otra institución";
			}elseif($destino == "Liberación de responsabilidad"){
				$resp = "Alta voluntaria";
			}elseif($destino == "Fuga"){
				$resp = "Fuga del paciente";
			}elseif($destino == "hospitalización domiciliaria"){
				$resp = "Hospitalización domiciliaria";
			}elseif($destino == "alta sin liberar cama"){
				$resp = "Alta sin liberar cama";
			}elseif($destino == "fallecimiento"){
				$resp = "Fallecimiento";
			}
			else{	
				$resp = ucwords($destino);
			}					
		}
		return $resp;
	}

	public static function fechaPrimerRegistroIngresoEnfermeria($caso){
		try {
			$fecha_actual = Carbon::now()->format("Y-m-d H:i:s");

			$anamnesis = IEAnamnesis::where('caso',$caso)->orderBy('fecha_creacion','asc')->first('fecha_creacion');
			$fecha_anamnesis = ($anamnesis) ? Carbon::parse($anamnesis->fecha_creacion)->format("Y-m-d H:i:s") : $fecha_actual;

			$medicamentos = Medicamento::where('caso',$caso)->where('visible',true)->orderBy('fecha_creacion','asc')->first('fecha_creacion');
			$fecha_medicamento = ($medicamentos) ? Carbon::parse($medicamentos->fecha_creacion)->format("Y-m-d H:i:s") : $fecha_actual;
			
			$general = IEGeneral::where('caso',$caso)->where('visible',true)->orderBy('fecha_creacion','asc')->first();
			Log::info($general);
			$fecha_general = ($general) ? Carbon::parse($general->fecha_creacion)->format("Y-m-d H:i:s") : $fecha_actual;
			// if($general->tipo_modificacion == "Editado" && $general->id_anterior == null){
			// 	Log::info("que wea");
			// 	$fecha_general = $fecha_actual;
			// }

			$segmentado = IESegmentado::where('caso',$caso)->orderBy('fecha_creacion','asc')->first('fecha_creacion');
			$fecha_segmentado = ($segmentado) ? Carbon::parse($segmentado->fecha_creacion)->format("Y-m-d H:i:s") : $fecha_actual;

			$cateter = Cateter::where('caso',$caso)->where('visible',true)->orderBy('fecha_creacion','asc')->first('fecha_creacion');
			$fecha_cateter = ($cateter) ? Carbon::parse($cateter->fecha_creacion)->format("Y-m-d H:i:s") : $fecha_actual;

			$alias_sigicam = "sigicam_padre";
			if($alias_sigicam == 'sigicam_padre' || $alias_sigicam == 'sigicam_serena' || $alias_sigicam == 'sigicam_concepcion'){
				$fechas = [$fecha_anamnesis, $fecha_medicamento, $fecha_general, $fecha_segmentado, $fecha_cateter];
			}else{ 
				//sigicam_copiapo
				$pertenencias = Pertenencias::where('caso',$caso)->where('visible',true)->orderBy('fecha_creacion','asc')->first('fecha_creacion');
				$fecha_pertenencia = ($pertenencias) ? Carbon::parse($pertenencias->fecha_creacion)->format("Y-m-d H:i:s") : $fecha_actual;

				$fechas = [$fecha_anamnesis, $fecha_medicamento, $fecha_general, $fecha_segmentado, $fecha_cateter, $fecha_pertenencia];
			}

			$fecha_minima = min($fechas);

			return ($fecha_minima == Carbon::now()->format("Y-m-d H:i:s")) ? "Sin registros" : Carbon::parse($fecha_minima)->format('d-m-Y H:i');
		} catch (Exception $ex) {
			Log::info($ex);
			return "Sin registros";
		}
	}

	public static function puedeHacer($caso, $ubicacion){
		$respuesta = "Exito";
		try {

			if($ubicacion != "lista_espera"){
				//validar si el paciente esta en lista de espera
				$listaEspera = ListaEspera::casoEnListaEspera($caso);
				if($listaEspera){
					throw new Exception("El paciente se encuentra en lista de espera de cama.");
				}
			}

			if($ubicacion != "lista_transito" && $ubicacion != "salida_preAlta"){
				//validar si paciente esta en lista de trasito
				$enlistatransito = ListaTransito::casoEnListaTransito($caso);
				if($enlistatransito){
					throw new Exception("El paciente posee una espera de hospitalización pendiente.");
				}
			}

			if($ubicacion != "salida_urgencia"){
				//validar si el paciente esta en transito a piso (Salida de urgencia)
				$salida_urgencia = ListaTransito::casoEnSalidaUrgencia($caso);
				if($salida_urgencia){
					throw new Exception("El paciente se encuentra en transito a piso.");
				}
			}
			
			if($ubicacion != "salida_preAlta"){
				//validar si el paciente esta en pre alta
				$salida_urgencia = PreAlta::casoEnPreAlta($caso);
				if($salida_urgencia){
					throw new Exception("El paciente se encuentra en pre alta.");
				}
			}

			//validar si el paciente se encuentra hospitalizado
			$hospitalizado = THistorialOcupaciones::casoHospitalizado($caso);
			if($hospitalizado){
				throw new Exception("El paciente ya se encuentra hospitalizado.");
			}
	
			//validar si el paciente fue egresado
			$egresado = Caso::casoEgresado($caso);
			if($egresado){
				throw new Exception("El paciente ya ha sido egresado.");
			}
	
			return $respuesta;
		} catch (Exception $ex) {
			Log::info($ex);
			$errores_controlados = [
				"El paciente posee una espera de hospitalización pendiente.",
				"El paciente ya se encuentra hospitalizado.",
				"El paciente ya ha sido egresado.",
				"El paciente se encuentra en lista de espera de cama.",
				"El paciente se encuentra en transito a piso.",
				"El paciente se encuentra en pre alta."
			];
			$error = "Ha ocurrido un error.";
			if(in_array($ex->getMessage(), $errores_controlados)){ 
				$error = $ex->getMessage();
			}
			
			return $error;
		}
	}

	public static function revisionPacienteMapaCamas($idCaso,$color_cama){
		$respuesta = "Exito";
		try {
			$naranja = THistorialOcupaciones::where("caso",$idCaso)->whereNull("fecha_liberacion")->whereNull('fecha_ingreso_real')->first();
			$hospitalizado = THistorialOcupaciones::where("caso",$idCaso)->whereNull("fecha_liberacion")->whereNotNull('fecha_ingreso_real')->first();
			$cama_temporal = CamaTemporal::where("camas_temporales.caso",$idCaso)
							->leftjoin("t_historial_ocupaciones", "t_historial_ocupaciones.id", "camas_temporales.id_historial_ocupaciones")
							->where("camas_temporales.visible",true)
							->first();

			$colores_hospitalizado = ["1" => "RIESGO_A.png","RIESGO_B.png","SIN_CATEGORIZACION.png","RIESGO_D.png"];
			// $color_reservado = "cama_reservada.png";

			$cama_hospitalizado = array_search($color_cama,$colores_hospitalizado);
			if($cama_hospitalizado){
				if(!$hospitalizado && !$cama_temporal){
					throw new Exception("La información del paciente sera actualizada.");
				}
			}else{				
				if(!$naranja && !$cama_temporal){
					throw new Exception("La información del paciente sera actualizada.");
				}
			}

			return $respuesta;
		} catch (Exception $ex) {
			Log::info($ex);
			return $ex->getMessage();
		}
	}

	// info turno
	public static function pacientes_hospitalizados($inicio, $fin){
		//En esta seccion se dejan los pacientes hospitalizados anteriormente en el servicio
		

		$formatoFecha = "d-m-Y H:i:s";
		$formatoFecha2 = "%h horas %i minutos %s segundos";
		$formatoFecha3 = "%i minutos %s segundos";
		$formatoFecha4 = "%s segundos";

		$establecimiento = Auth::user()->establecimiento;

		$pacientes_hospitalizados = DB::select(DB::raw("SELECT
		t.fecha_ingreso_real as hospitalizacion,
		p.rut, p.dv, p.nombre, p.apellido_paterno,
		p.apellido_materno, p.fecha_nacimiento, p.sexo,
		u.alias as nombre_servicio, p.id as id_paciente,
		s.nombre as nombre_sala, 
		ca.id_cama, c.id as caso, 
		c.procedencia, t.motivo, 
		t.id as id_historial
		from  t_historial_ocupaciones t
		inner join casos as c on c.id = t.caso
		inner join camas as ca on ca.id = t.cama
		inner join salas as s on s.id = ca.sala
		inner join unidades_en_establecimientos as u on u.id = s.establecimiento
		inner join pacientes as p on p.id = c.paciente
		where 
			t.fecha >= '$inicio'
			and t.fecha <= '$fin' 
			and u.establecimiento = $establecimiento
		"));

		/* Log::info("------inicio PACIENTES HOSPITALIZADOS------");
		Log::info("INICIO ". $inicio);
		Log::info("FIN ".$fin); */

		$lista_pacientes_hospitalizados = [];
		$hospitalizados = 0;
		$listaweones ="";
		foreach($pacientes_hospitalizados as $d){

			//Revisar historial Anterior para identificar de que venga de otra area y no de la misma
			$histo_anterior = ThistorialOcupaciones::join("camas as c","c.id","t_historial_ocupaciones.cama")
				->join("salas as s","s.id","c.sala")
				->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
				->select("c.id_cama as nombre_cama", "s.nombre as nombre_sala","u.alias as nombre_unidad")
				->where("t_historial_ocupaciones.id","<",$d->id_historial)
				->where("t_historial_ocupaciones.caso",$d->caso)
				->where("t_historial_ocupaciones.motivo","<>","corrección cama")
				->orderBy("t_historial_ocupaciones.id","desc")
				->first();

			//Datos para obtener Servicio y cama anterior
			if ((isset($histo_anterior->nombre_unidad) && ($histo_anterior->nombre_unidad != $d->nombre_servicio)) || !$histo_anterior) {
				//con esto comprobamos que el paciente viene de otro sector o simplemente fue ingresado como primer historial de ocupaciones
				$observacion = "";
				if (!$histo_anterior) {
					$observacion = "Ingreso";
				}else if (isset($histo_anterior->nombre_unidad) && ($histo_anterior->nombre_unidad != $d->nombre_servicio)){
					$observacion = "Traslado desde: <br><strong>".$histo_anterior->nombre_unidad." - ".$histo_anterior->nombre_sala." - ".$histo_anterior->nombre_cama."</strong>";
				}
				
				$fecha_fin = Carbon::parse($fin);

				//Preguntar si tiene fecha de hospitalizacion, de lo contrario buscarla con algun historial que lo tenga
				$fecha_hospitalizacion = null;
				if($d->hospitalizacion == null){
					//Con esto tratamos de encontrar una fecha de ingreso real o hospitalizacion dentro de los limites
					$Fhosp = ThistorialOcupaciones::where("caso", $d->caso)->whereNotNull("fecha_ingreso_real")
						->whereRaw("fecha_ingreso_real >= '$inicio' and fecha_ingreso_real <= '$fin'")
						->where("motivo","<>","corrección cama")
						->first();
					$fecha_hospitalizacion = ($Fhosp) ? Carbon::parse($Fhosp->fecha_ingreso_real)->format($formatoFecha) : null;
				}else if ($d->hospitalizacion != null) {
					//En el caso de traer una fecha de hospitalizacion, se debe trasnformar la fecha
					$fecha_hospitalizacion = ($d->hospitalizacion) ? Carbon::parse($d->hospitalizacion)->format($formatoFecha) : null;
				}

				$listaweones .=",(".$d->id_historial.", ".$d->caso.", ".$fecha_hospitalizacion.")";
				if($fecha_hospitalizacion != null && Carbon::parse($d->hospitalizacion) <= $fecha_fin){

					$listaweones .="(ACEPTADO)";
					$hospitalizados++;
					//Esta seccion solo se encargara de ver los hospitalizados, debido a que habra otra tabla para los pacientes en espera de hospitalizacion, 
					//donde la suma de ambas listads, ddeberia ser el total de ingresos en la tabla principal
					//Para esto, se necesitara que los pacientes tengan una fecha de ingreso real, dentro del turno, para que asi se considere como que fue hospitalizado dentr ode estas fechas 

					$nombreCompleto = $d->nombre ." ". $d->apellido_paterno ." ". $d->apellido_materno;
					$rut = ($d->rut) ? $d->rut ."-". $d->dv : "-";

					$fechaSolicitud = ListaEspera::select('fecha')->where('caso',$d->caso)->first();
					$fecha_solicitud = ($fechaSolicitud['fecha']) ? Carbon::parse($fechaSolicitud['fecha'])->format($formatoFecha) : '--';

					if($fecha_solicitud == '--'){
						$fechaSolicitud = Caso::select('fecha_ingreso2')->find($d->caso);
						$fecha_solicitud = ($fechaSolicitud['fecha_ingreso2']) ? Carbon::parse($fechaSolicitud['fecha_ingreso2'])->format($formatoFecha) : '--';
					}

					$fechaAsignacion = ListaTransito::select('fecha')->where('caso',$d->caso)->first();

					$fecha_asignacion = ($fechaAsignacion['fecha']) ? Carbon::parse($fechaAsignacion['fecha'])->format($formatoFecha) : '--';


					$soli = Carbon::parse($fecha_solicitud);
					$soli = ($soli) ? $soli : null;
					$asig = Carbon::parse($fecha_asignacion);
					$asig = ($asig) ? $asig : null;
					$hosp = Carbon::parse($fecha_hospitalizacion);
					$hosp = ($hosp) ? $hosp : null;

					if($soli == null || $asig == null){
						$q = "--";
					}else{
						$dif_solicitar_asignar = $soli->diff($asig);
						$q = $dif_solicitar_asignar->format($formatoFecha2);
						$uno = substr($q, 0, 1);
						$q = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha3) : $dif_solicitar_asignar->format($formatoFecha2);
						$uno = substr($q, 0, 1);
						$q = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha4) : $q;
					}

					if($asig == null || $hosp == null){
						$p = "--";
					}else{
						$dif_asignar_hospitalizar = $asig->diff($hosp);
						$p = $dif_asignar_hospitalizar->format($formatoFecha2);
						$uno = substr($p, 0, 1);
						$p = ($uno == "0") ? $dif_asignar_hospitalizar->format($formatoFecha3) : $dif_asignar_hospitalizar->format($formatoFecha2);
						$uno = substr($p, 0, 1);
						$p = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha4) : $p;
					}

					$caso = $d->caso;
					//Identificar especialidades del dia
					$especialidades =  EvolucionEspecialidad::select("fecha_termino","comentario","nombre")
						->join("especialidades as e","e.id","=","t_evolucion_especialidades.id_especialidad")
						->where(function($query) use ($fecha_fin, $caso) {
							$query->where("id_caso", $caso)
							->where("fecha","<=",$fecha_fin)
							->where(function($query) use ($fecha_fin) {
								$query->Where("fecha_termino",">",$fecha_fin)
								->orWhereNull("fecha_termino");
							})
							->where(function($query) {
								$query->where("comentario","<>","correccion")
								->orWhereNull("comentario")   ;
							});
						})	
						->orderBy("t_evolucion_especialidades.id", "desc")
						->get();

					$lista_especialidades = "";
					foreach ($especialidades as $key => $esp) {
						if ($key != 0) {
							$lista_especialidades .= ", ";
						}
						$lista_especialidades .=$esp->nombre;
					}

					// Log::info($especialidad);

					$diagnosticos = HistorialDiagnostico::where('caso',$caso)
						->where(function($query) {
							$query->Where("id_tipo_diagnostico",4)//Con esto indicamos que es el de ingreso
							->orWhereNull("id_tipo_diagnostico");
						})
						->whereNotNull("diagnostico") 
						->orderBy('fecha','desc')
						->get();
					
					$lista_diagnosticos = "";
					foreach ($diagnosticos as $key => $diagn) {
						if($key > 0){
							$lista_diagnosticos .= ".<br> ".ucwords($diagn->diagnostico);
						}else{
							$lista_diagnosticos = ucwords($diagn->diagnostico);
						}
					}

					// $traslado = ThistorialOcupaciones::where('caso',$d->caso)->whereBetween("fecha",[$inicio,$fin])->where('motivo','=','traslado interno')->orderBy("fecha","desc")->first();
					
					$lista_pacientes_hospitalizados [] =[
						"origen" => Procedencia::select('nombre')->where('id',$d->procedencia)->first()->nombre,
						"servicioDestino" => $d->nombre_servicio,
						"cama" => $d->id_cama,
						"especialidad" => $lista_especialidades,
						"nombrePaciente" => $nombreCompleto,
						"sexo" => Paciente::homologarSexo($d->sexo),
						"edad" => ($d->fecha_nacimiento) ? Carbon::parse($d->fecha_nacimiento)->age : '',
						"rut" => $rut,
						"diagnostico" => $lista_diagnosticos,
						"observacion" => $observacion,
						"fechaSolicitud" => Carbon::parse($fecha_solicitud)->format('d-m-Y'),
						"horaSolicitud" => Carbon::parse($fecha_solicitud)->format('H:i'),
						"fechaAsignacion" => Carbon::parse($fecha_asignacion)->format('d-m-Y'),
						"horaAsignacion" => Carbon::parse($fecha_asignacion)->format('H:i'),
						"dif_solicitar_asignar" => $q,
						"fechaHospitalizacion" => Carbon::parse($fecha_hospitalizacion)->format('d-m-Y'),
						"horaHospitalizacion" => Carbon::parse($fecha_hospitalizacion)->format('H:i'),
						"dif_asignar_hospitalizar" => $p
						// "fechaSolicitud" => $fecha_solicitud,
						// "fechaAsignacion" => $fecha_asignacion,
						// "dif_solicitar_asignar" => $q,
						// "fechaHospitalizacion" => $fecha_hospitalizacion,
						// "dif_asignar_hospitalizar" => $p
					];
				}else{
					$listaweones .="(RECHAZADO)";
				}
			}
		}
		//Log::info($hospitalizados);
		Log::info($listaweones);
		Log::info("------fin PACIENTES HOSPITALIZADOS------");
		return $lista_pacientes_hospitalizados;
	}

	public static function pacientes_trasladados($inicio, $fin){
		$formatoFecha = "d-m-Y H:i:s";
		$formatoFecha2 = "%h horas %i minutos %s segundos";
		$formatoFecha3 = "%i minutos %s segundos";
		$formatoFecha4 = "%s segundos";
		$establecimiento = Auth::user()->establecimiento;
		
		//Se busca informacion de los pacientes que llevaron a cabo traslados en el sistema en ese turno especifico
		$traslados = DB::select("SELECT tho.caso as caso,
			tho.fecha_ingreso_real as hospitalizacion, p.rut, p.dv, p.nombre, p.apellido_paterno, p.apellido_materno, p.fecha_nacimiento, p.sexo, uee.alias as nombre_servicio,
			s.nombre as nombre_sala, c.id_cama, ca.procedencia, tho.motivo, tho.fecha_liberacion, tho.fecha, tho.id
			from t_historial_ocupaciones as tho
			join camas as c on tho.cama = c.id
			join salas as s on c.sala = s.id
			join unidades_en_establecimientos as uee on s.establecimiento = uee.id
			join casos as ca on tho.caso = ca.id
			join pacientes as p on ca.paciente = p.id
			where tho.fecha_liberacion >= '$inicio' and tho.fecha_liberacion <= '$fin'
			and tho.motivo = 'traslado interno'
			and uee.created_at <=  '$fin'
			-- and tho.fecha_ingreso_real is not null  --> consultar por un tema de confirmacion de traslado interno que es mas que nada ingresar la fecha ingreso real(hospitalizar al pj)
			and uee.establecimiento = $establecimiento
			");
			
		$lista_traslados_directos = [];
		foreach ($traslados as $key => $d) {
			

			//Se busca informacion de donde fue a parar el paciente luego del traslado
			$histo_destino = ThistorialOcupaciones::join("camas as c","c.id","t_historial_ocupaciones.cama")
				->join("salas as s","s.id","c.sala")
				->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
				->select("c.id_cama as nombre_cama", "s.nombre as nombre_sala","u.alias as nombre_unidad","t_historial_ocupaciones.id")
				->where("t_historial_ocupaciones.id",">",$d->id)
				->where("t_historial_ocupaciones.caso",$d->caso)
				//->where("t_historial_ocupaciones.motivo","<>","correccion cama")
				->orderBy("t_historial_ocupaciones.id","asc")
				->first();

			/* Log::info("origen: ");
			Log::info($d->id);
			Log::info("destino: ");
			Log::info($histo_destino->nombre_unidad); */
			//Se comprueba que el destino del paciente sea distinto del origen, para comprobar que el paciente salio de la unidad
			if (isset($histo_destino->nombre_unidad) && $histo_destino->nombre_unidad != $d->nombre_servicio) {
				
				$ubicacion_posterior_unidad = $histo_destino->nombre_unidad;
				$ubicacion_posterior_cama = $histo_destino->nombre_sala." - ".$histo_destino->nombre_cama;

				//Especialidad
				$caso = $d->caso;
				//Identificar especialidades del dia
				$especialidades =  EvolucionEspecialidad::select("fecha_termino","comentario","nombre")
					->join("especialidades as e","e.id","=","t_evolucion_especialidades.id_especialidad")
					->where(function($query) use ($fin, $caso) {
						$query->where("id_caso", $caso)
						->where("fecha","<=",$fin)
						->where(function($query) use ($fin) {
							$query->Where("fecha_termino",">",$fin)
							->orWhereNull("fecha_termino");
						})
						->where(function($query) {
							$query->where("comentario","<>","correccion")
							->orWhereNull("comentario")   ;
						});
					})	
					->orderBy("t_evolucion_especialidades.id", "desc")
					->get();

				$lista_especialidades = "";
				foreach ($especialidades as $key => $esp) {
					if ($key != 0) {
						$lista_especialidades .= ", ";
					}
					$lista_especialidades .=$esp->nombre;
				}

				//Nombre y rut
				$nombreCompleto = $d->nombre . " ".$d->apellido_paterno ." ".$d->apellido_materno;
				$dv = ($d->dv == 10) ? 'k' : $d->dv;
				$rut = $d->rut ."-".$dv;

				//Informacion para fecha y hora de hospitalizacion
				if($d->hospitalizacion == null){
					$Fhosp = ThistorialOcupaciones::where("caso", $d->caso)->whereNotNull("fecha_ingreso_real")
						->whereRaw("fecha_ingreso_real >= '$inicio' and fecha_ingreso_real <= '$fin'")
						->first();
					$fecha_hospitalizacion = ($Fhosp) ? Carbon::parse($Fhosp->fecha_ingreso_real)->format($formatoFecha) : null;
				}else{
					$fecha_hospitalizacion = ($d->hospitalizacion) ? Carbon::parse($d->hospitalizacion)->format($formatoFecha) : null;
				}

				$diagnosticos = HistorialDiagnostico::where('caso',$caso)
					->where(function($query) {
						$query->Where("id_tipo_diagnostico",4)//Con esto indicamos que es el de ingreso
						->orWhereNull("id_tipo_diagnostico");
					})
					->whereNotNull("diagnostico") 
					->orderBy('fecha','desc')
					->get();
				
				$lista_diagnosticos = "";
				foreach ($diagnosticos as $key => $diagn) {
					if($key > 0){
						$lista_diagnosticos .= ".<br> ".ucwords($diagn->diagnostico);
					}else{
						$lista_diagnosticos = ucwords($diagn->diagnostico);
					}
				}


				$fechaSolicitud = ListaEspera::select('fecha')->where('caso',$d->caso)->first();
				$fecha_solicitud = ($fechaSolicitud['fecha']) ? Carbon::parse($fechaSolicitud['fecha'])->format($formatoFecha) : null;

				$soli = Carbon::parse($fecha_solicitud);
				$soli = ($soli) ? $soli : null;

				$fechaAsignacion = ListaTransito::select('fecha')->where('caso',$d->caso)->first();
				$fecha_asignacion = ($fechaAsignacion['fecha']) ? Carbon::parse($fechaAsignacion['fecha'])->format($formatoFecha) : null;

				$asig = Carbon::parse($fecha_asignacion);
				$asig = ($asig) ? $asig : null;

				$hosp = Carbon::parse($fecha_hospitalizacion);
				$hosp = ($hosp) ? $hosp : null;

				//Fecha y hora de solicitud
				if($fecha_solicitud == null){
					$fechaSolicitud = Caso::select('fecha_ingreso2')->find($d->caso);
					$fecha_solicitud = ($fechaSolicitud['fecha_ingreso2']) ? Carbon::parse($fechaSolicitud['fecha_ingreso2'])->format($formatoFecha) : '--';
				}

				if($soli == null || $asig == null){
					$q = "--";
				}else{
					//diferencia entre solicitud y asignacion dentro de esto
					$dif_solicitar_asignar = $soli->diff($asig);
					$q = $dif_solicitar_asignar->format($formatoFecha2);
					$uno = substr($q, 0, 1);
					$q = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha3) : $dif_solicitar_asignar->format($formatoFecha2);
					$uno = substr($q, 0, 1);
					$q = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha4) : $q;
				}

				//Fecha de hospitalizacion
				if($asig == null || $hosp == null){
					$p = "--";
				}else{
					//diferencia entre asignacion y hospitalizacion dentro de esto
					$dif_asignar_hospitalizar = $asig->diff($hosp);
					$p = $dif_asignar_hospitalizar->format($formatoFecha2);
					$uno = substr($p, 0, 1);
					$p = ($uno == "0") ? $dif_asignar_hospitalizar->format($formatoFecha3) : $dif_asignar_hospitalizar->format($formatoFecha2);
					$uno = substr($p, 0, 1);
					$p = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha4) : $p;
				}
				
				

				//nuevos datos por traslado.
				$fecha_liberacion = Carbon::parse($d->fecha_liberacion);
				$historial_actual = DB::table('t_historial_ocupaciones as t')
				->select('u.alias as nombre_servicio','s.nombre','ca.id_cama','t.id','t.fecha')
				->join('casos as c','c.id','=','t.caso')
				->join('camas as ca','ca.id','=','t.cama')
				->join('salas as s','s.id','=','ca.sala')
				->join('unidades_en_establecimientos as u','u.id','=','s.establecimiento')
				->where('t.caso',$d->caso)
				->whereBetween('t.fecha',[$fecha_liberacion->subSeconds(3), $fecha_liberacion->addSeconds(3)])
				->first();
					
				$solicitud_traslado = DB::table('solicitud_traslado_interno')
				->where('id_historial_ocupaciones',$d->id)
				->first();
					
				$observacion = ($solicitud_traslado) ? 'Solicitud traslado interno' : 'Traslado interno';

				$lista_traslados_directos [] = [
					"id_historial" => $historial_actual->id,
					//"origen" => Procedencia::select('nombre')->where('id',$d->procedencia)->first()->nombre,
					"origen" => $d->nombre_servicio,
					"servicioDestino" => $ubicacion_posterior_unidad,
					"cama" => $ubicacion_posterior_cama,
					"especialidad" => $lista_especialidades,
					"nombrePaciente" => $nombreCompleto,
					"sexo" => Paciente::homologarSexo($d->sexo),
					"edad" => ($d->fecha_nacimiento) ? Carbon::parse($d->fecha_nacimiento)->age : '',
					"rut" => $rut,
					"diagnostico" => $lista_diagnosticos,
					"observacion" => $observacion,
					"fechaSolicitud" => Carbon::parse($fecha_solicitud)->format('d-m-Y'),
					"horaSolicitud" => Carbon::parse($fecha_solicitud)->format('H:i'),
					"fechaAsignacion" => Carbon::parse($fecha_asignacion)->format('d-m-Y'),
					"horaAsignacion" => Carbon::parse($fecha_asignacion)->format('H:i'),
					"dif_solicitar_asignar" => $q,
					"fechaHospitalizacion" => Carbon::parse($fecha_hospitalizacion)->format('d-m-Y'),
					"horaHospitalizacion" => Carbon::parse($fecha_hospitalizacion)->format('H:i'),
					"dif_asignar_hospitalizar" => $p
				];
			}
		}

		return $lista_traslados_directos;
	}

	public static function pacientes_lista_transito($inicio, $fin){

		$formatoFecha = "d-m-Y H:i:s";
		$formatoFecha2 = "%m meses %d dias %h horas %i minutos %s segundos";
		$formatoFecha3 = "%i minutos %s segundos";
		$formatoFecha4 = "%s segundos";
		$establecimiento = Auth::user()->establecimiento;

		//Buscar pacientes que pasaron el dia x
		//Comprobar que el paciente x haya sido ingresado el dia x a su cama
		/* Log::info("------inicio PACIENTES ESPERA DE HOSPITALIZACION------"); */

		$transitos = DB::select(DB::raw("SELECT
		t.fecha_ingreso_real as hospitalizacion,
		p.rut, p.dv, p.nombre, p.apellido_paterno,
		p.apellido_materno, p.fecha_nacimiento, p.sexo,
		u.alias as nombre_servicio, p.id as id_paciente,
		s.nombre as nombre_sala, ca.id_cama, 
		c.id as idcaso, 
		c.procedencia, 
		t.motivo, t.id as id_historial,
		t.fecha
		from  t_historial_ocupaciones t
		inner join casos as c on c.id = t.caso
		inner join camas as ca on ca.id = t.cama
		inner join salas as s on s.id = ca.sala
		inner join unidades_en_establecimientos as u on u.id = s.establecimiento
		inner join pacientes as p on p.id = c.paciente
		where 
			t.fecha >= '$inicio'
			and t.fecha <= '$fin' 
			and u.establecimiento = $establecimiento
			and (t.fecha_ingreso_real > '$fin' or t.fecha_ingreso_real is null)
		"));

		$lista_transito = [];
		$lista_otros_weones = "";
		foreach ($transitos as $key => $t) {
			
			$histo_anterior = ThistorialOcupaciones::join("camas as c","c.id","t_historial_ocupaciones.cama")
				->join("salas as s","s.id","c.sala")
				->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
				->select("c.id_cama as nombre_cama", "s.nombre as nombre_sala","u.alias as nombre_unidad","t_historial_ocupaciones.motivo")
				->where("t_historial_ocupaciones.id","<",$t->id_historial)
				->where("t_historial_ocupaciones.caso",$t->idcaso)
				->orderBy("t_historial_ocupaciones.id","desc")
				->first();

			
			$lista_otros_weones .= ", (".$t->id_historial.", ".$t->idcaso.")";
			if ((isset($histo_anterior->nombre_unidad) && ($histo_anterior->nombre_unidad != $t->nombre_servicio)) || !$histo_anterior) {
				$nombreCompleto = $t->nombre . " ".$t->apellido_paterno ." ".$t->apellido_materno;
				$dv = ($t->dv == 10) ? 'k' : $t->dv;
				$rut = $t->rut ."-".$dv;
				$lista_otros_weones .= "(ACEPTADO)";
				
				//Especialidad
				$caso = $t->idcaso;
				//Identificar especialidades del dia
				$especialidades =  EvolucionEspecialidad::select("fecha_termino","comentario","nombre")
					->join("especialidades as e","e.id","=","t_evolucion_especialidades.id_especialidad")
					->where(function($query) use ($fin, $caso) {
						$query->where("id_caso", $caso)
						->where("fecha","<=",$fin)
						->where(function($query) use ($fin) {
							$query->Where("fecha_termino",">",$fin)
							->orWhereNull("fecha_termino");
						})
						->where(function($query) {
							$query->where("comentario","<>","correccion")
							->orWhereNull("comentario")   ;
						});
					})	
					->orderBy("t_evolucion_especialidades.id", "desc")
					->get();

				$lista_especialidades = "";
				foreach ($especialidades as $key => $esp) {
					if ($key != 0) {
						$lista_especialidades .= ", ";
					}
					$lista_especialidades .=$esp->nombre;
				}

				$cama = DB::table('t_historial_ocupaciones')
				->join("camas","camas.id","=","t_historial_ocupaciones.cama")
				->join("salas","camas.sala","=","salas.id")
				->join("unidades_en_establecimientos AS uee","salas.establecimiento","=","uee.id")
				->where("caso", "=", $t->idcaso)
				->where("t_historial_ocupaciones.id", $t->id_historial)
				// ->whereNull("fecha_liberacion")
				->select("cama","id_cama","salas.nombre as sala_nombre","uee.alias as nombre_unidad","t_historial_ocupaciones.fecha_ingreso_real as fecha_hosp")
				->first();

				$diagnosticos = HistorialDiagnostico::where('caso',$caso)
					->where(function($query) {
						$query->Where("id_tipo_diagnostico",4)//Con esto indicamos que es el de ingreso
						->orWhereNull("id_tipo_diagnostico");
					})
					->whereNotNull("diagnostico") 
					->orderBy('fecha','desc')
					->get();
				
				$lista_diagnosticos = "";
				foreach ($diagnosticos as $key => $diagn) {
					if($key > 0){
						$lista_diagnosticos .= ".<br> ".ucwords($diagn->diagnostico);
					}else{
						$lista_diagnosticos = ucwords($diagn->diagnostico);
					}
				}

				$fechaSolicitud = ListaEspera::select('fecha')->where('caso',$t->idcaso)->first();
				$fecha_solicitud = ($fechaSolicitud['fecha']) ? Carbon::parse($fechaSolicitud['fecha'])->format($formatoFecha) : '--';
				
				if($fecha_solicitud == '--'){
					$fechaSolicitud = Caso::select('fecha_ingreso2')->find($t->idcaso);
					$fecha_solicitud = ($fechaSolicitud['fecha_ingreso2']) ? Carbon::parse($fechaSolicitud['fecha_ingreso2'])->format($formatoFecha) : '--';
				}

				$fecha_asignacion = Carbon::parse($t->fecha);

				$soli = Carbon::parse($fecha_solicitud);
				$soli = ($soli) ? $soli : null;

				if($soli == null || $fecha_asignacion == null){
					$q = "--";
				}else{
					$dif_solicitar_asignar = $soli->diff($fecha_asignacion);
					$q = $dif_solicitar_asignar->format($formatoFecha2);
					$uno = substr($q, 0, 1);
					$q = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha3) : $dif_solicitar_asignar->format($formatoFecha2);
					$uno = substr($q, 0, 1);
					$q = ($uno == "0") ? $dif_solicitar_asignar->format($formatoFecha4) : $q;
				}

				$observacion = "Ingreso";
				/* Log::info($histo_anterior); */
				if ($cama->fecha_hosp == null && $t->hospitalizacion != null) {
					$observacion = "Suspendido";
				}else if(isset($histo_anterior->motivo) && $histo_anterior->motivo == 'corrección cama'){
					$observacion = 'Correccón de cama';
				}else if(isset($histo_anterior->motivo) && $histo_anterior->motivo == 'traslado interno'){
					$observacion = "Traslado desde: <br><strong>".$histo_anterior->nombre_unidad." - ".$histo_anterior->nombre_sala." - ".$histo_anterior->nombre_cama."</strong>";
				}

				$lista_transito [] = [
					"origen" => Procedencia::select('nombre')->where('id', $t->procedencia)->first()->nombre,
					"servicioDestino" => $cama->nombre_unidad,
					"cama" => $cama->sala_nombre ." - ".$cama->id_cama,
					"especialidad" => $lista_especialidades,
					"nombrePaciente" => $nombreCompleto,
					"sexo" => Paciente::homologarSexo($t->sexo),
					"edad" => ($t->fecha_nacimiento) ? Carbon::parse($t->fecha_nacimiento)->age : '',
					"rut" => $rut,
					"diagnostico" => $lista_diagnosticos,
					"observacion" => $observacion,
					"fechaSolicitud" => Carbon::parse($fecha_solicitud)->format('d-m-Y'),
					"horaSolicitud" => Carbon::parse($fecha_solicitud)->format('H:i'),
					"fechaAsignacion" => Carbon::parse($fecha_asignacion)->format('d-m-Y'),
					"horaAsignacion" => Carbon::parse($fecha_asignacion)->format('H:i'),
					"dif_solicitar_asignar" => $q,
					"fechaHospitalizacion" => '',
					"horaHospitalizacion" => '',
					"dif_asignar_hospitalizar" => ''
				];
			}else{
				$lista_otros_weones .= "(RECHAZADO)";
			}

			

			

			
		}
		Log::info($lista_otros_weones);
		Log::info("------fin PACIENTES ESPERA DE HOSPITALIZACION------");
		return $lista_transito;
	}

	public static function validacionHospitalizacion($idCaso){
		$mensaje = "";
		try {
			//validar si el paciente esta en lista de espera
			$listaEspera = ListaEspera::casoEnListaEspera($idCaso);
			if($listaEspera){
				$mensaje = "El paciente se encuentra en lista de espera de cama.";
			}

			//validar si el paciente esta en pre alta
			$salida_urgencia = PreAlta::casoEnPreAlta($idCaso);
			if($salida_urgencia){
				$mensaje = "El paciente se encuentra en pre alta.";
			}
			
			//validar si el paciente se encuentra hospitalizado
			$hospitalizado = THistorialOcupaciones::casoHospitalizado($idCaso);
			if($hospitalizado){
				$mensaje = "El paciente ya se encuentra hospitalizado.";
			}
	
			//validar si el paciente fue egresado
			$egresado = Caso::casoEgresado($idCaso);
			if($egresado){
				$mensaje = "El paciente ya ha sido egresado.";
			}
	
			return $mensaje;
			
		} catch (Exception $ex) {
			Log::info($ex);
			return "Ha ocurrido un error.";
		}
	}
}

}
