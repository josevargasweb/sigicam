<?php

namespace App\Http\Controllers\FormulariosGinecologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\FormulariosGinecologia\SolicitudTransfusionProductosSanguineosHelper;


use Exception;
use View;
use Auth;
use Log;
use DB;
use PDF;

class SolicitudTransfusionProductosSanguineosController extends Controller
{

    public function view($caso_id){

		try {
			$stpsH = new SolicitudTransfusionProductosSanguineosHelper();
			if(!$stpsH->validarUnidad($caso_id)){return redirect("index");}
			$formulario_data = $stpsH->getSolicitudTransfusionProductosSanguineosData($caso_id);
			return View::make("FormulariosGinecologia.solicitudTransfusionProductosSanguineos")
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
			$stpsH = new SolicitudTransfusionProductosSanguineosHelper();
			$data = $stpsH->store($request);
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
				'Campo solicitud_transfusion_productos_sanguineos_diagnostico no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_trans_previas no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_reacciones_transfusiones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_numero_embarazos no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ttpa no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_tp no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_plaq no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_hb no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_hto no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_g_rojos_cantidad no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_g_rojos_horario no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_g_rojos_cantidad, solicitud_transfusion_productos_sanguineos_g_rojos_horario no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_g_rojos_observaciones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_p_fresco_cantidad no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_p_fresco_horario no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_p_fresco_cantidad, solicitud_transfusion_productos_sanguineos_p_fresco_horario no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_p_fresco_observaciones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_plaquetas_cantidad no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_plaquetas_horario no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_plaquetas_cantidad, solicitud_transfusion_productos_sanguineos_plaquetas_horario no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_plaquetas_observaciones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_crioprec_cantidad no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_crioprec_horario no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_crioprec_cantidad, solicitud_transfusion_productos_sanguineos_crioprec_horario no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_crioprec_observaciones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_exsanguineot_horario no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad, solicitud_transfusion_productos_sanguineos_exsanguineot_horario no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_leucorreducidos no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_irradiado no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_recepcion_responsable no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_recepcion_responsable, solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_nivel_urgencia no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_reserva_pabellon no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_medico_responsable no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_reserva_pabellon, solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora, solicitud_transfusion_productos_sanguineos_medico_responsable, solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_observaciones no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha, solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha, solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_uc_resp no valido.',
				'Grupo solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles, solicitud_transfusion_productos_sanguineos_uc_hora, solicitud_transfusion_productos_sanguineos_uc_resp  no valido.',
				'Campo input_multiple no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_n_matraz no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_psl no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_cantidad no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_t no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_p_arterial no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_t_10 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso_10 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_10 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_10 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_t_30 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso_30 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_30 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_30 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_t_60 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso_60 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_60 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_60 no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_tratamiento no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_hora no valido.',
				'Campo solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion no valido.',

			];
			
			if (!in_array($error_msg, $errores_controlados)) { 
				$error_msg = "Ha ocurrido un error";
			}

			return response()->json(["status" => $error_msg],500);
		}

	}
	public function pdf($id_caso)
	{
		$sth = new SolicitudTransfusionProductosSanguineosHelper();
		$formulario_data = $sth->pdf($id_caso);
		
		$fecha = date("d-m-Y");
		
		$pdf = PDF::loadView('FormulariosGinecologia.pdf.pdfSolicitudTransfusionProductosSanguineos',
			[
				"formulario" => $formulario_data["formulario"],
				"instalaciones" => $formulario_data["instalaciones"]
			]);
		return $pdf->inline('solicitud_transfusion_productos_sanguineos_'.$fecha.'.pdf');
		
	}

}
