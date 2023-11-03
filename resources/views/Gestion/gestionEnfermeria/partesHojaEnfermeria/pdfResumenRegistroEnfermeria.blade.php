<!DOCTYPE HTML>
<html>
<head>
    <title>Resumen Registro Enfermeria</title>
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

        /* .box3{
            display: inline-block;
        } */

        .letra{
            font-size: 12px;
            margin-top: 0px;
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
            <label class="titulo">RESUMEN REGISTRO ENFERMERIA</label>
        </div>
        <br>
        
        <div class="row">
            <div style="text-align:center;" class="col-md-offset-3 col-md-3 letra">Inicio: <b>{{\Carbon\carbon::parse($fecha)->format('d-m-Y')}} 00:00:00</b></div>
            <div style="text-align:center;" class="col-md-3 letra">Fin: <b>{{\Carbon\carbon::parse($fecha)->format('d-m-Y')}} 23:59:59</b></div>
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
    </div>
        
    <br><br><br>
    <div class="container-fluid">   
        <div class="row">
            <legend class="letra12">CUIDADOS ENFERMERIA REALIZADOS</legend>
            <br>
            <table class="table" style="width: 100%">
                <tr style="background:#399865;">
                    <th style="width: 30%;" class="centrar letra10Header">Cuidado</th>
                    @foreach ($horas as $hora)
                        <th style="width: 2.8%;" class="centrar letra10Header">{{$hora}}</th>
                    @endforeach
                </tr>
                @forelse ($todos_cuidados as $key => $cuidado)
                    <tr>
                        <td><p class="letra10Body">{{$cuidado}}</p></td>
                        @for ($i = 0; $i < 24; $i++)
                            @if(in_array((int) $horas[$i], $horarios_cuidados[$key]))
                                @if($responsables_cuidados[$key][(int)$horas[$i]] == "gestion_clinica")
                                    <td style="background-color: #0062cc;"></td>
                                @elseif($responsables_cuidados[$key][(int)$horas[$i]] == "tens")   
                                    <td style="background-color: #A3B5FD;"></td>
                                @elseif($responsables_cuidados[$key][(int)$horas[$i]] == "matrona")   
                                    <td style="background-color: #8D121D;"></td>    
                                @else
                                    <td style="background-color:#026332;"></td>
                                @endif
                            @else
                                <td></td>    
                            @endif
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="25" style="text-align: center"><p class="letra10Body">Sin Información</p></td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>    
        
    
        <br>
        <div class="row">
            <legend class="letra12">INDICACIONES O MEDICAMENTOS REALIZADOS</legend>
            <br>
            <table class="table" style="width: 100%">
                <tr style="background:#399865;">
                    <th style="width: 30%;" class="centrar letra10Header">Indicación o Medicamento</th>
                    @foreach ($horas as $hora)
                        <th style="width: 2.8%;" class="centrar letra10Header">{{$hora}}</th>
                    @endforeach
                </tr>
                @forelse ($todos_indicaciones as $key => $indicacion)
                    <tr>
                        @if($indicacion[0] == "Medicamento")
                            <td>
                                <p class="letra10Body"><b>{{$indicacion[0]}}: </b> {{$indicacion[1]}}<br></p>
                                <p class="letra10Body">(Dosis: {{$indicacion[2]}}, {{$indicacion[3]}})</p>
                            </td>
                        @else
                            <td>
                                <p class="letra10Body"><b>{{$indicacion[0]}}: </b><br></p>
                                <p class="letra10Body">{{$indicacion[4]}}</p>
                            </td>
                        @endif
                        
                        @for ($i = 0; $i < 24; $i++)
                            @if(in_array((int) $horas[$i], $horarios_indicaciones[$key]))
                                @if($responsables_indicaciones[$key][(int)$horas[$i]] == "gestion_clinica")
                                    <td style="background-color: #0062cc;"></td>
                                @elseif($responsables_indicaciones[$key][(int)$horas[$i]] == "tens")   
                                    <td style="background-color: #A3B5FD;"></td>
                                @elseif($responsables_indicaciones[$key][(int)$horas[$i]] == "matrona")   
                                    <td style="background-color: #8D121D;"></td>
                                @else
                                    <td style="background-color:#026332;"></td>
                                @endif
                            @else
                                <td></td>    
                            @endif
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="25" style="text-align: center"><p class="letra10Body">Sin Información</p></td>
                    </tr>
                @endforelse
            </table>
        </div>

        <br>
        <div class="row">
            <legend class="letra12">VALORACIONES ENFERMERIA</legend>
            <br>
            <table class="table" style="width: fix; table-layout:fixed;">
                <thead>
                    <tr style="background:#399865;">
                        <th class="letra10Header" style="width: 30%">ENFERMERA(O)</th>
                        <th class="letra10Header" style="width: 20%">FECHA</th>
                        <th class="letra10Header" style="width: 50%">OBSERVACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($valoraciones_realizadas as $valoracion)
                        <tr>
                            <td><p class="letra10Body">{{$valoracion[0]}}</p></td>
                            <td><p class="letra10Body">{{$valoracion[1]}}</p></td>
                            <td style="width:250px; Word-wrap: break-Word;"><p class="letra10Body">{{$valoracion[2]}}</p></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center;"><p class="letra10Body">Sin Información</p></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>