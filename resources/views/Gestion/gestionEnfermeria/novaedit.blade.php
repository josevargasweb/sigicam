<!-- <legend class="text-center"><u>Escala Nova</u></legend> -->

{{-- {{ HTML::link("gestionEnfermeria/$caso/historialNova", ' Ver Historial', ['class' => 'btn btn-default']) }} --}}


{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaNovaform')) }}
{{ Form::hidden ('caso', $caso, array('id' => 'caso') )}}
 <div class="formulario">
        <br>   
                <input type="hidden" value="" name="id_formulario_escala_nova" id="id_formulario_escala_nova">

                <div class="panel panel-default">
                        <div class="panel-heading panel-info">
                            <h4>Editar Escala Nova:</h4>
                        </div>
               
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{-- <div class="col-sm-10">
                                        <label for="estado_mental" class="control-label" title="Estado mental">Estado mental </label>
                                        {{Form::select('estado_mental', ['disabled' => 'Seleccionar','0' => 'Alerta','1' => 'Desorientado','2' => 'Letárgico','3' => 'Coma'], null, array('class' => 'form-control', 'id' => 'estado_mental'))}}
                                        @if ($errors->has('estado_mental'))
                                            <small class="form-text text-danger">{{ $errors->first('estado_mental') }}</small>
                                        @endif
                                    </div> --}}
                                    <div class="col-sm-10">
                                        <label for="estado_mental" class="control-label" title="Estado mental">Estado mental</label>
                                        <select class="form-control" id="estado_mental" name="estado_mental" required>
                                            <option value="" disabled selected>Seleccionar</option>
                                            <option value="0">Alerta</option>
                                            <option value="1">Desorientado</option>
                                            <option value="2">Letárgico</option>
                                            <option value="3">Coma</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{-- <div class="col-sm-10">
                                        <label for="incontinencia" class="control-label" title="Incontinencia">Incontinencia </label>
                                        {{Form::select('incontinencia', ['disabled' => 'Seleccionar','0' => 'No','1' => 'Ocasionalmente limitada','2' => 'Urinario o Fecal importante','3' => 'Urinario y Fecal'], null,array('class' => 'form-control', 'id' => 'incontinencia'))}}
                                        @if ($errors->has('incontinencia'))
                                            <small class="form-text text-danger">{{ $errors->first('incontinencia') }}</small>
                                        @endif
                                    </div> --}}
                                    <div class="col-sm-10">
                                        <label for="incontinencia" class="control-label" title="Incontinencia">Incontinencia</label>
                                        <select class="form-control" id="incontinencia" name="incontinencia" required>
                                            <option value="" disabled selected>Seleccionar</option>
                                            <option value="0">No</option>
                                            <option value="1">Ocasionalmente limitada</option>
                                            <option value="2">Urinario o Fecal importante</option>
                                            <option value="3">Urinario y Fecal</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{-- <div class="col-sm-10">
                                        <label for="movilidad" class="control-label" title="Movilidad">Movilidad </label>
                                        {{Form::select('movilidad', ['disabled' => 'Seleccionar','0' => 'Completa','1' => 'Ligeramente con ayuda','2' => 'Limitación','3' => 'Inmovil'], null,array('class' => 'form-control', 'id' => 'movilidad'))}}
                                        @if ($errors->has('movilidad'))
                                            <small class="form-text text-danger">{{ $errors->first('movilidad') }}</small>
                                        @endif
                                    </div> --}}
                                    <div class="col-sm-10">
                                        <label for="movilidad" class="control-label" title="Movilidad">Movilidad</label>
                                        <select class="form-control" id="movilidad" name="movilidad" required>
                                            <option value="" disabled selected>Seleccionar</option>
                                            <option value="0">Completa</option>
                                            <option value="1">Ligeramente con ayuda</option>
                                            <option value="2">Limitación</option>
                                            <option value="3">Inmovil</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{-- <div class="col-sm-10">
                                        <label for="nutricion_ingesta" class="control-label" title="Nutrición ingesta">Nutrición ingesta</label>
                                        {{Form::select('nutricion_ingesta', ['disabled' => 'Seleccionar','0' => 'Correcta','1' => 'Ocasionalmente con ayuda','2' => 'Incompleta siempre con ayuda','3' => 'No ingesta oral, ni enteral, ni parenteral superior a 72 hrs y/o desnutrición previa'], null,array('class' => 'form-control', 'id' => 'nutricion_ingesta'))}}
                                        @if ($errors->has('nutricion_ingesta'))
                                            <small class="form-text text-danger">{{ $errors->first('nutricion_ingesta') }}</small>
                                        @endif
                                    </div> --}}
                                    <div class="col-sm-10">
                                        <label for="nutricion_ingesta" class="control-label" title="Nutricion ingesta">Nutricion ingesta</label>
                                        <select class="form-control" id="nutricion_ingesta" name="nutricion_ingesta" required>
                                            <option value="" disabled selected>Seleccionar</option>
                                            <option value="0">Correcta</option>
                                            <option value="1">Ocasionalmente con ayuda</option>
                                            <option value="2">Incompleta siempre con ayuda</option>
                                            <option value="3">No ingesta oral, ni enteral, ni parenteral superior a 72 hrs y/o desnutrición previa</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{-- <div class="col-sm-10">
                                        <label for="actividad" class="control-label" title="Actividad">Actividad</label>
                                        {{Form::select('actividad', ['disabled' => 'Seleccionar','0' => 'Deambula','1' => 'Deambula con ayuda','2' => 'Deambula siempre precisa ayuda','3' => 'No deambula, encamado'], null,array('class' => 'form-control', 'id' => 'actividad'))}}
                                        @if ($errors->has('actividad'))
                                            <small class="form-text text-danger">{{ $errors->first('actividad') }}</small>
                                        @endif
                                    </div> --}}
                                    <div class="col-sm-10">
                                        <label for="actividad" class="control-label" title="Actividad">Actividad</label>
                                        <select class="form-control" id="actividad" name="actividad" required>
                                            <option value="" disabled selected>Seleccionar</option>
                                            <option value="0">Deambula</option>
                                            <option value="1">Deambula con ayuda</option>
                                            <option value="2">Deambula siempre precisa ayuda</option>
                                            <option value="3">No deambula, encamado</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <div class="col-sm-5">
                                        <label for="total" class="control-label" title="Total">Total:</label>
                                        <input type="number" name="total" id="total" class="form-control" readonly>
                                       
                                    </div>
                                    <div class="col-sm-5">
                                        <label for="total" class="control-label" title="Total">&nbsp </label>
                                        {{Form::text('puntos', null, array('disabled','id' => 'puntos', 'class' => 'form-control'))}}
                                       
                                    </div>
                                    
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    </div>

                    <!-- {{-- <div class="panel panel-default">
                        
                        <div class="panel-heading panel-info">
                            <h4>Categorias de riesgo</h4>
                        </div>
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <div class="col-sm-10">
                                        <label for="sin_riesgo" class="col-form-label">0 puntos, Sin riesgo</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="col-sm-10">
                                        <label for="sin_riesgo" class="col-form-label">De 1 a 4 puntos, Riesgo bajo</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="col-sm-10">
                                        <label for="sin_riesgo" class="col-form-label">De 5 a 8 puntos, Riesgo medio</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="col-sm-10">
                                        <label for="sin_riesgo" class="col-form-label">De 9 a 15 puntos, Riesgo alto</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}} -->
                    <input type="submit" name="" class="btn btn-primary" value="Editar Información">
                    {{ Form::close() }}
                    </div>

                    <script>

                        // var numero3, numero1, numero2, numero4, numero5;
                        // caja = document.forms["escalaNovaform"].elements;

                        // puntos = document.forms["escalaNovaform"].elements;
                        // mostrar();
                        // $("#estado_mental").change(function() {
                        // numero1 = parseInt(caja["estado_mental"].value);
                        // mostrar();
                        // });
                        // $("#incontinencia").change(function() {
                        // numero2 = parseInt(caja["incontinencia"].value);
                        // mostrar();
                        // });
                        // $("#movilidad").change(function() {
                        // numero3 = parseInt(caja["movilidad"].value);
                        // mostrar();
                        // });
                        // $("#nutricion_ingesta").change(function() {
                        // numero4 = parseInt(caja["nutricion_ingesta"].value);
                        // mostrar();
                        // });
                        // $("#actividad").change(function() {
                        // numero5 = parseInt(caja["actividad"].value);
                        // mostrar();
                        // });
                        // function mostrar() {
                        // if (numero1 >= 0 && numero2 >= 0 && numero3 >= 0 && numero4 >= 0 && numero5 >= 0) {
                        //     resultado = numero1 + numero2 + numero3 + numero4 + numero5;
                        //     if (resultado == 0) {
                        //         caja["puntos"].value = 'Sin Riesgo';
                        //     }else
                        //     if (resultado >= 1 && resultado <=4) {
                        //         caja["puntos"].value = 'Riesgo Bajo';
                        //     }else
                        //     if (resultado >= 5 && resultado <=8) {
                        //         caja["puntos"].value = 'Riesgo Medio';
                        //     }else
                        //     if (resultado >= 9 && resultado <=15) {
                        //         caja["puntos"].value = 'Riesgo Alto';
                        //     }
                        // }
                              
                        // }
                        

                                               
                        $("#escalaNovaform").bootstrapValidator({            
                                excluded: ':disabled',            
                                fields: { }        
                        }).on('status.field.bv', function(e, data) {            
                            $("#escalaNovaform input[type='submit']").prop("disabled", false);        
                        }).on("success.form.bv", function(evt){            
                            $("#escalaNovaform input[type='submit']").prop("disabled", false);            
                            evt.preventDefault(evt);            
                            var $form = $(evt.target);
                            bootbox.confirm({
                                message: "<h4>¿Está seguro de ingresar la información?</h4>",
                                buttons: {
                                    confirm: {
                                        label: 'Si',
                                        className: 'btn-primary'
                                    },
                                    cancel: {
                                        label: 'No',
                                        className: 'btn-light'
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
                                                data: $form .serialize(),					    
                                                success: function(data){					        
                                                    //$("#formEnviarDerivado").modal("hide");					        
                                                    //if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); });
                                                    console.log("editnova");
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

                    </script>

