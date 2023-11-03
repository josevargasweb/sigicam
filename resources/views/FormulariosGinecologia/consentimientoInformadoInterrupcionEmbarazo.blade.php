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
            array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'consentimiento_informado_interrupcion_embarazo_form')
        ) 
    }}

    <br>

    <!-- -->

    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>
            Consentimiento informado para interrupci&oacute;n voluntaria del embarazo para mujeres adultas adolescentes entre 14 y 18 a&ntilde;os y mujeres con discapacidad no declaradas interdictas
            </h4>
        </div>

        <div class="panel-body">

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>Procedimiento</strong> (Marcar el o los procedimiento(s) que corresponda(n))</h5>
                    <br>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <span>
                            <strong>Medicamentoso</strong> (incluida v&iacute;a de administraci&oacute;n)
                         </span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="mifepristona" id="consentimiento_informado_interrupcion_embarazo_mifepristona" name="consentimiento_informado_interrupcion_embarazo_medicantoso[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_mifepristona" style="font-weight: 100;">
                                Mifepristona
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="misoprostol" id="consentimiento_informado_interrupcion_embarazo_misoprostol" name="consentimiento_informado_interrupcion_embarazo_medicantoso[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_misoprostol" style="font-weight: 100;">
                                Misoprostol
                            </label>
                        </div>

                    </div>                
                
                </div>
                

                <div class="col-md-6">
                
                    <div class="col-md-12 form-group">
                        <span>
                            <strong>Instrumental</strong>
                         </span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="aspiracion_endouterina" id="consentimiento_informado_interrupcion_embarazo_aspiracion_endouterina" name="consentimiento_informado_interrupcion_embarazo_instrumental[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_aspiracion_endouterina" style="font-weight: 100;">
                                Aspiraci&oacute;n endouterina (manual o el&eacute;ctrica)
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="legrado_uterino" id="consentimiento_informado_interrupcion_embarazo_legrado_uterino" name="consentimiento_informado_interrupcion_embarazo_instrumental[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_legrado_uterino" style="font-weight: 100;">
                                Legrado uterino
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="dilatacion_evacuacion_uterina" id="consentimiento_informado_interrupcion_embarazo_dilatacion_evacuacion_uterina" name="consentimiento_informado_interrupcion_embarazo_instrumental[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_dilatacion_evacuacion_uterina" style="font-weight: 100;">
                                Dilataci&oacute;n y evacuaci&oacute;n uterina
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="induccion_parto_prematuro" id="consentimiento_informado_interrupcion_embarazo_induccion_parto_prematuro" name="consentimiento_informado_interrupcion_embarazo_instrumental[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_induccion_parto_prematuro" style="font-weight: 100;">
                                Inducci&oacute;n de parto prematuro
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="cesarea" id="consentimiento_informado_interrupcion_embarazo_cesarea" name="consentimiento_informado_interrupcion_embarazo_instrumental[]">
                            <label class="form-check-label" for="consentimiento_informado_interrupcion_embarazo_cesarea" style="font-weight: 100;">
                                Ces&aacute;rea
                            </label>
                        </div>

                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>Informaci&oacute;n</strong> (Una vez de alta)</h5>
                    <br>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-6 form-group">
                        <label for="consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar">Consultar inmediatamente en caso de presentar:</label>
                        <textarea class="form-control" rows="5" name="consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar" id="consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar"></textarea>
                    </div>                
                </div>
                
            </div>


            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-6 form-group">
                        <label for="consentimiento_informado_interrupcion_embarazo_controlada_en">Ser&aacute; controlada en:</label>
                        <input type="text" class="form-control" id="consentimiento_informado_interrupcion_embarazo_controlada_en" name="consentimiento_informado_interrupcion_embarazo_controlada_en">                                           
                    </div>                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-6 form-group">
                        <label for="consentimiento_informado_interrupcion_embarazo_consultas_contacto">Dudas o consultas, contactar a (indicar Nombre o Cargo de la Persona, Tel&eacute;fono u otra forma de contacto):</label>
                        <textarea class="form-control" rows="5" name="consentimiento_informado_interrupcion_embarazo_consultas_contacto" id="consentimiento_informado_interrupcion_embarazo_consultas_contacto"></textarea>
                    </div>                
                </div>
                
            </div>


        </div>
    </div>




    {{ Form::close()}}   
    @if (!isset($formulario_data->form_id))
    <input id="consentimiento_informado_interrupcion_embarazo_save" type="button" name="" class="btn btn-primary" value="Guardar">
    <input id="consentimiento_informado_interrupcion_embarazo_save_pdf" type="button" name="" class="btn btn-primary" value="Guardar e imprimir">
    @endif

    @if (isset($formulario_data->form_id))
    <input id="consentimiento_informado_interrupcion_embarazo_save" type="button" name="" class="btn btn-primary" value="Modificar">
    <input id="consentimiento_informado_interrupcion_embarazo_save_pdf" type="button" name="" class="btn btn-primary" value="Modificar e imprimir">
    <a id="consentimiento_informado_interrupcion_embarazo_pdf" href="{{url('formularios-ginecologia/consentimiento-informado-interrupcion-embarazo/pdf/'.$formulario_data->caso_id)}}" target="_blank" class="btn btn-primary">Imprimir</a>
    @endif


    <script>

        const bv_options = {
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {

                'consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar': {
                    validators:{
                        stringLength: {
                            max: 500,
                            message: 'Máximo 500 caracteres.'
                        },
                    }
                },

                'consentimiento_informado_interrupcion_embarazo_controlada_en': {
                    validators:{
                        stringLength: {
                            max: 500,
                            message: 'Máximo 500 caracteres.'
                        },
                    }
                },

                'consentimiento_informado_interrupcion_embarazo_consultas_contacto': {
                    validators:{
                        stringLength: {
                            max: 500,
                            message: 'Máximo 500 caracteres.'
                        },
                    }
                },

                'consentimiento_informado_interrupcion_embarazo_medicantoso[]' : {
                    validators:{
                        callback: {
                            message: 'Seleccione al menos un medicamento',
                            callback: function(value, validator) {
                               return ($('input[name="consentimiento_informado_interrupcion_embarazo_medicantoso[]"]:checked').length<1) ? false : true ;
                            }
                        }
                    }
                },
                'consentimiento_informado_interrupcion_embarazo_instrumental[]' : {
                    validators:{
                        callback: {
                            message: 'Seleccione al menos un instrumento',
                            callback: function(value, validator) {
                               return ($('input[name="consentimiento_informado_interrupcion_embarazo_instrumental[]"]:checked').length<1) ? false : true ;
                            }
                        }
                    }
                },

            }
        };

        function load() {

            $("#consentimiento_informado_interrupcion_embarazo_ficha_clinica").val("{{$formulario_data->ficha_clinica}}");

            @if (isset($formulario_data->form_id))

            @if ($formulario_data->mifepristona === "si")
            $('#consentimiento_informado_interrupcion_embarazo_mifepristona').prop('checked', true);
            @endif

            @if ($formulario_data->misoprostol === "si")
            $('#consentimiento_informado_interrupcion_embarazo_misoprostol').prop('checked', true);
            @endif

            @if ($formulario_data->aspiracion_endouterina === "si")
            $('#consentimiento_informado_interrupcion_embarazo_aspiracion_endouterina').prop('checked', true);
            @endif

            @if ($formulario_data->legrado_uterino === "si")
            $('#consentimiento_informado_interrupcion_embarazo_legrado_uterino').prop('checked', true);
            @endif

            @if ($formulario_data->dilatacion_evacuacion_uterina === "si")
            $('#consentimiento_informado_interrupcion_embarazo_dilatacion_evacuacion_uterina').prop('checked', true);
            @endif

            @if ($formulario_data->induccion_parto === "si")
            $('#consentimiento_informado_interrupcion_embarazo_induccion_parto_prematuro').prop('checked', true);
            @endif

            @if ($formulario_data->cesarea === "si")
            $('#consentimiento_informado_interrupcion_embarazo_cesarea').prop('checked', true);
            @endif

            $("#consentimiento_informado_interrupcion_embarazo_consultar_caso_presentar").val({!! json_encode($formulario_data->consultas) !!});

            $("#consentimiento_informado_interrupcion_embarazo_controlada_en").val({!! json_encode($formulario_data->controles) !!});

            $("#consentimiento_informado_interrupcion_embarazo_consultas_contacto").val({!! json_encode($formulario_data->dudas) !!});

            @endif



        }

        $(document).ready(function() { 

            $(document).on('click', '#consentimiento_informado_interrupcion_embarazo_save,#consentimiento_informado_interrupcion_embarazo_save_pdf', function() {
            	var con_pdf = $(this).attr("id") == "consentimiento_informado_interrupcion_embarazo_save_pdf";
                $("#consentimiento_informado_interrupcion_embarazo_form").bootstrapValidator(bv_options);
                var bv = $("#consentimiento_informado_interrupcion_embarazo_form").data('bootstrapValidator');
                bv.validate();

                //if(true){
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
                                $("#consentimiento_informado_interrupcion_embarazo_save,#consentimiento_informado_interrupcion_embarazo_save_pdf").attr("disabled", true);
                                var form_data = $("#consentimiento_informado_interrupcion_embarazo_form").serialize()+"&caso_id={{$formulario_data->caso_id}}&form_id={{$formulario_data->form_id}}";

                                $.ajax({
                                    url: "{{URL::route('consentimiento-informado-interrupcion-embarazo-save')}}",
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
                                                    	window.open("{{url('formularios-ginecologia/consentimiento-informado-interrupcion-embarazo/pdf/'.$formulario_data->caso_id)}}","imprimir","resizable,scrollbars,status");
                                                    }
                                                    @if (!isset($formulario_data->form_id))
                                                    location.reload();
                                                    @else
                                                    $("#consentimiento_informado_interrupcion_embarazo_save,#consentimiento_informado_interrupcion_embarazo_save_pdf").attr("disabled", false);
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
                                        $("#consentimiento_informado_interrupcion_embarazo_save,#consentimiento_informado_interrupcion_embarazo_save_pdf").attr("disabled", false);

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