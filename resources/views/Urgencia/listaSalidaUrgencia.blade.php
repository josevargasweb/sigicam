@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")

<script>
	var table=null;
	var idCaso=null;
	var idLista=null;

	function editarPaciente(idPaciente,idCaso){
		var ubicacion = "salida_urgencia";
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
					table.api().ajax.reload();
				}
 			},
 			error: function(error){
				console.log(error);
 			}
 		});
	}

	function regresarListaEspera(idCaso){
		var ubicacion = "salida_urgencia";
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
					table.api().ajax.reload();
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
	$("#modaltrasladoUnidadHosp").modal("show");
	$(".idLista").val(idLista);

	$.ajax({
		url: "getFechaTrasladoUnidadHosp",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		type: "post",
		dataType: "json",
		data: {idLista:idLista},
		success: function(data){

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
		console.log("llego?");
		setTimeout(function() {
			$("#modalIngresar").modal("show");
		},2000);
		$("#mensajeError").hide();

	}

	var getUnidades=function(){
		swalCargando.fire({});
		var unidades=[];
		console.log("getio?");
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
					//$("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+"</a></li>");
					$("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'></div>");
					generarMapaCamasDisponibles(id, data[i].url, true);
				}
				console.log("genero?");
				console.log("data: "+data[1].alias);
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
				setTimeout(function() {
				swalCargando.close();
				Swal.hideLoading();
				},2000)
			},
			error: function(error){
				setTimeout(function() {
				swalCargando.close();
				Swal.hideLoading();
				},2000)
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
		idCaso=caso;
		idLista=lista;
		idCama = cama;
		var ubicacion = 'salida_urgencia';
		var dialog = bootbox.dialog({
            //title: 'Se ha realizado el traslado interno',
            message: "<h4>¿Desea hospitalizar al paciente?</h4>",
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
							url: "ingresarACamaReal",
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
										text:data.error
									});
									table.api().ajax.reload();
								}else{
									swalExito.fire({
										title: 'Exito!',
										text: data.exito,
										didOpen: function() {
											setTimeout(function() {
												table.api().ajax.reload();
											}, 2000)
										},
									});
								}
							},
							error: function(error){
								swalCargando.close();
								Swal.hideLoading();
								console.log(error);
							}
						});
                    }
                }
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

	var darAltaTransito=function(idCaso, idLista, ficha, nombreCompleto, cama, sexo){
		limpiarFormDatAlta();
		$("#idLista").val(idLista);
		$("#idCaso").val(idCaso);
		$(".idLista").val(idLista);
		$(".idCaso").val(idCaso);
		$("#ficha").val(ficha);
		$("#ubicacionEgreso").val('salida_urgencia');
		$(".nombreModal").html(nombreCompleto);
		if(sexo == "femenino"){
			$("#divParto").show();
		}else{
			$("#divParto").hide();
		}
		$("#camaLiberar").val(cama);
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
		var ubicacion = "salida_urgencia";
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
					table.api().ajax.reload();
					$("#modalIngresar").modal("hide");
				}else{
					swalExito.fire({
						title: 'Exito!',
						text: "Se le ha asignado la cama al paciente",
						didOpen: function() {
							setTimeout(function() {
								table.api().ajax.reload();
								$("#modalIngresar").modal("hide");
							}, 2000)
						},
					});
				}
			},
			error: function(error){
				console.log(error);
				swalCargando.close();
				Swal.hideLoading();
			}
		});
	}

	var tipoTransito=function(idLista){
		console.log(idLista);
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



	$(function(){
		/*@include('General.jsGeneral')*/
		$("#urgenciaMenu").collapse();

		$("#fecha-indicacion").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});

		$("#fechaFallecimiento").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});

		@include('General.jsEstablecimientos');

		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm"
		}).on('dp.change', function (e) {
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
		});

		$("#fechaEgreso").on('keyup', function(){
			$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');	
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
			//console.log(data.nombre_apellido);
			var nombres = data;
			//console.log(data);
			return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
		},
		header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
	  }
	}).on('typeahead:selected', function(event, selection){
		console.log(event);
		$("#medico").val('asdas');
		$("[name='id_medico']").val(selection.id_medico);
		//$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
	}).on('typeahead:close', function(ev, suggestion) {
	  console.log('Close typeahead: ' + suggestion);
	  var $med=$(this).parents(".medicos").find("input[name='id_medico']");
	  if(!$med.val()&&$(this).val()){
		  $(this).val("");
		  $med.val("");
		  $(this).trigger('input');
		  console.log("limpiado?");
	  }
	});

