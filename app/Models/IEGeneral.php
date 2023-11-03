<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use Auth;
use Log;


class IEGeneral extends Model
{
    protected $table = 'formulario_ie_fisico_general';
	public $timestamps = false;
	protected $primaryKey = 'id';

	public static function historicoGeneral($caso){
		$response = [];
		$hGeneral = IEGeneral::select(
			'formulario_hoja_ingreso_enfermeria.fecha_creacion',
			'formulario_hoja_ingreso_enfermeria.fecha_modificacion',
			// 'formulario_hoja_ingreso_enfermeria.tipo_modificacion',
			'formulario_hoja_ingreso_enfermeria.peso',
			'formulario_hoja_ingreso_enfermeria.altura',
			'formulario_hoja_ingreso_enfermeria.presion_arterial_sistolica',
			'formulario_hoja_ingreso_enfermeria.presion_arterial_diastolica',
			'formulario_hoja_ingreso_enfermeria.pulso',
			'formulario_hoja_ingreso_enfermeria.frecuencia_cardiaca',
			'formulario_hoja_ingreso_enfermeria.temperatura',
			'formulario_hoja_ingreso_enfermeria.saturacion',
			'formulario_hoja_ingreso_enfermeria.patron_nutricional',
			'formulario_hoja_ingreso_enfermeria.estado_conciencia',
			'formulario_hoja_ingreso_enfermeria.glasgow',
			'formulario_hoja_ingreso_enfermeria.funcion_respiratoria',
			'formulario_hoja_ingreso_enfermeria.higiene',
			'formulario_hoja_ingreso_enfermeria.nova',
			'formulario_hoja_ingreso_enfermeria.riesgo_caida',
			'u.nombres', 'u.apellido_paterno', 'u.apellido_materno')
			->join("usuarios as u","u.id","formulario_hoja_ingreso_enfermeria.usuario_responsable")
			->where('formulario_hoja_ingreso_enfermeria.caso',$caso)
			->orderBy('formulario_hoja_ingreso_enfermeria.fecha_creacion', 'asc')
			->get();

			foreach ($hGeneral as $key => $g) {
				$fecha_creacion = ($g->fecha_creacion) ? Carbon::parse($g->fecha_creacion)->format("d-m-Y H:i") : '--';
				$fecha_modificacion = ($g->fecha_modificacion) ? Carbon::parse($g->fecha_modificacion)->format("d-m-Y H:i:s") : '--';
				$peso = ($g->peso) ? $g->peso : '--';
				$altura = ($g->altura) ? $g->altura : '--';

				$imc = "30.9";
				$pas = ($g->presion_arterial_sistolica) ? $g->presion_arterial_sistolica : '--';
				$pad = ($g->presion_arterial_diastolica) ? $g->presion_arterial_diastolica : '--';
				$pulso = ($g->pulso) ? $g->pulso : '--';
				$f_cardiaca = ($g->frecuencia_cardiaca) ? $g->frecuencia_cardiaca : '--';
				$temperatura = ($g->temperatura) ? $g->temperatura : '--';
				$saturacion = ($g->saturacion) ? $g->saturacion : '--';
				$p_nutricional = ($g->patron_nutricional) ? $g->patron_nutricional : '--';
				$e_conciencia = ($g->estado_conciencia) ? $g->estado_conciencia : '--';
				$glasgow = ($g->glasgow) ? $g->glasgow : '--';
				$f_respiratoria = ($g->funcion_respiratoria) ? $g->funcion_respiratoria : '--';
				$higiene = ($g->higiene) ? $g->higiene : '--';
				$nova = ($g->nova) ? $g->nova : '--';
				$r_caida = ($g->riesgo_caida) ? $g->riesgo_caida : '--';
				$response [] = [
					$fecha_creacion, $fecha_modificacion,
					"Peso: ".$peso." <br>
					Altura: ".$altura." <br>
					IMC: ".$imc."",
					"P.A Sistolica: ".$pas." <br>
					P.A Diastolica: ".$pad." <br>
					Pulso: ".$pulso." <br>
					F.Cardiaca: ".$f_cardiaca." <br>
					Saturación: ".$saturacion." <br>
					Temperatura: ".$temperatura."",
					$p_nutricional,
					$e_conciencia,
					$f_respiratoria,
					$higiene,
					"Nova: ".$nova." <br>
					Riesgo Caída: ".$r_caida." <br>
					Glasgow: ".$glasgow."",
					$g->nombres. " " .$g->apellido_paterno. " " .$g->apellido_materno
				];
			}
		return $response;
	}

    public static function crearNuevo($request, $modificar,$indglasgow,$indbarthel,$indriesgo,$indnova){

        $general = new IEGeneral;
        ///DB::beginTransaction();
        $general->caso = $request->idCaso;

        if($modificar != null || $modificar != ""){
			$general->id_anterior = $modificar->id;
		}
        
        $general->usuario_responsable = Auth::user()->id;
        $general->visible = true;
        $general->fecha_creacion = (isset($modificar) && isset($modificar->fecha_creacion)) ? $modificar->fecha_creacion : Carbon::now()->format("Y-m-d H:i:s");

        $general->peso = ($request->peso) ? $request->peso : null;
        $general->altura = ($request->altura) ? $request->altura : null;
        $general->presion_arterial_sistolica = $request->pas;
        $general->presion_arterial_diastolica = $request->pad;
        $general->pulso = $request->pulso;
        $general->frecuencia_cardiaca = $request->fr;
        $general->temperatura = $request->temperatura;
        $general->saturacion = $request->saturacion;
        $general->patron_nutricional = $request->nutricional;
        $general->estado_conciencia = $request->conciencia;
        $general->indglasgow = $indglasgow;
        $general->indbarthel = $indbarthel;
        $general->indriesgo = $indriesgo;
        $general->indnova = $indnova;
        $general->funcion_respiratoria = $request->funcionRespiratoria;
        $general->higiene = $request->higiene;
        $general->nova = ($request->nova) ? $request->nova : null;
        $general->riesgo_caida = ($request->caida) ? $request->caida : null;
        $general->glasgow = ($request->glasgow) ? $request->glasgow : null;
        $general->barthel = ($request->barthel) ? $request->barthel : null;
        $general->save();

        return $general;
    }

