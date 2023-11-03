@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("script")

<script>
	var table=null;
	var idCaso=null;
	var idLista=null;

	var agregar = function(boton){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		$clone.find("input").eq(2).val("");
		invoice_no_setup_typeahead($clone.find("input")[1],$clone.find("input")[2]);
		$(boton).prop("disabled", true);
	}

	var borrar=function(boton){
		$(boton).parent().parent().parent().remove();
		var diagnosticos = $("[name='diagnosticos[]']");
		var cantidad = diagnosticos.length -1;
		var anterior = cantidad - 1;
		var a = $(diagnosticos[anterior]).parent().parent().parent().parent().children(".col-md-3").children().eq(0);
		if(cantidad - 1 == 0){
			$("#cie10-principal").prop("disabled", false);
		}else{
			a.prop("disabled", false);
		}
	}

	function invoice_no_setup_typeahead(self, self2) {
		var datos_cie10 = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie10',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 50
		});

		datos_cie10.initialize();

		$(self).typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_cie10',
			source: datos_cie10.ttAdapter(),
			limit: 50,
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					return  "<div class='col-sm-12' ><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
			}
		}).on('typeahead:selected', function(event, selection){
			$(self2).val(selection.id_cie10);
		}).on('typeahead:close', function(ev, suggestion) {
			var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
			var a = $(this).parent().parent().parent().parent().children(".col-md-3").children().eq(0);
			if(!$cie10.val()&&$(this).val()){
				$(this).val("");
				$cie10.val("");
				$(this).trigger('input');
			}else{
				a.prop("disabled", false);
			}
		});
	}

	$(".fecha-sel").datetimepicker({
		locale: "es",
		format: "DD-MM-YYYY HH:mm"
	});

	var datos_establecimientos = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('estab'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '{{URL::to('/')}}/'+'%QUERY/consulta_establecimientos',
			wildcard: '%QUERY',
			filter: function(response) {
			    return response;
			}
		},
		limit: 50
	});

	datos_establecimientos.initialize();

	$('.estab .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'nombre_establecimiento',
		source: datos_establecimientos.ttAdapter(),
		limit: 50,
		templates: {
			empty: [
			'<div class="empty-message">',
				'No hay resultados',
			'</div>'
			].join('\n'),
			suggestion: function(data){
				var nombres = data;
				return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_establecimiento + "</b></span><span class='col-sm-4'><b>"+data.region_nombre+"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Establecimiento</span><span class='col-sm-4' style='color:#1E9966;'>Región</span></div><br>"
		}
	}).on('typeahead:selected', function(event, selection){
		$("[name='id_procedencia']").val(selection.id_establecimiento);
	}).on('typeahead:close', function(ev, suggestion) {
	  var $estable=$(this).parents(".estab").find("input[name='id_procedencia']");
	  if(!$estable.val()&&$(this).val()){
		  $(this).val("");
		  $estable.val("");
		  $(this).trigger('input');
	  }
	});

	var verDiagnosticos = function(idCaso,nombreCompleto){
		var ubicacion = 'hosp_dom';
		$.ajax({
		url: "{{ URL::to('/diagnosticosCaso') }}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {caso: idCaso, ubicacion: ubicacion},
		dataType: "json",
		type: "post",
			success: function(data){
				$("#modalVerDiagnosticos .modal-body").html(data.contenido);
				$(".detalle-diagnostico").val(idCaso);
				$(".nombreModal").html(nombreCompleto);
				$(".ubicacion").val('hosp_dom');
				$("#modalVerDiagnosticos").modal();
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var verRiesgos = function(idCaso){
		$(".detalles-caso").val(idCaso);
		$.ajax({
			// url: "{{ URL::to('/detallesCaso') }}",
			url: "{{ URL::to('/detallesCasoHospDom') }}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {caso: idCaso,unidad: ""},
			dataType: "json",
			type: "post",
			success: function(data){
				//console.log(data.contenido);
				$("#modalVerDetalles .modal-body").html(data.contenido);
				//dialog.modal("hide");
				$("#modalVerDetalles").modal();
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var ingresar=function(caso, lista, nombreCompleto){
		idCaso=caso;
		idLista=lista;
	}

	var reingresarListaEspera = function(caso, nombrePaciente, rutPaciente){
		$("#idCasoParaCierre").val(caso);
		$("#nombrePaciente").html(nombrePaciente);
		$("#rutPaciente").html(rutPaciente);
		$("#modalReingresoListaEspera").modal("show");
	}

	var limpiarFormDarAlta=function(){
		$("#motivo").val('').change;
		$("#fechaFallecimiento").val("");
		$("#fechaFallecimiento").attr('disabled', true);
		$("#fallecimientofecha").addClass("hidden");
		$("#medicoAlta").val("");
		$("#id_medico").val("");
		$("#detalle").val("");
		$("#detalle").attr('disabled', true);
		$("#detalleOtro").attr('disabled', true);
		$("#detalleOtro").addClass('hidden');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
		$("[name='parto']").prop('checked',false).change;
		$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
		$("#btn_egreso").prop('disabled',false);
	}

	var darAlta=function(idCaso, idLista, sexo){
		$("#idLista").val(idLista);
		$("#idCaso").val(idCaso);
		limpiarFormDarAlta();
		if(sexo == "femenino"){
			$("#divParto").show();
		}else{
			$("#divParto").hide();
		}
		$("#modalAllta").modal("show");
	};

	$(document).on('show.bs.modal', '#modalAllta', function () {
		var fecha = $("#fechaEgreso").data('DateTimePicker');
		fecha.date(moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm'));
		setTimeout(function() {
				$("#formDarAlta").bootstrapValidator("revalidateField", "fechaEgreso");
		}, 200);
	});

	var comentario=function(idCaso, idLista){
		$("#idListaComentario").val(idLista);
		$("#idCasoComentario").val(idCaso);
		cargarRegistroActividades(idCaso, idLista);
		$("#modalListaComentariosHosp").modal("show");
	}

	var cargarRegistroActividades = function(idCaso,idLista){
		$.ajax({
			url: "obtenerComentariosHospDom",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {idCaso:idCaso},
			dataType: "json",
			type: "post",
			success: function(data){
				$("#tbodyComentariosHospDom").empty();
				$('#listaComentariosHospDom').dataTable( {
					"aaData": data,
					"bDestroy": true,
					"columnDefs": [
						{ type: 'date-euro', targets: 0 }
					],
					"columns": [
						{ "data": "complejidad" },
						{ "data": "consejeria" },
						{ "data": "procedimientos" },
						{ "data": "tipo_profesional" },
						{ "data": "comentario" },
						{ "data": "fecha" },
						{ "data": "usuario_comenta"}
					]
				})
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var marcarCamaDisponible=function(event, cama){
        event.preventDefault();
		$.ajax({
			url: "DomingresarACama",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {cama: cama, idCaso: idCaso, idLista: idLista},
			type: "post",
			success: function(){
					swalExito.fire({
					title: 'Exito!',
					text: "El paciente ha sido ingresado a la cama",
					didOpen: function() {
						setTimeout(function() {
							table . api() . ajax . reload();
							location . reload(true);
						}, 2000)
					},
					});
			

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
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+" (Pediatría) </a></li>");
					}else if(data[i].id_area_funcional == 6){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+" (Adulto)</a></li>");
					}else if(data[i].id_area_funcional == 11){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+" (Neonatología)</a></li>");
					}else if(data[i].id_area_funcional == 2 && data[i].alias == "Cuidados medios"){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+" (Pediatría)</a></li>");
					}else if(data[i].id_area_funcional == 10){
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+" (Neonatología)</a></li>");
					}else{
						$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+"</a></li>");
					}
					$("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'></div>");
					generarMapaCamasDisponibles(id, data[i].url, true);
				}
				for(var i=0; i<data.length; i++){
					$("#id-"+data[i].url).removeClass("active");
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

	var generarMapaCamasDisponibles=function(mapaDiv, unidad){
 		$.ajax({
 			url: "{{URL::to('/')}}/unidad/"+unidad+"/getCamasDisponiblesVerdes",
 			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 			data: {unidad: unidad},
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

	var reporteHospDom = function(){
		$.ajax({
			url: "resumenHospDom",
			type: "GET",
			dataType: "json",
			success: function(response){
				var data = response.data;
				$("#campoTitulo1").html(data.total_hospitalizados);
				$("#campoTitulo2").html(data.total_hombres);
				$("#campoTitulo3").html(data.total_mujeres);
				$("#campoTitulo4").html(data.total_0_19);
				$("#campoTitulo5").html(data.total_20_64);
				$("#campoTitulo6").html(data.total_mayor_65);
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var limpiarRegistroActividades = function(){
		//especificacion de visitantes
		$("#tipo_profesional").attr('disabled', false);
		$("#prof").removeClass('hide');
		$("#tipo_tecnico").attr('disabled', true);
		$("#tec").addClass('hide');

		$('#formComentario').bootstrapValidator('resetForm', true);
		$("#complejidad_patologia").val('').change;
		$("#conserjeria").val('').change;
		$("#encargado_visita").val('').change;
		$("#procedimientos").selectpicker("refresh").val('');
		$("#procedimientos").selectpicker("refresh");
		$("#procedimientos_item").val(0);
		//limpiar slect especificar
		$("#tipo_profesional").selectpicker("refresh").val('');
		$("#tipo_profesional").selectpicker("refresh");
		$('#formComentario').bootstrapValidator('revalidateField', 'tipo_profesional[]');
		$('#formComentario').bootstrapValidator('revalidateField', 'procedimientos_item');
	}

	var validaciones =  function(){
		$("#formReingresoListaEspera").bootstrapValidator({
			excluded: [':disabled', ':hidden', ':not(:visible)'],
			fields: {
				caso_social: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								if(value == "no"){
									$("#tipo_caso_social").addClass("hidden");
								}else{
									$("#tipo_caso_social").removeClass("hidden");
								}
								return true;
							}
						}
					}
				},
				'diagnosticos[]': {
					validators: {
						notEmpty: {
							message: 'Debe ingresar al menos 1 diagnóstico'
						}
					}
				},
				'diagnostico[]': {
					validators: {
						notEmpty: {
							message: 'Debe ingresar el comentario'
						}
					}
				},
				"tipo-procedencia": {
					validators:{
						regexp: {
							regexp: /[12346]/,
							message: "Debe seleccionar la procedencia"
						}
					}
				},
				input_procedencia: {
					validators:{
						notEmpty: {
							message: "Debe especificar la procedencia"
						}
					}
				},
				"comentario-riesgo":{
					validators:{
						notEmpty:{
							message: "El comentario D2 y D3 debe ser obligatorio"
						}
					}
				},
				t_caso_social:{
					validators:{
						notEmpty: {
							message: "Debe especificar la el tipo de caso social"
						}
					}
				},
				'fecha-indicacion': {
					validators:{
						notEmpty: {
							message: 'La fecha es obligatoria'
						},
						callback: {
							callback: function(value, validator, $field){
								var esValidao=validarFormatoFechaHora(value);
								if(!esValidao){
									return {valid: false, message: "Formato de fecha inválido"};
								}

								if($("#fechaIngreso").val() == "")return true;

								var esMenor= compararFechaIndicacion(value, $("#fechaIngreso").val());

								if(!esMenor){
									return {valid: false, message: "Fecha debe ser menor a fecha de solicitud"};
								}
								return true;
							}
						}
					}
				},
				fechaIngreso:{
					validators:{
						notEmpty: {
							message: 'Debe ingresar una fecha de solicitud'
						},
						remote:{
							data: function(validator){
								return {
									rut: validator.getFieldElements('rut').val(),
									fechaIngreso: validator.getFieldElements('fechaIngreso').val()
								};
							},
							url: "{{ URL::to("/validarFechaIngreso") }}"
						}
					}
				},
				recibe_visitas: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								if(value == "no"){
									$(".div-recibe-visitas").addClass("hidden");
									$("#cantidad_personas").val("");
									$("#cantidad_horas").val("");
								}else{
									$(".div-recibe-visitas").removeClass("hidden");
									$("#cantidad_personas").val("");
									$("#cantidad_horas").val("");
								}
								return true;
							}
						}
					}
				},
				cantidad_personas: {
                    validators: {
                        notEmpty: {
                            message: "Debe ingresar la cantidad de personas"
                        },
                        stringLength: {
                            min: 1,
                            message: "Debe ingresar minimo 1 persona"
                        },
						integer: {
							message: 'Debe ingresar solo números'
						}
                    }
                },
				cantidad_horas: {
                    validators: {
                        notEmpty: {
                            message: "Debe ingresar la cantidad de horas"
                        },
                        stringLength: {
                            min: 1,
                            message: "Debe ingresar minimo 1 hora"
                        },
						integer: {
							message: 'Debe ingresar solo números'
						}
                    }
                }
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on('error.form.bv', function(e) {
		}).on("success.form.bv", function(evt){
			var $form = $(evt.target);
			var $button      = $form.data('bootstrapValidator').getSubmitButton();

			fv = $form.data('bootstrapValidator');
			evt.preventDefault();

			$("#btnSolicitar").attr('disabled', 'disabled');

			bootbox.confirm("<h4>¿Está seguro de solicitar la rehospitalización de este paciente?</h4>", function(result) {
				if (result) {
					$.ajax({
						url: "reingresarListaEspera",
						type: 'post',
						dataType: 'json',
						data: $('#formReingresoListaEspera').serialize()
					})
					.done(function(data) {
						if(data.error) {

						swalError.fire({
							title: 'Error',
							text:data.error
							}).then(function(result) {
							if (result.isDenied) {
								  location . reload();
							}
							});
						}else{
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
					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
					});
				}else{
					$('#btnSolicitar').removeAttr("disabled");
				}
			});
		});
	}

	var resetValidaciones =  function(){
		$("#formReingresoListaEspera").trigger("reset");
		$("#formReingresoListaEspera").data('bootstrapValidator').destroy();
		$('#formReingresoListaEspera').data('bootstrapValidator',null);
		validaciones();
	}

	$(function(){
		// validaciones();

		$("#HospitalizacionMenu").collapse();

		reporteHospDom();

		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm"
		}).on('dp.change', function (e) {
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
		});

		$("#fechaEgreso").on('keyup', function(){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
		});

		$("#modalListaComentariosHosp").on("shown.bs.modal", function () {
			$("#tipo_tecnico").attr('disabled', true);
			$('#formComentario').bootstrapValidator('revalidateField', 'tipo_profesional[]');
			$('#formComentario').bootstrapValidator('revalidateField', 'procedimientos_item');
		});

		$("#modalListaComentariosHosp").on('hidden.bs.modal', function () {
			limpiarRegistroActividades();
		});

		table=$('#listaEspera2').dataTable({
			"bJQueryUI": true,
			"iDisplayLength": 15,
			"ajax": "obtenerListaPacientes",
			"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}
		});

		table.on('preXhr.dt', function(e, settings, data){
			$(this).dataTable().api().clear();
			settings.iDraw = 0;
			$(this).dataTable().api().draw();
		});

		$("#motivo").on("change", function(){
			$("#medicoAlta").val("");
			$("#id_medico").val("");
			$("#formDarAlta").bootstrapValidator("revalidateField", "medicoAlta");
			var valor = $("#motivo").val();
			$("#fechaFallecimiento").val("");
			$("#detalle").val("");
			$("#detalleOtro").addClass('hidden');
			$("#fechaFallecimiento").attr('disabled', true);
			$("#fallecimientofecha").addClass("hidden");
			$("#detalle").attr('disabled', true);
			$("#detalleOtro").attr('disabled', true);
			if(valor == "derivacion otra institucion"){
				$("#detalle").attr('disabled', false);
				$("#detalleOtro").attr('disabled', false);
				$("#formDarAlta").bootstrapValidator("revalidateField", "detalle");
				$("#detalleOtro").removeClass('hidden');
			}else if(valor == "fallecimiento"){
				$("#fechaFallecimiento").attr('disabled', false);
				$("#fallecimientofecha").removeClass("hidden");
				$("#formDarAlta").bootstrapValidator("revalidateField", "fechaFallecimiento");
			}
		});

		var datos_cie10 = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie10',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 50
		});

		datos_cie10.initialize();

		$('.diagnostico_cie101 .typeahead').typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_cie10',
			source: datos_cie10.ttAdapter(),
			limit: 50,
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					if(data.nombre_categoria == null){
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}else{
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
			}
		}).on('typeahead:selected', function(event, selection){
			$("#texto_cie10").val(selection.nombre_cie10);
			$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
			$("#cie10-principal").prop("disabled", false);
		}).on('typeahead:close', function(ev, suggestion) {
			var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
			if(!$cie10.val()&&$(this).val()){
				$(this).val("");
				$cie10.val("");
				$(this).trigger('input');
			}
		});

		$("#formDarAlta").bootstrapValidator({
			excluded: [':disabled',':hidden'],
			fields: {
				medicoAlta:{
					validators: {
						notEmpty: {
							message: 'El nombre del médico es obligatorio'
						}
					}
				},
				fechaFallecimiento:{
					validators:{
						notEmpty: {
							message: 'Debe ingresar la fecha de fallecimiento'
						},
						callback: {
							callback: function(value, validator, $field){
								if (value === '') {
									return true;
								}
								var esValidao=validarFormatoFechaHora(value);
								if(!esValidao){
									return {valid: false, message: "Formato de fecha inválido"};
								}
								return true;
							}
						}
					}
				},
				detalle:{
					validators:{
						notEmpty: {
							message: 'Debe ingresar el detalle'
						}
					}
				},
				parto:{
					validators:{
						trigger: 'change keyup',
						notEmpty: {
							message: 'Debe seleccionar una opción'
						}
					}
				},
				fechaEgreso: {
					validators:{
						notEmpty: {
							message: 'La Fecha debe ser obligatoria'
						},
						remote: {
							data: function(validator){
								return {
									idLista: validator.getFieldElements('idLista').val(), //id lista hosp dom
									fechaEgreso: validator.getFieldElements('fechaEgreso').val()
								};
							},
							url: "{{ URL::to("/validarFechaHospDomCasoEgresado") }}"
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			$("#formDarAlta input[type='submit']").prop("disabled", false);
		}).on("success.form.bv", function(evt){
			$("#formDarAlta input[type='submit']").prop("disabled", false);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			$.ajax({
				url: "darAltaDom",
				headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "post",
				dataType: "json",
				data: $form .serialize(),
				success: function(data){
					$("#modalAllta").modal("hide");
					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								table.api().ajax.reload(); 
								reporteHospDom();
								$("#modalAllta").on('hidden.bs.modal', function () {
								limpiarFormDarAlta();
							});
							}, 2000)
						},
						});
					} 
					if(data.error){
						swalError.fire({
						title: 'Error',
						text:data.error
						});
						$("#modalAllta").on('hidden.bs.modal', function () {
							limpiarFormDarAlta();
						});
					}
				},
				error: function(error){
					console.log(error);
				}
			});
		});

		validaciones();
		
		$("#modalAllta").on('hidden.bs.modal', function () {
			limpiarFormDarAlta();
		});

		$("#btn_egresar").on('click', function (){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
			$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
		});

		$("#comentarioSubmit").on("click", function(){
			$('#formComentario').bootstrapValidator('revalidateField', 'comentario');
			$('#formComentario').bootstrapValidator('revalidateField', 'procedimientos_item');
			$('#formComentario').bootstrapValidator('revalidateField', 'tipo_profesional[]');
		});

		$("#formComentario").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				comentario: {
					validators:{
						notEmpty: {
							message: 'El comentario es obligatorio'
						}
					}
				},
				'procedimientos_item':{
  					validators: {
    					callback: {
							callback: function(value, validator, $field){
								var cantidad = $("#procedimientos_item").val();
								if (cantidad <= 0) {
									return {valid: false, message: "Debe seleccionar al menos un procedimiento" };
								}else{
									return true;
								}
							}
    					}
  					}
				},
				'tipo_profesional[]':{
					validators: {
    					callback: {
							callback: function(value, validator, $field){
								if (value == null || value.length <= 0) {
									return {valid: false, message: "Debe seleccionar al menos un procedimiento" };
								}else{
									return true;
								}
							}
    					}
  					}
				}
			}
		}).on('status.field.bv', function(e, data) {
		}).on("success.form.bv", function(evt){
			$("#comentarioSubmit").prop("disabled", true);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			$.ajax({
				url: "comentarioHospDom",
				headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
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
								limpiarRegistroActividades();
								cargarRegistroActividades(data . idcaso, data . idlista);
								table . api() . ajax . reload();
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
			$("#comentarioSubmit").prop("disabled", false);
		});

		$("#fecha-indicacion").on("dp.change", function(){
			$("#formReingresoListaEspera").bootstrapValidator("revalidateField", "fecha-indicacion");
		});

		$("#fechaIngreso").on("dp.change", function(){
			$("#formReingresoListaEspera").bootstrapValidator("revalidateField", "fechaIngreso");
		}); 

	});


	$("#procedimientos").on("change", function(){
		var largo= $("#procedimientos").children(':selected').length;
		$("#procedimientos_item").val(largo);
		$('#formComentario').bootstrapValidator('revalidateField', 'procedimientos_item');
	});

	$("#tipo-procedencia").on("change", function(){
		var value=$(this).val();
		if(value == 2){
			$(".ocultar").addClass("hidden");
			$(".estabOculto").show();
		}else{
			$(".estabOculto").hide();
			$("#input_procedencia").val("");
			$.ajax({
				url: '{{URL::to("getEspecificarProcedencia")}}',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: { "tipo-procedencia": value },
				dataType: "json",
				type: "get",
				success: function(data){
					$("#row-procedencia").empty();
					$("#row-procedencia").html(data.data);
					$("#buscarCamasForm").bootstrapValidator('addField', 'input_procedencia');
				},
				error: function(error){
					console.log(error);
				}
			});
		}
	});

	$("#fechaFallecimiento").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});

	var datos_medicos = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('medicos'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '{{URL::to('/')}}/'+'%QUERY/consulta_medicos',
			wildcard: '%QUERY',
			filter: function(response) {
			    return response;
			}
		},
		limit: 50
	});

	datos_medicos.initialize();

	$('.medicos .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'nombre_apellido',
		source: datos_medicos.ttAdapter(),
		limit: 50,
		templates: {
			empty: [
			'<div class="empty-message">',
				'No hay resultados',
			'</div>'
			].join('\n'),
			suggestion: function(data){
				var nombres = data;
				return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
		}
	}).on('typeahead:selected', function(event, selection){
		$("#medico").val('asdas');
		$("[name='id_medico']").val(selection.id_medico);
	}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".medicos").find("input[name='id_medico']");
		if(!$med.val()&&$(this).val()){
			$(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
	});

	$("#modalReingresoListaEspera").on('hidden.bs.modal', function () {
		resetValidaciones();
		$("input[name=recibe_visitas][value='no']").prop("checked",false);
		$("input[name=recibe_visitas][value='yes']").prop("checked",false);
		$(".div-recibe-visitas").addClass("hidden");
		$('.diagnostico_cie101 .typeahead').typeahead('val', '');
		$("input[name='hidden_diagnosticos[]']").val('');
	});

	$("#modalReingresoListaEspera").on('show.bd.modal', function(){
		var checkeado = $("input[name='recibe_visitas']:checked").val();
		if(checkeado === undefined){
			$("input[name=recibe_visitas][value='no']").change();
		}
	});

	$( "#riesgo" ).on("change", function() {
		$('#formReingresoListaEspera').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', false);
		if($("#riesgo").val() == "D2" || $("#riesgo").val() == "D3"){
			$('#formReingresoListaEspera').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', true);
		}
	});

	//Fecha de solicitud
	var fecha = $("#fechaIngreso").data("DateTimePicker");

	window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
				$("#fechaIngreso").focus(function(){

				   fecha.date(window._gc_now);
				   });
	// fecha.date(window._gc_now);
	fecha.minDate(moment(window._gc_now).subtract(1, "days").startOf('day'));
	fecha.maxDate(moment(window._gc_now).add(2, 'days'));

	//Fecha indicacion
	var fechaIndicacion = $("#fecha-indicacion").data("DateTimePicker");

	window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
		$("#fecha-indicacion").focus(function(){
				   fechaIndicacion.date(window._gc_now);
				   });
	// fechaIndicacion.date(window._gc_now);
	fechaIndicacion.maxDate(moment(window._gc_now).add(2, 'days'));

	function modalRiesgoDependencia(){
		idServicio = $("#servicios").val();
		if(idServicio == "195" || idServicio == "196"){
			$("#modalFormularioRiesgo2").modal();
		}
		else{
			$("#modalFormularioRiesgo").modal();
		}
	}

	function btnRiesgoDependencia (){
		var valorDependencia = 0;

		valorDependencia = parseInt($('#dependencia1').val()) +parseInt($('#dependencia2').val()) +parseInt($('#dependencia4').val()) +parseInt($('#dependencia5').val());

		if (parseInt($('#dependencia3').val()) > 10) {
			valorDependencia += parseInt($('#dependencia3').val().substr(0,1));
		}else{
			valorDependencia += parseInt($('#dependencia3').val());
		}

		if (parseInt($('#dependencia6').val()) > 10) {
			valorDependencia += parseInt($('#dependencia6').val().substr(0,1));
		}else{
			valorDependencia += parseInt($('#dependencia6').val());
		}

		var valorRiesgo = 0;

		valorRiesgo = parseInt($('#riesgo1').val()) + parseInt($('#riesgo2').val()) +parseInt($('#riesgo3').val());

		if (parseInt($('#riesgo4').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo4').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo4').val());
		}
		if (parseInt($('#riesgo5').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo5').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo5').val());
		}
		if (parseInt($('#riesgo6').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo6').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo6').val());
		}
		if (parseInt($('#riesgo7').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo7').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo7').val());
		}
		if (parseInt($('#riesgo8').val()) > 10) {
			valorRiesgo += parseInt($('#riesgo8').val().substr(0,1));
		}else{
			valorRiesgo += parseInt($('#riesgo8').val());
		}

		var riesgoDependencia = "";
		if (valorRiesgo >=19) {
			riesgoDependencia = "A";
		}else if(valorRiesgo >= 12 && valorRiesgo <= 18){
			riesgoDependencia = "B";
		}else if (valorRiesgo >= 6 && valorRiesgo <= 11) {
			riesgoDependencia = "C";
		}else{
			riesgoDependencia = "D";
		}


		if (valorDependencia >=13) {
			riesgoDependencia += "1";
		}else if(valorDependencia >= 7 && valorDependencia <= 12){
			riesgoDependencia += "2";
		}else{
			riesgoDependencia += "3";
		}
		$("#riesgo").val(riesgoDependencia).trigger("change");
		$("#div-comentario-riesgo").show();

		$('#modalFormularioRiesgo').modal('hide');

	}

	//por alguna razon el segundo modal se abria detras, esta funcion evita eso.
	(function($, window) {
		'use strict';

		var MultiModal = function(element) {
			this.$element = $(element);
			this.modalCount = 0;
		};

		MultiModal.BASE_ZINDEX = 1040;

		MultiModal.prototype.show = function(target) {
			var that = this;
			var $target = $(target);
			var modalIndex = that.modalCount++;

			$target.css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20) + 10);

			window.setTimeout(function() {
				if(modalIndex > 0)
					$('.modal-backdrop').not(':first').addClass('hidden');

				that.adjustBackdrop();
			});
		};

		MultiModal.prototype.hidden = function(target) {
			this.modalCount--;

			if(this.modalCount) {
			this.adjustBackdrop();
				$('body').addClass('modal-open');
			}
		};

		MultiModal.prototype.adjustBackdrop = function() {
			var modalIndex = this.modalCount - 1;
			$('.modal-backdrop:first').css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20));
		};

		function Plugin(method, target) {
			return this.each(function() {
				var $this = $(this);
				var data = $this.data('multi-modal-plugin');

				if(!data)
					$this.data('multi-modal-plugin', (data = new MultiModal(this)));

				if(method)
					data[method](target);
			});
		}

		$.fn.multiModal = Plugin;
		$.fn.multiModal.Constructor = MultiModal;

		$(document).on('show.bs.modal', function(e) {
			$(document).multiModal('show', e.target);
		});

		$(document).on('hidden.bs.modal', function(e) {
			$(document).multiModal('hidden', e.target);
		});
	}(jQuery, window));

