@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("script")

<script>
	//Modificar tamaño tabla
	$("#main_").removeClass("col-md-7");
	$("#main_").addClass("col-md-9");

	var table=null;
	var idCaso=null;
	var idLista=null;

	function editarPaciente(idPaciente,idCaso){
		var ubicacion = "lista_transito";
		$.ajax({
 			url: "{{URL::to('/')}}/paciente/puedeHacer/"+idCaso+"/"+ubicacion,
 			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 			dataType: "json",
 			type: "get",
 			success: function(response){
				console.log(response);
				if(response.exito){
					window.location.href = "{{url('paciente/editar/')}}"+"/"+idPaciente;
				}

				if(response.error){
					swalError.fire({
						title: 'Error',
						text:response.error
					});
					cargarListaEsperaHosp($("#procedencia").val());
				}
 			},
 			error: function(error){
				console.log(error);
 			}
 		});
	}

	function regresarListaEspera(idCaso){
		var ubicacion = "lista_transito";
		$.ajax({
 			url: "{{URL::to('/')}}/regresarEspera/"+idCaso+"/"+ubicacion,
 			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 			dataType: "json",
 			type: "get",
 			success: function(response){
				console.log(response);
				if(response.exito){
					window.location.href = "{{url('urgencia/listaEspera')}}";
				}

				if(response.error){
					swalError.fire({
						title: 'Error',
						text:response.error
					});
					cargarListaEsperaHosp($("#procedencia").val());
				}
 			},
 			error: function(error){
				console.log(error);
 			}
 		});
	}

	var trasladoUnidadHosp=function(caso, lista){

		idCaso = caso;
		idLista = lista;
		var ubicacion = 'lista_transito';
		$("#modaltrasladoUnidadHosp").modal("show");
		$(".idLista").val(idLista);

		$.ajax({
			url: "getFechaTrasladoUnidadHosp",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: {idLista:idLista, idCaso: idCaso, ubicacion:ubicacion},
			success: function(data){

				if(data.error){
					swalError.fire({
						title: 'Error',
						text:data.error
					});
					cargarListaEsperaHosp($("#procedencia").val());
					$("#modaltrasladoUnidadHosp").modal("hide");
				}

				$("#fecha-indicacion").val(data.fecha);

				//$("#select_tipo_transito").val(data.tipo_transito);
			},
			error: function(error){
				console.log(error);
			}
		});


	}

	var editarCama=function(caso, lista, nombreCompleto){
		idCaso=caso;
		idLista=lista;
		$(".nombreModal").html(nombreCompleto);
		getUnidades();
		setTimeout(function(){
			$("#modalIngresar").modal("show");
		},2000);
		$("#mensajeError").hide();

	}

	var getUnidades=function(){
		var unidades=[];
		swalCargando.fire({});
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

					//$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+"</a></li>");
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

				if(data.length > 0) {
					$("#id-"+data[0].url).addClass("active");
					$("#id-"+data[0].url).tab("show");
				}
				setTimeout(function(){
				swalCargando.close();
				Swal.hideLoading();
				},2000);
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



	var ingresar=function(caso, lista, cama){
		$("#input_comentario_visitas").hide();
		$("#idCasoVisitas").val(caso);
		$("#idCama").val(cama);
		$("#idLista").val(lista);
		$("#modalVisitas").modal();
	}

	var limpiarFormDatAlta=function(){
		$("#select-motivo-liberacion").val('').change;
		$("#input-alta").val('');
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#inputProcedenciaExtra").val('');
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").val("");
		$("#id_procedenciaExtra").attr('disabled', true);
		$(".extraOculto").hide();
		$("#inputProcedencia").val('');
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".estabOculto").hide();
		$("#fechaFallecimiento").val("");
		$("#fallecimientofecha").addClass("hidden");
		$("#fechaFallecimiento").removeClass("required");
		$("#fechaFallecimiento").attr('disabled',true);
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled',false);
		$("#medicoAlta").val("");
		$("#id_medico").attr('disabled', false);
		$("#id_medico").val("");
		$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'ficha');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
	}

	var darAltaTransito=function(idCaso, idLista, ficha, nombreCompleto, cama, sexo){
		limpiarFormDatAlta();
		$("#idLista").val(idLista);
		$("#idCaso").val(idCaso);
		$(".idLista").val(idLista);
		$(".idCaso").val(idCaso);
		$("#camaLiberar").val(cama);
		$("#ficha").val(ficha);
		$("#ubicacionEgreso").val('lista_transito');
		$(".nombreModal").html(nombreCompleto);
		if(sexo == "femenino"){
			$("#divParto").show();
		}else{
			$("#divParto").hide();
		}
		$("#modalAllta").modal("show");
	}

	$(document).on('show.bs.modal', '#modalAllta', function () {
		var fecha = $("#fechaEgreso").data('DateTimePicker');
		fecha.date(moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm'));
		setTimeout(function() {
				$("#formDarAlta").bootstrapValidator("revalidateField", "fechaEgreso");
		}, 200);
	});

	var marcarCamaDisponible=function(event, cama){
		var ubicacion = "lista_transito";
		swalCargando.fire({});
        event.preventDefault();
		$.ajax({
			url: "cambiarCama",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {cama: cama, idCaso: idCaso, idLista: idLista, ubicacion: ubicacion},
			type: "post",
			success: function(data){
				swalCargando.close();
				Swal.hideLoading();
				if (data.error) {
					swalError.fire({
						title: 'Error',
						text: data.error,
					});
					cargarListaEsperaHosp($("#procedencia").val());
					$("#modalIngresar").modal("hide");
				}else{
					swalExito.fire({
						title: 'Exito!',
						text: "Se le ha asignado la cama al paciente",
						didOpen: function() {
							setTimeout(function() {
								cargarListaEsperaHosp($("#procedencia").val());
								$("#modalIngresar").modal("hide");
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

	var tipoTransito=function(idLista){
		$(".idLista").val(idLista);
		//$(".nombreModal").html(nombreCompleto);
		$("#modalTipoTransito").modal("show");
		$.ajax({
			url: "getTipoTransito",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: {idLista:idLista},
			success: function(data){

				$("#select_tipo_transito").val(data.tipo_transito);
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	function cargarListaEsperaHosp(procedencia){
		procedencia = (procedencia != '') ? procedencia : '';
		swalCargando.fire({title:'Cargando Lista de Espera de Hospitalización'});

		table = $('#listaEspera').dataTable({
				"destroy": true,
				"iDisplayLength": 15,
				"order": [[ 4, "asc" ]],
				"columnDefs": [
       			{ type: 'date-euro', targets: 4 }
     			],
				"bJQueryUI": true,
				// "scrollY": "48vh",
				// "scrollX": true,
				// "scrollCollapse": true,
				"oLanguage": {
					"sUrl": "{{URL::to('/')}}/js/spanish.txt"
				},
				"ajax": {
					"url": "obtenerListaTransito",
					"dataType": "json",
					"type": "get",
					"data":{"procedencia": procedencia}
				},
				"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
					if ( $('td', nRow).find('label')[0].outerText >= 12 ){
						//$('td', nRow).css('background-color', '#ffbcaf');
						//$('td', nRow).css('color', 'rgb(88,86,86)');
						$('td', nRow).css('color', '#d14d33');
						$('td', nRow).css('font-weight', 'bold');
					}else if ( $('td', nRow).find('label')[0].outerText >= 6 && $('td', nRow).find('label')[0].outerText < 12 ){
						//$('td', nRow).css('background-color', 'rgb(255,2366,161)');
						//$('td', nRow).css('color', 'rgb(88,86,86)');
						$('td', nRow).css('color', 'rgb(186,186,57)');
						$('td', nRow).css('font-weight', 'bold');
					}else if($('td', nRow).find('label')[0].outerText >= 1 && $('td', nRow).find('label')[0].outerText < 6){
						//$('td', nRow).css('background-color', '#dcedc8');
						//$('td', nRow).css('color', 'rgb(88,86,86)');
						$('td', nRow).css('color', '#41a643');
						$('td', nRow).css('font-weight', 'bold');
					}else{
						$('td', nRow).css('color', 'rgb(88,86,86)');
						$('td', nRow).css('font-weight', 'bold');
					}
				}
		});
		
		setTimeout(function() {
			swalCargando.close();
			Swal.hideLoading();
		}, 2000);
	}

	$(function(){

		cargarListaEsperaHosp($("#procedencia").val());

		$("#urgenciaMenu").collapse();

		$("#fecha-indicacion").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});

		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm"
		}).on('dp.change', function (e) {
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
		});

		$("#fechaEgreso").on('keyup', function(){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');	
		});

		$("#procedencia").on("change", function(){
			cargarListaEsperaHosp($("#procedencia").val());
		});

		$("#visitasAcostar").on("click", function(){
			$("#modalVisitas").find("input[name=recibe_visitas][value='no']").change();
			$("#modalVisitas").find("#cantidad_personas").change();
			$("#modalVisitas").find("#cantidad_horas").change();
			$("#modalVisitas").find("#comentario_visitas").change();
		});

		$("#modalVisitas").on("hidden.bs.modal",function(){
			$('#formVisitas').bootstrapValidator('resetForm', true);
			$(".div-recibe-visitas").hide();
			$("#input_comentario_visitas").hide();
			var checkeadorv = $("#modalVisitas").find("input[name='recibe_visitas']:checked").val();
			if(checkeadorv === undefined){
				$("#modalVisitas").find("input[name=recibe_visitas][value='no']").prop("checked",false);
				$("#modalVisitas").find("input[name=recibe_visitas][value='no']").prop("checked",false);
			}
		});

		$("input[name='recibe_visitas']").on("change", function(){
		var value = $("input:radio[name=recibe_visitas]:checked").val();
		if(value == "si"){
			$(".div-recibe-visitas").show();
			$("#input_comentario_visitas").hide();
		}else if(value == "no"){
			$(".div-recibe-visitas").hide();
			$("#input_comentario_visitas").show();
		}else{
			$(".div-recibe-visitas").hide();
			$("#input_comentario_visitas").hide();
		}
	});

		$("#btnExcelListaEspera").click(function(){
			var procedencia = ($("#procedencia").val() == '') ? 'x' : $("#procedencia").val();
			window.location.href = "{{url('urgencia/excelListaEsperaHosp')}}"+"/"+procedencia;
		});

		$( "#refresh_lista_espera" ).on( "click", function() {
			swalPregunta.fire({
				title: "¿Esta seguro de recargar la información?"
				}).then(function(result){
				if (result.isConfirmed) {
					cargarListaEsperaHosp($("#procedencia").val());
				}else if(result.isDenied){
					setTimeout(refresh, 1000);
				}
			});
		}); 

    	// RECARGAR
      	var time = new Date().getTime();

		$(document.body).bind("mousemove keypress", function(e) {
			time = new Date().getTime();
		});
		
		function refresh() {
          if(new Date().getTime() - time >= 900000){//900000 => 15 minutos
			swalPregunta.fire({
					title: "Han pasado 15 minutos de inactividad. <br>¿Desea recargar la información?"
				}).then(function(result){
					if (result.isConfirmed) {
						cargarListaEsperaHosp($("#procedencia").val());
					}else if(result.isDenied){
						setTimeout(refresh, 1000);
					}
				}); 
			}else{
				setTimeout(refresh, 1000);
			}
		}

      setTimeout(refresh, 1000);
      //RECARGAR

		$("#formVisitas").bootstrapValidator({
			excluded: [':disabled',':hidden'],
			fields: {
				recibe_visitas: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'Campo obligatorio'
						}
					}
				},
				cantidad_personas: {
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'Campo obligatorio'
						}
					}
				},
				cantidad_horas:{
					trigger: 'change keyup',
					validators:{
						notEmpty: {
							message: 'Campo obligatorio'
						}
					}
				},
				comentario_visitas: {
					trigger: 'change keyup',
					validators: {
						notEmpty: {
							message: 'Debe ingresar el comentario.'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt, data){
			var cama = $("#idCama").val();
			var idCaso = $("#idCasoVisitas").val();
			var idLista = $("#idLista").val();
			var ubicacion = 'lista_transito';
			var recibe_visitas = $("input[name='recibe_visitas']:checked").val();
			var cantidad_personas = $("#cantidad_personas").val();
			var cantidad_horas = $("#cantidad_horas").val();
			var comentario_visitas = $("#comentario_visitas").val();

			$.ajax({
				url: "ingresarACamaReal",
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				data: {cama: cama, 
					idCaso: idCaso, 
					idLista: idLista, 
					ubicacion: ubicacion,
					recibe_visitas: recibe_visitas,
					cantidad_personas: cantidad_personas,
					cantidad_horas: cantidad_horas,
					comentario_visitas: comentario_visitas
				},
				type: "post",
				success: function(data){
					console.log("paso2");
					swalCargando.close();
					Swal.hideLoading();
					if (data.error) {
						swalError.fire({
							title: 'Error',
							text: data.error
						}).then(function(result) {
							cargarListaEsperaHosp($("#procedencia").val());
						});
						
					}else{
						swalExito.fire({
							title: 'Exito!',
							text: data.exito,
							didOpen: function() {
								setTimeout(function() {
									cargarListaEsperaHosp($("#procedencia").val());
								}, 2000)
							},
						});
					}
					$("#modalVisitas").modal('hide');
				},
				error: function(error){
					console.log("paso3");
					swalCargando.close();
					Swal.hideLoading();
					console.log(error);
				}
			});
		});

		$("#formDarAlta").bootstrapValidator({
			excluded: [':disabled',':hidden'],
			fields: {
				fechaEgreso: {
					validators:{
						notEmpty: {
							message: 'La Fecha debe ser obligatoria'
						},
						remote: {
							data: function(validator){
								return {
									casoLiberar: validator.getFieldElements('idCaso').val(),
									cama: validator.getFieldElements('cama').val(),
									fechaEgreso: validator.getFieldElements('fechaEgreso').val()
								};
							},
							url: "{{ URL::to("/validarFechaEgresoThistorial") }}"
						}
					}
				},
				"medicoAlta": {
					validators:{
						trigger: 'change keyup',
						notEmpty: {
							message: 'El nombre del médico es obligatorio'
						}
					}
				},
				ficha: {
					validators:{
						notEmpty: {
							message: 'El número de ficha es obligatorio'
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
				}
			}
		}).on('status.field.bv', function(e, data) {
			$("#formDarAlta input[type='submit']").prop("disabled", false);
		}).on("success.form.bv", function(evt){
			$("#formDarAlta input[type='submit']").prop("disabled", false);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			bootbox.confirm({
				message: "¿Está seguro de querer egresar al paciente?",
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
							url: "darAltaTransito",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type: "post",
							dataType: "json",
							data: $form .serialize(),
							success: function(data){
								console.log("success");
								console.log(data);
								$("#modalAllta").modal("hide");
								if(data.exito) {
									swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									didOpen: function() {
										setTimeout(function() {
											cargarListaEsperaHosp($("#procedencia").val());
											$("#modalAllta").on('hidden.bs.modal', function () {
												limpiarFormDatAlta();
											}); 
										}, 2000)
									},
									});
									
								}
								
								if(data.error){
									console.log("entro al error");
									swalError.fire({
										title: 'Error',
										text: data.error
									}).then(function(result) {
										$("#modalAllta").on('hidden.bs.modal', function () {
											limpiarFormDatAlta();
										});
										cargarListaEsperaHosp($("#procedencia").val());
									});
								} 
							},
							error: function(error){
								console.log("error");
								console.log(error);
								swalError.fire({
									title: 'Error',
									text:data.error
								});
								$("#modalAllta").on('hidden.bs.modal', function () {
									limpiarFormDatAlta();
								});
								cargarListaEsperaHosp($("#procedencia").val());
							}
						});
					}
				}
			});
		});

		$("#modalAllta").on('hidden.bs.modal', function () {
            limpiarFormDatAlta();
        });

		$("#solicitar").on("click", function(){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
			$('#formDarAlta').bootstrapValidator('revalidateField', 'ficha');
			$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
        });

		$("#formTipoTransito").bootstrapValidator({
			excluded: ':disabled'

		}).on('status.field.bv', function(e, data) {
			$("#formTipoTransito button").prop("disabled",false)
		}).on("success.form.bv", function(evt){
			$("#formTipoTransito button").prop("disabled",false)
			evt.preventDefault(evt);
			var $form = $(evt.target);
			bootbox.confirm({
				message: "¿Está seguro?",
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
							url: "cambiarTipoTransito",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type: "post",
							dataType: "json",
							data: $form .serialize(),
							success: function(data){
								$("#modalTipoTransito").modal("hide");
								if(data.exito) swalExito.fire({
												title: 'Exito!',
												text: data.exito,
												});
								if(data.error) swalError.fire({
												title: 'Error',
												text:data.error
												});
							},
							error: function(error){
								console.log(error);
							}
						});
					}
				}
			});


		});



		$("#formtrasladoUnidadHosp").bootstrapValidator({
			excluded: ':disabled'

		}).on('status.field.bv', function(e, data) {
			$("#formtrasladoUnidadHosp button").prop("disabled",false)
		}).on("success.form.bv", function(evt){
			$("#formtrasladoUnidadHosp button").prop("disabled",false)
			evt.preventDefault(evt);
			var $form = $(evt.target);

			$.ajax({
				url: "cambiarFechaTrasladoUnidadHosp",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "post",
				dataType: "json",
				data: $form .serialize(),
				success: function(data){
					$("#modaltrasladoUnidadHosp").modal("hide");
					$("#fecha-indicacion").val("");
					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								cargarListaEsperaHosp($("#procedencia").val());
							}, 2000)
						},
						});
					
					} 
					if(data.error) swalError.fire({
									title: 'Error',
									text:data.error
									});
				},
				error: function(error){
					console.log(error);
				}
			});

		});


	});

	$("#fechaFallecimiento").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});

	@include('General.jsEstablecimientos');

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
		//$("[name='id_medico']").val(selection.id_medico);
		$("[name='id_procedencia']").val(selection.id_establecimiento);
	}).on('typeahead:close', function(ev, suggestion) {
	  var $estable=$(this).parents(".estab").find("input[name='id_procedencia']");
	  if(!$estable.val()&&$(this).val()){
		  $(this).val("");
		  $estable.val("");
		  $(this).trigger('input');
	  }
	});

	var datos_extrasistema = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('extra'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '{{URL::to('/')}}/'+'%QUERY/consulta_extrasistema',
			wildcard: '%QUERY',
			filter: function(response) {
			    return response;
			}
		},
		limit: 50
	});

	datos_extrasistema.initialize();

	$('.extra .typeahead').typeahead(null, {
	  name: 'best-pictures',
	  display: 'nombre_establecimiento',
	  source: datos_extrasistema.ttAdapter(),
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
		//$("[name='id_medico']").val(selection.id_medico);
		$("[name='id_procedenciaExtra']").val(selection.id_establecimiento);
	}).on('typeahead:close', function(ev, suggestion) {
	  var $extra=$(this).parents(".extra").find("input[name='id_procedenciaExtra']");
	  if(!$extra.val()&&$(this).val()){
		  $(this).val("");
		  $extra.val("");
		  $(this).trigger('input');
	  }
	});

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
		limit: 1000
	});

	datos_medicos.initialize();

	$('.medicos .typeahead').typeahead(null, {
	  name: 'best-pictures',
	  display: 'nombre_apellido',
	  source: datos_medicos.ttAdapter(),
	  limit: 1000,
	  templates: {
		empty: [
		  '<div class="empty-message">',
			'No hay resultados',
		  '</div>'
		].join('\n'),
		suggestion: function(data){
			//console.log(data.nombre_apellido);
			var nombres = data;
			//console.log(data);
			return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
		},
		header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
	  }
	}).on('typeahead:selected', function(event, selection){
		//console.log(selection);
		$("#medico").val('asdas');
		//console.log("id_m", selection.id_medico);
		$("[name='id_medico']").val(selection.id_medico);

		//$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
	}).on('typeahead:close', function(ev, suggestion) {//Mauricio
		var $med=$(this).parents(".medicos").find("input[name='id_medico']");
	  if(!$med.val()&&$(this).val()){
		  $(this).val("");
		  $med.val("");
		  $(this).trigger('input');
	  }
	  	//$("#medico").val("selection.nombre_medico");
	  	/* var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		console.log("padre:",$(this).parents(".diagnostico_cie101"));
		console.log("cie10:",$cie10.val(),!$cie10.val());
		console.log("this:",$(this).val(),!!$(this).val());
		if(!$cie10.val()&&$(this).val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
		} */

		// if ($("[name='id_medico']").val() == '' || $("#medicoAlta").val() == null) {
		// 	$("[name='id_medico']").val("");
		// 	$("#medicoAlta").val("");
		// }


	});//Mauricio//
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Espera de hospitalización</a></li>
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
/* .btn{
	margin-bottom: 10px;
} */
</style>

