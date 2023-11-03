<?php

namespace App\Http\Controllers;

use App\Helpers\InterConsultaHelper;
use App\Helpers\SignosVitalesHelper;
use App\Models\CaracteristicasAgente;
use App\Models\HojaEnfermeria;
use App\Models\HojaEnfermeriaControlEgreso;
use App\Models\HojaEnfermeriaControlEstada;
use App\Models\HojaEnfermeriaControlSignoVital;
use App\Models\HojaEnfermeriaCuidadoEnfermeria;
use App\Models\HojaEnfermeriaCuidadoEnfermeriaAtencion;
use App\Models\HojaEnfermeriaCuidadoEnfermeriaIndicacion;
use App\Models\HojaEnfermeriaEnfermeriaIndicacionMedica;
use App\Models\HojaEnfermeriaExamenImagen;
use App\Models\HojaEnfermeriaExamenLaboratorio;
use App\Models\HojaEnfermeriaInterconsulta;
use App\Models\HojaEnfermeriaProcedimientoInvasivo;
use App\Models\HojaEnfermeriaRiesgoCaida;
use App\Models\HojaEnfermeriaValoracionEnfermeria;
use App\Models\HojaEnfermeriaVolumenSolucion;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\Telefono;
use App\Models\Caso;
use App\Models\IndicacionMedica;
use App\Models\ArsenalFarmacia;
use App\Models\PlanificacionIndicacionMedica;
use App\Models\PlanificacionCuidadoAtencionEnfermeria;
use Auth;
use Carbon\Carbon;
use Crypt;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Log;
use View;
use App\Models\InformeEpicrisis;

class EnfermeriaHojaEnfermeriaController extends Controller
{
    public static function datosHoja(Request $request)
    {

        $response = [];

        $hojaEnfermeria = HojaEnfermeria::where("id_formulario_hoja_enfermeria", $request->idHojaEnfermeria)->first();
        $HojaEnfermeriaControlSignoVital = HojaEnfermeriaControlSignoVital::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();
        $HojaEnfermeriaVolumenSolucion = HojaEnfermeriaVolumenSolucion::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();
        $HojaEnfermeriaControlEgreso = HojaEnfermeriaControlEgreso::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();
        $HojaEnfermeriaExamenLaboratorio = HojaEnfermeriaExamenLaboratorio::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();
        $HojaEnfermeriaExamenImagen = HojaEnfermeriaExamenImagen::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();
        $HojaEnfermeriaCuidadoEnfermeria = HojaEnfermeriaCuidadoEnfermeria::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();
        $HojaEnfermeriaInterconsulta = HojaEnfermeriaInterconsulta::where("id_hoja_enfermeria", $hojaEnfermeria->id_formulario_hoja_enfermeria)->get();

        $response = [
            "hojaEnfermeria" => $hojaEnfermeria,
            "HojaEnfermeriaControlSignoVital" => $HojaEnfermeriaControlSignoVital,
            "HojaEnfermeriaVolumenSolucion" => $HojaEnfermeriaVolumenSolucion,
            "HojaEnfermeriaControlEgreso" => $HojaEnfermeriaControlEgreso,
            "HojaEnfermeriaExamenLaboratorio" => $HojaEnfermeriaExamenLaboratorio,
            "HojaEnfermeriaExamenImagen" => $HojaEnfermeriaExamenImagen,
            "HojaEnfermeriaCuidadoEnfermeria" => $HojaEnfermeriaCuidadoEnfermeria,
            "HojaEnfermeriaInterconsulta" => $HojaEnfermeriaInterconsulta,
        ];

        return response()->json($response);
    }

    public static function buscarHistorialHojaEnfermeria(Request $request)
    {
        $HojasDeEnfermeria = HojaEnfermeria::where("caso", strip_tags($request->idCaso))->get();
        $response = [];

        foreach ($HojasDeEnfermeria as $hojaEnfermeria) {

            $usuario = Usuario::where("id", $hojaEnfermeria->usuario_responsable)->first();
            $response[] = [
                "<b>Usuario responsable: </b>" . $usuario->nombres . " " . $usuario->apellido_paterno . " " . $usuario->apellido_materno . "<br> <b>Fecha de creación: </b>" . Carbon::parse($hojaEnfermeria->fecha_creacion)->format("d-m-Y H:m:i"),
                "<button class='btn btn-primary' type='button' onclick='editar(" . $hojaEnfermeria->id_formulario_hoja_enfermeria . ")'>Ver/Editar</button>",
                "<b>Indicaciones</b>: " . $hojaEnfermeria->indicacion . "<br> <b>Horario: </b>" . $hojaEnfermeria->horario,
                "<div class='col'><b>Nombre Enfermera(o) Largo</b>: " . $hojaEnfermeria->enfermeraturnol . "<br> <b>Descripción Largo: </b>" . $hojaEnfermeria->valoracionturnol . "</div><br><div class='col'><b>Nombre Enfermera(o) Noche</b>: " . $hojaEnfermeria->enfermeraturnon . "<br> <b>Descripción Noche: </b>" . $hojaEnfermeria->valoracionturnon . "</div>"
                ,

            ];

        }
        return response()->json($response);
    }

    public static function histHojaEnfemeria($caso)
    {
        $info = DB::table("formulario_hoja_enfermeria as f")
            ->where("f.caso", $caso)
            ->orderBy("f.fecha_creacion", "asc")
            ->get();

        $paciente = DB::table("pacientes as p")
            ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno")
            ->join("casos as c", "p.id", "=", "c.paciente")
            ->where("c.id", $caso)
            ->first();

        return View::make("Gestion/gestionEnfermeria/historialHojaEnfermeria")
            ->with(array(
                "caso" => $caso,
                "info" => $info,
                "paciente" => $paciente->nombre . " " . $paciente->apellido_paterno . " " . $paciente->apellido_materno,
            ));
    }