</script>
<meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("estilo-tabla")
	{{ HTML::style('css/sigicam/tablas.css') }}
@stop

@section("miga")
	<li><a href="#">Hospitalización Domiciliaria</a></li>
	<li><a href="#">Lista de pacientes</a></li>
@stop

@section("section")
<style>

	.formulario > .panel-default > .panel-heading {
		background-color: #bce8f1 !important;
	}
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

	#modalReingresoListaEspera {
		overflow-y:scroll
	}

	#modalVerDetalles {
		overflow-y:scroll
	}

	#modalFormularioRiesgo {
		overflow-y:scroll
	}

	/*diseño*/
	.tituloReporte {
		color: #6A7888;

	}

	.main-overview {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(225px, 1fr)); /* Where the magic happens */
		grid-auto-rows: 74px;
		grid-gap: 10px;
		margin: 20px;
	}

	.overviewcard {
		height: 70px !important;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 10px;
		background-color: #cff0ce;
	}

	.numeroActual {
		color: #6A7888;
		margin-top: 0px !important;
		margin-bottom: 0px !important;
		/*font-size: 15px !important;*/
	}

	.tamano {
		font-size: 13px !important;
	}

	h1 {
		font-size: 25px;
		text-align: center;
		font-weight: bold;
	}
	.padding_corregidgo{
		padding-left: 5px;
		padding-right: 5px;
		padding-top: 5px;
		padding-bottom: 5px;
	}
	.calculando {
		font-size: 23px;
	}
