@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("script")

<script>

//var fechareal = fecha_ingreso_real;

 var paciente_plan;

  	var plan=function(id){
 		paciente_plan=id;
 		$("#modalPlanTratamiento").modal("show");
  }
 	var getPlanTratamiento = function(id) {
	 	$.ajax({
	 		url: "{{URL::to('/')}}/getPlanTratamiento",
	 		type: "post",
	 		data: {id: id},
	 		dataType: "json",
	 		success: function(data) {
	 			var html="";
	 			var detalle;
	 			if(data.detalle.length<=0){
	 				alert("No hay registros");
	 			}
	 			else{
					for(i=0;i<data.detalle.length;i++){
	                detalle=data.detalle[i];
		            html+="<fieldset>";
	                html += "<pre class='list-group-item'>" + detalle + "</pre>";
	                html+="<br>";
		            html+="</fieldset>";
		        	}
		            dialog=bootbox.dialog({
		            	message: html,
		            	title: "Historico Plan de tratamiento",
		            	buttons: {
		            		success: {
		            			label: "Aceptar",
		            			className: "btn-primary",
		            			callback: function() {
		            			}
		            		}
		            	}
		            });
	            }
	        },
	        error: function(error) {
	        	console.log(error);
	        }
	    });
	}
	var display_tiempo_estada = function(source, type, val){
		if (type === 'set'){
			return;
		}
		if (type === 'display'){
			return source[val];
		}
		if (type === 'filter'){
			return source[val];
		}
		return source[10];
	}

	var display_num_cama = function(source, type){
		if (type === 'set'){
			return;
		}
		if (type === 'display'){
			return source[2];
		}
		if (type === 'filter'){
			return source[2];
		}
		return source[11];
	}

	var display_estado = function(source, type, val){
		if (type === 'set'){
			return;
		}
		if (type === 'display'){
			return source[8];
		}
		if (type === 'filter'){
			return source[8];
		}
		switch (source[8]){
			case 'Libre': return 0;
			case 'Bloqueada': return 1;
			case 'Reservada': return 2;
			case 'Ocupada': return 3;
		}
	}


	var inicializarTabla=function(id){
		$.fn.dataTable.ext.errMode = 'none';
		/* $("#"+id).on( 'error.dt', function ( e, settings, techNote, message ) {
			console.log( 'An error has been reported by DataTables: ', message );
		} ) .DataTable(); */

		$("#"+id).dataTable({
			"aaSorting": [[6, "desc"],[7, "desc"]],
			"iDisplayLength": 10,
			"bJQueryUI": true,
			"bAutoWidth" : false,
			"oLanguage": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			},
			aoColumns: [
				{mData: 0}, //servicio
				{mData: 1}, //sala
				//{mData: function(source, type, val) {return display_num_cama(source, type);}},
				{mData: 2}, //cama
				{mData: 6}, //run
				{mData: 5}, //nombres paciente
				{mData: 4}, //diagnostico
				{mData: 7},	//categorización
				//{mData: function(source, type, val) {return display_estado(source, type, val);}},
				{mData: 9}, //estado
				//{mData: function(source, type, val) {return display_tiempo_estada(source, type, 9);}},
				{mData: 13}, //edad
			]
		});
	}

	var getUnidades=function(){
		$.ajax({
			url: "{{URL::to('/')}}/getUnidadesIndex",
			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			data: {id: "{{$id}}"},
			type: "post",
			dataType: "json",
			success: function(data){
				//console.log("jejej", data);
				$("#tabUnidad").empty();
				$("#contentUnidad").empty();
				var infectados = data[1];
				var data = data[0];
				for(var i=0; i<data.length; i++){
					var nombre=data[i].alias;
					var id="id-"+nombre;
					console.log(data[i].id_area_funcional);
					var active = (i == 0) ? "active" : "";
					var img_iaas = '';
					$.each(infectados, function(index, value){
						if(data[i].id == value){
							img_iaas = "<img src='{{asset('img/iaas.png')}}' style='width:16px; margin-left:3px;'>";
						}
					});
					if(data[i].id_area_funcional == 8){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+" (Pediatría) </a></li>");
					}else if(data[i].id_area_funcional == 6){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+" (Adulto)</a></li>");
					}else if(data[i].id_area_funcional == 11){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+" (Neonatología)</a></li>");
					}else if(data[i].id_area_funcional == 2 && data[i].alias == "Cuidados medios"){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+" (Pediatría)</a></li>");
					}else if(data[i].id_area_funcional == 10){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+" (Neonatología)</a></li>");
					}else{
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+"</a></li>");
					}
					//$("#tabUnidad").append("<li class='"+active+"'><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].nombre+img_iaas+"\
					//</a></li>");
					$("#contentUnidad").append("<div id='"+id+"' class='tab-pane mapa-camas-class active' style='margin-top: 20px;'></div>");
				}
				for(var i=0; i<data.length; i++){
					var id = "id-"+data[i].alias;
					if($("#vista").val() == 1) generarMapaCamas(id, data[i].alias);
					else generarListaCama(id, data[i].alias);

				}
				for(var i=0; i<data.length; i++){
					resizeMapaCamas("id-"+data[i].alias);
					$("#id-"+data[i].alias).removeClass("active");
				}

				$("#id-"+data[0].alias).addClass("active");
				$("#id-"+data[0].alias).tab("show");
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var generarListaCama=function(id, unidad){
		$.ajax({
			url: "getListacCamaUnidad",
			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			data: {id: "{{$id}}", unidad: unidad},
			type: "post",
			dataType: "json",
			success: function(data){
				//console.log(data);
				var idTabla=id+"-tabla";
				var table="<div class='table-responsive'>";
				table+="<table id='"+idTabla+"' class='table table-striped table-bordered tableLista' width='100%'>";
				table+="<thead>";
				//table+="<th>Servicio</th> <th>Sala</th> <th>Cama</th> <th>Diagnóstico</th> <th>Paciente</th> <th>Run</th> <th>Estado</th><th>Tiempo</th><th>Edad</th><th>Opciones</th>";
				table+="<th>Servicio</th> <th>Sala</th> <th>Cama</th> <th>Run</th> <th>Paciente</th> <th>Diagnóstico</th> <th>Categorización</th> <th>Estado</th><th>Edad</th>";
				table+="</thead>";
				table+="<tbody>";
				table+="</tbody>";
				table+="</table>";
				table+="</div>";
				$("#"+id).html(table);
				inicializarTabla(idTabla);
				var tabla=$("#"+idTabla).dataTable();
				//tabla.fnClearTable();
				if(data.length != 0) tabla.fnAddData(data);
				//console.log(data);
			},
			error: function(error){
				console.log(error);
			}
		})
	}

 	var generarMapaCamas=function(mapaDiv, unidad){
 		$.ajax({
 			url: "getCamas",
 			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 			data: {unidad: unidad, id: "{{$id}}"},
 			dataType: "json",
			type: "post",
//			async: false,
 			success: function(data){
				// console.log
 				crearMapaCamas(mapaDiv, data);
 				//resizeMapaCamas(mapaDiv);
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

 	 	var infecciones=function(idCaso,idEstablecimiento){
 		$.ajax({
 			url: "{{URL::to('/')}}/validarTraslado2",
 			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 			data: {idCaso: idCaso},
 			type: "post",
 			dataType: "json",
 			success: function(data){

 				if(data.error) swalError.fire({
								title: 'Error',
								text:data.error
								});
 				else location.href="{{URL::to('/')}}/infecciones2/"+idCaso+"/"+idEstablecimiento;

 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

  	var VerInfecciones=function(idCaso,idEstablecimiento){
 		$.ajax({
 			url: "{{URL::to('/')}}/validarTraslado3",
 			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 			data: {idCaso: idCaso},
 			type: "post",
 			dataType: "json",
 			success: function(data){
        console.log(data);
 				if(data.error) swalError.fire({
								title: 'Error',
								text:data.error
								});
				else location.href="{{URL::to('/')}}/verinfecciones2/"+idCaso+'/'+idEstablecimiento;
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

 	var historial=function(idCaso){
 		$.ajax({
 			url: "{{URL::to('/')}}/validarTraslado4",
 			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 			data: {idCaso: idCaso},
 			type: "post",
 			dataType: "json",
 			success: function(data){
 				if(data.error) swalError.fire({
								title: 'Error',
								text:data.error
								});
 				else
        location.href="{{URL::to('/')}}/historial2/"+idCaso;
        //location.href="{{URL::to("historial2/'+idCaso+'")}};
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}


 	var getPaciente = function(id, unidad,idCaso,idEstablecimiento) {
	 	$.ajax({
	 		url: "{{URL::to('/')}}/getPaciente",
	 		headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
	 		type: "post",
	 		data: {id: id, unidad: unidad, idCaso},
	 		dataType: "json",
	 		success: function(data) {
	 			console.log(data);
	 			var riesgo = data.riesgo == null? "No especificado":data.riesgo.categoria;
	 			var dieta  = data.dieta  == null? "No especificado":data.dieta;
                var caso_social = data.caso_social == null? "No especificado" : data.caso_social? 'Sí':'No';
                var extranjero = data.extranjero == null? "No especificado" : data.extranjero? "Sí":"No";
	 			var some="{{Session::get('some')}}";
	 			//var html = "<ol class='list-group'>";
					var html = "<button class='botonCerrar btn btn-danger' style='position:fixed; width:19%;      height: 30px; z-index: 99;'><b>CERRAR  </b><span class='glyphicon glyphicon-remove' style=' height: 30px; z-index: 1000;'></span></button> <ol class='list-group'>";
	 			html += "<li class='list-group-item'> <label class='control-label'>Run: </label>&nbsp;&nbsp;" + data.rut + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Extranjero: </label>&nbsp;&nbsp;" + extranjero + "</li>";
	 			html += "<li class='list-group-item'> <label class='control-label'>Nombre: </label>&nbsp;&nbsp;" + data.nombre + "</li>";
	 			html += "<li class='list-group-item'> <label class='control-label'>Apellido paterno: </label>&nbsp;&nbsp;" + data.apellidoP + "</li>";
	 			html += "<li class='list-group-item'> <label class='control-label'>Apellido materno: </label>&nbsp;&nbsp;" + data.apellidoM + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Genero: </label>&nbsp;&nbsp;" + data.genero + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Fecha nacimiento: </label>&nbsp;&nbsp;" + data.fecha + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Edad: </label>&nbsp;&nbsp;" + data.edad + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Previsión: </label>&nbsp;&nbsp;" + data.prevision + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Fecha de ingreso: </label>&nbsp;&nbsp;" + data.fechaReal + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Caso social: </label>&nbsp;&nbsp;" + caso_social + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Último diagnóstico: </label>&nbsp;&nbsp;" + data.diagnostico.id_cie_10 + " "+data.diagnostico.nombre+"</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Riesgo: </label>&nbsp;&nbsp;" + riesgo + "</li>";
	           	html+= "</ol>";
	           	@if(Session::get("usuario")->tipo == TipoUsuario::ADMINIAAS||Session::get("usuario")->tipo == TipoUsuario::IAAS||(Session::get("usuario")->tipo == TipoUsuario::ADMIN && Session::get("usuario")->iaas))
	            html+="<fieldset>";
	            html+="<legend>Opciones</legend>";
	            html+="<ol>";
	           	@if(Session::get("usuario")->tipo != TipoUsuario::ADMINIAAS)html+="<li> <a class='cursor' onclick='infecciones("+idCaso+","+idEstablecimiento+")'>Notificar Infección Intrahospitalaria</a> </li>";@endif
				html+="<li> <a class='cursor' onclick='VerInfecciones("+idCaso+","+idEstablecimiento+")'>Ver / Editar Infección Intrahospitalaria</a> </li>";
	           	html+="<li> <a class='cursor' onclick='historial("+idCaso+")'>Datos Históricos Del Paciente</a> </li>";
	            html += "</ol>";
	            html+="</fieldset>";
				@endif
				$("#descripcionPaciente").html(html);
				$("#descripcionPaciente").show();
				$("#simbolo").hide();
	            /* dialog=bootbox.dialog({
	            	message: html,
	            	title: "Datos del paciente",
	            	buttons: {
	            		success: {
	            			label: "Aceptar",
	            			className: "btn-primary",
	            			callback: function() {
	            			}
	            		}
	            	}
	            }); */
	        },
	        error: function(error) {
	        	console.log(error);
	        }
	    });
	}

	var abrirDesbloquear=function(idCama){
		$.ajax({
			url: "obtenerMensajeBloqueo",
			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
			type: "post",
			data: {idCama: idCama},
			dataType: "json",
			success: function(data){
				$("#msgMotivo").text(data.motivo+".");
			},
			error: function(error){
				console.log(error);
			}
		});
		$("#modalDesbloquear").modal("show");
	}

	$(function(){
getUnidades();

		$("#vista").on("change", function(){
			getUnidades();
		});

 		$("#formPlan").submit(function(evt){
 			evt.preventDefault(evt);
 			var detalle=$('#detallePLan').val();
 			$.ajax({
 				url: "{{ URL::to('/')}}/planTratamiento",
 				headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 				data:'detalle='+detalle+'&paciente_plan='+paciente_plan,
 				type: "post",
 				dataType: "json",
 				success: function(data){
 					$("#modalPlanTratamiento").modal("hide");
 					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						});
 					}
 					else{
 						swalError.fire({
						title: 'Error',
						text:data.error
						});
 						console.log(data.msg);
 					}
 				},
 				error: function(error){
						swalExito.fire({
						title: 'Exito!',
						text: "Error al ingresar plan de tratamiento",
						});
 					console.log(error);
 				}
 			});
 		});

	});

	$(document).on('click', '.botonCerrar', function () {
	$("#descripcionPaciente").hide();
	$("#simbolo").show();
});
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

 @section("miga")
 <li><a href="#">Gestión de Camas</a></li>
 <li><a href="#">{{$nombre}}</a></li>
 @stop


@section("section")
<h3>Camas de {{$nombre}}</h3>
<fieldset>
	<div class="form-inline">
		<label>Seleccionar vista: </label>
		<select id="vista" class="form-control">
			<option value="1">Vista iconos</option>
			<option value="2">Vista lista</option>
		</select>
		{{ HTML::link("index/camas/$id/exportar", 'Exportar', ['class' => 'btn btn-default']) }}
	</div>
	<br><br>
	<div id="divMapa" class="row">
		<ul id="tabUnidad" class="nav nav-tabs" role="tablist">
		</ul>
		<div id="contentUnidad" class="tab-content">
		</div>
	</div>

</fieldset>

<div id="modalDesbloquear" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Motivo</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label style="font-size: 14px;">Motivo de bloqueo: <span id="msgMotivo"></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="solicitar" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalPlanTratamiento" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Ingreso</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formPlan')) }}
			{{ Form::hidden('sala', '', array('id' => 'salaLiberar')) }}
			{{ Form::hidden('cama', '', array('id' => 'camaLiberar')) }}
			{{ Form::hidden('caso', '', array('id' => 'casoLiberar')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>Ingrese nuevo Plan de Tratamiento</h4>
					</div>
				</div>
				<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Detalle: </label>
						<div class="col-sm-10">
							<textarea required id="detallePLan" name="detalle" class="form-control"></textarea>
						</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="solicitar" type="submit" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}

      <?php
       ?>


		</div>
	</div>
</div>

@stop
