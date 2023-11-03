<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;
use Log;

class IEAnamnesis extends Model
{
    protected $table = 'formulario_ie_anamnesis';
	public $timestamps = false;
	protected $primaryKey = 'id';
	protected $guarded = [];

	public static function historicoAnamnesis($caso){
		$response = [];
		$hAnamnesis = IEAnamnesis::select(
			'formulario_hoja_ingreso_enfermeria.fecha_creacion',
			'formulario_hoja_ingreso_enfermeria.fecha_modificacion',
			// 'formulario_hoja_ingreso_enfermeria.tipo_modificacion',
			'formulario_hoja_ingreso_enfermeria.anamnesis_ant_morbidos',
			'formulario_hoja_ingreso_enfermeria.anamnesis_ant_quirurgicos',
			'formulario_hoja_ingreso_enfermeria.anamnesis_ant_alergicos',
			'formulario_hoja_ingreso_enfermeria.habito_tabaco',
			'formulario_hoja_ingreso_enfermeria.habito_alcohol',
			'formulario_hoja_ingreso_enfermeria.habito_drogas',
			'formulario_hoja_ingreso_enfermeria.habito_otros',
			'formulario_hoja_ingreso_enfermeria.detalle_otro_habito',
			'formulario_hoja_ingreso_enfermeria.diagnosticos_medicos',
			'formulario_hoja_ingreso_enfermeria.anamnesis_actual',
			'formulario_hoja_ingreso_enfermeria.deis',
			'formulario_hoja_ingreso_enfermeria.acom',
			'u.nombres', 'u.apellido_paterno', 'u.apellido_materno')
			->join("usuarios as u","u.id","formulario_hoja_ingreso_enfermeria.usuario_responsable")
			->where('formulario_hoja_ingreso_enfermeria.caso',$caso)
			->orderBy('formulario_hoja_ingreso_enfermeria.fecha_creacion', 'asc')
			->get();

			foreach ($hAnamnesis as $key => $h) {

				$fecha_creacion = ($h->fecha_creacion) ? Carbon::parse($h->fecha_creacion)->format("d-m-Y H:i") : '--';
				$fecha_modificacion = ($h->fecha_modificacion) ? Carbon::parse($h->fecha_modificacion)->format("d-m-Y H:i:s") : '--';
				$morbidos = ($h->anamnesis_ant_morbidos) ? $h->anamnesis_ant_morbidos : '--';
				$quirurgicos = ($h->anamnesis_ant_quirurgicos) ? $h->anamnesis_ant_quirurgicos : '--';
				$alergicos = ($h->anamnesis_ant_alergicos) ? $h->anamnesis_ant_alergicos : '--';
				$tabaco = ($h->habito_tabaco) ? 'Si' : 'No';
				$alcohol = ($h->habito_alcohol) ? 'Si' : 'No';
				$drogas = ($h->habito_drogas) ? 'Si' : 'No';
				$otros = ($h->habito_otros) ? $h->detalle_otro_habito : 'No';
				$diagMedico = ($h->diagnosticos_medicos) ? $h->diagnosticos_medicos : '--';
				$actual = ($h->anamnesis_actual) ? $h->anamnesis_actual : '--';
				$response [] = [
					$fecha_creacion, $fecha_modificacion, $morbidos, $quirurgicos, $alergicos,
					"<div class='col-md-12'>
						<div class='col-md-6'><label>Tabaco: ".$tabaco."</label></div><br>
						<div class='col-md-6'><label>Alcohol: ".$alcohol."</label></div>
					</div>
					<div class='col-md-12'>
						<div class='col-md-6'><label>Drogas: ".$drogas."</label></div><br>
						<div class='col-md-6'><label>Otros: ".$otros."</label></div>
					</div>
          ",
					$diagMedico, $actual,
					$h->nombres. " " .$h->apellido_paterno. " " .$h->apellido_materno
				];
			}
		return $response;
	}

