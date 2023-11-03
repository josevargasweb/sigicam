@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

<script>	
	//Modificar tamaño tabla
	$("#main_").removeClass("col-md-7");
	$("#main_").addClass("col-md-9");
	
	$(".fechaPC").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});
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
						cargarlistaespera($("#procedencia").val());
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


	var table=null;
	var idCaso=null;
	var idLista=null;

	var cambiarUnidad = function(idCaso){
		$(".idCaso").val(idCaso);
		$("#modalVerUnidad").modal();
	}

	var ingresar=function(caso, lista, nombreCompleto){
		idCaso=caso;
		idLista=lista;
		$(".nombreModal").html(nombreCompleto);
		getUnidades();
		setTimeout(function() {
			$("#modalIngresar").modal("show");
		},1700)
		$("#mensajeError").hide();

	}

	function editarPaciente(idPaciente,idCaso){
		var ubicacion = "lista_espera";
		$.ajax({
 			url: "{{URL::to('/')}}/paciente/puedeHacer/"+idCaso+"/"+ubicacion,
 			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 			dataType: "json",
 			type: "get",
 			success: function(response){
				if(response.exito){
					window.location.href = "{{url('paciente/editar/')}}"+"/"+idPaciente;
				}

				if(response.error){
					swalError.fire({
						title: 'Error',
						text:response.error
					});
					cargarlistaespera($("#procedencia").val());
				}
 			},
 			error: function(error){
				console.log(error);
 			}
 		});
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

	var darAlta=function(idCaso, idLista, ficha, nombreCompleto, sexo){
		limpiarFormDatAlta();
		$("#idLista").val(idLista);
		$("#idCaso").val(idCaso);
		$("#ficha").val(ficha);
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

	var agregarComentario=function(idLista, nombreCompleto){
		$("#idListaComentario").val(idLista);
		$(".nombreModal").html(nombreCompleto);
		$("#modalAgregarComentario").modal("show");
	}

	var agregarUbicacion=function(idLista, nombreCompleto, ubicacion){
		$("#idListaUbicacion").val(idLista);
		$(".nombreModal").html(nombreCompleto);
		$("#ubicacion").val(ubicacion);
		$("#modalAgregarUbicacion").modal("show");
	}

	var marcarCamaDisponible=function(event, cama){
        event.preventDefault();
		var dialog = bootbox.dialog({
            //title: 'Se ha realizado el traslado interno',
            message: "<h4>¿Está seguro de solicitar la cama para el paciente?</h4>",
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
							url: "ingresarACama",
							headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
							data: {cama: cama, idCaso: idCaso, idLista: idLista},
							type: "post",
							success: function(data){
								if(data.error){
									swalError.fire({
									title: 'Error',
									text:data.error
									});
									$("#modalIngresar").modal('toggle');
									// table . api() . ajax . reload();
									cargarListaEspera($("#procedencia").val());
								}else if(data.hospitalizado){
										swalError.fire({
											title: 'Error!',
											text:data.hospitalizado
										});
										$("#modalIngresar").modal('toggle');
										// table . api() . ajax . reload();
										cargarListaEspera($("#procedencia").val());
								}else if(data.exito){
									swalExito.fire({
										title: 'Exito',
										text:data.exito,
										didOpen: function() {
											setTimeout(function() {
												// table . api() . ajax . reload();
												cargarListaEspera($("#procedencia").val());
												$("#modalIngresar").modal('toggle');
											}, 2000)
										},
									});
									// swalCargando.fire({title:'Ingresando paciente a Espera de hospitalización'});
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

	var getUnidades=function(){
		var unidades=[];
		//console.log("getio?");
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
				console.log("succesio?");
				console.log(data);
				unidades=data;
				$("#tabUnidad").empty();
				$("#contentUnidad").empty();
				for(var i=0; i<data.length; i++){
					var nombre=data[i].url;
					var id="id-"+nombre;
					console.log(data[i].id_area_funcional);
					console.log(data[i].alias);
					console.log(data[i].tooltip);
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
				//console.log("genero?");
				//console.log("data: "+data[1].alias);
				for(var i=0; i<data.length; i++){
					console.log("#id-"+data[i].alias);
					// if(data[i].alias != "U.T.I."){
					// 			$("#id-"+data[i].alias).removeClass("active");
					// }

				}
				console.log("forio?");
				if(data.length > 0) {
					$("#id-"+data[0].url).addClass("active");
					$("#id-"+data[0].url).tab("show");
				}
			},
			error: function(error){
				console.log(error);
				swalCargando.close();
				Swal.hideLoading();
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
				 swalCargando.close();
				 Swal.hideLoading();
 			},
 			error: function(error){
 				console.log(error);
				 swalCargando.close();
				Swal.hideLoading();
 			}
 		});
	 }


	 var nosepuede=function(){
		//alert("<h4>La cama no puede ser seleccionada</h4>");
		//bootbox.alert("<h4>La cama no puede ser seleccionada</h4>");
		//$("#mensajeError").hide();
		$("#mensajeError").show();
	}




	var verDiagnosticos = function(idCaso, nombreCompleto){
		$("#detalle-diagnostico").val(idCaso);
		$(".nombreModal").html(nombreCompleto);
		$.ajax({
			url: "{{ URL::to('/diagnosticosCaso') }}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {caso: idCaso, ubicacion:'lista_espera'},
			dataType: "json",
			type: "post",
			success: function(data){
				if(data.error){
					swalError.fire({
						title: 'Error',
						text:data.error
					}).then(function(result) {
						cargarListaEspera($("#procedencia").val());
					});
					
				}else{
					$("#modalVerDiagnosticos .modal-body").html(data.contenido);
					$("#modalVerDiagnosticos").modal();
					$(".ubicacion").val('lista_espera');
					$('#modalVerDiagnosticos').on('hidden.bs.modal', function () {
					//cargarListaEspera($("#procedencia").val());
				});
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}
	var modalRiesgoDepen=function(idCaso){
		$.ajax({
			url: "RiesgoDependencia",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: {idCaso: idCaso},
			success: function(data){

				dependencia1 = parseInt(data.riesgo.dependencia1,0);
                dependencia2 = parseInt(data.riesgo.dependencia2,0);
                dependencia3 = parseInt(data.riesgo.dependencia3,0);
                dependencia4 = parseInt(data.riesgo.dependencia4,0);
                dependencia5 = parseInt(data.riesgo.dependencia5,0);
                dependencia6 = parseInt(data.riesgo.dependencia6,0);

                riesgo1 = parseInt(data.riesgo.riesgo1, 0);
                riesgo2 = parseInt(data.riesgo.riesgo2, 0);
                riesgo3 = parseInt(data.riesgo.riesgo3, 0);
                riesgo4 = parseInt(data.riesgo.riesgo4, 0);
                riesgo5 = parseInt(data.riesgo.riesgo5, 0);
                riesgo6 = parseInt(data.riesgo.riesgo6, 0);
                riesgo7 = parseInt(data.riesgo.riesgo7, 0);
                riesgo8 = parseInt(data.riesgo.riesgo8, 0);
                riesgo9 = parseInt(data.riesgo.riesgo9, 0);

				$("#pruebaModal").show();
				$('#pruebaModal').modal({backdrop:'static', keyboard:false});
				$("#nombre_paciente_riesgo").html(data.paciente);
				$("#dependencia1").val(dependencia1);
				$("#dependencia2").val(dependencia2);
				$("#dependencia3").val(dependencia3);
				$("#dependencia4").val(dependencia4);
				$("#dependencia5").val(dependencia5);
				$("#dependencia6").val(dependencia6);
				$("#riesgo1").val(riesgo1);
				$("#riesgo2").val(riesgo2);
				$("#riesgo3").val(riesgo3);
				$("#riesgo4").val(riesgo4);
				$("#riesgo5").val(riesgo5);
				$("#riesgo6").val(riesgo6);
				$("#riesgo7").val(riesgo7);
				$("#riesgo8").val(riesgo8);
				$('.selectpicker').selectpicker('refresh');

			},
			error: function(error){

				console.log(error);
			}
		});


	}

	function cargarListaEspera(procedencia){
		procedencia = (procedencia != '') ? procedencia : '';
		swalCargando.fire({title:'Cargando Lista de Espera'});
		table = $('#listaEspera').dataTable({
				"destroy": true,
				"aaSorting": [[0, "desc"]],
				"iDisplayLength": 15,
				"bJQueryUI": true,
				"scrollY": "48vh",
				"scrollX": true,
				"scrollCollapse": true,
				"oLanguage": {
					"sUrl": "{{URL::to('/')}}/js/spanish.txt"
				},
				"ajax": {
					"url": "obtenerListaEspera",
					"dataType": "json",
					"type": "get",
					"data":{"procedencia": procedencia}
				},
				"sPaginationType": "full_numbers",
				"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
					if ( $('td', nRow).find('label')[1].outerText >= 12 ){
						//$('td', nRow).css('background-color', '#ffbcaf');
						//$('td', nRow).css('color', 'rgb(88,86,86)');
						$('td', nRow).css('color', '#d14d33');
						$('td', nRow).css('font-weight', 'bold');
					}else if ( $('td', nRow).find('label')[1].outerText >= 6 && $('td', nRow).find('label')[1].outerText < 12 ){
						//$('td', nRow).css('background-color', 'rgb(255,2366,161)');
						//$('td', nRow).css('color', 'rgb(88,86,86)');
						$('td', nRow).css('color', 'rgb(186,186,57)');
						$('td', nRow).css('font-weight', 'bold');
					}else if($('td', nRow).find('label')[1].outerText >= 1 && $('td', nRow).find('label')[1].outerText < 6){
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

		cargarListaEspera($("#procedencia").val());

		$("#urgenciaMenu").collapse();
		/*@include('General.jsGeneral')*/

		$('.selectpicker').prop('disabled', true);
		$("#cambiarUnidad").bootstrapValidator({
            excluded: ':disabled',
			fields: {
				confirmar_error:{
					validators:{
						notEmpty : {
							message: 'Debe confirmar el cambio'
						}
					}
				}
			}
        }).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt, data){

			$.ajax({
				url: "cambiarUnidad",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "post",
				dataType: "json",
				data: $("#cambiarUnidad") .serialize(),
				success: function(data){
					$("#modalVerUnidad").modal("hide");

					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								// table . api() . ajax . reload();
								cargarListaEspera($("#procedencia").val());
							}, 2000)
						},
						});
					} 
					if(data.error){
						swalError.fire({
							title: 'Error',
							text:data.error
						});
						cargarlistaespera($("#procedencia").val());
					} 
				},
				error: function(error){
					console.log(error);
				}
			});

    	});

		$("#modalAgregarCOmentario").on('hidden.bs.modal', function () {
			$('#formAgregarCOmentario').bootstrapValidator('resetForm', true);
		});

		$("#modalAgregarUbicacion").on('hidden.bs.modal', function () {
			$('#formAgregarUbicacion').bootstrapValidator('resetForm', true);
		});

		//por alguna razon se saltaba estas validaciones
		$("#btn_egresar").on('click', function (){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
			$('#formDarAlta').bootstrapValidator('revalidateField', 'ficha');
			$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
		});

		$("#procedencia").on("change", function(){
			cargarListaEspera($("#procedencia").val());
		});

		$("#btnExcelListaEspera").click(function(){
			var procedencia = ($("#procedencia").val() == '') ? 'x' : $("#procedencia").val();
			window.location.href = "{{url('urgencia/excelListaEspera')}}"+"/"+procedencia;
		});

		$("#btnPdfListaEspera").click(function(){
			var procedencia = ($("#procedencia").val() == '') ? 'x' : $("#procedencia").val();
			window.location.href = "{{url('urgencia/pdfListaEspera')}}"+"/"+procedencia;
		});

	  	$( "#refresh_lista_espera" ).on( "click", function() {
			swalPregunta.fire({
				title: "¿Esta seguro de recargar la información?"
				}).then(function(result){
				if (result.isConfirmed) {
					cargarListaEspera($("#procedencia").val());
				}else if(result.isDenied){
					setTimeout(refresh, 1000);
				}
			});
		});  


		$("#formDarAlta").bootstrapValidator({
			excluded: [':disabled',':hidden'],
			fields: {
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
						trigger: 'change keyup',
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
				},
				fechaEgreso: {
					validators:{
						notEmpty: {
							message: 'La Fecha debe ser obligatoria'
						},
						remote: {
							data: function(validator){
								return {
									casoLiberar: validator.getFieldElements('idCaso').val(),
									// cama: validator.getFieldElements('cama').val(),
									fechaEgreso: validator.getFieldElements('fechaEgreso').val()
								};
							},
							url: "{{ URL::to("/validarFechaEgresoCaso") }}"
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
				message: "<h4>¿Está seguro de querer egresar al paciente?</h4>",
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
							url: "darAlta",
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
												cargarlistaespera($("#procedencia").val());
												$("#modalAllta").on('hidden.bs.modal', function () {
													limpiarFormDatAlta();
												}); 
											}, 2000)
										},
										});
								}else if(data.error){
									swalError.fire({
									title: 'Error',
									text:data.error
									});
									cargarlistaespera($("#procedencia").val());
									$("#modalAllta").on('hidden.bs.modal', function () {
										limpiarFormDatAlta();
									});
								}
							},
							error: function(error){
								console.log(error);
							}
						});
					}
				}
			});
		});

		$("#modalAllta").on('hidden.bs.modal', function () {
            limpiarFormDatAlta();
        });

		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm"
		}).on('dp.change', function (e) {
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
		});

		$("#fechaEgreso").on('keyup', function(){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');	
		});

		$("#formAgregarComentario").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				comentario: {
					validators:{
						notEmpty: {
							message: 'El comentario es obligatorio'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			$("#formAgregarComentario input[type='submit']").prop("disabled", false);
		}).on("success.form.bv", function(evt){
			$("#formAgregarComentario input[type='submit']").prop("disabled", false);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			bootbox.confirm({
				message: "¿Está seguro de querer agregar el comentario?",
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
					console.log('This was logged in the callback: ' + result);
					if(result){
						$.ajax({
							url: "agregarComentario",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type: "post",
							dataType: "json",
							data: $form .serialize(),
							success: function(data){
								$("#modalAgregarComentario").modal("hide");
								if(data.exito){
									swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									didOpen: function() {
										setTimeout(function() {
										location.reload();
										// table.api().ajax.reload()
										cargarListaEspera($("#procedencia").val());
										}, 2000)
									},
									});
								} 
								if(data.error) {
									swalError.fire({
										title: 'Error',
										text:data.error
									});
									cargarlistaespera($("#procedencia").val());
								}
							},
							error: function(error){
								console.log(error);
							}
						});
					}
				}
			});
		});

		$("#formAgregarUbicacion").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				ubicacion: {
					validators:{
						notEmpty: {
							message: 'La ubicación obligatorio'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			$("#formAgregarComentario input[type='submit']").prop("disabled", false);
		}).on("success.form.bv", function(evt){
			$("#formAgregarComentario input[type='submit']").prop("disabled", false);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			bootbox.confirm({
				message: "¿Está seguro de querer agregar la ubicación?",
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
							url: "agregarUbicacion",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type: "post",
							dataType: "json",
							data: $form .serialize(),
							success: function(data){
								$("#modalAgregarUbicacion").modal("hide");
								if(data.exito){
									swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									didOpen: function() {
										setTimeout(function() {
											cargarlistaespera($("#procedencia").val());
										}, 2000)
									},
									});
								} 
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

	});

	//$("#fechaFallecimiento").data('DateTimePicker');
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
				console.log(data);
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
		console.log(event);
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
				var nombres = data;
				return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
		}
	}).on('typeahead:selected', function(event, selection){
		$("#medico").val('asdas');
		$("[name='id_medico']").val(selection.id_medico);
	}).on('typeahead:close', function(ev, suggestion) {//Mauricio
		var $med=$(this).parents(".medicos").find("input[name='id_medico']");
	  if(!$med.val()&&$(this).val()){
		  $(this).val("");
		  $med.val("");
		  $(this).trigger('input');
	  }
	  
	});//Mauricio//

