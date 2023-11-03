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

    function validateTab(index) {
        
        // The current tab
        $tab = $('#formInfeccion').find('.tab-pane').eq(index);

        return true;
    }	

	var ocultarReingreso=function(){
		var value=$("input[name='reingreso']:checked").val();

		if(value == "Si")$("#divIaas").show();
		else $("#divIaas").hide();
	}

	var ocultarMuerte=function(){
		var value=$("input[name='fallecimiento']:checked").val();

		if(value == "Si")$("#muerte").show("slow");
		else $("#muerte").hide("slow");
	}	

	$(function(){
	ocultarMuerte();

	$("input[name='fallecimiento']").on("change", function(){
			var value=$(this).val();
			if(value == "Si")$("#muerte").show("slow");
			else $("#muerte").hide("slow");
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

	var ocultarMorbido=function(){
		var value=$("input[name='morbidos[]']:checked").val();

		if(value == "MorbidoOtros")$("#divMorbido").show();
		else $("#divMorbido").hide();
	}	

	$(function(){
	ocultarMorbido();

	$("input[name='morbidos[]']").on("change", function(){
			var value=$(this).val();
			if(value == "MorbidoOtros")$("#divMorbido").show();
			else $("#divMorbido").hide();
		});
	});


	var ocultarLocalizacion=function(){
		var value=$("select[name='localizacion[]']").val();

		if(value =="Otro")$("#divLocalizacion").show();
		else $("#divLocalizacion").hide();
	}	

	$(function(){
	ocultarLocalizacion();

	$("select[name='localizacion[]']").on("change", function(){
			var value=$(this).val();
			if(value == "Otro")$("#divLocalizacion").show();
			else $("#divLocalizacion").hide();
		});
	});

	var ocultarProcedimiento=function(){
		var value=$("select[name='procedimiento[]']").val();

		if(value =="Otro")$("#divProcedimiento").show();
		else $("#divProcedimiento").hide();
	}	

	$(function(){
	ocultarProcedimiento();

	$("select[name='procedimiento[]']").on("change", function(){
			var value=$(this).val();
			if(value =="Otro")$("#divProcedimiento").show();
			else $("#divProcedimiento").hide();
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
			//buscarCama();
		}
	}
	var getPaciente=function(rut){
		$.ajax({
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

	var registrarPaciente=function(form){
		$.ajax({
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
			headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

		$("#formInfeccion").bootstrapWizard({
	    tabClass: 'nav nav-tabs',
	    onTabClick: function(tab, navigation, index) {
	        console.log("cambio tab");
	        return validateTab(index);
	    },
	    onNext: function(tab, navigation, index) {
	        console.log("siguiente tab");
	        var numTabs    = $('#installationForm').find('.tab-pane').length,
	            isValidTab = validateTab(index - 1);
	        if (!isValidTab) {
	            return false;
	        }

	        if (index === numTabs) {
	            // We are at the last tab
	        }

	        return true;
	    },
	    onPrevious: function(tab, navigation, index) {
	        console.log("volver tab anterior");
	        return validateTab(index + 1);
	    },
	    onTabShow: function(tab, navigation, index) {
	        console.log("mostrar tab");
	        // validar aps y hospital cuando se carga la pestaña
	        // -------------------------------------------------------------

	    }
	    });

    	$("#agente1").on("change", function(){
 			$('#solicitar').removeAttr('disabled');
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
				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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


 		$("#formInfeccion").submit(function(evt){
 			evt.preventDefault(evt);
 			$('#solicitar').removeAttr('disabled');
 			$.ajax({
 				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
 				url: "{{ URL::to('/')}}/ingresarinfeccion",
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
								window.location.href="{{URL::to('/')}}/index/camas/<?php echo $esta;?>";
							}, 2000)
						},
						});
 					
 					}
 					else{
 						swalError.fire({
						title: 'Error',
						text:data.error
						});
 						$('#solicitar').removeAttr('disabled');
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


 	});
 </script>
<meta name="csrf-token" content="{{{ Session::token() }}}">
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
	       <!--	<li><a id="Motivonotificacion" href="#tab-motivo" role="tab" data-toggle="tab">Motivo de Notificación</a></li>    
	        <li><a id="procedimiento" href="#tab-procedimiento" role="tab" data-toggle="tab">Procedimiento Invasivo</a></li> 
	         <li><a id="fallecimiento" href="#tab-fallecimiento" role="tab" data-toggle="tab">Fallecimiento del paciente</a></li>    -->
	       <!-- <li><a id="datosDerivacion" href="#tab-datosDerivacion" role="tab" data-toggle="tab">Datos de derivación</a></li> -->               
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
				<div class="form-group col-md-6">
					<label for="prevision" class="col-sm-2 control-label">Servicio de Ingreso: </label>
					<div class="col-sm-10">
						{{ Form::select('sala', $UnidadesIAAS, 'SIN INFORMACION', array('class' => 'form-control')) }}
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
							{{Form::text('numero_ficha', null, array('id' => 'numero_ficha', 'class' => 'form-control'))}}
						</div>
				</div>
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Peso de nacimiento: </label>
						<div class="col-sm-6">
						{{ Form::select('peso_naci', array('no aplica'=>'no aplica','menor o igual a 750 gramos'=>'menor o igual a 750 gramos','751-1000 gramos'=>'751-1000 gramos','1001-1499 gramos'=>'1001-1499 gramos','1500-2499 gramos'=>'1500-2499 gramos','mayor o igual a 2500 gramos'=>'mayor o igual a 2500 gramos'),null, array('id' => 'peso_naci', 'class' => 'form-control')) }}
						</div>
				</div>
			</div>
		@endif
			<div class="row">
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Categoria: </label>
						<div class="col-sm-6">
						{{ Form::select('categoria', array('Neonato' => 'Neonato', 'Adulto' => 'Adulto','Ginecobstetrico' => 'Ginecobstetrico','Lactante' => 'Lactante','Pediatrico' => 'Pediatrico'),null, array('id' => 'categoria', 'class' => 'form-control')) }}
						</div>
				</div>
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Tipo de aislamiento: </label>
						<div class="col-sm-6">
				            <select  name="aislamiento[]" class="selectpicker" multiple> 
				             <option>Sin aislamiento</option>
				             <option>Contacto</option>
				             <option>Aereo</option> 
				             <option>Por gotitas</option>  
				            </select>
						</div>
				</div>
				<div class="form-group col-md-6">
					<label for="fecha" class="col-sm-2 control-label">Fecha de Ingreso: </label>
					<div class="col-sm-10">
						{{Form::text('fechaIngreso2', null, array('id' => 'fechaIngreso2', 'class' => 'form-control', 'required' => "true"))}}
					</div>
				</div>
			<div class="row">
				<div class="form-group col-md-6">
						<label  class="col-sm-2 control-label">Reingreso: </label>
						<div class="col-sm-3">
						{{ Form::radio('reingreso','Si') }} SI
						{{ Form::radio('reingreso','No',true)}} NO
						</div>
				</div>
				<div id="divIaas" class="form-group col-md-6">
						<label  class="col-sm-3 control-label">Numero de días de reingreso: </label>
						<div class="col-sm-9">
						{{Form::text('numero_reingreso', null, array('id' => 'numero_reingreso', 'class' => 'form-control'))}}
						</div>
				</div>
			</div>
		</div>
		</fieldset>

		<fieldset>
		<legend>ANTECEDENTES MORBIDOS</legend><br>
					<div class="form-group col-xs-6 col-sm-6 col-md-9">
						<label class="col-sm-3 control-label">ANTECEDENTES</label>
						<div class="col-sm-9">
							<label> <input name="morbidos[]" type="checkbox" value="diabetes" title="diabetes" /> <span title="diabetes">Diabetes</span></label>
							<label> <input name="morbidos[]" type="checkbox" value="hipertension" title="hipertension"/> <span title="hipertension">Hipertensión</span></label>
							<label> <input name="morbidos[]" type="checkbox" value="enfermedad"/> Enfermedad autoinmune</label>
							<label> <input name="morbidos[]" type="checkbox" value="MorbidoOtros" /> Otras</label>
						</div>
					</div>
					<div id="divMorbido" class="form-group col-xs-6 col-sm-6 col-md-9">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-6" style="width:402px">
						{{Form::text('Otro', null, array('id' => 'Otro','placeholder'=>'Ingrese Otro', 'class' => 'form-control'))}}
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
					{{ Form::radio('fallecimiento','Si') }} SI
					{{ Form::radio('fallecimiento','No',true) }} NO
				</div>
			</div>
	</div>
	<div id="muerte" class="row">
			<div class="form-group col-md-6">
				<label  class="col-sm-1 control-label" style="width:170px">Fecha de fallecimiento: </label>
				<div class="col-sm-9" style="width:235px">
					{{Form::text('fechaMuerte', null, array('id' => 'fechaMuerte', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-2 control-label"></label>
				<div class="col-sm-10">
				{{ Form::radio('muerte','La muerte fue causada por la IAAS') }} La muerte fue causada por la IAAS<br>
				{{ Form::radio('muerte','La IAAS contribuyo a la muerte sin ser la causa de ella') }} La IAAS contribuyó a la muerte sin ser la causa de ella<br>
				{{ Form::radio('muerte','No hubo relacion entra la IAAS y la muerte') }} No hubo relación entra la IAAS y la muerte<br>
				{{ Form::radio('muerte','Se desconoce la asociación entre la IAAS y la muerte') }} Se desconoce la asociación entre la IAAS y la muerte
				</div>
			</div>
			<br>	
	</div>	
</fieldset>
</div> <!-- Fin datos pacientes -->


<div class="tab-pane fade " id="tab-localizacion" >
		<fieldset>
		<legend>IAAS</legend><br>
		<div class="row">
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
				<div class="col-sm-9">
					{{Form::text('fechaIngreso[]', null, array('id' => 'fechaIngreso', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Fecha Inicio IAAS: </label>
				<div class="col-sm-9">
					{{Form::text('fechaInicio[]', null, array('id' => 'fechaInicio', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group">
			</div>
			<div class="form-group col-md-6">
				<label  class="col-sm-3 control-label">Servicio Notificación IAAS: </label>
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
			<div id="divLocalizacion" class="form-group">
						<label class="col-sm-2 control-label" style="width:215px"></label>
						<div class="col-sm-6" style="width:365px">
						{{Form::text('OtroLocal', null, array('id' => 'OtroLocal','placeholder'=>'Ingrese Otro', 'class' => 'form-control'))}}
						</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspProcedimiento Invasivo: </label>
				<div class="col-sm-10" style="width:720px" >
					{{ Form::select('procedimiento[]', $procedimiento, 'SIN INFORMACION', array('class' => 'form-control')) }}
				</div>
			</div>

			<div id="divProcedimiento" class="form-group">
						<label class="col-sm-2 control-label" style="width:215px"></label>
						<div class="col-sm-6" style="width:365px">
						{{Form::text('OtroProcedu', null, array('id' => 'OtroProcedu','placeholder'=>'Ingrese Otro', 'class' => 'form-control'))}}
						</div>
			</div>

			<div class="row">
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label" style="width:215px">&nbsp&nbsp&nbsp&nbsp&nbspAgente Etiológico 1: </label>
				<div class="col-sm-10" style="width:720px" >
						{{ Form::select('agente1[]', $AgenteEtiologico, 'SIN INFORMACION', array('class' => 'form-control')) }}
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
		<br><br><br><br>
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

		</div> <!-- Fin Localizacion-->	
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
				<label class="col-sm-3 control-label">Fecha de notificación de IAAS: </label>
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
						{{ Form::select('agente1[]', $AgenteEtiologico, 'SIN INFORMACION', array('class' => 'form-control', 'required' => "true")) }}
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
</div><!-- Fin agente 2-->
	
		<br><br><br>
			<div class="input-group col-md-10">
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Agregar Localización</a>
					<a class="btn btn-default" onclick="deleteOtro(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Quitar Localización</a>
				</div>
			</div>
			<!--<tfoot>
						<tr>
							<td colspan="4" class="text-left">
								<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus"></span> Agregar Localización</a>
								<a class="btn btn-default trat" onclick="deleteOtro(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Quitar Localización</a>
							</td>
						</tr>
			</tfoot> -->
		<br><br>

</div> <!-- Fin Localizacion copia-->
</fieldset>

<div class="modal-footer">
	<div class="row" align="center">
		<button id="solicitar" type="submit" class="btn btn-primary">Enviar</button>
	</div>
</div>

</div> <!-- Fin pestaña localizacion -->

<div class="tab-pane fade " id="tab-motivo" >

<legend>Motivo de Notificación</legend>
<fieldset>
	<div class="row">
			<div class="form-group col-md-6">
				<label class="col-sm-2 control-label">Criterio IAAS: </label>
				<div class="col-sm-10">
					{{ Form::radio('criterio_iaas','Si') }} SI
					{{ Form::radio('criterio_iaas','No',true) }} NO
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-2 control-label">Cambio ATB: </label>
				<div class="col-sm-10">
				{{ Form::radio('cambio_atb','Si') }} SI
				{{ Form::radio('cambio_atb','No',true) }} NO
				</div>
			</div>
	</div>
	<div class="row">
			<div class="form-group col-md-6">
				<label class="col-sm-2 control-label">Cirujano: </label>
				<div class="col-sm-10">
					{{Form::text('cirujano', null, array('id' => 'cirujano', 'class' => 'form-control'))}}
				</div>
			</div>

			<div class="form-group col-md-6">

				<label class="col-sm-2 control-label">Electiva</label>
				<div class="col-sm-3">
				{{ Form::radio('electiva','Si') }} SI
				{{ Form::radio('electiva','No',true) }} NO
				</div>

				<label class="col-sm-2 control-label">Urgencia</label>
				<div class="col-sm-3">
				{{ Form::radio('urgencia','Si') }} SI
				{{ Form::radio('urgencia','No',true)}} NO
				</div>
			</div>
	</div>


	<div class="row">
			<div class="form-group col-md-6">
				<label class="col-sm-2 control-label">Cesaria</label>
				<div class="col-sm-3">
				{{ Form::radio('cesaria','Si') }} SI
				{{ Form::radio('cesaria','No',true) }} NO
				</div>

				<label class="col-sm-3 control-label">Reintervención</label>
				<div class="col-sm-3">
				{{ Form::radio('reintervencion','Si') }} SI
				{{ Form::radio('reintervencion','No',true) }} NO
				</div>	
			</div>	

			<div class="form-group col-md-6">
				<label class="col-sm-2 control-label">Tipo Herida</label>
				<div class="col-sm-10">
				{{ Form::radio('tipo_herida',1,true) }} 1
				{{ Form::radio('tipo_herida',2) }} 2
				{{ Form::radio('tipo_herida',3) }} 3
				{{ Form::radio('tipo_herida',4) }} 4
				</div>

			</div>
	</div>	
</fieldset>
</div> <!-- Fin pestaña Motivo de notificacion -->

<div class="tab-pane fade " id="tab-procedimiento" >
<legend>Procedimiento Invasivo (Ingrese el número de días solo cuando corresponda)</legend>
<fieldset>
	<div class="row">
		<div class="form-group col-md-6">
			<label class="col-sm-2 control-label">V.M</label>
			<div class="col-sm-3">
				{{Form::text('VM', null, array('id' => 'VM', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>

			<label class="col-sm-2 control-label">Porta cath</label>
			<div class="col-sm-3">
				{{Form::text('porta_cath', null, array('id' => 'porta_cath', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
		</div>
		<div class="form-group col-md-6">
			<label class="col-sm-2 control-label">C.Umbilical</label>
			<div class="col-sm-3">
				{{Form::text('umbilical', null, array('id' => 'umbilical', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
			<label class="col-sm-2 control-label">Central Periférico</label>
			<div class="col-sm-3">
				{{Form::text('central_periferico', null, array('id' => 'central_periferico', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-6">
			<label class="col-sm-2 control-label">Periférico</label>
			<div class="col-sm-3">
				{{Form::text('periferico', null, array('id' => 'periferico', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
			<label class="col-sm-2 control-label">NPT</label>
			<div class="col-sm-3">
				{{Form::text('npt', null, array('id' => 'npt', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
		</div>
		<div class="form-group col-md-6">
			<label class="col-sm-2 control-label">C.U.P</label>
			<div class="col-sm-3">
				{{Form::text('CUP', null, array('id' => 'CUP', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>			
			<label class="col-sm-2 control-label">Shunt V-P</label>
			<div class="col-sm-3">
				{{Form::text('shunt', null, array('id' => 'shunt', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
			
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-6">
			<label class="col-sm-2 control-label">Swan ganz</label>
			<div class="col-sm-3">
				{{Form::text('swan', null, array('id' => 'swan', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
			<label class="col-sm-2 control-label">CHCD</label>
			<div class="col-sm-3">
				{{Form::text('chcd', null, array('id' => 'chcd', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
		</div>
		<div class="form-group col-md-6">
			<label class="col-sm-2 control-label">CHLD</label>
			<div class="col-sm-3">
				{{Form::text('CHLD', null, array('id' => 'CHLD', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>			
			<label class="col-sm-2 control-label">L.Arterial</label>
			<div class="col-sm-3">
				{{Form::text('arterial', null, array('id' => 'arterial', 'class' => 'form-control','placeholder'=>'N° días'))}}
			</div>	
			
		</div>
	</div>

	<div class="row">
		<label class="col-sm-1 control-label">C.V.C</label>
			<div class="col-sm-3">
				<input type="text" class="form-control" name="cvc[]" placeholder="C.V.C N° días" />
			</div>
	</div>
<br>
	<div id="templateRow3" class="row form-group error hide">
			<label class="col-sm-1 control-label"></label>
			<div class="col-sm-3">
				<input type="text" class="form-control" name="cvc[]" placeholder="C.V.C N° días" disabled/>
			</div>
	</div>
<br>
<tfoot>
	<tr>
		<td colspan="4" class="text-left">
			<a class="btn btn-default" onclick="addFila3();"><span class="glyphicon glyphicon-plus"></span> Agregar cvc</a>
		</td>
    </tr>
</tfoot>

</fieldset>

</div> <!-- Fin pestaña Motivo de notificacion -->


<div class="tab-pane fade " id="tab-fallecimiento" >

</div> <!-- Fin pestaña Fallecimiento -->


            <ul class="pager wizard">
                <li class="previous first" style="display:none;"><a href="#">Primera</a></li>
                <li class="previous"><a href="#">Anterior</a></li>
                <li class="next last" style="display:none;"><a href="#">Última</a></li>
                <li class="next"><a href="#">Siguiente</a></li>
            </ul>
</div>
</div> <!-- Fin panel-->
{{ Form::close() }}

<br><br>

@stop