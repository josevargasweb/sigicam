<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <body>

        <div class="row">
            <h4>SIGICAM - REPORTE DE PACIENTES</h4>	
        </div>

        
        <div class="row">
            <table>
                <thead>
                    <tr>
                        <td colspan="5">
                            <b>Establecimiento: {{ $establecimiento->nombre }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <b>Fecha Actual : {{\Carbon\Carbon::parse($hoy)->format('d/m/Y H:i')}}</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <b>Fecha Solicitud: {{\Carbon\Carbon::parse($fecha_solicitud)->format('d/m/Y')}}</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <b>Turno de {{$horarioMañana}}</b>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
        <br>
        <div class="row">

            <table>
                <thead>
                    <tr>
                        <th class='azulMarino' colspan="4" rowspan="2">ENTREGA DE TURNO UNIDAD GESTIÓN DE PACIENTES</th>
                        <th style="font-size: 30px;" class='azulMarino' colspan="2" rowspan="2">LARGA</th>
                        <th class='azulMarino' colspan="3" rowspan="2">N° PCTES INGRESADOS ESPERANDO CAMA</th>
                        <th class='azulMarino' rowspan="2">UNIDAD EMERGENCIA HOSPITALARIA</th>
                        <th class='azulMarino' rowspan="2">UNIDAD DE RECUPERACIÓN</th>
                        <th class='azulMarino' rowspan="2">INGRESOS POLI</th>
                        <th style="background-color: #203864; color: #FFF;" rowspan="2">INGRESOS TOTALES DEL DÍA UEH</th>
                        <th class='azulMarino' rowspan="2">INGRESOS HOSPITAL MODULAR</th>
                    </tr>
                    <tr>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class='azulMarino' colspan="3">ENFERMERA/O DE TURNO UGP</td>
                        <td class='azulMarino' colspan="3"></td>
                        <td class='azulMarino' colspan="3">U. ADULTO</td>
                        <td class='azulMarino'>{{$ueh_1_adulto}}</td>
                        <td class='azulMarino'>{{$ur_1_adulto}}</td>
                        <td class='azulMarino'>{{$ip_1_adulto}}</td>
                        <td style="background-color: #203864; color: #FFF;">{{$it_ueh_1_adulto}}</td>
                        <td class='azulMarino'></td>
                    </tr>
                    <tr>
                        <td class='azulMarino' colspan="3">ENFERMERA/O JEFE TURNO UEH</td>
                        <td class='azulMarino' colspan="3"></td>
                        <td class='azulMarino' colspan="3">PEDIATRIA</td>
                        <td class='azulMarino'>{{$ueh_1_pediatria}}</td>
                        <td class='azulMarino'>{{$ur_1_pediatria}}</td>
                        <td class='azulMarino'>{{$ip_1_pediatria}}</td>
                        <td style="background-color: #203864; color: #FFF;">{{$it_ueh_1_pediatria}}</td>
                        <td class='azulMarino'></td>
                    </tr>
                    <tr>
                        <td class='azulMarino' colspan="3">MÉDICO JEFE TURNO UEH</td>
                        <td class='azulMarino' colspan="3"></td>
                        <td class='azulMarino' colspan="3">TOTAL</td>
                        <td class='azulMarino'>{{$ueh_1_adulto + $ueh_1_pediatria}}</td>
                        <td class='azulMarino'>{{$ur_1_adulto + $ur_1_pediatria}}</td>
                        <td class='azulMarino'>{{$ip_1_adulto + $ip_1_pediatria}}</td>
                        <td style="background-color: #203864; color: #FFF;">{{$it_ueh_1_adulto + $it_ueh_1_pediatria}}</td>
                        <td class='azulMarino'></td>
                    </tr>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <td class='azulMarino' colspan="2">SERVICIO</td>
                        <td class='azulMarino'>N° CAMAS DE DOTACION</td>
                        <td class='azulMarino'>N° CAMAS HABILITADAS</td>
                        <td class='azulMarino'>N° CAMAS OCUPADAS</td>
                        <td class='azulMarino'>N° CAMAS DISPONIBLES RECIBIDAS</td>
                        <td class='azulMarino'>N° CAMAS BLOQUEADAS RECIBIDAS</td>
                        <td class='azulMarino'>CAMAS DESBLOQUEADAS</td>
                        <td class='azulMarino'>CAMAS BLOQUEADAS EN EL TURNO</td>
                        <td class='azulMarino'>ALTAS</td>
                        <td class='azulMarino'>TRASLADOS</td>
                        <td class='azulMarino'>INGRESOS</td>
                        <td class='azulMarino'>FALLECIDOS</td>
                        <td class='azulMarino'>N° DE CAMAS OCUPADAS</td>
                        <td class='azulMarino'>N° CAMAS DISPONIBLES</td>
                        <td class='azulMarino'>N° CAMAS BLOQUEADAS</td>
                        <td class='azulMarino'>N° TOTAL DE CAMAS</td>
                    </tr>
                </thead>
                <tbody>
                    {{ $totalCamasDotacion = 0}}
                    {{ $totalCamasHabilitadasRecibidas = 0}}
                    {{ $totalCamasOcupadasRecibidas = 0 }}
                    {{ $totalCamasDisponiblesRecibidas = 0 }}
                    {{ $totalCamasBloqueadasRecibidas = 0 }}
                    {{ $totalCamasDesbloqueadasActual = 0 }}
                    {{ $totalCamasBloqueadasActual = 0 }}
                    {{ $totalAltas = 0 }}
                    {{ $totalTralados = 0 }}
                    {{ $totalIngresos = 0 }}
                    {{ $totalFallecidos = 0 }}
                    {{ $totalNumCamasOcupadas = 0 }}
                    {{ $totalNumCamasDisponibles = 0 }}
                    {{ $totalNumCamasBloqueadas = 0 }}
                    {{ $totalNumCamas = 0 }}

                    {{$porcentajeDeOcupacion = 0}}
                    {{$porcentajeCamasDisponibles = 0}}
                    {{$porcentajeCamasBloqueadas = 0}}

                    @foreach($resumenTurnoUno as $t)
                        {{ $totalCamasDotacion += $t->dotacion}}
                        {{ $totalCamasHabilitadasRecibidas += $t->camas_habilitadas}}
                        {{ $totalCamasOcupadasRecibidas += $t->camas_ocupadas_recibidas }}
                        {{ $totalCamasDisponiblesRecibidas += $t->camas_disponibles_recibidas }}
                        {{ $totalCamasBloqueadasRecibidas += $t->camas_bloqueadas_recibidas }}
                        {{ $totalCamasDesbloqueadasActual += $t->camas_desbloqueadas }}
                        {{ $totalCamasBloqueadasActual += $t->bloqueadas_actual }}
                        {{ $totalAltas += $t->altas }}
                        {{ $totalTralados += $t->traslados }}
                        {{ $totalIngresos += $t->ingresos }}
                        {{ $totalFallecidos += $t->fallecidos }}
                        {{ $totalNumCamasOcupadas += $t->test_ocupadas }}
                        {{ $totalNumCamasDisponibles += $t->test_disponibles }}
                        {{ $totalNumCamasBloqueadas += $t->test_bloqueadas }}
                        {{ $totalNumCamas += $t->test_total }}

                        @if($totalNumCamasOcupadas == 0 || $totalNumCamas == 0)
                            0
                        @else   
                            {{$porcentajeDeOcupacion = ($totalNumCamasOcupadas * 100) / $totalNumCamas}}
                        @endif

                        @if($totalNumCamasDisponibles == 0 || $totalNumCamas == 0)
                            0
                        @else   
                        {{$porcentajeCamasDisponibles = ($totalNumCamasDisponibles * 100) / $totalNumCamas}}
                        @endif

                        @if($totalNumCamasBloqueadas == 0 || $totalNumCamas == 0)
                            0
                        @else   
                            {{$porcentajeCamasBloqueadas = ($totalNumCamasBloqueadas * 100) / $totalNumCamas}}
                        @endif
                        
                        <tr>
                            <td class='azulMarino' colspan="2">{{$t->alias}}</td>
                            <td class='azulMarino' colspan="1">{{$t->dotacion}}</td>
                            <td style="background-color: #d9e2f3; color: #000;" colspan="1">{{$t->camas_habilitadas}}</td>
                            <td style="background-color: #d9e2f3; color: #000;" colspan="1">{{$t->camas_ocupadas_recibidas}}</td>
                            <td style="background-color: #d9e2f3; color: #000;" colspan="1">{{$t->camas_disponibles_recibidas}}</td>
                            <td style="background-color: #d9e2f3; color: #000;" colspan="1">{{$t->camas_bloqueadas_recibidas}}</td>
                            <td colspan="1">{{$t->camas_desbloqueadas}}</td>
                            <td colspan="1">{{$t->bloqueadas_actual}}</td>
                            <td colspan="1">{{$t->altas}}</td>
                            <td colspan="1">{{$t->traslados}}</td>
                            <td colspan="1">{{$t->ingresos}}</td>
                            <td colspan="1">{{$t->fallecidos}}</td>
                            <td style="background-color: #bcd6ee; color: #000;" colspan="1">{{$t->test_ocupadas}}</td>
                            <td style="background-color: #ffda65; color: #000;" colspan="1">{{$t->test_disponibles}}</td>
                            <td style="background-color: #f8cbac; color: #000;" colspan="1">{{$t->test_bloqueadas}}</td>
                            <td class='azulMarino' colspan="1">{{$t->test_total}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class='azulMarino' colspan="2">TOTAL</td>
                        <td class='azulMarino'>{{$totalCamasDotacion}}</td>
                        <td class='azulMarino'>{{$totalCamasHabilitadasRecibidas}}</td>
                        <td class='azulMarino'>{{$totalCamasOcupadasRecibidas}}</td>
                        <td class='azulMarino'>{{$totalCamasDisponiblesRecibidas}}</td>
                        <td class='azulMarino'>{{$totalCamasBloqueadasRecibidas}}</td>
                        <td class='azulMarino'>{{$totalCamasDesbloqueadasActual}}</td>
                        <td class='azulMarino'>{{$totalCamasBloqueadasActual}}</td>
                        <td class='azulMarino'>{{$totalAltas}}</td>
                        <td class='azulMarino'>{{$totalTralados}}</td>
                        <td class='azulMarino'>{{$totalIngresos}}</td>
                        <td class='azulMarino'>{{$totalFallecidos}}</td>
                        <td class='azulMarino'>{{$totalNumCamasOcupadas}}</td>
                        <td class='azulMarino'>{{$totalNumCamasDisponibles}}</td>
                        <td class='azulMarino'>{{$totalNumCamasBloqueadas}}</td>
                        <td class='azulMarino'>{{$totalNumCamas}}</td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td style="background-color: #FFC000; color: #000;">NUMERO CAMAS OCUPADAS</td>
                        <td style="background-color: #ffc000; color: #000;">{{$totalNumCamasOcupadas}}</td>
                        <td style="background-color: #ffc000; color: #000;">PORCENTAJE DE OCUPACIÓN</td>
                        <td style="background-color: #ffc000; color: #000;">{{str_replace(".",",",round($porcentajeDeOcupacion,1))}}%</td>
                        <td style="background-color: #ffc000; color: #000;">NÚMERO CAMAS BASICAS DISPONIBLES</td>
                        <td style="background-color: #ffc000; color: #000;">{{$camasBasicasUno}}</td>
                        <td style="background-color: #ffc000; color: #000;">TOTAL CAMA CRÍTICA DISPONIBLE</td>
                        <td style="background-color: #ffc000; color: #000;">{{$camasCriticasUno}}</td>
                        <td style="background-color: #ffc000; color: #000;">NÚMERO TOTAL PACIENTES ESPERANDO CAMA</td>
                        <td style="background-color: #ffc000; color: #000;">{{$pacientesEsperaUno}}</td>
                        <td style="background-color: #ffc000; color: #000;">PORCENTAJE CAMAS DISPONIBLES</td>
                        <td style="background-color: #ffc000; color: #000;">{{str_replace(".",",",round($porcentajeCamasDisponibles,1))}}%</td>
                        <td style="background-color: #ffc000; color: #000;">PORCENTAJE CAMAS BLOQUEADAS</td>
                        <td style="background-color: #ffc000; color: #000;">{{str_replace(".",",",round($porcentajeCamasBloqueadas,1))}}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <table>
                <thead>
                    <tr>
                        <td colspan="4" style="background-color: #2f5497;"></td>
                        <td colspan="11" style="background-color: #ffc000; color: #000; text-align: center;">ESPERA DE HOSPITALIZACIÓN</td>
                        <td colspan="1" style="background-color: #2f5497;"></td>
                    </tr>
                    <tr>
                        <td class='azulMarino'>NÚMERO DE INGRESO DEL DIA</td>
                        <td class='azulMarino'>ORIGEN</td>
                        <td class='azulMarino'>SERVICIO DESTINO</td>
                        <td class='azulMarino'>NÚMERO DE CAMA</td>
                        <td class='azulMarino'>ESPECIALIDAD</td>
                        <td class='azulMarino'>NOMBRE DEL PACIENTE</td>
                        <td class='azulMarino'>SEXO</td>
                        <td class='azulMarino'>EDAD</td>
                        <td class='azulMarino'>RUT</td>
                        <td class='azulMarino'>DIAGNOSTICO</td>
                        <td class='azulMarino'>OBSERVACIONES</td>
                        <td class='azulMarino'>FECHA DE SOLICITUD</td>
                        <td class='azulMarino'>HORA DE SOLICITUD</td>
                        <td class='azulMarino'>FECHA DE ASIGNACIÓN</td>
                        <td class='azulMarino'>HORA DE ASIGNACIÓN</td>
                        <td class='azulMarino'>DIFERENCIA SOLICITAR - ASIGNAR</td>
                        {{-- <td class='azulMarino'>FECHA DE HOSPITALIZACIÓN</td>
                        <td class='azulMarino'>HORA DE HOSPITALIZACIÓN</td>
                        <td class='azulMarino'>DIFERENCIA ASIGNAR - HOSPITALIZAR</td> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($response["transito"] as $n)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$n["origen"]}}</td>
                            <td>{{$n["servicioDestino"]}}</td>
                            <td>{{$n["cama"]}}</td>
                            <td>{{$n["especialidad"]}}</td>
                            <td>{{$n["nombrePaciente"]}}</td>
                            <td>{{$n["sexo"]}}</td>
                            <td>{{$n["edad"]}}</td>
                            <td>{{$n["rut"]}}</td>
                            <td>{!!$n["diagnostico"]!!}</td>
                            <td>{!!$n["observacion"]!!}</td>
                            <td>{{$n["fechaSolicitud"]}}</td>
                            <td>{{$n["horaSolicitud"]}}</td>
                            <td>{{$n["fechaAsignacion"]}}</td>
                            <td>{{$n["horaAsignacion"]}}</td>
                            <td>{{$n["dif_solicitar_asignar"]}}</td>
                            {{-- <td>{{$n["fechaHospitalizacion"]}}</td>
                            <td>{{$n["horaHospitalizacion"]}}</td>
                            <td>{{$n["dif_asignar_hospitalizar"]}}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>   
        </div>
        <br>
        <div class="row">
            <table>
                <thead>
                    <tr>
                        <td colspan="4" style="background-color: #2f5497;"></td>
                        <td colspan="11" style="background-color: #ffc000; color: #000; text-align: center;">HOSPITALIZADOS</td>
                        <td colspan="4" style="background-color: #2f5497;"></td>
                    </tr>
                    <tr>
                        <td class='azulMarino'>NÚMERO DE INGRESO DEL DIA</td>
                        <td class='azulMarino'>ORIGEN</td>
                        <td class='azulMarino'>SERVICIO DESTINO</td>
                        <td class='azulMarino'>NÚMERO DE CAMA</td>
                        <td class='azulMarino'>ESPECIALIDAD</td>
                        <td class='azulMarino'>NOMBRE DEL PACIENTE</td>
                        <td class='azulMarino'>SEXO</td>
                        <td class='azulMarino'>EDAD</td>
                        <td class='azulMarino'>RUT</td>
                        <td class='azulMarino'>DIAGNOSTICO</td>
                        <td class='azulMarino'>OBSERVACIONES</td>
                        <td class='azulMarino'>FECHA DE SOLICITUD</td>
                        <td class='azulMarino'>HORA DE SOLICITUD</td>
                        <td class='azulMarino'>FECHA DE ASIGNACIÓN</td>
                        <td class='azulMarino'>HORA DE ASIGNACIÓN</td>
                        <td class='azulMarino'>DIFERENCIA SOLICITAR - ASIGNAR</td>
                        <td class='azulMarino'>FECHA DE HOSPITALIZACIÓN</td>
                        <td class='azulMarino'>HORA DE HOSPITALIZACIÓN</td>
                        <td class='azulMarino'>DIFERENCIA ASIGNAR - HOSPITALIZAR</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($response["hospitalizados"] as $n)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$n["origen"]}}</td>
                            <td>{{$n["servicioDestino"]}}</td>
                            <td>{{$n["cama"]}}</td>
                            <td>{{$n["especialidad"]}}</td>
                            <td>{{$n["nombrePaciente"]}}</td>
                            <td>{{$n["sexo"]}}</td>
                            <td>{{$n["edad"]}}</td>
                            <td>{{$n["rut"]}}</td>
                            <td>{!!$n["diagnostico"]!!}</td>
                            <td>{!!$n["observacion"]!!}</td>
                            <td>{{$n["fechaSolicitud"]}}</td>
                            <td>{{$n["horaSolicitud"]}}</td>
                            <td>{{$n["fechaAsignacion"]}}</td>
                            <td>{{$n["horaAsignacion"]}}</td>
                            <td>{{$n["dif_solicitar_asignar"]}}</td>
                            <td>{{$n["fechaHospitalizacion"]}}</td>
                            <td>{{$n["horaHospitalizacion"]}}</td>
                            <td>{{$n["dif_asignar_hospitalizar"]}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>   
        </div>
        <br>
        <div class="row">
            <table>
                <thead>
                    <tr>
                        <td colspan="4" style="background-color: #2f5497;"></td>
                        <td colspan="11" style="background-color: #ffc000; color: #000; text-align: center;">TRASLADOS</td>
                        <td colspan="4" style="background-color: #2f5497;"></td>
                    </tr>
                    <tr>
                        <td class='azulMarino'>NÚMERO DE INGRESO DEL DIA</td>
                        <td class='azulMarino'>SERVICIO ORIGEN</td>
                        <td class='azulMarino'>SERVICIO DESTINO</td>
                        <td class='azulMarino'>NÚMERO DE CAMA DESTINO</td>
                        <td class='azulMarino'>ESPECIALIDAD</td>
                        <td class='azulMarino'>NOMBRE DEL PACIENTE</td>
                        <td class='azulMarino'>SEXO</td>
                        <td class='azulMarino'>EDAD</td>
                        <td class='azulMarino'>RUT</td>
                        <td class='azulMarino'>DIAGNOSTICO</td>
                        <td class='azulMarino'>OBSERVACIONES</td>
                        <td class='azulMarino'>FECHA DE SOLICITUD</td>
                        <td class='azulMarino'>HORA DE SOLICITUD</td>
                        <td class='azulMarino'>FECHA DE ASIGNACIÓN</td>
                        <td class='azulMarino'>HORA DE ASIGNACIÓN</td>
                        <td class='azulMarino'>DIFERENCIA SOLICITAR - ASIGNAR</td>
                        <td class='azulMarino'>FECHA DE HOSPITALIZACIÓN</td>
                        <td class='azulMarino'>HORA DE HOSPITALIZACIÓN</td>
                        <td class='azulMarino'>DIFERENCIA ASIGNAR - HOSPITALIZAR</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($response["traslados"] as $n)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$n["origen"]}}</td>
                            <td>{{$n["servicioDestino"]}}</td>
                            <td>{{$n["cama"]}}</td>
                            <td>{{$n["especialidad"]}}</td>
                            <td>{{$n["nombrePaciente"]}}</td>
                            <td>{{$n["sexo"]}}</td>
                            <td>{{$n["edad"]}}</td>
                            <td>{{$n["rut"]}}</td>
                            <td>{!!$n["diagnostico"]!!}</td>
                            <td>{{$n["observacion"]}}</td>
                            <td>{{$n["fechaSolicitud"]}}</td>
                            <td>{{$n["horaSolicitud"]}}</td>
                            <td>{{$n["fechaAsignacion"]}}</td>
                            <td>{{$n["horaAsignacion"]}}</td>
                            <td>{{$n["dif_solicitar_asignar"]}}</td>
                            <td>{{$n["fechaHospitalizacion"]}}</td>
                            <td>{{$n["horaHospitalizacion"]}}</td>
                            <td>{{$n["dif_asignar_hospitalizar"]}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>   
        </div>
    </body>
    <style>
        td, th {
            vertical-align: top;
            text-align: left;
        }
        table, th, td {
            border: 1px solid #fff;
        }
        .azulMarino {
            background-color: #2f5497; color: #fff;
        }
    </style>
</html>
