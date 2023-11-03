<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioProtocoloParto;



class ProtocoloDePartoHelper extends CasoHelper{ 

    function getData($req){

		//validar

		if($this->todosVacios($req->all())){
			throw new FormulariosGinecologiaException("Debe completar al menos un campo.");
		}
		
        $now = Carbon::now()->format('d/m/Y');

        /* CAPTURAR LO QUE LLEGA */
		$datos = [];

        $datos["diagnostico_preoperatorio"] = $this->traerValor($req->diagnostico_preoperatorio);
        $datos["cirujano"] = $this->traerValor($req->cirujano);
        $datos["anestesista"] = $this->traerValor($req->anestesista);
		$datos["ayudante"] = $this->traerValor($req->ayudante);
        $datos["matrona"] = $this->traerValor($req->matrona);
        $datos["arsenalera"] = $this->traerValor($req->arsenalera);
        $datos["operacion"] = $this->traerValor($req->operacion);
        $datos["diagnostico_postoperatorio"] = $this->traerValor($req->diagnostico_postoperatorio);
        $datos["descripcion_operatoria"] = $this->traerValor($req->descripcion_operatoria);
		
		$datos["tipo_parto"] = $this->traerValor($req->tipo_parto);
		$datos["termino_embarazo"] = $this->traerValor($req->termino_embarazo);
		$datos["indicaciones_principales"] = $this->traerValor($req->indicaciones_principales);
		$datos["indicaciones_principales_mini"] = $this->traerValor($req->mini_indicaciones_principales);
		$datos["nivel_atencion"] = $this->traerValor($req->nivel_atencion);
		$datos["atendido_parto"] = $req->atendido_parto;
		$datos["responsable_at_maternal"] = $this->traerValor($req->responsable_at_maternal);
		$datos["atendido_neonato"] = $req->atendido_neonato;
		$datos["responsable_at_neonatal"] = $this->traerValor($req->responsable_at_neonatal);
		$datos["muerte_intrauterina"] = $this->traerValorRadio($req->muerte_intrauterina);
		$datos["muerte_intrauterina_detalle"] = $this->traerValor($req->muerte_intrauterina_detalle);
		$datos["episiotomia"] = $this->traerValorRadio($req->episiotomia);
		$datos["desgarros"] = $this->traerValorRadio($req->desgarros);
		$datos["alumbramiento_natural"] = $this->traerValorRadio($req->alumbramiento_natural);
		$datos["alumbramiento_completo"] = $this->traerValorRadio($req->alumbramiento_completo);
		$datos["revision_instrumental"] = $this->traerValorRadio($req->revision_instrumental);
		$datos["peso_placenta"] = $this->traerValor($req->peso_placenta);
		$datos["long_cordon"] = $this->traerValor($req->long_cordon);
		$datos["observaciones_placenta_cordon"] = $this->traerValor($req->observaciones_placenta_cordon);
		
		$datos["anestesia_peridural"] = $this->traerValorCheck($req->anestesia_peridural);
		$datos["anestesia_raquidea"] = $this->traerValorCheck($req->anestesia_raquidea);
		$datos["anestesia_general"] = $this->traerValorCheck($req->anestesia_general);
		$datos["anestesia_local"] = $this->traerValorCheck($req->anestesia_local);
		$datos["anestesia_analgesia_tranquilizante"] = $this->traerValorCheck($req->anestesia_analgesia_tranquilizante);
		$datos["anestesia_ninguna"] = $this->traerValorCheck($req->anestesia_ninguna);
		$datos["medicamento"] = $req->medicamento;
		$datos["medicamento_detalle_otro"] = $this->traerValor($req->medicamento_detalle_otro);
		$datos["sexo_recien_nacido"] = $this->traerValor($req->sexo_recien_nacido);
		$datos["peso_al_nacer"] = $this->traerValor($req->peso_al_nacer);
		$datos["peso_menor_2500"] = $this->traerValor($req->peso_menor_2500);
		$datos["talla"] = $this->traerValor($req->talla);
		$datos["per_cef"] = $this->traerValor($req->per_cef);
		$datos["edad_ex_fisico"] = $this->traerValor($req->edad_ex_fisico);
		$datos["edad_menor_37"] = $this->traerValor($req->edad_menor_37);
		$datos["peso_eg"] = $this->traerValor($req->peso_eg);
		$datos["apgar_1_min"] = $this->traerValor($req->apgar_1_min);
		$datos["apgar_3_min"] = $this->traerValor($req->apgar_3_min);
		$datos["apgar_5_min"] = $this->traerValor($req->apgar_5_min);
		$datos["apgar_10_min"] = $this->traerValor($req->apgar_10_min);
		
		$datos["reanim_resp"] = $this->traerValor($req->reanim_resp);
		$datos["vdrl"] = $this->traerValor($req->vdrl);
		$datos["vih"] = $this->traerValor($req->vih);
		$datos["examen_fisico"] = $this->traerValor($req->examen_fisico);
		$datos["alojamiento_conjunto"] = $this->traerValorRadio($req->alojamiento_conjunto);
		$datos["hospitalizado"] = $this->traerValorRadio($req->hospitalizado);
		
        //caso_id
        $caso_id = $this->traerValor($req->caso_id);
		
        //form_id
        $datos["form_id"] = $this->traerValor($req->form_id);
        
        //caso_id
        $caso = parent::getCasoById($caso_id);
        if(!isset($caso)){ throw new FormulariosGinecologiaException('Campo caso_id no valido.');  }

		$datos["usuario_responsable"] = Auth::user()->id;
		
		$datos["caso"] = $caso;
		$datos["caso_id"] = $caso[0]->caso_id;
		$datos["paciente_id"] = $caso[0]->paciente_id;

        return (object)$datos;

    }
	/**
	 * Para los radio que son si/no
	 * @param string $val valor que viene del formulario
	 * @return bool
	 */
	private function traerValorRadio($val){
		$v = $this->traerValor($val);
		return $v === "si" ? true : ($v === "no" ? false : null);
	}
	private function traerValor($val){
		return (isset($val) && trim($val) !== "") ? trim(strip_tags($val)) : null;  
	}
	private function traerValorCheck($val){
		$v = $this->traerValor($val);
		return $v !== "" && $v !== null ? true : false;
	}
	private function todosVacios($datos){
		$variables_excluidas = ["caso_id","form_id","_token"];
		foreach($datos as $variable => $dato){
			if(in_array($variable,$variables_excluidas)){
				continue;
			}
			if(trim($dato) !== ""){
				return false;
			}
		}
		return true;
	}
	private function atendido_parto(&$form,$valores){
		$form->atendido_parto_medico = null;
		$form->atendido_parto_matrona = null;
		$form->atendido_parto_aux = null;
		$form->atendido_parto_alumno = null;
		$form->atendido_parto_otro = null;
		if(!is_array($valores))
		{
			return;
		}
		foreach($valores as $valor){
			$form->{$valor} = true;
		}
	}
	private function atendido_neonato(&$form,$valores){
		$form->atendido_neonato_medico = null;
		$form->atendido_neonato_matrona = null;
		$form->atendido_neonato_aux = null;
		$form->atendido_neonato_alumno = null;
		$form->atendido_neonato_otro = null;
		if(!is_array($valores))
		{
			return;
		}
		foreach($valores as $valor){
			$form->{$valor} = true;
		}
	}
	private function medicamento(&$form,$valores){
		$form->medicamento_ocitocina = null;
		$form->medicamento_antibioticos = null;
		$form->medicamento_otro = null;
		$form->medicamento_ninguno = null;
		if(!is_array($valores))
		{
			return;
		}
		foreach($valores as $valor){
			$form->{$valor} = true;
		}
	}

