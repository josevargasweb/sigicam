@extends("Templates/template")

@section("titulo")
Gestión Unidad
@stop

@section("miga")
<li><a href="#">Administración</a></li>
<li>{{ HTML::link(URL::route('gestionEstablecimientos'), 'Gestión de establecimiento')}}</li>
<li>{{ HTML::link("/administracionUnidad/unidad/$idEstab", "$nombre") }}</li>
<li><a href="#" onclick='location.reload()'>{{$alias}}</a></li>
@stop

@section("script")

<script>
var table=null;

var bloquearSala=function(id, nombre){
	bootbox.dialog({
		message: "<h4>¿ Desea bloquear la sala "+nombre+" ?</h4>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "{{URL::to('/')}}/administracionUnidad/bloquearSala",
						data: {id: id},
						dataType: "json",
						type: "post",
						success: function(data){
							table.api().ajax.reload();
							if(data.error){
								swalError.fire({
								title: 'Error',
								text:data.error
								});
							} 
						}
					});
				}
			},
			danger: {
				label: "Cancelar",
				className: "btn-danger",
				callback: function() {
				}
			},
		}
	});
}

var desbloquearSala=function(id, nombre){
	bootbox.dialog({
		message: "<h4>¿ Desea desbloquear la sala "+nombre+" ?</h4>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "{{URL::to('/')}}/administracionUnidad/desbloquearSala",
						data: {id: id},
						dataType: "json",
						type: "post",
						success: function(data){
							table.api().ajax.reload();
							if(data.error){
								swalError.fire({
								title: 'Error',
								text:data.error
								});
							} 
						}
					});
				}
			},
			danger: {
				label: "Cancelar",
				className: "btn-danger",
				callback: function() {
				}
			},
		}
	});
}

 function editarUnidad(){
	 var nombreUnidad = "{{$alias}}";
	 var dotacion = "{{$dotacion}}";
	 $("#nombre").val(nombreUnidad);
	 $("#dotacion").val(dotacion);
	 $("#modalEditarUnidad").modal("show");
 }

