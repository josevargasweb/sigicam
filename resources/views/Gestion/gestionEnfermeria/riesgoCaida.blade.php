@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("script")
        @include('Gestion.gestionEnfermeria.partials.scriptRiesgoCaida')    
        <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("miga")
<li><a href="#">Formulario Riesgo Caídas</a></li>
@stop

@section("section")

        <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
	<br> <br>
        {{ HTML::link("gestionEnfermeria/$caso/historialRiesgoCaida", 'Ver Historial', ['class' => 'btn btn-default', "id" => "btnHistorial"]) }}
               
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'riesgoCaidaform')) }}

                {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoCaida')) }}
                <input type="hidden" value="" name="id_formulario_riesgo_caida" id="id_formulario_riesgo_caida">
                <input type="hidden" value="En Curso" name="tipoFormRiesgoCaida" id="tipoFormRiesgoCaida">
                <br>  
                @include('Gestion.gestionEnfermeria.partials.FormRiesgoCaida')    
        
        {{ Form::close()}}
@stop