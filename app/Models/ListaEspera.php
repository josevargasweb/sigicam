<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use View;
use Auth;
use TipoUsuario;
use Log;
use Carbon\Carbon;
use App\Models\Paciente;
use Session;
use DateTime;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Str;

class ListaEspera extends Model implements Auditable{
	use \OwenIt\Auditing\Auditable;

	protected $table = "lista_espera";

	protected $auditInclude = [

	];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;


	public function casos(){
		return $this->belongsTo("App\Models\Caso", "caso", "id");
	}

	public static function cantidadPacientes(){
		return DB::table('lista_espera')->select(DB::raw("count(*) as total_espera"))->whereNull("fecha_termino")->get();

	}

	public static function existeEnlistaPorCaso($idCaso){
		$lista=self::where("caso", "=", $idCaso)->whereNull("fecha_termino")->first();
		if(is_null($lista)){ return false;}
		return true;
	}

	public static function pacienteEnListaEspera($idPaciente){
		$lista=DB::table("lista_espera as l")->join("casos as c", "c.id", "=", "l.caso")->where("c.paciente", "=", $idPaciente)
		->whereNull("l.fecha_termino")->first();
		if(is_null($lista)){ return false;}
		return true;
	}

	public static function obtenerListaEspera($idEst,$procedencia){
		
		$formatoFecha = "Y-m-d H:i:s";
		$inicioLabel = "<label hidden>";
		$finLabel = "</label>";

		$datos = Array();
		$datos = ListaEspera::dataListaEspera($idEst,$procedencia);
		$response = []; 

		foreach ($datos as $dato) {
			$hoy = Carbon::now();
			$fechaCarbon = Carbon::parse($dato["fecha_solicitud"]);
			$diff = $hoy->diffInHours($fechaCarbon);

			$color ="";
			if($hoy >= $fechaCarbon ){
				if($diff >= 12){
					$color = "background: #d14d33 !important;     color: cornsilk;";
				}else if($diff >= 6 && $diff < 12){
					$color = "background: rgb(186,186,57) !important;     color: cornsilk;";
				}else if($diff >= 1 && $diff < 6){
					$color = "background: #41a643 !important;     color: cornsilk;";
				}
			}

			$categorizacion = "" ;
			$infoCategorizacion = "";
			if($dato["categorizacion"] != "Sin categorizar"){
				$categorizacion = $infoCategorizacion."<center><button class='btn' onclick='modalRiesgoDepen(".$dato["id_caso"].")'><strong style='color:black;'>".$dato["categorizacion"]."</strong></button></center>" ;
			}else{
				$categorizacion = $dato["categorizacion"];
			}
			
			$opciones=View::make("Urgencia/OpcionesLista", ["idCaso" => $dato["id_caso"],
															"idLista" => $dato["id_lista"],
															"idPaciente"=> $dato["id_paciente"],
															"color" => $color, "diff" => $diff,
															"ficha"=>$dato["ficha_clinica"],
															"nombreCompleto"=>$dato["nombre_completo"],
															"ubicacion"=>$dato["ubicacion"],
															"sexo" => $dato["sexo"]
															])->render();

			//Esto calcula el tiempo de espera que lleva el paciente
			$espera = "";
			$orden = Carbon::parse($dato["fecha_solicitud"])->format($formatoFecha);
			if($fechaCarbon->toDateString() < $hoy->toDateString()){
				$espera = $inicioLabel.$orden.$finLabel.$dato["fecha_solicitud"]." <br> <strong style='color:black;'>(<label >".$diff."</label> Horas en espera)</strong> <br>  <div > Usuario solicita: <br> <strong style='color:black;'>".$dato["usuario_solicita"]."</strong></div>";
			}else if ($fechaCarbon->toDateString() == $hoy->toDateString()){
				$nuevo1 = $hoy->format("H:i:s");
				$nuevo2 = $fechaCarbon->format("H:i:s");
				if($nuevo1 < $nuevo2 || $diff == 0){
					$espera =$inicioLabel.$orden.$finLabel.$dato["fecha_solicitud"]."<label hidden>0</label> <br>  <div >Usuario solicita: <br> <strong style='color:black;'>".$dato["usuario_solicita"]."</strong></div>" ;// tiene que ser diferencia negativa para mostrarla al final
				}else{
					$espera = $inicioLabel.$orden.$finLabel.$dato["fecha_solicitud"]." <br> <strong style='color:black;'>(<label >".$diff."</label>Horas en espera)</strong> <br> <div '>Usuario solicita: <br> <strong style='color:black;'>".$dato["usuario_solicita"]."</strong></div>";
				}
			}else{
				$espera = $inicioLabel.$orden.$finLabel.$dato["fecha_solicitud"]."<label hidden>0</label> <br> <div >Usuario solicita: <br> <strong style='color:black;'>".$dato["usuario_solicita"]."</strong></div>";
			}		
			//fecha nacimiento
			$fecha_nac = ($dato["fecha_nacimiento"] == "Sin Especificar")?$dato["fecha_nacimiento"]:$dato["fecha_nacimiento"]." (".$dato["edad"].")"; 

			$response[]=[
				Session::get("usuario")->tipo === \App\util\TipoUsuario::VISUALIZADOR ? "" : $opciones, 
				$dato["rut"], 
				$dato["nombre_completo"], 
				$fecha_nac, 
				$dato["diagnostico"]." <strong style='color:black;'>Comentario: " .$dato["comentario_d"]."</strong>", 
				$espera,
				$dato["fecha_indicacion_hospitalizacion"], 
				$dato["procedencia"],
				$dato["motivo_hospitalizacion"], 
				"<strong style='color:black;'>".$dato["area_funcional_cargo"]."</strong>-".$dato["servicio_cargo"],
				"<strong style='color:black;'>".$dato["nombre_area_funcional"]."<strong>-".$dato["nombre_unidad"], 
				$dato["comentario_lista"], 
				$dato["dau"],
				$categorizacion];
			

		// FIX cuando la lista queda abierta y ya se creo un nuevo caso,
		$paciente = Paciente::find($dato["id_paciente"]);
		$idPaciente = $paciente->id;
		$lista=DB::table( DB::raw("(SELECT l.id as lis,c.id as cas FROM casos as c,lista_espera as l where c.id=l.caso and c.paciente=$idPaciente and l.fecha_termino is null) as rea"))->get();

		$casosVar=DB::table( DB::raw("(select count(*) as contador from pacientes as p,casos as c where p.id=$idPaciente and c.fecha_termino is null and c.paciente=p.id) as re"))->first();

			if(!is_null($lista)){
				foreach ($lista as $lis){
				if($casosVar->contador>=2){
						$caso2 = Caso::find($lis->cas); // id caso de la lista
						$caso2->fecha_termino = date($formatoFecha);
						$caso2->motivo_termino = "alta";
						$caso2->save();

						$listas=ListaEspera::find($lis->lis);  // se vcierra la lista
						$listas->fecha_termino=date($formatoFecha);
						$listas->motivo_salida="hospitalización";
						$listas->save();
					}
				}
			}
		}
		return $response;
	}