    function store($req){

        $data = $this->getData($req);

            /* PERSISTIR */
            if(!isset($data->form_id)){
                $form_bd = $this->getProtocoloPartoByCasoId($data->caso_id);
                if(isset($form_bd)){ throw new FormulariosGinecologiaException('Campo form_bd no valido.');  }
    
                $form = new FormularioProtocoloParto();
                $form->caso = $data->caso_id;
                $form->id_paciente = $data->paciente_id ;
                $form->usuario_responsable = $data->usuario_responsable;

                //
                $form->diagnostico_pre_operatorio = $data->diagnostico_preoperatorio;
                $form->cirujano = $data->cirujano;
                $form->anestesista = $data->anestesista;
				$form->ayudante = $data->ayudante;
				$form->matrona = $data->matrona;
                $form->arsenalera = $data->arsenalera;
                $form->operacion = $data->operacion;
                $form->diagnostico_post_operatorio = $data->diagnostico_postoperatorio;
                $form->descripcion_operatoria = $data->descripcion_operatoria;
				
				$form->tipo_parto = $data->tipo_parto;
				$form->termino_embarazo = $data->termino_embarazo;
				$form->indicaciones_principales = $data->indicaciones_principales;
				$form->indicaciones_principales_mini = $data->indicaciones_principales_mini;
				
				$form->nivel_atencion = $data->nivel_atencion;
				
				$this->atendido_parto($form, $data->atendido_parto);
				
				$form->responsable_at_maternal = $data->responsable_at_maternal;
				
				$this->atendido_neonato($form, $data->atendido_neonato);
				
				$form->responsable_at_neonatal = $data->responsable_at_neonatal;
				$form->muerte_intrauterina = $data->muerte_intrauterina;
				$form->muerte_intrauterina_detalle = $data->muerte_intrauterina_detalle;
				$form->episiotomia = $data->episiotomia;
				$form->desgarros = $data->desgarros;
				$form->alumbramiento_natural = $data->alumbramiento_natural;
				$form->alumbramiento_completo = $data->alumbramiento_completo;
				$form->revision_instrumental = $data->revision_instrumental;
				$form->peso_placenta = $data->peso_placenta;
				$form->long_cordon = $data->long_cordon;
				$form->observaciones_placenta_cordon = $data->observaciones_placenta_cordon;
				
				$form->anestesia_peridural = $data->anestesia_peridural;
				$form->anestesia_raquidea = $data->anestesia_raquidea;
				$form->anestesia_general = $data->anestesia_general;
				$form->anestesia_local = $data->anestesia_local;
				$form->anestesia_analgesia_tranquilizante = $data->anestesia_analgesia_tranquilizante;
				$form->anestesia_ninguna = $data->anestesia_ninguna;

				$this->medicamento($form, $data->medicamento);
				$form->medicamento_detalle_otro = $data->medicamento_detalle_otro;
				$form->sexo_recien_nacido = $data->sexo_recien_nacido;
				$form->peso_al_nacer = $data->peso_al_nacer;
				$form->peso_menor_2500 = ($data->peso_menor_2500 ? true : false);
				$form->talla = $data->talla;
				$form->per_cef = $data->per_cef;
				$form->edad_ex_fisico = $data->edad_ex_fisico;
				$form->edad_menor_37 = ($data->edad_menor_37 ? true : false);
				$form->peso_eg = $data->peso_eg;
				$form->apgar_1_min = $data->apgar_1_min;
				$form->apgar_3_min = $data->apgar_3_min;
				$form->apgar_5_min = $data->apgar_5_min;
				$form->apgar_10_min = $data->apgar_10_min;

				$form->reanim_resp = $data->reanim_resp;
				$form->vdrl = $data->vdrl;
				$form->vih = $data->vih;
				$form->examen_fisico = $data->examen_fisico;
				$form->alojamiento_conjunto = $data->alojamiento_conjunto;
				$form->hospitalizado = $data->hospitalizado;

                $form->save();
    
            }
    
            /* ACTUALIZAR */
            else {
                $form = $this->getProtocoloPartoById($data->form_id);
                if(!isset($form)){ throw new FormulariosGinecologiaException('Campo form_id no valido.');  }
                $form->diagnostico_pre_operatorio = $data->diagnostico_preoperatorio;
                $form->cirujano = $data->cirujano;
                $form->anestesista = $data->anestesista;
				$form->ayudante = $data->ayudante;
				$form->matrona = $data->matrona;
                $form->arsenalera = $data->arsenalera;
                $form->operacion = $data->operacion;
                $form->diagnostico_post_operatorio = $data->diagnostico_postoperatorio;
                $form->descripcion_operatoria = $data->descripcion_operatoria;
				
				$form->tipo_parto = $data->tipo_parto;
				$form->termino_embarazo = $data->termino_embarazo;
				$form->indicaciones_principales = $data->indicaciones_principales;
				$form->indicaciones_principales_mini = $data->indicaciones_principales_mini;
				//poner nivel de atención
				$form->nivel_atencion = $data->nivel_atencion;
				
				$this->atendido_parto($form, $data->atendido_parto);
				
				$form->responsable_at_maternal = $data->responsable_at_maternal;
				$this->atendido_neonato($form, $data->atendido_neonato);
				$form->responsable_at_neonatal = $data->responsable_at_neonatal;
				$form->muerte_intrauterina = $data->muerte_intrauterina;
				$form->muerte_intrauterina_detalle = $data->muerte_intrauterina_detalle;
				$form->episiotomia = $data->episiotomia;
				$form->desgarros = $data->desgarros;
				$form->alumbramiento_natural = $data->alumbramiento_natural;
				$form->alumbramiento_completo = $data->alumbramiento_completo;
				$form->revision_instrumental = $data->revision_instrumental;
				$form->peso_placenta = $data->peso_placenta;
				$form->long_cordon = $data->long_cordon;
				$form->observaciones_placenta_cordon = $data->observaciones_placenta_cordon;
				
				$form->anestesia_peridural = $data->anestesia_peridural;
				$form->anestesia_raquidea = $data->anestesia_raquidea;
				$form->anestesia_general = $data->anestesia_general;
				$form->anestesia_local = $data->anestesia_local;
				$form->anestesia_analgesia_tranquilizante = $data->anestesia_analgesia_tranquilizante;
				$form->anestesia_ninguna = $data->anestesia_ninguna;
				
				$this->medicamento($form, $data->medicamento);
				$form->medicamento_detalle_otro = $data->medicamento_detalle_otro;
				$form->sexo_recien_nacido = $data->sexo_recien_nacido;
				$form->peso_al_nacer = $data->peso_al_nacer;
				$form->peso_menor_2500 = ($data->peso_menor_2500 ? true : false);
				$form->talla = $data->talla;
				$form->per_cef = $data->per_cef;
				$form->edad_ex_fisico = $data->edad_ex_fisico;
				$form->edad_menor_37 = ($data->edad_menor_37 ? true : false);
				$form->peso_eg = $data->peso_eg;
				$form->apgar_1_min = $data->apgar_1_min;
				$form->apgar_3_min = $data->apgar_3_min;
				$form->apgar_5_min = $data->apgar_5_min;
				$form->apgar_10_min = $data->apgar_10_min;
				$form->reanim_resp = $data->reanim_resp;
				$form->vdrl = $data->vdrl;
				$form->vih = $data->vih;
				$form->examen_fisico = $data->examen_fisico;
				$form->alojamiento_conjunto = $data->alojamiento_conjunto;
				$form->hospitalizado = $data->hospitalizado;
				
                $form->save();
            }
    
            $data->form = $form;
            return $data;

    }


