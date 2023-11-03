@extends("Templates/template")

@section("titulo")
Gestión de usuario
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión de usuario</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>

	var deshabilitar=function(id){
		bootbox.dialog({
			message: "<h4>¿ Desea deshabilitar al usuario ?</h4>",
			title: "Confirmación",
			buttons: {
				main: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							url: "desactivarUsuario",
							headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
							data: {id: id},
							type: "post",
							dataType: "json",
							success: function(data){
								mensaje(data); 
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

	var habilitar=function(id){
		bootbox.dialog({
			message: "<h4>¿ Desea habilitar al usuario ?</h4>",
			title: "Confirmación",
			buttons: {
				main: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							url: "activarUsuario",
							headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
							data: {id: id},
							type: "post",
							dataType: "json",
							success: function(data){
								mensaje(data);
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

	var ocultarEstablecimiento=function(){
		var value=$("#tipoUsuario").val();
		$('#establecimiento').val('').change();
		$('#especialidad ').val('').change();
		if(value == "admin_ss" || value == 'monitoreo_ssvq' || value == "admin_iaas")
			{ 
				$("#divEstab").hide();
			}
		else 
			{
				$("#divEstab").show();
			}

		if(value == "admin")$("#divIaas").show();
		else $("#divIaas").hide();

		if(value == "secretaria")$("#divEsp").show();
		else $("#divEsp").hide();
	}

	var getUsuario = function(rut){
		$.ajax({
			url: "{{URL::to('obtenerUsuario')}}",
			dataType: "json",
			data: {"rut":rut},
			success: function(data){
				if(data.exito){
					$("#nombre").val(data.usuario.nombres);
					$("#apellido_paterno").val(data.usuario.apellido_paterno);
					$("#apellido_materno").val(data.usuario.apellido_materno);
					$("#email").val(data.usuario.email);
				}
			},
			error: function(error){

			}
		});
	}
	
	$(function(){
		$('a[href="#deshabilitarUsuarioT"]').click(); 
		ocultarEstablecimiento();
		
		$("#administracionMenu").collapse();

		$("#tipoUsuario").on("change", function(){
			var value=$(this).val();
			$('#establecimiento').val('').change();
			$('#especialidad').val('').change();
			if(value == "admin_ss" || value == "monitoreo_ssvq" || value == "admin_iaas"){
				$("#divEstab").hide();
			}
			else{
				$("#divEstab").show();

			}

			if(value == "admin")$("#divIaas").show();
			else $("#divIaas").hide();

			if(value == "secretaria")$("#divEsp").show();
			else $("#divEsp").hide(); 

		});

		$('#tablaUsuariosHabilitados').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": -1,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 		});

		$('#tablaUsuariosDeshabilitados').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": -1,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 		});

		$("#formCrearUsuario").bootstrapValidator({
			  fields: {
 			 	rut: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El Run es obligatorio'
 			 			},
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $.trim($("#rut").val());
								var dv = $.trim($("#dv").val());
								if (!esRutValido(field_rut, dv)){
									$("#dv").val('');
								}
								return true;
							}
						},
						remote: {
							url: '{{ URL::to("/validarRutUsuario") }}'
						}
 			 		}
 			 	},
 			 	dv: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El dígito verificador es obligatorio'
 			 			},
 			 			callback: {
 			 				callback: function(value, validator, $field){
 			 					if (value === '') {
 			 						return true;
 			 					}
 			 					var rut=$.trim($("#rut").val());
 			 					var esValido=esRutValido(rut, value);
 			 					if(!esValido){
 			 						return {valid: false, message: "Dígito verificador no coincide con el run"};
 			 					}
								getUsuario(rut)
 			 					return true;
 			 				}
 			 			}
 			 		}
 			 	},
 			 	email:{
 			 		validators: {
						notEmpty: {
							  message: 'El correo no debe quedar vacio'
						  },
 			 			emailAddress: {
 			 				message: "La dirección de correo electrónico no es válida"
 			 			},
 			 			regexp: {
                            regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                            message: "Dominio no válido"
                        }
 			 		}
 			 	},
 			 	apellido_paterno:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			}
 			 		}
 			 	},
 			 	nombre:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			}
 			 		}
 			 	},
			      'establecimiento': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar un establecimiento'
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    establecimiento: validator.getFieldElements('establecimiento').val()
                                };
                            },
                            url: "{{URL::to("/validar_establecimiento")}}"
                        }
                    }
                } ,
				'especialidad': {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar una especialidad'
                        },
                        remote: {
                            data: function(validator){
                                return {
                                    especialidad: validator.getFieldElements('especialidad').val()
                                };
                            },
                            url: "{{URL::to("/validar_especialidades")}}"
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
 				url: $form .prop("action"),
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(data){
 					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data['exito'],
						didOpen: function() {
						 setTimeout(function() {
							$form[0] . reset();
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
 			$("#formCrearUsuario input[type='submit']").prop("disabled", false);
 		});
	});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("section")

<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#crearUsuarioT" aria-controls="crearUsuarioT" role="tab" data-toggle="tab">Crear usuario</a></li>
		<li role="presentation"><a href="#deshabilitarUsuarioT" aria-controls="deshabilitarUsuarioT" role="tab" data-toggle="tab">Usuarios habilitados</a></li>
		<li role="presentation"><a href="#habilitarUsuarioT" aria-controls="habilitarUsuarioT" role="tab" data-toggle="tab">Usuarios deshabilitados</a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="crearUsuarioT" style="margin-top:20px;">
			{{ Form::open(array('url' => 'administracion/registrarUsuario', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formCrearUsuario')) }}
			<div class="form-group">
				<label for="rut" class="col-sm-2 control-label">Run: </label>
				<div class="col-sm-4" style="width: 490px;z-index: 0;">
					<div class="input-group">
						{{Form::number('rut', null, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true',"step"=>0))}}
						<span class="input-group-addon"> - </span>
						{{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="nombre" class="col-sm-2 control-label">Nombre(s): </label>
				<div class="col-sm-4">
					{{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group">
				<label for="apellido_paterno" class="col-sm-2 control-label">Apellido paterno: </label>
				<div class="col-sm-4">
					{{Form::text('apellido_paterno', null, array('id' => 'apellido_paterno', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group">
				<label for="apellido_materno" class="col-sm-2 control-label">Apellido materno: </label>
				<div class="col-sm-4">
					{{Form::text('apellido_materno', null, array('id' => 'apellido_materno', 'class' => 'form-control'))}}
				</div>
			</div>			
			<div class="form-group">
				<label for="email" class="col-sm-2 control-label">Correo electrónico: </label>
				<div class="col-sm-4">
					{{Form::text('email', null, array('id' => 'email', 'class' => 'form-control'))}}
				</div>
			</div>
			<div class="form-group">
				<label for="tipoUsuario" class="col-sm-2 control-label">Tipo de usuario: </label>
				<div class="col-sm-4">
					{{ Form::select('tipoUsuario', $tipoUsuario, null, array('class' => 'form-control', 'id' => 'tipoUsuario')) }}
				</div>
			</div>
			<div id="divIaas" class="form-group">
				<label for="establecimiento" class="col-sm-2 control-label">Gestor-IAAS: </label>
				<div class="col-sm-4">
					{{ Form::radio('gestor_iaas','Si') }} SI
					{{ Form::radio('gestor_iaas','No',true)}} NO
			</div>
			</div>
			<div id="divEstab" class="form-group">
				<label for="establecimiento" class="col-sm-2 control-label">Establecimiento: </label>
				<div class="col-sm-4">
					{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento', 'placeholder' => 'Seleccione')) }}
				</div>
			</div>
			<div id="divEsp" class="form-group">
				<label for="especialidad" class="col-sm-2 control-label">Especialidad: </label>
				<div class="col-sm-4">
					{{ Form::select('especialidad', $especialidad, null, array('class' => 'form-control', 'id' => 'especialidad', 'placeholder' => 'Seleccione')) }}
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-4">
					{{Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
				</div>
			</div>
			{{ Form::close() }}	
		</div>

		<div role="tabpanel" class="tab-pane" id="deshabilitarUsuarioT" style="margin-top:20px;">
			<div class="table-responsive">
			<table id="tablaUsuariosHabilitados" class="table table-striped table-condensed table-bordered">
				<thead>
					<tr>
						<th>Run</th>
						<th>Nombres</th>
						<th>Apellidos</th>
						<th>Correo electrónico</th>
						<th>Tipo</th>
						<th>Establecimiento</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					@foreach($usuarios as $usuario)
					<tr>
						<td>{{$usuario["rut"]."-".$usuario["dv"]}}</td>
						<td>{{$usuario["nombres"]}}</td>
						<td>{{$usuario["apellido_paterno"]}} {{$usuario["apellido_materno"]}}</td>
						<td>{{$usuario["email"]}}</td>
						<td>{{$usuario["tipo"]}}</td>
						<td>{{$usuario["nombre"]}}</td>
						<td> 
							<button class="btn btn-danger" onclick="deshabilitar({{$usuario['id']}});">Deshabilitar</button> 
							<br>
							<button class="btn btn-primary" onclick="window.location.href='{{asset('administracion/editarUsuario/'.$usuario['id'])}}'">Editar</button>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="habilitarUsuarioT" style="margin-top:20px;">
			<div class="table-responsive">
			<table id="tablaUsuariosDeshabilitados" class="table table-striped table-condensed table-bordered">
				<thead>
					<tr>
						<th>Run</th>
						<th>Nombres</th>
						<th>Apellidos</th>
						<th>Correo electrónico</th>
						<th>Tipo</th>
						<th>Establecimiento</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					@foreach($usuariosDeshabilitados as $usuario)
					<tr>
						<td>{{$usuario["rut"]."-".$usuario["dv"]}}</td>
						<td>{{$usuario["nombres"]}}</td>
						<td>{{$usuario["apellido_paterno"]}} {{$usuario["apellido_materno"]}}</td>
						<td>{{$usuario["email"]}}</td>
						<td>{{$usuario["tipo"]}}</td>
						<td>{{$usuario["nombre"]}}</td>
						<td> <button class="btn btn-success" onclick="habilitar({{$usuario['id']}});">Habilitar</button></td>
					</tr>
					@endforeach
				</tbody>
			</table>
			</div>
		</div>
	</div>

</div>
@stop

