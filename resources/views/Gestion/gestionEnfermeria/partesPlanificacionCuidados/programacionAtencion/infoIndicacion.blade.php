<script>

    function isValidDate(d) {
        return d instanceof Date && !isNaN(d);
    }

    function load_modificacion_indicacion(){

        var horarios = "{{$indciacionInfo[0]->horario}}";
        var arr_horarios = horarios.split(',');
        var tipo = "{{$indciacionInfo[0]->tipo}}";
        $(".tipo-modificacion-indicacion-selectpicker").selectpicker('val', tipo).trigger("change");

        if(tipo === "Medicamento"){
            var medicamento_descripcion_modificacion_indicacion = "{{$indciacionInfo[0]->medicamento}}";
            var dosis_medicamento_modificacion_indicacion = "{{$indciacionInfo[0]->dosis}}";
            var via_medicamento_modificacion_indicacion = "{{$indciacionInfo[0]->via}}";
            var fecha_emision_medicamento_modificacion_indicacion = "{{\Carbon\Carbon::parse($indciacionInfo[0]->fecha_emision)->format('d-m-Y H:i:s')}}";
            var fecha_vigencia_medicamento_modificacion_indicacion = "{{\Carbon\Carbon::parse($indciacionInfo[0]->fecha_vigencia)->format('d-m-Y H:i:s')}}";
            var responsable_modificacion_medicamento = "{{$indciacionInfo[0]->responsable}}";
            var fecha_creacion_medicamento = "{{$indciacionInfo[0]->fecha_creacion}}";
            var creacion_medicamento = moment(fecha_creacion_medicamento).format("DD-MM-YYYY");

            $("#medicamento_descripcion_modificacion_indicacion").val(medicamento_descripcion_modificacion_indicacion);
            $("#dosis_medicamento_modificacion_indicacion").val(dosis_medicamento_modificacion_indicacion);
            $("#via_medicamento_modificacion_indicacion").val(via_medicamento_modificacion_indicacion);
            $("#horario_medicamento_modificacion_indicacion").selectpicker('val',arr_horarios).trigger("change");
            $("#fecha_emision_medicamento_modificacion_indicacion").val(fecha_emision_medicamento_modificacion_indicacion).trigger("change");
            $("#fecha_vigencia_medicamento_modificacion_indicacion").val(fecha_vigencia_medicamento_modificacion_indicacion).trigger("change");
            $("#responsable_modificacion_medicamento").val(responsable_modificacion_medicamento);
        
        }else if (tipo === "Indicación"){
            var descripcion_indicacion_modificacion_indicacion = "{{$indciacionInfo[0]->indicacion}}";
            var responsable_modificacion_indicacion = "{{$indciacionInfo[0]->responsable}}";
            
            var fecha_creacion_indicacion = "{{\Carbon\Carbon::parse($indciacionInfo[0]->fecha_vigencia)->format('d-m-Y H:i:s')}}";
            
            
            $("#descripcion_indicacion_modificacion_indicacion").val(descripcion_indicacion_modificacion_indicacion);
            $("#horario_indicacion_modificacion_indicacion").selectpicker('val',arr_horarios).trigger("change");
            $("#responsable_modificacion_indicacion").val(responsable_modificacion_indicacion);
            $("#fecha_creacion_indicacion_modificacion_indicacion").val(fecha_creacion_indicacion);

        }else if(tipo === "Interconsulta"){
            var tipo_interconsulta = "{{$indciacionInfo[0]->tipo_interconsulta}}";
            $("#tipo_interconsulta_modificacion_indicacion").val(tipo_interconsulta);
        }

    
    }


    $(document).ready(function() {

        $("#horario_medicamento_modificacion_indicacion").selectpicker();
        $("#horario_indicacion_modificacion_indicacion").selectpicker();
        $(".tipo-modificacion-indicacion-selectpicker").selectpicker();

        $('.imfecha-modificacion-indicacion').datetimepicker({
            locale: "es",
            format: 'DD-MM-YYYY HH:mm'
        }).on('dp.change', function (e) { 
            var dom = $(".imfecha-modificacion-indicacion");
            $('#form_modificacion_indicacion').bootstrapValidator('revalidateField', dom);
        });

        $('.dtpfecha-modificacion').datetimepicker({
            locale: "es",
            format: 'DD-MM-YYYY HH:mm'
        }).on('dp.change', function (e) { 
            // var dom = $(".dtpfecha-modificacion");
            $('#form_modificacion_indicacion').bootstrapValidator('revalidateField', $(this));
        });


        $("#form_modificacion_indicacion").bootstrapValidator({
            excluded: [':disabled'],
            fields: {    
                'medicamento_descripcion_modificacion_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar el medicamento'
                        }
                    }
                },
                'dosis_medicamento_modificacion_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar la dosis'
                        }
                    }
                },
                'horario_medicamento_modificacion_indicacion[]':{
                    validators: {
                        callback: {
                            message: 'Ingrese hora',
                            callback: function(value, validator) {
                                var count = (value !== null) ? value.length : null;
                                return count >= 1;
                            }
                        }
                    }
                },
                'fecha_emision_medicamento_modificacion_indicacion':{
                    validators: {
                        callback: {
                            message: 'La fecha de emisión debe ser menor a la de vigencia',
                            callback: function(value, validator) {

                                var is_valid = false;
                                var fecha_emision_str = $("#fecha_emision_medicamento_modificacion_indicacion").val();
                                var fecha_vigencia_str = $("#fecha_vigencia_medicamento_modificacion_indicacion").val();

                                try {

                                    var fecha_emision_date_arr = fecha_emision_str.split(" ")[0].split("-");
                                    var fecha_emision_date_str = fecha_emision_date_arr[1]+"-"+fecha_emision_date_arr[0]+"-"+fecha_emision_date_arr[2];
                                    var fecha_emision_time_str = fecha_emision_str.split(" ")[1];
                                    var fecha_emision_complete_str = fecha_emision_date_str+" "+fecha_emision_time_str;

                                    var fecha_vigencia_date_arr = fecha_vigencia_str.split(" ")[0].split("-");
                                    var fecha_vigencia_date_str = fecha_vigencia_date_arr[1]+"-"+fecha_vigencia_date_arr[0]+"-"+fecha_vigencia_date_arr[2];
                                    var fecha_vigencia_time_str = fecha_vigencia_str.split(" ")[1];
                                    var fecha_vigencia_complete_str = fecha_vigencia_date_str+" "+fecha_vigencia_time_str;


                                    var fecha_emision = new Date(fecha_emision_complete_str); 
                                    var fecha_vigencia = new Date(fecha_vigencia_complete_str);

                                    var is_valid_emision_date = isValidDate(fecha_emision);

                                    is_valid = (is_valid_emision_date && fecha_emision < fecha_vigencia) ? true : false;

                                } catch (error) {
                                    is_valid = false;
                                }
                                return is_valid;
                            }
                        }
                    }
                },
                'fecha_vigencia_medicamento_modificacion_indicacion':{
                    validators: {
                        callback: {
                            message: 'La fecha de emisión debe ser mayor a la de vigencia',
                            callback: function(value, validator) {

                                var is_valid = false;
                                var fecha_emision_str = $("#fecha_emision_medicamento_modificacion_indicacion").val();
                                var fecha_vigencia_str = $("#fecha_vigencia_medicamento_modificacion_indicacion").val();

                                try {

                                    var fecha_emision_date_arr = fecha_emision_str.split(" ")[0].split("-");
                                    var fecha_emision_date_str = fecha_emision_date_arr[1]+"-"+fecha_emision_date_arr[0]+"-"+fecha_emision_date_arr[2];
                                    var fecha_emision_time_str = fecha_emision_str.split(" ")[1];
                                    var fecha_emision_complete_str = fecha_emision_date_str+" "+fecha_emision_time_str;

                                    var fecha_vigencia_date_arr = fecha_vigencia_str.split(" ")[0].split("-");
                                    var fecha_vigencia_date_str = fecha_vigencia_date_arr[1]+"-"+fecha_vigencia_date_arr[0]+"-"+fecha_vigencia_date_arr[2];
                                    var fecha_vigencia_time_str = fecha_vigencia_str.split(" ")[1];
                                    var fecha_vigencia_complete_str = fecha_vigencia_date_str+" "+fecha_vigencia_time_str;


                                    var fecha_emision = new Date(fecha_emision_complete_str); 
                                    var fecha_vigencia = new Date(fecha_vigencia_complete_str);

                                    var is_valid_vigencia_date = isValidDate(fecha_vigencia);

                                    is_valid = (is_valid_vigencia_date && fecha_emision < fecha_vigencia) ? true : false;

                                } catch (error) {
                                    is_valid = false;
                                }
                                return is_valid;
                            }
                        }
                    }
                },
                'descripcion_indicacion_modificacion_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar la indicación'
                        }
                    }
                },
                'horario_indicacion_modificacion_indicacion[]':{
                    validators: {
                        callback: {
                            message: 'Ingrese hora',
                            callback: function(value, validator) {
                                var count = (value !== null) ? value.length : null;
                                return count >= 1;
                            }
                        }
                    }
                },
                
                'tipo_interconsulta_modificacion_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar el tipo'
                        }
                    }
                },
                'responsable_modificacion_indicacion': {
                    validators: {
                        notEmpty: {
                            message: 'Debe seleccionar un responsable'
                        }
                    }
                },
                'responsable_modificacion_medicamento': {
                    validators: {
                        notEmpty: {
                            message: 'Debe seleccionar un responsable'
                        }
                    }
                },
                'fecha_creacion_indicacion_modificacion_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar una fecha'
                        }
                    }
                }
            }
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault();
            var $form = $(evt.target);
            bootbox.confirm({
                    message: "<h4>¿Está seguro de actualizar la información?</h4>",
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
                        $("#btn_actualizar_modificacion_indicacion").prop("disabled", true);
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/modificarPCIndicacion",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(res){
                                $("#modalModificarIndicacion").modal('hide');

                                swalExito.fire({
                                title: 'Exito!',
                                text: res.exito,
                                });
                                    
                                //actualizar tabla
                                tablePCIndicaciones.api().ajax.reload();

                            },
                            error: function(xhr, status, error){
                                var error_json = JSON.parse(xhr.responseText);
                                swalError.fire({
                                title: 'Error',
                                text:error_json.error
                                }).then(function(result) {
                                    console.log(result);
                                if (result.isDenied) {
                                    $("#modalModificarIndicacion").modal('hide');
                                    tablePCIndicaciones.api().ajax.reload();
                                }
                                });
                            },
                            complete: function (){
                                $("#btn_guardar_agregar_indicacion").prop("disabled", false);
                            }
                        });				
                    }				
                }
            });  
        });


        //* TIPO INDICACIÓN */
        $(".modificacion-indicacion-container").on('change', '#tipo_modificacion_indicacion', function(){   
            var tipo = $(this).val();

            if (tipo === 'Medicamento'){

                //habilitar seccion medicamento
                $(".medicamento-modificacion-indicacion-container").show();
                $(".medicamento-modificacion-indicacion-container").find('input, textarea, select, button').prop("disabled",false).removeClass("disabled");

                //deshabilitar secciones indicacion e inter-consulta
                $(".indicacion-modificacion-indicacion-container, .interconsulta-modificacion-indicacion-container").find('input, textarea, select').prop("disabled",true);
                $(".indicacion-modificacion-indicacion-container, .interconsulta-modificacion-indicacion-container").hide();

                //reset indicacion
                $("#descripcion_indicacion_modificacion_indicacion").val("");
                $("#horario_indicacion_modificacion_indicacion").val('default').selectpicker('deselectAll');
                $("#horario_indicacion_modificacion_indicacion").selectpicker('refresh');

                //reset inter-consulta
                $("#tipo_interconsulta_modificacion_indicacion").val("").trigger("change");

            } else if (tipo === 'Indicación') {
                
                //habilitar seccion indicacion
                $(".indicacion-modificacion-indicacion-container").show();
                $(".indicacion-modificacion-indicacion-container").find('input, textarea, select, button').prop("disabled",false).removeClass("disabled");
                
                //deshabilitar secciones medicamento e inter-consulta
                $(".medicamento-modificacion-indicacion-container, .interconsulta-modificacion-indicacion-container").find('input, textarea, select').prop("disabled",true);
                $(".medicamento-modificacion-indicacion-container, .interconsulta-modificacion-indicacion-container").hide();
                
                
                //reset medicamento
                $("#medicamento_descripcion_modificacion_indicacion").val("");
                $("#dosis_medicamento_modificacion_indicacion").val("");
                $("#via_medicamento_modificacion_indicacion").val("")
                $("#fecha_emision_medicamento_modificacion_indicacion").val("").trigger("change");
                $("#fecha_vigencia_medicamento_modificacion_indicacion").val("").trigger("change");
                $("#horario_medicamento_modificacion_indicacion").val('default').selectpicker('deselectAll');
                $("#horario_medicamento_modificacion_indicacion").selectpicker('refresh');

                //reset inter-consulta
                $("#tipo_interconsulta_modificacion_indicacion").val("").trigger("change");



            }else if (tipo === 'Interconsulta'){
                
                //habilitar seccion inter-consulta
                $(".interconsulta-modificacion-indicacion-container").show();
                $(".interconsulta-modificacion-indicacion-container").find('input, textarea, select, button').prop("disabled",false).removeClass("disabled");
                
                //deshabilitar secciones medicamento e indicacion
                $(".medicamento-modificacion-indicacion-container, .indicacion-modificacion-indicacion-container").find('input, textarea, select').prop("disabled",true);
                $(".medicamento-modificacion-indicacion-container, .indicacion-modificacion-indicacion-container").hide();

                //reset medicamento
                $("#medicamento_descripcion_modificacion_indicacion").val("");
                $("#dosis_medicamento_modificacion_indicacion").val("");
                $("#via_medicamento_modificacion_indicacion").val("")
                $("#fecha_emision_medicamento_modificacion_indicacion").val("").trigger("change");
                $("#fecha_vigencia_medicamento_modificacion_indicacion").val("").trigger("change");

                $("#horario_medicamento_modificacion_indicacion").val('default').selectpicker('deselectAll');
                $("#horario_medicamento_modificacion_indicacion").selectpicker('refresh');

                //reset indicacion
                $("#descripcion_indicacion_modificacion_indicacion").val("");
                $("#horario_indicacion_modificacion_indicacion").val('default').selectpicker('deselectAll');
                $("#horario_indicacion_modificacion_indicacion").selectpicker('refresh');
                

            }


        });

        load_modificacion_indicacion();

    });
   