    public function eliminarCuidadoEnfermeria(Request $request)
    {

        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = HojaEnfermeriaCuidadoEnfermeria::where("id", strip_tags($request->id))->first();
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now();
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado control egreso exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar control egreso"]);
        }
    }

    public function obtenerCuidadosEnfermeria($caso)
    {

        /* Falta modificar */
        $fin = Carbon::now()->endOfDay();

        $cuidadoEnf = DB::select(DB::raw("select
                f.id,
                f.fecha_creacion,
                f.turno,
                c.tipo,
                c.id as id_cuidado,
                f.turno,
                f.realizado,
                f.tipo_realizado,
                f.hora,
                fi.indicacion
                from formulario_hoja_enfermeria_cuidado_enfermeria as f
                inner join usuarios as u on u.id = f.usuario
                left join tipo_cuidado as c on c.id = f.tipo
                left join formulario_planificacion_cuidados_indicaciones_medicas as fi  on fi.id = f.tipo_indicacion
                where
                f.caso = $caso and f.visible = true and f.fecha_vigencia > '$inicio' and f.fecha_vigencia < '$fin'
                "));

        $tipo = [];
        /* separar */
        foreach ($cuidadoEnf as $key => $cuidado) {
            $valor = "";
            if ($cuidado->id_cuidado) {
                /* si es un tipo_cuidado */
                if ($cuidado->id_cuidado >= 1 && $cuidado->id_cuidado <= 14) {
                    /* solo tienen hora */
                    $valor = Carbon::parse($cuidado->hora)->format("H:i");
                } else if ($cuidado->id_cuidado >= 15 && $cuidado->id_cuidado <= 21) {
                    /* solo tienen un si o no */
                    $valor = ($cuidado->realizado == true) ? 'Si' : 'No';
                } else if ($cuidado->id_cuidado == 24 || $cuidado->id_cuidado == 25) {
                    /* solo posicion y hora */
                    $valor = "<p style='font-size: 13px;'>" . $cuidado->tipo_realizado . " " . Carbon::parse($cuidado->hora)->format("H:i") . "</p>";
                } else {
                    /* solo hora */
                    $valor = Carbon::parse($cuidado->hora)->format("H:i");
                }
            } else {
                /* es una indicacion */
                $valor = "<p style='font-size: 13px;'>" . $cuidado->tipo_realizado . " " . Carbon::parse($cuidado->hora)->format("H:i") . "</p>";
            }

            /* seleccionar el nombre del tipo */
            $nombre_tipo = ($cuidado->tipo) ? $cuidado->tipo . "<br><b> (Atención enfermeria)</b>" : ucwords($cuidado->indicacion) . "<br><b> (Indicación medica)</b>";

            if (!array_key_exists($nombre_tipo, $tipo)) {
                /* en caso de que no esten creados, se habilitan */
                if ($cuidado->turno == "TURNO DÍA") {
                    $tipo[$nombre_tipo]["dia"] = "<div class='colorCelda'><div class=''><button class='btn btn-danger botonCerrar' type='button' onclick='eliminarCuidado($cuidado->id)'>X</button></div><div class=' valorInterno'>$valor</div></div>";
                    $tipo[$nombre_tipo]["noche"] = "";
                } else {
                    $tipo[$nombre_tipo]["dia"] = "";
                    $tipo[$nombre_tipo]["noche"] = "<div class='colorCelda'><div class=''><button class='btn btn-danger botonCerrar' type='button' onclick='eliminarCuidado($cuidado->id)'>X</button></div><div class=' valorInterno'>$valor</div></div>";
                }
            } else {
                /* sino se siguen incorporando nuevos */
                if ($cuidado->turno == "TURNO DÍA") {
                    $tipo[$nombre_tipo]["dia"] .= "<div class='colorCelda'><div class=''><button class='btn btn-danger botonCerrar' type='button' onclick='eliminarCuidado($cuidado->id)'>X</button></div><div class=' valorInterno'>$valor</div></div>";
                } else {
                    $tipo[$nombre_tipo]["noche"] .= "<div class='colorCelda'><div class=''><button class='btn btn-danger botonCerrar' type='button' onclick='eliminarCuidado($cuidado->id)'>X</button></div><div class=' valorInterno'>$valor</div></div>";
                }
            }
        }

        $resultado = [];
        /* ordenar para el datatable */
        foreach ($tipo as $key => $t) {
            $resultado[] = [
                $key,
                $t["dia"],
                $t["noche"],
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function anadirCheckParaSignosVitales(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->data;
            $signosH = new SignosVitalesHelper();
            $signosH->anadirCheckParaSignosVitales($data);

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado el cuidado de enfemería exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al agregar cuidados de enfemería"]);
        }
    }

    public function addCuidadoEnfermeria(Request $request)
    {

        try {
            Log::info($request);

            $fecha_1 = strip_tags($request->fecha_uno);
            $fecha_2 = strip_tags($request->fecha_dos);

            $turno = ["21", "22", "23", "00", "01", "02", "03", "04", "05", "06", "07", "08"];
            if ($request->turno == "dia") {
                $turno = ["09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20"];
            }

            Log::info('entra a esta funcion');

            DB::beginTransaction();
            if (isset($request->cuidadoRealizado)) {
                foreach ($request->cuidadoRealizado as $cuidado) {
                    $delimiter = explode('_', $cuidado);

                    //delimiter 1 trae el id, ya sea de indicacion o atencion enfermeria
                    //delimiter 2 trae si es indicacion o atencion enfermeria
                    if ($delimiter[2] == 'c') {

                        $existe = PlanificacionCuidadoAtencionEnfermeria::where('id',$delimiter[1])->where('visible',true)
                        ->whereNull("tipo_modificacion")->first();
    
                        if(empty($existe)){
                            //Esto indica que la planificacion fue actualizada y ya no existe o fue cambiada
                            return response()->json(["error" => "Error al agregar la atención de enfemería. Planificación actualizada"]);
                        }

                        //Con esto se decide en que hora debe revisarse si el cuidado fue realizado
                        if ($fecha_1 != null && $fecha_2 != null ) {
                            //Significa que hay 2 dias dentro de las opciones de fecha y debe verse la que corresponde
                            if ((int) $turno[$delimiter[0]] <= 23 || (int) $turno[$delimiter[0]] > 8) {
                                //Si es una hora menor o igual a las 23 o mayor a las 8 Significa que debe ver la fecha_1
                                $fecha_usar = $fecha_1;
                            }else{
                                //Significa que tomo la de la fecha 2
                                $fecha_usar = $fecha_2;
                            }
                        }else if($fecha_1){
                            //Significa que solo hay que figjarse en ese dia
                            $fecha_usar = $fecha_1;
                        }

                        $existeHora = HojaEnfermeriaCuidadoEnfermeriaAtencion::where('id_atencion',$delimiter[1])
                            ->where('horario',$turno[$delimiter[0]])
                            ->where('fecha_creacion', $fecha_usar)
                            ->first();

                        if($existeHora){
                            //Este error es debido a que el cuidado ya fue marcado
                            return response()->json(["error" => "Error al agregar la atención de enfemería. Cuidados seleccionados posiblemnete fueron marcados"]);
                        }
                        
                        $atencion = new HojaEnfermeriaCuidadoEnfermeriaAtencion;
                        $atencion->id_atencion = $delimiter[1];

                    } else {
                        $existe = PlanificacionIndicacionMedica::where('id',$delimiter[1])->where('visible',true)
                            ->whereNull("tipo_modificacion")->first();
                            
                        if(empty($existe)){
                            //No existe planificacion asignada para ese dia
                            return response()->json(["error" => "Error al agregar cuidados de enfemería"]);
                        }
                        $atencion = new HojaEnfermeriaCuidadoEnfermeriaIndicacion;
                        $atencion->id_indicacion = $delimiter[1];
                    }

                    $atencion->usuario = Auth::user()->id;
                    $hora = (int) $turno[$delimiter[0]];

                    if ($request->turno === "dia") {
                        $atencion->fecha_creacion = Carbon::createFromFormat('d-m-Y H', $fecha_1 . " " . $hora)->format('Y-m-d H:i:s');
                    } else if ($request->turno === "noche") {
                        if ($hora >= 21 && $hora <= 23) {
                            $atencion->fecha_creacion = Carbon::createFromFormat('d-m-Y H', $fecha_1 . " " . $hora)->format('Y-m-d H:i:s');
                        } else if ($hora >= 0 && $hora <= 8) {
                            $atencion->fecha_creacion = Carbon::createFromFormat('d-m-Y H', $fecha_2 . " " . $hora)->format('Y-m-d H:i:s');
                        }
                    }

                    $atencion->horario = $turno[$delimiter[0]]; //delimiter 0 trae el id del turno que se uso dependiendo si es dia o noche
                    $atencion->realizado = true;
                    $atencion->visible = true;
                    $atencion->save();

                }
            }
            
            if(isset($request->cuidadoMedicoRealizado)){
                foreach ($request->cuidadoMedicoRealizado as $cuidado) {
                    $delimiter = explode('_', $cuidado);

                    //delimiter 1 trae el id, ya sea de indicacion o atencion enfermeria
                    //delimiter 2 trae si es indicacion o atencion enfermeria

                    $hora = (int) $turno[$delimiter[0]];
                   
                    $existe = PlanificacionIndicacionMedica::where('id',$delimiter[1])->where('visible',true)
                    ->whereNull("tipo_modificacion")->first();

                    if(!empty($existe)){
                        $atencion = new HojaEnfermeriaEnfermeriaIndicacionMedica;
                        if ($request->turno === "dia") {
                            $atencion->fecha_creacion = Carbon::createFromFormat('d-m-Y H', $fecha_1 . " " . $hora)->format('Y-m-d H:i:s');
                        } else if ($request->turno === "noche") {
                            if ($hora >= 21 && $hora <= 23) {
                                $atencion->fecha_creacion = Carbon::createFromFormat('d-m-Y H', $fecha_1 . " " . $hora)->format('Y-m-d H:i:s');
                            } else if ($hora >= 0 && $hora <= 8) {
                                $atencion->fecha_creacion = Carbon::createFromFormat('d-m-Y H', $fecha_2 . " " . $hora)->format('Y-m-d H:i:s');
                            }
                        }
    
                        $atencion->id_indicacion = $delimiter[1];
                        $atencion->usuario = Auth::user()->id;
                        $atencion->horario = $turno[$delimiter[0]]; //delimiter 0 trae el id del turno que se uso dependiendo si es dia o noche
                        $atencion->realizado = true;
                        $atencion->visible = true;
                        $atencion->save();
                    }else{
                        return response()->json(["error" => "Error al agregar cuidados de enfemería"]);
                    }

                }
            }
            
            DB::commit();
            return response()->json(["exito" => "Se ha ingresado el cuidado de enfemería exitosamente"]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info($e);
            return response()->json(["error" => "Error al agregar cuidados de enfemería"]);
        }
    }

    public function getCuidadosSignosVitalesJson(Request $request)
    {
        try {
            $signosH = new SignosVitalesHelper();
            $signos_vitales_json = $signosH->getAllSignosVitalesCuidadosDelDia($request);
            $signos_vitales_json = (count($signos_vitales_json) > 0) ? json_encode($signos_vitales_json) : "[]";
            return response()->json(["data" => $signos_vitales_json], 200);
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(["data" => "Ha ocurrido un error con la obtención de los datos de signos vitales"], 500);
        }

    }

    public function checkSiPlanificacionExisteDespuesQueIngresoDeSignos(Request $request)
    {
        try {
            $caso_id = $request->caso_id;
            $signos_vitales_id = $request->signos_vitales_id;
            $hora = $request->hora;
            $fecha = $request->fecha;

            $signosH = new SignosVitalesHelper();
            $data = $signosH->comprobarIngresoDeSignosSinCheck($caso_id, $signos_vitales_id, $hora, $fecha);
            return response()->json(["data" => $data], 200);
        } catch (Exception $e) {
            return response()->json(["data" => "Ha ocurrido un error"], 500);
        }

    }

    public function obtenerCuidados($caso)
    {

        $hora = (int) Carbon::now()->format('H');

        //horario en que se encuentra el paciente
        if ($hora < 9 || $hora >= 21) {
            $horario = "(f.horario < 9 or f.horario >= 21)";
            $turno = ["21", "22", "23", "00", "01", "02", "03", "04", "05", "06", "07", "08"];
            $tipo_turno = "noche";
            $fecha_inicio = Carbon::now()->subDay(1)->format('Y-m-d H:i:s'); //dia de ayer
            $fecha_inicio2 = Carbon::now()->startOfDay()->format('Y-m-d H:i:s'); // inidio del mismo dia
            $fecha_fin = Carbon::now()->addDay(1)->endOfDay()->format('Y-m-d H:i:s'); // dia de mañana al final del dia
            $inicio_ayer = Carbon::now()->subDay(1)->startOfDay()->format('Y-m-d H:i:s'); //inicio dia de ayer
            $fin_ayer = Carbon::now()->subDay(1)->endOfDay()->format('Y-m-d H:i:s'); //inicio dia de ayer
            $fin_hoy = Carbon::now()->endOfDay()->format('Y-m-d H:i:s'); //inicio dia de ayer
            $inicio_mañana = Carbon::now()->addDay(1)->startOfDay()->format('Y-m-d H:i:s'); // dia de mañana al inicio del dia

            if ($hora < 24 && $hora >= 21) {
                //Si son menor que las 24 y mayor a las 21, significa que debe tener los datos de hoy y mañana
                $sql_rango_indicaciones = "caso = $caso and visible = true and(
                    (fecha_emision is null and fecha_vigencia BETWEEN '$fecha_inicio2' AND '$fecha_fin')
                    or (fecha_emision is not null and fecha_emision <= '$fecha_inicio2' and (fecha_vigencia >= '$fecha_inicio2' and fecha_vigencia <= '$fecha_fin') )
                    or (fecha_emision is not null and fecha_emision >= '$fecha_inicio2' and fecha_vigencia <= '$fecha_fin')
                    or (fecha_emision is not null and (fecha_emision >= '$fecha_inicio2' and fecha_emision <= '$fecha_fin') and fecha_vigencia >= '$fecha_fin')
                    )";
             
                    $sql_rango_indicaciones_medicas = "i.caso = $caso and i.visible = true and(
                    (i.fecha_emision is null and i.fecha_vigencia BETWEEN '$fecha_inicio2' AND '$fecha_fin')
                    or (i.fecha_emision is not null and i.fecha_emision <= '$fecha_inicio2' and (i.fecha_vigencia >= '$fecha_inicio2' and i.fecha_vigencia <= '$fecha_fin') )
                    or (i.fecha_emision is not null and i.fecha_emision >= '$fecha_inicio2' and i.fecha_vigencia <= '$fecha_fin')
                    or (i.fecha_emision is not null and (i.fecha_emision >= '$fecha_inicio2' and i.fecha_emision <= '$fecha_fin') and i.fecha_vigencia >= '$fecha_fin')
                    )";

            } else if ($hora >= 0 && $hora <= 8) {
                // si es mayor o igual a las 00 y menor a las 09, significa que debe teren datos de ayer y hoy
                $sql_rango_indicaciones = "caso = $caso and visible = true and(
                    (fecha_emision is null and fecha_vigencia BETWEEN '$inicio_ayer' AND '$fin_hoy')
                    or (fecha_emision is not null and fecha_emision <= '$inicio_ayer' and (fecha_vigencia >= '$inicio_ayer' and fecha_vigencia <= '$fin_hoy') )
                    or (fecha_emision is not null and fecha_emision >= '$inicio_ayer' and fecha_vigencia <= '$fin_hoy')
                    or (fecha_emision is not null and (fecha_emision >= '$inicio_ayer' and fecha_emision <= '$fin_hoy') and fecha_vigencia >= '$fin_hoy')
                    )";
              
                $sql_rango_indicaciones_medicas = "i.caso = $caso and i.visible = true and(
                    (i.fecha_emision is null and i.fecha_vigencia BETWEEN '$inicio_ayer' AND '$fin_hoy')
                    or (i.fecha_emision is not null and i.fecha_emision <= '$inicio_ayer' and (i.fecha_vigencia >= '$inicio_ayer' and i.fecha_vigencia <= '$fin_hoy') )
                    or (i.fecha_emision is not null and i.fecha_emision >= '$inicio_ayer' and i.fecha_vigencia <= '$fin_hoy')
                    or (i.fecha_emision is not null and (i.fecha_emision >= '$inicio_ayer' and i.fecha_emision <= '$fin_hoy') and i.fecha_vigencia >= '$fin_hoy')
                    )";
            }

        } else {
            $horario = "(f.horario >= 9 or f.horario < 22)";
            $turno = ["09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20"];
            $tipo_turno = "dia";
            $fecha_inicio = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $fecha_inicio2 = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
            $sql_rango_indicaciones = "caso = $caso and visible = true and (
                (fecha_emision is null and fecha_vigencia BETWEEN '$fecha_inicio' AND '$fecha_inicio2')
                or (fecha_emision is not null and fecha_emision <= '$fecha_inicio' and (fecha_vigencia >= '$fecha_inicio' and fecha_vigencia <= '$fecha_inicio2') )
                or (fecha_emision is not null and fecha_emision <= '$fecha_inicio' and fecha_vigencia >= '$fecha_inicio2')
                or (fecha_emision is not null and fecha_emision >= '$fecha_inicio' and fecha_vigencia <= '$fecha_inicio2')
                or (fecha_emision is not null and (fecha_emision >= '$fecha_inicio' and fecha_emision <= '$fecha_inicio2') and fecha_vigencia >= '$fecha_inicio2')
                )";
            $sql_rango_indicaciones_medicas = "i.caso = $caso and i.visible = true and (
                (i.fecha_emision is null and i.fecha_vigencia BETWEEN '$fecha_inicio' AND '$fecha_inicio2')
                or (i.fecha_emision is not null and i.fecha_emision <= '$fecha_inicio' and (i.fecha_vigencia >= '$fecha_inicio' and i.fecha_vigencia <= '$fecha_inicio2') )
                or (i.fecha_emision is not null and i.fecha_emision <= '$fecha_inicio' and i.fecha_vigencia >= '$fecha_inicio2')
                or (i.fecha_emision is not null and i.fecha_emision >= '$fecha_inicio' and i.fecha_vigencia <= '$fecha_inicio2')
                or (i.fecha_emision is not null and (i.fecha_emision >= '$fecha_inicio' and i.fecha_emision <= '$fecha_inicio2') and i.fecha_vigencia >= '$fecha_inicio2')
                )";
        }

        //resscata los indices de los turnos ya sea 21,22,.. 08 o 09,10 ... 20
        $indice_turno_actual = array_search(Carbon::now()->format('H'), $turno);

        //busqueda de la planificacion de atenciones hecho para el paciente
        $cuidados = DB::select(DB::raw("select
            f.id,
            t.tipo,
            t.id as id_tipo,
            f.visible,
            f.caso,
            f.horario
            from formulario_planificacion_cuidados_atencion_enfermeria as f
            inner join tipo_cuidado as t on t.id = f.tipo
            where
            f.tipo_modificacion is null and
            (f.caso = $caso and f.visible = true and $horario)
            group by f.id,t.tipo,t.id,f.caso,f.visible,f.horario order by t.tipo asc"));

        //atenciones de enfermeria que se realizaron al paciente
        $cuidados_realizado = DB::select(DB::Raw("select
                f.horario,
                u.tipo as tipou,
                fc.id,
                fc.tipo
                from formulario_hoja_enfermeria_cuidado_enfermeria_atencion as  f
                join formulario_planificacion_cuidados_atencion_enfermeria fc on fc.id = f.id_atencion
                join usuarios u on u.id = f.usuario
                where
                fc.caso = $caso and f.fecha_creacion > '$fecha_inicio' and f.realizado = true and $horario and f.visible = true
                "));
        //ordenar atenciones del paciente que fueron realizadas
        $atencion_realizada = [];
        $tipo_profecional_atencion = [];
        foreach ($cuidados_realizado as $c_relizado) {
            $atencion_realizada[$c_relizado->tipo][] = (int) $c_relizado->horario;
            $tipo_profecional_atencion[$c_relizado->tipo][(int) $c_relizado->horario] = ["tipoU" => $c_relizado->tipou, "horario" => (int) $c_relizado->horario];
        }

        //busqueda de planificaicon de indiciaciones al paciente
        $indicaciones = DB::select("select
            f.id,
            f.visible,
            f.indicacion,
            f.medicamento,
            f.dosis,
            f.via,
            f.horario,
            f.fecha_creacion,
            f.fecha_emision,
            f.fecha_vigencia,
            f.tipo
            from formulario_planificacion_cuidados_indicaciones_medicas as f
            where
            f.tipo_modificacion is null and
            $sql_rango_indicaciones and tipo in ('Medicamento','Indicación') ");

        //indicaciones medicas que se realizaron al paciente
        $indicaciones_realizado = DB::select("select
            f.horario,
            u.tipo as tipou,
            fc.id
            from formulario_hoja_enfermeria_cuidado_enfermeria_indicacion as  f
            join formulario_planificacion_cuidados_indicaciones_medicas fc on fc.id = f.id_indicacion
            join usuarios u on u.id = f.usuario
            where
            fc.caso = ? and f.fecha_creacion > ? and f.realizado = true and $horario and fc.tipo in ('Medicamento','Indicación')
            ", [$caso, $fecha_inicio]);

        //ordenar atenciones del paciente que fueron realizadas
        $indicacion_realizada = [];
        $tipo_profecional_indicacion = [];
        foreach ($indicaciones_realizado as $i_relizado) {
            $indicacion_realizada[$i_relizado->id][] = (int) $i_relizado->horario;
            $tipo_profecional_indicacion[$i_relizado->id][(int) $i_relizado->horario] = ["tipoU" => $i_relizado->tipou, "horario" => (int) $i_relizado->horario];
        }

        $resultado = [];
        $atencion_realizar = [];
        $horario_realizar = [];

        $indicacion_realizar = [];

        foreach ($cuidados as $cuidado) {
            $resultado[$cuidado->id_tipo] = [
                $cuidado->id . "_c", //id_atencion -> seria el id del formulario y c indicando a que es una atencion
                $cuidado->tipo, //tipo -> es el nombre de la atencion
            ];
            //nombres e id de las atenciones que se van a realizar
            $atencion_realizar[$cuidado->id_tipo] = $cuidado->tipo;
            //horarios que deben ser marcados
            $horario_realizar[$cuidado->id_tipo][] = $cuidado->horario;
        }

        $horario_realizar_indicacion = [];
        //Aqui esta buscando que indicaciones corresponde hacer y dependiendo de la hora del dia, mostrara info de un dia y el posteriore, del mismo dia o del dia anterior y el actual

        foreach ($indicaciones as $indicacion) {
            $horarios_inidicacion = explode(",", $indicacion->horario);
            foreach ($horarios_inidicacion as $horario_indicacion) {

                $escribir_indicacion = false;
                if (!array_key_exists($indicacion->id, $horario_realizar_indicacion)) {
                    $horario_realizar_indicacion[$indicacion->id] = [];
                }
                //Si es turno de dia, la fecha de creacion es el mismo dia, se deben condsiderar horarios entre las 9 y 20
                if ($tipo_turno == "dia" && $horario_indicacion > 8 && $horario_indicacion < 21) {
                    $escribir_indicacion = true;
                    //horario
                    $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                }
                //si es turno noche
                if ($tipo_turno == "noche") {
                    if ($hora < 24 && $hora >= 21) {
                        //Si son menor que las 24 y mayor a las 21,
                        //indicacion
                        if ($indicacion->fecha_vigencia >= $fecha_inicio2 && $indicacion->fecha_vigencia < $inicio_mañana &&
                            ($indicacion->fecha_emision == 'NULL' || $indicacion->fecha_emision == null || $indicacion->fecha_emision == '') &&
                            $horario_indicacion >= 21 && $horario_indicacion < 24) {
                            $escribir_indicacion = true;
                            //Debes tomar los datos de hoy que tengan horario entre las 21 y 23
                            $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                        }

                        //medicamento  
                        //Debes tomar los datos de hoy que tengan horario entre las 21 y 23
                        if (( //fe <= fi y fv >= fi y fv <= ff
                            ($indicacion->fecha_emision < $fecha_inicio2 && ($indicacion->fecha_vigencia >= $fecha_inicio2 &&                $indicacion->fecha_vigencia <= $inicio_mañana)) ||
                            //fe >= fi y fv <= ff
                            ($indicacion->fecha_emision >= $fecha_inicio2 &&
                            $indicacion->fecha_vigencia < $inicio_mañana) ||
                            //fe >= fi y fe <= ff y fv >= ff
                            (($indicacion->fecha_emision >= $fecha_inicio2 && $indicacion->fecha_emision < $inicio_mañana) &&
                            $indicacion->fecha_vigencia >= $inicio_mañana) ||
                                //fe >= fi y fe <= ff
                            ($indicacion->fecha_emision < $fecha_inicio2 && $indicacion->fecha_vigencia >= $inicio_mañana)) &&
                            $horario_indicacion >= 21 && $horario_indicacion < 24 && $indicacion->fecha_emision != null) 
                            {
                                $escribir_indicacion = true;
                                $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                            }

                       


                        if ($horario_indicacion >= 0 && $horario_indicacion <= 8 && $indicacion->fecha_vigencia >= $inicio_mañana) {
                            $escribir_indicacion = true;
                            //Debes tomar los datos de mañana que tengan horario entre las 00 y 08
                            $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                        }
                    } else if ($hora >= 0 && $hora <= 8) {
                        //si la hora actual esta entre las 00 y las 08 AM
                        //indicacion
                        if ($horario_indicacion >= 21 && $horario_indicacion < 24 && $indicacion->fecha_vigencia >= $fecha_inicio && $indicacion->fecha_vigencia < $fecha_inicio2 && $indicacion->fecha_emision == null) {
                            $escribir_indicacion = true;
                            //Debes tomar los datos de ayer que tengan horario entre las 21 y 23
                            $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                        }
                        // medicamento
                        if (( //fe <= fi y fv >= fi y fv <= ff
                            ($indicacion->fecha_emision < $fecha_inicio && ($indicacion->fecha_vigencia >= $fecha_inicio &&                $indicacion->fecha_vigencia <= $fecha_inicio2)) ||
                            //fe >= fi y fv <= ff
                            ($indicacion->fecha_emision >= $fecha_inicio &&
                            $indicacion->fecha_vigencia < $fecha_inicio2) ||
                            //fe >= fi y fe <= ff y fv >= ff
                            (($indicacion->fecha_emision >= $fecha_inicio && $indicacion->fecha_emision < $fecha_inicio2) &&
                            $indicacion->fecha_vigencia >= $fecha_inicio2) ||
                                //fe >= fi y fe <= ff
                            ($indicacion->fecha_emision < $fecha_inicio && $indicacion->fecha_vigencia >= $fecha_inicio2)) &&
                            $horario_indicacion >= 21 && $horario_indicacion < 24 && $indicacion->fecha_emision != null) 
                            {
                                $escribir_indicacion = true;
                                $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                            }

                 
                        if ($indicacion->fecha_vigencia >= $fecha_inicio2 && $horario_indicacion >= 0 && $horario_indicacion <= 8) {
                            $escribir_indicacion = true;
                            //Debes tomar los datos de hoy que tengan horario entre las 00 y 08
                            $horario_realizar_indicacion[$indicacion->id][] = (int) $horario_indicacion;
                        }
                    }
                }

                if ($escribir_indicacion) {
                    $indicacion_realizar[$indicacion->id] = [
                        $indicacion->id . "_i",
                        $indicacion->indicacion,
                        $indicacion->medicamento, //en caso de que tenga medicamento, este debera mostrar una pequeña descripcion debajo
                        "(Dosis: $indicacion->dosis, $indicacion->via)",
                        $indicacion->tipo,
                    ];
                }
            }
        }

        $epicrisis = InformeEpicrisis::datosEpicrisis($caso);
        $sub_categoria = $epicrisis["sub_categoria"];


        //busqueda de indicacion realizadas por el medico

        $datos_indicaciones_medicas = DB::select("select
            i.id,
            i.visible,
            i.tipo_reposo,
            i.otro_reposo,
            i.grados_semisentado,
            i.tipo_via,
            i.detalle_via,
            i.tipo_consistencia,
            i.detalle_consistencia,
            i.volumen,
            i.fecha_creacion,
            i.fecha_emision,
            i.fecha_vigencia,
            string_agg(t.tipo::varchar,',') as tipo,
            t.detalle_tipo
            from indicaciones_medicas as i
            LEFT JOIN tipos_reposo_indicacion_medica t
            ON t.im_id = i.id
            where
            $sql_rango_indicaciones_medicas 
            GROUP BY
            i.ID,
            i.visible,
            i.tipo_via,
            i.tipo_consistencia,
            i.volumen,
            i.fecha_creacion,
            i.fecha_emision,
            i.fecha_vigencia,
            t.detalle_tipo
            ");

            $tipos_indicacion_array = [];
            $tipos_indicaciones_medicas = "";
            foreach ($datos_indicaciones_medicas as $indicacion_medica) {
                if($indicacion_medica->tipo != ''){
                    $tipos_indicacion = explode(",", $indicacion_medica->tipo);
                    foreach ($tipos_indicacion as $tipo_indicacion) {
                        switch ($tipo_indicacion) {
                            case '1':
                                $tipos_indicacion_array[] = 'Hiposódico';
                                break;
                            case '2':
                                $tipos_indicacion_array[] = 'Hipocalórico';
                                break;
                            case '3':
                                $tipos_indicacion_array[] = 'Hipograso';
                                break;
                            case '4':
                                $tipos_indicacion_array[] = 'Hipoglúcido';
                                break;
                            case '5':
                                $tipos_indicacion_array[] = 'Liviano';
                                break;
                            case '6':
                                $tipos_indicacion_array[] = 'Sin residuos';
                                break;
                            case '7':
                                $tipos_indicacion_array[] = 'Rico en fibra';
                                break;
                            case '8':
                                $tipos_indicacion_array[] = 'Común';
                                break;
                            case '9':
                                $tipos_indicacion_array[] = 'Otro';
                                break;
                        }
                    }
                }
                $tipos_indicaciones_medicas = implode(',',$tipos_indicacion_array);
              
            }


                  //busqueda de planificaicon de indiciaciones medicas al paciente
        $indicaciones_medicas = DB::select("select
        f.id,
        f.visible,
        f.horario,
        f.fecha_creacion,
        f.tipo,
        f.id_indicacion,
        f.id_farmaco,
        i.fecha_vigencia,
        i.fecha_emision,
        f.responsable
        from formulario_planificacion_indicaciones_medicas  as f
        left join indicaciones_medicas as i on i.id = f.id_indicacion
        where
        f.tipo_modificacion is null and
        $sql_rango_indicaciones_medicas and tipo in ('Control de signos vitales', 'Control de hemoglucotest','Suero','Farmacos') ");

       

        //indicaciones medicas que se realizaron al paciente
        $indicaciones_medicas_realizado = DB::select("select
            f.horario,
            u.tipo as tipou,
            fc.id
            from formulario_hoja_enfermeria_enfermeria_indicacion_medica  as  f
            join formulario_planificacion_indicaciones_medicas  fc on fc.id = f.id_indicacion
            join usuarios u on u.id = f.usuario
            where
            fc.caso = ? and f.fecha_creacion > ? and f.realizado = true and $horario and fc.tipo in ('Control de signos vitales', 'Control de hemoglucotest','Suero','Farmacos')
            ", [$caso, $fecha_inicio]);


            //ordenar atenciones del paciente que fueron realizadas
            $indicacion_medica_realizada = [];
            $tipo_medica_profecional_indicacion = [];
            foreach ($indicaciones_medicas_realizado as $i_relizado) {
                $indicacion_medica_realizada[$i_relizado->id][] = (int) $i_relizado->horario;
                $tipo_medica_profecional_indicacion[$i_relizado->id][(int) $i_relizado->horario] = ["tipoU" => $i_relizado->tipou, "horario" => (int) $i_relizado->horario];
            }


            $indicacion_medica_realizar = [];
            $nombre_farmaco = "";
            $horario_realizar_indicacion_medica = [];

        //Aqui esta buscando que indicaciones corresponde hacer y dependiendo de la hora del dia, mostrara info de un dia y el posteriore, del mismo dia o del dia anterior y el actual
        foreach ($indicaciones_medicas as $indicacion) {
            $horarios_inidicacion = explode(",", $indicacion->horario);
            foreach ($horarios_inidicacion as $horario_indicacion) {

                $escribir_indicacion_medica = false;
                if (!array_key_exists($indicacion->id, $horario_realizar_indicacion_medica)) {
                    $horario_realizar_indicacion_medica[$indicacion->id] = [];
                }
                //Si es turno de dia, la fecha de creacion es el mismo dia, se deben condsiderar horarios entre las 9 y 20
                if ($tipo_turno == "dia" && $horario_indicacion > 8 && $horario_indicacion < 21) {
                    $escribir_indicacion_medica = true;
                    //horario
                    $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                }
                //si es turno noche
                if ($tipo_turno == "noche") {
                    if ($hora < 24 && $hora >= 21) {
                        //Si son menor que las 24 y mayor a las 21,
                        //indicacion
                        if ($indicacion->fecha_vigencia >= $fecha_inicio2 && $indicacion->fecha_vigencia < $inicio_mañana &&
                            ($indicacion->fecha_emision == 'NULL' || $indicacion->fecha_emision == null || $indicacion->fecha_emision == '') &&
                            $horario_indicacion >= 21 && $horario_indicacion < 24) {
                            $escribir_indicacion_medica = true;
                            //Debes tomar los datos de hoy que tengan horario entre las 21 y 23
                            $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                        }

                        //medicamento  
                        //Debes tomar los datos de hoy que tengan horario entre las 21 y 23
                        if (( //fe <= fi y fv >= fi y fv <= ff
                            ($indicacion->fecha_emision < $fecha_inicio2 && ($indicacion->fecha_vigencia >= $fecha_inicio2 &&                $indicacion->fecha_vigencia <= $inicio_mañana)) ||
                            //fe >= fi y fv <= ff
                            ($indicacion->fecha_emision >= $fecha_inicio2 &&
                            $indicacion->fecha_vigencia < $inicio_mañana) ||
                            //fe >= fi y fe <= ff y fv >= ff
                            (($indicacion->fecha_emision >= $fecha_inicio2 && $indicacion->fecha_emision < $inicio_mañana) &&
                            $indicacion->fecha_vigencia >= $inicio_mañana) ||
                                //fe >= fi y fe <= ff
                            ($indicacion->fecha_emision < $fecha_inicio2 && $indicacion->fecha_vigencia >= $inicio_mañana)) &&
                            $horario_indicacion >= 21 && $horario_indicacion < 24 && $indicacion->fecha_emision != null) 
                            {
                                $escribir_indicacion_medica = true;
                                $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                            }

                       


                        if ($horario_indicacion >= 0 && $horario_indicacion <= 8 && $indicacion->fecha_vigencia >= $inicio_mañana) {
                            $escribir_indicacion_medica = true;
                            //Debes tomar los datos de mañana que tengan horario entre las 00 y 08
                            $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                        }
                    } else if ($hora >= 0 && $hora <= 8) {
                        //si la hora actual esta entre las 00 y las 08 AM
                        //indicacion
                        if ($horario_indicacion >= 21 && $horario_indicacion < 24 && $indicacion->fecha_vigencia >= $fecha_inicio && $indicacion->fecha_vigencia < $fecha_inicio2 && $indicacion->fecha_emision == null) {
                            $escribir_indicacion_medica = true;
                            //Debes tomar los datos de ayer que tengan horario entre las 21 y 23
                            $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                        }
                        // medicamento
                        if (( //fe <= fi y fv >= fi y fv <= ff
                            ($indicacion->fecha_emision < $fecha_inicio && ($indicacion->fecha_vigencia >= $fecha_inicio &&                $indicacion->fecha_vigencia <= $fecha_inicio2)) ||
                            //fe >= fi y fv <= ff
                            ($indicacion->fecha_emision >= $fecha_inicio &&
                            $indicacion->fecha_vigencia < $fecha_inicio2) ||
                            //fe >= fi y fe <= ff y fv >= ff
                            (($indicacion->fecha_emision >= $fecha_inicio && $indicacion->fecha_emision < $fecha_inicio2) &&
                            $indicacion->fecha_vigencia >= $fecha_inicio2) ||
                                //fe >= fi y fe <= ff
                            ($indicacion->fecha_emision < $fecha_inicio && $indicacion->fecha_vigencia >= $fecha_inicio2)) &&
                            $horario_indicacion >= 21 && $horario_indicacion < 24 && $indicacion->fecha_emision != null) 
                            {
                                $escribir_indicacion_medica = true;
                                $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                            }

                 
                        if ($indicacion->fecha_vigencia >= $fecha_inicio2 && $horario_indicacion >= 0 && $horario_indicacion <= 8) {
                            $escribir_indicacion_medica = true;
                            //Debes tomar los datos de hoy que tengan horario entre las 00 y 08
                            $horario_realizar_indicacion_medica[$indicacion->id][] = (int) $horario_indicacion;
                        }
                    }
                }
                
                $ultimaIndicacion = IndicacionMedica::where('id',$indicacion->id_indicacion)->where('visible',true)->orderBy('id','desc')->first();
                if($indicacion->tipo == 'Farmacos' && $indicacion->id_farmaco){
                    if($ultimaIndicacion && !empty($ultimaIndicacion->farmacos)){
                        foreach ($ultimaIndicacion->farmacos as $key => $farmaco) {
                            if($farmaco->id == $indicacion->id_farmaco){
                                $datos_farmacos = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"))
                                ->whereIn('tipo',['ANTIBIOTICO','ANTIBIOTICO-ANTIFUNGICO'])
                                ->where('id',$farmaco->id_farmaco)
                                ->first();
                                $nombre_farmaco = $datos_farmacos->nombre_unidad;
                            }
                        }
                    }
                }elseif($indicacion->tipo == 'Suero' && $indicacion->id_farmaco){
                    
                    if($ultimaIndicacion && $ultimaIndicacion->sueros == true && $ultimaIndicacion->suero != null){
                        $datos_sueros = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"), 'id')
                        ->where('tipo','SUERO')
                        ->where('id',$ultimaIndicacion->suero)
                        ->first();
                        $nombre_farmaco = $datos_sueros->nombre_unidad;
                    }
                }else{
                    $nombre_farmaco = "";
                }

                if ($escribir_indicacion_medica) {
                    $indicacion_medica_realizar[$indicacion->id] = [
                        $indicacion->id . "_i",
                        $indicacion->tipo,
                        $nombre_farmaco,
                        $indicacion->responsable
                    ];
                }
            }
        }




        $data = [
            //atencion
            "cuidados" => $resultado,
            "atencion_realizar" => $atencion_realizar, //listado de atenciones
            "horario_realizar" => $horario_realizar, //horario en que se deben realizar para marcar en el hroarioo
            "atencion_realizada" => $atencion_realizada, //atenciones ya realziadas
            "tipo_profecional_atencion" => $tipo_profecional_atencion, //Profecional realiza curacion
            //indicaciones
            "indicacion_realizar" => $indicacion_realizar, //indicaciones que se deben realizar
            "horario_realizar_indicacion" => $horario_realizar_indicacion, //horario en que se  debe realizar al indicacion
            "indicacion_realizada" => $indicacion_realizada, //indicaciones que se deben marcar
            "tipo_profecional_indicacion" => $tipo_profecional_indicacion, //tipo de profecional realiza indicacion
             //indicaciones medicas
             "datos_indicaciones_medicas" => $datos_indicaciones_medicas,
             "tipos_indicaciones_medicas" => $tipos_indicaciones_medicas,
             "indicacion_medica_realizar" => $indicacion_medica_realizar, //indicaciones que se deben marcar
             "horario_realizar_indicacion_medica" => $horario_realizar_indicacion_medica, //horario en que se  debe realizar al indicacion
             "indicacion_medica_realizada" => $indicacion_medica_realizada, //indicaciones que se deben marcar
             "tipo_medica_profecional_indicacion" => $tipo_medica_profecional_indicacion, //tipo de profecional realiza indicacion
            //datos turno
            "turno" => $turno,
            "tipo_turno" => $tipo_turno,
            "indice_turno" => $indice_turno_actual,
            "caso_id" => $caso,
            "caso_id_encrypted" => Crypt::encrypt($caso),
            "sub_categoria" => $sub_categoria

        ];
        $resp = View::make("Gestion/gestionEnfermeria/partesHojaEnfermeria/cuidadosEnfermeria/infoCuidadosEnfermeria", $data)->render();

        return response()->json(["contenido" => $resp]);
    }

    public function pdfCuidadosRealizados($caso,$fechaX){
        $fecha = Carbon::parse($fechaX)->format('Y-m-d');
        $horas = ["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23"];

        $cuidados_realizado = DB::select(DB::Raw("select
            f.id,
            f.horario,
            fc.id,
            fc.tipo,
            t.id as id_tipo,
            t.tipo as tipo_cuidado,
            u.tipo as tipou
            from formulario_hoja_enfermeria_cuidado_enfermeria_atencion as  f
            join formulario_planificacion_cuidados_atencion_enfermeria fc on fc.id = f.id_atencion
            inner join tipo_cuidado as t on t.id = fc.tipo
            join usuarios u on u.id = f.usuario
            where
            fc.caso = $caso 
            and (f.fecha_creacion >= '$fecha 00:00:00' and f.fecha_creacion <= '$fecha 23:59:59')
            and f.realizado = true 
            and f.visible = true
            order by f.id desc
        "));

        $todos_cuidados = [];
        $horario_cuidado = [];
        $responsable_cuidado = [];
        foreach ($cuidados_realizado as $cuidado) {
            $todos_cuidados[$cuidado->id_tipo] = $cuidado->tipo_cuidado;
            $horario_cuidado[$cuidado->id_tipo][] = $cuidado->horario;
            $responsable_cuidado[$cuidado->id_tipo][(int) $cuidado->horario] = $cuidado->tipou;
        }

        $indicaciones_realizado = DB::select("select
            f.horario,
            u.tipo as tipou,
            fc.id,
            fc.tipo,
            fc.medicamento,
            fc.dosis,
            fc.via,
            fc.indicacion
            from formulario_hoja_enfermeria_cuidado_enfermeria_indicacion as  f
            join formulario_planificacion_cuidados_indicaciones_medicas fc on fc.id = f.id_indicacion
            join usuarios u on u.id = f.usuario
            where
            fc.caso = $caso 
            and (f.fecha_creacion >= '$fecha 00:00:00' and f.fecha_creacion <= '$fecha 23:59:59') 
            and f.realizado = true 
            and fc.tipo in ('Medicamento','Indicación')
        ");

        $todos_indicaciones = [];
        $horario_indicacion = [];
        $responsable_indicacion = [];
        foreach ($indicaciones_realizado as $indicacion) {
            $todos_indicaciones[$indicacion->id] = [$indicacion->tipo,$indicacion->medicamento,$indicacion->dosis,$indicacion->via,$indicacion->indicacion];
            $horario_indicacion[$indicacion->id][] = $indicacion->horario;
            $responsable_indicacion[$indicacion->id][(int) $indicacion->horario] = $indicacion->tipou;
        }
        
        $valoraciones_enfermeria = DB::select(DB::raw("select
            f.id,
            u.nombres,
            u.apellido_paterno,
            u.apellido_materno,
            f.observacion,
            f.visible,
            f.fecha_creacion
            from formulario_hoja_enfermeria_valoracion_enfermeria as f
            inner join usuarios as u on u.id = f.usuario
            where
            (f.caso = $caso and f.visible = true)
            and (f.fecha_creacion >= '$fecha 00:00:00' and f.fecha_creacion <= '$fecha 23:59:59')
        "));
        
        $valoraciones_realizadas = [];
        foreach ($valoraciones_enfermeria as $key => $valoracion) {
            $valoraciones_realizadas [] = [
                $valoracion->nombres." ".$valoracion->apellido_paterno." ".$valoracion->apellido_materno,
                "Creado el: ".Carbon::parse($valoracion->fecha_creacion)->format("d-m-Y H:i"),
                $valoracion->observacion
            ];
        }

        $paciente = Paciente::getPacientePorCaso($caso);
        $prevision = Caso::find($caso,'prevision');
        $telefonos = Telefono::where('id_paciente',$paciente->id)->get();
        
        $pdf = \Barryvdh\DomPDF\Facade::loadView("Gestion/gestionEnfermeria/partesHojaEnfermeria/pdfResumenRegistroEnfermeria", [
            "fecha" => $fechaX,
            "horas" => $horas,
            "todos_cuidados" => $todos_cuidados,
            "horarios_cuidados" => $horario_cuidado,
            "responsables_cuidados" => $responsable_cuidado,
            "todos_indicaciones" => $todos_indicaciones,
            "horarios_indicaciones" => $horario_indicacion,
            "responsables_indicaciones" => $responsable_indicacion,
            "valoraciones_realizadas" => $valoraciones_realizadas,
            "paciente" => $paciente,
            "prevision" => $prevision->prevision,
            "telefonos" => $telefonos
        ]);
        //download stream
        return $pdf->setPaper('legal','landscape')->download('Resumen Registro de enfermeria.pdf');

        // return View::make("Gestion/gestionEnfermeria/partesHojaEnfermeria/pdfResumenRegistroEnfermeria")->with(array(
        //     "fecha" => $fecha,
        //     "horas" => $horas,
        //     "todos_cuidados" => $todos_cuidados,
        //     "horarios_cuidados" => $horario_cuidado,
        //     "responsables_cuidados" => $responsable_cuidado,
        //     "valoraciones_realizadas" => $valoraciones_realizadas
        // ));
    }


    public function pdfResumenCuidados($caso, $fecha){
        $fecha_inicio = carbon::parse($fecha)->format('Y-m-d');
        $fecha_fin = Carbon::parse($fecha)->addDay(1)->format('Y-m-d');

        $turno = ["08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "00", "01", "02", "03", "04", "05", "06", "07"];
        $turnouno = ["08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"];
        $turnodos = ["00", "01", "02", "03", "04", "05", "06", "07"];
        $atenciones_turno_uno = [];
        $horarios_atencion_turno_uno = [];
        $responsable_atencion_horario_turno_uno = [];
        $atenciones_turno_dos = [];
        $horarios_atencion_turno_dos = [];
        $responsable_atencion_horario_turno_dos = [];
        $todas_atenciones = [];

        // ATENCIONES DE ENFERMERIA
        //cuando es visible false, y tipomodificacion eliminado significa que no se considera,
        //pero cuando tiene visible true y tiene tipomodificacion terminado se considera una modificacion y se considera
        //debe encontrarse dentro del rango.
        //si tiene fecha de creacion MENOR QUE fecha actual y no tiene modificacion, se debe tener en cuenta.
        //si tiene fecha de cracion es MENOR QUE fecha actual y fecha modificacion esta dentro del dia, si cuenta.
        //no debe tener horario null (esto surge de control de signos vitales).

        $atenciones = DB::select(DB::raw("select
        f.ID,
        T.tipo,
        T.ID AS id_tipo,
        f.visible,
        f.caso,
        f.horario,
        f.fecha_creacion,
        f.resp_atencion,
        f.fecha_modificacion,
        f.tipo_modificacion
            FROM
            (
            select m.*, row_number() over (partition by horario,tipo order by fecha_creacion desc) as rn
            from formulario_planificacion_cuidados_atencion_enfermeria m where
                m.caso = '$caso'
                AND m.fecha_creacion <= '$fecha_fin 07:59:59'
                AND (
                        (
                            (m.fecha_modificacion is null and m.fecha_creacion <= '$fecha_fin 07:59:59')
                            or
                            (
                                --esta dentro del rango
                                (
                                    m.fecha_modificacion is not null and m.fecha_modificacion >= '$fecha_fin 07:59:59'
                                    and m.fecha_creacion <= '$fecha_fin 07:59:59'
                                )
                                and
                                (
                                    m.fecha_modificacion is not null and m.fecha_modificacion >= '$fecha_inicio 08:00:00' and
                                    m.fecha_creacion <= '$fecha_inicio 08:00:00'
                                )


                            )
                            or
                            (

                                --finicio  fecha creación ffin
                                --	|___|___|
                                -- tiene fde inicio fuera de fecha de cracion, pero tiene la fecha fianl dentro de la creacion
                                (
                                    m.fecha_modificacion is not null and m.fecha_modificacion >= '$fecha_fin 07:59:59'
                                    and m.fecha_creacion <= '$fecha_fin 07:59:59'
                                )
                                and
                                (
                                    m.fecha_modificacion is not null and m.fecha_modificacion >= '$fecha_inicio 08:00:00' and
                                    m.fecha_creacion >= '$fecha_inicio 08:00:00'
                                )
                            )
                            or
                            (

                                --finicio  fecha  ffin
                                --	|____|_____|

                                --fde inicio este dentro de fecha de modificacion, pero tiene la fecha fianl fuera de la modificacion
                                (
                                    m.fecha_modificacion is not null and m.fecha_modificacion <= '$fecha_fin 07:59:59'
                                    and m.fecha_creacion <= '$fecha_fin 07:59:59'
                                )
                                and
                                (
                                    m.fecha_modificacion is not null and m.fecha_modificacion >= '$fecha_inicio 08:00:00' and
                                    m.fecha_creacion <= '$fecha_inicio 08:00:00'
                                )
                            )

                        )
                    )
                AND ( m.tipo_modificacion IS NULL OR m.tipo_modificacion = 'Terminado' OR m.tipo_modificacion = 'Modificado')
                AND horario IS NOT NULL
                AND resp_atencion IS NOT NULL
                --AND m.visible = TRUE
                ) f
            INNER JOIN tipo_cuidado AS T ON T.ID = f.tipo
            WHERE
            f.rn = 1
            ORDER BY
                T.tipo desc
        "));

        foreach ($atenciones as $atencion) {
            $todas_atenciones[$atencion->id_tipo] = $atencion->tipo;

            //horario entre las 8 AM y las 23:59
            if ($atencion->horario >= 8 && $atencion->horario < 24) {
                $atenciones_turno_uno[$atencion->id_tipo] = $atencion->tipo;
                $horarios_atencion_turno_uno[$atencion->id_tipo][] = $atencion->horario;
                $responsable_atencion_horario_turno_uno[$atencion->id_tipo][(int) $atencion->horario] = $atencion->resp_atencion;
            }
            //horario ente las 00 y las 07:59 AM
            if ($atencion->horario >= 0 && $atencion->horario < 8) {
                $atenciones_turno_dos[$atencion->id_tipo] = $atencion->tipo;
                $horarios_atencion_turno_dos[$atencion->id_tipo][] = $atencion->horario;
                $responsable_atencion_horario_turno_dos[$atencion->id_tipo][(int) $atencion->horario] = $atencion->resp_atencion;
            }
        }

        //INDICACIONES Y MEDICAMENTOS DEL DIA
        //Se debe seleccionar lo con fecha de creacion del mismo dia entre las 00:00 y las 23:59, con una restriccion del horario que empieza desde las 8 y 23 hrs del dia
        //Ademas se debe restringir los del dia siguiente, pero con horario entre las 00 y las 07 de la mañana
        //no debe mostrarse casos falsos, porque esos estan mal, puesto que siempre son reemplazados
        $indicaciones = DB::select(DB::raw("select
        f.id,
        f.visible,
        f.indicacion,
        f.medicamento,
        f.dosis,
        f.via,
        f.fecha_creacion,
        f.fecha_emision,
        f.fecha_vigencia,
        f.fecha_modificacion,
        f.tipo_modificacion,
        f.tipo,
        f.horario,
        f.responsable
        from formulario_planificacion_cuidados_indicaciones_medicas as f
        inner join usuarios as u on u.id = f.usuario
        where
        f.caso = '$caso'
        and f.visible = true
        and (
            --indicacion  tipo_modificacion  null
            (f.fecha_emision is null and f.tipo_modificacion is null and f.fecha_vigencia BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59')
            --indicacion  tipo_modificacion no null
                            or
                             (f.fecha_emision is null and f.tipo_modificacion is not null and f.fecha_modificacion BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59')
            --medicamento tipo_modificacion null
                            or (fecha_emision is not null and f.tipo_modificacion is null and fecha_emision <= '$fecha_inicio 00:00:00'  and (fecha_vigencia >= '$fecha_inicio 00:00:00'  and fecha_vigencia <= '$fecha_fin 23:59:59') )
            or (fecha_emision is not null and f.tipo_modificacion is null and fecha_emision <= '$fecha_inicio 00:00:00'  and fecha_vigencia >= '$fecha_fin 23:59:59')
            or (fecha_emision is not null and f.tipo_modificacion is null and fecha_emision >= '$fecha_inicio 00:00:00'  and fecha_vigencia <= '$fecha_fin 23:59:59')
            or (fecha_emision is not null and f.tipo_modificacion is null and (fecha_emision >= '$fecha_inicio 00:00:00'  and fecha_emision <= '$fecha_fin 23:59:59') and fecha_vigencia >= '$fecha_fin 23:59:59')
            --medicamento tipo_modificacion no null
                            or (fecha_emision is not null and f.tipo_modificacion is not null and fecha_emision <= '$fecha_inicio 00:00:00'  and (fecha_modificacion >= '$fecha_inicio 00:00:00'  and fecha_modificacion <= '$fecha_fin 23:59:59') )
            or (fecha_emision is not null and f.tipo_modificacion is not null and fecha_emision <= '$fecha_inicio 00:00:00'  and fecha_modificacion >= '$fecha_fin 23:59:59')
            or (fecha_emision is not null and f.tipo_modificacion is not null and fecha_emision >= '$fecha_inicio 00:00:00'  and fecha_modificacion <= '$fecha_fin 23:59:59')
            or (fecha_emision is not null and f.tipo_modificacion is not null and (fecha_emision >= '$fecha_inicio 00:00:00'  and fecha_emision <= '$fecha_fin 23:59:59') and fecha_modificacion >= '$fecha_fin 23:59:59')
        )
        order by f.id desc"));

        $indicaciones_turno_uno = [];
        $indicaciones_turno_dos = [];
        $horarios_indicaciones_turno_uno = [];
        $horarios_indicaciones_turno_dos = [];
        $responsable_indicacion_turno_uno = [];
        $responsable_indicacion_turno_dos = [];
        foreach ($indicaciones as $indicacion) {
            $horarios = explode(",", $indicacion->horario);
            foreach ($horarios as $horario) {
                //horario entre las 8 AM y las 23:59 del dia actual
                //indicaciones  tipo_modificacion  null
                if ($indicacion->fecha_vigencia >= Carbon::parse($fecha_inicio . ' 00:00:00') &&
                    $indicacion->fecha_vigencia <= Carbon::parse($fecha_inicio . ' 23:59:59') &&
                    $indicacion->tipo_modificacion == null &&
                    $indicacion->fecha_emision == null &&
                    ($horario >= 8 && $horario < 24)) {
                    //informacion de la indicacion
                    $indicaciones_turno_uno[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_uno[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_uno[$indicacion->id][(int) $horario] = $indicacion->responsable;
                }
                //indicaciones  tipo_modificacion no null
                if ($indicacion->fecha_modificacion >= Carbon::parse($fecha_inicio . ' 00:00:00') &&
                    $indicacion->fecha_modificacion <= Carbon::parse($fecha_inicio . ' 23:59:59') &&
                    $indicacion->tipo_modificacion != null &&
                    $indicacion->fecha_emision == null &&
                    ($horario >= 8 && $horario < 24)) {
                    //informacion de la indicacion
                    $indicaciones_turno_uno[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_uno[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_uno[$indicacion->id][(int) $horario] = $indicacion->responsable;
                }
                //medicamentos tipo_modificacion  null
                   if (( //fe <= fi y fv >= fi y fv <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_inicio . ' 00:00:00') && ($indicacion->fecha_vigencia >= Carbon::parse($fecha_inicio . ' 00:00:00') && $indicacion->fecha_vigencia <=Carbon::parse($fecha_inicio . ' 23:59:59'))) ||
                    //fe >= fi y fv <= ff
                    ($indicacion->fecha_emision >= Carbon::parse($fecha_inicio . ' 00:00:00') &&
                    $indicacion->fecha_vigencia < Carbon::parse($fecha_inicio . ' 23:59:59')) ||
                    //fe >= fi y fe <= ff y fv >= ff
                    (($indicacion->fecha_emision >= Carbon::parse($fecha_inicio . ' 00:00:00') && $indicacion->fecha_emision < Carbon::parse($fecha_inicio . ' 23:59:59')) &&
                    $indicacion->fecha_vigencia >= Carbon::parse($fecha_inicio . ' 23:59:59')) ||
                        //fe >= fi y fe <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_inicio . ' 00:00:00') && $indicacion->fecha_vigencia >= Carbon::parse($fecha_inicio . ' 23:59:59'))) &&
                    $horario >= 8 && $horario < 24 && $indicacion->tipo_modificacion == null && $indicacion->fecha_emision != null) 
                    {
                                //informacion de la indicacion
                    $indicaciones_turno_uno[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_uno[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_uno[$indicacion->id][(int) $horario] = $indicacion->responsable;
                    }

                    //medicamentos tipo_modificacion no null
                   if (( //fe <= fi y fv >= fi y fv <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_inicio . ' 00:00:00') && ($indicacion->fecha_modificacion >= Carbon::parse($fecha_inicio . ' 00:00:00') && $indicacion->fecha_modificacion <=Carbon::parse($fecha_inicio . ' 23:59:59'))) ||
                    //fe >= fi y fv <= ff
                    ($indicacion->fecha_emision >= Carbon::parse($fecha_inicio . ' 00:00:00') &&
                    $indicacion->fecha_modificacion < Carbon::parse($fecha_inicio . ' 23:59:59')) ||
                    //fe >= fi y fe <= ff y fv >= ff
                    (($indicacion->fecha_emision >= Carbon::parse($fecha_inicio . ' 00:00:00') && $indicacion->fecha_emision < Carbon::parse($fecha_inicio . ' 23:59:59')) &&
                    $indicacion->fecha_modificacion >= Carbon::parse($fecha_inicio . ' 23:59:59')) ||
                        //fe >= fi y fe <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_inicio . ' 00:00:00') && $indicacion->fecha_modificacion >= Carbon::parse($fecha_inicio . ' 23:59:59'))) &&
                    $horario >= 8 && $horario < 24 && $indicacion->tipo_modificacion != null && $indicacion->fecha_emision != null) 
                    {
                                //informacion de la indicacion
                    $indicaciones_turno_uno[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_uno[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_uno[$indicacion->id][(int) $horario] = $indicacion->responsable;
                    }

                
                
                //horario ente las 00 y las 07:59 AM del dia de mañana
                //indicaciones tipo_modificacion null
                if ($indicacion->fecha_vigencia <= Carbon::parse($fecha_fin . ' 23:59:59') &&
                    $indicacion->fecha_vigencia >= Carbon::parse($fecha_fin . ' 00:00:00') &&
                    $indicacion->tipo_modificacion == null &&
                    $indicacion->fecha_emision == null &&
                    ($horario >= 0 && $horario < 8)) {
                    //informacion de la indicacion
                    $indicaciones_turno_dos[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_dos[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_dos[$indicacion->id][(int) $horario] = $indicacion->responsable;
                }
                //indicaciones tipo_modificacion no null
                if ($indicacion->fecha_modificacion <= Carbon::parse($fecha_fin . ' 23:59:59') &&
                    $indicacion->fecha_modificacion >= Carbon::parse($fecha_fin . ' 00:00:00') &&
                    $indicacion->tipo_modificacion != null &&
                    $indicacion->fecha_emision == null &&
                    ($horario >= 0 && $horario < 8)) {
                    //informacion de la indicacion
                    $indicaciones_turno_dos[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_dos[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_dos[$indicacion->id][(int) $horario] = $indicacion->responsable;
                }

                //medicamentos tipo_modificacion null
                if (( //fe <= fi y fv >= fi y fv <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_fin . ' 00:00:00') && ($indicacion->fecha_vigencia >= Carbon::parse($fecha_fin . ' 00:00:00') && $indicacion->fecha_vigencia <= Carbon::parse($fecha_fin . ' 23:59:59'))) ||
                    //fe >= fi y fv <= ff
                    ($indicacion->fecha_emision >= Carbon::parse($fecha_fin . ' 00:00:00') &&
                    $indicacion->fecha_vigencia < Carbon::parse($fecha_fin . ' 23:59:59')) ||
                    //fe >= fi y fe <= ff y fv >= ff
                    (($indicacion->fecha_emision >= Carbon::parse($fecha_fin . ' 00:00:00') && $indicacion->fecha_emision < Carbon::parse($fecha_fin . ' 23:59:59')) &&
                    $indicacion->fecha_vigencia >= Carbon::parse($fecha_fin . ' 23:59:59')) ||
                        //fe >= fi y fe <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_fin . ' 00:00:00') && $indicacion->fecha_vigencia >= Carbon::parse($fecha_fin . ' 23:59:59'))) &&
                    $horario >= 0 && $horario < 8 && $indicacion->tipo_modificacion == null && $indicacion->fecha_emision != null) 
                    {
                    //informacion de la indicacion
                    $indicaciones_turno_dos[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_dos[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_dos[$indicacion->id][(int) $horario] = $indicacion->responsable;
                    }

                //medicamentos tipo_modificacion no null
                if (( //fe <= fi y fv >= fi y fv <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_fin . ' 00:00:00') && ($indicacion->fecha_modificacion >= Carbon::parse($fecha_fin . ' 00:00:00') && $indicacion->fecha_modificacion <=Carbon::parse($fecha_fin . ' 23:59:59'))) ||
                    //fe >= fi y fv <= ff
                    ($indicacion->fecha_emision >= Carbon::parse($fecha_fin . ' 00:00:00') &&
                    $indicacion->fecha_modificacion < Carbon::parse($fecha_fin . ' 23:59:59')) ||
                    //fe >= fi y fe <= ff y fv >= ff
                    (($indicacion->fecha_emision >= Carbon::parse($fecha_fin . ' 00:00:00') && $indicacion->fecha_emision < Carbon::parse($fecha_fin . ' 23:59:59')) &&
                    $indicacion->fecha_modificacion >= Carbon::parse($fecha_fin . ' 23:59:59')) ||
                        //fe >= fi y fe <= ff
                    ($indicacion->fecha_emision < Carbon::parse($fecha_fin . ' 00:00:00') && $indicacion->fecha_modificacion >= Carbon::parse($fecha_fin . ' 23:59:59'))) &&
                    $horario >= 0 && $horario < 8 && $indicacion->tipo_modificacion != null && $indicacion->fecha_emision != null) 
                    {
                    //informacion de la indicacion
                    $indicaciones_turno_dos[$indicacion->id] = [
                        $indicacion->id,
                        $indicacion->tipo,
                        $indicacion->indicacion,
                        $indicacion->medicamento,
                        $indicacion->dosis,
                        $indicacion->via,
                    ];
                    //horario
                    $horarios_indicaciones_turno_dos[$indicacion->id][(int) $horario] = $horario;
                    //responsable de ese horario
                    $responsable_indicacion_turno_dos[$indicacion->id][(int) $horario] = $indicacion->responsable;
                    }

            
            }
        }

        // novedades
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
            and f.fecha_creacion >= '$fecha_inicio 08:00:00'
            and f.fecha_creacion <= '$fecha_fin 07:59:59'"));

        $resultado_novedades = [];

        foreach ($novedades as $novedad) {
            $resultado_novedades[] = [
                "<b>" . $novedad->nombres . " " . $novedad->apellido_paterno . " " . $novedad->apellido_materno . "</b> <br> Creado el: " . Carbon::parse($novedad->fecha_creacion)->format("d-m-Y H:i"),
                $novedad->novedad,
            ];
        }

        $paciente = Paciente::getPacientePorCaso($caso);
        $prevision = Caso::find($caso,'prevision');
        $telefonos = Telefono::where('id_paciente',$paciente->id)->get();

        $pdf = \Barryvdh\DomPDF\Facade::loadView("Gestion/gestionEnfermeria/partesPlanificacionCuidados/pdfResumenPlanificacion", [
            "turnouno" => $turnouno,
            "turnodos" => $turnodos,

            "atenciones_turno_uno" => $atenciones_turno_uno,
            "horarios_turno_uno" => $horarios_atencion_turno_uno,
            "resposable_atencion_horario_uno" => $responsable_atencion_horario_turno_uno,

            "atenciones_turno_dos" => $atenciones_turno_dos,
            "horarios_turno_dos" => $horarios_atencion_turno_dos,
            "resposable_atencion_horario_dos" => $responsable_atencion_horario_turno_dos,

            "indicaciones_turno_uno" => $indicaciones_turno_uno,
            "horarios_indicaciones_turno_uno" => $horarios_indicaciones_turno_uno,
            "responsable_indicacion_turno_uno" => $responsable_indicacion_turno_uno,

            "indicaciones_turno_dos" => $indicaciones_turno_dos,
            "horarios_indicaciones_turno_dos" => $horarios_indicaciones_turno_dos,
            "responsable_indicacion_turno_dos" => $responsable_indicacion_turno_dos,

            "todas_atenciones" => $todas_atenciones,
            "turno" => $turno,
            "fecha_inicio" => $fecha_inicio,
            "fecha_fin" => $fecha_fin,
            "novedades" => $resultado_novedades,
            "paciente" => $paciente,
            "prevision" => $prevision->prevision,
            "telefonos" => $telefonos
        ]);
        //stream download
        return $pdf->setPaper('legal', 'landscape')->download('Resumen Planificación de los cuidados.pdf');
    }

    public function infocuidadoseindicaciones($caso)
    {
        $hora = (int) Carbon::now()->format('H');
        //horario en que se encuentra el paciente
        if ($hora < 9 || $hora >= 21) {
            $horario = "(f.horario < 9 or f.horario >= 21)";
            $fecha_inicio = Carbon::now()->subDay(1)->format('Y-m-d H:i:s');
            $fecha_inicio2 = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $sql_rango = "f.caso = $caso and f.visible = true and (f.fecha_vigencia >= '$fecha_inicio2' and f.fecha_vigencia <= '$fecha_inicio')";
        } else {
            $horario = "(f.horario >= 9 or f.horario < 22)";
            $fecha_inicio = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $fecha_fin = Carbon::now()->format('Y-m-d 21:00:00');
            $sql_rango = "f.caso = $caso and f.visible = true and (f.fecha_vigencia >= '$fecha_inicio' and f.fecha_vigencia <= '$fecha_fin')";
        }

        $cuidados = DB::select(DB::raw("select
            f.id,
            t.tipo,
            t.id as id_tipo,
            f.visible,
            f.caso,
            f.horario
            from formulario_planificacion_cuidados_atencion_enfermeria as f
            inner join tipo_cuidado as t on t.id = f.tipo
            where f.caso = $caso and f.visible = true and $horario"));
        // group by f.id,t.tipo,t.id,f.caso,f.visible,f.horario order by t.tipo asc"));

        $cuidados_realizado = DB::select(DB::Raw("select
            f.horario,
            fc.id,
            fc.tipo
            from formulario_hoja_enfermeria_cuidado_enfermeria_atencion as  f
            join formulario_planificacion_cuidados_atencion_enfermeria fc on fc.id = f.id_atencion
            where fc.caso = $caso and f.fecha_creacion > '$fecha_inicio' and f.realizado = true and $horario and f.visible = true
        "));

        //indicaciones deben ser mnostradas dependiendo de si son de turno dia o noche
        $indicaciones = DB::select(DB::raw("select
            f.id,
            f.visible,
            f.indicacion,
            f.medicamento,
            f.dosis,
            f.via,
            f.horario
            from formulario_planificacion_cuidados_indicaciones_medicas as f
            where
            $sql_rango"));

        $indicacionesArray = array();
        foreach ($indicaciones as $indicacion) {
            $tieneComa = strpos($indicacion->horario, ',');
            if ($tieneComa !== false) {
                $horario_array = explode(",", $indicacion->horario);
                foreach ($horario_array as $hor) {
                    $indicacionesArray[] = array(
                        'id' => $indicacion->id,
                        'visible' => $indicacion->visible,
                        'indicacion' => $indicacion->indicacion,
                        'medicamento' => $indicacion->medicamento,
                        'dosis' => $indicacion->dosis,
                        'via' => $indicacion->via,
                        'horario' => $hor,
                    );
                }
            } else {
                $indicacionesArray[] = array(
                    'id' => $indicacion->id,
                    'visible' => $indicacion->visible,
                    'indicacion' => $indicacion->indicacion,
                    'medicamento' => $indicacion->medicamento,
                    'dosis' => $indicacion->dosis,
                    'via' => $indicacion->via,
                    'horario' => $indicacion->horario,
                );
            }
        }

        $indicaciones_realizado = DB::select(DB::Raw("select
            f.horario,
            fc.id
            from formulario_hoja_enfermeria_cuidado_enfermeria_indicacion as  f
            join formulario_planificacion_cuidados_indicaciones_medicas fc on fc.id = f.id_indicacion
            where
            fc.caso = $caso and f.fecha_creacion > '$fecha_inicio' and f.realizado = true and $horario and f.visible = true
        "));

        return [
            "cuidados" => $cuidados,
            "cuidados_realizados" => $cuidados_realizado,
            "indicaciones" => $indicacionesArray,
            "indicaciones_realizadas" => $indicaciones_realizado,
        ];
    }

    public function obtenerValoracionesEnfermeria($caso)
    {

        $examenes = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.observacion,
                f.visible,
                f.fecha_creacion
                from formulario_hoja_enfermeria_valoracion_enfermeria as f
                inner join usuarios as u on u.id = f.usuario
                where
                (f.caso = $caso and f.visible =true)"));

        $resultado = [];

        foreach ($examenes as $examen) {
            $resultado[] = [
                "<b>" . $examen->nombres . " " . $examen->apellido_paterno . " " . $examen->apellido_materno . "</b> ",
                "Creado el: " . Carbon::parse($examen->fecha_creacion)->format("d-m-Y H:i"),
                $examen->observacion,
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function addValoracionEnfermeria(Request $request)
    {
        try {
            DB::beginTransaction();

            $valoracionEnfermeria = new HojaEnfermeriaValoracionEnfermeria;
            $valoracionEnfermeria->caso = strip_tags($request->caso);
            $valoracionEnfermeria->usuario = Auth::user()->id;
            $valoracionEnfermeria->visible = true;
            $valoracionEnfermeria->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* datos importantes */
            $valoracionEnfermeria->observacion = strip_tags($request->observacion);
            $valoracionEnfermeria->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado la valoración de enfemería exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de agregar la valoración de enfemería"]);
        }
    }

    public function modificarControlEgreso(Request $request)
    {
        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $modificar = HojaEnfermeriaControlEgreso::where("id", strip_tags($request->id))->first();
            $modificar->usuario_modifica = Auth::user()->id;
            $modificar->fecha_modificacion = Carbon::now();
            $modificar->visible = false;
            $modificar->tipo_modificacion = 'Editado';
            $modificar->save();
            DB::commit();

            /* se crea el nuevo examen */
            $controlEgreso = new HojaEnfermeriaControlEgreso;
            $controlEgreso->caso = $modificar->caso;
            $controlEgreso->id_anterior = $modificar->id;
            $controlEgreso->usuario = Auth::user()->id;
            $controlEgreso->visible = true;
            $controlEgreso->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* datos importantes */
            $controlEgreso->control = $modificar->control;
            $controlEgreso->dia = strip_tags($request->dia);
            $controlEgreso->noche = strip_tags($request->noche);
            $controlEgreso->observacion = $modificar->observacion;
            $controlEgreso->save();

            DB::commit();
            return response()->json(["exito" => "Se ha modificado un examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al modificar el examen"]);
        }

    }

    public function eliminarControlEgreso(Request $request)
    {
        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = HojaEnfermeriaControlEgreso::where("id", strip_tags($request->id))->first();
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now();
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado control egreso exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar control egreso"]);
        }
    }

    public function obtenerControlesEgresos($caso)
    {
        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();

        $examenes = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.control,
                f.dia,
                f.noche,
                f.observacion,
                f.visible,
                f.fecha_creacion
                from formulario_hoja_enfermeria_controles_egresos as f
                inner join usuarios as u on u.id = f.usuario
                where
                (f.caso = $caso and f.visible =true and f.fecha_creacion > '$inicio' and f.fecha_creacion < '$fin'
                )"));

        $resultado = [];

        foreach ($examenes as $key => $examen) {
            $control = "OTROS";
            if ($examen->control == 1) {
                $control = "DIURESIS";
            } elseif ($examen->control == 2) {
                $control = "DEPOSICIONES";
            } elseif ($examen->control == 3) {
                $control = "VOMITOS";
            } elseif ($examen->control == 4) {
                $control = "SNG";
            } elseif ($examen->control == 5) {
                $control = "DRENAJE 1";
            } elseif ($examen->control == 6) {
                $control = "DRENAJE 2";
            } elseif ($examen->control == 7) {
                $control = "DRENAJE 3";
            } elseif ($examen->control == 8) {
                $control = "Perd. Insensibles";
            }

            $html2 = "<div class='form-group'>
                <div class='col-md-10'>
                <input  class='controlFTotal form-control' id='diaControl" . $key . "' type='number' min='0' value='" . $examen->dia . "' data-id='" . $key . "'>
                </div>";
            $html3 = "<div class='form-group'>
            <div class='col-md-10'>
            <input  class='controlFTotal form-control' id='nocheControl" . $key . "' type='number' min='0' value='" . $examen->noche . "' data-id='" . $key . "'>
            </div>";

            $total = $examen->dia + $examen->noche;
            $resultado[] = [
                "<b>" . $control . "</b> <br> Creado el: " . Carbon::parse($examen->fecha_creacion)->format("d-m-Y H:i"),
                $html2,
                $html3,
                "<input class='form-control' id='totalControl" . $key . "' type='number' value='" . $total . "' disabled>",
                $examen->observacion,
                $examen->nombres . " " . $examen->apellido_paterno . " " . $examen->apellido_materno,
                "<div class='row'>
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-warning' onclick='modificarControl(" . $examen->id . "," . $key . ")'>Modificar</button>
                </div>
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarControl(" . $examen->id . ")'>Eliminar</button>
                </div>
                </div>",
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function addControlEgreso(Request $request)
    {
        try {
            DB::beginTransaction();

            $controlEgreso = new HojaEnfermeriaControlEgreso;
            $controlEgreso->caso = strip_tags($request->caso);
            $controlEgreso->usuario = Auth::user()->id;
            $controlEgreso->visible = true;
            $controlEgreso->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* datos importantes */
            $controlEgreso->control = strip_tags($request->control);
            $controlEgreso->dia = (strip_tags($request->dia) != "") ? strip_tags($request->dia) : 0;
            $controlEgreso->noche = (strip_tags($request->noche) != "") ? strip_tags($request->noche) : 0;
            $controlEgreso->observacion = strip_tags($request->observacion);
            $controlEgreso->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado control egreso exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de agregar control egreso"]);
        }
    }

    public function modificarLaboratorio(Request $request)
    {
        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $modificar = HojaEnfermeriaExamenLaboratorio::where("id", strip_tags($request->id))->first();
            $modificar->usuario_modifica = Auth::user()->id;
            $modificar->fecha_modificacion = Carbon::now();
            $modificar->tipo_modificacion = 'Finalizado';
            $modificar->usuario = Auth::user()->id;
            $modificar->visible = true;
            /* datos importantes */
            $modificar->estado = strip_tags($request->estadoLab);
            $modificar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha modificado un examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al modificar el examen"]);
        }

    }

    public function eliminarLaboratorio(Request $request)
    {
        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = HojaEnfermeriaExamenLaboratorio::where("id", strip_tags($request->id))->first();
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar examen"]);
        }
    }

    public function obtenerExamenesLaboratorio($caso)
    {

        $fin = Carbon::now()->endOfDay();

        $examenes = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.solicitado,
                f.tomado,
                f.visible,
                f.fecha_creacion,
                f.estado,
                f.fecha_programada
                from formulario_hoja_enfermeria_examenes_laboratorio as f
                inner join usuarios as u on u.id = f.usuario
                where
                (f.caso = $caso and f.visible =true and f.fecha_creacion < '$fin'
                )")); //and f.fecha_creacion > '$inicio'

        $resultado = [];

        foreach ($examenes as $key => $examen) {
            /* scripts par activar el input de horas  inicio y termino */

            if ($examen->solicitado == 1) {
                $solicitado = "Venosa";
            } elseif ($examen->solicitado == 2) {
                $solicitado = "Arterial";
            } elseif ($examen->solicitado == 3) {
                $solicitado = "Orina";
            } elseif ($examen->solicitado == 4) {
                $solicitado = "Clasificacion Bioquimicos";
            } else {
                $solicitado = "Hematológicos";
            }

            $tomado = "";
            if ($examen->tomado) {
                $tomado = Carbon::parse($examen->tomado)->format("d-m-Y H:i");
            }

            $html2 = "<div class='form-group'>
                <div class='col-md-10' style='text-align:center'>
                <h4>" . $tomado . "</h4>
                </div>";

            if ($examen->estado != 3) {
                $html = "<div style='text-align:center'><h4><span class='label label-warning'>SI</span></h4></div>";
                $botones = "<div class='row'>
                <div class='col-md-5'>
                  <input hidden type='number' name='estadoLab' id='estadoLab" . $key . "' value='3'>
                    <button type='button' class='btn-xs btn-success' onclick='modificarLaboratorio(" . $examen->id . "," . $key . ")'>Finalizar</button>
                </div>
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarLaboratorio(" . $examen->id . ")'>Eliminar</button>
                </div>
                </div>";
            } else {
                $html = "<div style='text-align:center'><h4><span class='label label-success'>NO</span></h4></div>";
                $botones = "<div class='row'>
              <div class='col-md-5'>
                  <button type='button' class='btn-xs btn-danger' onclick='eliminarLaboratorio(" . $examen->id . ")'>Eliminar</button>
              </div>
              </div>";
            }

            $resultado[] = [
                "<b>" . $solicitado . "</b> <br> Creado el: " . Carbon::parse($examen->fecha_creacion)->format("d-m-Y H:i"),
                $html2,
                $html,
                $examen->nombres . " " . $examen->apellido_paterno . " " . $examen->apellido_materno,
                $botones,
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function addExamenLaboratorio(Request $request)
    {
        try {
            DB::beginTransaction();

            $examenLaboratiorio = new HojaEnfermeriaExamenLaboratorio;
            $examenLaboratiorio->caso = strip_tags($request->caso);
            $examenLaboratiorio->usuario = Auth::user()->id;
            $examenLaboratiorio->visible = true;
            $examenLaboratiorio->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* datos importantes */
            $examenLaboratiorio->solicitado = strip_tags($request->solicitado);
            if ($request->tomado) {
                $examenLaboratiorio->tomado = Carbon::parse(strip_tags($request->tomado));
            }
            $examenLaboratiorio->estado = 1;
            $examenLaboratiorio->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar examen"]);
        }
    }

    public function modificarVolumenes(Request $request)
    {

        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $modificar = HojaEnfermeriaVolumenSolucion::where("id", $request->id)->first();
            $modificar->usuario_modifica = Auth::user()->id;
            $modificar->fecha_modificacion = Carbon::now();
            $modificar->visible = false;
            $modificar->tipo_modificacion = 'Editado';
            $modificar->save();

            /* se crea el nuevo examen */
            $volumenSolucion = new HojaEnfermeriaVolumenSolucion;
            $volumenSolucion->caso = $modificar->caso;
            $volumenSolucion->usuario = Auth::user()->id;
            $volumenSolucion->visible = true;
            $volumenSolucion->id_anterior = $modificar->id;
            $volumenSolucion->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* datos importantes */
            $volumenSolucion->tipo_solucion = $modificar->tipo_solucion;
            if ($request->tDia) {
                $volumenSolucion->volumendia = strip_tags($request->tDia);
            }
            if ($request->tNoche) {
                $volumenSolucion->volumennoche = strip_tags($request->tNoche);
            }
            if ($request->inicio) {
                $volumenSolucion->inicio = Carbon::parse(strip_tags($request->inicio));
            }
            if ($request->termino) {
                $volumenSolucion->termino = Carbon::parse(strip_tags($request->termino));
            }
            $volumenSolucion->save();

            DB::commit();
            return response()->json(["exito" => "Se ha modificado un examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar la planificación de cuidados"]);
        }

    }

    public function eliminarVolumen(Request $request)
    {

        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = HojaEnfermeriaVolumenSolucion::where("id", strip_tags($request->id))->first();
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now();
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al tratar de eliminar examen"]);
        }
    }

    public function obtenerVolumenesSolucionesPendientes($caso)
    {

        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();

        $examenes = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.tipo_solucion,
                f.volumendia,
                f.volumennoche,
                f.inicio,
                f.termino,
                f.visible,
                f.fecha_creacion
                from formulario_hoja_enfermeria_volumenes_soluciones as f
                inner join usuarios as u on u.id = f.usuario
                where
                (f.caso = $caso and f.visible = true and f.fecha_creacion > '$inicio' and f.fecha_creacion < '$fin'
                )"));

        $resultado = [];

        foreach ($examenes as $key => $examen) {
            /* scripts par activar el input de horas  inicio y termino */

            $tipo_solucion = "Ringer Lactato";
            if ($examen->tipo_solucion == 1) {
                $tipo_solucion = "S. Fisiologico";
            } elseif ($examen->tipo_solucion == 2) {
                $tipo_solucion = "S. Glucosalino";
            } elseif ($examen->tipo_solucion == 3) {
                $tipo_solucion = "S. Glucosado";
            }

            $fecha_inicio = "";
            if ($examen->inicio) {
                $fecha_inicio = Carbon::parse($examen->inicio)->format("H:i");
            }

            $fecha_termino = "";
            if ($examen->termino) {
                $fecha_termino = Carbon::parse($examen->termino)->format("H:i");
            }

            $html5 = "<div class='form-group'>
                <div class='col-md-10'>
                <input class='dPVolumen form-control' id='inicio" . $key . "' type='text' value='" . $fecha_inicio . "'>
                </div>";

            $html6 = "<div class='form-group'>
                <div class='col-md-10'>
                <input class='dPVolumen form-control' id='termino" . $key . "' type='text' value='" . $fecha_termino . "'>
                </div>";

            $total = $examen->volumendia + $examen->volumennoche;

            $resultado[] = [
                "<b>" . $tipo_solucion . "</b> <br> Creado el: " . Carbon::parse($examen->fecha_creacion)->format("d-m-Y H:i"),
                "<input class='form-control calcularTotal' id='tDia" . $key . "' data-id='" . $key . "' type='number' min='0' value='" . $examen->volumendia . "'>",
                "<input class='form-control calcularTotal' id='tNoche" . $key . "' data-id='" . $key . "' type='number' min='0' value='" . $examen->volumennoche . "'>",
                "<input class='form-control calcularTotal' id='tTotal" . $key . "' type='number' value='" . $total . "' disabled>",
                $html5,
                $html6,
                $examen->nombres . " " . $examen->apellido_paterno . " " . $examen->apellido_materno,
                "<div class='row'>
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-warning' onclick='modificarVolumen(" . $examen->id . "," . $key . ")'>Modificar</button>
                </div>
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarVolumen(" . $examen->id . ")'>Eliminar</button>
                </div>
                </div>",
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function addVolumenSolucion(Request $request)
    {

        try {
            DB::beginTransaction();

            $volumenSolucion = new HojaEnfermeriaVolumenSolucion;
            $volumenSolucion->caso = strip_tags($request->caso);
            $volumenSolucion->usuario = Auth::user()->id;
            $volumenSolucion->visible = true;
            $volumenSolucion->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            /* satos importantes */
            $volumenSolucion->tipo_solucion = strip_tags($request->tipoVolumen);
            if ($request->volumenDia) {
                $volumenSolucion->volumendia = strip_tags($request->volumenDia);
            }
            if ($request->volumenNoche) {
                $volumenSolucion->volumennoche = strip_tags($request->volumenNoche);
            }
            if ($request->inicio) {
                $volumenSolucion->inicio = Carbon::parse(strip_tags($request->inicio));
            }
            if ($request->termino) {
                $volumenSolucion->termino = Carbon::parse(strip_tags($request->termino));
            }
            $volumenSolucion->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado solución exitosamente"]);

        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al tratar de ingresar solución"]);
        }
    }

    public function eliminarExamenImagen(Request $request)
    {

        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = HojaEnfermeriaExamenImagen::where("id", strip_tags($request->id))->first();
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now();
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar la planificación de cuidados"]);
        }
    }

    public function modificarExamenImagen(Request $request)
    {

        try {
            DB::beginTransaction();

            /* se modifica el actual */
            $modificar = HojaEnfermeriaExamenImagen::where("id", strip_tags($request->id))->first();
            $modificar->usuario_modifica = Auth::user()->id;
            $modificar->fecha_modificacion = Carbon::now();

            $modificar->visible = false;

            /* si se marca como realizado, el examen debe ser solo borrado */
            if ($request->estado == 3) {
                $modificar->estado = strip_tags($request->estado);
                $modificar->tipo_modificacion = 'Terminado';
            } else {
                /* se crea uno nuevo */
                $modificar->tipo_modificacion = 'Editado';
                $examenLab = new HojaEnfermeriaExamenImagen;
                $examenLab->caso = $modificar->caso;
                $examenLab->usuario = Auth::user()->id;
                $examenLab->visible = true;
                $examenLab->solicitado = $modificar->solicitado;
                $examenLab->fecha_creacion = Carbon::parse($modificar->fecha_creacion);
                $examenLab->estado = strip_tags($request->estado); //1=pendiente,2=programado,3=realizado
                $examenLab->id_anterior = $modificar->id;
                if ($request->solicitado) {
                    $examenLab->fecha_solicitada = Carbon::parse(strip_tags($request->solicitado));
                }
                $examenLab->save();
            }
            $modificar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha modificado un examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar la planificación de cuidados"]);
        }

    }

    public function obtenerExamenesPendientes($caso)
    {

        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();

        $examenes = DB::select(DB::raw("select
                f.id,
                u.nombres,
                u.apellido_paterno,
                u.apellido_materno,
                f.fecha_solicitada,
                f.estado,
                f.solicitado,
                f.fecha_creacion
                from formulario_hoja_enfermeria_examenes_imagen as f
                inner join usuarios as u on u.id = f.usuario
                where
                f.caso = $caso and ((f.visible = true and f.estado in ('1','2') and f.fecha_creacion < '$inicio')
                or
                (f.visible = true and f.fecha_creacion > '$inicio' and f.fecha_creacion < '$fin' )
                or
                (f.visible = false and f.fecha_creacion > '$inicio' and f.fecha_creacion < '$fin' and f.estado = '3'))
                "));

        $resultado = [];
        foreach ($examenes as $key => $examen) {

            /* Input de fecha solicitada o FECHA/HORA, esta debe ser llenada cuando se tenga un eamen programado*/
            $fecha_solicitada = "";
            if ($examen->fecha_solicitada) {
                $fecha_solicitada = Carbon::parse($examen->fecha_solicitada)->format("d-m-Y H:i");
            }

            /* ESTADO es la que indica que puede estar pendientes por lo que seguira apareciendo mientras no se marque como realizado, lo mismo para el programado */
            $html4 = $fecha_solicitada;
            $html6 = "";
            if ($examen->estado == '3') {
                $html = "<div style='text-align:center'><h4><span class='label label-success'>Realizado</span></h4></div>";

            } else {
                /* si es distindo de realizado puede realizar modificaciones */
                $html6 = "<div class='col-md-5'>
                    <button type='button' class='btn-xs btn-warning' onclick='modificar(" . $examen->id . "," . $key . ")'>Modificar</button>
                </div>";
                $html4 = "<div class='form-group'>
                <div class='col-md-10'>
                <input class='dPImagen form-control' id='solicitada" . $key . "' type='text' value='" . $fecha_solicitada . "'>
                </div>";

                $html = "<select name='estadoExamen' class='form-control' id='estado" . $key . "'>";
                if ($examen->estado == "1") {
                    $html .= "<option value='1' selected>Pendiente</option>
                    <option value='2'>Programado</option>
                    <option value='3'>Realizado</option>";
                } else if ($examen->estado == "2") {
                    $html .= "<option value='1'>Pendiente</option>
                    <option value='2'selected>Programado</option>
                    <option value='3'>Realizado</option>";
                }
                $html .= "</select>";
            }
            $prestacion = "";
            $subunidad = "";
            $modalidad = "";
            $codigo = "";

            $info_examen = DB::table('catalogo_imagenologia')->where('id', $examen->solicitado)->first();
            if ($info_examen) {
                $prestacion = $info_examen->prestacion;
                $subunidad = $info_examen->subunidad;
                $modalidad = $info_examen->modalidad;
                $codigo = $info_examen->codigo;
            }

			$resultado [] = [
                "<b>".$prestacion."</b> <br> Código: ".$codigo." - Subunidad: ".$subunidad." <br> Modalidad: ".$modalidad." <br> Creado el: ".Carbon::parse($examen->fecha_creacion)->format("d-m-Y H:i"),
                $html,
                $html4,
                $examen->nombres . " " . $examen->apellido_paterno . " " . $examen->apellido_materno,
                "<div class='row'>
                " . $html6 . "
                <div class='col-md-5'>
                    <button type='button' class='btn-xs btn-danger' onclick='eliminarFila(" . $examen->id . ")'>Eliminar</button>
                </div>
                </div>",
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function consulta_examenes_imagenes($palabra){
        $datos = DB::table("catalogo_imagenologia")
            ->select(DB::raw("prestacion AS nombre, id, codigo,subunidad, modalidad"))
            ->where('prestacion', 'ilike', '%'.strtoupper($palabra).'%')
            ->orderBy('prestacion', 'asc')
            ->limit(100)
            ->get();

        return response()->json($datos);
    }

    public function addExamenImagen(Request $request)
    {

        try {
            DB::beginTransaction();

            $examenLab = new HojaEnfermeriaExamenImagen;
            $examenLab->caso = strip_tags($request->caso);
            $examenLab->usuario = Auth::user()->id;
            $examenLab->visible = true;
            $examenLab->solicitado = strip_tags($request->exam_item);
            $examenLab->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            // if ($request->exam_img2) {
            //     $examenLab->fecha_tomado =Carbon::parse(strip_tags($request->exam_img2));
            // }
            $examenLab->estado = strip_tags($request->exam_img3);
            if ($request->exam_img4) {
                $examenLab->fecha_solicitada = Carbon::parse(strip_tags($request->exam_img4));
            }
            $examenLab->save();
            DB::commit();
            return response()->json(["exito" => "Se ha ingresado examen exitosamente"]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar la planificación de cuidados"]);
        }

    }

    public function obtenerInterconsultas($id_caso)
    {
        try {

            DB::beginTransaction();

            $dao = new InterConsultaHelper();
            $resultado = $dao->obtenerInterconsultas($id_caso);

            DB::commit();
            return response()->json(["aaData" => $resultado], 200);

        } catch (Exception $e) {
            DB::rollback();
            $errores_controlados = ['Caso es nulo.'];
            $error = "Ha ocurrido un error.";
            if (in_array($e->getMessage(), $errores_controlados)) {$error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }

    }

    public function modificarInterconsulta(Request $request)
    {
        try {
            DB::beginTransaction();

            $dao = new InterConsultaHelper();
            $dao->modificarInterconsulta($request);

            DB::commit();

            return response()->json(["exito" => "Se ha actualizado el estado de la interconsulta exitosamente"], 200);
        } catch (Exception $e) {
            DB::rollback();

            $errores_controlados = ['Estado no es Realizada.'];
            $error = "Ha ocurrido un error.";
            if (in_array($e->getMessage(), $errores_controlados)) {$error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }

    public function finalizarInterconsulta(Request $request)
    {
        try {
            DB::beginTransaction();

            $dao = new InterConsultaHelper();
            $dao->finalizarInterconsulta($request);

            DB::commit();

            return response()->json(["exito" => "Se ha actualizado el estado de la interconsulta exitosamente"], 200);
        } catch (Exception $e) {
            DB::rollback();

            $errores_controlados = ['Estado no es Pendiente.'];
            $error = "Ha ocurrido un error.";
            if (in_array($e->getMessage(), $errores_controlados)) {$error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }

    public function eliminarInterconsulta(Request $request)
    {
        try {
            DB::beginTransaction();

            $dao = new InterConsultaHelper();
            $dao->eliminarInterconsulta($request);

            DB::commit();

            return response()->json(["exito" => "Se ha eliminado la interconsulta exitosamente"], 200);

        } catch (Exception $e) {
            DB::rollback();
            $errores_controlados = ['Id es nulo.'];
            $error = "Ha ocurrido un error.";
            if (in_array($e->getMessage(), $errores_controlados)) {$error = $e->getMessage();}

            return response()->json(["error" => $error], 500);
        }
    }

    public function validarTipoControlEstada(Request $request)
    {
        try {
            $valoresPermitidos = ['1' => '1', '2', '3','4'];
            $existe = array_search($request->tipo[0], $valoresPermitidos);
            if ($existe) {
                return response()->json(["valid" => true]);
            } else {
                return response()->json(["valid" => false, "message" => "Debe seleccionar un tipo de control existente"]);
            }

        } catch (Exception $e) {
            return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
        }
    }

    public function validarSelectAntibioticos(Request $request)
    {
        try {
            $existe = CaracteristicasAgente::find($request->antibiotico[0]);
            if ($existe) {
                return response()->json(["valid" => true]);
            } else {
                return response()->json(["valid" => false, "message" => "Debe seleccionar un antibiótico existente"]);
            }

        } catch (Exception $e) {
            return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
        }
    }

    public function validarSelectTipoProcedimiento(Request $request)
    {
        try {
            $valoresPermitidos = ['1' => '1', '2', '3', '4', '5', '6'];
            $existe = array_search($request->tipoProcedimiento[0], $valoresPermitidos);
            if ($existe) {
                return response()->json(["valid" => true]);
            } else {
                return response()->json(["valid" => false, "message" => "Debe seleccionar una técnica invasiva existente"]);
            }

        } catch (Exception $e) {
            return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
        }
    }

    public function validarSelectNumeroProcedimiento(Request $request)
    {
        try {
            $tipo_sng = ['1' => '8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30'];
            $tipo_cup = ['1' => '8', '10', '12', '14', '16', '18', '20'];
            $tipo_sny = ['1' => '6', '8', '10', '12'];
            $tipo_vpp = ['1' => '14', '16', '18', '20', '22', '24', '26'];

            if ($request->tipoProcedimiento == 1) {
                $existe = array_search($request->numero, $tipo_sng);
                if ($existe) {
                    return response()->json(["valid" => true]);
                } else {
                    return response()->json(["valid" => false, "message" => "Debe seleccionar un número correcto de SNG"]);
                }
            } elseif ($request->tipoProcedimiento == 2) {
                $existe = array_search($request->numero, $tipo_cup);
                if ($existe) {
                    return response()->json(["valid" => true]);
                } else {
                    return response()->json(["valid" => false, "message" => "Debe seleccionar un número correcto de CUP"]);
                }
            } elseif ($request->tipoProcedimiento == 3) {
                $existe = array_search($request->numero, $tipo_sny);
                if ($existe) {
                    return response()->json(["valid" => true]);
                } else {
                    return response()->json(["valid" => false, "message" => "Debe seleccionar un número correcto de SNY"]);
                }
            } elseif ($request->tipoProcedimiento == 4) {
                $existe = array_search($request->numero, $tipo_vpp);
                if ($existe) {
                    return response()->json(["valid" => true]);
                } else {
                    return response()->json(["valid" => false, "message" => "Debe seleccionar un número correcto de VPP"]);
                }
            }
        } catch (Exception $e) {
            return response()->json(["valid" => false, "message" => "Error de número"]);
        }
    }

    public function agregarAntibiotico(Request $request)
    {

        $validarTipo = ["1" =>'1','2','3','4'];
        $existe = array_diff($request->tipo, $validarTipo);
            if(count($existe) > 0){
                return response()->json(['error' =>'Debe seleccionar un tipo de control existente']);
            }

            
        foreach ($request->tipo as $key => $tipo) {
            if ($tipo == 3) {
                if ($request->tipoProcedimiento[$key] == 1) {
                    $tipo_sng = ['8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30'];

                    if (in_array($request->tipoProcedimiento[$key], $tipo_sng)) {
                        return response()->json(['error' =>'Debe seleccionar un número correcto de SNG']);
                    }
                } elseif ($request->tipoProcedimiento[$key] == 2) {
                    $tipo_cup = ['8', '10', '12', '14', '16', '18', '20'];

                    if (in_array($request->tipoProcedimiento[$key], $tipo_cup)) {
                        return response()->json(['error' =>'Debe seleccionar un número correcto de CUP']);
                    }
                } elseif ($request->tipoProcedimiento[$key] == 3) {
                    $tipo_sny = ['6', '8', '10', '12'];

                    if (in_array($request->tipoProcedimiento[$key], $tipo_sny)) {
                        return response()->json(['error' =>'Debe seleccionar un número correcto de SNY']);
                    }

                } elseif ($request->tipoProcedimiento[$key] == 4) {
                    $tipo_vpp = ['14', '16', '18', '20', '22', '24', '26'];

                    if (in_array($request->tipoProcedimiento[$key], $tipo_vpp)) {
                        return response()->json(['error' =>'Debe seleccionar un número correcto de VPP']);
                    }
                }
            }
    
            if (strlen($request->comentario[$key]) > 500) {
                return response()->json(["error" => "Mas de 500 caracteres"]);
            }
        }
        try {
            DB::beginTransaction();
            foreach ($request->tipo as $key => $tipo) {
                $controlEstada = new HojaEnfermeriaControlEstada;
                if ($tipo == 1) {
                    $controlEstada->tipo = 'Antibiótico';
                    $controlEstada->antibiotico = strip_tags($request->antibiotico[$key]);
                } elseif ($tipo == 2) {
                    $controlEstada->tipo = 'Operación';
                    $controlEstada->descripcion = strip_tags($request->operacion[$key]);
                } elseif ($tipo == 3) {
                    $controlEstada->tipo = 'Procedimiento invasivo';
                    $controlEstada->tipo_procedimiento = strip_tags($request->tipoProcedimiento[$key]);
                    if ($request->tipoProcedimiento[$key] == 1 && $request->numero_hidden[$key] != '' || $request->tipoProcedimiento[$key] == 2 && $request->numero_hidden[$key] != '' || $request->tipoProcedimiento[$key] == 3 && $request->numero_hidden[$key] != '' || $request->tipoProcedimiento[$key] == 4 && $request->numero_hidden[$key] != '') {
                        $controlEstada->numero = strip_tags($request->numero_hidden[$key]);
                    }
                }elseif ($tipo == 4) {
                    $controlEstada->tipo = 'Otro';
                }
                $controlEstada->caso = strip_tags($request->caso);
                $controlEstada->usuario_ingresa = Auth::user()->id;
                $controlEstada->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
                $controlEstada->fecha_colocacion = Carbon::parse(strip_tags($request->fechaColocacion[$key]));
                $controlEstada->visible = true;
                $controlEstada->estado = false;
                $controlEstada->comentario = $request->comentario[$key];
                $controlEstada->save();
            }

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado el control de estada exitosamente"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar el control de estada"]);
        }
    }

    public function llenarSelectTipoProcedecimiento(Request $request)
    {
        return HojaEnfermeriaControlEstada::infoSelectTipoProcedecimiento($request->tipoP);
    }

    public function obtenerControlEstadaAntibioticos($caso)
    {

        $resultado = [];
        $fechaHoy = Carbon::now();

        $antibioticos = HojaEnfermeriaControlEstada::where('caso', $caso)
            ->select("formulario_hoja_enfermeria_control_estada.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa", "formulario_hoja_enfermeria_control_estada.fecha_creacion", "formulario_hoja_enfermeria_control_estada.tipo", "formulario_hoja_enfermeria_control_estada.fecha_colocacion", "formulario_hoja_enfermeria_control_estada.visible", "formulario_hoja_enfermeria_control_estada.antibiotico", "u.nombres", "u.apellido_paterno", "u.apellido_materno", "formulario_hoja_enfermeria_control_estada.estado", "formulario_hoja_enfermeria_control_estada.comentario")
            ->join("usuarios as u", "u.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa")
            ->where('formulario_hoja_enfermeria_control_estada.caso', $caso)
            ->where('formulario_hoja_enfermeria_control_estada.visible', true)
            ->where('formulario_hoja_enfermeria_control_estada.tipo', '=', 'Antibiótico')->get();

        foreach ($antibioticos as $a) {

            $divInicioOpciones = "<div class='row'>";
            $divCierreOpciones = "</div>";
            $htmlFinalizar = "<div class='col-md-3'>
            <button type='button' class='btn-xs btn-success' onclick='finalizarAntibiotico(" . $a->id . ")'>Finalizar</button>
            </div>";
            $marginEliminar = "<div class='col-md-3' style='margin-left:15px;'>";
            // $noMarginEliminar = "<div class='col-md-3'>";
            $htmlEliminar = "<button type='button' class='btn-xs btn-danger' onclick='eliminarAntibiotico(" . $a->id . ")'>Eliminar</button>
            </div>";

            $opciones = "";
            if (!$a->estado) {
                $opciones .= $divInicioOpciones . $htmlFinalizar . $marginEliminar . $htmlEliminar . $divCierreOpciones;
                // $opciones .= $divInicioOpciones.$noMarginEliminar.$htmlEliminar.$divCierreOpciones; //comentado en caso de que deseen borrar un procedimiento ya finalizado.
            }
            $nombre_antibiotico = "Sin información";
            if ($a->antibiotico) {
                $info_antibiotico = CaracteristicasAgente::find($a->antibiotico);
                $nombre_antibiotico = ($info_antibiotico->nombre) ? $info_antibiotico->nombre : "Sin información";
            }

            $comentario = $a->comentario;

            $fecha_creacion = "";
            if ($a->fecha_creacion) {
                $fecha_creacion = Carbon::parse($a->fecha_creacion)->format("d-m-Y H:i");
            }
            $detalle = "<b>" . $nombre_antibiotico . "</b><br>" . $comentario . " <br><b> Agregado el: " . $fecha_creacion . "</b>";

            $comentario = $a->comentario;

            $detalle = "<div class='col-md-12'><div class='row'> <b>" . $nombre_antibiotico . "</b></div> <div class='row'><b>Comentario:</b></div> <div style='width:250px;overflow-wrap: break-word;'>" . $comentario . "</div> <div class='row'><b> Agregado el: " . $fecha_creacion . "</b></div> </div>";

            $fecha_colocacion = "";
            if ($a->fecha_colocacion) {
                $fecha_colocacion = Carbon::parse($a->fecha_colocacion)->format("d-m-Y H:i");
            }

            $inicioAntibiotico = Carbon::parse($a->fecha_colocacion);
            $diasDeAntibiotico = ($inicioAntibiotico->gt($fechaHoy)) ? $diasDeAntibiotico = 0 : $diasDeAntibiotico = $inicioAntibiotico->diffInDays($fechaHoy);

            $estado = (!$a->estado) ? "<div style='text-align:center'><h4><span class='label label-danger'>Pendiente</span></h4></div>" : "<div style='text-align:center'><h4><span class='label label-success'>Finalizado</span></h4></div>";

            $resultado[] = [
                $detalle,
                $fecha_colocacion,
                $diasDeAntibiotico,
                $estado,
                $a->nombres . " " . $a->apellido_paterno . " " . $a->apellido_materno,
                $opciones,
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function obtenerControlEstadaOperaciones($caso)
    {

        $resultado = [];
        $fechaHoy = Carbon::now();

        $operaciones = HojaEnfermeriaControlEstada::where('caso', $caso)
            ->select("formulario_hoja_enfermeria_control_estada.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa", "formulario_hoja_enfermeria_control_estada.fecha_creacion", "formulario_hoja_enfermeria_control_estada.tipo", "formulario_hoja_enfermeria_control_estada.fecha_colocacion", "formulario_hoja_enfermeria_control_estada.visible", "formulario_hoja_enfermeria_control_estada.antibiotico", "u.nombres", "u.apellido_paterno", "u.apellido_materno", "formulario_hoja_enfermeria_control_estada.estado", "formulario_hoja_enfermeria_control_estada.descripcion", "formulario_hoja_enfermeria_control_estada.comentario")
            ->join("usuarios as u", "u.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa")
            ->where('formulario_hoja_enfermeria_control_estada.caso', $caso)
            ->where('formulario_hoja_enfermeria_control_estada.visible', true)
            ->where('formulario_hoja_enfermeria_control_estada.tipo', '=', 'Operación')->get();

        foreach ($operaciones as $o) {
            $divInicioOpciones = "<div class='row'>";
            $divCierreOpciones = "</div>";
            $htmlFinalizar = "<div class='col-md-3'>
            <button type='button' class='btn-xs btn-success' onclick='finalizarAntibiotico(" . $o->id . ")'>Finalizar</button>
            </div>";
            $marginEliminar = "<div class='col-md-3' style='margin-left:15px;'>";
            $htmlEliminar = "<button type='button' class='btn-xs btn-danger' onclick='eliminarAntibiotico(" . $o->id . ")'>Eliminar</button>
            </div>";

            $opciones = "";
            if (!$o->estado) {
                $opciones .= $divInicioOpciones . $htmlFinalizar . $marginEliminar . $htmlEliminar . $divCierreOpciones;
                // $opciones .= $divInicioOpciones.$noMarginEliminar.$htmlEliminar.$divCierreOpciones; //comentado en caso de que deseen borrar un procedimiento ya finalizado.
            }

            $fecha_creacion = "";
            if ($o->fecha_creacion) {
                $fecha_creacion = Carbon::parse($o->fecha_creacion)->format("d-m-Y H:i");
            }

            $comentario = $o->comentario;

            $detalle = "<b>" . $o->descripcion . "</b><br>" . $comentario . "<br> <b>Agregado el: " . $fecha_creacion . "</b>";

            $fecha_colocacion = "";
            if ($o->fecha_colocacion) {
                $fecha_colocacion = Carbon::parse($o->fecha_colocacion)->format("d-m-Y H:i");
            }

            $inicioAntibiotico = Carbon::parse($o->fecha_colocacion);
            $diasDeAntibiotico = ($inicioAntibiotico->gt($fechaHoy)) ? $diasDeAntibiotico = 0 : $diasDeAntibiotico = $inicioAntibiotico->diffInDays($fechaHoy);

            $estado = (!$o->estado) ? "<div style='text-align:center'><h4><span class='label label-danger'>Pendiente</span></h4></div>" : "<div style='text-align:center'><h4><span class='label label-success'>Finalizado</span></h4></div>";

            $resultado[] = [
                $detalle,
                $fecha_colocacion,
                $diasDeAntibiotico,
                $estado,
                $o->nombres . " " . $o->apellido_paterno . " " . $o->apellido_materno,
                $opciones,
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function obtenerControlEstadaProcedimientos($caso)
    {

        $resultado = [];
        $fechaHoy = Carbon::now();

        $procedimientos = HojaEnfermeriaControlEstada::where('caso', $caso)
            ->select("formulario_hoja_enfermeria_control_estada.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa", "formulario_hoja_enfermeria_control_estada.fecha_creacion", "formulario_hoja_enfermeria_control_estada.tipo", "formulario_hoja_enfermeria_control_estada.fecha_colocacion", "formulario_hoja_enfermeria_control_estada.visible", "formulario_hoja_enfermeria_control_estada.antibiotico", "u.nombres", "u.apellido_paterno", "u.apellido_materno", "formulario_hoja_enfermeria_control_estada.tipo_procedimiento", "formulario_hoja_enfermeria_control_estada.estado", "formulario_hoja_enfermeria_control_estada.numero", "formulario_hoja_enfermeria_control_estada.comentario")
            ->join("usuarios as u", "u.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa")
            ->where('formulario_hoja_enfermeria_control_estada.caso', $caso)
            ->where('formulario_hoja_enfermeria_control_estada.visible', true)
            ->where('formulario_hoja_enfermeria_control_estada.tipo', '=', 'Procedimiento invasivo')->get();

        foreach ($procedimientos as $p) {

            $divInicioOpciones = "<div class='row'>";
            $divCierreOpciones = "</div>";
            $htmlFinalizar = "<div class='col-md-3'>
            <button type='button' class='btn-xs btn-success' onclick='finalizarAntibiotico(" . $p->id . ")'>Finalizar</button>
            </div>";
            $marginEliminar = "<div class='col-md-3' style='margin-left:15px;'>";
            $htmlEliminar = "<button type='button' class='btn-xs btn-danger' onclick='eliminarAntibiotico(" . $p->id . ")'>Eliminar</button>
            </div>";

            $opciones = "";
            if (!$p->estado) {
                $opciones .= $divInicioOpciones . $htmlFinalizar . $marginEliminar . $htmlEliminar . $divCierreOpciones;
                // $opciones .= $divInicioOpciones.$noMarginEliminar.$htmlEliminar.$divCierreOpciones; //comentado en caso de que deseen borrar un procedimiento ya finalizado.
            }

            $fecha_creacion = "";
            if ($p->fecha_creacion) {
                $fecha_creacion = Carbon::parse($p->fecha_creacion)->format("d-m-Y H:i");
            }

            $comentario = $p->comentario;

            if ($p->tipo_procedimiento == 1 || $p->tipo_procedimiento == 02 || $p->tipo_procedimiento == 3 || $p->tipo_procedimiento == 4) {
                $detalle = "<b>" . HojaEnfermeriaControlEstada::tipoProcedimiento($p->tipo_procedimiento) . " Numero: " . $p->numero . "</b><br>" . $comentario . " <br> <b>Agregado el: " . $fecha_creacion . "</b>";
            } else {
                $detalle = "<b>" . HojaEnfermeriaControlEstada::tipoProcedimiento($p->tipo_procedimiento) . "</b><br>" . $comentario . " <br> <b>Agregado el: " . $fecha_creacion . "</b>";
            }

            $fecha_colocacion = "";
            if ($p->fecha_colocacion) {
                $fecha_colocacion = Carbon::parse($p->fecha_colocacion)->format("d-m-Y H:i");
            }

            $inicioAntibiotico = Carbon::parse($p->fecha_colocacion);
            $diasDeAntibiotico = ($inicioAntibiotico->gt($fechaHoy)) ? $diasDeAntibiotico = 0 : $diasDeAntibiotico = $inicioAntibiotico->diffInDays($fechaHoy);

            $estado = (!$p->estado) ? "<div style='text-align:center'><h4><span class='label label-danger'>Pendiente</span></h4></div>" : "<div style='text-align:center'><h4><span class='label label-success'>Finalizado</span></h4></div>";

            $resultado[] = [
                $detalle,
                $fecha_colocacion,
                $diasDeAntibiotico,
                $estado,
                $p->nombres . " " . $p->apellido_paterno . " " . $p->apellido_materno,
                $opciones,
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }


    public function obtenerControlEstadaOtros($caso)
    {

        $resultado = [];
        $fechaHoy = Carbon::now();

        $otros = HojaEnfermeriaControlEstada::where('caso', $caso)
        ->select("formulario_hoja_enfermeria_control_estada.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa", "formulario_hoja_enfermeria_control_estada.fecha_creacion", "formulario_hoja_enfermeria_control_estada.tipo", "formulario_hoja_enfermeria_control_estada.fecha_colocacion", "formulario_hoja_enfermeria_control_estada.visible", "formulario_hoja_enfermeria_control_estada.antibiotico", "u.nombres", "u.apellido_paterno", "u.apellido_materno", "formulario_hoja_enfermeria_control_estada.estado", "formulario_hoja_enfermeria_control_estada.comentario")
        ->join("usuarios as u", "u.id", "formulario_hoja_enfermeria_control_estada.usuario_ingresa")
        ->where('formulario_hoja_enfermeria_control_estada.caso', $caso)
        ->where('formulario_hoja_enfermeria_control_estada.visible', true)
        ->where('formulario_hoja_enfermeria_control_estada.tipo', '=', 'Otro')->get();

        foreach ($otros as $a) {

            $divInicioOpciones = "<div class='row'>";
            $divCierreOpciones = "</div>";
            $htmlFinalizar = "<div class='col-md-3'>
            <button type='button' class='btn-xs btn-success' onclick='finalizarAntibiotico(" . $a->id . ")'>Finalizar</button>
            </div>";
            $marginEliminar = "<div class='col-md-3' style='margin-left:15px;'>";
            // $noMarginEliminar = "<div class='col-md-3'>";
            $htmlEliminar = "<button type='button' class='btn-xs btn-danger' onclick='eliminarAntibiotico(" . $a->id . ")'>Eliminar</button>
            </div>";

            $opciones = "";
            if (!$a->estado) {
                $opciones .= $divInicioOpciones . $htmlFinalizar . $marginEliminar . $htmlEliminar . $divCierreOpciones;
                // $opciones .= $divInicioOpciones.$noMarginEliminar.$htmlEliminar.$divCierreOpciones; //comentado en caso de que deseen borrar un procedimiento ya finalizado.
            }
   
            /* $nombre_otro = "Sin información";
            if ($a->antibiotico) {
                $info_otro = CaracteristicasAgente::find($a->antibiotico);
                $nombre_otro = ($info_otro->nombre) ? $info_otro->nombre : "Sin información";
            } */

            $comentario = $a->comentario;

            $fecha_creacion = "";
            if ($a->fecha_creacion) {
                $fecha_creacion = Carbon::parse($a->fecha_creacion)->format("d-m-Y H:i");
            }
            /* <b>" . $nombre_otro . "</b> */
            $detalle = "<br>" . $comentario . " <br><b> Agregado el: " . $fecha_creacion . "</b>";

            $comentario = $a->comentario;

            /* <b>" . $nombre_otro . "</b> */
            $detalle = "<div class='col-md-12'><div class='row'> </div> <div class='row'><b>Comentario:</b></div> <div style='width:250px;overflow-wrap: break-word;'>" . $comentario . "</div> <div class='row'><b> Agregado el: " . $fecha_creacion . "</b></div> </div>";

            $fecha_colocacion = "";
            if ($a->fecha_colocacion) {
                $fecha_colocacion = Carbon::parse($a->fecha_colocacion)->format("d-m-Y H:i");
            }

            $inicioOtro = Carbon::parse($a->fecha_colocacion);
            $diasOtro = ($inicioOtro->gt($fechaHoy)) ? $diasOtro = 0 : $diasOtro = $inicioOtro->diffInDays($fechaHoy);

            $estado = (!$a->estado) ? "<div style='text-align:center'><h4><span class='label label-danger'>Pendiente</span></h4></div>" : "<div style='text-align:center'><h4><span class='label label-success'>Finalizado</span></h4></div>";

            $resultado[] = [
                $detalle,
                $fecha_colocacion,
                $diasOtro,
                $estado,
                $a->nombres . " " . $a->apellido_paterno . " " . $a->apellido_materno,
                $opciones,
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function eliminarAntibiotico(Request $request)
    {
        try {
            DB::beginTransaction();

            $fecha_eliminacion = Carbon::now()->format("Y-m-d H:i:s");

            $eliminado = HojaEnfermeriaControlEstada::where("id", $request->id)->first();
            $eliminado->usuario_modifica = Auth::user()->id;
            $eliminado->fecha_eliminacion = $fecha_eliminacion;
            $eliminado->tipo_modificacion = 'Eliminado';
            $inicioAntibiotico = Carbon::parse($eliminado->fecha_colocacion);
            $diasDeAntibiotico = $inicioAntibiotico->diffInDays($fecha_eliminacion);
            $eliminado->dias_antibiotico = $diasDeAntibiotico;
            $eliminado->visible = false;
            $eliminado->estado = false;
            $eliminado->save();
            DB::commit();

            return response()->json(["exito" => "Se ha eliminado el control de estada exitosamente"]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al eliminar el control de estada"]);
        }
    }

    public function finalizarAntibiotico(Request $request)
    {
        try {
            DB::beginTransaction();
            $fecha_finalizar = Carbon::now()->format("Y-m-d H:i:s");
            $finalizar = HojaEnfermeriaControlEstada::where("id", $request->id)->first();
            $finalizar->usuario_modifica = Auth::user()->id;
            $finalizar->fecha_modificacion = $fecha_finalizar;
            $inicioAntibiotico = Carbon::parse($finalizar->fecha_colocacion);
            $diasDeAntibiotico = $inicioAntibiotico->diffInDays($fecha_finalizar);
            $finalizar->dias_antibiotico = $diasDeAntibiotico;
            $finalizar->visible = true;
            $finalizar->tipo_modificacion = 'Finalizado';
            $finalizar->estado = true;
            $finalizar->save();

            /*registro nuevo*/
            $finalizado = new HojaEnfermeriaControlEstada;
            $finalizado->id_anterior = $finalizar->id;
            $finalizado->caso = $finalizar->caso;
            $finalizado->usuario_ingresa = Auth::user()->id;
            $finalizado->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $finalizado->dias_antibiotico = $finalizar->dias_antibiotico;
            $finalizado->visible = false;
            $finalizado->save();
            DB::commit();

            return response()->json(["exito" => "Se ha finalizado el control de estada exitosamente"]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al finalizar el control de estada"]);
        }
    }

    public function obtenerDiasEstada(Request $request)
    {
        $diasEstada = null;
        try {
            $caso = $request->caso;
            $estada = DB::table('ultimas_ocupaciones')->where('caso', $caso)->first(['fecha_ingreso_real']);

            $hoy = Carbon::now()->format("Y-m-d H:i:s");
            $inicioEstada = Carbon::parse($estada->fecha_ingreso_real);
            $diasEstada = $inicioEstada->diffInDays($hoy);
        } catch (\Exception $e) {}
        return $diasEstada;
    }

    public function agregarSignosVitales(Request $request)
    { 
        $fio1 = 24;
        if (is_numeric(strip_tags($request->fio1))) {
            $fio1 = strip_tags($request->fio1);
        } 

        try {
            DB::beginTransaction();
            $signosH = new SignosVitalesHelper();
            $signosH->agregarSignosVitales($request);


            DB::commit();
            return response()->json(["exito" => "Se han ingresado los signos vitales exitosamente"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            Log::error($ex);
            return response()->json(["error" => "Error al ingresar los signos vitales"]);
        }
    }

    public function validarObtenerSignosVitales(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'date_format:d-m-Y',
            'fecha_hasta' => 'date_format:d-m-Y',
        ]);

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if($request->fecha_desde == '' && $request->fecha_hasta != ''){
            return response()->json(["error" => "La fecha <b>'Desde'</b> es obligatoria"]);
        }elseif($request->fecha_desde != '' && $request->fecha_hasta == ''){
            return response()->json(["error" => "La fecha <b>'Hasta'</b> es obligatoria"]);
        }elseif($request->fecha_desde == '' && $request->fecha_hasta == ''){
            return response()->json(["error" => "Las fechas son obligatorias"]);
        }elseif($fecha_desde > $fecha_hasta){
            return response()->json(["error" => "La fecha <b>'Desde'</b> debe ser menor a la fecha <b>'Hasta'</b>"]);
        }elseif($fecha_hasta < $fecha_desde){
            return response()->json(["error" => "La fecha <b>'Hasta'</b> debe ser mayor a la fecha <b>'Desde'</b>"]);
        }else{
            return response()->json(["exito" => "correcto"]);
        }
    }

    public function obtenerSignosVitales(Request $request)
    {
        try {
            $signosH = new SignosVitalesHelper();
            $resultado = $signosH->obtenerSignosVitales($request);
            return response()->json(["aaData" => $resultado]);
        } catch (Exception $ex) {
            DB::rollback();
            Log::info($ex);
            return response()->json(["error" => "Ha ocurrido un error", 500]);
        }
    }

    public function graficarSignosVitales(Request $request, $caso_id)
    {

        try {

            $request->validate([
                'fecha' => 'date_format:d-m-Y',
            ]);

            $fecha = $request->fecha;
            if($request->fecha == ''){
                return response()->json(["error" => "La <b>'Fecha'</b> es obligadoria"]);
            }

            $query = "select distinct on (date_trunc('hour', i.horario1)) extract(hour from i.horario1) as horario, i.presion_arterial1 as presion_alterial_sis, i.presion_arterial1dia as presion_alterial_dia, i.pulso1 as frecuencia_cardiaca, i.frec_respiratoria1 as frecuencia_respiratoria, i.temp_axilo1 as temperatura_axilo,i.temp_rectal as temperatura_rectal, 	i.saturacion1 as saturacion_oxigeno, i.hemoglucotest1 as hemoglucotest 
            from formulario_hoja_enfermeria_signos_vitales as i
            inner join usuarios as u on u.id = i.usuario_ingresa
            where i.caso = ? and i.visible = true and i.horario1::date =  ? and i.id_indicacion is null
            order by date_trunc('hour', i.horario1), i.horario1  DESC";

            $ingreso_datos = DB::select($query, [$caso_id, Carbon::parse($fecha)->format('Y-m-d')]);

            //CREACION DE DATA SET POR TIPO DE SIGNOS
            $presion_alterial_sis_data_set = [];
            $presion_alterial_dia_data_set = [];
            $frecuencia_cardiaca_data_set = [];
            $frecuencia_respiratoria_data_set = [];
            $temperatura_axilo_data_set = [];
            $temperatura_rectal_data_set = [];
            $saturacion_origeno_data_set = [];
            $hemoglucotest_data_set = [];

            //inicializacion de los data set
            for ($hora = 0; $hora < 24; $hora++) {
                array_push($presion_alterial_sis_data_set, null);
                array_push($presion_alterial_dia_data_set, null);
                array_push($frecuencia_cardiaca_data_set, null);
                array_push($frecuencia_respiratoria_data_set, null);
                array_push($temperatura_axilo_data_set, null);
                array_push($temperatura_rectal_data_set, null);
                array_push($saturacion_origeno_data_set, null);
                array_push($hemoglucotest_data_set, null);
            }

            foreach ($ingreso_datos as $key => $signos) {

                $presion_alterial_sis = (isset($signos->presion_alterial_sis) && trim($signos->presion_alterial_sis) != "") ? (float) $signos->presion_alterial_sis : null;

                $presion_alterial_dia = (isset($signos->presion_alterial_dia) && trim($signos->presion_alterial_dia) != "") ? (float) $signos->presion_alterial_dia : null;

                $frecuencia_cardiaca = (isset($signos->frecuencia_cardiaca) && trim($signos->frecuencia_cardiaca) != "") ? (float) $signos->frecuencia_cardiaca : null;

                $frecuencia_respiratoria = (isset($signos->frecuencia_respiratoria) && trim($signos->frecuencia_respiratoria) != "") ? (float) $signos->frecuencia_respiratoria : null;

                $temperatura_axilo = (isset($signos->temperatura_axilo) && trim($signos->temperatura_axilo) != "") ? (float) $signos->temperatura_axilo : null;

                $temperatura_rectal = (isset($signos->temperatura_rectal) && trim($signos->temperatura_rectal) != "") ? (float) $signos->temperatura_rectal : null;

                $saturacion_oxigeno = (isset($signos->saturacion_oxigeno) && trim($signos->saturacion_oxigeno) != "") ? (float) $signos->saturacion_oxigeno : null;

                $hemoglucotest = (isset($signos->hemoglucotest) && trim($signos->hemoglucotest) != "") ? (float) $signos->hemoglucotest : null;

                $horario = (int) $signos->horario;

                $presion_alterial_sis_data_set[$horario] = $presion_alterial_sis;
                $presion_alterial_dia_data_set[$horario] = $presion_alterial_dia;
                $frecuencia_cardiaca_data_set[$horario] = $frecuencia_cardiaca;
                $frecuencia_respiratoria_data_set[$horario] = $frecuencia_respiratoria;
                $temperatura_axilo_data_set[$horario] = $temperatura_axilo;
                $temperatura_rectal_data_set[$horario] = $temperatura_rectal;
                $saturacion_origeno_data_set[$horario] = $saturacion_oxigeno;
                $hemoglucotest_data_set[$horario] = $hemoglucotest;

            }
            //MASTER DATASET
            $master_data_set = [
                "presion_alterial_sis_data_set" => $presion_alterial_sis_data_set,
                "presion_alterial_dia_data_set" => $presion_alterial_dia_data_set,
                "frecuencia_cardiaca_data_set" => $frecuencia_cardiaca_data_set,
                "frecuencia_respiratoria_data_set" => $frecuencia_respiratoria_data_set,
                "temperatura_axilo_data_set" => $temperatura_axilo_data_set,
                "temperatura_rectal_data_set" => $temperatura_rectal_data_set,
                "saturacion_origeno_data_set" => $saturacion_origeno_data_set,
                "hemoglucotest_data_set" => $hemoglucotest_data_set,
            ];
            return response()->json(["master_data_set" => $master_data_set], 200);

        } catch (Exception $e) {
            return response()->json(["error" => "Ha ocurrido un error con la carga del grafico"], 500);
        }

    }

    public function eliminarSignoVital(Request $request)
    {
        try {
            DB::beginTransaction();
            $signosH = new SignosVitalesHelper();
            $delete = $signosH->eliminarSignoVital($request);
            DB::commit();

            $message = "Se ha eliminado el signo vital exitosamente";
            if (!$delete->otros_signos) {
                $message = "Se ha eliminado el signo vital exitosamente y la hora ha quedado sin check";
            }

            return response()->json(["exito" => $message]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al eliminar el signo vital"]);
        }
    }

    public function modificarSignoVital(Request $request)
    {
        if (is_numeric(strip_tags($request->fio1))) {
            $fio1 = strip_tags($request->fio1);
        } else {
            $fio1 = 24;
        }

        try {
            DB::beginTransaction();
            $signosH = new SignosVitalesHelper();
            $signosH->modificarSignoVital($request);
            DB::commit();

            return response()->json(["exito" => "Se ha actualizado el signo vital exitosamente"]);
        } catch (Exception $ex) {
            DB::rollback();
            Log::info($ex);
            $error_msg = $ex->getMessage();
            return response()->json(["error" => $error_msg]);
        }
    }

    public function agregarRiesgoCaida(Request $request)
    {
        try {
            DB::beginTransaction();
            $riesgoCaida = new HojaEnfermeriaRiesgoCaida;
            $riesgoCaida->caso = $request->caso;
            $riesgoCaida->procedencia = "Barandas";
            $riesgoCaida->usuario_ingresa = Auth::user()->id;
            $riesgoCaida->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $riesgoCaida->horario = Carbon::parse($request->horario);
            $riesgoCaida->visible = true;
            $riesgoCaida->criterio_edad = $request->criterioEdad;
            $riesgoCaida->criterio_compr_conciencia = $request->criterioComprConciencia;
            $riesgoCaida->criterio_agi_psicomotora = $request->criterioAgiPsicomotora;
            $riesgoCaida->criterio_lim_sensorial = $request->criterioLimSensorial;
            $riesgoCaida->criterio_lim_motora = $request->criterioLimMotora;
            $riesgoCaida->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado el riesgo caída exitosamente"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar el riesgo caída"]);
        }
    }

    public function obtenerRiesgoCaidas($caso)
    {
        $hoy = Carbon::now()->startOfDay();
        $mañana = Carbon::now()->endOfDay();

        $resultado = [];

        $riesgoCaidas = DB::select(DB::raw("
        select i.id, i.fecha_creacion, i.horario, i.criterio_edad, i.criterio_compr_conciencia, i.criterio_agi_psicomotora, i.criterio_lim_sensorial, i.criterio_lim_motora, u.nombres, u.apellido_paterno, u.apellido_materno
        from formulario_hoja_enfermeria_riesgo_caida as i
        inner join usuarios as u on u.id = i.usuario_ingresa
        where i.caso = $caso and i.visible = true and i.fecha_creacion > '$hoy' and i.fecha_creacion < '$mañana' and procedencia = 'Barandas'"));

        foreach ($riesgoCaidas as $key => $caida) {
            $opciones = "<div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-warning' onclick='modificarRiesgoCaida(" . $caida->id . "," . $key . ")'>Modificar</button>
            </div>
            <br><br>
            <div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-danger' onclick='eliminarRiesgoCaida(" . $caida->id . ")'>Eliminar</button>
            </div>";

            $horario = "";
            if ($caida->horario) {
                $horario = Carbon::parse($caida->horario)->format("H:i");
            }
            $horario = "<div class='form-group'>
            <div class='col-md-11'>
            <input class='dPriesgo form-control' id='thorario" . $key . "' type='text' value='" . $horario . "'>
            </div>";

            $criterioEdad = "<select class='form-control calculartTotal' data-id='" . $key . "' id='tcriterioEdad" . $key . "'>";
            $criterioComprConciencia = "<select  class='form-control calculartTotal' data-id='" . $key . "' id='tcriterioComprConciencia" . $key . "'>";
            $criterioAgiPsicomotora = "<select  class='form-control calculartTotal' data-id='" . $key . "' id='tcriterioAgiPsicomotora" . $key . "'>";
            $criterioLimSensorial = "<select  class='form-control calculartTotal' data-id='" . $key . "' id='tcriterioLimSensorial" . $key . "'>";
            $criterioLimMotora = "<select  class='form-control calculartTotal' data-id='" . $key . "' id='tcriterioLimMotora" . $key . "'>";

            $criterioedad = 0;
            if ($caida->criterio_edad == true) {
                $criterioEdad .= "<option value='true' selected>Si</option>
                <option value='false'>No</option>";
                $criterioedad = 1;
            } else {
                $criterioEdad .= "<option value='true'>Si</option>
                <option value='false' selected>No</option>";
                $criterioedad = 0;
            }

            $criteriocomprconciencia = 0;
            if ($caida->criterio_compr_conciencia == true) {
                $criterioComprConciencia .= "<option value='true' selected>Si</option>
                <option value='false'>No</option>";
                $criteriocomprconciencia = 2;
            } else {
                $criterioComprConciencia .= "<option value='true'>Si</option>
                <option value='false' selected>No</option>";
                $criteriocomprconciencia = 0;
            }

            $criterioagipsicomotora = 0;
            if ($caida->criterio_agi_psicomotora == true) {
                $criterioAgiPsicomotora .= "<option value='true' selected>Si</option>
                <option value='false'>No</option>";
                $criterioagipsicomotora = 2;
            } else {
                $criterioAgiPsicomotora .= "<option value='true'>Si</option>
                <option value='false' selected>No</option>";
                $criterioagipsicomotora = 0;
            }

            $criteriolimsensorial = 0;
            if ($caida->criterio_lim_sensorial == true) {
                $criterioLimSensorial .= "<option value='true' selected>Si</option>
                <option value='false'>No</option>";
                $criteriolimsensorial = 1;
            } else {
                $criterioLimSensorial .= "<option value='true'>Si</option>
                <option value='false' selected>No</option>";
                $criteriolimsensorial = 0;
            }

            $criteriolimmotora = 0;
            if ($caida->criterio_lim_motora == true) {
                $criterioLimMotora .= "<option value='true' selected>Si</option>
                <option value='false'>No</option>";
                $criteriolimmotora = 1;
            } else {
                $criterioLimMotora .= "<option value='true'>Si</option>
                <option value='false' selected>No</option>";
                $criteriolimmotora = 0;
            }

            $criterioEdad .= "</select>";
            $criterioComprConciencia .= "</select>";
            $criterioAgiPsicomotora .= "</select>";
            $criterioLimSensorial .= "</select>";
            $criterioLimMotora .= "</select>";

            $total = ($criterioedad + $criteriocomprconciencia + $criterioagipsicomotora + $criteriolimsensorial + $criteriolimmotora);

            $resultado[] = [
                "</b>Creado el: </b>" . Carbon::parse($caida->fecha_creacion)->format("d-m-Y H:i"),
                $horario,
                $criterioEdad,
                $criterioComprConciencia,
                $criterioAgiPsicomotora,
                $criterioLimSensorial,
                $criterioLimMotora,
                "<input class='form-control' id='ttotal" . $key . "' type='number' value='" . $total . "' readonly>",
                $caida->nombres . " " . $caida->apellido_paterno . " " . $caida->apellido_materno,
                $opciones,
            ];
        }

        return response()->json(["aaData" => $resultado]);

    }

    public function eliminarRiesgoCaida(Request $request)
    {
        try {
            DB::beginTransaction();
            $eliminado = HojaEnfermeriaRiesgoCaida::where("id", $request->id)->first();
            $eliminado->usuario_modifica = Auth::user()->id;
            $eliminado->fecha_eliminacion = Carbon::now()->format("Y-m-d H:i:s");
            $eliminado->visible = false;
            $eliminado->tipo_modificacion = 'Eliminado';

            $eliminado->save();
            DB::commit();

            return response()->json(["exito" => "Se ha eliminado el riesgo caída exitosamente"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al eliminar el riesgo caída"]);
        }
    }

    public function modificarRiesgoCaida(Request $request)
    {
        try {
            DB::beginTransaction();
            $editado = HojaEnfermeriaRiesgoCaida::where("id", $request->id)->first();
            $editado->usuario_modifica = Auth::user()->id;
            $editado->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $editado->horario = Carbon::parse($request->horario);
            $editado->criterio_edad = $request->criterioEdad;
            $editado->criterio_compr_conciencia = $request->criterioComprConciencia;
            $editado->criterio_agi_psicomotora = $request->criterioAgiPsicomotora;
            $editado->criterio_lim_sensorial = $request->criterioLimSensorial;
            $editado->criterio_lim_motora = $request->criterioLimMotora;
            $editado->visible = false;
            $editado->tipo_modificacion = 'Editado';
            $editado->save();

            /*registro nuevo */
            $rCaida = new HojaEnfermeriaRiesgoCaida;
            $rCaida->id_anterior = $editado->id;
            $rCaida->procedencia = $editado->procedencia;
            $rCaida->caso = $editado->caso;
            $rCaida->usuario_ingresa = Auth::user()->id;
            $rCaida->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $rCaida->horario = $editado->horario;
            $rCaida->criterio_edad = $editado->criterio_edad;
            $rCaida->criterio_compr_conciencia = $editado->criterio_compr_conciencia;
            $rCaida->criterio_agi_psicomotora = $editado->criterio_agi_psicomotora;
            $rCaida->criterio_lim_sensorial = $editado->criterio_lim_sensorial;
            $rCaida->criterio_lim_motora = $editado->criterio_lim_motora;
            $rCaida->visible = true;
            $rCaida->save();

            DB::commit();

            return response()->json(["exito" => "Se ha actualizado el signo riesgo caída"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al actualizar la informacion del riesgo caída"]);
        }
    }

    public function agregarProcedimientoInvasivo(Request $request)
    {
        try {
            DB::beginTransaction();
            $procedimientoInvasivo = new HojaEnfermeriaProcedimientoInvasivo;
            $procedimientoInvasivo->caso = $request->caso;
            $procedimientoInvasivo->usuario = Auth::user()->id;
            $procedimientoInvasivo->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $procedimientoInvasivo->visible = true;
            $procedimientoInvasivo->tipo_procedimiento = $request->tipoP;
            $procedimientoInvasivo->numero = ($request->numeroP != '') ? $request->numeroP : null;
            $procedimientoInvasivo->fecha_procedimiento = ($request->fechaProcedimiento != '') ? Carbon::parse($request->fechaProcedimiento) : null;
            $procedimientoInvasivo->estado = $request->estadoP;
            // $procedimientoInvasivo->
            $procedimientoInvasivo->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado el procedimiento invasivo"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al ingresar el riesgo procedimiento invasivo"]);
        }
    }

    public function obtenerProcedimientosInvasivos($caso)
    {

        $resultado = [];
        $fechaHoy = Carbon::now();

        $procedimientos = HojaEnfermeriaProcedimientoInvasivo::where('caso', $caso)
            ->select('formulario_hoja_enfermeria_procedimientos_invasivos.id', 'formulario_hoja_enfermeria_procedimientos_invasivos.estado', 'formulario_hoja_enfermeria_procedimientos_invasivos.tipo_procedimiento',
                'formulario_hoja_enfermeria_procedimientos_invasivos.fecha_creacion', 'formulario_hoja_enfermeria_procedimientos_invasivos.fecha_procedimiento',
                'formulario_hoja_enfermeria_procedimientos_invasivos.numero', 'formulario_hoja_enfermeria_procedimientos_invasivos.visible', 'formulario_hoja_enfermeria_procedimientos_invasivos.usuario', 'u.nombres', 'u.apellido_paterno', 'u.apellido_materno')
            ->join("usuarios as u", "u.id", "formulario_hoja_enfermeria_procedimientos_invasivos.usuario")
            ->where('formulario_hoja_enfermeria_procedimientos_invasivos.caso', $caso)
            ->where('formulario_hoja_enfermeria_procedimientos_invasivos.visible', true)
            ->get();

        foreach ($procedimientos as $p) {
            $opciones = "<div class='row'>
            <div class='col-md-3'>
                <button type='button' class='btn-xs btn-success' onclick='finalizarProcedimiento(" . $p->id . ")'>Finalizar</button>
            </div>
            <div class='col-md-3'>
                <button type='button' style='margin-left: 10px' class='btn-xs btn-danger' onclick='eliminarProcedimiento(" . $p->id . ")'>Eliminar</button>
            </div>
            </div>";

            if ($p->estado == 1) {
                $htmlEstado = 'Pendiente';
            } else {
                $htmlEstado = "<div style='text-align:center'><h4><span class='label label-success'>Finalizado</span></h4></div>";
                $opciones = "<div class='row'>
                <div class='col-md-3'>
                <button type='button' class='btn-xs btn-success' disabled>Finalizar</button>
                </div>
                <div class='col-md-3'>
                <button type='button' style='margin-left: 10px' class='btn-xs btn-danger' disabled>Eliminar</button>
                </div>
                </div>";
            }

            $fecha_creacion = "";
            if ($p->fecha_creacion) {
                $fecha_creacion = Carbon::parse($p->fecha_creacion)->format("d-m-Y H:i");
            }

            $fecha_procedimiento = "";
            if ($p->fecha_procedimiento) {
                $fecha_procedimiento = Carbon::parse($p->fecha_procedimiento)->format("d-m-Y H:i");
            }

            $tipo_procedimiento = "";
            if ($p->tipo_procedimiento == 1) {
                $tipo_procedimiento = "SNG";
            } elseif ($p->tipo_procedimiento == 2) {
                $tipo_procedimiento = "SNY";
            } elseif ($p->tipo_procedimiento == 3) {
                $tipo_procedimiento = "VVP";
            } else {
                $tipo_procedimiento = "CUP";
            }

            $inicioProcedimiento = Carbon::parse($p->fecha_procedimiento);
            $duracionProcedimiento = $inicioProcedimiento->diffInDays($fechaHoy);

            $resultado[] = [
                "<b>" . $tipo_procedimiento . " </b> <br> Agregado el: " . $fecha_creacion,
                $p->numero,
                $htmlEstado,
                $fecha_procedimiento,
                $duracionProcedimiento,
                $p->nombres . " " . $p->apellido_paterno . " " . $p->apellido_materno,
                $opciones,
            ];
        }
        return response()->json(["aaData" => $resultado]);
    }

    public function eliminarProcedimientoInvasivo(Request $request)
    {
        try {
            DB::beginTransaction();
            $fecha_eliminacion = Carbon::now()->format("Y-m-d H:i:s");
            $eliminado = HojaEnfermeriaProcedimientoInvasivo::where("id", $request->id)->first();
            $eliminado->usuario_modifica = Auth::user()->id;
            $eliminado->fecha_modificacion = $fecha_eliminacion;
            $inicio = Carbon::parse($eliminado->fecha_procedimiento);
            $diasDe = $inicio->diffInDays($fecha_eliminacion);
            $eliminado->tipo_modificacion = 'Eliminado';
            $eliminado->contador_dias = $diasDe;
            $eliminado->visible = false;
            $eliminado->save();
            DB::commit();
            return response()->json(["exito" => "Se ha eliminado el procedimiento invasivo exitosamente"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al eliminar el procedimiento"]);
        }
    }

    public function finalizarProcedimientoInvasivo(Request $request)
    {
        try {
            DB::beginTransaction();

            $fecha_finalizar = Carbon::now()->format("Y-m-d H:i:s");

            $finalizar = HojaEnfermeriaProcedimientoInvasivo::where("id", $request->id)->first();
            $finalizar->usuario_modifica = Auth::user()->id;
            $finalizar->fecha_modificacion = $fecha_finalizar;
            $finalizar->visible = false;
            $finalizar->tipo_modificacion = 'Finalizado';
            $inicio = Carbon::parse($finalizar->fecha_procedimiento);
            $diasDe = $inicio->diffInDays($fecha_finalizar);
            $finalizar->contador_dias = $diasDe;
            $finalizar->save();

            /*registro nuevo*/
            $finalizado = new HojaEnfermeriaProcedimientoInvasivo;
            $finalizado->id_anterior = $finalizar->id;
            $finalizado->caso = $finalizar->caso;
            $finalizado->usuario = Auth::user()->id;
            $finalizado->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $finalizado->visible = true;
            $finalizado->estado = 'Finalizado';
            $finalizado->tipo_procedimiento = $finalizar->tipo_procedimiento;
            $finalizado->numero = $finalizar->numero;
            $finalizado->fecha_procedimiento = $finalizar->fecha_procedimiento;
            $finalizado->save();

            DB::commit();

            return response()->json(["exito" => "Se ha finalizado el antibiótico exitosamente"]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Error al finalizar el antibiótico"]);
        }
    }

 


}
