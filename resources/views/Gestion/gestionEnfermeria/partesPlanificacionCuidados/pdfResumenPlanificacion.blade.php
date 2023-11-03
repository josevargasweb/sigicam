<!DOCTYPE HTML>
<html>
<head>
    <title>Resumen planificación de cuidados</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        
        .centrar{
          text-align:center;
        }

        table, td {
            border: 1px solid black;
        }

        .titulo {
            font-size: 14px;
        }

        .letra{
            font-size: 12px;
            margin-top: 0px;
        }

        .letra12{
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .letra10Header{
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .letra10Body{
            font-size: 10px;
        }

        .letra10Body:first-letter{
            text-transform: capitalize;
        }

        .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
            float: left;
            padding: 0px !important;
        }
        .col-md-12 {
            width: 100%;
        }
        .col-md-11 {
            width: 91.66666667%;
        }
        .col-md-10 {
            width: 83.33333333%;
        }
        .col-md-9 {
            width: 75%;
        }
        .col-md-8 {
            width: 66.66666667%;
        }
        .col-md-7 {
            width: 58.33333333%;
        }
        .col-md-6 {
            width: 50%;
        }
        .col-md-5 {
            width: 41.66666667%;
        }
        .col-md-4 {
            width: 33.33333333%;
        }
        .col-md-3 {
            width: 25%;
        }
        .col-md-2 {
            width: 16.66666667%;
        }
        .col-md-1 {
            width: 8.33333333%;
        }
        
        .col-md-offset-12 {
            margin-left: 100%;
        }
        .col-md-offset-11 {
            margin-left: 91.66666667%;
        }
        .col-md-offset-10 {
            margin-left: 83.33333333%;
        }
        .col-md-offset-9 {
            margin-left: 75%;
        }
        .col-md-offset-8 {
            margin-left: 66.66666667%;
        }
        .col-md-offset-7 {
            margin-left: 58.33333333%;
        }
        .col-md-offset-6 {
            margin-left: 50%;
        }
        .col-md-offset-5 {
            margin-left: 41.66666667%;
        }
        .col-md-offset-4 {
            margin-left: 33.33333333%;
        }
        .col-md-offset-3 {
            margin-left: 25%;
        }
        .col-md-offset-2 {
            margin-left: 16.66666667%;
        }
        .col-md-offset-1 {
            margin-left: 8.33333333%;
        }
        .col-md-offset-0 {
            margin-left: 0;
        }

        .col-md-offset-1 {
            margin-left: 40px;
        }

        td input[type="checkbox"] {
            text-align:center;
            display: flex;
            justify-content: center; 
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="centrar">
            <label class="titulo">RESUMEN DE PLANIFICACION DE CUIDADOS</label>
        </div>
        <br>

        <div class="row">
            <div style="text-align:center;" class="col-md-offset-3 col-md-3 letra">Inicio: <b>{{\Carbon\carbon::parse($fecha_inicio)->format('d-m-Y')}} 08:00:00</b></div>
            <div style="text-align:center;" class="col-md-3 letra">Fin: <b>{{\Carbon\carbon::parse($fecha_fin)->format('d-m-Y')}} 07:59:59</b></div>
        </div>

        <br>
        <div class="row" >
            <div class="col-md-3" style="">
                <b> {{Form::label('', "NOMBRE PACIENTE: ", array('class' => 'letra12' ))}} </b> 
                @if($paciente)
                    @if($paciente->nombre)
                        <p class="letra">{{$paciente->nombre}} {{$paciente->apellido_paterno}} {{$paciente->apellido_materno}}</p>
                    @else
                        <p class="letra">--</p>
                    @endif
                @endif
            </div>
            <div class="col-md-3" style="">
                <b> {{Form::label('', "RUT: ", array('class' => 'letra12' ))}} </b>
                @if($paciente)
                    @if($paciente->rut)
                    <p class="letra">{{$paciente->rut}}-{{$paciente->dv}}</p>
                    @else
                        <p class="letra">--</p>
                    @endif
                @endif
            </div>
            <div class="col-md-3" style="">
                <b> {{Form::label('', "FECHA NACIMIENTO: ", array( 'class' => 'letra12'))}} </b>
                @if($paciente)
                    @if($paciente->fecha_nacimiento)
                        <p class="letra">{{ Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d-m-Y') }} ({{ Carbon\Carbon::now()->diffInYears($paciente->fecha_nacimiento)}} AÑOS)</p>
                    @else
                        <p class="letra">--</p>
                    @endif
                @endif
            </div>
            <div class="col-md-3" style="">
                <b> {{Form::label('','NOMBRE SOCIAL:', array( 'class' => 'letra12'))}} </b>
                @if($paciente->nombre_social)
                    <p class="letra">{{strtoupper($paciente->nombre_social)}}</p>
                @else
                    <p class="letra">--</p>
                @endif
            </div>
        </div>

        
        <br><br><br>
        <div class="row" style="">
            <div class="col-md-3" style="">
                <b> {{Form::label('','SEXO:', array( 'class' => 'letra12'))}} </b>
                @if($paciente->sexo)
                    <p class="letra">{{strtoupper($paciente->sexo)}}</p>
                @else
                    <p class="letra">--</p>
                @endif
            </div>
            <div class="col-md-3" style="">
                <b> {{Form::label('','PREVISION:', array( 'class' => 'letra12'))}} </b>
                @if ($prevision)
                    <p class="letra">{{$prevision}}</p>
                @else
                    <p class="letra">--</p>
                @endif
            </div>
            <div class="col-md-3">  
                <b> {{Form::label('','TELEFONOS:', array( 'class' => 'letra12'))}}</b>
                @forelse ($telefonos as $item)
                    <p style="display: inline" class="letra">({{strtoupper($item->tipo)}}) {{$item->telefono}}. </p>
                @empty
                    <p class="letra">TELEFONO: SIN INFORMACION</p>
                    @endforelse
            </div>
        </div>
        
        <br><br><br>
        <legend class="letra12">CUIDADOS DE ENFERMERIA</legend>
        
        <div class="row">
            <legend class="letra10Body"><b class="letra12">{{\Carbon\carbon::parse($fecha_inicio)->format('d-m-Y')}}</b> </legend>{{-- 08:00:00 - 23:59:59 --}}
            <table class="table" style="width: fix">
                <tr style="background:#399865;">
                    <th style="width: 30%;" class="letra10Body centrar">Cuidado</th>
                    @foreach ($turnouno as $tu)
                        <th style="width:10%;" class="letra10Body centrar">{{$tu}}</th>
                    @endforeach
                </tr>
                @forelse ($atenciones_turno_uno as $key => $atencion)
                    <tr>
                        <td class="letra10Body">{{$atencion}}</td>
                        @for($i = 0; $i < 16; $i++)
                            @if(in_array((int) $turnouno[$i], $horarios_turno_uno[$key]))
                                @if($resposable_atencion_horario_uno[$key][(int)$turnouno[$i]] == 1)
                                    <td style="background-color: #0062cc;" class="letra10Body">
                                        
                                    </td>
                                @elseif($resposable_atencion_horario_uno[$key][(int)$turnouno[$i]] == 2)
                                    <td style="background-color:#A3B5FD;" class="letra10Body">
                                        
                                    </td>
								@elseif($resposable_atencion_horario_uno[$key][(int)$turnouno[$i]] == 3)
                                    <td style="background-color:#8D121D;" class="letra10Body">
                                        
                                    </td>
                                @else
                                    {{-- Significa que no tiene encargado pero si fue planificado --}}
                                    <td style="background-color:#026332;" class="letra10Body">
                                        
                                    </td>
                                @endif
                            @else
                                <td>
                                    
                                </td>
                            @endif
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="17" style="text-align: center" class="letra10Body">Sin información</td>
                    </tr>
                @endforelse
            </table>
        </div>
        
        <div class="row">
            <legend class="letra12"><b>{{\Carbon\carbon::parse($fecha_fin)->format('d-m-Y')}}</b> {{-- 00:00:00 - 07:59:59 --}}</legend>
            <table class="table" style="width: fix">
                <tr style="background:#399865;">
                    <th style="width: 30%;" class="letra10Body centrar">Cuidado</th>
                    @foreach ($turnodos as $td)
                        <th style="width:10%;" class="letra10Body centrar">{{$td}}</th>
                    @endforeach
                </tr>
                @forelse ($atenciones_turno_dos as $key => $atencion)
                    <tr>
                        <td class="letra10Body">{{$atencion}}</td>
                        @for($i = 0; $i < 8; $i++)
                            @if(in_array((int) $turnodos[$i], $horarios_turno_dos[$key]))
                                @if($resposable_atencion_horario_dos[$key][(int)$turnodos[$i]] == 1)
                                    <td style="background-color: #0062cc;" class="letra10Body">
                                        
                                    </td>
                                @elseif($resposable_atencion_horario_dos[$key][(int)$turnodos[$i]] == 2)
                                    <td style="background-color:#A3B5FD;" class="letra10Body">
                                        
                                    </td>
								@elseif($resposable_atencion_horario_dos[$key][(int)$turnodos[$i]] == 3)
                                    <td style="background-color:#8D121D;" class="letra10Body">
                                        
                                    </td>
                                @else
                                    {{-- Significa que no tiene encargado --}}
                                    <td style="background-color:#026332;" class="letra10Body">
                                        
                                    </td>
                                @endif
                            @else
                                <td>
                                    
                                </td>
                            @endif
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center" class="letra10Body">Sin información</td>
                    </tr>
                @endforelse
            </table>
        </div>
        <br>
        <legend class="letra12">INDICACIONES Y MEDICAMENTOS</legend>
        <br>
        <div class="row">
            <legend class="letra12"><b>{{\Carbon\carbon::parse($fecha_inicio)->format('d-m-Y')}}</b> {{-- 08:00:00 - 23:59:59 --}}</legend>
            <table class="table" style="width: fix">
                <tr style="background:#399865;">
                    <th style="width: 30%;" class="letra10Body centrar">Indicación o medicamento</th>
                    @foreach ($turnouno as $tu)
                        <th style="width:10%;" class="letra10Body centrar">{{$tu}}</th>
                    @endforeach
                </tr>
                @forelse ($indicaciones_turno_uno as $key => $indicacion_uno)
                    <tr>
                        <td class="letra10Body">
                            <b>({{$indicacion_uno[1]}})</b><br>
                            @if($indicacion_uno[1] == "Medicamento" )
                                {{$indicacion_uno[3]}}<br>
                                <b>Dosis:</b> {{$indicacion_uno[4]}} <b>Vía:</b> {{$indicacion_uno[5]}}
                            @else
                                {{$indicacion_uno[2]}} 
                            @endif
                            
                        </td>
                        @for($i = 0; $i < 16; $i++)
                            @if(in_array((int) $turnouno[$i], $horarios_indicaciones_turno_uno[$key]))
                                @if($responsable_indicacion_turno_uno[$key][(int)$turnouno[$i]] == 1)
                                    <td style="background-color: #0062cc;" class="letra10Body">
                                    </td>
                                @elseif($responsable_indicacion_turno_uno[$key][(int)$turnouno[$i]] == 2)
                                    <td style="background-color:#A3B5FD;" class="letra10Body">
                                    </td>
								@elseif($responsable_indicacion_turno_uno[$key][(int)$turnouno[$i]] == 3)
                                    <td style="background-color:#8D121D;" class="letra10Body">
                                    </td>
                                @else
                                    {{-- Significa que no tiene encargado pero si fue planificado --}}
                                    <td style="background-color:#026332;" class="letra10Body">
                                    </td>
                                @endif
                            @else
                                <td>
                                </td>
                            @endif
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="17" style="text-align: center" class="letra10Body">Sin información</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <div class="row">
            <legend class="letra12"><b>{{\Carbon\carbon::parse($fecha_fin)->format('d-m-Y')}}</b> {{-- 00:00:00 - 07:59:59 --}}</legend>
            <table class="table" style="width: fix">
                <tr style="background:#399865;">
                    <th style="width: 30%;" class="letra10Body centrar">Indicación o medicamento</th>
                    @foreach ($turnodos as $td)
                        <th style="width:10%;" class="letra10Body centrar">{{$td}}</th>
                    @endforeach
                </tr>
                @forelse ($indicaciones_turno_dos as $key => $indicacion_dos)
                    <tr>
                        <td class="letra10Body">
                            <b>({{$indicacion_dos[1]}})</b><br>
                            @if($indicacion_dos[1] == "Medicamento" )
                                {{$indicacion_dos[3]}} <br>
                                <b>Dosis:</b> {{$indicacion_dos[4]}} <b>Vía:</b> {{$indicacion_dos[5]}}
                            @else
                                {{$indicacion_dos[2]}} 
                            @endif
                        </td>
                        @for($i = 0; $i < 8; $i++)
                            @if(in_array((int) $turnodos[$i], $horarios_indicaciones_turno_dos[$key]))
                                @if($responsable_indicacion_turno_dos[$key][(int)$turnodos[$i]] == 1)
                                    <td style="background-color: #0062cc;" class="letra10Body">
                                        
                                    </td>
                                @elseif($responsable_indicacion_turno_dos[$key][(int)$turnodos[$i]] == 2)
                                    <td style="background-color:#A3B5FD;" class="letra10Body">
                                    </td>
								@elseif($responsable_indicacion_turno_dos[$key][(int)$turnodos[$i]] == 3)
                                    <td style="background-color:#8D121D;" class="letra10Body">
                                    </td>
                                @else
                                    {{-- Significa que no tiene encargado --}}
                                    <td style="background-color:#026332;" class="letra10Body">
                                    </td>
                                @endif
                            @else
                                <td>
                                </td>
                            @endif
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center" class="letra10Body">Sin información</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <br>
        <div class="row">
            <legend class="letra12">NOVEDADES</legend>
            <table class="table" style="width: fix; table-layout:fixed;">
                <thead>
                    <tr style="background:#399865;" class="letra10Body">
                        <th style="width: 20%;">USUARIO</th>
                        <th style="width: 80%;">NOVEDAD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($novedades as $novedad)
                        <tr class="letra10Body">
                            <td>{!!$novedad[0]!!}</td>
                            <td style="width:250px; Word-wrap: break-Word;">{{$novedad[1]}}</td>
                        </tr>  
                    @empty    
                        <tr>
                            <td colspan="2" style="text-align: center" class="letra10Body">Sin información</td>
                        </tr>  
                    @endforelse
                </tbody>
            </table> 
        </div>
    </div>
</body>


</html>