<fieldset>
	<legend style="margin-bottom: 30px;">Espera de hospitalización</legend>
	<br>

	<div class="row">
		<div class="col-md-3">
			<label for="inicio">Origen de la solicitud</label>
			{{Form::select('procedencia', $procedencias, '', array('id' => 'procedencia', 'class' => 'form-control', 'placeholder' => 'Todos'))}}
		</div>

		<div class="col-md-4 col-md-offset-1">
			<br>
			<button class="btn btn-success" id="btnExcelListaEspera"> Reporte Excel</button>
		</div>

		<div class="col-md-4" style="text-align: right;">
			<br>
			<a id="refresh_lista_espera" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> Recargar Datos</a>
		</div>
	</div>

	<div class="table-responsive">
		<div class="dataTables_wrapper no-foot heightgrilla_transito">
			<br><br>
			<table id="listaEspera" class="table table-condensed table-hover">
				<thead>
					<tr>
					<th>Opciones</th>
						<th>Run</th>
						<th>Nombre completo</th>
						<th>Diagnóstico</th>
						<th>Fecha de asignación</th>
						<th width="120">Fecha de indicación médica</th>
						<th>Servicio</th>
						<th>Cama</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</fieldset>



<div id="modalAllta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
				<div class="nombreModal"></div>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formDarAlta')) }}
			{{ Form::hidden('idLista', '', array('class'=>"idLista")) }}
			{{ Form::hidden('idCaso', '', array('id' => 'idCaso', 'class'=>"idCaso")) }}
			{{ Form::hidden('cama', '', array('id' => 'camaLiberar')) }}
			{{ Form::hidden('ubicacion','',array('id' => 'ubicacionEgreso'))}}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea egresar al paciente ?</h4>
					</div>
					{{-- DESTINO ALTA HOSPITALIZACION O TRANSITO --}}
					<div class="form-group col-md-12">
						<label for="fechaEgreso" class="col-sm-2 control-label">Fecha de egreso: </label>
						<div class="col-sm-10">
							{{Form::text('fechaEgreso', null, array('id' => 'fechaEgreso', 'class' => 'form-control fecha-sel'))}}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Destino de alta: </label>
						<div class="col-sm-10">
							{{ Form::select('motivo', $motivo, null, array('class' => 'form-control', 'id' => "select-motivo-liberacion" )) }}
						</div>
					</div>
					<div class="form-group col-md-12" id="motivo-liberacion"></div>

					<div class="form-group hidden col-md-12" id="fallecimientofecha">
						<label for="fallec" class="col-sm-2 control-label">Fecha: </label>
						<div class="col-sm-10 ">
							{{Form::text('fechaFallecimiento', null, array('id' => 'fechaFallecimiento', 'class' => 'form-control'))}}
						</div>
					</div>

					<div class="form-group col-md-12 altaOculto" style="display: none">
						<label for="otroMotivoBloqueo" class="col-sm-2 control-label">Especifique: </label>
						<div class="col-sm-10">
							{{Form::textarea('input-alta', null, array('id' => 'input-alta', 'class' => 'form-control', 'rows' => '2', 'required' => 'required'))}}
						</div>
					</div>

					<div class="form-group col-md-12 estabOculto" style="display: none">
						<label for="Establecimiento" class="col-sm-2 control-label">Especifique: </label>
						<div class="col-sm-10 estab">
							{{Form::text('inputProcedencia', null, array('id' => 'inputProcedencia', 'class' => 'form-control typeahead', 'required' => 'required'))}}
							{{Form::hidden('id_procedencia', null, array('id' => 'id_procedencia'))}}
						</div>
					</div>

					<div class="form-group col-md-12 extraOculto" style="display: none">
						<label for="Establecimiento" class="col-sm-2 control-label">Especifique: </label>
						<div class="col-sm-10 extra">
							{{Form::text('inputProcedenciaExtra', null, array('id' => 'inputProcedenciaExtra', 'class' => 'form-control typeahead', 'required' => 'required'))}}
							{{Form::hidden('id_procedenciaExtra', null, array('id' => 'id_procedenciaExtra'))}}
						</div>
					</div>

					{{-- DETALLE EGRESO --}}
					{{-- <div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Detalle: </label>
						<div class="col-sm-10">
							<textarea name="detalle" class="form-control"></textarea>
						</div>
					</div> --}}
					<div class="form-group col-md-12 medicoOculto">
						<label for="medicoAlta" class="col-sm-2 control-label">Medico alta: </label>
						<div class="col-sm-10 medicos">
							{{-- {{Form::select('medicoAlta', $medicos,null, array('id' => 'medicoAlta', 'class' => 'form-control'))}} --}}
							{{Form::text('medicoAlta', null, array('id' => 'medicoAlta', 'class' => 'form-control typeahead'))}}
							{{Form::hidden('id_medico', null, array('id' => 'id_medico'))}}
						</div>
					</div>
					<div class="form-group col-md-12">
						<label for="ficha" class="col-sm-2 control-label">N° de ficha: </label>
						<div class="col-sm-10">
							{{Form::text('ficha', null, array('id' => 'ficha', 'class' => 'form-control'))}}
						</div>
					</div>

					@include('Egreso.partialParto')

				</div>
			</div>
			<div class="modal-footer">
				<button id="solicitar" type="submit" class="btn btn-primary">Liberar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>


