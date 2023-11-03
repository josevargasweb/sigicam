<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
            .col-md-1, .col-md-2, .col-md-3, .col-md-4,.col-md-44, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
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
  .col-md-44 {
    width: 26.33333333%;
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
  .offset-1 {
    margin-left: 5.33333333%;
}
  .offset-2 {
    margin-left: 9.33333333%;
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
.p-text-little{
    font-size:11px !important;
}
.w100{
    width:100%;
}

.p-text{
    font-size:12px !important;
}

hr{
    border:1px solid #B4B4B4 !important;
    margin-top:0 !important;
    margin-bottom:5px !important;
}


ul.formularios > li{
    padding-left:15px;
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

.label-12{
    width:100%;
    text-decoration: underline;
}
.label-4{
    width:40%;
    text-decoration: underline;
    float:left;
}

.u{
    text-decoration: underline;
}

.line-height-2{
    line-height:20px;
}

    </style>
     {{ HTML::style('css/bootstrap.min.css') }}
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
            {{$establecimiento}}
            </div> 
            <div class="col-md-4">
            <h4 class="text-center"><b>HOJA INGRESO ENFERMERIA</b></h4>
            </div> 
            <div class="col-md-4 text-right">
            Fecha: {{$fecha_ingreso_enfermeria}}
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
            <p class="box4">{{$paciente->nombre ." ".$paciente->apellido_paterno. " ".$paciente->apellido_materno}}</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "RUN", array( ))}}
            <p class="box4">{{$paciente->rut."-"}}{{ ($paciente->dv == 10)?"K":$paciente->dv }}</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "Fecha nacimiento", array( ))}}
            <p class="box4">{{Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d-m-Y')}}</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "Edad", array( ))}}
            <p class="box4">{{Carbon\Carbon::now()->diffInYears($paciente->fecha_nacimiento)}} años</p>
            </div>
            <div class="col-md-3">
            {{Form::label('', "Nombre social", array( ))}}
            @if(isset($paciente->nombre_social))
                <p class="box4">{{strtoupper($paciente->nombre_social)}}</p>
            @else
                <p class="box4">Sin información</p>
            @endif
            </div>
            <div class="col-md-3">
            {{ Form::label('', "Genero", array()) }}
            @if(isset($paciente->sexo))
                <p class="box4">{{strtoupper($paciente->sexo)}}</p>
            @else
                <p class="box4">Sin información</p>
            @endif
            </div>
            <div class="col-md-2">
            {{ Form::label('', "Previsión", array()) }}
            @if(isset($prevision))
                <p class="box4">{{$prevision}}</p>
            @else
                <p class="box4">Sin información</p>
            @endif
            </div>
            <div class="col-md-5 texto">
            {{ Form::label('', "Telefono", array()) }}
            @if(isset($telefonos))
            @forelse ($telefonos as $item)
            <p class="box4">({{strtoupper($item->tipo)}}) {{$item->telefono}}</p>
            @empty
            <p class="box4">Sin información</p>
            @endforelse
            @else
            <p class="box4">Sin información</p>
            @endif
            </div>
        </div>
    </div>
    <br>
    <legend>I.- Anamnesis</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                {{Form::label('', "A) Antecedentes morbidos y tratamiento", array('class' => 'label-12'))}}
                @if($anamnesis != null && $anamnesis->anamnesis_ant_morbidos)
                    <p>{{$anamnesis->anamnesis_ant_morbidos}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                    <div class="col-md-4">
                        {{Form::label('', "B) Ofrecimiento de acompañamiento", array('class' => 'u'))}}
                    </div>
                @if($anamnesis != null && $anamnesis->acom == true)
                    <div class="col-md-12">
                        <div class="col-md-3">
                            {{Form::label('', "Nombre de cuidador o familiar", array('class' => 'p-text text-center'))}}
                                <p class="text-center">{{$anamnesis->acompanante}}</p>
                        </div>
                        <div class="col-md-3"  style="margin-left:90px;">
                            {{Form::label('', "Vinculo familiar", array('class' => 'p-text text-center'))}}
                                <p class="text-center">{{$anamnesis->vinculo_acompanante}}</p>
                        </div>
                        <div class="col-md-4">
                            {{Form::label('', "Número telefonico de cuidador o familiar", array('class' => 'p-text text-center'))}}
                                <p class="text-center">{{$anamnesis->telefono_acompanante}}</p>
                        </div>
                    </div>
                @elseif($anamnesis != null && $anamnesis->acom == false)
                    <div class="col-md-3">
                        <p>No</p>
                    </div>
                @else
                    <div class="col-md-12">
                        <p>Sin información</p>
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    {{Form::label('', "C) DEIS", array('class' => 'label-12'))}}
                </div>
                @if($anamnesis != null && $anamnesis->deis == true)
                    <div class="col-md-3">
                        <p>Si</p>
                    </div>
                @elseif($anamnesis != null && $anamnesis->deis == false)
                    <div class="col-md-3">
                        <p>No</p>
                    </div>
                @else
                    <div class="col-md-12">
                        <p>Sin información</p>
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                {{Form::label('', "D) Lista Medicamentos", array('class' => 'label-12'))}}
                @if(isset($medicamentos) && count($medicamentos) > 0)
                <ul class="formularios">
                    @foreach ($medicamentos as $medicamento)
                        <li  style="list-style-position: inside;">
                            <span>{{  $medicamento["nombre"] }}</span>
                        </li>
                    @endforeach
                </ul>
                @else
                <p>Sin información</p>
                @endif 
            </div>
            <div class="col-md-12">
                {{Form::label('', "E) Antecedentes quirúrgicos", array('class' => 'label-12'))}}
                @if($anamnesis != null && $anamnesis->anamnesis_ant_quirurgicos == true)
                    <p>{{$anamnesis->anamnesis_ant_quirurgicos}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    {{Form::label('', "F) Antecedentes alergicos", array('class' => 'label-12'))}}
                </div>
                @if($anamnesis != null && $anamnesis->anamnesis_ant_alergicos == true)
                <div class="col-md-3">
                        <p>Si</p>
                    </div>
                    <div class="col-md-12">
                        <p>{{$anamnesis->anamnesis_ant_alergicos}}</p>
                    </div>
                @elseif($anamnesis != null && $anamnesis->anamnesis_ant_alergicos == false)
                    <div class="col-md-3">
                        <p>No</p>
                    </div>
                @else
                    <div class="col-md-12">
                        <p>Sin información</p>
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                {{Form::label('', "G) Habitos", array('class' => 'label-12'))}}
                @if(isset($anamnesis) && ($anamnesis->habito_tabaco == true || $anamnesis->habito_alcohol == true || $anamnesis->habito_drogas == true || $anamnesis->habito_otros == true))                    
                    <ul class="formularios">
                        @if($anamnesis != null && $anamnesis->habito_tabaco == true)
                            <li  style="list-style-position: inside;">
                                <span>Tabaco</span>
                            </li>
                        @endif
                        @if($anamnesis != null && $anamnesis->habito_alcohol == true)
                            <li  style="list-style-position: inside;">
                                <span>Alcohol</span>
                            </li>
                        @endif
                        @if($anamnesis != null && $anamnesis->habito_drogas == true)
                            <li  style="list-style-position: inside;">
                                <span>Drogas</span>
                            </li>
                        @endif
                        @if($anamnesis != null && $anamnesis->habito_otros == true)
                            <li  style="list-style-position: inside;">
                                <span>Otras:&nbsp;</span><span class="">{{  $anamnesis->detalle_otro_habito }}</span>
                            </li>
                        @endif
                    </ul>
                @else
                    <div class="col-md-12">
                        <p>Sin información</p>
                    </div>
                @endif
            </div>
            
			@if($sub_categoria == 4)
			<div class="col-md-12">
				{{Form::label('', "   Precaución estándar", array('class' => 'label-12'))}}
				@if($anamnesis->precaucion_estandar)
				<ul>
					@if($anamnesis->precaucion_respiratorio)
					<li>Respiratorio</li>
					@endif
					@if($anamnesis->precaucion_contacto)
					<li>Contacto</li>
					@endif
					@if($anamnesis->precaucion_gotitas)
					<li>Gotitas</li>
					@endif
				</ul>
				@else
				<p>Sin información</p>
				@endif
			</div>
			@endif
            <div class="col-md-12">
                {{Form::label('', "H) Diagnostricos médicos", array('class' => 'label-12'))}}
                @if($anamnesis != null && $anamnesis->diagnosticos_medicos)
                    <p>{{$anamnesis->diagnosticos_medicos}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                {{Form::label('', "I) Anamnesis actual", array('class' => 'label-12'))}}
                @if($anamnesis != null && $anamnesis->anamnesis_actual)
                    <p>{{$anamnesis->anamnesis_actual}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
        </div>    
    </div> 
    @if($sub_categoria == 2)
        <h6><b>ANTECEDENTES OBSTÉTRICOS</b></h6>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    {{Form::label('', "Gesta", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->gesta == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->gesta == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->gesta_observacion != null)    
                        <div class="col-md-12">
                            {{Form::label('', "Observación", array('class' => 'p-text'))}}
                        </div>
                        <p class="">{{$ginecologico->gesta_observacion}}</p>
                    @endif
                </div>
                <div class="col-md-2">
                    {{Form::label('', "Parto", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->parto == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->parto == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->parto_observacion != null)
                        {{Form::label('', "Observación", array('class' => 'p-text'))}}
                        <p class="">{{$ginecologico->parto_observacion}}</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    {{Form::label('', "Aborto", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->aborto == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->aborto == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->aborto_observacion != null)    
                        {{Form::label('', "Observación", array('class' => 'p-text'))}}
                        <p class="">{{$ginecologico->aborto_observacion}}</p>
                    @endif
                </div>
                <div class="col-md-2">
                    {{Form::label('', "Parto vaginal", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->parto_vaginal == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->parto_vaginal == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->parto_vaginal_observacion != null)
                        {{Form::label('', "Observación", array('class' => 'p-text'))}}
                        <p class="">{{$ginecologico->parto_vaginal_observacion}}</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    {{Form::label('', "Forceps", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->forceps == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->forceps == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-2">
                    {{Form::label('', "Cesarias", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->cesarias == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->cesarias == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->cesarias_observacion != null)
                        {{Form::label('', "Observación", array('class' => 'p-text'))}}
                        <p class="">{{$ginecologico->cesarias_observacion}}</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    {{Form::label('', "Vivos/muertos", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->vivos_muertos != null)
                        <p class="">{{$ginecologico->vivos_muertos}}</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    {{Form::label('', "Fecha de ultimo parto", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->fecha_ultimo_parto != null)
                        <p class="">{{$ginecologico->fecha_ultimo_parto}}</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    {{Form::label('', "Método anticonceptivo", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->metodo_anticonceptivo == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->metodo_anticonceptivo == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->metodo_anticonceptivo_observacion != null)    
                        {{Form::label('', "Observación", array('class' => 'p-text'))}}
                        <p class="">{{$ginecologico->metodo_anticonceptivo_observacion}}</p>
                    @endif
                </div>
            </div>
        </div>

        <h6><b>ANTECEDENTES GINECOLÓGICOS</b></h6>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    {{Form::label('', "Menarquia", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->menarquia == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->menarquia == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->menarquia_observacion != null)    
                        {{Form::label('', "Observación", array('class' => ''))}}
                        <p class="">{{$ginecologico->menarquia_observacion}}</p>
                    @endif
                </div>
                <div class="col-md-2">
                    {{Form::label('', "Ciclo menstrual", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->ciclo_menstrual != null)
                        <p class="">{{$ginecologico->ciclo_menstrual}}</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    {{Form::label('', "Menopausia", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->menopausia == true)
                        <p class="">Si</p>
                    @elseif($ginecologico != null &&  $ginecologico != '' && $ginecologico->menopausia == false)
                        <p class="">No</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-4">
                    {{Form::label('', "Pap", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->pap != null)
                        <p class="">{{$ginecologico->pap}}</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
                <div class="col-md-2">
                    {{Form::label('', "Fur", array('class' => 'p-text'))}}
                    @if($ginecologico != null &&  $ginecologico != '' && $ginecologico->fur != null)
                        <p class="">{{$ginecologico->fur}}</p>
                    @else
                        <p class="">Sin información</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
        
    <br>
    <legend>II. Examen físico general</legend>
    <hr>
    @if($sub_categoria == 3)
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                        <div class="col-md-4">
                            {{Form::label('', "Peso (KG):", array('class' => 'p-text'))}}
                        </div>
                        @if($general != null && $general->peso)
                        <p class="line-height-2">{{$general->peso}}</p> 
                        @else
                        <p class="line-height-2">Sin información</p>
                        @endif
                </div>
                <div class="col-md-3">
                        <div class="col-md-5">
                            {{Form::label('', "Talla (CM):", array('class' => 'p-text'))}}
                        </div>
                        @if($general != null && $general->altura)
                        <p class="line-height-2">{{$general->altura}}</p>
                        @else
                            <p class="line-height-2">Sin información</p>
                        @endif
                </div>
                <div class="col-md-3">
                        {{Form::label('', "Estado nutricional:", array('class' => 'p-text'))}}
                        @if($general != null && $general->patron_nutricional)
                        <p>{{$general->patron_nutricional}}</p>
                        @else
                        <p>Sin información</p>
                        @endif
                </div>
            </div>
        </div> 
    @else
        <h6><b>INFORMACIÓN IMC</b></h6>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                        <div class="col-md-4">
                            {{Form::label('', "Peso (KG):", array('class' => 'p-text'))}}
                        </div>
                        @if($general != null && $general->peso)
                        <p class="line-height-2">{{$general->peso}}</p> 
                        @else
                        <p class="line-height-2">Sin información</p>
                        @endif
                </div>
                <div class="col-md-3">
                        <div class="col-md-5">
                            {{Form::label('', "Altura (MT):", array('class' => 'p-text'))}}
                        </div>
                        @if($general != null && $general->altura)
                        <p class="line-height-2">{{$general->altura}}</p>
                        @else
                            <p class="line-height-2">Sin información</p>
                        @endif
                </div>
                <div class="col-md-3">
                        <div class="col-md-2">
                            {{Form::label('', "IMC:", array('class' => 'p-text'))}}
                        </div>
                        <?php
                            $resultado = 0
                        ?>
                        @if($general != null && $general->peso && $general->altura)
                            <?php
                            $peso = $general->peso;
                            $altura = $general->altura;
                            $altura2 = $altura * $altura;
                            $resultado = $peso/$altura2;
                            $resultado = round($resultado,1);
                            ?>
                        <p class="line-height-2">{{$resultado}}</p>
                        @else
                        <p class="line-height-2">Sin información</p>
                        @endif
                </div>
                @if($general != null && $general->peso && $general->altura)
                    <div class="col-md-3">
                            <div class="col-md-4">
                                {{Form::label('', "Categoria:", array('class' => 'p-text'))}}
                            </div>
                            <?php
                                $edad = ($paciente->fecha_nacimiento) ? Carbon\Carbon::now()->diffInYears($paciente->fecha_nacimiento) : null;
                            ?>
                        
                        @if($paciente->fecha_nacimiento && $edad < 60)
                            @if($resultado < 18.5)
                            <p class="line-height-2">Insuficiencia ponderal</p>
                            @elseif($resultado >= 18.5 && $resultado <= 24.9)
                            <p class="line-height-2">Intervalo normal</p>
                            @elseif($resultado == 25.0)
                            <p class="line-height-2">Sobrepeso</p>
                            @elseif($resultado > 25.0 && $resultado <= 29.9)
                            <p class="line-height-2">Preobesidad</p>
                            @elseif($resultado == 30.0)
                            <p class="line-height-2">Obesidad</p>
                            @elseif($resultado > 30.0 && $resultado <= 34.9)
                            <p class="line-height-2">Obesidad de clase I</p>
                            @elseif($resultado >= 35.0 && $resultado <= 39.9)
                            <p class="line-height-2">Obesidad de clase II</p>
                            @elseif($resultado >= 40.0)
                            <p class="line-height-2">Obesidad de clase III</p>
                            @else
                            <p class="line-height-2">Sin información</p>
                            @endif
                        @elseif($paciente->fecha_nacimiento && $edad >= 60)
                            @if($resultado <= 23)
                            <p class="line-height-2">Bajo peso</p>
                            @elseif($resultado > 23 && $resultado < 28)
                            <p class="line-height-2">Normal</p>
                            @elseif($resultado >= 28 && $resultado < 32)
                            <p class="line-height-2">Sobrepeso</p>
                            @elseif($resultado >=32)
                            <p class="line-height-2">Obesidad</p>
                            @else
                            <p class="line-height-2">Sin información</p>
                            @endif
                        @else
                            <p class="line-height-2">Sin información</p>
                        @endif
                    </div>
                @endif
                <div class="col-md-12">
                        {{Form::label('', "Estado nutricional:", array('class' => 'p-text'))}}
                        @if($general != null && $general->patron_nutricional)
                        <p>{{$general->patron_nutricional}}</p>
                        @else
                        <p>Sin información</p>
                        @endif
                </div>
            </div>
        </div>
    @endif
    <h6><b>SIGNOS VITALES</b></h6>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                {{Form::label('', "Presión arterial sistolica:", array('class' => 'p-text'))}} 
                @if($general != null && $general->presion_arterial_sistolica)
                    <p>{{$general->presion_arterial_sistolica}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Presión arterial diastolica:", array('class' => 'p-text'))}} 
                @if($general != null && $general->presion_arterial_diastolica)
                    <p>{{$general->presion_arterial_diastolica}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Frecuencia respiratoria:", array('class' => 'p-text'))}} 
                @if($general != null && $general->pulso)
                    <p>{{$general->pulso}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Frecuencia cardiaca:", array('class' => 'p-text'))}} 
                @if($general != null && $general->frecuencia_cardiaca)
                    <p>{{$general->frecuencia_cardiaca}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Temperatura:", array('class' => 'p-text'))}} 
                @if($general != null && $general->temperatura)
                    <p>{{$general->temperatura}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Saturación:", array('class' => 'p-text'))}} 
                @if($general != null && $general->saturacion)
                    <p>{{$general->saturacion}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
        </div>
    </div>
    <h6><b>VALORACIÓN DE NECESIDADES</b></h6>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                {{Form::label('', "Estado conciencia:", array('class' => 'p-text'))}} 
                @if($general != null && $general->estado_conciencia)
                    <p>{{$general->estado_conciencia}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Función respiratoria:", array('class' => 'p-text'))}} 
                @if($general != null && $general->funcion_respiratoria)
                    <p>{{$general->funcion_respiratoria}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Higiene (Aseo y confort):", array('class' => 'p-text'))}} 
                @if($general != null && $general->higiene)
                    <p>{{$general->higiene}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="col-md-4">
                    {{Form::label('', "Nova:", array('class' => 'p-text'))}} 
                </div>
                @if($general != null && $general->nova)
                    <p>{{$general->nova}}</p>
                @else
                    <p class="line-height-2">Sin información</p>
                @endif
            </div>
            <div class="col-md-3">
                <div class="col-md-4">
                    {{Form::label('', "Riesgo caida:", array('class' => 'p-text'))}} 
                </div>
                @if($general != null && $general->riesgo_caida)
                    <p>{{$general->riesgo_caida}}</p>
                @else
                    <p class="line-height-2">Sin información</p>
                @endif
            </div>
            <div class="col-md-3">
                <div class="col-md-3">
                    {{Form::label('', "Glasgow:", array('class' => 'p-text'))}} 
                </div>
                @if($general != null && $general->glasgow)
                    <p>{{$general->glasgow}}</p>
                @else
                    <p class="line-height-2">Sin información</p>
                @endif
            </div>
            <div class="col-md-3">
                <div class="col-md-3">
                    {{Form::label('', "Barthel:", array('class' => 'p-text'))}} 
                </div>
                @if($general != null && $general->barthel)
                    <p>{{$general->barthel}}</p>
                @else
                    <p class="line-height-2">Sin información</p>
                @endif
            </div>
        </div>
    </div>
    <legend>III. Examen físico segmentario</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                {{Form::label('', "Cabeza", array( ))}} 
                @if($segmentado != null && $segmentado->cabeza)
                    <p>{{$segmentado->cabeza}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Genitales", array())}} 
                @if($segmentado != null && $segmentado->genitales)
                    <p>{{$segmentado->genitales}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Piel", array())}} 
                @if($segmentado != null && $segmentado->piel)
                    <p>{{$segmentado->piel}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Cuello", array())}} 
                @if($segmentado != null && $segmentado->cuello)
                    <p>{{$segmentado->cuello}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Torax", array())}} 
                @if($segmentado != null && $segmentado->torax)
                    <p>{{$segmentado->torax}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Abdomen", array())}} 
                @if($segmentado != null && $segmentado->abdomen)
                    <p>{{$segmentado->abdomen}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Extremidades superiores", array())}} 
                @if($segmentado != null && $segmentado->extremidades_superiores)
                    <p>{{$segmentado->extremidades_superiores}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Extremidades inferiores", array())}} 
                @if($segmentado != null && $segmentado->extremidades_inferiores)
                    <p>{{$segmentado->extremidades_inferiores}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Columna y dorso", array())}} 
                @if($segmentado != null && $segmentado->columna_torso)
                    <p>{{$segmentado->columna_torso}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('', "Protesis dental", array())}} 
                </div>
                @if($segmentado != null && $segmentado->protesis_dental == true)
                <div class="col-md-3">
                        <p class="line-height-2">Si</p>
                </div>
                <div class="col-md-12">
                    <div class="col-md-2 offset-1">
                        {{Form::label('', "Ubicación", array('class' => 'p-text'))}}
                        @if($segmentado->ubicacion_protesis_dental)
                            <p>{{$segmentado->ubicacion_protesis_dental}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        {{Form::label('', "Detalle", array('class' => 'p-text '))}}
                        @if($segmentado->detalle_protesis_dental)
                            <p>{{$segmentado->detalle_protesis_dental}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                </div>
                @elseif($segmentado != null && $segmentado->protesis_dental == false)
                <div class="col-md-3">
                        <p class="line-height-2">No</p>
                </div>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('', "Brazalete", array())}} 
                </div>   
                @if($segmentado != null && $segmentado->brazalete == true)
                    <div class="col-md-3">
                        <p class="line-height-2">Si</p>
                    </div>
                    <div class="col-md-11  offset-1">
                        {{Form::label('', "Ubicación", array('class' => 'p-text'))}}
                        @if($segmentado->ubicacion_brazalete)
                            <p>{{$segmentado->ubicacion_brazalete}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                @elseif($segmentado != null && $segmentado->brazalete == false)
                    <div class="col-md-3">
                        <p class="line-height-2">No</p>
                    </div>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('', "Discapacidad auditiva", array())}} 
                </div>
                @if($segmentado != null && $segmentado->discapacidad_auditiva == true)
                    <div class="col-md-3">
                        <p class="line-height-2">Si</p>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2  offset-1">
                            {{Form::label('', "Audifonos", array('class' => 'p-text'))}} 
                        </div>
                        @if($segmentado != null && $segmentado->audifonos_discapacidad_auditiva == true)
                            <div class="col-md-3">
                                <p class="line-height-2">Si</p>
                            </div>
                            <div class="col-md-12 offset-2">
                                <div class="col-md-2">
                                    {{Form::label('', "Ubicación", array('class' => 'p-text'))}}
                                    @if($segmentado->ubicacion_discapacidad_auditiva)
                                        <p>{{$segmentado->ubicacion_discapacidad_auditiva}}</p>
                                    @else
                                        <p>Sin información</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    {{Form::label('', "Detalle audifonos", array('class' => 'p-text'))}}
                                    @if($segmentado->detalle_discapacidad_auditiva)
                                        <p>{{$segmentado->detalle_discapacidad_auditiva}}</p>
                                    @else
                                        <p>Sin información</p>
                                    @endif
                                </div>
                            </div>
                        @elseif($segmentado != null && $segmentado->audifonos_discapacidad_auditiva == false)
                            <div class="col-md-3">
                                <p class="line-height-2">No</p>
                            </div>
                        @else
                        <p>Sin información</p>
                        @endif
                    </div>
                @elseif($segmentado != null && $segmentado->discapacidad_auditiva == false)
                    <div class="col-md-3">
                        <p class="line-height-2">No</p>
                    </div>   
                @else
                    <p>Sin información </p>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('', "Discapacidad visual", array())}} 
                </div>
                @if($segmentado != null && $segmentado->discapacidad_visual == true)
                    <div class="col-md-3">
                        <p class="line-height-2">Si</p>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2  offset-1">
                            {{Form::label('', "Lentes", array('class' => 'p-text col-md-12'))}} 
                        </div>
                        @if($segmentado != null && $segmentado->lentes_discapacidad_visual == true)
                            <div class="col-md-3">
                                <p class="line-height-2">Si</p>
                            </div>
                            <div class="col-md-12 offset-2">
                                <div class="col-md-2">
                                    {{Form::label('', "Ubicación", array('class' => 'p-text'))}}
                                    @if($segmentado->ubicacion_discapacidad_visua)
                                        <p>{{$segmentado->ubicacion_discapacidad_visua}}</p>
                                    @else
                                        <p>Sin información</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    {{Form::label('', "Detalle lentes", array('class' => 'p-text'))}}
                                    @if($segmentado->detalle_discapacidad_visua)
                                        <p>{{$segmentado->detalle_discapacidad_visua}}</p>
                                    @else
                                        <p>Sin información</p>
                                    @endif
                                </div>
                            </div>
                        @elseif($segmentado != null && $segmentado->lentes_discapacidad_visual == false)
                            <div class="col-md-3">
                                <p class="line-height-2">No</p>
                            </div>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                @elseif($segmentado != null && $segmentado->discapacidad_visual == false)
                    <div class="col-md-3">
                        <p class="line-height-2">No</p>
                    </div>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('', "Presencia lesiones", array())}} 
                </div>
                @if($segmentado != null && $segmentado->presencia_lesiones == true)
                <div class="col-md-3">
                    <p class="line-height-2">Si</p>
                </div>
                <div class="col-md-12">
                            <div class="col-md-2  offset-1">
                            {{Form::label('', "Tipo", array('class' => 'p-text'))}} 
                                @if($segmentado->tipo_presencia_lesiones)
                                    <p>{{$segmentado->tipo_presencia_lesiones}}</p>
                                @else
                                    <p>Sin información</p>
                                @endif
                            </div>
                                @if($segmentado != null && $segmentado->tipo_presencia_lesiones == "Otras")
                                    <div class="col-md-3">
                                        {{Form::label('', "Descripción", array('class' => 'p-text'))}}
                                        @if($segmentado->descripcion_presencia_lesiones)
                                            <p>{{$segmentado->descripcion_presencia_lesiones}}</p>
                                        @else
                                            <p>Sin información</p>
                                        @endif
                                    </div>
                                @endif
                                    <div class="col-md-6">
                                        {{Form::label('', "Ubicación", array('class' => 'p-text'))}}
                                        @if($segmentado->ubicacion_presencia_lesiones)
                                            <p>{{$segmentado->ubicacion_presencia_lesiones}}</p>
                                        @else
                                            <p>Sin información</p>
                                        @endif
                                    </div>
                
                </div>
                @elseif($segmentado != null && $segmentado->presencia_lesiones == false)
                <div class="col-md-3">
                    <p class="line-height-2">No</p>
                </div>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <br>
            @if($sub_categoria == 2)
            <h6><b>EXAMEN OBSTÉTRICO</b></h6>
            <div class="col-md-4">
                {{Form::label('', "Altura uterina", array( ))}} 
                @if($segmentado != null && $segmentado->altura_uterina)
                    <p>{{$segmentado->altura_uterina}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Tacto vaginal", array())}} 
                @if($segmentado != null && $segmentado->tacto_vaginal)
                    <p>{{$segmentado->tacto_vaginal}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Membranas", array())}} 
                @if($segmentado != null && $segmentado->membranas)
                    <p>{{$segmentado->membranas}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Liquido anmiotico", array())}} 
                @if($segmentado != null && $segmentado->liquido_anmiotico)
                    <p>{{$segmentado->liquido_anmiotico}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Amnioscopia", array())}} 
                @if($segmentado != null && $segmentado->amnioscopia)
                    <p>{{$segmentado->amnioscopia}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Amniocentesis", array())}} 
                @if($segmentado != null && $segmentado->amnioscentesis)
                    <p>{{$segmentado->amnioscentesis}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Presentacion", array())}} 
                @if($segmentado != null && $segmentado->presentacion)
                    <p>{{$segmentado->presentacion}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Contracciones", array())}} 
                @if($segmentado != null && $segmentado->contracciones)
                    <p>{{$segmentado->contracciones}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Lcf (latidos cardio fetales)", array())}} 
                @if($segmentado != null && $segmentado->lfc)
                    <p>{{$segmentado->lfc}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <br>
            <h6><b>EXAMEN GINECOLÓGICO</b></h6>
            <div class="col-md-4">
                {{Form::label('', "Vagina", array( ))}} 
                @if($segmentado != null && $segmentado->vagina)
                    <p>{{$segmentado->vagina}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Perine", array())}} 
                @if($segmentado != null && $segmentado->perine)
                    <p>{{$segmentado->perine}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-4">
                {{Form::label('', "Tacto vaginal", array())}} 
                @if($segmentado != null && $segmentado->tacto_vaginal_eg)
                    <p>{{$segmentado->tacto_vaginal_eg}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            @endif
        </div>
    </div>
	@php
	$procesarValor = function($val){
		if($val === null || $val === ""){
			return "Sin información";
		}
		if($val === true){
			return "Sí";
		}
		if($val === false){
			return "No";
		}
		return $val;
	};
	@endphp
	@if($sub_categoria == 1)
	<legend>IV. Examen Ginecoobstétrico</legend>
	<hr>
	<div class="container">
		<div class="row">
			<legend>Examen ginecológico</legend>
		</div>
		<div class="row">
			<h4>Tacto vaginal</h4>
		</div>
			<div class="row">
				<div class="col-md-4">
					<label>Vulva</label>
					<p id="vulva_ego_resumen">{{$procesarValor($ginecoobstetrico->vulva)}}</p>
				</div>
				<div class="col-md-4">
					<label>Vagina</label>
					<p id="vagina_tacto_vaginal_ego_resumen">{{$procesarValor($ginecoobstetrico->vagina_tacto_vaginal)}}</p>
				</div>
				<div class="col-md-4">
					<label>Fondo de saco</label>
					<p id="fondo_de_saco_tacto_vaginal_ego_resumen">{{$procesarValor($ginecoobstetrico->fondo_de_saco_tacto_vaginal)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>Anexos</label>
					<p id="anexos_ego_resumen">{{$procesarValor($ginecoobstetrico->anexos)}}</p>
				</div>
				<div class="col-md-8">
					<label>Otros</label>
					<p id="otros_tacto_vaginal_ego_resumen">{{$procesarValor($ginecoobstetrico->otros_tacto_vaginal)}}</p>
				</div>
			</div>
		<div class="row">
			<h4>Especuloscopía</h4>
		</div>
			<div class="row">
				<div class="col-md-4">
					<label>Vagina</label>
					<p id="vagina_especuloscopia_ego_resumen">{{$procesarValor($ginecoobstetrico->vagina_especuloscopia)}}</p>
				</div>
				<div class="col-md-4">
					<label>Útero</label>
					<p id="utero_ego_resumen">{{$procesarValor($ginecoobstetrico->utero)}}</p>
				</div>
				<div class="col-md-4">
					<label>Cérvix</label>
					<p id="cervix_ego_resumen">{{$procesarValor($ginecoobstetrico->cervix)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>Fondo de saco</label>
					<p id="fondo_de_saco_especuloscopia_ego_resumen">{{$procesarValor($ginecoobstetrico->fondo_de_saco_especuloscopia)}}</p>
				</div>
				<div class="col-md-8">
					<label>Otros</label>
					<p id="otros_especuloscopia_ego_resumen">{{$procesarValor($ginecoobstetrico->otros_especuloscopia)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label>Recto/Ano</label>
					<p id="recto_ano_ego_resumen">{{$procesarValor($ginecoobstetrico->recto_ano)}}</p>
				</div>
			</div>
			<div class="row">
				<legend>Examen obstétrico</legend>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>Presentación</label>
					<p id="presentacion_ego_resumen">{{$procesarValor($ginecoobstetrico->presentacion)}}</p>
				</div>
				<div class="col-md-4">
					<label>Altura uterina</label>
					<p id="altura_uterina_ego_resumen">{{$procesarValor($ginecoobstetrico->altura_uterina)}}</p>
				</div>
				<div class="col-md-4">
					<label>Tono</label>
					<p id="tono_ego_resumen">{{$procesarValor($ginecoobstetrico->tono)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>Encajamiento</label>
					<p id="encajamiento_ego_resumen">{{$procesarValor($ginecoobstetrico->encajamiento)}}</p>
				</div>
				<div class="col-md-4">
					<label>Dorso</label>
					<p id="dorso_ego_resumen">{{$procesarValor($ginecoobstetrico->dorso)}}</p>
				</div>
				<div class="col-md-4">
					<label>Contracciones</label><br>
					<p id="contracciones_ego_resumen">{{$procesarValor($ginecoobstetrico->contracciones)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>L.C.F.</label>
					<p id="lcf_ego_resumen">{{$procesarValor($ginecoobstetrico->lcf)}}</p>
				</div>
				<div class="col-md-4">
					<label>Desaceleraciones</label><br>
					<p id="desaceleraciones_ego_resumen">{{$procesarValor($ginecoobstetrico->desaceleraciones)}}</p>
				</div>
				<div class="col-md-4">
					<label>Longitud cuello uterino</label>
					<p id="longitud_cuello_uterino_ego_resumen">{{$procesarValor($ginecoobstetrico->longitud_cuello_uterino)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>Dilatación cuello uterino</label>
					<p id="dilatacion_cuello_uterino_ego_resumen">{{$procesarValor($ginecoobstetrico->dilatacion_cuello_uterino)}}</p>
				</div>
				<div class="col-md-4">
					<label>Membranas</label>
					<p id="membranas_ego_resumen">{{$procesarValor($ginecoobstetrico->membranas)}}</p>
				</div>
				<div class="col-md-4">
					<label>Líquido amniótico</label>
					<p id="liquido_amniotico_ego_resumen">{{$procesarValor($ginecoobstetrico->liquido_amniotico)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label>Posición</label>
					<p id="posicion_ego_resumen">{{$procesarValor($ginecoobstetrico->posicion)}}</p>
				</div>
				<div class="col-md-4">
					<label>Plano</label>
					<p id="plano_ego_resumen">{{$procesarValor($ginecoobstetrico->plano)}}</p>
				</div>
				<div class="col-md-4">
					<label>Evaluación pelvis</label>
					<p id="evaluacion_pelvis_ego_resumen">{{$procesarValor($ginecoobstetrico->evaluacion_pelvis)}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label>Otros</label>
					<p id="otros_examen_obstetrico_ego_resumen">{{$procesarValor($ginecoobstetrico->otros_examen_obstetrico)}}</p>
				</div>
			</div>
	</div>
	@endif
	@if($sub_categoria == 1)
	<legend>V. Otros</legend>
	@else
    <legend>IV. Otros</legend>
	@endif
    <hr>
    @forelse ($otros as $cateter)
        @if($cateter->tipo_cateter == 0)
            <li><h6><b>BRANULA 1</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación:", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Lugar de instalación:", array('class' => 'p-text-little'))}} 
                        @if($cateter->lugar_instalacion)
                            <p>{{$cateter->lugar_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Responsable de instalación:", array('class' => 'p-text-little'))}} 
                        @if($cateter->responsable_instalcion)
                            <p>{{$cateter->responsable_instalcion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Calculo días instalada:", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>
            </div>   
        @endif
        @if($cateter->tipo_cateter == 1)
        <li><h6><b>BRANULA 2</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                <div class="col-md-2">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Lugar de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->lugar_instalacion)
                            <p>{{$cateter->lugar_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Responsable de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->responsable_instalcion)
                            <p>{{$cateter->responsable_instalcion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>    
            </div> 
        @endif
        @if($cateter->tipo_cateter == 2)
        <li><h6><b>S. FOLEY</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Lugar de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->lugar_instalacion)
                            <p>{{$cateter->lugar_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Material de fabricación", array('class' => 'p-text-little'))}}  
                        @if($cateter->material_fabricacion)
                            <p>{{$cateter->material_fabricacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de Mantención", array('class' => 'p-text-little'))}}  
                        @if($cateter->fecha_curacion)
                            <p>{{Carbon\Carbon::parse($cateter->fecha_curacion)->format('d-m-Y')}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>  
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Responsable de curación", array('class' => 'p-text-little'))}}  
                        @if($cateter->responsable_curacioin)
                            <p>{{$cateter->responsable_curacioin}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-8">
                        {{Form::label('', "Observación", array('class' => 'p-text-little'))}} 
                        @if($cateter->observacion)
                            <p>{{$cateter->observacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>  
            </div> 
        @endif
        @if($cateter->tipo_cateter == 3)
        <li><h6><b>SNG</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Lugar de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->lugar_instalacion)
                            <p>{{$cateter->lugar_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Material de fabricación", array('class' => 'p-text-little'))}}  
                        @if($cateter->material_fabricacion)
                            <p>{{$cateter->material_fabricacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>                     
                </div>  
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Responsable de curación", array('class' => 'p-text-little'))}}  
                        @if($cateter->responsable_curacioin)
                            <p>{{$cateter->responsable_curacioin}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>  
            </div> 
        @endif
        @if($cateter->tipo_cateter == 4)
        <li><h6><b>CVC</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Lugar de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->lugar_instalacion)
                            <p>{{$cateter->lugar_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Material de fabricación", array('class' => 'p-text-little'))}}  
                        @if($cateter->material_fabricacion)
                            <p>{{$cateter->material_fabricacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>                     
                </div>  
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Tipo", array('class' => 'p-text-little'))}} 
                        @if($cateter->tipo)
                            <p>{{$cateter->tipo}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Vía de instalación", array('class' => 'p-text-little'))}}   
                        @if($cateter->via_instalacion)
                            <p>{{$cateter->via_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Responsable de curación", array('class' => 'p-text-little'))}}  
                        @if($cateter->responsable_curacioin)
                            <p>{{$cateter->responsable_curacioin}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>  
            </div> 
        @endif
        @if($cateter->tipo_cateter == 5)
        <li><h6><b>SONDAS NASOYEYUNALES</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Responsable de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->responsable_instalcion)
                            <p>{{$cateter->responsable_instalcion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>    
            </div> 
        @endif
        @if($cateter->tipo_cateter == 6)
        <li><h6><b>TRAQUEOTOMÍA</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Responsable de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->responsable_instalcion)
                            <p>{{$cateter->responsable_instalcion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha cambio de filtro", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_curacion)
                            <p>{{Carbon\Carbon::parse($cateter->fecha_curacion)->format('d-m-Y')}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-3">
                        {{Form::label('', "Medición efluente en cc", array('class' => 'p-text-little'))}} 
                        @if($cateter->medicion_cuff)
                            <p>{{$cateter->medicion_cuff}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>    
                <div class="row">
                    <div class="col-md-12">
                        {{Form::label('', "Valoración de estoma y piel de periostomal", array('class' => 'p-text-little'))}} 
                        @if($cateter->observacion)
                            <p>{{$cateter->observacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                </div>
            </div> 
        @endif
        @if($cateter->tipo_cateter == 7)
        <li><h6><b>OSTOMÍAS</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Tipo", array('class' => 'p-text-little'))}} 
                        @if($cateter->tipo)
                            <p>{{$cateter->tipo}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{$cateter->fecha_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Cuidados de enfermería", array('class' => 'p-text-little'))}} 
                        @if($cateter->cuidado_enfermeria)
                            <p>{{$cateter->cuidado_enfermeria}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha cambio de bolsa", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_curacion)
                            <p>{{Carbon\Carbon::parse($cateter->fecha_curacion)->format('d-m-Y')}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-4">
                        {{Form::label('', "Responsable (cuidados Enfermeria y cambio bolsa)", array('class' => 'p-text-little w100'))}} 
                        @if($cateter->responsable_curacioin)
                            <p>{{$cateter->responsable_curacioin}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                </div>    
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-4">
                        {{Form::label('', "Valoración de estoma y piel de periostomal", array('class' => 'p-text-little'))}} 
                        @if($cateter->valoracion_estomaypiel)
                            <p>{{$cateter->valoracion_estomaypiel}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>   
                    <div class="col-md-4">
                        {{Form::label('', "Responsable (Valoración de estoma y piel periostomal)", array('class' => 'p-text-little w100'))}} 
                        @if($cateter->responsable_curacion_ostomias)
                            <p>{{$cateter->responsable_curacion_ostomias}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>   
                    <div class="col-md-2">
                        {{Form::label('', "Medición efluente en CC", array('class' => 'p-text-little'))}}
                        @if($cateter->medicion_efluente)
                            <p>{{$cateter->medicion_efluente}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Características", array('class' => 'p-text-little'))}}
                        @if($cateter->observacion)
                            <p>{{$cateter->observacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        {{Form::label('', "Detalle de la educación al paciente", array('class' => 'p-text-little'))}}
                        @if($cateter->detalle_educacion)
                            <p>{{$cateter->detalle_educacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        {{Form::label('', "bagueta", array('class' => 'p-text-little'))}}
                        @if($cateter->bagueta)
                            <p>Si</p>
                            @else
                            <p>No</p>
                        @endif
                    </div>
                </div>
            </div> 
        @endif
        @if($cateter->tipo_cateter == 8)
        <li><h6><b>OTRO</b></h6></li>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Detalle", array('class' => 'p-text-little'))}} 
                        @if($cateter->detalle)
                            <p>{{$cateter->detalle}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-1">
                        {{Form::label('', "Número", array('class' => 'p-text-little'))}} 
                        @if($cateter->numero)
                            <p>{{$cateter->numero}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Tipo", array('class' => 'p-text-little'))}} 
                        @if($cateter->tipo)
                            <p>{{$cateter->tipo}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Fecha de Instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{Carbon\Carbon::parse($cateter->fecha_instalacion)->format('d-m-Y')}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div> 
                    <div class="col-md-2">
                        {{Form::label('', "Lugar de Instalación", array('class' => 'p-text-little w100'))}} 
                        @if($cateter->lugar_instalacion)
                            <p>{{$cateter->lugar_instalacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-2">
                        {{Form::label('', "Material de fabricación", array('class' => 'p-text-little w100'))}} 
                        @if($cateter->material_fabricacion)
                            <p>{{$cateter->material_fabricacion}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                </div>    
                <div class="row">
                    <div class="col-md-2">
                        {{Form::label('', "Vía de Instalación", array('class' => 'p-text-little'))}} 
                        @if($cateter->via_instalacion_otro)
                            <p>{{$cateter->via_instalacion_otro}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        {{Form::label('', "Responsable de Curación", array('class' => 'p-text-little w100'))}} 
                        @if($cateter->responsable_curacioin)
                            <p>{{$cateter->responsable_curacioin}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>  
                    <div class="col-md-3">
                        {{Form::label('', "Calculo días instalada", array('class' => 'p-text-little'))}} 
                        @if($cateter->fecha_instalacion)
                            <p>{{ Carbon\Carbon::now()->diffInDays($cateter->fecha_instalacion)}}</p>
                        @else
                            <p>Sin información</p>
                        @endif
                    </div>
                </div>
            </div> 
        @endif
    @empty
        <p class="text-center">Sin información</p>
    @endforelse 
</body>
</html>