</script>

<fieldset>
    <div class="modificacion-indicacion-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_indicacion')) }}

        {{ Form::hidden ('id_indicacion_actualizar', $indciacionInfo[0]->id, array('class' => '') )}}

        <div class="tipo-modificacion-indicacion-container">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {{Form::label('', "INGRESO DE", array( ))}} <br>
                        {{Form::select('tipo_modificacion_indicacion', [ 'Medicamento'=> 'Medicamento', 'Indicación' => 'Indicación', 'Interconsulta' => 'Interconsulta'],null ,array('class' => 'form-control selectpicker tipo-modificacion-indicacion-selectpicker', 'id' => 'tipo_modificacion_indicacion'))}}
                    </div>
                </div>
            </div>
        </div>

        <div class="medicamento-modificacion-indicacion-container">
            <div class="col-md-12 ">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>MEDICAMENTO</label>
                        {{Form::text('medicamento_descripcion_modificacion_indicacion', null, array('class' => 'form-control', 'id' => 'medicamento_descripcion_modificacion_indicacion'))}}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>DOSIS</label>
                            {{Form::text('dosis_medicamento_modificacion_indicacion', null, array('class' => 'form-control', 'id' => 'dosis_medicamento_modificacion_indicacion'))}}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "VÍA:", array( ))}}
                            {{ Form::select('via_medicamento_modificacion_indicacion', array('Oral' => 'Oral', 'Sublingual' => 'Sublingual', 'Tópica' => 'Tópica', 'Transdérmica' => 'Transdérmica', 'Oftalmológica' => 'Oftalmológica', 'Inhalatoria' => 'Inhalatoria', 'Rectal' => 'Rectal', 'Vaginal' => 'Vaginal', 'Intravenosa' => 'Intravenosa', 'Intramuscular' => 'Intramuscular', 'Subcutánea' => 'Subcutánea', 'Intradérmica' => 'Intradérmica', 'Ótica' => 'Ótica', 'Nasal' => 'Nasal'), null, array( 'class' => 'form-control', 'id' => 'via_medicamento_modificacion_indicacion')) }}
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12 ">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>HORARIO</label>
                        {{Form::select('horario_medicamento_modificacion_indicacion[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker', 'id' => 'horario_medicamento_modificacion_indicacion', 'multiple'))}}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>FECHA EMISION</label>
                            {{Form::text('fecha_emision_medicamento_modificacion_indicacion', null, array('id' => 'fecha_emision_medicamento_modificacion_indicacion', 'class' => 'form-control imfecha-modificacion-indicacion'))}}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="col-md-offset-1"> 
                        <div class="form-group"> 
                            <label>FECHA VIGENCIA</label>
                            {{Form::text('fecha_vigencia_medicamento_modificacion_indicacion', null, array('id' => 'fecha_vigencia_medicamento_modificacion_indicacion', 'class' => 'form-control imfecha-modificacion-indicacion'))}}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>RESPONSABLE</label>
                        {{Form::select('responsable_modificacion_medicamento', array('1' => 'Enfermera', '2' => 'Tens'), null, array('id' => 'responsable_modificacion_medicamento', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                    </div>
                </div>

            </div>

        </div>

        <div class="indicacion-modificacion-indicacion-container">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>FECHA VIGENCIA</label>
                        {{Form::text('fecha_creacion_indicacion_modificacion_indicacion', null, array('id' => 'fecha_creacion_indicacion_modificacion_indicacion', 'class' => 'form-control dtpfecha-modificacion'))}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label> INDICACIÓN</label>
                        {{Form::textarea('descripcion_indicacion_modificacion_indicacion', null, array('id' => 'descripcion_indicacion_modificacion_indicacion', 'class' => 'form-control', 'rows' => '3', 'style' => 'resize:none'))}}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>HORARIO</label>
                            {{Form::select('horario_indicacion_modificacion_indicacion[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker','id' => 'horario_indicacion_modificacion_indicacion' ,'multiple'))}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>RESPONSABLE</label>
                        {{Form::select('responsable_modificacion_indicacion', array('1' => 'Enfermera', '2' => 'Tens'), null, array('id' => 'responsable_modificacion_indicacion', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                    </div>
                </div>
            </div>
        </div>

        <div class="interconsulta-modificacion-indicacion-container">
            <div class="col-md-12">
                <div class="col-md-6"> 
                    <div class="form-group">
                        <label> TIPO</label>
                        {{Form::text('tipo_interconsulta_modificacion_indicacion', null, array('class' => 'form-control', 'id' => 'tipo_interconsulta_modificacion_indicacion'))}} 
                    </div>   
                </div>

            </div>
        </div>
            
        <div class="btn-actualizar-modificacion-indicacion-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modificacion_indicacion" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>

