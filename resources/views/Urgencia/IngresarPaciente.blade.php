@extends("Templates/template")

@section("titulo")
Pacientes
@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>

	var trasladoInterno = function(idCama, caso){
		console.log(idCama);
		console.log(caso);

		$.ajax({
		url: '{{URL::to("ingresarPacienteOptimizacion")}}',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: { "cama": idCama , "caso":caso},
		dataType: "json",
		type: "post",
		success: function(data){
			console.log(data['exito']);
			swalExito.fire({
			title: 'Exito!',
			text: data.exito,
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

	var ocultarMuerte=function(){
		var value=$("input[name='rn']:checked").val();

		if(value == "si")$("#RnMadre").show("slow");
		else $("#RnMadre").hide("slow");
	}
	ocultarMuerte();

	var getPacienteRut=function(rut){
		$("#divLoadBuscarPaciente").show();
		$.ajax({
		url: "{{URL::to('/getPaciente')}}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {rut: rut},
		dataType: "json",
		type: "post",
		success: function(data){
			console.log(data);
			//$("#buscarCamasForm").data('bootstrapValidator').resetForm();
			var fechaHoy = $("#fechaIngreso").val();
			var dvHoy = $("#dv").val();
			$("#buscarCamasForm").find('input:text, select').val('');
			$("#fechaIngreso").val(fechaHoy);
    		//$("#buscarCamasForm").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
			$("#divLoadBuscarPaciente").hide();
			if(rut != ""){
			$("#rut").val(data.rutSin);
			$("#dv").val(dvHoy); 
			$("#fechaNac").datepicker('update', data.fecha);
			$("#nombre").val(data.nombre);
			$("#sexo").val(data.genero);
			$("#apellidoP").val(data.apellidoP);
			$("#apellidoM").val(data.apellidoM);
			}
			$('#buscarCamasForm').bootstrapValidator('revalidateField', 'nombre');
			$('#buscarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
			$('#buscarCamasForm').bootstrapValidator('revalidateField', 'diagnosticos[]');
			$('#buscarCamasForm').bootstrapValidator('revalidateField', 'tipo-procedencia');
			$('#buscarCamasForm').bootstrapValidator('revalidateField', 'numeroCalle');
			
			if(data.en_cama)
			{
					let mensaje = data.detalle_cama[0].nombre_establecimiento + ", unidad " + data.detalle_cama[0].alias + ", sala " + data.detalle_cama[0].nombre + ", cama " + data.detalle_cama[0].id_cama;
						swalInfo2.fire({
					title: 'El paciente ya se encuentra en una cama, su ubicación es:',
					text:mensaje
					}).then(function(result) {
						location.reload();
						$("form").trigger("reset");
					});	
			}
		},
		error: function(error){
			$("#divLoadBuscarPaciente").hide();
			console.log(error);
		}
		});
	}
	/* var count=0; */
	var agregar = function(){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		//var $clone =  $template.clone().removeClass('hide').insertBefore($template);

		//console.log($clone.find("input"));
		//var el = $("<input type='text' name='retrun-order-invoice_no' class='return-order-invoice_no'>").insertAfter($('#fileTemplate'));
		$clone.find("input").eq(2).val("");
		console.log($clone.find("input")[1]);
		
		//$clone.find("input").val("");
		invoice_no_setup_typeahead($clone.find("input")[1],$clone.find("input")[2]);

		/*var $input = $clone.find('input[type="file"]');
		var id="id_"+count;
		$input.prop("id", id);
		$('#'+id).fileinput();
		console.log("#"+id+" .file-input-new .input-group");
		$(".file-input-new .input-group").css({"width": "95%", "margin-left": "10px"});
		count++;
		*/
	}

	var borrar=function(boton){
		$(boton).parent().parent().parent().remove();
	}

	var modalDerivacion=function(cama, sala, unidad, idCama, idCaso){
		console.log(idCaso);
		$("#idCama").val(idCama);
		$("#idCaso").attr("value",idCaso["id"]);
		$("#idCasoRiesgo").attr("value",idCaso["id"]);
		$("#asunto").prop("disabled", false);
		$("#asunto").val("Solicitud de cama: "+cama+" en sala: "+sala+" de la unidad: "+unidad);
		$("#texto").prop("disabled", false);
		$(".file").prop("disabled", false);
		$("#idEstablecimiento").val(idCaso["establecimiento"]);
		$("#modalSolicitar").modal();
	}

	$("#fechaNac").datepicker({
		autoclose: true,
		language: "es",
		format: "dd-mm-yyyy",
		todayHighlight: true,
		endDate: "+0d"
	}).on("changeDate", function(){
		$('#buscarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
	});

	$("#fechaNac").datepicker({
		autoclose: true,
		language: "es",
		format: "dd-mm-yyyy",
		todayHighlight: true,
		endDate: "+0d"
	}).on("changeDate", function(){
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
	});

	$(".fecha-sel").datetimepicker({
		locale: "es",
		format: "DD-MM-YYYY HH:mm:ss"
	});

	$("#tipo-procedencia").on("change", function(){
		var value=$(this).val();
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
				$("#buscarCamasForm").bootstrapValidator('addField', 'input-procedencia');
			},
			error: function(error){
				console.log(error);
			}
		});
	});

	var fecha = $("#fechaIngreso").data("DateTimePicker");
	console.log(fecha);
	window._gc_now = moment();
	fecha.date(window._gc_now);
	fecha.minDate(moment(window._gc_now).subtract(3, "days").startOf('day'));
	fecha.maxDate(moment(window._gc_now));

	function modalRiesgoDependencia(){
		$("#modalFormularioRiesgo").modal();
	}

	$("#optimizacion-table").dataTable({
		"order": [[0,'desc']],
		"language": {
			"lengthMenu":     "Mostrar _MENU_ por página",
			"zeroRecords":    "No se ha encontrado registros",
			"info":           "Mostrando pagina _PAGE_ de _PAGES_",
			"infoEmpty":      "No se ha encontrado información",
			"infoFiltered":   "(filtered from _MAX_ total records)",
			"search":         "Buscar:",
			"paginate": {
				"first":      "Primero",
				"last":       "Ultimo",
				"next":       "Siguiente",
				"previous":   "Anterior"
			},
		}
	});


	$("#comuna").on("change", function(){
		buscarGeo();
	});

	$("#region").on("change", function(){
		//buscarGeo();
		console.log("hola");

		$.ajax({
			url: "{{URL::to('/comunas')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "region": $("#region").val() },
			dataType: "json",
			type: "post",
			success: function(data){
				console.log("data de region",data);
				//la variable que llevaralas opciones y estara en html
				var html = "<select name='comuna' id='comuna' class='form-control'>";

				data.forEach(function(element){
					html +=  "<option value="+element.id_comuna+">"+element.nombre_comuna+"</option>";
				});

				html += "</select>";
				//se anade al select
				$("#comunas").find('#comuna').remove().end().append(html);
				//$("#comunas");
				//$("#comunas").find('option').remove().end().append('<option value="whatever">text</option>');

			},
			error: function(error){
				//$("#divLoadBuscarPaciente").hide();
				console.log(error);
			}
		});
		
	});

	$("#calle, #numeroCalle").on('keyup', function(){
		buscarGeo();
	});
  
	function buscarGeo(){
		var geocoder = new google.maps.Geocoder();
		var address =  $("#calle").val()+" "+$("#numeroCalle").val()+" ,"+$("#comuna option:selected").text()+", Chile";
		console.log(address);

		geocoder.geocode({
			'address': address
		}, function (results, status) {

			var latitud=null;
			var longitud=null;



			console.log("buscando lat y lon");
			if (status == google.maps.GeocoderStatus.OK) {
					latitud = results[0].geometry.location.lat();
					longitud = results[0].geometry.location.lng();
			} else {
					latitud= null;
					longitud=null;
			}

			console.log("buscando...");
			console.log("latitud: ", latitud);
			console.log("longitud: ", longitud);
			// Completar Los Campos de Latitud y Longitud
			// Completar Los Campos de Latitud y Longitud
			$("#latitud").val(latitud);
			$("#longitud").val(longitud);
			//document.getElementById('longitud').value = longitud;
			//google.maps.event.trigger(map, 'resize');
			//map.setCenter(new google.maps.LatLng(latitud,longitud));
			//marker.setPosition(new google.maps.LatLng(latitud,longitud));



		});
	}

	$("#derivarForm").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			asunto: {
				validators:{
					notEmpty: {
						message: 'El Asunto es obligatorio'
					}
				}
			},
			texto: {
				validators:{
					notEmpty: {
						message: 'La descripción de la derivación es necesaria'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt, data){
		//evt.preventDefault();
		//data.bv.disableSubmitButtons(false);
		//console.log(evt);

		/* console.log("buscare la cama");
		console.log($('#derivarForm').serialize());
		console.log($('#riesgoDependenciaForm').serialize()); */
		$.ajax({
			url: "{{URL::to('/registrarTrasladoOptimizacion')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data:  $('#derivarForm').serialize(),
			dataType: "json",
			type: "post",
			success: function(data){
				/* console.log("exito");
				console.log(data); */
				if (data.exito) {
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

				if (data.error) {
					swalError.fire({
					title: 'Error',
					text:data.error
					}).then(function(result) {
					if (result.isDenied) {
							location . reload();
					}
					});
				}
			},
			error: function(error){
				//$("#divLoadBuscarPaciente").hide();
				console.log(error);
			}
		})
		
	});

	var enviado = false;

	
	// }
	$("input[name='rn']").on("change", function(){
		var value=$(this).val();
		if(value == "si")$("#RnMadre").show("slow");
		else $("#RnMadre").hide("slow");
	});

	

	//TODO ESTO ANTES ESTABA ABAJO, SE CAMBIO POR ERROR EN INPUT DIAGNOSTICO
	

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

		$("#riesgo").val(riesgoDependencia);

		$('#modalFormularioRiesgo').modal('hide');

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
			limit: 6
		});

		datos_cie10.initialize();

		console.log('focus acheived');
		
		$(self).typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_cie10',
			source: datos_cie10.ttAdapter(),
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					console.log(data);
					if(data.nombre_categoria == null){
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin Especificar</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}else{
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}					
				},
				//
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
			}
		}).on('typeahead:selected', function(event, selection){
			//$("#texto_cie10").val(selection.nombre_cie10);
			$(self2).val(selection.id_cie10);
		});
	}
	

	
