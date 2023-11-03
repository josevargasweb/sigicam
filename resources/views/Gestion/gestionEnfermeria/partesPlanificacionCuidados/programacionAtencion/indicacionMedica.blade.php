<script>

    function generarTablaPCIndicaciones() {
        tablePCIndicaciones = $("#tablePCIndicacionesMedicas").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerPlanificacionIndicacionesMedicas/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            /* "initComplete":  */ 
        });
    }

    function isValidDate(d) {
        return d instanceof Date && !isNaN(d);
    }

    function activarValidacionesIndicacion(){
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'fecha_creacion_indicacion_agregar_indicacion');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'descripcion_indicacion_agregar_indicacion');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'horario_indicacion_agregar_indicacion[]');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'responsable_agregar_indicacion');
    }

    function activarValidacionesMedicamento(){
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'responsable_agregar_medicamento');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'medicamento_descripcion_agregar_indicacion');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'dosis_medicamento_agregar_indicacion');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'horario_medicamento_agregar_indicacion[]');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'fecha_emision_medicamento_agregar_indicacion');
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'fecha_vigencia_medicamento_agregar_indicacion');
    }

    function activarValidacionesInterconsulta(){
        $("#form_agregar_indicacion").bootstrapValidator('revalidateField', 'tipo_interconsulta_agregar_indicacion');
    }

    function resetAllSection(){

        //reset medicamento
        $("#medicamento_descripcion_agregar_indicacion").val("").change();
        $("#dosis_medicamento_agregar_indicacion").val("").change();
        $("#via_medicamento_agregar_indicacion").val("");
        $("#fecha_emision_medicamento_agregar_indicacion").val("").trigger("change");
        $("#fecha_vigencia_medicamento_agregar_indicacion").val("").trigger("change");
        $("#horario_medicamento_agregar_indicacion").val('default').selectpicker('deselectAll');
        $("#horario_medicamento_agregar_indicacion").selectpicker('refresh').change();
        $("#responsable_agregar_medicamento").val('').change();

        //reset indicacion
        $("#descripcion_indicacion_agregar_indicacion").val("").change();
        $("#horario_indicacion_agregar_indicacion").val('default').selectpicker('deselectAll').change();
        $("#horario_indicacion_agregar_indicacion").selectpicker('refresh');
        $("#fecha_creacion_indicacion_agregar_indicacion").val("").change();
        $("#responsable_agregar_indicacion").val('').change();

        //reset inter-consulta
        $("#tipo_interconsulta_agregar_indicacion").val("").trigger("change");


    }



    function mostrarInfoMedicamento(){
        
        var value=$("input[name='medic']:checked").val();
        if(value == "si"){
            $("#medicamentosIM").show("slow");
            $("#medicamentosIM").attr("hidden",false);
        }else{
            $("#medicamentosIM").hide("slow");
            $("#medicamentosIM").attr("hidden",true);
            //LIMPIAR valores
            $("#medicamentosIM input").val("");
        } 
        
        
    }

    
    function obtenerIndicacion(idIndicacion,tipo) {
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/obtenerIndicacion/"+idIndicacion+"/"+tipo,
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                var titulo = '';
                if(tipo == 1){
                    titulo = "Modificar datos indicaciones medicas";
                }else if(tipo == 2){
                    titulo = "Terminar datos indicaciones medicas";
                }else if(tipo == 3){
                    titulo = "Eliminar datos indicaciones medicas";
                }
                $("#modalModificarIndicacion .modal-title").html(titulo);
                $("#modalModificarIndicacion .modal-body").html(data.contenido);
			    $("#modalModificarIndicacion").modal();

            },
            error: function(error){
                $("#btnIndicacionMedica").prop("disabled", false);
                console.log(error);
            }
        });	

    }
    
    function obtenerIndicacionEliminarTerminar(idIndicacion,tipo_modificacion) {
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/obtenerIndicacionEliminarTerminar",
            data: {
              idIndicacion : idIndicacion,
              tipo_modificacion : tipo_modificacion
            },
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "post",
            success: function(data){
                //console.log(data);
                if(data.exito){
                $('#modalTestIndicacion').modal('hide');
                swalInfo2.fire({
                    title: 'Información',
                    text:data.exito,
                    showConfirmButton: false,
                    timer:3000
                });
                }else{
                var horarios = data.contenido[0].horario;
                var arr_horarios = horarios.split(',');
                var color = '';
                if(data.contenido[0].responsable == 1){
                    color = "colorEnfermera";
                }else if(data.contenido[0].responsable == 2){
                    color= "colorTens";
                }

                var color_boton = '';
                var titulo = '';
                var boton_eliminarTerminar = '';
                var signo = '';
                if(tipo_modificacion == 2){
                    color_boton = 'success';
                    titulo = "Terminar datos indicaciones medicas";
                    signo = 'FIN';
                    boton_eliminarTerminar = '<a type="button" class="btn btn-'+color_boton+'"  onclick="eliminarFilaIndicacion('+data.contenido[0].id+',2)" style="margin-top:15%;">Terminar Todo</a>';
                }else if(tipo_modificacion == 3){
                    color_boton = 'danger';
                    titulo = "Eliminar datos indicaciones medicas";
                    signo = 'X';
                    boton_eliminarTerminar = '<a type="button" class="btn btn-'+color_boton+'" onclick="eliminarFilaIndicacion('+data.contenido[0].id+',3)" style="margin-top:15%;">Eliminar Todo</a>';
                }
                var datoHora = '';
                arr_horarios.forEach(function(hora) {
                    datoHora += '<div  class="'+color+'"><div class=""><button class="btn btn-'+color_boton+' botonCerrar" type="button"  onclick="eliminarHoraIndicacion('+hora+','+data.contenido[0].id+','+tipo_modificacion+')">'+signo+'</button><div class="valorInterno">'+hora+'</div></div></div>';
                    }); 
                var tipo_formulario = data.contenido[0].tipo;
                var eliminar_descripcion = '';

                if(tipo_formulario === "Medicamento"){
                    eliminar_descripcion = data.contenido[0].medicamento;
                }else if (tipo_formulario === "Indicación"){
                    eliminar_descripcion = data.contenido[0].indicacion;            
                }else if(tipo_formulario === "Interconsulta"){
                    eliminar_descripcion = data.contenido[0].tipo_interconsulta;
                }



        
                
                $('#modalTestIndicacion').modal('show');
                $('#modalTestIndicacion .modal-title').html(titulo);
                document.getElementById("tipo_eliminar").innerHTML = tipo_formulario+":";
                document.getElementById("eliminar_descripcion").innerHTML = eliminar_descripcion;
                document.getElementById("horas-eliminar-indicacion-container").innerHTML = datoHora;
                document.getElementById('btn_actualizar_eliminar_indicacion').innerHTML = boton_eliminarTerminar;
               
             
                }
            
            },
            error: function(error){
                $("#btnIndicacionMedica").prop("disabled", false);
                console.log(error);
            }
        });	

    }


    function eliminarHoraIndicacion(hora,idIndicacion,tipo_modificacion){
        if(tipo_modificacion == 2){
            title = '¿Está seguro de terminar este horario de la indicación medica?';
            text = 'Al Terminar este horario quedara disponible para visualizar en RESUMEN DE PLANIFICACION DE CUIDADOS';
        } 
        else if(tipo_modificacion == 3){
            title = '¿Está seguro de eliminar este horario de la indicación medica?';
            text = 'Al eliminar este horario, no se vera reflejado en RESUMEN DE PLANIFICACION DE CUIDADOS';
        }
        swalPregunta.fire({
            title: title,
            text: text,
        }).then(function(result){
            if (result.isConfirmed) {
                $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarTerminarPCIndicacionHora",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idIndicacion,
                            hora:hora,
                            tipo_modificacion:tipo_modificacion
                        },
                        dataType: "json",
                        type: "post",
                        success: function(res){
                            if(res.exito){
                            swalExito.fire({
                                title: 'Exito!',
                                text: res.exito,
                                didOpen: function() {
                                    setTimeout(function() {
                                    obtenerIndicacionEliminarTerminar(res.nueva_id,tipo_modificacion);
                                    //actualizar tabla
                                    tablePCIndicaciones.api().ajax.reload();
                                    }, 2000)
                                },
                            });	
                            }else if(res.error){
                                $('#modalTestIndicacion').modal('hide');
                                swalInfo2.fire({
                                    title: 'Información',
                                    text:res.error,
                                    showConfirmButton: false,
                                    timer:3000
                                });
                                tablePCIndicaciones.api().ajax.reload();
                            }                         

                        },
                        error: function(xhr, status, error){
                            var error_json = JSON.parse(xhr.responseText);
                            	swalError.fire({
                                title: 'Error',
                                text:error_json.error
                                });
						
                        }
                    });	
            }
        });

    }

    function eliminarFilaIndicacion (idIndicacion,tipo_modificacion) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de modificar la información?</h4>",				
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarTerminarPCIndicacion",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idIndicacion,
                            tipo_modificacion:tipo_modificacion
                        },
                        dataType: "json",
                        type: "post",
                        success: function(res){

                            swalExito.fire({
                            title: 'Exito!',
                            text: res.exito,
                            });
                                    
                            //actualizar tabla
                            tablePCIndicaciones.api().ajax.reload();

                            if(tipo != ''){
                                $("#modalTestIndicacion").modal('hide');
                            }


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
						
                        }
                    });				
                }				
            }
        });  
        	
    }

    function cargarVistaIndicacionMedica(){
        if (typeof tablePCIndicaciones !== 'undefined' && tablePCIndicaciones !== null) {
            tablePCIndicaciones.api().ajax.reload(false);
        }else{
            generarTablaPCIndicaciones();
        }
    }

    $(document).ready(function() {


        tablePCIndicaciones = null;

        $("#horario_medicamento_agregar_indicacion").selectpicker();
        $("#horario_indicacion_agregar_indicacion").selectpicker();

        $("#form_agregar_indicacion").bootstrapValidator({
            excluded: [':disabled'],
            fields: {  
                'medicamento_descripcion_agregar_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar el medicamento'
                        },
                        stringLength: {
                            max: 100,
                            message: "El campo no deber tener mas de 100 caracteres"
                        }
                    }
                },
                'dosis_medicamento_agregar_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar la dosis'
                        },
                        stringLength: {
                            max: 100,
                            message: "El campo no deber tener mas de 100 caracteres"
                        }
                    }
                },
                'horario_medicamento_agregar_indicacion[]':{
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
                'fecha_emision_medicamento_agregar_indicacion':{
                    validators: {
                        callback: {
                            message: 'La fecha de emisión debe ser menor a la de vigencia',
                            callback: function(value, validator) {

                                var is_valid = false;
                                var fecha_emision_str = $("#fecha_emision_medicamento_agregar_indicacion").val();
                                var fecha_vigencia_str = $("#fecha_vigencia_medicamento_agregar_indicacion").val();

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

                                    var is_valid_emision_date = isValidDate(fecha_vigencia);

                                    is_valid = (is_valid_emision_date && fecha_emision < fecha_vigencia) ? true : false;

                                } catch (error) {
                                    console.log(error);
                                    is_valid = false;
                                }
                                return is_valid;
                            }
                        }
                    }
                },
                'fecha_vigencia_medicamento_agregar_indicacion':{
                    validators: {
                        callback: {
                            message: 'La fecha de emisión debe ser mayor a la de vigencia',
                            callback: function(value, validator) {

                                var is_valid = false;
                                var fecha_emision_str = $("#fecha_emision_medicamento_agregar_indicacion").val();
                                var fecha_vigencia_str = $("#fecha_vigencia_medicamento_agregar_indicacion").val();

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
                'responsable_agregar_medicamento':{
                    validators: {
                        notEmpty: {
                            message: 'Debe seleccionar un responsable'
                        }
                    }
                },
                'descripcion_indicacion_agregar_indicacion':{
                    validators: {
                        trigger: 'change',
                        notEmpty: {
                            message: 'Debe ingresar la indicación'
                        },
                        stringLength: {
                            max: 500,
                            message: "El campo no deber tener mas de 500 caracteres"
                        }
                    }
                },
                'horario_indicacion_agregar_indicacion[]':{
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
                'tipo_interconsulta_agregar_indicacion':{
                    validators: {
                        notEmpty: {
                            message: 'Debe ingresar el tipo'
                        },
                        stringLength: {
                            max: 100,
                            message: "El campo no deber tener mas de 100 caracteres"
                        }
                    }
                },
                'responsable_agregar_indicacion': {
                    validators: {
                        notEmpty: {
                            message: 'Debe seleccionar un responsable'
                        }
                    }
                },
                'fecha_creacion_indicacion_agregar_indicacion':{
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
                    message: "<h4>¿Está seguro de agregar la información?</h4>",				
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
                        $("#btn_guardar_agregar_indicacion").prop("disabled", true);				
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/addIndicacionMedica",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(res){

                                //reset
                                resetAllSection();
                                activarValidacionesMedicamento();
                                activarValidacionesIndicacion();
                                activarValidacionesInterconsulta();
                                	swalExito.fire({
                                    title: 'Exito!',
                                    text: res.exito,
                                    });

                                /* actualizar tabla  */
                                tablePCIndicaciones.api().ajax.reload();
                                

                            },
                            error: function(xhr, status, error){
                                var error_json = JSON.parse(xhr.responseText);
                                            swalError.fire({
                                            title: 'Error',
                                            text:error_json.error
                                            });
                                activarValidacionesMedicamento();
                                activarValidacionesIndicacion();
                                activarValidacionesInterconsulta();
                            },
                            complete: function (){
                                $("#btn_guardar_agregar_indicacion").prop("disabled", false);
                            }
                        });				
                    }			
                }
            });  
        });

        activarValidacionesMedicamento();
        activarValidacionesIndicacion();
        activarValidacionesInterconsulta();

        
        $('.imfecha-agregar-indicacion').datetimepicker({
            locale: "es",
            format: 'DD-MM-YYYY HH:mm'
        }).on('dp.change', function (e) { 
            var dom = $(".imfecha-agregar-indicacion");
            $('#form_agregar_indicacion').bootstrapValidator('revalidateField', dom);
        });

        $('.dtpfecha-creacion').datetimepicker({
            locale: "es",
            format: 'DD-MM-YYYY HH:mm'
        }).on('dp.change', function (e) { 
            var dom = $(".dtpfecha-creacion");
            $('#form_agregar_indicacion').bootstrapValidator('revalidateField', dom);
        });
        
        $( "#planificacion" ).click(function() {
            var tabsPlanificacionCuidados = $("#tabsPlanificacionCuidados").tabs().find(".active");
            tabPC = tabsPlanificacionCuidados[0].id;

            if(tabPC == "1p"){
                //console.log("tabPC indicacion medica: ", tabPC);
                cargarVistaIndicacionMedica();
            }
            
        });

        $( "#pAt" ).click(function() {
            cargarVistaIndicacionMedica();
        });

        //* TIPO INDICACIÓN */
        $(".tipo-agregar-indicacion-container").on('change', '#tipo_agregar_indicacion', function(){   
            var tipo = $(this).val();

            if (tipo === 'Medicamento'){
                //reset
                resetAllSection();

                //habilitar seccion medicamento
                $(".medicamento-agregar-indicacion-container").show();
                $(".medicamento-agregar-indicacion-container").find('input, textarea, select, button').prop("disabled",false).removeClass("disabled");
                activarValidacionesMedicamento();

                //deshabilitar secciones indicacion e inter-consulta
                $(".indicacion-agregar-indicacion-container, .interconsulta-agregar-indicacion-container").find('input, textarea, select').prop("disabled",true);
                $(".indicacion-agregar-indicacion-container, .interconsulta-agregar-indicacion-container").hide();

            }else if (tipo === 'Indicación') {

                //reset
                resetAllSection();
                
                //habilitar seccion indicacion
                $(".indicacion-agregar-indicacion-container").show();
                $(".indicacion-agregar-indicacion-container").find('input, textarea, select, button').prop("disabled",false).removeClass("disabled");
                activarValidacionesIndicacion();
                
                //deshabilitar secciones medicamento e inter-consulta
                $(".medicamento-agregar-indicacion-container, .interconsulta-agregar-indicacion-container").find('input, textarea, select').prop("disabled",true);
                $(".medicamento-agregar-indicacion-container, .interconsulta-agregar-indicacion-container").hide();
                

            }else if (tipo === 'Interconsulta'){

                //reset
                resetAllSection();
                
                //habilitar seccion inter-consulta
                $(".interconsulta-agregar-indicacion-container").show();
                $(".interconsulta-agregar-indicacion-container").find('input, textarea, select, button').prop("disabled",false).removeClass("disabled");
                activarValidacionesInterconsulta();
                
                //deshabilitar secciones medicamento e indicacion
                $(".medicamento-agregar-indicacion-container, .indicacion-agregar-indicacion-container").find('input, textarea, select').prop("disabled",true);
                $(".medicamento-agregar-indicacion-container, .indicacion-agregar-indicacion-container").hide();                

            }


        });

        $("#tipo_agregar_indicacion").trigger("change");

    });
 
    
