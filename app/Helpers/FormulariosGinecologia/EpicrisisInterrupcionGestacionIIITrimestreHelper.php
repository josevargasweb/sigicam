<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioEpicrisisInterrupcionGestacion;



class EpicrisisInterrupcionGestacionIIITrimestreHelper extends CasoHelper{ 

    function getData($req){

        $now = Carbon::now()->format('d/m/Y');

        /* CAPTURAR LO QUE LLEGA */

        //epicrisis_interrupcion_gestacion_iii_trimestre_p
        $epicrisis_interrupcion_gestacion_iii_trimestre_p = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_p) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_p) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_p)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_v
        $epicrisis_interrupcion_gestacion_iii_trimestre_v = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_v) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_v) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_v)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional
        $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional)) : null;
		
		//epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias
		$epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1
        $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2
        $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2)) : null;  
        
        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3
        $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4
        $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut
        $epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_cons
        $epicrisis_interrupcion_gestacion_iii_trimestre_cons = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_cons) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_cons) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_cons)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_presentacion
        $epicrisis_interrupcion_gestacion_iii_trimestre_presentacion = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_presentacion) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_presentacion) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_presentacion)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal
        $epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion
        $epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion
        $epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist
        $epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento
        $epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_dilat
        $epicrisis_interrupcion_gestacion_iii_trimestre_dilat = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_dilat) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_dilat) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_dilat)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_bishop
        $epicrisis_interrupcion_gestacion_iii_trimestre_bishop = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_bishop) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_bishop) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_bishop)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_puntos
        $epicrisis_interrupcion_gestacion_iii_trimestre_puntos = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_puntos) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_puntos) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_puntos)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria
        $epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_cd
        $epicrisis_interrupcion_gestacion_iii_trimestre_cd = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_cd) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_cd) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_cd)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_cv
        $epicrisis_interrupcion_gestacion_iii_trimestre_cv = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_cv) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_cv) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_cv)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_ec
        $epicrisis_interrupcion_gestacion_iii_trimestre_ec = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_ec) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_ec) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_ec)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_rn
        $epicrisis_interrupcion_gestacion_iii_trimestre_rn = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_rn) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_rn) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_rn)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_tipo
        $epicrisis_interrupcion_gestacion_iii_trimestre_tipo = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_tipo) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_tipo) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_tipo)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal
        $epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_normal
        $epicrisis_interrupcion_gestacion_iii_trimestre_normal = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_normal) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_normal) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_normal)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_alterado
        $epicrisis_interrupcion_gestacion_iii_trimestre_alterado = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_alterado) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_alterado) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_alterado)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal
        $epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1
        $epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2
        $epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3
        $epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3 = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada
        $epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada)) : null;      
        
        //epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion
        $epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion)) : null;

        //epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion
        $epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion = (isset($req->epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion) && trim($req->epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion) !== "") ?
        trim(strip_tags($req->epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion)) : null;

        //caso_id
        $caso_id = (isset($req->caso_id) && trim($req->caso_id) !== "") ?
        trim(strip_tags($req->caso_id)) : null;

        //form_id
        $form_id = (isset($req->form_id) && trim($req->form_id) !== "") ?
        trim(strip_tags($req->form_id)) : null;   
        
        /* VALIDAR DOMINIO DE LO QUE LLEGA */

        //epicrisis_interrupcion_gestacion_iii_trimestre_p
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_p) > 60){ 
            throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_p no valido.');
        }      
        
        //epicrisis_interrupcion_gestacion_iii_trimestre_v
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_v) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_v no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional
        if (mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional) > 0 &&   
            (
            filter_var( $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional, FILTER_VALIDATE_INT ) === false ||  
            $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional < 27 || $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional > 42
            )
        ) {
            throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional no valido.');       
        }
		
		if (mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias) > 0 &&   
            (
            filter_var( $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias, FILTER_VALIDATE_INT ) === false ||  
            $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias < 0 || $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias > 6
            )
        ) {
            throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias no valido.');       
        }

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1 no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2 no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3 no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4 no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_cons
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_cons) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_cons no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_presentacion
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_presentacion) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_presentacion no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_dilat
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_dilat) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_dilat no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_bishop
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_bishop) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_bishop no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_puntos
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_puntos) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_puntos no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_cd
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_cd) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_cd no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_cv
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_cv) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_cv no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_ec
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_ec) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_ec no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_rn
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_rn) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_rn no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_tipo
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_tipo) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_tipo no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_normal
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_normal) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_normal no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_alterado
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_alterado) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_alterado no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal
        if (mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal) > 0 &&   
        (
        filter_var( $epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal, FILTER_VALIDATE_INT ) === false ||  
        $epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal < 0 || $epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal > 7000
        )
    ) {
        throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal no valido.');       
    }

        //epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1 no valido.'); } 
        
        //epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2 no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3
        if(mb_strlen($epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3) > 60){ throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3 no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada
        $epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada_valid = [
            "induccion",
            "induccion_monitorizada",
            "cesarea"
        ];

        if (!in_array($epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada, $epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada_valid)) { throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada no valido.'); }

        //epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion
        $epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion_is_valid = 
        parent::validateDate($epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion , 'd/m/Y') && 
        parent::dateComparison($epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion, $now, 'd/m/Y', 'a<=b');

        if(!$epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion_is_valid){
            throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion no valido.');
        }

        $epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion =
        Carbon::createFromFormat('d/m/Y', $epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion)->format('Y-m-d');

        //epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion
        $epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion_is_valid = 
        parent::validateDate($epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion , 'H:i');

        if(!$epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion_is_valid){
            throw new Exception('Campo epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion no valido.');
        }

        $epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion =
        Carbon::createFromFormat('H:i', $epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion)->format('H:i');

        //caso_id
        $caso = parent::getCasoById($caso_id);
        if(!isset($caso)){ throw new Exception('Campo caso_id no valido.');  }


        $data = (object)[
            "epicrisis_interrupcion_gestacion_iii_trimestre_p" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_p,
            "epicrisis_interrupcion_gestacion_iii_trimestre_v" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_v,
            "epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional" => $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional,
			"epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias" => $epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias,
			"epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones" => $req->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones,
            "epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1" => $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1,
            "epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2" => $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2,
            "epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3" => $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3,
            "epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4" => $epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4,
            "epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut,
            "epicrisis_interrupcion_gestacion_iii_trimestre_cons" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_cons,
            "epicrisis_interrupcion_gestacion_iii_trimestre_presentacion" => $epicrisis_interrupcion_gestacion_iii_trimestre_presentacion,
            "epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal" => $epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal,
            "epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion" => $epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion,
            "epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion" => $epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion,
            "epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist" => $epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist,
            "epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento" => $epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento,
            "epicrisis_interrupcion_gestacion_iii_trimestre_dilat" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_dilat,
            "epicrisis_interrupcion_gestacion_iii_trimestre_bishop" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_bishop,
            "epicrisis_interrupcion_gestacion_iii_trimestre_puntos" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_puntos,
            "epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria" => $epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria,
            "epicrisis_interrupcion_gestacion_iii_trimestre_cd" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_cd,
            "epicrisis_interrupcion_gestacion_iii_trimestre_cv" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_cv,
            "epicrisis_interrupcion_gestacion_iii_trimestre_ec" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_ec,
            "epicrisis_interrupcion_gestacion_iii_trimestre_rn" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_rn,
            "epicrisis_interrupcion_gestacion_iii_trimestre_tipo" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_tipo,
            "epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal" => $epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal,
            "epicrisis_interrupcion_gestacion_iii_trimestre_normal" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_normal,
            "epicrisis_interrupcion_gestacion_iii_trimestre_alterado" => 
            $epicrisis_interrupcion_gestacion_iii_trimestre_alterado,
            "epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal" => $epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal,
            "epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1" => $epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1,
            "epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2" => $epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2,
            "epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3" => $epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3,
            "epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" => $epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada,
            "epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion" => $epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion,
            "epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion" => $epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion,
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
                $form_bd = $this->getEpicrisisInterrupcionGestacionIIITrimestreByCasoId($data->caso_id);
                if(isset($form_bd)){ throw new Exception('Campo form_bd no valido.');  }
    
                $form = new FormularioEpicrisisInterrupcionGestacion();
                $form->caso = $data->caso_id;
                $form->id_paciente = $data->paciente_id ;
                $form->usuario_responsable = $data->usuario_responsable;
                $form->fecha = Carbon::now()->format("Y-m-d H:i:s");

                //
                $form->p = $data->epicrisis_interrupcion_gestacion_iii_trimestre_p;
                $form->v = $data->epicrisis_interrupcion_gestacion_iii_trimestre_v;
                $form->edad_gestacional = $data->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional;
				$form->edad_gestacional_dias = $data->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias;
				$form->edad_gestacional_observacion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones;
                $form->diagnostico_patologia_1 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1;
                $form->diagnostico_patologia_2 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2;
                $form->diagnostico_patologia_3 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3;
                $form->diagnostico_patologia_4 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4;
                $form->alt_ut = $data->epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut;
                $form->cons = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cons;
                $form->presentacion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_presentacion;
                $form->tacto_vaginal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal;
                $form->plano_presentacion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion;
                $form->cuello_posicion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion;
                $form->cuello_consist = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist;
                $form->cuello_borramiento = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento;
                $form->dilat = $data->epicrisis_interrupcion_gestacion_iii_trimestre_dilat;
                $form->bishop = $data->epicrisis_interrupcion_gestacion_iii_trimestre_bishop;
                $form->puntos = $data->epicrisis_interrupcion_gestacion_iii_trimestre_puntos;
                $form->pelvimetria = $data->epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria;
                $form->cd = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cd;
                $form->cv = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cv;
                $form->ec = $data->epicrisis_interrupcion_gestacion_iii_trimestre_ec;
                $form->rn = $data->epicrisis_interrupcion_gestacion_iii_trimestre_rn;
                $form->tipo = $data->epicrisis_interrupcion_gestacion_iii_trimestre_tipo;
                $form->proporcion_pelvis_fetal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal;
                $form->normal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_normal;
                $form->alterado = $data->epicrisis_interrupcion_gestacion_iii_trimestre_alterado;
                $form->peso_estimado_fetal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal;
                $form->indicacion_1 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1;
                $form->indicacion_2 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2;
                $form->indicacion_3 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3;
                $form->via_solicitada = $data->epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada;
                $form->fecha_intervencion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion;
                $form->hora_intervencion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion;

                $form->save();
    
            }
    
            /* ACTUALIZAR */
            else {
                $form = $this->getEpicrisisInterrupcionGestacionIIITrimestreById($data->form_id);
                if(!isset($form)){ throw new Exception('Campo form_id no valido.');  }
                $form->p = $data->epicrisis_interrupcion_gestacion_iii_trimestre_p;
                $form->v = $data->epicrisis_interrupcion_gestacion_iii_trimestre_v;
                $form->edad_gestacional = $data->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional;
				$form->edad_gestacional_dias = $data->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias;
				$form->edad_gestacional_observacion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones;
                $form->diagnostico_patologia_1 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1;
                $form->diagnostico_patologia_2 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2;
                $form->diagnostico_patologia_3 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3;
                $form->diagnostico_patologia_4 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4;
                $form->alt_ut = $data->epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut;
                $form->cons = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cons;
                $form->presentacion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_presentacion;
                $form->tacto_vaginal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal;
                $form->plano_presentacion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion;
                $form->cuello_posicion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion;
                $form->cuello_consist = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist;
                $form->cuello_borramiento = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento;
                $form->dilat = $data->epicrisis_interrupcion_gestacion_iii_trimestre_dilat;
                $form->bishop = $data->epicrisis_interrupcion_gestacion_iii_trimestre_bishop;
                $form->puntos = $data->epicrisis_interrupcion_gestacion_iii_trimestre_puntos;
                $form->pelvimetria = $data->epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria;
                $form->cd = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cd;
                $form->cv = $data->epicrisis_interrupcion_gestacion_iii_trimestre_cv;
                $form->ec = $data->epicrisis_interrupcion_gestacion_iii_trimestre_ec;
                $form->rn = $data->epicrisis_interrupcion_gestacion_iii_trimestre_rn;
                $form->tipo = $data->epicrisis_interrupcion_gestacion_iii_trimestre_tipo;
                $form->proporcion_pelvis_fetal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal;
                $form->normal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_normal;
                $form->alterado = $data->epicrisis_interrupcion_gestacion_iii_trimestre_alterado;
                $form->peso_estimado_fetal = $data->epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal;
                $form->indicacion_1 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1;
                $form->indicacion_2 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2;
                $form->indicacion_3 = $data->epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3;
                $form->via_solicitada = $data->epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada;
                $form->fecha_intervencion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion;
                $form->hora_intervencion = $data->epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion;
                $form->save();
            }
    
            $data->form = $form;
            return $data;

    }


    function getEpicrisisInterrupcionGestacionIIITrimestreById($id_formulario){
        try {

            $formulario = FormularioEpicrisisInterrupcionGestacion::
            where("id_formulario_epicrisis_interrupcion_gestacion", $id_formulario)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }

    }

    function getEpicrisisInterrupcionGestacionIIITrimestreByCasoId($caso_id){
        try {

            $formulario = FormularioEpicrisisInterrupcionGestacion::
            where("caso", $caso_id)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }   
    }


    function getEpicrisisInterrupcionGestacionIIITrimestreData($caso_id){
        
        $caso = parent::getCasoById($caso_id);

        if(!isset($caso)){ 
            throw new Exception('Campo caso_id no valido.');  
        } else {
            $doc = $this->getEpicrisisInterrupcionGestacionIIITrimestreByCasoId($caso_id);
            $data = (object)[
                "caso_id" => $caso_id,
                "form_id" => (isset($doc)) ? $doc->id_formulario_epicrisis_interrupcion_gestacion : null,
                "paciente_edad" => (string) parent::getAge($caso[0]->fecha_nacimiento), 
                "p" => (isset($doc)) ? $doc->p : null,
                "v" => (isset($doc)) ? $doc->v : null,
                "edad_gestacional" => (isset($doc)) ? $doc->edad_gestacional : null,
				"edad_gestacional_dias" => (isset($doc)) ? $doc->edad_gestacional_dias : null,
				"edad_gestacional_observacion" => (isset($doc)) ? $doc->edad_gestacional_observacion : null,
                "diagnostico_patologia_1" => (isset($doc)) ? $doc->diagnostico_patologia_1 : null,
                "diagnostico_patologia_2" => (isset($doc)) ? $doc->diagnostico_patologia_2 : null,
                "diagnostico_patologia_3" => (isset($doc)) ? $doc->diagnostico_patologia_3 : null,
                "diagnostico_patologia_4" => (isset($doc)) ? $doc->diagnostico_patologia_4 : null,
                "alt_ut" => (isset($doc)) ? $doc->alt_ut : null,
                "cons" => (isset($doc)) ? $doc->cons : null,
                "presentacion" => (isset($doc)) ? $doc->presentacion : null,
                "tacto_vaginal" => (isset($doc)) ? $doc->tacto_vaginal : null,
                "plano_presentacion" => (isset($doc)) ? $doc->plano_presentacion : null,
                "cuello_posicion" => (isset($doc)) ? $doc->cuello_posicion : null,
                "cuello_consist" => (isset($doc)) ? $doc->cuello_consist : null,
                "cuello_borramiento" => (isset($doc)) ? $doc->cuello_borramiento : null,
                "dilat" => (isset($doc)) ? $doc->dilat : null,
                "bishop" => (isset($doc)) ? $doc->bishop : null,
                "puntos" => (isset($doc)) ? $doc->puntos : null,
                "pelvimetria" => (isset($doc)) ? $doc->pelvimetria : null,
                "cd" => (isset($doc)) ? $doc->cd : null,
                "cv" => (isset($doc)) ? $doc->cv : null,
                "ec" => (isset($doc)) ? $doc->ec : null,
                "rn" => (isset($doc)) ? $doc->rn : null,
                "tipo" => (isset($doc)) ? $doc->tipo : null,
                "proporcion_pelvis_fetal" => (isset($doc)) ? $doc->proporcion_pelvis_fetal : null,
                "normal" => (isset($doc)) ? $doc->normal : null,
                "alterado" => (isset($doc)) ? $doc->alterado : null,
                "peso_estimado_fetal" => (isset($doc)) ? $doc->peso_estimado_fetal : null,
                "indicacion_1" => (isset($doc)) ? $doc->indicacion_1 : null,
                "indicacion_2" => (isset($doc)) ? $doc->indicacion_2 : null,
                "indicacion_3" => (isset($doc)) ? $doc->indicacion_3 : null,
                "via_solicitada" => (isset($doc)) ? $doc->via_solicitada : null,
                "fecha_intervencion" => (isset($doc)) ? Carbon::parse($doc->fecha_intervencion)->format("d/m/Y") : null,
                "hora_intervencion" => (isset($doc)) ? Carbon::parse($doc->hora_intervencion)->format("H:i")  : null,
            ];

            
            return $data;

        }

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
		
		ca.id_cama AS cama,
		s.nombre AS sala,
		uee.alias AS servicio,

		r.nombre_region,
		feig.*,
		TO_CHAR(feig.fecha,'DD-MM-YYYY')AS fecha,
		TO_CHAR(feig.fecha_intervencion,'DD-MM-YYYY')AS fecha_intervencion,
		TO_CHAR(feig.hora_intervencion,'HH24:MI')AS hora_intervencion
		FROM formulario_epicrisis_interrupcion_gestacion feig
		INNER JOIN casos c ON c.id = feig.caso
		INNER JOIN pacientes p ON p.id = c.paciente
		INNER JOIN establecimientos e ON e.id = c.establecimiento
		INNER JOIN region r ON r.id_region = e.id_region
		
		INNER JOIN t_historial_ocupaciones as t on c.id=t.caso
        INNER JOIN camas as ca on ca.id = t.cama
        INNER JOIN salas as s on ca.sala = s.id
        INNER JOIN unidades_en_establecimientos AS uee on s.establecimiento = uee.id

		WHERE c.id = ?
		AND t.motivo IS NULL",[$caso_id]);
    	if($datos_pdf)
    	{
    		return $datos_pdf[0];
    	}
    	return null;
    	
    }
    

}