    function getProtocoloPartoById($id_formulario){
        try {

            $formulario = FormularioProtocoloParto::
            where("id_formulario_protocolo_parto", $id_formulario)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }

    }

    function getProtocoloPartoByCasoId($caso_id){
        try {

            $formulario = FormularioProtocoloParto::
            where("caso", $caso_id)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }   
    }


    function getProtocoloPartoData($caso_id){
        
        $caso = parent::getCasoById($caso_id);

        if(!isset($caso)){ 
            throw new FormulariosGinecologiaException('Campo caso_id no valido.');  
        } else {
            $doc = $this->getProtocoloPartoByCasoId($caso_id);
            $data = (object)[
                "caso_id" => $caso_id,
                "form_id" => (isset($doc)) ? $doc->id_formulario_protocolo_parto : null,
                "diagnostico_preoperatorio" => (isset($doc)) ? $doc->diagnostico_pre_operatorio : null,
                "cirujano" => (isset($doc)) ? $doc->cirujano : null,
                "anestesista" => (isset($doc)) ? $doc->anestesista : null,
				"ayudante" => (isset($doc)) ? $doc->ayudante : null,
				"matrona" => (isset($doc)) ? $doc->matrona : null,
                "arsenalera" => (isset($doc)) ? $doc->arsenalera : null,
                "operacion" => (isset($doc)) ? $doc->operacion : null,
                "diagnostico_postoperatorio" => (isset($doc)) ? $doc->diagnostico_post_operatorio : null,
                "descripcion_operatoria" => (isset($doc)) ? $doc->descripcion_operatoria : null,
				
				"tipo_parto" => (isset($doc)) ? $doc->tipo_parto : null,
				"termino_embarazo" => (isset($doc)) ? $doc->termino_embarazo : null,
				"indicaciones_principales" => (isset($doc)) ? $doc->indicaciones_principales : null,
				"indicaciones_principales_mini" => (isset($doc)) ? $doc->indicaciones_principales_mini : null,
				"nivel_atencion" => (isset($doc)) ? $doc->nivel_atencion : null,
				"atendido_parto_medico" => (isset($doc)) ? $doc->atendido_parto_medico : null,
				"atendido_parto_matrona" => (isset($doc)) ? $doc->atendido_parto_matrona : null,
				"atendido_parto_aux" => (isset($doc)) ? $doc->atendido_parto_aux : null,
				"atendido_parto_alumno" => (isset($doc)) ? $doc->atendido_parto_alumno : null,
				"atendido_parto_otro" => (isset($doc)) ? $doc->atendido_parto_otro : null,
				"responsable_at_maternal" => (isset($doc)) ? $doc->responsable_at_maternal : null,
				"atendido_neonato_medico" => (isset($doc)) ? $doc->atendido_neonato_medico : null,
				"atendido_neonato_matrona" => (isset($doc)) ? $doc->atendido_neonato_matrona : null,
				"atendido_neonato_aux" => (isset($doc)) ? $doc->atendido_neonato_aux : null,
				"atendido_neonato_alumno" => (isset($doc)) ? $doc->atendido_neonato_alumno : null,
				"atendido_neonato_otro" => (isset($doc)) ? $doc->atendido_neonato_otro : null,
				"responsable_at_neonatal" => (isset($doc)) ? $doc->responsable_at_neonatal : null,
				"muerte_intrauterina" => (isset($doc)) ? $doc->muerte_intrauterina : null,
				"muerte_intrauterina_detalle" => (isset($doc)) ? $doc->muerte_intrauterina_detalle : null,
				"episiotomia" => (isset($doc)) ? $doc->episiotomia : null,
				"desgarros" => (isset($doc)) ? $doc->desgarros : null,
				"alumbramiento_natural" => (isset($doc)) ? $doc->alumbramiento_natural : null,
				"alumbramiento_completo" => (isset($doc)) ? $doc->alumbramiento_completo : null,
				"revision_instrumental" => (isset($doc)) ? $doc->revision_instrumental : null,
				"peso_placenta" => (isset($doc)) ? $doc->peso_placenta : null,
				"long_cordon" => (isset($doc)) ? $doc->long_cordon : null,
				"observaciones_placenta_cordon" => (isset($doc)) ? $doc->observaciones_placenta_cordon : null,
				"anestesia_peridural" => (isset($doc)) ? $doc->anestesia_peridural : null,
				"anestesia_raquidea" => (isset($doc)) ? $doc->anestesia_raquidea : null,
				"anestesia_general" => (isset($doc)) ? $doc->anestesia_general : null,
				"anestesia_local" => (isset($doc)) ? $doc->anestesia_local : null,
				"anestesia_analgesia_tranquilizante" => (isset($doc)) ? $doc->anestesia_analgesia_tranquilizante : null,
				"anestesia_ninguna" => (isset($doc)) ? $doc->anestesia_ninguna : null,
				"medicamento_ocitocina" => (isset($doc)) ? $doc->medicamento_ocitocina : null,
				"medicamento_antibioticos" => (isset($doc)) ? $doc->medicamento_antibioticos : null,
				"medicamento_otro" => (isset($doc)) ? $doc->medicamento_otro : null,
				"medicamento_ninguno" => (isset($doc)) ? $doc->medicamento_ninguno : null,
				"medicamento_detalle_otro" => (isset($doc)) ? $doc->medicamento_detalle_otro : null,
				"sexo_recien_nacido" => (isset($doc)) ? $doc->sexo_recien_nacido : null,
				"peso_al_nacer" => (isset($doc)) ? $doc->peso_al_nacer : null,
				"peso_menor_2500" => (isset($doc)) ? $doc->peso_menor_2500 : null,
				"talla" => (isset($doc)) ? $doc->talla : null,
				"per_cef" => (isset($doc)) ? $doc->per_cef : null,
				"edad_ex_fisico" => (isset($doc)) ? $doc->edad_ex_fisico : null,
				"edad_menor_37" => (isset($doc)) ? $doc->edad_menor_37 : null,
				"peso_eg" => (isset($doc)) ? $doc->peso_eg : null,
				"apgar_1_min" => (isset($doc)) ? $doc->apgar_1_min : null,
				"apgar_3_min" => (isset($doc)) ? $doc->apgar_3_min : null,
				"apgar_5_min" => (isset($doc)) ? $doc->apgar_5_min : null,
				"apgar_10_min" => (isset($doc)) ? $doc->apgar_10_min : null,
				
				"reanim_resp" => (isset($doc)) ? $doc->reanim_resp : null,
				"vdrl" => (isset($doc)) ? $doc->vdrl : null,
				"vih" => (isset($doc)) ? $doc->vih : null,
				"examen_fisico" => (isset($doc)) ? $doc->examen_fisico : null,
				"alojamiento_conjunto" => (isset($doc)) ? $doc->alojamiento_conjunto : null,
				"hospitalizado" => (isset($doc)) ? $doc->hospitalizado : null
            ];

            return $data;

        }

    }
    function pdf($caso_id){
    	
    	$datos_pdf = DB::select("SELECT
		EXTRACT(YEAR FROM AGE(c.fecha_ingreso ,fecha_nacimiento)) AS edad,
		c.ficha_clinica AS ficha_clinica,
		p.rut AS run,
		CASE p.dv = 10 THEN 
			'K'
		ELSE
			p.dv
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