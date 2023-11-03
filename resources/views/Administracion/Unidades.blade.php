@extends("Templates/template")

@section("titulo")
Gestión Camas
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión Unidades</a></li>
@stop

@section("script")
<script>

var getSalas=function(){
	var establecimiento=$("#unidades").val();
	if(establecimiento != null){
		$.ajax({
			url: "getSalas",
			type: "post",
			dataType: "json",
			data: {establecimiento: establecimiento},
			async: false,
			success: function(data){
				var tabla=$("#tSalas").dataTable();
				tabla.fnClearTable();
				for(var i=0; i<data.length; i++){
					tabla.fnAddData([data[i].nombre, data[i].idSala, data[i].editar]);   
				} 
			},
			error: function(error){
				console.log(error);
			}
		});
	}
	else $("#tSalas").dataTable().fnClearTable();
}

var getSalasSelect=function(){
	var value=$("#unidades").val();
	if(value != null){
		$.ajax({
			url: "getSalasSelect",
			type: "post",
			dataType: "json",
			async: false,
			data: {establecimiento: $("#unidades").val()},
			success: function(data){
				$("#salasSelect").empty();
				for(var i=0; i<data.length; i++){
					var option="<option value='"+data[i].id+"'>"+data[i].nombre+"</option>";
					$("#salasSelect").append(option);
				}
			},
			error: function(error){
				console.log(data);
			}
		});
	}
	else $("#salasSelect").empty();
}

var getCamas=function(){
	var establecimiento=$("#unidades").val();
	var sala=$("#salasSelect").val();
	if(establecimiento != null && sala != null){
		$.ajax({
			url: "getCamas",
			type: "post",
			dataType: "json",
			async: false,
			data: {establecimiento: establecimiento, sala: sala},
			success: function(data){
				var tabla=$("#tCama").dataTable();
				tabla.fnClearTable();
				for(var i=0; i<data.length; i++){
					tabla.fnAddData([data[i].idCama, data[i].tipo, data[i].diferenciacion, data[i].editar]);   
				} 
			},
			error: function(error){
				console.log(error);
			}
		});
	}
	else $("#tCama").dataTable().fnClearTable();
}

var editar=function(id, url){
	$.ajax({
		url: url,
		data: {id: id},
		dataType: "json",
		type: "post",
		success: function(data){

		},
		error: function(error){
			console.log(error);
		}
	});
}

var editarSala=function(id, nombre){
	$("#formEditarSala input[name='sala']").val(nombre);
	$("#idSalaH").val(id);
	$("#modalEditarSala").modal("show");
}

var editarCama=function(id, cama, tipo, diferenciacion){
	$("#formEditarCama input[name='cama']").val(cama);
	$("#tipoCamaE").val(tipo);
	$("#diferenciacionE").val(diferenciacion);
	$("#idCamaH").val(id);
	$("#modalEditarCama").modal("show");
}

var idSalaUnico=function(id){
	var esUnico=false;
	$.ajax({
		url: "idSalaUnico",
		dataType: "json",
		data: {id: id, establecimiento: $("#unidades").val()},
		type: "post",
		async: false,
		success: function(data){
			esUnico=data.unico;
		},
		error: function(error){
			console.log(error);
		}
	})
	return esUnico;
}

