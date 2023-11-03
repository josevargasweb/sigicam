<?php

namespace App\Http\Controllers;

use App\Models\HojaIngresoEnfermeria;
use App\Models\IEAnamnesis;
use App\Models\Ginecologica;
use App\Models\IEGeneral;
use App\Models\IESegmentado;
use App\Models\IEOtros;
use App\Models\Establecimiento;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\Caso;
use App\Models\Telefono;
use App\Models\Cateter;
use App\Models\Pertenencias;
use App\Models\Consultas;
use App\Models\Barthel;
use App\Models\Glasgow;
use App\Models\Nova;
use App\Models\HojaEnfermeriaRiesgoCaida;
use App\Models\InformeEpicrisis;

use App\Helpers\Formularios\NovaHelper;
use App\Helpers\Formularios\RiesgoCaidaHelper;
use App\Helpers\Formularios\GlasgowHelper;
use App\Helpers\Formularios\BarthelHelper;

use App\Models\HistorialSubcategoriaUnidad;
use App\Models\FormularioExamenGinecoobstetrico;

use Illuminate\Http\Request;
use Session;
use Auth;
use TipoUsuario;
use DB;
use Log;
use Carbon\Carbon;
use View;
use App\Models\Indicacion;
use Form;
use PDF;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class HojaIngresoEnfermeriaController extends Controller
{
    public function existenHojaIngresoEnfermeria(Request $request){
        $anamnesis = IEAnamnesis::where('caso',$request->caso)->where('visible', true)->first();
        $ginecologico = '';
        if(!empty($anamnesis) && isset($anamnesis->idginecologica) && $anamnesis->idginecologica != null || !empty($anamnesis) &&  isset($anamnesis->idginecologica) && $anamnesis->idginecologica != ''){
            $ginecologico = Ginecologica::where('id',$anamnesis->idginecologica)->where('visible', true)->first();
        }
        $medicamentos = Medicamento::where('caso',$request->caso)->where('visible', true)->get();
        $general = IEGeneral::where('caso',$request->caso)->where('visible', true)->first();
        $segmentado = IESegmentado::where('caso',$request->caso)->where('visible', true)->first();
        $otros = Cateter::where('caso',$request->caso)->where('visible', true)->get();
		$ginecoobstetrico = FormularioExamenGinecoobstetrico::where('caso',$request->caso)->where('visible', true)->first();
        //$otros = IEOtros::where('caso',$request->caso)->where('visible', true)->first();
        $id_paciente_caso = Caso::find($request->caso,'paciente');
        $fecha_nacimiento_paciente = Paciente::find($id_paciente_caso->paciente,'fecha_nacimiento');
        $fecha_ingreso_enfermeria = Consultas::fechaPrimerRegistroIngresoEnfermeria($request->caso);
        return [
            "anamnesis" => $anamnesis, 
            "ginecologico" => $ginecologico, 
            "general" => $general, 
            "segmentado" => $segmentado, 
            "otros" => $otros, 
            "medicamentos", json_decode($medicamentos), 
            "fecha_nacimiento" => $fecha_nacimiento_paciente->fecha_nacimiento,
            "fecha_ingreso_enfermeria" => $fecha_ingreso_enfermeria,
			"ginecoobstetrico" => $ginecoobstetrico
        ];
    }

    public function existenDatosAnamnesis(Request $request){
        $id_paciente_caso = Caso::find($request->caso,'paciente');
        $fecha_nacimiento_paciente = Paciente::find($id_paciente_caso->paciente,'fecha_nacimiento');
        $anamnesis = IEAnamnesis::where('caso',$request->caso)->where('visible', true)->get();
        $gicologico = '';
        if(!empty($anamnesis) && isset($anamnesis[0]) && $anamnesis[0]->idginecologica != null || !empty($anamnesis) && isset($anamnesis[0]) &&  $anamnesis[0]->idginecologica != ''){
            $gicologico = Ginecologica::where('id',$anamnesis[0]->idginecologica)->where('visible', true)->first();
        }

        return ["datos_anamnesis" => $anamnesis, "fecha_nacimiento" => $fecha_nacimiento_paciente->fecha_nacimiento,"datos_gicologico"=>$gicologico];
    }

    public function existenDatosGeneral(Request $request){
       $IEGeneral = IEGeneral::where('caso',$request->caso)
       ->where('visible', true)->get();
    
       $Glasgow = [];
       $Barthel = [];
       $Riesgo = [];
       $Nova = [];
       if(!empty($IEGeneral)){
           if(isset($IEGeneral[0]->indglasgow) && $IEGeneral[0]->indglasgow != null){
               $Glasgow = Glasgow::where('caso',$request->caso)
               ->where('tipo', 'Ingreso')
               ->where('id_formulario_escala_glasgow', $IEGeneral[0]->indglasgow)
               ->where('visible', true)->first();
           }
           if(isset($IEGeneral[0]->indbarthel) && $IEGeneral[0]->indbarthel != null){
               $Barthel = Barthel::where('caso',$request->caso)
               ->where('tipo', 'Ingreso')
               ->where('id_formulario_barthel', $IEGeneral[0]->indbarthel)
               ->where('visible', true)->first();
           }
           if(isset($IEGeneral[0]->indriesgo) && $IEGeneral[0]->indriesgo != null){
               $Riesgo = HojaEnfermeriaRiesgoCaida::where('caso',$request->caso)
               ->where('tipo', 'Ingreso')
               ->where('id', $IEGeneral[0]->indriesgo)
               ->where('visible', true)->first();
           }
           if(isset($IEGeneral[0]->indnova) && $IEGeneral[0]->indnova != null){
               $Nova = Nova::where('caso',$request->caso)
               ->where('tipo', 'Ingreso')
               ->where('id_formulario_escala_nova', $IEGeneral[0]->indnova)
               ->where('visible', true)->first();
           }
       }
        return [
            "datos_IEGeneral" => $IEGeneral,
            "datos_Glasgow" => $Glasgow,
            "datos_Barthel" => $Barthel,
            "datos_Riesgo" => $Riesgo,
            "datos_Nova" => $Nova,
            ];

    }

    public function existenDatosSegmentario(Request $request){
        return IESegmentado::where('caso',$request->caso)->where('visible', true)->get();
    }

    public function existenDatosOtros(Request $request){
        return IEOtros::where('caso',$request->caso)->where('visible', true)->get();
    }

    public function existenDatosCateteres(Request $request){
        $resultado = Cateter::where('caso',$request->caso)->where('visible', true)->get();
        return $resultado;
    }

    // public function existenDatosPueblo(Request $request){
    //     return Paciente::getPacientePorCaso($request->caso);
    // }

    public function existenDatosMedicamentos(Request $request){
        $medicamento = $request->caso;
        return DB::select(DB::raw("select *
    		from medicamentos
    		where caso =".$medicamento." and visible = true"));
    }

    public function eliminarMedicamento(Request $request){

        $eliminar = $request->eliminar;
        $caso = $request->caso;
        DB::beginTransaction();
        try {
            $upmedicamento = Medicamento::where("caso", "=", $caso)->where("id", "=", $eliminar)->first();
            $upmedicamento->visible = false;
            $upmedicamento->usuario_modifica = Auth::user()->id;
            $upmedicamento->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $upmedicamento->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado el medicamento"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al eliminar el medicamento"]);
        }
    }

    public function endKey( $array ){
        end( $array );
        return key( $array );
    }

    public function agregarIEAnamnesis(Request $request){
        try {
            $idGinecologica = null;
            DB::beginTransaction();
            if(isset($request->sub_categoria) && $request->sub_categoria == 2){
                if($request->idGineCologica != '' || $request->idGineCologica != null){
                    $modificar_ginecologica = Ginecologica::find($request->idGineCologica);
                    if($modificar_ginecologica != null && $modificar_ginecologica->visible == true || $modificar_ginecologica != '' && $modificar_ginecologica->visible == true){
               
                        $modificar_ginecologica->visible = false;
                        $modificar_ginecologica->usuario_modifica = Auth::user()->id;
                        $modificar_ginecologica->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                        $modificar_ginecologica->save();

                        $nuevo_ginecologica = Ginecologica::crearNuevo($request, $modificar_ginecologica);
                        $nuevo_ginecologica->visible = true;
                        $nuevo_ginecologica->save();

                        $idGinecologica = $nuevo_ginecologica->id;

                    }else{
                        return response()->json(["error" => "Esta unidad ya ha sido modificada"]);
                    }
                }else{

                    $nuevo_ginecologica = Ginecologica::crearNuevo($request, $modificar_ginecologica = null);
                    $nuevo_ginecologica->visible = true;
                    $nuevo_ginecologica->save();
                    $idGinecologica = $nuevo_ginecologica->id;
                }
            }

            if($request->id_formulario_ingreso_enfermeria){
                //pueblo originario
                // $paciente = Paciente::getPacientePorCaso($request->idCaso);
                // if ($request->puebloind == 'si'){
                //     $paciente->pueblo_indigena = $request->pueblo_ind;
                // }else{
                //     $paciente->pueblo_indigena = 'Ninguno';
                // }

                // if ($request->pueblo_ind == 'Otro' && $request->puebloind != 'no') {
                //     $paciente->detalle_pueblo_indigena = $request->esp_pueblo;
                // }else{
                //     $paciente->detalle_pueblo_indigena = "";
                // }
                // $paciente->save();
                
                //buscar actual
                $modificar = IEAnamnesis::find($request->id_formulario_ingreso_enfermeria);

                //comparar si trae datos nuevos
                // $guardar = IEAnamnesis::datosNuevos($request, $modificar);
                // Log::info("guardar: {$guardar}");
                // if($guardar == 0){
                //     return response()->json(["aviso" => "No hay nuevos datos para ingresar"]);
                // }
                
                //modificar actual
                $modificar->usuario_modifica = Auth::user()->id;
                $modificar->fecha_modificacion = Carbon::now();
                $modificar->visible = false;
                $modificar->tipo_modificacion = 'Editado';
                
                $modificar->save();
                

                //crear copia del registro anterior
                $copia = IEAnamnesis::crearNuevo($request, $modificar,$idGinecologica);
                $copia->visible = true;
                $copia->save();
            }else{
              //pueblo originario
                // $paciente = Paciente::getPacientePorCaso($request->idCaso);
                // $paciente->pueblo_indigena = $request->pueblo_ind;

                // if ($request->pueblo_ind == 'Otro') {
                //   $paciente->detalle_pueblo_indigena = $request->esp_pueblo;
                // }
                // $paciente->save();

                //crear nuevo registro
                $nuevo = IEAnamnesis::crearNuevo($request, $modificar = null,$idGinecologica);
                $nuevo->visible = true;
                $nuevo->save();
            }

            $medicamento = $request->nombreMedicamento;
            $ids = $request->ids;

            $a_med_bd = []; //para almacenar los que ya estan en la bd
            $tienemedicamentos = Medicamento::select('id')->where("caso", $request->idCaso)->where("visible", true)->get();
            foreach ($tienemedicamentos as $value) {
                $a_med_bd[] = $value->id; //guardar los id de los medicamentos que hay en bd
            }

            //validar que existan medicamentos ingresados
            if($ids){
                //comprarar los arreglos de los datos en bd y los datos enviados 
                $resultado = array_diff($a_med_bd,$ids);
                //si hay resultados, se recorre
                if($resultado){
                    foreach ($resultado as $value) {
                        //se busca uno por uno y se pone en falso
                        $falseado = Medicamento::find($value);
                        $falseado->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                        $falseado->usuario_modifica = Auth::user()->id;
                        $falseado->visible = false;
                        $falseado->tipo_modificacion = 'Eliminado';
                        $falseado->save();
                    }
                }
            }
            
            if($medicamento){
                foreach ($medicamento as $key => $medi) {
                    if(!empty($medi)){
                        $id = $request->ids[$key];
                        if($id != null){
                            $existe = Medicamento::where('id',$id)->first();
                            //si encuentra un id con el nombre similar al indicado en la tabla
                            if($existe){
                                if($existe->nombre != $medi){
                                    //falsea el encontrado
                                    $existe->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                                    $existe->usuario_modifica = Auth::user()->id;
                                    $existe->visible = false;
                                    $existe->tipo_modificacion = 'Editado';
                                    $existe->save();

                                    //crea uno nuevo
                                    $medicamentoNuevo = new Medicamento;
                                    $medicamentoNuevo->caso = $request->idCaso;
                                    $medicamentoNuevo->visible = true;
                                    $medicamentoNuevo->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                                    $medicamentoNuevo->usuario_asigna = Auth::user()->id;
                                    $medicamentoNuevo->nombre = $medi;
                                    $medicamentoNuevo->id_anterior = $existe->id;
                                    $medicamentoNuevo->save();
                                }
                            }
                        }else{
                            //no tiene ids, es un registro nuevo
                            $medicamentoNuevo = new Medicamento;
                            $medicamentoNuevo->caso = $request->idCaso;
                            $medicamentoNuevo->visible = true;
                            $medicamentoNuevo->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                            $medicamentoNuevo->usuario_asigna = Auth::user()->id;
                            $medicamentoNuevo->nombre = $medi;
                            $medicamentoNuevo->save();
                        }
                    }
                }
            }else{
                $tienemedicamentos = Medicamento::select('id')->where("caso", $request->idCaso)->where("visible", true)->get();
                foreach ($tienemedicamentos as $key => $elme) {
                    //los busca uno por uno para falsearlos
                    $falseado = Medicamento::find($elme->id);
                    $falseado->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                    $falseado->usuario_modifica = Auth::user()->id;
                    $falseado->visible = false;
                    $falseado->tipo_modificacion = 'Eliminado';
                    $falseado->save();
                }
            }

            //
            $objetoPersonal = $request->objetoPersonal;
            $idsobj = $request->idsobj;

            $a_obj_bd = []; //para almacenar los que ya estan en la bd
            $tieneobjetos = Pertenencias::select('id')->where("caso", $request->idCaso)->where("visible", true)->get();
            foreach ($tieneobjetos as $value) {
                $a_obj_bd[] = $value->id; //guardar los id de los medicamentos que hay en bd
            }
            //validar que existan medicamentos ingresados
            if($idsobj){
                //comprarar los arreglos de los datos en bd y los datos enviados
                $resultado = array_diff($a_obj_bd,$idsobj);
                //si hay resultados, se recorre
                if($resultado){
                    foreach ($resultado as $value) {
                        //se busca uno por uno y se pone en falso
                        $modobj = Pertenencias::find($value);
                        $modobj->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                        $modobj->usuario_modifica = Auth::user()->id;
                        $modobj->visible = false;
                        $modobj->tipo_modificacion = 'Eliminado';
                        $modobj->save();
                    }
                }
            }

            if($objetoPersonal){
                foreach ($objetoPersonal as $key => $obj) {
                    if(!empty($obj)){
                        $id = $request->idsobj[$key];
                        if($id != null){
                            $existe = Pertenencias::where('id',$id)->first();
                            //si encuentra un id con el nombre similar al indicado en la tabla
                            if($existe){
                                if($existe->pertenencia != $obj || $existe->responsable != $request->responsable[$key]){
                                    //falsea el encontrado
                                    $existe->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                                    $existe->usuario_modifica = Auth::user()->id;
                                    $existe->visible = false;
                                    $existe->tipo_modificacion = 'Editado';
                                    $existe->save();

                                    //crea uno nuevo
                                    $objetoNuevo = new Pertenencias;
                                    $objetoNuevo->caso = $request->idCaso;
                                    $objetoNuevo->visible = true;
                                    $objetoNuevo->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                                    $objetoNuevo->usuario = Auth::user()->id;
                                    $objetoNuevo->pertenencia = $obj;
                                    $objetoNuevo->responsable = $request->responsable[$key];
                                    $objetoNuevo->id_anterior = $existe->id;
                                    $objetoNuevo->save();
                                }
                            }
                        }else{
                            //no tiene ids, es un registro nuevo
                            $objetoNuevo = new Pertenencias;
                            $objetoNuevo->caso = $request->idCaso;
                            $objetoNuevo->visible = true;
                            $objetoNuevo->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                            $objetoNuevo->usuario = Auth::user()->id;
                            $objetoNuevo->pertenencia = $obj;
                            $objetoNuevo->responsable = $request->responsable[$key];
                            $objetoNuevo->save();
                        }
                    }
                }
            }else{
                $tieneobjetos = Pertenencias::select('id')->where("caso", $request->idCaso)->where("visible", true)->get();
                foreach ($tieneobjetos as $key => $val) {
                    //los busca uno por uno para falsearlos
                    $modobj = Pertenencias::find($val->id);
                    $modobj->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                    $modobj->usuario_modifica = Auth::user()->id;
                    $modobj->visible = false;
                    $modobj->tipo_modificacion = 'Eliminado';
                    $modobj->save();
                }
            }

            DB::commit();
            return response()->json(["exito" => "Se han ingresado los datos de anamnesis"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar los datos de anamnesis"]);
        }
    }

    public function agregarIEGeneral(Request $request){
        try {
            $tipo = 'Ingreso';
            $errores = '';

            DB::beginTransaction();
            if($request->id_formulario_ingreso_enfermeria){
                
                Log::info("Formualrio creado previamente");
                $validar_formulario = IEGeneral::formularioVacio($request);
                if($validar_formulario == true){
                    //Si se encuentra que no cumple con la cantidad de datos necesarios, este registro de  ingreso general se elimina
                    $eliminar = IEGeneral::find($request->id_formulario_ingreso_enfermeria);
                    $eliminar->usuario_modifica = Auth::user()->id;
                    $eliminar->fecha_modificacion = Carbon::now();
                    $eliminar->visible = false;
                    $eliminar->tipo_modificacion = 'Eliminado';
                    $eliminar->save();
                    DB::commit();
                    return response()->json(["exito" => "Se han eliminado los datos del examen físico general"]);
                }

                //Si hay datos, se busca el actual
                $modificar = IEGeneral::find($request->id_formulario_ingreso_enfermeria);
                //modificar actual
                $modificar->usuario_modifica = Auth::user()->id;
                $modificar->fecha_modificacion = Carbon::now();
                $modificar->visible = false;
                $modificar->tipo_modificacion = 'Editado';
                $modificar->save();


                //NOVA
                $nova = NovaHelper::novaModificacion($request, $modificar, $tipo);

                //RIESGO CAIDA 
                $riesgo = RiesgoCaidaHelper::riesgoCaidaModificacion($request, $modificar, $tipo);

                //GLASGOW 
                $glasgow = GlasgowHelper::glasgowModificacion($request, $modificar, $tipo);

                //BARTHEL 
                $barthel = BarthelHelper::barthelModificacion($request, $modificar, $tipo);


                if ($nova['error'] != "" || $riesgo['error'] != "" || $glasgow['error'] != "" || $barthel['error'] != "") {
                    
                    Log::info("NOVA modificar (error)");
                    Log::info($nova['error']);
                    Log::info("RIESGO CAIDA modificar (error)");
                    Log::info($riesgo['error']);
                    Log::info("GLASGOW modificar (error)");
                    Log::info($glasgow['error']);
                    Log::info("BARTHEL modificar (error)");
                    Log::info($barthel['error']);
                    DB::rollback();
                    return response()->json(["error" => $nova['error'].",".$riesgo['error'].",".$glasgow['error'].",".$barthel['error']]);
                }
                
                $indnova = ($nova['exito'] != '') ? $nova['exito']->id_formulario_escala_nova : null;   
                $indriesgo = ($riesgo['exito'] != '') ? $riesgo['exito']->id : null;   
                $indglasgow = ($glasgow['exito'] != '') ? $glasgow['exito']->id_formulario_escala_glasgow : null;   
                $indbarthel = ($barthel['exito'] != '') ? $barthel['exito']->id_formulario_barthel : null;   

                $copia = IEGeneral::crearNuevo($request, $modificar,$indglasgow,$indbarthel,$indriesgo,$indnova);
                $copia->visible = true;
                $copia->save();
            }
            else{
                $validar_formulario = IEGeneral::formularioVacio($request);
                if($validar_formulario == true){
                    return response()->json(["info" => "Debe ingresar al menos un valor para poder guardar el formulario"]);
                }else{

                    Log::info("Formularios nuevo");
                    //NOVA
                    $nuevonova = NovaHelper::novaModificacion($request, $modificar = null, $tipo);

                    //RIESGO CAIDA 
                    $nuevoriesgo = RiesgoCaidaHelper::riesgoCaidaModificacion($request, $modificar = null, $tipo);

                    //GLASGOW
                    $nuevoGlasgow = GlasgowHelper::glasgowModificacion($request, $modificar = null, $tipo);

                    //BARTHEL 
                    $nuevobarthel = BarthelHelper::barthelModificacion($request, $modificar = null,$tipo);

                    if ($nuevonova['error'] != "" || $nuevoriesgo['error'] != "" || $nuevoGlasgow['error'] != "" || $nuevobarthel['error'] != "") {
                        Log::info("NOVA nuevo (error)");
                        Log::info($nuevonova['error']);
                        Log::info("RIESGO CAIDA nuevo (error)");
                        Log::info($nuevoriesgo['error']);
                        Log::info("GLASGOW nuevo (error)");
                        Log::info($nuevoGlasgow['error']);
                        Log::info("BARTHEL nuevo (error)");
                        Log::info($nuevobarthel['error']);
                        DB::rollback();
                        return response()->json(["error" => $nuevonova['error'].",".$nuevoriesgo['error'].",".$nuevoGlasgow['error'].",".$nuevobarthel['error']]);   
                    }

                    $indnova = ($nuevonova['exito'] != '') ? $nuevonova['exito']->id_formulario_escala_nova : null;   
                    $indriesgo = ($nuevoriesgo['exito'] != '') ? $nuevoriesgo['exito']->id : null;   
                    $indglasgow = ($nuevoGlasgow['exito'] != '') ? $nuevoGlasgow['exito']->id_formulario_escala_glasgow : null;   
                    $indbarthel = ($nuevobarthel['exito'] != '') ? $nuevobarthel['exito']->id_formulario_barthel : null;   

                    $nuevo = IEGeneral::crearNuevo($request,$modificar = null,$indglasgow,$indbarthel,$indriesgo,$indnova);
                    $nuevo->visible = true;
                    $nuevo->save();
                }
            }
            DB::commit();
            return response()->json(["exito" => "Se han ingresado los datos del examen físico general"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar los datos del examen físico general"]);
        }
    }

    public function agregarIESegmentario(Request $request){
        DB::beginTransaction();
        try {
            if($request->id_formulario_ingreso_enfermeria){
                //buscar actual
                $modificar = IESegmentado::find($request->id_formulario_ingreso_enfermeria);
                //modificar actual
                $modificar->usuario_modifica = Auth::user()->id;
                $modificar->fecha_modificacion = Carbon::now();
                $modificar->visible = false;
                $modificar->tipo_modificacion = 'Editado';
                $modificar->save();

                $copia = IESegmentado::crearNuevo($request, $modificar);
                $copia->visible = true;
                $copia->save();
            }
            else{
                $nuevo = IESegmentado::crearNuevo($request, $modificar = null);
                $nuevo->visible = true;
                $nuevo->save();
            }
            DB::commit();
            return response()->json(["exito" => "Se han ingresado los datos del examen físico segmentario"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar los datos del examen físico segmentario"]);
        }
    }

    public function agregarIEOtros(Request $request){
        DB::beginTransaction();
        $listaCateteres = [];
        if ($request->cateteres) {
            $listaCateteres = $request->cateteres;
        }else{
            return response()->json(["error" => "Debe seleccionar al menos 1 cateter"]);
        }
        $tipos_cateteres_array = ["0","1","2","3","4","5","6","7","8"];
        try {
            //buscar cuales de los cateteres no fueron 
            //Detectar los cateteres que no fueron seleccionados en el listado
            $diff = array_diff($tipos_cateteres_array, $listaCateteres);
            foreach ($diff as $value) {
                //comprobar si existe y eliminarlos o ocultarlo
                $ocultar = Cateter::where([["caso", $request->idCaso], ["tipo_cateter",$value], ["visible",true]])->first();
                if ($ocultar) {
                    $ocultar->usuario_modifica = Auth::user()->id;
                    $ocultar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                    $ocultar->visible = false;
                    $ocultar->tipo_modificacion = 'Eliminado';
                    $ocultar->save();
                }
            }
            //dd($diff);

            //comparar el id cateter que me envian desde el front con uno existente 
            foreach($listaCateteres as $key => $cateters){
                if(isset($request->idcateter[$key]) && $request->idcateter[$key] != ''){
                    //Si posee un id de cataeter significa que tuvo un anterior
                    /*LLENAR Y GENERAR UN ARREGLO CON DATOS DEL FORMULARIO PARA PODER COMPRARLO*/
                    //dd( $request->fechaCura[$key] );
                    $boolean = NULL;
                    if($cateters == '7'){
                        if($request->baguetaOsto == 'no'){
                            $boolean = false;
                        }elseif($request->baguetaOsto == 'si'){
                            $boolean = true;
                        }
                    }

                    $arrayForm= array(
                        "id" => (int)$request->idcateter[$key],
                        "caso" => $request->idCaso,
                        "numero" => $request->numero[$key],
                        "fecha_instalacion" => Carbon::parse($request->fecha[$key])->format("Y-m-d H:i:s"),
                        "lugar_instalacion" => $request->lugar[$key],
                        "responsable_instalcion" => $request->responsableInst[$key],
                        "material_fabricacion" => $request->material[$key],
                        "fecha_curacion" => ($request->fechaCura[$key] != '')?Carbon::parse($request->fechaCura[$key])->format("Y-m-d H:i:s"):NULL,
                        "responsable_curacioin" => $request->responsableCura[$key],
                        "observacion" => $request->observacion[$key],
                        "tipo" => ($request->tipo[$key] !== '0')?$request->tipo[$key]:NULL,
                        "via_instalacion" => ($cateters == '4' && $request->viaCvc != '')?$request->viaCvc:NULL,
                        "medicion_cuff" => ($cateters == '6' && $request->cuffTraqueo !='')?$request->cuffTraqueo:NULL,
                        "cuidado_enfermeria" => ($cateters == '7' && $request->cuidadoOsto !='')?$request->cuidadoOsto:NULL,
                        "valoracion_estomaypiel" => ($cateters == '7' && $request->valoracionEstomaOsto != '')?$request->valoracionEstomaOsto:NULL,
                        "responsable_curacion_ostomias" => ($cateters == '7' && $request->cuidadoEstomaOsto != '')?$request->cuidadoEstomaOsto:NULL,
                        "medicion_efluente" => ($cateters == '7' && $request->medicionEfluenteOsto != '')?$request->medicionEfluenteOsto:NULL,
                        "detalle_educacion" => ($cateters == '7' && $request->detalleEducacionOsto != '')?$request->detalleEducacionOsto:NULL,
                        "bagueta" => $boolean,
                        "tipo_cateter" => $cateters,
                        "detalle" => ($cateters == '8' && $request->detalleOtro != '') ? $request->detalleOtro:NULL,
                        "via_instalacion_otro" => ($cateters == '8' && $request->viaOtro != '') ? $request->viaOtro:NULL,
                    );
                    
                    /*Se envia el arreglo para comparar*/
                    $Modifica =  Cateter::diferencia($request->idcateter[$key],$arrayForm);
                    //dd($Modifica);
                    if($Modifica == 'modificar'){
                        //modificar actual
                        $modificar = Cateter::find($request->idcateter[$key]);                        
                        $modificar->usuario_modifica = Auth::user()->id;
                        $modificar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                        $modificar->visible = false;
                        $modificar->tipo_modificacion = 'Editado';
                        $modificar->save();
                        // Log::info("modificado: ");
                        // Log::info($modificar);

                        $cateter = new Cateter;
                        $cateter->caso = strip_tags($request->idCaso);
                        $cateter->id_anterior = $modificar->id;
                        $cateter->usuario_responsable = Auth::user()->id;
                        $cateter->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                        $cateter->visible = true;
                        $cateter->tipo_cateter = $cateters;
                        $cateter->numero = ($request->numero[$key] != '')?strip_tags($request->numero[$key]):NULL;
                        $cateter->fecha_instalacion = ($request->fecha[$key] != '')?Carbon::parse(strip_tags($request->fecha[$key]))->format("Y-m-d H:i:s"):NULL;
                        $cateter->lugar_instalacion = ($request->lugar[$key] != '')?strip_tags($request->lugar[$key]):NULL;

                        $cateter->responsable_instalcion = ($request->responsableInst[$key] != '')?strip_tags($request->responsableInst[$key]):NULL;
                        $cateter->material_fabricacion = ($request->material[$key] != '')?strip_tags($request->material[$key]):NULL;
                        $cateter->fecha_curacion = ($request->fechaCura[$key] != '')?Carbon::parse(strip_tags($request->fechaCura[$key]))->format("Y-m-d H:i:s"):NULL;

                        $cateter->responsable_curacioin = ($request->responsableCura[$key] != '')?strip_tags($request->responsableCura[$key]):NULL; 
                        $cateter->observacion = ($request->observacion[$key] != '')?strip_tags($request->observacion[$key]):NULL; 

                        if($cateters == '4'){
                            $cateter->via_instalacion = ($request->viaCvc != '')?$request->viaCvc:NULL;
                        }
                        if($cateters == '6'){
                            $cateter->medicion_cuff =   ($request->cuffTraqueo !='')?$request->cuffTraqueo:NULL;
                        }else{
                            $cateter->medicion_cuff = null;
                        }
                        if($request->tipo[$key] !='0'){
                            $tipo = $request->tipo[$key] ;
                        }else{
                            $tipo = null;
                        }

                        if($cateters == '7'){
                            /*if ($request->tipo[$cateters]!='' || $request->tipo[$cateters]!=0) {
                            $cateter->tipo = $request->tipo[$cateters] ;
                            }else{ $cateter->tipo = null; }*/
                            $boolean = NULL;
                            if($request->baguetaOsto == 'no'){
                                $boolean = false;
                            }elseif($request->baguetaOsto == 'si'){
                                $boolean = true;
                            }
                            $cateter->cuidado_enfermeria =  ($request->cuidadoOsto !='')?$request->cuidadoOsto:NULL ;
                            $cateter->valoracion_estomaypiel = ($request->valoracionEstomaOsto != '')?$request->valoracionEstomaOsto:NULL ;
                            $cateter->responsable_curacion_ostomias =( $request->cuidadoEstomaOsto != '')?$request->cuidadoEstomaOsto:NULL ;
                            $cateter->medicion_efluente = ($request->medicionEfluenteOsto != '')?$request->medicionEfluenteOsto:NULL ;
                            $cateter->detalle_educacion = ($request->detalleEducacionOsto !='')?$request->detalleEducacionOsto:NULL ;
 
                            $cateter->bagueta = $boolean ;
                        }

                        $cateter->tipo = $tipo;

                        if($cateters == '8'){ Log::info($request);
                            $cateter->detalle = $request->detalle;
                            $cateter->via_instalacion_otro = $request->viaOtro;
                        }

                        $cateter->save();
                    }

                }else{
                   

                    $cateter = new Cateter;
                    $cateter->caso = strip_tags($request->idCaso);
                    $cateter->usuario_responsable = Auth::user()->id;
                    $cateter->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                    $cateter->visible = true;
                    $cateter->tipo_cateter = $cateters;
                    $cateter->numero = ($request->numero[$key] != '')?strip_tags($request->numero[$key]):NULL;
                    $cateter->fecha_instalacion = ($request->fecha[$key] != '')?Carbon::parse(strip_tags($request->fecha[$key]))->format("Y-m-d H:i:s"):NULL;
                    $cateter->lugar_instalacion = ($request->lugar[$key] != '')?strip_tags($request->lugar[$key]):NULL;

                    $cateter->responsable_instalcion = ($request->responsableInst[$key] != '')?strip_tags($request->responsableInst[$key]):NULL;
                    $cateter->material_fabricacion = ($request->material[$key] != '')?strip_tags($request->material[$key]):NULL;
                    $cateter->fecha_curacion = ($request->fechaCura[$key] != '')?Carbon::parse(strip_tags($request->fechaCura[$key]))->format("Y-m-d H:i:s"):NULL;

                    $cateter->responsable_curacioin = ($request->responsableCura[$key] != '')?strip_tags($request->responsableCura[$key]):NULL; 
                    $cateter->observacion = ($request->observacion[$key] != '')?strip_tags($request->observacion[$key]):NULL; 

                    if($cateters == '4'){
                        $cateter->via_instalacion = $request->viaCvc ;
                    }
                    if($cateters == '6'){
                        $cateter->medicion_cuff = $request->cuffTraqueo ;
                    }else{
                        $cateter->medicion_cuff = null;
                    }
                    if($request->tipo[$key] !='0'){
                        $tipo = $request->tipo[$key] ;
                    }else{
                        $tipo = null;
                    }

                    if($cateters == '7'){
                        $boolean = NULL;
                        if($request->baguetaOsto == 'no'){
                            $boolean = false;
                        }elseif($request->baguetaOsto == 'si'){
                            $boolean = true;
                        }
                        $cateter->cuidado_enfermeria =  ($request->cuidadoOsto !='')?$request->cuidadoOsto:NULL ;
                        $cateter->valoracion_estomaypiel = ($request->valoracionEstomaOsto != '')?$request->valoracionEstomaOsto:NULL ;
                        $cateter->responsable_curacion_ostomias =( $request->cuidadoEstomaOsto != '')?$request->cuidadoEstomaOsto:NULL ;
                        $cateter->medicion_efluente = ($request->medicionEfluenteOsto != '')?$request->medicionEfluenteOsto:NULL ;
                        $cateter->detalle_educacion = ($request->detalleEducacionOsto !='')?$request->detalleEducacionOsto:NULL ;
                        $cateter->bagueta = $boolean ;
                    }
                    $cateter->tipo = $tipo;

                    if($cateters == '8'){ Log::info($request);
                        $cateter->detalle = $request->detalle;
                        $cateter->via_instalacion_otro = $request->viaOtro;
                    }

                    $cateter->save();
                }

            }

            DB::commit();
            return response()->json(["exito" => "Se han ingresado los datos exitosamente"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar los datos"]);
        }

    }

    public function obtenerIndicacionesMedicas(Request $request){
        return Indicacion::indicacionesMedicas($request->caso);
    }

    public function pdfResumenHojaIngresoEnfermeria($caso){
        try {
            $fecha = Carbon::now()->format('d-m-Y');
            $anamnesis = IEAnamnesis::where('caso',$caso)->where('visible', true)->first();
            $ginecologico = '';
            if(!empty($anamnesis) && isset($anamnesis->idginecologica) && $anamnesis->idginecologica != null || !empty($anamnesis) && isset($anamnesis->idginecologica) && $anamnesis->idginecologica != ''){
                $ginecologico = Ginecologica::where('id',$anamnesis->idginecologica)->where('visible', true)->first();
            }
            $medicamentos = Medicamento::where('caso',$caso)->where('visible', true)->get();
            $general = IEGeneral::where('caso',$caso)->where('visible', true)->first();
            $segmentado = IESegmentado::where('caso',$caso)->where('visible', true)->first();
            $otros = Cateter::where('caso',$caso)->where('visible', true)->orderBy('tipo_cateter', 'asc')->get();
            $paciente = Paciente::getPacientePorCaso($caso);
		    $establecimiento = Establecimiento::where("id",Auth::user()->establecimiento)->first();
            $prevision = Caso::find($caso,'prevision');
            $telefonos = Telefono::where('id_paciente',$paciente->id)->get();
            $fecha_ingreso_enfermeria = Consultas::fechaPrimerRegistroIngresoEnfermeria($caso);
			$ginecoobstetrico = FormularioExamenGinecoobstetrico::where('caso',$caso)->where('visible', true)->first();
            //$epicrisis = InformeEpicrisis::datosEpicrisis($caso); 
			
			$ubicacion = DB::table('t_historial_ocupaciones as t')
                    ->join("camas as c", "c.id", "=", "t.cama")
                    ->join("salas as s", "c.sala", "=", "s.id")
                    ->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
                    ->join("area_funcional AS af", "uee.id_area_funcional", "=", "af.id_area_funcional")
                    ->where("t.caso", $caso)
                    ->whereNull("t.motivo")
                    ->select("uee.alias as nombre_unidad",  "af.nombre as nombre_area_funcional","uee.id")
                    ->first();

			$sub_categoria = HistorialSubcategoriaUnidad::select("id_subcategoria")->where('id_unidad',$ubicacion->id)->where('visible',true)->first();

            $pdf = PDF::loadView('Gestion.gestionEnfermeria.partesIngresoEnfermeria.pdfResumenIngresoEnfermeria',
                [
                "anamnesis" => $anamnesis,
                "ginecologico" => $ginecologico,
                "medicamentos" => $medicamentos,
                "general" => $general,
                "segmentado" => $segmentado,
                "otros" => $otros,
                "paciente" => $paciente,
                "telefonos" => $telefonos,
                "prevision" => $prevision->prevision,
                "establecimiento" => $establecimiento->nombre,
                "fecha_ingreso_enfermeria" => $fecha_ingreso_enfermeria,
				"sub_categoria" => $sub_categoria ? $sub_categoria->id_subcategoria : null,
				"ginecoobstetrico" => $ginecoobstetrico
                //"sub_categoria" => $epicrisis["sub_categoria"]
                ]);
            return $pdf->inline('Resumen_ingreso_enfermeria_'.$fecha.'.pdf');
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    public function obtenerHistoricoAnamnesis($caso){
        $response = HojaIngresoEnfermeria::historicoAnamnesis($caso);
        return response()->json(["aaData" => $response]);
    }

    public function obtenerHistoricoGeneral($caso){
        $response = HojaIngresoEnfermeria::historicoGeneral($caso);
        return response()->json(["aaData" => $response]);
    }

    public function obtenerHistoricoSegmentario($caso){
        $response = HojaIngresoEnfermeria::historicoSegmentario($caso);
        return response()->json(["aaData" => $response]);
    }

}
