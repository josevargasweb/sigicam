<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use Auth;
use TipoUsuario;
use DB;
use Log;
use View;
use Carbon\Carbon;
use Exception;
use App\Helpers\IndicacionMedicaHelper;
use App\Helpers\PlanificacionesHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\PlanificacionCuidados;//eliminar proxiumanete
use App\Models\PlanificacionCuidadoCuracion;
use App\Models\PlanificacionCuidadoProteccion;
use App\Models\PlanificacionCuidadoNovedad;
use App\Models\PlanificacionCuidadoAtencionEnfermeria;
use App\Models\PlanificacionCuidadoIndicacionMedica;
use App\Models\Usuario;
use App\Models\Indicacion;
use App\Models\HojaEnfermeriaCuidadoEnfermeriaAtencion;
use App\Models\HojaCuracionesSimple;
use App\Models\TipoCuidado;
use App\Models\IndicacionMedica;
use App\Models\ArsenalFarmacia;

use App\Models\PlanificacionIndicacionMedica;



class EnfermeriaPlanificacionCuidadosController extends Controller
{

    public static function datosPlanificacion(Request $request)
    {
        $response = [];

        $planificaionCuidados = PlanificacionCuidados::where("id_formulario_planificacion_cuidados",$request->idPlanificacionCuidados)->first();

        $response = [
            "PlanificacionCuidados" => $planificaionCuidados,
        ];


        return response()->json($response);
    }

    public static function histPlanificacionCuidados($caso){

        $paciente = DB::table("pacientes as p")
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno")
                    ->join("casos as c", "p.id", "=", "c.paciente")
                    ->where("c.id", $caso)
                    ->first();

        $info = DB::table("formulario_planificacion_cuidados as f")
                    ->where("f.caso",$caso)
                    ->orderBy("f.fecha_creacion", "asc")
                    ->get();

        return View::make("Gestion/gestionEnfermeria/historialPlanificacionCuidados")
            ->with(array(
                "caso" => $caso,
                "info" => $info,
                "paciente" => $paciente->nombre." ".$paciente->apellido_paterno." ".$paciente->apellido_materno
            ));
    }

    public static function buscarHistorialPlanificacion(Request $request){

        $planificaciones = PlanificacionCuidados::where("caso",$request->idCaso)->get();

        foreach($planificaciones as $planificacion){

            $usuario = Usuario::where("id", $planificacion->usuario_responsable)->first();
            $response [] = [
                "<b>Usuario responsable: </b>".$usuario->nombres." ".$usuario->apellido_paterno." ".$usuario->apellido_materno."<br> <b>Fecha de creación: </b>".Carbon::parse($planificacion->fecha_creacion)->format("d-m-Y H:m:i"),
                "<button class='btn btn-primary' type='button' onclick='editar(".$planificacion->id_formulario_planificacion_cuidados.")'>Ver/Editar</button>",
                $planificacion->novedades,
                "<b>Nombre Responsable: </b> ".$planificacion->proteccion_nobre."<br> <b>Próximo cambio: </b>".Carbon::parse($planificacion->proteccion_fecha)->format("d-m-Y H:m"),
                "<b>Nombre Responsable: </b> ".$planificacion->curacion_nobre."<br> <b>Próximo cambio: </b>".Carbon::parse($planificacion->curacion_fecha)->format("d-m-Y H:m"),

            ];

        }
        /* $response [] = ["a","b","b","b","b","b","b","b","b","b","b","b"]; */
        return response()->json($response);
    }


    /* Comienzo de Planificacion cuidados */



