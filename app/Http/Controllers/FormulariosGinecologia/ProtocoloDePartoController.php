<?php

namespace App\Http\Controllers\FormulariosGinecologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\FormulariosGinecologia\ProtocoloDePartoHelper;
use App\Helpers\FormulariosGinecologia\FormulariosGinecologiaException;

use Exception;
use View;
use Auth;
use Log;
use DB;
use PDF;

class ProtocoloDePartoController extends Controller
{
	public function view($caso_id){

		try {
			$aH = new ProtocoloDePartoHelper();
			if(!$aH->validarUnidad($caso_id))
			{
				return redirect("index");
			}
			$formulario_data = $aH->getProtocoloPartoData($caso_id);
			return View::make("FormulariosGinecologia.protocoloDeParto")
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
			$aH = new ProtocoloDePartoHelper();
			$data = $aH->store($request);
			$msg = (!isset($data->form_id)) ? "Formulario guardado exitosamente" : "Formulario modificado exitosamente";
			DB::commit();
			return response()->json(["status" => $msg],200);
		} 
		catch(FormulariosGinecologiaException $fge){
			DB::rollback();
			Log::error($fge);

			return response()->json(["status" => $fge->getMessage()],500);
		}
		catch(Exception $e){
			DB::rollback();
			Log::error($e);

			return response()->json(["status" => "Ha ocurrido un error"],500);
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
