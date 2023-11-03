@extends("Templates/template")

@section("titulo")
Gestión Unidad
@stop

@section("miga")
<li><a href="#">Administración</a></li>
<li>{{ HTML::link(URL::route('gestionEstablecimientos'), 'Gestión de establecimiento')}}</li>
<li><a href="#" onclick='location.reload()'>{{$nombre}}</a></li>
@stop

@section("css")
<style>
	.lista_orden_areas{
		border-style: solid;
		border-width:1px;
		border-radius:10px;
		border-color: black;
		padding:10px;
		background-color: white;
		
	}
	.lista_orden_areas .area_funcional{
		padding:5px;
		font-size:16px;
		font-weight: bold;
	}
	.lista_orden_areas .area_funcional:hover{
		background-color: green;
	}
	.lista_orden_areas .area_funcional.seleccionado{
		background-color: steelblue;
	}
	.botones_orden_areas{
		padding:10px;
	}
	.botones_orden_areas button{
		width:100%;
		
	}
</style>
@stop

@section("script")
<!-- script para la lista de orden -->
<script>
$(function(){
	var item_seleccionado = null;
	$(document).on("click",".lista_orden_areas .area_funcional",function(){
		deseleccionar();
		seleccionar(this);
	});
	function deseleccionar(){
		$(".lista_orden_areas .area_funcional").removeClass("seleccionado");
		$(".botones_orden_areas button").prop("disabled",true);
		item_seleccionado = null;
	}
	function seleccionar(item){
		$(item).addClass("seleccionado");
		$(".botones_orden_areas button").prop("disabled",false);
		item_seleccionado = item;
	}
	
	function ordenar(){
		$(".lista_orden_areas .area_funcional").each(function(indice,elemento){
			$(elemento).data("orden",indice + 1);
		});
	}
	function agregarAreaFuncional(id,nombre,orden){
		var $item = $("<div>");
		$item.addClass("area_funcional");
		$item.data("id",id);
		$item.data("orden",orden);
		$item.text(nombre);
		$(".lista_orden_areas").append($item);
	}
	
	function cargar(){
		$(".lista_orden_areas ").empty();
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/areasFuncionalesEstablecimientoOrdenadas",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			dataType: "json",
			type: "post",
			success: function(data){

				for(var i = 0; i < data.length; i++){
					agregarAreaFuncional(data[i].id,data[i].nombre,data[i].orden);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}
	
	$("#btn_primero_areas").on("click",function(){
		$(".lista_orden_areas").prepend(item_seleccionado);
		ordenar();
	});
	
	$("#btn_arriba_areas").on("click",function(){
		$(item_seleccionado).insertBefore($(item_seleccionado).prev());
		ordenar();
	});
	
	$("#btn_abajo_areas").on("click",function(){
		$(item_seleccionado).insertAfter($(item_seleccionado).next());
		ordenar();
	});
	
	$("#btn_ultimo_areas").on("click",function(){
		$(".lista_orden_areas").append(item_seleccionado);
		ordenar();
	});
	
	function datos(){
		var datos = [];
		$(".lista_orden_areas .area_funcional").each(function(indice,elemento){
			var d = {
				id: $(elemento).data("id"),
				orden: $(elemento).data("orden")
			};
			datos.push(d);
		});
		console.log("datos",datos);
		return datos;
		
	}
	function guardar(){
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/guardarOrdenAreasFuncionales",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			dataType: "json",
			data:{datos: datos()},
			type: "post",
			success: function(data){
				if(!data.error){
					swalExito.fire({
						title: 'Exito!',
						text: "Se ha guardado correctamente",
						didOpen: function() {
							setTimeout(function() {
								cargar();
								cargarMenuOpciones();
							}, 2000)
						},
					});
				}
				else{
					swalError.fire({
						title: 'Error',
						text:data.msg
					});
				} 
			},
			error: function(error){
				console.log(error);
			},
			complete:function(){
				$("#form_orden_areas button[type='submit']").prop("disabled", false);
			}
		});
	}
	
	deseleccionar();
	cargar();
	
	$("#form_orden_areas").bootstrapValidator().on("success.form.bv", function(evt){
		evt.preventDefault();
		$("#form_orden_areas button[type='submit']").prop("disabled", true);
		guardar();
	});
	
});
</script>
<script>
var tableUnidad=null;
var tableAreas=null;