/*diseño*/
</style>

<div class="col-sm-12">
	<fieldset>
		<legend>Resumen Hospitalización Domiciliaria</legend>
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
				<div class="overviewcard">
					<div class="overviewcard__icon">
						<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40"> Total Pacientes</label>
					</div>
					<div class="overviewcard__info" >
						<h1 class="numeroActual calculando" id="campoTitulo1">Calculando...</h1>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
				<div class="overviewcard">
					<div class="overviewcard__icon">
						<label class="tituloReporte"><img src="{{ asset('img/hombre.png') }}" width="30" height="40"> Pacientes hombres</label>
					</div>
					<div class="overviewcard__info" >
						<h1 class="numeroActual calculando" id="campoTitulo2">Calculando...</h1>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
				<div class="overviewcard">
					<div class="overviewcard__icon">
						<label class="tituloReporte"><img src="{{ asset('img/mujer.png') }}" width="30" height="40"> Pacientes mujeres</label>
					</div>
					<div class="overviewcard__info" >
						<h1 class="numeroActual calculando" id="campoTitulo3">Calculando...</h1>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
				<div class="overviewcard">
					<div class="overviewcard__icon">
						<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40"> Tramo edad 0-19 años</label>
					</div>
					<div class="overviewcard__info" >
						<h1 class="numeroActual calculando" id="campoTitulo4">Calculando...</h1>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
				<div class="overviewcard">
					<div class="overviewcard__icon">
						<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40"> Tramo edad 20-64 años</label>
					</div>
					<div class="overviewcard__info" >
						<h1 class="numeroActual calculando" id="campoTitulo5">Calculando...</h1>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
				<div class="overviewcard">
					<div class="overviewcard__icon">
						<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40"> Tramo mayor 65 años</label>
					</div>
					<div class="overviewcard__info" >
						<h1 class="numeroActual calculando" id="campoTitulo6">Calculando...</h1>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
			<legend>Lista de pacientes</legend>
			<div class="col form-inline">
				{{ HTML::link("hospitalizacion/excelHospitalizacionDomiciliaria", 'Excel', ['class' => 'btn btn-success']) }}
			</div>
			<br>
			<div class="table-responsive">
				<table id="listaEspera2" class="table table table-hover  tabla-sigicam">
					<thead>
						<tr>
							<th>Opciones</th>
							<th>Nombre y Apellidos</th>
							<th>Run</th>
							<th>Diagnostico</th>
							<th>Fecha</th>
							<th>Categorización</th>
							<th>Comentario</th>
							<th>Dirección</th>
							<th>Teléfono</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
	</fieldset>
