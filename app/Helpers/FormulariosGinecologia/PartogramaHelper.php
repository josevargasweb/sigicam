<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;
use App\Models\FormulariosGinecologia\FormularioPartograma;
use App\Models\FormulariosGinecologia\FormularioPartogramaBloque;
use App\Models\FormulariosGinecologia\FormularioPartogramaDatos;
use App\Models\FormulariosGinecologia\FormularioPartogramaTabla;
use App\Models\FormulariosGinecologia\FormularioPartogramaEvolucion;



class PartogramaHelper extends CasoHelper{ 

    function getData($req){

        $now_date = Carbon::now()->format('d/m/Y');
        $now_datetime = Carbon::now()->format('d/m/Y H:i:s');

        /* CAPTURAR LO QUE LLEGA */

		//tabla de datos

		//partograma_tabla_fila_id
        $partograma_tabla_fila_id = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_tabla_fila_id);		
		
        //partograma_hora 
        $partograma_hora = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_hora);
		$this->checkTimeArrayIsOrder($partograma_hora, 'd/m/Y H:i:s');

        //partograma_lcf 
        $partograma_lcf = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_lcf);	

        //partograma_pa_s 
        $partograma_pa_s = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_pa_s);	

        //partograma_pa_d 
        $partograma_pa_d = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_pa_d);	

        //partograma_pulso 
        $partograma_pulso = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_pulso);

        //partograma_du 
        $partograma_du = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_du);

        //partograma_frec 
        $partograma_frec = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_frec);

        //partograma_duracion 
        $partograma_duracion = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_duracion);

        //partograma_intensidad 
        $partograma_intensidad = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_intensidad);

        //partograma_cuello 
        $partograma_cuello = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_cuello);

        //partograma_membranas 
        $partograma_membranas = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_membranas);

        //partograma_la 
        $partograma_la = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_la);

        //partograma_uso_balon 
        $partograma_uso_balon = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_uso_balon);

        //partograma_posicion_materna 
        $partograma_posicion_materna = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_posicion_materna);

        //partograma_monitoreo 
        $partograma_monitoreo = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_monitoreo);


        //-------------------------------

        //partograma_analgesia_peridural 
        $partograma_analgesia_peridural = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_analgesia_peridural);


        //partograma_analgesia_peridural_observaciones 
        $partograma_analgesia_peridural_observaciones = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_analgesia_peridural_observaciones);

		//------------------

        //partograma_instalacion_de_via 
        $partograma_instalacion_de_via = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_instalacion_de_via);

        //partograma_instalacion_de_via_numero 
        $partograma_instalacion_de_via_numero = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_instalacion_de_via_numero);

        //partograma_instalacion_de_via_observaciones 
        $partograma_instalacion_de_via_observaciones = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_instalacion_de_via_observaciones);

        //partograma_instalacion_de_sonda_vesical 
        $partograma_instalacion_de_sonda_vesical = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_instalacion_de_sonda_vesical);

        //partograma_instalacion_de_sonda_vesical_numero 
        $partograma_instalacion_de_sonda_vesical_numero = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_instalacion_de_sonda_vesical_numero);


        //partograma_instalacion_de_sonda_vesical_observaciones 
        $partograma_instalacion_de_sonda_vesical_observaciones = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_instalacion_de_sonda_vesical_observaciones);

        //partograma_cateterismo_vesical 
        $partograma_cateterismo_vesical = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_cateterismo_vesical);

        //partograma_cateterismo_vesical_numero 
        $partograma_cateterismo_vesical_numero = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_cateterismo_vesical_numero);

        //partograma_cateterismo_vesical_observaciones 
        $partograma_cateterismo_vesical_observaciones = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_cateterismo_vesical_observaciones);

        //partograma_alergias 
        $partograma_alergias = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_alergias);

        //partograma_alergias_observaciones 
        $partograma_alergias_observaciones = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_alergias_observaciones);

        //partograma_medias_ate 
        $partograma_medias_ate = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_medias_ate);

		
		/* EVOLUCION */

		//partograma_id_evolucion
        $partograma_id_evolucion = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_id_evolucion);

        //partograma_evolucion_fecha 
        $partograma_evolucion_fecha = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->partograma_evolucion_fecha);
		$this->checkTimeArrayIsOrder($partograma_evolucion_fecha, 'd/m/Y H:i:s');


        //evolucion_observacion 
        $evolucion_observacion = 
        parent::uniArrayReplaceEmptyStringByNullAndSanitize($req->evolucion_observacion);

        //table_data rows ordenadas
        $table_data = parent::ordenamientoInputsMultiples(
            array(
				'partograma_tabla_fila_id' =>
				$partograma_tabla_fila_id,
                'partograma_hora'  =>
                $partograma_hora,
				'partograma_lcf' =>
				$partograma_lcf,
				'partograma_pa_s' =>
				$partograma_pa_s,
				'partograma_pa_d' =>
				$partograma_pa_d,
				'partograma_pulso' =>
				$partograma_pulso,
				'partograma_du' =>
				$partograma_du,
				'partograma_frec' =>
				$partograma_frec,
				'partograma_duracion' =>
				$partograma_duracion,
				'partograma_intensidad' =>
				$partograma_intensidad,
				'partograma_cuello' =>
				$partograma_cuello,
				'partograma_membranas' =>
				$partograma_membranas,
				'partograma_la' =>
				$partograma_la,
				'partograma_uso_balon' =>
				$partograma_uso_balon,
				'partograma_posicion_materna' =>
				$partograma_posicion_materna,
				'partograma_monitoreo' =>
				$partograma_monitoreo,
				'partograma_analgesia_peridural' =>
				$partograma_analgesia_peridural,
				'partograma_analgesia_peridural_observaciones' =>
				$partograma_analgesia_peridural_observaciones,
				'partograma_instalacion_de_via' =>
				$partograma_instalacion_de_via,
				'partograma_instalacion_de_via_numero' =>
				$partograma_instalacion_de_via_numero,
				'partograma_instalacion_de_via_observaciones' =>
				$partograma_instalacion_de_via_observaciones,
				'partograma_instalacion_de_sonda_vesical' =>
				$partograma_instalacion_de_sonda_vesical,
				'partograma_instalacion_de_sonda_vesical_numero' =>
				$partograma_instalacion_de_sonda_vesical_numero,
				'partograma_instalacion_de_sonda_vesical_observaciones' =>
				$partograma_instalacion_de_sonda_vesical_observaciones,
				'partograma_cateterismo_vesical' =>
				$partograma_cateterismo_vesical,
				'partograma_cateterismo_vesical_numero' =>
				$partograma_cateterismo_vesical_numero,
				'partograma_cateterismo_vesical_observaciones' =>
				$partograma_cateterismo_vesical_observaciones,
				'partograma_alergias' =>
				$partograma_alergias,
				'partograma_alergias_observaciones' =>
				$partograma_alergias_observaciones,
				'partograma_medias_ate' =>
				$partograma_medias_ate,

            )
        );



        //evolucion rows ordenadas
        $evolucion_data = parent::ordenamientoInputsMultiples(
            array(
                'partograma_id_evolucion'  =>
                $partograma_id_evolucion,
                'partograma_evolucion_fecha'  =>
                $partograma_evolucion_fecha,
				'evolucion_observacion' =>
				$evolucion_observacion,

            )
        );


    	$caso_id = (isset($req->caso_id) && trim($req->caso_id) !== "") ?
    	trim(strip_tags($req->caso_id)) : null;
    	
    	$form_id = (isset($req->form_id) && trim($req->form_id) !== "") ?
    	trim(strip_tags($req->form_id)) : null;

		/* VALIDAR DOMINIO DE LO QUE LLEGA  */


		//table
		foreach ($table_data as $key => $row){
			
			foreach ($row as $key => $input){

				//partograma_hora
				if($key === "partograma_hora"){

					$input_is_valid = isset($input) && parent::validateDate($input , 'd/m/Y H:i:s');
					if(!$input_is_valid){
						throw new Exception('Campo partograma_hora no valido.');
					}

				}

				//partograma_lcf
				else if ($key === "partograma_lcf"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT )&& (int) $input >=80 && (int) $input <=180;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_lcf no valido.');
					}
				}

				//partograma_pa_s
				else if ($key === "partograma_pa_s"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=40 && (int) $input <=250;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_pa_s no valido.');
					}
				}

				//partograma_pa_d
				else if ($key === "partograma_pa_d"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=20 && (int) $input <=150;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_pa_d
						 no valido.');
					}
				}

				// //partograma_pulso
				else if ($key === "partograma_pulso"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=10 && (int) $input <=300;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_pulso
						 no valido.');
					}
				}

				//partograma_du
				else if ($key === "partograma_du"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=0 && (int) $input <=10;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_du
						 no valido.');
					}
				}

				//partograma_frec
				else if ($key === "partograma_frec"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=20 && (int) $input <=240;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_du
						 no valido.');
					}
				}

				//partograma_duracion
				else if ($key === "partograma_duracion"){
					$input_is_valid = (mb_strlen($input) === 0) ? true :    
					filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=0 && (int) $input <= 300;

					if(!$input_is_valid){
						throw new Exception('Campo partograma_duracion
						 no valido.');
					}
				}

				//partograma_intensidad
				else if ($key === "partograma_intensidad"){

					$valid_data = ["+","++","+++"];
					$input_is_valid = (mb_strlen($input) === 0) ? true : in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_intensidad
						 no valido.');
					}
				}

                //partograma_cuello
                else if ($key === "partograma_cuello"){
                    $input_is_valid =  mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo partograma_cuello no valido.');
                    }

                }

				//partograma_membranas
				else if ($key === "partograma_membranas"){

					$valid_data = ["Integras","Rotas"];
					$input_is_valid = (mb_strlen($input) === 0) ? true : in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_membranas
						 no valido.');
					}
				}

                //partograma_la
                else if ($key === "partograma_la"){
                    $input_is_valid =  mb_strlen($input) <= 60;
                    if(!$input_is_valid){
                        throw new Exception('Campo partograma_la no valido.');
                    }

                }

				//partograma_uso_balon
				else if ($key === "partograma_uso_balon"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_uso_balon
						 no valido.');
					}
				}

				//partograma_posicion_materna
				else if ($key === "partograma_posicion_materna"){

					$valid_data = ["Decúbito lateral","Semi-sentada","Sentada","De pie","SIMS","Genupectoral"];
					$input_is_valid = (mb_strlen($input) === 0) ? true : in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_posicion_materna
						 no valido.');
					}
				}
				
				//partograma_monitoreo
				else if ($key === "partograma_monitoreo"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_monitoreo
						 no valido.');
					}
				}

				//partograma_analgesia_peridural
				else if ($key === "partograma_analgesia_peridural"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_analgesia_peridural
						 no valido. value: '.$input);
					}
				}

				//partograma_analgesia_peridural_observaciones
				else if ($key === "partograma_analgesia_peridural_observaciones"){


					if($row->partograma_analgesia_peridural == "no"){
						$input = null;
					}

				}


				//partograma_instalacion_de_via
				else if ($key === "partograma_instalacion_de_via"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_instalacion_de_via
						 no valido. value: '.$input);
					}
				}


				//partograma_instalacion_de_via_numero
				else if ($key === "partograma_instalacion_de_via_numero"){


					if($row->partograma_instalacion_de_via == "no"){
						$input = null;
					}
					else {
						$valid_data = ["14","16", "18", "20", "22", "24"];
						$input_is_valid = in_array($input, $valid_data);

						if(!$input_is_valid){
							throw new Exception('Campo partograma_instalacion_de_via_numero no valido. value: '.$input);
						}

					}

				}

				//partograma_instalacion_de_via_observaciones
				else if ($key === "partograma_instalacion_de_via_observaciones"){


					if($row->partograma_instalacion_de_via == "no"){
						$input = null;
					}

				}


				//partograma_instalacion_de_sonda_vesical
				else if ($key === "partograma_instalacion_de_sonda_vesical"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_instalacion_de_sonda_vesical
						 no valido. value: '.$input);
					}
				}

				//partograma_instalacion_de_sonda_vesical_numero
				else if ($key === "partograma_instalacion_de_sonda_vesical_numero"){


					if($row->partograma_instalacion_de_sonda_vesical == "no"){
						$input = null;
					}
					else {

						$input_is_valid = ($input != null && $input != "") ? filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=8 && (int) $input <=20 : false;

						if(!$input_is_valid){
							throw new Exception('Campo partograma_instalacion_de_sonda_vesical_numero no valido. value: '.$input);
						}

					}

				}

				//partograma_instalacion_de_sonda_vesical_observaciones
				else if ($key === "partograma_instalacion_de_sonda_vesical_observaciones"){


					if($row->partograma_instalacion_de_sonda_vesical == "no"){
						$input = null;
					}

				}

				//partograma_cateterismo_vesical
				else if ($key === "partograma_cateterismo_vesical"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_cateterismo_vesical
						 no valido. value: '.$input);
					}
				}

				//partograma_cateterismo_vesical_numero
				else if ($key === "partograma_cateterismo_vesical_numero"){


					if($row->partograma_cateterismo_vesical == "no"){
						$input = null;
					}
					else {

						$input_is_valid = ($input != null && $input != "") ? filter_var( $input, FILTER_VALIDATE_INT ) && (int) $input >=8 && (int) $input <=20 : false;

						if(!$input_is_valid){
							throw new Exception('Campo partograma_cateterismo_vesical_numero no valido. value: '.$input);
						}

					}

				}

				//partograma_cateterismo_vesical_observaciones
				else if ($key === "partograma_cateterismo_vesical_observaciones"){


					if($row->partograma_cateterismo_vesical == "no"){
						$input = null;
					}

				}

				//partograma_alergias
				else if ($key === "partograma_alergias"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_alergias
						 no valido. value: '.$input);
					}
				}


				//partograma_cateterismo_vesical_observaciones
				else if ($key === "partograma_alergias_observaciones"){


					if($row->partograma_cateterismo_vesical == "no"){
						$input = null;
					}


				}

				//partograma_medias_ate
				else if ($key === "partograma_medias_ate"){

					$valid_data = ["si","no"];
					$input_is_valid = in_array($input, $valid_data);

					if(!$input_is_valid){
						throw new Exception('Campo partograma_medias_ate
						 no valido. value: '.$input);
					}
				}


			}
			
		}

		//evolucion
		foreach ($evolucion_data as $key => $row){

			foreach ($row as $key => $input){

				//partograma_evolucion_fecha
				if($key === "partograma_evolucion_fecha"){

					$input_is_valid = isset($input) && parent::validateDate($input , 'd/m/Y H:i:s');
					if(!$input_is_valid){
						throw new Exception('Campo partograma_evolucion_fecha no valido.');
					}

				}

                //evolucion_observacion
                else if ($key === "evolucion_observacion"){
                    $input_is_valid =  mb_strlen($input) <= 300;
                    if(!$input_is_valid){
                        throw new Exception('Campo evolucion_observacion no valido.');
                    }

                }

			}
			
		}
    	
    	$caso = parent::getCasoById($caso_id);
    	if(!isset($caso)){ throw new Exception('Campo caso_id no valido.');  }
    	
        $data = (object)[
          
            "form_id" => $form_id,
            "usuario_responsable" => Auth::user()->id,
            "caso" => $caso,
            "caso_id" =>$caso[0]->caso_id,
            "paciente_id" => $caso[0]->paciente_id,
			"partograma_table_data" => $table_data,
			"partograma_evolucion_data" => $evolucion_data,
        ];
		

        return $data;

    } 


    function store($req){

        $data = $this->getData($req);

            /* PERSISTIR */
            if(!isset($data->form_id)){
                $form = $this->getPartogramaByCasoId($data->caso_id);
                if(!isset($form)){ 
					$form = new FormularioPartograma();
					$form->caso = $data->caso_id;
					$form->id_paciente = $data->paciente_id ;
					$form->usuario_responsable = $data->usuario_responsable;
					$form->fecha = Carbon::now()->format("Y-m-d H:i:s");
					$form->save();
				}

				//tabla
				foreach ($data->partograma_table_data as $key => $table_data){
					$tabla = new FormularioPartogramaTabla();
					$tabla->caso = $form->caso;
					$tabla->id_paciente = $form->id_paciente;
					$tabla->id_formulario_partograma =$form->id_formulario_partograma;
					$tabla->hora = $table_data->partograma_hora;
					$tabla->lcf = $table_data->partograma_lcf;
					$tabla->pulso = $table_data->partograma_pulso;
					$tabla->duracion = $table_data->partograma_duracion;
					$tabla->pa_s = $table_data->partograma_pa_s;
					$tabla->pa_d = $table_data->partograma_pa_d;
					$tabla->du = $table_data->partograma_du;
					$tabla->frecuencia_cardiaca = $table_data->partograma_frec;
					$tabla->intensidad = $table_data->partograma_intensidad;
					$tabla->cuello = $table_data->partograma_cuello;
					$tabla->posicion_materna = $table_data->partograma_posicion_materna;
					$tabla->membrana = $table_data->partograma_membranas;
					$tabla->la = $table_data->partograma_la;
					$tabla->uso_balon = ($table_data->partograma_uso_balon === "si") ? true : false;
					$tabla->monitoreo = ($table_data->partograma_monitoreo === "si") ? true : false;
					$tabla->analgesia_peridural = ($table_data->partograma_analgesia_peridural === "si") ? true : false;
					$tabla->observaciones_analgesia_peridural = $table_data->partograma_analgesia_peridural_observaciones;
					$tabla->instalacion_via	 = ($table_data->partograma_instalacion_de_via === "si") ? true : false;
					$tabla->numero_instalacion_via = $table_data->partograma_instalacion_de_via_numero;
					$tabla->observacion_instalacion_via	= $table_data->partograma_instalacion_de_via_observaciones;

					$tabla->sonda_vesical = ($table_data->partograma_instalacion_de_sonda_vesical === "si") ? true : false;
					$tabla->numero_sonda_vesical = $table_data->partograma_instalacion_de_sonda_vesical_numero;
					$tabla->observacion_sonda_vesical	= $table_data->partograma_instalacion_de_sonda_vesical_observaciones;

					$tabla->cateterismo_vesical = ($table_data->partograma_cateterismo_vesical === "si") ? true : false;
					$tabla->numero_cateterismo_vesical = $table_data->partograma_cateterismo_vesical_numero;
					$tabla->observacion_cateterismo_vesical	= $table_data->partograma_cateterismo_vesical_observaciones;

					$tabla->alergia	 = ($table_data->partograma_alergias === "si") ? true : false;
					$tabla->detalle_alergia = $table_data->partograma_alergias_observaciones;

					$tabla->medias_ate	= ($table_data->partograma_medias_ate === "si") ? true : false;


					$tabla->examinador = Auth::user()->id;
					$tabla->fecha = Carbon::now()->format("Y-m-d H:i:s");
					
					$tabla->save();
				}

				//evolucion
				foreach ($data->partograma_evolucion_data as $key => $evolucion_data){
					
					$evolucion = new FormularioPartogramaEvolucion();
					$evolucion->caso = $form->caso;
					$evolucion->id_paciente = $form->id_paciente;
					$evolucion->id_formulario_partograma =$form->id_formulario_partograma;
					$evolucion->fecha_evolucion = $evolucion_data->partograma_evolucion_fecha;
					$evolucion->observacion_evolucion = $evolucion_data->evolucion_observacion;	
					$evolucion->usuario_responsable = Auth::user()->id;
					$evolucion->fecha = Carbon::now()->format("Y-m-d H:i:s");				
					$evolucion->save();
				}
    
            }
    
            /* ACTUALIZAR */
            else {

                $form = $this->getPartogramaById($data->form_id);
                if(!isset($form)){ throw new Exception('Campo form_id no valido.');  }

				$id_formulario_partograma = $form->id_formulario_partograma	;

				//tabla
				foreach ($data->partograma_table_data as $key => $table_data){

					$partograma_tabla_fila_id = $table_data->partograma_tabla_fila_id;

					//no edita existentes
					if( !isset($partograma_tabla_fila_id)){ 

						$tabla = new FormularioPartogramaTabla();
						$tabla->caso = $form->caso;
						$tabla->id_paciente = $form->id_paciente;
						$tabla->id_formulario_partograma =$form->id_formulario_partograma;
						$tabla->hora = $table_data->partograma_hora;
						$tabla->lcf = $table_data->partograma_lcf;
						$tabla->pulso = $table_data->partograma_pulso;
						$tabla->duracion = $table_data->partograma_duracion;
						$tabla->pa_s = $table_data->partograma_pa_s;
						$tabla->pa_d = $table_data->partograma_pa_d;
						$tabla->du = $table_data->partograma_du;
						$tabla->frecuencia_cardiaca = $table_data->partograma_frec;
						$tabla->intensidad = $table_data->partograma_intensidad;
						$tabla->cuello = $table_data->partograma_cuello;
						$tabla->posicion_materna = $table_data->partograma_posicion_materna;
						$tabla->membrana = $table_data->partograma_membranas;
						$tabla->la = $table_data->partograma_la;
						$tabla->uso_balon = ($table_data->partograma_uso_balon === "si") ? true : false;
						$tabla->monitoreo = ($table_data->partograma_monitoreo === "si") ? true : false;
						$tabla->analgesia_peridural = ($table_data->partograma_analgesia_peridural === "si") ? true : false;
						$tabla->observaciones_analgesia_peridural = $table_data->partograma_analgesia_peridural_observaciones;
						$tabla->examinador = Auth::user()->id;

						$tabla->instalacion_via	 = ($table_data->partograma_instalacion_de_via === "si") ? true : false;
						$tabla->numero_instalacion_via = $table_data->partograma_instalacion_de_via_numero;
						$tabla->observacion_instalacion_via	= $table_data->partograma_instalacion_de_via_observaciones;

						$tabla->sonda_vesical = ($table_data->partograma_instalacion_de_sonda_vesical === "si") ? true : false;
						$tabla->numero_sonda_vesical = $table_data->partograma_instalacion_de_sonda_vesical_numero;
						$tabla->observacion_sonda_vesical	= $table_data->partograma_instalacion_de_sonda_vesical_observaciones;

						$tabla->cateterismo_vesical = ($table_data->partograma_cateterismo_vesical === "si") ? true : false;
						$tabla->numero_cateterismo_vesical = $table_data->partograma_cateterismo_vesical_numero;
						$tabla->observacion_cateterismo_vesical	= $table_data->partograma_cateterismo_vesical_observaciones;

						$tabla->alergia	 = ($table_data->partograma_alergias === "si") ? true : false;
						$tabla->detalle_alergia = $table_data->partograma_alergias_observaciones;

						$tabla->medias_ate	= ($table_data->partograma_medias_ate === "si") ? true : false;

						$tabla->save();	
					}



				}

				//evolucion
				foreach ($data->partograma_evolucion_data as $key => $evolucion_data){
					
					$evolucion = new FormularioPartogramaEvolucion();

					$partograma_id_evolucion = $evolucion_data->partograma_id_evolucion;

					//no edita existentes
					if( !isset($partograma_id_evolucion)){ 
						$evolucion->caso = $form->caso;
						$evolucion->id_paciente = $form->id_paciente;
						$evolucion->id_formulario_partograma =$form->id_formulario_partograma;
						$evolucion->fecha_evolucion = $evolucion_data->partograma_evolucion_fecha;
						$evolucion->observacion_evolucion = $evolucion_data->evolucion_observacion;		
						$evolucion->usuario_responsable = Auth::user()->id;			
						$evolucion->save();
					}
				}

            }
    
            $data->form = $form;
            return $data;

    }
    function guardarPartograma($request){
    	$usuario = Auth::user()->id;
    	
    	$id_formulario = $request->id_formulario;
    	try{
    		DB::beginTransaction();
    		
    		
    		$datos_formulario = FormularioPartograma::find($id_formulario);
    		if(!$datos_formulario)
    		{
    			throw new \Exception("Formulario no válido.");
    		}
    		
    		$datos = $request->datos;
    		
    		foreach($datos as $bloque){
    			$fpb = null;
    			if(!isset($bloque["id"])){
    				$fpb = new FormularioPartogramaBloque();
    				$fpb->caso = $datos_formulario->caso;
    				$fpb->id_paciente = $datos_formulario->id_paciente;
    				$fpb->id_formulario_partograma = $id_formulario;
    				$fpb->fecha_partograma = date("Y-m-d");
    				$fpb->hora_partograma = $bloque["hora"];
    				$fpb->usuario_responsable = $usuario;
    				
    				$fpb->save();
    			}
    			else{
    				$fpb = (object)["id_formulario_partograma_bloque" => $bloque["id"]];
    			}

    			if(isset($bloque["puntos"]))
    			{
	    			foreach($bloque["puntos"] as $datos_bloque){
	    				$fpd = null;
	    				if(isset($datos_bloque["id"])){
	    					continue;
	    				}
	    				else{
	    					$fpd = new FormularioPartogramaDatos();
	    				}
	    				$fpd->caso = $datos_formulario->caso;
	    				$fpd->id_paciente = $datos_formulario->id_paciente;
	    				$fpd->id_formulario_partograma = $id_formulario;
	    				$fpd->id_formulario_partograma_bloque = $fpb->id_formulario_partograma_bloque;
	    				$fpd->icono = $datos_bloque["valor"];
	    				$fpd->icono_x = $datos_bloque["x"];
	    				$fpd->icono_y = $datos_bloque["y"];
	    				$fpd->usuario_responsable = $usuario;
	    				$fpd->save();
	    			}
    			}
    			
    		}
    		DB::commit();
    		
    	}catch(\Exception $e){
    		DB::rollBack();
    		Log::error($e);
    	}
    }


    function getPartogramaById($id_formulario){
        try {

            $formulario = FormularioPartograma::
            where("id_formulario_partograma", $id_formulario)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }

    }

    function getPartogramaByCasoId($caso_id){
        try {

            $formulario = FormularioPartograma::
            where("caso", $caso_id)->
            first();
            return $formulario;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }   
    }
    function getPartogramaBloques($caso_id){
    	try {
    		
    		$bloques = DB::select("SELECT
			fpb.id_formulario_partograma_bloque AS id,
			TO_CHAR(fpb.hora_partograma,'HH24:MI') AS hora,
			fpb.fecha_partograma,
			u.nombres || ' ' || u.apellido_paterno || ' ' || u.apellido_materno AS usuario_responsable
			FROM formulario_partograma fp
			INNER JOIN formulario_partograma_bloque fpb ON fpb.id_formulario_partograma = fp.id_formulario_partograma
			INNER JOIN usuarios u ON u.id = fpb.usuario_responsable
			WHERE fp.caso = ?
			ORDER BY fpb.id_formulario_partograma_bloque ASC",[$caso_id]);
    		return $bloques;
    	}
    	catch (Exception $e){
    		Log::error($e);
    		return null;
    	}   
    }
    function getPartogramaDatosBloques($caso_id){
    	try {
    		
    		$datos = DB::select("SELECT
			fpd.id_formulario_partograma_datos AS id,
			fpd.id_formulario_partograma_bloque AS id_seccion,
			fpd.icono AS opcion,
			fpd.icono_x AS x,
			fpd.icono_y AS y,
			u.nombres || ' ' || u.apellido_paterno || ' ' || u.apellido_materno AS usuario_responsable
			FROM formulario_partograma fp
			INNER JOIN formulario_partograma_datos fpd ON fpd.id_formulario_partograma = fp.id_formulario_partograma
			INNER JOIN usuarios u ON u.id = fpd.usuario_responsable
			WHERE fp.caso = ?",[$caso_id]);
    		return $datos;
    	}
    	catch (Exception $e){
    		Log::error($e);
    		return null;
    	}
    }


    function getPartogramaData($caso_id){
        
        $caso = parent::getCasoById($caso_id);

        if(!isset($caso)){ 
            throw new Exception('Campo caso_id no valido.');  
        } else {
            $doc = $this->getPartogramaByCasoId($caso_id);

            if(isset($doc)){

                //formatear fechas de tabla dd/mm/YYYY
                foreach($doc->tabla as $data){

                    if(isset($data->hora)){
                        $data->hora = Carbon::parse($data->hora)->format("d/m/Y H:i:s");

                    }

					if($data->uso_balon === true){$data->uso_balon = "si";}
					else {$data->uso_balon = "no";}

					if($data->monitoreo === true){$data->monitoreo = "si";}
					else {$data->monitoreo = "no";}

					if($data->analgesia_peridural === true){$data->analgesia_peridural = "si";}
					else {$data->analgesia_peridural = "no";}

					//nuevos

					if($data->instalacion_via === true){$data->instalacion_via = "si";}
					else {$data->instalacion_via = "no";}

					if($data->sonda_vesical	 === true){$data->sonda_vesical = "si";}
					else {$data->sonda_vesical = "no";}

					if($data->cateterismo_vesical === true){$data->cateterismo_vesical = "si";}
					else {$data->cateterismo_vesical = "no";}

					if($data->alergia === true){$data->alergia = "si";}
					else {$data->alergia = "no";}

					if($data->medias_ate === true){$data->medias_ate = "si";}
					else {$data->medias_ate = "no";}


                }

                //formatear fechas de evolucion dd/mm/YYYY
                foreach($doc->evoluciones as $data){

                    if(isset($data->fecha_evolucion)){
                        $data->fecha_evolucion = Carbon::parse($data->fecha_evolucion)->format("d/m/Y H:i:s");

                    }

                }


            }

            
            $data = (object)[
                "caso_id" => $caso_id,
                "form_id" => (isset($doc)) ? $doc->id_formulario_partograma : null,
				"tabla" => (isset($doc)) ? $doc->tabla : array(),
				"evoluciones" => (isset($doc)) ? $doc->evoluciones : array(),
                
            ];
            
            return $data;

        }

    }

    function getTablaDePartograma($partograma_tabla_fila_id,$id_formulario_partograma){
        $tabla = FormularioPartogramaTabla::
            where("id_formulario_partograma_tabla", $partograma_tabla_fila_id)->
            where("id_formulario_partograma", $id_formulario_partograma)->
            first();
            return $tabla;

    }

    function getEvolucionDePartograma($partograma_id_evolucion,$id_formulario_partograma){
        $evolucion = FormularioPartogramaEvolucion::
            where("id_formulario_partograma_evolucion", $partograma_id_evolucion)->
            where("id_formulario_partograma", $id_formulario_partograma)->
            first();
            return $evolucion;

    }

	function checkTimeArrayIsOrder($timeArr, $format){
		$timestamp_array = array();

		foreach($timeArr as $time){

			if(parent::validateDate($time, $format)){
				$cTime = Carbon::createFromFormat($format, $time)->timestamp;;
				array_push($timestamp_array, $cTime);
				if(!parent::checkArrayIsSort($timestamp_array)){ throw new Exception('Campo fecha no valido.'); }
			}
			else {
				throw new Exception('Campo fecha no valido.');
			}

		}


	}
    function pdf($caso_id){
    	$datos = [];
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
		fp.*,
		TO_CHAR(fp.fecha,'DD-MM-YYYY')AS fecha
	
		FROM formulario_partograma fp
		INNER JOIN casos c ON c.id = fp.caso
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
    		$datos["formulario"] = $datos_pdf[0];
    	}
    	
    	$tabla = DB::select("SELECT
		fpt.*,
		TO_CHAR(fpt.hora,'DD-MM-YYYY HH24:MI:SS')AS hora,
		CASE WHEN fpt.uso_balon THEN 'Sí' ELSE 'No' END AS uso_balon,
		CASE WHEN fpt.monitoreo THEN 'Sí' ELSE 'No' END AS monitoreo,
		u.nombres || ' ' || u.apellido_paterno || ' ' || u.apellido_materno AS nombre_examinador
		FROM formulario_partograma_tabla fpt
		INNER JOIN usuarios u ON u.id = fpt.examinador
		WHERE fpt.caso = ?
		ORDER BY fpt.hora ASC",[$caso_id]);
    	
    	$minitabla = [];
    	$grupo_agregado = false;
   		for($i = 1; $i <= count($tabla); $i++)
   		{
   			$minitabla[] = $tabla[$i - 1];
   			if($i % 6 == 0)
   			{
   				$datos["tabla"][] = $minitabla;
   				$minitabla = [];
   				$grupo_agregado = true;
   			}
   			else{
   				$grupo_agregado = false;
   			}
   		}
   		if(!$grupo_agregado)
   		{
   			$datos["tabla"][] = $minitabla;
   		}
   		
   		
   		$evolucion = DB::select("SELECT
		fpe.*,
		TO_CHAR(fpe.fecha_evolucion,'DD-MM-YYYY')AS fecha_evolucion,
		u.nombres || ' ' || u.apellido_paterno || ' ' || u.apellido_materno AS nombre_usuario_responsable
		FROM formulario_partograma_evolucion fpe
		INNER JOIN usuarios u ON u.id = fpe.usuario_responsable
		WHERE fpe.caso = ?
		ORDER BY fpe.fecha_evolucion ASC",[$caso_id]);
   		
   		$datos["evolucion"] = $evolucion;

    	return $datos;
    	
    }

    function getPartogramaAlergias($req){

    	$alergias = array();

		/* CAPTURAR LO QUE LLEGA */

    	$caso_id = (isset($req->caso_id) && trim($req->caso_id) !== "") ?
    	trim(strip_tags($req->caso_id)) : null;


    	/* GET DATA FROM DATABASE */
		if(isset($caso_id)){

			
            $partogramaSeguimientos = FormularioPartogramaTabla::
            select('id_formulario_partograma_tabla', 'alergia', 'detalle_alergia')
            ->where("caso", $caso_id)->
            get();

            foreach ($partogramaSeguimientos as $key => $ps) {
            	$psArray = array(
            		"seguimiento_id" => $ps->id_formulario_partograma_tabla,
            		"alergia" => ($ps->alergia) ? "si" : "no",
            		"alergiaObs" => $ps->detalle_alergia,
            	);

            	array_push($alergias, $psArray);

			}

		}

		


		$alergiaData = array (
			"caso_id" => $caso_id,
			"alergias" => $alergias,
		);

		return json_encode($alergiaData);

    }
    public function tienePartograma($caso){
		$existe = DB::select("SELECT EXISTS(SELECT * FROM formulario_partograma WHERE caso = ?)AS existe",[$caso]);
		return $existe[0]->existe;
	}

}