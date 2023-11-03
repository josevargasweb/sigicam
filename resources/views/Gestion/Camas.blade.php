@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop



@section("script")

<script>
	var num = 1;
	var mostrar_error_get_paciente = 0;
	//modal alta sin liberar cama
	var ocultarCambiarMedico=function(){
		//$("#medicoAltaLC").attr("required", false);
		var value=$("input[name='cambiarMedico']:checked").val();
		if(value == "si"){
			$(".medicoDioAlta").show("slow");
			//$("#medicoAltaLC").attr("required", true);
			}
		else {
			$(".medicoAltaOculto").hide("slow");
			//$("#medicoAltaLC").attr("required", false);
			}
	}
	ocultarCambiarMedico();
	//modal alta sin liberar cama

	$("#complejidad_servicio").on("change", function(){
		var value = $(this).val();
		$.ajax({
			url: '{{URL::to("getAreaFuncionalPorServicio")}}',
			headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "complejidad_servicio": value },
			dataType: "json",
			type: "post",
			success: function(data){
				$("#servicios2").empty();
				data.forEach(function(element){
					$("#servicios2").append('<option value="'+element.id_complejidad_area_funcional+'" selected="selected">'+element.nombre+'</option>');
				});
			},
			error: function(error){
				console.log(error);
			}
		});
	});
	$("#riesgo").on('change', function(){
			var riesgo=$(this).val();

			if(riesgo == 'A1' || riesgo == "A2" || riesgo == "A3" || riesgo == "B1" || riesgo == "B2"){
				complejidad = 'crítico';
			}
			else if (riesgo == 'B3' || riesgo == "C1" || riesgo == "C2"){
				complejidad = 'medio';
			}
			else if(riesgo == 'C3' || riesgo == "D1" || riesgo == "D2" || riesgo == "D3"){
				complejidad = "básico";
			}

			$.ajax({
			url: '{{URL::to("getComplejidadPorRiesgo")}}',
			headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "complejidad": complejidad },
			dataType: "json",
			type: "post",
			success: function(data){
				$("#complejidad_servicio").empty();
				$("#complejidad_servicio").append('<option value="" selected="selected">Seleccione servicio</option>');

				$.each(data, function(kery, value){

					$("#complejidad_servicio").append('<option value='+value.id_complejidad+' >'+value.nombre_servicio+'</option>');
				});


			},
			error: function(error){
				console.log(error);
			}
		});
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
				$("#comuna").find('option').remove(); //limpiar opciones del select
				$(data).each(function(i,v){ //recorrer comunas
					$("#comuna").append('<option value="'+v.id_comuna+'">'+v.nombre_comuna+'</option>'); //agregarlas al option del select
				});
			},
			error: function(error){
				//$("#divLoadBuscarPaciente").hide();
				console.log(error);
			}
		});
	}

</script>

{{-- Fin Script Nolazko--}}