function cambiarNombreArea(id,nombre,orden){
	$("#idArea").val(id);
	$("#nombreAreaFuncional").val(nombre);
	$("#modalEditarArea").modal("show");
	$("#formEditarNombreArea").bootstrapValidator("revalidateField", "nombre-Area-Funcional");
	// $("#btnCrearUnidad").prop("disabled", false);
}

function generarTablaUnidades(){
	tableUnidad=$('#tableUnidades').dataTable({
		"bJQueryUI": true,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerUnidades/{{$idEstab}}",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});
}

function generarTablaAreas(){
	tableAreas=$('#tableAreas').dataTable({
		"bJQueryUI": true,
		"ajax": "{{URL::to('/')}}/administracionUnidad/obtenerTodasAreasFuncionales",
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		}
	});
}

function recargarSelectAreasFuncionales(){
	$.ajax({
		url: "{{URL::to('/')}}/administracionUnidad/todasAreasFuncionales",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		// data: {idCaso: idCaso},
		dataType: "json",
		type: "post",
		success: function(data){
			console.log(data);
			// $('#area_funcional').children('option:not(:first)').remove().end();
			var html = "<select name='area-funcional' id='area-funcional' class='form-control'>";

				data.forEach(function(element){
					html +=  "<option value="+element.id+">"+element.nombre+"</option>";
				});

				html += "</select>";
				//se anade al select
				$("#areasF").find('#area-funcional').remove().end().append(html);
				console.log("recargando");
		},
		error: function(error){
			console.log(error);
		}
	});
}

$(function(){
	generarTablaUnidades();
	generarTablaAreas();

	$("#unidades").click(function() {
		recargarSelectAreasFuncionales();
		if (typeof tableUnidad.api().ajax.reload() !== 'undefined'){
			tableUnidad.api().ajax.reload();
		}else{
			generarTablaUnidades();
		}
	});

	$("#areas").click(function() {
		if (typeof tableAreas.api().ajax.reload() !== 'undefined'){
			tableAreas.api().ajax.reload();
		}else{
			generarTablaAreas();
		}
	});

	$("#administracionMenu").collapse();




	$("#crearUnidadForm").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			nombreUnidad: {
				validators:{
					notEmpty: {
						message: 'El nombre de la unidad es obligatorio'
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
		$("#crearUnidadForm input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "crearUnidad",
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
					tableUnidad . api() . ajax . reload();

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

	$("#crearAreaForm").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			nombreArea: {
				validators:{
					notEmpty: {
						message: 'El nombre del área funcional es obligatorio'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#crearAreaForm input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "crearArea",
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
							tableAreas.api().ajax.reload();
							recargarSelectAreasFuncionales();
							$("#nombreArea").val('');
							$("#crearAreaForm").bootstrapValidator("revalidateField", "nombreArea");
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

	$("#formEditarNombreArea").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			"nombre-Area-Funcional": {
				validators:{
					notEmpty: {
						message: 'El nombre del área funcional es obligatorio'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		$("#formEditarNombreArea input[type='submit']").prop("disabled", false);
		$.ajax({
			url: "{{URL::to('/')}}/administracionUnidad/updateAreaFuncional",
			type: "post",
			dataType: "json",
			data: $form .serialize(),
			success: function(data){
				$('#formEditarNombreArea').trigger("reset");
				if(data.exito){
					$("#btnCrearUnidad").prop("disabled", false);
					$("#modalEditarArea").modal("hide");
					swalExito.fire({
					title: 'Exito!',
					text: data.exito,
					didOpen: function() {
						setTimeout(function() {
						tableAreas.api().ajax.reload();
							$("#nombreArea").val('');
							$("#formEditarNombreArea").bootstrapValidator("revalidateField", "nombre-Area-Funcional");
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

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")
	@if($estab->some)
		@include("AdministracionUnidad/Alerta")
	@endif

	<div class="container">
		<ul class="nav nav-tabs">
			<li class="nav active" id="unidades"><a href="#1u" data-toggle="tab">Unidades</a></li>
			<li class="nav" id="areas"><a href="#1a" data-toggle="tab">Áreas Funcionales</a></li>
		</ul>
		<div class="tab-content">
          <div class="tab-pane fade in active" id="1u">
		  <br>
			<fieldset>
				<legend>Crear nueva unidad</legend>
				{{ Form::open(array('id' => 'crearUnidadForm', 'url' => '#', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form')) }}
				{{ Form::hidden('establecimiento', "$idEstab", array('id' => 'estabHidden')) }}
				<div class="form-group" id="areasF">
					{{Form::text('nombreUnidad', null, array('id' => 'nombreUnidad', 'class' => 'form-control', 'placeholder' => 'Nombre unidad'))}}
					{{Form::select("tipo-unidad", $tipoUnidad, null, ["class" => "form-control", "id" => "tipo_unidad", "placeholder" => "Seleccione el tipo de unidad"]) }}
					<div class="form-group">{{Form::select("area-funcional", $areaFuncional, null, ["class" => "form-control", "id" => "area-funcional", "placeholder" => "Seleccione el area funcional"]) }}</div>
					{{Form::number('dotacionCamas', null, array('id' => 'dotacionCamas', 'class' => 'form-control', 'placeholder' => 'Dotación Camas'))}}
					{{-- <input type="checkbox" name="unidad_ginecologica" id="unidad_ginecologica" class="form-control"> <label for="unidad_ginecologica">Es unidad ginecológica</label> --}}
					{{Form::select("subcategoria_unidad", App\Models\SubcategoriaUnidad::pluck('nombre','id'), null, ["class" => "form-control", "id" => "subcategoria_unidad", "placeholder" => "Seleccione la subcategoria"]) }}
				</div>
				{{Form::submit('Crear unidad', array('class' => 'btn btn-primary', 'id' => 'btnCrearUnidad')) }}
				{{ Form::close() }}
			</fieldset>
			<br><br>
			<div class="table-responsive">
				<table id="tableUnidades" class="table table-striped table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nombre unidad</th>
							<th>Tipo Unidad</th>
							<th>Area Funcional</th>
							<th>Dotación de Camas</th>
							<th>Opción</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				</div>
			</div>
          <div class="tab-pane fade" id="1a">
		  	<br>
		  	<fieldset>
				<legend>Crear nueva Área Funcional</legend>
				{{ Form::open(array('id' => 'crearAreaForm', 'url' => '#', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form')) }}
				{{ Form::hidden('establecimiento', "$idEstab", array('id' => 'estabHidden')) }}
				<div class="form-group">
					{{Form::text('nombreArea', null, array('id' => 'nombreArea', 'class' => 'form-control', 'placeholder' => 'Nombre Área Funcional'))}}
				</div>
				{{Form::submit('Crear Área', array('class' => 'btn btn-primary')) }}
				{{ Form::close() }}
			</fieldset>
			<br><br>
			<div class="table-responsive">
				<table id="tableAreas" class="table table-striped table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nombre Área Funcional</th>
							<th>Opción</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				</div>
				<div id="ordenamiento_areas_funcionales" class="container">
					<fieldset>
						<legend>Orden de áreas funcionales</legend>
						<form id="form_orden_areas">
							<div class="row" style="margin-bottom:15px;">
								<div class="lista_orden_areas col-sm-10">
								</div>
								<div class="botones_orden_areas col-sm-2">
									<button class="btn btn-default" type="button" id="btn_primero_areas">↑↑</button>
									<button class="btn btn-default" type="button" id="btn_arriba_areas">↑</button>
									<button class="btn btn-default" type="button" id="btn_abajo_areas">↓</button>
									<button class="btn btn-default" type="button" id="btn_ultimo_areas">↓↓</button>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<button type="submit" class="btn btn-primary">Guardar orden</button>
								</div>
							</div>
						</form>
					</fieldset>
				</div>
			</div>
          </div>
      </div>
	</div>
	<div id="modalEditarArea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Editar Área Funcional</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarNombreArea')) }}
			{{ Form::hidden('idArea', null, array('id' => 'idArea')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label class="col-sm-2 control-label">Nombre: </label>
						<div class="col-sm-10">
							{{Form::text('nombre-Area-Funcional', null, array('id' => 'nombreAreaFuncional', 'class' => 'form-control', 'autofocus' => 'true'))}}
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
