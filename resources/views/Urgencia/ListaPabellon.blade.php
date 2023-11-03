@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

<script>
	var table=null;
	var idCaso=null;
	var idLista=null;


	$(function(){
		$("#urgenciaMenu").collapse();
		//alert();
		$.ajax({
			'url': "obtenerListaPabellon",
			//"dataSrc": "",
			'method': "GET",
			'contentType': 'application/json'
		}).done( function(data) {
			$('#listaPabellon').dataTable( {
				"aaData": data,
				"bDestroy": true,
				"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
				},
				"columns": [
					{ "data": "opciones" },
					{ "data": "rut" },
					{ "data": "nombre_completo" },
					{ "data": "diagnostico"},
					{ "data": "fecha_ingreso" },
					{ "data": "comentario"},
					{ "data": "usuario_solicito"},
				],
                initComplete: function(settings){
					var api = new $.fn.dataTable.Api( settings );
					var usuario = "{{Auth::user()->tipo}}";
					if(usuario == "cdt"){
						api.columns(0).visible(false);
					}
				}
			})
		})
	});

	var quitarPabellon=function(idCaso){
		$(".idCaso").val(idCaso);
		$("#modalquitarPabellon").modal("show");
	}

	var generarMapaCamasDisponibles=function(mapaDiv, unidad){
	//alert("map");
	$.ajax({
		url: unidad+"/getCamasDisponiblesVerdes2",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {unidad: unidad, idCaso: $("#casoReasignar").val()},
		dataType: "json",
		type: "post",
		success: function(data){
			crearMapaCamas(mapaDiv, data);
		},
		error: function(error){
			console.log(error);
		}
	});
}

	var getUnidades=function(){
	var unidades=[];
	$.ajax({
		url: "{{URL::to('/')}}/getUnidades",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		type: "post",
		dataType: "json",
		async: false,
		success: function(data){
			unidades=data;
			$("#tabUnidad").empty();
			$("#contentUnidad").empty();
			for(var i=0; i<data.length; i++){
				var nombre=data[i].url;
				var id="id-"+nombre;
				var active = (i == 0) ? "active" : "";
				if(data[i].id_area_funcional == 8){
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+" (Pediatría)</a></li>");
				}else if(data[i].id_area_funcional == 6){
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+" (Adulto)</a></li>");
				}else if(data[i].id_area_funcional == 11){
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+" (Neonatología)</a></li>");
			    }else if(data[i].id_area_funcional == 2 && data[i].alias == "Cuidados medios"){
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+" (Pediatría)</a></li>");
				}else if(data[i].id_area_funcional == 10){
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+" (Neonatología)</a></li>");
				}else{
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab' data-id="+data[i].id+">"+data[i].alias+" </a></li>");
				}

				$("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'></div>");
				generarMapaCamasDisponibles(id, data[i].url, true);
			}
			for(var i=0; i<data.length; i++){
				$("#id-"+data[i].url).removeClass("active");
			}
			if(data.length > 0) {
				//$("#id-"+data[0].url).tab("hide");
				$("#id-"+data[0].url).addClass("active");
				$("#id-"+data[0].url).tab("show");
				}

		},
		error: function(error){
			console.log(error);
		}
	});
	return unidades;
}

