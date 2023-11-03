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



class PreAlta extends Model{

	protected $table = "pre_alta";
	protected $primaryKey = 'id';

	public static function obtenerPreAlta($idEst){
		$response=[];
        $datos=DB::table("pre_alta as l")
        ->join("casos as c", "c.id", "=", "l.idcaso")
        ->join("pacientes as p", "p.id", "=", "c.paciente")
        ->join("usuarios as u", "l.usuario_solicita", "=", "u.id")
        ->whereNull("l.fecha_respuesta")
		->where("u.establecimiento", $idEst)
		->whereNull('solicitud_aceptada')
        ->select(
			"c.id as idCaso",
            "p.rut as rut",
            "p.dv as dv",
            "p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
            "c.prevision",
			"p.sexo",
			"p.fecha_nacimiento",
			"c.ficha_clinica",
			"c.dau",
			"c.id_unidad",
			"p.id as id_paciente",
			"l.fecha_solicita as fecha",
			"l.id as idPreAlta"
			)
			->get();
		foreach($datos as $dato){
			$apellido=$dato->apellidoP." ".$dato->apellidoM;
			$dv=($dato->dv == 10) ? "K" : $dato->dv;
			$rut=(empty($dato->rut)) ? "" : $dato->rut."-".$dv;
			$fecha=date("d-m-Y H:i", strtotime($dato->fecha));
			$ficha_dau =($dato->ficha_clinica != '' && $dato->ficha_clinica != null && $dato->dau != '' && $dato->dau != null) ?$dato->ficha_clinica."/".$dato->dau: '-';
			$fecha_nacimiento = ($dato->fecha_nacimiento != null && $dato->fecha_nacimiento != '')? Carbon::parse($dato->fecha_nacimiento)->format("Y-m-d").' edad '.Carbon::now()->diffInYears($dato->fecha_nacimiento). ' años': '-';
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
			->whereNotNull("fecha_liberacion")
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

			$opciones=View::make("Urgencia/OpcionesListaPreAlta", ["idCaso" => $dato->idCaso, "idPreAlta" => $dato->idPreAlta, "idPaciente"=> $dato->id_paciente, "idCama"=>(isset($dato->cama->cama))?$dato->cama->cama:"", "ficha"=>$dato->ficha_clinica, "nombreCompleto"=>$dato->nombre." ".$apellido, "color" => $color, "diff" => $diff, "sexo" => $sexo])->render();
			
			if($fechaCarbon->toDateString() < $hoy->toDateString()){
				$response[]=[ $rut, $dato->nombre. " " .$apellido,$dato->prevision,$dato->sexo, $datoDiagnostico,$fecha_nacimiento, $fecha." <label  hidden>".$diff."</label>",$ficha_dau,$dato->cama->sala_nombre." (".$dato->cama->id_cama.")",$opciones];
			}
			else if ($fechaCarbon->toDateString() == $hoy->toDateString()){

				$nuevo1 = $hoy->format("H:i");
				$nuevo2 = $fechaCarbon->format("H:i");

				if($nuevo1 < $nuevo2 || $diff == 0){
					$response[]=[$rut, $dato->nombre. " " .$apellido,$dato->prevision,$dato->sexo, $datoDiagnostico,$fecha_nacimiento, $fecha." <label  hidden>".-$diff."</label> ",$ficha_dau,$dato->cama->sala_nombre." (".$dato->cama->id_cama.")",$opciones];

				}
				else{
					$response[]=[$rut, $dato->nombre. " " .$apellido,$dato->prevision, $dato->sexo,$datoDiagnostico,$fecha_nacimiento, $fecha." <label  hidden>".$diff."</label> ",$ficha_dau,$dato->cama->sala_nombre." (".$dato->cama->id_cama.")",$opciones];

				}
				

			}
			else{
				$response[]=[$rut, $dato->nombre. " " .$apellido, $datoDiagnostico, $fecha." <label  hidden>".-$diff."</label> ",$dato->cama->nombre_unidad,$dato->cama->sala_nombre." (".$dato->cama->id_cama.")",$opciones];


			}



		}
		return $response;
	}

	public static function casoEnPreAlta($idCaso){
		return PreAlta::where('idcaso',$idCaso)->whereNull('fecha_respuesta')->whereNotNull('fecha_solicita')->first();
	}

}

