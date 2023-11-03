<?php

namespace App\Helpers;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

use App\Models\PlanificacionCuidadoIndicacionMedica;
use App\Models\HojaEnfermeriaInterconsulta;


class InterConsultaHelper {


    public function modificarInterconsulta($request){

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $id_interconsulta = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        $estado = (isset($request->estado) && trim($request->estado) !== "") ?
        trim(strip_tags($request->estado)) : null;

        if($estado !== "Realizada"){ throw new Exception('Estado no es Realizada.'); }

        $interconsulta_old = PlanificacionCuidadoIndicacionMedica::
        where("id", $id_interconsulta)->
        where("visible", true)->
        first();

        if($interconsulta_old){

            $interconsulta_old->usuario_modifica = $auth_user_id;
            $interconsulta_old->fecha_modificacion = $hoy;
            $interconsulta_old->tipo_modificacion = 'Editado';
            $interconsulta_old->visible = false;
            $interconsulta_old->save();
    
            /*registro nuevo */
            $interconsulta_new = new PlanificacionCuidadoIndicacionMedica();
            $interconsulta_new->id_anterior = $interconsulta_old->id;
            $interconsulta_new->caso = $interconsulta_old->caso;
            $interconsulta_new->usuario = $auth_user_id;
            $interconsulta_new->fecha_creacion = $hoy;
            $interconsulta_new->visible = true;
            $interconsulta_new->tipo_interconsulta = $interconsulta_old->tipo_interconsulta;
            $interconsulta_new->tipo = 'Interconsulta';
            $interconsulta_new->estado_interconsulta = 'Realizada';
            $interconsulta_new->save();
        }
        else {
            throw new Exception('Ha ocurrido un error.');
        }


    }


    public function eliminarInterconsulta($request){

        $user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $id_interconsulta = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        if(!isset($id_interconsulta)){ throw new Exception('Id es nulo.'); }

        $interconsulta = PlanificacionCuidadoIndicacionMedica::
        where("id", $id_interconsulta)->
        where("estado_interconsulta", "Pendiente")->
        where("visible", true)->
        first();

        if($interconsulta){
            $interconsulta->usuario_modifica = $user_id;
            $interconsulta->fecha_modificacion = $hoy;
            $interconsulta->tipo_modificacion = 'Eliminado';
            $interconsulta->visible = false;
            $interconsulta->save();
        }
        else {
            throw new Exception('Ha ocurrido un error.');
        }

    }

    public function finalizarInterconsulta($request){
        
        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $id_interconsulta = (isset($request->id) && trim($request->id) !== "") ?
        trim(strip_tags($request->id)) : null;

        $interconsulta_old = PlanificacionCuidadoIndicacionMedica::where("id", $id_interconsulta)
            ->where("visible", true)
            ->first();

        if($interconsulta_old->estado_interconsulta != "Pendiente"){ throw new Exception('Estado debe ser Pendiente.'); }

        if($interconsulta_old){
            $interconsulta_old->usuario_modifica = $auth_user_id;
            $interconsulta_old->fecha_modificacion = $hoy;
            $interconsulta_old->estado_interconsulta = 'Realizada';
            $interconsulta_old->save();

        }else {
            throw new Exception('Ha ocurrido un error.');
        }


    }

    public function obtenerInterconsultas($id_caso){

        $hoy = Carbon::now()->startOfDay();
        $mañana = Carbon::now()->endOfDay();

        /* CAPTURAR LO QUE LLEGA */
        $id_caso = (isset($id_caso) && trim($id_caso) !== "") ?
        trim(strip_tags($id_caso)) : null;

        if (!isset($id_caso)) { throw new Exception('Caso es nulo.'); }

        $resultado =  [];
        /* falta arreglar esto 
        $hoy = Carbon::createFromTime(8, 0, 0);  
        $mañana = Carbon::createFromTime(7, 59, 59)->addDays(1);
        */

        $sql = "select f.id as id, f.tipo_interconsulta as tipo, f.estado_interconsulta as estado,
        f.fecha_creacion as fecha_creacion, u.nombres as nombres, u.apellido_paterno as apellido_paterno, 
        u.apellido_materno as apellido_materno 
        from formulario_planificacion_cuidados_indicaciones_medicas as f 
        inner join usuarios as u on u.id = f.usuario 
        where f.caso = ? and f.tipo	= 'Interconsulta' and f.visible = true and 
        ( 
            (f.estado_interconsulta = 'Pendiente' and f.fecha_creacion < '$mañana') or 
            (f.estado_interconsulta = 'Realizada' and  f.fecha_creacion > '$hoy' and f.fecha_creacion < '$mañana') 
        )
        
        ";

        $interconsultas = DB::select($sql, [$id_caso]);

        
        foreach ($interconsultas as $key => $inter) {
            $opciones = "<div class='row'>
            <div class='col-md-5'> 
                <button type='button' class='btn-xs btn-danger' onclick='finalizarInterconsulta(".$inter->id.")'>Finalizar</button>
            </div>
            </div>";

            $fecha_creacion = "";
            if(isset($inter->fecha_creacion) && trim($inter->fecha_creacion) !== ""){
                $fecha_creacion = Carbon::parse($inter->fecha_creacion)->format("d-m-Y H:i");
            }

            // $htmlTipo = "<div class='col-md-12'>";
            // $end = "</div>";
            // $htmlTipo .= Form::text("tipoEditado", $inter->tipo, array( "class" => "form-control col-md-6", "id" => "tipoEditado".$key)).$end;
            // $htmlEstado = "<select name='estadoInterconsulta' class='form-control' onChange='modificarInterconsulta(".$inter->id.",".$key.")' id='estadoI".$key."'>";
            
            $htmlEstado = "";
            if($inter->estado == 'Pendiente'){
                $htmlEstado = "<div style='text-align:center'><h4><span class='label label-warning'>Pendiente</span></h4></div>";
            }else if($inter->estado == 'Realizada'){
                $htmlEstado = "<div style='text-align:center'><h4><span class='label label-success'>Realizada</span></h4></div>";
                $opciones = "";
                /* $opciones = "<div class='row'>
                <div class='col-md-5'> 
                    <button type='button' class='btn-xs btn-danger' disabled>Eliminar</button>
                </div>
                </div>"; */
            }
            // $htmlEstado.="</select>";
            

            $resultado [] = [
                "<b>".$inter->tipo."</b> <br> Creado el: ".Carbon::parse($fecha_creacion)->format("d-m-Y H:i"),
                //$fecha_creacion,
                // $inter->tipo,
                // $htmlTipo,
                $htmlEstado,
                $inter->nombres. " " .$inter->apellido_paterno. " " .$inter->apellido_materno,
                // "<div class='row'>
                // <div class='col-md-5'> 
                //     <button type='button' class='btn-xs btn-warning' onclick='modificarInterconsulta(".$inter->id.",".$key.")'>Modificar</button>
                // </div>
                // <div class='col-md-5'> 
                //     <button type='button' class='btn-xs btn-danger' onclick='eliminarInterconsulta(".$inter->id.")'>Eliminar</button>
                // </div>
                // </div>"
                $opciones
            ];
        }

        return $resultado;

    }


}