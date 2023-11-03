@extends("Templates/template")

@section("titulo")
Infección Intrahospitalaria
@stop

@section("script")
<script>
	var tipoSubmit="";
	var MAX_OPTIONS = 5;
	var count=0;
	var registrarExtraSistemaEnviado=false;

	var liberar=function(){
		var fecha = $("#fechaEgreso").data('DateTimePicker');
		fecha.date(moment());
		fecha.minDate(moment(window._gc_now).subtract(3, "days").startOf('day'));
		fecha.maxDate(moment(window._gc_now));
 		$("#modalLiberar").modal();
 	}

	var ocultarReingreso=function(){
		var value=$("input[name='reingreso']:checked").val();

		if(value == "Si")$("#divIaas").show();
		else $("#divIaas").hide();
	}

	var ocultarMuerte=function(){
		var value=$("input[name='fallecimiento']:checked").val();

		if(value == "Si")$("#muerte").show();
		else $("#muerte").hide();
	}	

	$(function(){
	ocultarMuerte();

	$("input[name='fallecimiento']").on("change", function(){
			var value=$(this).val();
			if(value == "Si")$("#muerte").show();
			else $("#muerte").hide();
		});

	});


	$(function(){
	ocultarReingreso();

	$("input[name='reingreso']").on("change", function(){
			var value=$(this).val();
			if(value == "Si")$("#divIaas").show();
			else $("#divIaas").hide();
		});

	});

	var deleteOtro = function(link){
		$(link).parent().parent().parent().remove();
	};
	var addFila2=function()
	{
		var $template = $('#templateRow2'),
		$clone    = $template
		.clone()
		.removeClass('hide')
		.removeAttr('id')
		.insertBefore($template);
		$clone.find("select").prop("disabled", false);
		$clone.find("input").prop("disabled", false);
		$clone.find("textarea").prop("disabled", false);
		$clone.find('[name="fechaIngreso[]"]').datetimepicker({
		    format: 'DD-MM-YYYY hh:mm:ss',
		    locale: "es",
		    useCurrent: false
		    });
		$clone.find('[name="fechaInicio[]"]').datetimepicker({
		    format: 'DD-MM-YYYY hh:mm:ss',
		    locale: "es",
		    useCurrent: false
		    });
		console.log($clone.find('[name="localizacion[]"]'));

	}

	var addFila3=function(){
		var $template = $('#templateRow3'),
		$clone    = $template
		.clone()
		.removeClass('hide')
		.removeAttr('id')
		.insertBefore($template);
		$clone.find("input").prop("disabled", false);
		console.log($clone.find('[name="cvc[]"]'));
	}
	var addFila=function(){
		var $template = $('#templateRow5'),
		$clone    = $template
		.clone()
		.removeAttr('id')
		.insertBefore($template);
		$clone.find("input").prop("disabled", false);
	}

	var getPacienteCallback = function(data){
		if(rut != ""){
			$("#registrarPaciente").prop("disabled", true);
			$("#rut").val(data.rutSin);
			$("#dv").val(data.dv);
			$("#fechaNac").datepicker('update', data.fecha);
			$("#nombre").val(data.nombre);
			$("#edad").val(data.edad);
			$("#sexo").val(data.genero);
			$("#apellidoP").val(data.apellidoP);
			$("#apellidoM").val(data.apellidoM);
			$("#diagnostico").val(data.diagnostico);
			$("#fechaIngreso2").val(data.fechaIngreso);
			$('#realizarDerivacion').bootstrapValidator('revalidateField', 'fechaNac');
			$('#realizarDerivacion').bootstrapValidator('revalidateField', 'nombre');
			$('#realizarDerivacion').bootstrapValidator('revalidateField', 'diagnostico');
			$("#realizarDerivacion input[type='submit']").prop("disabled", false);
 			$("#realizarDerivacion button[type='submit']").prop("disabled", false);
			//buscarCama();
		}
	}
	var getPaciente=function(rut){
		$.ajax({
			url: "{{URL::to('/')}}/getPaciente",
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
			url: "/trasladar/registrarExtraSistema",
			type: "post",
			dataType: "json",
			data: form.serialize(),
			success: function(data){
				registrarExtraSistemaEnviado=true;
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

		$("#fechaIngreso").datetimepicker({
		    format: 'DD-MM-YYYY hh:mm:ss',
		   	locale: "es",
		    useCurrent: false
		    });
		$("#fechaIngreso2").datetimepicker({
		    format: 'DD-MM-YYYY hh:mm:ss',
		   	locale: "es",
		    useCurrent: false
		    });

		$("#fechaInicio").datetimepicker({
		    format: 'DD-MM-YYYY hh:mm:ss',
		    locale: "es",
		    useCurrent: false
		    });

		$("#fechaMuerte").datetimepicker({
		    format: 'DD-MM-YYYY hh:mm:ss',
		    locale: "es",
		    useCurrent: false
		    });

		$("#solicitudMenu").collapse();

		@if($paciente)
		getPacienteId('{{$paciente->id}}');
		@endif
		$("#selectEstablecimiento").on("change", function(){
			$.ajax({
				url: "{{URL::to('getUnidadesSelect') }}",
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
			tipoSubmit="trasladar";
		});

		$("#registroExtraSistema").on("click", function(){
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
 			 excluded: ':disabled',
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
 			 	diagnostico: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El diagnóstico es obligatorio'
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
        	$("#registrarPaciente").prop("disabled", false);
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
 			if(tipoSubmit == "registrar") registrarPaciente($form);
 			if(tipoSubmit == "trasladar") trasladar($form[0]);
 			if(tipoSubmit == "extraSistema" && !registrarExtraSistemaEnviado) registrarExtraSistema($form);
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
		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm:ss"
		});

		$("#tipo-procedencia").on("change", function(){
			var value=$(this).val();
			$.ajax({
				url: '{{URL::to("getEspecificarProcedencia")}}',
				data: { "tipo-procedencia": value },
				dataType: "json",
				type: "get",
				success: function(data){
					$("#row-procedencia").empty();
					$("#row-procedencia").html(data.data);
				},
				error: function(error){
					console.log(error);
				}
			});
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

 		$("#formInfeccion").submit(function(evt){
 			evt.preventDefault(evt);
 			$.ajax({
 				url: "{{ URL::to('/')}}/Veringresarinfeccion",
 				data:$(this).serialize(),
 				type: "post",
 				dataType: "json",
 				success: function(data){
 					if(data.exito){
						 swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						window.location.href="{{URL::to('/')}}/unidad/<?php echo $Miunidad;?>";
							}, 2000)
						},
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
						text:"Error al ingresar infeccion"
						});
 					console.log(error);
 				}
 			});
 		});

 		$("#formLiberar").submit(function(evt){
 			evt.preventDefault(evt);
 			$.ajax({
 				url: "{{ URL::to('/')}}/liberarInfeccion",
 				data:$(this).serialize(),
 				type: "post",
 				dataType: "json",
 				success: function(data){
 					$("#modalLiberar").modal("hide");
 					if(data.exito){
						 swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						$("#opcionesCamas").hide();
						window.location.href="{{URL::to('/')}}/unidad/<?php echo $Miunidad;?>";
							}, 2000)
						},
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
						text:"Error al cerrar la infección"
						});
						
 					console.log(error);
 				}
 			});
 		});



 		$("#selectEstablecimiento").trigger("change");
 		@if($unidad_obj)
 		buscarCama();
 		@endif
 	});
 </script>