	public static function formularioVacio($request){
		//creamos un nuevo request
		$nuevoRequest = \Illuminate\Http\Request::capture();
		//le quito los campos que no debo considerar
        $nuevoRequest->replace($request->except(['_token','idCaso','id_formulario_ingreso_enfermeria','categoria','imc']));
        
        $cont_vacios = 0;
        foreach ($nuevoRequest->all() as $key => $value) {
			//reviso cada campo para ver si viene vacio
            if($value == ""){
				//si viene vacio, le sumo 1
                $cont_vacios++;
            }
        }

        if($cont_vacios == 15){
            $respuesta = true; //formulario vacio
        }else{
            $respuesta = false; //formulario con datos
        }

		return $respuesta;
	}


	public static function guardarGlasgow($request, $formulario_modificar,$tipo){
		$glasgowForm = explode(",", $request->arrayGlasgow);
		Log::info("Nuevo Glasgow");
		$glasgow = new Glasgow;
		$glasgow->usuario_responsable = Auth::user()->id;
		$glasgow->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$glasgow->caso = $request->idCaso;
		$glasgow->apertura_ocular = $glasgowForm[0];
		$glasgow->respuesta_verbal = $glasgowForm[1];
		$glasgow->respuesta_motora = $glasgowForm[2];
		$glasgow->total = $glasgowForm[0] + $glasgowForm[1] + $glasgowForm[2];
		$glasgow->tipo = $tipo;

		return $glasgow;
	}

	public static function guardarBarthel($request, $formulario_modificar,$tipo){
		$barthelForm = explode(",", $request->arrayBarthel);
		Log::info("Nuevo Barthel");
		$barthel = new Barthel;
		$barthel->caso = $request->idCaso;
		$barthel->usuario_responsable = Auth::user()->id;
		$barthel->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$barthel->comida = $barthelForm[0];
		$barthel->lavado = $barthelForm[1];
		$barthel->vestido = $barthelForm[2];
		$barthel->arreglo = $barthelForm[3];
		$barthel->deposicion = $barthelForm[4];
		$barthel->miccion = $barthelForm[5];
		$barthel->retrete = $barthelForm[6];
		$barthel->trasferencia = $barthelForm[7];
		$barthel->deambulacion = $barthelForm[8];
		$barthel->escaleras = $barthelForm[9];
		$barthel->tipo = $tipo;
		
		return $barthel;
	}

	public static function guardarRiesgo($request, $formulario_modificar,$tipo){
		$riesgoCaidaForm = explode(",", $request->arrayRiesgoCaida);
	
		$riesgo = new HojaEnfermeriaRiesgoCaida;
		$riesgo->caso = $request->idCaso;
		$riesgo->procedencia = "Formulario1";
		$riesgo->usuario_ingresa = Auth::user()->id;
		$riesgo->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$riesgo->caidas_previas = $riesgoCaidaForm[0];
		if(count(explode(",", $request->arrayRiesgoCaida)) == 4 &&  $request->arrayRiesgoCaidaMedicamento != ''){
			$riesgo->medicamentos = $request->arrayRiesgoCaidaMedicamento;
			$riesgo->deficits_sensoriales = $riesgoCaidaForm[1];
			$riesgo->estado_mental = $riesgoCaidaForm[2];
			$riesgo->deambulacion = $riesgoCaidaForm[3];

			if(strpos($request->arrayRiesgoCaidaMedicamento, ',')) {
                $medicamentos =  count(explode( ',', $request->arrayRiesgoCaidaMedicamento ));
			}else{
				$medicamentos = $request->arrayRiesgoCaidaMedicamento;
			}

			$riesgo->total = $riesgoCaidaForm[0] + $riesgoCaidaForm[1] + $riesgoCaidaForm[2] + $riesgoCaidaForm[3] + $medicamentos;
		}else if(count(explode(",", $request->arrayRiesgoCaida)) == 5){
			$riesgo->medicamentos = $riesgoCaidaForm[1];
			$riesgo->deficits_sensoriales = $riesgoCaidaForm[2];
			$riesgo->estado_mental = $riesgoCaidaForm[3];
			$riesgo->deambulacion = $riesgoCaidaForm[4];
			$riesgo->total = $riesgoCaidaForm[0] + $riesgoCaidaForm[1] + $riesgoCaidaForm[2] + $riesgoCaidaForm[3] + $riesgoCaidaForm[4];
		}
		$riesgo->tipo = $tipo;

		return $riesgo;
	}
	public static function guardarNova($request, $formulario_modificar,$tipo){
		$novaForm = explode(",", $request->arrayNova);
		Log::info("Nuevo Nova");
		$nova = new Nova;		
		$nova->caso = $request->idCaso;
		$nova->usuario_responsable = Auth::user()->id;
		$nova->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$nova->estado_mental = $novaForm[0];
		$nova->incontinencia = $novaForm[1];
		$nova->movilidad = $novaForm[2];
		$nova->nutricion_ingesta = $novaForm[3];
		$nova->actividad = $novaForm[4];
		$nova->total = $novaForm[0] + $novaForm[1] + $novaForm[2] + $novaForm[3] + $novaForm[4];
		$nova->tipo = $tipo;

		return $nova;
	}

}
