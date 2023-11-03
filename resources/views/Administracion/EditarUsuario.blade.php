@extends("Templates/template")

@section("titulo")
Editar Usuario
@stop

@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="{{URL::to('administracion/gestionUsuario')}}">Gestión de usuario</a></li>
<li><a href="#" onclick='location.reload()'>Editar usuario</a></li>
@stop

@section("script")

<script>
    $(function() {
        //primero se debe identificar el tipo de usuario que se esta editando
        var tipo_usuario=$("#tipoUsuario").val();
        //$('#establecimiento').val('').change();
        //$('#especialidad').val('').change();
        if(tipo_usuario == "admin_ss" || tipo_usuario == "monitoreo_ssvq" || tipo_usuario == "admin_iaas"){
            $("#divEstab").hide();
        }
        else{
            $("#divEstab").show();
        }

        if(tipo_usuario == "admin")$("#divIaas").show();
        else $("#divIaas").hide();

        if(tipo_usuario == "secretaria")$("#divEsp").show();
        else $("#divEsp").hide(); 
        
        @if($usuario->iaas)
            $('input:radio[name=gestor_iaas]').attr('checked',true);
        @endif


       /*  $.ajax({
            url: "{{URL::to('administracion/cargarRestricciones')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {"idUsuario": {{ $usuario->id }} },
            dataType: "json",
            type: "get",
            success: function(data){
                console.log(data);
                var ejemplo = ['180','181'];
                console.log("ejemplo", ejemplo);
                $(".selectpicker").val( ejemplo).selectpicker("refresh").trigger('change');

            },
            error: function(error){
                console.log(error);
            }
        }); */

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

        $("#formEditarUsuario").bootstrapValidator({
			fields: {
 			 	rut: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El run es obligatorio'
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
								//getUsuario(rut)
 			 					return true;
 			 				}
 			 			}
 			 		}
 			 	},
 			 	email:{
 			 		validators: {
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
 			 	password: {
 			 		validators: {
 			 			identical: {
 			 				field: 'passwordConfirm',
 			 				message: 'La contraseña y su confirmación no son iguales'
 			 			}
 			 		}
 			 	},
 			 	passwordConfirm: {
 			 		validators: {
 			 			identical: {
 			 				field: 'password',
 			 				message: 'La contraseña y su confirmación no son iguales'
 			 			}
 			 		}
 			 	},
                gestor_iaas : {
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
                },
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
            var $button = $form.data('bootstrapValidator').getSubmitButton();
 			$.ajax({
 				url: "{{URL::to('administracion/registrarCambioUsuario')}}",
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
                                    //limpiar datos 
                                    $('#password').val('').change();
                                    $('#passwordConfirm').val('').change(); 
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
                                // location . reload();
                            }
						});
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

@stop

@section("section")
    <style>
        .formulario > .panel-default > .panel-heading {
            background-color: #bce8f1 !important;
        }
    </style>

    <br>
    <a class="btn btn-primary" href="{{URL::to('administracion/gestionUsuario')}}"><i class="glyphicon glyphicon-chevron-left">Volver</i></a>
    {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarUsuario')) }}

    {{Form::hidden('idUsuario', $usuario->id)}}
    <div class="formulario" style="    height: 550px;">
		<div class="panel panel-default" >
			<div class="panel-heading panel-info">
                <h4>Edición de datos del usuario</h4>
            </div>
            <div class="panel-body">
                <legend>Datos de usuario</legend>

                <div class="col-md-12">
                    <div class="col-md-5 form-group">
                        <div class="col-md-12">
                            <label class="control-label" title="Extranjero">Run: </label>
                            <div class="input-group">
                                {{Form::text('rut', $usuario->rut, array('id' => 'rut', 'class' => 'form-control', 'autofocus' => 'true'))}}
                                <span class="input-group-addon"> - </span>
                                {{Form::text('dv', $usuario->dv, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-5 form-group">
                        <div class="col-md-12"> 
                            <label for="nombre" class="control-label">Correo electrónico: </label>
                            {{Form::text('email', $usuario->email, array('id' => 'email', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="control-label" title="visible">Visible: </label>
                        {{ Form::select('visible', array( true => 'Si', false => 'No'), $usuario->visible, array('id' => 'visible', 'class' => 'form-control')) }}
                        
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <div class="col-md-4 form-group">
                        <div class="col-md-12">
                            <label for="nombre" class="control-label">Nombre(s): </label>
                            {{Form::text('nombre', $usuario->nombres, array('id' => 'nombre', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="apellido_paterno" class="control-label">Apellido paterno: </label>
                        {{Form::text('apellido_paterno', $usuario->apellido_paterno, array('id' => 'apellido_paterno', 'class' => 'form-control'))}}
                    </div>
                    <div class="col-md-4">
                        <label for="apellido_materno" class="control-label">Apellido materno: </label>
                        {{Form::text('apellido_materno', $usuario->apellido_materno, array('id' => 'apellido_materno', 'class' => 'form-control'))}}
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <div class="col-md-4 form-group">
                        <div class="col-md-12">
                            <label for="tipoUsuario" class="control-label">Tipo de usuario: </label>
                            {{ Form::select('tipoUsuario', $tipoUsuario, $usuario->tipo, array('class' => 'form-control', 'id' => 'tipoUsuario')) }}
                        </div>                    
                    </div>
                    <div class="col-md-4 form-group" id="divEstab">
                        <div class="col-md-12">
                            <label for="establecimiento" class="control-label">Establecimiento: </label>
                            {{ Form::select('establecimiento', $establecimiento, $usuario->establecimiento, array('class' => 'form-control', 'id' => 'establecimiento')) }}
                        </div>                    
                    </div>
                    <div class="col-md-4 form-group" id="divEsp">
                        <div class="col-md-12">
                            <label for="especialidad" class="control-label">Especialidad: </label>
                            {{ Form::select('especialidad', $especialidad, $usuario->usuario_especialidad, array('class' => 'form-control', 'id' => 'especialidad', 'placeholder' => 'Seleccione')) }}  
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group col-md-3" id="divIaas">
                        <div class="col-sm-12">
                            <label for="gestor_iaas" class="control-label">Gestor-IAAS: </label>
                            <div class="input-group">
                                <label class="radio-inline">{{Form::radio('gestor_iaas', "no", true, array('required' => true))}}No</label>
                                <label class="radio-inline">{{Form::radio('gestor_iaas', "si", false, array('required' => true))}}Sí</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-5">
                        <div class="col-sm-12">
                            <label for="unidades" class="control-label">Restricción de acceso a las siguientes unidades: </label>
                            {{ Form::select('unidades[]', $unidades, $restricciones, array('id' => 'restriccion', 'class' => 'form-control selectpicker', 'multiple') ) }}
                        </div>
                    </div>
                </div>

                <legend>Cambiar contraseña del usuario</legend>

                <div class="col-md-12">
                    <div class="form-group col-md-12">
                        <div class="col-sm-4">
                            <label for="password" class="control-label">Contraseña: </label>
                            {{Form::password('password', array('id' => 'password', 'class' => ' form-control'))}}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group col-md-12">
                        <div class="col-sm-4">
                            <label for="password" class="control-label">Repetir contraseña: </label>
                            {{Form::password('passwordConfirm', array('id' => 'passwordConfirm', 'class' => 'form-control'))}}
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-12">
                    <div class="col-sm-4">
                        {{Form::submit('Aceptar', array('class' => 'btn btn-primary')) }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    

    
    {{ Form::close() }}	   
        
        
        
@stop
