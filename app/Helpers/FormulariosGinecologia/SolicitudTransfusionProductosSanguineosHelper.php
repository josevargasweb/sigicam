<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioSolicitudTransfusion;
use App\Models\FormulariosGinecologia\FormularioSolicitudTransfusionInstalacion;



class SolicitudTransfusionProductosSanguineosHelper extends CasoHelper{ 

    function getData($req){

        $now_date = Carbon::now()->format('d/m/Y');
        $now_datetime = Carbon::now()->format('d/m/Y H:i:s');

        /* CAPTURAR LO QUE LLEGA */

        //solicitud_transfusion_productos_sanguineos_diagnostico
        $solicitud_transfusion_productos_sanguineos_diagnostico = (isset($req->solicitud_transfusion_productos_sanguineos_diagnostico) && trim($req->solicitud_transfusion_productos_sanguineos_diagnostico) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_diagnostico)) : null;

        //solicitud_transfusion_productos_sanguineos_trans_previas
        $solicitud_transfusion_productos_sanguineos_trans_previas = (isset($req->solicitud_transfusion_productos_sanguineos_trans_previas) && trim($req->solicitud_transfusion_productos_sanguineos_trans_previas) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_trans_previas)) : null;

        //solicitud_transfusion_productos_sanguineos_reacciones_transfusiones
        $solicitud_transfusion_productos_sanguineos_reacciones_transfusiones = (isset($req->solicitud_transfusion_productos_sanguineos_reacciones_transfusiones) && trim($req->solicitud_transfusion_productos_sanguineos_reacciones_transfusiones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_reacciones_transfusiones)) : null;

        //solicitud_transfusion_productos_sanguineos_numero_embarazos
        $solicitud_transfusion_productos_sanguineos_numero_embarazos = (isset($req->solicitud_transfusion_productos_sanguineos_numero_embarazos) && trim($req->solicitud_transfusion_productos_sanguineos_numero_embarazos) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_numero_embarazos)) : null;

        //solicitud_transfusion_productos_sanguineos_ttpa
        $solicitud_transfusion_productos_sanguineos_ttpa = (isset($req->solicitud_transfusion_productos_sanguineos_ttpa) && trim($req->solicitud_transfusion_productos_sanguineos_ttpa) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ttpa)) : null;

        //solicitud_transfusion_productos_sanguineos_tp
        $solicitud_transfusion_productos_sanguineos_tp = (isset($req->solicitud_transfusion_productos_sanguineos_tp) && trim($req->solicitud_transfusion_productos_sanguineos_tp) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_tp)) : null;

        //solicitud_transfusion_productos_sanguineos_plaq
        $solicitud_transfusion_productos_sanguineos_plaq = (isset($req->solicitud_transfusion_productos_sanguineos_plaq) && trim($req->solicitud_transfusion_productos_sanguineos_plaq) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_plaq)) : null;

        //solicitud_transfusion_productos_sanguineos_hb
        $solicitud_transfusion_productos_sanguineos_hb = (isset($req->solicitud_transfusion_productos_sanguineos_hb) && trim($req->solicitud_transfusion_productos_sanguineos_hb) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_hb)) : null;

        //solicitud_transfusion_productos_sanguineos_hto
        $solicitud_transfusion_productos_sanguineos_hto = (isset($req->solicitud_transfusion_productos_sanguineos_hto) && trim($req->solicitud_transfusion_productos_sanguineos_hto) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_hto)) : null;

        //solicitud_transfusion_productos_sanguineos_g_rojos_cantidad
        $solicitud_transfusion_productos_sanguineos_g_rojos_cantidad = (isset($req->solicitud_transfusion_productos_sanguineos_g_rojos_cantidad) && trim($req->solicitud_transfusion_productos_sanguineos_g_rojos_cantidad) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_g_rojos_cantidad)) : null;

        //solicitud_transfusion_productos_sanguineos_g_rojos_horario
        $solicitud_transfusion_productos_sanguineos_g_rojos_horario = (isset($req->solicitud_transfusion_productos_sanguineos_g_rojos_horario) && trim($req->solicitud_transfusion_productos_sanguineos_g_rojos_horario) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_g_rojos_horario)) : null;

        //solicitud_transfusion_productos_sanguineos_g_rojos_observaciones
        $solicitud_transfusion_productos_sanguineos_g_rojos_observaciones = (isset($req->solicitud_transfusion_productos_sanguineos_g_rojos_observaciones) && trim($req->solicitud_transfusion_productos_sanguineos_g_rojos_observaciones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_g_rojos_observaciones)) : null;

        //solicitud_transfusion_productos_sanguineos_p_fresco_cantidad
        $solicitud_transfusion_productos_sanguineos_p_fresco_cantidad = (isset($req->solicitud_transfusion_productos_sanguineos_p_fresco_cantidad) && trim($req->solicitud_transfusion_productos_sanguineos_p_fresco_cantidad) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_p_fresco_cantidad)) : null;

        //solicitud_transfusion_productos_sanguineos_p_fresco_horario
        $solicitud_transfusion_productos_sanguineos_p_fresco_horario = (isset($req->solicitud_transfusion_productos_sanguineos_p_fresco_horario) && trim($req->solicitud_transfusion_productos_sanguineos_p_fresco_horario) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_p_fresco_horario)) : null;

        //solicitud_transfusion_productos_sanguineos_p_fresco_observaciones
        $solicitud_transfusion_productos_sanguineos_p_fresco_observaciones = (isset($req->solicitud_transfusion_productos_sanguineos_p_fresco_observaciones) && trim($req->solicitud_transfusion_productos_sanguineos_p_fresco_observaciones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_p_fresco_observaciones)) : null;

        //solicitud_transfusion_productos_sanguineos_plaquetas_cantidad
        $solicitud_transfusion_productos_sanguineos_plaquetas_cantidad = (isset($req->solicitud_transfusion_productos_sanguineos_plaquetas_cantidad) && trim($req->solicitud_transfusion_productos_sanguineos_plaquetas_cantidad) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_plaquetas_cantidad)) : null;

        //solicitud_transfusion_productos_sanguineos_plaquetas_horario
        $solicitud_transfusion_productos_sanguineos_plaquetas_horario = (isset($req->solicitud_transfusion_productos_sanguineos_plaquetas_horario) && trim($req->solicitud_transfusion_productos_sanguineos_plaquetas_horario) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_plaquetas_horario)) : null;

        //solicitud_transfusion_productos_sanguineos_plaquetas_observaciones
        $solicitud_transfusion_productos_sanguineos_plaquetas_observaciones = (isset($req->solicitud_transfusion_productos_sanguineos_plaquetas_observaciones) && trim($req->solicitud_transfusion_productos_sanguineos_plaquetas_observaciones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_plaquetas_observaciones)) : null;

        //solicitud_transfusion_productos_sanguineos_crioprec_cantidad
        $solicitud_transfusion_productos_sanguineos_crioprec_cantidad = (isset($req->solicitud_transfusion_productos_sanguineos_crioprec_cantidad) && trim($req->solicitud_transfusion_productos_sanguineos_crioprec_cantidad) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_crioprec_cantidad)) : null;

        //solicitud_transfusion_productos_sanguineos_crioprec_horario
        $solicitud_transfusion_productos_sanguineos_crioprec_horario = (isset($req->solicitud_transfusion_productos_sanguineos_crioprec_horario) && trim($req->solicitud_transfusion_productos_sanguineos_crioprec_horario) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_crioprec_horario)) : null;

        //solicitud_transfusion_productos_sanguineos_crioprec_observaciones
        $solicitud_transfusion_productos_sanguineos_crioprec_observaciones = (isset($req->solicitud_transfusion_productos_sanguineos_crioprec_observaciones) && trim($req->solicitud_transfusion_productos_sanguineos_crioprec_observaciones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_crioprec_observaciones)) : null;

        //solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad
        $solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad = (isset($req->solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad) && trim($req->solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad)) : null;

        //solicitud_transfusion_productos_sanguineos_exsanguineot_horario
        $solicitud_transfusion_productos_sanguineos_exsanguineot_horario = (isset($req->solicitud_transfusion_productos_sanguineos_exsanguineot_horario) && trim($req->solicitud_transfusion_productos_sanguineos_exsanguineot_horario) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_exsanguineot_horario)) : null;

        //solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones
        $solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones = (isset($req->solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones) && trim($req->solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones)) : null;

        //
        
        //solicitud_transfusion_productos_sanguineos_leucorreducidos
        $solicitud_transfusion_productos_sanguineos_leucorreducidos = (isset($req->solicitud_transfusion_productos_sanguineos_leucorreducidos) && trim($req->solicitud_transfusion_productos_sanguineos_leucorreducidos) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_leucorreducidos)) : null;

        //solicitud_transfusion_productos_sanguineos_irradiado
        $solicitud_transfusion_productos_sanguineos_irradiado = (isset($req->solicitud_transfusion_productos_sanguineos_irradiado) && trim($req->solicitud_transfusion_productos_sanguineos_irradiado) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_irradiado)) : null;

        //solicitud_transfusion_productos_sanguineos_recepcion_responsable
        $solicitud_transfusion_productos_sanguineos_recepcion_responsable = (isset($req->solicitud_transfusion_productos_sanguineos_recepcion_responsable) && trim($req->solicitud_transfusion_productos_sanguineos_recepcion_responsable) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_recepcion_responsable)) : null;

        //solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora
        $solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora = (isset($req->solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora) && trim($req->solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora)) : null;

        $solicitud_transfusion_productos_sanguineos_nivel_urgencia = (isset($req->solicitud_transfusion_productos_sanguineos_nivel_urgencia) && trim($req->solicitud_transfusion_productos_sanguineos_nivel_urgencia) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_nivel_urgencia)) : null;    

        //solicitud_transfusion_productos_sanguineos_reserva_pabellon
        $solicitud_transfusion_productos_sanguineos_reserva_pabellon = (isset($req->solicitud_transfusion_productos_sanguineos_reserva_pabellon) && trim($req->solicitud_transfusion_productos_sanguineos_reserva_pabellon) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_reserva_pabellon)) : null;

        //solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora
        $solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora = (isset($req->solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora) && trim($req->solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora)) : null;

        //solicitud_transfusion_productos_sanguineos_medico_responsable
        $solicitud_transfusion_productos_sanguineos_medico_responsable = (isset($req->solicitud_transfusion_productos_sanguineos_medico_responsable) && trim($req->solicitud_transfusion_productos_sanguineos_medico_responsable) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_medico_responsable)) : null;

        //solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora
        $solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora = (isset($req->solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora) && trim($req->solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora)) : null;

        //solicitud_transfusion_productos_sanguineos_observaciones
        $solicitud_transfusion_productos_sanguineos_observaciones = (isset($req->solicitud_transfusion_productos_sanguineos_observaciones) && trim($req->solicitud_transfusion_productos_sanguineos_observaciones) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_observaciones)) : null;

        //solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha
        $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha = (isset($req->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha) && trim($req->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha)) : null;

        //solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp
        $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp = (isset($req->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp) && trim($req->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp)) : null;

        //solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha
        $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha = (isset($req->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha) && trim($req->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha)) : null;

        //solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp
        $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp = (isset($req->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp) && trim($req->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp)) : null;

        //solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd
        $solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd = (isset($req->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd) && trim($req->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd)) : null;

        //solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca
        $solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca = (isset($req->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca) && trim($req->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca)) : null;

        //solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles
        $solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles = (isset($req->solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles) && trim($req->solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles)) : null;    
        
        //solicitud_transfusion_productos_sanguineos_uc_hora
        $solicitud_transfusion_productos_sanguineos_uc_hora = (isset($req->solicitud_transfusion_productos_sanguineos_uc_hora) && trim($req->solicitud_transfusion_productos_sanguineos_uc_hora) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_uc_hora)) : null;  
        
        //solicitud_transfusion_productos_sanguineos_uc_resp
        $solicitud_transfusion_productos_sanguineos_uc_resp = (isset($req->solicitud_transfusion_productos_sanguineos_uc_resp) && trim($req->solicitud_transfusion_productos_sanguineos_uc_resp) !== "") ?
        trim(strip_tags($req->solicitud_transfusion_productos_sanguineos_uc_resp)) : null; 

        //Instalacion 

        //solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora 
        $solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora);

        //solicitud_transfusion_productos_sanguineos_instalacion_n_matraz 
        $solicitud_transfusion_productos_sanguineos_instalacion_n_matraz = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_n_matraz);

        //solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo 
        $solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo);

        //solicitud_transfusion_productos_sanguineos_instalacion_psl 
        $solicitud_transfusion_productos_sanguineos_instalacion_psl = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_psl);

        //solicitud_transfusion_productos_sanguineos_instalacion_cantidad 
        $solicitud_transfusion_productos_sanguineos_instalacion_cantidad = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_cantidad);

        //solicitud_transfusion_productos_sanguineos_instalacion_t 
        $solicitud_transfusion_productos_sanguineos_instalacion_t = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_t);

        //solicitud_transfusion_productos_sanguineos_instalacion_pulso 
        $solicitud_transfusion_productos_sanguineos_instalacion_pulso = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pulso);

        //solicitud_transfusion_productos_sanguineos_instalacion_p_arterial 
        $solicitud_transfusion_productos_sanguineos_instalacion_p_arterial = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_p_arterial);

        //solicitud_transfusion_productos_sanguineos_instalacion_responsable 
        $solicitud_transfusion_productos_sanguineos_instalacion_responsable =
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_responsable);

        //solicitud_transfusion_productos_sanguineos_instalacion_t_10 
        $solicitud_transfusion_productos_sanguineos_instalacion_t_10 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_t_10);

        //solicitud_transfusion_productos_sanguineos_instalacion_pulso_10 
        $solicitud_transfusion_productos_sanguineos_instalacion_pulso_10 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pulso_10);


        //solicitud_transfusion_productos_sanguineos_instalacion_pa_10 
        $solicitud_transfusion_productos_sanguineos_instalacion_pa_10 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pa_10);


        //solicitud_transfusion_productos_sanguineos_instalacion_responsable_10 
        $solicitud_transfusion_productos_sanguineos_instalacion_responsable_10 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_responsable_10);


        //solicitud_transfusion_productos_sanguineos_instalacion_t_30 
        $solicitud_transfusion_productos_sanguineos_instalacion_t_30 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_t_30);


        //solicitud_transfusion_productos_sanguineos_instalacion_pulso_30 
        $solicitud_transfusion_productos_sanguineos_instalacion_pulso_30 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pulso_30);


        //solicitud_transfusion_productos_sanguineos_instalacion_pa_30 
        $solicitud_transfusion_productos_sanguineos_instalacion_pa_30 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pa_30);


        //solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora 
        $solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora);


        //solicitud_transfusion_productos_sanguineos_instalacion_responsable_30 
        $solicitud_transfusion_productos_sanguineos_instalacion_responsable_30 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_responsable_30);


        //solicitud_transfusion_productos_sanguineos_instalacion_t_60 
        $solicitud_transfusion_productos_sanguineos_instalacion_t_60 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_t_60);


        //solicitud_transfusion_productos_sanguineos_instalacion_pulso_60 
        $solicitud_transfusion_productos_sanguineos_instalacion_pulso_60 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pulso_60);


        //solicitud_transfusion_productos_sanguineos_instalacion_pa_60 
        $solicitud_transfusion_productos_sanguineos_instalacion_pa_60 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pa_60);


        //solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora 
        $solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora);


        //solicitud_transfusion_productos_sanguineos_instalacion_responsable_60 
        $solicitud_transfusion_productos_sanguineos_instalacion_responsable_60 = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_responsable_60);


        //solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional 
        $solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional);


        //solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat 
        $solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat);


        //solicitud_transfusion_productos_sanguineos_instalacion_tratamiento 
        $solicitud_transfusion_productos_sanguineos_instalacion_tratamiento = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_tratamiento);


        //solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable 
        $solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable);


        //solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra 
        $solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra);


        //solicitud_transfusion_productos_sanguineos_instalacion_hora 
        $solicitud_transfusion_productos_sanguineos_instalacion_hora = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_hora);

        //solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion
        $solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion);

        //instalaciones rows ordenadas
        $solicitud_transfusion_productos_sanguineos_instalaciones = $this->ordenamientoInputsMultiples(
            array(
                'solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora,
                'solicitud_transfusion_productos_sanguineos_instalacion_n_matraz'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_n_matraz,
                'solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo,
                'solicitud_transfusion_productos_sanguineos_instalacion_psl'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_psl,
                'solicitud_transfusion_productos_sanguineos_instalacion_cantidad'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_cantidad,
                'solicitud_transfusion_productos_sanguineos_instalacion_t'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_t,
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pulso,
                'solicitud_transfusion_productos_sanguineos_instalacion_p_arterial'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_p_arterial,
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_responsable,
                'solicitud_transfusion_productos_sanguineos_instalacion_t_10'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_t_10,
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso_10'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pulso_10,
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_10'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pa_10,
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_10'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_responsable_10,
                'solicitud_transfusion_productos_sanguineos_instalacion_t_30'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_t_30,
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso_30'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pulso_30,
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_30'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pa_30,
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora,
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_30'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_responsable_30,
                'solicitud_transfusion_productos_sanguineos_instalacion_t_60'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_t_60,
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso_60'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pulso_60,
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_60'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pa_60,
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora,
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_60'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_responsable_60,
                'solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional'  =>
                $solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional,
                'solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat'  =>                $solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat,
                'solicitud_transfusion_productos_sanguineos_instalacion_tratamiento'  => 
                $solicitud_transfusion_productos_sanguineos_instalacion_tratamiento,
                'solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable'  => 
                $solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable,
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra'  => 
                $solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra,
                'solicitud_transfusion_productos_sanguineos_instalacion_hora'  => 
                $solicitud_transfusion_productos_sanguineos_instalacion_hora,
                'solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion'  => 
                $solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion,
            )
        );


        //--//

        //caso_id
        $caso_id = (isset($req->caso_id) && trim($req->caso_id) !== "") ?
        trim(strip_tags($req->caso_id)) : null;

        //form_id
        $form_id = (isset($req->form_id) && trim($req->form_id) !== "") ?
        trim(strip_tags($req->form_id)) : null;

        /* VALIDAR DOMINIO DE LO QUE LLEGA */

        //solicitud_transfusion_productos_sanguineos_diagnostico
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_diagnostico) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_diagnostico no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_trans_previas
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_trans_previas) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_trans_previas no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_reacciones_transfusiones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_reacciones_transfusiones) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_reacciones_transfusiones no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_numero_embarazos
        if (mb_strlen($solicitud_transfusion_productos_sanguineos_numero_embarazos) > 0 &&   
            (
            filter_var( $solicitud_transfusion_productos_sanguineos_numero_embarazos, FILTER_VALIDATE_INT ) === false ||  
            $solicitud_transfusion_productos_sanguineos_numero_embarazos < 0 || $solicitud_transfusion_productos_sanguineos_numero_embarazos > 42
            )
        ) {
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_numero_embarazos no valido.');       
        }

        //solicitud_transfusion_productos_sanguineos_ttpa
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_ttpa) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ttpa no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_tp
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_tp) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_tp no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_plaq
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_plaq) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_plaq no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_hb
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_hb) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_hb no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_hto
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_hto) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_hto no valido.');
        }

        /* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_g_rojos_cantidad, $solicitud_transfusion_productos_sanguineos_g_rojos_horario))){

            //solicitud_transfusion_productos_sanguineos_g_rojos_cantidad
            $solicitud_transfusion_productos_sanguineos_g_rojos_cantidad_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_g_rojos_cantidad) <= 60;
            if(!$solicitud_transfusion_productos_sanguineos_g_rojos_cantidad_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_g_rojos_cantidad no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_g_rojos_horario        
            if(isset($solicitud_transfusion_productos_sanguineos_g_rojos_horario) ){

                $solicitud_transfusion_productos_sanguineos_g_rojos_horario_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_g_rojos_horario , 'd/m/Y H:i:s') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_g_rojos_horario, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_g_rojos_horario_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_g_rojos_horario no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_g_rojos_horario =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_g_rojos_horario)->format('Y-m-d H:i:s');

            } 

        }
        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_g_rojos_cantidad, solicitud_transfusion_productos_sanguineos_g_rojos_horario no valido.');
        }


        //solicitud_transfusion_productos_sanguineos_g_rojos_observaciones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_g_rojos_observaciones) > 300){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_g_rojos_observaciones no valido.'); }


        /* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_p_fresco_cantidad, $solicitud_transfusion_productos_sanguineos_p_fresco_horario))){

            //solicitud_transfusion_productos_sanguineos_p_fresco_cantidad
            $solicitud_transfusion_productos_sanguineos_p_fresco_cantidad_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_p_fresco_cantidad) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_p_fresco_cantidad_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_p_fresco_cantidad no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_p_fresco_horario
            if(isset($solicitud_transfusion_productos_sanguineos_p_fresco_horario) ){
                $solicitud_transfusion_productos_sanguineos_p_fresco_horario_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_p_fresco_horario , 'd/m/Y H:i:s') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_p_fresco_horario, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_p_fresco_horario_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_p_fresco_horario no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_p_fresco_horario =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_p_fresco_horario)->format('Y-m-d H:i:s');

            }

        }
        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_p_fresco_cantidad, solicitud_transfusion_productos_sanguineos_p_fresco_horario no valido.');
        }


        //solicitud_transfusion_productos_sanguineos_p_fresco_observaciones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_p_fresco_observaciones) > 300){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_p_fresco_observaciones no valido.'); 
        }

        /* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_plaquetas_cantidad, $solicitud_transfusion_productos_sanguineos_plaquetas_horario))){

            //solicitud_transfusion_productos_sanguineos_plaquetas_cantidad
            $solicitud_transfusion_productos_sanguineos_plaquetas_cantidad_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_plaquetas_cantidad) <= 60;


            if(!$solicitud_transfusion_productos_sanguineos_plaquetas_cantidad_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_plaquetas_cantidad no valido.');
            }
            
            //solicitud_transfusion_productos_sanguineos_plaquetas_horario
            if(isset($solicitud_transfusion_productos_sanguineos_plaquetas_horario) ){
                $solicitud_transfusion_productos_sanguineos_plaquetas_horario_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_plaquetas_horario , 'd/m/Y H:i:s') && 
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_plaquetas_horario, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_plaquetas_horario_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_plaquetas_horario no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_plaquetas_horario =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_plaquetas_horario)->format('Y-m-d H:i:s');

            }

        }
        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_plaquetas_cantidad, solicitud_transfusion_productos_sanguineos_plaquetas_horario no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_plaquetas_observaciones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_plaquetas_observaciones) > 300){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_plaquetas_observaciones no valido.'); 
        }

        /* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_crioprec_cantidad, $solicitud_transfusion_productos_sanguineos_crioprec_horario))){

            //solicitud_transfusion_productos_sanguineos_crioprec_cantidad
            $solicitud_transfusion_productos_sanguineos_crioprec_cantidad_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_crioprec_cantidad) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_crioprec_cantidad_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_crioprec_cantidad no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_crioprec_horario
            if(isset($solicitud_transfusion_productos_sanguineos_crioprec_horario) ){
                $solicitud_transfusion_productos_sanguineos_crioprec_horario_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_crioprec_horario , 'd/m/Y H:i:s') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_crioprec_horario, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_crioprec_horario_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_crioprec_horario no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_crioprec_horario =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_crioprec_horario)->format('Y-m-d H:i:s');

            }

        }
        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_crioprec_cantidad, solicitud_transfusion_productos_sanguineos_crioprec_horario no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_crioprec_observaciones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_crioprec_observaciones) > 300){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_crioprec_observaciones no valido.'); 
        }

        /* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad, $solicitud_transfusion_productos_sanguineos_exsanguineot_horario))){

            //solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad
            $solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_exsanguineot_horario
            if(isset($solicitud_transfusion_productos_sanguineos_exsanguineot_horario) ){
                $solicitud_transfusion_productos_sanguineos_exsanguineot_horario_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_exsanguineot_horario , 'd/m/Y H:i:s') && 
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_exsanguineot_horario, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_exsanguineot_horario_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_exsanguineot_horario no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_exsanguineot_horario =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_exsanguineot_horario)->format('Y-m-d H:i:s');

            }

        }
        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad, solicitud_transfusion_productos_sanguineos_exsanguineot_horario no valido.');
        }

        //solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones) > 300){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones no valido.'); 
        }

        //

        //solicitud_transfusion_productos_sanguineos_leucorreducidos
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_leucorreducidos) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_leucorreducidos no valido.');
        }
        
        //solicitud_transfusion_productos_sanguineos_irradiado
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_irradiado) > 60){ 
            throw new Exception('Campo solicitud_transfusion_productos_sanguineos_irradiado no valido.');
        }


        /* GRUPO */

        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_recepcion_responsable, $solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora))){

            //solicitud_transfusion_productos_sanguineos_recepcion_responsable
            $solicitud_transfusion_productos_sanguineos_recepcion_responsable_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_recepcion_responsable) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_recepcion_responsable_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_recepcion_responsable no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora
            if(isset($solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora) ){

                $solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora , 'd/m/Y H:i:s') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora)->format('Y-m-d H:i:s');

            }


        }

        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_recepcion_responsable, solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora no valido.');            
        }


        //solicitud_transfusion_productos_sanguineos_nivel_urgencia
        $solicitud_transfusion_productos_sanguineos_nivel_urgencia_valid = [
            "inmediata",
            "urgente",
            "no_urgente"
        ];

        if (!in_array($solicitud_transfusion_productos_sanguineos_nivel_urgencia, $solicitud_transfusion_productos_sanguineos_nivel_urgencia_valid)) { throw new Exception('Campo solicitud_transfusion_productos_sanguineos_nivel_urgencia no valido.'); }

        /* GRUPO */

        if(parent::allOrNone(
            array(
                $solicitud_transfusion_productos_sanguineos_reserva_pabellon, $solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora,
                $solicitud_transfusion_productos_sanguineos_medico_responsable,
                $solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora
                )
            )
        ){

            //solicitud_transfusion_productos_sanguineos_reserva_pabellon
            if( isset($solicitud_transfusion_productos_sanguineos_reserva_pabellon) &&
                $solicitud_transfusion_productos_sanguineos_reserva_pabellon !== "reserva_pabellon"){
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_reserva_pabellon no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora
            if(isset($solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora) ){

                $solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora , 'd/m/Y H:i:s');

                if(!$solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora)->format('Y-m-d H:i:s');

            }

            //solicitud_transfusion_productos_sanguineos_medico_responsable
            if(mb_strlen($solicitud_transfusion_productos_sanguineos_medico_responsable) > 60){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_medico_responsable no valido.');
            }

            //solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora

            if(isset($solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora) ){
                $solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora , 'd/m/Y H:i:s') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora, $now_datetime, 'd/m/Y H:i:s', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora =
                Carbon::createFromFormat('d/m/Y H:i:s', $solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora)->format('Y-m-d H:i:s');

            }

        }
        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_reserva_pabellon, solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora, solicitud_transfusion_productos_sanguineos_medico_responsable, solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora no valido.');   

        }   

        //solicitud_transfusion_productos_sanguineos_observaciones
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_observaciones) > 300){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_observaciones no valido.'); 
        }

        /* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha, $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp))){

            //solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha

            if(isset($solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha) ){

                $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha , 'd/m/Y') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha, $now_date, 'd/m/Y', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha =
                Carbon::createFromFormat('d/m/Y', $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha)->format('Y-m-d');

            }


            //solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp
            $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp no valido.');
            }

        }

        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha, solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp no valido.');            
        }

        //* GRUPO */
        if(parent::allOrNone(array($solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha, $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp))){

            //solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha
            if(isset($solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha) ){

                $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha , 'd/m/Y') &&
                parent::dateComparison($solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha, $now_date, 'd/m/Y', 'a<=b');

                if(!$solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha =
                Carbon::createFromFormat('d/m/Y', $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha)->format('Y-m-d');

            }


            //solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp
            $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp no valido.');
            }

        }

        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha, solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp no valido.');            
        }

        //solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd) > 60){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd no valido.'); 
        }

        //solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca
        if(mb_strlen($solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca) > 60){ throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca no valido.'); 
        }

        //* GRUPO */
        if(parent::allOrNone(
            array(
                $solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles, $solicitud_transfusion_productos_sanguineos_uc_hora,
                $solicitud_transfusion_productos_sanguineos_uc_resp
                )
            )
        ){

            //solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles
            $solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles) <= 300;

            if(!$solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles no valido.');
            }


            //solicitud_transfusion_productos_sanguineos_uc_hora

            if(isset($solicitud_transfusion_productos_sanguineos_uc_hora) ){

                $solicitud_transfusion_productos_sanguineos_uc_hora_is_valid = 
                parent::validateDate($solicitud_transfusion_productos_sanguineos_uc_hora , 'H:i:s');

                if(!$solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha_is_valid){
                    throw new Exception('Campo solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha no valido.');
                }

                $solicitud_transfusion_productos_sanguineos_uc_hora =
                Carbon::createFromFormat('H:i:s', $solicitud_transfusion_productos_sanguineos_uc_hora)->format('H:i:s');

            }


            //solicitud_transfusion_productos_sanguineos_uc_resp
            $solicitud_transfusion_productos_sanguineos_uc_resp_is_valid = mb_strlen($solicitud_transfusion_productos_sanguineos_uc_resp) <= 60;

            if(!$solicitud_transfusion_productos_sanguineos_uc_resp_is_valid){ 
                throw new Exception('Campo solicitud_transfusion_productos_sanguineos_uc_resp no valido.');
            }

        }

        else {
            throw new Exception('Grupo solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles, solicitud_transfusion_productos_sanguineos_uc_hora, solicitud_transfusion_productos_sanguineos_uc_resp  no valido.');            
        }

        //instalaciones


        foreach ($solicitud_transfusion_productos_sanguineos_instalaciones as $key => $row){

            foreach ($row as $key => $input){

                //solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora
                if($key === "solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora"){

                    $input_is_valid = isset($input) && parent::validateDate($input , 'd/m/Y H:i:s');
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora no valido.');
                    }

                }
                
                //solicitud_transfusion_productos_sanguineos_instalacion_n_matraz
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_n_matraz"){
                    $input_is_valid = isset($input) && mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_n_matraz no valido.');
                    }

                }

                //solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo"){
                    $input_is_valid = isset($input) && mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_psl
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_psl"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_psl no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_cantidad
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_cantidad"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_cantidad no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_t
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_t"){

                    $input_is_valid = (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_FLOAT ) &&parent::numberOfDecimals($input) <= 1 && $input >=34;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_t no valido.');
                    }
                } 

                //solicitud_transfusion_productos_sanguineos_instalacion_pulso
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pulso"){
                    (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_INT ) && $input >=10 &&  $input <=300;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_p_arterial
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_p_arterial"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_p_arterial no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_responsable
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_responsable"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_t_10
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_t_10"){
                    $input_is_valid = (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_FLOAT )  &&parent::numberOfDecimals($input) <= 1 && $input >=34;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_t_10 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pulso_10
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pulso_10"){
                    (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_INT ) && $input >=10 &&  $input <=300;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso_10 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pa_10
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pa_10"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_10 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_responsable_10
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_responsable_10"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_10 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_t_30
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_t_30"){
                    $input_is_valid = (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_FLOAT )  &&parent::numberOfDecimals($input) <= 1 && $input >=34;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_t_30 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pulso_30
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pulso_30"){
                    (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_INT ) && $input >=10 &&  $input <=300;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso_30 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pa_30
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pa_30"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_30 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora"){

                    $input_is_valid = !isset($input) || parent::validateDate($input , 'H:i:s');
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_responsable_30
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_responsable_30"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_30 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_t_60
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_t_60"){
                    $input_is_valid = (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_FLOAT )  &&parent::numberOfDecimals($input) <= 1 && $input >=34;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_t_60 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pulso_60
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pulso_60"){
                    (mb_strlen($input) === 0) ? true : filter_var( $input, FILTER_VALIDATE_INT ) && $input >=10 &&  $input <=300;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pulso_60 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pa_60
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pa_60"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_60 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora"){
                    $input_is_valid = !isset($input) || parent::validateDate($input , 'H:i:s');
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_responsable_60
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_responsable_60"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_60 no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_tratamiento
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_tratamiento"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_tratamiento no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra"){
                    $input_is_valid = mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra no valido.');
                    }
                }
                
                //solicitud_transfusion_productos_sanguineos_instalacion_hora
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_hora"){
                    $input_is_valid = !isset($input) || parent::validateDate($input , 'H:i:s');
                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_hora no valido.');
                    }
                }

                //solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion
                else if ($key === "solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion"){
                    $input_is_valid = (mb_strlen($input) === 0) ? true :    
                    filter_var( $input, FILTER_VALIDATE_INT );

                    if(!$input_is_valid){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion no valido.');
                    }
                }

            }
            
        }

        //caso_id
        $caso = parent::getCasoById($caso_id);
        if(!isset($caso)){ throw new Exception('Campo caso_id no valido.');  }

        $data = (object)[
            "solicitud_transfusion_productos_sanguineos_diagnostico" => 
            $solicitud_transfusion_productos_sanguineos_diagnostico,
            "solicitud_transfusion_productos_sanguineos_trans_previas" => 
            $solicitud_transfusion_productos_sanguineos_trans_previas,
            "solicitud_transfusion_productos_sanguineos_reacciones_transfusiones" => $solicitud_transfusion_productos_sanguineos_reacciones_transfusiones,
            "solicitud_transfusion_productos_sanguineos_numero_embarazos" => $solicitud_transfusion_productos_sanguineos_numero_embarazos,
            "solicitud_transfusion_productos_sanguineos_ttpa" => 
            $solicitud_transfusion_productos_sanguineos_ttpa,
            "solicitud_transfusion_productos_sanguineos_tp" => 
            $solicitud_transfusion_productos_sanguineos_tp,
            "solicitud_transfusion_productos_sanguineos_plaq" => 
            $solicitud_transfusion_productos_sanguineos_plaq,
            "solicitud_transfusion_productos_sanguineos_hb" => 
            $solicitud_transfusion_productos_sanguineos_hb,
            "solicitud_transfusion_productos_sanguineos_hto" => 
            $solicitud_transfusion_productos_sanguineos_hto,
            "solicitud_transfusion_productos_sanguineos_g_rojos_cantidad" => $solicitud_transfusion_productos_sanguineos_g_rojos_cantidad,
            "solicitud_transfusion_productos_sanguineos_g_rojos_horario" => $solicitud_transfusion_productos_sanguineos_g_rojos_horario,
            "solicitud_transfusion_productos_sanguineos_g_rojos_observaciones" => $solicitud_transfusion_productos_sanguineos_g_rojos_observaciones,
            "solicitud_transfusion_productos_sanguineos_p_fresco_cantidad" => $solicitud_transfusion_productos_sanguineos_p_fresco_cantidad,
            "solicitud_transfusion_productos_sanguineos_p_fresco_horario" => $solicitud_transfusion_productos_sanguineos_p_fresco_horario,
            "solicitud_transfusion_productos_sanguineos_p_fresco_observaciones" => $solicitud_transfusion_productos_sanguineos_p_fresco_observaciones,
            "solicitud_transfusion_productos_sanguineos_plaquetas_cantidad" => 
            $solicitud_transfusion_productos_sanguineos_plaquetas_cantidad,
            "solicitud_transfusion_productos_sanguineos_plaquetas_horario" => 
            $solicitud_transfusion_productos_sanguineos_plaquetas_horario,
            "solicitud_transfusion_productos_sanguineos_plaquetas_observaciones" => 
            $solicitud_transfusion_productos_sanguineos_plaquetas_observaciones,
            "solicitud_transfusion_productos_sanguineos_crioprec_cantidad" => $solicitud_transfusion_productos_sanguineos_crioprec_cantidad,
            "solicitud_transfusion_productos_sanguineos_crioprec_horario" => 
            $solicitud_transfusion_productos_sanguineos_crioprec_horario,
            "solicitud_transfusion_productos_sanguineos_crioprec_observaciones" => 
            $solicitud_transfusion_productos_sanguineos_crioprec_observaciones,
            "solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad" => 
            $solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad,
            "solicitud_transfusion_productos_sanguineos_exsanguineot_horario" => 
            $solicitud_transfusion_productos_sanguineos_exsanguineot_horario,
            "solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones" => 
            $solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones,
            "solicitud_transfusion_productos_sanguineos_leucorreducidos" => 
            $solicitud_transfusion_productos_sanguineos_leucorreducidos,
            "solicitud_transfusion_productos_sanguineos_irradiado" => 
            $solicitud_transfusion_productos_sanguineos_irradiado,
            "solicitud_transfusion_productos_sanguineos_recepcion_responsable" => 
            $solicitud_transfusion_productos_sanguineos_recepcion_responsable,
            "solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora" => 
            $solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora,
            "solicitud_transfusion_productos_sanguineos_nivel_urgencia" => 
            $solicitud_transfusion_productos_sanguineos_nivel_urgencia,
            "solicitud_transfusion_productos_sanguineos_reserva_pabellon" => 
            isset($solicitud_transfusion_productos_sanguineos_reserva_pabellon) ? true : false,
            "solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora" => 
            $solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora,
            "solicitud_transfusion_productos_sanguineos_medico_responsable" => 
            $solicitud_transfusion_productos_sanguineos_medico_responsable,
            "solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora" => 
            $solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora,
            "solicitud_transfusion_productos_sanguineos_observaciones" => 
            $solicitud_transfusion_productos_sanguineos_observaciones,
            "solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha" => 
            $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha,
            "solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp" => 
            $solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp,
            "solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha" => 
            $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha,
            "solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp" => 
            $solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp,
            "solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd" => 
            $solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd,
            "solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca" => 
            $solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca,
            "solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles" => 
            $solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles,
            "solicitud_transfusion_productos_sanguineos_uc_hora" => 
            $solicitud_transfusion_productos_sanguineos_uc_hora,
            "solicitud_transfusion_productos_sanguineos_uc_resp" => 
            $solicitud_transfusion_productos_sanguineos_uc_resp,
            "solicitud_transfusion_productos_sanguineos_instalaciones" =>
            $solicitud_transfusion_productos_sanguineos_instalaciones,
            "form_id" => $form_id,
            "usuario_responsable" => Auth::user()->id,
            "caso" => $caso,
            "caso_id" =>$caso[0]->caso_id,
            "paciente_id" => $caso[0]->paciente_id

        ];

        return $data;


    } 


    function store($req){

        $data = $this->getData($req);
    
        /* PERSISTIR */
        if(!isset($data->form_id)){
            $form_bd = $this->getSolicitudTransfusionProductosSanguineosByCasoId($data->caso_id);
            if(isset($form_bd)){ throw new Exception('Campo form_bd no valido.');  }

            $form = new FormularioSolicitudTransfusion();
            $form->caso = $data->caso_id;
            $form->id_paciente = $data->paciente_id ;
            $form->usuario_responsable = $data->usuario_responsable;
            $form->fecha = Carbon::now()->format("Y-m-d H:i:s");

            //
            $form->diagnostico = $data->solicitud_transfusion_productos_sanguineos_diagnostico;
            $form->transf_previas = $data->solicitud_transfusion_productos_sanguineos_trans_previas;
            $form->reacciones_transfusiones = $data->solicitud_transfusion_productos_sanguineos_reacciones_transfusiones;
            $form->n_embarazos = $data->solicitud_transfusion_productos_sanguineos_numero_embarazos;
            $form->ttpa = $data->solicitud_transfusion_productos_sanguineos_ttpa;
            $form->tp = $data->solicitud_transfusion_productos_sanguineos_tp;
            $form->plaq = $data->solicitud_transfusion_productos_sanguineos_plaq;
            $form->hb = $data->solicitud_transfusion_productos_sanguineos_hb;
            $form->hto = $data->solicitud_transfusion_productos_sanguineos_hto;
            $form->g_rojos_cantidad = $data->solicitud_transfusion_productos_sanguineos_g_rojos_cantidad;
            $form->g_rojos_horario = $data->solicitud_transfusion_productos_sanguineos_g_rojos_horario;
            $form->g_rojos_observaciones = $data->solicitud_transfusion_productos_sanguineos_g_rojos_observaciones;
            $form->p_fresco_cantidad = $data->solicitud_transfusion_productos_sanguineos_p_fresco_cantidad;
            $form->p_fresco_horario = $data->solicitud_transfusion_productos_sanguineos_p_fresco_horario;
            $form->p_fresco_observaciones = $data->solicitud_transfusion_productos_sanguineos_p_fresco_observaciones;
            $form->plaquetas_cantidad = $data->solicitud_transfusion_productos_sanguineos_plaquetas_cantidad;
            $form->plaquetas_horario = $data->solicitud_transfusion_productos_sanguineos_plaquetas_horario;
            $form->plaquetas_observaciones = $data->solicitud_transfusion_productos_sanguineos_plaquetas_observaciones;
            $form->crioprec_cantidad = $data->solicitud_transfusion_productos_sanguineos_crioprec_cantidad;
            $form->crioprec_horario = $data->solicitud_transfusion_productos_sanguineos_crioprec_horario;
            $form->crioprec_observaciones = $data->solicitud_transfusion_productos_sanguineos_crioprec_observaciones;
            $form->exsanguineot_cantidad = $data->solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad;
            $form->exsanguineot_horario = $data->solicitud_transfusion_productos_sanguineos_exsanguineot_horario;
            $form->exsanguineot_observaciones = $data->solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones;
            $form->leucorreducidos = $data->solicitud_transfusion_productos_sanguineos_leucorreducidos;
            $form->irradiado = $data->solicitud_transfusion_productos_sanguineos_irradiado;
            $form->responsable_recepcion = $data->solicitud_transfusion_productos_sanguineos_recepcion_responsable;
            $form->fecha_recepcion = $data->solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora;
            $form->gravedad = $data->solicitud_transfusion_productos_sanguineos_nivel_urgencia;
            $form->reserva_pabellon = $data->solicitud_transfusion_productos_sanguineos_reserva_pabellon;
            $form->fecha_reserva_pabellon = $data->solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora;
            $form->medico_responsable = $data->solicitud_transfusion_productos_sanguineos_medico_responsable;
            $form->fecha_solicitud = $data->solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora;
            $form->observaciones = $data->solicitud_transfusion_productos_sanguineos_observaciones;
            $form->clasific_abo_fecha = $data->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha;
            $form->clasific_abo_resp = $data->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp;
            $form->reclasific_abo_fecha = $data->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha;
            $form->reclasific_abo_resp = $data->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp;
            $form->ac_irregulares_tcd = $data->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd;
            $form->ac_irregulares_ca = $data->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca;
            $form->unidades_compatibles = $data->solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles;
            $form->unidades_compatibles_hora = $data->solicitud_transfusion_productos_sanguineos_uc_hora;
            $form->unidades_compatibles_resp = $data->solicitud_transfusion_productos_sanguineos_uc_resp;
            $form->save();

            //instalaciones
            foreach ($data->solicitud_transfusion_productos_sanguineos_instalaciones as $key => $instalacion_data){
                $instalacion = new FormularioSolicitudTransfusionInstalacion();
                $instalacion->caso = $form->caso;
                $instalacion->id_paciente = $form->id_paciente;
                $instalacion->id_formulario_solicitud_transfusion =$form->id_formulario_solicitud_transfusion;
                $instalacion->fecha_instalacion = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora;
                $instalacion->n_maltraz = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_n_matraz;
                $instalacion->grupo_abo = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo;
                $instalacion->psl = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_psl;
                $instalacion->cantidad = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_cantidad;
                $instalacion->temp = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t;
                $instalacion->pulso = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso;
                $instalacion->p_arterial = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_p_arterial;
                $instalacion->responsable = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable;
                $instalacion->temp_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t_10;
                $instalacion->pulso_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso_10;
                $instalacion->p_arterial_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_10;
                $instalacion->responsable_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_10;
                $instalacion->temp_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t_30;
                $instalacion->pulso_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso_30;
                $instalacion->p_arterial_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_30;
                $instalacion->hora_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora;
                $instalacion->responsable_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_30;
                $instalacion->temp_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t_60;
                $instalacion->pulso_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso_60;
                $instalacion->p_arterial_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_60;
                $instalacion->hora_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora;
                $instalacion->responsable_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_60;
                $instalacion->reaccion_adversa_transfusional = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional;
                $instalacion->folio_ficha_rat = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat;
                $instalacion->tratamiento = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_tratamiento;
                $instalacion->medico_responsable = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable;
                $instalacion->responsable_toma_muestra = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra;
                $instalacion->hora = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_hora;
                $instalacion->fecha = Carbon::now()->format("Y-m-d H:i:s");
                $instalacion->save();
            }

        }

        /* ACTUALIZAR */
        else {
            $form = $this->getSolicitudTransfusionProductosSanguineosById($data->form_id);
            if(!isset($form)){ throw new Exception('Campo form_id no valido.');  }
            $form->diagnostico = $data->solicitud_transfusion_productos_sanguineos_diagnostico;
            $form->transf_previas = $data->solicitud_transfusion_productos_sanguineos_trans_previas;
            $form->reacciones_transfusiones = $data->solicitud_transfusion_productos_sanguineos_reacciones_transfusiones;
            $form->n_embarazos = $data->solicitud_transfusion_productos_sanguineos_numero_embarazos;
            $form->ttpa = $data->solicitud_transfusion_productos_sanguineos_ttpa;
            $form->tp = $data->solicitud_transfusion_productos_sanguineos_tp;
            $form->plaq = $data->solicitud_transfusion_productos_sanguineos_plaq;
            $form->hb = $data->solicitud_transfusion_productos_sanguineos_hb;
            $form->hto = $data->solicitud_transfusion_productos_sanguineos_hto;
            $form->g_rojos_cantidad = $data->solicitud_transfusion_productos_sanguineos_g_rojos_cantidad;
            $form->g_rojos_horario = $data->solicitud_transfusion_productos_sanguineos_g_rojos_horario;
            $form->g_rojos_observaciones = $data->solicitud_transfusion_productos_sanguineos_g_rojos_observaciones;
            $form->p_fresco_cantidad = $data->solicitud_transfusion_productos_sanguineos_p_fresco_cantidad;
            $form->p_fresco_horario = $data->solicitud_transfusion_productos_sanguineos_p_fresco_horario;
            $form->p_fresco_observaciones = $data->solicitud_transfusion_productos_sanguineos_p_fresco_observaciones;
            $form->plaquetas_cantidad = $data->solicitud_transfusion_productos_sanguineos_plaquetas_cantidad;
            $form->plaquetas_horario = $data->solicitud_transfusion_productos_sanguineos_plaquetas_horario;
            $form->plaquetas_observaciones = $data->solicitud_transfusion_productos_sanguineos_plaquetas_observaciones;
            $form->crioprec_cantidad = $data->solicitud_transfusion_productos_sanguineos_crioprec_cantidad;
            $form->crioprec_horario = $data->solicitud_transfusion_productos_sanguineos_crioprec_horario;
            $form->crioprec_observaciones = $data->solicitud_transfusion_productos_sanguineos_crioprec_observaciones;
            $form->exsanguineot_cantidad = $data->solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad;
            $form->exsanguineot_horario = $data->solicitud_transfusion_productos_sanguineos_exsanguineot_horario;
            $form->exsanguineot_observaciones = $data->solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones;
            $form->leucorreducidos = $data->solicitud_transfusion_productos_sanguineos_leucorreducidos;
            $form->irradiado = $data->solicitud_transfusion_productos_sanguineos_irradiado;
            $form->responsable_recepcion = $data->solicitud_transfusion_productos_sanguineos_recepcion_responsable;
            $form->fecha_recepcion = $data->solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora;
            $form->gravedad = $data->solicitud_transfusion_productos_sanguineos_nivel_urgencia;
            $form->reserva_pabellon = $data->solicitud_transfusion_productos_sanguineos_reserva_pabellon;
            $form->fecha_reserva_pabellon = $data->solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora;
            $form->medico_responsable = $data->solicitud_transfusion_productos_sanguineos_medico_responsable;
            $form->fecha_solicitud = $data->solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora;
            $form->observaciones = $data->solicitud_transfusion_productos_sanguineos_observaciones;
            $form->clasific_abo_fecha = $data->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha;
            $form->clasific_abo_resp = $data->solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp;
            $form->reclasific_abo_fecha = $data->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha;
            $form->reclasific_abo_resp = $data->solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp;
            $form->ac_irregulares_tcd = $data->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd;
            $form->ac_irregulares_ca = $data->solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca;
            $form->unidades_compatibles = $data->solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles;
            $form->unidades_compatibles_hora = $data->solicitud_transfusion_productos_sanguineos_uc_hora;
            $form->unidades_compatibles_resp = $data->solicitud_transfusion_productos_sanguineos_uc_resp;
            $form->save();

            $id_formulario_solicitud_transfusion = $form->id_formulario_solicitud_transfusion;

            //instalaciones

            foreach ($data->solicitud_transfusion_productos_sanguineos_instalaciones as $key => $instalacion_data){
                $instalacion = new FormularioSolicitudTransfusionInstalacion();

                $id_formulario_solicitud_transfusion_instalacion = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion;

                //check si instalacion pertenece al formulario
                if( isset($id_formulario_solicitud_transfusion_instalacion)){ 

                    $instalacion = $this->getInstalacionDeSolicitudTransfucionProductoSanguineos($id_formulario_solicitud_transfusion_instalacion,$id_formulario_solicitud_transfusion);

                    if($instalacion === null){
                        throw new Exception('Campo solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion no valido.');
                    }

                }

                $instalacion->caso = $form->caso;
                $instalacion->id_paciente = $form->id_paciente;
                $instalacion->id_formulario_solicitud_transfusion =$form->id_formulario_solicitud_transfusion;
                $instalacion->fecha_instalacion = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora;
                $instalacion->n_maltraz = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_n_matraz;
                $instalacion->grupo_abo = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo;
                $instalacion->psl = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_psl;
                $instalacion->cantidad = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_cantidad;
                $instalacion->temp = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t;
                $instalacion->pulso = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso;
                $instalacion->p_arterial = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_p_arterial;
                $instalacion->responsable = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable;
                $instalacion->temp_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t_10;
                $instalacion->pulso_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso_10;
                $instalacion->p_arterial_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_10;
                $instalacion->responsable_10 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_10;
                $instalacion->temp_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t_30;
                $instalacion->pulso_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso_30;
                $instalacion->p_arterial_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_30;
                $instalacion->hora_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora;
                $instalacion->responsable_30 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_30;
                $instalacion->temp_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_t_60;
                $instalacion->pulso_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pulso_60;
                $instalacion->p_arterial_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_60;
                $instalacion->hora_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora;
                $instalacion->responsable_60 = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_60;
                $instalacion->reaccion_adversa_transfusional = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional;
                $instalacion->folio_ficha_rat = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat;
                $instalacion->tratamiento = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_tratamiento;
                $instalacion->medico_responsable = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable;
                $instalacion->responsable_toma_muestra = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra;
                $instalacion->hora = $instalacion_data->solicitud_transfusion_productos_sanguineos_instalacion_hora;
                $instalacion->save();
            }


        }

        $data->form = $form;
        return $data;

    }

    function getInstalacionDeSolicitudTransfucionProductoSanguineos($id_formulario_solicitud_transfusion_instalacion,$id_formulario_solicitud_transfusion){
        $instalacion = FormularioSolicitudTransfusionInstalacion::
            where("id_formulario_solicitud_transfusion_instalacion", $id_formulario_solicitud_transfusion_instalacion)->
            where("id_formulario_solicitud_transfusion", $id_formulario_solicitud_transfusion)->
            first();
            return $instalacion;

    }


    function getSolicitudTransfusionProductosSanguineosById($id_formulario){
        try {
            $formulario = FormularioSolicitudTransfusion::
            where("id_formulario_solicitud_transfusion", $id_formulario)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }    
    }

    function getSolicitudTransfusionProductosSanguineosByCasoId($caso_id){
        try {
            $formulario = FormularioSolicitudTransfusion::
            where("caso", $caso_id)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }  
    }

    function getSolicitudTransfusionProductosSanguineosData($caso_id){
        
        $caso = parent::getCasoById($caso_id);

        if(!isset($caso)){ 
            throw new Exception('Campo caso_id no valido.');  
        } else {
            $doc = $this->getSolicitudTransfusionProductosSanguineosByCasoId($caso_id);

            if(isset($doc)){
                //formatear fechas de instalaciones dd/mm/YYYY
                foreach($doc->instalaciones as $instalacion){

                    if(isset($instalacion->fecha_instalacion)){
                        $instalacion->fecha_instalacion = Carbon::parse($instalacion->fecha_instalacion)->format("d/m/Y H:i:s");

                    }

                    if (isset($instalacion->fecha)){
                        $instalacion->fecha = Carbon::parse($instalacion->fecha)->format("d/m/Y H:i:s");

                    }


                }
            }


            $data = (object)[
                "caso_id" => $caso_id,
                "form_id" => (isset($doc)) ? $doc->id_formulario_solicitud_transfusion : null,
                "paciente_edad" => (string) parent::getAge($caso[0]->fecha_nacimiento),
                "run_dv" => $caso[0]->run."-".($caso[0]->dv == 10 ? "K" : $caso[0]->dv),
                "run" => $caso[0]->run,
                "dv" => $caso[0]->dv == 10 ? "K" : $caso[0]->dv,
                "paciente_nombre" => $caso[0]->paciente_nombre,
                "paciente_apellido_paterno" => $caso[0]->paciente_apellido_paterno,
                "paciente_apellido_materno" => $caso[0]->paciente_apellido_materno,
                "ficha_clinica" => $caso[0]->ficha_clinica,
                "unidad" => $caso[0]->unidad,
                "cama" => $caso[0]->cama,
                "sala" => $caso[0]->sala,
                "diagnostico" => (isset($doc)) ? $doc->diagnostico : null,
                "transf_previas" => (isset($doc)) ? $doc->transf_previas : null,
                "reacciones_transfusiones" => (isset($doc)) ? $doc->reacciones_transfusiones : null,
                "n_embarazos" => (isset($doc)) ? $doc->n_embarazos : null,
                "ttpa" => (isset($doc)) ? $doc->ttpa : null,
                "tp" => (isset($doc)) ? $doc->tp : null,
                "plaq" => (isset($doc)) ? $doc->plaq : null,
                "hb" => (isset($doc)) ? $doc->hb : null,
                "hto" => (isset($doc)) ? $doc->hto : null,
                "g_rojos_cantidad" => (isset($doc)) ? $doc->g_rojos_cantidad : null,
                "g_rojos_horario" => 
                (isset($doc)) ? ((isset($doc->g_rojos_horario)) ? Carbon::parse($doc->g_rojos_horario)->format("d/m/Y H:i:s") : null) : null,
                "g_rojos_observaciones" => (isset($doc)) ? $doc->g_rojos_observaciones : null,
                "p_fresco_cantidad" => (isset($doc)) ? $doc->p_fresco_cantidad : null,
                "p_fresco_horario" => 
                (isset($doc)) ? ((isset($doc->p_fresco_horario)) ? Carbon::parse($doc->p_fresco_horario)->format("d/m/Y H:i:s") : null) : null,
                "p_fresco_observaciones" => (isset($doc)) ? $doc->p_fresco_observaciones : null,
                "plaquetas_cantidad" => (isset($doc)) ? $doc->plaquetas_cantidad : null,
                "plaquetas_horario" => 
                (isset($doc)) ? ((isset($doc->plaquetas_horario)) ? Carbon::parse($doc->plaquetas_horario)->format("d/m/Y H:i:s") : null) : null,
                "plaquetas_observaciones" => (isset($doc)) ? $doc->plaquetas_observaciones : null,
                "crioprec_cantidad" => (isset($doc)) ? $doc->crioprec_cantidad : null,
                "crioprec_horario" => 
                (isset($doc)) ? ((isset($doc->crioprec_horario)) ? Carbon::parse($doc->crioprec_horario)->format("d/m/Y H:i:s") : null) : null,
                "crioprec_observaciones" => (isset($doc)) ? $doc->crioprec_observaciones : null,
                "exsanguineot_cantidad" => (isset($doc)) ? $doc->exsanguineot_cantidad : null,
                "exsanguineot_horario" =>
                (isset($doc)) ? ((isset($doc->exsanguineot_horario)) ? Carbon::parse($doc->exsanguineot_horario)->format("d/m/Y H:i:s") : null) : null,
                "exsanguineot_observaciones" => (isset($doc)) ? $doc->exsanguineot_observaciones : null,
                "leucorreducidos" => (isset($doc)) ? $doc->leucorreducidos : null,
                "irradiado" => (isset($doc)) ? $doc->leucorreducidos : null,
                "responsable_recepcion" => (isset($doc)) ? $doc->responsable_recepcion : null,
                "fecha_recepcion" => 
                (isset($doc)) ? ((isset($doc->fecha_recepcion)) ? Carbon::parse($doc->fecha_recepcion)->format("d/m/Y H:i:s") : null) : null,
                "gravedad" => (isset($doc)) ? $doc->gravedad : null,
                "reserva_pabellon" => 
                (isset($doc)) ? (($doc->reserva_pabellon === true) ? "si" : null) : null,
                "fecha_reserva_pabellon" => 
                (isset($doc)) ? ((isset($doc->fecha_reserva_pabellon)) ? Carbon::parse($doc->fecha_reserva_pabellon)->format("d/m/Y H:i:s") : null) : null,
                "medico_responsable" => (isset($doc)) ? $doc->medico_responsable : null,
                "fecha_solicitud" => 
                (isset($doc)) ? ((isset($doc->fecha_solicitud)) ? Carbon::parse($doc->fecha_solicitud)->format("d/m/Y H:i:s") : null) : null,
                "observaciones" => (isset($doc)) ? $doc->observaciones : null,
                "clasific_abo_fecha" => 
                (isset($doc)) ? ((isset($doc->clasific_abo_fecha)) ? Carbon::parse($doc->clasific_abo_fecha)->format("d/m/Y") : null) : null,
                "clasific_abo_resp" => (isset($doc)) ? $doc->clasific_abo_resp : null,
                "reclasific_abo_fecha" => 
                (isset($doc)) ? ((isset($doc->reclasific_abo_fecha)) ? Carbon::parse($doc->reclasific_abo_fecha)->format("d/m/Y") : null) : null,
                "reclasific_abo_resp" => (isset($doc)) ? $doc->reclasific_abo_resp : null,
                "ac_irregulares_tcd" => (isset($doc)) ? $doc->ac_irregulares_tcd : null,
                "ac_irregulares_ca" => (isset($doc)) ? $doc->ac_irregulares_ca : null,
                "unidades_compatibles" => (isset($doc)) ? $doc->unidades_compatibles : null,
                "unidades_compatibles_hora" => 
                (isset($doc)) ? ((isset($doc->unidades_compatibles_hora)) ? Carbon::parse($doc->unidades_compatibles_hora)->format("H:i:s") : null) : null,
                "unidades_compatibles_resp" => (isset($doc)) ? $doc->unidades_compatibles_resp : null,
                "instalaciones" => (isset($doc)) ? $doc->instalaciones : array(),

            ];
            
            return $data;

        }

    }
    

    /*
        Funcion que ordena inputs multiples y
        entrega un arreglo de objetos donde 
        cada uno corresponde a una fila de los inputs.

        Valida que lleguen inputs multiples de manera
        consistente.

    */

    function ordenamientoInputsMultiples($arrayOfInputsMultiples){

        $rows[] = array();
        $rowsO = array();

        if(isset($arrayOfInputsMultiples) && is_array($arrayOfInputsMultiples) ){

            $length_array = [];

            foreach ($arrayOfInputsMultiples as $key => $inputMultiple) {

                if(isset($inputMultiple) && is_array($inputMultiple) ){
                    $l = count($inputMultiple);
                    array_push($length_array, $l);

                    //check de consistencia
                    if (! parent::checkArrayElementsAreSame($length_array)) {
                        throw new Exception('Campo input_multiple no valido.');
                    }

                    for ($i = 0; $i < $l; $i++) {
                        $elem = [];
                        $elem = array($key => $inputMultiple[$i]);
                        if(!isset($rows[$i])){ $rows[$i] = array();}
                        $rows[$i] = $rows[$i]+$elem;
                    }


                } else {
                    throw new Exception('Campo input_multiple no valido.');
                }

            }

            if($length_array[0] === 0){ return $rowsO; }

            foreach ($rows as $key => $r) {
                array_push($rowsO, (object)$r);

            }

        }
        else {
            throw new Exception('Campo input_multiple no valido.');
        }

        return $rowsO;

    }
    function pdf($caso_id){
    	
    	$datos_pdf = DB::select("SELECT
		EXTRACT(YEAR FROM AGE(c.fecha_ingreso ,fecha_nacimiento)) AS edad,
		c.ficha_clinica AS ficha_clinica,
		p.rut AS run,
		CASE WHEN p.dv = 10 THEN 
			'K'
		ELSE
			p.dv::varchar
		END AS dv,
		c.id AS caso_id,
		p.id AS paciente_id,
		p.nombre,
		p.apellido_paterno,
		p.apellido_materno,
		e.nombre AS nombre_establecimiento,
		e.nombre AS nombre_hospital,
		e.logo AS logo_hospital,
		r.nombre_region,
		fst.*,
		ca.id_cama AS cama,
		s.nombre AS sala,
		uee.alias AS servicio,
		TO_CHAR(fst.fecha,'DD-MM-YYYY')AS fecha,
		TO_CHAR(fst.g_rojos_horario,'DD-MM-YYYY HH24:MI:SS')AS g_rojos_horario,
		TO_CHAR(fst.p_fresco_horario,'DD-MM-YYYY HH24:MI:SS')AS p_fresco_horario,
		TO_CHAR(fst.plaquetas_horario,'DD-MM-YYYY HH24:MI:SS')AS plaquetas_horario,
		TO_CHAR(fst.crioprec_horario,'DD-MM-YYYY HH24:MI:SS')AS crioprec_horario,
		TO_CHAR(fst.exsanguineot_horario,'DD-MM-YYYY HH24:MI:SS')AS exsanguineot_horario,
		TO_CHAR(fst.fecha_recepcion,'DD-MM-YYYY HH24:MI:SS')AS fecha_recepcion,
		TO_CHAR(fst.fecha_reserva_pabellon,'DD-MM-YYYY HH24:MI:SS')AS fecha_reserva_pabellon,
		TO_CHAR(fst.fecha_solicitud,'DD-MM-YYYY HH24:MI:SS')AS fecha_solicitud,
		TO_CHAR(fst.clasific_abo_fecha,'DD-MM-YYYY')AS clasific_abo_fecha,
		TO_CHAR(fst.reclasific_abo_fecha,'DD-MM-YYYY')AS reclasific_abo_fecha
		
		FROM formulario_solicitud_transfusion fst
		INNER JOIN casos c ON c.id = fst.caso
		INNER JOIN pacientes p ON p.id = c.paciente
		INNER JOIN establecimientos e ON e.id = c.establecimiento
		INNER JOIN t_historial_ocupaciones as t on c.id=t.caso
        INNER JOIN camas as ca on ca.id = t.cama
        INNER JOIN salas as s on ca.sala = s.id
        INNER JOIN unidades_en_establecimientos AS uee on s.establecimiento = uee.id
		INNER JOIN region r ON r.id_region = e.id_region
		WHERE c.id = ?
		AND t.motivo IS NULL",[$caso_id]);
    	$datos = [
    		"formulario" => null,
    		"instalaciones" => []
    	];
    	if($datos_pdf)
    	{
    		$datos["formulario"] = $datos_pdf[0];
    	}
    	
    	$datos_instalaciones = DB::select("SELECT
		*,
		TO_CHAR(fsti.fecha_instalacion,'DD-MM-YYYY HH24:MI:SS')AS fecha_instalacion
		FROM formulario_solicitud_transfusion_instalacion fsti
		WHERE caso = ?
		",[$caso_id]);
    	
    	$datos["instalaciones"] = $datos_instalaciones;
    	
    	return $datos;
    	
    }


}