var datos_establecimientos = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('estab'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '{{URL::to('/')}}/'+'%QUERY/consulta_establecimientos',
			wildcard: '%QUERY',
			filter: function(response) {
				console.log(response);
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
		console.log(event);
		//$("[name='id_medico']").val(selection.id_medico);
		$("[name='id_procedencia']").val(selection.id_establecimiento);
	}).on('typeahead:close', function(ev, suggestion) {
	  console.log('Close typeahead: ' + suggestion);
	  var $estable=$(this).parents(".estab").find("input[name='id_procedencia']");
	  if(!$estable.val()&&$(this).val()){
		  $(this).val("");
		  $estable.val("");
		  $(this).trigger('input');
		  console.log("limpiado?");
	  }
	});

	var datos_extrasistema = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('extra'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '{{URL::to('/')}}/'+'%QUERY/consulta_extrasistema',
			wildcard: '%QUERY',
			filter: function(response) {
				console.log(response);
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
			console.log(data);
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
	  console.log('Close typeahead: ' + suggestion);
	  var $extra=$(this).parents(".extra").find("input[name='id_procedenciaExtra']");
	  if(!$extra.val()&&$(this).val()){
		  $(this).val("");
		  $extra.val("");
		  $(this).trigger('input');
		  console.log("limpiado?");
	  }
	});

	$("#btn_egresar").on('click', function (){
		$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'ficha');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
	});

		table=$('#listaEspera').dataTable({
			dom: 'Bfrtip',
			buttons: [
        		{
					extend: 'excelHtml5',
					messageTop: 'En tránsito a piso',
					text: 'Exportar',
					exportOptions: {
						columns: [1,2,3,4,5,6]
					},
					className: 'btn btn-default',
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						var clRow = $('row', sheet);
						//$('row c', sheet).attr( 's', '25' );  //bordes
						$('row:first c', sheet).attr( 's', '67' ); //color verde, letra blanca, centrado
						$('row', sheet).attr('ht',15);
						$('row:first', sheet).attr( 'ht', 50 ); //ancho columna
						$('row:eq(1) c', sheet).attr('s','67'); //color verde, letra blanca, centrado
					}
				}
    		],


			"bJQueryUI": true,
			"iDisplayLength": 15,
			"order": [[ 4, "asc" ]],
			"columnDefs": [
       			{ type: 'date-euro', targets: 4 }
     		],
			"ajax": "obtenerSalidaUrgencia",
			"language": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
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
				ficha: {
					validators:{
						notEmpty: {
							message: 'El número de ficha es obligatorio'
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
					console.log('This was logged in the callback: ' + result);
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
								$("#modalAllta").modal("hide");
								if(data.exito){
									swalExito.fire({
									title: 'Exito!',
									text: data.exito,
									didOpen: function() {
										setTimeout(function() {
											table.api().ajax.reload();
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
									$("#modalAllta").on('hidden.bs.modal', function () {
										limpiarFormDatAlta();
									});
									table.api().ajax.reload();
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
					console.log('This was logged in the callback: ' + result);
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
								if(data.exito)swalExito.fire({
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
									table . api() . ajax . reload();
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
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">En tránsito a piso</a></li>
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
	<legend>En tránsito a piso</legend>
	<div class="table-responsive">
	<div class="dataTables_wrapper no-foot heightgrilla">
	<table id="listaEspera" class="table  table-condensed table-hover">
		<thead>
			<tr>
			<th>Opciones</th>
				<th>Run</th>
				<th>Nombre completo</th>
				<!-- <th>Apellido</th> -->
				<th>Diagnóstico</th>
				<th>Fecha de asignación</th>
				<th width="120">Fecha de indicación médica</th>

				<th>Servicio</th>
				<th>Cama</th>
				<th>Salida de urgencia</th>


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
			{{ Form::hidden('idCaso', '', array('id' => 'idCaso','class'=>"idCaso")) }}
			{{ Form::hidden('cama', '', array('id' => 'camaLiberar')) }}
			{{ Form::hidden('ubicacion','',array('id' => 'ubicacionEgreso'))}}
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

					<div class="form-group col-md-12 medicoOculto">
						<label for="medicoAlta" class="col-sm-2 control-label">Medico alta: </label>
						<div class="col-sm-10 medicos">
							{{-- {{Form::select('medicoAlta', $medicos,null, array('id' => 'medicoAlta', 'class' => 'form-control'))}} --}}
							{{Form::text('medicoAlta', null, array('id' => 'medicoAlta', 'class' => 'form-control typeahead'))}}
							{{Form::hidden('id_medico', null, array('id' => 'id_medico'))}}
						</div>
					</div>


					{{-- <div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Detalle: </label>
						<div class="col-sm-10">
							<textarea name="detalle" class="form-control"></textarea>
						</div>
					</div> --}}
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
				<button id="btn_egresar" type="submit" class="btn btn-primary">Liberar</button>
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
				<h4 class="modal-title">En tránsito a piso</h4>
			</div>

			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formtrasladoUnidadHosp')) }}
			{{ Form::hidden('idLista', '', array('id' => 'idLista', 'class'=>"idLista")) }}
			{{ Form::hidden('idCaso', '', array('id' => 'idCaso', 'class'=>"idCaso")) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea editar la fecha?</h4>
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Fecha y hora: </label>
						<div class="col-sm-10">
						{{Form::text('fecha-indicacion', null, array('id' => 'fecha-indicacion', 'class' => 'form-control'))}}
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


@stop
