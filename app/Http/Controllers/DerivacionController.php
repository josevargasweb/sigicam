<?php
namespace App\Http\Controllers;

use App\Http\Controllers\FormularioDerivacionController;
use App\Models\Cama;
use App\Models\Caso;
use App\Models\Derivacion;
use App\Models\DerivacionesExtrasistema;
use App\Models\Documento;
use App\Models\Establecimiento;
use App\Models\HistorialDiagnostico;
use App\Models\HistorialOcupacion;
use App\Models\MensajeDerivacion;
use App\Models\MensajeUsuario;
use App\Models\Paciente;
use App\Models\Prevision;
use App\Models\Riesgo;
use App\Models\Usuario;
use Auth;
use Consultas;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpcionSolicitud;
use Session;
use TipoUsuario;
use URL;
use View;

class DerivacionController extends Controller
{

    public function tablaDerivaciones($tipo, $cantRegistros = 100)
    {
        $solicitud = ($tipo == "enviadas") ? "Solicitudes enviadas" : "Solicitudes recibidas";
        $miga = ($tipo == "enviadas") ? "Enviadas" : "Recibidas";
        $mensajes = "";
        $esta = Session::get('idEstablecimiento');
        if ($esta != null) {
            $alerta = DB::table(DB::raw(
                "(select d.id,to_char(d.fecha,'DD-MM-YYYY') as fecha,p.nombre,p.apellido_paterno,p.apellido_materno,p.rut,p.dv,d.establecimiento from derivaciones as d,unidades_en_establecimientos as u,establecimientos as e, casos as c,pacientes as p
					where d.destino=u.id and e.id=$esta and u.establecimiento=e.id and p.id=c.paciente and c.id=d.caso
					and fecha_cierre is null and revisada='no') as ra"
            ))
                ->get();

            foreach ($alerta as $alert) {
                $establecimiento = Establecimiento::getNombre($alert->establecimiento);
                $mensajes .= "&#x25b6 $alert->fecha $establecimiento, $alert->nombre $alert->apellido_paterno $alert->apellido_materno<br><br>";
            }
        }
        $motivos = Derivacion::obtenerMotivosCierres();

