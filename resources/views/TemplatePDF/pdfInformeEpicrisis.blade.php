<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
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
  .col-md-pull-12 {
    right: 100%;
  }
  .col-md-pull-11 {
    right: 91.66666667%;
  }
  .col-md-pull-10 {
    right: 83.33333333%;
  }
  .col-md-pull-9 {
    right: 75%;
  }
  .col-md-pull-8 {
    right: 66.66666667%;
  }
  .col-md-pull-7 {
    right: 58.33333333%;
  }
  .col-md-pull-6 {
    right: 50%;
  }
  .col-md-pull-5 {
    right: 41.66666667%;
  }
  .col-md-pull-4 {
    right: 33.33333333%;
  }
  .col-md-pull-3 {
    right: 25%;
  }
  .col-md-pull-2 {
    right: 16.66666667%;
  }
  .col-md-pull-1 {
    right: 8.33333333%;
  }
  .col-md-pull-0 {
    right: auto;
  }
  .col-md-push-12 {
    left: 100%;
  }
  .col-md-push-11 {
    left: 91.66666667%;
  }
  .col-md-push-10 {
    left: 83.33333333%;
  }
  .col-md-push-9 {
    left: 75%;
  }
  .col-md-push-8 {
    left: 66.66666667%;
  }
  .col-md-push-7 {
    left: 58.33333333%;
  }
  .col-md-push-6 {
    left: 50%;
  }
  .col-md-push-5 {
    left: 41.66666667%;
  }
  .col-md-push-4 {
    left: 33.33333333%;
  }
  .col-md-push-3 {
    left: 25%;
  }
  .col-md-push-2 {
    left: 16.66666667%;
  }
  .col-md-push-1 {
    left: 8.33333333%;
  }
  .col-md-push-0 {
    left: auto;
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

  legend{
    padding:0 !important;
    margin:0 !important;
    border:none !important;
    font-size:15px !important;
}

  label {
    display: inline-block;
    width:250px;
    margin-bottom: 0px;
    font-weight: 700;
    padding:0 !important;
    font-size:13px !important;
}

p,
span,
li{
    font-size:11px !important;
}

hr{
    border:1px solid #B4B4B4 !important;
    margin-top:0 !important;
    margin-bottom:5px !important;
}

ul.formularios {
    padding:0px;
}

.form-group {
    display: inline-block;
    margin-bottom: 0 !important;
    padding:0 !important;
    vertical-align: middle;

}
.form-control {
    display: inline-block !important;
    width: auto !important;
    vertical-align: middle !important;
}


.bb-1{
    margin-bottom: 10px;
}

.table>thead>tr>th {
    border-bottom: 2px solid #ddd !important;
}

.texto>label{
    width:100%;
}

    </style>
     {{ HTML::style('css/bootstrap.min.css') }}
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
            {{$establecimiento->nombre}}
            </div> 
            <div class="col-md-4">
            <h4 class="text-center"><b>INFORME EPICRISIS</b></h4>
            </div> 
            <div class="col-md-4 text-right">
            Fecha: {{$fecha}}
            </div> 
        </div>
    </div>
    <br>

    <legend>Identificación del paciente</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
            {{Form::label('', "Nombre", array( ))}}
            <p class="box4">{{$infoPaciente->nombre ." ".$infoPaciente->apellido_paterno. " ".$infoPaciente->apellido_materno}}</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "RUN", array( ))}}
            <p class="box4">{{$infoPaciente->rut."-"}}{{ ($infoPaciente->dv == 10)?"K":$infoPaciente->dv }}</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "Edad", array( ))}}
            <p class="box4">{{Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento)}} años</p>
            </div>
            <div class="col-md-3">
            {{Form::label('', "N° de ficha", array( ))}}
            <p class="box4">{{$dau->dau}}</p>
            </div>
            <div class="col-md-3">
            {{ Form::label('', "Nombre de cuidador o familiar", array()) }}
            @if(isset($infoResponsable->acompanante))
                <p class="box4">{{ $infoResponsable->acompanante }}</p>
            @else
                <p class="box4">Sin información</p>
            @endif
            </div>
            <div class="col-md-2">
            {{ Form::label('', "Vinculo familiar", array()) }}
            @if(isset($infoResponsable->vinculo_acompanante))
                <p class="box4">{{ $infoResponsable->vinculo_acompanante }}</p>
            @else
                <p class="box4">Sin información</p>
            @endif
            </div>
            <div class="col-md-5 texto">
            {{ Form::label('', "Número telefonico de cuidador o familiar", array()) }}
            @if(isset($infoResponsable->telefono_acompanante))
                <p class="box4">{{ $infoResponsable->telefono_acompanante }}</p>
            @else
                <p class="box4">Sin información</p>
            @endif
            </div>
        </div>
    </div>
    <br>

    <legend>Antecedentes de hospitalización</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                {{Form::label('', "Fecha Ingreso", array( ))}}
                <p>{{$fechaSolicitud}}</p>
            </div>
            <div class="col-md-3">
                {{Form::label('', "Fecha Hospitalización", array( ))}}
                <p>{{$fechaHospitalizacion}}</p>
            </div>
            <div class="col-md-3">
                {{Form::label('', "Fecha Egreso", array( ))}}
                <p>{{$fechaEgreso}}</p>
            </div>
            <div class="col-md-3">
                {{Form::label('', "Estadia (Hospitalización y Egreso)", array( ))}}
                <p>{{$diffHospEgreso}}</p>
            </div>
            <div class="col-md-3">
            {{Form::label('', "Destino", array( ))}}
                @if(isset($destino) && $destino != '')
                    <p>{{ $destino }}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
        </div>
    </div>
    <br>


    <legend>Diagnóstico médico</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">  
            @if(!empty($susDiagnosticos))    
              @foreach($diagnosticos as $d)
                @if(in_array($d->id, $susDiagnosticos))
                  <ul class="formularios">
                      <li  style="list-style-position: inside;">{{$d->diagnostico}}</li>
                  </ul>
                @endif           
              @endforeach
            @else
              <p class="text-center">Sin información</p>
            @endif 
            </div>
        </div>
    </div>        
    <br>

    <legend>Intervención Quirurgica</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
        @if(isset($infoEpicrisis->intervencion_quirurgica) && isset($infoEpicrisis->fecha_intervencion))
            <div class="col-md-4">
            {{Form::label('', "Intervención", array( ))}}
            <p>{{$infoEpicrisis->intervencion_quirurgica}}</p>
            </div>
            <div class="col-md-4">
            {{Form::label('', "Fecha", array( ))}}
            <p>{{$infoEpicrisis->fecha_intervencion}}</p>
            </div>
        @else
            <p class="text-center">Sin información</p>
        @endif   
        </div>
    </div>
    <br>

    <legend>Evolución enfermeria</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 bb-1">
                <div class="form-group">
                    {{Form::label('', "Sistema Neurológico", array( ))}}
                    @if(isset($infoDatosEvolucionEnfermeria->neurologico))
                    <span>{{$infoDatosEvolucionEnfermeria->neurologico}}</span>
                    @else
                    <span class="">Sin información</span>
                    @endif   
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                    {{Form::label('', "Sistema Metabólico", array( ))}}
                    @if(isset($infoDatosEvolucionEnfermeria->metabolico))
                        <span>{{$infoDatosEvolucionEnfermeria->metabolico}}</span>
                    @else
                        <span>Sin información</span>
                    @endif     
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Sistema Cardiovascular", array( ))}}
                @if(isset($infoDatosEvolucionEnfermeria->cardiovascular))
                <span>{{$infoDatosEvolucionEnfermeria->cardiovascular}}</span>
                @else
                <span>Sin información</span>
                @endif     
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Sistema Musculo esquelético", array( ))}}
                @if(isset($infoDatosEvolucionEnfermeria->musculoesqueletico))
                <span>{{$infoDatosEvolucionEnfermeria->musculoesqueletico}}</span>
                @else
                <span>Sin información</span>
                @endif      
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Sistema Respiratorio", array( ))}}
                @if(isset($infoDatosEvolucionEnfermeria->respiratorio))
                <span>{{$infoDatosEvolucionEnfermeria->respiratorio}}</span>
                @else
                <span>Sin información</span>
                @endif        
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Sistema Tegumentario", array( ))}}
                @if(isset($infoDatosEvolucionEnfermeria->tegumentario))
                <span class="box4">{{$infoDatosEvolucionEnfermeria->tegumentario}}</span>
                @else
                <span class="box4">Sin información</span>
                @endif         
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Sistema Digestivo", array( ))}}
                @if(isset($infoDatosEvolucionEnfermeria->digestivo))
                <span>{{$infoDatosEvolucionEnfermeria->digestivo}}</span>
                @else
                <span>Sin información</span>
                @endif            
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Sistema Genito urinario", array( ))}}
                @if(isset($infoDatosEvolucionEnfermeria->genitourinario))
                <span class="box4">{{$infoDatosEvolucionEnfermeria->genitourinario}}</span>
                @else
                <span class="box4">Sin información</span>
                @endif           
                </div>
            </div>
            <div class="col-md-6 bb-1">
                <div class="form-group">
                {{Form::label('', "Indice barthel", array( ))}}
                @if(isset($indiceBarthel) && $indiceBarthel != '' && isset($totalBarthel)  && $totalBarthel != '')
                <span>{{$indiceBarthel}}</span>
                <span class="col-md-offset-1">{{$totalBarthel}}</span>
                @else
                <span>Sin información</span>
                @endif              
                </div>
            </div>
        </div>
    </div>
