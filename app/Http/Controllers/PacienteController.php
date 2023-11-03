<?php
namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Comuna;
use App\Models\Consultas;
use App\Models\Establecimiento;
use App\Models\InformeEgreso;
use App\Models\IntervencionQuirurgica;
use App\Models\Paciente;
use App\Models\Pais;
use App\Models\Prevision;
use App\Models\RecienNacido;
use App\Models\TrasladoEstadistica;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use PDF;
//use Vsmoraes\Pdf\Pdf;
use View;
use App\Models\Telefono;

class PacienteController extends Controller
{

    private $pdfEgreso; //este es para cuando se muestra el pdf desde la web

    public function __construct(Pdf $pdfEgreso)
    {
        $this->pdfEgreso = $pdfEgreso;
    }

    public function fichaEgresoPDF(Request $request)
    {

        $caso_paciente = Caso::find($request->id_caso);

        $paciente = Paciente::find($caso_paciente->paciente);
        $caso = DB::table("casos as c")
            ->select('c.id as caso_id', 'c.establecimiento', 'c.ficha_clinica', 'c.prevision', 'c.modalidad_fonasa', 'c.leyes_previsionales', 'c.ley', 'c.procedencia', 'c.fecha_termino', 'c.condicion_egreso', 'c.motivo_termino', 'c.id_medico_alta')
            ->join("historial_ocupaciones as h", "h.caso", "=", "c.id")
        /* ->where("c.paciente", $paciente->id) */
            ->where("c.id", $request->id_caso)
            ->orderby("c.id", "desc")
        //->whereNull("h.fecha_liberacion")
            ->first();
        /* return response()->json($paciente); */
        $establecimiento = Establecimiento::find($caso->establecimiento);
        $informeEgreso = InformeEgreso::where("id_caso", $caso->caso_id)->first();

        //calcular identificacion del paciente
        $tipo_tmp = null;
        if ($paciente->identificacion != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_identificacion')
                ->where('e.enumlabel', $paciente->identificacion)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $paciente->identificacion = $tipo_tmp;
        $num_identificacion = $paciente->n_identificacion;
        //fecha de nacimiento

        $edad = "";
        $unidad_medida = 0;
        $fecha_nacimiento = true;

        if (strtotime($paciente->fecha_nacimiento)) {

            $paciente->fecha_nacimiento = date("d-m-Y", strtotime($paciente->fecha_nacimiento));

            $unidad_medida = 1;
            if (!is_null($paciente->fecha_nacimiento)) {
                $fecha = \Carbon\Carbon::parse($paciente->fecha_nacimiento);
                $edad = \Carbon\Carbon::now()->diffInYears($fecha);

                if ($edad == 0) {
                    $edad = \Carbon\Carbon::now()->diffInMonths($fecha);
                    $unidad_medida = 2;

                    if ($edad == 0) {
                        $edad = \Carbon\Carbon::now()->diffInDays($fecha);
                        $unidad_medida = 3;

                        if ($edad == 0) {
                            $edad = \Carbon\Carbon::now()->diffInHours($fecha);
                            $unidad_medida = 4;
                        }
                    }

                }
            }
            //$algo = $fecha->format("d-m-Y");
            $fn_dia = $fecha->format("d");
            $fn_mes = $fecha->format("m");
            $fn_year = $fecha->format("Y");

        } else {
            $fecha_nacimiento = false;
        }

        //calcular pueblo indigena
        $tipo_tmp = null;
        if ($paciente->pueblo_indigena != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_pueblo_indigena')
                ->where('e.enumlabel', $paciente->pueblo_indigena)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $paciente->pueblo_indigena = $tipo_tmp;
        
        //pais origen
        $pais = Pais::where("id_pais", $paciente->id_pais)->first();

        //calcular categoria ocupacional
        $tipo_tmp = null;
        if ($paciente->categoria_ocupacional != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_categoria_ocupacional')
                ->where('e.enumlabel', $paciente->categoria_ocupacional)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $paciente->categoria_ocupacional = $tipo_tmp;

        //calcular nivel de intruccion
        $tipo_tmp = null;
        if ($paciente->nivel_instruccion != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_nivel_instruccion')
                ->where('e.enumlabel', $paciente->nivel_instruccion)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $paciente->nivel_instruccion = $tipo_tmp;

        //calcular tipo de direccion
        $tipo_tmp = null;
        if ($paciente->tipo_direccion != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_direccion')
                ->where('e.enumlabel', $paciente->tipo_direccion)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $paciente->tipo_direccion = $tipo_tmp;

        //comuna del paciente
        $comuna = Comuna::where("id_comuna", $paciente->id_comuna)->first();

        //prevision
        $prevision_18 = 0;
        $beneficiario_19 = "";

        if ($caso->prevision == "FONASA A") {
            $prevision_18 = 1;
            $beneficiario_19 = "A";
        } else if ($caso->prevision == "FONASA B") {
            $prevision_18 = 1;
            $beneficiario_19 = "B";
        } else if ($caso->prevision == "FONASA C") {
            $prevision_18 = 1;
            $beneficiario_19 = "C";
        } else if ($caso->prevision == "FONASA D") {
            $prevision_18 = 1;
            $beneficiario_19 = "C";
        } else if ($caso->prevision == "ISAPRE") {
            $prevision_18 = 2;
        } else if ($caso->prevision == "CAPREDENA") {
            $prevision_18 = 3;
        } else if ($caso->prevision == "DIPRECA") {
            $prevision_18 = 4;
        } else if ($caso->prevision == "SISA") {
            $prevision_18 = 5;
        } else {
            $prevision_18 = 99;
        }

        //calcular mai y mle
        $tipo_tmp = null;
        if ($caso->modalidad_fonasa != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_modalidad_fonasa')
                ->where('e.enumlabel', $caso->modalidad_fonasa)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $caso->modalidad_fonasa = $tipo_tmp;

        //leyes previsionales
        if ($caso->leyes_previsionales == true) {
            $caso->leyes_previsionales = 'true';
        } else {
            $caso->leyes_previsionales = 'false';
        }

        //calcular tipo de ley
        $tipo_tmp = null;
        if ($caso->ley != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_ley')
                ->where('e.enumlabel', $caso->ley)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $caso->ley = $tipo_tmp;

        //procedencia
        $procedencia_22 = 0;
        if ($caso->procedencia == 1) {
            $procedencia_22 = 1;
        } else if ($caso->procedencia == 2) {
            $procedencia_22 = 3;
        } else if ($caso->procedencia == 3) {
            $procedencia_22 = 4;
        }

        //datos hospitalizacion

        $historial_ocupaciones = DB::table("historial_ocupaciones")
            ->where("caso", $caso->caso_id)
            ->orderBy("fecha", "asc")
            ->whereNotNull("fecha_ingreso_real")
            ->get();

        //return response()->json($historial_ocupaciones);

        $fecha_hosp_dia = [];
        $fecha_hosp_mes = [];
        $fecha_hosp_year = [];
        $unidad_f = [];
        $cod_area = [];
        $servicio_c = [];

        //traslados sobre los 5
        $fecha_hosp_dia_extras = [];
        $fecha_hosp_mes_extras = [];
        $fecha_hosp_year_extras = [];
        $unidad_f_extras = [];
        $cod_area_extras = [];
        $servicio_c_extras = [];

        $p_egresado = 1; //paciente egresado

        $cont_egreso = $historial_ocupaciones->count() - 1;

        //egreso del paciente
        if (is_null($caso->fecha_termino)) {
            $p_egresado = 2;
            #hr egreso
            $hr_egreso = Carbon::now()->format("H");
            if (intval($hr_egreso) == 0) {
                $hr_egreso = '00';
            } else if (intval($hr_egreso) < 10) {
                $hr_egreso = '0' . intval($hr_egreso);
            } else {
                $hr_egreso = intval($hr_egreso);
            }

            #min egreso
            $min_egreso = Carbon::now()->format("i");
            if (intval($min_egreso) == 0) {
                $min_egreso = '00';
            } else if (intval($min_egreso) < 10) {
                $min_egreso = '0' . intval($min_egreso);
            } else {
                $min_egreso = intval($min_egreso);
            }

            #fecha egreso
            $fecha_egreso_dia = Carbon::now()->format("d");
            if (intval($fecha_egreso_dia) == 0) {
                $fecha_egreso_dia = '00';
            } else if (intval($fecha_egreso_dia) < 10) {
                $fecha_egreso_dia = '0' . intval($fecha_egreso_dia);
            } else {
                $fecha_egreso_dia = intval($fecha_egreso_dia);
            }
            $fecha_egreso_mes = Carbon::now()->format("m");
            if (intval($fecha_egreso_mes) == 0) {
                $fecha_egreso_mes = '00';
            } else if (intval($fecha_egreso_mes) < 10) {
                $fecha_egreso_mes = '0' . intval($fecha_egreso_mes);
            } else {
                $fecha_egreso_mes = intval($fecha_egreso_mes);
            }
            $fecha_egreso_year = Carbon::now()->format("Y");
            if (intval($fecha_egreso_year) == 0) {
                $fecha_egreso_year = '00';
            } else if (intval($fecha_egreso_year) < 10) {
                $fecha_egreso_year = '0' . intval($fecha_egreso_year);
            } else {
                $fecha_egreso_year = intval($fecha_egreso_year);
            }
        } else {
            $tmp_egreso = Carbon::parse($caso->fecha_termino);

            #hr egreso
            $hr_egreso = $tmp_egreso->format("H");
            if (intval($hr_egreso) == 0) {
                $hr_egreso = '00';
            } else if (intval($hr_egreso) < 10) {
                $hr_egreso = '0' . intval($hr_egreso);
            } else {
                $hr_egreso = intval($hr_egreso);
            }
            #min egreso
            $min_egreso = $tmp_egreso->format("i");
            if (intval($min_egreso) == 0) {
                $min_egreso = '00';
            } else if (intval($min_egreso) < 10) {
                $min_egreso = '0' . intval($min_egreso);
            } else {
                $min_egreso = intval($min_egreso);
            }
            #fecha egreso
            $fecha_egreso_dia = $tmp_egreso->format("d");
            if (intval($fecha_egreso_dia) == 0) {
                $fecha_egreso_dia = '00';
            } else if (intval($fecha_egreso_dia) < 10) {
                $fecha_egreso_dia = '0' . intval($fecha_egreso_dia);
            } else {
                $fecha_egreso_dia = intval($fecha_egreso_dia);
            }
            $fecha_egreso_mes = $tmp_egreso->format("m");
            if (intval($fecha_egreso_mes) == 0) {
                $fecha_egreso_mes = '00';
            } else if (intval($fecha_egreso_mes) < 10) {
                $fecha_egreso_mes = '0' . intval($fecha_egreso_mes);
            } else {
                $fecha_egreso_mes = intval($fecha_egreso_mes);
            }
            $fecha_egreso_year = $tmp_egreso->format("Y");
            if (intval($fecha_egreso_year) == 0) {
                $fecha_egreso_year = '00';
            } else if (intval($fecha_egreso_year) < 10) {
                $fecha_egreso_year = '0' . intval($fecha_egreso_year);
            } else {
                $fecha_egreso_year = intval($fecha_egreso_year);
            }
        }

        //ocupaciones
        $estada = 0;
        if($historial_ocupaciones != "[]"){
        foreach ($historial_ocupaciones as $key => $ocupacion) {
            $tmp_fecha = Carbon::parse($ocupacion->fecha);

            $tmp_area = DB::table("camas as c")
                ->select("a.nombre", "a.codigo as area_codigo", "u.codigo as unidad_codigo")
                ->join("salas as s", "s.id", "c.sala")
                ->join("unidades_en_establecimientos as u", "u.id", "s.establecimiento")
                ->join("area_funcional as a", "a.id_area_funcional", "u.id_area_funcional")
                ->where("c.id", $ocupacion->cama)
                ->first();

            //return response()->json($tmp_area);
            $tmp_hr = $tmp_fecha->format("H");
            $tmp_min = $tmp_fecha->format("i");
            $tmp_fecha_dia = $tmp_fecha->format("d");
            $tmp_fecha_mes = $tmp_fecha->format("m");
            $tmp_fecha_year = $tmp_fecha->format("Y");
            $tmp_fecha = $tmp_fecha->format("d-m-Y");

            if (0 == $key) {

                #calcular días de estada
                $tmp_1 = Carbon::parse($tmp_fecha);
                if ($caso->fecha_termino) {
                    $tmp_2 = Carbon::parse($caso->fecha_termino);
                    /* $tmp_2 = $tmp_2->format("d-m-Y"); */
                } else {
                    $tmp_2 = Carbon::now();
                }
                $estada = $tmp_1->diffInDays($tmp_2);
                #condicion del egreso

                #destino de alta

            }

            //return $tmp_hr;
            if ($key == 0) {
                # Hora y minutos
                if (intval($tmp_hr) == 0) {
                    $hr = '00';
                } else if (intval($tmp_hr) < 10) {
                    $hr = '0' . intval($tmp_hr);
                } else {
                    $hr = intval($tmp_hr);
                }

                if (intval($tmp_min) == 0) {
                    $min = '00';
                } else if (intval($tmp_min) < 10) {
                    $min = '0' . intval($tmp_min);
                } else {
                    $min = intval($tmp_min);
                }

                if (intval($tmp_fecha_dia) == 0) {
                    $fecha_hosp_dia[] = '00';
                } else if (intval($tmp_fecha_dia) < 10) {
                    $fecha_hosp_dia[] = '0' . intval($tmp_fecha_dia);
                } else {
                    $fecha_hosp_dia[] = intval($tmp_fecha_dia);
                }

                if (intval($tmp_fecha_mes) == 0) {
                    $fecha_hosp_mes[] = '00';
                } else if (intval($tmp_fecha_mes) < 10) {
                    $fecha_hosp_mes[] = '0' . intval($tmp_fecha_mes);
                } else {
                    $fecha_hosp_mes[] = intval($tmp_fecha_mes);
                }

                if (intval($tmp_fecha_year) == 0) {
                    $fecha_hosp_year[] = '00';
                } else if (intval($tmp_fecha_year) < 10) {
                    $fecha_hosp_year[] = '0' . intval($tmp_fecha_year);
                } else {
                    $fecha_hosp_year[] = intval($tmp_fecha_year);
                }

                #unidad funcional
                $unidad_f[] = $tmp_area->nombre;
                #codigo area funcional
                $cod_area[] = $tmp_area->area_codigo;
                #cdigo servicio clinico
                $servicio_c[] = $tmp_area->unidad_codigo;
            } else if ($key >= 1 && $key < 5) {

                #fecha dd-mm-YYYY
                $fecha_hosp_dia[] = $tmp_fecha_dia;
                $fecha_hosp_mes[] = $tmp_fecha_mes;
                $fecha_hosp_year[] = $tmp_fecha_year;
                #unidad funcional
                $unidad_f[] = $tmp_area->nombre;
                #codigo area funcional
                $cod_area[] = $tmp_area->area_codigo;
                #cdigo servicio clinico
                $servicio_c[] = $tmp_area->unidad_codigo;

            } else if ($key >= 5) {
                #fecha dd-mm-YYYY
                $fecha_hosp_dia_extras[] = $tmp_fecha_dia;
                $fecha_hosp_mes_extras[] = $tmp_fecha_mes;
                $fecha_hosp_year_extras[] = $tmp_fecha_year;
                #unidad funcional
                $unidad_f_extras[] = $tmp_area->nombre;
                #codigo area funcional
                $cod_area_extras[] = $tmp_area->area_codigo;
                #cdigo servicio clinico
                $servicio_c_extras[] = $tmp_area->unidad_codigo;
            }
        }
    }else{
        $hr = '';
        $min = '';
        $fecha_hosp[] = '';
        $unidad_f[] = '';
        $cod_area[] = '';
        $servicio_c[] = '';
    }

        //calcular destino de alta
        $tipo_tmp = null;
        if ($caso->motivo_termino != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'motivos_liberacion')
                ->where('e.enumlabel', '<>', "corrección cama")
                ->where('e.enumlabel', $caso->motivo_termino)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $caso->motivo_termino = $tipo_tmp;

        //diagnostico del paciente
        $diagnosticos = DB::table("diagnosticos")
            ->where("caso", $caso->caso_id)
            ->whereNotNull("id_cie_10")
            ->orderBy("fecha", "desc")
            ->get();

        $diagnostico_principal = null;
        $otro_diagnostico = [];
        $otro_diagnostico_extra = [];
        foreach ($diagnosticos as $key => $diagnostico) {
            if ($key == 0) {
                $diagnostico_principal = $diagnostico;
            } else if ($key <= 2 && $key > 0) {
                $otro_diagnostico[] = $diagnostico;
            } else {
                $otro_diagnostico_extra[] = $diagnostico;
            }
        }

        //causa externa
        $causa_externa = DB::table("diagnosticos")
            ->select("causa_externa")
            ->where("caso", $caso->caso_id)
            ->first();

        $causa_externa = $causa_externa->causa_externa;

        //recien nacidos
        $rns = RecienNacido::where("id_caso", $caso->caso_id)->get();

        $recien_nacido_tabla = [];
        $recien_nacido_tabla_extras = [];
        foreach ($rns as $key => $rn) {
            if ($rn->anomalia_congenita == true) {
                $rn->anomalia_congenita = 'Si';
            } else {
                $rn->anomalia_congenita = 'No';
            }

            if ($key < 3) {
                $recien_nacido_tabla[] = $rn;
            } else {
                $recien_nacido_tabla_extras[] = $rn;
            }

        }
        //return response()->json($recien_nacido_tabla);

        //Intervencion quirurgica
        $iq = IntervencionQuirurgica::where("id_caso", $caso->caso_id)->first();
        if ($iq) {
            $iq->intervencion_quirurgica = ($iq->intervencion_quirurgica) ? '1' : '2';
            $iq->procedimiento = ($iq->procedimiento) ? '1' : '2';
        }

        //medico alta
        $medico = DB::table("medico")
            ->where("id_medico", $caso->id_medico_alta)
            ->first();

        if (is_null($medico)) {
            $medico = false;
        } else {
            $apellidos_medico = explode(' ', $medico->apellido_medico);
            $medico->apellido_p = $apellidos_medico[0];
            $medico->apellido_m = $apellidos_medico[1];
        }

        $telefonocasa = Telefono::where('id_paciente',$caso_paciente->paciente)->where('tipo','Casa')->orderBy('id','desc')->first();
        $telefonomovil = Telefono::where('id_paciente',$caso_paciente->paciente)->where('tipo','Movil')->orderBy('id','desc')->first();
        //return response()->json($medico);

        //return $informeEgreso;
        // return 'oka';
        $pdf = \Barryvdh\DomPDF\Facade::loadView('Paciente/PdfEgreso', array(
            "paciente" => $paciente,
            "telefonocasa" => $telefonocasa,
            "telefonomovil" => $telefonomovil,
            "num_identificacion" => $num_identificacion,
            "caso" => $caso,
            "establecimiento" => $establecimiento,
            "informe_egreso" => $informeEgreso,
            "fn_dia" => ($fecha_nacimiento) ? $fn_dia : "",
            "fn_mes" => ($fecha_nacimiento) ? $fn_mes : "",
            "fn_year" => ($fecha_nacimiento) ? $fn_year : "",
            "pais" => $pais,
            "edad" => $edad,
            "unidad_medida" => $unidad_medida,
            "comuna_paciente" => $comuna,
            "prevision" => $prevision_18,
            "beneficio" => $beneficiario_19,
            "fecha_hosp_hr" => $hr,
            "fecha_hosp_min" => $min,
            "fecha_hosp_dia" => $fecha_hosp_dia,
            "fecha_hosp_mes" => $fecha_hosp_mes,
            "fecha_hosp_year" => $fecha_hosp_year,
            "fecha_hosp_cantidad" => count($fecha_hosp_year),
            "unidad_funcional" => $unidad_f,
            "codigo_area_funcional" => $cod_area,
            "codigo_servicio_clinico" => $servicio_c,
            "fecha_hosp_dia_extras" => $fecha_hosp_dia_extras,
            "fecha_hosp_mes_extras" => $fecha_hosp_mes_extras,
            "fecha_hosp_year_extras" => $fecha_hosp_year_extras,
            "unidad_f_extras" => $unidad_f_extras,
            "cod_area_extras" => $cod_area_extras,
            "servicio_c_extras" => $servicio_c_extras,
            "fechas_extras" => count($fecha_hosp_year_extras),
            "fecha_egreso_dia" => $fecha_egreso_dia,
            "fecha_egreso_mes" => $fecha_egreso_mes,
            "fecha_egreso_year" => $fecha_egreso_year,
            "hr_egreso" => $hr_egreso,
            "min_egreso" => $min_egreso,
            "estada" => $estada,
            "diagnostico_principal" => $diagnostico_principal,
            "otros_diagnosticos" => $otro_diagnostico,
            "otro_diagnostico_extra" => $otro_diagnostico_extra,
            "causa_externa" => $causa_externa,
            "recien_nacido_tabla" => $recien_nacido_tabla,
            "recien_nacido_tabla_extras" => $recien_nacido_tabla_extras,
            "intervencion_quirurgica" => $iq,
            "medico" => $medico,
        ));
        return $pdf->setPaper('a4', 'portrait')->download('fichaEgreso.pdf');
        //return $this->pdfEgreso->load("hola", "letter", "landscape")->download();
        //return $this->pdfEgreso->load($html, "legal", "portrait")->download();
    }

    public function calcularEdad(Request $request)
    {

        $fecha_nac = date("d-m-Y", strtotime($request->edad));
        $fecha = \Carbon\Carbon::parse($fecha_nac);
        $actual = \Carbon\Carbon::now();

        if ($fecha <= $actual) {
            $unidad_medida = 1;

            $edad = \Carbon\Carbon::now()->diffInYears($fecha);

            if ($edad == 0) {
                $edad = \Carbon\Carbon::now()->diffInMonths($fecha);
                $unidad_medida = 2;

                if ($edad == 0) {
                    $edad = \Carbon\Carbon::now()->diffInDays($fecha);
                    $unidad_medida = 3;

                    if ($edad == 0) {
                        $edad = \Carbon\Carbon::now()->diffInHours($fecha);
                        $unidad_medida = 4;
                    }
                }

            }

            return response()->json(["exito" => true, "edad" => $edad, "unidad_medida" => $unidad_medida]);
        } else {
            return response()->json(["exito" => false, "edad" => "", "unidad_medida" => 0]);
        }

    }

    public function generarEgreso($id)
    {

        //return response()->json(["fecha carbon" => \Carbon\Carbon::now(), "fecha date" => date("d-m-Y H:i:s")]);
        /////////////////////////
        //$id es el id del caso//
        /////////////////////////
        $idCaso = $id;
        $ie = InformeEgreso::where("id_caso", $idCaso)->first();

        $caso = DB::table("casos as c")
            ->join("historial_ocupaciones as h", "h.caso", "=", "c.id")
            ->where("c.id", $idCaso)
        /* ->whereNull("h.fecha_liberacion") */
            ->first(); 

        $paciente = Paciente::find($caso->paciente);
        $telefonocasa = Telefono::where('id_paciente',$caso->paciente)->where('tipo','Casa')->orderBy('id','desc')->first();
        $telefonomovil = Telefono::where('id_paciente',$caso->paciente)->where('tipo','Movil')->orderBy('id','desc')->first();
        //$paciente->fecha_nacimiento = "";
        $edad = "";
        $unidad_medida = 0;

        if (strtotime($paciente->fecha_nacimiento)) {

            $paciente->fecha_nacimiento = date("d-m-Y", strtotime($paciente->fecha_nacimiento));

            $unidad_medida = 1;
            if (!is_null($paciente->fecha_nacimiento)) {
                $fecha = \Carbon\Carbon::parse($paciente->fecha_nacimiento);
                $edad = \Carbon\Carbon::now()->diffInYears($fecha);

                if ($edad == 0) {
                    $edad = \Carbon\Carbon::now()->diffInMonths($fecha);
                    $unidad_medida = 2;

                    if ($edad == 0) {
                        $edad = \Carbon\Carbon::now()->diffInDays($fecha);
                        $unidad_medida = 3;

                        if ($edad == 0) {
                            $edad = \Carbon\Carbon::now()->diffInHours($fecha);
                            $unidad_medida = 4;
                        }
                    }

                }
            }
            //$algo = $fecha->format("d-m-Y");
            $fecha_nacimiento = $fecha->toDateTimeString();
        } else {
            $fecha_nacimiento = false;
        }

        //return response()->json($paciente->fecha_nacimiento);

        $extranjero = $paciente->extranjero;
        $tipo_identificacion = $paciente->identificacion;
        $num_identificacion = ($paciente->n_identificacion) ? $paciente->n_identificacion : null;

        // $num_pasaporte = $paciente->n_identificacion

        $paciente->dv = $paciente->dv == 10 ? 'K' : $paciente->dv;

        $medico = DB::table("medico")
            ->where("id_medico", $caso->id_medico_alta)
            ->first();

        if (is_null($medico)) {
            $medico = false;
        } else {
            $apellidos_medico = explode(' ', $medico->apellido_medico);
            $medico->apellido_p = $apellidos_medico[0];
            $medico->apellido_m = $apellidos_medico[1];
        }

        //diagnostico del paciente
        $diagnosticos = DB::table("diagnosticos")
            ->where("caso", $idCaso)
            ->whereNotNull("id_cie_10")
            ->orderBy("fecha", "desc")
            ->get();

        $diagnostico_principal = null;
        $otro_diagnostico = [];
        foreach ($diagnosticos as $key => $diagnostico) {
            if ($key == 0) {
                $diagnostico_principal = $diagnostico;
            } else {
                $otro_diagnostico[] = $diagnostico;
            }
        }

        //return response()->json($diagnostico_principal);

        $historial_ocupaciones = DB::table("historial_ocupaciones")
            ->where("caso", $idCaso)
            ->orderBy("fecha", "asc")
            ->whereNotNull("fecha_ingreso_real")
            ->get();

        ////////////////////////////////////////////////////////
        //Buscar habitacion en la que se encuentra el paciente//
        ////////////////////////////////////////////////////////

        $datos_ocupacion = DB::table("t_historial_ocupaciones as h")
            ->select("u.url", "s.id as idSala", "c.id as idCama")
            ->leftjoin("camas as c", "c.id", "=", "h.cama")
            ->leftjoin("salas as s", "s.id", "=", "c.sala")
            ->leftjoin("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
            ->where("h.caso", $idCaso)
            ->whereNull("h.fecha_liberacion")
            ->first();

        $cama = "";
        $sala = "";
        $url = "";
        if (isset($datos_ocupacion)) {
            $cama = $datos_ocupacion->idCama;
            $sala = $datos_ocupacion->idSala;
            $url = $datos_ocupacion->url;
        } else {
            $url = "error";
        }

        $fecha_hosp = [];
        $unidad_f = [];
        $cod_area = [];
        $servicio_c = [];
        $p_egresado = 1; //paciente egresado

        $cont_egreso = $historial_ocupaciones->count() - 1;

        if (is_null($caso->fecha_termino)) {
            $p_egresado = 2;
            #hr egreso
            $hr_egreso = Carbon::now()->format("H");
            #min egreso
            $min_egreso = Carbon::now()->format("i");
            #fecha egreso
            $fecha_egreso = Carbon::now()->format("d-m-Y");
        } else {
            $tmp_egreso = Carbon::parse($caso->fecha_termino);

            #hr egreso
            $hr_egreso = $tmp_egreso->format("H");
            #min egreso
            $min_egreso = $tmp_egreso->format("i");
            #fecha egreso
            $fecha_egreso = $tmp_egreso->format("d-m-Y");
        }
        $estada = 0;
        /* return response()->json($caso); */

        //traslados

        //traslados
        if($historial_ocupaciones != "[]"){
            foreach ($historial_ocupaciones as $key => $ocupacion) {
                $tmp_fecha = Carbon::parse($ocupacion->fecha);

                $tmp_area = DB::table("camas as c")
                    ->select("c.id","a.nombre", "a.codigo as area_codigo", "u.codigo as unidad_codigo")
                    ->join("salas as s", "s.id", "c.sala")
                    ->join("unidades_en_establecimientos as u", "u.id", "s.establecimiento")
                    ->join("area_funcional as a", "a.id_area_funcional", "u.id_area_funcional")
                    ->where("c.id", $ocupacion->cama)
                    ->first();

                $tmp_hr = $tmp_fecha->format("H");
                $tmp_min = $tmp_fecha->format("i");
                $tmp_fecha = $tmp_fecha->format("d-m-Y");

                if (0 == $key) {

                    #calcular días de estada
                    $tmp_1 = Carbon::parse($tmp_fecha);
                    if ($caso->fecha_termino) {
                        $tmp_2 = Carbon::parse($caso->fecha_termino);
                    } else {
                        $tmp_2 = Carbon::now();
                    }

                    $estada = $tmp_1->diffInDays($tmp_2);
                    #condicion del egreso

                    #destino de alta

                }

                if ($key == 0) {
                    # Hora y minutos
                    $hr = intval($tmp_hr);
                    $min = $tmp_min;
                    #fecha dd-mm-YYYY
                    $fecha_hosp[] = $tmp_fecha;
                    #unidad funcional
                    $unidad_f[] = $tmp_area->nombre;
                    #codigo area funcional
                    $cod_area[] = $tmp_area->area_codigo;
                    #cdigo servicio clinico
                    $servicio_c[] = $tmp_area->unidad_codigo;
                } else if ($key >= 1) {

                    #fecha dd-mm-YYYY
                    $fecha_hosp[] = $tmp_fecha;
                    #unidad funcional
                    $unidad_f[] = $tmp_area->nombre;
                    #codigo area funcional
                    $cod_area[] = $tmp_area->area_codigo;
                    #cdigo servicio clinico
                    $servicio_c[] = $tmp_area->unidad_codigo;
                } else {
                    #caso especial
                }
            }
        }else{
            $hr = '';
            $min = '';
            $fecha_hosp[] = '';
            $unidad_f[] = '';
            $cod_area[] = '';
            $servicio_c[] = '';
        }


        $establecimiento = Establecimiento::find($caso->establecimiento);

        if (!is_null($paciente->rut)) {
            $tipo_identificacion = 1;
        } else {
            $tipo_identificacion = 2;
        }

        if ($paciente->sexo == "masculino") {
            $sexo = 1;
        } else if ($paciente->sexo == "femenino") {
            $sexo = 2;
        } else {
            $sexo = 4;
        }

        $prevision_18 = 0;
        $beneficiario_19 = 0;

        if ($caso->prevision == "FONASA A") {
            $prevision_18 = 1;
            $beneficiario_19 = 1;
        } else if ($caso->prevision == "FONASA B") {
            $prevision_18 = 1;
            $beneficiario_19 = 2;
        } else if ($caso->prevision == "FONASA C") {
            $prevision_18 = 1;
            $beneficiario_19 = 3;
        } else if ($caso->prevision == "FONASA D") {
            $prevision_18 = 1;
            $beneficiario_19 = 4;
        } else if ($caso->prevision == "ISAPRE") {
            $prevision_18 = 2;
        } else if ($caso->prevision == "CAPREDENA") {
            $prevision_18 = 3;
        } else if ($caso->prevision == "DIPRECA") {
            $prevision_18 = 4;
        } else if ($caso->prevision == "SISA") {
            $prevision_18 = 5;
        } else {
            $prevision_18 = 99;
        }

        //sacar tipo de calle
        $tipo_tmp = null;
        if ($paciente->tipo_direccion != null) {

            $tip = DB::table("pg_type as t")
                ->select('e.enumsortorder as nombre')
                ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
                ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace')
                ->where('t.typname', 'tipo_direccion')
                ->where('e.enumlabel', $paciente->tipo_direccion)
                ->get();
            $tipo_tmp = $tip[0]->nombre;
        }
        $paciente->tipo_direccion = $tipo_tmp;

        $procedencia_22 = 0;
        if ($caso->procedencia == 1) {
            $procedencia_22 = 1;
        } else if ($caso->procedencia == 2) {
            $procedencia_22 = 3;
        } else if ($caso->procedencia == 3) {
            $procedencia_22 = 4;
        }

        //leyes provisionales
        if (is_null($caso->leyes_previsionales)) {
            $caso->leyes_previsionales = 'false';
        } else {
            if ($caso->leyes_previsionales == true) {
                $caso->leyes_previsionales = 'true';
            } else {
                $caso->leyes_previsionales = 'false';
            }
        }

        //sacar ley

        /* $tipo_tmp = null;
        if($caso->ley != null){

        $tip = DB::table("pg_type as t")
        ->select('e.enumsortorder as nombre')
        ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
        ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace' )
        ->where('t.typname', 'tipo_ley')
        ->where('e.enumlabel', $caso->ley)
        ->get();
        $tipo_tmp = $tip[0]->nombre;
        }
        $caso->ley =$tipo_tmp; */

        //sacar fallecimiento
        /* $tipo_tmp = null;
        if($caso->motivo_termino != null){

        $tip = DB::table("pg_type as t")
        ->select('e.enumsortorder as nombre')
        ->join('pg_enum as e', 'e.enumtypid', '=', 't.oid')
        ->join('pg_catalog.pg_namespace as n', 'n.oid', '=', 't.typnamespace' )
        ->where('t.typname', 'motivos_liberacion')
        ->where('e.enumlabel', $caso->motivo_termino)
        ->get();
        $tipo_tmp = $tip[0]->nombre;
        }
        $caso->motivo_termino =$tipo_tmp; */

        $causa_externa = DB::table("diagnosticos")
            ->select("causa_externa")
            ->where("caso", $caso->id)
            ->first();

        //paises
        $paises = Pais::pluck('nombre_pais', 'id_pais');

        //recien nacidos
        $rn = RecienNacido::where("id_caso", $caso->caso)->get();

        //Intervencion quirurgica
        $iq = IntervencionQuirurgica::where("id_caso", $caso->caso)->first();
        if ($iq) {
            $iq->intervencion_quirurgica = ($iq->intervencion_quirurgica) ? 'true' : 'false';
            $iq->procedimiento = ($iq->procedimiento) ? 'true' : 'false';
        }

        return View::make("Paciente/Egreso", [
            //"probando" => $probando,
            "paciente" => $paciente,
            "telefonocasa" =>$telefonocasa,
            "telefonomovil" =>$telefonomovil,
            "prevision" => Prevision::getPrevisiones(),
            "comunas" => Comuna::getComunas(),
            'regiones' => Consultas::getRegion(),
            "region" => Consultas::getRegionPaciente($paciente->id_comuna),
            'id_caso' => $idCaso,
            'caso' => $caso,
            "establecimiento" => $establecimiento,
            // "tipo_ident" => $tipo_identificacion,
            "extranjero" => $extranjero,
            "tipo_identificacion" => $tipo_identificacion,
            "num_identificacion" => $num_identificacion,
            "sexo" => $sexo,
            "edad" => $edad,
            "unidad_medida" => $unidad_medida,
            "prevision_18" => $prevision_18,
            "beneficiario_19" => $beneficiario_19,
            "procedencia_22" => $procedencia_22,
            "fecha_nacimiento" => $fecha_nacimiento,
            "hr" => $hr,
            "min" => $min,
            "fecha_hosp" => $fecha_hosp,
            "unidad_f" => $unidad_f,
            "cod_area" => $cod_area,
            "servicio_c" => $servicio_c,
            "medico" => $medico,
            "hr_egreso" => $hr_egreso,
            "min_egreso" => $min_egreso,
            "fecha_egreso" => $fecha_egreso,
            "estada" => $estada,
            "diagnostico_principal" => $diagnostico_principal,
            "p_egresado" => $p_egresado,
            "causa_externa" => $causa_externa,
            "otro_diagnostico" => $otro_diagnostico,
            "paises" => $paises,
            "recien_nacidos" => $rn,
            "intervencion_quirurgica" => $iq,
            "informe_egreso" => $ie,
            "url" => $url,
            "sala" => $sala,
            "cama" => $cama,
        ]);
    }

    public function fichaEgresoPaciente(Request $request)
    {
        //INFORME EGRESO
        //buscar si se tiene una ficha de egreso creada.
        $informe_egreso = InformeEgreso::where("id_caso", $request->id_caso)->first();

        try {
            if (is_null($informe_egreso)) {

                $nuevo_egreso = new InformeEgreso;
                $nuevo_egreso->id_caso = $request->id_caso;
                $nuevo_egreso->id_paciente = $request->id_paciente;
                $nuevo_egreso->n_egreso = $request->num_egreso;
                $nuevo_egreso->n_admision = $request->n_admision;
                $nuevo_egreso->cod_fun_egreso = $request->cod_fun_egreso;
                $nuevo_egreso->cod_ser_egreso = $request->cod_ser_egreso;
                $nuevo_egreso->save();

            } else {
                $informe_egreso->n_egreso = $request->num_egreso;
                $informe_egreso->n_admision = $request->n_admision;
                $informe_egreso->cod_fun_egreso = $request->cod_fun_egreso;
                $informe_egreso->cod_ser_egreso = $request->cod_ser_egreso;
                $informe_egreso->save();
            }
        } catch (Exception $e) {
            // if an exception happened in the try block above
            return response()->json(["error" => $e]);
        }

        //PACIENTES
        //salvar el prueblo indigena

        try {
            $paciente = Paciente::find($request->id_paciente);
            $paciente->pueblo_indigena = $request->pueblo_ind;

            if ($request->pueblo_ind == 'Otro') {
                $paciente->detalle_pueblo_indigena = $request->esp_pueblo;
            }

            //pais de origen
            $paciente->id_pais = $request->nombre_pais;

            //categoria ocupacional

            $paciente->categoria_ocupacional = $request->cat_ocup;

            if ($request->cat_ocup == "activos") {
                $paciente->categoria_activo = $request->cat_ocup_activo;
            } else {
                $paciente->categoria_activo = 'sin información';
            }
            //nivel de instrccuion
            $paciente->nivel_instruccion = $request->educacion;
            //telefono movil
            $paciente->telefono_movil = $request->tel_movil;
            $paciente->save();

            if(empty($request->id_movil)){
              $idnuevo_fonomovil = Telefono::select('id')->orderBy('id', 'desc')->first();
              $nuevo_fonomovil = new Telefono;
              $nuevo_fonomovil->id = $idnuevo_fonomovil->id +1;
              $nuevo_fonomovil->id_paciente = $request->id_paciente;
              $nuevo_fonomovil->tipo = 'Movil';
              $nuevo_fonomovil->telefono = $request->tel_movil;
              $nuevo_fonomovil->save();
            }else{
              $telefonomovil = Telefono::find($request->id_movil);
              $telefonomovil->telefono = $request->tel_movil;
              $telefonomovil->save();
            }
        } catch (Exception $e) {
            // if an exception happened in the try block above
            return response()->json(["error" => $e]);
        }

        //CASOS
        //modalidad fonasa
        $caso = Caso::find($request->id_caso);

        if ($request->mod_aten != "null") {
            $caso->modalidad_fonasa = $request->mod_aten;
        } else {
            $caso->modalidad_fonasa = "sin información";
        }

        $caso->leyes_previsionales = $request->ley_previsional_opc;
        if ($request->ley_previsional_opc == 'true') {
            $caso->ley = $request->ley_previsional;
        } else {
            $caso->ley = 'sin información';
        }

        $caso->motivo_termino = $request->destino_alta;

        $caso->condicion_egreso = $request->cond_egreso;

        $caso->id_medico_alta = $request->id_medico;
        $caso->save();

        //RECIEN NACIDOS
        try {
            $rn = RecienNacido::select("id_datos_recien_nacido as id")->where("id_caso", $request->id_caso)->get();
            if (count($rn) > 0) {
                foreach ($rn as $id_rn) {
                    RecienNacido::destroy($id_rn->id);
                }
            }
            if (isset($request->cond_nacer) != false) {
                if (count($request->cond_nacer) > 0) {
                    foreach ($request->cond_nacer as $key => $cond_nacer) {
                        $rn = new RecienNacido;
                        $rn->id_caso = $request->id_caso;
                        $rn->orden_nacimiento = $key;
                        $rn->condicion = $cond_nacer;
                        $rn->sexo = $request->sexo[$key];
                        $rn->peso_gramos = $request->peso[$key] == "" ? null : $request->peso[$key];
                        $rn->apgar = $request->apgar[$key] == "" ? null : $request->apgar[$key];
                        $rn->anomalia_congenita = $request->anomalia[$key];
                        $rn->save();
                    }
                }
            }

        } catch (Exception $e) {
            // if an exception happened in the try block above
            return response()->json(["error" => $e]);
        }

        //traslados extras
         try {
             if(isset($request->fechaTraslado) != ""){
                 foreach ($request->fechaTraslado as $key => $fechaTraslado) {
                     $trasladosExtra = new TrasladoEstadistica;
                     $trasladosExtra->ficha_egreso = $informe_egreso->id_informe_egreso;
                     $trasladosExtra->fecha = $request->fechaTraslado[$key];
                     $trasladosExtra->area_funcional = $request->areaFuncional[$key] == "" ? null : $request->areaFuncional[$key];
                     $trasladosExtra->cod_unidad = $request->codigoUnidad[$key] == "" ? null : $request->codigoUnidad[$key];
                     $trasladosExtra->cod_servicio = $request->servicioClinico[$key] == "" ? null : $request->servicioClinico[$key];

                     $trasladosExtra->save();
                 }
             }

        } catch (Exception $e) {
            // if an exception happened in the try block above
            return response()->json(["error" => $e]);
        }
        //traslados extras

        //INTERVENCION QUIRURGICA
        try {
            $iq = IntervencionQuirurgica::select("id_intervencion_quirurgica as id")->where("id_caso", $request->id_caso)->get();
            if (count($iq) > 0) {
                foreach ($iq as $id_iq) {
                    IntervencionQuirurgica::destroy($id_iq->id);
                }
            }

            $iq = new IntervencionQuirurgica;
            $iq->id_caso = $request->id_caso;
            $iq->intervencion_quirurgica = $request->int_qu == "" ? null : $request->int_qu;
            $iq->intervencion_quirurgica_principal = $request->int_qu_pr == "" ? null : $request->int_qu_pr;
            $iq->codigo_intervencion_quirurgica_principal = $request->codigo_fo1 == "" ? null : $request->codigo_fo1;
            $iq->otra_intervencion_quirurgica = $request->ot_int_qu == "" ? null : $request->ot_int_qu;
            $iq->codigo_otra_intervencion_quirurgica = $request->codigo_fo2 == "" ? null : $request->codigo_fo2;
            $iq->procedimiento = $request->proc == "" ? null : $request->proc;
            $iq->procedimiento_principal = $request->proc_principal == "" ? null : $request->proc_principal;
            $iq->codigo_procedimiento_principal = $request->cod_fonasa_p_1 == "" ? null : $request->cod_fonasa_p_1;
            $iq->otro_procedimiento = $request->proc_principal2 == "" ? null : $request->proc_principal2;
            $iq->codigo_otro_procedimiento = $request->cod_fonasa_p_2 == "" ? null : $request->cod_fonasa_p_2;
            $iq->save();
        } catch (Exception $e) {
            // if an exception happened in the try block above
            return response()->json(["error" => $e]);
        }

        return response()->json(["exito" => "Los datos de egreso han sido guardados con exito"]);
    }

    public function obtenerPaciente(Request $request)
    {
        $rut = $request->input("rut");
        $id = $request->input("id");

        $caso = Caso::where("paciente", "=", $id)->orderBy("fecha_ingreso", "desc")->first();
        if ($caso == null) {
            return response()->json([]);
        }

        if ($caso->fecha_termino != null) {
            return response()->json([]);
        }

        $paciente = Paciente::where("rut", $rut)->first();
        if ($paciente == null) {
            return response()->json([]);
        }

        $paciente->fecha_nacimiento = date("d-m-Y", strtotime($paciente->fecha_nacimiento));
        $paciente->dv = ($paciente->dv == 10) ? 'K' : $paciente->dv;
        return response()->json($paciente);
    }

    public function editarPaciente(Request $request)
    {
        // return $request;
        try {
            DB::beginTransaction();
            $id = trim($request->input("id"));
            $rut = trim($request->input("rut"));
            $rut_madre = trim($request->input("rut_madre"));
            $dv_madre = trim($request->input("dv_madre"));
            $dv = (strtolower($request->input("dv")) == "k") ? 10 : $request->input("dv");
            $dv_madre = (strtolower($request->input("dv_madre")) == "k") ? 10 : $request->input("dv_madre");
            $nombre = trim($request->input("nombre"));
            $apellidoP = trim($request->input("apellido_paterno"));
            $apellidoM = trim($request->input("apellido_materno"));
            $sexo = $request->input("sexo");
            $fecha = date("Y-m-d", strtotime($request->input("fecha_nacimiento")));
            $extranjero = $request->input("extranjero");

            $pasaporte = strip_tags($request->n_pasaporte);
            $ficha = $request->input("ficha");
            $id_caso = $request->input("caso");
            if ($extranjero === 'si') {
                $extranjero = true;
            } elseif ($extranjero === 'no') {
                $extranjero = false;
            } else {
                $extranjero = null;
            }

            if ($rut !== '' && Paciente::existePaciente($rut)) {
                $paciente = Paciente::where("rut", $rut)->first();
                if ($paciente != null) {
                    $idPaciente = $paciente->id;
                    $casos = Caso::where("paciente", $idPaciente)->get();
                    foreach ($casos as $caso) {
                        $caso->paciente = $id;
                        $caso->save();
                    }
                    if ($idPaciente != $id) {
                        $paciente->delete();
                    }

                }
            }

            $paciente = Paciente::find($id);
            $paciente->rut = $rut;
            $paciente->dv = $dv;
            $paciente->nombre = $nombre;
            $paciente->extranjero = $extranjero;
            $paciente->identificacion = ($extranjero)?'pasaporte':'run';
            $paciente->n_identificacion = ($extranjero)?$pasaporte:"No especificado";
            if (!empty($apellidoP)) {
                $paciente->apellido_paterno = $apellidoP;
            }

            if (!empty($apellidoM)) {
                $paciente->apellido_materno = $apellidoM;
            }

            if (!empty($rut_madre) && !empty($dv_madre)) {
                $paciente->rut_madre = $rut_madre;
                $paciente->dv_madre = $dv_madre;
            }
            $paciente->sexo = $sexo;
            $paciente->fecha_nacimiento = $fecha;
            $paciente->calle = strip_tags(trim($request->input("calle")));
            if ($request->input("numeroCalle") == "") {
                $paciente->numero = null;
            } else {
                $paciente->numero = strip_tags($request->input("numeroCalle"));
            }
            
            $paciente->observacion = strip_tags(trim($request->input("observacionCalle")));
            $paciente->id_comuna = trim($request->input("comuna"));
            $paciente->telefono = ($request->filled("telefono_antiguo")) ? $request->telefono_antiguo : '-'; // 0 porque no guarda vacio :/
            
            $telefonos = Telefono::select("id")->where("id_paciente",$id)->get();
            if(count($telefonos) > 0){
                foreach ($telefonos as $t){
                    Telefono::destroy($t->id);
                }
            }
            $tipo_telefono = $request["tipo_telefono"];
            $telefono = $request["telefono"];
            if(isset($telefono)){
                foreach ($tipo_telefono as $key => $tipo) {
                    if($telefono[$key] != null){
                        $nuevo_telefono = new Telefono;
                        $nuevo_telefono->id_paciente = $id;
                        $nuevo_telefono->tipo = $tipo;
                        $nuevo_telefono->telefono = $telefono[$key];
                        $nuevo_telefono->save();
                    }
                }
            }

            if (trim($request->input("latitud")) == "") {
                $paciente->latitud = null;
            } else {
                $paciente->latitud = trim($request->input("latitud"));
            }
            if (trim($request->input("longitud")) == "") {
                $paciente->longitud = null;
            } else {
                $paciente->longitud = trim($request->input("longitud"));
            }
            $paciente->save();

            $caso = Caso::find($id_caso);
            $caso->ficha_clinica = $ficha;
            $caso->save();

            DB::commit();

            return response()->json(["exito" => "Los datos del paciente han sido actualizados"]);

        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al actualizar los datos del paciente", "msg" => $ex->getMessage()]);
        }
    }

    public function tieneCaso(Request $request)
    {
        /* Seguramente esto sirve para verificar si un paciente sin rut ya está ingresado también con rut
        (paciente dos veces).
         */
        $rut = $request->input("rut");
        $id_recibido = $request->input("id");
        $paciente = Paciente::where("rut", $rut)->first();
        if ($paciente == null) {
            return response()->json(["valid" => true]);
        }

        $id = $paciente->id;
        if ($id == $id_recibido) {
            return response()->json(["valid" => true]);
        }

        /*$paciente=new Paciente;*/
        $caso = Caso::where("paciente", "=", $id)->orderBy("fecha_ingreso", "desc")->first();
        if ($caso == null) {
            return response()->json(["valid" => true]);
        }

        if ($caso->fecha_termino == null) {
            return response()->json(["valid" => false, "message" => "El paciente tiene un caso asignado({$id_recibido})"]);
        }

        return response()->json(["valid" => true]);
    }

    public function existePaciente(Request $request)
    {

        $rut = (int) $request->input("rut");
        $esValidoElRut = $request->input("esValidoElRut");
        $paciente = Paciente::where("rut", $rut)->first();
        //return $paciente;
        //return $esValidoElRut;
        if ($esValidoElRut == 'false') {
            return response()->json(["valid" => false, "message" => "<a href=''></a>"]);
        } elseif ($paciente == null) {
            return response()->json(["valid" => false, "message" => "<a href='#modalAsignacionCama' data-toggle='modal' data-dismiss='modal'>Paciente no existe click aquí para crearlo</a>"]);
        } else {
            return response()->json(["valid" => true]);
        }

    }

    public function crearPaciente(Request $request)
    {

        $paciente = new Paciente;
        $rut = $request->input("rut");
        $dv = $request->input("dv");
        $extranjero = $request->input("extranjero");
        $fechaNac = $request->input("fechaNac");
        $nombre = $request->input("nombre");
        $apellidoP = $request->input("apellidoP");
        $apellidoM = $request->input("apellidoM");
        $sexo = $request->input("sexo");

        $paciente->rut = $rut;
        $paciente->dv = $dv;
        $paciente->sexo = $sexo;
        $paciente->fecha_nacimiento = date("Y-m-d", strtotime(trim($fechaNac)));
        $paciente->nombre = $nombre;
        $paciente->apellido_paterno = $apellidoP;
        $paciente->apellido_materno = $apellidoM;
        $paciente->extranjero = $extranjero;
        $paciente->save();

        return response()->json([
            "derivacion" => true,
            "msg" => "Paciente creado",
        ]);
        //return Input::all();
    }

    public function puedeEditar($idCaso, $ubicacion){

        //validaciones
        $respuesta = Consultas::puedeHacer($idCaso,$ubicacion);
        if($respuesta != "Exito"){
            return response()->json(array("error" => $respuesta));
        }else{
            return response()->json(["exito" => "Exito"]);
        }
        //validaciones
    }
}