<script>
$( document ).ready(function() {
	/* if( Auth::user()->tipo == 'medico')
		//Sacar de esta vista si no es usuario permitido
		window.location.href = "../index";
	endif */

	@if($unidad == "urgencia")
		$("#seccionUrgencia").removeAttr("hidden");
	@endif

	$('#modalAsignacionCama').on('hidden.bs.modal', function () {
		$("#asignarCamasForm")[0].reset();
		$("#casoHospDom").val('');
		$("#fechaNac").attr('readonly', false);
		$("#nombre").attr('readonly', false);
		$("#apellidoP").attr('readonly', false);
		$("#apellidoM").attr('readonly', false);
		$("#rango").attr('readonly', false);
		$('#rango option:not(:selected)').attr('disabled', false);
		$("#rango").val("");
		$("#riesgo").val("");
		$("#comentario-riesgo").val("");
		$("#div-comentario-riesgo").attr("style", "display:none;");
		buscarComuna();
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
		// $("#especialidades").change().val('');
		$("#especialidades").selectpicker("refresh");
		$("#especialidades_item").val(0);
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'especialidades_item');
		$(".borrarTelefonos").empty();
		$("#RnMadre").hide();
		$("#NumPasaporte").hide("slow");
		num = 1;
	});

	$("#modalAsignacionCama").on("shown.bs.modal", function () {
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'especialidades_item');
	});

	$("#fechaNac").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});

	$("#comuna").on("change", function(){
		buscarGeo();
	});

	$("#region").on("change", function(){
		buscarComuna();
	});

	$("#fechaNac").focusout(function(){
		//console.log($(this).val());
		fecha = $(this).val().split("-");

		fechaString = fecha[2]+"-"+fecha[1]+"-"+fecha[0];
		//console.log(fecha[2]+"-"+fecha[1]+"-"+fecha[0]);
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

	$("#calle, #numeroCalle").on('keyup', function(){
		buscarGeo();
	});

	function buscarGeo(){
		var geocoder = new google.maps.Geocoder();
		var address =  $("#calle").val()+" "+$("#numeroCalle").val()+" ,"+$("#comuna option:selected").text()+", Chile";

		geocoder.geocode({
						'address': address
		}, function (results, status) {

				var latitud=null;
				var longitud=null;

				if (status == google.maps.GeocoderStatus.OK) {
						latitud = results[0].geometry.location.lat();
						longitud = results[0].geometry.location.lng();
				} else {
						latitud= null;
						longitud=null;
				}
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




$(document).on('mouseover',".divContenedorCama",function(){
	//console.log($(this).attr("data-nombre"));
	nombrePaciente = $(this).attr("data-nombre");
	if(nombrePaciente == 'null'){
		nombrePaciente = "";
	}
	$(this)
	.attr('data-original-title', nombrePaciente)
          .tooltip('show');
	});



$(document).on('mouseout',".divContenedorCama",function(){
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

	$(document).on("input","input[name='diagnosticos[]']",function(){
		var a = $(this).parent().parent().parent().parent().children(".col-md-3").children().eq(0);
		//a.prop("disabled", true);
		//console.log("asasa", a);
		var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		if($cie10.val()){
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
		}else{
			//a.prop("disabled", true);
		}
	});

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
		$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
		$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
		$("#cie10-principal").prop("disabled", false);
	}).on('typeahead:close', function(ev, suggestion) {
	  	var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		if(!$cie10.val()&&$(this).val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
			//$("#cie10-principal").prop("disabled", false);
		}
	});

	var count=0;

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
					if(data.nombre_categoria == null){
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}else{
						return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
					}
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
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

// establecimientos
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
			
			var $estable=$(this).parents(".estabPrivadoOculto").find("input[name='id_procedencia_privado']");
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


// establecimientos

	//Mauricio//
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
		//$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
	}).on('typeahead:close', function(ev, suggestion) {
	  var $med=$(this).parents(".medicos").find("input[name='id_medico']");
	  if(!$med.val()&&$(this).val()){
		  $(this).val("");
		  $med.val("");
		  $(this).trigger('input');
	  }
	});

	$("#medicoDerivador").on('change keyup',function(){
		var me = $(this);
		if(!me.val()){
			me.val('');
			$("[name='id_medico']").val('');
		}
	});


  	agregar=function(boton){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		//var $clone =  $template.clone().removeClass('hide').insertBefore($template);

		//console.log($clone.find("input"));
		//var el = $("<input type='text' name='retrun-order-invoice_no' class='return-order-invoice_no'>").insertAfter($('#fileTemplate'));

		$clone.find("input").eq(2).val("");
		/* console.log($clone.find("input")[1]); */
		invoice_no_setup_typeahead($clone.find("input")[1],$clone.find("input")[2]);
		$(boton).prop("disabled", true);

		/*var $input = $clone.find('input[type="file"]');
		var id="id_"+count;
		$input.prop("id", id);
		$('#'+id).fileinput();
		console.log("#"+id+" .file-input-new .input-group");
		$(".file-input-new .input-group").css({"width": "95%", "margin-left": "10px"});
		count++;
		*/
		//$("#asignarCamasForm").bootstrapValidator('addField', 'diagnosticos[]');
	}

	borrar=function(boton){
		$(boton).parent().parent().parent().remove();
		var diagnosticos = $("[name='diagnosticos[]']");
		var cantidad = diagnosticos.length -1;
		var anterior = cantidad - 1;
		var a = $(diagnosticos[anterior]).parent().parent().parent().parent().children(".col-md-3").children().eq(0);
		/* console.log("botooon: ", a); */
		if(cantidad - 1 == 0){
			/* console.log("if"); */
			$("#cie10-principal").prop("disabled", false);
		}else{
			/* console.log("else"); */
			a.prop("disabled", false);
		}
	}


});


function modalRiesgoDependencia(){
	if('{{$unidad}}'=='saludmentalapace' || '{{$unidad}}'=='saludmentalapiace'){
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

function btnRiesgoDependencia2() {

		//busco los select dentro del modal
		selects = $('#modalFormularioRiesgo2').find("select");

		sumaRiesgo =0;
		sumaDependencia =0;
		//recorro los select
		$.each(selects, function(i, val){
			//el id del input
			idInput = val.id;
			//console.log($(this).val());
			//saco el primer caracter para saber si es riesgo o dependencia
			primerCaracter = idInput.substr(0,1);

			if(primerCaracter == "d"){
				sumaDependencia += parseInt($(this).val().substr(0,1));
				//console.log(parseInt($("#"+idInput).val()));
			}

			if(primerCaracter == "r"){
				sumaRiesgo += parseInt($(this).val().substr(0,1));
				//console.log(parseInt($("#"+idInput).val()));
			}



		});


		if (sumaRiesgo >=20) {
			riesgoDependencia = "A";
		}else if(sumaRiesgo >= 13 && sumaRiesgo <= 19){
			riesgoDependencia = "B";
		}else if (sumaRiesgo >= 6 && sumaRiesgo <= 12) {
			riesgoDependencia = "C";
		}else{
			riesgoDependencia = "D";
		}


		if (sumaDependencia >=12) {
			riesgoDependencia += "1";
		}else if(sumaDependencia >= 6 && sumaDependencia <= 11){
			riesgoDependencia += "2";
		}else{
			riesgoDependencia += "3";
		}

		//console.log(sumaRiesgo);
		//	console.log(sumaDependencia);
		$("#riesgo").val(riesgoDependencia).trigger("change");
		$("#div-comentario-riesgo").show();
		$('#modalFormularioRiesgo2').modal('hide');

	}
	@include('Gestion.boletinPago.js')

var dialog;
var dialog2;
var enviado = false;
var paciente_plan;
var click = false;
$(function(){



	$("#simbolo").show();
	$(".fecha-sel").datetimepicker({
		locale: "es",
		format: "DD-MM-YYYY HH:mm"
	}).on('dp.change', function (e) {
		$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
	});

	$("#fechaEgreso").on('keyup', function(){
		$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
	});

	$("#vista").on("change", function(){
		var value=$(this).val();
		if(value == 1){
			$("#tablaCamas").hide();
			generarMapaCamas("mapaSalas", "{{$unidad}}");
			$("#mapaSalas").show();
			$("#simbolo").show();
		}
		if(value == 2){
			$("#mapaSalas").hide();
			$(".descripcionPaciente").hide();
			$("#simbolo").hide();
			obtenerCamasLista();
			$("#tablaCamas").show();
		}
	});

	var ocultarMuerte=function(){
		var value=$("input[name='rn']:checked").val();

		if(value == "si") $("#RnMadre").show("slow");
		else $("#RnMadre").hide("slow");
	}

	ocultarMuerte();

	var ocultarPasaporte=function(){
		var value=$("input[name='extranjero']:checked").val();

		if(value == "si")$("#NumPasaporte").show("slow");
		else $("#NumPasaporte").hide("slow");
	}
	ocultarPasaporte();

	function activarValidacionRn(){
		// console.log("activar validacion de rut de la madre");
		$('#asignarCamasForm').bootstrapValidator('enableFieldValidators', 'rutMadre', true);
		$('#asignarCamasForm').bootstrapValidator('enableFieldValidators', 'dvMadre', true);
		$("#spanRN").show();
	}

	function desactivarValidacionRn(){
		// console.log("desactivar validacion de rut de la madre");
		$('#asignarCamasForm').bootstrapValidator('enableFieldValidators', 'rutMadre', false);
		$('#asignarCamasForm').bootstrapValidator('enableFieldValidators', 'dvMadre', false);
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

	$("input[name='recibe_visitas_']").on("change", function(){
		var value = $("input:radio[name=recibe_visitas_]:checked").val();
		if(value == "true"){
			$("#inputs_config_historial").prop("hidden",true);
			$("#input_comentario_visitas_").hide();
		}else if(value == "false"){
			$("#inputs_config_historial").prop("hidden",false);
			$("#input_comentario_visitas_").show();
		}else{
			$("#inputs_config_historial").prop("hidden",false);
			$("#input_comentario_visitas_").hide();
		}
	});

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
			return "";
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
			case 'Reconvertida': return 4;
		}
	}

	$('#tablaVistaLista').dataTable({
		//"aaSorting": [[8, "desc"],[9, "desc"]],
		"iDisplayLength": 50,
		"bJQueryUI": true,
		"oLanguage": {
			"sUrl": "{{ URL::to('/') }}/js/spanish.txt"
		},
		aoColumns: [
			{mData: 0},
			{mData: 1},
			{mData: function(source, type, val) {return display_num_cama(source, type);}},
			{mData: 3},
			{mData: 4},
			{mData: 5},
			{mData: 6},
			{mData: function(source, type, val) {return display_tiempo_estada(source, type, 7);}},
			//{mData: function(source, type, val) {return display_estado(source, type, val);}},
			{mData: 8},
			{mData: function(source, type, val) {return display_tiempo_estada(source, type, 9);}},
			{mData: 12},
			{mData: 13},
			{mData: 14},
		]
	});

	var habilitarCampos = function(){
		$("#fechaNac").attr('readonly', false);
		$("#nombre").attr('readonly', false);
		$("#apellidoP").attr('readonly', false);
		$("#apellidoM").attr('readonly', false);
		$("#rango").attr('readonly', false);
		$('#rango option:not(:selected)').attr('disabled', false);
	}

	var getPacienteRut=function(rut){

		// $("#divLoadBuscarPaciente").show();
		swalCargando.fire({title:'Buscando datos del paciente'});
		$.ajax({
			url: "{{URL::to('/getPaciente')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {rut: rut},
			dataType: "json",
			type: "post",
			success: function(data){
				habilitarCampos();
				swalCargando.close();
				// $("#divLoadBuscarPaciente").hide();
				var fechaHoy = $("#fechaIngreso").val();
				var dvHoy = $("#dv").val();
				$("#asignarCamasForm").find('#datosPersonales input:text, select').val('');
				$("#fechaIngreso").val(fechaHoy);
				$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
				if(rut != ""){
					$("#rut").val(data.rutSin);
					$("#dv").val(dvHoy);
					// $("#fechaNac").datepicker('update', data.fecha);

					if(data.fecha == "No disponible"){
						data.fecha = '';
					}

					$("#nombre").val(data.nombre);
					$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
					$("#sexo").val(data.genero);
					$("#apellidoP").val(data.apellidoP);
					$("#apellidoM").val(data.apellidoM);

					$("#rango").val(data.rango);
					$("#prevision-lbl").val(data.prevision);

					$("#nombreSocial").val(data.nombreSocial);
					// $("#telefono").val(data.telefono);
					$("#calle").val(data.calle);
					$("#numeroCalle").val(data.numero);
					$("#observacionCalle").val(data.observacion_direccion);

					if(data.nombre){
						$("#nombre").attr('readonly', true);
					}
					if($("#nombre").val() == ""){
						$("#nombre").attr('readonly', false);
						// $('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
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

					if(data.fecha != null || data.fecha != "" || data.fecha != "No disponible"){
						var fecha = data.fecha.replace('-', '');
						$("#fechaNac").val(fecha);
						$("#fechaNac").attr('readonly',true);
					}
					if($("#fechaNac").val() == "" || $("#fechaNac").val() == "No disponible"){
						$("#fechaNac").attr('readonly', false);
						$("#fechaNac").val('');
					}

					if(data.rango != "" && $("#fechaNac").val() != ""){
						$("#rango").attr('readonly', true);
						$('#rango option:not(:selected)').attr('disabled', true);
					}

					nombre_completo = data.nombre+" "+data.apellidoP+" "+data.apellidoM;
					$("#nombre_paciente_formulario").val(nombre_completo);
					if (data.region != null || data.region != '') {
						$('#region').val(data.region);
						buscarComuna();
					}
				}
				if(data.en_cama)
				{
						let mensaje = data.detalle_cama[0].nombre_establecimiento + ", unidad " + data.detalle_cama[0].alias + ", sala " + data.detalle_cama[0].nombre + ", cama " + data.detalle_cama[0].id_cama;
						swalInfo2.fire({
							title: 'El paciente ya se encuentra en una cama, su ubicación es:',
							text:mensaje
						}).then(function(result) {
								$("form").trigger("reset");
								$("#nombre").attr('readonly', false);
								$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
								$("#apellidoP").attr('readonly', false);
								$("#apellidoM").attr('readonly', false);
								$("#rango").attr('readonly', false);
								$('#rango option:not(:selected)').attr('disabled', false);
								$("#rango").val('');
								$("#fechaNac").attr('readonly', false);
						});
	
				}

				if(data.en_hosp_dom){
					bootbox.confirm({
					title: "¡El Paciente se encuentra con Hospitalización Domiciliaria!",
					message: "<h4>¿Desea volver a ingresar al paciente?</h4>",
						buttons: {
							confirm: {
								label: 'Aceptar',
								className: 'btn-success'
							},
							cancel: {
								label: 'Cancelar',
								className: 'btn-danger'
							}
						},
						callback: function (result) {
							if(result == true){
								$("#casoHospDom").val(data.en_hosp_dom["caso"]);
							}else{
								$("#casoHospDom").val('');
								$("form").trigger("reset");
								$("#nombre").attr('readonly', false);
								$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
								$("#apellidoP").attr('readonly', false);
								$("#apellidoM").attr('readonly', false);
								$("#rango").attr('readonly', false);
								$('#rango option:not(:selected)').attr('disabled', false);
								$("#rango").val('');
								$("#fechaNac").attr('readonly', false);
								$('#asignarCamasForm').bootstrapValidator('revalidateField', 'diagnosticos[]');
								$('#asignarCamasForm').bootstrapValidator('revalidateField', 'diagnostico');
								$('#asignarCamasForm').bootstrapValidator('revalidateField', 'tipo-procedencia');
								$("#fechaIngreso").val("{{ Carbon\Carbon::now()->format('d-m-Y H:i') }}");
							}
						}
					});
				}
			},
			error: function(error){
				swalCargando.close();
				// $("#divLoadBuscarPaciente").hide();
				console.log(error);
			}
		});
	}

	$('#modalBloqueo').on('show.bs.modal', function (e) {
		$("#otroMotivoBloqueo").val("");
		$("#motivoBloqueo").trigger("change");
	});

	$("#motivoBloqueo").on("change", function(){
		if($(this).val() == 'otros'){
			$("#divOtroMotivoBloqueo").css({"display":"block"});
		}
		else{
			$("#divOtroMotivoBloqueo").css({"display": "none"});
		}
	});

	$("#reconvertirOriginal").on("click", function(){
		$.ajax({
			url: "reconvertirOriginal",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {idCama: $("#camaReserva").val()},
			type: "post",
			dataType: "json",
			success: function(data){
				if(data.exito){
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
	});

	$("#servicios").on("change", function(){
		var value=$(this).val();
		$.ajax({
			url: "getSalas",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { idEstablecimiento: value },
			dataType: "json",
			type: "post",
			success: function(data){
				$("#salas").empty();
				for(var i=0; i<data.length; i++){
					var option="<option value='"+data[i].id+"'>"+data[i].nombre+"</option>";
					$("#salas").append(option);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	});

	$("#tipo-procedencia").on("change", function(){
		var value=$(this).val();
		if(value == 2){
			$(".ocultar").addClass("hidden");
			$(".estabOculto").show();
			$("#input_procedencia_privado").val("");
			$("#input_procedencia_establecimiento_privado").prop("required",false);
			$(".estabPrivadoOculto").hide();
		}
		else if(value == 7){
			$(".ocultar").addClass("hidden");
			$(".estabPrivadoOculto").show();
			$(".estabOculto").hide();
			$("#input_procedencia").val("");
			$("#input_procedencia_establecimiento_privado").prop("required",true);
		}else{
			$(".estabOculto").hide();
			$("#input_procedencia").val("");
			$("#input_procedencia_privado").val("");
			$("#input_procedencia_establecimiento_privado").prop("required",false);
			$(".estabPrivadoOculto").hide();
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

	/*@include('General.jsGeneral')*/

	$("#reconvertir").on("click", function(){
		var servicio=$("#servicios").val();
		var sala=$("#salas").val();
		var cama=$("#camaReserva").val();
		$.ajax({
			url: "reconvertir",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data:{ sala: sala, servicio: servicio, cama: cama },
			type: "post",
			dataType: "json",
			success: function(data){
				//modal reconvertir con aceptar o cancelar
				$("#modalReconvertir").hide();
				if(data.exito){
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
	});

	$("#fechaFallecimiento").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});

	@include('General.jsEstablecimientos');

	$("#fechaEgreso").on("dp.change keyup", function(){
		$('#formDarAlta').bootstrapValidator('revalidateField', 'fechaEgreso');
	});

	$("#fechaIngreso").on("dp.change", function(){
		//alert("change");
		/* var fecha = $("#fechaIngreso").data("DateTimePicker");
		var mom = fecha.date().format("YYYY-MM-DD HH:mm:ss");
		fecha.hide();
		$(this).blur();
		$.ajax({
			url: "selector_categorizacion",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {inicio: mom, fin: moment(window._gc_now).format("YYYY-MM-DD HH:mm:ss")},
			type: "post",
			success: function (data) {
				var html = '';
				for (var i = 0; i < data.length; i++) {
					html += data[i];
				}
				$("#categorizacionesIngreso").html(html);
			}
		}); */
		$("#asignarCamasForm").bootstrapValidator("revalidateField", "fechaIngreso");
	});

	$('#modalAsignacionCama').on('hidden.bs.modal', function() {
		$('#asignarCamasForm').bootstrapValidator('resetForm', true);
	}).on("show.bs.modal", function(){
		var fecha = $("#fechaIngreso").data("DateTimePicker");
		//$('#asignarCamasForm').bootstrapValidator('resetForm', true);
		$("input[name=extranjero][value='no']").prop("checked",false);
		$("input[name=extranjero][value='yes']").prop("checked",false);

		$("input[name=caso_social][value='no']").prop("checked",true);
		$("input[name=caso_social][value='yes']").prop("checked",false);

		$("input[name=rn][value='no']").prop("checked",false);
		$("input[name=rn][value='yes']").prop("checked",false);
		window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');

			 $("#fechaIngreso").focus(function(){
				fecha . date(window . _gc_now);
				});
		fecha.minDate(moment(window._gc_now).subtract(1, "days").startOf('day'));
		fecha.maxDate(moment(window._gc_now).add(2, 'days'));
	});


	$("#bloquear").on("click", function(){
		bloquearCama();
	});

	$("#renovar").on("click", function(){
		$.ajax({
			url: "{{URL::to('/')}}/renovar",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			data: {idCaso: $("#casoRenovar").val(), hora: $("#horaRenovar").val()},
			success: function(data){
				$("#modalRenovar").modal("hide");
				swalExito.fire({
				title: 'Exito!',
				text: "La reserva ha sido renovada",
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
	});

	$("#especialidades").on("change", function(){
		var largo= $("#especialidades").children(':selected').length;
		$("#especialidades_item").val(largo);
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'especialidades_item');
	});

	$( "#riesgo" ).on("change", function() {
		$('#asignarCamasForm').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', false);
		if($("#riesgo").val() == "D2" || $("#riesgo").val() == "D3"){
			$('#asignarCamasForm').bootstrapValidator('enableFieldValidators', 'comentario-riesgo', true);
		}
	});

	$("#rut").on("keyup", function() {
		if($("#rut").val() != ''){
			$('#asignarCamasForm').bootstrapValidator('revalidateField', 'dv');
		}
		if($("#rut").val() != ''  && $("#dv").val() != ''){
			$('#asignarCamasForm').bootstrapValidator('revalidateField', 'dv');
		}
		if($("#rut").val() == ''  && $("#dv").val() != ''){
			$('#asignarCamasForm').bootstrapValidator('revalidateField', 'dv');
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

	//solucion a problema en que a veces no valida al apretar en el boton
	$("#btnAceptarAsingar").on("click", function() {
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'diagnosticos[]');
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'diagnostico');
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'tipo-procedencia');
		var checkeadorn = $("input[name='rn']:checked").val();
		if(checkeadorn === undefined){
			$("input[name=rn][value='no']").change();
		}
		var checkeadorex = $("input[name='extranjero']:checked").val();
		if(checkeadorex === undefined){
			$("input[name=extranjero][value='no']").change();
		}
	});

	function reValidacionRut(){
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'dv');
			// $('#asignarCamasForm')[0].reset();
			limpiarDatosPersonales();
			buscarComuna();
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');

	}

	function limpiarDatosPersonales(){
		$("#rango").val('');
		$("#fechaNac").val('');
		$("#rut").val('');
		$("#dv").val('');
		$("#nombre").val('');
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'nombre');
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

	$("input[name='cambiarMedico']").on("change", function(){
		var value=$(this).val();
		if(value == "si"){
			$("#medicoAltaLC").val("");
			$("#id_medico").val("");
			$(".medicoAltaOculto").show();
			$(".medicoDioAltaOcultar").hide();
		}
		else{
			$("#medicoAltaLC").val("");
			$("#id_medico").val("");
			$(".medicoDioAltaOcultar").show();
			$(".medicoAltaOculto").hide();
		}
	});

	$("#asignarCamasForm").bootstrapValidator({
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		fields: {
			dau: {
				validators:{
					remote:{
						message: "DAU ya existe",
						url: "{{ URL::to("/validarDau") }}"
					}
				}
			},
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
			/*fichaClinica: {
				validators:{
					callback: {
						callback: function(value, validator, $field){
							var ficha = $("#fichaClinica");

							if (Auth::user()->tipo == "gestion_clinica")
								if(ficha.val() === ''){
									return {valid: false, message: "La ficha clínica es obligatoria"};
								}
							endif
							return true;
						}
					}
				}
			},
			dau:{

				validators:{
					callback: {
						callback: function(value, validator, $field){
							var dau = $("#dau");

							if (Auth::user()->tipo == "usuario")
								if(dau.val() === ''){
									return {valid: false, message: "El DAU es obligatorio"};
								}
							endif
							return true;

						}
					}
				}
			},*/
			fechaNac: {
				validators:{
					/* notEmpty: {
						message: 'El fecha de nacimiento es obligatoria'
					}, */
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
					}/* ,descomentar
					callback: {
						callback: function(value, validator, $field){
							console.log("click", click);
							var seleccionado = $("[name='hidden_diagnosticos[]']").val();
							console.log("hola: ", seleccionado);
							if(seleccionado != ""){
								return true;
							}else{
								return {valid: false, message: "Debe seleccionar un diagnóstico haciendo click en la lista"};
							}
						}
					}  */
				}
			},
			'diagnostico': {
				validators: {
					notEmpty: {
						message: 'Debe ingresar el comentario'
					}
				}
			},
			"tipo-procedencia": {
				validators:{
					regexp: {
						regexp: /[12345678]/,
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
			'input-procedencia': {
				validators: {
					notEmpty: {
						message: 'Debe especificar la procedencia'
					}
				}
			},
			'input_procedencia_privado': {
				validators: {
					notEmpty: {
						message: 'Debe especificar la procedencia'
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

							var cantidad = $("#especialidades_item").val();

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
			recibe_visitas: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								console.log(value);
								if(value == "no"){
									
									$(".div-recibe-visitas").addClass("hidden");
									// $("#cantidad_personas").val("");
									// $("#cantidad_horas").val("");
								}else{
									$(".div-recibe-visitas").removeClass("hidden");
									// $("#cantidad_personas").val("");
									// $("#cantidad_horas").val("");
								}
								return true;
							}
						}
					}
				},
			cantidad_personas: {
				trigger: 'change keyup',
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
				trigger: 'change keyup',
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
			},
			rn: {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Por favor introduce un valor'
					}
				}
			},
			extranjero: {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Por favor introduce un valor'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		bootbox.confirm("<h4>¿Está seguro de solicitar la cama?</h4>", function(result) {
			$("#btnAceptarAsingar").attr('disabled', 'disabled');
		
			if (result) {
				// swalCargando.fire({});
				if(!enviado){
					enviado = true;
				}
				else{
					return false;
				}
				var $form = $(evt.target);
				// swalCargando.fire({});
				swalCargando.fire({});
				$.ajax({
					url: $form .prop("action"),
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					dataType: "json",
					data: $form .serialize(),
					async: false,
					success: function(data){
						// swalCargando.close();
						Swal.hideLoading();
						swalCargando.close();
						Swal.hideLoading();
						enviado=true;
						if(data.derivacion){
							swalExito.fire({
							title: 'Exito!',
							text:data.msg,
								didOpen: function() {
								setTimeout(function() {
									location.href = "{{URL::to('derivaciones/recibidas')}}";
								}, 2000)
							},
							});
							
						}
						if(data.exito){
							swalExito.fire({
							title: 'Exito!',
							text: data.exito,
							didOpen: function() {
								setTimeout(function() {
									$form[0].reset();
									enviado = false;
									location.reload();
								}, 2000)
							},
							});
						}
						if(data.error){
							swalError.fire({
							title: 'Error',
							text:data.error
							}).then(function(result) {
							if (result.isDenied) {
								enviado = false;
									$form[0].reset();
									location.reload();
							}
							});
							console.log(data.error);
						}
					},
					error: function(error){
						// swalCargando.close();
						Swal.hideLoading();
						swalCargando.close();
						Swal.hideLoading();
						console.log(error);
					}
				});
			}else{
				$('#btnAceptarAsingar').removeAttr("disabled");
			}
		});



	});


	@if(Auth::user()->tipo != TipoUsuario::USUARIO)
		/* console.log("entro igual"); */
		$("#formDesbloquear").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				motivo: {
					validators:{
						notEmpty: {
							message: 'El motivo es obligatorio'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
			var $form = $(evt.target);
			$.ajax({
				url: "desbloquearCama",
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
							location . reload();
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
		}).on('error.field.bv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
				$icon   = $parent.find('.form-control-feedback[data-bv-icon-for="' + data.field + '"]'),
				title   = $icon.data('bs.tooltip').getTitle();

			$icon.tooltip('destroy').tooltip({
				html: true,
				placement: 'right',
				title: title,
				container: 'body'
			});
		});
	@endif

	$("#tipo").on("change", function(){
		if($(this).val() == "ingresar"){
			$("#divHora").hide();
			$("#divMotivo").hide();
			$("#divFechaIngreso").show();
			$("#fechaIngreso").prop("disabled", false);
			$('#horas').prop( "disabled", true );
			$('#motivoC').prop( "disabled", false );
			$('#horas').removeAttr('required');
			$('#motivoC').removeAttr('required');
		}
		if($(this).val() == "reservar"){
			$("#divHora").show();
			$("#divMotivo").show();
			$("#divFechaIngreso").hide();
			$("#fechaIngreso").prop("disabled", true);
			$('#horas').prop( "disabled", false );
			$('#motivoC').prop( "disabled", false );
			$('#horas').prop('required',true);
			$('#motivoC').prop( "required", true );
		}
	});

	$("#salaReserva").val("-1");
	$("#camaReserva").val("-1");

	/*$("#fechaNac").datepicker({
		autoclose: true,
		language: "es",
		format: "dd-mm-yyyy",
		todayHighlight: true,
		endDate: "+0d"
	}).on("changeDate", function(){
		$('#asignarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
	});*/



	$("#gestion").collapse();


	generarMapaCamas("mapaSalas", "{{$unidad}}");

	$("#dv_rn").on("change", function(){
		$("#formDarAlta").bootstrapValidator("revalidateField", "dv_rn");
	});
	$("#rut_rn").on("change", function(){
		$("#formDarAlta").bootstrapValidator("revalidateField", "dv_rn");
	});
	/*Con la variable elimina el error de que aparezcan 2 confirmaciones al momento de realizar el egreso del paciente, si se saca, debera buscarse el error que produce*/
	var liberar = 0;
	$("#formDarAlta").bootstrapValidator({
		excluded: [':disabled',':hidden'],
		fields: {
			ficha: {
				validators:{
					notEmpty: {
						message: 'El número de ficha es obligatorio'
					}
				}
			},
			rut_rn: {
				validators:{
					integer: {
							message: 'Debe ingresar solo números'
						},
					callback: {
						callback: function(value, validator, $field){
							/* $("#dv_rn").val(''); */
							return true;
						}
					}
				}
			},
			dv_rn: {
				validators:{
					regexp: {
						regexp: /([0-9]|k)/i,
						message: 'Dígito verificador no valido'
					},
					callback: {
						callback: function(value, validator, $field){
							var field_rut = $("#rut_rn");
							var dv = $("#dv_rn");
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
							return true;
						}
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
								casoLiberar: validator.getFieldElements('caso').val(),
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
			parto:{
				validators:{
					trigger: 'change keyup',
					notEmpty: {
						message: 'Debe seleccionar una opción'
					}
				}
			},
		}
	}).on('status.field.bv', function(e, data) {
		//data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault(evt);
		var $form = $(evt.target);
		if(liberar < 1){
			liberar++;
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
				liberar--;
				if(result){
					$.ajax({
						url: "{{ URL::to('/')}}/liberarCama",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						data: $form.serialize(),
						type: "post",
						dataType: "json",
						success: function(data){
							$("#modalAllta").modal("hide");
							if(data.exito){
								swalExito.fire({
								title: 'Exito!',
								text: data.exito,
								didOpen: function() {
									setTimeout(function() {
										$("#opcionesCamas").hide();
										$("#descripcionPaciente").hide();
										$("#modalAllta").on('hidden.bs.modal', function () {
											limpiarFormDatAlta();
										});
										generarMapaCamas("mapaSalas", "{{$unidad}}");
										traerCamasTemporales(true);
									}, 2000)
								},
						});
							
							}
							else{
								swalError.fire({
								title: 'Error',
								text:data.error
								});
								$("#modalAllta").on('hidden.bs.modal', function () {
									limpiarFormDatAlta();
								});
							}
						},
						error: function(error){
							swalError.fire({
							title: 'Error',
							text:"Error al liberar la cama"
							});
							console.log(error);
						}
					});
				}
			}
		});
	}});

	$("#modalAllta").on('hidden.bs.modal', function () {
		limpiarFormDatAlta();
	});

	$("#solicitar").on('click', function (){
		$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'ficha');
		$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
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
				swalError.fire({
				title: 'Error',
				text:"Error al ingresar plan de tratamiento"
				});
				console.log(error);
			}
		});
	});

	var enviar = false;
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
		console.log("enviar");
		if(!enviar){
			enviar = true;
		}else{
			return false;
		}
		$.ajax({
			url: "{{ URL::to('/')}}/acostarPaciente",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: $("#formVisitas").serialize(),
			type: "post",
			success: function(data){
				enviar = true;
				if (data.exito) {
					swalExito.fire({
						title: 'Exito!',
						text: "Se ha hospitalizado al paciente",
						didOpen: function() {
							setTimeout(function() {
								$("#opcionesCamas").hide();
								$(".descripcionPaciente").hide();
								generarMapaCamas("mapaSalas", "{{$unidad}}");
								enviar = false;
								$("#modalVisitas").modal("hide");
							}, 2000)
						},
					});
				}

				if (data.error) {
					swalError.fire({
						title: 'Error',
						text: data.error
						}).then(function(result) {
						if (result.isDenied) {
							$("#opcionesCamas").hide();
							$(".descripcionPaciente").hide();
							generarMapaCamas("mapaSalas", "{{$unidad}}");
							enviar = false;
							$("#modalVisitas").modal("hide");
						}
					});
				}
			},
			error: function(error){
				console.log("error: ", error);
				$("#modalVisitas").modal("hide");
			}
		});
	});
});

$(document).on('click', '.botonCerrar', function () {
	$("#descripcionPaciente").hide();
	$("#simbolo").show();
});

var mostrarOpciones=function(idEstablecimiento, idSala, idCama, idCaso){
	$("#opcionesCamas ol").empty();
	$("#opcionesCamas ol").append("<li><a class='cursor' onclick='liberar("+idEstablecimiento+", "+idSala+", "+idCama+","+idCaso+")'>Egreso</a></li>");
	$("#opcionesCamas ol").append("<li><a class='cursor' onclick='reasignar("+idEstablecimiento+", "+idSala+", "+idCama+","+idCaso+")'>Reasignar</a></li>");
	$("#opcionesCamas").show();
	document.getElementById('opcionesCamas').scrollIntoView();
}

var getPaciente = function(id, idSala, idCama, idCaso, idPaciente, elem) {
	// console.log($(elem));
	// console.log("elemento : ", elem);
	divMapaxy = $(elem).parent();
	//console.log($(elem).parent());
	$("#idcamacambio").val(idCama);
	$("#descripcionPaciente").hide();
	// swalCargando.fire({});
	swalCargando.fire({title:'Cargando datos del paciente'});
	$("#simbolo").hide();
	/*marcar en rojo la cama que hace click*/
	$(".cursor").css("color","#428bca");
	$(".divContenedorCama").css("border","");

	$(elem).css("color","red");
	$(elem).css("font-weight", "bold");


	$(divMapaxy).css("border", "2px solid #1e9966");
	$(divMapaxy).css("border-radius", "15px");
	
	$("#id_caso_historial_visitas").val(idCaso);

	var ubicacion = "mapa_de_camas";
	var color_cama = $(divMapaxy).children().attr("data-cama");
	$.ajax({
		url: "{{URL::to('/')}}/getPaciente",
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		type: "post",
		data: {id: id, unidad: "{{$unidad}}",idCaso:idCaso, idCama: idCama, ubicacion: ubicacion, color_cama: color_cama},
		dataType: "json",
		success: function(data) {
			if (data.info) {
				swalCargando.close();
				Swal.hideLoading();
				swalInfo2.fire({
					title: 'Información',
					text: data.info
				})
			}else if(data.warning){
				swalCargando.close();
				Swal.hideLoading();
				if (mostrar_error_get_paciente < 1) {
					swalInfo2.fire({
						title: 'Información',
						text: data.warning
					}).then(function(result) {			
						//para evitar que se haga el aviso del paciente que se movio de la cama, se hace esta restriccion para que solo se muestre 1 vez
						mostrar_error_get_paciente = mostrar_error_get_paciente+1;			
						generarMapaCamas("mapaSalas", "{{$unidad}}");
					});	
				}
			}else if(data.error){
				swalError.fire({
					title: 'Información',
					text: data.error
				}).then(function(result) {
					generarMapaCamas("mapaSalas", "{{$unidad}}");
				});
			}else if (data.nombre != "NULL" || data.apellidoM != "NULL" || data.apellidoP != "NULL"){
				$("#ficha").val(data.ficha);

				if(data.nombre_medico_alta && data.id_medico_alta){
					$("#medicoDioAlta").val(data.nombre_medico_alta);
					$("#id_medicoDioAlta").val(data.id_medico_alta);
				}else{
					//$("#medicoAlta").val("no tiene info de medico");
				}

				if (data.riesgo != null) {
					var riesgo = data.riesgo.categoria == null? "No especificado":data.riesgo.categoria;
					if(data.riesgo.urgencia == true){
						riesgo = riesgo + " (Urgencia)";
					}
				}else{
					riesgo = "No especificado";
				}
				var usuario_ingreso = data.usuario_ingreso == null? "No especificado":data.usuario_ingreso;
				var dieta  = data.dieta  == null? "No especificado":data.dieta;

				@if( Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get('usuario')->tipo == 'matrona_neonatologia' || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO ||Session::get("usuario")->tipo === TipoUsuario::CDT)
					var nombre= "Ver / Editar";
				@else
					var nombre = "Ver";
				@endif

				var href="paciente/editar/"+idPaciente;
				var editar = '<a href="{{URL::to('')}}/'+href+'">Editar paciente</a>'
				var caso_social = data.caso_social == null? "No especificado" : data.caso_social ? "Sí" : "No";
				var extranjero = data.extranjero == null? "No especificado" : data.extranjero ? "Sí" : "No";
				var num_pasaporte = (data.n_pasaporte) ? data.n_pasaporte : "No especificado";
				var patogeno=data.patogeno;
				var esIaas=data.esIaas;
				var rn=data.rn;
				var rut_madre=(data.rut_madre) ? data.rut_madre : "No especificado";
				var especialidad=data.especialidad;
				var genero = (data.genero == "femenino") ? 1 : 0;


				nombreModal = data.nombre + " " + data.apellidoP + " " + data.apellidoM;
				//sessionStorage.setItem("nombreTituloModal",nombreModal);
				$(".nombreModal").html(nombreModal);
				var html = "<button class='botonCerrar btn btn-danger' style='position:fixed; width:19%;      height: 30px; z-index: 99;'><b>CERRAR  </b><span class='glyphicon glyphicon-remove' style=' height: 30px; z-index: 1000;'></span></button> <ol class='list-group'>";
				html += "<li class='list-group-item' style='margin-top: 30px;'> <label class='control-label'>Ficha clínica: </label>&nbsp;&nbsp;" + data.ficha + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>DAU: </label>&nbsp;&nbsp;" + data.dau + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Run: </label>&nbsp;&nbsp;" + data.rut + "</li>";
				if(rn!='null'){
					html += "<li class='list-group-item'> <label class='control-label'>Rn: </label>&nbsp;&nbsp;" + rn + "</li>";
					html += "<li class='list-group-item'> <label class='control-label'>Run madre: </label>&nbsp;&nbsp;" + rut_madre + "</li>";
				}
				html += "<li class='list-group-item'> <label class='control-label'>Extranjero: </label>&nbsp;&nbsp;" + extranjero + "</li>";
				if(extranjero == "Sí"){
					html += "<li class='list-group-item'> <label class='control-label'>Número Pasaporte: </label>&nbsp;&nbsp;" + num_pasaporte + "</li>";
				}
				html += "<li class='list-group-item'> <label class='control-label'>Nombre: </label>&nbsp;&nbsp;" + data.nombre + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Apellido paterno: </label>&nbsp;&nbsp;" + data.apellidoP + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Apellido materno: </label>&nbsp;&nbsp;" + data.apellidoM + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Genero: </label>&nbsp;&nbsp;" + data.genero + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Teléfono: </label>&nbsp;&nbsp;" + data.telefono + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Nombre Social: </label>&nbsp;&nbsp;" + data.nombreSocial + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Fecha nacimiento: </label>&nbsp;&nbsp;" + data.fecha + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Edad: </label>&nbsp;&nbsp;" + data.edad + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Previsión: </label>&nbsp;&nbsp;" + data.prevision + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Fecha de solicitud: </label>&nbsp;&nbsp;" + data.fecha_ingreso_historial + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Fecha de hospitalización: </label>&nbsp;&nbsp;" + data.fecha_ingreso_real + "</li>";
				if(data.fecha_salida_urg!=""){
					html += "<li class='list-group-item'> <label class='control-label'>Fecha de salida urgencia: </label>&nbsp;&nbsp;" + data.fecha_salida_urg + "</li>";
				}
				html += "<li class='list-group-item'> <label class='control-label'>Caso social: </label>&nbsp;&nbsp;" + caso_social + "</li>";
				@if(Session::get('usuario')->tipo != TipoUsuario::CENSO)
					html += "<li class='list-group-item'> <label class='control-label'>Último diagnóstico: </label>&nbsp;&nbsp;" + /*data.id_cie_10*/data.diagnostico.id_cie_10 + " " + data.diagnostico.nombre + "</li>";
					html += "<li class='list-group-item'> <label class='control-label'>Comentario diagnóstico: </label>&nbsp;&nbsp;" + data.comentario_diagnostico + "</li>";
				@endif
				/* html += "<li class='list-group-item'> <label class='control-label'>Comentario lista: </label>&nbsp;&nbsp;" + data.comentario_lista + "</li>"; */
				html +="<li class='list-group-item'> <label class='control-label'>Sugerencia área funcional: </label>&nbsp;&nbsp;" + data.sugerencia_areaf + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Servicio a cargo: </label>&nbsp;&nbsp;" + data.servicio + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Área Funcional a cargo: </label>&nbsp;&nbsp;" + data.area + "</li>";
				html += "<li class='list-group-item'> <label class='control-label'>Requiere aislamiento: </label>&nbsp;&nbsp;" + data.req_aislamiento + "</li>";

				html += "<li class='list-group-item'> <label class='control-label'>Riesgo: </label>&nbsp;&nbsp;" + riesgo + "</li>";
				if(data.cant_examenes > 0){
					html += "<li class='list-group-item'> <label class='control-label'>Exámenes pendientes: </label>&nbsp;&nbsp;<span style='color: red;' id='numPendientes'>" + data.cant_examenes + "</span></li>";
				}
				html += "<li class='list-group-item'> <label class='control-label'>¿Quién solicito cama?: </label>&nbsp;&nbsp;<br>" + usuario_ingreso + "</li>";
				if (data.asignoCama != null) {
					html += "<li class='list-group-item'> <label class='control-label'>¿Quién asigno la cama?: </label>&nbsp;&nbsp;<br>" + data.asignoCama + "</li>";
				}

				if (data.hospitalizoPaciente != null) {
					html += "<li class='list-group-item'> <label class='control-label'>¿Quién hospitalizo al paciente?: </label>&nbsp;&nbsp;<br>" + data.hospitalizoPaciente + "</li>";
				}

				if(especialidad!='' && especialidad!=null)html += "<li class='list-group-item'> <label class='control-label'>Especialidad: </label>&nbsp;&nbsp;" + especialidad + "</li>";
				@if($permisos_establecimiento->ve_dietas)
					html += "<li class='list-group-item'> <label class='control-label'>Dieta actual: </label>&nbsp;&nbsp;" + dieta + "</li>";
				@endif
				if(patogeno)
				{
					if(data.aislamiento==""){
						aislamiento = "No especificado";
					}else{
						aislamiento = data.aislamiento;
					}
					html+= "<br>";
					html+="<fieldset>";
					html+="<legend>IAAS</legend>";

					html+= "<li class='list-group-item'> <label class='control-label'>Aislamiento: </label>&nbsp;&nbsp;" + aislamiento + "\
						<br><label class='control-label'>Tiempo desde la notificación: </label>&nbsp;&nbsp;" + data.dias_aislamiento + "</li>";
					html+="</fieldset>";
				}

				html+= "</ol>";
				html+="<fieldset>";
				html+="<legend>Opciones</legend>";
				html+="<ol>";

				@if(Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'master'   || Session::get('usuario')->tipo == 'master_ss')
					if(data.existeSolicitud == false){
						html+="<li> <a class='cursor' onclick='confirmarTI("+idCaso+","+data.existeSolicitud+")'>Confirmar traslado interno</a> </li>";
					}
					else{
						console.log("el valor de existe solicitud es"+data.existeSolicitud);
						if(data.fecha_ingreso_real == ""){
							if(data.motivo === "alta en evaluación extrahospitalaria" && data.tiene_fecha_extrahospitalaria != null){
								html+="<li> <a class='cursor' onclick='desextrahospitalizar("+idCaso+")'>Desextrahospitalizar paciente</a> </li>";
							}
							if(data.existeSolicitud == "null"){
								html+="<li> <a class='cursor' onclick='acostar("+idCaso+","+data.existeSolicitud+")'>Hospitalizar paciente</a> </li>";
							}
						}


						else{
							if(data.existeSolicitud !== true){
								if(data.motivo === "alta en evaluación extrahospitalaria" && data.tiene_fecha_extrahospitalaria != null){
									html+="<li> <a class='cursor' onclick='desextrahospitalizar("+idCaso+")'>Desextrahospitalizar paciente</a> </li>";
								}
								html+="<li> <a class='cursor' onclick='regresarTransito("+idCaso+")'>Regresar a lista transito</a> </li>";
							}

						}
					}
				@endif

				@if(Session::get('usuario')->tipo == TipoUsuario::ADMINCOMERCIAL
					|| Session::get('usuario')->tipo == TipoUsuario::MASTER
					|| Session::get('usuario')->tipo == TipoUsuario::MASTERSS
					/* || Session::get('usuario')->tipo ==  TipoUsuario::GESTION_CLINICA
					|| Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P */)
					html+="<li> <a class='cursor' onclick='boletinPago("+idCaso+")'>Boletín de Pago</a> </li>";
				@endif

				@if(Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get('usuario')->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::IAAS)
					html+="<li> <a class='cursor' onclick='infecciones("+idCaso+")'>Notificar Infección Intrahospitalaria</a> </li>";
					html+="<li> <a id=\"hola\" class='cursor' onclick='VerInfecciones("+idCaso+")'>Ver / Editar Infección Intrahospitalaria</a> </li>";
				@endif

				@if(Session::get('usuario')->tipo != TipoUsuario::ADMINCOMERCIAL && Session::get("usuario")->tipo != TipoUsuario::IAAS)
					/* if(Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss' || Session::get('usuario')->tipo == 'matrona_neonatologia' || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
						if(data.fecha_ingreso_real == ""){
							html+="<li> <a class='cursor' onclick='acostar("+idCaso+")'>Hospitalizar paciente</a> </li>";
						}else{
							html+="<li> <a class='cursor' onclick='regresarTransito("+idCaso+")'>Regresar a lista transito</a> </li>";
						}
					endif */

					@if(Session::get("usuario")->tipo !== TipoUsuario::CENSO || Session::get('usuario')->tipo != TipoUsuario::ADMINCOMERCIAL || Session::get('usuario')->tipo == 'matrona_neonatologia')
						html+="<li> <a class='cursor' onclick='verDiagnosticos("+idCaso+")'>"+nombre+" diagnósticos</a> </li>";
					@endif
					// Solo se peude categorizar cuando esté tenga fecha de ingreso y cuando no tenga restriccion de 8 hrs como minimo en el hospital
					/* if(data.fecha_ingreso_real != "" && data.restriccion == false){
						html+="<li> <a class='cursor' onclick='verDetalles("+idCaso+")'>"+nombre+" riesgo</a> </li>";
					} */

					@if(Session::get('usuario')->tipo == 'matrona_neonatologia' && $sub_categoria == 1)
						html+="<li> <a class='cursor' onclick='verFormulariosGinecologia("+idCaso+")'>Formularios de ginecología</a></li>";
					@endif
					if (data.fecha_ingreso_real != "") {
						if (data.t_estadia_restriccion == true) {
							if (data.restriccion == false) {
								html+="<li> <a class='cursor' onclick='verDetalles("+idCaso+")'>Ver / Editar riesgo</a> </li>";
							}else{
								html+="<li> <a class='cursor' onclick='verDetalles("+idCaso+")'>Ver riesgo</a> </li>";
							}
						}
					}
					@if(Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
						var dieta= "Ver / Editar";
					@else
						var dieta = "Ver";
					@endif

					@if(Session::get('usuario')->tipo != 'matrona_neonatologia' && Session::get('usuario')->tipo != TipoUsuario::DIRECTOR && Session::get('usuario')->tipo != TipoUsuario::MEDICO_JEFE_DE_SERVICIO  && Session::get("usuario")->tipo != TipoUsuario::CDT && Session::get("usuario")->tipo != TipoUsuario::MEDICO)
						html+="<li> <a class='cursor' onclick='verDetallesDieta("+idCaso+")'>"+dieta+" dieta</a> </li>";
					@endif

					@if(Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS
						|| Session::get("usuario")->tipo == TipoUsuario::MEDICO)
						html+="<li><a class='cursor' href='{{URL::to('')}}/gestionEnfermeria/"+idCaso+"'>Registro clínico de enfermería</a></li>";
						html+="<li><a class='cursor' href='{{URL::to('')}}/gestionMedica/"+data.idCasoEncrypt+"'>Gestión medica</a></li>";
					@endif

					@if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P  || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get('usuario')->tipo == 'matrona_neonatologia' || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO ||Session::get("usuario")->tipo === TipoUsuario::CDT)
					//  || Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO
						
						html+="<li> <a class='cursor' onclick='liberar("+idSala+", "+idCama+","+idCaso+", "+genero+")'>Egreso</a> </li>";
						@if(Session::get("usuario")->tipo != TipoUsuario::MEDICO_JEFE_DE_SERVICIO  && Session::get("usuario")->tipo != TipoUsuario::CDT)
							html+="<li> <a class='cursor' onclick='enviarPreAlta("+idSala+", "+idCama+","+idCaso+", "+genero+")'>Enviar a Pre-alta</a> </li>";
							html+="<li> <a class='cursor' onclick='reasignar("+idSala+", "+idCama+","+idCaso+")'>Traslado interno</a> </li>";
						@endif
						@if( Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::MASTER  || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::ADMIN
						 // || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO
						)
							if(data.existeSolicitud !==false )
							html+="<li> <a class='cursor' onclick='solicitudTrasladoInterno("+idSala+", "+idCama+","+idCaso+")'>Solicitar traslado interno</a> </li>";
						@endif
					@endif
					
					@if($cama_temporal
						&& (
							Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA
							|| Session::get("usuario")->tipo == TipoUsuario::MASTER
							|| Session::get("usuario")->tipo == TipoUsuario::MASTERSS
							|| Session::get("usuario")->tipo == TipoUsuario::ADMIN
						)
					)
						html += "<li><a class='cursor' onclick='moverACamaTemporal(" + idCaso + ")'>Traslado a cama volante</a></li>";
					@endif


					@if(0)
					/* if(Session::get("usuario")->tipo == TipoUsuario::ADMINIAAS ) */
						html+="<li> <a class='cursor' onclick='infecciones("+idCaso+")'>Notificar Infección Intrahospitalaria</a> </li>";
						html+="<li> <a id=\"hola\" class='cursor' onclick='VerInfecciones("+idCaso+")'>Ver / Editar Infección Intrahospitalaria</a> </li>";
					@endif
					@if(Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::IAAS
						&& Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS && Session::get("usuario")->tipo !== TipoUsuario::CENSO && Session::get("usuario")->tipo !== TipoUsuario::CDT
						&& Session::get("usuario")->tipo !== TipoUsuario::MEDICO
					)
						/* html+="<li> <a class='cursor' onclick='validarTraslado("+idCaso+")'>Traslado externo</a> </li>"; */
						html+="<li>"+editar+"</li>";
					@endif
					@if(0)
					/* if(Session::get("usuario")->tipo == TipoUsuario::IAAS || Session::get("usuario")->tipo == TipoUsuario::MASTER) */
						html+="<li> <a class='cursor' onclick='infecciones("+idCaso+")'>Notificar Infección Intrahospitalaria</a> </li>";
						html+="<li> <a id=\"hola\" class='cursor' onclick='VerInfecciones("+idCaso+")'>Ver / Editar Infección Intrahospitalaria</a> </li>";
					@endif

					@if(Session::get('usuario')->tipo != 'matrona_neonatologia')
					html+="<li> <a class='cursor' onclick='historial("+idCaso+")'>Datos históricos del paciente</a> </li>";
					@endif

					@if(Session::get('usuario')->tipo != 'medico')
					html+="<li> <a class='cursor' onclick='examenes("+idCaso+")'>Exámenes / Estudios / Procedimientos</a> </li>";
					@endif

					
					/*OPTIMIZACION*/
					/* if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA )
						html+="<li> <a class='cursor' onclick='buscarMejorCama("+idPaciente+","+idCaso+")'>Encontrar cama</a> </li>";
					endif */

					@if(Session::get('usuario')->tipo != 'director' && Session::get('usuario')->tipo != 'medico_jefe_servicio' && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS)
						@if(Session::get('usuario')->tipo != 'matrona_neonatologia')
						//html+="<li> <a class='cursor' onclick='plan("+id+")'>Plan de tratamiento</a></li>";
						@endif
					@endif

					@if(Session::get('usuario')->tipo != 'matrona_neonatologia')
						//html+="<li> <a class='cursor' onclick='getPlanTratamiento("+id+")'>Ver historico del plan de tratamiento</a></li>";
					@endif

					@if(Session::get('usuario')->tipo == TipoUsuario::ADMIN ||
					Session::get('usuario')->tipo == TipoUsuario::MASTER ||
					Session::get('usuario')->tipo == TipoUsuario::MASTERSS || Session::get('usuario')->tipo == 'matrona_neonatologia')
						if(data.derivacion == false){
							html+="<li> <a class='cursor' onclick='enviarDerivado("+idCaso+")'>Enviar a Derivados</a> </li>";
							//html+="<li> <a class='cursor' onclick='documentosDerivacion("+idSala+", "+idCama+","+idCaso+")'>Documentos derivación</a> </li>";
							//html+="<li> <a class='cursor' onclick='quitarDerivado("+data.idLista+")'>Quitar de Derivados</a> </li>";
						}
					@endif

					@if(Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss' || Session::get('usuario')->tipo == 'matrona_neonatologia' || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
					if(data.pabellon == "si"){
						html+="<li> <a class='cursor' onclick='quitarPabellon("+idCaso+")'>Sacar de Pabellón </a> </li>";
					}
					if(data.pabellon == "no"){
						html+="<li> <a class='cursor' onclick='enviarPabellon("+idCaso+")'>Enviar a Pabellón </a> </li>";
					}
					@endif
					@if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P  || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
						html+="<li><a class='cursor' href='{{URL::to('')}}/formularios/"+idCaso+"'>Formularios</a></li>";
					@endif

					@if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get('usuario')->tipo == 'matrona_neonatologia')
						html+="<li><a href='#modalDescripcion' data-toggle='modal' id='DCama' data-dismiss='modal'>Descripcion Cama</a></li>";
					@endif
					@if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA)
						html+="<li><a class='cursor' onclick='verHistorialVisitas("+idCaso+")'>Historial de visitas</a></li>";
					@endif
					// @if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P  || Session::get("usuario")->tipo == TipoUsuario::MASTER)
					// 	html += "<li><a class='cursor' href='{{URL::to('')}}/formulario/"+idCaso+"'>Formulario Escala Evaluación Riesgo de Ulceras por Presión</li>";
					// @endif

					var href="derivacionPaciente/"+idCaso;
					//var editar='{{ HTML::link("'+href+'", "Editar paciente") }}';
					var derivar = '<a href="{{URL::to('')}}/'+href+'">Derivar Paciente</a>'
					/*HASTA AQUI*/

					/* AÑADIR PUNTO 9.- SOLICITAR AMBULANCIA*/
					//html+="<li> <a class='cursor' onclick='solicitarAmbulancia("+idPaciente+","+idCaso+")'>Solicitar Ambulancia</a> </li>";
					/*HASTA AQUI*/

					var href_egreso="paciente/egreso/"+idCaso;

					$( "#fichaEgreso" ).html( '<a href="{{URL::to('')}}/'+href_egreso+'" class="btn btn-primary" type="button">Ver boletín de egreso</a>' );
				@endif

				html+="</ol>";
				html+="</fieldset>";

				$("#descripcionPaciente").html(html);
				cargarDatosEdicionVisitas(null,"","","");
				cargarDatosEdicionVisitasSoloLectura("true",1,"");
				alternarEdicionVisitas(false);
				swalCargando.close();
				Swal.hideLoading();
				$("#descripcionPaciente").show();
				//MODAL PARA DESCRIPCION DEL PQACIENTE
				/*
				dialog=bootbox.dialog({
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
				});
				*/
			}else{
				swalCargando.close();
				Swal.hideLoading();

				swalInfo2.fire({
					title: 'Información',
					text: "Informacion del paciente no encontrados"
				})
			}

			
		},
		error: function(error) {
			swalCargando.close();
			Swal.hideLoading();
			$("#descripcionPaciente").show();
			console.log(error);
		}
	});
};


var getPlanTratamiento = function(id) {
	$.ajax({
		url: "{{URL::to('/')}}/getPlanTratamiento",
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		type: "post",
		data: {id: id},
		dataType: "json",
		success: function(data) {
			var html="";
			var detalle;
			if(data.detalle.length<=0){
				//alert("No hay registros");
				swalInfo2.fire({
				title: 'Información',
				text:"No hay registros"
				});
			}
			else{
				for(i=0;i<data.detalle.length;i++){
					detalle=data.detalle[i];
					html+="<fieldset>";
					//html += "<pre class='list-group-item'>" + detalle + "</pre>";
					html += "<textarea class='form-control' readonly>"+detalle+"</textarea>"
					html+="<br>";
					html+="</fieldset>";
				}
				dialog=bootbox.dialog({
					message: html,
					title: "Histórico Plan de tratamiento",
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

var generarMapaCamas=function(mapaDiv, unidad){

	$.ajax({
		url: unidad+"/getCamas",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {unidad: unidad},
		dataType: "json",
		type: "post",
		success: function(data){
			crearMapaCamas(mapaDiv, data);
			@if($caso_id && $cama && $sala && $paciente)
				/* marcarCama({{$sala}}, {{$cama}}, true); */
				//console.log("esto?", document.getElementsByClassName('{{$paciente}}').text() );
				getPaciente({{$paciente}}, {{$sala}}, {{$cama}}, {{$caso_id}}, {{$paciente}}, document.getElementsByClassName('{{$paciente}}') );
			@endif
		},
		error: function(error){
			console.log(error);
		}
	});

}



var intercambiar = function(idCaso, idCasoOriginal){
	//dialog.modal("hide");

	if(idCaso == idCasoOriginal){
		swalInfo.fire({
		title: 'Información',
		text:"La cama de origen no puede ser igual a la cama de destino"
		});
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
						url: "intercambiar",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						data: {"idCaso": idCaso, "idCasoOriginal": idCasoOriginal},
						type: "post",
						success: function(data){
							//generarMapaCamas("mapaSalas", "{{$unidad}}");
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

var obtenerCamasLista=function(){
	$.ajax({
		url: "{{$unidad}}/obtenerCamasLista",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {unidad: "{{$unidad}}"},
		dataType: "json",
		type: "post",
		success: function(data){
			var tabla=$("#tablaVistaLista").dataTable();
			tabla.fnClearTable();
			if(data.length != 0) tabla.fnAddData(data);
		},
		error: function(error){
			console.log(error);
		}
	});
}

var generarMapaCamasDisponibles=function(mapaDiv, unidad){
	//alert("map");
	$.ajax({
		url: unidad+"/getCamasDisponiblesVerdes",
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
//Inicio Modulo Derivacion
var enviarDerivado=function(idCaso){
	$(".idCaso").val(idCaso);
	var fechaderivacion = $("#fechaDerivacion").data("DateTimePicker");
	var fechaida = $("#fechaIda").data("DateTimePicker");
	window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
	fechaderivacion.date(window._gc_now);
	fechaderivacion.maxDate(moment(window._gc_now))
	fechaida.date(window._gc_now);
	// fechaida.maxDate(moment(window._gc_now));
	$("#fecha_actual").text($("#fechaIda").val());

	var fecharescate = $("#fechaRescate").data("DateTimePicker");
	window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
	fechaida.date(window._gc_now);
	fecharescate.minDate(moment(window._gc_now));

	$.ajax({
		url: "datosParaDerivacion",
		data: {
			caso : idCaso
		},
		headers: {					         
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
		},
		type: "post",
		dataType: "json",
		success: function (data) {
			$("#nombreCompletoPaciente").text(data.nombreCompleto);
			$("#rutDv").text(data.rutDv);
			$("#grupoEtareo").text(data.grupoEtareo);
			$("#edadPaciente").text(data.fechaNacimiento + " (" + data.edad + ")");
			$("#fechaHospitalizacion").text(data.fechaHospitalizacion);
			$("#unidadFuncional").text(data.nombreUnidad);
			$("#idUnidadFuncional").val(data.idUnidad);
		},
		error: function (error) {
			console.log(error);
		}
	});

	$("#modalFormularioDerivado").modal();
}

var quitarDerivado=function(idLista){
	$("#idListaQuitarDerivados").val(idLista);
	$("#modalquitarDerivado").modal("show");
}

$("#formquitarDerivados").bootstrapValidator({
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
	$("#formquitarDerivados input[type='submit']").prop("disabled", false);
}).on("success.form.bv", function(evt){
	$("#formquitarDerivados input[type='submit']").prop("disabled", false);
	evt.preventDefault(evt);
	var $form = $(evt.target);
	bootbox.confirm({
		message: "<h4>¿Desea cerrar caso del módulo de derivación?</h4>",
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
					url: "../quitarDerivado",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					dataType: "json",
					data: $form .serialize(),
					success: function(data){
						$("#modalquitarDerivado").modal("hide");
						$("#btnQuitarDerivado").prop("disabled", false);
						//if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });
						if(data.exito) swalExito.fire({
										title: 'Exito!',
										text: data.exito,
										});
						$("#opcionesCamas").hide();
						$(".descripcionPaciente").hide();
						generarMapaCamas("mapaSalas", "{{$unidad}}");
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
//Fin Modulo Derivacion

var enviarPabellon=function(idCaso){
	$(".idCaso").val(idCaso);
	$("#comentario").val('');
	$("#modalEnviarPabellon").modal("show");
}

var quitarPabellon=function(idCaso){
	$(".idCaso").val(idCaso);
	$("#modalquitarPabellon").modal("show");
}

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
		message: "<h4>¿Está seguro de querer quitar al paciente de la lista de pabellón?</h4>",
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
					url: "quitarPabellonCamas",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					dataType: "json",
					data: $form .serialize(),
					success: function(data){
						$("#modalquitarPabellon").modal("hide");
						//if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });
						if(data.exito) swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						});
						$("#opcionesCamas").hide();
						$(".descripcionPaciente").hide();
						generarMapaCamas("mapaSalas", "{{$unidad}}"); 
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

$("#fechaDerivacion").on("dp.change", function(){
	$.ajax({
		url: "../urgencia/solicitarinfoFormularioDerivado",
		type: 'post',
		dataType: 'json',
		data: {
			'idCaso': $("#idCasoFormDerivacion").val(),
			'fecha': $("#fechaDerivacion").val()
		},
		success: function(data){
			$("#unidadFuncional").html(data["nombre"]);
			$("#idUnidadFuncional").val(data["id"]);
		},
		error: function(error){
			console.log(error);
		}
	});
	$("#formEnviarDerivado").bootstrapValidator("revalidateField", "fechaDerivacion");
});

$("#fechaIda").on("dp.change", function(){
	$("#formEnviarDerivado").bootstrapValidator("revalidateField", "fechaIda");
});

$("#fechaRescate").on("dp.change", function(){
	$("#formEnviarDerivado").bootstrapValidator("revalidateField", "fechaRescate");
});

$("#formEnviarDerivado").bootstrapValidator({
		excluded: [':disabled',':not(:visible)'],
		fields: {
			ugcc: {
				validators: {
					callback: {
						callback: function(value, validator, $field){
							if(value == "no"){
								$("#tipo_ugcc").addClass("hidden");
							}else{
								$("#tipo_ugcc").removeClass("hidden");
							}
							return true;
						}
					}
				}
			},
			tramo: {
				validators: {
					callback: {
						callback: function(value, validator, $field){
							if(value == 'pendiente'){
								$("#t_fecha_ida").addClass("hidden");
								$("#t_fecha_rescate").addClass("hidden");
							}
							else if(value == 'ida'){
								$("#t_fecha_ida").removeClass("hidden");
								$("#t_fecha_rescate").addClass("hidden");
							}
							else if(value == "ida-rescate"){
								$("#t_fecha_ida").removeClass("hidden");
								$("#t_fecha_rescate").removeClass("hidden");
							}
							return true;
						}
					}
				}
			},
			fechaDerivacion: {
				validators:{
					notEmpty: {   
					 	message: 'Debe ingresar la fecha de ida'
					}
				}
			},
			fechaIda: {
			validators:{
				notEmpty: {   
					 message: 'Debe ingresar la fecha de ida'
					}
				}
			},
			fechaRescate: {
			validators:{
				notEmpty: {   
					 message: 'Debe ingresar la fecha de rescate'
					}
				}
			},
			tipo_centro: {
				validators:{
					notEmpty: {
						message: 'Seleccione el tipo de centro'
					}
				}
			},
			otro_derivacion: {
			validators:{
				notEmpty: {   
					 message: 'Debe especificar el Centro'
					}
				}
			},
			compra_servicio_otro: {
			validators:{
				notEmpty: {   
					 message: 'Debe especificar otro movil'
					}
				}
			},
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on('error.form.bv', function(e) {
	}).on("success.form.bv", function(evt){
		var $form = $(evt.target);
		var $button      = $form.data('bootstrapValidator').getSubmitButton();

		fv = $form.data('bootstrapValidator');
		evt.preventDefault();

		$("#btnFormularioDerivar").attr('disabled', 'disabled');
		$.ajax({
			url: "enviarDerivado",
			type: 'post',
			dataType: 'json',
			data: $('#formEnviarDerivado').serialize()
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
					location . reload();
					setTimeout(function() {
						swalCargando.fire({});
					}, 2000)
				},
				});
			}
		})

	});


$("#formEnviarPabellon").bootstrapValidator({
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
		$("#formEnviarPabellon input[type='submit']").prop("disabled", false);
	}).on("success.form.bv", function(evt){
		$("#formEnviarPabellon input[type='submit']").prop("disabled", false);
		evt.preventDefault(evt);
		var $form = $(evt.target);
		bootbox.confirm({
			message: "<h4>¿Está seguro de querer enviar al paciente de la lista de pabellón?</h4>",
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
						url: "enviarPabellon",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: "post",
						dataType: "json",
						data: $form .serialize(),
						success: function(data){ 
							$("#modalEnviarPabellon").modal("hide");   
							if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
							$("#opcionesCamas").hide();
							$(".descripcionPaciente").hide();
							generarMapaCamas("mapaSalas", "{{$unidad}}");
							}, 2000)
						},
						});
						}
						if(data.error) swalError.fire({
										title: 'Error',
										text:data.error
										});
							$("#comentario").val('');   
						},
						error: function(error){
							console.log(error);
						}
					});
				}
			}
		});
	});



var plan=function(id){
	paciente_plan=id;
	$("#detallePLan").val('');
	$("#modalPlanTratamiento").modal("show");
}

var liberar=function(idSala, idCama, idCaso, sexo){
	$("#salaLiberar").val(idSala);
	$("#camaLiberar").val(idCama);
	$("#casoLiberar").val(idCaso);

	$.ajax({
		url: "{{URL::to('/rnInfo')}}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {idCaso: idCaso},
		dataType: "json",
		type: "get",
		success: function(data){
			if (data.rn == 'si') {
				var rut =data.rut;
				var dv =data.dv;
				$("#rut_rn").val(data.rut);
				if(data.dv == 10){
					$("#dv_rn").val("K");
				}else{
					$("#dv_rn").val(data.dv);
				}
				$("#rn_info").attr('hidden', false);
				$("#rut_rn").attr('disabled', false);
				$("#dv_rn").attr('disabled', false);
			}else{
				$("#rn_info").attr('hidden', true);
				$("#rut_rn").attr('disabled', true);
				$("#dv_rn").attr('disabled', true);
			}

		},
		error: function(error){
			console.log(error);
		}
	});
	//dialog.modal("hide");
	var fecha = $("#fechaEgreso").data('DateTimePicker');
	fecha.date(moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm'));
	/*fecha.minDate(moment(window._gc_now).subtract(3, "days").startOf('day'));
	fecha.maxDate(moment(window._gc_now)); */

	$.ajax({
		url: "{{ URL::to('/')}}/urgencia/cantidadExamenPendiente",
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		data: {idCaso: idCaso},
		type: "post",
		success: function(data){

			if(data.cantidad > 0){
				swalWarning.fire({
				title: 'Información',
				text:"El paciente tiene exámenes pendientes"
				}).then(function(result) {
					examenes(idCaso);
				});
			}else{
				//limpiarFormDatAlta();
				//$("#medicoAltaLC").attr("required", false);
				$("#medicoAltaLC").val("");
				$("#id_medico").val("");
				if(data.altaSinLiberar){
					$(".casoLiberar").val(idCaso);
					$(".medicoDioAltaOcultar").show();
					$(".medicoAltaOculto").hide();
					$("#medicoAltaLC").val("");
					$("#id_medico").val("");
					var opcion = "no";
					$("input[name=cambiarMedico]").filter("[value="+opcion+"]").prop("checked", true);
					//$("#medicoAltaLC").removeClass("required");
					//$("#medicoAltaLC").attr("required", false);
					$("#modalAltaSinLiberacion").modal();
				}else{
					limpiarFormDatAlta();
					if(sexo == 1){ //femenino
						$("#divParto").show();
					}else{
						$("#divParto").hide();
					}
					$("#modalAllta").modal();
				}
			}
		},
		error: function(error){
			console.log("error: ", error);
		}
	});
}

$("#formAltaSinLiberar").bootstrapValidator({
		excluded: ':disabled',
		fields: {}
		}).on('status.field.bv', function(e, data) {
			//data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
		var $form = $(evt.target);
		$.ajax({
			url: "AltaSinLiberarCama",
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
								$("#opcionesCamas").hide();
								$("#descripcionPaciente").hide();
								$("#modalAltaSinLiberacion").modal("hide");
								$("#simbolo").show();
								generarMapaCamas("mapaSalas", "{{$unidad}}");
							}, 2000)
						},
						});
				}
				if(data.faltaMedico){
					swalInfo2.fire({
					title: 'Información',
					text:data.faltaMedico
					}).then(function(result) {
						$("#solicitarSLC").attr("disabled", false);
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

var limpiarFormDatAlta=function(){
	$("#select-motivo-liberacion").val('').change;
	//$("#formDarAlta").get(0).reset();
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
	$(".esp_tipo_cama").removeAttr("hidden");
	$('#formDarAlta').bootstrapValidator('revalidateField', 'medicoAlta');
	$('#formDarAlta').bootstrapValidator('revalidateField', 'ficha');
	$('#formDarAlta').bootstrapValidator('revalidateField', 'parto');
}

var acostar=function(idCaso){
	$("#input_comentario_visitas").hide();
	$("#idCasoVisitas").val(idCaso);
	$("#modalVisitas").modal();
}

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

$("#visitasAcostar").on("click", function(){
	$("#modalVisitas").find("input[name=recibe_visitas][value='no']").change();
	$("#modalVisitas").find("#cantidad_personas").change();
	$("#modalVisitas").find("#cantidad_horas").change();
	$("#modalVisitas").find("#comentario_visitas").change();
});

$("#modalHistorialVisitas").on("hidden.bs.modal",function(){
	$('#formVisitas').bootstrapValidator('resetForm', true);
	$(".div-recibe-visitas").hide();
	$("#input_comentario_visitas").hide();
	var checkeadorv = $("#modalHistorialVisitas").find("input[name='recibe_visitas']:checked").val();
	if(checkeadorv === undefined){
		$("#modalHistorialVisitas").find("input[name=recibe_visitas][value='no']").prop("checked",false);
		$("#modalHistorialVisitas").find("input[name=recibe_visitas][value='no']").prop("checked",false);
	}
});

var confirmarTI=function(idCaso){
	var dialog = bootbox.dialog({
		message: "<h4>¿Desea confirmar el traslado interno?</h4>",
		buttons: {
			cancel: {
				label: "No",
				className: 'btn-danger',
			},
			ok: {
				label: "Si",
				className: 'btn-primary',
				callback: function(){
					$.ajax({
						url: "{{ URL::to('/')}}/confirmarTI",
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
						data: {idCaso: idCaso},
						type: "post",
						success: function(data){
							if (data.exito) {
								swalExito.fire({
									title: 'Exito!',
									text: "Se confirmo el traslado del paciente",
									didOpen: function() {
										setTimeout(function() {
											$("#opcionesCamas").hide();
											$(".descripcionPaciente").hide();
											generarMapaCamas("mapaSalas", "{{$unidad}}");
										}, 2000)
									},
								});
							}

							if (data.error) {
								swalError.fire({
									title: 'Error',
									text:"Se ha producido un error"
								}).then(function(result) {
									if (result.isDenied) {
										$("#opcionesCamas").hide();
										$(".descripcionPaciente").hide();
										generarMapaCamas("mapaSalas", "{{$unidad}}");
									}
								});
							}


						},
						error: function(error){
							console.log("error: ", error);
						}
					});
				}
			}
		}
	});

}

var regresarTransito=function(idCaso){
	var dialog = bootbox.dialog({
		//title: 'Se ha realizado el traslado interno',
		message: "<h4>¿Desea regresar al paciente a la lista de tránsito?</h4>",
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
					$.ajax({
						url: "{{ URL::to('/')}}/regresarTransito",
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
						data: {idCaso: idCaso},
						type: "post",
						success: function(data){

							// if (data.warning) {
							// 	swalWarning.fire({
							// 		title: 'Información',
							// 		text: "Ya tiene una solicitud de traslado pendiente"
							// 	}).then(function(result) {
							// 		$("#modalSolicitarTrasladoInterno").modal("hide");
							// 	});
							// }

							if(data.exito){
								swalExito.fire({
									title: 'Exito!',
									text: "Se ha regresado al paciente",
									didOpen: function() {
										setTimeout(function() {
										location . reload(true);
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
							console.log("error: ", error);
						}
					});
				}
			}
		}
	});

}

var darAlta= function(idSala,idCama,idCaso){
	$("#salaDarAlta").val(idSala);
	$("#camaDarAlta").val(idCama);
	$("#casoDarAlta").val(idCaso);
	dialog.modal("hide");
	var fecha = $("#fechaEgresoAlta").data('DateTimePicker');
	fecha.date(moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm'));
	fecha.minDate(moment(window._gc_now).subtract(3, "days").startOf('day'));
	fecha.maxDate(moment(window._gc_now));
	$("#modalDarAlta").modal();
}

var verDetalles = function(idCaso){
	$(".detalles-caso").val(idCaso);

	/* $.ajax({
		url: "{{ URL::to('/riesgoActual')}}",
		data: {caso: idCaso},
		type: "post",
		dataType: "json",
		success: function(data){
			console.log(data);

			console.log(data.dependencia1);
			dependencia1 = parseInt(data.dependencia1, 0);

			console.log(dependencia1);
			$("#dependencia1[value=1]").attr("selected", "selected");
			$("select[name='dependencia1']").val(1);
			$("#dependencia1").val(2);
			if (data.riesgo != null) {
				$("#dependencia1").val(dependencia1);
			}

		},
		error: function(error){

		}
	}); */

	$.ajax({
		url: "{{ URL::to('/detallesCaso') }}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {caso: idCaso,unidad: "{{$unidad}}"},
		dataType: "json",
		type: "post",
		success: function(data){
		//	console.log(data.contenido);
			$("#modalVerDetalles .modal-body").html(data.contenido);
			//dialog.modal("hide");
			$("#modalVerDetalles").modal();
		},
		error: function(error){
			console.log(error);
		}
	});
}

var verDetallesDieta = function(idCaso){
	$(".detalles-caso").val(idCaso);
	$.ajax({
		url: "{{ URL::to('/detallesCasoDieta') }}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {caso: idCaso},
		dataType: "json",
		type: "post",
		success: function(data){
			//console.log(data.contenido);
			$("#modalVerDetallesDieta .modal-body").html(data.contenido);
			//dialog.modal("hide");
			$("#modalVerDetallesDieta").modal();
		},
		error: function(error){
			console.log(error);
		}
	});
}

$("#btn-informe-excel").on("click", function(){
	var idCaso=$(".detalles-caso").val();
	var variableunidad=$("#unidad_original").val();

	window.location.href = "{{url('detallesCasodescargarExcel')}}/"+idCaso+"/"+variableunidad;
});

var verDiagnosticos = function(idCaso){
		var ubicacion = 'mapa_de_camas';
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
			$(".ubicacion").val('mapa_de_camas');
			//dialog.modal("hide");
			$(".detalle-diagnostico").val(idCaso);
			$("#modalVerDiagnosticos").modal();
		},
		error: function(error){
			console.log(error);
		}
	});
}

var examenes = function(idCaso){
	$(".detalle-diagnostico").val(idCaso);
	$.ajax({
		url: "{{ URL::to('/examenesCaso') }}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {caso: idCaso},
		dataType: "json",
		type: "post",
		success: function(data){
			/* añadir valores al sleect */
			$("#modalVerExamenes .modal-body").html(data.contenido);
			//dialog.modal("hide");
			$("#modalVerExamenes").modal();
		},
		error: function(error){
			console.log(error);
		}
	});
}


var solicitarAmbulancia = function(idPaciente,idCaso){
	$("#detalle-diagnostico").val(idCaso);
	$("#modalIngresarEsperaAmbulancia .modal-header .modal-title").html('Ingreso de paciente a lista de espera de ambulancia')
	$("#modalIngresarEsperaAmbulancia .modal-body #idPaciente").val(idPaciente);
	$("#modalIngresarEsperaAmbulancia .modal-body #iDCaso").val(idCaso);
	$("#modalIngresarEsperaAmbulancia .modal-body #unidad").val("{{$unidad}}");

	$("#modalIngresarEsperaAmbulancia .modal-body #datetimepicker").datetimepicker({
		format: 'DD-MM-YYYY HH:mm:ss'
	});


	//dialog.modal("hide");
	$("#modalIngresarEsperaAmbulancia").modal();

}

//OPTIMIZACION
var buscarMejorCama = function(idPaciente,idCaso){
	// EN PLENO PROCESO DE INGRESAR OPTIMIZACION DE CAMAS;
	swalCargando.fire({});

	$.ajax({
		url: "../optimizacion",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {idCaso: idCaso},
		type: "post",
		dataType: "json",
		success: function(data){
			swalCargando.close();
			Swal.hideLoading();
			$("#modalOptimizacion").modal();
			/* if (data.error) {
				console.log("no encontre datos");
			} */
			var tabla=$("#optimizacion-table").dataTable().columnFilter({
				aoColumns: [
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "text"},
					{type: "textarea"},
					null
				]
			});
			tabla.fnClearTable();
			if(data.length != 0) tabla.fnAddData(data);
		},
		error: function(error){
			swalCargando.close();
			Swal.hideLoading();
			console.log(error);
		}
	});
}

var documentosDerivacion = function(idSala,idCama,idCaso){

	$.ajax({
		url: "{{ URL::to('/documentosDerivacion') }}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {caso: idCaso},
		dataType: "json",
		type: "post",
		success: function(data){
			$("#modalDocumentosDerivacion .modal-body").html(data.contenido);
			$("#modalDocumentosDerivacion").modal();
		},
		error: function(error){
			console.log(error);
		}
	});

}

var modalDerivacion=function( cama, sala, unidad, idCama, idCaso){

	$("#idCamaDerivar").val(idCama);
	$("#idCasoDerivar").val(idCaso["id"]);
	$("#asuntoDerivar").prop("disabled", false);
	$("#asuntoDerivar").val("Solicitud de cama: "+cama+" en sala: "+sala+" de la unidad: "+unidad);
	$("#textoDerivar").prop("disabled", false);
	$(".fileDerivar").prop("disabled", false);
	$("#idEstablecimientoDerivar").val(idCaso["establecimiento"]);
	$("#modalSolicitar").modal();

}

var trasladoInterno=function(camaNueva,caso,camaVieja){
	var servicio_original = $("#unidad_original").val();

	$.ajax({
		url: "{{URL::to('/')}}/reasignar",
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		data: {camaOld: camaVieja, casoOld: caso['id'], camaNew: camaNueva},
		type: "post",
		dataType: "json",
		success: function(data){
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
						text: data.msg,
						didOpen: function() {
							setTimeout(function() {
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


	var dialog = bootbox.dialog({
			title: 'Se ha realizado el traslado interno',
			message: "<h4>¿Desea reconvertir la cama?</h4>",
			buttons: {
				cancel: {
					label: "No",
					className: 'btn-danger',
					callback: function(){
						location.reload();
					}
				},
				ok: {
					label: "Si",
					className: 'btn-info',
					callback: function(){
						//Example.show('Custom OK clicked');
						 //RECONVERTIR
						$.ajax({
						url: "reconvertir",
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
						data: {cama: camaNueva, servicio: servicio_original},
						type: "post",
						dataType: "json",
						success: function(data){
							if(data.exito){
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

	var derivarForm=function(){

		$("#derivarForm").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				asuntoDerivar: {
					validators:{
						notEmpty: {
							message: 'El Asunto es obligatorio'
						}
					}
				},
				textoDerivar: {
					validators:{
						notEmpty: {
							message: 'La descripción de la derivación es necesaria'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			//data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault();
			$.ajax({
				url: "{{URL::to('/registrarTrasladoOptimizacion')}}",
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: $('#derivarForm').serialize(),
				dataType: "json",
				type: "post",
				success: function(data){
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
			});
		});
	}
	//FIN OPTIMIZACION

	var liberarReserva=function(idSala, idCama, idCaso){
		dialog.modal("hide");
		bootbox.dialog({
			message: "<h4>¿ Desea liberar la reserva ?</h4>",
			title: "Confirmación",
			buttons: {
				success: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							url: "{{URL::to('/')}}/liberarReserva",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							data: {caso: idCaso},
							type: "post",
							success: function(data){
								generarMapaCamas("mapaSalas", "{{$unidad}}");
								swalExito.fire({
								title: 'Exito!',
								text: "Se ha liberado la reserva",
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

	var getUnidades=function(unidad){
		swalCargando.fire({});
		var dataPost = unidad ? {unidad:unidad} : null;
		
		var unidades=[];
		$.ajax({
			url: "{{URL::to('/')}}/getUnidades",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: dataPost,
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

				setTimeout(function () {
					swalCargando.close();
					Swal.hideLoading();
				}, 2000);
				//console.log($('#tabUnidad').find('.active').children().data("id"));
			},
			error: function(error){
				swalCargando.close();
				Swal.hideLoading();
				console.log(error);
			}
		});
		
		return unidades;
		
	}

 	var reasignar=function(idSala, idCama, idCaso,idUnidad/*opcional, solo requerido para camas temporales*/){
		 //alert();

 		$("#salaReasignar").val(idSala);
 		$("#camaReasignar").val(idCama);
 		$("#casoReasignar").val(idCaso);
		$("#unidadReasignar").val($('#tabUnidad').find('.active').children().data("id"));
		
 		//dialog.modal("hide");
 		getUnidades(idUnidad);
		setTimeout(function(){
			$("#modalReasignar").modal();
		},2000)
 	}

	 var enviarPreAlta=function(idSala, idCama, idCaso){
		swalPregunta.fire({
			title: "¿Seguro de enviar el paciente a pre alta?"
		}).then(function(result){
		if (result.isConfirmed) {
				$.ajax({
			url: "{{URL::to('/')}}/enviarPreAlta",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				idSala: idSala, 
				idCama: idCama,
				idCaso: idCaso
			},
			type: "post",
			dataType: "json",
			success: function(data){
				$("#descripcionPaciente").hide();
				if (data.exito) {
					swalExito.fire({
					title: 'Exito!',
					text: "Se ha enviado a pre alta",
					didOpen: function() {
						setTimeout(function() {
							generarMapaCamas("mapaSalas", "{{$unidad}}");
							traerCamasTemporales(true);
							//$("#modalSolicitarTrasladoInterno").modal("hide");
						}, 2000)
					},
					});
				}

				if (data.error) {
					generarMapaCamas("mapaSalas", "{{$unidad}}");
					swalError.fire({
					title: 'Error',
					text:"Se ha producido un error"
					}).then(function(result) {
						if (result.isDenied) {
							generarMapaCamas("mapaSalas", "{{$unidad}}");
							traerCamasTemporales(true);
							//$("#modalSolicitarTrasladoInterno").modal("hide");
						}
					});
				}

				if (data.warning) {
					swalWarning.fire({
						title: 'Información',
						text: "El paciente no se encuentra en la cama"
					}).then(function(result) {
						generarMapaCamas("mapaSalas", "{{$unidad}}");
						traerCamasTemporales(true);
						//$("#modalSolicitarTrasladoInterno").modal("hide");
					});
				}

			},
			error: function(error){
				console.log(error);
			}	 
		});
			}
		});
		
	}

	var solicitudTrasladoInterno=function(idSala, idCama, idCaso){
		
 		$("#salaSolicitaT").val(idSala);
 		$("#camaSolicitaT").val(idCama);
 		$("#casoSolicitaT").val(idCaso);
 		
		
 		var unidad = "#{{$unidad}}";
 		console.log(unidad);
		
	 	$("#modalSolicitarTrasladoInterno").modal();

 	}

	 var solicitarTraslado=function(){
		bootbox.dialog({
			message: "<h4>¿ Seguro que desea realizar el traslado ?</h4>",
			title: "Confirmación",
			buttons: {
				success: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							url: "{{URL::to('/')}}/solicitarTrasladoInterno",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							data: {
								unidad: $("#unidadSolicitar").val(), 
								casoOld: $("#casoSolicitaT").val(), 
								comentario: $("#requerimiento_solicitud").val()
							},
							type: "post",
							dataType: "json",
							success: function(data){
								if (data.exito) {
									swalExito.fire({
									title: 'Exito!',
									text: "Se ha realizado la solicitud traslado interno",
									didOpen: function() {
										setTimeout(function() {
											generarMapaCamas("mapaSalas", "{{$unidad}}");
											$("#modalSolicitarTrasladoInterno").modal("hide");
										}, 2000)
									},
									});
								}

								if (data.error) {
									generarMapaCamas("mapaSalas", "{{$unidad}}");
									swalError.fire({
									title: 'Error',
									text:"Se ha producido un error"
									}).then(function(result) {
										if (result.isDenied) {
											generarMapaCamas("mapaSalas", "{{$unidad}}");
											$("#modalSolicitarTrasladoInterno").modal("hide");
										}
									});
								}

								if (data.warning) {
									swalWarning.fire({
										title: 'Información',
										text: "Ya tiene una solicitud de traslado pendiente"
									}).then(function(result) {
										$("#modalSolicitarTrasladoInterno").modal("hide");
									});
								}

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


	

 	var validarTraslado=function(idCaso){

 		$.ajax({
 			url: "{{URL::to('/')}}/validarTraslado",
 			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 			data: {idCaso: idCaso},
 			type: "post",
 			dataType: "json",
 			success: function(data){
 				if(data.error){
					 swalError.fire({
					title: 'Error',
					text:data.error
					}).then(function(result) {
					if (result.isDenied) {
							location . reload();
					}
					});
				 }

 				else
 					location.href="{{URL::to('trasladar')}}/{{$unidad}}/"+idCaso;

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
 				else location.href="{{URL::to('historial')}}/{{$unidad}}/"+idCaso;
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

	var infecciones=function(idCaso){
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
 				else location.href="{{URL::to('infecciones')}}/{{$unidad}}/"+idCaso;
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

  	var VerInfecciones=function(idCaso){
		var tipo = "{{Session::get('usuario')->tipo}}";
		if(tipo == "iaas" || tipo == "admin"){
			var idEstablecimiento = "{{Session::get('usuario')->establecimiento}}";
		}
 		$.ajax({
 			url: "{{URL::to('/')}}/validarTraslado3",
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
				 //else location.href="{{URL::to('trasladar')}}/{{$unidad}}/"+idCaso;
				else location.href="{{URL::to('/')}}/verinfecciones2/"+idCaso+'/'+idEstablecimiento;
 			},
 			error: function(error){
 				console.log(error);
 			}
 		});
 	}

 	var marcarCamaDisponible=function(event, cama){
		 event.preventDefault();
		 var servicio_original = $("#unidad_original").val();
		 var dialog = bootbox.dialog({
			 //title: 'Se ha realizado el traslado interno',
			 message: "<h4>¿Desea trasladar al paciente?</h4>",
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
									setTimeout(function() {
										swalCargando.close();
										Swal.hideLoading();
									},2000)
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
													location . reload();
													swalCargando.fire({title:'Actualizando mapa de camas'});
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
							//Example.show('Custom OK clicked');
							//RECONVERTIR
							/* $.ajax({
								url: "reconvertir",
								headers: {
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
									},
								data: {cama: cama, servicio: servicio_original},
								type: "post",
								dataType: "json",
								success: function(data){
									console.log(data);
									if(data.exito){
										bootbox.alert("<h4>"+data.exito+"</h4>", function(){
											location.reload();
										});
									}
								},
								error: function(error){
									console.log(error);
								}
							}); */
					}
				}
			}
		});
 	}

 	var renovar=function(idSala, idCama, idCaso){
 		dialog.modal("hide");
 		$("#casoRenovar").val(idCaso);
 		$("#modalRenovar").modal();
 	}

 	var ingresar=function(idSala, idCama, idCaso){
 		dialog.modal("hide");
 		bootbox.dialog({
 			message: "<h4>¿ Desea ingresar el paciente a la cama ?</h4>",
 			title: "Confirmación",
 			buttons: {
 				success: {
 					label: "Aceptar",
 					className: "btn-primary",
 					callback: function() {
 						$.ajax({
 							url: "{{URL::to('/')}}/ingresar",
 							headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 							data: {caso: idCaso, cama: idCama},
 							type: "post",
 							success: function(data){
 								generarMapaCamas("mapaSalas", "{{$unidad}}");
 								swalExito.fire({
								title: 'Exito!',
								text:data.msg
								})
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

 	var getPacienteReserva=function(id, idSala, idCama, idCaso, reservada, idPaciente){
	 	$.ajax({
	 		url: "{{URL::to('/')}}/getPaciente",
	 		headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
	 		type: "post",
	 		data: {id: id, unidad: "{{$unidad}}"},
	 		dataType: "json",
	 		success: function(data) {
	 			var riesgo = data.riesgo == null ? "No especificado" : data.riesgo;
	 			//var some="{{--$some?'true':'false'--}}";
                var caso_social = data.caso_social == null? "No especificado" : data.caso_social ? "Sí" : "No";
                var extranjero = data.extranjero == null? "No especificado" : data.extranjero ? "Sí" : "No";

	 			var html = "<ol class='list-group'>";
	 			var href="paciente/editar/"+idPaciente;
	 			//var editar='{{ HTML::link("'+href+'", "Editar paciente") }}';
				var editar = '<a href="{{URL::to('')}}/'+href+'">Editar paciente</a>';
	 			html += "<li class='list-group-item'> <label class='control-label'>Run: </label>&nbsp;&nbsp;" + data.rut + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Extranjero: </label>&nbsp;&nbsp;" + extranjero + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Genero: </label>&nbsp;&nbsp;" + data.genero + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Nombre: </label>&nbsp;&nbsp;" + data.nombre + "</li>";
	 			html += "<li class='list-group-item'> <label class='control-label'>Apellido paterno: </label>&nbsp;&nbsp;" + data.apellidoP + "</li>";
	 			html += "<li class='list-group-item'> <label class='control-label'>Apellido materno: </label>&nbsp;&nbsp;" + data.apellidoM + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Fecha nacimiento: </label>&nbsp;&nbsp;" + data.fecha + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Edad: </label>&nbsp;&nbsp;" + data.edad + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Previsión: </label>&nbsp;&nbsp;" + data.prevision + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Fecha de hospitalización: </label>&nbsp;&nbsp;" + data.fechaIngreso + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Caso social: </label>&nbsp;&nbsp;" + caso_social + "</li>";
                html += "<li class='list-group-item' style='min-width: 300px;'> <label class='control-label'>Último diagnóstico: </label>&nbsp;&nbsp;" + data.diagnostico + "</li>";
                html += "<li class='list-group-item'> <label class='control-label'>Diagnóstico CIE10: </label>&nbsp;&nbsp;" + (data.diagnostico_cie10?data.diagnostico_cie10:"") + "</li>";
	            html += "<li class='list-group-item'> <label class='control-label'>Riesgo: </label>&nbsp;&nbsp;" + riesgo + "</li>";
	            html += "</ol>";
	            @if(/* !$some && */ Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
		            html+="<fieldset>";
		            html+="<legend>Opciones</legend>";
		            html+="<ol>";
		            html+="<li> <a class='cursor' onclick='liberarReserva("+idSala+", "+idCama+","+idCaso+")'>Liberar</a> </li>";
		            html+="<li> <a class='cursor' onclick='ingresar("+idSala+", "+idCama+","+idCaso+")'>Ingresar</a> </li>";
		            if(reservada == 0) html+="<li> <a class='cursor' onclick='renovar("+idSala+", "+idCama+","+idCaso+")'>Renovar</a> </li>";
		            html+="<li>"+editar+"</li>";
		            html+="</ol>";
		            html+="</fieldset>";
	        	@endif
	            dialog=bootbox.dialog({
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
	            });
	        },
	        error: function(error) {
	        	console.log(error);
	        }
	    });
	 }

	var marcarCama=function(idSala, idCama, reconvertida){
		$("#salaReserva").val(idSala);
		$("#camaReserva").val(idCama);
		$("#idcamacambio").val(idCama);
		$("#descripcionPaciente").hide();
		$("#simbolo").show();

		var paraMarcar=idSala != -1 && idCama != -1;
		if(reconvertida) $("#liOriginal").show();
		else $("#liOriginal").hide();
		if(paraMarcar) $("#modalOpciones").modal();
	}

	var bloquearCama=function(){
		$.ajax({
			url: "bloquearCama",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {
				idCama: $("#camaReserva").val(),
				motivo: $("#motivoBloqueo").val(),
				otro_motivo: $("#otroMotivoBloqueo").val()
			},
			type: "post",
			dataType: "json",
			success: function(data){
				if( data.exito){
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

	var desbloquearCama=function(){
		$.ajax({
			url: "bloquearCama",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {idCama: $("#camaReserva").val()},
			type: "post",
			dataType: "json",
			success: function(data){
				if( data.exito){
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

	@if( Auth::user()->tipo != TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS)
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
			$("#camaDesbloquear").val(idCama);
			$("#modalDesbloquear").modal("show");

		}


	@endif 

	$(document).on('click', 'a#DCama', function(){
		$.ajax({
			url: "descripcionCamas",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {idCama: $("#idcamacambio").val()},
			type: "post",
			dataType: "json",
			success: function(data){
				$("#cambioDescripcion").val(data['descripcion']);
			},
			error: function(error){
				console.log(error);
			}
		});

	});

	$("#cambiarD").on("click", function(){
		cambiarDescripcionCama();
	});

	var cambiarDescripcionCama=function(){
		$('#modalDescripcion').modal("hide");
		$.ajax({
			url: "cambiarDescripcion",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {
				idCama: $("#idcamacambio").val(),
				descripcion: $("#cambioDescripcion").val()
			},
			type: "post",
			dataType: "json",
			success: function(data){


				if(data.exito){
					swalExito.fire({
					title: 'Exito!',
					text: data.exito,
					didOpen: function() {
						setTimeout(function() {
							generarMapaCamas("mapaSalas", "{{$unidad}}");
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
	}
	function verFormulariosGinecologia(idCaso)
	{
		
		var a1 = $("<li><a href='{{url('/')}}/formularios-ginecologia/" + idCaso + "/entrega-documentos-alta'>Entrega Documentos Alta</a></li>");
		var a2 = $("<li><a href='{{url('/')}}/formularios-ginecologia/" + idCaso + "/consentimiento-informado-interrupcion-embarazo'>Consentimiento informado de interrupción del embarazo</a></li>");
		var a3 = $("<li><a href='{{url('/')}}/formularios-ginecologia/" + idCaso + "/epicrisis-interrupcion-gestacion-iii-trimestre'>Epicrisis interrupción de gestación III trimestre</a></li>");
		var a4 = $("<li><a href='{{url('/')}}/formularios-ginecologia/" + idCaso + "/partograma'>Partograma</a></li>");
		var a5 = $("<li><a href='{{url('/')}}/formularios-ginecologia/" + idCaso + "/solicitud-transfusion-productos-sanguineos'>Solicitud de transfusión de productos sanguíneos</a></li>");
		
		var ul = $("<ul>");
		ul.append(a1);
		ul.append(a2);
		ul.append(a3);
		ul.append(a4);
		ul.append(a5);
		var div = $("<div>");
		div. append(ul);
		
		var d = bootbox.dialog({
			title: "Seleccione un formulario",
			message: div.html()
		});
		d.modal("show");
	}

	$(function(){
		var num = 1;
		var limite = 3;
		var agregarBoton = $('.agregar_boton');
	// var removerBoton = $('.remover_boton');
		var contenedor = $('#dynamicTable');

		agregarBoton.click(function(){
			if(num < limite){
				num++;
				var campos = '<tr><td class="row-index"></td><td><select class="form-control" name="tipo_telefono[]" id="tipo_telefono_'+num+'"><option value="Movil">Movil</option><option value="Casa">Casa</option><option value="Trabajo">Trabajo</option></select></td><td><input type="number" id="telefono_'+num+'" name="telefono[]" placeholder="Ingrese número de telefono" class="form-control" /></td><td><button type="button" class="remover_boton btn btn-danger">Remover</button></td></tr>';
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

	function generarTablaHistorialVisitas(visitas) {
        tableHistorialVisitas = $("#tableHistorialVisitas").dataTable({
			"destroy": true,
            "ordering": false,
            "searching": false,
            "data":visitas,
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }


	var verHistorialVisitas= function(idCaso){
	$.ajax({
		url: "{{ URL::to('/')}}/verHistorialVisitas/"+idCaso,
		headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		type: "get",
		success: function(data){
			console.log(data)
			if (typeof data.paciente[0] !== 'undefined') {
				rut = "RUN no disponible";
				if(data.paciente[0].rut != null && data.paciente[0].rut != ""){
					rut = data.paciente[0].rut +"-"+ data.paciente[0].dv; 
				}
				$("#modalHistorialVisitas").find('.visitas-datosPacientes').text(data.paciente[0].nombre_completo.toUpperCase()+" ("+rut+")");
				if(data.paciente[0].recibe_visitas == true){
					num_personas_visitas = (data.paciente[0].num_personas_visitas) ? data.paciente[0].num_personas_visitas : 0;
					cant_horas_visitas = (data.paciente[0].cant_horas_visitas) ? data.paciente[0].cant_horas_visitas : 0;
					$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:Si');
					$("#modalHistorialVisitas").find('.visitas-comentarios').prop("hidden",true);
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').prop("hidden",false);
					$("#modalHistorialVisitas").find('.visitas-cantHoras').prop("hidden",false);
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas:'+num_personas_visitas);
					$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas:'+cant_horas_visitas);
					
					cargarDatosEdicionVisitas(true,num_personas_visitas,cant_horas_visitas,"");
				}else if(data.paciente[0].recibe_visitas == false){
					comentario_visitas = (data.paciente[0].comentario_visitas) ? data.paciente[0].comentario_visitas : 'No configurado';
					$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:No');
					$("#modalHistorialVisitas").find('.visitas-comentarios').prop("hidden",false);
					$("#modalHistorialVisitas").find('.visitas-comentarios').text('Comentario:' + comentario_visitas);
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').prop("hidden",true);
					$("#modalHistorialVisitas").find('.visitas-cantHoras').prop("hidden",true);
					
					cargarDatosEdicionVisitas(false,0,0,comentario_visitas);
				}else{
					$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?: Sí');
					$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas: 2');
					$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas: No configurado');
					
					cargarDatosEdicionVisitas(null,"","","");
				}
			}
			if (typeof tableHistorialVisitas != 'undefined') {
				generarTablaHistorialVisitas(data.visitas);
			}
			                             
			$("#modalHistorialVisitas").modal();
		},
		error: function(error){
			console.log("error: ", error);
		}
	});
}




 </script>
<!-- scripts para el modal del historial de visitas-->
<script>
function alternarEdicionVisitas(editar){
	$("#configuracion_visitas_solo_lectura").attr("hidden",editar);
	$("#configuracion_visitas_edicion").attr("hidden",!editar);
	$("#div_btn_editar_registro_visita").attr("hidden",editar);
	$("#div_btn_guardar_registro_visita").attr("hidden",!editar);
}
function cargarDatosEdicionVisitas(recibe_visitas,cant_personas,cant_horas, comentario_visitas)
{
	$("input[type=radio][name=recibe_visitas_]").on("change",function(){
		opcion = $(this).val(); console.log("ke ",opcion);
		if(opcion === "true" && cant_personas === 0){
			cant_personas = 2;
			console.log("cant personas: "+cant_personas,cant_personas);
			ocultarInputsConfigHistorial(false,cant_personas,cant_horas,comentario_visitas);
		}
		else if(opcion === "true" && cant_personas != 0)
		{
			console.log("true y cant_personas: ",cant_personas);
			ocultarInputsConfigHistorial(false,cant_personas,cant_horas,comentario_visitas);
		}
		else if(opcion === "false"){
			console.log("false y cant_personas: ",cant_personas);
			ocultarInputsConfigHistorial(true,cant_personas,cant_horas,comentario_visitas);
		}
	});

	$("#modalHistorialVisitas").find('input[name=recibe_visitas_]').prop("checked",false);
	if(recibe_visitas !== null)
	{
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_][value=' + (recibe_visitas ? "true" : "false") + ']').prop("checked",true);
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_]:checked').trigger("change");
		$("#modalHistorialVisitas").find('#cantidad_personas_').val(cant_personas);
		$("#modalHistorialVisitas").find('#cantidad_horas_').val(cant_horas);
	}
	else{
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_][value=true]').prop("checked",true);
		$("#modalHistorialVisitas").find('input[name=recibe_visitas_]:checked').trigger("change");
	}
}
function cargarDatosEdicionVisitasSoloLectura(recibe_visitas,cant_personas,cant_horas, comentario_visitas)
{
	var t_recibe_visitas = recibe_visitas === "true" ? "Sí" : (recibe_visitas === "false" ? "No" : "No configurado");
	var t_cant_personas = /\d/.test(cant_personas) ? cant_personas : "No configurado";
	var t_cant_horas = /\d/.test(cant_horas) ? cant_horas : "No configurado";
	var t_comentario_visitas = (comentario_visitas) ? comentario_visitas : "No configurado";

	if(t_recibe_visitas == "Sí"){
		$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:' + t_recibe_visitas);
		$("#modalHistorialVisitas").find('.visitas-comentarios').prop("hidden",true);
		$("#modalHistorialVisitas").find('.visitas-cantPersonas').prop("hidden",false);
		$("#modalHistorialVisitas").find('.visitas-cantHoras').prop("hidden",false);
		$("#modalHistorialVisitas").find('.visitas-cantPersonas').text('Cantidad de personas:' + (t_recibe_visitas == "No" ? 0 : t_cant_personas));
		$("#modalHistorialVisitas").find('.visitas-cantHoras').text('Cantidad de horas:'+(t_recibe_visitas == "No" ? 0 : t_cant_horas));
	}else{
		$("#modalHistorialVisitas").find('.visitas-bool').text('¿Puede recibir visitas?:' + t_recibe_visitas);
		$("#modalHistorialVisitas").find('.visitas-comentarios').prop("hidden",false);
		$("#modalHistorialVisitas").find('.visitas-comentarios').text('Comentario:' + t_comentario_visitas);
		$("#modalHistorialVisitas").find('.visitas-cantPersonas').prop("hidden",true);
		$("#modalHistorialVisitas").find('.visitas-cantHoras').prop("hidden",true);
	}
	
	
}
function ocultarInputsConfigHistorial(ocultar, cant_visitas, cant_horas, comentario_visitas){
	$("#inputs_config_historial").prop("hidden",ocultar);
	$("#modalHistorialVisitas").find('#cantidad_personas_').val(cant_visitas).change();
	$("#modalHistorialVisitas").find('#cantidad_horas_').val(cant_horas).change();
	$("#input_comentario_visitas").prop("hidden",!ocultar);
	$("#modalHistorialVisitas").find("#comentario_visitas_").val(comentario_visitas).change();
}
$(function(){
	
	$("#btn_editar_registro_visita").on("click",function(){
		alternarEdicionVisitas(true);
		$('#cantidad_personas_').change();
		// $('#cantidad_horas_').change();
		$("#comentario_visitas_").change();
		//$("#modalHistorialVisitas").find('#cantidad_horas_').prop("readonly", true);
	});

	$("#btn_descargar_historial_visita").on("click", function(){
		var caso = $("#id_caso_historial_visitas").val();
		console.log("caso: ",caso);
		window.location.href = "{{url('pdfHistorialRegistroVisitas')}}"+"/"+caso;
	});
	
	$("#historial_edicion_registro_visitas").bootstrapValidator({
		excluded: [':disabled',':hidden'],
		fields: {
			"recibe_visitas_": {
				trigger: 'change keyup',
				validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			"cantidad_personas_": {
				trigger: 'change keyup',
				validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			"cantidad_horas_": {
				trigger: 'change keyup',
				validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			},
			"comentario_visitas_": {
				trigger: 'change keyup',
				validators:{
					notEmpty: {
						message: 'Campo obligatorio'
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		//data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt, data){
		evt.preventDefault(evt);
		var $form = $(evt.target);

		$.ajax({
			url: "{{URL::to('/guardarConfiguracionVisitasHistorial')}}",
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data:  $form .serialize(),
			dataType: "json",
			type: "post",
			success: function(data){
				if (data.exito) {
					swalExito.fire({
						title: 'Exito!',
						text: "Se ha guardado correctamente",
						didOpen: function() {
							setTimeout(function() {
								cargarDatosEdicionVisitasSoloLectura($("#modalHistorialVisitas").find('input[name=recibe_visitas_]:checked').val(),$("#modalHistorialVisitas").find('#cantidad_personas_').val(),$("#modalHistorialVisitas").find('#cantidad_horas_').val(),$("#modalHistorialVisitas").find('#comentario_visitas_').val());
								alternarEdicionVisitas(false);
							}, 2000)
						},
					});
				}
				else{
					swalError.fire({
						title: 'Error',
						text: "Ha ocurrido un error"
					});
				}
			},
			error: function(error){
				console.log(error);
				$("#btnGuardarEpicrisis").prop("disabled", false);
			}
		});
	});
	
	// $("input[type=radio][name=recibe_visitas_]").on("change",function(){
	// 	console.log($(this).val());
	// 	if($(this).val() === "true")
	// 	{
	// 		ocultarInputsConfigHistorial(false);
	// 	}
	// 	else if($(this).val() === "false"){
	// 		ocultarInputsConfigHistorial(true);
	// 	}
	// });

	$("input[type=radio][name=recibe_visitas_]").on("change",function(){
		idCaso = $("#id_caso_historial_visitas").val();
		opcion = $(this).val(); console.log("ke fuera ",opcion);
		$.ajax({
			url: "{{ URL::to('/')}}/verHistorialVisitas/"+idCaso,
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			type: "get",
			success: function(data){
				console.log(data)
				cant_personas = (data.paciente[0].num_personas_visitas) ? data.paciente[0].num_personas_visitas : 0;
				cant_horas = (data.paciente[0].cant_horas_visitas) ? data.paciente[0].cant_horas_visitas : 0;
				comentario_visitas = (data.paciente[0].comentario_visitas) ? data.paciente[0].comentario_visitas : "";

				if(opcion === "true" && cant_personas === 0){
					//Por defecto
					cant_personas = 2;
					cant_horas = 6;
					console.log("cant personas: "+cant_personas,cant_personas);
					ocultarInputsConfigHistorial(false,cant_personas,cant_horas,comentario_visitas);
					$("#btn_guardar_registro_visita").prop( "disabled", false );
				}
				else if(opcion === "true" && cant_personas != 0)
				{
					console.log("true y cant_personas: ",cant_personas);
					ocultarInputsConfigHistorial(false,cant_personas,cant_horas,comentario_visitas);
				}
				else if(opcion === "false"){
					console.log("false y cant_personas: ",cant_personas);
					ocultarInputsConfigHistorial(true,cant_personas,cant_horas,comentario_visitas);
				}
			},
			error: function(error){
				console.log("error: ", error);
			}
		});
	});

	$("#modalHistorialVisitas").on("hidden.bs.modal",function(){
		alternarEdicionVisitas(false);
		$('#historial_edicion_registro_visitas').bootstrapValidator('resetForm', true);
	});
});
</script>
<!-- Camas temporales -->
<script>

function moverACamaTemporal(id_caso){
	swalPregunta.fire({
			title: "¿Seguro de enviar al paciente a una cama temporal?"
		}).then(function(result){
			if (result.isConfirmed) {
				$.ajax({
					url: '{{URL::to("moverACamaTemporal")}}',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: { "caso": id_caso},
					dataType: "json",
					type: "post",
					success: function(data){
						if(!data.error)
						{
							swalExito.fire({
								title: 'Exito!',
								text: "Se ha movido correctamente",
								didOpen: function() {
									setTimeout(function() {
										generarMapaCamas("mapaSalas", "{{$unidad}}");
										traerCamasTemporales(true);
									}, 2000)
								},
								});
						}
						else{
							swalError.fire({
								title: 'Error',
								text:data.msj
							});
						}
					},
					error: function(error){
						console.log(error);
					}
				});
			}
		});
	
}
function traerCamasTemporales(mostrar_mensaje){
	if ({{Auth::user()->tipo != 'medico'}}) {
		if(mostrar_mensaje === true){
			swalCargando.fire({title:'Actualizando mapa de camas temporales'});
		}
		$.ajax({
			url: '{{URL::to("traerCamasTemporales")}}',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { "unidad": {{$id_unidad}}},
			dataType: "json",
			type: "post",
			success: function(data){
				if(!data.error)
				{
					cargarCamasTemporales(data.camas);
				}else{
					swalCargando.close();
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}
}
function cargarCamasTemporales(camas){
	var $cama_temporal = $("#cama_temporal .row-cama");
	$cama_temporal.empty();
	
	if(camas.length === 0){
		$cama_temporal.append("<p style='font-size:18px;padding:5px;'>No hay camas volantes.<br>Los traslados a camas volantes aparecerán aquí.</p>");
		swalCargando.close();
	}
	
	for(var i = 0; i < camas.length; i++){
		$cama_temporal.append(camas[i]);
	}
	
	swalCargando.close();
}
function getPacienteCamaTemporal(caso,elem){
	
	var padre = $(elem).parent(); 
	
	$(".cursor").css("color","#428bca");
	$(".divContenedorCama").css("border","");
	
	$(elem).css("color","red");
	$(elem).css("font-weight", "bold");


	$(padre).css("border", "2px solid #1e9966");
	$(padre).css("border-radius", "15px");
	
	
	swalCargando.fire({title:'Cargando datos del paciente'});
	$.ajax({
		url: '{{URL::to("getPacienteCamaTemporal")}}',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: { "caso": caso},
		dataType: "json",
		type: "post",
		success: function(data){
			if(!data.error)
			{
				cargarDatosPacienteTemporal(data.datos);
			}
			else{
				swalError.fire({
					title: 'Error',
					text:data.msj
				});
			}
		},
		error: function(error){
			console.log(error);
		},
		complete:function(){
			swalCargando.close();
		}
	});
}
function cargarDatosPacienteTemporal(datos_paciente){
	
	var crearLI = function(titulo,texto){
		texto = texto || "";
		return $("<li class='list-group-item'><label class='control-label'>" + titulo + "</label> " + texto + "</li>");
	};
	var crearOpcion = function(titulo,evento){
		return $("<li><a class='cursor' onclick='" + evento + "'>" + titulo + "</a></li>");
	};
	
	var $div = $("#descripcionPaciente");
	
	$div.empty();
		
	var $boton = $("<button class='botonCerrar btn btn-danger' style='position:fixed; width:19%;height: 30px; z-index: 99;'><b>CERRAR  </b><span class='glyphicon glyphicon-remove' style='height: 30px; z-index: 1000;'></span></button>");
	
	var $ol = $("<ol class='list-group'></ol>");
	
	$ol.append(crearLI("Ficha clínica:",datos_paciente.ficha_clinica));
	$ol.append(crearLI("DAU:",datos_paciente.dau));
	$ol.append(crearLI("Run:",datos_paciente.rut_completo));
	$ol.append(crearLI("Rn:",datos_paciente.rn));
	$ol.append(crearLI("Run madre:",datos_paciente.rut_madre));
	$ol.append(crearLI("Extranjero:",datos_paciente.extranjero));
	$ol.append(crearLI("Nombre:",datos_paciente.nombre));
	$ol.append(crearLI("Apellido paterno:",datos_paciente.apellido_paterno));
	$ol.append(crearLI("Apellido materno:",datos_paciente.apellido_materno));
	$ol.append(crearLI("Género:",datos_paciente.sexo));
	$ol.append(crearLI("Teléfono:",datos_paciente.telefono));
	$ol.append(crearLI("Nombre social:",datos_paciente.nombre_social));
	$ol.append(crearLI("Fecha nacimiento:",datos_paciente.fecha_nacimiento));
	$ol.append(crearLI("Edad:",datos_paciente.edad));
	$ol.append(crearLI("Previsión:",datos_paciente.prevision));
	$ol.append(crearLI("Fecha de solicitud:",datos_paciente.fecha_solicitud));
	$ol.append(crearLI("Fecha de hospitalización:",datos_paciente.fecha_hospitalizacion));
	$ol.append(crearLI("Caso social:",datos_paciente.caso_social));
	$ol.append(crearLI("Último diagnóstico:",datos_paciente.ultimo_diagnostico));
	$ol.append(crearLI("Comentario diagnóstico:",datos_paciente.comentario_diagnostico));
	$ol.append(crearLI("Sugerencia área funcional:",datos_paciente.sugerencia_area_funcional));
	$ol.append(crearLI("Servicio a cargo:",datos_paciente.servicio_a_cargo));
	$ol.append(crearLI("Área Funcional a cargo:",datos_paciente.area_a_cargo));
	$ol.append(crearLI("Requiere aislamiento:",datos_paciente.requiere_aislamiento));
	$ol.append(crearLI("Riesgo:",datos_paciente.riesgo));
	$ol.append(crearLI("¿Quién solicito cama?:",datos_paciente.usuario_ingreso));
	$ol.append(crearLI("¿Quién asigno la cama?:",datos_paciente.usuario_asigna));
	$ol.append(crearLI("¿Quién hospitalizo al paciente?:",datos_paciente.usuario_hospitaliza === null ? datos_paciente.usuario_asigna : datos_paciente.usuario_hospitaliza));
	
	$ol.append(crearLI("Sala original:",datos_paciente.nombre_sala));
	$ol.append(crearLI("Cama original:",datos_paciente.nombre_cama));
	
	$iaas = "";
	if(datos_paciente.patogeno){
		$iaas = $("<fieldset><legend>IAAS</legend></fieldset>");
		$opciones_iaas = $("<ol></ol>");
		$opciones_iaas.append(crearLI("Aislamiento:",datos_paciente.aislamiento ? datos_paciente.aislamiento : "No especificado"));
		$opciones_iaas.append(crearLI("Tiempo desde la notificación:",datos_paciente.dias_aislamiento));
		
		$iaas.append($opciones_iaas);
	}
	
	$fieldset = $("<fieldset><legend>Opciones</legend></fieldset>");
	
	$opciones = $("<ol></ol>");
	
	$opciones.append(crearOpcion("Traslado interno","reasignar(" + datos_paciente.id_sala + "," + datos_paciente.id_cama + ", " + datos_paciente.id_caso + ",{{$id_unidad}})"));
	$opciones.append(crearOpcion("Egreso","liberar(" + datos_paciente.id_sala + "," + datos_paciente.id_cama + ", " + datos_paciente.id_caso + ", \"" + datos_paciente.sexo + "\")"));
	$opciones.append(crearOpcion("Enviar a Pre-alta","enviarPreAlta(" + datos_paciente.id_sala + "," + datos_paciente.id_cama + ", " + datos_paciente.id_caso + ")"));
	
	$fieldset.append($opciones);
	
	$div.append($boton);
	$div.append($ol);
	if(datos_paciente.patogeno){
		$div.append($iaas);
	}
	$div.append($fieldset);
	
	$div.show();
	$("#simbolo").hide();
}

$(function(){
	traerCamasTemporales(true);
});



</script>

  <meta name="csrf-token" content="{{{ Session::token() }}}">
  {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAKUCTycz-4g3GNG0uRjY6rXGui2PurUAM"></script> --}}
 @stop

 @section("miga")
	<li><a href="#">Gestión de Camas</a></li>
	<li><a href="#">{{$alias}} </a></li>
 @stop


@section("section")
{{ HTML::style('css/navegadortab.css') }}
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

#modalAsignacionCama {
	overflow-y:scroll
}

#modalVerDetalles {
	overflow-y:scroll
}

#modalBoletinPago {
	overflow-y:scroll
}

/*prueba*/
/* #notification_count {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            position: absolute;
            margin-top: -1px;
            font-size: 10px;
		} */
.botones-verdes{
	color:#399865 !important;
}

/* .dropdown-menu > li > a {
	color:#399865 !important;
} */


.table.table-bordered.categorizacion > thead > tr > th {
	color: white;
	font-size: 15px;
}

.table-bordered > tbody > tr > th {
	background: #F5F5F5;
	color: #695959;
	font-size: 15px;
}

.categorizacion {
	width: 83% !important;
	background: #1E9966;
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

<fieldset>
{{ Form::hidden('idCaso', '', array('id' => 'idCaso')) }}
	<div class="form-inline">
	<br><br>
		{{-- <label>Seleccionar vista: </label>
		<select id="vista" class="form-control">
			<option value="1">Vista iconos</option>
			<option value="2">Vista lista</option>
		</select> --}}
	@if(Session::get('usuario')->tipo != TipoUsuario::ADMINCOMERCIAL && Session::get('usuario')->tipo != TipoUsuario::MEDICO)
		{{ HTML::link("unidad/$unidad/exportar", 'Censo diario', ['class' => 'btn btn-default botones-verdes']) }}
    	{{ HTML::link("unidad/$unidad/exportarPdf", 'Pdf Censo diario', ['class' => 'btn btn-danger']) }}
		{{ HTML::link("unidad/todos/exportar", 'Exportar todas las unidades', ['class' => 'btn btn-default botones-verdes', 'target' => '_blank']) }}
		{{--@if ($unidad == "urgencia")
		{{ HTML::link("unidad/$unidad/exportarpacientes", 'Pacientes Urgencia' , ['class' => 'btn btn-default']) }}
		{{ HTML::link("unidad/$unidad/exportarpacientesPdf", 'Pdf Pacientes Urgencia', ['class' => 'btn btn-danger']) }}
		@endif--}}


		<br>
	</div>

	<div class="col form-inline" id="seccionUrgencia" hidden>
		<br>
		{{ HTML::link("unidad/$unidad/exportarExcelListaEspera", 'Excel Lista Espera', ['class' => 'btn btn-default botones-verdes']) }}
		{{ HTML::link("unidad/$unidad/exportarPdfListaEspera", 'Pdf Lista Espera', ['class' => 'btn btn-danger']) }}
	</div>
	@endif

	<legend>Gestión de camas - {{$area_funcional}} <b>{{$detalle_area}}</b> - {{$alias}} </legend>

	<br>
	<div>
		<div class="col-md-12">
			<table class="table table-bordered categorizacion">
				<thead>
					<tr>
						<th>Sin categorización</th>
						{{-- <th>Nohabilitadoparacategorizar</th> --}}
						<th>A1</th>
						<th>A2</th>
						<th>A3</th>
						<th>B1</th>
						<th>B2</th>
						<th>B3</th>
						<th>C1</th>
						<th>C2</th>
						<th>C3</th>
						<th>D1</th>
						<th>D2</th>
						<th>D3</th>
						<th>TOTAL</th>

					</tr>
				</thead>
				<tbody>
					<tr>
						<th>{{$categorizacion[13]}}</th>
						{{-- <th>$categorizacion[14]</th> --}}
						<th>{{$categorizacion[0]}}</th>
						<th>{{$categorizacion[1]}}</th>
						<th>{{$categorizacion[2]}}</th>
						<th>{{$categorizacion[3]}}</th>
						<th>{{$categorizacion[4]}}</th>
						<th>{{$categorizacion[5]}}</th>
						<th>{{$categorizacion[6]}}</th>
						<th>{{$categorizacion[7]}}</th>
						<th>{{$categorizacion[8]}}</th>
						<th>{{$categorizacion[9]}}</th>
						<th>{{$categorizacion[10]}}</th>
						<th>{{$categorizacion[11]}}</th>
						<th>{{$categorizacion[12]}}</th>
					</tr>
				</tbody>
			</table>
		</div>
		@if(Session::get("usuario")->tipo === TipoUsuario::DIRECTOR || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO)
			<div class="col-md-10">
				<div class="alert alert-info">
					{{$dotacion}}
				</div>
			</div>
		@endif


	</div>
<input type='hidden' id="unidad_original" value="{{$id_unidad}}" >



	@if("1" == "4")
		<span id="notification_count"></span>
		<div class="row">
			<div class="col-md-12">
				HTML::link("trasladar/$unidad", 'Traslado Externo', array('class' => 'btn btn-danger btn-lg', 'style' => 'display: none; width: 100%;', 'id' => 'derivar'))
			</div>
		</div>
	@endif

	<br>
	@if($cama_temporal
		&& (
			Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA
			|| Session::get("usuario")->tipo == TipoUsuario::MASTER
			|| Session::get("usuario")->tipo == TipoUsuario::MASTERSS
			|| Session::get("usuario")->tipo == TipoUsuario::ADMIN
		)
	)
	<div id="cama_temporal">
		<div class="col-md-10 bloque-sala">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="headerCama" style="height: 18px;">CAMAS VOLANTES</h3>
				</div> 
				<div class="panel-body" style="height: 161px;">
					<div class="row  row-cama">
						<p style="font-size:18px;padding:5px;">No hay camas volantes.<br>
							Los traslados a camas volantes aparecerán aquí.</p>
					</div>
				</div>
			</div>
		</div>		
	</div>
	@endif
	<div id="mapaSalas" class="mapa-camas-class"></div>
	<div id="tablaCamas" style="display: none;" class="table-responsive">
		<table id="tablaVistaLista" class="table table-striped table-bordered" width="100%">
			<thead>
				<tr>
					<th>Servicio</th>
					<th>Sala</th>
					<th>Cama</th>
					<th>Tipo</th>
					<th>Diagnóstico</th>
					<th>Paciente</th>
					<th>Run</th>
					<th>Categorización</th>
					<th>Ingreso</th>
					<th>Estado</th>
					<th>Tiempo</th>
					<th>Edad</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td colspan="13">
						{{ HTML::link("unidad/$unidad/exportar", 'Exportar', ['class' => 'btn btn-default']) }}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</fieldset>

<div id="modalOpciones" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Opciones</h4>
			</div>
			<div class="modal-body">
				<ul style="font-size: 14px;">


					@if(Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)


						@if(App\Models\Usuario::horarioInhabil())
						{{-- Es horario inhabil y pueden soliicitar cama durante todo el día --}}
							<li> <a href="#modalAsignacionCama" data-toggle="modal" data-dismiss="modal">Asignar cama</a> </li>

						@else
						{{-- En horario habil, solo pueden realizar solicitudes de cama, desde las  17:01 a 7:59--}}
							@if( App\Models\Usuario::horaInhabil() >= 17 || App\Models\Usuario::horaInhabil() < 8 )
								<li> <a href="#modalAsignacionCama" data-toggle="modal" data-dismiss="modal">Asignar cama</a> </li>
							@else
								<p>Solo se puede realizar asignación de cama de Lunes a Viernes desde las <b>17:01 a 7:59</b> y <b>feriados, sabados y domingos</b> todo el día </p>
							@endif

						@endif

					@elseif(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo === TipoUsuario::MATRONA_NEONATOLOGIA)
						<li> <a href="#modalAsignacionCama" data-toggle="modal" data-dismiss="modal">Asignar cama</a> </li>
					@endif

					@if($tipoUsuario != TipoUsuario::$USUARIO || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P)
						@if(Session::get('usuario')->tipo != 'matrona_neonatologia')
							<li> <a href="#modalBloqueo" data-toggle="modal" data-dismiss="modal">Bloquear cama</a> </li>
						@endif
					@endif

					@if(Session::get('usuario')->tipo != 'matrona_neonatologia')
						<li> <a href="#modalReconvertir" data-toggle="modal" data-dismiss="modal">Reconvertir</a> </li>
						<li id="liOriginal"> <a href="#modalReconvertirOriginal" data-toggle="modal" data-dismiss="modal">Reconvertir a original</a> </li>
					@endif

					{{-- Cambiar descripcion --}}
					@if(Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get('usuario')->tipo == 'matrona_neonatologia' || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
						<li> <a href="#modalDescripcion" data-toggle="modal" id="DCama" data-dismiss="modal">Descripcion Cama</a> </li>
					@endif
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalReconvertirOriginal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confimación</h4>
			</div>
			<div class="modal-body">
				<h4>¿ Desea reconvertir a la cama original ?</h4>
			</div>
			<div class="modal-footer">
				<button id="reconvertirOriginal" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>
{{--
falta personal
mala
problemas estructurales
problemas de mantención
otros


1.- cuando se va a procedimiento, la cama sigue ocupado

2.-
crear un paciente que se llame otro sistema
otros -> en otro sistema.


reporte de cama bloqueada
- sacar porcentaje,
- a la izq poner cantidad de camas (criticas)
- agregar tipo de cama en la tabla

Reporte de camas
-
cambiar tecto de deshabilitada a bloqueada


 --}}
<div id="modalBloqueo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Bloquear cama</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea bloquear la cama ?</h4>
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Motivo de bloqueo: </label>
						<div class="col-sm-10">
							{{ Form::select('motivoBloqueo', $motivoBloqueo, null, array('class' => 'form-control', 'id' => 'motivoBloqueo')) }}
						</div>
					</div>
					<div class="form-group col-md-12" style="display:none;" id="divOtroMotivoBloqueo">
						<label for="otroMotivoBloqueo" class="col-sm-2 control-label">Especifique otro motivo: </label>
						<div class="col-sm-10">
							{{ Form::textarea('otroMotivoBloqueo', null, array('class' => 'form-control', 'id' => 'otroMotivoBloqueo')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="bloquear" type="button" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalReconvertir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Reconvertir cama</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea reconvertir la cama ?</h4>
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Servicio: </label>
						<div class="col-sm-10">
							{{ Form::select('servicios', $servicios, null, array('class' => 'form-control', 'id' => 'servicios')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="reconvertir" type="button" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalDesbloquear" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Cama bloqueada</h4>
			</div>
			@if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Auth::user()->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
				{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formDesbloquear')) }}
				{{ Form::hidden('idCama', '', array('id' => 'camaDesbloquear')) }}
			@endif
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label style="font-size: 14px;">Motivo de bloqueo: <span id="msgMotivo"></span></label>
					</div>
					@if((Session::get("usuario")->tipo === TipoUsuario::ADMIN || Auth::user()->tipo == TipoUsuario::MASTER || Auth::user()->tipo == TipoUsuario::MASTERSS) || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
						<div class="form-group col-md-12">
							<h4>¿ Desea desbloquear la cama ?</h4>
						</div>
						<div class="form-group col-md-12">
							<label for="horas" class="col-sm-2 control-label">Motivo de desbloqueo: </label>
							<div class="col-sm-10">
								{{Form::textarea('motivo', null, array('id' => 'motivo', 'class' => 'form-control', 'rows' => '5'))}}
							</div>
						</div>
					@endif
				</div>
			</div>
			<div class="modal-footer">
				@if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Auth::user()->tipo == TipoUsuario::MASTER  || Auth::user()->tipo == TipoUsuario::MASTERSS|| Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
					<button  type="submit" class="btn btn-primary">Aceptar</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				@else
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				@endif
			</div>
			@if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Auth::user()->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO)
				{{ Form::close() }}
			@endif
		</div>
	</div>
</div>






<div id="modalVerDetalles" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Evolución riesgo dependencia
				</h4>
				<div class="form-group" style="float: right; margin-right: 5%; margin-top: -1%;">
						<button id="btn-informe-excel" class="btn btn-success">Descargar Excel</button>
				</div>
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

<div id="modalVerDetallesDieta" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Evolución de dieta
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

<div id="modalVerExamenes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Exámenes / Estudios / Procedimientos</h4>
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


<!-- MODAL OPTIMIZACION -->

<div id="modalOptimizacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" style="width: 80% !important;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<div>
					<h4>Lista de camas para el traslado</h4>
				</div>
			</div>
			<div class="modal-body">
				<div id='id-optimizacion' class='tab-pane' style='margin-top: 20px;'>

				    <table id='optimizacion-table' class='display responsive ' style="width:100%">
					    <thead>
					        <tr>
					    		<th>Calificación</th>
					    		<th>Hospital</th>
					    		<th>Servicio</th>

					    		<th>Sala</th>
					    		<th>Cama</th>
					    		<th>Opción</th>
					    	</tr>
					    </thead>
					    <tbody>

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


<div id="modalSolicitar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Solicitar cama</h4>
			</div>
			<div class="modal-body">

				{{ Form::open(array('class' => 'form-horizontal', 'id' => 'derivarForm')) }}
				{{ Form::hidden('idCama', '', array('id' => 'idCamaDerivar')) }}
				{{ Form::hidden('idEstablecimiento', '', array('id' => 'idEstablecimientoDerivar')) }}
				{{ Form::hidden('idCaso', '', array('id' => 'idCasoDerivar')) }}
				{{ Form::hidden('motivo', "traslado externo", array('id' => 'motivoDerivar')) }}

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
				{{ Form::close() }}
			</div>

			<div class="modal-footer">
				<button class="btn btn-primary" onclick="derivarForm()">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
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


<script>

	$("#optimizacion-table").dataTable({
		responsive: true,
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

</script>






<div id="modalAllta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
				<div class="nombreModal"></div>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formDarAlta', 'autocomplete' => 'off')) }}
			{{ Form::hidden('sala', '', array('id' => 'salaLiberar')) }}
			{{ Form::hidden('cama', '', array('id' => 'camaLiberar')) }}
			{{ Form::hidden('caso', '', array('id' => 'casoLiberar')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea liberar la cama ?</h4>
					</div>
				</div>
				<div class="row " id="divFechaEgreso">
					<div class="form-group col-md-12">
						<label for="fechaEgreso" class="col-sm-2 control-label">Fecha de egreso: </label>
						<div class="col-sm-10">
							{{Form::text('fechaEgreso', null, array('id' => 'fechaEgreso', 'class' => 'form-control fecha-sel'))}}
						</div>
					</div>
					<div class="form-group col-md-12" id="categorizacionesIngreso">
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Destino de alta: </label>
						<div class="col-sm-10">
							{{ Form::select('motivo', $motivo, null, ['class' => 'form-control', "id" => "select-motivo-liberacion"]) }}
						</div>
					</div>
					<div class="form-group col-md-12" id="motivo-liberacion">
					</div>

					{{--<div class="row hidden" >--}}
					<div class="form-group hidden col-md-12" id="fallecimientofecha">
						<label for="fallec" class="col-sm-2 control-label">Fecha: </label>
						<div class="col-sm-10 ">
							{{Form::text('fechaFallecimiento', null, array('id' => 'fechaFallecimiento', 'class' => 'form-control'))}}
						</div>
					</div>
					{{--</div>--}}

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
							{{Form::text('medicoAlta', null, array('id' => 'medicoAlta', 'class' => 'form-control typeahead'))}}
							{{Form::hidden('id_medico', null, array('id' => 'id_medico'))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="ficha" class="col-sm-2 control-label">N° de ficha: </label>
						<div class="col-sm-10">
							{{Form::text('ficha', null, array('id' => 'ficha', 'class' => 'form-control'))}}
						</div>
					</div>
					<div class="form-group col-md-12" id="motivo-liberacion">

					</div>
				</div>
				<div class="row">
					@include('Egreso.partialParto')
				</div>
				
				<div class="row" id="rn_info">
					<div class="form-group col-md-12">
						<label for="run_rn" class="col-sm-2 control-label">Run recien nacido: </label>
						<div class="col-sm-10">
							{{Form::text('rut_rn', null, array('id' => 'rut_rn', 'class' => 'col-md-8 form-control', 'autofocus' => 'true', 'style' => 'width: 150px;'))}}
							<span class="col-md-1 input-group-addon" style="height:34px;"> - </span>
							{{Form::text('dv_rn', null, array('id' => 'dv_rn', 'class' => 'col-md-1 form-control', 'style' => 'width: 70px;'))}}
							<br>
							<br>
						</div>
					</div>
					<div class="form-group col-md-12" id="motivo-liberacion">

					</div>
				</div>

				<div class="row" id="boletinEgresoBtn">
					<div class="col-md-12" id="fichaEgreso">
					</div>

				</div>


			</div>
			<div class="modal-footer">
				<button id="solicitar" type="submit" class="btn btn-primary">Egreso</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>

<div id="modalAltaSinLiberacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
				<div class="nombreModal"></div>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formAltaSinLiberar')) }}
			{{ Form::hidden('sala', '', array('class' => 'salaLiberar')) }}
			{{ Form::hidden('cama', '', array('class' => 'camaLiberar')) }}
			{{ Form::hidden('caso', '', array('class' => 'casoLiberar')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ El paciente desocupo la cama ?</h4>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12 medicoDioAltaOcultar">
						<label for="medicodioalta" class="col-sm-2 control-label">Medico: </label>
						<div class="col-sm-10 medicos">
							{{Form::text('medicoDioAlta', null, array('id' => 'medicoDioAlta', 'class' => 'form-control', 'readonly' => 'true'))}}
							{{Form::hidden('id_medicoDioAlta', null, array('id' => 'id_medicoDioAlta'))}}
						</div>
					</div>
				</div>

				<div class="row">
					<div class=" col-md-6">
						<label class="control-label">¿Cambiar médico que dio alta?: </label>
						<div class="input-group">
							<label class="radio-inline">{{Form::radio('cambiarMedico', "no", true, array('required' => true))}}No</label>
							<label class="radio-inline">{{Form::radio('cambiarMedico', "si", false, array('required' => true))}}Sí</label>
						</div>
					</div>
				</div>
				<br>

				<div class="row">
					<div class="form-group col-md-12 medicoAltaOculto">
						<label for="medicoAlta" class="col-sm-2 control-label">Medico alta: </label>
						<div class="col-sm-10 medicos">
							{{Form::text('medicoAltaLC', null, array('id' => 'medicoAltaLC', 'class' => 'form-control typeahead'))}}
							<span id="errorMedico" class="errorMedico"></span>
							{{Form::hidden('id_medico', null, array('id' => 'id_medico_confirmacion'))}}
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button id="solicitarSLC" type="submit" class="btn btn-primary">Si</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>


<div id="modalRenovar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea renovar la reserva por 6 horas ?</h4>
						<input id="casoRenovar" type="hidden"/>

					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Horas de renovación: </label>
						<div class="col-sm-10">
							<select id="horaRenovar" class="form-control">
								<option value="1">1 hora</option>
								<option value="2">2 horas</option>
								<option value="3">3 horas</option>
								<option value="4">4 horas</option>
								<option value="5">5 horas</option>
								<option value="6" selected>6 horas</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="renovar" type="submit" class="btn btn-primary">Renovar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalReasignar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

{{-- Modal para solicitar traslado interno --}}
<div id="modalSolicitarTrasladoInterno" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Traslado interno</h4>
				<div class="nombreModal"></div>
			</div>
			<div class="modal-body">
				
				<input type="hidden" id="salaSolicitaT"/>
				<input type="hidden" id="camaSolicitaT"/>
				<input type="hidden" id="casoSolicitaT"/>
				<input type="hidden" id="unidadSolicitaT"/>

				{!! Form::label('unidadesSolicitar', 'Servicio destino:', ['class' => 'col-lg-3 control-label']) !!}
				{{Form::select('unidadesSolicitar', $unidades, null, ["id" => "unidadSolicitar", "class" => "form-control"])}}
				<br>

				{!! Form::label('requerimiento_solicitud', 'Requerimiento solicitud:', ['class' => 'col-lg-3 control-label']) !!}
				{{Form::text('requerimiento_solicitud', null, array('id' => 'requerimiento_solicitud', 'class' => 'form-control'))}}
				<br>

				<button type="button" onclick="solicitarTraslado()" class="btn btn-success">Solicitar</button>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
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
				<div class="nombreModal"></div>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formPlan')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>Ingrese nuevo Plan de Tratamiento</h4>
					</div>
				</div>
				<div class="form-group col-md-12">
					<label for="horas" class="col-sm-2 control-label">Detalle: </label>
					<div class="col-sm-10">
						<textarea id="detallePLan" name="detalle" class="form-control" required></textarea>
					</div>
				</div>
        		<br><br><br><br>
			</div>
			<div class="modal-footer">
				<button  type="submit" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>
{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEnviarDerivado', 'autocomplete' => 'off')) }}
	@include('Gestion/formularioDerivacion')
{{ Form::close() }}

{{ Form::open(array('url' => 'asignarCama', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'asignarCamasForm', 'autocomplete' => 'off')) }}
<input type="hidden" name="unidad" value="{{$unidad}}">
<input type="hidden" name="id_unidad" value="{{$id_unidad}}">
{{ Form::hidden('casoHospDom', '', array('id' => 'casoHospDom')) }}
<div id="modalAsignacionCama" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Asignar cama</h4>
				<p>(*) : Campo obligatorio.</p>

			</div>
			<div class="modal-body">
				<div class="formulario" style="overflow-y: scroll;     height: 550px;">
					<div class="panel panel-default">
						<div class="panel-heading panel-info">
							<h4>Datos atención clínica:</h4>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="form-group col-md-2">
									<div class="col-sm-12">
										@if (Auth::user()->tipo == "usuario")
											<label for="dau" class="control-label" title="DAU">DAU (*): </label>
										@else
											<label for="dau" class="control-label" title="DAU">DAU: </label>
										@endif
										{{Form::text('dau', null, array('id' => 'dau', 'class' => 'form-control'))}}
									</div>
								</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12">
										@if (Auth::user()->tipo == "gestion_clinica" || Auth::user()->tipo == "enfermeraP" || Auth::user()->tipo == "supervisora_de_servicio")
											<label for="fichaClinica" class="control-label" title="Ficha clinica">Número de ficha clínica : </label>
										@else
											<label for="fichaClinica" class="control-label" title="Ficha clinica">Número de ficha clínica: </label>
										@endif
										{{Form::text('fichaClinica', null, array('id' => 'fichaClinica', 'class' => 'form-control'))}}
									</div>
								</div>

								<div class="form-group col-md-7">
									<div class="col-sm-12 medicos">
										<label for="medico" class="control-label" title="Médico">Médico: </label>
										{{Form::text('medico', null, array('id' => 'medico', 'class' => 'form-control typeahead'))}}
										{{Form::hidden('id_medico', null)}}
									</div>
								</div>

								<div hidden>
									<div class="col-sm-12">
										<label for="especialidad" class="control-label" title="Especialidad">Especialidad: </label>
										{{Form::text('especialidad', null, array('id' => 'especialidad', 'class' => 'form-control'))}}
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading"><h4>Datos personales:</h4></div>
						<div class="panel-body" id="datosPersonales">
							<div id="divLoadBuscarPaciente" class="row" style="display: none;">
								<div class="form-group col-md-12">
									<span class="col-sm-5 control-label">Buscando paciente </span>
									{{ HTML::image('images/ajax-loader.gif', '') }}
								</div>
							</div>

							<div class="row">
								<div class=" col-md-6">
									<label for="run" class=" control-label">Run: </label>
									<div class="input-group">
										{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
										<span class="input-group-addon"> - </span>
										{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
									</div>
								</div>

								<div class=" col-md-3">
									<div class="form-group">
										<label class="control-label">Recién Nacido: </label>
										<br>
										<label class="radio-inline">{{Form::radio('rn', "no", false, array())}}No</label>
										<label class="radio-inline">{{Form::radio('rn', "si", false, array())}}Sí</label>
									</div>
									{{-- Form::hidden('rn', 'no') --}}
								</div>

								<div class="form-group col-md-3">
									<div class="from-group">
										<label class="control-label" title="Extranjero">Extranjero (*): </label>
										<br>
										<label class="radio-inline">{{Form::radio('extranjero', "no", false, array())}}No</label>
										<label class="radio-inline">{{Form::radio('extranjero', "si", false, array())}}Sí</label>
									</div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="col-md-5">
									<div id="NumPasaporte" style="margin-left: -27px;">
										<label for="rut" class="control-label">Número Pasaporte:</label>
											{{Form::text('n_pasaporte', null, array('id' => 'n_pasaporte', 'class' => 'form-control', 'autofocus' => 'true'))}}
											<p style="color:black;">Ingresar el número de pasaporte en caso de tener.</p>
									</div>
								</div>
								<div class="col-md-5 col-md-offset-1">
									<div id="RnMadre" style="margin-left: -27px;" class="form-group">
										<label for="rut" class="control-label">Rut de la Madre: </label>
											<div class="input-group">
												{{Form::text('rutMadre', null, array('id' => 'rutMadre', 'class' => 'form-control', 'autofocus' => 'true'))}}
												<span class="input-group-addon"> - </span>
												{{Form::text('dvMadre', null, array('id' => 'dvMadre', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
											</div>
											{{-- <span id="spanRN" color="black">Debe ingresar el run de la madre. (*)</span> --}}
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

								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label for="fechaNac" class="control-label" title="Fecha de nacimiento">Fecha de nac.: </label>
										{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control'))}}
									</div>
								</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label for="rango" class="control-label" title="Rango de edad">Rango edad: </label>
										{{ Form::select('rango', array('seleccione' => 'Seleccione', '0-9' => '0-9', '10-19' => '10-19', '20-29' => '20-29', '30-39' => '30-39', '40-49' => '40-49',
										'50-59' => '50-59', '60-69' => '60-69', '70-79' => '70-79', '80-89' => '80-89', '90-99' => '90-99', '100-109' => '100-109',
										'110-119' => '110-119', '120-129' => '120-129'),
										null, array('id' => 'rango', 'class' => 'form-control')) }}
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
										{{--{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }} --}}
										{{ Form::select('sexo', array('masculino' => 'Hombre', 'femenino' => 'Mujer', 'indefinido' => 'Intersex','desconocido' => 'Desconocido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
									</div>
								</div>
							</div>

							<div class="row">
								{{--<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="nombreSocial" class="control-label" title="Nombre Social">Nombre Social: </label>
										{{Form::text('nombreSocial', null, array('id' => 'nombreSocial', 'class' => 'form-control'))}}
									</div>
								</div>--}}

								{{-- <div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="telefono" class="control-label" title="Nombre Social">Teléfono: </label>
										{{Form::number('telefono', null, array('id' => 'telefono', 'class' => 'form-control'))}}
									</div>
								</div> --}}
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
											<td><select class="form-control" name="tipo_telefono[]" id="tipo_telefono_1">
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
								</div>
							</div>

							<br>
							<legend>Datos de dirección</legend>

							<div class="row">
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

							<div class="row">
								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="region" class="control-label" title="Region">Region: </label>
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
						<div class="panel-heading"><h4>Datos de solicitud de cama:</h4></div>
						<div class="panel-body">

							<div class="row">
								<div class="form-group col-md-12">
									<div class="col-sm-12">
										<label for="caso_social" title="Caso social">Procedencia geográfica: </label>
										{{Form::text('procedencia-geo', null, array('id' => 'procedencia-geo', 'class' => 'form-control'))}}

									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6" id="divFechaIngreso">
									<div class="form-group">
										<div class="col-sm-12">
											<label for="fechaIngreso" class="control-label" title="Fecha de ingreso">Fecha de solicitud de cama (*): </label>
											{{Form::text('fechaIngreso', null, array('id' => 'fechaIngreso', 'class' => 'form-control fecha-sel'))}}
										</div>
									</div>
									<div class="form-group col-md-12" id="categorizacionesIngreso">
									</div>
								</div>

								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="tipo-procedencia" class="control-label" title="Procedencia">Origen de la solicitud (*): </label>
										{{ Form::select('tipo-procedencia', [0 => "Seleccionar procedencia"] + $procedencias, 0, array('class' => 'form-control', "id" => "tipo-procedencia")) }}
									</div>
								</div>

							</div>

							<div class="row" id="row-procedencia"></div>

							<div class="form-group col-md-12 estabOculto" style="display: none">
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

							<div id="divMotivo" class="row">
								<div class="form-group col-md-12 pr45">
									<div class="col-sm-12 col-md-12">
										<label for="motivo" class="control-label">Motivo de hospitalización: </label>
										{{Form::textarea('motivo_hosp', null, array('id' => 'motivoC', 'class' => 'form-control', 'rows' => '2', 'enabled'))}}

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="caso_social" title="Caso social">Caso social (*): </label>
										<div class="input-group">
											<label class="radio-inline">{{Form::radio('caso_social', "no", true, array('required' => true))}}No</label>
											<label class="radio-inline">{{Form::radio('caso_social', "si", false, array('required' => true))}}Sí</label>
										</div>
									</div>
								</div>

								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="prevision-lbl" class="control-label" title="Previsión">Previsión: </label>
										{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('id' => 'prevision-lbl', 'class' => 'form-control')) }}
									</div>
								</div>

							</div>

							<div class="row hidden" id="tipo_caso_social">
								<div class="form-group col-md-4">
									<div class="col-sm-12">
										<label for="t_caso_social" title="Tipo de caso social">Tipo de caso social: </label>
										{{ Form::text('t_caso_social', null, array( 'class' => 'form-control')) }}

									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-12">
									<div class="col-sm-1 pr0">
										<label for="riesgo" class="control-label" title="Riesgo">Riesgo: </label>
										{{ Form::text('riesgo', null, array('id' => 'riesgo','class' => 'form-control', 'readonly', 'style' => 'text-align:center;')) }}
									</div>
									<div class="col-sm-3">
										<label >&nbsp;&nbsp;</label>
										<a id="riesgo" type="" class="btn btn-primary w100" onclick="modalRiesgoDependencia()" >Calcular Riesgo - Dependencia</a>
									</div>

									<div class="form-group col-md-4">
										<div class="col-sm-12" >
											<label for="complejidad" class="control-label" title="Unidad">Servicio (*): </label>
											{{ Form::select('complejidad_servicio', array(""=>""), 0 , array('class' => 'form-control', 'id' => 'complejidad_servicio'), $atributos) }}
										</div>
									</div>

									<div class="form-group col-md-4">
										<div class="col-sm-12" >
											<label for="servicios2" class="control-label" title="Unidad">Área funcional(*): </label>
											{{ Form::select('servicios2', array(""=>""), 0 , array('class' => 'form-control', 'id' => 'servicios2')) }}
										</div>
									</div>
									<div class="col-sm-6" id="div-comentario-riesgo" hidden>
										<label for="riesgo" class="control-label" title="Riesgo">Comentario riesgo: </label>
										{{ Form::textarea('comentario-riesgo', null, array('id' => 'comentario-riesgo','class' => 'form-control', 'rows'=>'5')) }}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-12">
									<div class="col-sm-8 diagnostico_cie101 pr0" style="padding-bottom: 20px;">
										<label for="files[]" class="control-label" title="Diagnóstico CIE10">Especialidades:</label>
										{{ Form::select('especialidades[]', $especialidad, null, array('id' => 'especialidades','style' => 'backgroundColor:#000 !important', 'class' => 'selectpicker form-control', 'multiple', 'required', 'data-max-options'=>'3','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;, &quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;]")) }}
										{{ Form::text('especialidades_item', "0", array('class' => 'form-control ', "id" => "especialidades_item", "style" => "height:0px !important; padding:0; border:0px;")) }}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-12">
									<div class="col-sm-8 diagnostico_cie101 pr0" style="padding-bottom: 20px;">
										<label for="files[]" class="control-label" title="Diagnóstico CIE10">Diagnóstico CIE10 (*):</label>
										<input type="text" name="diagnosticos[]" class='form-control typeahead' />
										<input type="hidden" name="hidden_diagnosticos[]">
									</div>
									<div class="col-sm-2 pl0">
										<label>&nbsp;&nbsp;</label>
										<button disabled id="cie10-principal" class="btn btn-default w100" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
									</div>
								</div>

								<div id="fileTemplate" class="hide">
									<div class="form-group col-md-12">

										<div class="col-md-9 diagnostico_cie101">
											<label for="files[]" class="control-label" title="Diagnóstico CIE10">Diagnóstico CIE10:</label>
											<input type="text" name="diagnosticos[]" class='form-control typeahead'/>

											<input type="hidden" name="hidden_diagnosticos[]">
										</div>
										<div class="col-md-3" style="right: 70px; top: 23px">
											<button disabled class="btn btn-default" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
											<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="diagnostico" class="control-label" title="Comentario de diagnóstico">Complemento del diagnóstico: </label>
										{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
									</div>
								</div>
							</div>

						</div>
					</div>

					<div hidden>
						<div class="col-sm-12">
							<label for="tipo" class="control-label" title="Acción">Acción: </label>
							{{ Form::select('tipo', array('ingresar' => 'Ingresar'), null, array('id' => 'tipo', 'class' => 'form-control')) }}
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

				<br>

				{{ Form::hidden('sala', '', array('id' => 'salaReserva')) }}
				{{ Form::hidden('cama', '', array('id' => 'camaReserva')) }}
				{{ Form::hidden('id', '', array('id' => 'id')) }}
				{{ Form::hidden('latitud', null, array('id' => 'latitud')) }}
				{{ Form::hidden('longitud', null, array('id' => 'longitud')) }}

				<div class="modal-footer">
					{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }}
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>

				</div>
			</div>
		</div>
	</div>



<div id="modalFormularioRiesgo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<div class="modal-title">Formulario de Riesgo - Dependencia</div>
				<div id="nombre_paciente_formulario"></div>

			</div>

			<div class="modal-body">
				<div class="row" style="margin: 0;padding-bottom:15px;">
					<legend for="horas" class="col-sm-12 control-label">CUIDADOS QUE IDENTIFICAN DEPENDENCIA </legend>
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
								<option value="1" data-subtext="Usuario se alimenta por vía oral, con ayuda y supervisión">1 pts.</option>
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

				<div class="row" style="margin: 0;padding-bottom:15px;">
					<legend for="horas" class="col-sm-12 control-label">CUIDADOS ESPECIFICOS DE ENFERMERIA QUE IDENTIFICAN RIESGO </legend>
				</div>

				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">1.- Medición diaria de Signos Vitales (2 o mas parámetros simultáneos): </label>
						<label for="horas" class="col-sm-4 control-label">Presión arterial, temperatura corporal, frecuencia cardiaca, frecuencia respiratoria, nivel de dolor y otros</label>
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
						<label for="horas" class="col-sm-4 control-label">Intervenciones quirurgicas y procedimientos invasivos, tales como punciones, toma de muestras, instalaciones de las vías, tubos, sondas, etc.</label>
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
				<a  type="button" class="btn btn-primary"  onclick="btnRiesgoDependencia()">Aceptar</a>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

{{ Form::close() }}

<div id="modalDocumentosDerivacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Documentos de derivación</h4>
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

<div id="modalEnviarDerivado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Enviar a derivación</h4>
                <div class="nombreModal"></div>
            </div>
            {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEnviarDerivado')) }}
            {{ Form::hidden('idCaso', '', array('class' => 'idCaso')) }}

            <div class="modal-footer">
                <button  type="submit" class="btn btn-primary">Derivar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<div id="modalquitarDerivado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Quitar paciente de lista de Derivados</h4>
      </div>
      {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formquitarDerivados')) }}
      {{ Form::hidden('idCaso', '', array('class' => 'idCaso')) }}
	  {{ Form::hidden('idLista', '', array('class' => 'idLista', 'id' => 'idListaQuitarDerivados')) }}
      <div class="modal-footer">
        <button id="btnQuitarDerivado"  type="submit" class="btn btn-primary">Quitar paciente </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>


<div id="modalEnviarPabellon" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEnviarPabellon')) }}

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Enviar a Pabellón</h4>
				<div class="nombreModal"></div>

				<div class="" id="comentario">
					<label for="comentario" class="control-label" title="Comentario">Comentario: </label>
					{{ Form::textarea('comentario', null, array('id' => 'comentario','class' => 'form-control', 'rows'=>'2', 'required' => 'required')) }}
				</div>
            </div>

            {{ Form::hidden('idCaso', '', array('class' => 'idCaso')) }}

            <div class="modal-footer">
                <button  type="submit" class="btn btn-primary">Enviar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
            {{ Form::close() }}
		</div>
    </div>
</div>

<!-- MODAL RIESGO-->

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
        <button  type="submit" class="btn btn-primary">Quitar paciente </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>



<div id="modalBoletinPago" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Boletín de pago</h4>
				<div class="nombreModal"></div>
			</div>
			<div class="modal-body">
				@include("Gestion.boletinPago.general")
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalDescripcion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Cambiar Descripcion</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea cambiar la descripcion ?</h4>
					</div>
					<div class="form-group col-md-12">
						<label for="horas" class="col-sm-2 control-label">Descripcion: </label>
						<div class="col-sm-10">
							{{-- Form::select('Descripcion', array('Crítica' => 'Crítica', 'Media' => 'Media', 'Básica' => 'Básica'), array('class' => 'form-control', 'id' => 'cambioDescripcion' )) --}}
							<select id="cambioDescripcion" class ="form-control">
								<option value="Crítica">Crítica</option>
								<option value="Intermedia">Intermedia</option>
								<option value="Media">Media</option>
								<option value="Básica">Básica</option>
								<option value="">Ninguna Descripcion</option>
							</select>
							{{ Form::hidden('IDdecama', null, array('class' => 'form-control', 'id' => 'idcamacambio')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="cambiarD" type="button" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>


<div id="modalHistorialVisitas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Historial de visitas Paciente <button type="button" class="btn btn-danger" id="btn_descargar_historial_visita">PDF</button></h4>
				<p class="visitas-datosPacientes" style="margin-bottom: 0px;"></p>
            </div>
            <div class="modal-body">
				<form id="historial_edicion_registro_visitas">
					<input type="hidden" name="id_caso_" id="id_caso_historial_visitas">
					<div id="configuracion_visitas_solo_lectura">
						<div class="col-md-3 h5 visitas-bool" style="padding-left: 0;">
						</div>
						<div class="col-md-3 h5 visitas-comentarios" hidden></div>
						<div class="col-md-3 h5 visitas-cantPersonas text-center">
						</div>
						<div class="col-md-3 h5 visitas-cantHoras text-right">
						</div>
					</div>					
					<div id="configuracion_visitas_edicion" hidden>
						<div class="col-sm-3">
							<label class="control-label">¿Puede recibir visitas?</label><br>
							<input type="radio" id="recibe_visitas_si" name="recibe_visitas_" value="true">Sí&nbsp;
							<input type="radio" id="recibe_visitas_no" name="recibe_visitas_" value="false">No
						</div>
						<div id="input_comentario_visitas" hidden>
							<div class="col-sm-3 form-group">
								<label class="control-label">Comentario</label><br>
								<input type="text" name="comentario_visitas_" id="comentario_visitas_" class="form-control">
							</div>
						</div>
						<div id="inputs_config_historial">
							<div class="col-sm-3">
								<label class="control-label">Cantidad de personas</label>
								<input type="number" min="1" max="6" id="cantidad_personas_" name="cantidad_personas_" class="form-control">
							</div>
							<div class="col-sm-3">
								<label class="control-label">Cantidad de horas</label>
								<input type="number" min="1" max="6" id="cantidad_horas_" name="cantidad_horas_" class="form-control">
							</div>
						</div>
						<div id="input_comentario_visitas_" hidden>
							<div class="col-sm-6 form-group">
								<label class="control-label">Comentario</label><br>
								<input type="text" name="comentario_visitas_" id="comentario_visitas_" class="form-control">
							</div>
						</div>
					</div>
					<div class="col-md-3">
						@if(Session::get("usuario")->tipo != 'admin')
						<div id="div_btn_editar_registro_visita">
							<button type="button" class="btn btn-primary" id="btn_editar_registro_visita">Editar</button>
						</div>
						<div id="div_btn_guardar_registro_visita" hidden>
							<br>
							<button type="submit" class="btn btn-primary" id="btn_guardar_registro_visita">Guardar</button>
						</div>
						@endif
					</div>
				</form>
				<br>
				<br>
				
				<table id="tableHistorialVisitas" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>N°</th>
							<th>Fecha</th>
							<th>Hora ingreso</th>
							<th>Nombre completo</th>
							<th>RUN</th>
							<th>Telefono</th>
							<th>Relación con el paciente</th>
							<th>Usuario responsable</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


@stop
