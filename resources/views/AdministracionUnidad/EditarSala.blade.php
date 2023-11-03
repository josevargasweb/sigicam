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

<script>
var tableCama=null;
var tablaEliminadas=null;
var tablaBloqueadas=null;

var cambiarNombreCama=function(idCama){
	$("#formNombreCama input[name='idCama']").val(idCama);
	$.ajax({
		url: "{{URL::route('obtenerInfoCama')}}",
		data: {"idCama": idCama},
		dataType: "json",
		success: function(data){
			$("#formNombreCama select[name='tipo-cama']").val(data.tipo);
			$("#formNombreCama select[name='tipo-unidad']").val(data.tipo_unidad);
			//$("#formNombreCama select[name='tipo-cama']").bootstrapValidator("revalidate");
			$("#formNombreCama input[name='nombre']").val(data.id_cama);
			//$("#formNombreCama").bootstrapValidator();
			$("#modalNombreCama").modal("show");
		},
		error: function(error){
			$("#modalNombreCama").modal("show");
		}

	});

}

var eliminarCama=function(idCama, nombre){
	bootbox.dialog({
		message: "<h4>¿ Desea eliminar la cama "+nombre+" ?</h4>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "{{URL::to('/')}}/administracionUnidad/eliminarCama",
						headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
						data:{idCama: idCama},
						type: "post",
						dataType: "json",
						success: function(data){
							tableCama.api().ajax.reload();
							tablaEliminadas.api().ajax.reload();
							tablaBloqueadas.api().ajax.reload();
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
			}
		}
	});
}

var bloquearCama=function(idCama, nombre){

	bootbox.dialog({
		message: "<div id='dialogBloquearCama'><h4>¿ Desea bloquear la cama "+nombre+" ?</h4>" +
		$("#divBloquearCama").html() +
		"</div>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "{{URL::to('/')}}/administracionUnidad/bloquearCama",
						headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
						data:{idCama: idCama, motivo: $("#dialogBloquearCama form select[name='motivo']").val()},
						type: "post",
						dataType: "json",
						success: function(data){
							tableCama.api().ajax.reload();
							tablaEliminadas.api().ajax.reload();
							tablaBloqueadas.api().ajax.reload();
							if(data.error){
								swalError.fire({
								title: 'Error',
								text:data.error
								})
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
			}
		}
	});
}

var desbloquearCama=function(id, nombre){
	bootbox.dialog({
		message: "<h4>¿ Desea desbloquear la cama "+nombre+" ?</h4>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "{{URL::to('/')}}/administracionUnidad/desbloquearCama",
						headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
						data:{idCama: id},
						type: "post",
						dataType: "json",
						success: function(data){
							tableCama.api().ajax.reload();
							tablaEliminadas.api().ajax.reload();
							tablaBloqueadas.api().ajax.reload();
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
			}
		}
	});
}

var cancelarCama=function(id, nombre){
	bootbox.dialog({
		message: "<h4>¿ Desea cancelar la cama "+nombre+" ?</h4>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "{{URL::to('/')}}/administracionUnidad/cancelarCama",
						headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
						data:{idCama: idCama},
						type: "post",
						dataType: "json",
						success: function(data){
							tableCama.api().ajax.reload();
							tablaEliminadas.api().ajax.reload();
							tablaBloqueadas.api().ajax.reload();
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
			}
		}
	});
}

	function cambiarNombreSala(){
		var nombreSala = "{{$nombreSala}}";
		$("#nombreSala").val(nombreSala);
		$("#modalEditarSala").modal("show");
	}

