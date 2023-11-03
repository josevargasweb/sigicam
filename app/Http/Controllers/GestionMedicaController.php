<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Auth;
use DB;
use Log;
use Exception;
use Response;
use PDF;

use App\Models\IndicacionMedica;
use App\Models\TiposReposoIndicacionMedica;
use App\Models\FarmacosIndicacionMedica;
use App\Models\ComentariosIndicacionMedica;
use App\Models\Caso;
use App\Models\Procedencia;
use App\Models\HistorialDiagnostico;
use App\Models\Paciente;
use App\Models\Comuna;
use App\Models\Telefono;
use App\Models\Usuario;
use App\Models\PlanificacionIndicacionMedica;
use App\Models\HojaEnfermeriaEnfermeriaIndicacionMedica;
use App\Models\HojaEnfermeriaControlSignoVital;
use App\Models\GesNotificacion;

class GestionMedicaController extends Controller {
    public function getDatosDiagnoticosMedico(Request $request){
        try {
            $caso = $request->caso;
            $idDiagnostico = $request->diagnostico;
            $gesNotificacion = GesNotificacion::where("caso",$caso)
            ->where("id_diagnostico_ges",$idDiagnostico)
			->where('visible', true)
			->get();


			if(count($gesNotificacion) > 0){
				return response()->json(["informacion" => "Este diagnóstico ya esta designado"]);
			}
		
            $historialdiagnostico = HistorialDiagnostico::where("caso",$caso)->where("id",$idDiagnostico)->first();
            $paciente = Paciente::getPacientePorCaso($caso);
            $region = "";
			$comuna = "";
			if($paciente->id_comuna != null){
				$region = Comuna::getRegion($paciente->id_comuna)->id_region;
				$comuna = $paciente->id_comuna;
			}
            $prevision = Caso::find($caso,'prevision');
            $telefonos = Telefono::where('id_paciente',$paciente->id)->get();
           
            return response()->json(array("historialdiagnostico"=>$historialdiagnostico,
            "caso" => $caso,
            "infoPaciente" => $paciente,
            "region"=>$region,
            "comuna"=>$comuna,
            "telefonos" => $telefonos,
            "prevision" => $prevision->prevision)); 
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function agregarIndicacionMedica(Request $request){

        //Validacion usando la fuuncion ya existente
        $request->caso = $request->idCaso;
        $respuesta = $this->validarFechaIndicacion($request);
        $json = json_encode($respuesta);
        $valores = json_decode($json,true);
        if(!$valores["original"]["valid"]){
            return response()->json(["error" => "Ya existe una indicación ingresada para el día seleccionado."]);
        }

        try {
            $idCaso = base64_decode($request->idCaso);
            $idUsuario = Auth::user()->id;
            $fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');

            DB::beginTransaction();
            // indicaciones_medicas
            $indicacion_medica = new IndicacionMedica;
            $indicacion_medica->caso = $idCaso;
            $indicacion_medica->usuario_ingresa = $idUsuario;
            $indicacion_medica->fecha_indicacion_medica = $fecha_creacion;
            $indicacion_medica->fecha_creacion = $fecha_creacion;
            $indicacion_medica->tipo_reposo = $request->tipo_reposo;
            if ($request->grados_semisentado && $request->grados_semisentado != '') {
                $indicacion_medica->grados_semisentado = $request->grados_semisentado;
            }
            $indicacion_medica->otro_reposo = ($request->otro_reposo) ? $request->otro_reposo : null;
            $indicacion_medica->tipo_via = $request->tipo_via;
            $indicacion_medica->detalle_via = ($request->detalle_via) ? $request->detalle_via : null;
            $indicacion_medica->tipo_consistencia = $request->tipo_consistencia;
            $indicacion_medica->detalle_consistencia = ($request->detalle_consistencia) ? $request->detalle_consistencia : null;
            $indicacion_medica->volumen = ($request->volumen) ? $request->volumen : null;
            $indicacion_medica->horas_signos_vitales = ($request->horas_signos_vitales) ? $request->horas_signos_vitales : null;
            $indicacion_medica->detalle_signos_vitales = ($request->detalle_signos_vitales) ? $request->detalle_signos_vitales : null;
            $indicacion_medica->horas_hemoglucotest = ($request->horas_hemoglucotest) ? $request->horas_hemoglucotest : null;
            $indicacion_medica->detalle_hemoglucotest = ($request->detalle_hemoglucotest) ? $request->detalle_hemoglucotest : null;
            $indicacion_medica->oxigeno = ($request->oxigeno) ? $request->oxigeno : null;
            $indicacion_medica->sueros = ($request->sueros == "si") ? true : false;
            $indicacion_medica->suero = ($request->suero) ? $request->suero : null;
            $indicacion_medica->mililitro = ($request->mililitro) ? $request->mililitro : null;
            $indicacion_medica->atencion_terapeutica = ($request->atencion_terapeutica) ? implode(',',$request->atencion_terapeutica) : null;
            $indicacion_medica->visible = true;
            $indicacion_medica->fecha_emision = carbon::parse($request->fecha_emision)->format('Y-m-d H:i:s');
            $indicacion_medica->fecha_vigencia = carbon::parse($request->fecha_vigencia)->format('Y-m-d H:i:s');
            
            if(isset($request->padua) || isset($request->caprini)){
                if($request->padua == "si"){
                    $indicacion_medica->padua = true;
                }elseif($request->padua == "no"){
                    $indicacion_medica->padua = false;
                }
                if($request->caprini == "si"){
                    $indicacion_medica->caprini = true;
                }elseif($request->caprini == "no"){
                    $indicacion_medica->caprini = false;
                }
            }else{
                $indicacion_medica->padua = null;
                $indicacion_medica->caprini = null;
            }
            $indicacion_medica->save();
            
            $tipos = $request->tipos; 
            if($tipos){
                foreach ($tipos as $key => $tipo) {
                    $tipos_reposo_indicacion_medica = new TiposReposoIndicacionMedica;
                    $tipos_reposo_indicacion_medica->im_id = $indicacion_medica->id;
                    $tipos_reposo_indicacion_medica->caso = $idCaso;
                    $tipos_reposo_indicacion_medica->usuario_ingresa = $idUsuario;
                    $tipos_reposo_indicacion_medica->fecha_creacion = $fecha_creacion;
                    $tipos_reposo_indicacion_medica->visible = true;
                    $tipos_reposo_indicacion_medica->tipo = $tipo;
                    $tipos_reposo_indicacion_medica->detalle_tipo = ($tipo == 9) ? $request->detalle_tipo_otro : null;
                    $tipos_reposo_indicacion_medica->save();
                }
            }
            $farmacos = $request->nombre_farmaco; 
            if($farmacos){
                foreach ($farmacos as $key => $farmaco) {
                    if (isset($request->nombre_farmaco[$key]) && $request->nombre_farmaco[$key] != '') {
                        //Se valida que al menos tenga un farmaco
                        $farmacos_indicacion_medica = new FarmacosIndicacionMedica;
                        $farmacos_indicacion_medica->im_id = $indicacion_medica->id;
                        $farmacos_indicacion_medica->caso = $idCaso;
                        $farmacos_indicacion_medica->usuario_ingresa = $idUsuario;
                        $farmacos_indicacion_medica->fecha_creacion = $fecha_creacion;
                        $farmacos_indicacion_medica->visible = true;
                        $farmacos_indicacion_medica->id_farmaco = $request->nombre_farmaco[$key];
                        $farmacos_indicacion_medica->via_administracion = $request->via_administracion[$key];
                        $farmacos_indicacion_medica->intervalo_farmaco = (isset($request->intervalo_farmaco[$key]) && $request->intervalo_farmaco[$key] != '')?$request->intervalo_farmaco[$key]:null;
                        $farmacos_indicacion_medica->detalle_farmaco = $request->detalle_farmaco[$key];
                        $farmacos_indicacion_medica->save();
                    }
                    
                }
            }

            //comentarios_indicacion_medica
            $comentarioExtra = $request->campoExtra;
            if($comentarioExtra){
                foreach ($comentarioExtra as $key => $comentario) {
                    $comentarios_indicacion_medica = new ComentariosIndicacionMedica;
                    $comentarios_indicacion_medica->im_id = $indicacion_medica->id;
                    $comentarios_indicacion_medica->caso = $idCaso;
                    $comentarios_indicacion_medica->usuario_ingresa = $idUsuario;
                    $comentarios_indicacion_medica->fecha_creacion = $fecha_creacion;
                    $comentarios_indicacion_medica->visible = true;
                    $comentarios_indicacion_medica->comentario = $comentario;
                    $comentarios_indicacion_medica->save();
                }
            }
            
            DB::commit();
            return response()->json(array("exito"=>"Indicación médica ingresada correctamente."));
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            return response()->json(["error" => "Error al ingresar indicación médica"]);
        }
    }

    public function ultimaIndicacion(Request $request){
        try {
            $caso = base64_decode($request->caso);
            $fecha_actual = Carbon::now()->format('Y-m-d');
            $ultimaIndicacion = IndicacionMedica::where("caso",$caso)->where('visible',true)->whereDate('fecha_emision',$fecha_actual)->orderBy('id','desc')->first();
            $tipos_reposo = [];
            $farmacos = [];
            $comentarios = [];
            if($ultimaIndicacion){
                $tipos_reposo = $ultimaIndicacion->tipos_reposo;
                $farmacos = $ultimaIndicacion->farmacos;
                $comentarios = $ultimaIndicacion->comentarios;
            }
           
            return response()->json(array(
                "ultimaIndicacion"=>$ultimaIndicacion, 
                "tipos_reposo" => $tipos_reposo, 
                "farmacos" => $farmacos, 
                "comentarios" => $comentarios 
            )); 
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function cargarIndicaciones(Request $request){
        try {
            $caso = base64_decode($request->caso);
            $fecha_actual = Carbon::now()->format('Y-m-d');
            //Mostrar indicaciones activas independiente dse la fecha
            $indicaciones = IndicacionMedica::where("caso",$caso)
                    ->where('visible',true)
                    //->where("fecha_emision",)
                    //->whereDate('fecha_emision',$fecha_actual)
                    ->orderBy('fecha_emision','asc')
                    ->get();

            $resultado = [];
            if($indicaciones){
                foreach ($indicaciones as $key => $indicacion) {
                    $num_indicacion = $key + 1;
                    //$fecha_creacion = $indicacion["fecha_indicacion_medica"];
                    $fecha_creacion = Carbon::parse($indicacion["fecha_emision"])->format("d-m-Y");
                    $fecha_vigencia = Carbon::parse($indicacion["fecha_vigencia"])->format("d-m-Y");
                    $fecha_creacion_orden = Carbon::parse($indicacion["fecha_emision"]);
                    
                    $datos_usuario = Usuario::findOrFail($indicacion["usuario_ingresa"]);
                    $usuario_responsable = ($datos_usuario) ? $datos_usuario->nombres . " " .$datos_usuario->apellido_paterno . " " . $datos_usuario->apellido_materno : 'Sin información';
                    $tipo_reposo = IndicacionMedica::reposo($indicacion["id"],$indicacion["tipo_reposo"]);
                    $tipo_regimen = IndicacionMedica::regimen($indicacion["id"],$indicacion["tipo_via"]);
                    $horas_signos_vitales = $indicacion["horas_signos_vitales"];
                    $control_signos_vitales = ($indicacion["horas_signos_vitales"]) ? "cada {$horas_signos_vitales} horas" : "Sin información";
                    $horas_hemoglucotest = $indicacion["horas_hemoglucotest"];
                    $control_hemoglucotest = ($indicacion["horas_hemoglucotest"]) ? "cada {$horas_hemoglucotest} horas" : "Sin información";
                    $oxigeno = ($indicacion["oxigeno"]) ? $indicacion["oxigeno"] ."%" : "Sin información"; 
                    $sueros = ($indicacion["sueros"]) ? "Si" : "No";

                    $cantidad_farmacos = count($indicacion["farmacos"]);
                    if($cantidad_farmacos == 1){
                        $farmacos = "{$cantidad_farmacos} asignado";
                    }else if($cantidad_farmacos > 1){
                        $farmacos = "{$cantidad_farmacos} asignados";
                    }else{
                        $farmacos = "Sin información";
                    }

                    $atencion_terapeutica = ($indicacion["atencion_terapeutica"]) ? explode(",",$indicacion["atencion_terapeutica"]) : "";
                    $atenciones = ($atencion_terapeutica) ? count($atencion_terapeutica) : 0;
                    if($atenciones == 1){
                        $atencion = "{$atenciones} seleccionada";
                    }else if($atenciones > 1){
                        $atencion = "{$atenciones} seleccionadas";
                    }else{
                        $atencion = "Sin información";
                    }

                    $cantidad_comentarios_indicacion = count($indicacion["comentarios"]);
                    if($cantidad_comentarios_indicacion == 1){
                        $comentarios = "{$cantidad_comentarios_indicacion} comentario";
                    }else if($cantidad_comentarios_indicacion > 1){
                        $comentarios = "{$cantidad_comentarios_indicacion} comentarios";
                    }else{
                        $comentarios = "Sin información";
                    } 

                    $fechas_rango = "<label>Fecha de emisión: ".$fecha_creacion."</label><br>";
                    if ($fecha_creacion != $fecha_vigencia) {
                        $fechas_rango = "<label>Fecha de emisión: ".$fecha_creacion."</label><br>
                            <label>Fecha de vigencia: ".$fecha_vigencia."</label><br>";
                    }
                    $resultado [] = [
                        "<div>
                            <div hidden>$fecha_creacion_orden</div>
                            <label>Indicación N°: ".$num_indicacion."</label><br>
                            ".$fechas_rango."
                            <label>Usuario responsable: ".$usuario_responsable."</label>
                        </div>",
                        "<div>                            
                            <label>Reposo: ".$tipo_reposo."</label><br>
                            <label>Regimen: ".$tipo_regimen."</label><br>
                            <label>Control signos vitales: ".$control_signos_vitales."</label><br>
                            <label>Control hemoglucotest: ".$control_hemoglucotest."</label><br>
                            <label>Oxigeno para saturar: ".$oxigeno."</label><br>
                            <label>Suero: ".$sueros."</label><br>
                            <label>Farmacos: ".$farmacos."</label><br>
                            <label>Atención terapeutica: ".$atencion."</label><br>
                            <label>Comentarios: ".$comentarios."</label>
                        </div>",
                        "<div>
                            <button class='btn btn-primary' onClick='verEditarIndicacion(".$indicacion["caso"].",".$indicacion["id"].")'>Ver / Editar</button><br><br>
                            <button class='btn btn-danger' onClick='eliminarIndicacion(".$indicacion["id"].")'>Eliminar</button><br><br>
                            <button class='btn btn-success' onClick='gestionarComentariosIndicacion(".$indicacion["id"].")'>Comentarios</button>
                        </div>"
                    ];
                }
            }
            return response()->json(["aaData" => $resultado]);
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function cargarIndicacionMedica($id){
        try {
            $indicacion = IndicacionMedica::findOrFail($id);
            $pauda_caprini = IndicacionMedica::where('caso',$indicacion->caso)
                ->where('visible',true)
                ->whereNotNull('padua')
                ->whereNotNull('caprini')
                ->count();
            $tipos_reposo = [];
            $farmacos = [];
            $comentarios = [];
            if($indicacion){
                $tipos_reposo = $indicacion->tipos_reposo;
                $farmacos = $indicacion->farmacos->where('visible',true);
                $comentarios = $indicacion->comentarios->where('visible',true);
            }
           
            return response()->json([
                "indicacion" => $indicacion, 
                "tipos_reposo" => $tipos_reposo, 
                "farmacos" => $farmacos, 
                "comentarios" => $comentarios, 
                "pauda_caprini" => $pauda_caprini
            ]); 

        } catch (\Throwable $th) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function editarIndicacionMedica(Request $request){
        try {
            DB::beginTransaction();
            $idCaso = base64_decode($request->idCaso_);
            $id_indicacion = $request->idIndicacion;
            $falsear_indicacion = IndicacionMedica::findOrFail($id_indicacion);
            $usuario_habilitado = $falsear_indicacion->usuario_ingresa;
            $id_usuario_logeado = Auth::user()->id;
            $fecha = Carbon::now()->format('Y-m-d H:i:s'); 
            if($usuario_habilitado == $id_usuario_logeado){

                //comparar si tiene cambios.
                $resultado_indicacion = $this->compararIndicacion($request);
                if($resultado_indicacion == "nada"){
                    return response()->json(["error" => "La indicación no ha recibido cambios."]);
                }

                //falsear
                $falsear = IndicacionMedica::falso($falsear_indicacion,$fecha);

                if(isset($request->padua_) || isset($request->caprini_)){
                    if($request->padua_ == "si"){
                        $padua = true;
                    }elseif($request->padua_ == "no"){
                        $padua = false;
                    }
                    if($request->caprini_ == "si"){
                        $caprini = true;
                    }elseif($request->caprini_ == "no"){
                        $caprini = false;
                    }
                }else{
                    $padua = null;
                    $caprini = null;
                }

                // nuevo
                $data_indicacion = [
                    // "id" => ($id_indicacion) ? $id_indicacion : null,
                    "caso" => ($idCaso) ? $idCaso : null,
                    "tipo_reposo" => ($request->tipo_reposo_) ? $request->tipo_reposo_ : null,
                    "grados_semisentado" => ($request->grados_semisentado_ && $request->grados_semisentado_ != '') ? $request->grados_semisentado_ : null,
                    "otro_reposo" => ($request->otro_reposo_) ? $request->otro_reposo_ : null,
                    "tipo_via" => ($request->tipo_via_) ? $request->tipo_via_ : null,
                    "detalle_via" => ($request->detalle_via_) ? $request->detalle_via_ : null,
                    "tipo_consistencia" => ($request->tipo_consistencia_) ? $request->tipo_consistencia_ : null,
                    "detalle_consistencia" => ($request->detalle_consistencia_) ? $request->detalle_consistencia_ : null,
                    "volumen" => ($request->volumen_) ? $request->volumen_ : null,
                    "horas_signos_vitales" => ($request->horas_signos_vitales_) ? $request->horas_signos_vitales_ : null,
                    "detalle_signos_vitales" => ($request->detalle_signos_vitales_) ? $request->detalle_signos_vitales_ : null,
                    "horas_hemoglucotest" => ($request->horas_hemoglucotest_) ? $request->horas_hemoglucotest_ : null,
                    "detalle_hemoglucotest" => ($request->detalle_hemoglucotest_) ? $request->detalle_hemoglucotest_ : null,
                    "sueros" => ($request->sueros_ == "si") ? true : false,
                    "suero" => ($request->suero_) ? $request->suero_ : null,
                    "mililitro" => ($request->mililitro_) ? $request->mililitro_ : null,
                    "atencion_terapeutica" => ($request->atencion_terapeutica_) ? implode(',',$request->atencion_terapeutica_) : null,
                    "oxigeno" => ($request->oxigeno_) ? $request->oxigeno_ : null,
                    "fecha_emision" => ($request->fecha_emision_) ? Carbon::parse($request->fecha_emision_)->format('Y-m-d H:i:s') : null,
                    "fecha_vigencia" => ($request->fecha_vigencia_) ? Carbon::parse($request->fecha_vigencia_)->format('Y-m-d H:i:s') : null,
                    "padua" => $padua,
                    "caprini" => $caprini
                ];


                if(count($request->nombre_farmaco_) == 1  && $request->nombre_farmaco_[0] == ""){
                    Log::info('entrando');
                    PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                    ->where("visible", true)
                    ->where("tipo", "Farmacos")
                    ->update([
                        'usuario_modifica' => Auth::user()->id,
                        'fecha_modificacion' => $fecha,
                        'visible' => false,
                        'tipo_modificacion' => 'Eliminado'
                    ]);
                }
                
                if($request->horas_signos_vitales_ == ""){
                    PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                    ->where("visible", true)
                    ->where("tipo", "Control de signos vitales")
                    ->update([
                        'usuario_modifica' => Auth::user()->id,
                        'fecha_modificacion' => $fecha,
                        'visible' => false,
                        'tipo_modificacion' => 'Eliminado'
                    ]);
                }
              
                if($request->horas_hemoglucotest_ == ""){
                    PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                    ->where("visible", true)
                    ->where("tipo", "Control de hemoglucotest")
                    ->update([
                        'usuario_modifica' => Auth::user()->id,
                        'fecha_modificacion' => $fecha,
                        'visible' => false,
                        'tipo_modificacion' => 'Eliminado'
                    ]);
                }

                if($request->sueros_ == "no"){
                    PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                    ->where("visible", true)
                    ->where("tipo", "Suero")
                    ->update([
                        'usuario_modifica' => Auth::user()->id,
                        'fecha_modificacion' => $fecha,
                        'visible' => false,
                        'tipo_modificacion' => 'Eliminado'
                    ]);
                }

                $nueva = IndicacionMedica::nuevo($data_indicacion,$falsear,$fecha);
                $nueva->visible = true;
                $nueva->save();


                $tipos_ = $request->tipos_;
                $datos_tipos = []; 
                if($tipos_){
                    foreach ($tipos_ as $key => $tv) {
                        $datos_tipos = [
                            'caso' => ($idCaso) ? $idCaso : null,
                            'im_id' => ($id_indicacion) ? $id_indicacion : null, 
                            'tipo' => ($tv) ? $tv : null
                        ];

                        if ($tv == 9) {
                            //Solo en caso de que la opcion sea Otro, esta debe tener el detalle para comparar
                            $datos_tipos ['detalle_tipo'] = (isset($request->detalle_tipo_otro_) && $request->detalle_tipo_otro_)? $request->detalle_tipo_otro_: null;
                        }

                        if($tv){
                            $resp = TiposReposoIndicacionMedica::comparar($tv,$datos_tipos);
                            $info_tipo_falso = [
                                'caso' => $idCaso,
                                'im_id' => $id_indicacion,
                                'tipo' => $request->tipos_[$key]
                            ];
                            $info_tipo_nuevo = [
                                'caso' => $idCaso,
                                'im_id' => $id_indicacion,
                                'tipo' => $request->tipos_[$key]
                            ];

                            if ($tv == 9) {
                                //Solo en caso de que la opcion sea Otro, esta debe tener el detalle para comparar
                                $info_tipo_falso ['detalle_tipo'] = $request->detalle_tipo_otro_;
                                $info_tipo_nuevo ['detalle_tipo'] = $request->detalle_tipo_otro_;
                            }

                            if($resp == "cambios"){
                                //Se comenta la anterior 
                                $falso = TiposReposoIndicacionMedica::falso($info_tipo_falso,$fecha,null);
                                if(!$falso){
                                    // Se crea un nuevo reposo de indicacion medica
                                    $actualizado = TiposReposoIndicacionMedica::nuevo($info_tipo_nuevo,$fecha,$nueva->id);
                                    $actualizado->visible = true;
                                    $actualizado->save();
                                }
                            }else{
                                // Nueva indicacion de tipo reposo
                                $actualizado = TiposReposoIndicacionMedica::nuevo($info_tipo_nuevo,$fecha,$nueva->id);
                                $actualizado->visible = true;
                                $actualizado->save();
                            }
                        }
                    }
                }
                $datos_bd = $falsear_indicacion->tipos_reposo->where('visible',true);
                $arreglo_tipo_reposos = [];
                foreach ($datos_bd->toArray() as $key => $d_bd) {
                    $arreglo_tipo_reposos[] = $d_bd["tipo"];
                }

                foreach ($arreglo_tipo_reposos as $key => $value) {
                    $eliminar = TiposReposoIndicacionMedica::where('caso',$idCaso)
						->where('im_id',$id_indicacion)
						->where('tipo',$value)
						->where('visible',true)
						->first();

                    $comparar = TiposReposoIndicacionMedica::compararVista($eliminar->tipo,$tipos_);
                    if($comparar == false){
                        $info_tipo_eliminar = [
                            'caso' => $eliminar->caso,
                            'im_id' => $eliminar->im_id,
                            'tipo' => $eliminar->tipo
                        ];

                        $eliminado = TiposReposoIndicacionMedica::falso($info_tipo_eliminar,$fecha,$eliminar->id);
                    }
                }
                
                // FARMACOS
                $falsear_farmacos = $falsear_indicacion->farmacos->where('visible',true);
                $array_farmacos = [];
                foreach ($falsear_farmacos as $key => $farmaco) {
                    $array_farmacos [] = "$farmaco->id";
                }

                $farmacos_vista = $request->id_farmaco_;
                $buscando_id_farmaco = "";
                $buscando_id_farmaco_anterior = "";
                $array_buscando_id_farmaco = array();
                $datos = [];
                foreach ($farmacos_vista as $key => $fv) {
                    $datos = [
                        "id" => $fv,
                        "im_id" => $id_indicacion,
                        "caso" => $idCaso,
                        "id_farmaco" => $request->nombre_farmaco_[$key],
                        "via_administracion" => $request->via_administracion_[$key],
                        "intervalo_farmaco" => $request->intervalo_farmaco_[$key],
                        "detalle_farmaco" => ($request->detalle_farmaco_[$key]) ? $request->detalle_farmaco_[$key] : ""
                    ];

                    if($fv){
                        $resp = FarmacosIndicacionMedica::comparar($fv,$datos);
                        if($resp == "cambios"){
                            // FALSO.
                            $falseado = FarmacosIndicacionMedica::find($fv);
                            $falsear = FarmacosIndicacionMedica::falso($falseado,$fecha);

                            // NUEVO CON LOS CAMBIOS ACTUALIZADOS.
                            if ($datos["id_farmaco"] != "") {
                                $actualizado = FarmacosIndicacionMedica::nuevo($datos,$fecha,$nueva->id);
                                $actualizado->visible = true;
                                $actualizado->save();

                                $buscando_id_farmaco = $actualizado->id;
                            }

                        }else{
                            // FALSO.
                            $falseado = FarmacosIndicacionMedica::find($fv);
                            $falsear = FarmacosIndicacionMedica::falso($falseado,$fecha);

                            // NUEVO CON LOS DATOS DEL ANTERIOR.
                            if ($falsear["id_farmaco"] != "") {
                                $nuevo = FarmacosIndicacionMedica::nuevo($falsear,$fecha,$nueva->id);
                                $nuevo->visible = true;
                                $nuevo->save();

                                $buscando_id_farmaco = $nuevo->id;
                            }


                        }
                        $buscando_id_farmaco_anterior = $fv;
                    }else{
                        // NUEVO DESDE LA VISTA.
                        if ($datos["id_farmaco"] != "") {
                            $nuevo_farmaco = FarmacosIndicacionMedica::nuevo($datos,$fecha,$nueva->id);
                            $nuevo_farmaco->visible = true;
                            $nuevo_farmaco->save();

                            $buscando_id_farmaco = $nuevo_farmaco->id;
                        }
                        $buscando_id_farmaco_anterior = "";
                    }
                    //rellena los cambios hechos para los farmacos
                    $array_buscando_id_farmaco[] = array(
                        "id_anterior" => $buscando_id_farmaco_anterior,
                        "id_nueva" => $buscando_id_farmaco
                    );
                   
                }
                Log::info('buscando farmacos');
                Log::info($array_buscando_id_farmaco);
                //busca los farmacos nuevos y los modifica en la planificacion
                if(!empty($array_buscando_id_farmaco)){
                    foreach ($array_buscando_id_farmaco as $key => $farmaco) {
                        if($farmaco['id_anterior'] != ""){
                            $indicaciones_old_farmaco = PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                               ->where('caso',$idCaso)
                               ->where('id_farmaco',$farmaco['id_anterior'])
                               ->where("visible", true)
                               ->get();
           
                               if(!empty($indicaciones_old_farmaco)){
                                    PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                                    ->where('caso',$idCaso)
                                    ->where('id_farmaco',$farmaco['id_anterior'])
                                    ->where("visible", true)
                                    ->update([
                                        'id_farmaco' => $farmaco['id_nueva']
                                    ]);

                               }
                        }
                    }
                }



                //suspender
                // Log::info("suspender");
                foreach ($array_farmacos as $key => $value) {
                    $comparar = in_array($value,$farmacos_vista);
                    if($comparar == false){
                        $editado = FarmacosIndicacionMedica::find($value);
                        // FALSO SUSPENDIDO (QUITADO EN LA VISTA).
                        $suspender = FarmacosIndicacionMedica::suspender($editado,$fecha);
                        if($suspender){
                            $suspender_farmaco_planificacion = PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                            ->where('caso',$idCaso)
                            ->where('id_farmaco',$suspender->id)
                            ->where("visible", true)
                            ->first();
        
                            if(!empty($suspender_farmaco_planificacion)){
                                $suspender_farmaco_planificacion->usuario_modifica = Auth::user()->id;
                                $suspender_farmaco_planificacion->fecha_modificacion = $fecha;
                                $suspender_farmaco_planificacion->visible = false;
                                $suspender_farmaco_planificacion->tipo_modificacion ='Eliminado';
                                $suspender_farmaco_planificacion->save();
                            }
                        }
                    }
                }
                // FARMACOS


                // COMENTARIOS
                $falsear_comentarios = $falsear_indicacion->comentarios->where('visible',true);
                $array_comentarios = [];
                foreach ($falsear_comentarios as $key => $comentario) {
                    $array_comentarios [] = "$comentario->id";
                }

                $comentarios_vista = $request->id_comentario_;
                $datos_comentario = [];
                foreach ($comentarios_vista as $key => $cv) {
                    $datos_comentario = [
                        "id" => $cv,
                        "im_id" => $id_indicacion,
                        "caso" => $idCaso,
                        "comentario" => $request->campoExtra_[$key]
                    ];

                    if($cv){
                        $resp = ComentariosIndicacionMedica::comparar($cv,$datos_comentario);
                        if($resp == "cambios"){
                            // FALSO EDITADO.
                            $falseado = ComentariosIndicacionMedica::find($cv);
                            $falso = ComentariosIndicacionMedica::falso($falseado,$fecha);

                            // NUEVO CON LOS CAMBIOS ACTUALIZADOS.
                            $actualizado = ComentariosIndicacionMedica::nuevo($datos_comentario,$fecha,$nueva->id);
                            $actualizado->visible = true;
                            $actualizado->save();
                        }else{
                            $falseado = ComentariosIndicacionMedica::find($cv);
                            // FALSO.
                            $falso = ComentariosIndicacionMedica::falso($falseado,$fecha);

                             // NUEVO CON LOS DATOS DEL ANTERIOR.
                            $actual = ComentariosIndicacionMedica::nuevo($falso,$fecha,$nueva->id);
                            $actual->visible = true;
                            $actual->save();
                        }
                    }else{
                        // NUEVO DESDE LA VISTA.
                        $nuevo_comentario = ComentariosIndicacionMedica::nuevo($data_comentarios,$fecha,$nueva->id);
                        $nuevo_comentario->visible = true;
                        $nuevo_comentario->save();
                    }
                }

                // eliminar
                foreach ($array_comentarios as $key => $value) {
                    $comparar = in_array($value,$comentarios_vista);
                    if($comparar == false){
                        $editado = ComentariosIndicacionMedica::find($value);
                        // FALSO SUSPENDIDO (QUITADO EN LA VISTA).
                        $eliminar = ComentariosIndicacionMedica::eliminar($editado,$fecha);
                    }
                }
               

                //indicaciones medics rce
                   $indicaciones_old = PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                    ->where('caso',$idCaso)
                    ->where("visible", true)
                    ->get();


                    if(!empty($indicaciones_old)){

                        PlanificacionIndicacionMedica::where('id_indicacion',$id_indicacion)
                        ->where('caso',$idCaso)
                        ->where("visible", true)
                        ->update([
                            'usuario_modifica' => Auth::user()->id,
                            'fecha_modificacion' => $fecha,
                            'visible' => false,
                            'tipo_modificacion' => 'Eliminado'
                        ]);
                        foreach($indicaciones_old as $indicacion_old){
                            $indicacion_new = new PlanificacionIndicacionMedica();
                            $indicacion_new->caso = $indicacion_old->caso;
                            $indicacion_new->id_indicacion = $nueva->id;
                            $indicacion_new->tipo = $indicacion_old->tipo; 
                            $indicacion_new->id_farmaco = $indicacion_old->id_farmaco;
                            $indicacion_new->fecha_emision = $indicacion_old->fecha_emision;
                            $indicacion_new->fecha_vigencia = $indicacion_old->fecha_vigencia;
                            $indicacion_new->responsable = $indicacion_old->responsable;
                            $indicacion_new->horario = $indicacion_old->horario;
                            $indicacion_new->usuario = $indicacion_old->usuario;
                            $indicacion_new->visible = true;
                            $indicacion_new->fecha_creacion = $indicacion_old->fecha_creacion;
                            $indicacion_new->id_anterior = $indicacion_old->id; 
                            $indicacion_new->save();

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
                      
    
                        
                   
                    }

                // COMENTARIOS
            }else{
                return response()->json(["error" => "La indicación medica solo puede ser editada por el medico que la creo."]);
            }
            DB::commit();
            return response()->json(["exito" => "Indicación medica actualizada exitosamente."]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al actualizar la indicación medica"]);
        }
    }

    public function compararIndicacion($request){
        // INDICACION

        $idCaso = base64_decode($request->idCaso_);
        $id_indicacion = $request->idIndicacion;
        $data_indicacion = [
            "id" => ($id_indicacion) ? $id_indicacion : null,
            "caso" => ($idCaso) ? $idCaso : null,
            "tipo_reposo" => ($request->tipo_reposo_) ? $request->tipo_reposo_ : null,
            "grados_semisentado" => ($request->grados_semisentado_ && $request->grados_semisentado_ != '') ? $request->grados_semisentado_ : null,
            "otro_reposo" => ($request->otro_reposo_) ? $request->otro_reposo_ : null,
            "tipo_via" => ($request->tipo_via_) ? $request->tipo_via_ : null,
            "detalle_via" => ($request->detalle_via_) ? $request->detalle_via_ : null,
            "tipo_consistencia" => ($request->tipo_consistencia_) ? $request->tipo_consistencia_ : null,
            "detalle_consistencia" => ($request->detalle_consistencia_) ? $request->detalle_consistencia_ : null,
            "volumen" => ($request->volumen_) ? $request->volumen_ : null,
            "horas_signos_vitales" => ($request->horas_signos_vitales_) ? $request->horas_signos_vitales_ : null,
            "detalle_signos_vitales" => ($request->detalle_signos_vitales_) ? $request->detalle_signos_vitales_ : null,
            "horas_hemoglucotest" => ($request->horas_hemoglucotest_) ? $request->horas_hemoglucotest_ : null,
            "detalle_hemoglucotest" => ($request->detalle_hemoglucotest_) ? $request->detalle_hemoglucotest_ : null,
            "sueros" => ($request->sueros_ == "si") ? true : false,
            "suero" => ($request->suero_) ? $request->suero_ : null,
            "mililitro" => ($request->mililitro_) ? $request->mililitro_ : null,
            "atencion_terapeutica" => ($request->atencion_terapeutica_) ? implode(',',$request->atencion_terapeutica_) : null,
            "oxigeno" => ($request->oxigeno_) ? $request->oxigeno_ : null,
            "fecha_emision" => ($request->fecha_emision_) ? Carbon::parse($request->fecha_emision_)->format('Y-m-d H:i:s') : null,
            "fecha_vigencia" => ($request->fecha_vigencia_) ? Carbon::parse($request->fecha_vigencia_)->format('Y-m-d H:i:s') : null,
            "padua" => (isset($indicacion_medica->padua_) && $indicacion_medica->padua_ == 'si') ? true : (isset($indicacion_medica->padua_) && $indicacion_medica->padua_ == 'no') ? false : null,
            "caprini" => (isset($indicacion_medica->caprini_) && $indicacion_medica->caprini_ == 'si') ? true : (isset($indicacion_medica->caprini_) && $indicacion_medica->caprini_ == 'no') ? false : null 
        ];

        $editadoOriginalIndicacion = IndicacionMedica::find($id_indicacion);
        $editadoIndicacion = $editadoOriginalIndicacion->only([
            'id',
            'caso',
            'tipo_reposo',
            'grados_semisentado',
            'otro_reposo',
            'tipo_via',
            'detalle_via',
            'tipo_consistencia',
            'detalle_consistencia',
            'volumen',
            'horas_signos_vitales',
            'detalle_signos_vitales',
            'horas_hemoglucotest',
            'detalle_hemoglucotest',
            'sueros',
            'suero',
            'mililitro',
            'atencion_terapeutica',
            'oxigeno',
            'fecha_emision',
            'fecha_vigencia',
            'padua',
            'caprini'
        ]);

        $resultado_indicacion = array_diff_assoc($data_indicacion, $editadoIndicacion);
        if(empty($resultado_indicacion)){
            $resp_indicacion = "nada";
        }else{
            $resp_indicacion = "cambios";
        }

        // TIPOS REPOSO
        //comparar vista
        $tipos_ = $request->tipos_;
        $data_tipos_reposo = [];
        $resp_tipos_vista = [];
        foreach ($tipos_ as $key => $tipo) {
            if($tipo){
                $data_tipos_reposo = [
                    'caso' => ($idCaso) ? $idCaso : null,
                    'im_id' => ($id_indicacion) ? $id_indicacion : null,
                    'tipo' => ($request->tipos_[$key]) ? $request->tipos_[$key] : null,
                    'detalle_tipo' => (isset($request->detalle_tipo_otro_) && $request->detalle_tipo_otro_)? $request->detalle_tipo_otro_: null
                ];
                $resp_tipos_vista[]  = TiposReposoIndicacionMedica::comparar($tipo,$data_tipos_reposo);
            }else{
                $resp_tipos_vista[] = "nada";
            }
        }
        // comparar bd
        $falsear = IndicacionMedica::findOrFail($id_indicacion);
        $datos_bd = $falsear->tipos_reposo->where('visible', true);
        $arreglo_tipos_reposos = [];
        $resp_tipos_bd = [];
        foreach ($datos_bd as $key => $value) {
            $arreglo_tipos_reposos[] = $value["tipo"];
        }

        foreach ($arreglo_tipos_reposos as $key => $value) {
            $eliminar = TiposReposoIndicacionMedica::where('caso',$idCaso)
            ->where('im_id',$id_indicacion)
            ->where('tipo',$value)
            ->where('visible',true)
            ->first();

            $comparar_tipos_bd = TiposReposoIndicacionMedica::compararVista($eliminar->tipo,$tipos_);
            if($comparar_tipos_bd == false){
                $resp_tipos_bd[] = "cambios";
            }else{
                $resp_tipos_bd[] = "nada";
            }
        }
        // TIPOS REPOSO

        // FARMACOS
        // comparar vista
        $farmacos_vista = $request->id_farmaco_;
        $data_farmacos = [];
        $resp_farmacos = [];
        foreach ($farmacos_vista as $key => $fv) {
            if($fv){
                $data_farmacos = [
                    "id" => ($fv) ? $fv : null,
                    "im_id" => ($id_indicacion) ? $id_indicacion : null,
                    "caso" => ($idCaso) ? $idCaso : null,
                    "id_farmaco" => ($request->nombre_farmaco_[$key]) ? $request->nombre_farmaco_[$key] : null,
                    "via_administracion" => ($request->via_administracion_[$key]) ? $request->via_administracion_[$key] : null,
                    "intervalo_farmaco" => ($request->intervalo_farmaco_[$key]) ? $request->intervalo_farmaco_[$key] : null,
                    "detalle_farmaco" => ($request->detalle_farmaco_[$key]) ? $request->detalle_farmaco_[$key] : null
                ];

                $comparar_farmacos = FarmacosIndicacionMedica::comparar($fv,$data_farmacos);
                if($comparar_farmacos == "cambios"){
                    $resp_farmacos[] = $comparar_farmacos;
                }else{
                    $resp_farmacos[] = "nada";
                }
            }else{
                $resp_farmacos[] = "nada";
            }
        }

        // comparar bd
        $falsear_farmacos = $falsear->farmacos;
        $falsear_farmacos = $falsear_farmacos->where('visible', true);
        $array_farmacos = [];
        $resp_farmacos_vista = [];
        $resp_farmacos_bd = [];
        foreach ($falsear_farmacos as $key => $farmaco) {
            $array_farmacos [] = "$farmaco->id";
        }
        
        foreach ($array_farmacos as $key => $value) {
            $comparar = in_array($value,$farmacos_vista);
            if($comparar == false){
                $resp_farmacos_bd[] = "cambios";
            }else{
                $resp_farmacos_bd[] = "nada";
            }
        }
        // FARMACOS
        
        // COMENTARIOS
        // comparar vista
        $comentarios_vista = $request->id_comentario_;
        $data_comentarios = [];
        $resp_comentarios_vista = [];
        foreach ($comentarios_vista as $key => $cv) {
            if($cv){
                $data_comentarios = [
                    "id" => $cv,
                    "im_id" => $id_indicacion,
                    "caso" => $idCaso,
                    "comentario" => $request->campoExtra_[$key]
                ];

                $comparar_comentarios = ComentariosIndicacionMedica::comparar($cv,$data_comentarios);
                if($comparar_comentarios == "cambios"){
                    $resp_comentarios_vista[] = $comparar_comentarios;
                }else{
                    $resp_comentarios_vista[] = "nada";
                }
            }else{
                $resp_comentarios[] = "nada";
            }
        }

        // comparar bd
        $falsear_comentarios = $falsear->comentarios->where('visible', true);
        $array_comentarios = [];
        $resp_comentarios_bd = [];
        foreach ($falsear_comentarios as $key => $comentario) {
            $array_comentarios [] = "$comentario->id";
        }

        foreach ($array_comentarios as $key => $value) {
            $comparar = in_array($value,$comentarios_vista);
            if($comparar == false){
                $resp_comentarios_vista[] = "cambios";
            }else{
                $resp_comentarios_vista[] = "nada";
            }
        }
        // comprobando
        // INDICACION
        $respuesta_final = [];
        if($resp_indicacion == "cambios"){
            $respuesta_final[] = ($resp_indicacion) ? $resp_indicacion : "nada";
        }

        // REPOSO
        $resp_reposo_vista = in_array("cambios",$resp_tipos_vista);
        $respuesta_final[] = ($resp_reposo_vista == true) ? "cambios" : "nada";
        
        $resp_reposo_bd = in_array("cambios",$resp_tipos_bd);
        $respuesta_final[] = ($resp_reposo_bd == true) ? "cambios" : "nada";

        // FARMACOS
        $resp_farmacos_vista = in_array("cambios", $resp_farmacos_vista);
        $respuesta_final[]= ($resp_farmacos_vista == true) ? "cambios" : "nada";
        
        $resp_farmacos_bd = in_array("cambios",$resp_farmacos_bd);
        $respuesta_final[]= ($resp_farmacos == true) ? "cambios" : "nada";
        
        // COMENTARIOS
        $resp_comentarios_vista = in_array("cambios",$resp_comentarios_vista);
        $respuesta_final[] = ($resp_comentarios_vista == true) ? "cambios" : "nada";

        $resp_comentarios_bd = in_array("cambios",$resp_comentarios_bd);
        $respuesta_final[] = ($resp_comentarios_bd == true) ? "cambios" : "nada";

        $resultado = in_array("cambios",$respuesta_final);
        
        return ($resultado) ? "cambios" : "nada";
    }

    public function validarFechaIndicacion (Request $request){
        $caso = base64_decode($request->caso);
        $fecha_emision = "";
        $fecha_vigencia = $request->fecha_vigencia;
        if (isset($request->fecha_emision)) {
            $fecha_emision = $request->fecha_emision;
        }
        if (isset($request->fecha_emision_)) {
            $fecha_emision = $request->fecha_emision_;
        }

        //Fecha de emision que intentan asignar
        $fecha_emision_consulta = Carbon::parse($fecha_emision)->startOfDay();
        //format('Y-m-d');
        $fecha_vigencia_consulta = Carbon::parse($fecha_vigencia)->endOfDay();
        //format('Y-m-d');
        /* $inicio = Carbon::parse($request->fecha_emision)->startOfDay();
        $fin = Carbon::parse($request->fecha_emision)->endOfDay(); */

        //OPCIONES
        //Si la fecha de emision se encuentra dentro del rango
        //O si la fecha de vigencia se encuentra dentro del rango
        //Esto indica que tiene alguna fecha dentro de este rango

        $registro_diario = IndicacionMedica::select("id", "caso","fecha_emision", "fecha_vigencia")
            ->where('caso',$caso)
            ->where('visible',true)
            ->where(function($q) use ($fecha_emision_consulta,$fecha_vigencia_consulta){
                //Si la fecha de emision se encuentra dentro del rango
                $q->where(function($q1) use ($fecha_emision_consulta,$fecha_vigencia_consulta){
                    $q1->where('fecha_emision', '>=', $fecha_emision_consulta)
                    ->Where('fecha_emision', '<=', $fecha_vigencia_consulta);
                })
                ->orWhere(function($q2) use ($fecha_emision_consulta,$fecha_vigencia_consulta){
                    //Si la fecha de vigencia se encuentra dentro del rango
                    $q2->where('fecha_vigencia', '>=', $fecha_emision_consulta)
                    ->Where('fecha_vigencia', '<=', $fecha_vigencia_consulta);
                });
            })
            //->whereDate('fecha_emision',$fecha)
            //->between($inicio, $fin)
            ->get();
            Log::info($registro_diario);
        if($registro_diario->count()){
            return response()->json(["valid" => false, "message" => "Ya existe una indicación ingresada para el rango o día seleccionado. Diríjase al historial de indicaciones para poder consultar"]);
        }else{
            return response()->json(["valid" => true]);
        }     
    }

    public function validarFechaIndicacionActualizar (Request $request){
        //Esta funciona cuando cambia la emision en la parte de edicion de indicaciones
        $caso = base64_decode($request->caso);
        //$fecha_emision = Carbon::parse($request->fecha_emision_)->format('Y-m-d');
        $fecha_emision = "";
        $fecha_vigencia = $request->fecha_vigencia;
        if (isset($request->fecha_emision)) {
            $fecha_emision = $request->fecha_emision;
        }
        if (isset($request->fecha_emision_)) {
            $fecha_emision = $request->fecha_emision_;
        }

        //Fecha de emision que intentan asignar
        $fecha_emision_consulta = Carbon::parse($fecha_emision)->startOfDay();
        //format('Y-m-d');
        $fecha_vigencia_consulta = Carbon::parse($fecha_vigencia)->endOfDay();
        //format('Y-m-d');
        

        //Encontrar si es que hay indicaciones en la fecha que se esta consultando, omitiendo que sea el mismo id de la indicacion actual

        $registros = IndicacionMedica::where('caso',$caso)
            ->where('visible',true)
            //->whereDate('fecha_emision', $fecha_emision)
            ->where('id','<>',$request->indicacion)
            ->where(function($q) use ($fecha_emision_consulta,$fecha_vigencia_consulta){
                //Si la fecha de emision se encuentra dentro del rango
                $q->where(function($q1) use ($fecha_emision_consulta,$fecha_vigencia_consulta){
                    $q1->where('fecha_emision', '>=', $fecha_emision_consulta)
                    ->Where('fecha_emision', '<=', $fecha_vigencia_consulta);
                })
                ->orWhere(function($q2) use ($fecha_emision_consulta,$fecha_vigencia_consulta){
                    //Si la fecha de vigencia se encuentra dentro del rango
                    $q2->where('fecha_vigencia', '>=', $fecha_emision_consulta)
                    ->Where('fecha_vigencia', '<=', $fecha_vigencia_consulta);
                });
            })
            ->get();

        $valid = [];
        $message = "Ya existe una indicación ingresada para el día seleccionado.";
        if($registros->count()){
            return response()->json(["valid" => false, "message" => "Ya existe una indicación ingresada para el rango o día seleccionado. Diríjase al historial de indicaciones para poder consultar"]);
        }else{
            return response()->json(["valid" => true]);
        }  
        /* 
        if($registros->count()){
            //Si encuentra parametros
            foreach ($registros as $registro) {
                $fecha = Carbon::parse($registro->fecha_emision)->format('Y-m-d');
                if($fecha == $fecha_emision){
                    $valid[] = false;
                }else{
                    $valid[] = true;
                }
            }
        }else{
            //Si no enontro parametros asociado a la fecha
            $valid[] = true;
        }

        if (in_array(false, $valid)) {
            return response()->json(["valid" => false, "message" => $message]);
        }else{
            return response()->json(["valid" => true]);
        }     */ 
    }

    public function indicacionDiaActual (Request $request){
        $caso = base64_decode($request->caso);
        $fecha_actual = Carbon::now()->format('Y-m-d');
        $ultimaIndicacion = IndicacionMedica::where("caso",$caso)->where('visible',true)->whereDate('fecha_emision','<=',$fecha_actual)->whereDate('fecha_vigencia','>=',$fecha_actual)->orderBy('id','desc')->first();
        if($ultimaIndicacion){
            return response()->json(array("existe"=>true, "id_indicacion" => $ultimaIndicacion->id)); 
        }else{
            return response()->json(array("existe"=>false));
        }
    }

    public function eliminarIndicacion($id){
        try {
            DB::beginTransaction();
            $fecha = Carbon::now()->format('Y-m-d H:i:s');
            $eliminar = IndicacionMedica::find($id);

            if($eliminar){
                $eliminado = IndicacionMedica::eliminar($eliminar,$fecha);
                $tipos = $eliminar->tipos_reposo;
                // ->where('visible',true);
                if($tipos){
                    foreach ($tipos as $tipo) {
                        if($tipo->visible){
                            $eliminado = TiposReposoIndicacionMedica::eliminar($tipo,$fecha);
                        }
                    }
                }
                $farmacos = $eliminar->farmacos;
                // ->where('visible',true);
                if($farmacos){
                    foreach ($farmacos as $key => $farmaco) {
                        if($farmaco->visible){
                            $eliminado = FarmacosIndicacionMedica::eliminar($farmaco,$fecha);
                        }
                    }
                }
                $comentarios = $eliminar->comentarios;
                // ->where('visible',true);
                if($comentarios){
                    foreach ($comentarios as $key => $comentario) {
                        if($comentario->visible){
                            $eliminado = ComentariosIndicacionMedica::eliminar($comentario,$fecha);
                        }
                    }
                }

                PlanificacionIndicacionMedica::where('id_indicacion',$eliminar->id)->update([
                    'usuario_modifica' => Auth::user()->id,
                    'fecha_modificacion' => $fecha,
                    'visible' => false,
                    'tipo_modificacion' => 'Eliminado'
                ]);

                DB::commit();
                return response()->json(array("exito" => "La indicación ha sido eliminada exitosamente.")); 
            }else{
                return response()->json(array("error" => "No existe información sobre la indicación. <br> La información será actualizada."));
            }
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(array("error"=> "Error al eliminar la indicación."));
        }
    }

    public function consultaPrimeraIndicacion(Request $request){
        try {
            $caso = base64_decode($request->caso);
            $indicaciones = IndicacionMedica::where('caso',$caso)
            ->where('visible',true)
            ->count();
            
            $primera = ($indicaciones > 0) ? false : true;
            
            return response()->json(["primera" => $primera]);
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(["error" => $ex]);
        }    
    }

    public function pdfResumenIndicaciones(Request $request){
        $caso = base64_decode($request->caso);
        $fechames = $request->fecha;
        try {
            $now = Carbon::parse($fechames);                                       //fecha actual
            $inicio_mes_actual = $now->copy()->startOfMonth();                        //inicio del mes actual primerDia-xx-xxxx 00:00:00
            $final_mes_actual = $now->copy()->endOfMonth();          //final del mes actual. ultimoDia-xx-xxxx 23:59:59
            $mes_anterior = $inicio_mes_actual->copy()->subWeek(1);                   //ultima semana mes anterior.
            $mes_siguiente = $final_mes_actual->copy()->addWeek(1);  //primera semana mes siguiente.
            
            $indicaciones = IndicacionMedica::where(function($q) use($inicio_mes_actual,$final_mes_actual){
                $q->where(function($q) use($inicio_mes_actual){
                    $q->where('fecha_emision', '<', $inicio_mes_actual) 
                    ->where('fecha_vigencia', '>=', $inicio_mes_actual);
               })
                ->orwhere(function($q) use ($inicio_mes_actual,$final_mes_actual){
                    $q->where('fecha_emision', '>=', $inicio_mes_actual) 
                    ->where('fecha_vigencia', '<=', $final_mes_actual);
                })
                ->orwhere(function($q) use ($final_mes_actual){
                    $q->where('fecha_emision', '<', $final_mes_actual) 
                    ->where('fecha_vigencia', '>', $final_mes_actual);
                });
            })
            ->where('visible', true)
            ->where('caso',$caso)
            ->orderBy('fecha_emision','asc')
            ->get();

            $info_reposo = ["Reposo"];
            $detalle_reposo = [];
            $info_regimen = ["Régimen"];
            $info_signos_vitales = ["Signos vitales"];
            $detalle_signos_vitales = [];
            $info_hemoglucotest = ["Hemoglucotest"];
            $detalle_hemoglucotest = [];
            $info_oxigeno = ["Oxigeno"];
            $detalle_oxigeno = [];
            $info_suero = ["Suero"];
            $detalle_suero = [];
            $info_farmacos = ["Farmacos"];
            $detalle_farmaco = [];
            $info_atencion_terapeutica = ["Atención terapeutica"];
            $detalle_atencion_terapeutica = [];
            $info_prevension_trombosis = ["Prevension trombosis"];
            $detalle_prevension_trombosis = [];
            $info_indicaciones = [];

            $hoy = $now->copy();
            $dias_del_mes = $hoy->daysInMonth;
            for ($i=0; $i < $dias_del_mes; $i++) { 
                array_push($info_reposo, "");
                array_push($info_regimen, "");
                array_push($info_signos_vitales, "");
                array_push($info_hemoglucotest, "");
                array_push($info_oxigeno, "");
                array_push($info_suero, "");
                array_push($info_farmacos, "");
                array_push($info_atencion_terapeutica, "");
                array_push($info_prevension_trombosis, "");
            }

            foreach ($indicaciones as $key => $indicacion) {
                $variable_emision_indicacion = Carbon::parse($indicacion->fecha_emision);
                $variable_emision_indicacion2 = Carbon::parse($indicacion->fecha_emision);
                $variable_vigencia_indicacion = Carbon::parse($indicacion->fecha_vigencia);
                
                for ($i=$variable_emision_indicacion; $i <= $variable_vigencia_indicacion; $variable_emision_indicacion->addDays(1)) { 
                    if($i >= $inicio_mes_actual && $i <= $final_mes_actual){
                        $tipo_reposo = TiposReposoIndicacionMedica::primeraLetraTipoReposo($indicacion->tipo_reposo);
                        $dia = Carbon::parse($i)->format('d');
                        if($tipo_reposo){
                            $info_reposo[(int)$dia] = "x";
                            $detalle_reposo[(int)$dia] = $tipo_reposo;
                        }

                        // regimen es obligatorio.
                        $info_regimen[(int)$dia] = "x";

                        $signos_vitales = ($indicacion->horas_signos_vitales) ? $indicacion->horas_signos_vitales : "";
                        if($signos_vitales){
                            $info_signos_vitales[(int)$dia] = "x";
                            $detalle_signos_vitales[(int)$dia] = $signos_vitales;
                        }
                        
                        $oxigeno = ($indicacion->oxigeno) ? $indicacion->oxigeno : "";
                        if($oxigeno){
                            $info_oxigeno[(int)$dia] = "x";
                            $detalle_oxigeno[(int)$dia] = $oxigeno;
                        }
                        
                        $hemoglucotest = ($indicacion->horas_hemoglucotest) ? $indicacion->horas_hemoglucotest : "";
                        if($hemoglucotest){
                            $info_hemoglucotest[(int)$dia] = "x";
                            $detalle_hemoglucotest[(int)$dia] = $hemoglucotest;
                        }
                        
                        $suero = ($indicacion->suero) ? $indicacion->suero : "";
                        if($suero){
                            $mililitro = ($indicacion->mililitro) ? $indicacion->mililitro : 0; 
                            $info_suero[(int)$dia] = "x";
                            $detalle_suero[(int)$dia] = $mililitro;
                        } 
                        
                        $farmacos = $indicacion->farmacos->where('visible',true);
                        $farmacos = (count($farmacos)) ? count($farmacos) : false;
                        if($farmacos){
                            $info_farmacos[(int)$dia] = "x";
                            $detalle_farmaco[(int)$dia] = $farmacos;
                        }    


                        $atencion_terapeutica = ($indicacion->atencion_terapeutica) ? explode(",",$indicacion->atencion_terapeutica) : [];
                        $atencion_terapeutica = count($atencion_terapeutica);
                        if($atencion_terapeutica){
                            $info_atencion_terapeutica[(int)$dia] = "x";
                            $detalle_atencion_terapeutica[(int)$dia] = $atencion_terapeutica;
                        }
                        
                        $prevension_trombosis = "";
                        if($indicacion->padua != null || $indicacion->caprini != null){
                            if($indicacion->padua == true && $indicacion->caprini == false ){
                                $prevension_trombosis = "P";
                            }

                            if($indicacion->padua == false && $indicacion->caprini == true ){
                                $prevension_trombosis = "C";
                            }

                            if($indicacion->padua == true && $indicacion->caprini == true ){
                                $prevension_trombosis = "P/C";
                            }
                        }else{
                            $prevension_trombosis = "N/A";
                        }
                        $info_prevension_trombosis[(int)$dia] = "x";
                        $detalle_prevension_trombosis[(int)$dia] = $prevension_trombosis;
                    }
                }

                $info_indicaciones[] = [
                    "fecha_inicio" => $variable_emision_indicacion2->format('d-m-Y'),
                    "fecha_vigencia" => $variable_vigencia_indicacion->format('d-m-Y'),
                    "usuario" => "{$indicacion->usuario_ingreso->nombres} {$indicacion->usuario_ingreso->apellido_paterno} {$indicacion->usuario_ingreso->apellido_materno}",
                ];
            }

            $paciente = Paciente::getPacientePorCaso($caso);
            $prevision = Caso::find($caso,'prevision');
            $telefonos = Telefono::where('id_paciente',$paciente->id)->get();

            // return 'oka';

            $pdf = PDF::loadView("Gestion/gestionMedica/Pdf/pdfResumenPlanificacionIndicaciones", [
				"fecha" => $hoy->format("d/m/Y"),
                "paciente" => $paciente,
                "prevision" => $prevision->prevision,
                "telefonos" => $telefonos,
                "dias_del_mes" => $dias_del_mes,
                "info_reposo" => $info_reposo,
                "detalle_reposo" => $detalle_reposo,
                "info_regimen" => $info_regimen,
                "info_signos_vitales" => $info_signos_vitales,
                "detalle_signos_vitales" => $detalle_signos_vitales,
                "info_oxigeno" => $info_oxigeno,
                "detalle_oxigeno" => $detalle_oxigeno,
                "info_hemoglucotest" => $info_hemoglucotest,
                "detalle_hemoglucotest" => $detalle_hemoglucotest,
                "info_suero" => $info_suero,
                "detalle_suero" => $detalle_suero,
                "info_farmacos" => $info_farmacos,
                "detalle_farmaco" => $detalle_farmaco,
                "info_atencion_terapeutica" => $info_atencion_terapeutica,
                "detalle_atencion_terapeutica" => $detalle_atencion_terapeutica,
                "info_prevension_trombosis" => $info_prevension_trombosis,
                "detalle_prevension_trombosis" => $detalle_prevension_trombosis,
                "info_indicaciones" => $info_indicaciones,
            ]);

			return $pdf->setPaper('legal', 'landscape')->stream('f.pdf');
        } catch (Exception $e) {
           return response()->json($e->getMessage());
        }
    }
    public function editarDiagnostico(Request $request){
        try {
            DB::beginTransaction();

            $idDiagn = base64_decode($request->idDiagn);
            $comentario = strip_tags($request->comDiagModal);
            //Comprobar que efectivamente sean diferentes textos
            $diagnostico = HistorialDiagnostico::where("id", $idDiagn)->first();
            if ($diagnostico->comentario == $comentario) {
                //Si es lo mismo, mandar un mensaje que el comentari ode diagnostico no ha sido modificado
                return response()->json(["info" => "El comentario que esta guardando no ha tenido cambios"]);
            }
            $diagnostico->comentario = $comentario;
            $diagnostico->save();
            
            DB::commit();
            return response()->json(array("exito"=>"Diagnostico medico editado correctamente."));
        }catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            return response()->json(["error" => "Error al editar comentario del diagnostico"]);
        } 
    }

    public function ingresarDiagnostico(Request $request){
        try {
            DB::beginTransaction();

            $caso = base64_decode($request->caso);

            $diagnosticos = $request->diagnosticos;
            $hidden_diagnosticos = $request->hidden_diagnosticos;
            $fecha_ingreso = Carbon::now()->format("Y-m-d H:i:s");
            $comentario_diagnostico = $request->input("nuevo-diagnostico");

            $r=DB::table(DB::raw("(SELECT * FROM diagnosticos
                WHERE caso=$caso
                AND fecha::DATE=CURRENT_DATE)AS a
                order by fecha desc"))
                ->first();

            if($r && $request->motivo == ''){
                return response()->json(array("motivo"=>"Ya se ha guardado un diagnóstico hoy, debe ingresar el motivo"));
            }

            foreach ($diagnosticos as $key => $value) {
                if($value != "null" ){
                    $d = new HistorialDiagnostico();
                    $d->caso = $caso;
                    $d->fecha =$fecha_ingreso;
                    $d->diagnostico = $value;
                    $d->id_cie_10 = $hidden_diagnosticos[$key];
                    $d->id_usuario = Auth::user()->id;
                    $d->comentario = $comentario_diagnostico[$key];
                    $d->motivo = (isset($request->motivo))?$request->motivo:null;
                    $d->save();
                }
            }
            DB::commit();
            return response()->json(array("exito"=>"Diagnostico medico editado correctamente."));
        }catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            return response()->json(["error" => "Error al editar comentario del diagnostico"]);
        } 
    }
}
    