</div>


<div id="modalIngresar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Asignar cama</h4>
				<div class="nombreModal"></div>
			</div>
			<div class="modal-body">
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

<div id="modalAllta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formDarAlta')) }}
			{{ Form::hidden('idLista', '', array('id' => 'idLista')) }}
			{{ Form::hidden('idCaso', '', array('id' => 'idCaso')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea egresar al paciente ?</h4>
					</div>

					<div class="form-group col-md-12">
						<label for="fechaEgreso" class="col-sm-2 control-label">Fecha de egreso: </label>
						<div class="col-sm-10">
							{{Form::text('fechaEgreso', null, array('id' => 'fechaEgreso', 'class' => 'form-control fecha-sel'))}}
						</div>
					</div>

					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Motivo de egreso: </label>
						<div class="col-sm-10">
							{{ Form::select('motivo', array('alta' => '1. Alta', 'derivacion otra institucion' => '2. Derivación a otros centros u otra institución', 'rechaza atencion' => '3. Rechaza Atención', 'fallecimiento' => '4. Fallecimiento'), null, array('id' => 'motivo', 'class' => 'form-control')) }}
						</div>
					</div>

					<div class="form-group hidden col-md-12" id="fallecimientofecha">
						<label for="fallec" class="col-sm-2 control-label">Fecha: </label>
						<div class="col-sm-10 ">
							{{Form::text('fechaFallecimiento', null, array('id' => 'fechaFallecimiento', 'class' => 'form-control'))}}
						</div>
					</div>

					<div class="form-group hidden col-md-12" id="detalleOtro">
						<label for="horas" class="col-sm-2 control-label">Detalle: </label>
						<div class="col-sm-10">
							{{Form::textarea('detalle', null, array('id' => 'detalle', 'class' => 'form-control', 'rows' => '2'))}}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label for="medicoAlta" class="col-sm-2 control-label">Medico alta: </label>
						<div class="col-sm-10 medicos">
							{{Form::text('medicoAlta', null, array('id' => 'medicoAlta', 'class' => 'form-control typeahead'))}}
							{{Form::hidden('id_medico', null, array('id' => 'id_medico'))}}
						</div>
					</div>
					@include('Egreso.partialParto')
				</div>
			</div>
			<div class="modal-footer">
				<button id="btn_egresar" type="submit" class="btn btn-primary">Liberar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

<div id="modalListaComentariosHosp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
	  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			  <h4 class="modal-title">Historial De Comentarios</h4>
			</div>

		  	<div class="modal-body">
				{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formComentario')) }}
				{{ Form::hidden('idCaso', '', array('id' => 'idCasoComentario')) }}
				{{ Form::hidden('idLista', '', array('id' => 'idListaComentario')) }}
					<div class="row">
						<div class="form-group col-md-4">
							<div class="col-sm-12">
								<label for="dau" class="control-label" title="DAU">Complejidad patologia</label>
								{{ Form::select('complejidad_patologia', array('Baja' => '1. Baja', 'Media' => '2. Media', 'Alta' => '3. Alta'), null, array('id' => 'complejidad_patologia', 'class' => 'form-control')) }}
							</div>
						</div>
						<div class="form-group col-md-4 col-md-offset-1">
							<div class="col-sm-12">
								<label for="dau" class="control-label" title="DAU">Consejeria</label>
								{{ Form::select('conserjeria', array('Familiar' => '1. Familiar', 'Individual' => '2. Individual', 'N/A' => '3. N/A'), null, array('id' => 'conserjeria', 'class' => 'form-control')) }}
							</div>
						</div>
						<div class="form-group col-md-4">
							<div class="col-sm-12">
								<label for="dau" class="control-label" title="DAU">Procedimientos</label>
								{{ Form::select('procedimientos[]', array('Control de signos vitales' => '1. Control de signos vitales', 'Administración de medicación EV-SC' => '2. Administración de medicación EV-SC', 'Hemoglucotest' => '3. Hemoglucotest', 'Toma de examanes' => '4. Toma de examanes','Cuidados pie diabetico' => '5. Cuidados pie diabetico', 'Cuiración ulcera venosa' => '6. Cuiración ulcera venosa', 'Curación TQT' => '7. Curación TQT', 'Curación avanzada' => '8. Curación avanzada', 'Curación plana' => '9. Curación plana', 'Extracción de puntos' => '10. Extracción de puntos'), null, array('id' => 'procedimientos', 'class' => 'selectpicker form-control', 'multiple')) }}
								{{ Form::text('procedimientos_item', "0", array('class' => 'form-control ', "id" => "procedimientos_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-4">
							<div class="col-sm-12">
								<label for="dau" class="control-label" title="DAU">Encargado de visita</label>
								{{ Form::select('tipo_profesional[]', array('Tens' => 'Tens', 'Enfermera/o' => 'Enfermera/o' ,'Kinesiologa/o' => 'Kinesiologa/o', 'Médico' => 'Médico'), null, array('id' => 'tipo_profesional', 'class' => 'selectpicker form-control', 'multiple')) }}
							</div>
						</div>
					</div>

					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<h4>Evolución de paciente (máximo 100 caracteres)</h4>
						</div>
						<div class="form-group col-md-12">
							{!! Form::textarea('comentario', null, ['class' => 'form-control', 'id' => 'comentarioForm', 'rows' => 4, 'cols' => 54, 'style' => 'resize:none']) !!}
						</div>
						<button id="comentarioSubmit" type="submit" class="btn btn-primary">Agregar Comentario</button>
					</div>
				{{ Form::close() }}
				<fieldset>
					<legend>Hospitalización Domiciliaria</legend>
					<!-- recargando la tabla -->
					<div class="table-responsive">
					<table id="listaComentariosHospDom" class="table  table-condensed table-hover">
						<thead>
							<tr style="background:#399865;">
								<th>Complejidad patologia</th>
								<th>Consejeria</th>
								<th>Procedimientos</th>
								<th>Encargado de visita</th>
								<th style="width:100px">Comentario</th>
								<th>Fecha</th>
								<th>Usuario que comenta</th>
							</tr>
						</thead>
						<tbody id="tbodyComentariosHospDom"></tbody>
					</table>
					</div>
				</fieldset>
			</div>
		  <div class="modal-footer">
			  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
		  </div>
	  </div>
	</div>
</div>

<div id="modalVerDetalles" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Categorización riesgo dependencia
				</h4>
				<div class="nombreModal"></div>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalVerDiagnosticos" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Historial de diagnósticos</h4>
				<div class="nombreModal"></div>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formReingresoListaEspera')) }}
	{{ Form::hidden('idCaso', '', array('id' => 'idCasoParaCierre')) }}
	<div id="modalReingresoListaEspera" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title">Reingreso de Paciente</h4>
					</div>
					<div class="modal-body">
						<div class="formulario">
							<div class="panel panel-default">
								<div class="panel-heading panel-info">
									<h4>Datos Atención Clínica:</h4>
								</div>
								<div class="panel-body">
									<div class="col-md-12">
										<div class="col-md-2">
											@if (Auth::user()->tipo == "usuario")
												<label for="dau" class="control-label" title="DAU">DAU (*): </label>
											@else
												<label for="dau" class="control-label" title="DAU">DAU: </label>
											@endif
											{{Form::text('dau', null, array('id' => 'dau', 'class' => 'form-control'))}}
										</div>
										<div class="col-md-2 col-md-offset-1">
											@if (Auth::user()->tipo == "gestion_clinica" || Auth::user()->tipo == "enfermeraP")
												<label for="fichaClinica" class="control-label" title="Ficha clinica">Número de ficha clínica : </label>
											@else
												<label for="fichaClinica" class="control-label" title="Ficha clinica">Número de ficha clínica: </label>
											@endif
											{{Form::text('fichaClinica', null, array('id' => 'fichaClinica', 'class' => 'form-control'))}}
										</div>
										<div class="col-md-4 col-md-offset-1 medicos">
											<label for="medico" class="control-label" title="Médico">Médico: </label>
											{{Form::text('medico', null, array('id' => 'medico', 'class' => 'form-control typeahead'))}}
											{{Form::hidden('id_medico', null)}}
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading panel-info">
									<h4>Datos Personales:</h4>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-6">
											{{Form::label('Nombre Paciente:')}}
											<p id="nombrePaciente"></p>
										</div>
										<div class="col-md-2">
											{{Form::label('Rut Paciente:')}}
											<p id="rutPaciente"></p>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading panel-info">
									<h4>Datos de Solicitud de cama:</h4>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-12">
											<div class="col-sm-8 diagnostico_cie101 pr0 form-group" style="">
												<label for="files[]" class="control-label" title="Diagnóstico CIE10">Diagnóstico CIE10 (*):</label>
												<input type="text" name="diagnosticos[]" class='form-control typeahead' />
												<input type="hidden" name="hidden_diagnosticos[]">
											</div>
											<div class="col-sm-2">
												<label>&nbsp;&nbsp;</label>
												<button style="margin-left: 5px; margin-top: 2px;" disabled id="cie10-principal" class="btn btn-default w100" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
											</div>

											<div class="col-sm-12 form-group">
												<label for="diagnostico" class="control-label" title="Comentario de diagnóstico">Complemento del diagnóstico (*): </label>
												{{Form::text('diagnostico[]', null, array('class' => 'form-control', "style" => "width:96%"))}}
											</div>

											<br>

											<div id="fileTemplate" class="hide">

												<div class="form-group col-md-12">
														<!--<div class="col-md-2"></div>-->
													<div class="col-md-9 diagnostico_cie101">
													<label for="files[]" class="control-label" title="Diagnóstico CIE10">Diagnóstico CIE10:</label>

															<input type="text" name="diagnosticos[]" class='form-control typeahead'/>
															<input type="hidden" name="hidden_diagnosticos[]">
													</div>
													<div class="col-md-3" style="right: 25px; top: 23px">
														<button disabled class="btn btn-default" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
														<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
													</div>
													<div class="col-sm-12">
													<label for="diagnostico" class="control-label" title="Comentario de diagnóstico">Complemento del diagnóstico: </label>

														{{Form::text('diagnostico[]', null, array('class' => 'form-control', "style" => "width:96%"))}}
													</div>

												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group col-md-6">
											<div class="col-sm-6 pr0">
												<label for="riesgo" class="control-label" title="Riesgo">Riesgo: </label>
												{{ Form::text('riesgo', null, array('id' => 'riesgo','class' => 'form-control', 'readonly', 'style' => 'text-align:center;')) }}
											</div>
											<div class="col-sm-6">
												<label >&nbsp;&nbsp;</label>
												<a id="riesgo" type="" class="btn btn-primary w100" onclick="modalRiesgoDependencia()" >Calcular Riesgo - Dependencia</a>
											</div>
										</div>

										<div class="col-sm-6" id="div-comentario-riesgo" hidden>
											<label for="riesgo" class="control-label" title="Riesgo">Comentario riesgo: </label>
											{{ Form::textarea('comentario-riesgo', null, array('id' => 'comentario-riesgo','class' => 'form-control', 'rows'=>'2')) }}
										</div>
									</div>

									<div class="form-group col-md-4" style="margin-left:-28px;">
										<div class="col-sm-12">
											<label for="tipo-procedencia" class="control-label" title="Procedencia">Origen de la solicitud (*): </label>
											{{ Form::select('tipo-procedencia', [0 => "Seleccionar procedencia"] + $procedencias, 0, array('class' => 'form-control', "id" => "tipo-procedencia")) }}
										</div>
									</div>

									<div class="row" id="row-procedencia">
									</div>

									<div class="col-md-12" style="margin-left:-28px;">
										<div class="col-md-12">
											<div class="form-group estabOculto" style="display: none">
												<label for="Establecimiento" class="col-sm-2 control-label">Especifique origen: </label>
												<div class="col-sm-10 estab">
													{{Form::text('input_procedencia_establecimiento', null, array('id' => 'input_procedencia_establecimiento', 'class' => 'form-control typeahead', 'required' => 'required'))}}
													{{Form::hidden('id_procedencia', null)}}
												</div>
											</div>
										</div>
									</div>

									{{-- <div class="col-md-12" style="margin-left:-28px;"> 
										<div class="col-md-3" style="padding-top: 7px;">
											<div class="form-group col-md-12">
												{{Form::label('', "¿Puede recibir visitas?", array( 'class' => 'control-label', 'style' => 'text-align:left !important'))}}
												<br>
												<label class="radio-inline">{{Form::radio('recibe_visitas', "no", false, array('required' => true))}}No</label>
												<label class="radio-inline">{{Form::radio('recibe_visitas', "si", false, array('required' => true))}}Sí</label>
											</div>
										</div>
						
										<div class="form-group col-md-4 div-recibe-visitas hidden">
											<div class="col-sm-12 col-md-12">
												<label for="recibe_visitas" class="control-label" title="Motivo">Cantidad de personas: </label>
												{{Form::number('cantidad_personas', null, array('id' => 'cantidad_personas', 'class' => 'form-control', 'min' => '1', 'enabled', "style" => "width:100%"))}}
											</div>
										</div>
										<div class="form-group col-md-4 div-recibe-visitas hidden">
											<div class="col-sm-12 col-md-12">
												<label for="recibe_visitas" class="control-label" title="Motivo">Cantidad de horas: </label>
												{{Form::number('cantidad_horas', null, array('id' => 'cantidad_horas', 'class' => 'form-control', 'min' => '1', 'enabled', "style" => "width:100%"))}}
											</div>
										</div>
									</div> --}}

									<div class="col-md-12" style="margin-left: -27px;">
										{{-- <br> --}}
										<div class="col-md-2">
											<label for="caso_social" title="Caso social">Caso social (*): </label>
											<div class="input-group">
												<label class="radio-inline">{{Form::radio('caso_social', "no", true, array('required' => true))}}No</label>
												<label class="radio-inline">{{Form::radio('caso_social', "si", false, array('required' => true))}}Sí</label>
											</div>
										</div>
										<div class="col-md-4 col-md-offset-1 hidden" id="tipo_caso_social">
											<label for="t_caso_social" title="Tipo de caso social">Tipo de caso social: </label>
												{{ Form::text('t_caso_social', null, array( 'class' => 'form-control')) }}
										</div>

										<div class="col-md-2 col-md-offset-1">
											<label for="requiere_aislamiento" title="Caso social">Requiere aislamiento: </label>
											<div class="input-group">
												<label class="radio-inline">{{Form::radio('requiere_aislamiento', "false", true)}}No</label>
												<label class="radio-inline">{{Form::radio('requiere_aislamiento', "true", false)}}Sí</label>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group col-md-6">
											<div class="col-sm-12">
												<label for="fecha-indicacion" class="control-label" title="Fecha de nacimiento">Fecha indicación médica de hospitalización: </label>
												{{Form::text('fecha-indicacion', null, array('id' => 'fecha-indicacion', 'class' => 'form-control fecha-sel'))}}
											</div>
										</div>

										<div class="col-md-6" id="divFechaIngreso">
											<div class="form-group pr30">
												<div class="col-sm-12">
													<label for="fechaIngreso" class="control-label" title="Fecha de ingreso">Fecha de solicitud de cama (*): </label>
													{{Form::text('fechaIngreso', null, array('id' => 'fechaIngreso', 'class' => 'form-control fecha-sel'))}}
												</div>
											</div>
											<div class="form-group col-md-12" id="categorizacionesIngreso">
											</div>
										</div>
									</div>

									<div class="col-md-3">
										<br>
										<input id="btnSolicitar" type="submit" name="" class="btn btn-primary" value="Rehospitalizar">
									</div>
								</div>
							</div>
						</div>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

	<div id="modalFormularioRiesgo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

		<div class="modal-dialog" style="width: 80%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="modal-title" style="font-size:20px;">Formulario de Riesgo - Dependencia</div>
				</div>

				<div class="modal-body">
					<div class="row" style="margin: 0; padding-bottom: 15px;">
						<div class="col-sm-12 control-label" style="font-size:15px;">CUIDADOS QUE IDENTIFICAN DEPENDENCIA <div>
					</div>

					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">1.- Cuidados en Confort y Bienestar: </label>
							<label for="horas" class="col-sm-4 control-label">Cambio de Ropa de cama y/o personal, o Cambio de Pañales, o toallas o apositos higenicos</label>
							<div class="col-sm-6">
								<select name="dependencia1" id="dependencia1" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario receptor de estos cuidados básicos, requeridos 3 veces o más(con/sin participación de la familia)">3 pts. </option>
									<option value="2" data-subtext="Usuario receptor de estos cuidados básicos 2 veces al día (con/sin participación de la familia)">2 pts.</option>
									<option value="1" data-subtext="Usuario y familia realizan estos cuidados con ayuda y supervisión, cualquiera sea la frecuencia">1 pts.</option>
									<option value="0" data-subtext="Usuario realiza solo el auto cuidado de cambio de ropa o cambio de pañal, toallas o apósitos higienicos">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">2.- Cuidados en Confort y Bienestar: </label>
							<label for="horas" class="col-sm-4 control-label">Movilización y Transporte(levantada, deambulación y cambio de posición) </label>
							<div class="col-sm-6">

								<select name="dependencia2" id="dependencia2" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario no se levanta y requiere cambio de posición en cama, 10 o más veces al día con/sin participación de familia">3 pts. </option>
									<option value="2" data-subtext="Usuario es levantado a silla y requiere de cambio de posición, entre 4 a 9 veces al día sin/con participación de familia">2 pts.</option>
									<option value="1" data-subtext="Usuario se levanta y deambula con ayuda y se cambia de posición en cama, solo o con ayuda de familia">1 pts.</option>
									<option value="0" data-subtext="Usuario deambula sin ayuda y se moviliza solo en cama">0 pts.</option>
								</select>

							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">3.- Cuidados de Alimentación: </label>
							<label for="horas" class="col-sm-4 control-label"> Oral, Enteral o Parenteral  </label>
							<div class="col-sm-6">
								<select name="dependencia3" id="dependencia3" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Usuario recibe alimentación y/o hidratación por vía parenteral total/parcial y requiere control de ingesta oral">3 pts. </option>
									<option value="32" data-subtext="Usuario recibe alimentación por vía enteral permanente o discontinua">3 pts.</option>
									<option value="2" data-subtext="Usuario recibe alimentación por vía oral, con asistencia del personal de enfermería">2 pts.</option>
									<option value="1" data-subtext="Usuario se alimenta por vía oral, con ayuda y supervisión'">1 pts.</option>
									<option value="0" data-subtext="Usuario se alimenta sin ayuda">0 pts.</option>

								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">4.- Cuidados de Eliminación: </label>
							<label for="horas" class="col-sm-4 control-label">  Orina, Deposiciones </label>
							<div class="col-sm-6">
								<select name="dependencia4" id="dependencia4" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario elimina a través de sonda, prótesis, procedim, dialiticos, colectires adhesivos o pañales">3 pts. </option>
									<option value="2" data-subtext="Usuario elimina por vía natural y se le entregan o colocan al usuario los colectores(chata o pato)">2 pts.</option>
									<option value="1" data-subtext="Usuario y familia realizan recolección de egresos con ayuda o supervisión">1 pts.</option>
									<option value="0" data-subtext="Usuario usa colectores(chata o pato) sin ayuda y/o usa WC">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">5.- Apoyo Psicosocial y Emocional: </label>
							<label for="horas" class="col-sm-4 control-label"> a usuario receptivo, angustiado, triste, agresivo, evasivo </label>
							<div class="col-sm-6">
								<select name="dependencia5" id="dependencia5" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Usuario recibe más de 30 minutos de apoyo durante turno">3 pts. </option>
									<option value="2" data-subtext="Usuario recibe entre 15 y 30 min. de apoyo durante turno">2 pts.</option>
									<option value="1" data-subtext="Usuario recibe entre 5 y 14 min. de apoyo durante el turno">1 pts.</option>
									<option value="0" data-subtext="Usuario recibe menos de 5 min. de apoyo durante el turno">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">6.- Vigilancia: </label>
							<label for="horas" class="col-sm-4 control-label">  por alteración conciencia, riesgo caída o riesgo incidente (desplazamiento, retiro de vías, sondas, tubos), limitación física o por edad o de los sentidos </label>
							<div class="col-sm-6">
								<select name="dependencia6" id="dependencia6" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Usuario con alteración de conciencia">3 pts. </option>
									<option value="32" data-subtext="Usuario con riesgo de caída o incidentes">3 pts.</option>
									<option value="2" data-subtext="Usuario conciente pero intranquilo y c/riesgo caída o incidente">2 pts.</option>
									<option value="1" data-subtext="Usuario conciente pero con inestabilidad de la marcha o no camina por alteración física">1 pts.</option>
									<option value="0" data-subtext="Usuario conciente, orientado, autónomo">0 pts.</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row" style="margin: 0; padding-bottom: 15px;">
						<div for="horas" class="col-sm-12 control-label" style="font-size: 15px;">CUIDADOS ESPECIFICOS DE ENFERMERIA QUE IDENTIFICAN RIESGO <div>
					</div>

					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">1.- Medición diaria de Signos Vitales (2 o mas parámetros simultáneos): </label>
							<label for="horas" class="col-sm-4 control-label">  Presión arterial, temperatura corporal, frecuencia cardiaca, frecuencia respiratoria, nivel de dolor y otros  </label>
							<div class="col-sm-6">
								<select name="riesgo1" id="riesgo1" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Control por 8 veces y más (cada 3 horas o más frecuente)">3 pts. </option>
									<option value="2" data-subtext="Control por 4 a 7 veces (cada 4, 5, 6 o 7 horas)">2 pts.</option>
									<option value="1" data-subtext="Control por 2 a 3 veces (cada 8, 9, 10, 11 o 12 horas">1 pts.</option>
									<option value="0" data-subtext="Control por 1 vez (cada 13 a cada 24 horas">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">2.- Balance hidrico: </label>
							<label for="horas" class="col-sm-4 control-label">Medición de Ingreso y Egreso realizado por profesionales en las ultimas 24 hrs.</label>
							<div class="col-sm-6">
								<select name="riesgo2" id="riesgo2" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Balance hidrico por 6 veces o más (cada 4 horas o más frecuente)">3 pts. </option>
									<option value="2" data-subtext="Balance hidrico por 2 a 5 veces (cada 12, 8 ,6 o 5 horas)">2 pts.</option>
									<option value="1" data-subtext="Balance hidrico por 1 vez (cada 24 horas o menor de cada 12 horas)">1 pts.</option>
									<option value="0" data-subtext="No requiere">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">3.- Cuidados en Oxigenoterapia: </label>
							<label for="horas" class="col-sm-4 control-label">Por cánula de traqueostomía, tubo endotraqueal, cámara, halo, máscara,
							sonda o bigotera.</label>
							<div class="col-sm-6">
								<select name="riesgo3" id="riesgo3" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Administración de oxígeno por tubo o cánula endotraqueal">3 pts. </option>
									<option value="2" data-subtext="Administración de oxígeno por máscara">2 pts.</option>
									<option value="1" data-subtext="Administración de oxígeno por canula nasal">1 pts.</option>
									<option value="0" data-subtext="Sin oxigenoterapia">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">4.- Cuidados diarios de la Vía Aérea: </label>
							<label for="horas" class="col-sm-4 control-label">Aspiración de secreciones y Apoyo kinesico requerido</label>
							<div class="col-sm-6">
								<select name="riesgo4" id="riesgo4" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Usuario con vía aérea artificial (tubo o cánula endotraqueal)">3 pts. </option>
									<option value="32" data-subtext="Usuario con vía aérea artif. o natural con 4 o + aspiraciones secreciones fraqueales y/o kinésico + de 4 veces">3 pts. </option>
									<option value="2" data-subtext="Usuario respira por vía natural y requiere de 1 a 3 aspiraciones de secreciones y/o apoyo kinésico 2 a 3 veces al día">2 pts.</option>
									<option value="1" data-subtext="Usuario respira por vía natural, sin aspiración de secreciones y/o apoyo kinésico 1 vez al día">1 pts.</option>
									<option value="0" data-subtext="Usuario no requiere de apoyo ventilatorio adicional">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">5.- Intervenciones profesionales: </label>
							<label for="horas" class="col-sm-4 control-label">Intervenciones quirurgicas y procedimientos invasivos, tales como punciones, toma de muestras, instalaciones de las vías, sondas y tubos .</label>
							<div class="col-sm-6">
								<select name="riesgo5" id="riesgo5" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="1 o más procedimientos invasivos realizadosmédicos en ultimas 24 horas">3 pts. </option>
									<option value="32" data-subtext="3 o más procedimientos invasivos realizados por enfermeras en últimas 24 horas">3 pts. </option>
									<option value="21" data-subtext="1 o 2 procedimientos invasivos realizados por enfermeras en últimas 24 horas">2 pts.</option>
									<option value="22" data-subtext="1 o más procedimientos invasivos realizados por otros profesionales  en últimas 24 horas">2 pts.</option>
									<option value="0" data-subtext="No se realizan procedimientos invasivos en 24 horas">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">6.- Cuidados de Piel y Curaciones: </label>
							<label for="horas" class="col-sm-4 control-label">Prevención de lesiones de la piel y curaciones o refuerzo de apósitos</label>
							<div class="col-sm-6">
								<select name="riesgo6" id="riesgo6" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Curación o refuerzo de apósitos 3 o más veces en el día, independiente de la complejidad de la técnica empleada">3 pts. </option>
									<option value="21" data-subtext="Curación o refuerzo de apósitos 1 a 2 veces en el día, independiente de la complejidad de la técnica empleada">2 pts.</option>
									<option value="22" data-subtext="Prevención compleja de lesiones de la piel: uso de colchón antiescaras, piel de cordero u otro">2 pts.</option>
									<option value="1" data-subtext="Prevención corriente de lesiones: aseo, lubricación y protección de zonas propensas">1 pts.</option>
									<option value="0" data-subtext="No requiere">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">7.- Administración de Tratamiento Farmacologico: </label>
							<label for="horas" class="col-sm-4 control-label">Por vía inyectable EV, inyectable no EV, y por otras vías tales como oral, ocular, aérea, etc </label>
							<div class="col-sm-6">
								<select name="riesgo7" id="riesgo7" class="form-control selectpicker" data-show-subtext="true">
									<option value="31" data-subtext="Tratamiento intratecal e inyectable endovenoso, directo o por fleboclisis">3 pts. </option>
									<option value="32" data-subtext="Tratamiento dirario con 5 o más fármacos distintos, administrados por diferentes vías no inyectable">3 pts. </option>
									<option value="21" data-subtext="Tratamiento inyectable no endovenoso (IM, SC, ID)">2 pts.</option>
									<option value="22" data-subtext="Tratamiento diario con 2 a 3 fármacos, administrados por diferentes vías no inyectable">2 pts.</option>
									<option value="1" data-subtext="Tratamiento con 1 fármaco, administrado por diferentes vías no inyectable">1 pts.</option>
									<option value="0" data-subtext="Sin tratamiento farmacológico">0 pts.</option>
								</select>
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">8.- Presencia de Elementos Invasivos: </label>
							<label for="horas" class="col-sm-4 control-label">Catéteres y vías vasculares centrales y/o periféricas. Manejo de sondas urinarias y digestivas a permanencia. Manejo de drenajes intracavitareos o percutáneos</label>
							<div class="col-sm-6">
								<select name="riesgo8" id="riesgo8" class="form-control selectpicker" data-show-subtext="true">
									<option value="3" data-subtext="Con 3 o más elementos invasivos (sondas, drenajes, cateteres o vías vasculares)">3 pts. </option>
									<option value="21" data-subtext="Con 1 o 2 elementos invasivos (sonda, drenaje, vía arterial, cateter o vía venosa central)">2 pts.</option>
									<option value="22" data-subtext="Con 2 o más vías venosas perféricas (mariposas, teflones, agujas)">2 pts.</option>
									<option value="1" data-subtext="Con 1 vías venosa periférica (mariposas, teflones, agujas)">1 pts.</option>
									<option value="0" data-subtext="Sin elementos invasivos">0 pts.</option>
								</select>
							</div>
						</div>

					</div>

				</div>
				<div class="modal-footer">
					<a type="button" class="btn btn-primary"  onclick="btnRiesgoDependencia()">Aceptar</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
{{ Form::close() }}
@stop