$(function(){
	$("#administracionMenu").collapse();

	$("#unidadEnSala").val($("#unidades").val());

	$("#tUnidades, #tSalas, #tCama").dataTable({
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": -1,
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});

	$("#salasSelect").on("change", function(){
		getCamas();
	});

	$("#unidades").on("change", function(){
		var value=$(this).val();
		$("#unidadEnSala").val(value);
		getSalas();
		getSalasSelect();
		getCamas();
	});

	$("#establecimiento").on("change", function(){
		var value=$(this).val();
		$("#estabHidden").val(value);
		$.ajax({
			url: "getUnidades",
			data: {establecimiento: value},
			type: "post",
			dataType: "json",
			async: false,
			success: function(data){
				var tabla=$("#tUnidades").dataTable();
				tabla.fnClearTable();
				$("#unidades").empty();
				for(var i=0; i<data.length; i++){
					tabla.fnAddData([data[i].nombre]);   
					var option="<option value='"+data[i].id+"'>"+data[i].nombre+"</option>";
					$("#unidades").append(option);
				} 
			},
			error: function(error){
				console.log(error);
			}
		});
		getSalas();
		getSalasSelect();
		getCamas();
		$("#unidadEnSala").val($("#unidades").val());
	});

	$(".formCrear").bootstrapValidator({
		excluded: ':disabled',
		group: '.error',
		fields: {
			nombreUnidad: {
				validators:{
					notEmpty: {
						message: 'El nombre de la unidad es obligatorio'
					}
				}
			},
			sala: {
				validators:{
					notEmpty: {
						message: 'El nombre de la sala es obligatoria'
					}
				}
			},
			cama: {
				validators:{
					notEmpty: {
						message: 'El nombre de la cama es obligatorio'
					}
				}
			},
			idSala: {
				validators:{
					notEmpty: {
						message: 'El id de la sala es obligatorio'
					},
					callback: {
						message: "El id de la sala se encuentra registrado",
						callback: function(value, validator, $field){
							if(value == "") return false;
							if(!/^-?[0-9]+$/.test(value)) return {valid: false, message: "El id de la sala debe ser un valor numérico"};
							if(idSalaUnico(value)) return true;
							return {valid: false, message: "El id de la sala se encuentra registrado"};
						}
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$(".formCrear input[type='submit']").prop("disabled", false);
		$.ajax({
			url: $form .prop("action"),
			type: "post",
			dataType: "json",
			data: $form .serialize(),
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
	});

});

</script>
@stop

@section("section")

<fieldset>
	<legend>Gestión de unidades</legend>
	<div class="row">
		{{ Form::open(array('url' => 'administracion/editar', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formEditar', 'style' => 'padding-left: 15px;')) }}
		<div class="form-group">
			{{ Form::select('establecimiento', $establecimientos, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
		</div>
		<div class="form-group">
			{{ Form::select('unidad', $unidades, null, array('class' => 'form-control', 'id' => 'unidades')) }}
		</div>
		<div class="form-group">
			{{Form::submit('Editar unidad', array('id' => 'btnEditar', 'class' => 'btn btn-primary')) }}
		</div>
		{{ Form::close() }}
	</div>
	<br><br>
	<div class="table-responsive">
	<table id="tUnidades" class="table table-striped table-bordered table-hover" style="margin-top: 20px;">
		<thead>
			<tr>
				<th>Unidades</th>
			</tr>
		</thead>
		<tbody>
			@foreach($unidades as $key => $value)
			<tr> <td>{{$value}}</td> </tr>
			@endforeach
		</tbody>
	</table>
	</div>
	<legend>Crear nueva unidad</legend>
	<div class="row error">
		{{ Form::open(array('url' => 'administracion/crearUnidad', 'method' => 'post', 'class' => 'form-inline formCrear', 'role' => 'form', 'style' => 'padding-left: 15px;')) }}
		{{ Form::hidden('establecimiento', $idEstab, array('id' => 'estabHidden')) }}
		{{Form::text('nombreUnidad', null, array('id' => 'nombreUnidad', 'class' => 'form-control', 'placeholder' => 'Nombre unidad'))}}
		{{Form::submit('Crear unidad', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
	<br><br>
	<div class="row">
		<div class="col-md-6">
		<fieldset>
			<legend>Gestión de salas</legend>
			<div class="table-responsive">
			<table id="tSalas" class="table table-striped table-bordered table-hover" style="margin-top: 20px;">
				<thead>
					<tr>
						<th>Nombre sala</th>
						<th>ID Sala</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody>
					@foreach($salas as $value)
					<tr> 
						<td>{{$value["nombre"]}}</td> 
						<td>{{$value["idSala"]}}</td> 
						<td>{{$value["editar"]}}</td> 
					</tr>
					@endforeach
				</tbody>
			</table>
			</div>
			<legend>Crear nueva sala</legend>
			<div class="col-md-12">
				{{ Form::open(array('url' => 'administracion/crearSala', 'method' => 'post', 'class' => 'form-horizontal formCrear', 'role' => 'form', 'style' => 'padding-left: 15px;')) }}
				{{ Form::hidden('unidadEn', $idEstab, array('id' => 'unidadEnSala')) }}
				<div class="form-group error">
					{{Form::text('sala', null, array('id' => 'sala', 'class' => 'form-control', 'placeholder' => 'Nombre sala'))}}
				</div>
				<div class="form-group error">
					{{Form::text('idSala', null, array('id' => 'idSala', 'class' => 'form-control', 'placeholder' => 'ID sala', 'onkeypress' => 'return soloNumeros(event);'))}}
				</div>
				{{ Form::submit('Crear sala', array('class' => 'btn btn-primary')) }}
				{{ Form::close() }}	
			</div>
			</fieldset>
		</div>
		<div class="col-md-6">
		<fieldset>
			<legend>Gestión de camas</legend>
			<div class="table-responsive">
			<table id="tCama" class="table table-striped table-bordered table-hover" style="margin-top: 20px;">
				<thead>
					<tr>
						<th>Cama</th>
						<th>Tipo</th>
						<th>Diferenciación</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody>
				@foreach($camas as $cama)
					<tr> 
						<td>{{$cama["idCama"]}}</td> 
						<td>{{$cama["tipo"]}}</td> 
						<td>{{$cama["diferenciacion"]}}</td> 
						<td>{{$cama["editar"]}}</td> 
					</tr>
				@endforeach
				</tbody>
			</table>
			</div>
			<legend>Crear nueva cama</legend>
			{{ Form::open(array('url' => 'administracion/crearCama', 'method' => 'post', 'class' => 'form-horizontal formCrear', 'role' => 'form', 'style' => 'padding-left: 15px;')) }}
			<div class="form-group error">
				{{Form::text('cama', null, array('id' => 'cama', 'class' => 'form-control', 'placeholder' => 'Nombre Cama', 'maxlength' => '10' ))}}
			</div>
			<div class="form-group error">
				<label class="col-sm-2 control-label">Nombre sala: </label>
				<div class="col-sm-10">
					{{ Form::select('salasSelect', $salasSelect, null, array('class' => 'form-control', 'id' => 'salasSelect')) }}
				</div>
			</div>
			<div class="form-group error">
				<label class="col-sm-2 control-label">Tipo cama: </label>
				<div class="col-sm-10">
					{{ Form::select('tipoCama', $tipoCama, null, array('class' => 'form-control', 'id' => 'tipoCamaC')) }}
				</div>
			</div>
			<div class="form-group error">
				<label class="col-sm-2 control-label">Diferenciación: </label>
				<div class="col-sm-10">
					{{ Form::select('diferenciacion', $diferenciacion, null, array('class' => 'form-control', 'id' => 'diferenciacionC')) }}
				</div>
			</div>
			{{Form::submit('Crear cama', array('class' => 'btn btn-primary')) }}
			{{ Form::close() }}	
			</fieldset>
		</div>
	</div>
	<br><br>
</fieldset>

<div id="modalEditarSala" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
			</div>
			<form id="formEditarSala" action="editarSala" class="formCrear form-horizontal">
				<input type="hidden" name="idSalaH" id="idSalaH" />
				<div class="modal-body">
					<div class="form-group error">
						<label class="col-md-2">Nombre de sala: </label>
						<div class="col-md-6">
							<input name="sala" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Aceptar</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="modalEditarCama" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
			</div>
			<form id="formEditarCama" action="editarCama" class="formCrear form-horizontal">
				<input type="hidden" name="idCama" id="idCamaH" />
				<div class="modal-body">
					<div class="form-group error">
						<label class="col-md-2">Cama: </label>
						<div class="col-md-6">
							<input name="cama" class="form-control"/>
						</div>
					</div>
					<div class="form-group error">
						<label class="col-md-2">Tipo cama: </label>
						<div class="col-md-6">
							{{ Form::select('tipoCama', $tipoCama, null, array('class' => 'form-control', 'id' => 'tipoCamaE')) }}
						</div>
					</div>
					<div class="form-group error">
						<label class="col-md-2">Diferenciación: </label>
						<div class="col-md-6">
							{{ Form::select('diferenciacion', $diferenciacion, null, array('class' => 'form-control', 'id' => 'diferenciacionE')) }}
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Aceptar</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>

@stop