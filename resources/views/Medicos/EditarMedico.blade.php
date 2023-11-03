@extends("Templates/template")

@section("titulo")
Editar Médico
@stop

@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#">Gestión de Médicos</a></li>
<li><a href="#" onclick='location.reload()'>Editar Médico</a></li>
@stop

@section("script")

<script>
    $(function() {

        $("#formEditarMedico").bootstrapValidator({
			excluded: ':disabled', 
			fields: {
 			 	rut_medico: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El run es obligatorio'
 			 			},
						callback: {
							callback: function(value, validator, $field){
								var field_rut = $.trim($("#rut_medico").val());
								var dv = $.trim($("#dv_medico").val());
								if (!esRutValido(field_rut, dv)){
									$("#dv_medico").val('');
								}
								return true;
							}
						}
						
 			 		}
 			 	},
 			 	dv_medico: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El dígito verificador es obligatorio'
 			 			},
 			 			callback: {
 			 				callback: function(value, validator, $field){
 			 					if (value === '') {
 			 						return true;
 			 					}
 			 					var rut=$.trim($("#rut_medico").val());
 			 					var esValido=esRutValido(rut, value);
 			 					if(!esValido){
 			 						return {valid: false, message: "Dígito verificador no coincide con el run"};
 			 					}
 			 					return true;
 			 				}
 			 			}
 			 		}
 			 	},
 			 	correo:{
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
 			 	apellido_medico:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			}
 			 		}
 			 	},
 			 	nombre_medico:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			}
 			 		}
 			 	},
 			 	titulo:{
 			 		validators:{
 			 			notEmpty: {
 			 				message: "Campo obligatorio"
 			 			}
 			 		}
 			 	},
				'especialidad[]': {
					validators:{
						callback: {
							callback: function(value, validator, $field){
								if($("#especialidad").val() == '' || $("#especialidad").val() == null){
									return {valid: false, message: "Campo obligatorio"};
								}else{
									return true;
								}
							}
						},
					}
				},
 			 }
		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
            var $button = $form.data('bootstrapValidator').getSubmitButton();
 			$.ajax({
 				url: "{{URL::to('administracion/actualizarDatosMedico')}}",
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(response){
                     //mensaje(response);
 					if(response.exito){
                    swalExito.fire({
						title: 'Exito!',
						text: response.exito,
						didOpen: function() {
							setTimeout(function() {
						        window.location.href = "{{URL::to('/administracion/gestionMedicos')}}";
							}, 2000)
						},
						});

 					}
 					if(response.error){
                        swalError.fire({
                            title: 'Error',
                            text:response.error
						});
 					}
 				},
 				error: function(error){
 					console.log(error);
 				}
 			});
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
    <a class="btn btn-primary" href="{{URL::to('administracion/gestionMedicos')}}"><i class="glyphicon glyphicon-chevron-left">Volver</i></a>
    {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarMedico')) }}

    {{Form::hidden('id_medico', $medico->id_medico)}}
    <div class="formulario" style="height: 550px;">
		<div class="panel panel-default" >
			<div class="panel-heading panel-info">
                <h4>Editar Médico</h4>
            </div>
            <div class="panel-body">
                <legend>Datos de médico</legend>
                <div class="col-md-12">
                    <div class="col-md-5 form-group">
                        <div class="col-md-12">
                            <label class="control-label" title="">Run: </label>
                            <div class="input-group">
                                {{Form::text('rut_medico', $medico->rut_medico, array('id' => 'rut_medico', 'class' => 'form-control', 'autofocus' => 'true'))}}
                                <span class="input-group-addon"> - </span>
                                @if($medico->dv_medico == "10")
                                    {{Form::text('dv_medico', "k", array('id' => 'dv_medico', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                                @else
                                    {{Form::text('dv_medico', $medico->dv_medico, array('id' => 'dv_medico', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 form-group">
                        <div class="col-md-12"> 
                            <label for="correo" class="control-label">Correo electrónico: </label>
                            {{Form::text('correo', $medico->correo, array('id' => 'correo', 'class' => 'form-control'))}}
                        </div>
                    </div>    
                    <div class="col-md-2 form-group">
                        <label class="control-label" title="visible_medico">Visible: </label>
                        {{ Form::select('visible_medico', array( true => 'Si', false => 'No'), $medico->visible_medico, array('id' => 'visible_medico', 'class' => 'form-control')) }}
                        
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <div class="col-md-4 form-group">
                        <div class="col-md-12">
                            <label for="nombre" class="control-label">Nombre(s): </label>
                            {{Form::text('nombre_medico',  $medico->nombre_medico, array('id' => 'nombre_medico', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="apellido_medico" class="control-label">Apellidos: </label>
                        {{Form::text('apellido_medico',  $medico->apellido_medico, array('id' => 'apellido_medico', 'class' => 'form-control'))}}
                    </div>
                    <div class="col-md-4">
                        <label for="establecimiento" class="control-label">Establecimiento: </label>
                        {{ Form::select('establecimiento_medico', $establecimientos, $medico->establecimiento_medico, array('class' => 'form-control', 'id' => 'establecimiento_medico')) }}
                    </div>
                </div>                
                <br>
                <div class="col-md-12">
                    <div class="col-md-4 form-group">
                        <div class="col-md-12">    
                            <label for="titulo" class="control-label">Titulo profesional: </label>
                            {{ Form::select('titulo', $tituloProfesionales, $medico->cod_titulo, array('class' => 'form-control', 'id' => 'titulo', 'placeholder' => 'Seleccione')) }}
                        </div>  
                    </div>
                    <div class="col-md-5 form-group">
                        <label for="apellido_medico" class="control-label">Especialidad: </label>
                        {{ Form::select('especialidad[]', $especialidadMedica, $especialidadMedico, array('class' => 'form-control selectpicker', 'id' => 'especialidad','multiple')) }}
                    </div>
                    <div class="col-md-3">
                        <label for="Celular" class="control-label">Celular: </label>
                        {{Form::number('celular', $medico->celular, array('id' => 'celular', 'class' => 'form-control'))}}
                    </div>
                </div>

                <br>

                <div class="col-md-12">
                    <div class="col-sm-4">
                        {{Form::submit('Actualizar', array('class' => 'btn btn-primary')) }}
                    </div>
                </div>
                {{ Form::close() }}

            </div>
        </div>
        {{ Form::close() }}  
    </div>
	   
        
        
        
@stop
