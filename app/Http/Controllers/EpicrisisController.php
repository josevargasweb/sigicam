<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\InformeEpicrisis;
use App\Models\Paciente;
use App\Models\Establecimiento;
use App\Models\Consultas;
use App\Models\IEAnamnesis;
use App\Models\TipoCuidadoAlta;
use App\Models\TipoControlMedico;
use App\Models\TipoInterconsulta;
use App\Models\EpicrisisCuidado;
use App\Models\EpicrisisControlMedico;
use App\Models\EpicrisisInterconsulta;
use App\Models\EpicrisisExamenPendiente;
use App\Models\EpicrisisMedicamentoAlta;
use App\Models\EpicrisisEducacionRealizada;
use App\Models\EpicrisisOtros;
use App\Models\EvolucionEnfermeria;
use App\Models\Barthel;
use App\Helpers\EpicrisisHelper;
use Auth;
use Log;
use DB;
use Carbon\Carbon;
use PDF;

use Illuminate\Support\Arr;

class EpicrisisController extends Controller {

    public function epicrisis(Request $request) {
      // return $request;
      try {
        $diagnosticos=$request->input("diagnosticos");
        if($diagnosticos != ""){
            $diagnosticos = "{".implode(",", $diagnosticos)."}";
            $diagnosticos=trim($diagnosticos,"{}");
        }else{
          	$diagnosticos = null;
        }
        if($request->id_formulario_epicrisis){
			$epicrisis = InformeEpicrisis::find($request->id_formulario_epicrisis);
			$epicrisis->visible = false;
			$epicrisis->fecha_modificacion = \Carbon\Carbon::now();
			$epicrisis->usuario_modifica = Auth::user()->id;
		
			$editado = new InformeEpicrisis;
			$editado->fecha_creacion = $epicrisis->fecha_creacion;
			$editado->id_anterior = $epicrisis->id;
			$editado->caso = $epicrisis->caso;
			$editado->usuario_ingresa = $epicrisis->usuario_ingresa;
			$editado->destino_egreso = $request->destino_egreso;
			$editado->diagnosticos = ($request->input("diagnosticos") == "") ? $epicrisis->diagnosticos : $diagnosticos;
			$editado->intervencion_quirurgica = ($request->input("intervencion_quirurgica") == "") ? $epicrisis->intervencion_quirurgica : strip_tags($request->input("intervencion_quirurgica"));
			$editado->fecha_intervencion = ($request->fecha_intervencion) ? Carbon::parse($request->fecha_intervencion)->format("Y-m-d H:i:s") : null;
			$editado->visible = true;
			$editado->save();
			DB::commit();
			$mensaje = "La información ha sido actualizada exitosamente";
        }else{
			$epicrisis = new InformeEpicrisis;
			$epicrisis->fecha_creacion = \Carbon\Carbon::now();
			$epicrisis->caso = $request->idCaso;
			$epicrisis->usuario_ingresa = Auth::user()->id;
			$epicrisis->fecha_creacion = \Carbon\Carbon::now();
			$epicrisis->destino_egreso = $request->destino_egreso;
			$epicrisis->diagnosticos = $diagnosticos;
			$epicrisis->intervencion_quirurgica = strip_tags($request->input("intervencion_quirurgica"));
			$epicrisis->fecha_intervencion = ($request->fecha_intervencion) ? Carbon::parse($request->fecha_intervencion)->format("Y-m-d H:i:s") : null;
			$epicrisis->visible = true;
			$mensaje = "La información ha sido ingresada exitosamente";
        }
			$epicrisis->save();
			DB::commit();
			return response()->json(["exito" => $mensaje]);
        } catch (Exception $ex) {
			DB::rollback();
			return response()->json(["error" => $mensaje]);
        }
    }

    public function existenDatosEpicrisis(Request $request){
      $epicrisis = InformeEpicrisis::where('caso',$request->caso)->where("visible", true)->first();
      $datosResponsable = IEAnamnesis::select("acompanante","vinculo_acompanante","telefono_acompanante")->where('caso',$request->caso)->where('visible', true)->first();
      return ["epicrisis" => $epicrisis, "infoResponsable" => $datosResponsable];
  }