<br>

    <legend>Cuidados al alta</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoCuidadoAlAlta) && count($infoCuidadoAlAlta) > 0)
                @foreach ($infoCuidadoAlAlta as $cuidadoAlAlta)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{ $cuidadoAlAlta->fecha_creacion }}</span><span class="col-md-offset-1"> {{ $cuidadoAlAlta->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p class="text-center">Sin información</p>
                @endif 
            </div>
        </div>
    </div>
    
    <br>
    <legend>Continuidad de la atencion</legend>
    <hr>
    {{Form::label('', "Control médico", array( ))}}
    <div class="container-fluid mb-1">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoControlMedico) && count($infoControlMedico) > 0)
                @foreach ($infoControlMedico as $controlMedico)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{ $controlMedico->fecha_solicitada }}</span><span class="col-md-offset-1"> {{ $controlMedico->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p>Sin información</p>
                @endif 
            </div>
        </div>
    </div>

    {{Form::label('', "Interconsulta", array( ))}}
    <div class="container-fluid mb-1">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoInterconsulta) && count($infoInterconsulta) > 0)
                @foreach ($infoInterconsulta as $interconsulta)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{  $interconsulta->fecha_solicitada }}</span><span class="col-md-offset-1"> {{  $interconsulta->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p>Sin información</p>
                @endif 
            </div>
        </div>
    </div>

    {{Form::label('', "Examenes Pendientes", array( ))}}
    <div class="container-fluid mb-1">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoExamenesPendientes) && count($infoExamenesPendientes) > 0)
                @foreach ($infoExamenesPendientes as $examenesPendientes)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{  $examenesPendientes->fecha_solicitada }}</span><span class="col-md-offset-1"> {{  $examenesPendientes->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p>Sin información</p>
                @endif 
            </div>
        </div>
    </div>

    {{Form::label('', "Medicamento al Alta", array( ))}}
    <div class="container-fluid mb-1">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoMedicamentoAlAlta) && count($infoMedicamentoAlAlta) > 0)
                @foreach ($infoMedicamentoAlAlta as $medicamentoAlAlta)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{  $medicamentoAlAlta->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p>Sin información</p>
                @endif 
            </div>
        </div>
    </div>

    {{Form::label('', "Educaciones Realizadas", array( ))}}
    <div class="container-fluid mb-1">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoEducacionesRealizadas) && count($infoEducacionesRealizadas) > 0)
                @foreach ($infoEducacionesRealizadas as $educacionesRealizadas)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{  $educacionesRealizadas->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p>Sin información</p>
                @endif 
            </div>
        </div>
    </div>

    {{Form::label('', "Otros", array( ))}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(isset($infoOtros) && count($infoOtros) > 0)
                @foreach ($infoOtros as $otros)
                    <ul class="formularios">
                        <li  style="list-style-position: inside;">
                         <span>{{  $otros->tipo }}</span>
                        </li>
                    </ul>
                @endforeach
                @else
                <p>Sin información</p>
                @endif 
            </div>
        </div>
    </div>
</body>
</html>