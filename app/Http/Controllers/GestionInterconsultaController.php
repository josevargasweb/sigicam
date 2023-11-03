<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caso;
use Log;
use DB;
use Auth;
use Response;
use Session;
use Carbon\Carbon;
use Consultas;
use View;
use PDF;
use App\Models\MedicoInterconsulta;
use App\Models\HistorialDiagnostico;
use App\Models\Usuario;
use App\Models\Establecimiento;
use App\Models\EstablecimientosExtrasistema;
use App\Models\EspecialidadMedica;
use App\Models\Region;
use App\Models\Comuna;
use App\Models\Telefono;
use App\Models\Paciente;

class GestionInterconsultaController extends Controller
{
    public function agregarinterconsulta(Request $request){
        try {
            DB::beginTransaction();
			if($request->idCasoInterconsulta == ''){
				return response()->json(["error" => "Error al ingresar"]);
			}

			$idCaso = base64_decode($request->idCasoInterconsulta);
			$idAnterior = '';

			//modifica al paciente
			$Pacientecaso = Caso::where('id',$idCaso)->first();
			$paciente = Paciente::where("id",$Pacientecaso->paciente)->first();

			if(!$paciente){
				return response()->json(array("info" => "Error al buscar al paciente"));
			}
			
			
			//verifica si el formulario viene con id no vacia sino la modifica
			if(isset($request->id_formulario_interconsulta) &&  $request->id_formulario_interconsulta != ''){
				$interconsulta = MedicoInterconsulta::where('id',$request->id_formulario_interconsulta)
				->where('visible', true)
				->first();
                if (empty($interconsulta)) {
					//Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                    return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                }
                $interconsulta->usuario_modifica = Auth::user()->id;
                $interconsulta->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $interconsulta->visible = false;
	
                $interconsulta->save();
				$idAnterior = $interconsulta->id;
            }

			//verifica que los datos eliminados existan y que no vengan vacios
			$telefonos = Telefono::select("id")->where("id_paciente",$paciente->id)->get();
            if(count($telefonos) > 0){
                foreach ($telefonos as $t){
                    Telefono::destroy($t->id);
                }
            }

			//paciente
			if(isset($request->tipo_telefono_interconsulta) && !empty($request->tipo_telefono_interconsulta) && isset($request->telefono) && !empty($request->telefono) ){
				//buscar si existen el telefono y lo modifica
				foreach ($request->tipo_telefono_interconsulta  as $key => $telfono) {
						$nuevoTelefono = new Telefono();
						$nuevoTelefono->id_paciente = $paciente->id;
						$nuevoTelefono->tipo = $telfono;
						$nuevoTelefono->telefono = $request->telefono[$key];
						$nuevoTelefono->save();
				}
			}

			$paciente->calle = $request->paciente_calle_ges;
			$paciente->numero = $request->paciente_numero_ges;
			$paciente->observacion = $request->paciente_observacion_ges;
			$paciente->id_comuna = $request->comuna;
			$paciente->save();

			//Interconsulta
			$nuevaInterconsulta = new MedicoInterconsulta;
		
			if($idAnterior != ""){
				$nuevaInterconsulta->id_anterior = $idAnterior;
			}
			//Diagnostico
			if(isset($request->id_diagnostico_interconsulta) && !empty($request->id_diagnostico_interconsulta)){
				$nuevaInterconsulta->id_diagnostico_interconsulta = implode(",", $request->id_diagnostico_interconsulta);
			}else{
				return response()->json(["error" => "Error al ingresar"]);
			}
			$nuevaInterconsulta->tipo_diagnostico = $request->datos_clinicos_interconsulta;

			if($request->especialidad_interconsulta != ""){
				$nuevaInterconsulta->especialidad_interconsulta = $request->especialidad_interconsulta;
			}
			if($request->especialidad_interconsulta_dirigido != ""){
				$nuevaInterconsulta->especialidad_interconsulta_dirigido = $request->especialidad_interconsulta_dirigido;
			}
			if($request->datos_clinicos_interconsulta_otro != ""){
				$nuevaInterconsulta->tipo_diagnostico_otro = $request->datos_clinicos_interconsulta_otro;
			}
			if($request->auge_interconsulta == 'si'){
				$nuevaInterconsulta->problema_salud_auge = true;
				if($request->especificar_problema_interconsulta != ""){
					$nuevaInterconsulta->especificar_problema_salud_auge = $request->especificar_problema_interconsulta;
				}
			}else{
				$nuevaInterconsulta->problema_salud_auge = false;
			}
			if($request->programa_auge_interconsulta != ""){
				$nuevaInterconsulta->sub_programa_salud_auge = $request->programa_auge_interconsulta;
			}
			if($request->fund_diagnostico_interconsulta != ""){
				$nuevaInterconsulta->fundamentos_diagnostico = $request->fund_diagnostico_interconsulta;
			}
			if($request->examenes_realizados_interconsulta != ""){
				$nuevaInterconsulta->examenes_realizados = $request->examenes_realizados_interconsulta;
			}

			$nuevaInterconsulta->tipo_centro = strip_tags($request->tipo_centro);
			$nuevaInterconsulta->red_publica = ($request->tipo_centro == "derivacion")?$request->red_publica:null;
			$nuevaInterconsulta->red_privada = ($request->tipo_centro == "traslado extra sistema")?$request->red_privada:null;
			$nuevaInterconsulta->usuario_notifica = $request->id_medico_interconsulta;
			$nuevaInterconsulta->caso = $idCaso;
			$nuevaInterconsulta->usuario = Auth::user()->id;
			$nuevaInterconsulta->visible = true;
			$nuevaInterconsulta->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
			$nuevaInterconsulta->save();		

			DB::commit();
			return response()->json(array("exito" => 'Se ha ingresado correctamente'));
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => "Error al ingresar"]);
        }
    
    }


	public function historialDiagnosticosInterconsulta($caso){

		$caso = base64_decode($caso);
		$notificaciones = MedicoInterconsulta::select('formulario_medico_interconsulta.id','formulario_medico_interconsulta.fecha_creacion','u.nombres','u.apellido_paterno','u.apellido_materno','formulario_medico_interconsulta.tipo_diagnostico','formulario_medico_interconsulta.tipo_diagnostico_otro')
		->leftJoin('usuarios as u', 'u.id', '=', 'formulario_medico_interconsulta.usuario')
		->where('formulario_medico_interconsulta.caso',$caso)->where('formulario_medico_interconsulta.visible',true)
		->orderBy('formulario_medico_interconsulta.id','desc')
		->get();


		$resultado = [];

        foreach ($notificaciones as $key => $notificacion) {
			$usuario = "<b>". $notificacion->nombres." ".$notificacion->apellido_paterno."</b><br> Creado el: ".Carbon::parse($notificacion->fecha_creacion)->format("d-m-Y H:i");
			$tipo_diagnostico = "<b>". $notificacion->tipo_diagnostico."</b>";
			if($notificacion->tipo_diagnostico == 'otro' && $notificacion->tipo_diagnostico_otro != null || $notificacion->tipo_diagnostico == 'otro' && $notificacion->tipo_diagnostico_otro != ""){
				$tipo_diagnostico .= "<br>".$notificacion->tipo_diagnostico_otro;
			}

			$pdf = "<button class='btn btn-danger' type='button' onclick='generarInterconsultaPDF(".$notificacion->id.",".$caso.")'>PDF</button>";

            $opciones = "<div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-warning' onclick='modificar_interconsulta_medica(".$notificacion->id.")'>Modificar</button>
            </div>
            <br><br>
            <div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-danger' onclick='eliminar_interconsulta_medica(".$notificacion->id.")'>Eliminar</button>
            </div>";		

    
                $resultado [] = [
                    $usuario,
                    $tipo_diagnostico,
					$pdf,
                    $opciones
                ];
			
		}

		return response()->json(["aaData" => $resultado]);
    }


	public function obtenerDiagnosticosDatosPaciente($idCaso){
		
		try {
			$caso = base64_decode($idCaso);
			$historialdiagnostico = HistorialDiagnostico::where("caso",$caso)->orderBy("fecha","desc")->get();
            $paciente = Paciente::getPacientePorCaso($caso);
	
			$establecimiento = "";
			$edad = '';
			$region = "";
			$comuna = "";
			$telefonos = "";
			$usuario = array("id_usuario"=>Auth::user()->id,"rut_usuario"=>Auth::user()->rut,"dv_usuario"=>Auth::user()->dv,"nombre_usuario"=>Auth::user()->nombres.' '.Auth::user()->apellido_paterno.' '.Auth::user()->apellido_materno);
			
			if(!empty($paciente)){
				$establecimiento = DB::table('pacientes as p')
				->select('c.id', 'c.fecha_termino','c.fecha_ingreso','c.fecha_ingreso2', 'e.nombre as nombre_establecimiento', 'e.id as id_estab', 'e.snss as servicio_salud','u.alias as unidad','c.procedencia','c.detalle_procedencia')
				->leftjoin('casos as c', 'c.paciente','=','p.id')
				->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
				->leftjoin('camas as ca','ca.id','t.cama')
				->leftjoin('salas as s','s.id','ca.sala')
				->leftjoin('unidades_en_establecimientos as u','u.id','s.establecimiento')
				->leftjoin('establecimientos as e', 'e.id','u.establecimiento')
				->where('p.id',$paciente->id)
				->whereNull('c.fecha_termino')
				->first();	
				if($paciente->fecha_nacimiento != null){
					$edad=Paciente::edad($paciente->fecha_nacimiento);
				}
	
			   
				if($paciente->id_comuna != null){
					$region = Comuna::getRegion($paciente->id_comuna)->id_region;
					$comuna = $paciente->id_comuna;
				}
				$telefonos = Telefono::where('id_paciente',$paciente->id)->get();
			}

           
            return response()->json(array("historialdiagnostico"=>$historialdiagnostico,
            "caso" => $caso,
            "infoPaciente" => $paciente,
            "edad" => $edad,
            "region"=>$region,
            "comuna"=>$comuna,
            "telefonos" => $telefonos,
			"establecimiento"=>$establecimiento,
			"usuario"=>$usuario)); 
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

	public function obtenerDiagnosticosPorId($idCaso,$idDiagnostico){
		
		try {
			$caso = base64_decode($idCaso);
			$historialdiagnostico = HistorialDiagnostico::where("caso",$caso)->where("id",$idDiagnostico)->orderBy("fecha","desc")->first();
           
            return response()->json(array("historialdiagnostico"=>$historialdiagnostico)); 
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }


	public function eliminar_interconsulta_medica($idFormulario){
		try {

			if($idFormulario != "" && $idFormulario){

				$interconsulta = MedicoInterconsulta::where('id',$idFormulario)
				->where('visible', true)
				->first();

				if(!$interconsulta){
					return response()->json(["informacion" => "Este fomulario ya fue modificado"]);
				}
		
				$interconsulta->usuario_modifica = Auth::user()->id;
				$interconsulta->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
				$interconsulta->visible = false;
				$interconsulta->save();

				
				return response()->json(["exito" => "El formulario ha sido eliminado con exito"]);
			}else{
				return response()->json(["error" => "Error al recibir los datos"]);
			}
		

		} catch (\Throwable $th) {
			return response()->json(["error" => "Error al recibir los datos"]);
		}

		

    }

	public function modificar_interconsulta_medica($idFormulario)
    {
		try {
			/* Validar formulario */
			if(isset($idFormulario) &&  $idFormulario != ''){
				
				$interconsulta = MedicoInterconsulta::where('id',$idFormulario)
				->where('visible', true)
				->first();
				
				if(!$interconsulta){
					return response()->json(["informacion" => "Este fomulario ya fue modificado"]);
				}
				$paciente = Paciente::getPacientePorCaso($interconsulta->caso);
				$historialdiagnostico = HistorialDiagnostico::where("caso",$interconsulta->caso)->orderBy("fecha","desc")->get();
				
				$establecimiento = "";
				$edad = '';
				$region = "";
				$comuna = "";
				$telefonos = "";
				$usuario = Usuario::select('id as id_usuario','rut as rut_usuario','dv as dv_usuario',DB::raw("CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) AS nombre_usuario"))->where('id',$interconsulta->usuario)->first();
				
				if(!empty($paciente)){
					$establecimiento = DB::table('pacientes as p')
					->select('c.id', 'c.fecha_termino','c.fecha_ingreso','c.fecha_ingreso2', 'e.nombre as nombre_establecimiento', 'e.id as id_estab', 'e.snss as servicio_salud','u.alias as unidad','c.procedencia','c.detalle_procedencia')
					->leftjoin('casos as c', 'c.paciente','=','p.id')
					->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
					->leftjoin('camas as ca','ca.id','t.cama')
					->leftjoin('salas as s','s.id','ca.sala')
					->leftjoin('unidades_en_establecimientos as u','u.id','s.establecimiento')
					->leftjoin('establecimientos as e', 'e.id','u.establecimiento')
					->where('p.id',$paciente->id)
					->whereNull('c.fecha_termino')
					->first();	
					if($paciente->fecha_nacimiento != null){
						$edad=Paciente::edad($paciente->fecha_nacimiento);
					}
		
				   
					if($paciente->id_comuna != null){
						$region = Comuna::getRegion($paciente->id_comuna)->id_region;
						$comuna = $paciente->id_comuna;
					}
					$telefonos = Telefono::where('id_paciente',$paciente->id)->get();
				}
	
				return response()->json(array("historialdiagnostico"=>$historialdiagnostico,
				"infoPaciente" => $paciente,
				"edad" => $edad,
				"region"=>$region,
				"comuna"=>$comuna,
				"telefonos" => $telefonos,
				"establecimiento"=>$establecimiento,
				"usuario"=>$usuario,
				"interconsulta"=>$interconsulta,
				)); 
			}else{
				return response()->json(["error" => "Error al recibir los datos"]);
			}

		} catch (Exception $ex) {
			Log::info($ex);
			return response()->json(array("error"=>$ex));
		}
    }

	public function pdfInterconsulta($idFormulario,$caso){
        try {
			/* Validar formulario */
			if(isset($idFormulario) &&  $idFormulario != ''){
				
				$interconsulta = MedicoInterconsulta::where('id',$idFormulario)
				->where('visible', true)
				->first();
				
				if(!$interconsulta){
					return response()->json(["informacion" => "Este fomulario ya fue modificado"]);
				}
				$paciente = Paciente::getPacientePorCaso($interconsulta->caso);
				$historialdiagnostico = HistorialDiagnostico::where("caso",$interconsulta->caso)->orderBy("fecha","desc")->get();
				
				$establecimiento = "";
				$edad = '';
				$region = "";
				$comuna = "";
				$telefonos = "";
				$diagnosticos = "";
				$red_publica = "";
				$red_privada = "";
				$especialidad = "";
				$especialidad_dirigido = "";

				if(isset($especialidad) && $interconsulta->especialidad_interconsulta !== null && $interconsulta->especialidad_interconsulta !==''){
					$especialidad = EspecialidadMedica::select('nombre')->where('id',$interconsulta->especialidad_interconsulta)->first();
				}

				if(isset($especialidad_dirigido) && $interconsulta->especialidad_interconsulta_dirigido !== null && $interconsulta->especialidad_interconsulta_dirigido !==''){
					$especialidad_dirigido = EspecialidadMedica::select('nombre')->where('id',$interconsulta->especialidad_interconsulta_dirigido)->first();
				}

				if(isset($interconsulta) && $interconsulta->red_publica !== null && $interconsulta->red_publica !==''){
					$red_publica = Establecimiento::select('nombre')->where('id',$interconsulta->red_publica)->first();
				}
				if(isset($interconsulta) && $interconsulta->red_privada !== null && $interconsulta->red_privada !==''){
					$red_privada = EstablecimientosExtrasistema::select('nombre')->where('id',$interconsulta->red_privada)->first();
				}
				$usuario = Usuario::select('id as id_usuario','rut as rut_usuario','dv as dv_usuario','nombres','apellido_paterno','apellido_materno',DB::raw("CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) AS nombre_usuario"))->where('id',$interconsulta->usuario)->first();
				
				if(!empty($paciente)){
					$establecimiento = DB::table('pacientes as p')
					->select('c.id', 'c.fecha_termino','c.fecha_ingreso','c.fecha_ingreso2', 'e.nombre as nombre_establecimiento', 'e.id as id_estab', 'e.snss as servicio_salud','u.alias as unidad','c.procedencia','c.detalle_procedencia','c.ficha_clinica')
					->leftjoin('casos as c', 'c.paciente','=','p.id')
					->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
					->leftjoin('camas as ca','ca.id','t.cama')
					->leftjoin('salas as s','s.id','ca.sala')
					->leftjoin('unidades_en_establecimientos as u','u.id','s.establecimiento')
					->leftjoin('establecimientos as e', 'e.id','u.establecimiento')
					->where('p.id',$paciente->id)
					->whereNull('c.fecha_termino')
					->first();	
					if($paciente->fecha_nacimiento != null){
						$edad=Paciente::edad($paciente->fecha_nacimiento);
					}
		
				   
					if($paciente->id_comuna != null){
						$region = Comuna::getRegion($paciente->id_comuna)->id_region;
						$comuna = $paciente->id_comuna;
					}
					$telefonos = Telefono::where('id_paciente',$paciente->id)->limit(2)->get();
				}
	
				if($interconsulta->id_diagnostico_interconsulta != ''){
					$diagnosticos_interconsulta = explode(",", $interconsulta->id_diagnostico_interconsulta);
					$diagnosticos = HistorialDiagnostico::select('diagnostico','id_cie_10','comentario')->where("caso",$interconsulta->caso)->whereIn('id', $diagnosticos_interconsulta)->orderBy("fecha","desc")->get();
				}
			   

				$pdf = PDF::loadView('Gestion.gestionMedica.Pdf.pdfInterconsulta',
				[
				"historialdiagnostico" => $historialdiagnostico,
				"infoPaciente" => $paciente,
				"edad" => $edad,
				"region" => $region,
				"comuna" => $comuna,
				"telefonos" => $telefonos,
				"establecimiento" => $establecimiento,
				"usuario" => $usuario,
				"interconsulta" => $interconsulta,
				"diagnosticos" => $diagnosticos,
				"red_publica" => $red_publica,
				"red_privada" => $red_privada,
				"especialidad" => $especialidad,
				"especialidad_dirigido" => $especialidad_dirigido,
				]);
			return $pdf->stream('Interconsulta.pdf');
			// return $pdf->download('Interconsulta.pdf');
			}else{
				return response()->json(["error" => "Error al recibir los datos"]);
			}

		

        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
}