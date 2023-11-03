@extends("Templates/template")

@section("titulo")
Solicitudes enviadas
@stop


@section("miga")
<li><a href="#">Solicitudes de Traslado Externo</a></li>
<li><a href="#" onclick='location.reload()'>Enviadas</a></li>
@stop

@section("script")
<script>
	var cancelar=function(id){
		bootbox.dialog({
			message: "<h4>¿ Desea cancelar el traslado externo ?</h4>",
			title: "Confirmación",
			buttons: {
				success: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							url: "{{URL::to('/')}}/cancelarTraslado",
							data: {id: id},
							type: "post",
							dataType: "json",
							success: function(data){
								if(data.exito){
									swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									didOpen: function() {
										setTimeout(function() {
											location . reload();
										}, 2000)
									},
									});
								} 
								if(data.error){
								swalError.fire({
								title: 'Error',
								text:data.error
								});
								} 
							},
							error: function(error){
								console.log(error);
							}
						});
					}
				},
				danger: {
					label: "Cancelar",
					className: "btn-danger",
					callback: function() {
					}
				}
			}
		});
	}

	var responder=function(id){
		
	}

	var resolicitar=function(id){
		bootbox.dialog({
			message: "<h4>¿ Desea resolicitar la solicitud ?</h4>",
			title: "Confirmación",
			buttons: {
				success: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							url: "{{URL::to('/')}}/resolicitarTraslado",
							data: {id: id},
							type: "post",
							dataType: "json",
							success: function(data){
								if(data.exito){
									swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									});
								} 
									if(data.error){
									swalError.fire({
									title: 'Error',
									text:data.error
									});
									} 
								},
								error: function(error){
									console.log(error);
								}
							});
					}
				},
				danger: {
					label: "Cancelar",
					className: "btn-danger",
					callback: function() {
					}
				}
			}
		});
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
			data: {param: "enviadas"},
			type: "get",
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