<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caso;
use App\Models\Paciente;
use App\Models\HistorialDiagnostico;
use App\Models\UnidadEnEstablecimiento;
use App\Models\Usuario;
use App\Models\EvolucionCaso;
use App\Models\HistorialOcupacion;
use App\Models\SolicitudTrasladoInterno;
use App\Models\THistorialOcupaciones;
use Log;
use DB;
use Auth;
use App\Models\Consultas;
use Response;
use Session;
use Carbon\Carbon;

class TrasladoInternoController extends Controller
{
    public function recibidas(){//USADA
        return View("TrasladoInterno.recibidas");
    }


    public function enviadas(){//USADA
        return View("TrasladoInterno.enviadas");
    }

    public function aceptarTrasladoInterno($idHistorial_ocupacion, $usuario_alta, $idCaso, $idCama, $idListaSolicitud){//USADA
        try{
			DB::beginTransaction();
            
			//cerrar historial antiguo
            $hOcupacionAntiguo = THistorialOcupaciones::find($idHistorial_ocupacion);
            $hOcupacionAntiguo->fecha_liberacion = Carbon::now()->format("Y-m-d H:i:s"); 
            $hOcupacionAntiguo->motivo = "traslado interno"; 
            $hOcupacionAntiguo->id_usuario_alta = $usuario_alta; 
            $hOcupacionAntiguo->save();
           
            // se crea nuevo historial de ocupaciones en el lugar del traslado
			$hOcupacion= new THistorialOcupaciones;
			$hOcupacion->cama = $idCama;
			$hOcupacion->caso = $idCaso;
            $hOcupacion->fecha_ingreso_real = $hOcupacionAntiguo->fecha_ingreso_real;
            $hOcupacion->fecha = Carbon::now()->format("Y-m-d H:i:s");
            $hOcupacion->save();

            $lista=SolicitudTrasladoInterno::find($idListaSolicitud);
			$lista->fecha_respuesta = Carbon::now()->format("Y-m-d H:i:s");
            $lista->solicitud_aceptada = true;
            $lista->usuario_responde = $usuario_alta;
			$lista->save();

			DB::commit();
        
            return ["exito"=>"Solicitud Aceptada"];
		}catch(Exception $ex){
            Log::info($ex);
			DB::rollback();
            return ["error"=>"Ha ocurrido un error mientras se realizaba el traslado", "msj" => $ex];
        }

    }