@stop

@section("miga")
<li><a href="#">Gestión de Camas</a></li>
<li><a href="#">Infección Intrahospitalaria</a></li>
@stop

@section("section")
{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formInfeccion', 'onsubmit' => 'return false', 'files'=> true)) }}
{{ Form::hidden('idEstablecimiento', '', array('id' => 'idEstablecimiento')) }}
{{ Form::hidden('caso',$caso, array('id' => 'caso')) }}
{{ Form::hidden('caso2',$caso2, array('id' => 'caso2')) }}
{{ Form::hidden('MiInfeccion',$MiInfeccion, array('id' => 'MiInfeccion')) }}
{{ Form::hidden('MiPaciente',$MiPaciente, array('id' => 'MiPaciente')) }}
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
	<div class="row" align="center">
	<legend>NOTIFICACIÓN IAAS</legend><br>
	</div>
</fieldset>
    <div class="col-xs-12 panel">
        <ul class="nav nav-tabs " role="tablist" id="tab-operacion">       
	        <li class="active"><a id="datosPaciente" href="#tab-datosPaciente" role="tab" data-toggle="tab">Datos paciente</a></li>  
	        <li><a id="localiza" href="#tab-localizacion" role="tab" data-toggle="tab">IAAS</a></li>            
        </ul>

<div class="tab-content container-fluid panel-body"  style="background-color: white;">
	<div class="tab-pane fade active in" id="tab-datosPaciente" >
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
					<label for="edad" class="col-sm-2 control-label">Edad: </label>
					<div class="col-sm-10">
						{{Form::text('edad', null, array('id' => 'edad', 'class' => 'form-control'))}}
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

				<div class="form-group col-md-6">
					<label for="fecha" class="col-sm-2 control-label">Género: </label>
					<div class="col-sm-10">
						{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
					</div>
				</div>		
@foreach ($paciente_infeccion as $paciente_infec)
				<div class="form-group col-md-6">
					<label for="prevision" class="col-sm-2 control-label">Servicio de Ingreso: </label>
					<div class="col-sm-10">
						{{ Form::text('servicio', $paciente_infec->servicio_ingreso, array('class' => 'form-control','disabled')) }}
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
				<div class="form-group col-md-6">
					<label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>
					<div class="col-sm-10">
						{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>
		@if($esIaas || Session::get("usuario")->tipo === TipoUsuario::IAAS)
			<div class="row">
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Numero de Ficha: </label>
						<div class="col-sm-10">
							{{Form::text('numero_ficha', $paciente_infec->numero_ficha, array('id' => 'numero_ficha', 'class' => 'form-control'))}}
						</div>
				</div>
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Peso de nacimiento: </label>
						<div class="col-sm-6">
						<input disabled type="text" class="form-control" value = "{{$paciente_infec->peso_nacimiento}}"/>
						</div>
				</div>
			</div>
		@endif
			<div class="row">
				<div class="form-group col-sm-6">
						<label  class="col-sm-2 control-label">Categoria: </label>
						<div class="col-sm-8">
							<input disabled type="text" class="form-control" value = "{{$paciente_infec->categoria}}"/>
						</div>
				</div>
				<div class="form-group col-md-6">
					<label for="fecha" class="col-sm-2 control-label">Fecha de Ingreso: </label>
					<div class="col-sm-10">
						{{Form::text('fechaIngreso2', null, array('id' => 'fechaIngreso2', 'class' => 'form-control', 'required' => "true"))}}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm-6">
						<label  class="col-sm-2 control-label">Tipo de aislamiento Ingresado: </label>
						<div class="col-sm-8">
						<input disabled type="text" class="form-control" value = "{{trim($paciente_infec->aislamiento,"{}")}}"/>
						</div>
				</div>

				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Actualizar aislamiento: </label>
						<div class="col-sm-6">
				            <select  name="aislamiento[]" class="selectpicker" multiple> 
				             <option>Sin aislamiento</option>
				             <option>Contacto</option>
				             <option>Aereo</option> 
				             <option>Por gotitas</option>  
				            </select>
						</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Reingreso: </label>
						<div class="col-sm-3">
					@if(trim($paciente_infec->reingreso)=="Si"){{ Form::radio('reingreso','Si',true, array('disabled')) }} SI
					@else {{ Form::radio('reingreso','Si',false, array('disabled')) }} SI
					@endif
					@if(trim($paciente_infec->reingreso)=="No"){{ Form::radio('reingreso','No',true, array('disabled')) }} NO
					@else {{ Form::radio('reingreso','No',false, array('disabled')) }} NO
					@endif
						</div>
				</div>
				<div id="divIaas" class="form-group col-md-6">
						<label  class="col-sm-3 control-label">Numero de días de reingreso: </label>
						<div class="col-sm-9">
						<input disabled type="text" class="form-control" value = "{{$paciente_infec->dias_reingreso}}"/>
						</div>
				</div>
			</div>
		</fieldset>

		<fieldset>
		<legend>ANTECEDENTES MORBIDOS</legend><br>
					<div class="form-group col-xs-6 col-sm-6 col-md-9">
						<label class="col-sm-3 control-label">ANTECEDENTES</label>
						<div class="col-sm-9">
							@if($paciente_infec->diabetes)<label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" checked disabled="true" /> <span title="diabetes">Diabetes</span></label>
							@else <label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" disabled="true"/> <span title="diabetes" >Diabetes</span></label>
							@endif
							@if($paciente_infec->hipertension)<label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension" checked disabled="true"/> <span title="hipertension">Hipertensión</span></label>
							@else <label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension"disabled="true"/> <span title="hipertension" >Hipertensión</span></label>
							@endif
							@if($paciente_infec->enfermedad_autoinmune)<label> <input name="morbidos[]" type="checkbox" value="enfermedad" checked disabled="true"/> Enfermedad autoinmune</label>
							@else <label> <input name="morbidos[]" type="checkbox" value="enfermedad" disabled="true"/> Enfermedad autoinmune</label>
							@endif
							@if($paciente_infec->otro!='0')
								<label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" checked disabled="true"/> Otras</label>
								{{Form::text('Otro',$paciente_infec->otro, array('id' => 'Otro','placeholder'=>'Ingrese Otro','disabled'=>'true', 'class' => 'form-control'))}}
							@else <label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" disabled="true"/> Otras</label>
							@endif
							
						</div>
					</div>
		</fieldset>

<fieldset>
<br>
<legend>Auditoria de muerte</legend>
	<div class="row">
			<div class="form-group col-md-12">
				<label class="col-sm-2 control-label">Auditoria de muerte: </label>
				<div class="col-sm-10">
					@if(trim($paciente_infec->fallecimiento)=="Si"){{ Form::radio('fallecimiento','Si',true) }} SI
					@else {{ Form::radio('fallecimiento','Si') }} SI
					@endif

					@if(trim($paciente_infec->fallecimiento)=="No"){{ Form::radio('fallecimiento','No',true) }} NO
					@else {{ Form::radio('fallecimiento','No') }} No
					@endif
				</div>
			</div>
	</div>
	<div id="muerte" class="row">
				<div class="form-group col-md-6">
					<label  class="col-sm-1 control-label" style="width:170px">Fecha de fallecimiento: </label>
					<div class="col-sm-9" style="width:235px">
						{{Form::text('fechaMuerte',$paciente_infec->fecha_fallecimiento, array('id' => 'fechaMuerte', 'class' => 'form-control'))}}
					</div>
				</div>
				<div class="form-group col-md-6">
				<label class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						@if(trim($paciente_infec->motivo_fallecimiento)=="La muerte fue causada por la IAAS"){{ Form::radio('muerte','La muerte fue causada por la IAAS',true) }} La muerte fue causada por la IAAS<br>
						@else {{ Form::radio('muerte','La muerte fue causada por la IAAS') }} La muerte fue causada por la IAAS<br>
						@endif
						@if(trim($paciente_infec->motivo_fallecimiento)=="La IAAS contribuyo a la muerte sin ser la causa de ella"){{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella',true) }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
						@else {{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella') }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
						@endif
						@if(trim($paciente_infec->motivo_fallecimiento)=="No hubo relacion entra la IAAS y la muerte"){{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte',true) }} No hubo relación entra la IAAS y la muerte<br>
						@else {{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte') }} No hubo relación entra la IAAS y la muerte<br>
						@endif
						@if(trim($paciente_infec->motivo_fallecimiento)=="Se desconoce la asociación entre la IAAS y la muerte"){{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte',true) }} Se desconoce la asociación entre la IAAS y la muerte
						@else {{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte') }} Se desconoce la asociación entre la IAAS y la muerte
						@endif

					</div>
				</div>
				<br>

	</div>
</fieldset>

</div> 
@endforeach<!-- Fin datos pacientes -->

<div class="tab-pane fade " id="tab-localizacion" >
		<fieldset>
		<legend>IAAS</legend>
		<?php $contador2=1;?>
@foreach ($iaas2 as $iaas)
<br><br>
<div class="row">
		<legend>Notificación de Infección <?php echo $contador2;?></legend>
		</div>
		<br>
		<div class="row">
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
				<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$iaas->fecha_inicio}}"/>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
				<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$iaas->fecha_iaas}}"/>
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
				<div class="col-sm-9">
					<input disabled type="text" class="form-control" value = "{{$iaas->servicioiaas}}"/>
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspLocalización: </label>
				<div class="col-sm-10" style="width:720px" >
					<input disabled type="text" class="form-control" value = "{{$iaas->localizacion}}"/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
				<div class="col-sm-10" style="width:720px" >
				<input disabled type="text" class="form-control" value = "{{$iaas->procedimiento_invasivo}}"/>
				</div>
			</div>

			<div class="row">
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
				<div class="col-sm-10" style="width:720px" >
				<input disabled type="text" class="form-control" value = "{{$iaas->agente1}}"/>
				</div>
			</div>
		</div>
<div class="row">
		<tfoot>
		@if(trim($iaas->sensibilidad1)!="NINGUNA")
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label" style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1:</label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad1}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia1}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia1}}"/>
		        	</div>
			    </td>
			</tr>
		@endif
			@if(trim($iaas->sensibilidad2)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad2}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia2}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
						<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia2}}"/>
		        	</div>
			    </td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad3)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad3}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia3}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia3}}"/>
		        	</div>
			    </td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad4)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad4}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia4}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia4}}"/>
		        	</div>
			    </td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad5)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad5}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia5}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia5}}"/>
		        	</div>
			    </td>
			</tr>
			@endif
			@if(trim($iaas->sensibilidad6)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad6}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia6}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia6}}"/>
		        	</div>
			    </td>
			</tr>	
			@endif
		</tfoot>
