<?php

namespace App\Http\Controllers;

use App\Models\ExamenMedico;
use App\Models\Caso;
use App\Models\Procedencia;
use App\Models\HistorialDiagnostico;
use App\Models\CirugiaPreviaExamenMedico;
use App\Models\ProyeccionExamenMedico;
use App\Models\Paciente;
use App\Models\Usuario;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use Log;
use Exception;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExamenMedicoController extends Controller
{

    public function infoPacienteExamen(Request $request){
        try {
            $caso = base64_decode($request->caso);
            $infoPacienteExamen = [];
            $infoUbicacion = [];
            $datos_procedencia = Caso::find($caso,["id","procedencia","detalle_procedencia","requiere_aislamiento","paciente"]);

            if($datos_procedencia){
                $nombre_procedencia = Procedencia::find($datos_procedencia->procedencia,["nombre"]);
                $detalle_procedencia = ($datos_procedencia->detalle_procedencia) ? "({$datos_procedencia->detalle_procedencia})" : "";
                $procedencia = ($nombre_procedencia->nombre) ? "{$nombre_procedencia->nombre} {$detalle_procedencia}" : null;
                
                $paciente = Paciente::find($datos_procedencia->paciente); 
                $rut = $paciente->rut ? $paciente->rut : "";
                $dv = ($paciente->dv && $paciente->dv == 10) ? "k" : $paciente->dv;
                $infoAuthUsuario = Auth::user();
                $nombre_medico_solicitante = "{$infoAuthUsuario->nombres} {$infoAuthUsuario->apellido_paterno} {$infoAuthUsuario->apellido_materno}";
                $infoPacienteExamen = [
                    "id" => $datos_procedencia->id,
                    "nombre" => "{$paciente->nombre} {$paciente->apellido_paterno} {$paciente->apellido_materno}",
                    "rut" => "{$rut}-{$dv}",
                    "fecha_nacimiento" => ($paciente->fecha_nacimiento) ? Carbon::parse($paciente->fecha_nacimiento)->format('d-m-Y') : "Sin información",
                    "edad" => ($paciente->fecha_nacimiento) ? Carbon::parse($paciente->fecha_nacimiento)->age : "Sin Información",
                    "procedencia" => $procedencia,
                    "requiere_aislamiento" => $datos_procedencia->requiere_aislamiento,
                    "nombre_medico_solicitante" => $nombre_medico_solicitante,
                ];

                $diagnosticos = HistorialDiagnostico::where('caso',$caso)
                                ->select("diagnostico")
                                ->get();

                $cama = DB::table('t_historial_ocupaciones as t')
                ->join("camas as c", "c.id", "=", "t.cama")
                ->join("salas as s", "c.sala", "=", "s.id")
                ->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
                ->where("caso", "=", $caso)
                ->orderBy("t.created_at", "desc")
                ->select("cama", "c.id_cama as id", "s.nombre as sala_nombre", "uee.alias as nombre_unidad")
                ->first();
                if($cama){
                    $servicio = $cama->nombre_unidad;				
                    $detalleCama = $cama->sala_nombre." (".$cama->id.")";
                    $infoUbicacion = [
                        "servicio" => $servicio,
                        "detalleCama" => $detalleCama
                    ];
                }

                return response()->json(array("infoPacienteExamen" => $infoPacienteExamen,"infoUbicacion" => $infoUbicacion,"diagnosticos" => $diagnosticos)); 
            }else{
                return response()->json(array("error"=>"No se ha encontrado la información"));
            }
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>"Error..."));
        }
    }

    public function agregarExamenMedico(Request $request){
        Log::info($request);                    
        try {
            $opciones_examen_imagenologia = $request->examenes_imagenologia;
            $proyecciones = isset($request->proyecciones) ? $request->proyecciones : [];
            // $validador = Validator::make($request->all(), [
            //     'especialidad_paciente' => 'required',
            //     'cirugia_previa.*' => 'required',
                
            //     // tomografia
            //     'examen_solicitado' => Rule::requiredIf(in_array("1",$opciones_examen_imagenologia)),
            //     'contraste' => Rule::requiredIf(in_array("1",$opciones_examen_imagenologia)),
            //     'creatininemia' => Rule::requiredIf($request->contraste == 'si'),
            //     'fecha_contraste' => Rule::requiredIf($request->contraste == 'si'),
                
            //     // ecografia
            //     'ecografia' => Rule::requiredIf(in_array("2",$opciones_examen_imagenologia)),

            //     //radiografia
            //     'radiografia' => 'required',
            //     'proyecciones' => 'required',
            //     'comentario_proyeccion' => Rule::requiredIf(in_array("4",$proyecciones)),
                
            //     //otro examen
            //     'otro_examen' => 'required',
            // ],[
            //     'especialidad_paciente.required' => 'Debe ingresar una especialidad.',
            //     'cirugia_previa.required' => 'Debe ingresar la cirugia.',
            //     'examen_solicitado.required' => 'Debe ingresar un examen.',
            //     'contraste.required' => 'Debe seleccionar contraste.',
            //     'creatininemia.required' => 'Debe ingresar la creatininemia.',
            //     'fecha_contraste.required' => 'Debe ingresar la fecha de contraste.',
            //     'ecografia.required' => 'Debe ingresar una ecografia.',
            //     'radiografia.required' => 'Debe ingresar una radiografia.',
            //     'proyecciones.required' => 'Debe seleccionar al menos una proyeccion.',
            //     'comentario_proyeccion.required' => 'Debe ingresar un comentario de proyeccion.',
            //     'otro_examen.required' => 'Debe seleccionar otro examen.',
            // ]);

            // if($validador->fails()){
            //     return response()->json(['errores' => $validador->errors()->all()]);
            // }
            DB::beginTransaction();
            
            $examen_medico = new ExamenMedico;

            if($request->idExamenImagenologia){
                $falsear = ExamenMedico::where('id',$request->idExamenImagenologia)->where('visible',true)->first();
                if($falsear){
                    $falsear->visible = false;
                    $falsear->estado = "Editado";
                    $falsear->usuario_modifica = Auth::user()->id;
                    $falsear->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                    $falsear->save();
    
                    $ids_cirugias = $request->id_cirugia;
                    $eliminar_cirugias = $falsear->cirugias_previas->where('visible',true);
                    if($eliminar_cirugias){
                        foreach($eliminar_cirugias as $cirugia){
                            $cirugia->usuario_modifica = Auth::user()->id;
                            $cirugia->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                            $cirugia->visible = false;
                            $cirugia->estado = (in_array($cirugia->id,$ids_cirugias)) ? "Editado" : "Eliminado";
                            $cirugia->save();
                        }
                    }
    
                    $proyecciones = $falsear->proyecciones->where('visible',true);
                    if($proyecciones){
                        foreach ($proyecciones as $proyeccion){
                            $proyeccion->visible = false;
                            $proyeccion->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                            $proyeccion->usuario_modifica = Auth::user()->id;
                            $proyeccion->estado = "Editado";
                            $proyeccion->save();
                        }
                    }
    
                    $fecha_creacion = $falsear->fecha_creacion;
                    $examen_medico->id_anterior = $falsear->id;
                }else{
                    return response()->json(["error" => "Este formulario ya ha sido modificado o el formulario no se encuentra. <br>La información será actualizada."]);
                }

            }else{
                $fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
            }

            $idCaso = base64_decode($request->idCaso);
            $usuario_logeado = Auth::user()->id;

            $examen_medico->caso = $idCaso;

            if($request->aislamiento_paciente == 'si'){
                $examen_medico->aislamiento_paciente = true;
            }else if($request->aislamiento_paciente == 'no'){
                $examen_medico->aislamiento_paciente = false;
            }else{
                $examen_medico->aislamiento_paciente = null;
            }
            
            if($request->posibilidad_embarazo_paciente == 'si'){
                $examen_medico->posibilidad_embarazo = true;
            }else if($request->posibilidad_embarazo_paciente == 'no'){
                $examen_medico->posibilidad_embarazo = false;
            }else{
                $examen_medico->posibilidad_embarazo = null;
            }

            if($request->aislamiento_paciente == 'si'){
                $examen_medico->medidas_aislamiento = true;
            }else if($request->aislamiento_paciente == 'no'){
                $examen_medico->medidas_aislamiento = false;
            }else{
                $examen_medico->medidas_aislamiento = null;
            }

            $examen_medico->especialidad_paciente = $request->especialidad_paciente ? strip_tags($request->especialidad_paciente) : null;
            
            $examen_medico->opciones_examen_imagenologia = implode(',',$opciones_examen_imagenologia);
            if(in_array(0,$opciones_examen_imagenologia)){ 
                $examen_medico->examen_solicitado = $request->examen_solicitado ? strip_tags($request->examen_solicitado) : null;
                if($request->contraste == 'si'){
                    $examen_medico->contraste = true;
                }else if($request->contraste == 'no'){
                    $examen_medico->contraste = false;
                }else{
                    $examen_medico->contraste = null;
                }
                $examen_medico->creatininemia = ($request->contraste == 'si' && $request->creatininemia) ? $request->creatininemia : null;
                $examen_medico->fecha_contraste = ($request->contraste == 'si' && $request->fecha_contraste) ? Carbon::parse($request->fecha_contraste)->format('Y-m-d H:i:s') : null;
                $examen_medico->comentario_examen = $request->comentario_examen ? strip_tags($request->comentario_examen) : null;
            }else{
                $examen_medico->examen_solicitado = null;
                $examen_medico->contraste = null;
                $examen_medico->creatininemia = null;
                $examen_medico->fecha_contraste = null;
                $examen_medico->comentario_examen = null;
            }   

            // ECOGRAFIA                
            if(in_array(1, $opciones_examen_imagenologia)){
                $examen_medico->ecografia = $request->ecografia ? strip_tags($request->ecografia) : null;
                if($request->ecografia_doppler == 'si'){
                    $examen_medico->ecografia_doppler = true;
                    if($request->extremidades == 'venoso'){
                        $examen_medico->extremidades = true;
                    }else if($request->extremidades == 'arterial'){
                        $examen_medico->extremidades = false;
                    }else{
                        $examen_medico->extremidades = null;
                    }
    
                    if($request->lado_ecografia == 'derecho'){
                        $examen_medico->lado_ecografia = true;
                    }else if($request->extremidades == 'izquierda'){
                        $examen_medico->lado_ecografia = false;
                    }else{
                        $examen_medico->lado_ecografia = null;
                    }
                }else if($request->ecografia_doppler == 'no'){
                    $examen_medico->ecografia_doppler = false;
                    $examen_medico->extremidades = null;
                    $examen_medico->lado_ecografia = null;
                }
            }else{
                $examen_medico->ecografia = null;
                $examen_medico->ecografia_doppler = null;
                $examen_medico->extremidades = null;
                $examen_medico->lado_ecografia = null;
            }

            // RADIOGRAFIA
            if(in_array(2,$opciones_examen_imagenologia)){
                $examen_medico->radiografia = $request->radiografia ? strip_tags($request->radiografia) : null;
                if($request->lado_radiografia == 'derecho'){
                    $examen_medico->lado_radiografia = true;
                }else if($request->lado_radiografia == 'izquierda'){
                    $examen_medico->lado_radiografia = false;
                }else{
                    $examen_medico->lado_radiografia = null;
                }

                // 
            }else{
                $examen_medico->radiografia = null;
                $examen_medico->lado_radiografia = null;
            }

            // OTRO EXAMEN
            if(in_array(3,$opciones_examen_imagenologia)){
                $examen_medico->otro_examen = $request->otro_examen ? strip_tags($request->otro_examen) : null;
                $examen_medico->especificar_examen = $request->especificar_examen ? strip_tags($request->especificar_examen) : null;
            }else{
                $examen_medico->otro_examen = null;
                $examen_medico->especificar_examen = null;
            }
            
            $examen_medico->usuario_ingresa = $usuario_logeado;
            $examen_medico->fecha_creacion = $fecha_creacion;
            $examen_medico->visible = true;
            $examen_medico->save();

            if(in_array(2,$opciones_examen_imagenologia)){
                $proyecciones = $request->proyecciones;
                if($proyecciones){
                    foreach ($proyecciones as $proyeccion) {
                        $proyeccion_medica = new ProyeccionExamenMedico;
                        $proyeccion_medica->examen_medico_id = $examen_medico->id;
                        $proyeccion_medica->caso = $idCaso;
                        $proyeccion_medica->usuario_ingresa = $usuario_logeado;
                        $proyeccion_medica->fecha_creacion = $fecha_creacion;
                        $proyeccion_medica->visible = true;
                        $proyeccion_medica->proyeccion = $proyeccion;
                        $proyeccion_medica->comentario_proyeccion = ($proyeccion == 4) ? $request->comentario_proyeccion : null;
                        $proyeccion_medica->save();
                    }
                }
            }

            $opcion_cirugias_previas = $request->cirugias_previas;
            if($opcion_cirugias_previas == "si"){
                $cirugias_previas = $request->cirugia_previa;
                if($cirugias_previas){
                    foreach ($cirugias_previas as $cirugia) {
                        $comentarios_indicacion_medica = new CirugiaPreviaExamenMedico;
                        $comentarios_indicacion_medica->examen_medico_id = $examen_medico->id;
                        $comentarios_indicacion_medica->caso = $idCaso;
                        $comentarios_indicacion_medica->usuario_ingresa = $usuario_logeado;
                        $comentarios_indicacion_medica->fecha_creacion = $fecha_creacion;
                        $comentarios_indicacion_medica->visible = true;
                        $comentarios_indicacion_medica->cirugia_previa = $cirugia;
                        $comentarios_indicacion_medica->save();
                    }
                }
            }
            DB::commit();
            return response()->json(array("exito"=>"Examen médico ingresado correctamente."));
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            return response()->json(["error" => "Error al ingresar examen médico."]);
        }
    }

    public function listarExamenesMedicos(Request $request){
        try {
            $caso = base64_decode($request->caso);
            $examenesMedicos = ExamenMedico::where('caso',$caso)
                                ->where('visible',true)
                                ->orderBy('id','desc')
                                ->get();

            $resultado = [];
            if($examenesMedicos){
                foreach ($examenesMedicos as $key => $examen){
                    $fecha_creacion = ($examen["fecha_creacion"]) ? Carbon::parse($examen["fecha_creacion"])->format('d-m-Y H:i:s') : "Sin información";
                    $datos_usuario = Usuario::findOrFail($examen["usuario_ingresa"]);
                    $usuario_responsable = ($datos_usuario) ? $datos_usuario->nombres . " " .$datos_usuario->apellido_paterno . " " . $datos_usuario->apellido_materno : 'Sin información';
                    $opciones = "<div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn-xs btn-warning' onclick='editarExamenImageneologia(".$examen->id.")'>Modificar</button>
                    </div>
                    <br><br>
                    <div class='btn-group' role='group' aria-label='...'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarExamenImageneologia(".$examen->id.")'>Eliminar</button>
                    </div>";
                    $resultado [] = [$opciones,$usuario_responsable,$fecha_creacion];
                }
            }

            // 'id' => integer,
            // 'caso' => integer,
            // 'usuario_ingresa' => integer,
            // 'fecha_creacion' => 'timestamp',
            // 'especialidad_paciente' => 'text',
            // 'examen_solicitado' => 'text',
            // 'contraste' => boolean,
            // 'fecha_contraste' => timestamp,
            // 'comentario_examen' => 'text',
            // 'ecografia' => 'Accusamus et deleniti quia provident sit debitis.',
            // 'ecografia_doppler' => false,
            // 'extremidades' => NULL,
            // 'lado_ecografia' => NULL,
            // 'radiografia' => 'Non nemo voluptatem esse. Et reiciendis qui soluta qui labore hic qui. Nihil repellat debitis.',
            // 'lado_radiografia' => NULL,
            // 'otro_examen' => 2,
            // 'especificar_examen' => 'Et quo molestiae tempora illum.',
            // 'nombre_medico_solicitante' => 'repellat atque quo',
            // 'funcionario' => 'Laboriosam hic explicabo.',
            // 'visible' => true,
            // 'estado' => NULL,
            // 'usuario_modifica' => NULL,
            // 'fecha_modificacion' => NULL,
            // 'id_anterior' => NULL,
            // 'aislamiento_paciente' => false,
            // 'posibilidad_embarazo' => false,
            // 'creatininemia' => NULL,
            
            return response()->json(array("aaData"=>$resultado)); 
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function eliminarExamenImageneologia($id){
        try {
            $eliminar = ExamenMedico::where('id',$id)
            ->where('visible',true)
            ->first();

            if($eliminar){
                $eliminar->usuario_modifica = Auth::user()->id;
                $eliminar->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                $eliminar->visible = false;
                $eliminar->estado = "Eliminado";
                $eliminar->save();

                $eliminar_cirugias = $eliminar->cirugias_previas->where('visible',true);
                if($eliminar_cirugias){
                    foreach($eliminar_cirugias as $cirugia){
                        $cirugia->usuario_modifica = Auth::user()->id;
                        $cirugia->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                        $cirugia->visible = false;
                        $cirugia->estado = "Eliminado";
                        $cirugia->save();
                    }
                }

                $eliminar_proyecciones = $eliminar->proyecciones->where('visible',true);
                if($eliminar_proyecciones){
                    foreach ($eliminar_proyecciones as $proyeccion){
                        $proyeccion->usuario_modifica = Auth::user()->id;
                        $proyeccion->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                        $proyeccion->visible = false;
                        $proyeccion->estado = "Eliminado";
                        $proyeccion->save();
                    }
                }
                
                return response()->json(array("exito" => "El examen ha sido eliminada exitosamente."));
            }else{
                return response()->json(array("error" => "No existe información sobre el examen. <br> La información será actualizada."));
            }
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=> "Error al eliminar el examen de imageneologia."));
        }
    }

    public function editarExamenImagenologia($id){
        $examenImagenologia = ExamenMedico::where('id',$id)
        ->where('visible',true)
        ->first(); 

        if($examenImagenologia){
            $cirugias = $examenImagenologia->cirugias_previas->where('visible',true);
            $proyecciones = $examenImagenologia->proyecciones->where('visible',true);
            return response()->json(["examenImagenologia" => $examenImagenologia, "cirugias" => $cirugias, "proyecciones" => $proyecciones]);
        }else{
            return response()->json(["info" => "Este formulario ya ha sido modificado o el formulario no se encuentra. <br>La información será actualizada."]);
        }
    }
}
