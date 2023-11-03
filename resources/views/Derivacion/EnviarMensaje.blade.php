@extends("Templates/template")

@section("titulo")
Enviar mensaje
@stop


@section("miga")
<li><a href="#">Solicitudes de Traslado Externo</a></li>
<li><a href="#">Enviadas</a></li>
<li><a href="#" onclick='location.reload()'>Enviar mensaje</a></li>
@stop


@section("script")
<script>

	var count=0;

	var opciones={
		ENVIAR: 1,
		ACEPTAR: 2,
		RECHAZAR: 3,
		ACEPTARPENDIENTE: 4
	};

	var opcionSelected=opciones.ENVIAR;

	var agregar=function(){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		var $input = $clone.find('input[type="file"]');
		var id="id_"+count;
		$input.prop("id", id);
		$('#'+id).fileinput();
		console.log("#"+id+" .file-input-new .input-group");
		$(".file-input-new .input-group").css({"width": "95%", "margin-left": "10px"});
		count++;
	}

	var borrar=function(boton){
		$(boton).parent().parent().parent().remove();
	}

	var marcarOpcion=function(opcion){
		opcionSelected=opcion;
		if(opcion == opciones.ENVIAR) $("#msgSelect").text("Enviar mensaje");
		if(opcion == opciones.ACEPTAR) $("#msgSelect").text("Aceptar");
		if(opcion == opciones.RECHAZAR) $("#msgSelect").text("Rechazar");
	}

	var mostrarCamas=function(){
		getUnidades();
		$("#modalCamasDisponibles").modal("show");
	}

	var aceptarCama=function(cama){
		$("#cama").val(cama);
		$("#modalCama").modal("hide");
		$("#modalCamasDisponibles").modal("hide");
		$("#horaHidden").val($("#horas").val());
	}

	var marcarCamaDisponible=function(cama, sala, unidad){
		var msg="¿ Desea seleccionar la cama de la sala "+sala+" del servicio "+unidad+" ?";
		$("#msgCama").text(msg);
		var hora=$("#horas").val();
		var click="aceptarCama(\""+cama+"\")";
		$("#btnCama").attr("onclick", click);
		$("#modalCama").modal("show");
	}

	var enviarMensajeTraslado=function(){
		$('#formMensaje').bootstrapValidator('resetForm', 'true');
		if(opcionSelected == opciones.RECHAZAR) {
		}
		if(opcionSelected == opciones.ENVIAR){
		}
		if(opcionSelected == opciones.ACEPTAR){
		}
	}

	var abrirRechazar=function(){
		opcionSelected=opciones.RECHAZAR;
		$("#msgSelect").text("Rechazar");
		$("#modalMotivo").modal("show");
		$("#mensaje").text("");
		$("#motivoHidden").val($("#motivo").val());	
		$("#accion").val(opciones.RECHAZAR);
	}

	var rechazar=function(){
		$("#motivoHidden").val($("#motivo").val());
		$("#modalMotivo").modal("hide");

		if($("select[name='motivo2']").val()=="otro")
		{	
			$("#motivoHidden").val($("[name='inputAlta']").val());
			var msg="Estimada(o), la solicitud a sido rechaza por el siguiente motivo: "+$("#inputAlta").val();

		}
		else
		{
			var msg="Estimada(o), la solicitud a sido rechaza por el siguiente motivo: "+$("#motivo option:selected").text();
		}
		$("#mensaje").text(msg);
	}

	var abrirAceptar=function(){
		opcionSelected=opciones.ACEPTAR;
		$("#accion").val(opciones.ACEPTAR);
		$("#msgSelect").text("Aceptar");
		getUnidades();
		$("#modalCamasDisponibles").modal("show");
		$("#mensaje").text("Estimada(o), la solicitud ha sido aceptada");
	}

	var abrirAceptarPendiente = function(){
		opcionSelected = opciones.ACEPTARPENDIENTE;
		$("#accion").val(opciones.ACEPTARPENDIENTE);
		$("#msgSelect").text("Aceptar sin cama");
		$("#mensaje").text("Estimada(o), la solicitud ha sido aceptada");
	}

	var enviar=function(){
		opcionSelected=opciones.ENVIAR;
		$("#accion").val(opciones.ENVIAR);
		$("#msgSelect").text("Enviar mensaje");
		$("#formMensaje").submit();
	}

	var generarMapaCamasDisponibles=function(mapaDiv, unidad){
		$.ajax({
			url: "{{URL::to('/')}}/traslado/{{$tipo}}/enviarMensaje/getCamas",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			dataType: "json",
			type: "post",
			data: {unidad: unidad},
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
					var active = (i == 0) ? "active" : "";
					var nombre=data[i].url;
					var id="id-"+nombre;
					$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+"</a></li>");
					$("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'></div>");
					generarMapaCamasDisponibles(id, data[i].url, true);
				}
				if(data.length > 0) {
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

	var getMensajeTraslado=function(){
		$.ajax({
			url: "{{URL::to('/')}}/getMensajeTraslado",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			data: {id: "{{$id}}"},
			type: "post",
			dataType: "json",
			success: function(data){
				console.log(data);
				if(data["derivaciones"].length){
					$("#tableMensajes").dataTable().fnClearTable();
					$('#tableMensajes').dataTable().fnAddData(data["derivaciones"]);
				}
				if(data["archivos"].length){
					$("#tablaDocumentos").dataTable().fnClearTable();
					$("#tablaDocumentos").dataTable().fnAddData(data["archivos"]);

				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var rechazarMensajeTraslado=function(){
		$.ajax({
			url: "{{URL::to('/')}}/rechazarTraslado",
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			data: {idTraslado: "{{$id}}"},
			type: "post",
			dataType: "json",
			success: function(data){
				if(data.exito){
					swalExito.fire({
					title: 'Exito!',
					text: data.exito,
					didOpen: function() {
						setTimeout(function() {
							location.href="/derivaciones/{{$tipo}}";
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

	

	$(function(){

		var tabSeleccionado = sessionStorage.getItem("tabSeleccionado");



		var arregloIds = JSON.parse(sessionStorage.getItem("{{$motivo}}"));
		console.log(arregloIds);

		if(arregloIds != null)
		{


		var indice = arregloIds.indexOf({{$id}});
		



		var idSiguiente = arregloIds[indice+1];
		var idAnterior  = arregloIds[indice-1];
		console.log("indice actual",indice);
		console.log("indice anterior", indice-1)
		console.log("indice siguiente", indice+1);


		console.log("valor suigiente", idSiguiente);
		console.log("valor anterior", idAnterior);
		}

		if(idSiguiente == undefined)
		{
			$("#linkSiguiente").hide();
		}
		else
		{
			$("#generaLink").attr("href","{{ URL::to('/') }}/traslado/{{$tipo}}/enviarMensaje/"+idSiguiente+"/{{$motivo}}");
		}

		if(idAnterior == undefined)
		{
			$("#linkAtras").hide();
		}
		else
		{
			$("#generaLinkAtras").attr("href","{{ URL::to('/') }}/traslado/{{$tipo}}/enviarMensaje/"+idAnterior+"/{{$motivo}}");
		}

		

	$("select[name='motivo2']").on("change", function(){

		if($("select[name='motivo2']").val()=="otro")
		{	//alert($("#motivo2").val());
			$("#Otro").show("slow");
		}
		else
		{ 	//alert($("[name='inputAlta']").val());
			$("#Otro").hide("slow");

		}
	});

		$("#solicitudMenu").collapse();
		$("#accion").val(opciones.ENVIAR);

		$("#fileMain").fileinput();
		$(".file-input-new .input-group").css({"width": "95%", "margin-left": "10px"});

		$('#tableMensajes').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"bJQueryUI": true,
 			"searching": false,
 			"scrollCollapse": true,
 			"paging": false,
 			"lengthChange": false,
 			"info": false,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 		});

 		$('#tablaDocumentos').dataTable({	
 			"aaSorting": [[0, "asc"]],
 			"iDisplayLength": 15,
 			"bJQueryUI": true,
 			"searching": false,
 			"oLanguage": {
 				"sUrl": "{{ URL::to('/') }}/js/spanish.txt"
 			},
 			"fnInitComplete": function(oSettings, json) {
 				$("#tablaDocumentos_length").remove();
 			}
 		});

 		$("#rechazar").on("click", function(){
 			rechazarMensajeTraslado();
 		});

 		getMensajeTraslado();

 		$("#formMensaje").bootstrapValidator({
 			excluded: ':disabled',
 			group: '.error',
 			fields: {
 				mensaje: {
 					validators:{
 						notEmpty: {
 							message: 'El mensaje es obligatorio'
 						}
 					}
 				}
 			}
 		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
 			evt.preventDefault(evt);
 			$("#btnEnviarMensaje").prop("disabled", false);
 			var $form = $(evt.target);
 			var accion=$("#accion").val();
 			if(accion == opciones.ACEPTAR && $("#cama").val() == ""){
				swalInfo.fire({
				title: "Debe seleccionar una cama",
				allowOutsideClick: false,
				allowEscapeKey: false,
				 });
 				return
 			}
 			$("#asunto").prop("disabled", false);
 			$.ajax({
 				url: "{{URL::to('/')}}/enviarMensaje",
 				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 				type: "post",
 				dataType: "json",
 				data: new FormData($form[0]),
 				cache: false,
 				contentType: false,
 				processData: false,
 				success: function(data){
 					console.log(data);
 					if(data.exito){
 						getMensajeTraslado();
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
							location.href="{{URL::to('/')}}/derivaciones/{{$tipo}}";
							}, 2000)
						},
						});
 					}
 					if(data.error){
						swalError.fire({
						title: 'Error',
						text:data.error
						});
 						console.log(data.error);
 					}
 				},
 				error: function(error){
 					console.log(error);
 				}
 			});
 			$("#asunto").prop("disabled", true);
 		});
	});
</script>
@stop
<meta name="csrf-token" content="{{{ Session::token() }}}">
@section("section")

<div id="modalRechazar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea rechazar la solicitud ?</h4>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="rechazar" type="submit" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-sm-6">
		<div id="linkAtras" >
			<a id="generaLinkAtras" href="" class="btn btn-primary">Atrás</a>
		</div>
	</div>

	<div class="col-sm-6">
		<div id="linkSiguiente" class="col-md-offset-10">
			<a id="generaLink" href="" class="btn btn-primary">Siguiente</a>
		</div>
	</div>
</div>

{{ Form::model($solicitud, array('url' => array('#', $solicitud->id), 'id' => 'formMensaje', 'files'=> true)) }}
{{ Form::hidden('idEstablecimiento', "$solicitud->idEstablecimiento", array('id' => 'idEstablecimiento')) }}
{{ Form::hidden('idTraslado', "$solicitud->id", array('id' => 'idTraslado')) }}
{{ Form::hidden('motivo', "", array('id' => 'motivoHidden')) }}
{{ Form::hidden('accion', "1", array('id' => 'accion')) }}
{{ Form::hidden('cama', "", array('id' => 'cama')) }}
{{ Form::hidden('caso', "$solicitud->caso", array('id' => 'caso')) }}
{{ Form::hidden('hora', "", array('id' => 'horaHidden')) }}


<fieldset>
	<legend></legend>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="rut" class="col-sm-2 control-label">Run: </label>
			<div class="col-sm-10">
				<div class="input-group">
					{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'disabled'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;', 'disabled'))}}
				</div>
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Fecha de nacimiento: </label>
			<div class="col-sm-10">
				{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="rut" class="col-sm-2 control-label">Nombre: </label>
			<div class="col-sm-10">
				{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Género: </label>
			<div class="col-sm-10">
				{{ Form::select('sexo', array(
				'masculino' => 'Masculino',
				'femenino' => 'Femenino',
				'indefinido' => 'Indefinido'
				), null, array('id' => 'sexo', 'class' => 'form-control', 'disabled')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="rut" class="col-sm-2 control-label">Apellido paterno: </label>
			<div class="col-sm-10">
				{{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Apellido materno: </label>
			<div class="col-sm-10">
				{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>
			<div class="col-sm-10">
				{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Fecha solicitud: </label>
			<div class="col-sm-10">
				{{Form::text('fechaSolicitud', null, array('id' => 'fechaSolicitud', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="riesgo" class="col-sm-2 control-label">Riesgo: </label>
			<div class="col-sm-10">
				{{ Form::select('riesgo', $riesgo, null, array('id' => 'riesgo', 'class' => 'form-control', 'disabled')) }}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Estab. origen: </label>
			<div class="col-sm-10">
				{{Form::text('estabOrigen', null, array('id' => 'estabOrigen', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-12">
			<label for="fecha" class="col-sm-1 control-label">Asunto: </label>
			<div class="col-sm-11">
				{{Form::text('asunto', null, array('id' => 'asunto', 'class' => 'form-control', 'disabled'))}}
			</div>
		</div>
	</div>

@if($aislamiento!='0')
<br>
<h4 style="color:red">ESTE PACIENTE PRESENTA UNA IAAS</h2>
<div class="row">
		<div class="form-group col-md-8">
			<label class="col-sm-2 control-label"> Aislamiento: </label>
			<div class="col-sm-4">
				<input disabled type="text" class="form-control" value = "{{$aislamiento}}"/>
			</div>
		</div>
</div>
@endif
	
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table id="tableMensajes" class="table table-striped table-condensed table-bordered">
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Establecimiento Emisor Del Mensaje</th>
							<th>Mensaje</th>
							<th>Establecimiento Destino Derivación</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 30px;">
		<div class="col-md-12">
			<div class="table-responsive">
			<table id="tablaDocumentos" class="table table-striped table-condensed table-bordered">
				<thead>
					<tr>
						<th>Nombre</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div>
		</div>
	</div>


	<div class="row" style="margin-top: 20px;">
		<div id="divArea" class="col-md-12 error">
			{{Form::textarea('mensaje', null, array('id' => 'mensaje', 'class' => 'form-control', 'rows' => '5', 'placeholder' => 'Mensaje'))}}
		</div>
	</div>
	<div class="row" style="margin-top: 30px;">
		<div class="row">
			<div class="col-md-2" style="left: 50px;">
				<label for="files[]">Adjuntar archivo</label>
			</div>
			<div class="col-md-8">
				<input id="fileMain" type="file" name="files[]" class="file" title="Elegir archivo"  data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false"/>
			</div>
			<div class="col-md-2" style="right: 50px;">
				<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
			</div>
		</div>
	</div>

	<div id="fileTemplate" class="row hide" style="margin-top: 30px;">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<input type="file" name="files[]" class="file" title="Elegir archivo"  data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false"/>
			</div>
			<div class="col-md-2" style="right: 50px;">
				<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
				<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
			</div>
		</div>
	</div>
	@if($tipo == "recibidas")
	<div id="divRecibidas" class="row" style="margin-top: 20px;">
		<div class="col-md-3">
			<div class="dropup">
				<a class="btn btn-default dropdown-toggle" id="dropdownMenu" data-toggle="dropdown" aria-expanded="true">
					Seleccionar: 
					<span id="msgSelect"> Enviar mensaje</span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
					<li role="presentation"><a role="menuitem" tabindex="-1" href="#divRecibidas" onclick="enviar();">Enviar Mensaje</a></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="#divRecibidas" onclick="abrirAceptar();">Aceptar</a></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="#divRecibidas" onclick="abrirRechazar();">Rechazar</a></li>
				</ul>
			</div>
		</div>
		<div class="col-md-2">
			<a href="{{ URL::to("/derivaciones/{$tipo}") }}" class="btn btn-danger">Cancelar</a>
			<!--<a cursor="hand" class="btn btn-primary" onclick="enviarMensajeTraslado();">Aceptar</a>-->
			{{ Form::submit('Aceptar', array('class' => 'btn btn-primary', 'id' => 'aceptar')) }}
		</div>
	</div>
	@endif

	@if($tipo == "enviadas")
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-12">
			<a href="{{ URL::previous() }}" class="btn btn-danger">Cancelar</a>
			{{ Form::submit('Enviar mensaje', array('class' => 'btn btn-primary', 'id' => 'btnEnviarMensaje')) }}
		</div>
	</div>
	@endif

</fieldset>

{{ Form::close() }}

<div id="modalCamasDisponibles" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Asignación de cama</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="salaReasignar"/>
				<input type="hidden" id="camaReasignar"/>
				<input type="hidden" id="casoReasignar"/>
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

<div id="modalMotivo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Rechazado de Solicitud</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-md-12">
						<label for="fecha" class="col-sm-2 control-label">Asunto: </label>
						<div class="col-sm-10">
							<select id="motivo" name="motivo2" class="form-control">
								<option value="no_cupos">Falta de cupos</option>
								<option value="no_acorde">Condición del paciente no es acorde</option>
								<option value="otro">Otro</option>
							</select><br>
						</div>
					</div class="form-group col-md-12">

					<div id="Otro" class="form-group col-md-12" style="display:none;">
						<label class="col-sm-2 control-label">Motivo de rechazo: </label>
						<div class="col-sm-10">
							{{Form::textarea("inputAlta", null, array('id' => 'inputAlta','class' => 'form-control'))}}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" onclick="rechazar();" class="btn btn-primary">Aceptar</a>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalCama" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">¿Esta seguro de ingresarlo en la cama seleccionada? </h4>
			</div>

			<div class="modal-footer">
				<a id="btnCama" href="#" class="btn btn-primary" onclick="">Aceptar</a>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<br><br>
@stop