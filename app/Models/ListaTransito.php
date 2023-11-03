<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use View;
use Auth;
use TipoUsuario;
use Log;
use HistorialOcupacion;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Session;
use App\Models\CamaTemporal;


class ListaTransito extends Model implements Auditable{
	use \OwenIt\Auditing\Auditable;

	protected $table = "lista_transito";
	protected $primaryKey = "id_lista_transito";
	protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
	 

	
	 

	public static function cantidadPacientes(){
		return DB::table('lista_transito')->select(DB::raw("count(*) as total_transito"))->whereNull("fecha_termino")->get();
	}


    public static function obtenerListaTransito($idEst,$procedencia){
		$procedencia = ($procedencia == '' || $procedencia == 'x') ? '' : $procedencia;
		$response=[];
        $datos=DB::table("lista_transito as l")
        ->join("casos as c", "c.id", "=", "l.caso")
        ->join("pacientes as p", "p.id", "=", "c.paciente")
        ->join("usuarios as u", "l.id_usuario_ingresa", "=", "u.id")
		->whereNull("l.fecha_termino")
		->whereNull('traslado_unidad_hospitalaria')
        ->where("u.establecimiento", $idEst)
        ->select("p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			"p.rut as rut",
			"p.dv as dv",
			"p.sexo",
			"l.fecha as fecha",
			"c.indicacion_hospitalizacion",
			"c.id as idCaso",
			"l.id_lista_transito as idLista",
			"c.id_unidad",
			"c.ficha_clinica",
			"p.id as id_paciente",
			"l.traslado_unidad_hospitalaria",
			"c.procedencia")
			->when($procedencia != "", function($query) use ($procedencia){
				return $query->where("procedencia","=",$procedencia);
			})->get();

		foreach($datos as $dato){
			$apellido=$dato->apellidoP." ".$dato->apellidoM;
			$dv=($dato->dv == 10) ? "K" : $dato->dv;
			$rut=(empty($dato->rut)) ? "" : $dato->rut."-".$dv;
			$fecha=date("d-m-Y H:i", strtotime($dato->fecha));

			if($dato->traslado_unidad_hospitalaria)
			{
				$dato->traslado_unidad_hospitalaria = date("d-m-Y H:i", strtotime($dato->traslado_unidad_hospitalaria));

			}

			$indicacion_hospitalizacion = "";
			if($dato->indicacion_hospitalizacion){
				$indicacion_hospitalizacion = date("d-m-Y H:i", strtotime($dato->indicacion_hospitalizacion));
			}

			$dato->diagnostico = HistorialDiagnostico::where("caso","=",$dato->idCaso)->select("diagnostico")->first();

			if($dato->diagnostico){
				$datoDiagnostico = $dato->diagnostico->diagnostico;
			}
			//Obtener la cama
			$dato->cama = DB::table('t_historial_ocupaciones')
			->join("camas","camas.id","=","t_historial_ocupaciones.cama")
			->join("salas","camas.sala","=","salas.id")
			->join("unidades_en_establecimientos AS uee","salas.establecimiento","=","uee.id")
			->where("caso", "=", $dato->idCaso)
			->whereNull("fecha_liberacion")
			->select("cama","id_cama","salas.nombre as sala_nombre","uee.alias as nombre_unidad")
			->first();
			

			$hoy = Carbon::now();
			$fechaCarbon = Carbon::parse($fecha);
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

			$sexo = $dato->sexo;

			$cama_temporal = CamaTemporal::where("caso", $dato->idCaso)->where("visible", true)->first(); 

			$cama = "";
			$nombre_unidad = "";
			$sala_nombre = "";
			$id_cama = "";
			$opciones="";
			if ($cama_temporal) {
				$infoCama_tmp =  DB::table('t_historial_ocupaciones')
					->join("camas","camas.id","=","t_historial_ocupaciones.cama")
					->join("salas","camas.sala","=","salas.id")
					->join("unidades_en_establecimientos AS uee","salas.establecimiento","=","uee.id")
					->where("t_historial_ocupaciones.id", "=", $cama_temporal->id_historial_ocupaciones)
					->select("cama","id_cama","salas.nombre as sala_nombre","uee.alias as nombre_unidad")
					->first();

				$cama = $infoCama_tmp->cama;
				$nombre_unidad = $infoCama_tmp->nombre_unidad;
				$sala_nombre = $infoCama_tmp->sala_nombre;
				$id_cama = $infoCama_tmp->id_cama;
				$opciones="<h4><span class='label label-info'>En Cama Volante</span></h4>";
			}else{
				if ($dato->cama) {
					$cama = $dato->cama->cama;
					$nombre_unidad = $dato->cama->nombre_unidad;
					$sala_nombre = $dato->cama->sala_nombre;
					$id_cama = $dato->cama->id_cama;
				}
				$opciones=View::make("Urgencia/OpcionesListaTransito", ["idCaso" => $dato->idCaso, "idLista" => $dato->idLista, "idPaciente"=> $dato->id_paciente, "idCama"=>$cama, "ficha"=>$dato->ficha_clinica, "nombreCompleto"=>$dato->nombre." ".$apellido, "color" => $color, "diff" => $diff,"sexo" => $sexo])->render();
			}

			
			
			if(Session::get("usuario")->tipo === \App\util\TipoUsuario::VISUALIZADOR){
				$opciones = "";
			}
			
			if($fechaCarbon->toDateString() < $hoy->toDateString()){
				$response[]=[$opciones, $rut, $dato->nombre." " .$apellido, $datoDiagnostico, $fecha." <label style='display:none;'>".$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$fecha];
			}
			else if ($fechaCarbon->toDateString() == $hoy->toDateString()){

				$nuevo1 = $hoy->format("H:i");
				$nuevo2 = $fechaCarbon->format("H:i");

				if($nuevo1 < $nuevo2 || $diff == 0){
					$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label style='display:none;'>".-$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$fecha];

				}
				else{
					$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label style='display:none;'>".$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$fecha];

				}
				

			}
			else{
				$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label style='display:none;'>".-$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$fecha];


			}



			// FIX cuando la lista queda abierta y ya se creo un nuevo caso,
			$paciente = Paciente::where("rut", "=", $dato->rut)->first();
			$idPaciente = $paciente->id;
			$lista=DB::table( DB::raw(
             "(SELECT l.id as lis,c.id as cas FROM casos as c,lista_espera as l where c.id=l.caso and c.paciente=$idPaciente and l.fecha_termino is null) as rea"
         	))
			->get();


			$casosVar=DB::table( DB::raw(
             "(select count(*) as contador from pacientes as p,casos as c where p.id=$idPaciente and c.fecha_termino is null and c.paciente=p.id
				) as re"
         	))
			->first();



			if(!is_null($lista))
			{
				foreach ($lista as $lis)
				{

				if($casosVar->contador>=2)
					{


						$caso2 = Caso::find($lis->cas); // id caso de la lista
						$caso2->fecha_termino = date("Y-m-d H:i");
						$caso2->motivo_termino = "alta";
						$caso2->save();

						$listas=ListaTransito::find($lis->lis);  // se vcierra la lista
						$listas->fecha_termino=date("Y-m-d H:i");
						$listas->motivo_salida="hospitalización";
						$listas->save();

					}

				}
			}
		}
		return $response;
	}





	public static function obtenerSalidaUrgencia($idEst){
		$response=[];
        $datos=DB::table("lista_transito as l")
        ->join("casos as c", "c.id", "=", "l.caso")
        ->join("pacientes as p", "p.id", "=", "c.paciente")
        ->join("usuarios as u", "l.id_usuario_ingresa", "=", "u.id")
        ->whereNull("l.fecha_termino")
		->where("u.establecimiento", $idEst)
		->whereNotNull('traslado_unidad_hospitalaria')
        ->select("p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			"p.rut as rut",
			"p.sexo",
			"p.dv as dv",
			"l.fecha as fecha",
			"c.indicacion_hospitalizacion",
			"c.id as idCaso",
			"l.id_lista_transito as idLista",
			"c.id_unidad",
			"c.ficha_clinica",
			"p.id as id_paciente",
			"l.traslado_unidad_hospitalaria")
			->get();
		foreach($datos as $dato){
			$apellido=$dato->apellidoP." ".$dato->apellidoM;
			$dv=($dato->dv == 10) ? "K" : $dato->dv;
			$rut=(empty($dato->rut)) ? "" : $dato->rut."-".$dv;
			$fecha=date("d-m-Y H:i", strtotime($dato->fecha));

			if($dato->traslado_unidad_hospitalaria)
			{
				$dato->traslado_unidad_hospitalaria = date("d-m-Y H:i", strtotime($dato->traslado_unidad_hospitalaria));

			}

			$indicacion_hospitalizacion = "";
			if($dato->indicacion_hospitalizacion){
				$indicacion_hospitalizacion = date("d-m-Y H:i", strtotime($dato->indicacion_hospitalizacion));
			}

			$dato->diagnostico = HistorialDiagnostico::where("caso","=",$dato->idCaso)->select("diagnostico")->first();

			if($dato->diagnostico){
				$datoDiagnostico = $dato->diagnostico->diagnostico;
			}

			//Obtener la cama
			$dato->cama = DB::table('t_historial_ocupaciones')
			->join("camas","camas.id","=","t_historial_ocupaciones.cama")
			->join("salas","camas.sala","=","salas.id")
			->join("unidades_en_establecimientos AS uee","salas.establecimiento","=","uee.id")
			->where("caso", "=", $dato->idCaso)
			->whereNull("fecha_liberacion")
			->select("cama","id_cama","salas.nombre as sala_nombre","uee.alias as nombre_unidad")
			->first();
			

			$hoy = Carbon::now();
			$fechaCarbon = Carbon::parse($fecha);
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

			$sexo = $dato->sexo;

			
			$cama_temporal = CamaTemporal::where("caso", $dato->idCaso)->where("visible", true)->first(); 

			$cama_temporal = CamaTemporal::where("caso", $dato->idCaso)->where("visible", true)->first(); 

			$cama = "";
			$nombre_unidad = "";
			$sala_nombre = "";
			$id_cama = "";
			if ($cama_temporal) {
				$infoCama_tmp =  DB::table('t_historial_ocupaciones')
					->join("camas","camas.id","=","t_historial_ocupaciones.cama")
					->join("salas","camas.sala","=","salas.id")
					->join("unidades_en_establecimientos AS uee","salas.establecimiento","=","uee.id")
					->where("t_historial_ocupaciones.id", "=", $cama_temporal->id_historial_ocupaciones)
					->select("cama","id_cama","salas.nombre as sala_nombre","uee.alias as nombre_unidad")
					->first();

				$cama = $infoCama_tmp->cama;
				$nombre_unidad = $infoCama_tmp->nombre_unidad;
				$sala_nombre = $infoCama_tmp->sala_nombre;
				$id_cama = $infoCama_tmp->id_cama;
				$opciones="<h4><span class='label label-info'>En Cama Volante</span></h4>";
			}else{
				if ($dato->cama) {
					$cama = $dato->cama->cama;
					$nombre_unidad = $dato->cama->nombre_unidad;
					$sala_nombre = $dato->cama->sala_nombre;
					$id_cama = $dato->cama->id_cama;
				}
				$opciones=View::make("Urgencia/OpcionesListaTransitoDos", ["idCaso" => $dato->idCaso, "idLista" => $dato->idLista, "idPaciente"=> $dato->id_paciente, "idCama"=>$cama, "ficha"=>$dato->ficha_clinica, "nombreCompleto"=>$dato->nombre." ".$apellido, "color" => $color, "diff" => $diff, "sexo" => $sexo])->render();
			}
			
			if($fechaCarbon->toDateString() < $hoy->toDateString()){
				$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label  hidden>".$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$dato->traslado_unidad_hospitalaria];
			}
			else if ($fechaCarbon->toDateString() == $hoy->toDateString()){

				$nuevo1 = $hoy->format("H:i");
				$nuevo2 = $fechaCarbon->format("H:i");

				if($nuevo1 < $nuevo2 || $diff == 0){
					$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label  hidden>".-$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$dato->traslado_unidad_hospitalaria];

				}
				else{
					$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label  hidden>".$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")",$dato->traslado_unidad_hospitalaria];

				}
				

			}
			else{
				$response[]=[$opciones, $rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label  hidden>".-$diff."</label> ",$indicacion_hospitalizacion,$nombre_unidad,$sala_nombre." (".$id_cama.")", $dato->traslado_unidad_hospitalaria];


			}



			// FIX cuando la lista queda abierta y ya se creo un nuevo caso,
			$paciente = Paciente::where("rut", "=", $dato->rut)->first();
			$idPaciente = $paciente->id;
			$lista=DB::table( DB::raw(
             "(SELECT l.id as lis,c.id as cas FROM casos as c,lista_espera as l where c.id=l.caso and c.paciente=$idPaciente and l.fecha_termino is null) as rea"
         	))
			->get();


			$casosVar=DB::table( DB::raw(
             "(select count(*) as contador from pacientes as p,casos as c where p.id=$idPaciente and c.fecha_termino is null and c.paciente=p.id
				) as re"
         	))
			->first();



			if(!is_null($lista))
			{
				foreach ($lista as $lis)
				{

				if($casosVar->contador>=2)
					{


						$caso2 = Caso::find($lis->cas); // id caso de la lista
						$caso2->fecha_termino = date("Y-m-d H:i");
						$caso2->motivo_termino = "alta";
						$caso2->save();

						$listas=ListaTransito::find($lis->lis);  // se vcierra la lista
						$listas->fecha_termino=date("Y-m-d H:i");
						$listas->motivo_salida="hospitalización";
						$listas->save();

					}

				}
			}
		}
		return $response;
	}

	public static function casoEnListaTransito($idCaso){
		return ListaTransito::where('caso',$idCaso)->whereNull('fecha_termino')->whereNull('traslado_unidad_hospitalaria')->first();
	}

	public static function casoEnSalidaUrgencia($idCaso){
		return ListaTransito::where('caso',$idCaso)->whereNull('fecha_termino')->whereNotNull('traslado_unidad_hospitalaria')->first();
	}

	public static function pacientesFecha($inicio, $fin, $origen, $adultoOpediatria){
		if($fin){
			$fecha1 = "{$inicio} 20:00:00";
			$fecha2 = "{$fin} 07:59:59";
		}else{
			$fecha1 = "{$inicio} 08:00:00";
			$fecha2 = "{$inicio} 19:59:59";
		}

		$datos=DB::table("lista_transito as l")
			->select("p.fecha_nacimiento")
			->join("casos as c", "c.id", "=", "l.caso")
			->join("t_historial_ocupaciones as t", "t.caso","c.id")
			->join("camas as ca", "ca.id", "=", "t.cama")
			->join("salas as s", "s.id", "=", "ca.sala")
			->join("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
			->join("pacientes as p", "p.id", "=", "c.paciente")
			->whereBetween("l.fecha",[$fecha1,$fecha2])
			->where("c.procedencia",$origen)
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

}