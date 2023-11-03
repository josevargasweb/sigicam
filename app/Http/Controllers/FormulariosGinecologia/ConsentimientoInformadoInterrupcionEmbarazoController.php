<?php

namespace App\Http\Controllers\FormulariosGinecologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\FormulariosGinecologia\ConsentimientoInformadoInterrupcionEmbarazoHelper;

use Exception;
use View;
use Auth;
use Log;
use DB;
use PDF;

class ConsentimientoInformadoInterrupcionEmbarazoController extends Controller
{
	public function view($caso_id){

		try {

			$aH = new ConsentimientoInformadoInterrupcionEmbarazoHelper();
			if(!$aH->validarUnidad($caso_id))
			{
				return redirect("index");
			}
			$formulario_data = $aH->getConsentimientoInformadoInterrupcionEmbarazoData($caso_id);
			return View::make("FormulariosGinecologia.consentimientoInformadoInterrupcionEmbarazo")
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
			$aH = new ConsentimientoInformadoInterrupcionEmbarazoHelper();
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
                'Campo consentimiento_informado_interrupcion_embarazo_medicantoso no valido.',
                'Campo consentimiento_informado_interrupcion_embarazo_instrumental no valido.',
                'Campo consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar no valido.',
                'Campo consentimiento_informado_interrupcion_embarazo_controlada_en no valido.',
                'Campo consentimiento_informado_interrupcion_embarazo_consultas_contacto no valido.'
			];
			
			if (!in_array($error_msg, $errores_controlados)) { 
				$error_msg = "Ha ocurrido un error";
			}

			return response()->json(["status" => $error_msg],500);
		}

	}
	public function pdf($id_caso)
	{
		$ciie = new ConsentimientoInformadoInterrupcionEmbarazoHelper();
		$formulario_data = $ciie->pdf($id_caso);
		
		$fecha = date("d-m-Y");
		
		$pdf = PDF::loadView('FormulariosGinecologia.pdf.pdfConsentimientoInformadoInterrupcionEmbarazo',
			[
				"formulario" => $formulario_data
			]);
		return $pdf->inline('consentimiento_informado_interrupcion_embarazo_'.$fecha.'.pdf');
		
	}


    
}
