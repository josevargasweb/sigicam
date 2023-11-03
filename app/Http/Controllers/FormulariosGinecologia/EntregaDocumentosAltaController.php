<?php

namespace App\Http\Controllers\FormulariosGinecologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\FormulariosGinecologia\EntregaDocumentosAltaHelper;

use Exception;
use View;
use Auth;
use Log;
use DB;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioDocumentoAlta;
use PDF;
use App\Models\HistorialSubcategoriaUnidad;
use App\Models\UnidadEnEstablecimiento;


class EntregaDocumentosAltaController extends Controller
{
	public function view($caso_id){

		try {

			$edaH = new EntregaDocumentosAltaHelper();
			if(!$edaH->validarUnidad($caso_id))
			{
				return redirect("index");
			}
			$formulario_data = $edaH->getDocumentoAltaData($caso_id);
			return View::make("FormulariosGinecologia.entregaDocumentosAlta")
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
			$edaH = new EntregaDocumentosAltaHelper();
			$data = $edaH->store($request);
			$msg = (!isset($data->form_id)) ? "Formulario guardado exitosamente" : "Formulario modificado exitosamente";
			DB::commit();
			return response()->json(["status" => $msg],200);
		} 
		
		catch(Exception $e){
			DB::rollback();
			Log::error($e);
			$error_msg = $e->getMessage();
			$errores_controlados = [
				'Campo entrega_documentos_alta_epicrisis_medica no valido.',
				'Campo entrega_documentos_alta_carnet_alta no valido.',
				'Campo entrega_documentos_alta_recetas_farmacos no valido.',
				'Campo entrega_documentos_alta_citaciones_control no valido.',
				'Campo entrega_documentos_alta_carne_identidad no valido.',
				'Campo entrega_documentos_alta_comprobante_parto no valido.',
				'Campo entrega_documentos_alta_carne_control_parental no valido.',
				'Campo entrega_documentos_alta_egreso_hospitalario_acompanado no valido.',
				'Campo entrega_documentos_alta_acompanante no valido.',
				'Campo entrega_documentos_alta_observaciones no valido.',
				'Campo caso_id no valido.',
				'Campo form_id no valido.',
				'Campo form_bd no valido.'
			];
			
			if (!in_array($error_msg, $errores_controlados)) { 
				$error_msg = "Ha ocurrido un error";
			}

			return response()->json(["status" => $error_msg],500);
		}

	}
	public function pdf($id_caso)
	{
		$edaH = new EntregaDocumentosAltaHelper();
		$formulario_data = $edaH->getDocumentoAltaDataPDF($id_caso);
		
		$fecha = date("d-m-Y");
		
		$pdf = PDF::loadView('FormulariosGinecologia.pdf.pdfEntregaDocumentosAlta',
			[
				"formulario" => $formulario_data
			]);
		return $pdf->inline('documentos_al_alta_'.$fecha.'.pdf');
	
	}

	public function homologar_unidad_ginecologica(){
		$unidades_con_ginecologica = UnidadEnEstablecimiento::select('id')->where('unidad_ginecologica',true)->get();

		if($unidades_con_ginecologica){ log::info("existe");
			foreach ($unidades_con_ginecologica as $unidad_gineco) {
				$existe_subunidad = HistorialSubcategoriaUnidad::where('id_unidad',$unidad_gineco->id)->where('visible', true)->first();
				if(!$existe_subunidad){ log::info("no tiene registro en true, puede guardar");
					$agregar = new HistorialSubcategoriaUnidad();
					$agregar->fecha = Carbon::now()->format('Y-m-d H:i:s');
					$agregar->usuario_ingresa = Auth::user()->id;
					$agregar->id_unidad = $unidad_gineco->id;
					$agregar->id_subcategoria = 1; //Ginecologica
					$agregar->visible = true;
					$agregar->save();
				}
			}
		}
		return [$unidades_con_ginecologica,$existe_subunidad];
	}

}