</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    .valorInternoSinX{
        padding-top: 20px;
        text-align: center;
        font-size:15px;
    } 

</style>


<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>INDICACIONES MEDICAS</h4>
            </div>

            <div class="panel-body" id="">

                <legend>Ingresar nueva indicación</legend>

                <div class="agregar-indicacion-container">
                    {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_agregar_indicacion')) }}
                    {{ Form::hidden ('id_caso_agregar_indicacion', $caso, array('id' => 'id_caso_agregar_indicacion') )}}


                    <div class="tipo-agregar-indicacion-container">
                        <div class="col-md-12">
                            <div class="col-md-4"> 
                                <div class="form-group">
                                    {{Form::label('', "INGRESO DE", array( ))}} <br>
                                    {{Form::select('tipo_agregar_indicacion', [ 'Medicamento'=> 'Medicamento', 'Indicación' => 'Indicación', 'Interconsulta' => 'Interconsulta'],null ,array('class' => 'form-control', 'id' => 'tipo_agregar_indicacion'))}}
                                </div>
                            </div>
                        </div>
                    </div> 


                    <div class="medicamento-agregar-indicacion-container">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>RESPONSABLE</label>
                                    {{Form::select('responsable_agregar_medicamento', array('1' => 'Enfermera', '2' => 'Tens'), null, array('id' => 'responsable_agregar_medicamento', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="col-md-4"> 
                                <div class="form-group"> 
                                    <label>MEDICAMENTO</label>
                                    {{Form::text('medicamento_descripcion_agregar_indicacion', null, array('class' => 'form-control', 'id' => 'medicamento_descripcion_agregar_indicacion','autocomplete' => 'off'))}} 
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="col-md-offset-1"> 
                                    <div class="form-group">
                                        <label>DOSIS</label>
                                        {{Form::text('dosis_medicamento_agregar_indicacion', null, array('class' => 'form-control', 'id' => 'dosis_medicamento_agregar_indicacion','autocomplete' => 'off'))}}
                                    </div>  
                                </div> 
                            </div>

                            <div class="col-md-4"> 
                                <div class="col-md-offset-1">
                                    <div class="form-group">
                                        {{Form::label('', "VÍA:", array( ))}}
                                        {{ Form::select('via_medicamento_agregar_indicacion', array('Oral' => 'Oral', 'Sublingual' => 'Sublingual', 'Tópica' => 'Tópica', 'Transdérmica' => 'Transdérmica', 'Oftalmológica' => 'Oftalmológica', 'Inhalatoria' => 'Inhalatoria', 'Rectal' => 'Rectal', 'Vaginal' => 'Vaginal', 'Intravenosa' => 'Intravenosa', 'Intramuscular' => 'Intramuscular', 'Subcutánea' => 'Subcutánea', 'Intradérmica' => 'Intradérmica', 'Ótica' => 'Ótica', 'Nasal' => 'Nasal'), null, array( 'class' => 'form-control', 'id' => 'via_medicamento_agregar_indicacion')) }}
                                    </div>  
                                </div>                                  
                            </div>

                        </div>
                        
                        <div class="col-md-12 ">

                            <div class="col-md-4">
                                <div class="form-group"> 
                                    <label>HORARIO</label>
                                    {{Form::select('horario_medicamento_agregar_indicacion[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker', 'id' => 'horario_medicamento_agregar_indicacion', 'multiple'))}}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="col-md-offset-1"> 
                                    <div class="form-group"> 
                                        <label>FECHA EMISION</label>
                                        {{Form::text('fecha_emision_medicamento_agregar_indicacion', null, array('id' => 'fecha_emision_medicamento_agregar_indicacion', 'class' => 'form-control imfecha-agregar-indicacion'))}}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="col-md-offset-1"> 
                                    <div class="form-group"> 
                                        <label>FECHA VIGENCIA</label>
                                        {{Form::text('fecha_vigencia_medicamento_agregar_indicacion', null, array('id' => 'fecha_vigencia_medicamento_agregar_indicacion', 'class' => 'form-control imfecha-agregar-indicacion'))}}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="indicacion-agregar-indicacion-container">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>FECHA</label>
                                    {{Form::text('fecha_creacion_indicacion_agregar_indicacion', null, array('id' => 'fecha_creacion_indicacion_agregar_indicacion', 'class' => 'form-control dtpfecha-creacion','autocomplete' => 'off'))}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label> INDICACIÓN</label>
                                    {{Form::textarea('descripcion_indicacion_agregar_indicacion', null, array('id' => 'descripcion_indicacion_agregar_indicacion', 'class' => 'form-control', 'rows' => '3', 'style' => 'resize:none'))}}
                                </div>   
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-offset-1"> 
                                    <div class="form-group"> 
                                        <label>HORARIO</label>
                                        {{Form::select('horario_indicacion_agregar_indicacion[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker','id' => 'horario_indicacion_agregar_indicacion' ,'multiple'))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>RESPONSABLE</label>
                                    {{Form::select('responsable_agregar_indicacion', array('1' => 'Enfermera', '2' => 'Tens'), null, array('id' => 'responsable_agregar_indicacion', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                                </div>
                            </div>
                        </div>
                
                    </div>

                    <div class="interconsulta-agregar-indicacion-container">
                        <div class="col-md-12" style="margin-top: 15px;">
                            <div class="col-md-4"> 
                                <div class="form-group">
                                    <label> TIPO</label>
                                    {{Form::text('tipo_interconsulta_agregar_indicacion', null, array('class' => 'form-control', 'id' => 'tipo_interconsulta_agregar_indicacion','autocomplete' => 'off'))}} 
                                </div>   
                            </div>

                        </div>
                    </div>
            
                    <div class="btn-guardar-agregar-indicacion-container">
                        <div class="col-md-12" style="margin-top:15px;">
                                <button type="submit" class="btn btn-success pull-right" id="btn_guardar_agregar_indicacion">Guardar</button>
                        </div>
                    </div>



                    {{ Form::close() }} 
                </div>

                <legend>Listado de indicaciones medicas</legend>
                
                <p>Simbologia de responsables en colores</p>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-control" style="background-color: #0062cc; color: white; font-weight: bold;">Enfermera</div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-control" style="background-color: #A3B5FD; color: black; font-weight: bold;">Tens</div>
                    </div>
                </div>
                <table id="tablePCIndicacionesMedicas" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 20%">INDICACIÓN</th>
                            <th style="width: 20%">PRESCRIPCIÓN</th>
                            <th style="width: 25%">HORARIO DÍA</th>
                            <th style="width: 25%">HORARIO NOCHE</th>
                            {{-- <th>USUARIO</th> --}}
                            <th style="width: 10%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table> 

            </div>

        </div>
    </div>
</div>


<div id="modalTestIndicacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title"></h4>
				<div class="nombreIndicacion"></div>
            </div>
            <div class="modal-body">
                <div class="eliminar-indicacion-container">
                    <div class="col-md-12 pl-0">
                        <div class="col-md-2 p-0">
                            <div class="form-group">
                            <p id="tipo_eliminar"></p>
                            </div>
                        </div>
                        <div class="col-md-4 p-0">
                            <div class="form-group">
                            <p id="eliminar_descripcion"></p>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div id="horas-eliminar-indicacion-container">
                
                </div>
                <div class="form-group col-md-offset-9" id="btn_actualizar_eliminar_indicacion">
                
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
        </div>
    </div>
</div>
<div id="modalModificarIndicacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title"></h4>
				<div class="nombreIndicacion"></div>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

