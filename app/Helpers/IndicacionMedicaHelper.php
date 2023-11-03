<?php

namespace App\Helpers;

use Exception;
use DB;
use Log;
use Auth;
use Carbon\Carbon;

use App\Models\PlanificacionCuidadoIndicacionMedica;
use App\Models\PlanificacionIndicacionMedica;
use App\Models\HojaEnfermeriaEnfermeriaIndicacionMedica;
use App\Models\HojaEnfermeriaControlSignoVital;
use App\Models\IndicacionMedica;


class IndicacionMedicaHelper extends Helper{

    public function deleteIndicacionMedica($request){

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $id_indicacion = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        if(!isset($id_indicacion)){ throw new Exception('Id es nulo.'); }

        $indicacion = PlanificacionCuidadoIndicacionMedica::
        where("id", $id_indicacion)->
        where("visible", true)->
        first();

        if($indicacion){
            if($indicacion->estado_interconsulta === "Realizada"){ throw new Exception('Ha ocurrido un error.'); }

            $modificacion = '';
            if($request->tipo_modificacion == 2){
                $modificacion = 'Terminado';
            }elseif($request->tipo_modificacion == 3){
                $modificacion = 'Eliminado';
            }

            $indicacion->usuario_modifica = $auth_user_id;
            $indicacion->fecha_modificacion = $hoy;
            $indicacion->tipo_modificacion = $modificacion;
            $indicacion->visible = false;
            $indicacion->save();
        }
        else {
            throw new Exception('Ha ocurrido un error.');
        }

    }

    public function deleteHoraIndicacionMedica($request){

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");


        /* CAPTURAR LO QUE LLEGA */
        $id_indicacion = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        if(!isset($id_indicacion)){ throw new Exception('Id es nulo.'); }

        $indicacion = PlanificacionCuidadoIndicacionMedica::
        where("id", $id_indicacion)->
        where("visible", true)->
        first();


        if($indicacion){
            if($indicacion->estado_interconsulta === "Realizada"){ throw new Exception('Ha ocurrido un error.'); }

          $horas = explode(',',$indicacion->horario);
          if (in_array($request->hora, $horas)) {
            $posicion = array_search($request->hora, $horas);
            unset($horas[$posicion]);
    
            $tipo_modificacion = '';
            if($request->tipo_modificacion == 2){
                $tipo_modificacion = 'Terminado';
            }elseif($request->tipo_modificacion == 3){
                $tipo_modificacion = 'Eliminado';
            }
            /* indicacion_old */
            $indicacion->usuario_modifica = $auth_user_id;
            $indicacion->fecha_modificacion = $hoy;
            $indicacion->tipo_modificacion = $tipo_modificacion;
            $indicacion->horario = $request->hora;
            if($request->tipo_modificacion == 3){
            $indicacion->visible = false;
            }
            $indicacion->save();
      
            log::info('llega aca final'); 
            log::info($horas);
        if(!empty($horas)){
            /* indicacion_new */    
            $indicacion_new = new PlanificacionCuidadoIndicacionMedica();
            $indicacion_new->caso = $indicacion->caso;
            $indicacion_new->usuario = $auth_user_id;
            $indicacion_new->visible = true;
            $indicacion_new->fecha_creacion = Carbon::parse($indicacion->fecha_creacion);
            $indicacion_new->id_anterior = $indicacion->id; 
            $indicacion_new->tipo = $indicacion->tipo; 
            $indicacion_new->responsable = $indicacion->responsable;
            $indicacion_new->fecha_vigencia = $indicacion->fecha_vigencia;
            $indicacion_new->horario = implode(",", $horas);

                
             if($indicacion->tipo === "Medicamento"){
                log::info('medicamento');
                $indicacion_new->medicamento = $indicacion->medicamento;
                $indicacion_new->dosis = $indicacion->dosis;
                $indicacion_new->via = $indicacion->via;
                $indicacion_new->fecha_emision = $indicacion->fecha_emision;
                $indicacion_new->fecha_vigencia = $indicacion->fecha_vigencia;
                
            }elseif($indicacion->tipo === "Indicación"){
                log::info('Indicación');
                $indicacion_new->indicacion = $indicacion->indicacion;
            }      

            $indicacion_new->save();
     
            return (object) array (
                "nueva_id" =>  $indicacion_new->id
            );
            
        }else{
            return (object) array (
                "nueva_id" => '-1'
            );
        }
     
            }else{
                return (object) array (
                    "nueva_id" => '-1'
                );
            }
         
          
        }
        else {
            return false;
        }

    }