        return View::make("Derivacion/TablaDerivaciones", ["mensajes" => $mensajes, "solicitud" => $solicitud, "miga" => $miga, "tipo" => $tipo, "motivos" => $motivos, "cantRegistros" => $cantRegistros]);
    }

    public function obtenerMotivos()
    {
        $motivos = Derivacion::obtenerMotivosCierres();
        return response()->json($motivos);
    }

    public function getDerivaciones(Request $request)
    {
        try {
            $cantRegistros = $request->input("registros");
            $tipo = $request->input("param");
            $id = Session::get("idEstablecimiento");
            $motivo = strtolower($request->input("motivo"));
            $motivoOriginal = $request->input("motivo");
            $us = Session::get("usuario");
            $arregloDeIds = array();

            if ($us->tipo !== TipoUsuario::ADMINSS && $us->tipo !== TipoUsuario::MONITOREO_SSVQ) {
                if ($tipo == "enviadas") {
                    $derivaciones = Derivacion::getDerivacionesEnviadas($id, $motivo)->take($cantRegistros);
                } elseif ($tipo == "recibidas") {
                    $derivaciones = Derivacion::getDerivacionesRecibidas($id, $motivo)->take($cantRegistros);
                } else {
                    throw new Exception;
                }

            } else {
                $derivaciones = Derivacion::derivaciones($motivo)->get()->take($cantRegistros);

                //return $arregloDeIds;

            }

            $response = array();
            $x = 0;
            foreach ($derivaciones as $derivacion) {

                $x++;
                array_push($arregloDeIds, $derivacion->id);

                $fecha_derivacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $derivacion->fecha);
                if (!$derivacion->fecha_cierre) {
                    $fecha_cierre = \Carbon\Carbon::now();
                } else {
                    $fecha_cierre = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $derivacion->fecha_cierre);
                }

                $opciones = "<div class='dropdown'><button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu{$derivacion->id}' data-toggle='dropdown' aria-expanded='false'>
				Opciones<span class='caret'></span></button>";
                $opciones .= "<ul class='dropdown-menu dropdown-menu-right' role='menu' aria-labelledby='dropdownMenu{$derivacion->id}'>";
                $verMensajes = View::make("Derivacion/ver_mensajes", ["id" => $derivacion->id])->render();
                $enviarMensaje = View::make("Derivacion/enviar_mensaje", ["id" => $derivacion->id, "tipo" => $tipo, "motivo" => $motivo])->render();
                $cancelar = View::make("Derivacion/cancelar", ["id" => $derivacion->id])->render();
                $aceptar = View::make("Derivacion/aceptar", ["id" => $derivacion->id])->render();
                $rechazar = View::make("Derivacion/rechazar", ["id" => $derivacion->id])->render();
                $cancelar_aceptacion = View::make("Derivacion/cancelar_aceptacion", ["id" => $derivacion->id])->render();
                $resolicitar = View::make("Derivacion/resolicitar", ["derivacion" => $derivacion])->render();
                $seleccionar = View::make("Derivacion/seleccionar_cama", ["id" => $derivacion->id])->render();
                $cambiarDestino = View::make("Derivacion/cambiar_destino", ["derivacion" => $derivacion])->render();
                $restablecerDerivacion = View::make("Derivacion/restablecer", ["id" => $derivacion->id])->render();

                $li = "";
                $esta = Session::get('idEstablecimiento');
                if ($us->tipo != TipoUsuario::MONITOREO_SSVQ && $us->tipo !== TipoUsuario::DIRECTOR && $us->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO) {
                    if ($tipo == 'enviadas') {
                        if ($derivacion->motivo_cierre == "rechazado") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                            if ($us->tipo != TipoUsuario::ADMINSS) {
                                $li .= "<li role='presentation'>$resolicitar</li>";
                                $li .= "<li role='presentation'>$cambiarDestino</li>";
                            }
                            if ($us->tipo == TipoUsuario::ADMINSS) {
                                $li .= "<li role='presentation'>$restablecerDerivacion</li>";
                            }
                        } elseif ($derivacion->motivo_cierre == "aceptado, pendiente de cama") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                            if ($us->tipo != TipoUsuario::ADMINSS) {
                                $li .= "<li role='presentation'>$cancelar</li>";
                            }
                        } elseif ($derivacion->motivo_cierre == "aceptado") {
                            $li .= "<li role='presentation'>$verMensajes</li>";

                            if ($us->tipo == TipoUsuario::ADMINSS) {
                                $li .= "<li role='presentation'>$restablecerDerivacion</li>";
                            }

                        } elseif ($derivacion->motivo_cierre == "cancelado") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                            if ($us->tipo != TipoUsuario::ADMINSS) {
                                $li .= "<li role='presentation'>$resolicitar</li>";
                                $li .= "<li role='presentation'>$cambiarDestino</li>";
                            }
                        } elseif ($derivacion->motivo_cierre === null) {
                            $li .= "<li role='presentation'>$enviarMensaje</li>";
                            if ($us->tipo != TipoUsuario::ADMINSS) {
                                $li .= "<li role='presentation'>$cancelar</li>";
                                $li .= "<li role='presentation'>$cambiarDestino</li>";
                            }
                        } else {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                        }
                    } elseif ($tipo == 'recibidas') {
                        if ($derivacion->motivo_cierre == "rechazado") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                        } elseif ($derivacion->motivo_cierre == "aceptado, pendiente de cama") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                            $li .= "<li role='presentation'>$seleccionar</li>";
                            $li .= "<li role='presentation'>$cancelar_aceptacion</li>";
                        } elseif ($derivacion->motivo_cierre == "aceptado") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                        } elseif ($derivacion->motivo_cierre == "cancelado") {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                        } elseif ($derivacion->motivo_cierre === null) {
                            $li .= "<li role='presentation'>$enviarMensaje</li>";
                        } else {
                            $li .= "<li role='presentation'>$verMensajes</li>";
                        }
                    }

                    if ($us->tipo == TipoUsuario::ADMINSS && $derivacion->motivo_cierre === null) {
                        $li .= "<li role='presentation'>$aceptar</li>";
                        $li .= "<li role='presentation'>$rechazar</li>";
                        $li .= "<li role='presentation'>$cancelar</li>";
                    }
                    if ($esta == 8 && $derivacion->motivo_cierre === null && $tipo == 'recibidas') {
                        $li .= "<li role='presentation'>$aceptar</li>";
                        $li .= "<li role='presentation'>$rechazar</li>";
                    }
                } else {
                    $li .= "<li role='presentation'>$verMensajes</li>";
                }

                if (!empty($li)) {
                    $opciones .= "$li</ul></div>";
                } else {
                    $opciones = "";
                }

                $dv = ($derivacion
                        ->casoOrigen
                        ->pacienteCaso
                        ->dv == 10) ? "K" : $derivacion
                        ->casoOrigen
                        ->pacienteCaso
                        ->dv;
                    //return $derivacion->usuarioSolicitante;
                    //$dv_usuario = ($derivacion->usuarioSolicitante->dv == 10) ? "K" : $derivacion->usuarioSolicitante->dv
                    $dv_usuario = "";
                    if ($derivacion->usuarioSolicitante) {
                    //$nombres = $derivacion->usuarioSolicitante->nombres;
                    $dv_usuario = $derivacion->usuarioSolicitante->nombres . " " . $derivacion->usuarioSolicitante->apellido_paterno;
                }

                if ($us->tipo !== TipoUsuario::ADMINSS && $us->tipo !== TipoUsuario::MONITOREO_SSVQ) {
                    if ($tipo == "enviadas") {
                        $estabOrigen = $derivacion->establecimientoOrigen->nombre;
                        $estabDestino = $derivacion->unidadDestino->establecimientos->nombre;
                    } elseif ($tipo == "recibidas") {
                        $estabOrigen = $derivacion->unidadDestino->establecimientos->nombre;
                        $estabDestino = $derivacion->establecimientoOrigen->nombre;
                    } else {
                        throw new Exception("Tipo de derivación inválida: {$tipo}");
                    }
                } else {
                    $estabOrigen = $derivacion->establecimientoOrigen->nombre;
                    $estabDestino = $derivacion->unidadDestino->establecimientos->nombre;
                }
                $diff_d = $fecha_cierre->diffInDays($fecha_derivacion);
                $diff = $fecha_cierre->diff($fecha_derivacion);
                $diff_seg = $fecha_cierre->diffInSeconds($fecha_derivacion);

                $mensajeDerivacion = MensajeDerivacion::where("derivacion", "=", $derivacion->id)->orderBy("id", "desc")->first();

                $leido = "";
                if ($mensajeDerivacion) {
                    $mensajeUsuario = MensajeUsuario::where("id_usuario", "=", Auth::user()->id)
                        ->where("id_mensaje", "=", $mensajeDerivacion->id)->first();

                    if ($mensajeUsuario) {
                        if ($mensajeUsuario->leido == false) {
                            $leido = "<span style='position: absolute;
							margin-left: 2px;
							margin-top: 8px;
							padding-top: 8px;
							padding-bottom: 7PX;
							font-size: 10px;' class='label label-pill label-danger'>Nuevo Mensaje!</span>";
                        } else {
                            $leido = "";
                        }
                    }

                }

                $semaforo = "";
                if (ucwords(empty($derivacion->motivo_cierre))) {
                    if ($diff_d >= 0 && $diff_d <= 2) {
                        $semaforo = "style='color:green'";
                    } elseif ($diff_d > 2 && $diff_d <= 5) {
                        $semaforo = "style='color:#b96202'";
                    } elseif ($diff_d > 5) {
                        $semaforo = "style='color:red'";
                    } else {
                        $semaforo = "";
                    }
                }
                $part1 = array(
                    "fecha_format" => "<p $semaforo>" . $fecha_derivacion->format("d-m-Y H:i:s") . "</p>",
                    "fecha" => "<p $semaforo>" . $fecha_derivacion->timestamp . "</p>",
                    "tiempo_espera_format" => "<p $semaforo>" . $diff->format("{$diff_d} días, %H horas") . "</p>",
                    "tiempo_espera" => "<p $semaforo>" . $diff_seg . "</p>",
                    "estab_destino" => "<p $semaforo>" . $estabDestino . "</p>",
                );
                $part2 = array("estab_origen" => "<p $semaforo>" . $estabOrigen . "</p>");
                $paciente = $derivacion->casoOrigen->pacienteCaso;
                $paciente = mb_strtolower("{$paciente->apellido_paterno} {$paciente->apellido_materno}, {$paciente->nombre}");

                if ($derivacion->casoOrigen->pacienteCaso->fecha_nacimiento) {
                    $fechaNac = Paciente::edad($derivacion->casoOrigen->pacienteCaso->fecha_nacimiento);
                } else {
                    $fechaNac = "0";
                }

                if ($derivacion->casoOrigen->diagnosticos->count() > 0) {
                    $diagnostico_caso = "<p $semaforo>" . $derivacion->casoOrigen->diagnosticos->first()->diagnostico . "</p>";
                } else {
                    $diagnostico_caso = "<p $semaforo> No tiene diagnostico </p>";
                }
                $part3 = array(
                    "unidad_destino" => "<p $semaforo>" . $derivacion->unidadDestino->alias . "</p>",
                    "usuario_solicitante" => "<p $semaforo>" . $dv_usuario . "</p>",
                    "dv_usuario" => "<p $semaforo></p>",
                    "nombre_paciente" => "<a $semaforo href='" . URL::to('/') . "/traslado/$tipo/enviarMensaje/$derivacion->id/$motivoOriginal'><p $semaforo>" . ucwords($paciente) . "</p></a>",
                    "rut_paciente" => "<p $semaforo>" . $derivacion->casoOrigen->pacienteCaso->rut . "</p>",
                    "dv_paciente" => "<p $semaforo>" . $dv . "</p>",
                    "edad_paciente" => "<p $semaforo>" . $fechaNac . "</p>",
                    "diagnostico" => $diagnostico_caso,
                    "riesgo" => isset($derivacion->casoOrigen->historialEvolucion->riesgo) ? $derivacion->casoOrigen->historialEvolucion->riesgo : "",
                    "estado" => "<p $semaforo>" . ucwords(empty($derivacion->motivo_cierre) ? "En curso" : $derivacion->motivo_cierre) . "</p>",
                    "opciones" => $opciones . $leido,
                    "numero_solicitud" => "<p $semaforo>" . $x . "</p>",
                );
                $response["data"][] = array_merge($part1, $part2, $part3);
                $response["ids"] = $arregloDeIds;
            }
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    public function getRiesgos()
    {
        $riesgos = Riesgo::getRiesgos();
        $response = array();
        foreach ($riesgos as $key => $value) {
            $response[] = $value;
        }
        return response()->json($response);
    }

    public function cancelarTraslado(Request $request)
    {
        try {
            $id = $request->input("id");
            $derivacion = Derivacion::find($id);
            $derivacion->fecha_cierre = DB::raw("date_trunc('seconds', now())");
            $derivacion->motivo_cierre = "cancelado";
            $derivacion->save();
            return response()->json(array("exito" => "El traslado externo ha sido cancelada"));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al cancelar el traslado", "msg" => $ex->getMessage()));
        }
    }

    public function aceptarAdmin(Request $request)
    {
        try {
            $id = $request->input("id");
            if ($request->has("fecha_cierre")) {$fecha_cierre = $fecha_cierre = $request->input("fecha_cierre");} else { $fecha_cierre = date('Y-m-d');}
            $derivacion = Derivacion::find($id);
            //$derivacion->fecha_cierre=DB::raw("date_trunc('seconds', now())");
            $derivacion->fecha_cierre = $fecha_cierre;
            $derivacion->motivo_cierre = "aceptado";
            $derivacion->save();
            return response()->json(array("exito" => "El traslado externo ha sido aceptado", "all" => $request->all()));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al cancelar el traslado", "msg" => $ex->getMessage()));
        }
    }

    public function rechazarAdmin(Request $request)
    {
        try {
            $id = $request->input("id");
            $derivacion = Derivacion::find($id);
            $derivacion->fecha_cierre = DB::raw("date_trunc('seconds', now())");
            $derivacion->motivo_cierre = "rechazado";
            $derivacion->save();
            return response()->json(array("exito" => "El traslado externo ha sido rechazado"));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al cancelar el traslado", "msg" => $ex->getMessage()));
        }
    }

    public function cancelarAceptacionTraslado(Request $request)
    {
        try {
            $id = $request->input("id");
            $derivacion = Derivacion::find($id);
            $derivacion->fecha_cierre = null;
            $derivacion->motivo_cierre = null;
            $derivacion->save();
            return response()->json(array("exito" => "El estado de aceptado ha sido cancelado"));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al cancelar el estado", "msg" => $ex->getMessage()));
        }
    }

    public function enviarMensajeView($tipo, $id, $motivo)
    {
        $solicitud = Derivacion::getSolicitudId($id);
        if (is_null($solicitud)) {
            return Redirect::back();
        }

        $documentos = Documento::getDocumentos($id);

        $mensajeDerivacion = MensajeDerivacion::where("derivacion", "=", $id)->orderBy("id", "desc")->first();
        if ($mensajeDerivacion) {
            $mensajeUsuario = MensajeUsuario::where("id_usuario", "=", Auth::user()->id)->where("id_mensaje", "=", $mensajeDerivacion->id)->first();

            if ($mensajeUsuario) {

                $mensajeUsuario->leido = true;
                $mensajeUsuario->save();
            }
        }

        $MiInfeccion = 0;
        $patogeno = 0;
        $ubicacion = 0;
        $aislamiento = 0;
        $derivacion = Derivacion::findOrFail($id);
        $idCaso = $derivacion->caso;
        $derivacion->revisada = 'si';
        $derivacion->save();

        if (!is_null($idCaso)) {

            $infeccion = DB::table(DB::raw("(select i.id from casos as c,infecciones as i where c.id=i.caso and c.id=$idCaso and i.caso=$idCaso and i.fecha_termino is null) as re"
            ))->get();

            foreach ($infeccion as $infec) {
                $MiInfeccion = $infec->id;
            }

            $iaas2 = DB::table(DB::raw("(select * from iaas as i where i.id_infeccion=$MiInfeccion) as ru"
            ))->first();

            if ($iaas2 != null) {
                $patogeno = $iaas2->agente1;
                $ubicacion = $iaas2->localizacion;
            }

            $paciente_infeccion = DB::table(DB::raw("(select * from pacientes_infeccion as i where i.id_infeccion=$MiInfeccion) as ri"
            ))->get();

            foreach ($paciente_infeccion as $paciente_infec) {
                $aislamiento = $paciente_infec->aislamiento;
            }
        }
        return View::make("Derivacion/EnviarMensaje", ["id" => $id, "riesgo" => Consultas::getRiesgos(),
            "solicitud" => $solicitud, "tipo" => $tipo, "documentos" => $documentos, "aislamiento" => $aislamiento, "motivo" => $motivo]);
    }

    public function reservarPendiente(Request $request)
    {
        DB::beginTransaction();
        try {
            $derivacion = Derivacion::findOrFail($request->input("idTraslado"));
            $derivacion->motivo_cierre = "aceptado";
            $derivacion->save();
            //Caso::liberarCama($derivacion->caso, "traslado externo");
            $reserva = new Reserva();
            $reserva->cama = $request->input("idCama");
            $reserva->fecha = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
            $reserva->tiempo = $request->input("horas") . " hours";
            $reserva->caso = $derivacion->caso;
            $reserva->save();

            DB::commit();
            return response()->json(array("exito" => "Se ha realizado la reserva. El estado de la derivación es \"Aceptado\""));
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(array("error" => "Error al reservar cama al paciente", "msg" => $ex->getMessage()));
        }
    }

    public function enviarMensaje(Request $request)
    {
        //return response()->json("holaalalala");
        DB::beginTransaction();
        try {
            $msg = "";
            $idTraslado = $request->input("idTraslado");
            $mensaje = trim($request->input("mensaje"));
            $accion = $request->input("accion");
            $idCaso = $request->input("caso");
            $idUsuario = Auth::user()->id;
            $idCama = $request->input("cama");

            $derivacion = new MensajeDerivacion;
            $derivacion->derivacion = $idTraslado;
            $derivacion->usuario = $idUsuario;
            $derivacion->fecha = DB::raw("date_trunc('seconds', now())");
            $derivacion->contenido = $mensaje;
            $derivacion->save();

            $idMensaje = $derivacion->id;

            $usuarios = Usuario::select("id")->where("id", "<>", Auth::user()->id)->get();

            foreach ($usuarios as $user) {
                $mensajes = new MensajeUsuario;
                $mensajes->id_usuario = $user->id;
                $mensajes->id_mensaje = $idMensaje;
                $mensajes->save();
            }

            if ($accion == OpcionSolicitud::ACEPTAR) {
                $msg = "El traslado ha sido aceptado";
                /*
                $reserva=new Reserva;
                $reserva->cama=$request->input("cama");
                $reserva->fecha=DB::raw("date_trunc('seconds', now())");
                $reserva->tiempo=$request->input("hora")." hours";
                $reserva->caso=$request->input("caso");
                $reserva->save();

                /*Caso::liberarCama($request->input("caso"), "traslado externo");*/

                $caso = Caso::findOrFail($idCaso);
                $establecimiento_origen = $caso->establecimientoCaso()->firstOrFail();
                //$reserva = $caso->reservas()->orderBy("fecha", "desc")->firstOrFail();

                //obtener ultimo caso del paciente
                $ultimo_caso_paciente = Caso::select("id")
                    ->where("paciente", "=", $caso->paciente)
                    ->orderBy("id", "desc")
                    ->first();
                //return response()->json($ultimo_caso_paciente);
                //fin

                $cama = Cama::findOrFail($idCama);
                $unidad = $cama->sala()
                    ->firstOrFail()
                    ->unidadEnEstablecimiento()
                    ->firstOrFail();
                $establecimiento = $unidad->establecimientos()->firstOrFail();

                //$reserva->tiempo = 0;
                //$reserva->save();
                $now = \Carbon\Carbon::now();
                $caso->fecha_termino = $now;
                $caso->detalle_termino = "Traslado al servicio de {$unidad->alias} en {$establecimiento->nombre}";
                $caso->motivo_termino = "traslado externo";
                $caso->save();

                $h = $caso->historialOcupacion()->first();
                if ($h != null) {
                    $h->fecha_liberacion = $now;
                    $h->motivo = "traslado externo";
                    $h->save();
                }

                $n_caso = new Caso;
                $n_caso->fecha_ingreso = $now;
                $n_caso->paciente = $caso->paciente;
                $n_caso->establecimiento = $establecimiento->id;
                $n_caso->detalle_procedencia = "Traslado desde {$establecimiento_origen->nombre}";
                //$n_caso->diagnostico = $caso->diagnostico;
                $n_caso->medico = $caso->medico;
                $n_caso->prevision = $caso->prevision;
                $n_caso->procedencia = 2;
                $n_caso->save();

                //crear diagnostico al trasladar
                $ultimo_ice10 = HistorialDiagnostico::select("id_cie_10", "diagnostico")
                    ->where("caso", "=", $ultimo_caso_paciente->id)
                    ->whereNotNull("id_cie_10")
                    ->first();
                //return response()->json($ultimo_ice10->id_cie_10);
                $diagnostico = new HistorialDiagnostico;
                $diagnostico->caso = $n_caso->id;
                $diagnostico->fecha = $now;
                $diagnostico->diagnostico = $ultimo_ice10->diagnostico;
                $diagnostico->id_cie_10 = $ultimo_ice10->id_cie_10;
                //return response()->json($diagnostico);
                $diagnostico->save();
                //fin crear diagnostico

                $hOcupacion = new HistorialOcupacion;
                $hOcupacion->cama = $idCama;
                $hOcupacion->caso = $n_caso->id;
                $hOcupacion->fecha = $now;
                $hOcupacion->save();

                Derivacion::find($idTraslado)->cerrar("aceptado");
            }

            if ($accion == OpcionSolicitud::ACEPTARSINCAMA) {
                $msg = "El traslado ha sido aceptado. Recuerde reservar una cama para el paciente";
                Derivacion::find($idTraslado)->cerrar("aceptado, pendiente de cama");
            }

            if ($accion == OpcionSolicitud::RECHAZAR) {
                $msg = "El traslado ha sido rechazado";
                Derivacion::find($idTraslado)->cerrar("rechazado", $request->input("motivo"));
            }

            if ($accion == OpcionSolicitud::ENVIAR) {
                $msg = "El mensaje ha sido enviado";
            }

            $destino = storage_path() . '/data/mensajes/';

            $files = $request->file("files");
            $destino = "{$destino}/{$idCaso}/{$idTraslado}";
            File::makeDirectory($destino, 0775, true, true);
            if ($request->hasFile('files')) {
                foreach ($files as $file) {
                    if (empty($file)) {
                        continue;
                    }

                    $rand = rand(1, 9999);
                    $filename = $rand . "__" . $file->getClientOriginalName();
                    $file->move($destino, $filename);

                    $documento = new Documento;
                    $documento->derivacion = $idTraslado;
                    $documento->recurso = "{$destino}/{$filename}";
                    $documento->save();
                }
            }
            DB::commit();
            return response()->json(array("exito" => $msg));
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(array("error" => "Error al enviar el mensaje", "msg" => "{$ex->getMessage()} {$ex->getLine()} {$ex->getFile()}"));
        }
    }

    public function rechazarTraslado(Request $request)
    {
        try {
            $idTraslado = $request->input("idTraslado");
            $derivacion = Derivacion::find($idTraslado);
            $derivacion->fecha_cierre = DB::raw("date_trunc('seconds', now())");
            $derivacion->motivo_cierre = "rechazado";
            $derivacion->save();
            return response()->json(array("exito" => "El traslado externo ha sido cancelada"));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al cancelar el traslado", "msg" => $ex->getMessage()));
        }
    }

    public function getMensajeTraslado(Request $request)
    {

        $derivaciones = Derivacion::find($request->input("id"))->getMensajesDerivacion();

        $archivos = Derivacion::obtenerArchivosDerivaciones($request->input("id"));

        return response()->json(["derivaciones" => $derivaciones, "archivos" => $archivos]);
    }

    public function aceptarSolicitud(Request $request)
    {
        try {
            $idTraslado = $request->input("idTraslado");
            $derivacion = Derivacion::find($idTraslado);
            $derivacion->fecha_cierre = DB::raw("date_trunc('seconds', now())");
            $derivacion->motivo_cierre = "aceptado";
            $derivacion->save();
            return response()->json(array("exito" => "La solicitud ha sido aceptada"));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al aceptar la solicitud", "msg" => $ex->getMessage()));
        }
    }

    public function resolicitarTraslado(Request $request)
    {
        try {
            $idTraslado = $request->input("id");
            $derivacion = Derivacion::find($idTraslado);
            $mensaje = "Solicitud de traslado externo, cancelada el {$derivacion->fecha_cierre}, reabierta por el solicitante.";
            $derivacion->motivo_cierre = null;
            $derivacion->fecha_cierre = null;
            $derivacion->fecha = \Carbon\Carbon::now()->format("Y-m-d H:i:s");

            $derivacion->save();
            $derivacion->enviarMensaje($mensaje, "Reactivación de solicitud");

            return response()->json(array("exito" => "La solicitud ha sido resolicitada"));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al resolicitar la solicitud", "msg" => $ex->getMessage()));
        }
    }

    public function cambiarDestino(Request $request)
    {
        $este_establecimiento = Session::get("idEstablecimiento");
        $id_establecimiento_destino = $request->input("establecimiento");
        $id_servicio_destino = $request->input("servicio-destino");
        $id_derivacion = $request->input("id-derivacion");
        $derivacion = Derivacion::with("mensajes")->with("documentos")->findOrFail($id_derivacion);
        if ($derivacion->establecimiento != $este_establecimiento) {
            throw new MensajeException("No se puede modificar una derivación que no pertenece al establecimiento actual");
        }
        $usuario = Session::get("usuario");
        $fecha = \Carbon\Carbon::now();
        $nueva_derivacion = $derivacion->replicate([
            "id",
            "created_at",
            "updated_at",
            "destino",
            "usuario",
            "fecha",
            "fecha_cierre",
            "motivo_cierre",
        ]);

        $derivacio = Derivacion::find($id_derivacion);
        $derivacio->motivo_cierre = "cancelado";
        $derivacio->fecha_cierre = date("Y-m-d H:i:s");
        $derivacio->save();

        $nueva_derivacion->destino = $id_servicio_destino;
        $nueva_derivacion->usuario = $usuario->id;
        $nueva_derivacion->fecha = $fecha;
        $nueva_derivacion->save();
/*
if($derivacion->mensajes !== null){
$nuevo_mensaje = $derivacion->mensajes[0]->replicate([
"derivacion",
"usuario",
"fecha",
"id",
"created_at",
"updated_at",
]);
$nuevo_mensaje->derivacion = $nueva_derivacion->id;
$nuevo_mensaje->usuario = $usuario->id;
$nuevo_mensaje->fecha = $fecha;
$nuevo_mensaje->save();
}
 */

        $EstableciMensaje = DB::table(DB::raw("(select m.id,m.destino from mensajes_derivaciones as m where m.derivacion=$id_derivacion) as ra"))->get();

        foreach ($EstableciMensaje as $Messages) {
            $establecimi = MensajeDerivacion::find($Messages->id);
            $establecimi->derivacion = $nueva_derivacion->id;
            $establecimi->save();
        }

        if ($derivacion->documentos !== null) {
            foreach ($derivacion->documentos as $documento) {
                $nuevo_documento = $documento->replicate([
                    "id",
                    "derivacion",
                    "created_at",
                    "updated_at",
                ]);
                $nuevo_documento->derivacion = $nueva_derivacion->id;
                $nuevo_documento->save();
            }
        }

        /*    $derivacion=new MensajeDerivacion;
        $derivacion->derivacion=$idTraslado;
        $derivacion->usuario=$idUsuario;
        $derivacion->fecha=DB::raw("date_trunc('seconds', now())");
        $derivacion->contenido=$mensaje;
        $derivacion->save();

        $ActualizaInfeccion=IAAS::where("id_infeccion","=",$MiInfeccion)->where("cierre","=",'no')->get();
        $j=0;
        foreach ($ActualizaInfeccion as $actualizar) {
        $actualizar->cierre=trim($cerrar[$j]);
        $j=$j+1;
        $actualizar->save();
        }

         */

        return response()->json(["mensaje" => "Se ha cambiado el destino de la derivación exitosamente."]);

    }

    public function getCamasDisponibles(Request $request)
    {

        //return "OK";
        $url = URL::to('/');
        $tiene = true;
        $camas = array();
        $nombres = array();
        /*$ocupacionesCamaSala = Consultas::ultimoEstadoCamas()
        ->where("est.id", "=", Session::get("idEstablecimiento"))
        ->where("ue.url", "=", $request->input("unidad"))
        ->get();*/
        $unidad = $request->input("unidad");
        $est = Session::get("idEstablecimiento");
        $consulta = Consultas::ultimoEstadoCamas();
        $consulta = Consultas::addTiempoBloqueo($consulta);
        $consulta = Consultas::addTiempoReserva($consulta);
        $consulta = Consultas::addTiemposOcupaciones($consulta);
        $consulta = $consulta->addSelect("s.visible");
        $consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
            ->where("est.id", "=", $est)
            ->where("ue.url", "=", $unidad)
            ->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
            ->whereNotNull("id_sala")
            ->where("s.visible", true)
            ->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
            ->orderBy("s.nombre", "asc")
            ->orderBy("cm.id_cama", "asc");
        $ocupacionesCamaSala = $consulta->get();
        foreach ($ocupacionesCamaSala as $ocupacion) {
            if ($ocupacion->fecha === null) {
                if ($ocupacion->reservado !== null || $ocupacion->bloqueado !== null || $ocupacion->id_unidad_actual != $ocupacion->id_unidad) {
                    continue;
                }

                $tiene = true;
                $nombre_sala = empty($ocupacion->nombre_sala) ? "Sala sin nombre ({$ocupacion->id_sala})" : $ocupacion->nombre_sala;
                $nombres[$ocupacion->id_sala] = $nombre_sala;
                $imagen = "camaVerde.png";
                $unidad = ucwords($ocupacion->unidad);
                $camas[$ocupacion->id_sala][] = array(
                    "img" => "<a style='margin-left:5px;margin-right:5px;'class='cursor' onclick='marcarCamaDisponible(\"$ocupacion->id_cama_unq\", \"$nombre_sala\", \"$unidad\")'>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>{$ocupacion->id_cama}</figcaption> </figure> </a>", "sala" => $ocupacion->id_sala);
            }
        }

        $response = array("nombres" => $nombres, "salas" => $camas, "tiene" => $tiene);

        return response()->json($response);
    }

    public function viewTrasladoExtraSistema()
    {
        $cupos = Establecimiento::obtenerCuposExtraSistema(Session::get("idEstablecimiento"));
        return View::make("Derivacion/TrasladoExtraSistema", ["cupos" => $cupos, "riesgo" => Consultas::getRiesgos()]);
    }

    public function obtenerUnidades()
    {
        return response()->json(Consultas::getUnidadesEstablecimiento(Session::get("idEstablecimiento")));
    }

    public function getCamasParaRescate(Request $request)
    {
        $url = URL::to('/');
        $tiene = true;
        $camas = array();
        $unidad = $request->input("unidad");

        $ocupacionesCamaSala = Consultas::ultimoEstadoCamas()
            ->where("est.id", "=", Session::get("idEstablecimiento"))
            ->where("ue.url", "=", $unidad)
            ->get();
        foreach ($ocupacionesCamaSala as $ocupacion) {
            $nombre_sala = empty($ocupacion->nombre_sala) ? "Sala sin nombre ({$ocupacion->id_sala})" : $ocupacion->nombre_sala;
            $nombres[$ocupacion->id_sala] = $nombre_sala;
            if ($ocupacion->fecha === null) {
                if (
                    $ocupacion->reservado !== null ||
                    $ocupacion->bloqueado !== null ||
                    $ocupacion->id_unidad_actual != $ocupacion->id_unidad
                ) {
                    continue;
                }
                $tiene = true;
                $imagen = "camaVerde.png";
                $camas[$ocupacion->id_sala][] = array("img" => "<a class='cursor' onclick='rescatar(\"$ocupacion->id_cama_unq\")'>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption><br></figcaption> </figure> </a>", "sala" => $ocupacion->id_sala);
            }
        }

        $response = array("nombres" => $nombres, "salas" => $camas, "tiene" => $tiene);
        return response()->json($response);
    }

    public function rescatar(Request $request)
    {
        try {
            $derivacionExtra = DerivacionesExtrasistema::find($request->input("idExtra"));
            $derivacionExtra->fecha_rescate = DB::raw("date_trunc('seconds', now())");
            $derivacionExtra->save();

            $paciente = Paciente::find($request->input("id"));
            $paciente->nombre = trim($request->input("nombre"));
            $paciente->apellido_paterno = trim($request->input("apellidoP"));
            $paciente->apellido_materno = trim($request->input("apellidoM"));
            $paciente->sexo = $request->input("sexo");
            $paciente->fecha_nacimiento = $request->input("fechaNac");
            $paciente->save();

            $caso = Caso::find($request->input("idCaso"));
            $caso->medico = trim($request->input("medico"));
            $caso->diagnostico = $request->input("diagnostico");
            $caso->save();

            $reserva = new Reserva;
            $reserva->cama = $request->input("cama");
            $reserva->fecha = DB::raw("date_trunc('seconds', now())");
            $reserva->tiempo = $request->input("horas") . " hours";
            $reserva->motivo = trim($request->input("motivo"));
            $reserva->caso = $request->input("idCaso");
            $reserva->save();

            return response()->json(array("exito" => "Se ha hecho la reserva para el paciente en rescate."));
        } catch (Exception $ex) {
            return response()->json(array("error" => "Error al rescatar el paciente", "msg" => $ex->getMessage()));
        }
    }

    public function buscarPacientePorCaso(Request $request)
    {
        $idCaso = $request->input("idCaso");
        $caso = Caso::findOrFail($idCaso);
        $paciente = Paciente::find($caso->paciente);
        try {
            $riesgo = $caso->historialEvolucion()->firstOrFail()->riesgo;
        } catch (Exception $e) {
            $riesgo = null;
        }
        /*$paciente = Consultas::joinUltimoEstadoCamas()
        ->select("pac.id", "pac.nombre", "pac.dv", "pac.fecha_nacimiento", "uep.riesgo", "pac.sexo", "cs.diagnostico", "pac.apellido_paterno", "pac.apellido_materno")
        ->where("cs.id", "=", $idCaso)
        ->first();*/

        $dv = ($paciente->dv == "10") ? "K" : $paciente->dv;
        $edad = Paciente::edad($paciente->fecha_nacimiento);
        $apellidoP = (is_null($paciente->apellido_paterno)) ? "" : ucwords($paciente->apellido_paterno);
        $apellidoM = (is_null($paciente->apellido_materno)) ? "" : ucwords($paciente->apellido_materno);
        $datos = array("id" => $paciente->id, "rut" => $paciente->rut, "nombre" => ucwords($paciente->nombre),
            "fecha" => date("d-m-Y", strtotime($paciente->fecha_nacimiento)), "diagnostico" => $caso->diagnostico,
            "riesgo" => $riesgo, "genero" => $paciente->sexo, "edad" => $edad, "apellidoP" => $apellidoP,
            "apellidoM" => $apellidoM, "dv" => $dv);
        return response()->json($datos);
    }

    public function altaExtraSistema(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $id_caso = $request->input("caso");
                $dex = DerivacionesExtrasistema::where("caso", $id_caso)
                    ->where("fecha_rescate", "=", null)
                    ->orderBy("fecha", "desc")
                    ->firstOrFail();
                $caso = Caso::findOrFail($id_caso);
                $caso->fecha_termino = date("Y-m-d H:i:s");
                $dex->fecha_rescate = date("Y-m-d H:i:s");
                $dex->save();
                $caso->save();

                return response()->json(array("exito" => true, "error" => false));
            });
        } catch (Exception $e) {
            return response()->json(array("exito" => false, "error" => true));
        }

    }

    public function descargar($id)
    {
        //return Documento::getRuta($id);
        return response()->download(Documento::getRuta($id));
    }

    public function restablecerDerivacion(Request $request)
    {
        $id = $request->input("id");
        $derivacion = Derivacion::find($id);

        $derivacion->fecha_cierre = null;
        $derivacion->motivo_cierre = null;
        $derivacion->comentario = "Restablecido manualmente";
        $derivacion->save();

        //rescatando el caso de la derivacion
        $caso = Caso::find($derivacion->caso);

        //veo que casos tiene abiertos el paciente para cerrarlos
        $casosSinCerrar = Caso::whereNull("fecha_termino")->where("paciente", "=", $caso->paciente)->get();
        //return $casosSinCerrar;
        foreach ($casosSinCerrar as $casoSinCerrar) {

            $casoSinCerrar->find($casoSinCerrar->id_caso);
            $casoSinCerrar->fecha_termino = date("Y-m-d H:i:s");
            $casoSinCerrar->motivo_termino = 'alta';
            $casoSinCerrar->detalle_termino = "caso cerrado al reestablecer derivacion";

            $casoSinCerrar->save();

        }
        return response()->json(array("exito" => "Paciente reestablecido", "error" => false));
    }

    public function formularioDerivacion($id)
    {
        $motivos = DB::select(DB::raw("SELECT e.enumlabel AS enum_value, e.enumtypid as enumtypid
	 	  FROM pg_type t JOIN pg_enum e ON t.oid = e.enumtypid JOIN pg_catalog.pg_namespace n ON n.oid = t.typnamespace
		  WHERE t.typname = 'motivo_cierre_derivacion'"));

        $tiposUgcc = DB::select(DB::raw("SELECT e.enumlabel AS enum_value, e.enumtypid as enumtypid
	 	  FROM pg_type t JOIN pg_enum e ON t.oid = e.enumtypid JOIN pg_catalog.pg_namespace n ON n.oid = t.typnamespace
		  WHERE t.typname = 'tipo_ugcc'"));

        $caso = DB::table('casos')
            ->where('casos.id', $id)
            ->first();

        if ($caso) {
            if (count(Establecimiento::all()) > 1) {
                $establecimientos = DB::table('establecimientos')
                    ->where('id', '<>', $caso->establecimiento)
                    ->get();
            } else {
                $establecimientos = Establecimiento::all();
            }

            $establecimiento = DB::table('establecimientos')
                ->where('id', $caso->establecimiento)
                ->first();
            $thistorial = DB::table('t_historial_ocupaciones')
                ->where('t_historial_ocupaciones.caso', $caso->id)
                ->first();
            $paciente = DB::table('pacientes')
                ->where('pacientes.id', $caso->paciente)
                ->first();
            $diagnostico = DB::table('diagnosticos')
                ->where('diagnosticos.caso', $caso->id)
                ->first();
            $unidad = DB::table('unidades')
                ->where('unidades.id', $caso->id_unidad)
                ->first();
            $medico = DB::table('medico')
                ->where('medico.id_medico', $caso->id_medico)
                ->first();
            $unidadEnE = DB::table('unidades_en_establecimientos')
                ->where('unidades_en_establecimientos.id', $caso->id_unidad)
                ->first();
            if ($unidadEnE) {
                $unidades = DB::table('unidades')
                    ->where('unidades.id', $unidadEnE->unidad)
                    ->get();
                if (!$unidades) {
                    $unidades = "";
                }

            } else {
                $unidadEnE = null;
                $unidades = null;
            }
            if ($diagnostico) {
                $cieo = DB::table('cie_10')
                    ->where('cie_10.id_cie_10', $diagnostico->id_cie_10)
                    ->first();
            }

        }

        $datos_ocupacion = DB::table("t_historial_ocupaciones as h")
            ->select("u.url", "s.id as idSala", "c.id as idCama")
            ->leftjoin("camas as c", "c.id", "=", "h.cama")
            ->leftjoin("salas as s", "s.id", "=", "c.sala")
            ->leftjoin("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
            ->where("h.caso", $caso->id)
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

        return view('Derivacion.DerivarPaciente')
            ->with('caso', $caso)
            ->with('paciente', $paciente)
            ->with('diagnostico', $diagnostico)
            ->with('cieo', $cieo)
            ->with('unidad', $unidad)
            ->with('medico', $medico)
            ->with('unidades', $unidades)
            ->with('thistorial', $thistorial)
            ->with('establecimiento', $establecimiento)
            ->with('establecimientos', $establecimientos)
            ->with('unidadEnE', $unidadEnE)
            ->with('motivos', $motivos)
            ->with('url', $url)
            ->with('sala', $sala)
            ->with('cama', $cama)
            ->with('tiposUgcc', $tiposUgcc);
    }

    public function derivarPacienteStore(Request $request)
    {
        $this->validate($request, [
            'fechaHospitalizacion' => 'nullable|date',
            'fechaSolicitud' => 'required|date',
            'fechaDeri' => 'required|date',
            'unidadQderiva' => 'nullable|string',
            'tipoTraslado' => 'nullable|string',
            'idMedico' => 'nullable|numeric',
            'centroDer' => 'nullable|string',

            'otroCentro' => 'nullable|string',
            'origenDerivacion' => 'nullable|string',
            'idaDerivacion' => 'nullable|string',
            'fechaIda' => 'nullable|date',
            'comentarios' => 'nullable|string',
            'destino' => 'required|numeric',
            'rescateDerivacion' => 'nullable|string',
            'fechaResc' => 'nullable|date',
            'etario' => 'nullable|string',
            'viaTras' => 'nullable|string',
            'trasladoT' => 'nullable|string',
            'movil' => 'nullable|numeric',
            'compraSer' => 'nullable|string',
            'ciaoDesc' => 'nullable|string',
            'idCaso' => 'required|numeric',
            'diagnosticoClinico' => 'nullable|string',
            'tipoCentroPublica' => 'nullable|string',
            'tipoCEntroPrivada' => 'nullable|string',

        ]);
        $nuevo = new Derivacion();
        $nuevo->usuario = Auth::id();
        $nuevo->fecha = $request->fechaSolicitud;
        $nuevo->fecha_cierre = $request->fechaDeri;
        $nuevo->caso = $request->idCaso;
        $nuevo->motivo_cierre = $request->motivoDerivacion;
        $nuevo->comentario = $request->comentarios;
        $nuevo->destino = $request->destino;
        $nuevo->establecimiento = $request->origenDerivacion;
        $nuevo->revisada = null;
        $nuevo->establecimiento = $request->centroDer;
        $nuevo->destino = $request->destino;
        $urlxx = $request->url;
        $request->urlxx = $urlxx;

        (new FormularioDerivacionController)->formularioDerivacionStore($request);

        //return response()->json(["exito" => "El paciente ha sido derivado"]);
        if ($nuevo->save()) {

            return back()->with('msj', 'derivado');
        } else {
            return redirect();
        }

        //return redirect()->back('unidad', [$request->url])->with('alert-success', 'The data was saved successfully');

    }

    public function unidadesEnEstablecimiento($id)
    {

        $ue = DB::table('unidades_en_establecimientos')
            ->where('establecimiento', $id)
            ->get();
        return json_encode($ue);
    }

}