<div id="modalTipoTransito" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
				<div class="nombreModal"></div>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formTipoTransito')) }}
			{{ Form::hidden('idLista', '', array('class'=>"idLista")) }}
			{{ Form::hidden('idCaso', '', array('class'=>"idCaso")) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea editar el tipo de tránsito ?</h4>
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Seleccione tipo de tránsito: </label>
						<div class="col-sm-10">
							{{ Form::select('tipo_transito', $tipos_transito, null, array('class' => 'form-control',"id"=>"select_tipo_transito")) }}
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" data-disable="false">Si</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
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
			<div id="mensajeError" class="alert alert-danger" role="alert" hidden>
				<h4>La cama no puede ser seleccionada</h4>
			</div>
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

<div id="modaltrasladoUnidadHosp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Salida de urgencia</h4>
			</div>

			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formtrasladoUnidadHosp')) }}
			{{ Form::hidden('idLista', '', array('id' => 'idLista', 'class'=>"idLista")) }}
			{{ Form::hidden('idCaso', '', array('id' => 'idCaso', 'class'=>"idCaso")) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>Indique la fecha</h4>
					</div>

					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Fecha y hora: </label>
						<div class="col-sm-10">
						{{Form::text('fecha-indicacion', null, array('id' => 'fecha-indicacion', 'class' => 'form-control'))}}
						</div>
					</div>

					<div class="form-group col-md-12">
						<h4>Indique el motivo</h4>
					</div>

					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Seleccione: </label>
						<div class="col-sm-10">
							{{ Form::select('tipo_transito', $tipos_transito, null, array('class' => 'form-control select_tipo_transito')) }}
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" data-disable="false">Si</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}

		</div>
	</div>
</div>

<div id="modalVisitas" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Hospitalizar Paciente</h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formVisitas', 'autocomplete' => 'off')) }}
				{{ Form::hidden('idCasoVisitas', '', array('id' => 'idCasoVisitas')) }}
				{{ Form::hidden('idCama', '', array('id' => 'idCama')) }}
				{{ Form::hidden('idLista', '', array('id' => 'idLista')) }}
					@include('Visitas.modalVisitas')
			</div>
			<div class="modal-footer">
				<button id="visitasAcostar" type="submit" class="btn btn-primary">Hospitalizar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
				{{Form::close()}}
		</div>
	</div>
</div>
@stop
