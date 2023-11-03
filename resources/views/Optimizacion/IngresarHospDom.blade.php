@extends("Templates/template")

@section("titulo")
	Pacientes
@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
	<script>

		function mostrarPuebloOrigen(){

			var value=$("input[name='puebloind']:checked").val();

			if(value == "si"){
				$("#pueblo").show("slow");
			}else{
				$("#pueblo").hide("slow");$(".cla_ind").attr("hidden",true);
			} 
		}

		$(document).on('change', '#pueblo_ind', function () {
			//muestra la lista

			if ($("#pueblo_ind").val() == 'Otro') {
				$(".cla_ind").removeAttr("hidden");
			}else{
				$(".cla_ind").attr("hidden",true);
			}

		});

		var ocultarMuerte=function(){
			var value=$("input[name='rn']:checked").val();

			if(value == "si")$("#RnMadre").show("slow");
			else $("#RnMadre").hide("slow");
		}
		ocultarMuerte();

		var ocultarPasaporte=function(){
			var value=$("input[name='extranjero']:checked").val();

			if(value == "si")$("#NumPasaporte").show("slow");
			else $("#NumPasaporte").hide("slow");
		}
		ocultarPasaporte();

		var getPacienteRut=function(rut){
			// $("#divLoadBuscarPaciente").show();
			swalCargando.fire({title:'Cargando datos del paciente'});
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
				var fechaHoy = $("#fechaIngreso").val();
				var dvHoy = $("#dv").val();
				$("#buscarCamasForm").find('#datosPersonales input:text, select').val('');
				@if (Auth::user()->tipo == "usuario")
					$("#tipo-procedencia").val(1);
				@endif

				$("#fechaIngreso").val(fechaHoy);
				swalCargando.close();
				Swal.hideLoading();
				// $("#divLoadBuscarPaciente").hide();
				if(rut != ""){
					$("#rut").val(data.rutSin);
					$("#dv").val(dvHoy);
					$("#nombre").val(data.nombre);
					$("#sexo").val(data.genero);
					$("#apellidoP").val(data.apellidoP);
					$("#apellidoM").val(data.apellidoM);
					$("#rango").val(data.rango);
					$("#prevision-lbl").val(data.prevision);

					if(data.nombre){
						$("#nombre").attr('readonly', true);
					}
					if($("#nombre").val() == ""){
						$("#nombre").attr('readonly', false);
					}

					if(data.apellidoP){
						$("#apellidoP").attr('readonly', true);
					}
					if($("#apellidoP").val() == ""){
						$("#apellidoP").attr('readonly', false);
					}

					if(data.apellidoM){
						$("#apellidoM").attr('readonly', true);
					}
					if($("#apellidoM").val() == ""){
						$("#apellidoM").attr('readonly', false);
					}

					if (data.extranjero == true) {
						$("input:radio[name=extranjero]:not(:checked)").val('yes');
					}

					if(data.fecha == "No disponible"){
						data.fecha = '';
					}

					if(data.fecha != null || data.fecha != "" || data.fecha != "No disponible"){
						var fecha = data.fecha.replace('-', '');
						$("#fechaNac").val(fecha);
						$("#fechaNac").attr('readonly', true);
					}
					if($("#fechaNac").val() == "" || $("#fechaNac").val() == "No disponible"){
						$("#fechaNac").attr('readonly', false);
						$("#fechaNac").val('');
					}

					if(data.rango != "" && $("#fechaNac").val() != ""){
						$("#rango").attr('readonly', true);
						$('#rango option:not(:selected)').attr('disabled', true);
					}

					$("#nombreSocial").val(data.nombreSocial);
					// $("#telefono").val(data.telefono);
					$("#calle").val(data.calle);
					$("#numeroCalle").val(data.numero);
					$("#observacionCalle").val(data.observacion_direccion);

					if (data.region != null) {
						$("#region").val(data.region);
						buscarComuna();
					}
				}

				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'nombre');
				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'fechaIngreso');
				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'numeroCalle');

				if(data.en_cama){
					let mensaje = data.detalle_cama[0].nombre_establecimiento + ", unidad " + data.detalle_cama[0].alias + ", sala " + data.detalle_cama[0].nombre + ", cama " + data.detalle_cama[0].id_cama;
						swalInfo2.fire({
					title: 'El paciente ya se encuentra en una cama, su ubicación es:',
					text:mensaje
					}).then(function(result) {
						location.reload();
						$("form").trigger("reset");
					});
				}

				if(data.en_hosp_dom){
				swalInfo2.fire({
					title: 'Información',
					text:"El Paciente ya se encuentra con Hospitalización Domiciliaria"
					}).then(function(result) {
						location . reload();
					})
				
				}
			},
			error: function(error){
				swalCargando.close();
				Swal.hideLoading();
				// $("#divLoadBuscarPaciente").hide();
				console.log(error);
			}
			});
		}

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

		$("#comuna").on("change", function(){
			buscarGeo();
		});

		$("#fechaNac").focusout(function(){
			fecha = $(this).val().split("-");
			fechaString = fecha[2]+"-"+fecha[1]+"-"+fecha[0];
			edad = calcularEdad(fechaString);

			if((edad >0) && (edad<=9)){
				$("#rango").val("0-9");
			}
			else if((edad >10) && (edad<=19)){
				$("#rango").val("10-19");
			}
			else if((edad >20) && (edad<=29)){
				$("#rango").val("20-29");
			}
			else if((edad >30) && (edad<=39)){
				$("#rango").val("30-39");
			}
			else if((edad >40) && (edad<=49)){
				$("#rango").val("40-49");
			}
			else if((edad >50) && (edad<=59)){
				$("#rango").val("50-59");
			}
			else if((edad >60) && (edad<=69)){
				$("#rango").val("60-69");
			}
			else if((edad >70) && (edad<=79)){
				$("#rango").val("70-79");
			}
			else if((edad >80) && (edad<=89)){
				$("#rango").val("80-89");
			}
			else if((edad >90) && (edad<=99)){
				$("#rango").val("90-99");
			}
			else if((edad >100) && (edad<=109)){
				$("#rango").val("100-109");
			}
			else if((edad >110) && (edad<=119)){
				$("#rango").val("110-119");
			}
			else if((edad >120) && (edad<=129)){
				$("#rango").val("120-129");
			}
		});

		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm"
			// endDate: "+0d"
		});

		function buscarComuna(){
			$.ajax({
				url: "{{URL::to('/comunas')}}",
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: { "region": $("#region").val() },
				dataType: "json",
				type: "post",
				success: function(data){
					var html = "<select name='comuna' id='comuna' class='form-control'>";

					data.forEach(function(element){
						html +=  "<option value="+element.id_comuna+">"+element.nombre_comuna+"</option>";
					});

					html += "</select>";
					$("#comunas").find('#comuna').remove().end().append(html);
				},
				error: function(error){
					console.log(error);
				}
			});
		}

		$("#region").on("change", function(){
			buscarComuna();
		});

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
				limit: 1000
			});

			datos_cie10.initialize();

			$(self).typeahead(null, {
				name: 'best-pictures',
				display: 'nombre_cie10',
				source: datos_cie10.ttAdapter(),
				limit: 1000,
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
				if(!$cie10.val()&&$(this).val())
				{
					$(this).val("");
					$cie10.val("");
					$(this).trigger('input');
				}else{
					a.prop("disabled", false);
				}
			});
		}

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
		$(document).on("input","#input_procedencia_establecimiento",function(){
			
			var $id_procedencia = $(this).parents(".estabOculto").find("input[name='id_procedencia']");
			if($id_procedencia.val())
			{
				$(this).val("");
				$id_procedencia.val("");
				$(this).trigger('input');
			}else{
				//a.prop("disabled", true);
			}
		});

		$("#tipo-procedencia").on("change", function(){
			var value=$(this).val();
			if(value == 2){
				$(".ocultar").addClass("hidden");
				$(".estabPrivadoOculto").hide();
				$(".estabOculto").show();
				$("#input_procedencia_privado").val("");
				$("#input_procedencia_establecimiento_privado").prop("required",false);
			}
			else if(value == 7){
				$(".ocultar").addClass("hidden");
				$(".estabPrivadoOculto").show();
				$(".estabOculto").hide();
				$("#input_procedencia").val("");
				$("#input_procedencia_establecimiento_privado").prop("required",true);
			}else{
				$(".estabOculto").hide();
				$(".estabPrivadoOculto").hide();
				$("#input_procedencia").val("");
				$("#input_procedencia_privado").val("");
				$("#input_procedencia_establecimiento_privado").prop("required",false);
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

		// establecimientos privados
		var datos_establecimientos_privados = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('estabPrivado'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to('/')}}/'+'%QUERY/consulta_establecimientos_privados',
				wildcard: '%QUERY',
				filter: function(response) {
				    return response;
				}
			},
			limit: 50
		});

		datos_establecimientos_privados.initialize();
		
		$('.estabPrivado .typeahead').typeahead(null, {
			  name: 'best-pictures',
			  display: 'nombre_establecimiento',
			  source: datos_establecimientos_privados.ttAdapter(),
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
				$("[name='id_procedencia_privado']").val(selection.id_establecimiento);
			}).on('typeahead:close', function(ev, suggestion) {
			  var $estable=$(this).parents(".estabPrivado").find("input[name='id_procedencia_privado']");
			  if(!$estable.val()&&$(this).val()){
				  $(this).val("");
				  $estable.val("");
				  $(this).trigger('input');
			  }
			});
		$(document).on("input","#input_procedencia_establecimiento_privado",function(){
			
			var $id_procedencia_privado = $(this).parents(".estabPrivadoOculto").find("input[name='id_procedencia_privado']");
			if($id_procedencia_privado.val())
			{
				$(this).val("");
				$id_procedencia_privado.val("");
				$(this).trigger('input');
			}else{
				//a.prop("disabled", true);
			}
		});

		var fecha = $("#fechaIngreso").data("DateTimePicker");
		window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
		 $("#fechaIngreso").focus(function(){
				fecha . date(window . _gc_now);
				});
		
		// fecha.minDate(moment(window._gc_now).subtract(1, "days").startOf('day'));
		fecha.maxDate(moment(window._gc_now));

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

		$(document).ready(function() {

			$('.selectpicker').selectpicker();

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
				limit: 1000
			});

			datos_cie10.initialize();

			$('.diagnostico_cie101 .typeahead').typeahead(null, {
				name: 'best-pictures',
				display: 'nombre_cie10',
				source: datos_cie10.ttAdapter(),
				limit: 1000,
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
					console.log("RRRRRRRR");
				}
			});

			$("#fechaNac").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});

			function activarValidacionRn(){
				// console.log("activar validacion de rut de la madre");
				$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'rutMadre', true);
				$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'dvMadre', true);
				$("#spanRN").show();
			}

			function desactivarValidacionRn(){
				// console.log("desactivar validacion de rut de la madre");
				$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'rutMadre', false);
				$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'dvMadre', false);
				$("#spanRN").hide();
			}

			$("input[name='rn']").on("change", function(){
				var value=$(this).val();
				var extranjero = $("input:radio[name=extranjero]:checked").val();
				if(value == "si"){
					$("#RnMadre").show("slow");
					if(extranjero == "si"){
						desactivarValidacionRn();
					}else{
						activarValidacionRn();
					}
				}else{
					$("#RnMadre").hide("slow");
				} 
			});

			$("input[name='extranjero']").on("change", function(){
				var value=$(this).val();
				var recienNacido = $("input:radio[name=rn]:checked").val();
				if(value == "si"){
					$("#NumPasaporte").show("slow");
					if(recienNacido == "si"){
						desactivarValidacionRn();
					}else{
						activarValidacionRn();
					}
				}else{ 
					$("#NumPasaporte").hide("slow");
					activarValidacionRn();
				}
			});

			$("#patologias").on("change", function(){

				var largo= $("#patologias").children(':selected').length;
				$("#patologias_item").val(largo);
				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'patologias_item');
			});

			$("#buscarCamasForm").bootstrapValidator({
				excluded: [':disabled', 'hidden',':not(:visible)'],
				fields: {
					rut: {
						validators: {
							integer: {
								message: 'Debe ingresar solo números'
							}
						}
					},
					fechaIngreso:{
						validators:{
							notEmpty: {
								message: 'Debe ingresar una fecha de solicitud'
							},
							callback: {
								callback: function(value, validator, $field){
									var esValidao=validarFormatoFechaHora(value);
									if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
									return true;
								}
							}
						}
					},
					dv: {
						validators:{
							regexp: {
								regexp: /([0-9]|k)/i,
								message: 'Dígito verificador no valido'
							},
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
								regexp: /[123467]/,
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
					input_procedencia: {
						validators:{
							notEmpty: {

								message: "Debe especificar la procedencia"
							}
						}
					},
					input_procedencia_establecimiento_privado: {
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
					'especialidades_item':{
						validators: {
							callback: {
									callback: function(value, validator, $field){
										console.log("especialidades", value);
										var cantidad = $("#especialidades_item").val();
										console.log('cantidad', cantidad);
										if (value <= 0) {
											return {valid: false, message: "Debe seleccionar al menos una especialidad" };
										}else	if(cantidad >= 4){
											return {valid: false, message: "Debe seleccionar solamente tres especialidades" };
										}else{
											return true;
										}
									}
							}
						}
					},
					'patologias_item':{
						validators: {
							callback: {
								callback: function(value, validator, $field){
									console.log("patologias", value);
									var cantidad = $("#patologias_item").val();
									if (value <= 0) {
										return {valid: false, message: "Debe seleccionar al menos una patologia" };
									}else{
										return true;
									}
								}
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
				swalCargando.fire({});
				$.ajax({
					url: "{{URL::to('urgencia/agregarHospDom')}}",
					type: 'post',
					dataType: 'json',
					data: $('#buscarCamasForm').serialize()
				})
				.done(function(data) {
					swalCargando.close();
					Swal.hideLoading();
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

			});

			$("#especialidades").on("change", function(){
				var largo= $("#especialidades").children(':selected').length;
				$("#especialidades_item").val(largo);
				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'especialidades_item');
			});

			$( "#riesgo" ).on("change", function() {
				$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', false);
				if($("#riesgo").val() == "D2" || $("#riesgo").val() == "D3"){
					$('#buscarCamasForm').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', true);
				}
			});

			$("#rut").on("keyup", function() {
				if($("#rut").val() != ''){
					$('#buscarCamasForm').bootstrapValidator('revalidateField', 'dv');
				}
				if($("#rut").val() != ''  && $("#dv").val() != ''){
					$('#buscarCamasForm').bootstrapValidator('revalidateField', 'dv');
				}
				if($("#rut").val() == ''  && $("#dv").val() != ''){
					$('#buscarCamasForm').bootstrapValidator('revalidateField', 'dv');
				}
				if($("#rut").val() == '' && $("#dv").val() == ''){
					reValidacionRut();
				}
			});



			$("#dv").on("keyup", function() {
				if($("#rut").val() == '' && $("#dv").val() == ''){
					reValidacionRut();
				}
			});

			function reValidacionRut(){
				$('#buscarCamasForm').bootstrapValidator('revalidateField', 'dv');
					limpiarDatosPersonales();
					buscarComuna();
					$('#buscarCamasForm').bootstrapValidator('revalidateField', 'nombre');
			}

			function limpiarDatosPersonales(){
				$("#rango").val('');
				$("#fechaNac").val('');
				$("#rut").val('');
				$("#dv").val('');
				$("#nombre").val('');
				$("#sexo").val('');
				$("#apellidoP").val('');
				$("#apellidoM").val('');
				$("#rango").val('');
				$("#prevision-lbl").val('');
				$("#rnno").prop("checked", false);
				$("#RnMadre").hide();
				$("#rutMadre").val('');
				$("#dvMadre").val('');
				$("#extranjero").prop("checked", false);
				$("#n_pasaporte").val('');
				$("#NumPasaporte").hide();
				$("#nombreSocial").val('');
				// $("#telefono").val('');
				$("#calle").val('');
				$("#numeroCalle").val('');
				$("#observacionCalle").val('');
				$("#region").val('3');
				$("#nombre").attr('readonly', false);
				$("#apellidoP").attr('readonly', false);
				$("#apellidoM").attr('readonly', false);
				$("#rango").attr('readonly', false);
				$('#rango option:not(:selected)').attr('disabled', false);
				$("#rango").val('');
				$("#fechaNac").attr('readonly', false);
			}

			$("#fechaIngreso").on("dp.change", function(){
				$("#buscarCamasForm").bootstrapValidator("revalidateField", "fechaIngreso");
			});

		});
		
		$(function(){
			$("#HospitalizacionMenu").collapse();

			var num = 1;
			var limite = 3;
			var agregarBoton = $('.agregar_boton');
			// var removerBoton = $('.remover_boton');
			var contenedor = $('#dynamicTable');
		
			$(agregarBoton).click(function(){
				if(num < limite){
					num++;
					var campos = '<tr id="'+num+'"><td class="row-index"></td><td><select class="form-control" name="tipo_telefono[]" id="tipo_telefono_'+num+'"><option value="Movil">Movil</option><option value="Casa">Casa</option><option value="Trabajo">Trabajo</option></select></td><td><input type="number" id="telefono_${num}" name="telefono[]" placeholder="Ingrese número de telefono" class="form-control" /></td><td><button type="button" class="remover_boton btn btn-danger">Remover</button></td></tr>';
					$(contenedor).append(campos);
				}
			});

			$(contenedor).on('click', '.remover_boton', function(e){
				e.preventDefault();
				var child = $(this).closest('tr').nextAll();
				$(this).parents('tr').remove();
				num--;
			});
		});

	</script>
@stop

@section("miga")
    <li><a href="#">Ingreso Paciente</a></li>
    <li><a href="#">Hospitalizar en Domicilio</a></li>
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

		#dynamicTable tbody{
			counter-reset: Serial;           
		}

		table #dynamicTable{
			border-collapse: separate;
		}

		#dynamicTable tr td:first-child:before{
			counter-increment: Serial;      
			content: counter(Serial); 
		}
	</style>

	<legend>Hospitalizar en Domicilio</legend>
	<p>(*) : Campo obligatorio.</p>

	{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'buscarCamasForm', 'autocomplete' => 'off')) }}
		{{ Form::hidden('latitud', null, array('id' => 'latitud')) }}
		{{ Form::hidden('longitud', null, array('id' => 'longitud')) }}
		{{ Form::hidden('servicios', null, array('id' => 'servicios')) }}
				{{-- <!-- {{ Form::hidden('id', '', array('id' => 'id')) }} --> --}}

		<div class="formulario" style="overflow-y: scroll;     height: 550px;">
			<div class="panel panel-default">
				<div class="panel-heading"><h4>Datos personales:</h4></div>
				<div class="panel-body" id="datosPersonales">
					<div id="divLoadBuscarPaciente" class="col-md-12" style="display: none;">
						<div class="form-group col-md-12">
							<span class="col-sm-5 control-label">Buscando paciente </span>
								{{ HTML::image('images/ajax-loader.gif', '') }}
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group col-md-6">
							<div class="col-md-12">
								<label for="rut" class="control-label" title="Rut">Run: </label>
								<div class="input-group" style="z-index: 0;">
									{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control'))}}
									<span class="input-group-addon"> - </span>
									{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
								</div>
							</div>
						</div>

						<div class="form-group col-md-3">
							<div class="col-md-12">
								<label class="control-label" title="Rn">Recién Nacido: </label>
								<div class="input-group">
									<label class="radio-inline">{{Form::radio('rn', "no", false, array('required' => true, "id" => "rnno"))}}No</label>
									<label class="radio-inline">{{Form::radio('rn', "si", false, array('required' => true, "id" => "rnsi"))}}Sí</label>
								</div>
							</div>
						</div>

						<div class="form-group col-md-3">
							<div class="col-sm-12">
								<label class="control-label" title="Extranjero">Extranjero (*): </label>
								<div class="input-group">
									<label class="radio-inline">{{Form::radio('extranjero', "no", false, array('required' => true, "id" => "extranjero"))}}No</label>
									<label class="radio-inline">{{Form::radio('extranjero', "si", false, array('required' => true, "id" => "nacional"))}}Sí</label>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="col-md-5">
							<div id="NumPasaporte">
								<label for="npasaporte" class="control-label">Número Pasaporte:</label>
								{{Form::text('n_pasaporte', null, array('id' => 'n_pasaporte', 'class' => 'form-control', 'autofocus' => 'true'))}}
								<span color="black">Ingresar el número de pasaporte en caso de tener.</span>
							</div>
						</div>
						<div class="col-md-5 col-md-offset-1">
							<div id="RnMadre" style="margin-left: -27px;" class="form-group">
								<label for="rut" class="control-label">Rut De la Madre: </label>
								<div class="input-group">
									{{Form::text('rutMadre', null, array('id' => 'rutMadre', 'class' => 'form-control', 'autofocus' => 'true'))}}
									<span class="input-group-addon"> - </span>
									{{Form::text('dvMadre', null, array('id' => 'dvMadre', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
								</div>
								{{-- <span id="spanRN" color="black">Debe ingresar el run de la madre. (*)</span> --}}
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group col-md-6">
							<div class="col-sm-12">
								<label for="nombreSocial" class="control-label" title="Nombre Social">Nombre Social: </label>
								{{Form::text('nombreSocial', null, array('id' => 'nombreSocial', 'class' => 'form-control'))}}
							</div>
						</div>

						<div class="form-group col-md-3">
							<div class="col-sm-12">
								<label for="fechaNac" class="control-label" title="Fecha de nacimiento">Fecha de nac.: </label>
								{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control'))}}
							</div>
						</div>

						<div class="form-group col-md-3">
							<div class="col-sm-12">
								<label for="rango" class="control-label" title="Rango de edad">Rango edad: </label>
								{{ Form::select('rango', array('seleccione' => 'Seleccione', '0-9' => '0-9', '10-19' => '10-19', '20-29' => '20-29', '30-39' => '30-39', '40-49' => '40-49','50-59' => '50-59', '60-69' => '60-69', '70-79' => '70-79', '80-89' => '80-89', '90-99' => '90-99', '100-109' => '100-109',
								'110-119' => '110-119', '120-129' => '120-129'),
								null, array('id' => 'rango', 'class' => 'form-control')) }}
							</div>
						</div>
					</div>
					<div class="col-md-12">
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

					<div class="col-md-12">
						<div class="form-group col-md-6">
							<div class="col-sm-12">
								<label for="apellidoM" class="control-label" title="Apellido Materno">Apellido Materno: </label>
								{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
							</div>
						</div>

						<div class="form-group col-md-6">
							<div class="col-sm-12">
								<label for="sexo" class="control-label" title="Género">Género: </label>
								{{ Form::select('sexo', array('masculino' => 'Hombre', 'femenino' => 'Mujer', 'indefinido' => 'Intersex','desconocido' => 'Desconocido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group col-md-6">
							<div class="col-sm-12">
							<label for="prevision-lbl" class="control-label" title="Previsión">Previsión: </label>
								{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('id' => 'prevision-lbl', 'class' => 'form-control')) }}
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<label for="">Telefonos:</label>
						<table class="table table-bordered" id="dynamicTable">
							<thead>
								<tr>
								<th>Indice</th>
								<th>Tipo</th>
								<th>Número</th>
								<th>Acción</th>
								</tr>
							</thead>
							<tr>  
								<td></td> 
								<td>
									<select class="form-control" name="tipo_telefono[]" id="tipo_telefono_1">
										<option value="Movil">Movil</option>
										<option value="Casa">Casa</option>
										<option value="Trabajo">Trabajo</option>
									</select>
								</td>  
								<td><input type="number" name="telefono[]" id="telefono_1" placeholder="Ingrese número de telefono" class="form-control" /></td>   
								<td></td>  
							</tr>  
						</table>
						<div class="btn btn-primary agregar_boton" id="add">+ Teléfono</div> 
						<br><br>
					</div>
					
					
					<div class="col-md-12">
						<div class="col-md-4">
							<div class="form-group">
								<label for="" title="Caso social">Pertenencia a algún pueblo originario: </label>
								<br>
								<label class="radio-inline">{{Form::radio('puebloind', "no", false, array('id'=> 'pueblono', 'required' => true, 'onclick'=> 'mostrarPuebloOrigen()'))}}No</label>
								<label class="radio-inline">{{Form::radio('puebloind', "si", false, array('id'=> 'pueblosi', 'required' => true, 'onclick'=> 'mostrarPuebloOrigen()'))}}Sí</label>
							</div>
						</div>
						<div class="col-md-3 col-md-offset-1" id="pueblo" style="display: none">
							{{Form::label('', "Seleccione Pueblo:", array( ))}} <br>
							{{Form::select('pueblo_ind',["Mapuche" =>"1. Mapuche", "Aymara"=> "2. Aymara", "Rapa nui" => "3. Rapa Nui (Pascuense)", "Lican Antai" => "4. Lican Antai (Atacameño)", "Quechua" => "5. Quechua", "Colla" => "6. Colla","Diaguita" => "7. Diaguita" , "Kawéscar" => "8. Kawésqar", "Yagán" => "9. Yagán (Yámana)", "Ninguno" => "96. Ninguno", "Otro" => "99. Otro"], null, array('id' => 'pueblo_ind', 'class' => 'form-control', 'autofocus' => 'true'))}}
						</div>
						<div class="col-md-3 col-md-offset-1 cla_ind" hidden>
							{{Form::label('', "Especificar:", array( ))}} <br>
							{{Form::text('esp_pueblo', null, array('id' => 'esp_pueblo', 'class' => 'form-control', 'required' => true))}}
						</div>
						<br><br>
					</div>
					

					<legend>Datos de dirección</legend>
					<div class="col-md-12">
						<div class="form-group col-md-4">
							<div class="col-sm-12">
								<label for="calle" class="control-label" title="Calle">Calle: </label>
								{{Form::text('calle', null, array('id' => 'calle', 'class' => 'form-control'))}}
							</div>
						</div>

						<div class="form-group col-md-2">
							<div class="col-sm-12">
								<label for="numeroCalle" class="control-label" title="Número">Número: </label>
								{{Form::text('numeroCalle', null, array('id' => 'numeroCalle', 'class' => 'form-control'))}}
							</div>
						</div>

						<div class="form-group col-md-6">
							<div class="col-sm-12">
								<label for="observacionCalle" class="control-label" title="Observación">Observación dirección: </label>
								{{Form::text('observacionCalle', null, array('id' => 'observacionCalle', 'class' => 'form-control'))}}
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group col-md-6">
							<div class="col-sm-12">
								<label for="region" class="control-label" title="Region">Región: </label>
								{{ Form::select('region', $regiones, 3, array('id' => 'region', 'class' => 'form-control')) }}
							</div>
						</div>

						<div class="form-group col-md-6">
							<div class="col-sm-12" id="comunas">
								<label for="comuna" class="control-label" title="Comuna">Comuna: </label>
								{{Form::select('comuna', $comunas, 4, array('id' => 'comuna', 'class' => 'form-control'))}}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading"><h4>Datos de solicitud de hospitalización domiciliaria:</h4></div>
				<div class="panel-body">

					<div class="col-md-12">
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
								<label for="diagnostico" class="control-label" title="Comentario de diagnóstico">Complemento del diagnóstico: </label>
								{{Form::text('diagnostico[]', null, array('class' => 'form-control', "style" => "width:96%"))}}
							</div>

							<br>

							<div id="fileTemplate" class="col-md-12 hide">
								<div class="form-group col-md-12">
									<div class="col-md-9 diagnostico_cie101">
									<label for="files[]" class="control-label" title="Diagnóstico CIE10">Diagnóstico CIE10:</label>

											<input type="text" name="diagnosticos[]" class='form-control typeahead'/>
											<input type="hidden" name="hidden_diagnosticos[]">
									</div>
									<div class="col-md-3" style="right: 70px; top: 30px">
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

					<div class="col-md-12">
						<div class="col-md-5">
							<div class="col-md-12 form-group">
								<label  class="control-label">Patologia: </label>
								{{ Form::select('patologias', App\Models\Patologias::seleccion(), "select", array('class' => 'selectpicker form-control', "id" => "patologias", 'required' )) }}
								{{Form::text('patologias_item', "0", array('class' => 'form-control ', "id" => "patologias_item", "style" => "height:0px !important; padding:0; border:0px;"))}}
							</div>
						</div>
						<div class="col-md-4 form-group">
							<label for="riesgo" class="control-label" title="Riesgo">Especialidades: </label>
							{{ Form::select('especialidades[]', $especialidad, null, array('id' => 'especialidades', 'class' => 'selectpicker form-control', 'multiple', 'required', 'data-max-options'=>'3','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;, &quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;]")) }}
							{{ Form::text('especialidades_item', "0", array('class' => 'form-control ', "id" => "especialidades_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
						</div>
					</div>

					<div class="col-md-12">
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

					<div class="col-md-12" >
						<div class="col-md-3">
							<label for="requiere_aislamiento" title="Caso social">Requiere aislamiento: </label>
							<div class="input-group">
								<label class="radio-inline">{{Form::radio('requiere_aislamiento', "false", true)}}No</label>
								<label class="radio-inline">{{Form::radio('requiere_aislamiento', "true", false)}}Sí</label>
							</div>
						</div>
						<div class="col-md-3 offset-md-1">
							<label for="caso_social" title="Caso social">Caso social (*): </label>
							<div class="input-group">
								<label class="radio-inline">{{Form::radio('caso_social', "no", true, array('required' => true))}}No</label>
								<label class="radio-inline">{{Form::radio('caso_social', "si", false, array('required' => true))}}Sí</label>
							</div>
						</div>

						<div class="col-md-3 offset-md-1 form-group">
							<label for="fecha-ingreso" class="control-label" title="Fecha de ingreso">Fecha de Hosp. Dom.: </label>
							{{Form::text('fechaIngreso', null, array('id' => 'fechaIngreso', 'class' => 'form-control fecha-sel'))}}
						</div>
					</div>

					<div class="col-md-12" >
						<div class="col-md-3"></div>
						<div class="hidden" id="tipo_caso_social">
							<div class="col-md-3 offset-md-1">
								<div class="form-group">
									<label for="t_caso_social" title="Tipo de caso social">Tipo de caso social: </label>
									{{ Form::text('t_caso_social', null, array( 'class' => 'form-control')) }}
								</div>
							</div>
						</div>
						<div class="col-md-3 offset-md-1"></div>
					</div>

					<div class="col-md-12">
						<div class="col-md-6 ">
							<div class="col-md-12 form-group">
								<label for="tipo-procedencia" class="control-label" title="Procedencia">Origen de la solicitud (*): </label>
								{{ Form::select('tipo-procedencia', [0 => "Seleccionar procedencia"] + $procedencias, 0, array('class' => 'form-control', "id" => "tipo-procedencia")) }}
							</div>
						</div>
					</div>

					<div class="col-md-12" id="row-procedencia">
					</div>

					<div class="col-md-12" >
						<div class="col-md-12">
							<div class="form-group estabOculto" style="display: none">
								<label for="Establecimiento" class="col-sm-2 control-label">Especifique origen: </label>
								<div class="col-sm-10 estab">
									{{Form::text('input_procedencia_establecimiento', null, array('id' => 'input_procedencia_establecimiento', 'class' => 'form-control typeahead', 'required' => 'required'))}}
									{{Form::hidden('id_procedencia', null)}}
								</div>
							</div>
							<div class="form-group col-md-12 estabPrivadoOculto" style="display: none">
								<label for="Establecimiento" class="col-sm-2 control-label">Especifique origen: </label>
								<div class="col-sm-10 estabPrivado">
									{{Form::text('input_procedencia_establecimiento_privado', null, array('id' => 'input_procedencia_establecimiento_privado', 'class' => 'form-control typeahead', 'required' => 'required'))}}
									{{Form::hidden('id_procedencia_privado', null)}}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		@if(Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo === TipoUsuario::MASTER  || Session::get("usuario")->tipo === TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::ENCARGADO_HOSP_DOM)
			<input id="btnSolicitar" type="submit" name="" class="btn btn-primary" value="Hospitalizar en Domicilio">
		@endif

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
								<label class="col-sm-2 control-label">2.- Cuidados en Confort y Bienestar: </label>
								<label class="col-sm-4 control-label">Movilización y Transporte(levantada, deambulación y cambio de posición) </label>
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
								<label class="col-sm-2 control-label">3.- Cuidados de Alimentación: </label>
								<label class="col-sm-4 control-label"> Oral, Enteral o Parenteral  </label>
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
								<label class="col-sm-2 control-label">4.- Cuidados de Eliminación: </label>
								<label class="col-sm-4 control-label">  Orina, Deposiciones </label>
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
								<label class="col-sm-2 control-label">5.- Apoyo Psicosocial y Emocional: </label>
								<label class="col-sm-4 control-label"> a usuario receptivo, angustiado, triste, agresivo, evasivo </label>
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
								<label class="col-sm-2 control-label">6.- Vigilancia: </label>
								<label class="col-sm-4 control-label">  por alteración conciencia, riesgo caída o riesgo incidente (desplazamiento, retiro de vías, sondas, tubos), limitación física o por edad o de los sentidos </label>
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
								<label class="col-sm-2 control-label">1.- Medición diaria de Signos Vitales (2 o mas parámetros simultáneos): </label>
								<label class="col-sm-4 control-label">  Presión arterial, temperatura corporal, frecuencia cardiaca, frecuencia respiratoria, nivel de dolor y otros  </label>
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
								<label class="col-sm-2 control-label">2.- Balance hidrico: </label>
								<label class="col-sm-4 control-label">Medición de Ingreso y Egreso realizado por profesionales en las ultimas 24 hrs.</label>
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
								<label class="col-sm-2 control-label">3.- Cuidados en Oxigenoterapia: </label>
								<label class="col-sm-4 control-label">Por cánula de traqueostomía, tubo endotraqueal, cámara, halo, máscara,
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
								<label class="col-sm-2 control-label">4.- Cuidados diarios de la Vía Aérea: </label>
								<label class="col-sm-4 control-label">Aspiración de secreciones y Apoyo kinesico requerido</label>
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
								<label class="col-sm-2 control-label">5.- Intervenciones profesionales: </label>
								<label class="col-sm-4 control-label">Intervenciones quirurgicas y procedimientos invasivos, tales como punciones, toma de muestras, instalaciones de las vías, sondas y tubos .</label>
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
								<label class="col-sm-2 control-label">6.- Cuidados de Piel y Curaciones: </label>
								<label class="col-sm-4 control-label">Prevención de lesiones de la piel y curaciones o refuerzo de apósitos</label>
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
								<label class="col-sm-2 control-label">7.- Administración de Tratamiento Farmacologico: </label>
								<label class="col-sm-4 control-label">Por vía inyectable EV, inyectable no EV, y por otras vías tales como oral, ocular, aérea, etc </label>
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
								<label class="col-sm-2 control-label">8.- Presencia de Elementos Invasivos: </label>
								<label class="col-sm-4 control-label">Catéteres y vías vasculares centrales y/o periféricas. Manejo de sondas urinarias y digestivas a permanencia. Manejo de drenajes intracavitareos o percutáneos</label>
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
