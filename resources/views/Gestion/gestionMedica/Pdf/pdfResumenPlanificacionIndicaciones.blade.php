<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{ HTML::style('css/bootstrap.css') }}
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

        td {text-align: center;}

        /* p {text-align:center;} */
    </style>
</head>
<body>
    <div class="container">

        <div class="centrar">
            <label class="titulo">RESUMEN DE INDICACIONES</label>
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
        
        <br>
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
        
        <br>
        <legend class="letra12">INDICACIONES</legend>
        
        <div class="row">
            <table class="table" style="width: 100%;">
                <thead>
                    <tr style="background:#399865;">
                        <th style="width: 10%;" class="letra10Body centrar">Indicaciones</th>
                        @for($i = 1; $i <= $dias_del_mes; $i++)
                            @php
                                $width = 100 / $dias_del_mes;
                            @endphp
                            <th style="width:{{$width}}" class="letra10Body centrar">{{$i}}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @if ($info_reposo)
                    <tr>
                        <td>Reposo</td>
                        @for($i = 1; $i <= $dias_del_mes; $i++)
                            @if($info_reposo[$i] == "x")
                            <td style="background-color: green; color: white;">{{isset($detalle_reposo[$i]) ? $detalle_reposo[$i] : ''}}</td>
                            @else
                                <td style="background-color:white;"></td>    
                            @endif
                        @endfor
                    </tr>
                    @endif
                     @if ($info_regimen)
                        <tr>
                            <td>Regimen</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_reposo[$i] == "x")
                                    <td style="background-color:green;"></td>
                                @else
                                    <td style="background-color:white;"></td>    
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_signos_vitales)
                        <tr>
                            <td>Signos vitales</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_signos_vitales[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_signos_vitales[$i]) ? $detalle_signos_vitales[$i] : ''}} c/Hr</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_hemoglucotest)
                        <tr>
                            <td>Hemoglucotest</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_hemoglucotest[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_hemoglucotest[$i]) ? $detalle_hemoglucotest[$i] : ''}} c/Hr</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_oxigeno)
                        <tr>
                            <td>Oxigeno saturación</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_oxigeno[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_oxigeno[$i]) ? $detalle_oxigeno[$i] : ""}} %</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_suero)
                        <tr>
                            <td>Suero</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_suero[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_suero[$i]) ? $detalle_suero[$i] : ""}} ML</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_farmacos)
                        <tr>
                            <td>Farmaco</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_farmacos[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_farmaco[$i]) ? $detalle_farmaco[$i] : ""}}</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_atencion_terapeutica)
                        <tr>
                            <td>Atención terapeutica</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_atencion_terapeutica[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_atencion_terapeutica[$i]) ? $detalle_atencion_terapeutica[$i] : ""}}</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                    @if ($info_prevension_trombosis)
                        <tr>
                            <td>Prevención trombosis</td>
                            @for($i = 1; $i <= $dias_del_mes; $i++)
                                @if($info_prevension_trombosis[$i] == "x")
                                    <td style="background-color: green; color: white;">{{isset($detalle_prevension_trombosis[$i]) ? $detalle_prevension_trombosis[$i] : ""}}</td>
                                @else
                                    <td style="background-color: white;"></td>  
                                @endif
                            @endfor
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="row" style="font-size: 12px;">
            <label for="" style="margin-right: 5px;"> *** </label>
            <label for="" style="margin-right: 5px;">A: Absoluto</label>
            <label for="" style="margin-right: 5px;">S: Semisentado</label>
            <label for="" style="margin-right: 5px;">R: Relativo</label>
            <label for="" style="margin-right: 5px;">R-A: Relativo asistido</label>
            <label for="" style="margin-right: 5px;">O: Otro</label> 
        </div>
        <div class="row" style="font-size: 12px;">
            <label for="" style="margin-right: 5px;"> *** </label><label for="" style="margin-right: 5px;"> N/A: No aplica</label><label for="" style="margin-right: 5px;">P: Padua realizado</label><label for="" style="margin-right: 5px;">C: Caprini realizado</label> 
        </div>
        <div class="row">
            @foreach ($info_indicaciones as $key => $info_ind)
                <ul style="text-align: center;" class="list-group">
                    <li class="list-group-item list-group-item-info">Indicacion {{$key + 1}} - {{$info_ind["fecha_inicio"]}} - {{$info_ind["fecha_vigencia"]}} - {{$info_ind["usuario"]}}</li>
                    {{-- <li class="list-group-item list-group-item-info">{{$info_ind["usuario"]}}</li> --}}
                </ul>
            @endforeach
        </div>
    </div>
</body>
</html>