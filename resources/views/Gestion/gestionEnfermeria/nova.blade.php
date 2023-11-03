@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("script")
<script>
$(document).ready(function(){
                       
    $("#escalaNovaform").bootstrapValidator({            
        excluded: ':disabled',            
        fields: { 
            estado_mental:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            incontinencia:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            movilidad:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            nutricion_ingesta:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            },
            actividad:{
                validators:{
                    notEmpty: {
                        message: 'Campo obilgatorio'
                    }
                }
            }
        }        
    }).on('status.field.bv', function(e, data) {            
        $("#escalaNovaform input[type='submit']").prop("disabled", false);        
    }).on("success.form.bv", function(evt){            
        $("#escalaNovaform input[type='submit']").prop("disabled", false);            
        evt.preventDefault(evt);            
        var $form = $(evt.target);
        datos = $("#escalaNovaform").serialize();
        bootbox.confirm({
            message: "<h4>¿Está seguro de ingresar la información?</h4>",
            buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                console.log('This was logged in the callback: ' + result);
                if(result){					
                            console.log("entra alajax?");					
                            $.ajax({					    
                            //url: "escalaNovaform",	
                            url: "{{URL::to('/gestionEnfermeria')}}/store",
                            //url: '{{URL::to("escalaNovaform")}}', 				    
                            headers: {					         
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                            },					    
                            type: "post",					    
                            dataType: "json",					    
                            data: $("#escalaNovaform").serialize(), //$form .serialize(),					    
                            success: function(data){					        
                                //$("#formEnviarDerivado").modal("hide");					        
                                //if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });		
                                console.log("nova");			        
                                if(data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
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
                                } 		
                                if(data.info) {
                                    swalInfo2.fire({
                                        title: 'Información',
                                        text: data.info
                                    }).then(function(result) {
                                        location . reload();
                                    });
                                }
                                    			    
                            },					    
                            error: function(error){					        
                                console.log(error);					    
                            }					
                            });					
                        }
            }
        });
        
        
    //no borrar        
    });



});

</script>
@stop

@section("miga")
    <li><a href="#">Formulario Nova</a></li>
@stop

@section("section")
    <br>
    <a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>
    <br>

    <legend id="legendNova" class="text-center"><u>Escala Nova</u></legend>

    {{ HTML::link("gestionEnfermeria/$caso/historialNova", ' Ver Historial', ['class' => 'btn btn-default', 'id' => 'volver']) }}


    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaNovaform')) }}
    {{ Form::hidden ('caso', $caso, array('id' => 'caso') )}}
    <br>   
    <input type="hidden" value="" name="id_formulario_escala_nova" id="id_formulario_escala_nova">
    <input type="hidden" value="En Curso" name="tipoFormNova" id="tipoFormNova">
    <br>
    @include('Gestion.gestionEnfermeria.partials.FormNova')
    {{ Form::close() }}

</div>
@stop
