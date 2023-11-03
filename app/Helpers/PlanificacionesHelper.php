<?php

namespace App\Helpers;
use App\Models\HojaEnfermeriaCuidadoEnfermeriaAtencion;
use App\Models\PlanificacionCuidadoAtencionEnfermeria;
use App\Helpers\SignosVitalesHelper;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;


class PlanificacionesHelper {

    function obtenerPlanificacionesVigentes($caso_id){

        /* $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay(); */

        $query = "select
        f.id,
        u.nombres,
        u.apellido_paterno,
        u.apellido_materno,
        f.fecha_creacion,
        f.horario,
        t.tipo,
        f.tipo as tipo_id,
        f.resp_atencion
        from formulario_planificacion_cuidados_atencion_enfermeria as f
        inner join usuarios as u on u.id = f.usuario
        left join tipo_cuidado as t  on t.id = f.tipo
        where
        f.caso = ? and f.visible = true and f.fecha_modificacion is null and f.horario is not null
        order by f.horario asc
        ";

        $atencionesEnf = DB::select($query, [$caso_id]);

        $tipo = [];
        /* separar */
        foreach ($atencionesEnf as $key => $atencion) {
            
            if($atencion->resp_atencion == 1){
                $color = "colorEnfermera";
            }else if($atencion->resp_atencion == 2){
                $color = "colorTens";
			}else if($atencion->resp_atencion == 3){
                $color = "colorMatrona";
            }else{
                $color = "colorDefault";
            }

            $signos_hora_data = "false";
            $is_not_check = "false";

            //para planificaciones de signos vitales
            if($atencion->tipo_id === 32){

                $signosH = new SignosVitalesHelper();

                //obtener signos para la hora
                $signos_hora = [];
                $hora = (string) $atencion->horario;
                $fecha = Carbon::parse($atencion->fecha_creacion)->format("Y-m-d H:i:s");
                $is_not_check = $signosH->getAtencionCheckInPlanificacion($atencion->id, $hora, $fecha);
                $is_not_check = (!$is_not_check) ? "true" : "false";

                $query = "select extract(hour from i.horario1) as hora, extract(minute from i.horario1) as minutos, i.horario1 as horario1
                from formulario_hoja_enfermeria_signos_vitales as i
                where i.caso = ? and i.visible = true and i.horario1::date = ?
                and i.id_indicacion is null
                order by i.fecha_creacion  DESC";
                $signos = DB::select($query, [$caso_id,Carbon::now()]);

                foreach ($signos as $key => $s) {
                    if($s->hora === $hora){
                        $minutos = (intval($s->minutos) < 10) ? "0".$s->minutos : $s->minutos;
                        array_push($signos_hora, $s->hora.":".$minutos);
                    }
                }

                $signos_hora_data = (count($signos_hora) > 0) ? implode(",", $signos_hora) : "false";

            }


            if(!array_key_exists($atencion->tipo,$tipo)){
                /* en caso de que no esten creados, se habilitan */
                $eliminar = 1;
                $terminar =2;
                if( (int) $atencion->horario > 8 && (int) $atencion->horario <= 20 ){
                    $tipo[$atencion->tipo]["dia"]= "<div class='$color'><div class=' valorInternoSinX'>$atencion->horario</div></div>";
                    $tipo[$atencion->tipo]["noche"]= "";
                    $tipo[$atencion->tipo]["opciones"]= "<div class=''> <button type='button' class='btn-xs btn-warning' onclick='modificarAtencionHoras($atencion->tipo_id)'>Modificar</button><br><br><button type='button' class='btn-xs btn-danger' onclick='eliminarH($atencion->tipo_id,$eliminar)'>Eliminar</button><br><br><button type='button' class='btn-xs btn-success' onclick='eliminarH($atencion->tipo_id,$terminar)'>Terminar</button></div>";
                }else{
                    $tipo[$atencion->tipo]["dia"]= "";
                    $tipo[$atencion->tipo]["noche"]= "<div class='$color'><div class=' valorInternoSinX'>$atencion->horario</div></div>";
                    $tipo[$atencion->tipo]["opciones"]= "<div class=''> <button type='button' class='btn-xs btn-warning' onclick='modificarAtencionHoras($atencion->tipo_id)'>Modificar</button><br><br><button type='button' class='btn-xs btn-danger' onclick='eliminarH($atencion->tipo_id,$eliminar)'>Eliminar</button><br><br><button type='button' class='btn-xs btn-success' onclick='eliminarH($atencion->tipo_id,$terminar)'>Terminar</button></div>";
                }
			}else{
                /* sino se siguen incorporando nuevos */
                if((int) $atencion->horario > 8 && (int) $atencion->horario <= 20){
                    $tipo[$atencion->tipo]["dia"].= "<div class='$color'><div class=' valorInternoSinX'>$atencion->horario</div></div>";
                    $tipo[$atencion->tipo]["opciones"]= "<div class=''> <button type='button' class='btn-xs btn-warning' onclick='modificarAtencionHoras($atencion->tipo_id)'>Modificar</button><br><br><button type='button' class='btn-xs btn-danger' onclick='eliminarH($atencion->tipo_id,$eliminar)'>Eliminar</button><br><br><button type='button' class='btn-xs btn-success' onclick='eliminarH($atencion->tipo_id,$terminar)'>Terminar</button></div>";
                }else{
                    $tipo[$atencion->tipo]["noche"].= "<div class='$color'><div class=' valorInternoSinX'>$atencion->horario</div></div>";
                    $tipo[$atencion->tipo]["opciones"]= "<div class=''> <button type='button' class='btn-xs btn-warning' onclick='modificarAtencionHoras($atencion->tipo_id)'>Modificar</button><br><br><button type='button' class='btn-xs btn-danger' onclick='eliminarH($atencion->tipo_id,$eliminar)'>Eliminar</button><br><br><button type='button' class='btn-xs btn-success' onclick='eliminarH($atencion->tipo_id,$terminar)'>Terminar</button></div>";
                }
            }
        }
        $resultado = [];
        /* ordenar para el datatable */
        foreach ($tipo as $key => $t) {
            $resultado [] = [
                $key,
                $t["dia"],
                $t["noche"],
                $t["opciones"]
            ];
        }

        return $resultado;

    }


