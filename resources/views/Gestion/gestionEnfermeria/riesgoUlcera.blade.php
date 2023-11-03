@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("script")
@include('Gestion.gestionEnfermeria.partials.scriptRiesgoUlceras')   
<meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("miga")
<li><a href="#">Formulario Escala Evaluación Riesgo de Lesiones por Presión</a></li>
@stop

@section("section")
    <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
    <br><br>
    {{ HTML::link("formulario/$caso/historialRiesgoUlcera", 'Ver Historial', ['class' => 'btn btn-default', "id" => "btnHistorial"]) }}

    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'riesgoUlceraform')) }}

        {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoUlcera')) }}
        <input type="hidden" value="En Curso" name="tipoFormUlcera" id="tipoFormUlcera">
        <input type="hidden" value="" name="id_formulario_riesgo_ulcera" id="id_formulario_riesgo_ulcera">
        <br>
        @include('Gestion.gestionEnfermeria.partials.FormRiesgoUlceras') 

    {{ Form::close()}}   
@stop