</script>
<meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
	<li><a href="#">Urgencia</a></li>
	<li><a href="#">Espera de cama</a></li>
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

		#modalAsignacionCama {
			overflow-y:scroll
		}

		#modalVerDetalles {
			overflow-y:scroll
		}

	</style>

	<fieldset>
		<legend>Espera de cama</legend>
		<br>
		<div class="row">
			<div class="col-md-3">
				<label for="inicio">Origen de la solicitud</label>
				{{Form::select('procedencia', $procedencias, '', array('id' => 'procedencia', 'class' => 'form-control', 'placeholder' => 'Todos'))}}
			</div>
			
			<div class="col-md-4 col-md-offset-1">
				<br>
				<button class="btn btn-success" id="btnExcelListaEspera"> Reporte Excel</button>
				<button class="btn btn-danger" id="btnPdfListaEspera"> Reporte Pdf</button>
			</div>
			<div class="col-md-4" style="text-align: right;">
				<br>
				<a id="refresh_lista_espera" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> Recargar Datos</a>
			</div>
		</div>
		<div class="table-responsive">
			<div class="dataTables_wrapper no-foot heightgrilla">
				<br><br>
				<table id="listaEspera" class="tablax table-condensed table-hover">
					<thead>
						<tr>
						<th>Opciones</th>
							<th style="width:100px">Run</th>
							<th>Nombre completo</th>
							<!-- <th>Apellidos</th> -->
							<th>Fecha nacimiento</th>
							<th>Diagnóstico</th>
							<!-- <th>Comentario</th> -->
							<th width="120">Fecha solicitud</th>
							<th width="120">Fecha de indicación médica</th>
							<th>Procedencia</th>
							<th>Motivo de hospitalización</th>
							<th>Área funcional y Servicio a cargo</th>
							{{-- <th>Área funcional a cargo</th> --}}
							<th>Área funcional y Servicio destino</th>
							{{-- <th>Servicio Destino</th> --}}
							<th>Comentario</th>
							<th>N° Dau</th>
							<th>Riesgo - Dependencia</th>
							<!-- <th>Categorización</th> -->
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</fieldset>

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

    <div id="modalAllta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Confirmación</h4>
					<div class="nombreModal"></div>
				</div>
				{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formDarAlta')) }}
				{{ Form::hidden('idLista', '', array('id' => 'idLista')) }}
				{{ Form::hidden('idCaso', '', array('id' => 'idCaso')) }}
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
						{{-- DESTINO ALTA HOSPITALIZACION O TRANSITO --}}
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">Destino de alta: </label>
							<div class="col-sm-10">
								{{ Form::select('motivo', $motivo, null, array('class' => 'form-control', 'id' => "select-motivo-liberacion")) }}
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
								{{Form::text('medicoAlta', null, array('id' => 'medicoAlta', 'class' => 'form-control typeahead'/*, 'required' => 'required'*/))}}
								{{Form::hidden('id_medico', null, array('id' => 'id_medico'))}}
							</div>
						</div>
						{{-- <div class="row">

						</div> --}}

						<div class="form-group col-md-12">
							<label for="ficha" class="col-sm-2 control-label">N° de ficha: </label>
							<div class="col-sm-10">
								{{Form::text('ficha', null, array('id' => 'ficha', 'class' => 'form-control'))}}
							</div>
						</div>

						@include('Egreso.partialParto')
						
					</div>
					<div class="modal-footer">
						<button id="btn_egresar" type="submit" class="btn btn-primary">Egresar</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					</div>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>	

	<div id="modalVerDiagnosticos" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
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

	<div id="modalAgregarComentario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Agregar Comentario</h4>
					<div class="nombreModal"></div>
				</div>
				{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formAgregarComentario')) }}
				{{ Form::hidden('idLista', '', array('id' => 'idListaComentario')) }}
				<div class="modal-body">
					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<h4>Comentario (máximo 100 caracteres)</h4>
						</div>
						<div class="form-group col-md-12">
							{{Form::textarea('comentario', null, array('id' => 'comentario', 'class' => 'form-control', 'rows'=>'5', 'maxlength'=>'100'))}}
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Agregar comentario</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>

	<div id="modalAgregarUbicacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Agregar Ubicación</h4>
					<div class="nombreModal"></div>
				</div>
				{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formAgregarUbicacion')) }}
				{{ Form::hidden('idLista', '', array('id' => 'idListaUbicacion')) }}
				<div class="modal-body">
					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label>Ubicación</label>
							{{ Form::select('ubicacion', $ubicaciones, null, array('class' => 'form-control', 'id'=>'ubicacion')) }}
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Agregar ubicación</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>


	<div id="modalVerUnidad" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Cambiar servicio</h4>
					<div class="nombreModal"></div>
				</div>
				<div class="modal-body">
					{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'cambiarUnidad', 'autocomplete' => 'off')) }}

						{{ Form::hidden('idCaso', '', array('class' => 'idCaso')) }}
						{{ Form::hidden('ubicacion', 'lista_espera', array('class' => '')) }}
						<div class="form-group col-md-12">

									<label for="servicios" class="control-label" title="Unidad">Servicio para la solicitud: </label>

									{{ Form::select('servicios', $servicios, 0 , array('class' => 'form-control', 'id' => 'servicios'),
									$atributos) }}


						</div>

						<div class="form-group col-md-12">

									<label for="servicios" class="control-label" title="Unidad">Confirmo que se fue un error de ingreso: </label>

									{{ Form::checkbox('confirmar_error', '', false) }}



						</div>

										<button id="btnCambiarUnidad" type="" class="btn btn-primary">Aceptar</button>

					{{ Form::close() }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>


	</div>

	<div id="pruebaModal" class="modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document" style="width: 80%;">
			<div class="modal-content" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="modal-title" style="font-size:20px;">Formulario de Riesgo - Dependencia</div>
					<div id="nombre_paciente_riesgo"></div>
				</div>

				<div class="row" style="margin: 0;">
					<div class="col-sm-12 control-label" style="font-size:15px;">CUIDADOS QUE IDENTIFICAN DEPENDENCIA <div>
				</div>

				<div class="modal-body">
					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label class="col-sm-2 control-label">1.- Cuidados en Confort y Bienestar: </label>
							<label class="col-sm-4 control-label">Cambio de Ropa de cama y/o personal, o Cambio de Pañales, o toallas o apositos higenicos</label>
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
				</div>

				<div class="row" style="margin: 0;">
					<div for="horas" class="col-sm-12 control-label" style="font-size: 15px;">CUIDADOS ESPECIFICOS DE ENFERMERIA QUE IDENTIFICAN RIESGO <div>
				</div>

				<div class="modal-body">
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

				<div class="modal-footer" >
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>



@stop
