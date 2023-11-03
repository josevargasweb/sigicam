@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("script")
    @include('Gestion.gestionEnfermeria.partials.scriptMacdems')
    <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("miga")
<li><a href="#">Formulario Escala Macdems</a></li>
@stop

@section("section")


    <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
    <br> <br>
    {{ HTML::link("gestionEnfermeria/$caso/historialEscalaMacdems", 'Ver Historial', ['class' => 'btn btn-default', "id" => "btnHistorial"]) }}

    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaMacdemsform')) }}
    <input type="hidden" value="En Curso" name="tipoFormMacdems" id="tipoFormMacdems">
    {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoMacdems')) }}
        @include('Gestion.gestionEnfermeria.partials.FormMacdems') 
    {{ Form::close()}}   
@stop