  public function pdfInformeEpicrisis($caso){
    try {
		$fecha = Carbon::now()->format('d-m-Y');
		$epicrisis = InformeEpicrisis::datosEpicrisis($caso);
		$paciente = Paciente::getPacientePorCaso($caso);
		$dataEpicrisis = InformeEpicrisis::where('caso',$caso)->where("visible", true)->first();
		$susDiagnosticos = [];
		if(isset($dataEpicrisis->diagnosticos)){
			$susDiagnosticos = $dataEpicrisis->diagnosticos;
		}	
		$array = [];
		$array = ($susDiagnosticos) ? explode(",",$susDiagnosticos) : $array;
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
		$datosResponsable = IEAnamnesis::select("acompanante","vinculo_acompanante","telefono_acompanante")->where('caso',$caso)->where('visible', true)->first();


        
        //cuidados al alta
        $cuidadoAlAlta = DB::table('formulario_epicrisis_cuidados')
        ->join('tipo_cuidado_alta', 'formulario_epicrisis_cuidados.id_cuidado', '=', 'tipo_cuidado_alta.id')
        ->select("fecha_creacion","tipo")
        ->where('caso',$caso)
        ->where('visible', true)->get();

        $datosEvolucionEnfermeria =  EvolucionEnfermeria::where('caso',$caso)
        ->where("visible", true)
        ->first();

         $historialBarthel = Barthel::dataHistorialBarthel($caso);

         $indiceBarthel = '';
         $totalBarthel = '';
         if(!empty($historialBarthel)){
             if(count($historialBarthel[0]) > 0){
                 $indiceBarthel = $historialBarthel[0][3];
                 $totalBarthel = $historialBarthel[0][4];
             }
         }


        //control medico
        $controlMedico = DB::table('formulario_epicrisis_controles_medicos')
        ->join('tipo_controles_medicos', 'formulario_epicrisis_controles_medicos.id_cuidado', '=', 'tipo_controles_medicos.id')
        ->select("fecha_creacion","fecha_solicitada","tipo")
        ->where('caso',$caso)
        ->whereNotNull('fecha_creacion')
        ->where('visible', true)->get();

        //interconsulta
        $interconsulta = DB::table('formulario_epicrisis_interconsultas')
        ->join('tipo_interconsulta', 'formulario_epicrisis_interconsultas.id_cuidado', '=', 'tipo_interconsulta.id')
        ->select("fecha_creacion","fecha_solicitada","tipo")
        ->where('caso',$caso)
        ->whereNotNull('fecha_creacion')
        ->where('visible', true)->get();

        //examenes pendientes
        $examenesPendientes = DB::table('formulario_epicrisis_examenes_pendientes')
        ->join('tipo_examenes_pendientes', 'formulario_epicrisis_examenes_pendientes.id_cuidado', '=', 'tipo_examenes_pendientes.id')
        ->select("fecha_creacion","fecha_solicitada","tipo")
        ->where('caso',$caso)
        ->whereNotNull('fecha_creacion')
        ->where('visible', true)->get();

        //medicamento al alta
        $medicamentoAlAlta = DB::table('formulario_epicrisis_medicamentos_alta')
        ->join('tipo_medicamentos_alta', 'formulario_epicrisis_medicamentos_alta.id_cuidado', '=', 'tipo_medicamentos_alta.id')
        ->select("fecha_creacion","tipo")
        ->where('caso',$caso)
        ->whereNotNull('fecha_creacion')
        ->where('visible', true)->get();

        //medicamento al alta
        $educacionesRealizadas = DB::table('formulario_epicrisis_educaciones_realizadas')
        ->join('tipo_educaciones_realizadas', 'formulario_epicrisis_educaciones_realizadas.id_cuidado', '=', 'tipo_educaciones_realizadas.id')
        ->select("fecha_creacion","tipo")
        ->where('caso',$caso)
        ->whereNotNull('fecha_creacion')
        ->where('visible', true)->get();

        //otros
        $otros = DB::table('formulario_epicrisis_otros')
        ->join('tipo_otros', 'formulario_epicrisis_otros.id_cuidado', '=', 'tipo_otros.id')
        ->select("fecha_creacion","tipo")
        ->where('caso',$caso)
        ->whereNotNull('fecha_creacion')
        ->where('visible', true)->get();


      
		$pdf = PDF::loadView('TemplatePDF.pdfInformeEpicrisis',
			[
				"infoPaciente" => $paciente,
				"infoCuidadoAlAlta" => $cuidadoAlAlta,
				"infoDatosEvolucionEnfermeria" => $datosEvolucionEnfermeria,
				"indiceBarthel" => $indiceBarthel,
				"totalBarthel" => $totalBarthel,
				"infoEpicrisis" => $dataEpicrisis,
                "infoControlMedico"=>$controlMedico,
                "infoInterconsulta"=>$interconsulta,
                "infoExamenesPendientes"=>$examenesPendientes,
                "infoMedicamentoAlAlta"=>$medicamentoAlAlta,
                "infoEducacionesRealizadas"=>$educacionesRealizadas,
                "infoOtros"=>$otros,
				"infoResponsable" => $datosResponsable,
				"destino" => Consultas::traduccionDestinoEgreso($dataEpicrisis["destino_egreso"]),
				"establecimiento" => $nombreEstablecimiento,
				"dau" => $epicrisis["dau"],
				"unidad" => $epicrisis["nombre_unidad"],
				"area" => $epicrisis["nombre_area_funcional"],
				"fechaSolicitud" => $epicrisis["fecha_solicitud"],
				"fechaHospitalizacion" => $epicrisis["fecha_hospitalizacion"],
				"fechaEgreso" => $epicrisis["fecha_egreso"],
				"diffHospEgreso" => $epicrisis["estadia2"],
				"destinos" => $epicrisis["motivos"],
				"diagnosticos" => $epicrisis["diagnosticos"],
				"fecha" => $fecha,
				"susDiagnosticos" => $array
			]);
		return $pdf->stream('Informe_epicrisis_'.$fecha.'.pdf');

    } catch (Exception $ex) {
        return response()->json($ex->getMessage());
    }
}
    