	public static function datosNuevos($nuevo, $original){
		$diferencias = 0;
		if($original->anamnesis_ant_morbidos != strip_tags($nuevo->antecedentesM)){
			$diferencias++;
		}

		if($original->anamnesis_ant_quirurgicos != strip_tags($nuevo->antecedentesQ)){
			$diferencias++;
		}

		if($original->anamnesis_ant_alergicos != strip_tags($nuevo->antecedentesA)){
			$diferencias++;
		}

		$tabaco = false;
		$alcohol = false;
		$drogas = false;
		$otras = false;
		if($nuevo->habitos != null){
			foreach($nuevo->habitos as $h){
				$tabaco = ($h == 'tabaco') ? true : false;
				$alcohol = ($h == 'alcohol') ? true : false;
				$drogas = ($h == 'drogas') ? true : false;
				$otras = ($h == 'otras') ? true : false;
			}
		}

		if($original->habito_tabaco != $tabaco){
			$diferencias++;
		}
		if($original->habito_alcohol != $alcohol){
			$diferencias++;
		}
		if($original->habito_drogas != $drogas){
			$diferencias++;
		}
		if($original->habito_otros != $otras){
			$diferencias++;
		}

		$deis = ($nuevo->deis == "si") ? true : false;
		if($original->deis != $deis){
			$diferencias++;
		}

		$oacom = ($nuevo->oacom == "si") ? true : false;
		if($original->acom != $oacom){
			$diferencias++;
		}

		if($oacom){
			if($original->acompanante != strip_tags($nuevo->acompanante)){
				$diferencias++;
			}
	
			if($original->vinculo_acompanante != strip_tags($nuevo->vinculo_acompanante)){
				$diferencias++;
			}
	
			if($original->telefono_acompanante != strip_tags($nuevo->telefono_acompanante)){
				$diferencias++;
			}
		}

		if($original->detalle_otro_habito != strip_tags($nuevo->detalleOtroHabito)){
			$diferencias++;
		}

		if($original->diagnosticos_medicos != strip_tags($nuevo->diagnosticoMedico)){
			$diferencias++;
		}

		if($original->anamnesis_actual != strip_tags($nuevo->amnesisActual)){
			$diferencias++;
		}

		return $diferencias;
	}

	public static function crearNuevo($request, $modificar,$idGinecologica){
		$anamnesis = new IEAnamnesis;
		$anamnesis->caso = $request->idCaso;

		if($modificar != null || $modificar != ""){
			$anamnesis->id_anterior = $modificar->id;
		}

		if($idGinecologica != null || $idGinecologica != ""){
			$anamnesis->idginecologica = $idGinecologica;
		}

		$anamnesis->usuario_responsable = Auth::user()->id;
		$anamnesis->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$anamnesis->anamnesis_ant_morbidos = strip_tags($request->antecedentesM);
		$anamnesis->anamnesis_ant_quirurgicos = strip_tags($request->antecedentesQ);
		$anamnesis->anamnesis_ant_alergicos = strip_tags($request->antecedentesA);
		
		$anamnesis->precaucion_estandar = ($request->precaucion_estandar === "si" ? true : ($request->precaucion_estandar === "no" ? false : null));
		
		$anamnesis->precaucion_respiratorio = ($request->precaucion_respiratorio ? true : false);
		$anamnesis->precaucion_contacto = ($request->precaucion_contacto ? true : false);
		$anamnesis->precaucion_gotitas = ($request->precaucion_gotitas ? true : false);

		$anamnesis->habito_tabaco = false;
		$anamnesis->habito_alcohol = false;
		$anamnesis->habito_drogas = false;
		$anamnesis->habito_otros = false;
		if($request->habitos == null){
			$anamnesis->habito_tabaco = false;
			$anamnesis->habito_alcohol = false;
			$anamnesis->habito_drogas = false;
			$anamnesis->habito_otros = false;
		}else{
			foreach ($request->habitos as $h) {
				if($h == 'tabaco'){
					$anamnesis->habito_tabaco = true;
				}else
				if($h == 'alcohol'){
					$anamnesis->habito_alcohol = true;
				}else
				if($h == 'drogas'){
					$anamnesis->habito_drogas = true;
				}else
				if($h == 'otras'){
					$anamnesis->habito_otros = true;
				}
			}
		}

		if($request->deis == 'si'){
			$anamnesis->deis = true;
		}else{
			$anamnesis->deis = false;
		}

		if($request->oacom == 'si'){
			$anamnesis->acom = true;
			$anamnesis->acompanante = strip_tags($request->acompanante);
			$anamnesis->vinculo_acompanante = strip_tags($request->vinculo_acompanante);
			$anamnesis->telefono_acompanante = strip_tags($request->telefono_acompanante);
		}else{
			$anamnesis->acom = false;
			$anamnesis->acompanante = "";
			$anamnesis->vinculo_acompanante = "";
			$anamnesis->telefono_acompanante = "";
		}

		$anamnesis->detalle_otro_habito = strip_tags($request->detalleOtroHabito);
		$anamnesis->diagnosticos_medicos = strip_tags($request->diagnosticoMedico);
		$anamnesis->anamnesis_actual = strip_tags($request->amnesisActual);
		$anamnesis->visible = true;

		

		$anamnesis->save();

		return $anamnesis;
	}

}
