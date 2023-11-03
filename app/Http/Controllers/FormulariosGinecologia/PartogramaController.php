<?php

namespace App\Http\Controllers\FormulariosGinecologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\FormulariosGinecologia\PartogramaHelper;

use Exception;
use View;
use Auth;
use Log;
use DB;
use PDF;

class PartogramaController extends Controller
{
	public function view($caso_id){

		try {
			$aH = new PartogramaHelper();
			if(!$aH->validarUnidad($caso_id))
			{
				//return redirect("index");
				throw new Exception('Unidad no valida');
			}
			$formulario_data = $aH->getPartogramaData($caso_id);
			return View::make("FormulariosGinecologia.partograma_matroneria")
			->with("formulario_data", $formulario_data)
			->with("tab",false);
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
			$aH = new PartogramaHelper();
			$data = $aH->store($request);
			$msg = (!isset($data->form_id)) ? "Formulario guardado exitosamente" : "Formulario modificado exitosamente";
			DB::commit();
			return response()->json(["status" => $msg,"id" => $data->form->id_formulario_partograma],200);
		} 
		
		catch(Exception $e){
			DB::rollback();
			Log::error($e);
			$error_msg = $e->getMessage();
			$errores_controlados = [
				'Campo caso_id no valido.',
				'Campo form_id no valido.',
				'Campo form_bd no valido.',			
			];
			
			if (!in_array($error_msg, $errores_controlados)) { 
				$error_msg = "Ha ocurrido un error";
			}

			return response()->json(["status" => $error_msg],500);
		}

	}
	public function guardarPartograma(Request $request){
		try{
			$ph = new PartogramaHelper();
			$ph->guardarPartograma($request);
			
			return response()->json(["status" => "Se ha guardado correctamente"],200);
		}catch(Exception $e){
			Log::error($e);
		}
	}
	public function datosPartograma(Request $request){
		try{
			$ph = new PartogramaHelper();
			$datos["bloques"] = $ph->getPartogramaBloques($request->caso);
			$datos["datos_bloques"] = $ph->getPartogramaDatosBloques($request->caso);
			return response()->json((object)$datos);
		}catch(Exception $e){
			Log::error($e);
		}
	}
	public function pdf(Request $request)
	{
		$ph = new PartogramaHelper();
		$formulario_data = $ph->pdf($request->caso_id);
		$fecha = date("d-m-Y");
		$pdf = PDF::loadView('FormulariosGinecologia.pdf.pdfPartograma',
			[
				"formulario" => $formulario_data["formulario"],
				"imagenes" => $request->imagenes,
				"tabla" => $formulario_data["tabla"],
				"evolucion" => $formulario_data["evolucion"]
		]);
		return $pdf->inline('partograma_'.$fecha.'.pdf');
		
	}


	public function getPartogramaAlergias(Request $req){
		try{
			$ph = new PartogramaHelper();
			$alergiaData = $ph->getPartogramaAlergias($req);
			
			return response()->json(["status" => "exito", "alergiaData" => $alergiaData],200);
		}catch(Exception $e){
			Log::error($e);
			return response()->json(["status" => "Ha ocurrido un error."],500);
		}
	}

}