    public function solicitarTrasladoInterno(Request $request){//USADA
        try{
            $pendienteT = SolicitudTrasladoInterno::select("solicitud_aceptada", "fecha_respuesta")
                                ->where("caso",$request->casoOld)
                                ->orderBy("id_solicitud_traslado_interno", "desc")
                                ->first();

                                
            if (is_null($pendienteT) || $pendienteT->fecha_respuesta) {
                DB::beginTransaction();
                
                $solicitudT = new SolicitudTrasladoInterno();
                $solicitudT->caso = $request->casoOld;
                $solicitudT->id_historial_ocupaciones = THistorialOcupaciones::select('id')->where("caso",$request->casoOld)->whereNull('fecha_liberacion')->first()->id;
                $solicitudT->id_unidad_solicitada = $request->unidad;
                $solicitudT->fecha_solicitud = Carbon::now()->format("Y-m-d H:i:s");
                $solicitudT->usuario_solicita = Auth::user()->id;
                $solicitudT->comentario = strip_tags($request->comentario);                
                $solicitudT->save();

                DB::commit();
                return response()->json(array("exito" => 'Su solicitud ha sido enviada correctamente'));
            }else{
                return response()->json(array("warning" => 'Ya tiene una solicitud de traslado pendiente'));  
            }
 
		} catch(Exception $ex){
            Log::info($ex);
            DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => ""));
		}

        return $historial_ocup;
    }


    public function getTableRecibidas($motivo){
        $tipoUsuario=Auth::user()->tipo;
        $respuesta = [];
       
        $solicitudesTrasladoInterno = SolicitudTrasladoInterno::join('casos', 'solicitud_traslado_interno.caso', '=', 'casos.id')
        ->select("solicitud_traslado_interno.caso","solicitud_traslado_interno.id_unidad_solicitada","solicitud_traslado_interno.usuario_solicita","solicitud_traslado_interno.id_solicitud_traslado_interno","solicitud_traslado_interno.id_historial_ocupaciones","solicitud_traslado_interno.fecha_solicitud","solicitud_traslado_interno.comentario")        
        ->where('casos.establecimiento','=',Auth::user()->establecimiento);
        
        if($motivo == "encurso"){
            $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->whereNULL('solicitud_traslado_interno.fecha_respuesta');
        }
        elseif($motivo == "aceptado"){
            $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->where("solicitud_traslado_interno.solicitud_aceptada","=",true);
        }
        elseif($motivo == "rechazado"){
            $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->where("solicitud_traslado_interno.solicitud_aceptada","=",false);
        }

        $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->get();

        foreach($solicitudesTrasladoInterno as $solicitud){
            $rut_paciente = "";
            $caso = Caso::where("id","=",$solicitud->caso)->first();
            $paciente = Paciente::where("id","=",$caso->paciente)->first();
            $diagnostico = HistorialDiagnostico::where("caso","=",$caso->id)->orderby("id","desc")->first();
            $unidadEnEstablecimiento = UnidadEnEstablecimiento::where("id","=",$solicitud->id_unidad_solicitada)->first();
            $riesgo = EvolucionCaso::where("caso","=",$caso->id)->orderby("id","desc")->first();
            $usuarioSolicita = Usuario::where("id", $solicitud->usuario_solicita)->first();
            $opciones = "";
            if($motivo == "encurso"){
                $opciones = "<div class='dropdown'>
                <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu1' data-toggle='dropdown' aria-expanded='true' style='red'>
                    Opciones 
                    <span class='caret'></span>
                </button>
                <ul class='dropdown-menu dropdown-menu-left' role='menu' aria-labelledby='dropdownMenu1'>";   
                        if($tipoUsuario=='admin' || $tipoUsuario == 'master'
                         // || $tipoUsuario == 'supervisora_de_servicio'
                        ){    
                $opciones .=     "<li role='presentation'><a role='menuitem' tabindex='-1' class='cursor' onclick='asignar($caso->id, $solicitud->id_solicitud_traslado_interno,$solicitud->id_historial_ocupaciones)'>Asignar cama al traslado</a></li>";
                    $opciones.=    "<li role='presentation'><a role='menuitem' tabindex='-1' class='cursor' onclick='cancelar($caso->id, $solicitud->id_solicitud_traslado_interno,$solicitud->id_historial_ocupaciones)'>Cancelar traslado interno</a></li>";
                        }
                $opciones.= "<ul>";
            }
            
            if(isset($paciente->rut)){
                if($paciente->dv == 10){
                    $rut_paciente = $paciente->rut."-K";
                }else{
                    $rut_paciente = $paciente->rut."-".$paciente->dv;
                }
            }

            $solicitud_b = "";
            if($solicitud->fecha_solicitud != ""){
                $solicitud_b = ($solicitud->fecha_solicitud) ? carbon::parse($solicitud->fecha_solicitud)->format('d-m-Y H:i:s') : '';
            }
           
            $tmp_riesgo="";
            if($riesgo->riesgo == null){
                $riesgo->riesgo = "Sin Asignar";
            }else{
                if($riesgo->urgencia == true ){
                    $tmp_riesgo = $riesgo->riesgo." (Urgencia)";
                    $riesgo->riesgo = $tmp_riesgo;
                }
            }

            $establecimiento = Auth::user()->establecimiento;
            $servicio_origen = DB::select(DB::Raw("select unidades_en_establecimientos.alias as nombre_unidad from salas 
            inner join unidades_en_establecimientos on salas.establecimiento = unidades_en_establecimientos.id  
            inner join establecimientos on unidades_en_establecimientos.establecimiento = establecimientos.id 
            inner join camas on salas.id = camas.sala
            inner join t_historial_ocupaciones on camas.id = t_historial_ocupaciones.cama
            inner join solicitud_traslado_interno on t_historial_ocupaciones.id = solicitud_traslado_interno.id_historial_ocupaciones
            where establecimientos.id = $establecimiento
            and solicitud_traslado_interno.id_historial_ocupaciones = $solicitud->id_historial_ocupaciones limit 1;"));
            
            $comentario = ($solicitud->comentario)?"<b>Requerimiento:</b> ".$solicitud->comentario:"";

            $datos = array( $rut_paciente, 
                            $paciente->nombre,
                            $paciente->apellido_paterno." ".$paciente->apellido_materno,
                            ($paciente->fecha_nacimiento) ? carbon::parse($paciente->fecha_nacimiento)->format('d-m-Y') : '',
                            $diagnostico->diagnostico."<br> <b>Comentario: ".$diagnostico->comentario,
                            $solicitud_b,
                            $servicio_origen[0]->nombre_unidad,
                            $unidadEnEstablecimiento->alias."<br>".$comentario,
                            $usuarioSolicita->nombres." ".$usuarioSolicita->apellido_paterno,
                            $riesgo->riesgo,
                            $opciones);
            $respuesta[] = $datos;
        }

        return response()->json(["data"=>$respuesta]);
    }

    public function asignarTraslado(Request $request){//USADA
        $idCaso=$request->idCaso;
        $cama=$request->cama;
        $idLista=$request->idLista;
        $idHistorialOcupacion= $request->idHistorialOcupacion;
        $msj = "";
        $response = [];

        //ultima ubicacion del paciente
        $ubicacion = DB::table('t_historial_ocupaciones as th')
            ->select("c.id_cama","s.nombre","u.alias","th.id")
            ->join("camas as c","c.id","th.cama")
            ->join("salas as s", "s.id","c.sala")
            ->join("unidades_en_establecimientos as u", "u.id","s.establecimiento")
            ->where('th.caso',$idCaso)
            ->whereNull('th.fecha_liberacion')
            ->first();

        $lista_estado = SolicitudTrasladoInterno::where("id_solicitud_traslado_interno", $idLista)->where("solicitud_aceptada",true)->first();
        if ($lista_estado) {
            //si encontro lista de estado, se asume que el usuario tenia una solicitud y esta fue aceptada
            if($ubicacion){
                //significado de que el paciente esta en una cama
                $response = [
                    "info"=> "El paciente ya se le asigno una cama y su ubicación es:",
                    "mensaje" => "Cama: ". $ubicacion->id_cama." Sala: ".$ubicacion->nombre." Unidad: ". $ubicacion->alias 
                ];
            }else{
                //paciente se encuentar egresado
                $response = [
                    "info"=>"El paciente ya se encuentra egresado",
                    "mensaje" => ""
                ];
            }            
        }else{
            //si el paciente aun no se le cierra la solicitud o si fue cancelada

            $pendienteT = SolicitudTrasladoInterno::select("solicitud_aceptada", "fecha_respuesta")
                    ->where("caso",$idCaso)
                    ->whereNull("fecha_respuesta")
                    ->first();
            //Revisar si tiene solicitudes pendientes
            if (!$pendienteT) {
                //dato para comprobar que no tendga solicitud cancelada
                $ultimoPendiente = SolicitudTrasladoInterno::select("solicitud_aceptada", "fecha_respuesta")
                        ->where("caso",$idCaso)
                        ->whereNotNull("fecha_respuesta")
                        ->orderBy("fecha_respuesta","desc")
                        ->first();
                //Hay respuiesta a una solicitud aceptada o rechazada
                $fecha_respuesta = Carbon::parse($ultimoPendiente->fecha_respuesta)->format("d-m-Y H:i");
                //Solicitud Cancelada
                $respuesta_solicitud = "Solicitud Rechazada";
                if ($ultimoPendiente->solicitud_aceptada) {
                    //Solicitud Aceptada
                    $respuesta_solicitud = "Solicitud Aceptada";
                }
                // si tiene solicitud cancelada 
                $response = [
                    "info"=>"La solicitud ya ha sido respondida anteriormente",
                    "mensaje" => $respuesta_solicitud." el ".$fecha_respuesta
                ];
                
            }else if (isset($ubicacion->id) && ($idHistorialOcupacion != $ubicacion->id) ) {
                //Si el paciente fue movido de donde se solicito el traslado
                $response = [
                    "warning"=> "La paciente se movio desde donde se solicito el traslado interno, ¿Desea realizar el cambio igualmente?",
                    "mensaje" => "Actualmente se envuentra en: Cama: ". $ubicacion->id_cama." Sala:".$ubicacion->nombre." Unidad: ". $ubicacion->alias ,
                    "caso" => $idCaso,
                    "camaDestino" => $cama,
                    "idLista" => $idLista
                ];
            }else if(!$ubicacion){
                //paciente se encuentar egresado
                $response = [
                    "info"=>"El paciente ya se encuentra egresado",
                    "mensaje" => ""
                ];

            }else{
                //Si el paciente tiene correcto los datos, se debe verificar que la cama este disponible
                $cama_disponible = DB::table('t_historial_ocupaciones as th')
                    ->select('fecha_liberacion')
                    ->where('cama', '=', $cama)
                    ->whereNull('fecha_liberacion')
                    ->orderBy('fecha', 'desc')
                    ->first();
                
                if($cama_disponible){
                    //Si no se encuentra la cama, significa que ya esta ocupada
                    $response = [
                        "error"=> "Error, la cama ha sido ocupada",
                        "mensaje" => ""
                    ];
                }
                
            }
        }

        if(!empty($response)){
            //si trae algun error
            return response()->json($response);
        }
        
        $respuesta = $this->aceptarTrasladoInterno($idHistorialOcupacion, Auth::user()->id, $idCaso, $cama, $idLista);
        return  response()->json($respuesta);
    }

    public function rechazarTraslado(Request $request){ //USADA

        $idCaso=$request->idCaso;
        $idLista=$request->idLista;
        $idHistorialOcupacion= $request->idHistorialOcupacion;

        //Buscar si la solicitud aun tiene pendientes de ser respondidas
        $pendienteT = SolicitudTrasladoInterno::select("solicitud_aceptada", "fecha_respuesta")
                    ->where("caso",$idCaso)
                    ->whereNull("fecha_respuesta")
                    ->first();

        if (!$pendienteT) {
            $ultimoPendiente = SolicitudTrasladoInterno::select("solicitud_aceptada", "fecha_respuesta")
                    ->where("caso",$idCaso)
                    ->whereNotNull("fecha_respuesta")
                    ->orderBy("fecha_respuesta","desc")
                    ->first();
            //Hay respuiesta a una solicitud aceptada o rechazada
            $fecha_respuesta = Carbon::parse($ultimoPendiente->fecha_respuesta)->format("d-m-Y H:i");
            if ($ultimoPendiente->solicitud_aceptada) {
                //Solicitud Aceptada
                $respuesta_solicitud = "Solicitud Aceptada";
            }else{
                //Solicitud Cancelada
                $respuesta_solicitud = "Solicitud Rechazada";
            }

            return response()->json(["info"=>"La solicitud ya ha sido respondida anteriormente", "mensaje" => $respuesta_solicitud." el ".$fecha_respuesta]);
        }                  

        try{
			DB::beginTransaction();
            
			$lista=SolicitudTrasladoInterno::find($idLista);
			$lista->fecha_respuesta=DB::raw("date_trunc('seconds', now())");
            $lista->usuario_responde = Session::get('usuario')->id;
            $lista->solicitud_aceptada=false;            
			$lista->save();

			DB::commit();
            return response()->json(["exito"=>"Solicitud cancelada"]);
		}catch(Exception $ex){
            Log::info($ex);
			DB::rollback();
            return response()->json(["error"=>"Error al solicitar cancelación"]);
        }
        
        

    }
    
    public function getTableEnviadas($motivo){
        $respuesta = [];

        $solicitudesTrasladoInterno = SolicitudTrasladoInterno::join('casos', 'solicitud_traslado_interno.caso', '=', 'casos.id')
        ->select("solicitud_traslado_interno.caso","solicitud_traslado_interno.id_unidad_solicitada","solicitud_traslado_interno.usuario_solicita","solicitud_traslado_interno.id_solicitud_traslado_interno","solicitud_traslado_interno.id_historial_ocupaciones","solicitud_traslado_interno.fecha_solicitud","solicitud_traslado_interno.comentario")        
        ->where('casos.establecimiento','=',Auth::user()->establecimiento);
        
        if($motivo == "encurso"){
            $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->whereNULL('solicitud_traslado_interno.fecha_respuesta');
        }
        elseif($motivo == "aceptado"){
            $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->where("solicitud_aceptada","=",true);
        }
        elseif($motivo == "rechazado"){
            $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->where("solicitud_aceptada","=",false);
        }

        $solicitudesTrasladoInterno = $solicitudesTrasladoInterno->get();

        foreach($solicitudesTrasladoInterno as $solicitud){
            $rut_paciente = "";
            $caso = Caso::where("id","=",$solicitud->caso)
            ->first();
            
            $paciente = Paciente::where("id","=",$caso->paciente)->first();
            $diagnostico = HistorialDiagnostico::where("caso","=",$caso->id)->orderby("id","desc")->first();
            $unidadEnEstablecimiento = UnidadEnEstablecimiento::where("id","=",$solicitud->id_unidad_solicitada)->first();
            $riesgo = EvolucionCaso::where("caso","=",$caso->id)->orderby("id","desc")->first();
            $usuarioSolicita = Usuario::where("id", $solicitud->usuario_solicita)->first();

            $tmp_riesgo="";
            if($riesgo->riesgo == null){
                $riesgo->riesgo = "Sin Asignar";
            }else{
                if($riesgo->urgencia == true ){
                    $tmp_riesgo = $riesgo->riesgo." (Urgencia)";
                    $riesgo->riesgo = $tmp_riesgo;
                }
            }

            $solicitud_b = "";
            if($solicitud->fecha_solicitud != ""){
                $solicitud_b = ($solicitud->fecha_solicitud) ? carbon::parse($solicitud->fecha_solicitud)->format('d-m-Y H:i:s') : '';
            }

            if(isset($paciente->rut)){
                if($paciente->dv == 10){
                    $rut_paciente = $paciente->rut."-K";
                }else{
                    $rut_paciente = $paciente->rut."-".$paciente->dv;
                }
            }

            $establecimiento = Auth::user()->establecimiento;
            $servicio_origen = DB::select(DB::Raw("select unidades_en_establecimientos.alias as nombre_unidad from salas 
            inner join unidades_en_establecimientos on salas.establecimiento = unidades_en_establecimientos.id  
            inner join establecimientos on unidades_en_establecimientos.establecimiento = establecimientos.id 
            inner join camas on salas.id = camas.sala
            inner join t_historial_ocupaciones on camas.id = t_historial_ocupaciones.cama
            inner join solicitud_traslado_interno on t_historial_ocupaciones.id = solicitud_traslado_interno.id_historial_ocupaciones
            where establecimientos.id = $establecimiento
            and solicitud_traslado_interno.id_historial_ocupaciones = $solicitud->id_historial_ocupaciones limit 1;"));

            $comentario = ($solicitud->comentario)?"<b>Requerimiento:</b> ".$solicitud->comentario:"";

            $datos = array($rut_paciente, 
                           $paciente->nombre. " " .
                           $paciente->apellido_paterno." ".$paciente->apellido_materno,
                            ($paciente->fecha_nacimiento) ? carbon::parse($paciente->fecha_nacimiento)->format('d-m-Y') : '',
                            $diagnostico->diagnostico. "<strong style='color:black'> Comentario: </strong>" . 
                            $diagnostico->comentario,
                            $solicitud_b,
                            $servicio_origen[0]->nombre_unidad,
                            $unidadEnEstablecimiento->alias."<br>".$comentario,
                            $usuarioSolicita->nombres." ".$usuarioSolicita->apellido_paterno,
                            $riesgo->riesgo);
                            
            $respuesta[] = $datos;
           
            $contador = count($respuesta[0]);
          
        }

        return response()->json(["data"=>$respuesta]);
    }

    //contadordetraslados
    public function contadorTraslados(Request $request){    

        $enviadasEnCurso = Consultas::contadorEnviadasEnCurso();
        

        $recibidasEnCurso = Consultas::contadorRecibidasEnCurso();
        
        $resultado = 0;

        $user = Auth::user()->tipo;
        

        if($user === 'admin' || $user === 'director' || $user === 'medico_jefe_servicio' || $user === "admin_ss" || $user === "usuario" || $user === "monitoreo_ssvq" || $user === "admin_iaas" ){
            $resultado = $recibidasEnCurso;
            
        }
        else if($user === 'enfermeraP' || $user === 'gestion_clinica'){
            $resultado = $enviadasEnCurso;
            
        }else if($user === 'master'){
            $resultado = $enviadasEnCurso + $recibidasEnCurso;
        }


        if($enviadasEnCurso){
            return Response::json(array("enviadasEnCurso" => $enviadasEnCurso, "recibidasEnCurso"=>$recibidasEnCurso, "resultado" => $resultado));
            //return Response::json(array("exito" => $resultado, "cEspera"=>$cEspera,"cTransito"=>$cTransito, "cCategorizados"=>$cCate, "cEstudios"=>$cEstudios));
        }else{
            return Response::json(["error" => $enviadasEnCurso]);
        }
    }

    public function confirmarTI(Request $request){    

        DB::beginTransaction();
        try {
            //
            $idCaso = $request->idCaso;
            $usuario_ingresa = Auth::user()->id;

            //Cierra traslado interno//
            $solicitudTrasladoInterno = SolicitudTrasladoInterno::where('caso','=', $idCaso)->orderBy('created_at', 'DESC')->first();
            $solicitudTrasladoInterno->fecha_traslado = Carbon::now()->format("Y-m-d H:i:s");
            $solicitudTrasladoInterno->usuario_responde = $usuario_ingresa;
            $solicitudTrasladoInterno->save();
            //Fin cerrar traslado interno//

            
            $historialActual = THistorialOcupaciones::where('caso','=',$idCaso)->orderBy('id','DESC')->first();
            Log::info($historialActual);

            $historialAnterior = THistorialOcupaciones::where('id','=',$solicitudTrasladoInterno->id_historial_ocupaciones)->first();
            //cama nueva
            
            $historialActual->fecha_ingreso_real = Carbon::parse($historialAnterior->fecha_ingreso_real)->format("Y-m-d H:i:s");
            $historialActual->id_usuario_ingresa = $usuario_ingresa;
            $historialActual->save();

            DB::commit();
            return response()->json(["exito"=>"Cama confirmada"]);
		}catch(Exception $ex){
            Log::info($ex);
			DB::rollback();
            return response()->json(["error"=>"Solicitud cancelada", "msj" => $ex]);
        }
       
    }

    public function confirmarTraslado(Request $request){    //USADA
        $response = [];
        $idCaso = $request->caso;
        $idCamaDestino = $request->cama;
        $idListaSolicitudTI = $request->idlista;
        $usuario = Auth::user()->id;

        $historialActual2 = DB::table('t_historial_ocupaciones as th')
                ->select("th.id", "th.fecha_ingreso_real")
                ->where('th.caso',$idCaso)
                ->whereNull('th.fecha_liberacion')
                ->first();

        //Validar que el paciente aun siga en el hospital
        if (!$historialActual2 ) {
            $response = [
                "info"=>"El paciente ya se encuentra egresado",
                "mensaje" => ""
            ];
        }

        if(!empty($response)){
            //si trae algun error
            return response()->json($response);
        }

        $respuesta = $this->aceptarTrasladoInterno($historialActual2->id, $usuario, $idCaso, $idCamaDestino, $idListaSolicitudTI);
        return response()->json($respuesta);
           
    }


}
