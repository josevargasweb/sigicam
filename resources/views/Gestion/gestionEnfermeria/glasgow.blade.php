@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("script")
    <script>

        $( document ).ready(function() {
        
            $("#formGlasgow").bootstrapValidator({
                excluded: [':disabled', ':hidden', ':not(:visible)'],
                fields: {    
                    apertura_ocular:{
                            validators:{
                            notEmpty: {
                                message: 'Campo obligatorio'
                            }
                        }
                    },
                    respuesta_verbal:{
                            validators:{
                            notEmpty: {
                                message: 'Campo obligatorio'
                            }
                        }
                    },
                    respuesta_motora:{
                            validators:{
                            notEmpty: {
                                message: 'Campo obligatorio'
                            }
                        }
                    }
                }         
                        
            }).on('status.field.bv', function(e, data) {
                //$("#formBuscarNombres input[type='submit']").prop("disabled", false);        
            }).on("success.form.bv", function(evt){
                
                evt.preventDefault(evt); 
                datos = $("#formGlasgow").serialize();
        
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
                        console.log('This was logged in the callback: ' + result);					
                        if(result){					
                            console.log("entra alajax?");					
                            $.ajax({
                                //url: "buscarNombres", 
                                url: '{{URL::to("gestionEnfermeria/guardarGlasgow")}}',
                                headers: { 
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "post",
                                dataType: "json",
                                data: $("#formGlasgow").serialize(),
                                success: function(data){
                                    //if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });
                                    if(data.exito){
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
                                     
                                    if(data.error) {
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
                                error: function(error){
                                    //console.log(error);
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
<li><a href="#">Formulario Escala Glasgow</a></li>
@stop

@section("section")
<br>
<a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>
<br>

    <legend id="legendglasgow" class="text-center"><u>Escala de Glasgow</u></legend>
    {{ HTML::link("gestionEnfermeria/$caso/indexGlasgow", 'Ver Historial', ['class' => 'btn btn-default', 'id' => 'btnhistorialglasgow']) }}
    <br>
    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'formGlasgow', 'autocomplete' => 'off')) }}

    <input type="hidden" value="{{$caso}}" name="caso">
    <input type="hidden" value="" name="id_formulario_escala_glasgow" id="id_formulario_escala_glasgow">
    <input type="hidden" value="En Curso" name="tipoFormGlasgow" id="tipoFormGlasgow">
    <br>
    @include('Gestion.gestionEnfermeria.partials.Formglasgow')   
    {{ Form::close() }}
@stop

       
