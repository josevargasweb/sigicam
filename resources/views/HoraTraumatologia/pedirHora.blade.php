@extends("Templates/template")

@section("titulo")
Solicitar hora
@stop

@section("script")

<script>
var enviado=false;
var enviado2 = false;
var count=0;

var agregar=function(){
		var $template = $('#fileTemplate');
		var $clone =  $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
		var $input = $clone.find('input[type="file"]');
		var id="id_"+count;
		$input.prop("id", id);
		//$('#'+id).fileinput();
		console.log("#"+id+" .file-input-new .input-group");
		//$(".file-input-new .input-group").css({"width": "95%", "margin-left": "10px"});
		$("#"+id).css({"width": "100%"});
		count++;
	}

var borrar=function(boton){
	console.log(boton);
		$(boton).parent().parent().parent().parent().parent().remove();
	}

	$(function(){

		$("#traumatologiaHoras").collapse();

		if($("#rut").val() != "") $("#rut").prop("readonly", true);
		if($("#dv").val() != "") $("#dv").prop("readonly", true);

		if($("#rut").val() == "") $("#sinRut").val(1);

		$("#fecha").datetimepicker({
			//autoclose: true,
			locale: "es",
			format: "DD-MM-YYYY HH:mm:ss",
			//todayHighlight: true,
			minDate: new Date()
		}).on("dp.change", function(){
			//alert();
			$('#formHora').bootstrapValidator('revalidateField', 'fecha');
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

		var esValidoElRut  = false;
		console.log(esValidoElRut);
		$("#formHora").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				medico: {
					validators:{
						notEmpty:{
							message: 'El médico es obligatorio'
						}
					}
				},
				rut: {
					validators: {
						notEmpty: {
							message: 'El run es obligatorio'
						},
						callback: {
							callback: function(value, validator, $field){
								return true;
							}
						}
					}
				},
				dv: {
					validators:{
						notEmpty: {
							message: 'El dígito es obligatorio'
						},
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $("#rut");

								var dv = $("#dv");

								$("#rut2").val(field_rut.val());
								$("#dv2").val(dv.val());
								
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
									esValidoElRut = false;
									return {valid: false, message: "Dígito verificador no coincide con el run"};
								}
								esValidoElRut = true;
								return true;
							}
						},
						remote:{
							url: "paciente/existePaciente",
							headers: {        
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
							data: function(validator) {
								console.log(esValidoElRut);
								return {

									rut: validator.getFieldElements('rut').val(),
									esValidoElRut: esValidoElRut


								};
							},
							type: "post"
						}
					}
				},
				rutPaciente: {
					validators:{
						notEmpty: {
							message: 'El nombre es obligatorio'
						}
					}
				},
				fecha: {
					validators:{
						notEmpty: {
							message: 'La fecha de solicitud es obligatoria'
						},
						callback: {
							callback: function(value, validator, $field){
								if (value === '') {
									return true;
								}
								var esValidao=validarFormatoFechaHora(value);
								if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
								return true;
							}
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){

			evt.preventDefault(evt);
 			var $form = $(evt.target);
 			$("#btnEnviar").prop("disabled", false);
 			var fv = $form.data('bootstrapValidator');
 			if(!enviado2){
				enviado2 = true;
			}
			else{
				return false;
			}

			enviado2 = true;
 			console.log("ok");
 				$.ajax({
 				url: "{{URL::to('/')}}/pedirHora",
 				headers: {        
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
 				type: "post",
 				dataType: "json",
 				data: new FormData($form[0]),
 				cache: false,
 				contentType: false,
 				processData: false,
 				success: function(data){
 					console.log(data);
 					if(data.exito){
						 swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						location.href="{{URL::to('pedirHora')}}"; 
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
 				/*$.ajax({
 					url: $form .prop("action"),
 					type: "post",
 					dataType: "json",

 					data: new FormData($form[0]),
 					async: false,
 					//contentType: false,
 					//processData: false,
 					success: function(data){
 						enviado=true;
 						if(data.exito)
 							bootbox.alert("<h4>"+data.exito+"</h4>", function(){ location.href="{{URL::to('pedirHora')}}"; });
 						if(data.error)
 							swalError.fire({
						title: 'Error',
						text:data.error
						});
 					},
 					error: function(error){
 						console.log(error);
 					}
 				});*/
 			
 		});





 		$("#asignarCamasForm").bootstrapValidator({
 			 excluded: ':disabled',
 			 fields: {
 			 	/*rut: {
 			 		validators: {
						callback: {
							callback: function(value, validator, $field){
								$("#dv2").val('');
								return true;
							}
						}
					}
 			 	},*/
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
				},
 			 	/*dv: {
 			 		validators:{
 			 			callback: {
 			 				callback: function(value, validator, $field){
								var field_rut = $("#rut2");
								var dv = $("#dv2");
								if(field_rut.val() == '' && dv.val() == '') {
									return true;
								}
								if(field_rut.val() != '' && dv.val() == ''){
									return {valid: false, message: "Debe ingresar el dígito verificador"};
								}
								if(field_rut.val() == '' && dv.val() != ''){
									return {valid: false, message: "Debe ingresar el rut"};
								}
								var rut = $.trim(field_rut.val());
								var esValido=esRutValido(field_rut.val(), dv.val());
								if(!esValido){
									return {valid: false, message: "Dígito verificador no coincide con el rut"};
								}
								
								return true;
							}
 			 			}
 			 		}
 			 	},*/
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
 			 				message: 'La fecha de nacimiento es obligatoria'
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
 			 	diagnostico: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El diagnóstico es obligatorio'
 			 			}
 			 		}
 			 	},
 			 	motivo: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El motivo es obligatorio'
 			 			}
 			 		}
 			 	},
				 "tipo-procedencia": {
					 validators:{
						 regexp: {
							 regexp: /[123]/,
							 message: "Debe seleccionar la procedencia"
						 }
					 }
				 }
 			 }
 		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
			if(!enviado){
				enviado = true;
			}
			else{
				return false;
			}
 			var $form = $(evt.target);
 			showLoad();
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
					hideLoad();
					//alert();
					enviado=true;
					if(data.derivacion){
						swalInfo2.fire({
						title: 'Información',
						text: data.msg,
						didOpen: function() {
							$('#modalAsignacionCama').modal('hide');
							$('#formHora').bootstrapValidator('revalidateField', 'dv');	
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
								lenviado = false;
						$form[0].reset();
						}
						})
					
						console.log(data.error);
					}
				},
				error: function(error){
					hideLoad();
					console.log(error);
				}
			});

 		});

	});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

 @section("miga")
 <li><a href="#">Solicitar hora</a></li>
 @stop