	public function consultar_cuidados_epicrisis($palabra,$tipo_tabla){
		try{
            

            $epicrisisHelper = new EpicrisisHelper();
            $validar_formulario = $epicrisisHelper->validar_formulario($tipo_tabla);
            if($validar_formulario === false){
                return response()->json(["error" => "No debe ingresar valores no permitidos"]);
            }

            $datos=DB::select(DB::raw(
				"
				SELECT
				tipo,
				id
				FROM "
				.$tipo_tabla.
				" WHERE
				tipo ILIKE'%".$palabra."%' 
				ORDER BY
				tipo ASC 
				LIMIT 50
				"
			));
			return response()->json($datos);

		}catch(Exception $e){
            Log::info($e);
			return response()->json(["error" => "Error"]);
		}
	}

	public function validar_cuidados_epicrisis(Request $request){

        $epicrisisHelper = new EpicrisisHelper();
        $validar_formulario = $epicrisisHelper->validar_formulario($request->nombreForm);
        if($validar_formulario === false){
            return response()->json(["error" => "No debe ingresar valores no permitidos"]);
        }

        if(isset($request->cuidado_modificacion_alta) && isset($request->cuidado_modificacion_item)){
            $cuidado_item = $request->cuidado_modificacion_item;
            $cuidado_epi = $request->cuidado_modificacion_alta;
        }elseif(isset($request->cuidado_item) && $request->cuidado_epi){
            $cuidado_item = $request->cuidado_item;
            $cuidado_epi = $request->cuidado_epi;
        }

        //si el tipo cuidado viene vacio
        if($cuidado_item == '' && $cuidado_epi == ''){
            return response()->json([false]);
        }else{

            //si el tipo cuidado existe
            $cuidados_validos = DB::table($request->nombreForm)->orderBy("tipo","asc")->pluck('id');

            if(isset($request->cuidado_modificacion_alta) && isset($request->cuidado_modificacion_item)){
                $validador = Validator::make($request->all(), [
                    'cuidado_modificacion_item' => Rule::in($cuidados_validos)
                ]);
            }elseif(isset($request->cuidado_item) && isset($request->cuidado_epi)){
                $validador = Validator::make($request->all(), [
                    'cuidado_item' => Rule::in($cuidados_validos)
                ]);
            }
        
            //si no lanza error se comprueba nuevamente
            if(!$validador->fails() || $cuidado_item == -1){
                $cuidados_validos = DB::table($request->nombreForm)->orderBy("tipo","asc")->pluck('tipo');

                if(isset($request->cuidado_modificacion_alta) && isset($request->cuidado_modificacion_item)){
                    $validador = Validator::make($request->all(), [
                        'cuidado_modificacion_alta' => Rule::in($cuidados_validos)
                    ]);
                }elseif(isset($request->cuidado_item) && $request->cuidado_epi){
                    $validador = Validator::make($request->all(), [
                        'cuidado_epi' => Rule::in($cuidados_validos)
                    ]);
                }
                
                //si esque existe el tipo cuidado pero envia id -1 
                if(!$validador->fails()){
                    $cuidado = DB::table($request->nombreForm)->select('id')->where('tipo',trim(strip_tags($cuidado_epi)))->first();
                    return response()->json([false,'tipo'=>$cuidado->id]);
                    
                }else{

                    if($request->seleccionado_cuidado_epi != ''){
                        $where = ' id = '.$request->seleccionado_cuidado_epi;
                    }else{
                        $where = " tipo ILIKE'%".trim(strip_tags($cuidado_epi))."%'";
                    }
    
                    $datos=DB::select(DB::raw(
                        "
                        SELECT
                        tipo,
                        id
                        FROM
                        ".$request->nombreForm." 
                        WHERE
                        ".$where."
                        ORDER BY
                        tipo ASC 
                        LIMIT 1
                        "
                    ));

                    $referencia = "";
                    if(isset($datos[0])){
                        $referencia = $datos[0];
                    }
                    
                    // si esque envia todo vacio menos el tipo de dato
                    if($cuidado_epi != ''){
                        $cuidados_validos = DB::table($request->nombreForm)->orderBy("tipo","asc")->pluck('tipo');
                        
                        if(isset($request->cuidado_modificacion_alta)){
                            $validador = Validator::make($request->all(), [
                                'cuidado_modificacion_alta' => Rule::in($cuidados_validos)
                            ]);
                        }elseif(isset($request->cuidado_epi)){
                            $validador = Validator::make($request->all(), [
                                'cuidado_epi' => Rule::in($cuidados_validos)
                            ]);
                        }
                        
                        if(!$validador->fails()){
                            $cuidado = DB::table($request->nombreForm)->select('id')->where('tipo',trim(strip_tags($cuidado_epi)))->first();
                            return response()->json([false,'tipo'=>$cuidado->id]);
                            
                        }else{
                            return response()->json([true,'tipo'=>'-1','referencia'=>$referencia]);
                        }

                    }else{
                        // si esque no existe el tipo cuidado y ademas envia un -1
                        return response()->json([true,'tipo'=>$cuidado_item,'referencia'=>$referencia]);
                    }
                }
            }else{            
                return response()->json([false,'tipo'=>$cuidado_item]);
            }
        }
    }

