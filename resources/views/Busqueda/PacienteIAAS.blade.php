@extends("Templates/template")

@section("titulo")
Búsqueda
@stop

@section("miga")
<li><a href="#">Búsqueda</a></li>
<li><a href="#" onclick='location.reload()'>Busqueda de pacientes</a></li>
@stop

@section("script")

<script>

	
	$(function(){
		$("#buscarMenu").collapse();
		
	    $("form.submitBusqueda").on("submit", function(ev) {
	    	ev.preventDefault();
	    	ev.stopPropagation();
			$("#informacion").html('');
	        $.ajax({
	            url: $(this).prop("action"),
	            dataType: "json",
	            data: $(this).serialize(),
	            type: $(this).prop("method"),
	            success: function(data){
	            	$("#resultados").css({display:'initial'});
	                var tabla=$("#tablaResultadoBusqueda").dataTable();
	        		tabla.fnClearTable();

	        	
	               if(data.pacientes === undefined){
						tabla.fnAddData(data);
	               }
	               else{
	               	tabla.fnAddData(data.pacientes);
	               }
	               
	            },
	            error: function(error){
	                console.log(error);
						swalWarning.fire({
						title: 'Información',
						text:"No se encontraron resultados"
						});
	            }
	        });
			return false;
	    });

	    $("#resultados").on("click", "a.info-paciente", function(){
	    	$.ajax({
	    		url: $(this).prop("href"),
	    		type: "GET",
	    		success: function(data){
	    			console.log(data);
	    			$("#informacion").css({display:'initial'}).html(data);
	    		},
	    		error: function(error){
	    			console.log(error);
	    		}

	    	});
	    	return false;
	    });
		
	});
</script>

@stop

@section("section")
<fieldset>
	<legend>Búsqueda de pacientes</legend>

	{{ Form::open(array('route' => 'busquedaRut', "class" => "submitBusqueda", 'method' => 'get', 'role' => 'form', 'id' => 'formBusquedaRut')) }}
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			{{ Form::label('rut', 'Buscar por Run: ') }}
		</div>
		<div class="col-md-4">
			{{ Form::text('rut', '', array("style" => "width:100%;", "class" => "form-control")) }}
		</div>
		<div class="col-md-1">
			{{ Form::submit("Buscar", array("id" => "submit_rut", "class" => "btn btn-primary")) }}
		</div>
		<div class="col-md-5"></div>
	</div>
	{{ Form::close() }}

	{{ Form::open(array('route' => 'busquedaNombre', "class" => "submitBusqueda", 'method' => 'get', 'role' => 'form', 'id' => 'formBusquedaNombret')) }}
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			{{ Form::label('nombre', 'Buscar por nombre o apellido: ') }}
		</div>
		<div class="col-md-4">
			{{ Form::text('nombre', '', array("style" => "width:100%;", "class" => "form-control")) }}
		</div>
		<div class="col-md-1">
			{{ Form::submit("Buscar", array("id" => "submit_nombre", "class" => "btn btn-primary")) }}
		</div>
		<div class="col-md-5"></div>
	</div>
	{{ Form::close() }}

	<div class="row" style="margin-top: 20px;">
		<div id="resultados" class="col-md-12" style="display:none">
		@include("Busqueda/ResultadoPacienteIAAS")
		</div>
	</div>
	<div class="row" style="margin-top: 20px;">
		<div id="informacion" class="col-md-12">
		</div>
	</div>
</fieldset>
<br><br>
@stop
