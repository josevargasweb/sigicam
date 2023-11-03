<?php

namespace App\Http\Controllers\FormulariosGinecologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\FormulariosGinecologia\EpicrisisInterrupcionGestacionIIITrimestreHelper;

use Exception;
use View;
use Auth;
use Log;
use DB;
use PDF;

class EpicrisisInterrupcionGestacionIIITrimestreController extends Controller
{
	public function view($caso_id){

		try {
			$aH = new EpicrisisInterrupcionGestacionIIITrimestreHelper();
			if(!$aH->validarUnidad($caso_id))
			{
				return redirect("index");
			}
			$formulario_data = $aH->getEpicrisisInterrupcionGestacionIIITrimestreData($caso_id);
			return View::make("FormulariosGinecologia.epicrisisInterrupcionGestacionIIITrimestre")
			->with("formulario_data", $formulario_data);
		}
		catch (Exception $e){
			Log::error($e);
			Auth::logout();
			return redirect('/');
		}

	}


	public function store(Request $request){
		try {
			DB::beginTransaction();
			$aH = new EpicrisisInterrupcionGestacionIIITrimestreHelper();
			$data = $aH->store($request);
			$msg = (!isset($data->form_id)) ? "Formulario guardado exitosamente" : "Formulario modificado exitosamente";
			DB::commit();
			return response()->json(["status" => $msg],200);
		} 
		
		catch(Exception $e){
			DB::rollback();
			Log::error($e);
			$error_msg = $e->getMessage();
			$errores_controlados = [
				'Campo caso_id no valido.',
				'Campo form_id no valido.',
				'Campo form_bd no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_p no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_v no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_cons no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_presentacion no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_dilat no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_bishop no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_puntos no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_cd no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_cv no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_ec no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_rn no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_tipo no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_normal no valido',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_alterado no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3 no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion no valido.',
				'Campo epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion no valido.'				
			];
			
			if (!in_array($error_msg, $errores_controlados)) { 
				$error_msg = "Ha ocurrido un error";
			}

			return response()->json(["status" => $error_msg],500);
		}

	}
	
	public function pdf($id_caso)
	{
		$eigiiit = new EpicrisisInterrupcionGestacionIIITrimestreHelper();
		$formulario_data = $eigiiit->pdf($id_caso);
		
		$fecha = date("d-m-Y");
		
		$pdf = PDF::loadView('FormulariosGinecologia.pdf.pdfEpicrisisInterrupcionGestacionIIITrimestre',
			[
				"formulario" => $formulario_data
			]);
		return $pdf->inline('epicrisis_interrupcion_gestacion_iii_trimestre_'.$fecha.'.pdf');
		
	}

}
