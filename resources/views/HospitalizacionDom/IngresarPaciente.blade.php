@extends("Templates/template")

@section("titulo")
Gestión de Camas
@stop

@section("script")

<script>
	var ingresado=false;
	alert();
	var getPacienteRut=function(rut){
		alert(rut);
		$.ajax({
			url: "{{URL::to('/getPaciente')}}",
			headers: {        
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data: {rut: rut},
			dataType: "json",
			type: "post",
			success: function(data){
				if(rut != ""){
					$("#rut").val(data.rutSin);
					$("#fechaNac").datepicker('update', data.fecha);
					$("#nombre").val(data.nombre);
					$("#sexo").val(data.genero);
					$("#apellidoP").val(data.apellidoP);
					$("#apellidoM").val(data.apellidoM);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}


	
	$(function(){
		
		var fecha = $("#fechaIngreso").data("DateTimePicker");
		console.log($("#fechaIngreso"));
		window._gc_now = "{{ Carbon\Carbon::now()->format('d-m-Y H:i') }}";
		fecha.date(window._gc_now);
		fecha.minDate(moment(window._gc_now).subtract(3, "days").startOf('day'));
		fecha.maxDate(moment(window._gc_now));


		$("#urgenciaMenu").collapse();

		$("#fechaNac").datepicker({
			autoclose: true,
			language: "es",
			format: "dd-mm-yyyy",
			todayHighlight: true,
			endDate: "+0d"
		}).on("changeDate", function(){
			$('#agregarListaEspera').bootstrapValidator('revalidateField', 'fechaNac');
		});

		$("#fechaIngreso").on("dp.change", function(){
			//alert("cambio");
			var fecha = $(this).data("DateTimePicker");
			var mom = fecha.date().format("YYYY-MM-DD HH:mm:ss");
			fecha.hide();
			$(this).blur();
		});

		$("#agregarListaEspera").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				rut: {
					validators: {
						remote: {
							message: "El paciente ya se encuentra en la lista de espera",
							url: "pacienteEnListaEspera"
						},
						callback: {
							callback: function(value, validator, $field){
								$("#dv").val('');
								return true;
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
				diagnostico: {
					validators:{
						notEmpty: {
							message: 'El diagnóstico es obligatorio'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			$("#agregarListaEspera input[type='submit']").prop("disabled", false);
		}).on("success.form.bv", function(evt){
			$("#agregarListaEspera input[type='submit']").prop("disabled", false);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			if(!ingresado){
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
						ingresado=true;
						if(data.exito){
							swalExito.fire({
							title: 'Exito!',
							text: data.exito,
							didOpen: function() {
								setTimeout(function() {
									location . reload();
									$form[0] . reset();
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
		});
	});
</script>
<meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Ingresar paciente</a></li>
@stop

@section("section")

<fieldset>
	<legend>Ingresar paciente</legend>
	<div class="col-md-7">
	{{ Form::open(array('url' => 'urgencia/agregarListaEspera', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'agregarListaEspera')) }}
		<fieldset>
			<legend>Datos del paciente</legend>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="run" class="col-sm-2 control-label">Run: </label>
					<div class="col-sm-10">
						<div class="input-group">
							{{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
							<span class="input-group-addon"> - </span>
							{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
						</div>
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
		<fieldset>
			<legend>Datos de ingreso</legend>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="caso_social" class="col-sm-2 control-label">Caso social: </label>
					<div class="col-sm-2">
						<label for="caso_social" class="col-sm-2 control-label">
							{{Form::radio('caso_social', "no", true, array('required' => true, "style" => "vertical-align: baseline"))}}No</label>
					</div>
					<div class="col-sm-2">
						<label for="caso_social" class="col-sm-2 control-label">
							{{Form::radio('caso_social', "si", false, array('required' => true, "style" => "vertical-align: baseline"))}}Sí</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="prevision" class="col-sm-2 control-label">Previsión: </label>
					<div class="col-sm-10">
						{{ Form::select('prevision', $prevision, 'SIN INFORMACION', array('class' => 'form-control')) }}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="diagnostico" class="col-sm-2 control-label">Diagnóstico: </label>
					<div class="col-sm-10">
						{{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="riesgo" class="col-sm-2 control-label">Riesgo: </label>
					<div class="col-sm-10">
						{{ Form::select('riesgo', $riesgo, null, array('class' => 'form-control')) }}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="medico" class="col-sm-2 control-label">Medico: </label>
					<div class="col-sm-10">
						{{Form::text('medico', null, array('id' => 'medico', 'class' => 'form-control'))}}
					</div>
				</div>
			</div>
			<div class="row " id="divFechaIngreso">
				<div class="form-group col-md-12">
					<label for="fechaIngreso" class="col-sm-2 control-label">Fecha de ingreso: </label>
					<div class="col-sm-10">
						{{Form::text('fechaIngreso', null, array('id' => 'fechaIngreso', 'class' => 'form-control'))}}
					</div>
				</div>
				<div class="form-group col-md-12" id="categorizacionesIngreso">
				</div>
			</div>
		</fieldset>
	{{Form::submit('Ingresar a lista de espera', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }}
	{{ Form::close() }}
	</div>
</fieldset>
<br><br>
@stop