</div>
@if(trim($iaas->agente2)!="Sin información")
		<!-- Fin agente 1-->
		<br><br><br>
<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 2: </label>
				<div class="col-sm-10" style="width:720px" >
<input disabled type="text" class="form-control" value = "{{$iaas->agente2}}"/>
				</div>
			</div>
<div class="row">
		<tfoot>
		@if(trim($iaas->sensibilidad7)!="NINGUNA")
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad7}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia7}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia7}}"/>
		        	</div>
			    </td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad8)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad8}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia8}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia8}}"/>
		        	</div>
			    </td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad9)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad9}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia9}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia9}}"/>
		        	</div>
			    </td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad10)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad10}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia10}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia10}}"/>
		        	</div>
			    </td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad11)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad11}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia11}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia11}}"/>
		        	</div>
			    </td>
			</tr>
		@endif
		@if(trim($iaas->sensibilidad12)!="NINGUNA")
			<br><br><br>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->sensibilidad12}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->intermedia12}}"/>
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:220px">
				<input disabled type="text" class="form-control" value = "{{$iaas->resistencia12}}"/>
		        	</div>
			    </td>
			</tr>	
		@endif
		</tfoot>
</div>
		<!-- Fin agente 2-->
</div> 
@endif	
		<br>
        	@if(trim($iaas->cierre)=="no")
        	<label class="col-sm-9 control-label"> IAAS <?php echo $contador2;?> FINALIZADA
            <select  name="cerrar[]" class="horario"> 
             <option value="si">Si</option>
             <option value="no"selected>No</option>     
            </select>
            </label>
         	@endif	
         	@if(trim($iaas->cierre)=="si")
         	<label class="col-sm-9 control-label"> IAAS <?php echo $contador2;?> FINALIZADA 
            <select  name="cerrar[]" class="horario"> 
             <option value="si" selected>Si</option>
             <option value="no">No</option>     
            </select>
            </label>
         	@endif
        <br>
	<?php $contador2=$contador2+1;?>
	@endforeach<!-- Fin Localizacion-->	