	public static function dataListaEspera($idEst, $proce){
		$proce = ($proce == '' || $proce == 'x') ? '' : $proce;
		$informacion = [];
		$nombreAreaFuncional="";
		$datos=DB::table("lista_espera as l")->join("casos as c", "c.id", "=", "l.caso")
		->join("pacientes as p", "p.id", "=", "c.paciente")
		->join("usuarios as u", "l.usuario", "=", "u.id")
		->whereNull("l.fecha_termino")
		->where("u.establecimiento", $idEst)
		->select("p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			"p.rut as rut",
			"p.dv as dv",
			"p.fecha_nacimiento",
			"p.sexo",
			"l.fecha as fecha",
			"l.comentario_lista as comentario_lista",
			"c.indicacion_hospitalizacion",
			"c.id as idCaso",
			"c.procedencia",
			"c.detalle_procedencia",
			"c.dau",
			"l.id as idLista",
			"l.ubicacion",
			"c.id_unidad",
			"p.id as id_paciente",
			"c.ficha_clinica",
			"c.fecha_ingreso2 as solicitud",
			"c.id_complejidad_area_funcional",
			"c.motivo_hospitalizacion",
			"u.nombres",
			"u.apellido_paterno",
			"u.apellido_materno")
			->when($proce != "", function($query) use ($proce){
				return $query->where('procedencia','=',$proce);
			})->get();

		foreach($datos as $dato){

			$id_caso = $dato->idCaso;
			$id_lista = $dato->idLista;
			$id_paciente = $dato->id_paciente;
			$ficha_clinica = $dato->ficha_clinica;
			$dau = ($dato->dau) ? $dato->dau : "-";
			$ubicacion = $dato->ubicacion;
			$nombre = ($dato->nombre) ? $dato->nombre : '';
			$apellido=$dato->apellidoP." ".$dato->apellidoM;
			$nombre_completo = Str::upper($nombre) ." ". Str::upper($apellido);
			$dv=($dato->dv == 10) ? "K" : $dato->dv;
			$rut=(empty($dato->rut)) ? "-" : $dato->rut."-".$dv;
			$fecha=date("d-m-Y H:i", strtotime($dato->solicitud));
			$sexo = $dato->sexo;
			$indicacion_hospitalizacion = "";
			if($dato->indicacion_hospitalizacion){
				$indicacion_hospitalizacion = date("d-m-Y H:i", strtotime($dato->indicacion_hospitalizacion));
			}

			$servicio_a_cargo = '';
			$area_funcional_a_cargo = '';

			$diag = HistorialDiagnostico::where("caso","=",$id_caso)->orderby("fecha","desc")->select("diagnostico", "comentario")->first();
			$diagnostico = $diag->diagnostico;
			$comentario_diagnostico = $diag->comentario;
			if($comentario_diagnostico == ""){
				$comentario_diagnostico = "&nbsp";
			}
			$evolucioncaso = EvolucionCaso::where("caso","=",$id_caso)->leftjoin("riesgos as r", "t_evolucion_casos.riesgo_id", "=", "r.id")->orderby("fecha","desc")->select("categoria","urgencia")->first();

			/*Area  y servicio a cargo by nolazko*/
			$evolucion = EvolucionCaso::where("caso", $id_caso)
			->orderBy("fecha", "desc")
			->first();
			if($evolucion->id_complejidad_area_funcional != null){
					$servicio_a_cargo = $evolucion->complejidad_area_funcional->servicios->nombre_servicio;
					$area_funcional_a_cargo = $evolucion->complejidad_area_funcional->area->nombre;
			}

			$categoria = $evolucioncaso->categoria;
			$categoria = ($categoria) ? $categoria : 'Sin categorizar';

			$nombre_unidad ="";
			if($dato->id_unidad != null){
				$nombre = DB::table('unidades_en_establecimientos')->select('alias')->where('id','=',$dato->id_unidad)->first();
				$nombre_unidad = $nombre->alias;
			}

			if($dato->id_complejidad_area_funcional	!= null){
				$areafuncional = DB::table('complejidad_area_funcional')
					->join("area_funcional","area_funcional.id_area_funcional","=","complejidad_area_funcional.id_area_funcional")
					->where("id_complejidad_area_funcional","=",$dato->id_complejidad_area_funcional)
					->first();
				$nombreAreaFuncional = $areafuncional->nombre;
			}else{
				$nombreAreaFuncional = '';
			}

			$fecha_nacimiento = $dato->fecha_nacimiento;
			$fecha1 = new DateTime($fecha_nacimiento);
			$fecha2 = new DateTime();
			$fechaF = $fecha1->diff($fecha2);
			$diferencia = '';

			$fecha_nacimiento = ($fecha_nacimiento) ? date("d-m-Y", strtotime($dato->fecha_nacimiento)) : 'Sin Especificar';

			if($fechaF->y == 0){
				$diferencia = $fechaF->format('%m meses %a dias');
			}else{
				$diferencia = $fechaF->format('%y años %m meses');
			}

			$procedencia = DB::table("procedencias")
							->where("id",$dato->procedencia)
							->first()->nombre;

			if($dato->procedencia == 2 || $dato->procedencia == 3 || $dato->procedencia == 4 || $dato->procedencia == 7){
				$procedencia .= "<br><strong style='color:#000000'> Detalle: ".$dato->detalle_procedencia."</strong>";
			}

			$comentario_lista = $dato->comentario_lista;
			if($comentario_lista == null){
				$comentario_lista = "-";
			}else{
				$comentario_lista = $dato->comentario_lista;
			}

			$unidad_caso = Caso::find($id_caso, 'id_unidad');
			if($unidad_caso->id_unidad != null){
				$area_unidad = UnidadEnEstablecimiento::find($unidad_caso->id_unidad, 'id_area_funcional');
				$nombreAreaFuncional = AreaFuncional::find($area_unidad->id_area_funcional,'nombre');
				$nombreAreaFuncional = $nombreAreaFuncional->nombre;
			}

			$motivo_hospitalizacion = ($dato->motivo_hospitalizacion) ? $dato->motivo_hospitalizacion : "-";
			$usuario_solicita = $dato->nombres . " " . $dato->apellido_paterno . " " . $dato->apellido_materno;
			$informacion[] = [
				"id_caso" => $id_caso,
				"id_lista" => $id_lista,
				"id_paciente" => $id_paciente,
				"ficha_clinica" => $ficha_clinica,
				"ubicacion" => $ubicacion,
				"rut" => $rut,
				"nombre_completo" => $nombre_completo,
				"fecha_nacimiento" => $fecha_nacimiento,
				"edad" => $diferencia,
				"diagnostico" => $diagnostico,
				"comentario_d" => $comentario_diagnostico,
				"fecha_solicitud" => $fecha,
				"fecha_indicacion_hospitalizacion" => $indicacion_hospitalizacion,
				"procedencia" => $procedencia,
				"area_funcional_cargo" => $area_funcional_a_cargo,
				"servicio_cargo" => $servicio_a_cargo,
				"nombre_area_funcional" => $nombreAreaFuncional,
				"nombre_unidad" => $nombre_unidad,
				"comentario_lista" => $comentario_lista,
				"dau" => $dau,
				"categorizacion" => $categoria,
				"sexo" => $sexo,
				"motivo_hospitalizacion" => $motivo_hospitalizacion,
				"usuario_solicita" => $usuario_solicita
			];
		}
		return $informacion;
	}

