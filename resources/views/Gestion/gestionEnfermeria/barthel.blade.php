@extends("Templates/template")

@section("titulo")
	Formulario
@stop

@section("script")
	@include('Gestion.gestionEnfermeria.partials.scriptBarthel')  
	<meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("miga")
	<li><a href="#">Formulario Indice Barthel</a></li>
@stop

@section("section")

	<style>
	.table thead{
		background-color: #bce8f1;
	}

	.table > thead:first-child > tr:first-child > th {
		color: black;
	}
	</style>


	<a href="javascript:history.back()" class="btn btn-primary">Volver</a>
	<br> <br>
	{{ HTML::link("gestionEnfermeria/$caso/historialBarthel", ' Ver Historial', ['class' => 'btn btn-default' , "id" => "volver"]) }}
	
	{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'formBarthel', 'autocomplete' => 'off')) }}
		<input type="hidden" value="En Curso" name="tipoFormBarthel" id="tipoFormBarthel">
		@include('Gestion.gestionEnfermeria.partials.FormBarthel')

	{{Form::close()}}
@stop