</fieldset>
		<br><br>
			<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus"></span> Agregar Localización</a>
							</td>
						</tr>
			</tfoot>
		<br><br>

		<!-- Copia localizacion--> 
		<div id="templateRow2" class="row hide"><br>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
				<div class="col-sm-9">
					{{Form::text('fechaIngreso[]', null, array('id' => 'fechaIngreso', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
				<div class="col-sm-9">
					{{Form::text('fechaInicio[]', null, array('id' => 'fechaInicio', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
				<div class="col-sm-9">
					{{ Form::select('servicioIAAS[]', $UnidadesIAAS, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspLocalización: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('localizacion[]', $localizacion, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('procedimiento[]', $procedimiento, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>
			<div class="row">
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('agente1[]', $AgenteEtiologico, 'SIN INFORMACION', array('id' => 'agente1', 'class' => 'form-control')) }}
				</div>
			</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label" style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1:</label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('sensibilidad1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia1[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('sensibilidad2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('intermedia2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
						<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia2[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('sensibilidad3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:235px">
		             {{ Form::select('resistencia3[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
 				{{ Form::select('sensibilidad4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia4[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
				 {{ Form::select('sensibilidad5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia5[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia6[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>	
		</tfoot>
</div>
		<!-- Fin agente 1-->
		<br><br><br>
<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 2: </label>
				<div class="col-sm-10" style="width:720px" >
						{{ Form::select('agente2[]', $AgenteEtiologico, 'SIN INFORMACION', array('id' => 'agente2', 'class' => 'form-control')) }}
				</div>
			</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad1: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia1: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia1: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia7[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad2: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia2: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia2: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia8[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad3: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia3: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia3: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia9[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad4: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia4: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia4: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia10[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad5: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia5: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia5: </label>
					<div class="col-sm-3" style="width:235px">
		            {{ Form::select('resistencia11[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>
			</tfoot>
</div>
<div class="row">
		<tfoot>
			<tr>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label"style="width:100px">&nbsp&nbsp&nbsp&nbspSensibilidad6: </label>
					<div class="col-sm-3" style="width:220px">
		             {{ Form::select('sensibilidad12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Intermedia6: </label>
					<div class="col-sm-3" style="width:220px">
		            {{ Form::select('intermedia12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			    <td colspan="4" class="text-left">
					<label class="col-sm-1 control-label">Resistencia6: </label>
					<div class="col-sm-3" style="width:235px">
		           {{ Form::select('resistencia12[]', $CaracteristicasAgente, 'SIN INFORMACION', array('class' => 'form-control')) }}
		        	</div>
			    </td>
			</tr>	
		</tfoot>
</div>
		<!-- Fin agente 2-->
			
		<br><br><br>
			<div class="input-group col-md-10">
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Agregar Localización</a>
					<a class="btn btn-default" onclick="deleteOtro(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Quitar Localización</a>
				</div>
			</div>
		</div> <!-- Fin Localizacion copia-->	
		<br><br>
		</fieldset>


<div class="modal-footer">
	<div class="form-group col-md-6">
		<div class="col-sm-12">
			<!--{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }} -->
			<button id="solicitar" type="submit" class="btn btn-primary">Actualizar</button>
		</div>
	</div>
	<div class="form-group col-md-6">
		<div class="col-sm-3">
			<!--{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }} -->
			<button id="cerrar" type="button" onclick="liberar();" class="btn btn-primary">Cerrar Todas las Infecciones Notificadas</button>
		</div>
	</div>
</div>

</div> <!-- Fin pestaña localizacion -->


</div>
</div> <!-- Fin panel-->
{{ Form::close() }}

<div id="modalLiberar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Confirmación</h4>
			</div>
			{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formLiberar')) }}
			{{ Form::hidden('MiInfeccion',$MiInfeccion, array('id' => 'MiInfeccion')) }}
			<div class="modal-body">
				<div class="row" style="margin: 0;">
					<div class="form-group col-md-12">
						<h4>¿ Desea cerrar la infección ?</h4>
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
						<label for="horas" class="col-sm-2 control-label">Destino alta: </label>
						<div class="col-sm-10">
							{{ Form::textarea('motivo', null,['size' => '59x5'], ['class' => 'form-control', "id" => "motivo"]) }}
						</div>
					</div>
					<div class="form-group col-md-12" id="motivo-liberacion">

					</div>
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

<br><br>

@stop