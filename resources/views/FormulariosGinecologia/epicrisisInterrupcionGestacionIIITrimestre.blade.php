@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("section")

    <script>
        var caso_id = '{{$formulario_data->caso_id}}';
    </script>
    <!-- alertas de alergias logica-->
    {{ HTML::script('js/formularios_ginecologia/partograma-alergias-notificator.js') }}


    <style>
        .help-block {
            color: #a94442;
        }
    </style>

    <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
    <br><br>

    {{ 
        Form::open(
            array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'epicrisis_interrupcion_gestacion_iii_trimestre_form')
        ) 
    }}

    <br>

    <!-- -->

    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Epicrisis para interrupci&oacute;n de gestaci&oacute;n III trimestre</h4>
        </div>

        <div class="panel-body">


            <div class="row">

                <div class="col-md-12">
                    <h5><strong>1. Antecedentes generales</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_paciente_edad">Edad (A&ntilde;os)</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_paciente_edad" disabled>                                          
                    </div>                
                
                </div>
                
            </div>


            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_p">P</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_p" name="epicrisis_interrupcion_gestacion_iii_trimestre_p">                                           
                    </div>                
                
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_v">V</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_v" name="epicrisis_interrupcion_gestacion_iii_trimestre_v">                                           
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-6">
                
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional">Edad gestacional en semanas</label>
                        <input type="number" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional" name="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional" min="27" max="42">
                    </div>
                
                </div>
                <div class="col-md-6">
                
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias">Edad gestacional en días</label>
                        <input type="number" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias" name="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias" min="0" max="6">
                    </div>
                
                </div>
				<div class="col-md-12">
                
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones">Observaciones</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones" name="epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones">
                    </div>
                
                </div>
            </div>


            <div class="row">

                <div class="col-md-12">
                    <h5><strong>2. Diagn&oacute;stico de patolog&iacute;a agregada</strong></h5>
                </div>
            </div>


            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1">2.1-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1" name="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1">                        
                        
                        <div class="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar_container">
                        <br>
                        <button type="button" class="btn btn-primary" id="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar"  data_patologia_agregada_group_id="2">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button> 
                        </div>                                                        
                    </div>  


                    <div class="col-md-12 form-group data_patologia_agregada_group_2"  hidden>
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2">2.2-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2" name="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2">                                           
                    </div>  

                    <div class="col-md-12 form-group data_patologia_agregada_group_3" hidden>
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3">2.3-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3" name="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3">                                           
                    </div>  
                    
                    <div class="col-md-12 form-group data_patologia_agregada_group_4" hidden>
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4">2.4-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4" name="epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4">                                          
                    </div>                      

                </div>


            </div>           

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>3. Condiciones obst&eacute;tricas</strong></h5>
                </div>
            </div>


            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="entrega_documentos_alta_alt_ut">Alt. Ut</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut" name="epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut">                                           
                    </div>                
                
                </div>
                

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="entrega_documentos_alta_ficha_clinica">Cons</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_cons" name="epicrisis_interrupcion_gestacion_iii_trimestre_cons">                                           
                    </div>                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="entrega_documentos_alta_fecha">Presentaci&oacute;n</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_presentacion" name="epicrisis_interrupcion_gestacion_iii_trimestre_presentacion">                                           
                    </div>                
                </div>

            </div>


            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal">Tacto vaginal</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal" name="epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal">                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion">Plano de la presentaci&oacute;n</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion" name="epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion">
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion">Cuello posici&oacute;n</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion" name="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion">
                    </div>                
                </div>
                
                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist">Cuello consist</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist" name="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist">                                       
                    </div>                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento">Cuello borramiento</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento" name="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento">
                    </div>                
                </div>

            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion">Dilat</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_dilat" name="epicrisis_interrupcion_gestacion_iii_trimestre_dilat">
                    </div>                
                </div>
                
                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_bishop">Bishop</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_bishop" name="epicrisis_interrupcion_gestacion_iii_trimestre_bishop">                                       
                    </div>                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_puntos">Puntos</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_puntos" name="epicrisis_interrupcion_gestacion_iii_trimestre_puntos">
                    </div>                
                </div>

            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria">Pelvimetr&iacute;a</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria" name="epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria">
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_cd">CD</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_cd" name="epicrisis_interrupcion_gestacion_iii_trimestre_cd">
                    </div>                
                </div>
                
                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_cv">CV</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_cv" name="epicrisis_interrupcion_gestacion_iii_trimestre_cv">                                       
                    </div>                
                </div>

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_ec">EC</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_ec" name="epicrisis_interrupcion_gestacion_iii_trimestre_ec">
                    </div>                
                </div>

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_rn">RN</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_rn" name="epicrisis_interrupcion_gestacion_iii_trimestre_rn">
                    </div>                
                </div>

            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_tipo">Tipo</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_tipo" name="epicrisis_interrupcion_gestacion_iii_trimestre_tipo">
                    </div>                
                </div>
                
                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal">Proporci&oacute;n pelvis fetal</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal" name="epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal">                                       
                    </div>                
                </div>

            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>4. Estado unidad feto placentario</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_normal">4.1- Normal</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_normal" name="epicrisis_interrupcion_gestacion_iii_trimestre_normal">
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_alterado">4.2- Alterado (XINT-TTO-E)</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_alterado" name="epicrisis_interrupcion_gestacion_iii_trimestre_alterado">
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal">4.3- Peso estimado fetal (gramos)</label>
                        <input type="number" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal" name="epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal"
                        min="0" max="7000"
                        >
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>5. Indicaci&oacute;n de la interrupci&oacute;n</strong></h5>
                </div>
            </div>


            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1">5.1-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1" name="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1">  

                        <div class="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar_container"> 
                        <br>
                        <button type="button" class="btn btn-primary" id="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar"  data_indicacion_interrupcion_group_id="2">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button> 
                        </div>

                    </div>  


                    <div class="col-md-12 form-group data_indicacion_interrupcion_group_2" hidden>
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2">5.2-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2" name="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2">                                           
                    </div>  

                    <div class="col-md-12 form-group data_indicacion_interrupcion_group_3" hidden>
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3">5.3-</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3" name="epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3">                                           
                    </div>                      

                </div>

            </div> 

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>6. V&iacute;a solicitada</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" id="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" value="induccion" >
                        <label  class="form-check-label" for="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" style="font-weight: 100;">
                            Inducci&oacute;n
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" id="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" value="induccion_monitorizada" >
                        <label class="form-check-label" for="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" style="font-weight: 100;">
                            Inducci&oacute;n  monitorizada
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" id="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" value="cesarea" >
                        <label class="form-check-label" for="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada" style="font-weight: 100;">
                            Ces&aacute;rea
                        </label>
                    </div>

                </div>
            
            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>7. Fecha intervenci&oacute;n</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion">Fecha</label>
                        <input type="text" class="form-control dp" id="epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion" name="epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion">
                    </div>                
                </div>
                
                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion">Hora</label>
                        <input type="text" class="form-control" id="epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion" name="epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion" placeholder="HH:mm">                                       
                    </div>                
                </div>

            </div>


        </div>
    </div>




    {{ Form::close()}}   
    @if (!isset($formulario_data->form_id))
    <input id="epicrisis_interrupcion_gestacion_iii_trimestre_save" type="button" name="" class="btn btn-primary" value="Guardar">
    <input id="epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf" type="button" class="btn btn-primary" value="Guardar e imprimir">
    @endif

    @if (isset($formulario_data->form_id))
    <input id="epicrisis_interrupcion_gestacion_iii_trimestre_save" type="button" name="" class="btn btn-primary" value="Modificar">
    <input id="epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf" type="button" name="" class="btn btn-primary" value="Modificar e imprimir">
    <a id="epicrisis_interrupcion_gestacion_iii_trimestre_pdf" href="{{url('formularios-ginecologia/epicrisis-interrupcion-gestacion-iii-trimestre/pdf/'.$formulario_data->caso_id)}}" target="_blank" class="btn btn-primary">Imprimir</a>
    @endif

    {!! HTML::script('js/formularios_ginecologia/helper.js') !!}
    <script>

        const bv_options = {
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional': {
                    validators:{
                        between: {
                            min: 0,
                            max: 42,
                            message: 'El rango permitido es entre 27 y 42'
                        }
                    }
                },
				'epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias': {
                    validators:{
                        between: {
                            min: 1,
                            max: 6,
                            message: 'El rango permitido es entre 0 y 6'
                        }
                    }
                },
                'epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal': {
                    validators:{
                        between: {
                            min: 0,
                            max: 7000,
                            message: 'El rango permitido es entre 0 y 7000'
                        }
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        },
                        _not_future_date: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_p': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_v': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_cons': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_presentacion': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_dilat': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_bishop': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_puntos': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_cd': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_cv': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_ec': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_rn': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_tipo': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_normal': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_alterado': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

                'epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },

            }
        };

        $('#epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion').datetimepicker({
            format: 'HH:mm'
        }).on("dp.change", function () {
            initFormValidation ($("#epicrisis_interrupcion_gestacion_iii_trimestre_form"));
        });

        function load() {
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_paciente_edad").val("{{$formulario_data->paciente_edad}}");

            @if (isset($formulario_data->form_id))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_p").val({!! json_encode($formulario_data->p) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_v").val({!! json_encode($formulario_data->v) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional").val("{{$formulario_data->edad_gestacional}}");
			$("#epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_dias").val("{{$formulario_data->edad_gestacional_dias}}");
			$("#epicrisis_interrupcion_gestacion_iii_trimestre_edad_gestacional_observaciones").val("{{$formulario_data->edad_gestacional_observacion}}");
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_1").val({!! json_encode($formulario_data->diagnostico_patologia_1) !!});

            @if (isset($formulario_data->diagnostico_patologia_2))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_2").val({!! json_encode($formulario_data->diagnostico_patologia_2) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar").trigger("click");
            @endif

            @if (isset($formulario_data->diagnostico_patologia_3))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_3").val({!! json_encode($formulario_data->diagnostico_patologia_3) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar").trigger("click");
            @endif

            @if (isset($formulario_data->diagnostico_patologia_4))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregada_4").val({!! json_encode($formulario_data->diagnostico_patologia_4) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar").trigger("click");
            @endif

            $("#epicrisis_interrupcion_gestacion_iii_trimestre_alt_ut").val({!! json_encode($formulario_data->alt_ut) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_cons").val({!! json_encode($formulario_data->cons) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_presentacion").val({!! json_encode($formulario_data->presentacion) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_tacto_vaginal").val({!! json_encode($formulario_data->tacto_vaginal) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_plano_presentacion").val({!! json_encode($formulario_data->plano_presentacion) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_cuello_posicion").val({!! json_encode($formulario_data->cuello_posicion) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_cuello_consist").val({!! json_encode($formulario_data->cuello_consist) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_cuello_borramiento").val({!! json_encode($formulario_data->cuello_borramiento) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_dilat").val({!! json_encode($formulario_data->dilat) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_bishop").val({!! json_encode($formulario_data->bishop) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_puntos").val({!! json_encode($formulario_data->puntos) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_pelvimetria").val({!! json_encode($formulario_data->pelvimetria) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_cd").val({!! json_encode($formulario_data->cd) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_cv").val({!! json_encode($formulario_data->cv) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_ec").val({!! json_encode($formulario_data->ec) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_rn").val({!! json_encode($formulario_data->rn) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_tipo").val({!! json_encode($formulario_data->tipo) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_pelvis_fetal").val({!! json_encode($formulario_data->proporcion_pelvis_fetal) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_normal").val({!! json_encode($formulario_data->normal) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_alterado").val({!! json_encode($formulario_data->alterado) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_peso_estimado_fetal").val("{{$formulario_data->peso_estimado_fetal}}");
            
            @if (isset($formulario_data->indicacion_1))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_1").val({!! json_encode($formulario_data->indicacion_1) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar").trigger("click");
            @endif

            @if (isset($formulario_data->indicacion_2))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_2").val({!! json_encode($formulario_data->indicacion_2) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar").trigger("click");
            @endif

            @if (isset($formulario_data->indicacion_3))
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_3").val({!! json_encode($formulario_data->indicacion_3) !!});
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar").trigger("click");
            @endif

            jQuery('input:radio[name="epicrisis_interrupcion_gestacion_iii_trimestre_via_solicitada"]').filter('[value="{{$formulario_data->via_solicitada}}"]').prop('checked', true).trigger("change");            
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion").data("DateTimePicker").date("{{$formulario_data->fecha_intervencion}}");
            $("#epicrisis_interrupcion_gestacion_iii_trimestre_hora_intervencion").val("{{$formulario_data->hora_intervencion}}");
            initFormValidation ($("#epicrisis_interrupcion_gestacion_iii_trimestre_form"));
            @endif

        }

        $(document).ready(function() { 

            $('#epicrisis_interrupcion_gestacion_iii_trimestre_fecha_intervencion').datetimepicker({
                format: 'DD/MM/YYYY',

            }).on("dp.change", function () {
                initFormValidation ($("#epicrisis_interrupcion_gestacion_iii_trimestre_form"));
            });

            

            $(document).on('click', '#epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar', function() {
                var group_id = parseInt($(this).attr("data_patologia_agregada_group_id"));
                $(".data_patologia_agregada_group_"+group_id).show();

                if(group_id < 4){
                    group_id = group_id + 1;
                    $(this).attr("data_patologia_agregada_group_id", group_id);
                } 
                else {
                    $(".epicrisis_interrupcion_gestacion_iii_trimestre_diagnostico_patologia_agregar_container").hide();
                }

            });

            $(document).on('click', '#epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar', function() {
                var group_id = parseInt($(this).attr("data_indicacion_interrupcion_group_id"));
                $(".data_indicacion_interrupcion_group_"+group_id).show();

                if(group_id < 3){
                    group_id = group_id + 1;
                    $(this).attr("data_indicacion_interrupcion_group_id", group_id);
                } 
                else {
                    $(".epicrisis_interrupcion_gestacion_iii_trimestre_indicacion_interrupcion_agregar_container").hide();
                }

            });


            $(document).on('click', '#epicrisis_interrupcion_gestacion_iii_trimestre_save,#epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf', function() {

                var con_pdf = $(this).attr("id") == "epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf";
                var bv = initFormValidation ($("#epicrisis_interrupcion_gestacion_iii_trimestre_form"));

                if(bv.isValid()){
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
                                $("#epicrisis_interrupcion_gestacion_iii_trimestre_save,#epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf").attr("disabled", true);

                                var form_data = $("#epicrisis_interrupcion_gestacion_iii_trimestre_form").serialize()+"&caso_id={{$formulario_data->caso_id}}&form_id={{$formulario_data->form_id}}";

                                $.ajax({
                                    url: "{{URL::route('epicrisis-interrupcion-gestacion-iii-trimestre-save')}}",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:  form_data,
                                    dataType: "json",
                                    type: "post",
                                    success: function(data){
                                    	swalExito.fire({
                							title: 'Exito!',
                							text: data.status,
                							didOpen: function() {
                								setTimeout(function() {
                									if(con_pdf)
                                                    {                                               
                                                    	window.open("{{url('formularios-ginecologia/epicrisis-interrupcion-gestacion-iii-trimestre/pdf/'.$formulario_data->caso_id)}}","imprimir","resizable,scrollbars,status");   
                                                    } 
                                                    @if (!isset($formulario_data->form_id))
                                                    location.reload();
                                                    @else
                                                    $("#epicrisis_interrupcion_gestacion_iii_trimestre_save,#epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf").attr("disabled", false);
                                                    @endif
                								}, 2000);
                							},
										});
                                    },
                                    error: function(request, status, error){
                                        

                                    	try {
                                            var json_res = JSON.parse(request.responseText);
                                            swalError.fire({
                        						title: 'Error',
                        						text:json_res.status
                        					});
                                        } 
                                        
                                        catch (error) {
                                        	swalError.fire({
                        						title: 'Error',
                        						text:"Ha ocurrido un error"
                        					});
                                        }
                                        $("#epicrisis_interrupcion_gestacion_iii_trimestre_save,#epicrisis_interrupcion_gestacion_iii_trimestre_save_pdf").attr("disabled", false);

                                    }
                                });
                            }
                        }            
                    });               

                }                

            });

            //al final siempre
            load();
        });

    </script>

@stop