<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioDocumentoAlta;


class EntregaDocumentosAltaHelper extends CasoHelper{ 

    function getData($req){

        /* CAPTURAR LO QUE LLEGA */

        $entrega_documentos_alta_epicrisis_medica = (isset($req->entrega_documentos_alta_epicrisis_medica) && trim($req->entrega_documentos_alta_epicrisis_medica) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_epicrisis_medica)) : null;
        
        $entrega_documentos_alta_carnet_alta = (isset($req->entrega_documentos_alta_carnet_alta) && trim($req->entrega_documentos_alta_carnet_alta) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_carnet_alta)) : null;

        $entrega_documentos_alta_recetas_farmacos = (isset($req->entrega_documentos_alta_recetas_farmacos) && trim($req->entrega_documentos_alta_recetas_farmacos) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_recetas_farmacos)) : null;

        $entrega_documentos_alta_citaciones_control = (isset($req->entrega_documentos_alta_citaciones_control) && trim($req->entrega_documentos_alta_citaciones_control) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_citaciones_control)) : null;

        $entrega_documentos_alta_carne_identidad = (isset($req->entrega_documentos_alta_carne_identidad) && trim($req->entrega_documentos_alta_carne_identidad) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_carne_identidad)) : null;

        $entrega_documentos_alta_comprobante_parto = (isset($req->entrega_documentos_alta_comprobante_parto) && trim($req->entrega_documentos_alta_comprobante_parto) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_comprobante_parto)) : null;

        $entrega_documentos_alta_carne_control_parental = (isset($req->entrega_documentos_alta_carne_control_parental) && trim($req->entrega_documentos_alta_carne_control_parental) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_carne_control_parental)) : null;

        $entrega_documentos_alta_egreso_hospitalario_acompanado = (isset($req->entrega_documentos_alta_egreso_hospitalario_acompanado) && trim($req->entrega_documentos_alta_egreso_hospitalario_acompanado) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_egreso_hospitalario_acompanado)) : null;

        $entrega_documentos_alta_acompanante = (isset($req->entrega_documentos_alta_acompanante) && trim($req->entrega_documentos_alta_acompanante) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_acompanante)) : null;

        $entrega_documentos_alta_observaciones = (isset($req->entrega_documentos_alta_observaciones) && trim($req->entrega_documentos_alta_observaciones) !== "") ?
        trim(strip_tags($req->entrega_documentos_alta_observaciones)) : null;

        $caso_id = (isset($req->caso_id) && trim($req->caso_id) !== "") ?
        trim(strip_tags($req->caso_id)) : null;

        $form_id = (isset($req->form_id) && trim($req->form_id) !== "") ?
        trim(strip_tags($req->form_id)) : null;

        /* VALIDAR DOMINIO DE LO QUE LLEGA */

        $documentacion_entregada_valid = ["si","no","n/c"];
        $egreso_hospitalario_acompanado_valid = ["si","no"];

        //entrega_documentos_alta_epicrisis_medica
        if (!in_array($entrega_documentos_alta_epicrisis_medica, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_epicrisis_medica no valido.'); }

        //entrega_documentos_alta_carnet_alta
        if (!in_array($entrega_documentos_alta_carnet_alta, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_carnet_alta no valido.'); }

        //entrega_documentos_alta_recetas_farmacos
        if (!in_array($entrega_documentos_alta_recetas_farmacos, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_recetas_farmacos no valido.'); }

        //entrega_documentos_alta_citaciones_control
        if (!in_array($entrega_documentos_alta_citaciones_control, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_citaciones_control no valido.'); }

        //entrega_documentos_alta_carne_identidad
        if (!in_array($entrega_documentos_alta_carne_identidad, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_carne_identidad no valido.'); }

        //entrega_documentos_alta_comprobante_parto
        if (!in_array($entrega_documentos_alta_comprobante_parto, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_comprobante_parto no valido.'); }

        //entrega_documentos_alta_carne_control_parental
        if (!in_array($entrega_documentos_alta_carne_control_parental, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_carne_control_parental no valido.'); }

        //entrega_documentos_alta_egreso_hospitalario_acompanado
        if (!in_array($entrega_documentos_alta_egreso_hospitalario_acompanado, $documentacion_entregada_valid)) { throw new Exception('Campo entrega_documentos_alta_egreso_hospitalario_acompanado no valido.'); }
        $entrega_documentos_alta_egreso_hospitalario_acompanado = ($entrega_documentos_alta_egreso_hospitalario_acompanado === "si") ? true : false;

        //entrega_documentos_alta_acompanante
        if($entrega_documentos_alta_egreso_hospitalario_acompanado === true){
            if(!isset($entrega_documentos_alta_acompanante) || mb_strlen($entrega_documentos_alta_acompanante) > 200){ 
                throw new Exception('Campo entrega_documentos_alta_acompanante no valido.'); }
            }
        else {
            $entrega_documentos_alta_acompanante = null;
        }

        //entrega_documentos_alta_observaciones
        if(mb_strlen($entrega_documentos_alta_observaciones) > 500){ throw new Exception('Campo entrega_documentos_alta_observaciones no valido.'); }

        //caso_id
        $caso = parent::getCasoById($caso_id);
        if(!isset($caso)){ throw new Exception('Campo caso_id no valido.');  }


        $data = (object)[
            "entrega_documentos_alta_epicrisis_medica" => $entrega_documentos_alta_epicrisis_medica,
            "entrega_documentos_alta_carnet_alta" => $entrega_documentos_alta_carnet_alta,
            "entrega_documentos_alta_recetas_farmacos" => $entrega_documentos_alta_recetas_farmacos,
            "entrega_documentos_alta_citaciones_control" => $entrega_documentos_alta_citaciones_control,
            "entrega_documentos_alta_carne_identidad" => $entrega_documentos_alta_carne_identidad,
            "entrega_documentos_alta_comprobante_parto" => $entrega_documentos_alta_comprobante_parto,
            "entrega_documentos_alta_carne_control_parental" => $entrega_documentos_alta_carne_control_parental,
            "entrega_documentos_alta_egreso_hospitalario_acompanado" => $entrega_documentos_alta_egreso_hospitalario_acompanado,
            "entrega_documentos_alta_acompanante" => $entrega_documentos_alta_acompanante,
            "entrega_documentos_alta_observaciones" => $entrega_documentos_alta_observaciones,
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
            $form_bd = $this->getDocumentoAltaByCasoId($data->caso_id);
            if(isset($form_bd)){ throw new Exception('Campo form_bd no valido.');  }

            $form = new FormularioDocumentoAlta();
            $form->caso = $data->caso_id;
            $form->id_paciente = $data->paciente_id ;
            $form->epicrisis_medica = $data->entrega_documentos_alta_epicrisis_medica;
            $form->carnet_alta = $data->entrega_documentos_alta_carnet_alta;
            $form->recetas_farmacos = $data->entrega_documentos_alta_recetas_farmacos;
            $form->citaciones_control = $data->entrega_documentos_alta_citaciones_control;
            $form->carne_identidad = $data->entrega_documentos_alta_carne_identidad;
            $form->comprobante_parto = $data->entrega_documentos_alta_comprobante_parto;
            $form->carne_control_parental = $data->entrega_documentos_alta_carne_control_parental;
            $form->egreso_hospitalario_acompanado = $data->entrega_documentos_alta_egreso_hospitalario_acompanado;
            $form->quien_acompana_paciente = $data->entrega_documentos_alta_acompanante;
            $form->observaciones = $data->entrega_documentos_alta_observaciones;
            $form->usuario_responsable = $data->usuario_responsable;
            $form->fecha = Carbon::now()->format("Y-m-d H:i:s");
            $form->save();

        }

        /* ACTUALIZAR */
        else {
            $form = $this->getDocumentoAltaById($data->form_id);
            if(!isset($form)){ throw new Exception('Campo form_id no valido.');  }
            $form->epicrisis_medica = $data->entrega_documentos_alta_epicrisis_medica;
            $form->carnet_alta = $data->entrega_documentos_alta_carnet_alta;
            $form->recetas_farmacos = $data->entrega_documentos_alta_recetas_farmacos;
            $form->citaciones_control = $data->entrega_documentos_alta_citaciones_control;
            $form->carne_identidad = $data->entrega_documentos_alta_carne_identidad;
            $form->comprobante_parto = $data->entrega_documentos_alta_comprobante_parto;
            $form->carne_control_parental = $data->entrega_documentos_alta_carne_control_parental;
            $form->egreso_hospitalario_acompanado = $data->entrega_documentos_alta_egreso_hospitalario_acompanado;
            $form->quien_acompana_paciente = $data->entrega_documentos_alta_acompanante;
            $form->observaciones = $data->entrega_documentos_alta_observaciones;
            $form->save();
        }

        $data->form = $form;
        return $data;

    }


    function getDocumentoAltaById($id_formulario){

        try {

            $formulario = FormularioDocumentoAlta::
            where("id_formulario_documentos_alta", $id_formulario)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }
    }

    function getDocumentoAltaByCasoId($caso_id){

        try {

            $formulario = FormularioDocumentoAlta::
            where("caso", $caso_id)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }
    }


    function getDocumentoAltaData($caso_id){
        
        $caso = parent::getCasoById($caso_id);
        
        if(!isset($caso)){ 
            throw new Exception('Campo caso_id no valido.');  
        } else {
            $doc = $this->getDocumentoAltaByCasoId($caso_id);

            $data = (object)[
                "caso_id" => $caso_id,
                "run_dv" => $caso[0]->run."-".($caso[0]->dv == 10 ? "K" : $caso[0]->dv),
                "run" => $caso[0]->run,
                "dv" => $caso[0]->dv == 10 ? "K" : $caso[0]->dv,
                "ficha_clinica" => $caso[0]->ficha_clinica,
                "form_id" => (isset($doc)) ? $doc->id_formulario_documentos_alta : null,
                "fecha_documento" => (isset($doc)) ? Carbon::parse($doc->fecha)->format("d/m/Y") : null,
                "epicrisis_medica" => (isset($doc)) ? $doc->epicrisis_medica : null,
                "carnet_alta" => (isset($doc)) ? $doc->carnet_alta : null,
                "recetas_farmacos" => (isset($doc)) ? $doc->recetas_farmacos : null,
                "citaciones_control" => (isset($doc)) ? $doc->citaciones_control : null,
                "carne_identidad" => (isset($doc)) ? $doc->carne_identidad : null,
                "comprobante_parto" => (isset($doc)) ? $doc->comprobante_parto : null,
                "carne_control_parental" => (isset($doc)) ? $doc->carne_control_parental : null,
                "egreso_hospitalario_acompanado" => (isset($doc)) ? (($doc->egreso_hospitalario_acompanado === true) ? "si" : "no") : "no",
                "quien_acompana_paciente" => (isset($doc)) ? $doc->quien_acompana_paciente : null,
                "observaciones" => (isset($doc)) ? $doc->observaciones : null,
            ];
            
            return $data;

        }

    }
    
    function getDocumentoAltaDataPDF($caso_id){
    	
    	$datos_pdf = DB::select("SELECT 
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
		fda.*,
		TO_CHAR(fda.fecha,'DD-MM-YYYY')AS fecha,
		est.nombre AS nombre_hospital,
		est.logo AS logo_hospital,
		
		ca.id_cama AS cama,
		s.nombre AS sala,
		uee.alias AS servicio

		FROM formulario_documentos_alta fda
		INNER JOIN casos c ON c.id = fda.caso
		INNER JOIN pacientes p ON p.id = c.paciente 
		INNER JOIN establecimientos est ON est.id = c.establecimiento
		
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