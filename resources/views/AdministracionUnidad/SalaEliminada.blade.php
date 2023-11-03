@extends("Templates/template")

@section("titulo")
Gestión Unidad
@stop

@section("miga")
<li>{{ HTML::link(URL::route('gestionEstablecimientos'), 'Gestión de establecimiento')}}</li>
<li>{{ HTML::link("/administracionUnidad/unidad/$idEstab", "$nombreEstab") }}</li>
<li>{{ HTML::link("/administracionUnidad/editarUnidad/$idEstab/$idUnidad", "$alias") }}</li>
<li><a href="#" onclick='location.reload()'>{{$nombreSala}}</a></li>
@stop


@section("script")
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("estilo-tablas-verdes")	
<style>
</style>
@stop

@section("section")
	@if($estab->some)
		@include("AdministracionUnidad/Alerta")
	@endif
	<div class="row">
		<div class="" style="padding-top:30px; margin-left: 33.3%;text-align: center;">
			<img class="" src="{{URL::to('/')}}/images/sinacceso.png" alt="" style="width: 40%;" >
			
			<br><br>
			<p class="h4">No tiene permitido el acceso </p>
		</div>
	</div>


@stop