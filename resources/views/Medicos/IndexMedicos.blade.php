@extends("Templates/template")

@section("titulo")
Gestión de Médicos
@stop


@section("miga")
<li><a href="#">Administración</a></li>
<li><a href="#" onclick='location.reload()'>Gestión de Médicos</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>
    $('#tablaMedicos').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": 10,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 	});

    $('#tablaMedicosDeshabilitados').dataTable({	
 			"aaSorting": [[0, "desc"]],
 			"iDisplayLength": 10,
 			"bJQueryUI": true,
 			"oLanguage": {
 				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
 			}
 	});
     
    var deshabilitar = function(id_medico){
        console.log("id medico: ", id_medico);
        bootbox.dialog({
            message: "<h4>¿ Desea eliminar el médico ?</h4>",
            title: "Confirmación",
            buttons: {
                main: {
                    label: "Aceptar",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                            url: "deshabilitarMedico",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {id_medico: id_medico},
                            type: "post",
                            dataType: "json",
                            success: function(response){
                                mensaje(response);
                                console.log("respuesta: ", response);
                            },
                            error: function(error){
                                console.log("error: ", error);
                            }
                        });
                    }
                },
                danger: {
                    label: "Cancelar",
                    className: "btn-danger",
                    callback: function(){

                    }
                }
            }
        });
    }

    var habilitar = function(id_medico){
        console.log("id medico: ", id_medico);
        bootbox.dialog({
            message: "<h4>¿ Desea reingresar al médico ?</h4>",
            title: "Confirmación",
            buttons: {
                main: {
                    label: "Aceptar",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                            url: "habilitarMedico",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {id_medico: id_medico},
                            type: "post",
                            dataType: "json",
                            success: function(response){
                                mensaje(response);
                                console.log("respuesta: ", response);
                            },
                            error: function(error){
                                console.log("error: ", error);
                            }
                        });
                    }
                },
                danger: {
                    label: "Cancelar",
                    className: "btn-danger",
                    callback: function(){
                    }
                }
            }
        });
    }

    $(function() {
    $("#formCrearMedico").bootstrapValidator({
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
				}
 			 }
		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
            var $button = $form.data('bootstrapValidator').getSubmitButton();
 			$.ajax({
 				url: "{{URL::to('administracion/registrarMedico')}}",
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(response){
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
  <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("section")

<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#crearMedicoT" aria-controls="crearMedicoT" role="tab" data-toggle="tab">Crear Médicos</a></li>
        <li role="presentation"><a href="#gestionMedicoT" aria-controls="gestionMedicoT" role="tab" data-toggle="tab">Gestionar Médicos</a></li>
        <li role="presentation"><a href="#HabilitarMedicoT" aria-controls="HabilitarMedicoT" role="tab" data-toggle="tab">Habilitar Médicos</a></li>
        
    </ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="crearMedicoT" style="margin-top:20px;">
            {{ Form::open(array('url' => 'administracion/registrarMedico', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formCrearMedico')) }}
            <div class="formulario" style="    height: 550px;">
                <div class="panel panel-default" >
                    <div class="panel-heading panel-info">
                        <h4>Crear médico</h4>
                    </div>
                    <div class="panel-body">
                        <legend>Datos medico</legend>
                        <div class="col-md-12">
                            <div class="col-md-5 form-group">
                                <div class="col-md-12">
                                    <label class="control-label" title="">Run: </label>
                                    <div class="input-group">
                                        {{Form::text('rut_medico', null, array('id' => 'rut_medico', 'class' => 'form-control', 'autofocus' => 'true'))}}
                                        <span class="input-group-addon"> - </span>
                                        {{Form::text('dv_medico', null, array('id' => 'dv_medico', 'class' => 'form-control', 'style' => 'width: 70px;'))}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 form-group">
                                <div class="col-md-12"> 
                                    <label for="email" class="control-label">Correo electrónico: </label>
                                    {{Form::text('email', null, array('id' => 'email', 'class' => 'form-control'))}}
                                </div>
                            </div>    
                            <div class="col-md-2 form-group">
                                <label class="control-label" title="visible">Visible: </label>
                                {{ Form::select('visible', array( true => 'Si', false => 'No'), null, array('id' => 'visible', 'class' => 'form-control')) }}
                                
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <div class="col-md-4 form-group">
                                <div class="col-md-12">
                                    <label for="nombre" class="control-label">Nombre(s): </label>
                                    {{Form::text('nombre_medico', null, array('id' => 'nombre_medico', 'class' => 'form-control'))}}
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="apellido_medico" class="control-label">Apellidos: </label>
                                {{Form::text('apellido_medico', null, array('id' => 'apellido_medico', 'class' => 'form-control'))}}
                            </div>
                            <div class="col-md-4">
                                <label for="establecimiento" class="control-label">Establecimiento: </label>
                                {{ Form::select('establecimiento_medico', $establecimientos, null, array('class' => 'form-control', 'id' => 'establecimiento_medico')) }}
                            </div>
                        </div>

                        <br>
                        <div class="col-md-12">
                            <div class="col-md-4 form-group">
                                <div class="col-md-12">    
                                    <label for="titulo" class="control-label">Titulo profesional: </label>
                                    {{ Form::select('titulo', $tituloProfesionales, null, array('class' => 'form-control', 'id' => 'titulo', 'placeholder' => 'Seleccione')) }}
                                </div>  
                            </div>
                            <div class="col-md-5 form-group">
                                <label for="apellido_medico" class="control-label">Especialidad: </label>
                                {{ Form::select('especialidad[]', $especialidadMedica, null, array('class' => 'form-control selectpicker', 'id' => 'especialidad','multiple')) }}
                            </div>
                            <div class="col-md-3">
                                <label for="Celular" class="control-label">Celular: </label>
                                {{Form::number('celular', null, array('id' => 'celular', 'class' => 'form-control'))}}
                            </div>
                        </div>

                        <br>

                        <div class="col-md-12">
                            <div class="col-sm-4">
                                {{Form::submit('Crear', array('class' => 'btn btn-primary')) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
		</div>

		<div role="tabpanel" class="tab-pane" id="HabilitarMedicoT" style="margin-top:20px;">
			<div class="table-responsive">
                <table id="tablaMedicosDeshabilitados" class="table table-striped table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>Run</th>
                            <th>Establecimiento</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Titulo</th>
                            <th>Especialidad</th>
                            {{-- <th>Celular</th>
                            <th>Correo electrónico</th> --}}
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deshabilitados as $deshabilitado)
                        <tr>
                            @if($deshabilitado["dv_medico"] == 10)
                            <td>{{$deshabilitado["rut_medico"]."-"."k"}}</td>
                            @else
                            <td>{{$deshabilitado["rut_medico"]."-".$deshabilitado["dv_medico"]}}</td>
                            @endif
                            <td>{{$deshabilitado["nombre_establecimiento"]}}</td> 
                            <td>{{$deshabilitado["nombre_medico"]}}</td>
                            <td>{{$deshabilitado["apellido_medico"]}}</td>
                            <td>{{$deshabilitado["titulo_profesional"]}}</td>
                            <td style="font-size: 11px !important;">
                                @if ($deshabilitado["especialidad"]!= "")
                                @foreach(explode(',', $deshabilitado["especialidad"]) as $especialidad) 
                                {{$especialidad}}
                                @endforeach
                                @endif
                            </td>
                            {{-- @if($deshabilitado["celular"] != "")
                            <td>{{$deshabilitado["celular"]}}</td>
                            @else
                            <td>---</td>
                            @endif
                            @if($deshabilitados["correo"] != "")
                            <td>{{$deshabilitados["correo"]}}</td> 
                            @else
                            <td>---</td>
                            @endif --}}
                            <td> <a class="btn btn-sm btn-primary" onclick="habilitar({{$deshabilitado['id_medico']}});" class="cursor">Habilitar</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
        </div>
        
        <div role="tabpanel" class="tab-pane" id="gestionMedicoT" style="margin-top:20px;">
			<div class="table-responsive">
                <table id="tablaMedicos" class="table table-striped table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>Run</th>
                            <th>Establecimiento</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Titulo</th>
                            <th>Especialidad</th>
                            {{-- <th>Celular</th>
                            <th>Correo electrónico</th> --}}
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicos as $medico)
                        <tr>
                            @if($medico["dv_medico"] == 10)
                            <td>{{$medico["rut_medico"]."-"."k"}}</td>
                            @else
                            <td>{{$medico["rut_medico"]."-".$medico["dv_medico"]}}</td>
                            @endif
                            <td>{{$medico["nombre_establecimiento"]}}</td> 
                            <td>{{$medico["nombre_medico"]}}</td>
                            <td>{{$medico["apellido_medico"]}}</td>
                            <td>{{$medico["titulo_profesional"]}}</td>
                            <td style="font-size: 11px !important;">
                                @if ($medico["especialidad"]!= "")
                                @foreach(explode(',', $medico["especialidad"]) as $especialidad) 
                                {{$especialidad}}
                                @endforeach
                                @endif
                            </td>
                            {{-- @if($medico["celular"] != "")
                            <td>{{$medico["celular"]}}</td>
                            @else
                            <td>---</td>
                            @endif
                            @if($medico["correo"] != "")
                            <td>{{$medico["correo"]}}</td> 
                            @else
                            <td>---</td>
                            @endif --}}
                            <td> <a class="btn btn-sm btn-danger" onclick="deshabilitar({{$medico['id_medico']}});" class="cursor">Borrar</a>    <a class="btn btn-sm btn-primary" href="{{asset('administracion/editarMedico/'.$medico['id_medico'])}}" class="cursor">Editar</a> </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
		</div>
	</div>

</div>
@stop

