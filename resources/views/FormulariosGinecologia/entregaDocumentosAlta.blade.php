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
            array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'entrega_documentos_alta_form')
        ) 
    }}

    <br>

    <!-- -->

    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Entrega de documentos al alta</h4>
        </div>

        <div class="panel-body">


            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="entrega_documentos_alta_run">RUN</label>
                        <input type="text" class="form-control" id="entrega_documentos_alta_run" disabled>                                           
                    </div>                
                
                </div>
                

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="entrega_documentos_alta_ficha_clinica">Ficha clinica</label>
                        <input type="text" class="form-control" id="entrega_documentos_alta_ficha_clinica" disabled>                                           
                    </div>                
                </div>

                @if (isset($formulario_data->form_id))
                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="entrega_documentos_alta_fecha">Fecha</label>
                        <input type="text" class="form-control" id="entrega_documentos_alta_fecha" disabled>                                           
                    </div>                
                </div>
                @endif
            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>Documentaci&oacute;n entregada</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_epicrisis_medica">Epicrisis m&eacute;dica</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_epicrisis_medica" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_epicrisis_medica" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_epicrisis_medica" value="n/c">
                        N/C
                    </label>               
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_carnet_alta">Carnet de alta</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carnet_alta" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carnet_alta" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carnet_alta" id="entrega_documentos_alta_carnet_alta" value="n/c">
                        N/C
                    </label>               
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_recetas_farmacos">Recetas de f&aacute;rmacos</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_recetas_farmacos" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_recetas_farmacos" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_recetas_farmacos" value="n/c">
                        N/C
                    </label>               
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_citaciones_control">Citaciones a control</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_citaciones_control" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_citaciones_control" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_citaciones_control" value="n/c">
                        N/C
                    </label>               
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_carne_identidad">Carn&eacute; de identidad</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carne_identidad" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carne_identidad" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carne_identidad" value="n/c">
                        N/C
                    </label>               
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_comprobante_parto">Comprobante de parto</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_comprobante_parto" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_comprobante_parto" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_comprobante_parto" value="n/c">
                        N/C
                    </label>               
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-sm-3">
                    <label class="radio-inline" for="entrega_documentos_alta_carne_control_parental">Carn&eacute; de control prenatal</label>
                </div>

                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carne_control_parental" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carne_control_parental" value="no">
                        No
                    </label>                                        
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_carne_control_parental" value="n/c">
                        N/C
                    </label>               
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <br>
                    <h5><strong>Egreso hospitalario acompa&ntilde;ado</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-12">
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_egreso_hospitalario_acompanado" value="si">
                        S&iacute;
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="entrega_documentos_alta_egreso_hospitalario_acompanado" value="no">
                        No
                    </label>                                        
              
                </div>
                
            </div>

            <div class="row" id="entrega_documentos_alta_acompanante_container" hidden>

                <div class="col-md-12">
                <br>
                    <div class="col-md-6 form-group">
                        <label for="entrega_documentos_alta_acompanante">¿Qui&eacute;n acompa&ntilde;a al paciente?</label>
                        <input type="text" class="form-control" id="entrega_documentos_alta_acompanante" name="entrega_documentos_alta_acompanante">                                           
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>Observaciones</strong></h5>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-6 form-group">
                        <textarea class="form-control" rows="5" name="entrega_documentos_alta_observaciones" id="entrega_documentos_alta_observaciones"></textarea>
                    </div>                
                </div>
                
            </div>


        </div>
    </div>




    {{ Form::close()}}   
    @if (!isset($formulario_data->form_id))
    <input id="entrega_documentos_alta_save" type="button" name="" class="btn btn-primary" value="Guardar">
    <input id="entrega_documentos_alta_save_pdf" type="button" class="btn btn-primary" value="Guardar e imprimir">
    @endif

    @if (isset($formulario_data->form_id))
    <input id="entrega_documentos_alta_save" type="button" name="" class="btn btn-primary" value="Modificar">
    <input id="entrega_documentos_alta_save_pdf" type="button" name="" class="btn btn-primary" value="Modificar e imprimir">
    <a id="entrega_documentos_alta_pdf" href="{{url('formularios-ginecologia/pdf/'.$formulario_data->caso_id)}}" target="_blank" class="btn btn-primary">Imprimir</a>
    @endif

    <script>

        const bv_options = {
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'entrega_documentos_alta_observaciones': {
                    validators:{
                        stringLength: {
                            max: 500,
                            message: 'Máximo 500 caracteres.'
                        },
                    }
                },
                'entrega_documentos_alta_epicrisis_medica': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_carnet_alta' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_recetas_farmacos' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_citaciones_control' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_carne_identidad' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_comprobante_parto': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_carne_control_parental': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'entrega_documentos_alta_egreso_hospitalario_acompanado': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    },
                },
                'entrega_documentos_alta_acompanante': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        },
                        stringLength: {
                            max: 200,
                            message: 'Máximo 200 caracteres.'
                        },
                    }
                },
            }
        };

        function load() {
            $("#entrega_documentos_alta_run").val("{{$formulario_data->run_dv}}");
            $("#entrega_documentos_alta_ficha_clinica").val("{{$formulario_data->ficha_clinica}}");

            @if (isset($formulario_data->form_id))
            $("#entrega_documentos_alta_fecha").val("{{$formulario_data->fecha_documento}}");

            jQuery('input:radio[name="entrega_documentos_alta_epicrisis_medica"]').filter('[value="{{$formulario_data->epicrisis_medica}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_carnet_alta"]').filter('[value="{{$formulario_data->carnet_alta}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_recetas_farmacos"]').filter('[value="{{$formulario_data->recetas_farmacos}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_citaciones_control"]').filter('[value="{{$formulario_data->citaciones_control}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_carne_identidad"]').filter('[value="{{$formulario_data->carne_identidad}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_comprobante_parto"]').filter('[value="{{$formulario_data->comprobante_parto}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_carne_control_parental"]').filter('[value="{{$formulario_data->carne_control_parental}}"]').prop('checked', true).trigger("change");

            jQuery('input:radio[name="entrega_documentos_alta_egreso_hospitalario_acompanado"]').filter('[value="{{$formulario_data->egreso_hospitalario_acompanado}}"]').prop('checked', true).trigger("change");

            $("#entrega_documentos_alta_acompanante").val({!! json_encode($formulario_data->quien_acompana_paciente) !!});
            $("#entrega_documentos_alta_observaciones").val({!! json_encode($formulario_data->observaciones) !!});
            @endif

        }

        $(document).ready(function() { 


            $(document).on('change', '[name="entrega_documentos_alta_egreso_hospitalario_acompanado"]', function(e) {
                $( "#entrega_documentos_alta_acompanante_container" ).hide();
                $( "#entrega_documentos_alta_acompanante" ).val("");
                if($(this).val() === 'si'){
                    $( "#entrega_documentos_alta_acompanante_container" ).show();

                }

            });

            $(document).on('click', '#entrega_documentos_alta_save,#entrega_documentos_alta_save_pdf', function() {

                $("#entrega_documentos_alta_form").bootstrapValidator(bv_options);
                var con_pdf = $(this).attr("id") == "entrega_documentos_alta_save_pdf";
                var bv = $("#entrega_documentos_alta_form").data('bootstrapValidator');
                bv.validate();

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
                                $("#entrega_documentos_alta_save,#entrega_documentos_alta_save_pdf").attr("disabled", true);

                                var form_data = $("#entrega_documentos_alta_form").serialize()+"&caso_id={{$formulario_data->caso_id}}&form_id={{$formulario_data->form_id}}";

                                $.ajax({
                                    url: "{{URL::route('entrega-documentos-alta-save')}}",
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
                									@if (!isset($formulario_data->form_id))
                                                    if(con_pdf)
                                                    {
                                                    	window.open("{{url('formularios-ginecologia/pdf/'.$formulario_data->caso_id)}}","imprimir","resizable,scrollbars,status");
                                                    }
                                                    location.reload();
                                                    @else
                                                    $("#entrega_documentos_alta_save,#entrega_documentos_alta_save_pdf").attr("disabled", false);
                                                    if(con_pdf)
                                                    {
                                                    	window.open("{{url('formularios-ginecologia/pdf/'.$formulario_data->caso_id)}}","imprimir","resizable,scrollbars,status");
                                                    }
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
                                        $("#entrega_documentos_alta_save,#entrega_documentos_alta_save_pdf").attr("disabled", false);

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