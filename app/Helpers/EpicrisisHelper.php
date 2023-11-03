<?php

namespace App\Helpers;
use App\Models\EpicrisisCuidado;
use App\Models\EpicrisisControlMedico;
use App\Models\EpicrisisInterconsulta;
use App\Models\EpicrisisExamenPendiente;
use App\Models\EpicrisisMedicamentoAlta;
use App\Models\EpicrisisEducacionRealizada;
use App\Models\EpicrisisOtros;

use Log;
use Exception;
use DB;
use Auth;
use Crypt;
use Session;
use Carbon\Carbon;


class EpicrisisHelper extends Helper {

    public function validar_formulario($tipo_tabla){
        $tablaArray = ["1" =>'tipo_cuidado_alta','tipo_controles_medicos','tipo_interconsulta','tipo_examenes_pendientes','tipo_medicamentos_alta','tipo_educaciones_realizadas','tipo_otros'];
        $existe = in_array($tipo_tabla, $tablaArray);
        if(!$existe){
            return false;
        }
    }
  
    public function obtener_datos_formulario($tipo_formulario){
        $fecha = '';
        $fecha_solicitada = '';
        $formulario = '';
        $modificar = '';
        $eliminar = '';
        $modal = '';
        
        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();

        switch ($tipo_formulario) {
            case 'tipo_cuidado_alta':
                $formulario = 'formulario_epicrisis_cuidados';
                $modificar = "obtenerCuidados";
                $eliminar = "eliminarCuidado";
                $fecha = "and f.fecha_creacion > '$inicio' and f.fecha_creacion < '$fin'";
                $modal = 'cuidadoAlAlta';
                $mensaje = "el cuidado de alta";
                break;
            case 'tipo_controles_medicos':
                $formulario = 'formulario_epicrisis_controles_medicos';
                $modificar = "obtenerControlMedico";
                $eliminar = "eliminarControlMedico";
                $fecha_solicitada = 'f.fecha_solicitada,';
                $fecha = "and f.fecha_solicitada IS NOT NULL";
                $modal = 'modalControlMedico';
                $mensaje = "el control medico";
            break;
            case 'tipo_interconsulta':
                $formulario = 'formulario_epicrisis_interconsultas';
                $modificar = "obtenerInterconsulta";
                $eliminar = "eliminarInterconsulta";
                $fecha_solicitada = 'f.fecha_solicitada,';
                $fecha = "and f.fecha_solicitada IS NOT NULL";
                $modal = 'modalInterconsulta';
                $mensaje = "la interconsulta";
            break;
            case 'tipo_examenes_pendientes':
                $formulario = 'formulario_epicrisis_examenes_pendientes';
                $modificar = "obtenerExamenesPendientes";
                $eliminar = "eliminarExamanesPendientes";
                $fecha_solicitada = 'f.fecha_solicitada,';
                $fecha = "and f.fecha_solicitada IS NOT NULL";
                $modal = 'modalExamenesPendientes';
                $mensaje = "el examen pendiete";
            break;
            case 'tipo_medicamentos_alta':
                $formulario = 'formulario_epicrisis_medicamentos_alta';
                $modificar = "obtenerMedicamentosAlta";
                $eliminar = "eliminarMedicamentoAlta";
                $modal = 'modalMedicamentoAlAlta';
                $mensaje = "el medicamento";
                break;
            case 'tipo_educaciones_realizadas':
                $formulario = 'formulario_epicrisis_educaciones_realizadas';
                $modificar = "obtenerEducacionesRealizadas";
                $eliminar = "eliminarEducacionesRealizadas";
                $modal = 'modalEducacionesRealizadas';
                $mensaje = "la educacion realizada";
                break;
            case 'tipo_otros':
                $formulario = 'formulario_epicrisis_otros';
                $modificar = "obtenerOtros";
                $eliminar = "eliminarOtros";
                $modal = 'modalOtros';
                $mensaje = "el tipo";
                break;
            default:
                break;
        }
        
        return (object) array(
            'formulario' =>$formulario,
            'tipo_formulario'=>$tipo_formulario,
            'boton_modificar'=>$modificar,
            'boton_eliminar'=>$eliminar,
            'fecha_query'=>$fecha,
            'fecha_solicitada_query'=>$fecha_solicitada,
            'modal'=>$modal,
            'mensaje'=>$mensaje
        );
        
    }

    public function CuidadoAlta($tipo_formulario,$idCuidado){
       $obtener_datos = $this->obtener_datos_formulario($tipo_formulario);

       $datos =  DB::select(DB::raw("select
				f.*,
				a.tipo
				from ".$obtener_datos->formulario. " as f
				inner join ".$tipo_formulario." as a on f.id_cuidado = a.id 
                where f.id = ".$idCuidado." and f.visible = true"));

        return (object) array(
            'informacion' =>$datos[0],
            'modal'=>$obtener_datos->modal,
        );
    }


    public function updateFormulario($request){   
        

        $auth_user_id = Auth::user()->id;
        $hoy = Carbon::now()->format("Y-m-d H:i:s");

        /* CAPTURAR LO QUE LLEGA */
        $tipo_formulario = (isset($request->nombreForm) && trim($request->nombreForm) !== "") ?
        trim(strip_tags($request->nombreForm)) : null;

        $obtener_datos = $this->obtener_datos_formulario($tipo_formulario);


        $cuidado_id = (isset($request->id_cuidado_actualizar) && trim($request->id_cuidado_actualizar) !== "") ?
        trim(strip_tags($request->id_cuidado_actualizar)) : null;

        $id_cuidado_tipo = (isset($request->cuidado_modificacion_item) && trim($request->cuidado_modificacion_item) !== "" ) ?
        trim(strip_tags($request->cuidado_modificacion_item)) : null;


        /* VALIDAR DOMINIO DE LO QUE LLEGA Y PERSISTIR */
        if(!isset($cuidado_id)){ throw new Exception('Id es nulo.'); }

        $cuidados_old =   DB::table($obtener_datos->formulario)
        ->where("id", $cuidado_id)
        ->where("visible", true)
        ->first();
        
        if(!$cuidados_old){ throw new Exception('Ha ocurrido un error.'); }

        /* cuidado_old */
        DB::table($obtener_datos->formulario)
        ->where("id", $cuidado_id)
        ->where("visible", true)
        ->update(
            [
            'usuario_modifica' => $auth_user_id,
            'fecha_modificacion' => $hoy,
            'tipo_modificacion' => 'Editado',
            'visible' => false
            ]
        );


   
        /* cuidado_new */  
        if($tipo_formulario == 'tipo_controles_medicos' || $tipo_formulario == 'tipo_interconsulta' || $tipo_formulario == 'tipo_examenes_pendientes'){
            DB::table($obtener_datos->formulario)->insert(
                [
                'caso' =>   $cuidados_old->caso,
                'usuario' =>   $auth_user_id,
                'fecha_creacion' => Carbon::parse($cuidados_old->fecha_creacion),
                'fecha_solicitada' => $request->fecha_modificacion,
                'id_anterior' => $cuidados_old->id,
                'id_cuidado' => $id_cuidado_tipo,
                'visible' => true
                ]
            );
        }else{
            DB::table($obtener_datos->formulario)->insert(
                [
                'caso' =>   $cuidados_old->caso,
                'usuario' =>   $auth_user_id,
                'fecha_creacion' => Carbon::parse($cuidados_old->fecha_creacion),
                'id_anterior' => $cuidados_old->id,
                'id_cuidado' => $id_cuidado_tipo,
                'visible' => true
                ]
            );
        } 
    

    }

}