	public function addaepicrisistipo(Request $request){
        try{

            $epicrisisHelper = new EpicrisisHelper();
            $validar_formulario = $epicrisisHelper->validar_formulario($request->nombreForm);
            if($validar_formulario === false){
                return response()->json(["error" => "No debe ingresar valores no permitidos"]);
            }
            
            $fecha_creacion =  Carbon::now()->format("Y-m-d H:i:s");

            $obtenerDatos = $epicrisisHelper->obtener_datos_formulario($request->nombreForm);

            if($request->idCaso){
                $idCaso = $request->idCaso;
                $cuidado_epi = $request->cuidado_epi;
            }elseif ($request->id_cuidado_actualizar) {
                $idCasoFormulario = $request->id_cuidado_actualizar;
                $cuidado_epi = $request->cuidado_modificacion_alta;
            }
            
            DB::beginTransaction();

            DB::table($request->nombreForm)->insert(
                ['tipo' => trim(strip_tags($cuidado_epi))]
            );
            $cuidadoIdTipo = DB::table($request->nombreForm)->select('id')->where('tipo',trim(strip_tags($cuidado_epi)))->first();

            if($request->idCaso){
                 if($request->nombreForm == 'tipo_controles_medicos' || $request->nombreForm == 'tipo_interconsulta' || $request->nombreForm == 'tipo_examenes_pendientes'){
                    DB::table($obtenerDatos->formulario)->insert(
                        [
                        'id_cuidado' => $cuidadoIdTipo->id,
                        'caso' =>  strip_tags($idCaso),
                        'usuario' => Auth::user()->id,
                        'visible' => true,
                        'fecha_creacion' => $fecha_creacion,
                        'fecha_solicitada' => $request->fecha_creacion
                        ]
                    );
                 }else{
                     DB::table($obtenerDatos->formulario)->insert(
                         [
                         'id_cuidado' => $cuidadoIdTipo->id,
                         'caso' =>  strip_tags($idCaso),
                         'usuario' => Auth::user()->id,
                         'visible' => true,
                         'fecha_creacion' => $fecha_creacion
                         ]
                     );
                 }
            }elseif ($request->id_cuidado_actualizar) {
                $request->merge([
                    'cuidado_modificacion_item' => $cuidadoIdTipo->id,
                ]);
                $epicrisisHelper->updateFormulario($request);    
            }
        
            DB::commit();
            return response()->json(["exito" => "Se ha ingresado ".$obtenerDatos->mensaje." exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar ".$obtenerDatos->mensaje]);
        }   
    }

	
	public function addcuidadoAlta(Request $request){

        try{

            $epicrisisHelper = new EpicrisisHelper();
            $validar_formulario = $epicrisisHelper->validar_formulario($request->nombreForm);
            if($validar_formulario === false){
                return response()->json(["error" => "No debe ingresar valores no permitidos"]);
            }
            
            $fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");

            $obtenerDatos = $epicrisisHelper->obtener_datos_formulario($request->nombreForm);

            DB::beginTransaction();

            if($request->cuidado_item == -1){
                $cuidadoIdTipo = DB::table($request->nombreForm)->select('id')->where('tipo',trim(strip_tags($request->cuidado_epi)))->first();
                $request->merge([
                    'cuidado_item' => $cuidadoIdTipo->id,
                ]);
            }

            if($request->nombreForm == 'tipo_controles_medicos' || $request->nombreForm == 'tipo_interconsulta' || $request->nombreForm == 'tipo_examenes_pendientes'){
                DB::table($obtenerDatos->formulario)->insert(
                    [
                    'id_cuidado' => strip_tags($request->cuidado_item),
                    'caso' =>  strip_tags($request->idCaso),
                    'usuario' => Auth::user()->id,
                    'visible' => true,
                    'fecha_creacion' => $fecha_creacion,
                    'fecha_solicitada' => $request->fecha_creacion,
                    ]
                );
            }else{
                DB::table($obtenerDatos->formulario)->insert(
                    [
                    'id_cuidado' => strip_tags($request->cuidado_item),
                    'caso' =>  strip_tags($request->idCaso),
                    'usuario' => Auth::user()->id,
                    'visible' => true,
                    'fecha_creacion' => $fecha_creacion
                    ]
                );
            }
            

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado exitosamente"]);

        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al ingresar"]);
        }

    }


