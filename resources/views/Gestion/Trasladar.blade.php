@extends("Templates/template")

@section("titulo")
Traslado paciente
@stop

@section("script")

<script>

function modalRiesgoDependencia(){
    $("#modalFormularioRiesgo").modal();
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

  $("#riesgo").val(riesgoDependencia);

  $('#modalFormularioRiesgo').modal('hide');

}

$( document ).ready(function() {
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
	$(document).on("input","input[name='diagnosticos[]']",function(){
		var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
		
		if($cie10.val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
			
		}
	});
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
			console.log(data);
			if(data.nombre_categoria == null){
				return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
			}else{
				return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
			}	
		},
		header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
	  }
	}).on('typeahead:selected', function(event, selection){
			//$("#texto_cie10").val(selection.nombre_cie10);
			$("[name='hidden_diagnosticos[]']").val(selection.id_cie10);

	}).on('typeahead:close', function(ev, suggestion) {
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
		
	});


	var count=0;

	

	agregar=function(){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		$clone.addClass("otroDiagnostico");
		//var $clone =  $template.clone().removeClass('hide').insertBefore($template);

		//console.log($clone.find("input"));
		//var el = $("<input type='text' name='retrun-order-invoice_no' class='return-order-invoice_no'>").insertAfter($('#fileTemplate'));

		$clone.find("input").eq(2).val("");
		console.log($clone.find("input")[1]);
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

	borrar=function(boton){
		$(boton).parent().parent().parent().remove();
	}

	borrar2=function(boton){
		/* console.log("botonnnn ",$(boton).parent().parent().remove()); */
		$(boton).parent().parent().remove();
	}
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
			limit: 6
		});

		datos_cie10.initialize();
	    /* console.log('focus acheived'); */

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
				if(data.nombre_categoria == null){
					return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
				}else{
					return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
				}	
			},
			header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
		  }
		}).on('typeahead:selected', function(event, selection){
			console.log("algo raro paso", selection);
				//$("#texto_cie10").val(selection.nombre_cie10);
			$(self2).val(selection.id_cie10);
			
		}).on('typeahead:close', function(ev, suggestion) {
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
		
	});

	}

	var tipoSubmit="";
	var MAX_OPTIONS = 5;
	var count=0;
	var $eventoExtrasistema = false;
	//var registrarExtraSistemaEnviado=false;
	var getPacienteCallback = function(data){
		console.log(data);
		if(rut != ""){
			$("#registrarPaciente").prop("disabled", true);
			$("#rut").val(data.rutSin);
			$("#dv").val(data.dv);
			$("#fechaNac").datepicker('update', data.fecha);
			$("#nombre").val(data.nombre);
			$("#nombreSocial").val(data.nombreSocial);
			$("#sexo").val(data.genero);
			$("#apellidoP").val(data.apellidoP);
			$("#apellidoM").val(data.apellidoM);
			$("#diagnostico").val(data.comentario_diagnostico);
			$('[name="diagnosticos[]"]').val(data.diagnostico);
			$('[name="hidden_diagnosticos[]"]').val(data.id_cie_10);
			
			/* console.log("data. algo ", data.riesgo["categoria"]); */
			
			/* console.log("rao", $("#riesgo").val("") ) ;
			console.log("ra2o", Number.parseInt(data.riesgo["dependencia1"])  ) ; */
			

			
			if (data.riesgo != null ) {
				console.log("hola");
				$("#riesgo").val(data.riesgo["categoria"]);
				$("#dependencia1").val(Number.parseInt(data.riesgo["dependencia1"]));
				$("#dependencia2").val(Number.parseInt(data.riesgo["dependencia2"]));
				$("#dependencia3").val(Number.parseInt(data.riesgo["dependencia3"]));
				$("#dependencia4").val(Number.parseInt(data.riesgo["dependencia4"]));
				$("#dependencia5").val(Number.parseInt(data.riesgo["dependencia5"]));
				$("#dependencia6").val(Number.parseInt(data.riesgo["dependencia6"]));
				$("#riesgo2").val(Number.parseInt(data.riesgo["riesgo2"]));
				$("#riesgo1").val(Number.parseInt(data.riesgo["riesgo1"]));
				$("#riesgo3").val(Number.parseInt(data.riesgo["riesgo3"]));
				$("#riesgo4").val(Number.parseInt(data.riesgo["riesgo4"]));
				$("#riesgo5").val(Number.parseInt(data.riesgo["riesgo5"]));
				$("#riesgo6").val(Number.parseInt(data.riesgo["riesgo6"]));
				$("#riesgo7").val(Number.parseInt(data.riesgo["riesgo7"]));
				$("#riesgo8").val(Number.parseInt(data.riesgo["riesgo8"]));
			}else{
				console.log("no tiene categoria");
			}
						
			/* document.getElementsByClassName('diagnosticos').innerHTML=''; */
			/* $('#dependencia1').val("null"); */
			console.log("diagnosticos: ",data.diagnosticos );
			$( ".primerDiagnostico" ).remove();
			$( ".otroDiagnostico" ).remove();
			
			primero = "<div class='form-group col-md-12 primerDiagnostico'> <div class='col-sm-2'>	<label for='files[]' class='control-label'>Diagnóstico CIE10 (*):</label> </div> <div class='col-sm-9 diagnostico_cie101'> <input type='text' name='diagnosticos[]' class='form-control typeahead'  />	<input type='hidden' name='hidden_diagnosticos[]' > </div> <div class='col-sm-1' style='right: 50px;'> <button class='btn btn-default' type='button' onclick='agregar();'><span class='glyphicon glyphicon-plus-sign'></span></button> </div> </div>";

			typeheadPrimero = $( ".diagnosticos" ).append(primero);
			
			invoice_no_setup_typeahead(typeheadPrimero.find("input")[0], typeheadPrimero.find("input")[1]);
			
			var contador = 0;
			if( data.diagnosticos != null ){
				data.diagnosticos.forEach(function(entry) {
					texto = "<div class='form-group col-md-12 otroDiagnostico'> <div class='col-md-2'></div> <div class='col-md-8 diagnostico_cie101'> <input type='text' name='diagnosticos[]' class='form-control typeahead' value='"+entry.diagnostico+"' readonly/> <input type='hidden' name='hidden_diagnosticos[]' value='"+entry.id_cie_10+"'> </div> <div class='col-md-2' style='right: 50px;'> <button class='btn btn-default' type='button' onclick='borrar2(this);'><span class='glyphicon glyphicon-minus-sign'></span></button> </div> </div>";
					$( ".nuevos" ).append(texto);
						
				}); 
			}
			
			

			$('#realizarDerivacion').bootstrapValidator('revalidateField', 'fechaNac');
			$('#realizarDerivacion').bootstrapValidator('revalidateField', 'nombre');
			/* $('#realizarDerivacion').bootstrapValidator('revalidateField', 'diagnostico[]'); */
			$("#realizarDerivacion input[type='submit']").prop("disabled", false);
 			$("#realizarDerivacion button[type='submit']").prop("disabled", false);
			//buscarCama();
		}
	}

	var getPaciente=function(rut){
		$.ajax({
			url: "{{URL::to('/')}}/getPaciente",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {rut: rut, dv: $("#dv").val()},
			dataType: "json",
			type: "post",
			success: getPacienteCallback,
			error: function(error){
				console.log(error);
			}
		});
	}
	var getPacienteId=function(id) {
		$.ajax({
			url: "{{URL::to('/')}}/getPaciente",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {id: id},
			dataType: "json",
			type: "post",
			success: getPacienteCallback,
			error: function (error) {
				console.log(error);
			}
		});
	}

	var buscarCama=function(){
		$.ajax({
			url: "{{URL::to('/')}}/getCamasDisponibles",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			type: "post",
			dataType: "json",
			@if($unidad_obj)
			data: {unidad: "{{$unidad_obj->url }}"},
			@else
			data: {unidad: ""},
			@endif
			success: function(data){
				if(data.datos.length == 0){
					$("#tableCamas").hide();
					$("#tableCamas_wrapper").hide();
					$("#mensajeNoCamas").html(data.mensaje);
					$("#noCamas").show();
				}
				else{
					$("#tableCamas_wrapper").show();
					$("#tableCamas").dataTable().fnClearTable();
					$("#tableCamas").dataTable().fnAddData(data.datos);
					$("#noCamas").hide();
				}
				$("#derivarCamas").show();
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var abrirSolicitar=function(id){
		tipoSubmit="solicitar";
		var valid=$("#realizarDerivacion").data('bootstrapValidator').validate();
		$("#registrarTrasladoBtn").prop("disabled", false);
		console.log(valid.$invalidFields);
		console.log(valid.$invalidFields.length);
		if(valid.$invalidFields.length == 0){
			$("#asunto").prop("disabled", false);
			$("#texto").prop("disabled", false);
			$(".file").prop("disabled", false);
			$("#idEstablecimiento").val(id);
			$("#modalSolicitar").modal();
		}
		else{
			$(".file").prop("disabled", true);
			$("#asunto").prop("disabled", true);
			$("#texto").prop("disabled", true);
		}
	}

	var registrarPaciente=function(form){
		$.ajax({
			url: "{{URL::to('/')}}/registrarPaciente",
			type: "post",
			dataType: "json",
			data: form.serialize(),
			success: function(data){
				if(data.exito) swalExito.fire({
								title: 'Exito!',
								text: data.exito,
								});
				if(data.error) {
					swalError.fire({
					title: 'Error',
					text:data.error
					});
					console.log(data.msg);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var trasladar=function(form){
		$.ajax({
			url: "{{URL::to('/')}}/registrarTraslado",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			type: "post",
			dataType: "json",
			data: new FormData(form),
			cache: false,
			contentType: false,
			processData: false,
			success: function(data){
				if(data.exito){
					swalExito.fire({
					title: 'Exito!',
					text: data.exito,
					didOpen: function() {
						setTimeout(function() {
							window.location.replace("{{ URL::to('derivaciones/enviadas') }}");
						}, 2000)
					},
					});
					
				} 
				if(data.error) {
					swalError.fire({
					title: 'Error',
					text:data.error
					});
					console.log(data.msg);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var registrarExtraSistema=function(form){
		$.ajax({
			url: "{{URL::to('/')}}/trasladar/registrarExtraSistema",
			headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			type: "post",
			dataType: "json",
			data: form.serialize(),
			success: function(data){
				//registrarExtraSistemaEnviado=true;
				if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								window.location.replace("{{ URL::to('trasladar/trasladosExtraSistema') }}");
							}, 2000)
						},
						});

				
				} 
				if(data.error) {
					swalError.fire({
					title: 'Error',
					text:data.error
					});
					console.log(data.msg);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var dv_callback = function(value, validator, $field){
		var field_rut = $("#rut");
		var dv = $("#dv");
		var field_rut_val = $.trim(field_rut.val());
		var dv_val = $.trim(dv.val());
		if(field_rut_val == '' && dv_val == '') {
			return true;
		}
		if(field_rut_val != '' && dv_val == ''){
			return {valid: false, message: "Debe ingresar el dígito verificador"};
		}
		if(field_rut_val == '' && dv_val != ''){
			return {valid: false, message: "Debe ingresar el run"};
		}
		var esValido=esRutValido(field_rut_val, dv_val);
		if(!esValido){
			return {valid: false, message: "Dígito verificador no coincide con el run"};
		}
		else{
			getPaciente(field_rut_val);
		}
		return true;
	}

	var rut_callback = function(value, validator, $field){
		var field_rut = $.trim($("#rut").val());
		var dv = $.trim($("#dv").val());
		if (!esRutValido(field_rut, dv)){
			$("#dv").val('');
		}
		return true;
	}

	$(function(){
		$("#solicitudMenu").collapse();

		@if($paciente)
		getPacienteId('{{$paciente->id}}');
		@endif
		$("#selectEstablecimiento").on("change", function(){
			//alert();
			console.log($(this).val());
			$.ajax({
				url: "{{URL::to('getUnidadesSelect') }}",
				headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "get",
				dataType: "json",
				data: { selectEstablecimiento: $(this).val() },
				success: function(data){
					var select = "";
					for(var i in data){
						select += "<option value='" + i + "'>" + data[i] + "</option>";
					}
					var disabled=(select == "") ? true : false;
					$("#solicitarTrasladoBtn").prop("disabled", disabled);
					$("#selectUnidades").html(select);
				},
				error: function(error){
					console.log(error);
				}
			});
		});
		$('#fileMain').fileinput();
		$(".file-input-new .input-group").css("width", "100%");

		$("#registrarPaciente").on("click", function(){
			tipoSubmit="registrar";
		});

		$("#registrarTrasladoBtn").on("click", function(){
			console.log("registro un traslado");
			tipoSubmit="trasladar";
		});

		$("#registroExtraSistema").on("click", function(){
			console.log("ea");
			tipoSubmit="extraSistema";
		});

		$("#addEstab").on("click", function(){
			$("#estabExterno").prop("disabled", false);
			$("#divEstabExterno").show();
			$("#addEstab").closest(".form-group").hide();
		});

		$("#btnGuardarEstab").on("click", function(){
			$.ajax({
				url: "{{URL::route("nuevoEstablecimientoExtrasistema")}}",
				headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "post",
				dataType: "json",
				data: {estabExterno: $("#estabExterno").val()},
				success: function(data){
					$("#estabsExterno").closest("div").html(data.exito);
					$("#estabExterno").val("");
					$("#estabExterno").prop("disabled", true);
					$("#divEstabExterno").hide();
					$("#addEstab").closest(".form-group").show();
				},
				error: function(error){
					$("#estabExterno").val("");
					$("#estabExterno").prop("disabled", true);
					$("#divEstabExterno").hide();
					$("#addEstab").closest(".form-group").show();
				}
			});
		});

		$('#modalSolicitar').on('hidden.bs.modal', function() {
 			$(".file").prop("disabled", true);
			$("#asunto").prop("disabled", true);
			$("#texto").prop("disabled", true);
 		});

		$("#realizarDerivacion").bootstrapValidator({
 			 excluded: [':disabled', ':hidden', ':not(:visible)'],
 			 fields: {
 			 	rut: {
 			 		validators:{
						remote: {
							url: "{{ URL::to("/validarParaTraslado") }}"
						},
						callback: {
							callback: rut_callback
						}
 			 		}
 			 	},
 			 	dv: {
 			 		validators:{
 			 			callback: {
 			 				callback: dv_callback
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
 			 						return {
                                        valid: false,
                                        message: "La fecha de nacimiento no puede ser mayor a la fecha actual"
                                    };
 			 					}
 			 					var esValidao=validarFormatoFecha(value);
 			 					if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
 			 					return true;
 			 				}
 			 			}
 			 		}
 			 	},
 			 	// diagnostico: {
 			 	// 	validators:{
 			 	// 		notEmpty: {
 			 	// 			message: 'El diagnóstico es obligatorio'
 			 	// 		}
 			 	// 	}
 			 	// },
 			 	'diagnosticos[]': {
 			 	    validators: {
 			 	        notEmpty: {
 			 	            message: 'Debe ingresar al menos 1 diagnóstico'
 			 	        }
 			 	    }
 			 	},
 			 	asunto: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El asunto es obligatorio'
 			 			}
 			 		}
 			 	},
 			 	texto: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El texto del asunto es obligatorio'
 			 			}
 			 		}
 			 	}
 			 }
 		}).on('status.field.bv', function(e, data) {
 			$("#registrarPaciente").prop("disabled", false);
        }).on("success.form.bv", function(evt){
        	console.log($eventoExtrasistema);
        	$("#registrarPaciente").prop("disabled", false);
 			evt.preventDefault(evt);
 			var $form = $(evt.target);


 			if(tipoSubmit == "registrar") registrarPaciente($form);
 			if(tipoSubmit == "trasladar") trasladar($form[0]);
 			if(tipoSubmit == "extraSistema"  && $eventoExtrasistema == false){
				$eventoExtrasistema = true;
			
				registrarExtraSistema($form);
 			}
 			$("#realizarDerivacion input[type='submit']").prop("disabled", false);
 			$("#realizarDerivacion button[type='submit']").prop("disabled", false);
 			/*AQUI*/
 		}).on('click', '.addButton', function() {
            var $template = $('#optionTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .insertBefore($template),
                $option   = $clone.find('[name="files[]"]');
                $option.prop("disabled", false);
                var id="id_"+count;
                $option.prop("id", id);
                $('#'+id).fileinput();
                $(".file-input-new .input-group").css("width", "100%");
                count++;
            $('#realizarDerivacion').bootstrapValidator('addField', $option);
        }).on('click', '.removeButton', function() {
            var $row    = $(this).parents('.form-group'),
                $option = $row.find('[name="files[]"]');
            $row.remove();
            $('#realizarDerivacion').bootstrapValidator('removeField', $option);
        }).on('added.field.bv', function(e, data) {
            if (data.field === 'files[]') {
                if ($('#realizarDerivacion').find(':visible[name="files[]"]').length >= MAX_OPTIONS) {
                    $('#realizarDerivacion').find('.addButton').attr('disabled', 'disabled');
                }
            }
        }).on('removed.field.bv', function(e, data) {
           if (data.field === 'files[]') {
                if ($('#realizarDerivacion').find(':visible[name="files[]"]').length < MAX_OPTIONS) {
                    $('#realizarDerivacion').find('.addButton').removeAttr('disabled');
                }
            }
        });

		$('#modalSolicitar').on('hidden.bs.modal', function (e) {
			$("#divErroresDerivar div ol").empty();
			$("#divErroresDerivar").hide();
		});

		$("#fechaNac").datepicker({
			autoclose: true,
			language: "es",
			format: "dd-mm-yyyy",
			todayHighlight: true,
			endDate: "+0d"
		}).on("changeDate", function(){
			$('#realizarDerivacion').bootstrapValidator('revalidateField', 'fechaNac');
		});

		$('#tableCamas').dataTable({
 			"aaSorting": [[0, "asc"]],
 			"iDisplayLength": 5,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{ URL::to('/') }}/js/spanish.txt"
 			}
 		});

 		$("#solicitarTrasladoBtn").on("click", function(){
 			abrirSolicitar($("#selectUnidades").val());
 		});

 		$("#buscarCama").on("click", function(){
 			buscarCama();
 		});

 		$("#selectEstablecimiento").trigger("change");
 		@if($unidad_obj)
 		buscarCama();
 		@endif
 	});
 </script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<li><a href="#">Gestión de Camas</a></li>
<li><a href="#">Traslado Paciente</a></li>
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
</style>

{{ Form::open(array('url' => 'realizarDerivacion', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'realizarDerivacion', 'onsubmit' => 'return false', 'files'=> true)) }}
{{ Form::hidden('idEstablecimiento', '', array('id' => 'idEstablecimiento')) }}
@if($unidad_obj)
{{ Form::hidden('unidad', "$unidad_obj->url", array('id' => 'unidad')) }}
@endif
@if($paciente)
{{ Form::hidden('rutCaso', $paciente->rut, array('id' => 'rutCaso')) }}
{{ Form::hidden('idPaciente', $paciente->id, array("id" => "idPaciente")) }}
@else
{{ Form::hidden('rutCaso', null, array('id' => 'rutCaso')) }}
{{ Form::hidden('idPaciente', null, array("id" => "idPaciente")) }}
@endif
{{ Form::hidden('motivo', "traslado externo", array('id' => 'motivo')) }}
<fieldset>
	@if($unidad_obj)
	<legend>Gestión Camas {{$unidad_obj->alias}}</legend>
	@else
	<legend>Solicitar traslado externo</legend>
	@endif
	<div class="row">
		<div class="form-group col-md-6">
			<label for="run" class="col-sm-2 control-label">Run: </label>
			<div class="col-sm-10">
				<div class="input-group">
					@if(!$paciente)
					{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
					@else
					{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true', 'readonly'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;', 'readonly'))}}
					@endif
				</div>
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Fecha de nacimiento: </label>
			<div class="col-sm-10">
				{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control', 'required' => "true"))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="nombre" class="col-sm-2 control-label">Nombre: </label>
			<div class="col-sm-10">
				{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Género: </label>
			<div class="col-sm-10">
				{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
			</div>
		</div>

	</div>

	<div class="row">
		<div class="form-group col-md-6">
			<label for="nombre" class="col-sm-2 control-label">Nombre Social: </label>
			<div class="col-sm-10">
				{{Form::text('nombreSocial', null, array('id' => 'nombreSocial', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="apellido" class="col-sm-2 control-label">Apellido paterno: </label>
			<div class="col-sm-10">
				{{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control'))}}
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Apellido materno: </label>
			<div class="col-sm-10">
				{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="prevision" class="col-sm-2 control-label">Previsión: </label>
			<div class="col-sm-10">
				{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('class' => 'form-control')) }}
			</div>
		</div>
		<!-- <div class="form-group col-md-6">
			<label for="riesgo" class="col-sm-2 control-label">Riesgo: </label>
			<div class="col-sm-10">
				{{ Form::select('riesgo', $riesgo, null, array('id' => 'riesgo', 'class' => 'form-control')) }}
			</div>
		</div> -->
	</div>

	<div class="row">
			<div class="form-group col-md-12">
				<label for="riesgo" class="col-sm-2 control-label">Riesgo: </label>
				<div class="col-sm-10" style="width: 25%;">
					{{ Form::text('riesgo', null, array('id' => 'riesgo','class' => 'form-control', 'readonly', 'style' => 'text-align:center;')) }}

				</div>
				<div>
					<a id="riesgo" type="" class="btn btn-primary" onclick="modalRiesgoDependencia()" >Calcular Riesgo - Dependencia</a>
				</div>
			</div>
	</div>


		<div class="row diagnosticos">
			<div class="form-group col-md-12 primerDiagnostico">
				<div class="col-sm-2">
						<label for="files[]" class="control-label">Diagnóstico CIE10 (*):</label>
				</div>
				<div class="col-sm-9 diagnostico_cie101">
					<input type="text" name="diagnosticos[]" class='form-control typeahead' />
					<input type="hidden" name="hidden_diagnosticos[]">
				</div>
				<div class="col-sm-1" style="right: 50px;">
						<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
				</div>
			</div>
		</div>

		<div id="fileTemplate" class="row hide">
			<div class="form-group col-md-12">
				<div class="col-md-2"></div>
				<div class="col-md-8 diagnostico_cie101">
					<input type="text" name="diagnosticos[]" class='form-control typeahead'/>
					<input type="hidden" name="hidden_diagnosticos[]">
				</div>
				<div class="col-md-2" style="right: 50px;">
					<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
					<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
				</div>
			</div>
		</div>

		<div class="nuevos">

		</div>
	    


	<div class="row">
			<div class="form-group col-md-12">
				<label for="diagnostico" class="col-sm-2 control-label">Comentario de diagnóstico: </label>

				<div class="col-sm-10">
					{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
				</div>
			</div>
		</div>
	<!-- <div class="row">
		<div class="form-group col-md-6">
			<label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>
			<div class="col-sm-10">
				{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
			</div>
		</div>

		<div class="form-group col-md-6">
			{{-- Form::submit('Registrar paciente', array('id' => 'registrarPaciente', 'class' => 'btn btn-primary')) --}}
			{{--
			@if($unidad_obj)
			<a id="buscarCama" class="cursor btn btn-primary">Buscar Camas</a>
			@endif
			--}}
		</div>
	</div> -->

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
</fieldset>
@if($patogeno!='0')
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

<div id="modalSolicitar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
				<button id="registrarTrasladoBtn" type="submit" class="btn btn-primary">Aceptar</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

@if($unidad_obj)
<div class="row">
	<h4>Seleccionar establecimiento destino dentro del Servicio de Salud por cupo</h4>
	<fieldset><legend></legend>
	<div id="derivarCamas" style="display: none;">
		<div class="table-responsive">
		<table id="tableCamas" class="table table-striped table-condensed table-bordered">
			<thead>
			<tr>
				<th>Establecimiento</th>
				<th>Servicio</th>
				<th>Cupos</th>
				<th>Solicitar</th>
			</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</div>
		<div id="noCamas">
			<div class="alert alert-danger" role="alert" style="text-align: center; font-size: 14px;">
				<strong id="mensajeNoCamas"></strong>
			</div>
		</div>
	</div>
	</fieldset>
</div>
@endif

<div class="row">
	<h4>Seleccionar establecimiento y servicio destino</h4>
	<fieldset><legend></legend>
		<div id="seleccionarDestino">
			<div class="row">
				<div class="form-group col-md-12">
					<div class="col-sm-5">
						{{Form::select('selectEstablecimiento', App\Models\Establecimiento::getEstablecimientos(false, [Session::get("idEstablecimiento")]), null, array('id' => 'selectEstablecimiento', 'class' => 'form-control'))}}
					</div>
					<div class="col-sm-5">
						{{Form::select('selectUnidades', [], null, array('id' => 'selectUnidades', 'class' => 'form-control'))}}
					</div>
					<div class="col-sm-2">
						<button id="solicitarTrasladoBtn" type="submit" class="btn btn-primary">Solicitar</button>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>

<br><br>
<div class="row">
	<h4>Seleccionar establecimiento extra sistema</h4>
	<fieldset><legend></legend>
		<div class="form-group col-md-10">
			<label for="estabsExterno" class="col-sm-2 control-label">Hospital externo:<br>(Privado) </label>
			<div class="col-sm-8">
				{{ Form::select('estabsExterno', $establecimiento, null, array('id' => 'estabsExterno', 'class' => 'form-control')) }}
			</div>
			<div class="col-sm-2">
				<button id="registroExtraSistema" type="submit" class="btn btn-primary">Solicitar al extra sistema</button>
			</div>

		</div>
		<div id="divEstabExterno" class="form-group col-md-10" style="display: none;">
			<label for="estabExterno" class="col-sm-2 control-label">Establecimiento: </label>
			<div class="col-sm-8">
				{{Form::text('estabExterno', null, array('id' => 'estabExterno', 'class' => 'form-control', 'disabled', 'required'))}}
			</div>
			<div class="col-sm-2">
				{{Form::button('Guardar establecimiento', array("class" => "btn btn-primary","id" => "btnGuardarEstab"))}}
			</div>
		</div>
		<div class="form-group col-md-10">
			<div class="col-sm-2"></div>
			<div class="col-sm-2">
				<button id="addEstab" type="button" class="btn btn-primary">Agregar establecimiento</button>
			</div>
		</div>
	</fieldset>
</div>


{{ Form::close() }}

<br><br>

@stop