    public function addIndicacionMedica($request){

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $id_caso_agregar_indicacion = (isset($request->id_caso_agregar_indicacion) && trim($request->id_caso_agregar_indicacion) !== "") ?
        trim(strip_tags($request->id_caso_agregar_indicacion)) : null;

        $tipo = (isset($request->tipo_agregar_indicacion) && trim($request->tipo_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->tipo_agregar_indicacion)) : null;        

        $indicacion_descripcion = (isset($request->descripcion_indicacion_agregar_indicacion) && trim($request->descripcion_indicacion_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->descripcion_indicacion_agregar_indicacion)) : null;

        $medicamento = (isset($request->medicamento_descripcion_agregar_indicacion) && trim($request->medicamento_descripcion_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->medicamento_descripcion_agregar_indicacion)) : null;

        $fecha_emision = (isset($request->fecha_emision_medicamento_agregar_indicacion) && trim($request->fecha_emision_medicamento_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->fecha_emision_medicamento_agregar_indicacion)) : null;

        $fecha_vigencia = (isset($request->fecha_vigencia_medicamento_agregar_indicacion) && trim($request->fecha_vigencia_medicamento_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->fecha_vigencia_medicamento_agregar_indicacion)) : null;

        $dosis = (isset($request->dosis_medicamento_agregar_indicacion) && trim($request->dosis_medicamento_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->dosis_medicamento_agregar_indicacion)) : null;

        $via = (isset($request->via_medicamento_agregar_indicacion) && trim($request->via_medicamento_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->via_medicamento_agregar_indicacion)) : null;

        $horario_medicamento = (isset($request->horario_medicamento_agregar_indicacion)) ? 
        $request->horario_medicamento_agregar_indicacion : array();

        $horario_indicacion = (isset($request->horario_indicacion_agregar_indicacion)) ? 
        $request->horario_indicacion_agregar_indicacion : array();

        $fecha_creacion_indicacion = (isset($request->fecha_creacion_indicacion_agregar_indicacion) && trim($request->fecha_creacion_indicacion_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->fecha_creacion_indicacion_agregar_indicacion)) : null;

        $tipo_interconsulta = (isset($request->tipo_interconsulta_agregar_indicacion) && trim($request->tipo_interconsulta_agregar_indicacion) !== "" ) ?
        trim(strip_tags($request->tipo_interconsulta_agregar_indicacion)) : null;

        $responsable_medicamento = (isset($request->responsable_agregar_medicamento)) ?
        $request->responsable_agregar_medicamento : null;

        $responsable_indicacion = (isset($request->responsable_agregar_indicacion)) ?
        $request->responsable_agregar_indicacion : null;

        /* VALIDAR DOMINIO DE LO QUE LLEGA Y PERSISTIR */

        $tipo_valid = ['Medicamento', 'Indicación', 'Interconsulta'];
        $horario_valid = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
        $via_valid = ['Oral', 'Sublingual', 'Tópica', 'Transdérmica', 'Oftalmológica', 'Inhalatoria', 'Rectal', 'Vaginal', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Intradérmica', 'Ótica', 'Nasal'];
        $responsable_valid = ['1','2'];

        if (!in_array($tipo, $tipo_valid)) { throw new Exception('Campo tipo no valido.'); }

        /* indicacion */    
        $indicacion = new PlanificacionCuidadoIndicacionMedica();
        $indicacion->caso = $id_caso_agregar_indicacion;
        $indicacion->usuario = $auth_user_id;
        $indicacion->visible = true;
        $indicacion->tipo = $tipo; 

        if
        ($tipo === "Medicamento"){

            //via
            if (!in_array($via, $via_valid)) { throw new Exception('Campo via no valido.'); }

            //responsable
            if (!in_array($responsable_medicamento, $responsable_valid)) { throw new Exception('Campo responsable no valido'); }


            if (strlen($medicamento) > 100){
                throw new Exception("Campo medicamento no debe tener mas de 100 caracteres.");
            }

            if (strlen($dosis) > 100){
                throw new Exception("Campo dosis no debe tener mas de 100 caracteres.");
            }

            //fecha_emision_medicamento_modificacion_indicacion y fecha_vigencia_medicamento_modificacion_indicacion
            $request->validate([ 'fecha_emision_medicamento_agregar_indicacion' => 'date_format:d-m-Y H:i', 'fecha_vigencia_medicamento_agregar_indicacion' => 'date_format:d-m-Y H:i',]);

            $fecha_emision_vigencia_medicamento_is_valid = parent::dateComparison($fecha_emision, $fecha_vigencia,'d-m-Y H:i', 'a<b');
            if($fecha_emision_vigencia_medicamento_is_valid === false){
                throw new Exception('fecha emision medicamento debe ser menor a vigencia.');
            }

            //horario_medicamento
            if(count($horario_medicamento) > 0){
                foreach ($horario_medicamento as $key => $hora) {
                    if (!in_array($hora, $horario_valid)) { throw new Exception('Campo horario medicamento no valido.'); }
                }
            }
            else { throw new Exception('Campo horario medicamento no valido.');}

            $indicacion->responsable = $responsable_medicamento;
            $indicacion->fecha_creacion = $hoy;
            $indicacion->medicamento = $medicamento;
            $indicacion->dosis = $dosis;
            $indicacion->via = $via;
            $indicacion->horario = implode(",", $horario_medicamento);
            $indicacion->fecha_emision = $fecha_emision;
            $indicacion->fecha_vigencia = $fecha_vigencia;
        } 
        
        else if
        ($tipo === "Indicación") {

            /* responsable */
            if (!in_array($responsable_indicacion, $responsable_valid)) { throw new Exception('Campo responsable no valido'); }

            if (strlen($indicacion_descripcion) > 500){
                throw new Exception("Campo indicación no debe tener mas de 500 caracteres.");
            }

            //feecha_creacion
            if($fecha_creacion_indicacion == null){
                throw new Exception("Campo fecha no debe tener ser vacío.");
            }

            //horario_indicacion
            if(count($horario_indicacion) > 0){
                foreach ($horario_indicacion as $key => $hora) {
                    if (!in_array($hora, $horario_valid)) { throw new Exception('Campo horario medicamento no valido.'); }
                }
            }
            else { throw new Exception('Campo horario medicamento no valido.');}

            $indicacion->responsable = $responsable_indicacion;
            $indicacion->fecha_creacion = $hoy;
            $indicacion->fecha_vigencia = $fecha_creacion_indicacion;
            $indicacion->indicacion = $indicacion_descripcion;
            $indicacion->horario = implode(",", $horario_indicacion);
            
        }

        else if 
        ($tipo === "Interconsulta") {

            if (strlen($tipo_interconsulta) > 100){
                throw new Exception("Campo tipo no debe tener mas de 100 caracteres.");
            }

            $indicacion->tipo_interconsulta = $tipo_interconsulta;
            $indicacion->estado_interconsulta = 'Pendiente';
            $indicacion->fecha_creacion = $hoy;
        }
        
        $indicacion->save();

    }

    public function updateIndicacionMedica($request){   

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $indicacion_id = (isset($request->id_indicacion_actualizar) && trim($request->id_indicacion_actualizar) !== "") ?
        trim(strip_tags($request->id_indicacion_actualizar)) : null;

        $tipo = (isset($request->tipo_modificacion_indicacion) && trim($request->tipo_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->tipo_modificacion_indicacion)) : null;

        $indicacion_descripcion = (isset($request->descripcion_indicacion_modificacion_indicacion) && trim($request->descripcion_indicacion_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->descripcion_indicacion_modificacion_indicacion)) : null;

        $medicamento = (isset($request->medicamento_descripcion_modificacion_indicacion) && trim($request->medicamento_descripcion_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->medicamento_descripcion_modificacion_indicacion)) : null;

        $fecha_emision = (isset($request->fecha_emision_medicamento_modificacion_indicacion) && trim($request->fecha_emision_medicamento_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->fecha_emision_medicamento_modificacion_indicacion)) : null;

        $fecha_vigencia = (isset($request->fecha_vigencia_medicamento_modificacion_indicacion) && trim($request->fecha_vigencia_medicamento_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->fecha_vigencia_medicamento_modificacion_indicacion)) : null;

        $dosis = (isset($request->dosis_medicamento_modificacion_indicacion) && trim($request->dosis_medicamento_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->dosis_medicamento_modificacion_indicacion)) : null;

        $via = (isset($request->via_medicamento_modificacion_indicacion) && trim($request->via_medicamento_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->via_medicamento_modificacion_indicacion)) : null;

        $horario_medicamento = (isset($request->horario_medicamento_modificacion_indicacion)) ?
        $request->horario_medicamento_modificacion_indicacion : array();

        $horario_indicacion = (isset($request->horario_indicacion_modificacion_indicacion)) ?
        $request->horario_indicacion_modificacion_indicacion : array();

        $fecha_modificacion_indicacion = (isset($request->fecha_creacion_indicacion_modificacion_indicacion) && trim($request->fecha_creacion_indicacion_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->fecha_creacion_indicacion_modificacion_indicacion)) : null;

        $tipo_interconsulta = (isset($request->tipo_interconsulta_modificacion_indicacion) && trim($request->tipo_interconsulta_modificacion_indicacion) !== "" ) ?
        trim(strip_tags($request->tipo_interconsulta_modificacion_indicacion)) : null;

        $responsable_medicamento = (isset($request->responsable_modificacion_medicamento)) ?
        $request->responsable_modificacion_medicamento : null;

        $responsable_indicacion = (isset($request->responsable_modificacion_indicacion)) ?
        $request->responsable_modificacion_indicacion : null;
        
        /* VALIDAR DOMINIO DE LO QUE LLEGA Y PERSISTIR */

        $tipo_valid = ['Medicamento', 'Indicación', 'Interconsulta'];
        $horario_valid = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
        $via_valid = ['Oral', 'Sublingual', 'Tópica', 'Transdérmica', 'Oftalmológica', 'Inhalatoria', 'Rectal', 'Vaginal', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Intradérmica', 'Ótica', 'Nasal'];
        $responsable_valid = ['1','2'];

        if(!isset($indicacion_id)){ throw new Exception('Id es nulo.'); }

        $indicacion_old = PlanificacionCuidadoIndicacionMedica::
        where("id", $indicacion_id)
        ->where("visible", true)
        ->where(function($q){
            $q->whereNull("estado_interconsulta")
            ->orWhere("estado_interconsulta","Pendiente");
         })
        ->first();


        
        if(!$indicacion_old){ throw new Exception('Ha ocurrido un error.'); }


        if (!in_array($tipo, $tipo_valid)) { throw new Exception('Campo tipo no valido.'); }


        /* indicacion_old */
        $indicacion_old->usuario_modifica = $auth_user_id;
        $indicacion_old->fecha_modificacion = $hoy;
        $indicacion_old->tipo_modificacion = 'Modificado';
        $indicacion_old->visible = false;
        $indicacion_old->save();
        
        /* indicacion_new */    
        $indicacion_new = new PlanificacionCuidadoIndicacionMedica();
        $indicacion_new->caso = $indicacion_old->caso;
        $indicacion_new->usuario = $auth_user_id;
        $indicacion_new->visible = true;
        $indicacion_new->fecha_creacion = Carbon::parse($indicacion_old->fecha_creacion);
        $indicacion_new->id_anterior = $indicacion_old->id; 
        $indicacion_new->tipo = $tipo; 

        if
        ($tipo === "Medicamento"){

            //via
            if (!in_array($via, $via_valid)) { throw new Exception('Campo via no valido.'); }

            //responsable
            if (!in_array($responsable_medicamento, $responsable_valid)) { throw new Exception('Campo responsable no valido'); }

            //fecha_emision_medicamento_modificacion_indicacion y fecha_vigencia_medicamento_modificacion_indicacion
            $request->validate([ 'fecha_emision_medicamento_modificacion_indicacion' => 'date_format:d-m-Y H:i', 'fecha_vigencia_medicamento_modificacion_indicacion' => 'date_format:d-m-Y H:i',]);

            $fecha_emision_vigencia_medicamento_is_valid = parent::dateComparison($fecha_emision, $fecha_vigencia,'d-m-Y H:i', 'a<b');
            if($fecha_emision_vigencia_medicamento_is_valid === false){
                throw new Exception('fecha emision medicamento debe ser menor a vigencia.');
            }


            //horario_medicamento
            if(count($horario_medicamento) > 0){
                foreach ($horario_medicamento as $key => $hora) {
                    if (!in_array($hora, $horario_valid)) { throw new Exception('Campo horario medicamento no valido.'); }
                }
            }
            else { throw new Exception('Campo horario medicamento no valido.');}

            $indicacion_new->responsable = $responsable_medicamento;
            $indicacion_new->fecha_creacion = $hoy;
            $indicacion_new->medicamento = $medicamento;
            $indicacion_new->dosis = $dosis;
            $indicacion_new->via = $via;
            $indicacion_new->horario = implode(",", $horario_medicamento);
            $indicacion_new->fecha_emision = $fecha_emision;
            $indicacion_new->fecha_vigencia = $fecha_vigencia;
        } 
        
        else if
        ($tipo === "Indicación") {

            //responsable
            if (!in_array($responsable_indicacion, $responsable_valid)) { throw new Exception('Campo responsable no valido'); }

            if($fecha_modificacion_indicacion == null){
                throw new Exception("Campo fecha no debe tener ser vacío.");
            }

            //horario_indicacion
            if(count($horario_indicacion) > 0){
                foreach ($horario_indicacion as $key => $hora) {
                    if (!in_array($hora, $horario_valid)) { throw new Exception('Campo horario indicación no valido.'); }
                }
            }
            else { throw new Exception('Campo horario indicación no valido.');}

            $indicacion_new->responsable = $responsable_indicacion;
            $indicacion_new->fecha_creacion = $hoy;
            $indicacion_new->fecha_vigencia = $fecha_modificacion_indicacion;
            $indicacion_new->indicacion = $indicacion_descripcion;
            $indicacion_new->horario = implode(",", $horario_indicacion);
        }

        else if 
        ($tipo === "Interconsulta") {
            $indicacion_new->tipo_interconsulta = $tipo_interconsulta;
            
            if($indicacion_old->estado_interconsulta === 'Pendiente' || $indicacion_old->estado_interconsulta === 'Realizada'){
                $indicacion_new->estado_interconsulta = $indicacion_old->estado_interconsulta;
            }else {
                $indicacion_new->estado_interconsulta = 'Pendiente';
            }
        }
        
        $indicacion_new->save();

    }
    public function updateDatosIndicacionMedica($request){   

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $indicacion_id = (isset($request->id_indicacion_medica_actualizar) && trim($request->id_indicacion_medica_actualizar) !== "") ?
        trim(strip_tags($request->id_indicacion_medica_actualizar)) : null;

        $tipo = (isset($request->tipo_modificacion_indicacion_medica) && trim($request->tipo_modificacion_indicacion_medica) !== "" ) ?
        trim(strip_tags($request->tipo_modificacion_indicacion_medica)) : null;

        $horario_indicacion = (isset($request->horario_modificacion_indicacion_medica)) ?
        $request->horario_modificacion_indicacion_medica : array();

        $responsable_indicacion = (isset($request->responsable_modificacion_indicacion_medica)) ?
        $request->responsable_modificacion_indicacion_medica : null;

        $farmacos_modificacion_indicacion_medica = (isset($request->farmacos_modificacion_indicacion_medica)) ?
        $request->farmacos_modificacion_indicacion_medica : null;
        
        /* VALIDAR DOMINIO DE LO QUE LLEGA Y PERSISTIR */
        
        $tipo_valid = ['Control de signos vitales', 'Control de hemoglucotest','Suero','Farmacos'];
        $horario_valid = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
        $responsable_valid = ['1','2'];

        if(!isset($indicacion_id)){ throw new Exception('Id es nulo.'); }

        $indicacion_old = PlanificacionIndicacionMedica::where("id", $indicacion_id)
            ->where("visible", true)
            ->whereNull("tipo_modificacion")
            ->first();
        Log::info("indicacion_old1");
        Log::info($indicacion_old);
        
        if(!$indicacion_old){ throw new Exception('Ha ocurrido un error.'); }


        if (!in_array($tipo, $tipo_valid)) { throw new Exception('Campo tipo no valido.'); }


        /* indicacion_old */
        $indicacion_old->usuario_modifica = $auth_user_id;
        $indicacion_old->fecha_modificacion = $hoy;
        $indicacion_old->tipo_modificacion = 'Modificado';
        $indicacion_old->visible = false;
        $indicacion_old->save();
        Log::info("indicacion_old");
        Log::info($indicacion_old);
        /* indicacion_new */    
        $indicacion_new = new PlanificacionIndicacionMedica();
        $indicacion_new->caso = $indicacion_old->caso;
        $indicacion_new->id_indicacion = $indicacion_old->id_indicacion;
        $indicacion_new->tipo = $tipo; 
        $indicacion_new->id_farmaco = $farmacos_modificacion_indicacion_medica;
        //responsable
        if (!in_array($responsable_indicacion, $responsable_valid)) { throw new Exception('Campo responsable no valido'); }
        

        //horario_indicacion
        if(count($horario_indicacion) > 0){
            foreach ($horario_indicacion as $hora) {
                if (!in_array($hora, $horario_valid)) { throw new Exception('Campo horario indicación no valido.'); }
            }
        }else {
             throw new Exception('Campo horario indicación no valido.');
        }
        Log::info("array horarios");
        Log::info($horario_indicacion);
        $indicacion_new->fecha_emision = $indicacion_old->fecha_emision;
        $indicacion_new->fecha_vigencia = $indicacion_old->fecha_vigencia;
        $indicacion_new->responsable = $responsable_indicacion;
        $indicacion_new->horario = implode(",", $horario_indicacion);
        $indicacion_new->usuario = $auth_user_id;
        $indicacion_new->visible = true;
        $indicacion_new->fecha_creacion = Carbon::parse($indicacion_old->fecha_creacion);
        $indicacion_new->id_anterior = $indicacion_old->id; 
        $indicacion_new->save();
        Log::info("indicacion_new");
        Log::info($indicacion_new);

        $indicacion_hoja_old = HojaEnfermeriaEnfermeriaIndicacionMedica::
        where("id", $indicacion_old->id)
        ->where("visible", true)
        ->get();
        
        Log::info("indicacion_hoja_old");
        Log::info($indicacion_hoja_old);
        
        if(!$indicacion_hoja_old){ throw new Exception('Ha ocurrido un error.'); }


        $signos_vitales = HojaEnfermeriaControlSignoVital::
        where("id_indicacion", $indicacion_old->id)->get();

        if(!$signos_vitales){ throw new Exception('Ha ocurrido un error.'); }


        HojaEnfermeriaEnfermeriaIndicacionMedica::
            where("id_indicacion", $indicacion_old->id)
            ->update([
                'id_indicacion' => $indicacion_new->id
            ]);

        HojaEnfermeriaControlSignoVital::
            where("id_indicacion", $indicacion_old->id)
            ->update([
                'id_indicacion' => $indicacion_new->id
            ]);
        
        

    }


    
    public function deleteDatosIndicacionMedica($request){

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $id_indicacion = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        if(!isset($id_indicacion)){ throw new Exception('Id es nulo.'); }

        $indicacion = PlanificacionIndicacionMedica::
        where("id", $id_indicacion)->
        where("visible", true)->
        first();

        if($indicacion){
          
            $modificacion = '';
            if($request->tipo_modificacion == 2){
                $modificacion = 'Terminado';
            }elseif($request->tipo_modificacion == 3){
                $modificacion = 'Eliminado';
            }

            $indicacion->usuario_modifica = $auth_user_id;
            $indicacion->fecha_modificacion = $hoy;
            $indicacion->tipo_modificacion = $modificacion;
            $indicacion->visible = false;
            $indicacion->save();
        }
        else {
            throw new Exception('Ha ocurrido un error.');
        }

    }


    public function deleteDataHoraIndicacionMedica($request){

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");


        /* CAPTURAR LO QUE LLEGA */
        $id_indicacion = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        if(!isset($id_indicacion)){ throw new Exception('Id es nulo.'); }

        $indicacion = PlanificacionIndicacionMedica::where("id", $id_indicacion)
            ->where("visible", true)
            ->whereNull("tipo_modificacion")
            ->first();

        if($indicacion){

            $horas = explode(',',$indicacion->horario);
            if (in_array($request->hora, $horas)) {
                $posicion = array_search($request->hora, $horas);
                unset($horas[$posicion]);
        
                $tipo_modificacion = '';
                if($request->tipo_modificacion == 2){
                    $tipo_modificacion = 'Terminado';
                }elseif($request->tipo_modificacion == 3){
                    $tipo_modificacion = 'Eliminado';
                }
                /* indicacion_old */
                $indicacion->usuario_modifica = $auth_user_id;
                $indicacion->fecha_modificacion = $hoy;
                $indicacion->tipo_modificacion = $tipo_modificacion;
                $indicacion->horario = $request->hora;
                //if($request->tipo_modificacion == 3){
                    $indicacion->visible = false;
                //}
                $indicacion->save();
                
                if(!empty($horas)){
                    /* indicacion_new */    
                    Log::info("horas");
                    Log::info($horas);
                    $indicacion_new = new PlanificacionIndicacionMedica();
                    $indicacion_new->caso = $indicacion->caso;
                    $indicacion_new->id_indicacion = $indicacion->id_indicacion; 
                    $indicacion_new->tipo = $indicacion->tipo; 
                    $indicacion_new->fecha_emision = $indicacion->fecha_emision;
                    $indicacion_new->fecha_vigencia = $indicacion->fecha_vigencia;
                    $indicacion_new->responsable = $indicacion->responsable;
                    $indicacion_new->horario = implode(",", $horas);
                    $indicacion_new->usuario = $auth_user_id;
                    $indicacion_new->visible = true;
                    $indicacion_new->fecha_creacion = Carbon::parse($indicacion->fecha_creacion);
                    $indicacion_new->id_anterior = $indicacion->id; 
                    if($indicacion->id_farmaco != null){
                        $indicacion_new->id_farmaco = $indicacion->id_farmaco; 
                    }
                    $indicacion_new->save();
                    Log::info("indicacion_new TEMIRANDO ELIMNADO");
                    Log::info($indicacion_new);      

                    $indicacion_hoja_old = HojaEnfermeriaEnfermeriaIndicacionMedica::where("id", $indicacion->id)
                        ->where("visible", true)
                        ->get();
            
                    if(!$indicacion_hoja_old){ throw new Exception('Ha ocurrido un error.'); }
            
            
                    $signos_vitales = HojaEnfermeriaControlSignoVital::
                    where("id_indicacion", $indicacion->id)->get();
            
                    if(!$signos_vitales){ throw new Exception('Ha ocurrido un error.'); }
            
            
                    HojaEnfermeriaEnfermeriaIndicacionMedica::
                        where("id_indicacion", $indicacion->id)
                        ->update([
                            'id_indicacion' => $indicacion_new->id
                        ]);
            
                    HojaEnfermeriaControlSignoVital::
                        where("id_indicacion", $indicacion->id)
                        ->update([
                            'id_indicacion' => $indicacion_new->id
                        ]);
                    
                    return (object) array (
                        "nueva_id" =>  $indicacion_new->id
                    );
                    
                }else{
                    return false;
                    // return (object) array (
                    //     "nueva_id" => '-1'
                    // );
                }
                
            }else{
                return false;
                // return (object) array (
                //     "nueva_id" => '-1'
                // );
            } 
        }else{
            return false;
        }
    }


  
}