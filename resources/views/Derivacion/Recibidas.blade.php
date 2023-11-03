@extends("Templates/template")

@section("titulo")
{{$solicitud}}
@stop


@section("miga")
<li><a href="#">Solicitudes de Traslado Externo</a></li>
<li><a href="#" onclick='location.reload()'>Recibidas</a></li>
@stop

@section("script")
<script>

	var enviarMensaje=function(id){

	}

	var cancelar=function(id){
		
	}

	var responder=function(id){
		
	}

	var resolicitar=function(id){
		
	}

	var getRiesgos=function(){
		var riesgos=[];
		$.ajax({
			url: "{{URL::to('/')}}/getRiesgos",
			dataType: "json",
			type: "post",
			async: false,
			success: function(data){
				riesgos=data;
			},
			error: function(error){
				console.log(error);
			}
		});
		return riesgos;
	}
	
	var getDerivaciones=function(){
		$.ajax({
			url: "{{URL::to('/')}}/getDerivaciones",
			type: "get",
			data: {param: "recibidas"},
			dataType: "json",
			success: function(data){
				var tabla=$("#derivaciones").dataTable().columnFilter({
					aoColumns: [
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "select", values: getRiesgos()},
					{type: "text"},
					null
					]
				});
				getRiesgos();
				tabla.fnClearTable();
				if(data.length != 0) tabla.fnAddData(data);
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	$(function(){
		$("#solicitudMenu").collapse();

		$('#derivaciones').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": 25,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 		});

 		getDerivaciones();
	});

</script>
@stop

@section("section")
<div class="table-responsive">
<table id="derivaciones" class="table table-striped table-condensed table-bordered">
	<tfoot>
		<tr>
			<th>Fecha solicitud</th>
			<th>Tiempo de espera</th>
			<th>Establecimiento origen</th>
			<th>Servicio</th>
			<th>Usuario</th>
			<th>Paciente</th>
			<th>Edad</th>
			<th>Diagnóstico</th>
			<th>Riesgo</th>
			<th>Estado solicitud</th>
			<th>Opciones</th>
		</tr>
	</tfoot>
	<thead>
		<tr>
			<th>Fecha solicitud</th>
			<th>Tiempo de espera</th>			
			<th>Establecimiento destino</th>
			<th>Servicio</th>
			<th>Usuario</th>
			<th>Paciente</th>
			<th>Edad</th>
			<th>Diagnóstico</th>
			<th>Riesgo</th>	
			<th>Estado solicitud</th>
			<th>Opciones</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
</div>
@stop