@section("section")

<fieldset>
	<legend>Solicitar hora traumatología</legend>
	<!--{{ Form::open(array('method'=> 'post', 'url' => 'pedirHora', 'id'=>'formEditarPaciente','files'=> true)) }}-->
<form action="pedirHora" id='formHora' method="post" enctype="multipart/form-data">

	<input type="hidden" name="establecimiento_origen" value="{{$establecimiento_origen}}">
	<input type="hidden" name="establecimiento_destino" value="1">
	<input type="hidden" name="rutUsuario" value= "{{$usuario->id}}">



	  <div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label"> Run paciente:* </label>
			<div class="col-sm-10">
				<div class="input-group">
					{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control'))}}
					<span class="input-group-addon"> - </span>
					{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 50px;'))}}
				</div>
			</div>
		</div>
	</div>


  	 <div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label">Fecha de solicitud:* </label>
			<div class="col-sm-10">
					{{Form::text('fecha', null, array('id' => 'fecha', 'class' => 'form-control'))}}

			</div>
		</div>
	</div>


	<div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label">Médico:* </label>
			<div class="col-sm-10">
					{{Form::select('medico',$medicos,null,array('id'=>"medico", 'class'=>'form-control'))}}

			</div>
		</div>
	</div>


	<div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label">Comentario: </label>
			<div class="col-md-10">
					<textarea class="form-control" name="comentario" id="comentario"></textarea>
					
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-10">
			<label for="rut" class="col-sm-2 control-label">Archivo: </label>
			<div class="col-md-10">

				<!--<input type="file" name="file" class="form-control" style="width:100%">	-->
				<div class="col-md-10" style="padding-left: 0px;">
					<input id="fileMain" type="file" name="file[]" class="form-control" style="width:100%" />
				</div>
				<div class="col-md-2">
					<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
				</div>
			</div>
		</div>
	</div>


	<div id="fileTemplate" class="row hide" style="margin-top: 30px;">
		<div class="row">
			<div class="form-group col-md-10">
				<div class="col-md-2"></div>
					<div class="col-md-10" style="padding-left: 10px;">
						<div class="col-md-10">
							<input type="file" name="file[]" class="form-control" />
						</div>
						<div class="col-md-2">
							<button class="btn btn-default" type="button" onclick="agregar();"><span class="glyphicon glyphicon-plus-sign"></span></button>
							<button class="btn btn-default" type="button" onclick="borrar(this);"><span class="glyphicon glyphicon-minus-sign"></span></button>
						</div>
					</div>
			</div>
		</div>
	</div>	
        

  <div class="form-group">

  </div>

	<div class="row">
		<div class="form-group col-md-10">
			{{Form::submit('Aceptar', array('class' => 'btn btn-primary', 'id'=>'btnEnviar')) }}
			<a href="{{URL::previous()}}" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
		</div>
		
	</div>
	{{ Form::close() }}
</fieldset>
















<div id="modalAsignacionCama" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Crear paciente</h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url' => 'paciente/crearPaciente', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'asignarCamasForm')) }}
				<fieldset><legend>Datos del paciente</legend>
					<div id="divLoadBuscarPaciente" class="row" style="display: none;">
						<div class="form-group col-md-12">
							<span class="col-sm-5 control-label">Buscando paciente </span>
							{{ HTML::image('images/ajax-loader.gif', '') }}
						</div>
					</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="rut" class="col-sm-2 control-label">Run: </label>
						<div class="col-sm-10">
							<div class="input-group">
								{{Form::text('rut', null, array('id' => 'rut2', 'class' => 'form-control', 'autofocus' => 'true', 'readonly'=>'true'))}}
								<span class="input-group-addon"> - </span>
								{{Form::text('dv', null, array('id' => 'dv2', 'class' => 'form-control', 'style' => 'width: 70px;', 'readonly'=>'true'))}}
							</div>
						</div>
					</div>
				</div>
                
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="extranjero" class="col-sm-2 control-label">Extranjero: </label>
                        <div class="col-sm-2">
                            <label for="extranjero" class="col-sm-2 control-label">
                                {{Form::radio('extranjero', "no", true, array('required' => true, "style" => "vertical-align: baseline"))}}No</label>
                        </div>
                        <div class="col-sm-2">
                            <label for="extranjero" class="col-sm-2 control-label">
                                {{Form::radio('extranjero', "si", false, array('required' => true, "style" => "vertical-align: baseline"))}}Sí</label>
                        </div>
                    </div>
                </div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="fechaNac" class="col-sm-2 control-label">Fecha de nacimiento: </label>
						<div class="col-sm-10">
							{{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control'))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="nombre" class="col-sm-2 control-label">Nombre: </label>
						<div class="col-sm-10">
							{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="apellidoP" class="col-sm-2 control-label">Apellido Paterno: </label>
						<div class="col-sm-10">
							{{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control'))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="apellidoM" class="col-sm-2 control-label">Apellido Materno: </label>
						<div class="col-sm-10">
							{{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-12">
						<label for="sexo" class="col-sm-2 control-label">Género: </label>
						<div class="col-sm-10">
							{{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
						</div>
					</div>
				</div>
				</fieldset>
				
				
				<div class="modal-footer">
					{{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }}
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
				{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
@stop