$(function(){

	$("#administracionMenu").collapse();

	tableCama=$('#tablaCamas').dataTable({	
		"bJQueryUI": true,
		'iDisplayLength': -1,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerCamasVigentes/{{$idSala}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	tablaEliminadas=$('#tablaCamasEliminadas').dataTable({	
		"bJQueryUI": true,
		'iDisplayLength': -1,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerCamasEliminadas/{{$idSala}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	tablaBloqueadas=$('#tablaCamasBloqueadas').dataTable({	
		"bJQueryUI": true,
		'iDisplayLength': -1,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerCamasBloqueadas/{{$idSala}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	$("#formEditarSala").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			nombre: {
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
		$("#formEditarSala input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/updateNombreSala",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			type: "post",
			dataType: "json",
			data: $form.serialize(),
			success: function(data){
				$('#formEditarSala').trigger("reset");
				if(data.exito){ 
					$("#modalEditarSala").modal("hide");
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

$("#formBorrarSala").bootstrapValidator({
		excluded: ':disabled',
		fields: {

		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#formEditarSala input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/borrarSala",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			type: "post",
			dataType: "json",
			data: $form.serialize(),
			success: function(data){
				$('#formBorrarSala').trigger("reset");
				if(data.exito){ 
					swalExito.fire({
					title: 'Exito!',
					text: data.exito,
					didOpen: function() {
						setTimeout(function() {
							window.location.href="{{URL::to('/')}}/administracionUnidad/editarUnidad/<?php echo $idEstab;?>/<?php echo $idUnidad;?>";
						}, 2000)
					},
					});
				}
				if(data.error){
					swalError.fire({
					title: 'Error',
					text:data.error
					}).then(function(result) {
						if (result.isDenied) {
							window.location.href="{{URL::to('/')}}/administracionUnidad/editarUnidad/<?php echo $idEstab;?>/<?php echo $idUnidad;?>";
						}
					});
				} 
			},
			error: function(error){
				console.log(error);
			}
		});
	});


	$("#formNombreCama").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			nombre: {
				validators:{
					notEmpty: {
						message: 'El nombre de la cama es obligatorio'
					}
				}
			},
			"tipo-cama": {
				validators:{
					notEmpty: {
						message: 'Debe seleccionar el tipo de cama'
					}
				}
			},
			"tipo-unidad": {
				validators:{
					notEmpty: {
						message: 'Debe seleccionar el tipo de unidad'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#formNombreCama input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/cambiarNombreCama",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			type: "post",
			dataType: "json",
			data: $form.serialize(),
			success: function(data){
				$('#formNombreCama').trigger("reset");
				tableCama.api().ajax.reload();
				$("#modalNombreCama").modal("hide");
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

	$("#formAgregarCamas").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			numCamas: {
				validators:{
					notEmpty: {
						message: 'El número de camas es obligatorio'
					}
				}
			},
			"tipo-cama": {
				validators:{
					notEmpty: {
						message: 'Debe seleccionar el tipo de cama'
					}
				}
			},
			"tipo-unidad": {
				validators:{
					notEmpty: {
						message: 'Debe seleccionar el tipo de unidad'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#formAgregarCamas input[type='submit']").prop("disabled", false);
		var camas=$("input[name='numCamas']").val();
		bootbox.confirm("<h4>¿ Desea agregar "+camas+" camas a la sala {{$nombreSala}} ?</h4>", function (result) {
			if (result) {
				$.ajax({
					url: "{{URL::to('/')}}/administracionUnidad/agregarCamas",
					headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
					type: "post",
					dataType: "json",
					data: $form.serialize(),
					success: function(data){
						$('#formAgregarCamas').trigger("reset");
						tableCama.api().ajax.reload();
						if(data.exito){ 
							swalExito.fire({
							title: 'Exito!',
							text: data.exito,
							didOpen: function() {
								setTimeout(function() {
									$("#formAgregarCamas").bootstrapValidator("revalidateField", "numCamas");
									$("#formAgregarCamas").bootstrapValidator("revalidateField", "tipo-cama");
									$("#formAgregarCamas").bootstrapValidator("revalidateField", "tipo-unidad");
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
		});
		
	});

});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

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
		<input type="text" class="form-control" value="{{$nombreSala}}" disabled/>
	</div>
	<div class="form-group">
		<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEditarSala">Cambiar</button> -->
		<button type="button" class="btn btn-primary" onclick=cambiarNombreSala()>Cambiar</button>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalBorrarSala">Borrar Sala</button>	
	</div>
</div>
<br><br>

<div class="row">
	{{ Form::open(array('id' => 'formAgregarCamas', 'url' => '#', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form')) }}
	{{ Form::hidden('idSala', $idSala) }}
	<div class="form-group">
		<label>Agregar camas: </label>
	</div>
	<div class="form-group">
		<input type="number" class="form-control" name="numCamas" min="1" placeholder="Ingrese cantidad" />
		<!-- {{Form::select("tipo-cama", App\Models\TipoCama::seleccion(), null, ["class" => "form-control", "id" => "tipo_cama", "placeholder" => "Seleccione el tipo de cama", "required"]) }}
		{{Form::select("tipo-unidad", App\Models\TipoUnidad::seleccion(), null, ["class" => "form-control", "id" => "tipo_unidad", "placeholder" => "Seleccione el tipo de unidad", "required"]) }} -->
		{{Form::select("tipo-cama", $tipoCama, null, ["class" => "form-control", "id" => "tipo_cama", "placeholder" => "Seleccione el tipo de cama"]) }}
		{{Form::select("tipo-unidad", $tipoUnidad, null, ["class" => "form-control", "id" => "tipo_unidad", "placeholder" => "Seleccione el tipo de unidad"]) }}
	</div>
	<div class="form-group">
		{{Form::submit('Agregar', array('class' => 'btn btn-primary')) }}
	</div>
	{{ Form::close() }}	
</div>
<br><br>

<fieldset>
	<legend>Camas vigentes</legend>
	<div class="table-responsive">
	<table id="tablaCamas" class="table table-striped table-condensed table-bordered">
		<thead>
			<tr>
				<th>Cama</th>
				<th>Tipo Cama</th>
				<th>Tipo Unidad</th>
				<th>Fecha creación</th>
				<th>Opciones</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
</fieldset>

<fieldset>
	<legend>Camas bloqueadas</legend>
	<div class="table-responsive">
	<table id="tablaCamasBloqueadas" class="table table-striped table-condensed table-bordered">
		<thead>
			<tr>
				<th>Cama</th>
				<th>Fecha bloqueo</th>
				<th>Motivo del bloqueo</th>
				<th>Opciones</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
</fieldset>

<fieldset>
	<legend>Camas eliminadas</legend>
	<div class="table-responsive">
	<table id="tablaCamasEliminadas" class="table table-striped table-condensed table-bordered">
		<thead>
			<tr>
				<th>Cama</th>
				<th>Fecha eliminación</th>
				<!-- <th>Opciones</th> -->
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
</fieldset>

<div id="modalEditarSala" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Editar nombre sala</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarSala')) }}
			{{ Form::hidden('idSala', "$idSala") }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Nombre: </label>
						<div class="col-sm-10">
							{{Form::text('nombre', null, array('id' => 'nombreSala', 'class' => 'form-control', 'autofocus' => 'true'))}}
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


<div id="modalBorrarSala" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Borrar sala</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formBorrarSala')) }}
			{{ Form::hidden('idSala', "$idSala") }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
				<h4 class="modal-title">¿Esta seguro que desea borrar la sala?</h4>
			    </div>
			</div>
			<div class="modal-footer">
				<button id="solicitar" type="submit" class="btn btn-primary">Si</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

<div id="divBloquearCama" class="hidden">
	{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarSala')) }}
		<div class="row" style="margin: 0;">
			<div class="form-group col-md-12">
				<label class="col-sm-2 control-label">Motivo del bloqueo: </label>
				<div class="col-sm-10">
					{{Form::select('motivo', Consultas::getMotivosBloqueo(), null, array('id' => 'motivos-bloqueos', 'class' => 'form-control', 'autofocus' => 'true'))}}
				</div>
			</div>
		</div>
	{{ Form::close() }}
</div>


<div id="modalNombreCama" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Editar cama</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formNombreCama')) }}
			{{ Form::hidden('idCama', "$idEstab") }}
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
						<label class="col-sm-2 control-label">Tipo Cama: </label>
						<div class="col-sm-10">
						{{Form::select("tipo-cama", $tipoCama, null, ["class" => "form-control", "id" => "tipo_cama", "placeholder" => "Seleccione el tipo de cama"]) }}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Tipo Unidad: </label>
						<div class="col-sm-10">
						{{Form::select("tipo-unidad", $tipoUnidad, null, ["class" => "form-control", "id" => "tipo_unidad", "placeholder" => "Seleccione el tipo de unidad"]) }}
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