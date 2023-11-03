@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("script")

<script>
    $("#fecha").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy HH:MM"});
    </script>
    
    <script>
        $( document ).ready(function() {
            $("#ingresoPacientePostradoform").bootstrapValidator({            
                        excluded: ':disabled',            
                        fields: {				
                        fecha: {					
                            validators:{					
                                notEmpty: {					   
                                     message: 'La fecha es obligatoria'					
                                    },
                                    date: {
                                        format: 'DD-MM-YYYY h:m',
                                        message: 'Ingrese la fecha en el formato correcto'
                                    }				
                                }				
                            },
                        sitio: {					
                            validators:{					
                                notEmpty: {					   
                                     message: 'El sitio es obligatorio'					
                                    }					
                                }				
                            },
                    }        
                }).on('status.field.bv', function(e, data) {            
                    $("#ingresoPacientePostradoform input[type='submit']").prop("disabled", false);        
                }).on("success.form.bv", function(evt){            
                    $("#ingresoPacientePostradoform input[type='submit']").prop("disabled", false);            
                    evt.preventDefault(evt);            
                    var $form = $(evt.target);            
                    bootbox.confirm({				
                        message: "<h4>¿Está seguro de ingresar la información?</h4>",				
                        buttons: {					
                            confirm: {					
                                label: 'Si',					
                                className: 'btn-success'					
                            },					
                            cancel: {					
                                label: 'No',					
                                className: 'btn-danger'					
                            }				
                        },				
                        callback: function (result) {				
                            if(result){						
                                $.ajax({					   	
                                url: "{{URL::to('/gestionEnfermeria')}}/ingresoPacientePostrado",			    
                                headers: {					         
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                                },					    
                                type: "post",					    
                                dataType: "json",					    
                                data: $form .serialize(),					    
                                success: function(data){					        					        
                                    if(data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    didOpen: function() {
                                        setTimeout(function() {
                                    $form[0] . reset();
                                    location . reload();
                                        }, 2000)
                                    },
                                    });
                                        
                                    }
                                    if(data.error) swalError.fire({
                                                    title: 'Error',
                                                    text:data.error
                                                    });					    
                                },					    
                                error: function(error){					        
                                    console.log(error);					    
                                }					
                                });					
                            }				
                        }            
                    });        
                });
        });
    </script>
@stop

@section("miga")
<li><a href="#">Formulario Paciente Dismovilizado</a></li>
@stop

@section("section")
    <br>
    <a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>
    <br>

    <legend class="text-center" id="legendPostrado"><u>Ingreso Paciente Dismovilizado</u></legend>

    {{ HTML::link("gestionEnfermeria/$caso/historialPacientePostrado", ' Ver Historial', ['class' => 'btn btn-default', "id" => "volver"]) }}
    <br>
    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'ingresoPacientePostradoform', 'autocomplete' => 'on')) }}
    {{ Form::hidden('idCaso', $caso, array('id' => 'idCaso')) }}
    <input type="hidden" value="En Curso" name="tipoFormPacientePostrado" id="tipoFormPacientePostrado">
    <input type="hidden" value="" name="id_formulario_paciente_postrado" id="id_formulario_paciente_postrado">

    @include('Gestion.gestionEnfermeria.partials.FormPacientePostrado')
    {{ Form::close() }}
@stop