/* COMIENZO DEL EDGAR*/
$( document ).ready(function() {

	//carga de unidades al select
	function cargarUnidades() {
		console.log("unidades");
		$.ajax({
			url: "{{URL::to('/unidades_funcionales')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			dataType: "json",
			type: "post",
			success: function(data){
				//se asigna la variable que determina el grupo al que pertenece
				var grupo = -1;
				//la variable que llevaralas opciones y estara en html
				var html;
				html ="<select name='unidad[]' id='unidadChange' class='form-control selectpicker' multiple data-actions-box ='true'> <option value='0'>UNIDADES DEL HOSPITAL</option>"
				data.forEach(function(element){
					console.log("hola",grupo);
					
					
					//console.log("elementos", element);
					if (grupo == -1) {
						//cuando esta recien ingresando se debe poner esta cabecera
						html += "<optgroup label='"+element.area+"'>";
						grupo = element.id_area;
					}

					if (grupo != element.id_area) {
						//si cambia de grupo se debe crear una nueva cabecerA
						html += "</optgroup>";
						html += "<optgroup label='"+element.area+"'>";
						html +=  "<option value="+element.id_unidad+">"+element.unidad+"</option>";
						grupo = element.id_area;
					}else{
						//sino se anade una nueva opcion
						html += "<option value="+element.id_unidad+">"+element.unidad+"</option>";
					}
				});
				//finaliza el html
				if (grupo != -1) {
					html += "</optgroup></select>";
				}else{
					html += "</select>";
				}
				
				//se anade al select
				console.log("html ", html)
				$("#unidades").html(html);
				$("#unidadChange").selectpicker('refresh');
				
				//$("#algo").append(html);


			},
			error: function(error){
				//$("#divLoadBuscarPaciente").hide();
				console.log(error);
			}
		});
	}
	//se deshabilito la funcion nueva para cargar unidades debido a que no es lo que creian que era, pero queda por si quieren usarla proxima vez
	//cargarUnidades();
	//Mauricio
	$(document).on("input","input[name='diagnosticos[]']",function(){
		var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		
		if($cie10.val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
		}
	});
	
	//Mauricio//
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
		limit: 6
	});

	datos_cie10.initialize();

	$('.diagnostico_cie101 .typeahead').typeahead(null, {
	  name: 'best-pictures',
	  display: 'nombre_cie10',
	  source: datos_cie10.ttAdapter(),
	  templates: {
		empty: [
		  '<div class="empty-message">',
			'No hay resultados',
		  '</div>'
		].join('\n'),
		suggestion: function(data){
			var nombres = data;
			//console.log(data);
			return  "<div class='col-sm-12' ><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
		},
		header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
	  }
	}).on('typeahead:selected', function(event, selection){
			//$("#texto_cie10").val(selection.nombre_cie10);
		$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
	}).on('typeahead:close', function(ev, suggestion) {//Mauricio
	  console.log('Close typeahead: ' + suggestion);
	  	var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		console.log("padre:",$(this).parents(".diagnostico_cie101"));
		console.log("cie10:",$cie10.val(),!$cie10.val());
		console.log("this:",$(this).val(),!!$(this).val());
		if(!$cie10.val()&&$(this).val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
		}
	});//Mauricio//
	// .on('typeahead:render',function(event, selection){
	// 	//console.log("evento2: ",event);
	// 	console.log("selection2: ",$('.diagnostico_cie101 .typeahead'));
	// 	//console.log("selection2to1: ",selection);
	// })
	
	$( "#unidadChange" ).change(function() {
		$('#unidadA').val( $("#unidadChange").val() );
		console.log($("#unidadChange").val());
		/* array = $("#unidadChange").val();
		var suculento = [];

		array.forEach(function(element){
			console.log("elementos", element);
			suculento.push(element);
		});

		console.log("suculento : ", suculento);

		$('#unidadA').val(suculento);
		console.log($('#unidadA').val()); */
	});
	

  	$("#buscarCamasForm").bootstrapValidator({
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		fields: {
			rut: {
				validators: {
					callback: {
						callback: function(value, validator, $field){
							$("#dv").val('');
							return true;
						}
					}
				},
				fechaIngreso:{
					validators:{
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
				}
			},
			dv: {
				validators:{
					callback: {
						callback: function(value, validator, $field){
							var field_rut = $("#rut");
							var dv = $("#dv");
							if(field_rut.val() == '' && dv.val() == '') {
								return true;
							}
							if(field_rut.val() != '' && dv.val() == ''){
								return {valid: false, message: "Debe ingresar el dígito verificador"};
							}
							if(field_rut.val() == '' && dv.val() != ''){
								return {valid: false, message: "Debe ingresar el run"};
							}
							var rut = $.trim(field_rut.val());
							var esValido=esRutValido(field_rut.val(), dv.val());
							if(!esValido){
								return {valid: false, message: "Dígito verificador no coincide con el run"};
							}
							else{
								getPacienteRut(rut);
							}
							return true;
						}
					}
				}
			},
			rutMadre: {
				validators: {
					callback: {
						callback: function(value, validator, $field){
							$("#dvMadre").val('');
							return true;
						}
					}
				}
			},
			dvMadre: {
				validators:{
					callback: {
						callback: function(value, validator, $field){
							var field_rut = $("#rutMadre");
							var dv = $("#dvMadre");
							var rn = $('input[name="rn"]:checked').val();

							
							if(field_rut.val() == '' && dv.val() == '' &&  rn == 'no') {
								return true;
							}
							if(field_rut.val() != '' && dv.val() == '' &&  rn == 'no'){
								return {valid: false, message: "Debe ingresar el dígito verificador"};
							}
							if(field_rut.val() == '' && dv.val() != '' &&  rn == 'no' ){
								return {valid: false, message: "Debe ingresar el run"};
							}
							var rut = $.trim(field_rut.val());
							var esValido=esRutValido(field_rut.val(), dv.val());
							if(!esValido){
								return {valid: false, message: "Dígito verificador no coincide con el run"};
							}
							return true;
						}
					}
				}
			},
			nombre: {
				validators:{
					notEmpty: {
						message: 'El nombre es obligatorio'
					}
				}
			},
			fechaNac: {
				validators:{
					notEmpty: {
						message: 'El fecha de nacimiento es obligatoria'
					},
					callback: {
						callback: function(value, validator, $field){
							if (value === '') {
								return true;
							}
							var esMayor=esFechaMayor(value);
							if(esMayor){
								return {valid: false, message: "La fecha de nacimiento no puede ser mayor a la fecha actual"};
							}
							var esValidao=validarFormatoFecha(value);
							if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
							return true;
							}
					}
				}
			},
			'unidad[]':{
				validators:{
					notEmpty: {
						message: 'El nombre es obligatorio'
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
			"tipo-procedencia": {
				validators:{
					regexp: {
						regexp: /[1234]/,
						message: "Debe seleccionar la procedencia"
					}
				}
			},
			numeroCalle: {
				validators:{
					integer: {
						message: "Debe ingresar solo números"
					}
				}
			},
			"input-procedencia": {
				validators:{
					notEmpty: {
						
						message: "Debe especificar la procedencia"
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on('error.form.bv', function(e) {
		console.log(e);
	}).on("success.form.bv", function(evt){
		var $form = $(evt.target);
		var $button      = $form.data('bootstrapValidator').getSubmitButton();
		console.log($form);
		/* console.log("button");
		console.log($button); */

		switch ($button.attr("id")) {
			case 'btnSolicitar':
				/* console.log("BtnSolicitar"); */
				fv = $form.data('bootstrapValidator');
				//fv.disableSubmitButtons(false);
				evt.preventDefault();

				$.ajax({
					url: "{{URL::to('urgencia/agregarListaEspera')}}",
					type: 'post',
					dataType: 'json',
					data: $('#buscarCamasForm').serialize()
				})
				.done(function(data) {
					console.log(data);
					swalExito.fire({
					title: 'Exito!',
					text: data.exito,
					didOpen: function() {
						setTimeout(function() {
							location . reload();
						}, 2000)
					},
					});

					})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});
				break;

			case 'btnBuscar':
				console.log("BTNBuscarCama");
				fv = $form.data('bootstrapValidator');
				fv.disableSubmitButtons(false);
				evt.preventDefault();
				console.log(" formulario info : ", $('#buscarCamasForm').serialize());
				
				showLoad();
				$.ajax({
					url: "{{URL::to('/buscarCama')}}",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: $('#buscarCamasForm').serialize(),
					dataType: "json",
					type: "post",
					success: function(data){
						hideLoad();
						console.log("exito");
						console.log("data: ", data);

						window._gc_now = moment();
						fecha.date(window._gc_now);
						fecha.minDate(moment(window._gc_now).subtract(3, "days").startOf('day'));
						fecha.maxDate(moment(window._gc_now));

						if(data.error) {
							swalError.fire({
							title: 'Error',
							text:data.error
							});
						}else{
							//console.log("exito");
							enviado=true;
							//console.log("creando tabla");
							var myTable = $('#optimizacion-table').DataTable();
							myTable.clear();
							var tabla2=$("#optimizacion-table").dataTable().columnFilter({
								aoColumns: [
									{type: "text"},
									{type: "text"},
									{type: "text"},
									{type: "text", style: "width=50px;"},
									{type: "text"},
									{type: "text"},
									{type: "textarea"},
									null
								]
							});
							if(data.length != 0){
								tabla2.fnAddData(data);
							}

							$("#modalCamas").modal();
							//console.log("debeia ver");
						}
					},
					error: function(error){
						hideLoad();
						console.log(error);
						console.log("GTM");
					}
				});
				break
			// FIN BOTON BUSCAR
			default:
				console.log("ningun boton");
				break;
		}
	});

	
}); 
//FIN

/* }); */
</script>
@stop

@section("miga")
<li><a href="#">Pacientes</a></li>
<li><a href="#">Ingresar paciente</a></li>
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
  /*width: 430px;*/
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

#modalCamas {
	overflow-y:scroll
}
</style>

<legend>Ingresar pacientes</legend>
<p>(*) : Campo obligatorio.</p>
<!-- <div class="panel panel-primary">
	<div class="panel-heading">
	    Ayuda en la Búsqueda
	</div>
	<div class="panel-body">La idea de búsqueda es dar al gestor una herramienta para la elección mas óptima de la cama que necesita el paciente que se esta ingresando. Es dependiendo de los datos que se ingresen del paciente, la forma en que se le dará la cama y sala más factible al paciente. Cabe destacar que solo es una sugerencia y queda libre de elegir cualquier cama</div>
</div> -->

<!-- MODAL OPTIMIZACION -->
<div id="modalCamas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" style="width: 80% !important;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<div>
				<h4>Lista de camas</h4>
				<!-- <legend>En esta sección se muestran las camas con mayor posibilidad de </legend> -->
				</div>
			</div>
			<div class="modal-body">
				<div id='id-optimizacion' class='tab-pane' style='margin-top: 20px;'>
				    <table id='optimizacion-table' class='display responsive ' style="width:100%">
					    <thead>
					    	<tr>
					    		<th>Calificación</th>
					    		<th>Hospital</th>
					    		<th>Unidad</th>
					    		<th>Unidades recibidas</th>
					    		<th>Sala</th>
					    		<th>Cama</th>
					    		<th>Opción</th>
					    	</tr>

					    </thead>
					    <tbody>
					    	<!-- <td id='eliminar'>
					    	<input class="star" value='2'>
					    		<div id = "carga1" style="text-align:center;">
					    			<img src="{{ asset('images/default.gif') }}">
					    		</div>
					    	</td>
					    	 -->

					    </tbody>
				    </table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>



{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'derivarForm')) }}
<div id="modalSolicitar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



	{{ Form::hidden('idCama', '', array('id' => 'idCama')) }}

	{{ Form::hidden('idEstablecimiento', '', array('id' => 'idEstablecimiento')) }}

	{{ Form::hidden('idCaso', '', array('id' => 'idCaso')) }}

	{{ Form::hidden('motivo', "traslado externo", array('id' => 'motivo')) }}
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Solicitar cama</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						{{Form::text('asunto', null, array('id' => 'asunto', 'class' => 'form-control', 'disabled', 'placeholder' => 'Asunto'))}}
					</div>
					<div class="form-group col-md-12">
						{{Form::textarea('texto', null, array('id' => 'texto', 'class' => 'form-control', 'rows' => '5',  'disabled'))}}
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
								<td style="width: 100%;"><input multiple type="file" name="files[]" class="file" title="Elegir archivo"  data-upload-label="Subir" data-browse-label="Seleccionar ..." data-remove-label="Eliminar" data-show-preview="false" data-show-upload="false"/></td>
							</tr>
						</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="btnDerivar" type="" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>

</div>

{{ Form::close() }}
<!-- FIN MODAL OPTIMIZACION -->


<fieldset>

	{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'buscarCamasForm', 'autocomplete' => 'off')) }}
	{{ Form::hidden('latitud', null, array('id' => 'latitud')) }}
	{{ Form::hidden('longitud', null, array('id' => 'longitud')) }}
				<!-- {{ Form::hidden('id', '', array('id' => 'id')) }} -->
	<fieldset><legend>Datos del paciente</legend>
		<div id="divLoadBuscarPaciente" class="row" style="display: none;">
			<div class="form-group col-md-12">
				<span class="col-sm-5 control-label">Buscando paciente </span>
					{{ HTML::image('images/ajax-loader.gif', '') }}
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-md-12">
					<label for="rut" class="control-label" title="Run">Run: </label>
					<div class="input-group">
						{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
						<span class="input-group-addon"> - </span>
						{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
					</div>
				</div>
			</div>
				
            <div class="form-group col-md-6">
				<div class="col-md-12">
					<label class="control-label" title="Rn">Recién nacido: </label>
					<div class="input-group">
						<label class="radio-inline">{{Form::radio('rn', "no", true, array('id' => 'rn', 'required' => true))}}No</label>
						<label class="radio-inline">{{Form::radio('rn', "si", false, array('id' => 'rn', 'required' => true))}}Sí</label>
					</div>
				</div>
            </div>
        </div>

        <div  class="row">
            <div id="RnMadre" class="col-md-6 col-md-offset-5">
				<div class="col-md-12" style="padding-left:50px;">
					<label  class="control-label"><font color="#F42525"></font></label>
					<div class="input-group">
						<font color="#F42525">Debe ingresar el run de la madre. (*)</font>
					</div>
				</div>

				<div class="form-group col-md-12" style="padding-left:50px;">
					<div class="col-sm-12">
						<label for="rut" class="control-label"></label>
						<div class="input-group">
							{{Form::text('rutMadre', null, array('id' => 'rutMadre', 'class' => 'form-control', 'autofocus' => 'true'))}}
								<span class="input-group-addon"> - </span>
							{{Form::text('dvMadre', null, array('id' => 'dvMadre', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
						</div>
					</div>
				</div>
			</div> 
		</div> 

	    <div class="row">
	        <div class="form-group col-md-6">
	            <div class="col-sm-12">
	            	<label class="control-label" title="Extranjero">Extranjero: </label>
					<div class="input-group">
	                	<label class="radio-inline">{{Form::radio('extranjero', "no", true, array('required' => true))}}No</label>
	                	<label class="radio-inline">{{Form::radio('extranjero', "si", false, array('required' => true))}}Sí</label>
	        		</div>
				</div>
			</div>
	  
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="fechaNac" class="control-label" title="Fecha de nacimiento">Fecha de nac. (*): </label>
					{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control'))}}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="nombre" class="control-label" title="Nombre">Nombre (*): </label>
					{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
				</div>
			</div>
		
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="apellidoP" class="control-label" title="Apellido Paterno">Apellido Paterno: </label>
					{{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control'))}}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="apellidoM" class="control-label" title="Apellido Materno">Apellido Materno: </label>
					{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
				</div>
			</div>
		
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="sexo" class="control-label" title="Género">Género: </label>
					{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="nombreSocial" class="control-label" title="Nombre Social">Nombre Social: </label>
					{{Form::text('nombreSocial', null, array('id' => 'nombreSocial', 'class' => 'form-control'))}}
				</div>
			</div>

			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="calle" class="control-label" title="Calle">Calle: </label>
					{{Form::text('calle', null, array('id' => 'calle', 'class' => 'form-control'))}}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="numeroCalle" class="control-label" title="Número">Número: </label>
					{{Form::text('numeroCalle', null, array('id' => 'numeroCalle', 'class' => 'form-control'))}}
				</div>
			</div>

			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="observacionCalle" class="control-label" title="Observación">Observación: </label>
					{{Form::text('observacionCalle', null, array('id' => 'observacionCalle', 'class' => 'form-control'))}}
				</div>
			</div>
		</div>


		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="region" class="control-label" title="Region">Region: </label>
					{{ Form::select('region', $regiones, null, array('id' => 'region', 'class' => 'form-control')) }}
				</div>
			</div>
			
			<div class="form-group col-md-6">
				<div class="col-sm-12" id="comunas">
					<label for="comuna" class="control-label" title="Comuna">Comuna: </label>
					{{Form::select('comuna', $comunas, null, array('id' => 'comuna', 'class' => 'form-control'))}} 
				</div>
			</div>
		</div>
	</fieldset>
	


	<fieldset>
		<legend>Datos extra</legend>
		<div class="row">
			<div class="form-group col-md-12 pr45">
				<div class="col-sm-12 col-md-12" id="unidades">
					<label for="unidadChange" class="control-label" title="Unidad">Unidad: </label> 
					{{ Form::select('unidad[]', [0 => "UNIDADES DEL HOSPITAL"] +$unidades, 0 , array('class' => 'form-control selectpicker', 'multiple data-actions-box' => 'true', 'id' => 'unidadChange')) }} 
					{{ Form::hidden('unidadA[]',null,  array('id' => 'unidadA') ) }}
				</div>
			</div>
		</div>
	<fieldset>


	<legend>Datos de ingreso</legend>
    <div class="row">
        <div class="form-group col-md-6">
            <div class="col-sm-12">
            	<label for="caso_social" title="Caso social">Caso social: </label>
				<div class="input-group">
                	<label class="radio-inline">{{Form::radio('caso_social', "no", true, array('required' => true))}}No</label>
                	<label class="radio-inline">{{Form::radio('caso_social', "si", false, array('required' => true))}}Sí</label>
				</div>
			</div>
        </div>

        <div class="form-group col-md-6">
        	<!--<div class="col-sm-2">-->
        	<!--</div>-->
        	<div class="col-sm-8 diagnostico_cie101 pr0">
				<label for="files[]" class="control-label" title="Diagnóstico CIE10">Diagnóstico CIE10 (*):</label>
        		<input type="text" name="diagnosticos[]" class='form-control typeahead' />
        		<input type="hidden" name="hidden_diagnosticos[]">
        	</div>
        	<div class="col-sm-2 pl0">
				<label>&nbsp;&nbsp;</label>
        		<button class="btn btn-default w100" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
        	</div>
        </div>
    </div>

    <div id="fileTemplate" class="row hide">
        <div class="form-group col-md-12">
         	<!--<div class="col-md-2"></div>-->
	        <div class="col-md-10 diagnostico_cie101">
	          	<input type="text" name="diagnosticos[]" class='form-control typeahead'/>
	          	<input type="hidden" name="hidden_diagnosticos[]">
	        </div>
         	<div class="col-md-2" style="right: 50px;">
          		<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
          		<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
         	</div>
        </div>
    </div>


	<div class="row">
		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<label for="diagnostico" class="control-label" title="Comentario de diagnóstico">Comentario de diagnóstico: </label>
				{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
			</div>
		</div>

		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<label for="tipo-procedencia" class="control-label" title="Procedencia">Procedencia (*): </label>
				{{ Form::select('tipo-procedencia', [0 => "Seleccionar procedencia"] + $procedencias, 0, array('class' => 'form-control', "id" => "tipo-procedencia")) }}
			</div>
		</div>
	</div>

	<div class="row" id="row-procedencia">

	</div>

	<div class="row">
		<div class="form-group col-md-6">
			<div class="col-sm-12">
			<label for="prevision-lbl" class="control-label" title="Previsión">Previsión: </label>
				{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('id' => 'prevision-lbl', 'class' => 'form-control')) }}
			</div>
		</div>

		<div class="form-group col-md-6">
			<div class="col-sm-3 pr0">
				<label for="riesgo" class="control-label" title="Riesgo">Riesgo: </label>
				{{ Form::text('riesgo', null, array('id' => 'riesgo','class' => 'form-control', 'readonly', 'style' => 'text-align:center;')) }}

			</div>
			<div class="col-sm-7">
				<label >&nbsp;&nbsp;</label>
				<a id="riesgo" type="" class="btn btn-primary w100" onclick="modalRiesgoDependencia()" >Calcular Riesgo - Dependencia</a>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<label for="medico" class="control-label" title="Médico">Médico: </label>
				{{Form::text('medico', null, array('id' => 'medico', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="col-sm-12">
				<label for="especialidad" class="control-label" title="Especialidad">Especialidad: </label>
				{{Form::text('especialidad', null, array('id' => 'especialidad', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>
	</fieldset>

	<fieldset><legend></legend>
		<div class="row">
			<div class="form-group col-md-6">
				<div class="col-sm-12">
					<label for="tipo" class="control-label" title="Acción">Acción: </label>
					{{ Form::select('tipo', array('ingresar' => 'Ingresar'), null, array('id' => 'tipo', 'class' => 'form-control')) }}
				</div>
			</div>
		
			<div class="col-md-6" id="divFechaIngreso">
				<div class="form-group pr30">
					<div class="col-sm-12">
						<label for="fechaIngreso" class="control-label" title="Fecha de ingreso">Fecha de ingreso (*): </label>
						{{Form::text('fechaIngreso', null, array('id' => 'fechaIngreso', 'class' => 'form-control fecha-sel'))}}
					</div>
				</div>
				<div class="form-group col-md-12" id="categorizacionesIngreso">
				</div>
			</div>

			<div id="divHora" class="col-md-6" style="display: none;">
				<div class="form-group ">
					<div class="col-sm-12">
						<label for="horas" class="control-label" title="Horas de reserva">Horas de reserva: </label>
						{{ Form::select('horas', array('6' => '6 horas','5' => '5 horas','4' => '4 horas','3' => '3 horas','2' => '2 horas','1' => '1 hora' ), null, array('id' => 'horas', 'class' => 'form-control', 'disabled')) }}
					</div>
				</div>
			</div>
		</div>

		<div id="divMotivo" class="row">
			<div class="form-group col-md-12 pr45">
				<div class="col-sm-12 col-md-12">
					<label for="motivoC" class="control-label" title="Motivo">Motivo: </label>
					{{Form::textarea('motivo', null, array('id' => 'motivoC', 'class' => 'form-control', 'rows' => '5', 'enabled'))}}
				</div>
			</div>
		</div>
	</fieldset>

	<!-- <button id="btnBuscar" type="submit" class="btn btn-primary" onclick="buscarCamas()" >Buscar Cama</button> -->


	<div id="modalFormularioRiesgo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

		<div class="modal-dialog" style="width: 80%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Formulario de Riesgo - Dependencia</h4>
				</div>

				<div class="row" style="margin: 0;">
					<h3 for="horas" class="col-sm-12 control-label">CUIDADOS QUE IDENTIFICAN DEPENDENCIA <h3>
				</div>

				<div class="modal-body">

					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">1.- Cuidados en Confort y Bienestar: </label>
							<label for="horas" class="col-sm-4 control-label">Cambio de Ropa de cama y/o personal, o Cambio de Pañales, o toallas o apositos higenicos</label>
							<div class="col-sm-6">
								{{ Form::select('dependencia1', array('3' => 'Usuario receptor de estos cuidados básicos, requeridos 3 veces o más(con/sin participación de la familia)','2' => 'Usuario receptor de estos cuidados básicos 2 veces al día (con/sin participación de la familia)','1' => 'Usuario y familia realizan estos cuidados con ayuda y supervisión, cualquiera sea la frecuencia','0' => 'Usuario realiza solo el auto cuidado de cambio de ropa o cambio de pañal, toallas o apósitos higienicos'), 0, array('id' => 'dependencia1', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">2.- Cuidados en Confort y Bienestar: </label>
							<label for="horas" class="col-sm-4 control-label">Movilización y Transporte(levantada, deambulación y cambio de posición) </label>
							<div class="col-sm-6">
								{{ Form::select('dependencia2', array('3' => 'Usuario no se levanta y requiere cambio de posición en cama, 10 o más veces al día con/sin participación de familia','2' => 'Usuario es levantado a silla y requiere de cambio de posición, entre 4 a 9 veces al día sin/con participación de familia','1' => 'Usuario se levanta y deambula con ayuda y se cambia de posición en cama, solo o con ayuda de familia','0' => 'Usuario deambula sin ayuda y se moviliza solo en cama'), 0, array('id' => 'dependencia2', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">3.- Cuidados de Alimentación: </label>
							<label for="horas" class="col-sm-4 control-label"> Oral, Enteral o Parenteral  </label>
							<div class="col-sm-6">
								{{ Form::select('dependencia3', array('31' => 'Usuario recibe alimentación y/o hidratación por vía parenteral total/parcial y requiere control de ingesta oral ','32' => 'Usuario recibe alimentación por vía enteral permanente o discontinua ','2' => 'Usuario recibe alimentación por vía oral, con asistencia del personal de enfermería ','1' => 'Usuario se alimenta por vía oral, con ayuda y supervisión','0' => 'Usuario se alimenta sin ayuda '), 0, array('id' => 'dependencia3', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">4.- Cuidados de Eliminación: </label>
							<label for="horas" class="col-sm-4 control-label">  Orina, Deposiciones </label>
							<div class="col-sm-6">
								{{ Form::select('dependencia4', array('3' => 'Usuario elimina a través de sonda, prótesis, procedim, dialiticos, colectires adhesivos o pañales  ','2' => 'Usuario elimina por vía natural y se le entregan o colocan al usuario los colectores(chata o pato) ','1' => 'Usuario y familia realizan recolección de egresos con ayuda o supervisión','0' => 'Usuario usa colectores(chata o pato) sin ayuda y/o usa WC'), 0, array('id' => 'dependencia4', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">5.- Apoyo Psicosocial y Emocional: </label>
							<label for="horas" class="col-sm-4 control-label"> a usuario receptivo, angustiado, triste, agresivo, evasivo </label>
							<div class="col-sm-6">
								{{ Form::select('dependencia5', array('3' => 'Usuario recibe más de 30 minutos de apoyo durante turno ','2' => 'Usuario recibe entre 15 y 30 min. de apoyo durante turno','1' => 'Usuario recibe entre 5 y 14 min. de apoyo durante el turno','0' => 'Usuario recibe menos de 5 min. de apoyo durante el turno'), 0, array('id' => 'dependencia5', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">6.- Vigilancia: </label>
							<label for="horas" class="col-sm-4 control-label">  por alteración conciencia, riesgo caída o riesgo incidente (desplazamiento, retiro de vías, sondas, tubos), limitación física o por edad o de los sentidos </label>
							<div class="col-sm-6">
								{{ Form::select('dependencia6', array('31' => 'Usuario con alteración de conciencia  ','32' => 'Usuario con riesgo de caída o incidentes','2' => 'Usuario conciente pero intranquilo y c/riesgo caída o incidente','1' => 'Usuario conciente pero con inestabilidad de la marcha o no camina por alteración física','0' => 'Usuario conciente, orientado, autónomo'), 0, array('id' => 'dependencia6', 'class' => 'form-control')) }}
							</div>
						</div>



					</div>

					<div class="row" style="margin: 0;">
						<h3 for="horas" class="col-sm-12 control-label">CUIDADOS ESPECIFICOS DE ENFERMERIA QUE IDENTIFICAN RIESGO <h3>
					</div>

					<div class="modal-body">
					<div class="row" style="margin: 0;">
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">1.- Medición diaria de Signos Vitales (2 o mas parámetros simultáneos): </label>
							<label for="horas" class="col-sm-4 control-label">  Presión arterial, temperatura corporal, frecuencia cardiaca, frecuencia respiratoria, nivel de dolor y otros  </label>
							<div class="col-sm-6">
								{{ Form::select('riesgo1', array('3' => 'Control por 8 veces y más (cada 3 horas o más frecuente)','2' => 'Control por 4 a 7 veces (cada 4, 5, 6 o 7 horas)','1' => 'Control por 2 a 3 veces (cada 8, 9, 10, 11 o 12 horas','0' => 'Control por 1 vez (cada 13 a cada 24 horas'), 0, array('id' => 'riesgo1', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">2.- Balance hidrico: </label>
							<label for="horas" class="col-sm-4 control-label">Medición de Ingreso y Egreso realizado por profesionales en las ultimas 24 hrs.</label>
							<div class="col-sm-6">
								{{ Form::select('riesgo2', array('3' => 'Balance hidrico por 6 veces o más (cada 4 horas o más frecuente)','2' => 'Balance hidrico por 2 a 5 veces (cada 12, 8 ,6 o 5 horas)','1' => 'Balance hidrico por 1 vez (cada 24 horas o menor de cada 12 horas)','0' => 'No requiere'), 0, array('id' => 'riesgo2', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">3.- Cuidados en Oxigenoterapia: </label>
							<label for="horas" class="col-sm-4 control-label">Por cánula de traqueostomía, tubo endotraqueal, cámara, halo, máscara,
							sonda o bigotera.</label>
							<div class="col-sm-6">
								{{ Form::select('riesgo3', array('3' => 'Administración de oxígeno por tubo o cánula endotraqueal ','2' => 'Administración de oxígeno por máscara ','1' => 'Administración de oxígeno por canula nasal ','0' => 'Sin oxigenoterapia '), 0, array('id' => 'riesgo3', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">4.- Cuidados diarios de la Vía Aérea: </label>
							<label for="horas" class="col-sm-4 control-label">Aspiración de secreciones y Apoyo kinesico requerido</label>
							<div class="col-sm-6">
								{{ Form::select('riesgo4', array('31' => 'Usuario con vía aérea artificial (tubo o cánula endotraqueal)','32' => 'Usuario con vía aérea artif. o natural con 4 o + aspiraciones secreciones fraqueales y/o kinésico + de 4 veces','2' => 'Usuario respira por vía natural y requiere de 1 a 3 aspiraciones de secreciones y/o apoyo kinésico 2 a 3 veces al día','1' => 'Usuario respira por vía natural, sin aspiración de secreciones y/o apoyo kinésico 1 vez al día','0' => 'Usuario no requiere de apoyo ventilatorio adicional'), 0, array('id' => 'riesgo4', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">5.- Intervenciones profesionales: </label>
							<label for="horas" class="col-sm-4 control-label">Intervenciones quirurgicas y procedimientos invasivos, tales como punciones, toma de muestras, instalaciones de las vías, sondas y tubos .</label>
							<div class="col-sm-6">
								{{ Form::select('riesgo5', array('31' => '1 o más procedimientos invasivos realizadosmédicos en ultimas 24 horas.', '32' => '3 o más procedimientos invasivos realizados por enfermeras en últimas 24 horas','21' => '1 o 2 procedimientos invasivos realizados por enfermeras en últimas 24 horas','22' => '1 o más procedimientos invasivos realizados por otros profesionales  en últimas 24 horas','0' => 'No se realizan procedimientos invasivos en 24 horas'), 0, array('id' => 'riesgo5', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">6.- Cuidados de Piel y Curaciones: </label>
							<label for="horas" class="col-sm-4 control-label">Prevención de lesiones de la piel y curaciones o refuerzo de apósitos</label>
							<div class="col-sm-6">
								{{ Form::select('riesgo6', array('3' => 'Curación o refuerzo de apósitos 3 o más veces en el día, independiente de la complejidad de la técnica empleada','21' => 'Curación o refuerzo de apósitos 1 a 2 veces en el día, independiente de la complejidad de la técnica empleada','22' => 'Prevención compleja de lesiones de la piel: uso de colchón antiescaras, piel de cordero u otro','1' => 'Prevención corriente de lesiones: aseo, lubricación y protección de zonas propensas','0' => 'No requiere'), 0, array('id' => 'riesgo6', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">7.- Administración de Tratamiento Farmacologico: </label>
							<label for="horas" class="col-sm-4 control-label">Por vía inyectable EV, inyectable no EV, y por otras vías tales como oral, ocular, aérea, etc </label>
							<div class="col-sm-6">
								{{ Form::select('riesgo7', array('31' => 'Tratamiento intratecal e inyectable endovenoso, directo o por fleboclisis','32' => 'Tratamiento dirario con 5 o más fármacos distintos, administrados por diferentes vías no inyectable','21' => 'Tratamiento inyectable no endovenoso (IM, SC, ID)','22' => 'Tratamiento diario con 2 a 3 fármacos, administrados por diferentes vías no inyectable','1' => 'Tratamiento con 1 fármaco, administrado por diferentes vías no inyectable','0' => 'Sin tratamiento farmacológico'), 0, array('id' => 'riesgo7', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">8.- Presencia de Elementos Invasivos: </label>
							<label for="horas" class="col-sm-4 control-label">Catéteres y vías vasculares centrales y/o periféricas. Manejo de sondas urinarias y digestivas a permanencia. Manejo de drenajes intracavitareos o percutáneos</label>
							<div class="col-sm-6">
								{{ Form::select('riesgo8', array('3' => 'Con 3 o más elementos invasivos (sondas, drenajes, cateteres o vías vasculares)','21' => 'Con 1 o 2 elementos invasivos (sonda, drenaje, vía arterial, cateter o vía venosa central)','22' => 'Con 2 o más vías venosas perféricas (mariposas, teflones, agujas)','1' => 'Con 1 vías venosa periférica (mariposas, teflones, agujas)','0' => 'Sin elementos invasivos'), 0, array('id' => 'riesgo8', 'class' => 'form-control')) }}
							</div>
						</div>

					</div>

				</div>
				<div class="modal-footer">
					<a id="" type="" class="btn btn-primary"  onclick="btnRiesgoDependencia()">Aceptar</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	@if( Session::get("usuario")->tipo != TipoUsuario::USUARIO)
		<input id="btnBuscar" type="submit" name="" class="btn btn-primary" value="Asignar Cama">
	@endif
 	@if(Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::USUARIO )
  		<input id="btnSolicitar" type="submit" name="" class="btn btn-primary" value="Solicitar Cama">
	@endif
	{{ Form::close() }}





	<!-- MODAL RIESGO-->



	<!-- MODAL RIESGO-->

<br><br>
<meta name="csrf-token" content="{{{ Session::token() }}}">
{{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAKUCTycz-4g3GNG0uRjY6rXGui2PurUAM"></script> --}}
@stop 