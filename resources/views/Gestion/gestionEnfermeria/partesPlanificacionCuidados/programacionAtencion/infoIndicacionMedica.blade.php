<script>

    function isValidDate(d) {
        return d instanceof Date && !isNaN(d);
    }

    function load_modificacion_indicacion_medica(){

        $('.modificacion-indicacion-medica-farmaco-container').hide();
        var horarios = "{{$indciacionInfo[0]->horario}}";
        console.log("horarios");
        console.log(horarios);
        var arr_horarios = horarios.split(',');
        var tipo = "{{$indciacionInfo[0]->tipo}}";
        options_indicacion_medica = "";
        options_indicacion_medica_farmaco = "";

        indicacion = {!! json_encode($indciacionInfo) !!};
        ultimaIndicacion = {!! json_encode($ultimaIndicacion) !!};
        console.log(ultimaIndicacion);
        if(ultimaIndicacion.sueros === undefined){
             sueros = false;
        }else{
             sueros = ultimaIndicacion.sueros;
        }
        if(ultimaIndicacion.horas_signos_vitales === undefined){
            signos_vitales = null;
        }else{
            signos_vitales = ultimaIndicacion.horas_signos_vitales;
        }
        if(ultimaIndicacion.horas_hemoglucotest === undefined){
            hemoglucotest = null;
         }else{
            hemoglucotest = ultimaIndicacion.horas_hemoglucotest;
         }        
              
        if(ultimaIndicacion.farmacos === undefined){
            farmacos = null;
        }else{
            farmacos = ultimaIndicacion.farmacos;
        }        
              

        if(sueros == true){
            options_indicacion_medica += "<option value='Suero'>Suero</option>";
        }

        if(signos_vitales != null){
            options_indicacion_medica += "<option value='Control de signos vitales'>Control de signos vitales</option>";
            $('#signos_horas_modificacion_indicacion_medica').text(signos_vitales);
        }

        if(hemoglucotest != null){
            options_indicacion_medica  += "<option value='Control de hemoglucotest'>Control de hemoglucotest</option>";
            $('#hemoglucotest_horas_modificacion_indicacion_medica').text(hemoglucotest);
        }

        if(typeof farmacos !== 'undefined' && farmacos.length > 0){
            farmacos.forEach(function(farmaco) {
                options_indicacion_medica_farmaco  += "<option value='"+farmaco.id+"'>"+farmaco.nombre_unidad+"</option>";
            });

            $('#farmacos_modificacion_indicacion_medica').html(options_indicacion_medica_farmaco);
            $('#farmacos_modificacion_indicacion_medica').val(indicacion[0].id_farmaco).change();
            if (typeof via_intervalo !== 'undefined') {
                $('#via_modificacion_indicacion_medica').text(via_intervalo.via_administracion);
                via_intervalo= "Sin información";
                if (via_intervalo.intervalo_farmaco != null && via_intervalo.intervalo_farmaco != '') {
                    via_intervalo = via_intervalo.intervalo_farmaco;
                }
                $('#intervalo_modificacion_indicacion_medica').text(via_intervalo);
            }

            options_indicacion_medica  += "<option value='Farmacos'>Farmacos</option>";
        }

        via_intervalo = {!! json_encode($via_intervalo) !!};
        if (typeof via_intervalo !== 'undefined') {
            $('#via_modificacion_indicacion_medica').text(via_intervalo.via_administracion);
            via_intervalo= "Sin información";
            if (via_intervalo.intervalo_farmaco != null && via_intervalo.intervalo_farmaco != '') {
                via_intervalo = via_intervalo.intervalo_farmaco;
            }
            $('#intervalo_modificacion_indicacion_medica').text(via_intervalo);
        }

        $('#tipo_modificacion_indicacion_medica').html(options_indicacion_medica);
        $('#tipo_modificacion_indicacion_medica').val("{{$indciacionInfo[0]->tipo}}").change();
        
        if( $('#tipo_modificacion_indicacion_medica').val() == 'Farmacos'){
            $('.modificacion-indicacion-medica-farmaco-container').show();
            $('.hemoglucotest-modificacion-indicacion-medica-container').hide();
            $('.suero-modificacion-indicacion-medica-container').hide();
            $('.signos-modificacion-indicacion-medica-container').hide();
            $('.farmacos-modificacion-indicacion-medica-container').show();
        }else if($('#tipo_modificacion_indicacion_medica').val() == 'Suero'){
            $('.suero-modificacion-indicacion-medica-container').show();
            $('.signos-modificacion-indicacion-medica-container').hide();
            $('.hemoglucotest-modificacion-indicacion-medica-container').hide();
            $('.modificacion-indicacion-medica-farmaco-container').hide();
            $('.farmacos-modificacion-indicacion-medica-container').hide();
        }else if($('#tipo_modificacion_indicacion_medica').val() == 'Control de signos vitales'){
            $('.signos-modificacion-indicacion-medica-container').show();
            $('.hemoglucotest-modificacion-indicacion-medica-container').hide();
            $('.suero-modificacion-indicacion-medica-container').hide();
            $('.modificacion-indicacion-medica-farmaco-container').hide();
            $('.farmacos-modificacion-indicacion-medica-container').hide();
        }else if($('#tipo_modificacion_indicacion_medica').val() == 'Control de hemoglucotest'){
            $('.hemoglucotest-modificacion-indicacion-medica-container').show();
            $('.signos-modificacion-indicacion-medica-container').hide();
            $('.suero-modificacion-indicacion-medica-container').hide();
            $('.modificacion-indicacion-medica-farmaco-container').hide();
            $('.farmacos-modificacion-indicacion-medica-container').hide();
            
        }


        var responsable_modificacion_indicacion_medica = {!! json_encode($indciacionInfo[0]->responsable) !!};
        $("#responsable_modificacion_indicacion_medica").val(responsable_modificacion_indicacion_medica).change();

        $("#horario_modificacion_indicacion_medica").selectpicker('val',arr_horarios).trigger("change");
        
        var fecha_emision_medicamento_modificacion_indicacion_medica = "{{\Carbon\Carbon::parse($indciacionInfo[0]->fecha_emision)->format('d-m-Y H:i:s')}}";
        var fecha_vigencia_medicamento_modificacion_indicacion_medica = "{{\Carbon\Carbon::parse($indciacionInfo[0]->fecha_vigencia)->format('d-m-Y H:i:s')}}";
        $("#fecha_emision_medicamento_modificacion_indicacion_medica").text(fecha_emision_medicamento_modificacion_indicacion_medica);
        $("#fecha_vigencia_medicamento_modificacion_indicacion_medica").text(fecha_vigencia_medicamento_modificacion_indicacion_medica);

        console.log("arr_horarios");
        console.log(arr_horarios);
    
    }

    function resetAllModificacionIndicacionMedica(){
        //reset medicamento
        $("#horario_modificacion_indicacion_medica").val('default').selectpicker('deselectAll');
        $("#horario_modificacion_indicacion_medica").selectpicker('refresh').change();
        $("#responsable_modificacion_indicacion_medica").val('').change();

    }

    function activarValidacionesIndicacionMedica(){
        $("#form_modificacion_indicacion_medica").bootstrapValidator('revalidateField', 'responsable_modificacion_indicacion_medica');
        $("#form_modificacion_indicacion_medica").bootstrapValidator('revalidateField', 'horario_modificacion_indicacion_medica[]');
    }


    $(document).ready(function() {

        $("#horario_modificacion_indicacion_medica").selectpicker();

        $("#form_modificacion_indicacion_medica").bootstrapValidator({
            excluded: [':disabled'],
            fields: {    
                'horario_modificacion_indicacion_medica[]':{
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
                'responsable_modificacion_indicacion_medica': {
                    validators: {
                        notEmpty: {
                            message: 'Debe seleccionar un responsable'
                        }
                    }
                },
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
                            url: "{{URL::to('/gestionEnfermeria')}}/modificarDatosPCIndicacion",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(res){
                                $("#modalModificarIndicacionMedica").modal('hide');

                                swalExito.fire({
                                title: 'Exito!',
                                text: res.exito,
                                });
                                    
                                //actualizar tabla
                                tableIndicacionesMedicas.api().ajax.reload();

                            },
                            error: function(xhr, status, error){
                                var error_json = JSON.parse(xhr.responseText);
                                swalError.fire({
                                title: 'Error',
                                text:error_json.error
                                }).then(function(result) {
                                    console.log(result);
                                if (result.isDenied) {
                                    $("#modalModificarIndicacionMedica").modal('hide');
                                    tableIndicacionesMedicas.api().ajax.reload();
                                    cargarDatosIndicacionMedica();
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
           $(".tipo-modificacion-indicacion-medica-container").on('change', '#tipo_modificacion_indicacion_medica', function(){   
            var tipo = $(this).val();

            if (tipo === 'Farmacos'){
                //reset
                resetAllModificacionIndicacionMedica();

                //habilitar seccion farmaco
                $(".modificacion-indicacion-medica-farmaco-container").show();
                activarValidacionesIndicacionMedica();
                $('.hemoglucotest-modificacion-indicacion-medica-container').hide();
                $('.suero-modificacion-indicacion-medica-container').hide();
                $('.signos-modificacion-indicacion-medica-container').hide();
                $('.farmacos-modificacion-indicacion-medica-container').show();

            }else if (tipo === 'Control de hemoglucotest'){
                //reset
                resetAllModificacionIndicacionMedica();
                
                //habilitar seccion hemoglucotest
                $(".modificacion-indicacion-medica-farmaco-container").hide();
                $('.hemoglucotest-modificacion-indicacion-medica-container').show();
                $('.signos-modificacion-indicacion-medica-container').hide();
                $('.suero-modificacion-indicacion-medica-container').hide();
                $('.farmacos-modificacion-indicacion-medica-container').hide();
            }else if (tipo === 'Control de signos vitales'){
                //reset
                resetAllModificacionIndicacionMedica();
                
                //habilitar seccion signos
                $(".modificacion-indicacion-medica-farmaco-container").hide();
                $('.signos-modificacion-indicacion-medica-container').show();
                $('.hemoglucotest-modificacion-indicacion-medica-container').hide();
                $('.suero-modificacion-indicacion-medica-container').hide();
                $('.farmacos-modificacion-indicacion-medica-container').hide();
            }else if (tipo === 'Suero'){
                //reset
                resetAllModificacionIndicacionMedica();

                   //habilitar seccion Suero
                $('.suero-modificacion-indicacion-medica-container').show();
                $('.signos-modificacion-indicacion-medica-container').hide();
                $('.hemoglucotest-modificacion-indicacion-medica-container').hide();
                $('.modificacion-indicacion-medica-farmaco-container').hide();
                $('.farmacos-modificacion-indicacion-medica-container').hide();
            }

            
        });


        $(".farmacos-modificacion-indicacion-medica-container").on('change', '#farmacos_modificacion_indicacion_medica', function(){   
            var tipoFarmaco = $(this).val();
            var id_indicacion_medica = "{{$ultimaIndicacion->id}}";
            var id_caso_agregar_indicacion_medica = "{{$ultimaIndicacion->caso}}";
                $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/obtenerDatosIndicacionFarmacos/"+id_caso_agregar_indicacion_medica+"/"+tipoFarmaco+"/"+id_indicacion_medica,
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "get",
                        success: function(data){
                            if(data.via_administracion){
                               $('#via_modificacion_indicacion_medica').text(data.via_administracion) ;
                            }                            
                            if(data.intervalo){
                                $('#intervalo_modificacion_indicacion_medica').text(data.intervalo) ;
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });	
        });
        
        $("#tipo_modificacion_indicacion_medica").trigger("change");


        load_modificacion_indicacion_medica();

    });
   
</script>

<style>

#fecha_emision_medicamento_modificacion_indicacion_medica,#fecha_vigencia_medicamento_modificacion_indicacion_medica{
    margin-top:10px;
}

</style>

<fieldset>
    <div class="modificacion-indicacion-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_indicacion_medica')) }}

        {{ Form::hidden ('id_indicacion_medica_actualizar', $indciacionInfo[0]->id, array('id' => 'id_indicacion_medica_actualizar') )}}

        <div class="tipo-modificacion-indicacion-medica-container">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {{Form::label('', "INGRESO DE", array( ))}} <br>
                        {{Form::select('tipo_modificacion_indicacion_medica', array(),null ,array('class' => 'form-control', 'id' => 'tipo_modificacion_indicacion_medica'))}}
                    </div>
                </div>
                <div class="col-md-7 suero-modificacion-indicacion-medica-container">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>Sueros</label>
                            {{Form::select('suero_modificacion_indicacion_medica', $sueros,null ,array('class' => 'form-control', 'id' => 'suero_modificacion_indicacion_medica'))}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 signos-modificacion-indicacion-medica-container">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>Cada Cuantas Horas</label>
                            <p id="signos_horas_modificacion_indicacion_medica"></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 hemoglucotest-modificacion-indicacion-medica-container">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>Cada Cuantas Horas</label>
                            <p id="hemoglucotest_horas_modificacion_indicacion_medica"></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 farmacos-modificacion-indicacion-medica-container">
                    <div class="col-md-offset-1"> 
                        <div class="form-group"> 
                            <label for="">LISTADO DE FARMACOS</label>
                            {{Form::select('farmacos_modificacion_indicacion_medica', array(),null ,array('class' => 'form-control', 'id' => 'farmacos_modificacion_indicacion_medica'))}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modificacion-indicacion-medica-container">

            <div class="col-md-12 ">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>HORARIO</label>
                        {{Form::select('horario_modificacion_indicacion_medica[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker', 'id' => 'horario_modificacion_indicacion_medica', 'multiple'))}}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>RESPONSABLE</label>
                            {{Form::select('responsable_modificacion_indicacion_medica', array('1' => 'Enfermera', '2' => 'Tens'), null, array('id' => 'responsable_modificacion_indicacion_medica', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="modificacion-indicacion-medica-farmaco-container">

            <div class="col-md-12 ">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>VÍA DE ADMINISTRACIÓN</label>
                      <p id="via_modificacion_indicacion_medica"></p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="col-md-offset-1">
                        <div class="form-group">
                            <label>INTERVALO</label>
                            <p id="intervalo_modificacion_indicacion_medica"></p>
                        </div>
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

