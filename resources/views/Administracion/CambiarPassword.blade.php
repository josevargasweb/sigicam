@extends("Templates/template")

@section("titulo")
Gestión de usuario
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Cambiar contraseña</a></li>
@stop

@section("script")
<script>
	$(document).ready(function(){
		function cambiarClave(){

			usuario = {!! auth()->user() !!}
			fecha_ingreso = usuario["fecha_ingreso"];
			fecha_actualizacion = usuario["updated_at"];
			hoy = moment().format("YYYY-MM-DD HH:mm:ss");
			if(fecha_actualizacion == null || fecha_actualizacion == ""){
				fecha_actualizacion = null;
			}else{
				fecha_actualizacion = moment(fecha_actualizacion).format("YYYY-MM-DD");
			}

			if(fecha_ingreso == "" || fecha_ingreso == null){
				fecha_ingreso = null;
			}else{
				fecha_ingreso = moment(fecha_ingreso).format("YYYY-MM-DD");
			}

			if(fecha_actualizacion == null){
				tiempo = moment(hoy).diff(fecha_ingreso, "months");
			}
			else{
				tiempo = moment(hoy).diff(fecha_actualizacion,"months");
			}

			if(tiempo >= 4){
				//$("#oculto").show();
				
				bootbox.confirm({
				message: "<h4>No ha actualizado su clave en 4 meses, ¿Desea hacerlo ahora?</h4>",
				buttons: {
					confirm: {
						label: 'Cambiar Clave',
						className: 'btn-success'
					},
					cancel: {
						label: 'Omitir',
						className: 'btn-danger'
					}
				},
				callback: function (result) {
					if(result == true){
						$("#correo").val(usuario["email"]);
					}else{
						$.ajax({
							url: "actualizarFechaClave",
							headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
							type: "get",
							//dataType: "json",
							success: function(data){
								if(data.exito){
									swalExito.fire({
									title: 'Exito!',
									text: data['exito'],
									didOpen: function() {
										setTimeout(function() {
										window.location.href = "{{URL::to('/index')}}";
										}, 2000)
									},
									});	
								}
								if(data.error){
									swalError.fire({
									title: 'Cambio de clave',
									text:data.error
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
			}

		}
		cambiarClave();
		tieneCorreo();
		
		function tieneCorreo(){
			usuario = {!! auth()->user() !!}
			correo = usuario["email"];
			echa_ingreso = usuario["fecha_ingreso"];

			if(correo == null || correo == ""){

			}else{
				$("#correo").val(correo);
			}
		}
	});
	
	
	$(function(){
		$("#administracionMenu").collapse();

		$("#formCambiarPassword").bootstrapValidator({
				fields: {
					password: {
						validators: {
							notEmpty: {
								message: 'La contraseña es obligatoria'
							},
							remote: {
								message: 'La contraseña ingresada no corresponde a la original',
								url: 'mismaPassword',
								headers: {        
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
								type: 'POST'
							}
						}
					},
					passwordNew: {
						validators: {
							notEmpty: {
								message: 'La contraseña es obligatoria'
							},
							identical: {
								field: 'passwordNew2',
								message: 'La contraseña y su confirmación no son iguales'
							}
						}
					},
					passwordNew2: {
						validators: {
							notEmpty: {
								message: 'La contraseña es obligatoria'
							},
							identical: {
								field: 'passwordNew',
								message: 'La contraseña y su confirmación no son iguales'
							}
						}
					},
					correo:{
						validators: {
							notEmpty: {
								message: 'El correo no debe quedar vacio'
							},
							emailAddress: {
							message: 'El correo no tiene un formato valido'
						}
						}
					}
				}
			}).on('status.field.bv', function(e, data) {
			}).on("success.form.bv", function(evt){
				evt.preventDefault(evt);
				var $form = $(evt.target);
				$("#formCambiarPassword input[type='submit']").prop("disabled", true);
				showLoad();
				$.ajax({
					url: $form .prop("action"),
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
								text: data.mensaje,
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
						$("#formCambiarPassword input[type='submit']").prop("disabled", false);
						hideLoad();
					},
					error: function(error){
						console.log(error);
						$("#formCambiarPassword input[type='submit']").prop("disabled", false);
						hideLoad();
					}
				});
			});
	});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")

<fieldset>
	<legend>Cambiar contraseña</legend>
	{{ Form::open(array('url' => 'administracion/cambiarPassword', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formCambiarPassword')) }}
	<div class="form-group">
		<label for="nombre" class="col-sm-2 control-label">Contraseña antigua: </label>
		<div class="col-sm-4">
			{{Form::password('password', array('id' => 'password', 'class' => 'form-control'))}}
		</div>
	</div>
	<div class="form-group">
		<label for="nombre" class="col-sm-2 control-label">Contraseña nueva: </label>
		<div class="col-sm-4">
			{{Form::password('passwordNew', array('id' => 'passwordNew', 'class' => 'form-control'))}}
		</div>
	</div>
	<div class="form-group">
		<label for="nombre" class="col-sm-2 control-label">Repita contraseña: </label>
		<div class="col-sm-4">
			{{Form::password('passwordNew2', array('id' => 'passwordNew2', 'class' => 'form-control'))}}
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Correo electrónico: </label>
		<div class="col-sm-5">
			{{Form::text('correo', '', array('id' => 'correo', 'class' => 'form-control'))}}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-4">
			{{Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
		</div>
	</div>
	{{ Form::close() }}	
</fieldset>

@stop