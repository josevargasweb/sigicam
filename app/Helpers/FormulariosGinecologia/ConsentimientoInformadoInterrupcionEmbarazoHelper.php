<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioInterrupcionEmbarazo;


class ConsentimientoInformadoInterrupcionEmbarazoHelper extends CasoHelper { 

    function getData($req){

        // CAPTURAR LO QUE LLEGA

        //init
        $mifepristona = false;
        $misoprostol = false;

        $aspiracion_endouterina = false;
        $legrado_uterino = false;
        $dilatacion_evacuacion_uterina = false;
        $induccion_parto_prematuro = false;
        $cesarea = false;

        //consentimiento_informado_interrupcion_embarazo_medicantoso
        $consentimiento_informado_interrupcion_embarazo_medicantoso = 
        (isset($req->consentimiento_informado_interrupcion_embarazo_medicantoso) && 
        is_array($req->consentimiento_informado_interrupcion_embarazo_medicantoso)) ? 
        $req->consentimiento_informado_interrupcion_embarazo_medicantoso : array();

        //consentimiento_informado_interrupcion_embarazo_instrumental
        $consentimiento_informado_interrupcion_embarazo_instrumental = 
        (isset($req->consentimiento_informado_interrupcion_embarazo_instrumental) && 
        is_array($req->consentimiento_informado_interrupcion_embarazo_instrumental)) ? 
        $req->consentimiento_informado_interrupcion_embarazo_instrumental : array();

        //consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar
        $consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar = (isset($req->consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar) && trim($req->consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar) !== "") ?
        trim(strip_tags($req->consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar)) : null;

        //consentimiento_informado_interrupcion_embarazo_controlada_en
        $consentimiento_informado_interrupcion_embarazo_controlada_en = (isset($req->consentimiento_informado_interrupcion_embarazo_controlada_en) && trim($req->consentimiento_informado_interrupcion_embarazo_controlada_en) !== "") ?
        trim(strip_tags($req->consentimiento_informado_interrupcion_embarazo_controlada_en)) : null;

        //consentimiento_informado_interrupcion_embarazo_consultas_contacto
        $consentimiento_informado_interrupcion_embarazo_consultas_contacto = (isset($req->consentimiento_informado_interrupcion_embarazo_consultas_contacto) && trim($req->consentimiento_informado_interrupcion_embarazo_consultas_contacto) !== "") ?
        trim(strip_tags($req->consentimiento_informado_interrupcion_embarazo_consultas_contacto)) : null;

        //caso_id
        $caso_id = (isset($req->caso_id) && trim($req->caso_id) !== "") ?
        trim(strip_tags($req->caso_id)) : null;

        //form_id
        $form_id = (isset($req->form_id) && trim($req->form_id) !== "") ?
        trim(strip_tags($req->form_id)) : null;


        // VALIDAR DOMINIO DE LO QUE LLEGA

        //consentimiento_informado_interrupcion_embarazo_medicantoso
        if(count($consentimiento_informado_interrupcion_embarazo_medicantoso) === 0){
            throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_medicantoso no valido.'); 
        } 
        else {

            $consentimiento_informado_interrupcion_embarazo_medicantoso_valid = [
                "mifepristona",
                "misoprostol"
            ];

            foreach ($consentimiento_informado_interrupcion_embarazo_medicantoso as $item) {
                if(!in_array($item, $consentimiento_informado_interrupcion_embarazo_medicantoso_valid)) { 
                    throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_medicantoso no valido.'); 
                }

                if($item === "mifepristona"){
                    $mifepristona = true;
                }
                else if ($item === "misoprostol"){
                    $misoprostol = true;
                }

            }
        }


        //consentimiento_informado_interrupcion_embarazo_instrumental
        if(count($consentimiento_informado_interrupcion_embarazo_instrumental) === 0){
            throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_instrumental no valido.'); 
        } 
        else {

            $consentimiento_informado_interrupcion_embarazo_instrumental_valid = [
                "aspiracion_endouterina",
                "legrado_uterino",
                "dilatacion_evacuacion_uterina",
                "induccion_parto_prematuro",
                "cesarea"
            ];

            foreach ($consentimiento_informado_interrupcion_embarazo_instrumental as $item) {
                if(!in_array($item, $consentimiento_informado_interrupcion_embarazo_instrumental_valid)) { 
                    throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_instrumental no valido.'); 
                }

                if($item === "aspiracion_endouterina"){
                    $aspiracion_endouterina = true;
                }
                else if ($item === "legrado_uterino"){
                    $legrado_uterino = true;
                }
                else if ($item === "dilatacion_evacuacion_uterina"){
                    $dilatacion_evacuacion_uterina = true;
                }
                else if ($item === "induccion_parto_prematuro"){
                    $induccion_parto_prematuro = true;
                }
                else if ($item === "cesarea"){
                    $cesarea = true;
                }

            }
        }

        //consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar
        if(mb_strlen($consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar) > 500){ throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar no valido.'); }

        //consentimiento_informado_interrupcion_embarazo_controlada_en
        if(mb_strlen($consentimiento_informado_interrupcion_embarazo_controlada_en) > 500){ throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_controlada_en no valido.'); }

        //consentimiento_informado_interrupcion_embarazo_consultas_contacto
        if(mb_strlen($consentimiento_informado_interrupcion_embarazo_consultas_contacto) > 500){ throw new Exception('Campo consentimiento_informado_interrupcion_embarazo_consultas_contacto no valido.'); }

        //caso_id
        $caso = parent::getCasoById($caso_id);
        if(!isset($caso)){ throw new Exception('Campo caso_id no valido.');  }

        $data = (object)[
            "mifepristona" => $mifepristona,
            "misoprostol" => $misoprostol,
            "aspiracion_endouterina" => $aspiracion_endouterina,
            "legrado_uterino" => $legrado_uterino,
            "dilatacion_evacuacion_uterina" => $dilatacion_evacuacion_uterina,
            "induccion_parto_prematuro" => $induccion_parto_prematuro,
            "cesarea" => $cesarea,
            "consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar" => $consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar,
            "consentimiento_informado_interrupcion_embarazo_controlada_en" => $consentimiento_informado_interrupcion_embarazo_controlada_en,
            "consentimiento_informado_interrupcion_embarazo_consultas_contacto" => $consentimiento_informado_interrupcion_embarazo_consultas_contacto,
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


        // PERSISTIR
        if(!isset($data->form_id)){
            $form_bd = $this->getFormularioInterrupcionEmbarazoByCasoId($data->caso_id);
            if(isset($form_bd)){ throw new Exception('Campo form_bd no valido.');  }

            $form = new FormularioInterrupcionEmbarazo();
            $form->caso = $data->caso_id;
            $form->id_paciente = $data->paciente_id ;
            $form->mifepristona = $data->mifepristona;
            $form->misoprostol = $data->misoprostol;
            $form->aspiracion_endouterina = $data->aspiracion_endouterina;
            $form->legrado_uterino = $data->legrado_uterino;
            $form->dilatacion_evacuacion_uterina = $data->dilatacion_evacuacion_uterina;
            $form->induccion_parto = $data->induccion_parto_prematuro;
            $form->cesarea = $data->cesarea;
            $form->consultas = $data->consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar;
            $form->controles = $data->consentimiento_informado_interrupcion_embarazo_controlada_en;
            $form->dudas = $data->consentimiento_informado_interrupcion_embarazo_consultas_contacto;
            $form->usuario_responsable = $data->usuario_responsable;
            $form->fecha = Carbon::now()->format("Y-m-d H:i:s");
            $form->save();

        }

        // ACTUALIZAR
        else {
            $form = $this->getFormularioInterrupcionEmbarazoById($data->form_id);
            if(!isset($form)){ throw new Exception('Campo form_id no valido.');  }
            $form->mifepristona = $data->mifepristona;
            $form->misoprostol = $data->misoprostol;
            $form->aspiracion_endouterina = $data->aspiracion_endouterina;
            $form->legrado_uterino = $data->legrado_uterino;
            $form->dilatacion_evacuacion_uterina = $data->dilatacion_evacuacion_uterina;
            $form->induccion_parto = $data->induccion_parto_prematuro;
            $form->cesarea = $data->cesarea;
            $form->consultas = $data->consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar;
            $form->controles = $data->consentimiento_informado_interrupcion_embarazo_controlada_en;
            $form->dudas = $data->consentimiento_informado_interrupcion_embarazo_consultas_contacto;
            $form->save();
        }
        
        $data->form = $form;
        return $data;

    }




    function getFormularioInterrupcionEmbarazoById($id_formulario){

        try {

            $formulario = FormularioInterrupcionEmbarazo::
            where("id_formulario_interrupcion_embarazo", $id_formulario)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }

    }

    function getFormularioInterrupcionEmbarazoByCasoId($caso_id){

        try {

            $formulario = FormularioInterrupcionEmbarazo::
            where("caso", $caso_id)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }
        
    }


    function getConsentimientoInformadoInterrupcionEmbarazoData($caso_id){
        
        $caso = parent::getCasoById($caso_id);
        
        if(!isset($caso)){ 
            throw new Exception('Campo caso_id no valido.');  
        } else {

            $doc = $this->getFormularioInterrupcionEmbarazoByCasoId($caso_id);

            $data = (object)[
                "caso_id" => $caso_id,
                "ficha_clinica" => $caso[0]->ficha_clinica,
                "form_id" => (isset($doc)) ? $doc->id_formulario_interrupcion_embarazo : null,
                "mifepristona" => (isset($doc)) ? (($doc->mifepristona === true) ? "si" : "no") : "no",
                "misoprostol" => (isset($doc)) ? (($doc->misoprostol === true) ? "si" : "no") : "no",
                "aspiracion_endouterina" => (isset($doc)) ? (($doc->aspiracion_endouterina === true) ? "si" : "no") : "no",
                "dilatacion_evacuacion_uterina" => (isset($doc)) ? (($doc->dilatacion_evacuacion_uterina === true) ? "si" : "no") : "no",
                "legrado_uterino" => (isset($doc)) ? (($doc->legrado_uterino === true) ? "si" : "no") : "no",
                "induccion_parto" => (isset($doc)) ? (($doc->induccion_parto === true) ? "si" : "no") : "no",
                "cesarea" => (isset($doc)) ? (($doc->cesarea === true) ? "si" : "no") : "no",
                "consultas" => (isset($doc)) ? $doc->consultas : null,
                "controles" => (isset($doc)) ? $doc->controles : null,
                "dudas" => (isset($doc)) ? $doc->dudas : null,

            ];
            
            return $data;

        }


    }
    function pdf($caso_id){
    	
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
		e.nombre AS nombre_establecimiento,
		e.nombre AS nombre_hospital,
		e.logo AS logo_hospital,
		
		ca.id_cama AS cama,
		s.nombre AS sala,
		uee.alias AS servicio,

		r.nombre_region,
		fie.*,
		TO_CHAR(fie.fecha,'DD-MM-YYYY')AS fecha
		FROM formulario_interrupcion_embarazo fie
		INNER JOIN casos c ON c.id = fie.caso
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