    function eliminarPlanificacion($request){

        $signosH = new SignosVitalesHelper();
        $planificacion_id = strip_tags(trim($request->id));
        $mantener_check_tras_eliminar_planificacion = (strip_tags($request->mantener_check_tras_eliminar_planificacion)=== "true") ? true : false;

        //obtener planificacion
        $planificacion_hora_old = PlanificacionCuidadoAtencionEnfermeria::where("id", $planificacion_id)->first();

        if($planificacion_hora_old){

            $tipo = $planificacion_hora_old->tipo;

            //para signos vitales
            if($tipo === 32){
                $hora = $planificacion_hora_old->horario;
                $id_atencion_old = $planificacion_hora_old->id;
    
                //obtener check 
                $atencion_plan_hora_old = HojaEnfermeriaCuidadoEnfermeriaAtencion::
                where("horario", $hora)
                ->where("id_atencion", $id_atencion_old)
                ->whereDate('fecha_creacion','=', Carbon::now())
                ->first();
    
                //si existe check, entonces:
                if($atencion_plan_hora_old){
    
                    $atencion_plan = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan_hora_old->id);

                    //mantener_check_tras_eliminar_planificacion
                    if($mantener_check_tras_eliminar_planificacion){
                        $caso_id = $planificacion_hora_old->caso;  
                        
                        //obtener planificacion hora nula o crearla en caso que no exista
                        $id_atencion_null = $this->crearPlanificacionHoraNula($caso_id,$tipo);    
                        $atencion_plan->id_atencion = $id_atencion_null;
                        $atencion_plan->visible = true;
                    } else {
                        $atencion_plan->visible = false;
                    }
    
                    $atencion_plan->save();
                }
    
            }

            //borra planificacion
            $planificacion_hora_old->usuario_modifica = Auth::user()->id;
            $planificacion_hora_old->fecha_modificacion = Carbon::now();
            $planificacion_hora_old->tipo_modificacion = 'Eliminado';
            $planificacion_hora_old->visible = false;
            $planificacion_hora_old->save();


        } else {
            throw new Exception("Ha ocurrido un error");
        }

    }


    function crearPlanificacionHoraNula($id_caso,$tipo){
        //$id_atencion_nulo = null;
        
        //Se busca una planificacion de la atencion antigua
        $plan_hora_nula_old = PlanificacionCuidadoAtencionEnfermeria::
            whereNull("formulario_planificacion_cuidados_atencion_enfermeria.horario")
            ->where("formulario_planificacion_cuidados_atencion_enfermeria.caso", $id_caso)
            ->where("formulario_planificacion_cuidados_atencion_enfermeria.tipo", $tipo)
            ->whereNull("formulario_planificacion_cuidados_atencion_enfermeria.fecha_modificacion")
            ->first();

        //Si o si se creara una nueva planificacion

        //evaluar si existe planificacion con fecha de termino nula o anterior, Si existe fecha nula, no hay necesidad de crear una nnueva, de lo contrario se deb e crear una. Esto se hace para evitar que por cada modificacion se cree una nueva
        if(!$plan_hora_nula_old){
            $plan_hora_nula_new = new PlanificacionCuidadoAtencionEnfermeria;
            $plan_hora_nula_new->caso = $id_caso;
            $plan_hora_nula_new->usuario = Auth::user()->id;
            $plan_hora_nula_new->visible = true;
            $plan_hora_nula_new->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $plan_hora_nula_new->tipo = $tipo;
            $plan_hora_nula_new->save();
            $id = $plan_hora_nula_new->id;
        }else{
            $id = $plan_hora_nula_old->id;
        }
        
        return $id;
    }



    function crearPlanificacionHora($id_caso, $tipo, $hora,$responsable ){
        //$id_atencion_hora = null;

        //Se busca la panificacion del mismo tipo, pero que se encuentre abierta
        $plan_hora_old = PlanificacionCuidadoAtencionEnfermeria::
        where("formulario_planificacion_cuidados_atencion_enfermeria.horario", $hora)
        ->where("formulario_planificacion_cuidados_atencion_enfermeria.caso", $id_caso)
        ->where("formulario_planificacion_cuidados_atencion_enfermeria.tipo", $tipo)
        ->whereNull("formulario_planificacion_cuidados_atencion_enfermeria.fecha_modificacion")
        ->first();
        
        $plan_hora_new = new PlanificacionCuidadoAtencionEnfermeria;

        //si existe planificación para la hora que se ingresa se obtiene y se modifica, porque ya no sera utilizada, sino que sera reemplazada
        if($plan_hora_old){
            $update = PlanificacionCuidadoAtencionEnfermeria::find($plan_hora_old->id);
            $update->visible = true;
            $update->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $update->tipo_modificacion = "Terminado";
            $update->save();
            $plan_hora_new->id_anterior = $update->id;
        }

        //siempre se creara una nueva planificacion de la hora
        $plan_hora_new->caso = $id_caso;
        $plan_hora_new->usuario = Auth::user()->id;
        $plan_hora_new->visible = true;
        $plan_hora_new->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
        $plan_hora_new->tipo = $tipo;
        $plan_hora_new->horario = $hora;
        $plan_hora_new->resp_atencion = $responsable;
        $plan_hora_new->save();

        return $plan_hora_new->id;
    }


    function actualizarAtencionPlanificacionHoraNulaConAtencionPlanificacionHora($id_atencion_nulo, $id_atencion_hora, $hora){

        /*
            traer check de la $hr que apunte a planificacion horario nulo y actualizarlos a la nueva planificacion
            recien creada
        */
        //Se usa el inicio y fin debido a que puede que tenga horarios creados antes que la planificacion nueva , lo que puede provocar que esos anteriores se asocien a la planificacion actual.
        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();

        //Lo que hace esto es buscar las atenciones marcadas como realizadas (con check) y que estan asociadas al check de nulls , buscarla y reasociarlas a su verdadera planificacion. Ejemplo: cree un control de signos vitales alas 19 y no tenia la planificacion hecha. Si creo la planificacion, buscare la atencion realizada y marcada anteriormente, para asi poder asignarle el id_atencion correrectamente a su planificacion.
        $atencion_plan_hora_nula = HojaEnfermeriaCuidadoEnfermeriaAtencion::
            select(
                "id_atencion as id_atencion",
                "id as id"
            )
            ->where("id_atencion", $id_atencion_nulo)
            ->where("horario", $hora)
            ->orderBy("id","desc")
            ->whereBetween('fecha_creacion', [$inicio, $fin])
            ->first();

        if($atencion_plan_hora_nula){
            //Si encuentr alguna atencion realizada a esa hora, se le actualiza su id de atencion
            $update = HojaEnfermeriaCuidadoEnfermeriaAtencion::find($atencion_plan_hora_nula->id);
            $update->id_atencion = $id_atencion_hora;
            $update->save();
        }

    }

}