var intercambiar = function(idCaso, idCasoOriginal){
	//dialog.modal("hide");

	if(idCaso == idCasoOriginal){
								swalWarning.fire({
								title: 'Información',
								text:"La cama de origen no puede ser igual a la cama de destino"
								})
		return;
	}
	bootbox.dialog({
		message: "<h4>¿ Desea realizar el intercambio de pacientes ?</h4><p>Se realizará un intercambio de pacientes entre las camas.</p>",
		title: "Confirmación",
		buttons: {
			success: {
				label: "Aceptar",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						url: "intercambiar2",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						data: {"idCaso": idCaso, "idCasoOriginal": idCasoOriginal},
						type: "post",
						success: function(data){
							swalExito.fire({
							title: 'Exito!',
							text: "Se ha realizado el intercambio",
							didOpen: function() {
								setTimeout(function() {
							location . reload();
								}, 2000)
							},
							});
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

	var trasladoInterno=function(caso, nombreCompleto){
		 //alert(nombreCompleto);
		//test(caso);
		 //console.log(test);
		 swalCargando.fire({});
		 $.ajax({
			url: "probando",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "caso": caso },
			dataType: "json",
			type: "get",
			success: function(data){
				var idSala = data.idSala;
				var idCama = data.idCama;
				var caso = data.idCaso;
				$("#salaReasignar").val(idSala);
				$("#camaReasignar").val(idCama);
				$("#casoReasignar").val(caso);
				$("#unidadReasignar").val($('#tabUnidad').find('.active').children().data("id"));
				//dialog.modal("hide");
				$(".nombreModal").html(nombreCompleto);
				getUnidades();
				setTimeout(function() {
					$("#modalReasignarPabellon").modal("show");
				swalCargando.close();
				Swal.hideLoading();
				},2000);
			},
			error: function(error){
				console.log(error);
			}
		});


	 }

//todo de camas
var marcarCamaDisponible=function(event, cama){

        event.preventDefault();

		var servicio_original = $("#unidad_original").val();

		var dialog = bootbox.dialog({
			//title: 'Se ha realizado el traslado interno',
			message: "¿Desea trasladar al paciente?",
			buttons: {
				cancel: {
					label: "No",
					className: 'btn-danger',
					callback: function(){
						//location.reload();
					}
				},
				ok: {
					label: "Si",
					className: 'btn-primary',
					callback: function(){
						swalCargando.fire({});
						$.ajax({
							url: "{{URL::to('/')}}/reasignar",
							headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
							data: {camaOld: $("#camaReasignar").val(), casoOld: $("#casoReasignar").val(), camaNew: cama},
							type: "post",
							dataType: "json",
							success: function(data){
								swalCargando.close();
								Swal.hideLoading();
								//console.log(data);
								if(data.error){
									swalError.fire({
									title: 'Error',
									text:data.error
									});
									$("#modalReasignar").modal('toggle');
									//$("#modalIngresar").data('bs.modal', null);
								}else{
									swalExito.fire({
									title: 'Exito!',
									text:data.msg,
										didOpen: function() {
										setTimeout(function() {
											swalCargando.fire({});
											location . reload();
										}, 2000)
									},
									});
									
								}
							},
							error: function(error){
								console.log(error);
							}
						});
					}
				}
			}
		});
 	}

//todo de camas

	// inicio recuperacion
$("#formquitarPabellon").bootstrapValidator({
	excluded: ':disabled',
	fields: {
		comentario: {
			validators:{
				notEmpty: {
					 //message: 'El comentario es obligatorio'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		$("#formquitarPabellon input[type='submit']").prop("disabled", false);
	}).on("success.form.bv", function(evt){
		$("#formquitarPabellon input[type='submit']").prop("disabled", false);
		evt.preventDefault(evt);
		var $form = $(evt.target);
		bootbox.confirm({
			message: "¿Está seguro de querer quitar al paciente de la lista de pabellón?",
			buttons: {
				confirm: {
					label: 'Si',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {  
				if(result){
					$.ajax({
						url: "quitarPabellon",
						headers: { 
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: "post",
						dataType: "json",
						data: $form .serialize(),
						success: function(data){
							$("#modalquitarPabellon").modal("hide");
							//if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });
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
							window.location.reload();  
						},
						error: function(error){
							console.log(error);
						}
					});
				}
			}
		});
	});
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Pabellón</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")
<style>
.tt-input{
	width:100%;
}
.tt-query {
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	 -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
  color: #999
}

.tt-menu {    /* used to be tt-dropdown-menu in older versions */
 /* width: 430px;*/
  margin-top: 4px;
 /* padding: 4px 0;*/
  background-color: #fff;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 4px;
	 -moz-border-radius: 4px;
		  border-radius: 4px;
  -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	 -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
		  box-shadow: 0 5px 10px rgba(0,0,0,.2);
	overflow-y: scroll;
	max-height: 350px;
}

.tt-suggestion {
 /* padding: 3px 20px;*/
  line-height: 24px;
}

.tt-suggestion.tt-cursor,.tt-suggestion:hover {
  color: #fff;
  background-color: #1E9966;

}

.tt-suggestion p {
  margin: 0;
}
.twitter-typeahead{
	width:100%;
}

</style>
	<fieldset>
		<legend>Pabellón</legend>
		@if(Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS)
		<h4>Reportes por Servicios (Vista solo para master)</h4>
		{{ HTML::link(URL::route('pdfPacientesPabellonPorUnidad'), 'Generar Pdf', ['class' => 'btn btn-danger', 'title' => 'Reporte por Servicio']) }}
		{{ HTML::link(URL::route('excelListaPabellonPorUnidad'), 'Generar Excel', ['class' => 'btn btn-success', 'title' => 'Reporte por Servicio']) }}
		<br><br>
		<h4>Reportes de Lista</h4>
		@endif
		{{ HTML::link(URL::route('pdfPacientesPabellon'), 'Generar Pdf', ['class' => 'btn btn-danger']) }}
		{{ HTML::link(URL::route('excelListaPabellon'), 'Generar Excel', ['class' => 'btn btn-success']) }}
		<!-- recargando la tabla -->
		<br><br>
		{{-- <div class="table-responsive"> --}}
		<div class="dataTables_wrapper no-foot heightgrilla_pabellon">	
			<table id="listaPabellon" class="table  table-condensed table-hover">
				<thead>
					<tr>
					<th>Opciones</th>
						<th style="width:100px">Run</th>
						<th>Nombre completo</th>
						<th>Diagnóstico</th>
						<th>Fecha de Ingreso</th>
						<th>Comentario</th>
						<th>Usuario que Solicitó</th>
						</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</fieldset>

<!-- inicio quitar recuperacion -->
<div id="modalquitarPabellon" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Quitar paciente de lista de Pabellón</h4>
      </div>
      {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formquitarPabellon')) }}
      {{ Form::hidden('idCaso', '', array('class' => 'idCaso')) }}
      <div class="modal-footer">
        <button id="solicitar" type="submit" class="btn btn-primary">Quitar paciente </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
<!-- fin quitar recuperacion -->

<!--pabellon -->
<div id="modalReasignarPabellon" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Traslado interno</h4>
				<div class="nombreModal"></div>
			</div>
			<div class="modal-body">
				<input type="hidden" id="salaReasignar"/>
				<input type="hidden" id="camaReasignar"/>
				<input type="hidden" id="casoReasignar"/>
				<input type="hidden" id="unidadReasignar"/>
				<div class="row">
					<ul id="tabUnidad" class="nav nav-tabs" role="tablist">
					</ul>
					<div id="contentUnidad" class="tab-content">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalSolicitar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	

	<div class="modal-dialog">
		<div class="modal-content">
			{{ Form::open(array('class' => 'form-horizontal', 'id' => 'derivarForm')) }}


			{{ Form::hidden('idCama', '', array('id' => 'idCamaDerivar')) }}

			{{ Form::hidden('idEstablecimiento', '', array('id' => 'idEstablecimientoDerivar')) }}

			{{ Form::hidden('idCaso', '', array('id' => 'idCasoDerivar')) }}

			{{ Form::hidden('motivo', "traslado externo", array('id' => 'motivoDerivar')) }}
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Solicitar cama</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						{{Form::text('asunto', null, array('id' => 'asuntoDerivar', 'class' => 'form-control', 'disabled', 'placeholder' => 'Asunto'))}}
					</div>
					<div class="form-group col-md-12">
						{{Form::textarea('texto', null, array('id' => 'textoDerivar', 'class' => 'form-control', 'rows' => '5',  'disabled'))}}
					</div>
					<div class="form-group col-md-12">
						<div class="table-responsive">
						<table>
							<tr>
								<td><button type="button" class="btn btn-default addButton"><span class="glyphicon glyphicon-plus"></span></button></td>
								<td style="width: 100%;"><input id="fileMain" multiple type="file" name="files[]" class="file" title="Elegir archivo"  data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false"/></td>
							</tr>
						</table>
						</div>
					</div>
					<div class="form-group hide col-md-12" id="optionTemplate">
						<div class="table-responsive">
						<table>
							<tr>
								<td><button type="button" class="btn btn-default removeButton"><span class="glyphicon glyphicon-minus"></span></button></td>
								<td style="width: 100%;"><input multiple type="file" name="filesDerivar[]" class="file" title="Elegir archivo"  data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false"/></td>
							</tr>
						</table>
						</div>
					</div>
				</div>
			</div>
			{{ Form::close() }}
			<div class="modal-footer">
				<button class="btn btn-primary" onclick="derivarForm()">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

@stop