$(function(){

	$("#administracionMenu").collapse();

	table=$('#tableSalasCamas').dataTable({
		"bJQueryUI": true,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerSalasCamas/{{$idEstab}}/{{$idUnidad}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	$('#tablaServiciosOfrecidos').dataTable({
		"bJQueryUI": true,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerServiciosOfrecidos/{{$idEstab}}/{{$idUnidad}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	$('#tablaServiciosRecibidos').dataTable({
		"bJQueryUI": true,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerServiciosRecibidos/{{$idEstab}}/{{$idUnidad}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	})

	$("#formEditarUnidad").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			nombre: {
				validators:{
					notEmpty: {
						message: 'El nombre de la unidad es obligatorio'
					}
				}
			},
			"tipo-unidad": {
				validators:{
					notEmpty: {
						message: 'El tipo de unidad es obligatorio'
					}
				}
			},
			"area-funcional": {
				validators:{
					notEmpty: {
						message: 'El área funcional es obligatoria'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#formEditarUnidad input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/updateUnidad",
			type: "post",
			dataType: "json",
			data: $form .serialize(),
			success: function(data){
				$('#formEditarUnidad').trigger("reset");
				if(data.exito){
					$("#modalEditarUnidad").modal("hide");
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
	});

	$("#formCrearSala").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			sala: {
				validators:{
					notEmpty: {
						message: 'El nombre de la sala es obligatorio'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#formCrearSala input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/crearSala",
			type: "post",
			dataType: "json",
			data: $form .serialize(),
			success: function(data){
				$('#formCrearSala').trigger("reset");
				if(data.exito == "") table.api().ajax.reload();;
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
	});
});

</script>

@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")
	@if($estab->some)
		@include("AdministracionUnidad/Alerta")
	@endif

	<div class="form-inline">
	<div class="form-group">
		<label>Nombre</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" value="{{$alias}}" disabled/>
	</div>
	<div class="form-group">
		<label>Unidad</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" value="{{$nombreUnidad}}" disabled/>
	</div>
	<div class="form-group">
		<label>Área Funcional</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" value="{{$nombreArea}}" disabled/>
	</div>
	<div class="form-group">
		<label>Dotación de Camas</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" value="{{$dotacion}}" disabled/>
	</div>
	<div class="form-group">
		{{Form::select("", App\Models\SubcategoriaUnidad::pluck('nombre','id'), $subcategoria_unidad, ["class" => "form-control", "placeholder" => "Sin subcategoria", "disabled"]) }}
	</div>
	<div class="form-group">
		<input type="checkbox" class="form-control" @if($cama_temporal) checked @endif disabled/> Cama volantes activa
	</div>
	<div class="form-group">
		<button type="button" class="btn btn-primary" onclick="editarUnidad()">Editar</button>
	</div>
</div>
<br><br>

{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formCrearSala')) }}
<div class="form-group">
	<label>Nombre sala: </label>
</div>

{{ Form::hidden('idEstab', $idEstab) }}
{{ Form::hidden('idUnidad', $idUnidad) }}
<div class="form-group">
	{{Form::text('sala', null, array('id' => 'sala', 'class' => 'form-control', 'placeholder' => 'Nombre sala'))}}
</div>
{{ Form::submit('Crear sala', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}
<br><br>
<div class="table-responsive">
<table id="tableSalasCamas" class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>Salas</th>
			<th>Camas</th>
			<th>Opciones</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
<fieldset>
	<legend>Servicios ofrecidos</legend>
	<div class="table-responsive">
	<table id="tablaServiciosOfrecidos" class="table table-striped table-condensed table-bordered">
		<thead>
			<tr>
				<th>Servicio</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
			<tr>
				<td>
					{{HTML::link("/administracionUnidad/editarServicioView/$idEstab/$idUnidad/ofrecido", "Editar", ["class" => "btn btn-primary"])}}
				</td>
			</tr>
		</tfoot>
	</table>
	</div>
</fieldset>
<br><br>
<fieldset>
	<legend>Servicios recibidos</legend>
	<div class="table-responsive">
	<table id="tablaServiciosRecibidos" class="table table-striped table-condensed table-bordered">
		<thead>
			<tr>
				<th>Servicio</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
			<tr>
				<td>
					{{HTML::link("/administracionUnidad/editarServicioView/$idEstab/$idUnidad/recibido", "Editar", ["class" => "btn btn-primary"])}}
				</td>
			</tr>
		</tfoot>
	</table>
	</div>
</fieldset>

<div id="modalEditarUnidad" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Editar unidad</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarUnidad')) }}
			{{ Form::hidden('idEstab', "$idEstab", array('id' => 'idEstab')) }}
			{{ Form::hidden('idUnidad', "$idUnidad", array('id' => 'idUnidad')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Nombre: </label>
						<div class="col-sm-10">
							{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control', 'autofocus' => 'true'))}}
						</div>
					</div>
				</div>
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Tipo Unidad: </label>
						<div class="col-sm-10">
						{{Form::select("tipo-unidad", $tipoUnidad, $unidadSeleccionada, ["class" => "form-control", "id" => "tipo_unidad", "placeholder" => "Seleccione el tipo de unidad"]) }}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Área Funcional: </label>
						<div class="col-sm-10">
						{{Form::select("area-funcional", $areasFuncionales, $areaSeleccionada, ["class" => "form-control", "id" => "area_funcional", "placeholder" => "Seleccione el area funcional"]) }}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Dotación de Camas: </label>
						<div class="col-sm-10">
						{{Form::number("dotacion", null, array('id' => 'dotacion', 'class' => 'form-control', 'autofocus' => 'true'))}}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Sub categoria: </label>
						<div class="col-sm-10">
							{{Form::select("subcategoria_unidad", App\Models\SubcategoriaUnidad::pluck('nombre','id'), $subcategoria_unidad, ["class" => "form-control", "id" => "subcategoria_unidad", "placeholder" => "Seleccione la subcategoria"]) }}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Activar cama temporal: </label>
						<div class="col-sm-10">
							<input type="checkbox" name="cama_temporal" @if($cama_temporal) checked @endif>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="solicitar" type="submit" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

@stop