    public function eliminarTerminarPCIndicacion(Request $request){
        try{
            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $dao->deleteIndicacionMedica($request);

            DB::commit();
            $texto = '';
            if($request->tipo_modificacion == 2){
                $texto = 'terminado la';
            }elseif($request->tipo_modificacion == 3){
                $texto = 'eliminado';
            }

            return response()->json(["exito" => "Se ha ".$texto." indicación planificada"],200);

        }catch(Exception $e){
            DB::rollback();
            $errores_controlados = [
                'Id es nulo.'
            ];
            $error = "Error al eliminar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }

    public function eliminarTerminarPCIndicacionHora(Request $request){
        try{
            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $eliminar = $dao->deleteHoraIndicacionMedica($request);
            DB::commit();

            if($eliminar == false){
                $texto = '';
                if($request->tipo_modificacion == 2){
                    $texto = 'terminar';
                }elseif($request->tipo_modificacion == 3){
                    $texto = 'eliminar';
                }

                return response()->json(["error" => "La hora que desea ".$texto." no existe"]);
            }else{
                return response()->json(["exito" => "Se ha eliminado indicación planificada","nueva_id"=>$eliminar->nueva_id],200);
            }

        }catch(Exception $e){
            DB::rollback();
            $errores_controlados = [
                'Id es nulo.'
            ];
            $error = "Error al eliminar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }

    public function addIndicacionMedica(Request $request){
        /* comprobar que no exista repetido el horario que se desea agregar */
        try{

            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $dao->addIndicacionMedica($request);

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado una indicación medica exitosamente"],200);

        }catch(Exception $e){
            Log::error($e);
            DB::rollback();
            $errores_controlados = [
                'Campo tipo indicación no valido.', 'Campo via no valido.', 'Campo horario medicamento no valido.',
                'fecha emision medicamento debe ser menor a vigencia.', 'Campo dosis no debe tener mas de 50 caracteres.',
                'Campo medicamento no debe tener mas de 50 caracteres.', 'Campo indicación no debe tener mas de 500 caracteres.',
                'Campo tipo no debe tener mas de 100 caracteres.','Campo fecha no debe tener ser vacío.'
            ];
            $error = "Error al ingresar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }

    }

    public function modificarPCIndicacion(Request $request){
        try{
            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $data = $dao->updateIndicacionMedica($request);

            DB::commit();
            return response()->json(["exito" => "Se ha modificado una planificación de indicación medica"],200);

        }catch(Exception $e){
            DB::rollback();
            $errores_controlados = [
                'Campo tipo indicación no valido.', 'Campo via no valido.', 'Campo horario medicamento no valido.',
                'fecha emision medicamento debe ser menor a vigencia.',"Campo fecha no debe tener ser vacío."
            ];
            $error = "Error al modificar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }

    }


    public function obtenerPlanificacionIndicacionesMedicas($caso){
        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now();

        $indicaciones = DB::select("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.fecha_creacion,
                f.created_at,
                f.indicacion,
                f.medicamento,
                f.dosis,
                f.via,
                f.horario,
                f.fecha_vigencia,
                f.fecha_emision,
                f.tipo_interconsulta,
                f.estado_interconsulta,
                f.tipo,
                f.responsable
                from formulario_planificacion_cuidados_indicaciones_medicas as f
                inner join usuarios as u on u.id = f.usuario
                where
                f.caso = ? and f.visible = true and f.tipo_modificacion is null and
                (
                    (
                     f.estado_interconsulta = 'Pendiente'
                    )
                    or
                    (
                      f.fecha_emision is null and f.fecha_vigencia is not null and f.fecha_vigencia >= '$inicio' and f.fecha_vigencia >= '$inicio'
                    )
                    or
                    (
                      f.fecha_emision is null and f.fecha_vigencia is not null and f.fecha_vigencia <= '$inicio' and f.fecha_vigencia >= '$fin'
                    )
                    or
                    (
                        f.fecha_emision is null and f.fecha_vigencia is not null and  f.fecha_vigencia >= '$inicio' and f.fecha_vigencia <= '$fin'
                    )
                    or
                    (
                        f.fecha_emision is null and f.fecha_vigencia is not null and  f.fecha_vigencia >= '$inicio' and f.fecha_vigencia >= '$fin'
                    )
                    or
                    (
                      f.fecha_emision is not null and f.fecha_vigencia is not null and f.fecha_emision >= '$inicio' and f.fecha_vigencia >= '$inicio'
                    )
                    or
                    (
                      f.fecha_emision is not null and f.fecha_vigencia is not null and f.fecha_emision <= '$inicio' and f.fecha_vigencia >= '$fin'
                    )
                    or
                    (
                        f.fecha_emision is not null and f.fecha_vigencia is not null and  f.fecha_emision >= '$inicio' and f.fecha_vigencia <= '$fin'
                    )
                    or
                    (
                        f.fecha_emision is not null and f.fecha_vigencia is not null and  f.fecha_emision >= '$inicio' and f.fecha_vigencia >= '$fin'
                    )
                )        
              
                ",[$caso]);

        $resultado = [];
        $color = "";
        foreach ($indicaciones as $key => $indicacion) {


            //Columna indicación
            $columna_indicacion = "Sin indicación";
            $medicamento_desc = (isset($indicacion->medicamento) && trim($indicacion->medicamento) !=="") ? "<b>".ucwords($indicacion->medicamento)."</b>" : "";
            $medicamento_desc = ($medicamento_desc) ? wordwrap($medicamento_desc, 40, "<br />\n",true) : "";
            $indicacion_desc = (isset($indicacion->indicacion) && trim($indicacion->indicacion) !=="") ? "<b>".ucwords($indicacion->indicacion)."</b>" : "";
            $indicacion_desc = ($indicacion_desc) ? wordwrap($indicacion_desc, 40, "<br />\n",true) : "";
            $tipo_inter_desc = (isset($indicacion->tipo_interconsulta) && trim($indicacion->tipo_interconsulta) !=="") ? "<b>".ucwords($indicacion->tipo_interconsulta)."</b>" : "";
            $tipo_inter_desc = ($tipo_inter_desc) ? wordwrap($tipo_inter_desc, 40, "<br />\n",true) : "";
            $fecha_agregado_desc = "<br> Agregado el: ".Carbon::parse($indicacion->created_at)->format("d-m-Y H:i");
            $fecha_vigencia_indicacion = "<br> Fecha a realizar: ".Carbon::parse($indicacion->fecha_vigencia)->format("d-m-Y H:i");
            $tipo =  (isset($indicacion->tipo) && trim($indicacion->tipo) !=="") ? "<br> ".$indicacion->tipo : "";

            //Columna prescripcion
            $columna_prescripcion = "Sin prescripción";
            $dosis = (isset($indicacion->dosis) && trim($indicacion->dosis) !=="") ? "Dosis: ".$indicacion->dosis : "";
            $dosis = ($dosis) ? wordwrap($dosis, 40, "<br />\n",true) : "";
            $via = (isset($indicacion->via) && trim($indicacion->via) !=="") ? "<br> Vía: ".$indicacion->via : "";
            $fecha_emision = (isset($indicacion->fecha_emision) && trim($indicacion->fecha_emision) !=="") ? "<br> Fecha emision: ".Carbon::parse($indicacion->fecha_emision)->format("d-m-Y H:i") : "";
            $fecha_vigencia = (isset($indicacion->fecha_vigencia) && trim($indicacion->fecha_vigencia) !=="") ? "<br> Fecha vigencia: ".Carbon::parse($indicacion->fecha_vigencia)->format("d-m-Y H:i") : "";

            //Columna horario
            $columna_horario_dia = "Sin horario";
            $columna_horario_noche = "Sin horario";
            $horarios = (isset($indicacion->horario) && trim($indicacion->horario) !== "") ? $indicacion->horario : "";
            
            if($indicacion->responsable == 1){
                $color = "colorEnfermera";
            }else if($indicacion->responsable == 2){
                $color = "colorTens";
            }else{
                $color = "colorDefault";
            }
            $turno_dia = "";
            $turno_noche = "";

            if($horarios){
                $array_horarios = explode(',', $horarios);

                $dia = ['08','09','10','11','12','13','14','15','16','17','18','19','20'];
                $noche = ['21','22','23','00','01','02','03','04','05','06','07'];

                foreach ($array_horarios as $hora){
                    if(in_array($hora,$dia)){
                        $turno_dia .= "<div class='$color'><div class=''></div><div class='valorInternoSinX'>$hora</div></div>";
                    }else if(in_array($hora,$noche)){
                        $turno_noche .= "<div class='$color'><div class=''></div><div class='valorInternoSinX'>$hora</div></div>";
                    }
                }
            }

            //Columna opciones
            $columna_botonera = "";
            $btn_modificar = "<div class='col-md-5'> <button type='button' class='btn-xs btn-warning' onclick='obtenerIndicacion(".$indicacion->id.",1)'>Modificar</button>";
            if($indicacion->tipo === "Interconsulta"){
                $btn_eliminar = "<button type='button' class='btn-xs btn-danger' onclick='eliminarFilaIndicacion(".$indicacion->id.",3)'>Eliminar</button></div>";
            }else{
                $btn_eliminar = "<button type='button' class='btn-xs btn-danger' onclick='obtenerIndicacionEliminarTerminar(".$indicacion->id.",3)'>Eliminar</button>";
                $btn_terminar = "<button type='button' class='btn-xs btn-success' onclick='obtenerIndicacionEliminarTerminar(".$indicacion->id.",2)'>Terminar</button></div>";
            }

            //Columna usuario
            $nombres = (isset($indicacion->nombres) && trim($indicacion->nombres) !=="") ? $indicacion->nombres : "";
            $apellido_paterno = (isset($indicacion->apellido_paterno) && trim($indicacion->apellido_paterno) !=="") ? $indicacion->apellido_paterno : "";
            $apellido_materno = (isset($indicacion->apellido_materno) && trim($indicacion->apellido_materno) !=="") ? $indicacion->apellido_materno : "";
            $nombre_completo_usuario = "<span>".$nombres." ".$apellido_paterno." ".$apellido_materno."</span>";


            if($indicacion->tipo === "Medicamento"){
                $columna_indicacion = $medicamento_desc.$fecha_agregado_desc.$tipo. "<br> Usuario: ".$nombre_completo_usuario;
                $columna_prescripcion = $dosis.$via.$fecha_emision.$fecha_vigencia;
                $columna_horario_dia = $turno_dia;
                $columna_horario_noche = $turno_noche;
                $columna_botonera = $btn_modificar."<br><br>".$btn_eliminar."<br><br>".$btn_terminar;
            }else if($indicacion->tipo === "Indicación"){
                $columna_indicacion = $indicacion_desc.$fecha_agregado_desc.$fecha_vigencia_indicacion.$tipo. "<br> Usuario: ".$nombre_completo_usuario;
                $columna_horario_dia = $turno_dia;
                $columna_horario_noche = $turno_noche;
                $columna_botonera = $btn_modificar."<br><br>".$btn_eliminar."<br><br>".$btn_terminar;
            }else if ($indicacion->tipo === "Interconsulta"){
                $columna_indicacion = $tipo_inter_desc.$fecha_agregado_desc.$tipo. "<br> Usuario: ".$nombre_completo_usuario;

                if(isset($indicacion->estado_interconsulta) && $indicacion->estado_interconsulta == "Pendiente"){
                    $columna_botonera = $btn_modificar."<br><br>".$btn_eliminar;
                }else if (isset($indicacion->estado_interconsulta) && $indicacion->estado_interconsulta == "Realizada"){
                    $columna_botonera = $btn_modificar;
                }
            }

			$resultado [] = [
                $columna_indicacion,
                $columna_prescripcion,
                $columna_horario_dia,
                $columna_horario_noche,
                $columna_botonera
			];
		}

		return response()->json(["aaData" => $resultado]);
    }



    public function obtenerIndicacion($idIndicacion,$tipo){
        /* comprobar que no exista repetido el horario que se desea agregar */
        $indciacionInfo = PlanificacionCuidadoIndicacionMedica::indicacionMedica($idIndicacion);

        $view = '';
        if ($tipo == 1) {
            $view = "infoIndicacion";
        } elseif ($tipo == 2) {
            $view = "terminarIndicacion";
        } elseif ($tipo == 3) {
            $view = "eliminarIndicacion";
        }

        $resp = View::make("Gestion/gestionEnfermeria/partesPlanificacionCuidados/programacionAtencion/".$view, [
            "indciacionInfo" => $indciacionInfo
		] )->render();

		return response()->json(array("contenido"=>$resp));
    }
    public function obtenerIndicacionEliminarTerminar(Request $request){
        $indciacionInfo = PlanificacionCuidadoIndicacionMedica::where('id',$request->idIndicacion)->whereNull('fecha_modificacion')->whereNotNull('horario')->get();
        if(empty($indciacionInfo) || count($indciacionInfo) == 0) {
            return response()->json(["exito" => "No hay datos para mostrar"]);
        }else{
            return response()->json(array("contenido"=>$indciacionInfo));
        }


    }


    public function modificarAtencionHoras(Request $request){
      $id = $request->id;
      $caso = $request->caso;
        /* comprobar que no exista repetido el horario que se desea agregar */
      $indicacionInfo = PlanificacionCuidadoAtencionEnfermeria::where('tipo',$id)->where('caso',$caso)->whereNull('fecha_modificacion')->whereNotNull('horario')->orderBy('horario')->get();
      $tipoCuidado = TipoCuidado::where('id',$id)->first();
      return response()->json(array("contenido"=>$indicacionInfo, "tipoCuidado"=>$tipoCuidado));
    }
    
    public function modHorasAtencion(Request $request){
        
        try{    
            DB::beginTransaction();
            $copia_horas_enfermera =  PlanificacionCuidadoAtencionEnfermeria::obtenerHorasPlanificacion($request->tipo, $request->caso, 1);
            $copia_horas_tens =  PlanificacionCuidadoAtencionEnfermeria::obtenerHorasPlanificacion($request->tipo, $request->caso, 2);
           
            //se compara si las horas si tiene horas en la bd y ademas si viene vacio las nuevas horas
            
            
            if (count($copia_horas_enfermera) > 0 && $request->nuevas_horas_enfermera == '' || count($copia_horas_tens) > 0 && $request->nuevas_horas_tens == '') {
                return response()->json(["error" => "Error al actualizar horario para atención de enfermeria, no puedo dejar al responsable sin horas"]);
            }elseif(count($copia_horas_enfermera) > 0 &&  count($copia_horas_tens) > 0){
                if(count(array_intersect($request->nuevas_horas_tens, $request->nuevas_horas_enfermera)) > 0){
                    return response()->json(["error" => "Error al actualizar horario para atención de enfermeria, no se puede asignar el mismo horario para ambos responsables"]);
                }
            }
            
            $horas_enfermera_eliminar = '';
            $horas_enfermera_agregar = '';
            if(count($copia_horas_enfermera) > 0){
                $horas_enfermera_eliminar = array_diff($copia_horas_enfermera, $request->nuevas_horas_enfermera);
                $horas_enfermera_agregar = array_diff($request->nuevas_horas_enfermera,$copia_horas_enfermera);
            }
            
            $horas_tens_eliminar = '';
            $horas_tens_agregar = '';
            if(count($copia_horas_tens) > 0){               
                $horas_tens_eliminar = array_diff($copia_horas_tens, $request->nuevas_horas_tens);
                $horas_tens_agregar = array_diff($request->nuevas_horas_tens,$copia_horas_tens);
            }



            $usuario_modifica = Auth::user()->id;
            $fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");

            if(!empty($horas_enfermera_eliminar)){
                PlanificacionCuidadoAtencionEnfermeria::where('tipo',$request->tipo)
                ->where('caso',$request->caso)
                ->whereNull('fecha_modificacion')
                ->where('resp_atencion','1')
                ->whereIn('horario', $horas_enfermera_eliminar)
                ->update([
                    'usuario_modifica' => $usuario_modifica,
                    'visible' => false, 
                    'fecha_modificacion' => $fecha_modificacion, 
                    'tipo_modificacion' => 'Modificado',
                ]);
            }
            if(!empty($horas_tens_eliminar)){
                PlanificacionCuidadoAtencionEnfermeria::where('tipo',$request->tipo)
                ->where('caso',$request->caso)
                ->whereNull('fecha_modificacion')
                ->where('resp_atencion','2')
                ->whereIn('horario', $horas_tens_eliminar)
                ->update([
                    'usuario_modifica' => $usuario_modifica,
                    'visible' => false, 
                    'fecha_modificacion' => $fecha_modificacion, 
                    'tipo_modificacion' => 'Modificado',
                ]);
            }

            if(!empty($horas_enfermera_agregar)){
                foreach($horas_enfermera_agregar as $hora){  
                $hora_new = new PlanificacionCuidadoAtencionEnfermeria;
                $hora_new->caso = $request->caso;
                $hora_new->usuario = Auth::user()->id;
                $hora_new->visible = true;
                $hora_new->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                $hora_new->tipo = $request->tipo;
                $hora_new->horario = $hora;
                $hora_new->resp_atencion = 1;
                $hora_new->save();
                }
            }
            if(!empty($horas_tens_agregar)){
                foreach($horas_tens_agregar as $hora){  
                    $hora_new = new PlanificacionCuidadoAtencionEnfermeria;
                    $hora_new->caso = $request->caso;
                    $hora_new->usuario = Auth::user()->id;
                    $hora_new->visible = true;
                    $hora_new->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                    $hora_new->tipo = $request->tipo;
                    $hora_new->horario = $hora;
                    $hora_new->resp_atencion = 2;
                    $hora_new->save();
                    }
            }
            DB::commit();
            return response()->json(["exito" => "Se ha actualizado el horario de atención de enfermeria exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al actualizar horario para atención de enfermeria"]);
        }

      }

    public function terminarAtencionEnfermeria(Request $request){
      try{
          DB::beginTransaction();

            $indicacionInfo = PlanificacionCuidadoAtencionEnfermeria::where('id',$request ->id)->first();
            $indicacionInfo->usuario_modifica = Auth::user()->id;
            $indicacionInfo->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $indicacionInfo->tipo_modificacion = "Terminado";
            $indicacionInfo->save();

            //log::info($indicacionInfo->tipo);

          DB::commit();
          return response()->json(["exito" => "Se ha dato Termino al horario de atención de enfermeria exitosamente", "tipo"=>$indicacionInfo->tipo, "opcion"=>2]);

        }catch(Exception $e){
          Log::info($e);
          DB::rollback();
          return response()->json(["error" => "Error al dar Termino al horario para atención de enfermeria"]);
        }
    }


    public function eliminarAtencionEnfermeria(Request $request){
        try{
            DB::beginTransaction();
            $pHelper = new PlanificacionesHelper();
            $pHelper->eliminarPlanificacion($request);
            //log::info($request->tipo);
            DB::commit();
            return response()->json(["exito" => "Se ha eliminado horario de atención de enfermeria", "tipo"=>$request->tipo, "opcion"=>1]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar horario de atención de enfermeria"]);
        }
    }
    public function eliminarOTerminarAtencion(Request $request){
        try{
            //log::info($request);
            DB::beginTransaction();

            if($request->estado == 1){
                $tipo_modificacion = 'Eliminado';
                $visible = false;
            }elseif($request->estado == 2){
                $tipo_modificacion = 'Terminado';
                $visible = true;

            }

            $usuario_modifica = Auth::user()->id;
            $fecha_modificacion = Carbon::now();

            PlanificacionCuidadoAtencionEnfermeria::where('tipo',$request->tipo)
                ->where('caso',$request->caso)
                ->whereNull('fecha_modificacion')
                ->update([
                    'usuario_modifica' => $usuario_modifica,
                    'visible' => $visible, 
                    'fecha_modificacion' => $fecha_modificacion, 
                    'tipo_modificacion' => $tipo_modificacion,
                ]);

            DB::commit();
            return response()->json(["exito" => "Se ha ".mb_strtolower($tipo_modificacion)." todos los horarios de atención de enfermeria", "tipo"=>$request->tipo, "opcion"=>1]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al tratar de modificar los horarios de atención de enfermeria"]);
        }
    }

    public function obtenerAtencionEnfermeria($caso){
        /* $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay(); */
        
        
        try{
            $pHelper = new PlanificacionesHelper();
            $resultado = $pHelper->obtenerPlanificacionesVigentes($caso);
            return response()->json(["aaData" => $resultado]);
        }
        catch(Exception $e){
            Log::info($e);
            return response()->json(["error" => "Ha ocurrido un error al traer las planificaciones vigentes"]);
        }
    }
    public function obtenerCuidadosAlAlta($caso){
        /* $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay(); */
        
        
        try{
            $resultado = $pHelper->obtenerPlanificacionesVigentes($caso);
            return response()->json(["aaData" => $resultado]);
        }
        catch(Exception $e){
            Log::info($e);
            return response()->json(["error" => "Ha ocurrido un error al traer los vigentes"]);
        }
    }

    public function eliminarHora(Request $request){

      $caso = $request->caso;
      $tipo = $request->grupoId;


      $datos = PlanificacionCuidadoAtencionEnfermeria::select('id','horario','resp_atencion')->where('caso',$caso)->where('tipo',$tipo)->whereNull('fecha_modificacion')->whereNotNull('horario')->orderBy('horario')->get();
      if(empty($datos) || count($datos) == 0) {
        return response()->json(["exito" => "No hay datos para mostrar"]);
        }else{

            $tipoCuidado = TipoCuidado::where('id',$tipo)->first();
    
            $id = [];
            $horario = [];
            $color = [];
    
            foreach($datos as $dato){
    
            $id[] = $dato->id;
            $horario[] = $dato->horario;
    
            if($dato->resp_atencion == 1){
                $color[] = "colorEnfermera";
            }else if($dato->resp_atencion == 2){
                $color[] = "colorTens";
            }else{
                $color[] = "colorDefault";
            }
    
            }
    
                return response()->json(["dato"=>$dato, "id"=>$id, "horarios"=>$horario, "color"=>$color, "tipoCuidado"=>$tipoCuidado->tipo]);
        }
    }

    public function validar_aetipo(Request $request){

        $cuidados_validos = TipoCuidado::orderBy("tipo","asc")->pluck('id');
        $validador = Validator::make($request->all(), [
            'AETipo' => Rule::in($cuidados_validos)
        ]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe ingresar un cuidado existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }
 
    public function validar_aetipo2(Request $request){
        log::info($request);
        $AETipo = $request->AETipo;
        //si el tipo cuidado viene vacio
        if($AETipo == ''){
            return response()->json([false]);
        }else{
            //si el tipo cuidado existe
        $cuidados_validos = TipoCuidado::orderBy("tipo","asc")->pluck('id');
        $validador = Validator::make($request->all(), [
            'AETipo' => Rule::in($cuidados_validos)
        ]);
         //si no lanza error se comprueba nuevamente
        if(!$validador->fails() || $AETipo == -1){
            $cuidados_validos = TipoCuidado::orderBy("tipo","asc")->pluck('tipo');
            $validador = Validator::make($request->all(), [
                'tipo_c' => Rule::in($cuidados_validos)
            ]);

            //si esque existe el tipo cuidado, pero envia id -1 
            if(!$validador->fails()){
                $cuidado = TipoCuidado::select('id')->where('tipo',trim(strip_tags($request->tipo_c)))->first();
                return response()->json([false,'tipo'=>$cuidado->id]);
                
            }else{
                if($request->seleccionado_AETipo != ''){
                    $where = ' id = '.$request->seleccionado_AETipo;
                }else{
                    $where = " tipo ILIKE'%".$request->tipo_c."%'";
                }

                $datos=DB::select(DB::raw(
                    "
                    SELECT
                    tipo,
                    id
                    FROM
                    tipo_cuidado 
                    WHERE
                    ".$where."
                    ORDER BY
                    tipo ASC 
                    LIMIT 1
                    "
                ));
                
                if(isset($datos[0])){
                    // si esque no existe el tipo cuidado y ademas envia un -1
                    log::info($request);
                    return response()->json([true,'tipo'=>$AETipo,'tipo_parecido'=>$datos[0]]);
                }else{
                    return response()->json([true,'tipo'=>$AETipo]);
                }
            }
        }else{
        
            return response()->json([false,'tipo'=>$AETipo]);
        }
    }
    }

    public function addaetipo(Request $request){
        try{
            DB::beginTransaction();

            $cuidado = new TipoCuidado;
            $cuidado->tipo = trim(strip_tags($request->tipo_c));
            $cuidado->save();

            DB::commit();
            $cuidado = TipoCuidado::select('id')->where('tipo',trim(strip_tags($request->tipo_c)))->first();
         
            $request->merge([
                'AETipo' => $cuidado->id,
            ]);


            
    try{
        DB::beginTransaction();

        $pHelper = new PlanificacionesHelper();

        $this->resp_validos = ['1'=>'1','2'];
        $this->horarios_validos = [ '0'=> '00', '1' => '01', '2' => '02', '3' => '03','4'=> '04', '5' => '05', '6' => '06', '7' => '07', '8'=> '08', '9' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'];
        $this->cuidados_validos = TipoCuidado::orderBy("tipo","asc")->pluck('id');
        $validador = Validator::make($request->all(), [
            'AETipo' => Rule::in($this->cuidados_validos),
            'horario2.*' => [Rule::in($this->horarios_validos),'required'],
            'resp_atencion' => [Rule::in($this->resp_validos),'required']
            
        ],[
            'AETipo.in' => 'Debe ingresar un cuidado existente',
            'AETipo.required' => 'Debe seleccionar un cuidado',
            'horario2.*.in' => 'Debe ingresar un horario existente',
            'horario2.required' => 'Debe ingresar una hora a la atención',
            'resp_atencion.in' => 'Debe ingresar un responsable existente',
            'resp_atencion.required' => 'Debe seleccionar un responsable'
        ]);

        if($validador->fails()){
            return response()->json(['errores' => $validador->errors()->all()]);
        }
        $tipo = strip_tags($request->AETipo);
        $id_caso = strip_tags($request->idCaso);
        $id_atencion_nulo = null;

        //para control de signos vitales
        if($tipo === "32"){
            //Si es un control de  signos vitales se crea un registro con hora vacia, para que este asocie todos los registros de cuidado que no tenian una planificacion asociada
            $id_atencion_nulo = $pHelper->crearPlanificacionHoraNula($id_caso,$tipo);
        }
        
        foreach ($request->horario2 as $hr) {
            $hora = strip_tags($hr);
            //Aqui se crean las horas planificadas normalmente
            $id_atencion_hora = $pHelper->crearPlanificacionHora($id_caso, $tipo, $hora, $request->resp_atencion);

            //Para planificacion de signos vitales 
            if($tipo === "32"){
                $pHelper->actualizarAtencionPlanificacionHoraNulaConAtencionPlanificacionHora($id_atencion_nulo, $id_atencion_hora , $hora);
            }

        }

        DB::commit();
        return response()->json(["exito" => "Se ha actualizado el horario de atención de enfermeria exitosamente",$request->all()]);

        }catch(Exception $e){
            dd($e);
            DB::rollback();
            return response()->json(["error" => "Error al actualizar horario para atención de enfermeria"]);
        }

            

        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al ingresar el tipo de cuidado"]);
        }   
    }
    
 

    

    public function validar_horario2(Request $request){
        $horarios_validos = [ '0'=> '00', '1' => '01', '2' => '02', '3' => '03','4'=> '04', '5' => '05', '6' => '06', '7' => '07', '8'=> '08', '9' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'];
        $validador = Validator::make($request->all(), [
            'horario2.*' => Rule::in($horarios_validos)
        ]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe ingresar un horario existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }

    public function validar_resp_atencion(Request $request){
        
        if($request->sub_categoria == 1){
            $this->resp_validos = ['1'=>'2','3'];    
        }
        $this->resp_validos = ['1'=>'1','2','3'];
        $validador = Validator::make($request->all(), [
            'resp_atencion' => Rule::in($this->resp_validos)
        ]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe ingresar un responsable existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }

    public function validar_metodo1(Request $request){
        $metodos_validos = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'];
        $validador = Validator::make($request->all(), [
            'metodo1' => Rule::in($metodos_validos)
        ]);
   

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe seleccionar un metodo existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }
    public function validar_fio1(Request $request){
        $metodos_validos = ['21' => '21','24' => '24', '26' => '26', '28' => '28', '32' => '32', '35' => '35', '36' => '36', '40' => '40', '45' => '45', '50' => '50', '60' => '60', '70' => '70', '90' => '90'];
        $validador = Validator::make($request->all(), [
            'fio1' => Rule::in($metodos_validos)
        ]);
   
        //Log::info([$request->fio1]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe seleccionar un fio2 existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }
    
    public function addAtencionEnfermeria(Request $request){
        //Esta funcion se encarga de crear las atenciones  de enfermeria
        try{
            DB::beginTransaction();

            $pHelper = new PlanificacionesHelper();

            $this->resp_validos = ['1'=>'1','2','3'];
            $this->horarios_validos = [ '0'=> '00', '1' => '01', '2' => '02', '3' => '03','4'=> '04', '5' => '05', '6' => '06', '7' => '07', '8'=> '08', '9' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'];
            $this->cuidados_validos = TipoCuidado::orderBy("tipo","asc")->pluck('id');
            $validador = Validator::make($request->all(), [
                'AETipo' => Rule::in($this->cuidados_validos),
                'horario2.*' => [Rule::in($this->horarios_validos),'required'],
                'resp_atencion' => [Rule::in($this->resp_validos),'required']
                
            ],[
                'AETipo.in' => 'Debe ingresar un cuidado existente',
                'AETipo.required' => 'Debe seleccionar un cuidado',
                'horario2.*.in' => 'Debe ingresar un horario existente',
                'horario2.required' => 'Debe ingresar una hora a la atención',
                'resp_atencion.in' => 'Debe ingresar un responsable existente',
                'resp_atencion.required' => 'Debe seleccionar un responsable'
            ]);
    
            if($validador->fails()){
                return response()->json(['errores' => $validador->errors()->all()]);
            }
            $tipo = strip_tags($request->AETipo);
            $id_caso = strip_tags($request->idCaso);
            $id_atencion_nulo = null;

            //para control de signos vitales
            if($tipo === "32"){
                //Si es un control de  signos vitales se crea un registro con hora vacia, para que este asocie todos los registros de cuidado que no tenian una planificacion asociada
                $id_atencion_nulo = $pHelper->crearPlanificacionHoraNula($id_caso,$tipo);
            }
            
            foreach ($request->horario2 as $hr) {
                $hora = strip_tags($hr);
                //Aqui se crean las horas planificadas normalmente
                $id_atencion_hora = $pHelper->crearPlanificacionHora($id_caso, $tipo, $hora, $request->resp_atencion);

                //Para planificacion de signos vitales 
                if($tipo === "32"){
                    $pHelper->actualizarAtencionPlanificacionHoraNulaConAtencionPlanificacionHora($id_atencion_nulo, $id_atencion_hora , $hora);
                }

            }

            DB::commit();
            return response()->json(["exito" => "Se ha actualizado el horario de atención de enfermeria exitosamente",$request->all()]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al actualizar horario para atención de enfermeria"]);
        }

    }

    public function obtenerNovedades($caso){
        $novedades = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.fecha_creacion,
                f.novedad
                from formulario_planificacion_cuidados_novedades as f
                inner join usuarios as u on u.id = f.usuario
                where
                f.caso = $caso and f.visible = true
                order by id desc"));

        $resultado = [];
        foreach ($novedades as $key => $novedad) {
            
            $nov = ($novedad->novedad) ? $novedad->novedad : "";
            $html_novedad = "<div class='form-group'><div class='col-md-10'><input class='form-control' id='novedad".$key."' type='text' value='".$nov."' onKeyup='validarNovedad(".$key.")';><span style='color:#a94442' id='errorNovedad".$key."'></span></div>";

			$resultado [] = [
                "<b>".$novedad->nombres." ".$novedad->apellido_paterno." ".$novedad->apellido_materno."</b> <br> Creado el: ".Carbon::parse($novedad->fecha_creacion)->format("d-m-Y H:i"),
                $html_novedad,
                "<div class='row'>
                <div class='col-md-3'>
                    <button type='button' class='btn-xs btn-warning' onclick='modificarNovedad(".$novedad->id.",".$key.")'>Modificar</button>
                </div>
                <div class='col-md-3'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarNovedad(".$novedad->id.")'>Eliminar</button>
                </div>
                </div>"
			];
		}

		return response()->json(["aaData" => $resultado]);
    }

    public function addNovedades(Request $request){
        try{
            DB::beginTransaction();

            $novedad = new PlanificacionCuidadoNovedad;
            $novedad->caso = strip_tags($request->idCaso);
            $novedad->usuario = Auth::user()->id;
            $novedad->visible = true;
            $novedad->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* Datos importantes */
            $novedad->novedad =strip_tags($request->novedad);
            $novedad->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado una novedad del paciente exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar novedad"]);
        }   
    }

    public function modificarNovedad(Request $request){


        //validar datos
        $validador = Validator::make($request->all(), [
            'novedad' => 'required'
        ],[
            'novedad.required' => 'Debe Ingresar la novedad'
        ]);

        if($validador->fails()){
            return response()->json(['errores' => $validador->errors()->all()]);
        }

        if($validador->passes()){
            try{
                DB::beginTransaction();
    
                /* se modifica el actual */
                $modificar = PlanificacionCuidadoNovedad::find($request->id);
                $modificar->usuario_modifica = Auth::user()->id;
                $modificar->fecha_modificacion = Carbon::now();
                $modificar->visible = false;
                $modificar->tipo_modificacion = 'Editado';
                $modificar->save();
    
                /* se crea el nuevo examen */
                $nuevaNovedad = new PlanificacionCuidadoNovedad;
                $nuevaNovedad->caso = $modificar->caso;
                $nuevaNovedad->usuario = Auth::user()->id;
                $nuevaNovedad->visible = true;
                $nuevaNovedad->id_anterior = $modificar->id;
                $nuevaNovedad->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                /* datos importantes */
                $nuevaNovedad->novedad = $request->novedad;
                $nuevaNovedad->save();
    
                DB::commit();
                return response()->json(["exito" => "Se ha modificado la novedad exitosamente"]);
    
            }catch(Exception $e){
                Log::info($e);
                DB::rollback();
                return response()->json(["error" => "Error al modificar la novedad"]);
            }
        }
    }

    public function eliminarNovedad(Request $request){

        try{
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = PlanificacionCuidadoNovedad::find($request->id);
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now();
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado la novedad exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar la novedad"]);
        }
    }


    public function obtenerProtecciones($caso){
        $protecciones = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.fecha_creacion,
                f.fecha
                from formulario_planificacion_cuidados_protecciones as f
                inner join usuarios as u on u.id = f.usuario
                where
                f.caso = $caso and f.visible = true
                "));

        $resultado = [];
        foreach ($protecciones as $key => $proteccion) {

            /* Innput Fecha TOMADOS es aquella en que se hizo el examen */

			$resultado [] = [
                "<b>".$proteccion->nombres." ".$proteccion->apellido_paterno." ".$proteccion->apellido_materno."</b> <br> Creado el: ".Carbon::parse($proteccion->fecha_creacion)->format("d-m-Y H:i"),
                Carbon::parse($proteccion->fecha)->format("d-m-Y"),
                /* "<div class='row'>
                ".$html6."
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarFila(".$examen->id.")'>Eliminar</button>
                </div>
                </div>" */
			];
		}

		return response()->json(["aaData" => $resultado]);
    }

    public function addProtecciones(Request $request){
        try{
            DB::beginTransaction();

            $curacion = new PlanificacionCuidadoProteccion;
            $curacion->caso = strip_tags($request->idCaso);
            $curacion->usuario = Auth::user()->id;
            $curacion->visible = true;
            $curacion->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* Datos importantes */
            $curacion->fecha =Carbon::parse(strip_tags($request->fecha_proteccion));
            $curacion->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado programa de curaciones exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar programa de curaciones"]);
        }

    }

    public function obtenerCuraciones($caso){
        $curaciones = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.fecha_creacion,
                f.fecha
                from formulario_planificacion_cuidados_curaciones as f
                inner join usuarios as u on u.id = f.usuario
                where
                f.caso = $caso and f.visible = true
                "));

        $resultado = [];
        foreach ($curaciones as $key => $curacion) {

            /* Innput Fecha TOMADOS es aquella en que se hizo el examen */

			$resultado [] = [
                "<b>".$curacion->nombres." ".$curacion->apellido_paterno." ".$curacion->apellido_materno."</b> <br> Creado el: ".Carbon::parse($curacion->fecha_creacion)->format("d-m-Y H:i"),
                Carbon::parse($curacion->fecha)->format("d-m-Y"),
                /* "<div class='row'>
                ".$html6."
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarFila(".$examen->id.")'>Eliminar</button>
                </div>
                </div>" */
			];
		}

		return response()->json(["aaData" => $resultado]);
    }

    public function addCuraciones(Request $request){
        try{
            DB::beginTransaction();

            $curacion = new PlanificacionCuidadoCuracion;
            $curacion->caso = strip_tags($request->idCaso);
            $curacion->usuario = Auth::user()->id;
            $curacion->visible = true;
            $curacion->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* Datos importantes */
            $curacion->fecha =Carbon::parse(strip_tags($request->fecha_curacion));
            $curacion->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado programa de curaciones exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar programa de curaciones"]);
        }

    }

    public function alertaCuracionesSimples(Request $request){
        return HojaCuracionesSimple::alertaCuracionesSimple($request->caso);
    }


    public function consulta_aetipo($palabra){
        try{

        $datos=DB::select(DB::raw(
           "
           SELECT
           tipo,
           id
           FROM
           tipo_cuidado 
           WHERE
           tipo ILIKE'%".$palabra."%' 
		   AND id_subcategoria_unidad IS NULL
           ORDER BY
           tipo ASC 
           LIMIT 50
           "
       ));
       return response()->json($datos);

    }catch(Exception $e){
        return response()->json(["error" => "Error"]);
    }
   }
   public function consulta_aetipo_pediatria($palabra){
        try{

        $datos=DB::select(DB::raw(
           "
           SELECT
           tipo,
           id
           FROM
           tipo_cuidado 
           WHERE
           tipo ILIKE'%".$palabra."%' 
		   AND (id_subcategoria_unidad = 4 OR id_subcategoria_unidad IS NULL)
           ORDER BY
           tipo ASC 
           LIMIT 50
           "
       ));
       return response()->json($datos);

    }catch(Exception $e){
        return response()->json(["error" => "Error"]);
    }
   }

   //Nuevas funciones indicacion medica
   public function cargarDatosIndicacionMedica($caso){ 
    try {
        $fecha_actual = Carbon::now()->format('Y-m-d');
        $ultimaIndicacion = IndicacionMedica::where("caso",$caso)->where('visible',true)->whereDate('fecha_emision','<=',$fecha_actual)->whereDate('fecha_vigencia','>=',$fecha_actual)->orderBy('id','desc')->first();
        $tipos_reposo = [];
        $farmacos = [];
        $sueros = [];

        if($ultimaIndicacion && !empty($ultimaIndicacion->farmacos)){
            foreach ($ultimaIndicacion->farmacos as $key => $farmaco) {
                $datos_farmacos = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"))
                ->whereIn('tipo',['ANTIBIOTICO','ANTIBIOTICO-ANTIFUNGICO'])
                ->where('id',$farmaco->id_farmaco)
                ->first();

                $ultimaIndicacion->farmacos[$key]['nombre_unidad'] = $datos_farmacos->nombre_unidad;
            }
            $farmacos = $ultimaIndicacion->farmacos;
        }
        
        if($ultimaIndicacion && $ultimaIndicacion->sueros == true && !empty($ultimaIndicacion->suero)){
            $sueros = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"), 'id')
            ->where('tipo','SUERO')
            ->where('id',$ultimaIndicacion->suero)
            ->get();

        }
       
        return response()->json(array("ultimaIndicacion"=>$ultimaIndicacion, "farmacos" => $farmacos,"sueros"=>$sueros)); 
    } catch (Exception $ex) {
        Log::info($ex);
        return response()->json(array("error"=>$ex));
    }
}

    public function addDatosIndicacionMedica(Request $request){
        /* comprobar que no exista repetido el horario que se desea agregar */
        try{

            DB::beginTransaction();

            $auth_user_id = Auth::user()->id;
            $hoy = Carbon::now()->format("Y-m-d H:i:s");

            /* CAPTURAR LO QUE LLEGA */
            $id_caso_agregar_indicacion = (isset($request->id_caso_agregar_indicacion_medica) && trim($request->id_caso_agregar_indicacion_medica) !== "") ?
            trim(strip_tags($request->id_caso_agregar_indicacion_medica)) : null;

            $tipo = (isset($request->tipo_agregar_indicacion_medica) && trim($request->tipo_agregar_indicacion_medica) !== "" ) ?
            trim(strip_tags($request->tipo_agregar_indicacion_medica)) : null;        

            $id_indicacion_medica = (isset($request->id_indicacion_medica) && trim($request->id_indicacion_medica) !== "" ) ?
            trim(strip_tags($request->id_indicacion_medica)) : null;

            $horario_indicacion = (isset($request->horario_indicacion_medica)) ? 
            $request->horario_indicacion_medica : array();

            $responsable_indicacion = (isset($request->responsable_indicacion_medica)) ?
            $request->responsable_indicacion_medica : null;

            $farmacos_agregar_indicacion_medica = (isset($request->farmacos_agregar_indicacion_medica) && trim($request->farmacos_agregar_indicacion_medica) !== "") ?
            trim(strip_tags($request->farmacos_agregar_indicacion_medica)) : null;

            /* VALIDAR DOMINIO DE LO QUE LLEGA Y PERSISTIR */

            $tipo_valid = ['Control de signos vitales', 'Control de hemoglucotest','Suero','Farmacos'];
            $horario_valid = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
            $responsable_valid = ['1','2'];



            if (!in_array($tipo, $tipo_valid)) { return response()->json(array("error"=>"Error al guardar la indicacion medica")); }


            Log::info($request);
            /* responsable */
            if (!in_array($responsable_indicacion, $responsable_valid)) { return response()->json(array("error"=>"Error al guardar la indicacion medica")); }



            //horario_indicacion
            if(count($horario_indicacion) > 0){
                foreach ($horario_indicacion as $key => $hora) {
                    if (!in_array($hora, $horario_valid)) { 
                        return response()->json(array("error"=>"Error al guardar la indicacion medica"));
                    }
                }
            }
            else { return response()->json(array("error"=>"Error al guardar la indicacion medica"));
            }

            
            $fecha_actual = Carbon::now()->format('Y-m-d');
            $ultimaIndicacion = IndicacionMedica::where("caso",$id_caso_agregar_indicacion)->where('id',$id_indicacion_medica)->whereDate('fecha_emision','<=',$fecha_actual)->whereDate('fecha_vigencia','>=',$fecha_actual)->where('visible',true)->orderBy('id','desc')->first();
            Log::info($ultimaIndicacion);
            if(!empty($ultimaIndicacion)){
                /* indicacion */    
                $indicacion = new PlanificacionIndicacionMedica();
                $indicacion->caso = $id_caso_agregar_indicacion;
                $indicacion->id_indicacion = $ultimaIndicacion->id;
                $indicacion->usuario = $auth_user_id;
                $indicacion->visible = true;
                $indicacion->tipo = $tipo; 
                $indicacion->fecha_creacion = $hoy;
                $indicacion->id_farmaco = $farmacos_agregar_indicacion_medica;
                // $indicacion->fecha_emision = $ultimaIndicacion->fecha_emision;
                // $indicacion->fecha_vigencia = $ultimaIndicacion->fecha_vigencia;
                $indicacion->responsable = $responsable_indicacion;
                $indicacion->horario = implode(",", $horario_indicacion);
                $indicacion->save();
                
            }else{
                return response()->json(array("error"=>"La indicacion no existe"));
            }
            DB::commit();
            return response()->json(["exito" => "Se ha ingresado una indicación medica exitosamente"],200);

        }catch(Exception $e){
            DB::rollback();
            return response()->json(array("error"=>"Error al ingresar la indicación medica"));
        }

    }


    public function obtenerDatosIndicacionFarmacos($caso,$id_farmaco,$id_indicacion_medica){
        try{
            $ultimaIndicacion = IndicacionMedica::where("caso",$caso)->where('id',$id_indicacion_medica)->where('visible',true)->orderBy('id','desc')->first();
            $farmacos = [];
            if($ultimaIndicacion && !empty($ultimaIndicacion->farmacos)){
                foreach ($ultimaIndicacion->farmacos as $key => $farmaco) {
                    if($farmaco->id == $id_farmaco){
                        $farmacos = array("via_administracion"=>$farmaco->via_administracion,"intervalo"=>$farmaco->intervalo_farmaco);
                    }
                }
            }

            return response()->json($farmacos);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(array("error"=>"Error al ingresar la indicación medica"));
        }
      
    }

    
    public function obtenerDatosPlanificacionIndicacionesMedicas($caso){
        $inicio = Carbon::now()->startOfDay()->format('Y-m-d');
        $fin = Carbon::now()->format('Y-m-d');

        $indicaciones = DB::select("select
                f.id,
                f.id_indicacion,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.id_farmaco,
                f.fecha_creacion,
                f.tipo,
                f.horario,
                i.fecha_vigencia,
                i.fecha_emision,
                f.responsable
                from formulario_planificacion_indicaciones_medicas as f
                inner join usuarios as u on u.id = f.usuario
                left join indicaciones_medicas as i on i.id = f.id_indicacion
                where
                f.caso = ? and f.visible = true and f.tipo_modificacion is null      
                and
                (
                    (
                    i.fecha_emision is null and i.fecha_vigencia is not null and i.fecha_vigencia >= '$inicio' and i.fecha_vigencia >= '$inicio'
                    )
                    or
                    (
                    i.fecha_emision is null and i.fecha_vigencia is not null and i.fecha_vigencia <= '$inicio' and i.fecha_vigencia >= '$fin'
                    )
                    or
                    (
                        i.fecha_emision is null and i.fecha_vigencia is not null and  i.fecha_vigencia >= '$inicio' and i.fecha_vigencia <= '$fin'
                    )
                    or
                    (
                        i.fecha_emision is null and i.fecha_vigencia is not null and  i.fecha_vigencia >= '$inicio' and i.fecha_vigencia >= '$fin'
                    )
                    or
                    (
                    i.fecha_emision is not null and i.fecha_vigencia is not null and i.fecha_emision >= '$inicio' and i.fecha_vigencia >= '$inicio'
                    )
                    or
                    (
                    i.fecha_emision is not null and i.fecha_vigencia is not null and i.fecha_emision <= '$inicio' and i.fecha_vigencia >= '$fin'
                    )
                    or
                    (
                        i.fecha_emision is not null and i.fecha_vigencia is not null and  i.fecha_emision >= '$inicio' and i.fecha_vigencia <= '$fin'
                    )
                    or
                    (
                        i.fecha_emision is not null and i.fecha_vigencia is not null and  i.fecha_emision >= '$inicio' and i.fecha_vigencia >= '$fin'
                    )
                )   
                ",[$caso]);

        $resultado = [];
        $color = "";
        foreach ($indicaciones as $key => $indicacion) {

            //Columna indicación
            $columna_indicacion = "";
            $tipo_indicacion = (isset($indicacion->tipo) && trim($indicacion->tipo) !=="") ? "<b>".ucwords($indicacion->tipo)."</b>" : "";
            $tipo_indicacion = ($tipo_indicacion) ? wordwrap($tipo_indicacion, 40, "<br />\n",true) : "";

            $fecha_agregado_desc = "<br> Agregado el: ".Carbon::parse($indicacion->fecha_creacion)->format("d-m-Y H:i");

            // $via = (isset($indicacion->via) && trim($indicacion->via) !=="") ? "<br> Via de administración: ".$indicacion->via : "";
            // $intervalo = (isset($indicacion->dosis) && trim($indicacion->dosis) !=="") ? "Dosis: ".$indicacion->dosis : "";
            // $intervalo = ($dosis) ? wordwrap($dosis, 40, "<br />\n",true) : "";

            //Columna horario
            $columna_horario_dia = "Sin horario";
            $columna_horario_noche = "Sin horario";
           
            $horarios = (isset($indicacion->horario) && trim($indicacion->horario) !== "") ? $indicacion->horario : "";
            
            if($indicacion->responsable == 1){
                $color = "colorEnfermera";
            }else if($indicacion->responsable == 2){
                $color = "colorTens";
            }else{
                $color = "colorDefault";
            }
            $turno_dia = "";
            $turno_noche = "";

            if($horarios){
                $array_horarios = explode(',', $horarios);

                $dia = ['08','09','10','11','12','13','14','15','16','17','18','19','20'];
                $noche = ['21','22','23','00','01','02','03','04','05','06','07'];

                foreach ($array_horarios as $hora){
                    if(in_array($hora,$dia)){
                        $turno_dia .= "<div class='$color'><div class=''></div><div class='valorInternoSinX'>$hora</div></div>";
                    }else if(in_array($hora,$noche)){
                        $turno_noche .= "<div class='$color'><div class=''></div><div class='valorInternoSinX'>$hora</div></div>";
                    }
                }
            }

            //Columna opciones
            $columna_botonera = "";
            $btn_modificar = "<div class='col-md-5'> <button type='button' class='btn-xs btn-warning' onclick='obtenerIndicacionMedica(".$indicacion->id.",1)'>Modificar</button>";
            $btn_eliminar = "<button type='button' class='btn-xs btn-danger' onclick='obtenerIndicacionEliminarTerminarMedica(".$indicacion->id.",3)'>Eliminar</button>";
            $btn_terminar = "<button type='button' class='btn-xs btn-success' onclick='obtenerIndicacionEliminarTerminarMedica(".$indicacion->id.",2)'>Terminar</button></div>";

            //Columna usuario
            $nombres = (isset($indicacion->nombres) && trim($indicacion->nombres) !=="") ? $indicacion->nombres : "";
            $apellido_paterno = (isset($indicacion->apellido_paterno) && trim($indicacion->apellido_paterno) !=="") ? $indicacion->apellido_paterno : "";
            $apellido_materno = (isset($indicacion->apellido_materno) && trim($indicacion->apellido_materno) !=="") ? $indicacion->apellido_materno : "";
            $nombre_completo_usuario = "<span>".$nombres." ".$apellido_paterno." ".$apellido_materno."</span>";

            if($indicacion->tipo === "Farmacos"){
            $ultimaIndicacion = IndicacionMedica::where("caso",$caso)->where('id',$indicacion->id_indicacion)->where('visible',true)->orderBy('id','desc')->first();
                if($ultimaIndicacion && count($ultimaIndicacion->farmacos) > 0){
                    foreach ($ultimaIndicacion->farmacos as $key => $farmaco) {
                        if($farmaco->id == $indicacion->id_farmaco){
                            $datos_farmacos = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"))
                            ->whereIn('tipo',['ANTIBIOTICO','ANTIBIOTICO-ANTIFUNGICO'])
                            ->where('id',$farmaco->id_farmaco)
                            ->first();
                            $columna_indicacion = $tipo_indicacion. "<br>".$datos_farmacos->nombre_unidad.$fecha_agregado_desc. "<br> Usuario: ".$nombre_completo_usuario;
                        }
                    }
                }
            }else if($indicacion->tipo === "Suero"){
                $ultimaIndicacion = IndicacionMedica::where("caso",$caso)->where('id',$indicacion->id_indicacion)->where('visible',true)->orderBy('id','desc')->first();
                if($ultimaIndicacion && $ultimaIndicacion->sueros == true && $ultimaIndicacion->suero != null){
                    $datos_sueros = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"), 'id')
                    ->where('tipo','SUERO')
                    ->where('id',$ultimaIndicacion->suero)
                    ->first();
                    $columna_indicacion = $tipo_indicacion. "<br>".$datos_sueros->nombre_unidad.$fecha_agregado_desc. "<br> Usuario: ".$nombre_completo_usuario;
                }
            }else{
                $columna_indicacion = $tipo_indicacion.$fecha_agregado_desc. "<br> Usuario: ".$nombre_completo_usuario;
            }
            // if($indicacion->tipo === "Control de signos vitales" || $indicacion->tipo === "Control de hemoglucotest"){
                // $columna_indicacion = $tipo_indicacion.$fecha_agregado_desc.$tipo. "<br> Usuario: ".$nombre_completo_usuario;
            // }else if ($indicacion->tipo === "Farmacos"){
            //     $columna_indicacion = $tipo_indicacion.$fecha_agregado_desc.$tipo. "<br> Usuario: ".$nombre_completo_usuario. "<br> Via de administración: ".$nombre_completo_usuario. "<br> Intervalo: ".$nombre_completo_usuario;  
            // }
            $columna_horario_dia = $turno_dia;
            $columna_horario_noche = $turno_noche;
            $columna_botonera = $btn_modificar."<br><br>".$btn_eliminar."<br><br>".$btn_terminar;

            $resultado [] = [
                $columna_indicacion,
                $columna_horario_dia,
                $columna_horario_noche,
                $columna_botonera
            ];
        }
        return response()->json(["aaData" => $resultado]);
    }

    public function obtenerIndicacionMedica($idIndicacion,$tipo){
        /* comprobar que no exista repetido el horario que se desea agregar */
        
        $indciacionInfo = PlanificacionIndicacionMedica::where('id',$idIndicacion)->where('visible',true)->get();
        if(count($indciacionInfo) == 0) {
            return response()->json(["info" => "No hay datos para mostrar"]);
        }else{
             $ultimaIndicacion = IndicacionMedica::where('id',$indciacionInfo[0]->id_indicacion)->first();
        // $farmacos = $ultimaIndicacion->farmacos;
        $sueros = [];
        $via_intervalo = [];
        $farmacos = [];
        if($ultimaIndicacion && isset($ultimaIndicacion->farmacos) && !empty($ultimaIndicacion->farmacos)){
            foreach ($ultimaIndicacion->farmacos as $key => $farmaco) {
                $datos_farmacos = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"))
                ->whereIn('tipo',['ANTIBIOTICO','ANTIBIOTICO-ANTIFUNGICO'])
                ->where('id',$farmaco->id_farmaco)
                ->first();

                $ultimaIndicacion->farmacos[$key]['nombre_unidad'] = $datos_farmacos->nombre_unidad;

                if(isset($indciacionInfo[0]->id_farmaco) && $farmaco->id == $indciacionInfo[0]->id_farmaco){
                    $via_intervalo = array("via_administracion"=>$farmaco->via_administracion,
                    "intervalo_farmaco"=>$farmaco->intervalo_farmaco);
                }
            }
           if(isset($ultimaIndicacion->farmacos)){
               $farmacos = $ultimaIndicacion->farmacos;
           }
        }
        if($ultimaIndicacion && $ultimaIndicacion->sueros == true && !empty($ultimaIndicacion->suero)){
            $sueros = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"), 'id')
            ->where('tipo','SUERO')
            ->where('id',$ultimaIndicacion->suero)
            ->pluck('nombre_unidad','id')
            ->toArray();

        }

        $view = '';
        if ($tipo == 1) {
            $view = "infoIndicacionMedica";
        } elseif ($tipo == 2) {
            $view = "terminarIndicacionMedica";
        } elseif ($tipo == 3) {
            $view = "eliminarIndicacionMedica";
        }

        Log::info($ultimaIndicacion);

        $resp = View::make("Gestion/gestionEnfermeria/partesPlanificacionCuidados/programacionAtencion/".$view, [
            "indciacionInfo" => $indciacionInfo,
            "ultimaIndicacion" => $ultimaIndicacion,
            "farmacos" => $farmacos,
            "sueros" => $sueros,
            "via_intervalo" => $via_intervalo,
        ] )->render();

        return response()->json(array("contenido"=>$resp));
        }
     
    }

    public function modificarDatosPCIndicacion(Request $request){
        try{
            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $data = $dao->updateDatosIndicacionMedica($request);

            DB::commit();
            return response()->json(["exito" => "Se ha modificado una  indicación medica"],200);

        }catch(Exception $e){
            DB::rollback();
            $errores_controlados = [
                'Id es nulo.',
                'Ha ocurrido un error.',
                'Campo tipo no valido.',
                'Campo responsable no valido',
                'Campo horario indicación no valido.'
            ];
            $error = "Error al modificar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }

    }

    public function obtenerIndicacionEliminarTerminarMedica(Request $request){
        $indciacionInfo = PlanificacionIndicacionMedica::where('id',$request->idIndicacion)->whereNull('fecha_modificacion')->whereNotNull('horario')->get();
        Log::info(count($indciacionInfo) == 0);
        if(count($indciacionInfo) == 0) {
            return response()->json(["exito" => "No hay datos para mostrar"]);
        }else{
            return response()->json(array("contenido"=>$indciacionInfo));
        }


    }


    
    public function eliminarTerminarPCIndicacionMedica(Request $request){
        try{
            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $dao->deleteDatosIndicacionMedica($request);

            DB::commit();
            $texto = '';
            if($request->tipo_modificacion == 2){
                $texto = 'terminado la';
            }elseif($request->tipo_modificacion == 3){
                $texto = 'eliminado';
            }

            return response()->json(["exito" => "Se ha ".$texto." indicación planificada"],200);

        }catch(Exception $e){
            DB::rollback();
            $errores_controlados = [
                'Id es nulo.'
            ];
            $error = "Error al eliminar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }


    public function eliminarTerminarPCIndicacionHoraMedica(Request $request){
        try{
            DB::beginTransaction();

            $dao = new IndicacionMedicaHelper();
            $eliminar = $dao->deleteDataHoraIndicacionMedica($request);
            DB::commit();

            if($eliminar == false){
                $texto = '';
                if($request->tipo_modificacion == 2){
                    $texto = 'terminar';
                }elseif($request->tipo_modificacion == 3){
                    $texto = 'eliminar';
                }

                return response()->json(["error" => "La hora que desea ".$texto." no existe"]);
            }else{
                return response()->json(["exito" => "Se ha eliminado indicación planificada","nueva_id"=>$eliminar->nueva_id],200);
            }

        }catch(Exception $e){
            DB::rollback();
            $errores_controlados = [
                'Id es nulo.'
            ];
            $error = "Error al eliminar la planificación de indicación medica";
            if (in_array($e->getMessage(), $errores_controlados)){ $error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }



}