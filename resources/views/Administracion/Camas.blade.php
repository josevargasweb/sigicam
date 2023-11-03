@extends("Templates/template")

@section("titulo")
Gestión Camas
@stop


@section("miga")
<nav class="navbar navbar-default navbar-static subir-nav-header miga">
	@include("Templates/migaCollapse")
	<div class="collapse navbar-collapse bs-js-navbar-collapse">
		<div class="navbar-header">
			<ol class="breadcrumb listaMiga">
				<li><a href="{{URL::to('index')}}"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
				<li><a href="#">Administración</a></li>
				<li><a href="#" onclick='location.reload()'>Gestión Camas</a></li>
			</ol>
		</div>
		@include("Templates/migaAcciones")
	</div>
</nav>
@stop

@section("script")
<script>

	var editar=function(nEst, nUnidad, nSala, cantSala, idSala){
		//bootbox.alert("<h4>editar</h4>");
		console.log(nEst+"-"+nUnidad+"-"+nSala+"-"+cantSala+"-"+idSala);
		$('#id-establecimiento').val(nEst);
		$('#id-servicio').val(nUnidad);
		$('#id-sala').val(nSala);
		$('#cant-cama').val(cantSala);
		$('#cant-vieja').val(cantSala);
		$('#u-sala').val(idSala);
		$("#modalEditar").modal();
	}

	var eliminar=function(id){
		swalNormal.fire({
		title: 'Eliminar',
		});
	}

/*
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
*/
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

<br>
<!-- Editar -->
<div id="modalEditar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Editar cama</h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url' => 'editarCama', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'editarCamasForm')) }}
				<div class="row">
					<div class="form-group col-md-12">
						<label for="rut" class="col-sm-2 control-label">Establecimiento: </label>
						<div class="col-sm-10">
							<div class="input-group">
								{{Form::text('id-establecimiento', null, array('id' => 'id-establecimiento', 'class' => 'form-control', 'readonly'))}}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="rut" class="col-sm-2 control-label">Servicio: </label>
						<div class="col-sm-10">
							<div class="input-group">
								{{Form::text('id-servicio', null, array('id' => 'id-servicio', 'class' => 'form-control', 'readonly'))}}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="rut" class="col-sm-2 control-label">Sala: </label>
						<div class="col-sm-10">
							<div class="input-group">
								{{Form::text('id-sala', null, array('id' => 'id-sala', 'class' => 'form-control', 'readonly'))}}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="rut" class="col-sm-2 control-label">Cama: </label>
						<div class="col-sm-10">
							<div class="input-group">
								{{Form::text('cant-cama', null, array('id' => 'cant-cama', 'class' => 'form-control', 'autofocus' => 'true'))}}
								{{ Form::hidden('cant-vieja', '', array('id' => 'u-sala')) }}
								{{ Form::hidden('u-sala', '', array('id' => 'u-sala')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!--{{Form::submit('Aceptar', array('id' => 'editarCama', 'class' => 'btn btn-primary')) }}-->
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

{{ Form::close() }}
@stop