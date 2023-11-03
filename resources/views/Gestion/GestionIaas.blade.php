@extends("Templates/template")


@section("titulo")
Infección Intrahospitalaria
@stop

@section("script")
<script>
$(function(){
	
	$("#form-localizacion").on("submit", function(ev) {
	    	ev.preventDefault();
	    	ev.stopPropagation();	
	        $.ajax({
	            url: $(this).prop("action"),
	            dataType: "json",
	            data: $(this).serialize(),
	            type: $(this).prop("method"),
	            success: function(data){
					swalInfo2.fire({
					title: 'Información',
					text:data.data
					})
	            },
	            error: function(error){
	                alert("error");
	            }
	        });	
	});

	$("#form-invasivo").on("submit", function(ev) {
	    	ev.preventDefault();
	    	ev.stopPropagation();	
	        $.ajax({
	            url: $(this).prop("action"),
	            dataType: "json",
	            data: $(this).serialize(),
	            type: $(this).prop("method"),
	            success: function(data){
					swalInfo2.fire({
					title: 'Información',
					text:data.data
					})
	            },
	            error: function(error){
	                alert("error");
	            }
	        });	
	});

	$("#form-etiologico1").on("submit", function(ev) {
	    	ev.preventDefault();
	    	ev.stopPropagation();	
	        $.ajax({
	            url: $(this).prop("action"),
	            dataType: "json",
	            data: $(this).serialize(),
	            type: $(this).prop("method"),
	            success: function(data){
					swalInfo2.fire({
					title: 'Información',
					text:data.data
					});
	            },
	            error: function(error){
	                alert("error");
	            }
	        });	
	});

	$("#form-caracteristica").on("submit", function(ev) {
	    	ev.preventDefault();
	    	ev.stopPropagation();	
	        $.ajax({
	            url: $(this).prop("action"),
	            dataType: "json",
	            data: $(this).serialize(),
	            type: $(this).prop("method"),
	            success: function(data){
					swalInfo2.fire({
					title: 'Información',
					text:data.data
					});
	            },
	            error: function(error){
	                alert("error");
	            }
	        });	
	});	
	
	
});


</script>
@stop

@section("section")
<fieldset>
	<legend>Gestión IAAS</legend>

	{{ Form::open(array('route' => 'agregarLocalizacion', 'method' => 'get', 'role' => 'form', 'id' => 'form-localizacion')) }}
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			{{ Form::label('localizacion', 'Localización: ') }}
		</div>
		<div class="col-md-4">
			{{ Form::text('localizacion','', array("style" => "width:100%;", "class" => "form-control")) }}
		</div>
		<div class="col-md-1">
		{{ Form::submit("Agregar", array("id" => "submit_nombre", "class" => "btn btn-primary")) }}
		</div>
		<div class="col-md-5"></div>
	</div>
	{{ Form::close() }}

	{{ Form::open(array('route' => 'agregarInvasivo', 'method' => 'get', 'role' => 'form', 'id' => 'form-invasivo')) }}
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			{{ Form::label('invasivo', 'Procedimiento Invasivo: ') }}
		</div>
		<div class="col-md-4">
			{{ Form::text('invasivo','', array("style" => "width:100%;", "class" => "form-control")) }}
		</div>
		<div class="col-md-1">
			{{ Form::submit("Agregar", array("id" => "submit_nombre", "class" => "btn btn-primary")) }}
		</div>
		<div class="col-md-5"></div>
	</div>
	{{ Form::close() }}

	
	{{ Form::open(array('route' => 'agregarEtiologia', 'method' => 'get', 'role' => 'form', 'id' => 'form-etiologico1')) }}
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			{{ Form::label('etiologico1', 'Agente Etiológico: ') }}
		</div>
		<div class="col-md-4">
			{{ Form::text('etiologico1','', array("style" => "width:100%;", "class" => "form-control")) }}
		</div>
		<div class="col-md-1">
			{{ Form::submit("Agregar", array("id" => "submit_nombre", "class" => "btn btn-primary")) }}
		</div>
		<div class="col-md-5"></div>
	</div>
	{{ Form::close() }}	

	{{ Form::open(array('route' => 'agregarCaracteristicaAgente', 'method' => 'get', 'role' => 'form', 'id' => 'form-caracteristica')) }}
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			{{ Form::label('caracteristica', 'Característica Agente: ') }}
		</div>
		<div class="col-md-4">
			{{ Form::text('caracteristica','', array("style" => "width:100%;", "class" => "form-control")) }}
		</div>
		<div class="col-md-1">
			{{ Form::submit("Agregar", array("id" => "submit_nombre", "class" => "btn btn-primary")) }}
		</div>
		<div class="col-md-5"></div>
	</div>
	{{ Form::close() }}	

	
</fieldset>
<br><br>
@stop