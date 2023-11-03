@extends("Templates/template")

@section("titulo")
Gestión Salas
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión Salas</a></li>
@stop

@section("script")
<script>

	var editar=function(id){
		swalInfo.fire({
		title: 'Editar',
		});
	}

	var eliminar=function(id){
		swalInfo.fire({
		title: 'Eliminar',
		});
	}

	var agregar=function(id){
		swalInfo.fire({
		title: 'Agregar',
		});

	}

	var getTablaCamas=function(){
		$.ajax({
			url: "{{URL::to('/')}}/getTablaCamas",
			type: "post",
			data: {param: "recibidas"},
			dataType: "json",
			success: function(data){
				var tabla=$("#admCamas").dataTable().columnFilter({
					aoColumns: [
					{type: "text"},
					{type: "text"},
					//{type: "select", values: getRiesgos()},
					{type: "text"},
					{type: "text"},
					null
					]
				});
				
				tabla.fnClearTable();
				if(data.length != 0) tabla.fnAddData(data);
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	$(function(){
		$("#administracionMenu").collapse();

		$('#admCamas').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": -1,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 		});

 		getTablaCamas();

	});

</script>
@stop

@section("section")
<div class="table-responsive">
<table id="admCamas" class="table table-striped table-condensed table-bordered">
	<tfoot>
		<tr>
			<th>Establecimiento</th>
			<th>Servicio</th>
			<th>Sala</th>
			<th>Número camas</th>
			<th></th>
		</tr>
	</tfoot>
	<thead>
		<tr>
			<th>Establecimiento</th>
			<th>Servicio</th>
			<th>Sala</th>
			<th>Número camas</th>
			<th>Opciones</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
</div>

<br>
@stop