	public function obtenerCuidadosAlta($caso,$formulario){

        $epicrisisHelper = new EpicrisisHelper();
        $validar_formulario = $epicrisisHelper->validar_formulario($formulario);
        if($validar_formulario === false){
            return response()->json(["error" => "No debe ingresar valores no permitidos"]);
        }
        $obtenerDatos = $epicrisisHelper->obtener_datos_formulario($formulario);
 
        $cuidados = DB::select(DB::raw("select
				f.id,
				u.nombres,
				u.apellido_paterno,
				u.apellido_materno,
				a.tipo,
                ".$obtenerDatos->fecha_solicitada_query."
				f.fecha_creacion
				from ".$obtenerDatos->formulario. " as f
				inner join ".$obtenerDatos->tipo_formulario." as a on a.id = f.id_cuidado
				inner join usuarios as u on u.id = f.usuario
                where f.caso = $caso and f.visible = true ".$obtenerDatos->fecha_query));

        $resultado = [];

        foreach ($cuidados as $key => $cuidado) {
			$usuario_fecha = "<b>". $cuidado->nombres." ".$cuidado->apellido_paterno." ".$cuidado->apellido_materno."</b><br> Creado el: ".Carbon::parse($cuidado->fecha_creacion)->format("d-m-Y H:i");

            $opciones = "<div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-warning' onclick='".$obtenerDatos->boton_modificar."(".$cuidado->id.")'>Modificar</button>
            </div>
            <br><br>
            <div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-danger' onclick='".$obtenerDatos->boton_eliminar."(".$cuidado->id.")'>Eliminar</button>
            </div>";		

            if($formulario == 'tipo_controles_medicos' || $formulario == 'tipo_interconsulta' || $formulario == 'tipo_examenes_pendientes'){
                $resultado [] = [
                    $usuario_fecha,
                    "<div class='form-group'>
                <div class='col-md-10'>
                <h5>".Carbon::parse($cuidado->fecha_solicitada)->format("d-m-Y H:i")."</h5>
                </div>",
                    $cuidado->tipo,
                    $opciones
                ];
            }else{
                $resultado [] = [
                    $usuario_fecha,
                    $cuidado->tipo,
                    $opciones
                ];
            }
			
		}

		return response()->json(["aaData" => $resultado]);
    }

	public function eliminarCuidado(Request $request){

        try{
            $epicrisisHelper = new EpicrisisHelper();
            $validar_formulario = $epicrisisHelper->validar_formulario($request->nombreForm);
            if($validar_formulario === false){
                return response()->json(["error" => "No debe ingresar valores no permitidos"]);
            }
           $obtenerDatos = $epicrisisHelper->obtener_datos_formulario($request->nombreForm);
            DB::beginTransaction();
            
            

            /* se modifica segun la tabla y la id */
            DB::table($obtenerDatos->formulario)
            ->where('id', $request->id)
            ->update(
                [
                'usuario_modifica' => Auth::user()->id,
                'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
                'tipo_modificacion' => 'Eliminado',
                'visible' => false
                ]
            );

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado exitosamente"]);

        }catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar"]);
        }
    }


	public function obtenerCuidadoAlta($idCuidado,$formulario){
        /* comprueba el tipo de modal que se va a mostrar */
        try{

            $epicrisisHelper = new EpicrisisHelper();

            $obtenerDatos = $epicrisisHelper->CuidadoAlta($formulario,$idCuidado);
 
            $resp = View::make("Gestion/gestionEnfermeria/epicrisis/".$obtenerDatos->modal, [
                "cuidadoInfo" => $obtenerDatos->informacion,"idCuidado"=>$idCuidado
            ] )->render();

            return response()->json(array("contenido"=>$resp));
        }catch(Exception $e){
            return response()->json(["error" => "error al mostrar el modal"],404);
        }
    }


    public function modificarPCCuidado(Request $request){
        try{
            $epicrisisHelper = new EpicrisisHelper();
            $validar_formulario = $epicrisisHelper->validar_formulario($request->nombreForm);
            if($validar_formulario === false){
                return response()->json(["error" => "No debe ingresar valores no permitidos"]);
            }

            DB::beginTransaction();

            $cuidadoIdTipo = DB::table($request->nombreForm)->select('id')->where('tipo',trim(strip_tags($request->cuidado_modificacion_alta)))->orWhere('id', $request->cuidado_modificacion_item)->first();

            $request->merge([
                'cuidado_modificacion_item' => $cuidadoIdTipo->id,
            ]);

            $epicrisisHelper->updateFormulario($request);


            DB::commit();
            return response()->json(["exito" => "Se ha modificado con exito"],200);

        }catch(Exception $e){
            Log::info($e);
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

    public function guardarEvolucionEnfermeria(Request $request){
        try {
            DB::beginTransaction();
            $tipo_barthel = 'Epicrisis';

            if($request->id_formulario_evolucion_enfermeria){
                Log::info("ya tenia");
                //si ya tiene registro, lo cambio a visible falso.
                $modificar = EvolucionEnfermeria::find($request->id_formulario_evolucion_enfermeria);
                $modificar->usuario_modifica = Auth::user()->id;
                $modificar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $modificar->visible = false;
                $modificar->estado = "Editado";
                $modificar->save();
                $id_modificarx = $modificar;
                log::info($id_modificarx);

                if($modificar->indbarthel){
                    //si tiene un formulario barthel asociado en la epicrisis, debe ocultarse y actualizar al nuevo
                    $barthel = EvolucionEnfermeria::guardarBarthel($request, $id_modificarx,$tipo_barthel);
                    if ($barthel["info"]) {
                        DB::rollBack();
                        return response()->json(["error" => $barthel["info"]]);
                    }
                    Log::info("Barthel ");
                    Log::info($barthel);
                    $barthel->visible = true;
                    $barthel->save();
                }

                //guardo el nuevo registro de evolucion enfermeria.
                $copia = EvolucionEnfermeria::guardar($request);
                $ind_barthel = (isset($barthel) && $barthel->id_formulario_barthel) ? $barthel->id_formulario_barthel : null;  
                $copia->indbarthel = $ind_barthel;
                $copia->id_anterior = $modificar->id;
                $copia->visible = true;
                $copia->save();
            }else{
                Log::info("No existe antuigo");

                //si no tiene registro, reviso si debe guardar barthel
                if(isset($request->formulariobarthel) && $request->formulariobarthel == "si"){
                    //guardo el barthel
                    $nuevoBarthel = EvolucionEnfermeria::guardarBarthel($request, $id_modificarx = null,$tipo_barthel);
                    $nuevoBarthel->visible = true;
                    $nuevoBarthel->save();
                }

                //guardo el nuevo registro de evolucion enfermeria
                $nuevoEF = EvolucionEnfermeria::guardar($request);
                $ind_barthel = (isset($nuevoBarthel) && $nuevoBarthel->id_formulario_barthel) ? $nuevoBarthel->id_formulario_barthel : null;  
                $nuevoEF->indbarthel = $ind_barthel;
                $nuevoEF->visible = true;
                $nuevoEF->save();
            }

            DB::commit();
            return response()->json(["exito" => "se ha ingresado la evolución de enfermeria exitosamente"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            return response()->json(["error" => "mensaje de error"]);
        }
    }

    public function existenDatosEvolucionEnfermeria(Request $request){

        $evolucionEnfermeria = DB::table('evolucion_enfermeria as e')
        ->where('e.caso',$request->caso)
        ->where('e.visible', true)
        ->first();

        $datosBarthel = [];

        if($evolucionEnfermeria && $evolucionEnfermeria->indbarthel){
            $datosBarthel = Barthel::where('id_formulario_barthel',$evolucionEnfermeria->indbarthel)->where('visible',true)->first();
        }

        return ["evolucionesEnfermeria" => $evolucionEnfermeria, "datosBarthel" => $datosBarthel];
    }

    public function datosBarthelEvolucionEnfermeria(Request $request){
        
        $evolucionEnfermeria = EvolucionEnfermeria::find($request->id_barthel,'indbarthel');
        
        return Barthel::where('id_formulario_barthel',$evolucionEnfermeria->indbarthel)->where('visible',true)->first();
    }
}