	public static function casoEnListaEspera($idCaso){
		return ListaEspera::where('caso',$idCaso)->whereNull('fecha_termino')->first();
	}

	public static function pacientesFecha($inicio, $fin, $origen, $adultoOpediatria){
		if($fin){
			$fecha1 = "{$inicio} 20:00:00";
			$fecha2 = "{$fin} 07:59:59";
		}else{
			$fecha1 = "{$inicio} 08:00:00";
			$fecha2 = "{$inicio} 19:59:59";
		}

		$datos=DB::table("lista_espera as l")
			->select("p.fecha_nacimiento")
			->join("casos as c", "c.id", "=", "l.caso")
			->join("pacientes as p", "p.id", "=", "c.paciente")
			->join("usuarios as u", "l.usuario", "=", "u.id")
			->whereBetween("l.fecha",[$fecha1,$fecha2])
			->where("c.procedencia",$origen)
			// ->whereNull("l.fecha_termino") // (en base al rango de fecha) descomentado: esta en la lista. comentado: estuvo o esta en la lista
			->where("u.establecimiento", Auth::user()->establecimiento)
			->get();

		$contador = 0;
		foreach ($datos as $dato) {
			if($dato->fecha_nacimiento){
				$edad = Carbon::parse($dato->fecha_nacimiento)->age;
				if($adultoOpediatria == 'ADULTO' && $edad >= 15){
					$contador++;
				}else if($adultoOpediatria == 'PEDIATRIA' && $edad <= 15){
					$contador++;
				}
			}
		}
		return $contador;
	}

	public static function cantidadPacientesTurno($inicio, $fin){
		if($fin){
			$fecha1 = "{$inicio} 20:00:00";
			$fecha2 = "{$fin} 07:59:59";
		}else{
			$fecha1 = "{$inicio} 08:00:00";
			$fecha2 = "{$inicio} 19:59:59";
		}

		return DB::table("lista_espera as l")
		->join("casos as c", "c.id", "=", "l.caso")
		->join("pacientes as p", "p.id", "=", "c.paciente")
		->join("usuarios as u", "l.usuario", "=", "u.id")
		->whereBetween("l.fecha",[$fecha1,$fecha2])
		// ->whereNull("l.fecha_termino") // (en base al rango de fecha) descomentado: esta en la lista. comentado: estuvo o esta en la lista
		->where("u.establecimiento", Auth::user()->establecimiento)
		->count();
	}
}
