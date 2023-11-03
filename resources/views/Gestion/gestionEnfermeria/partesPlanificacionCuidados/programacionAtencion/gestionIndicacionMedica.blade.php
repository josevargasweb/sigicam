<script>

    function generarTablaIndicacionesMedicas() {
        tableIndicacionesMedicas = $("#tabledIndicacionesMedicas").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerDatosPlanificacionIndicacionesMedicas/{{ $caso }}" ,
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



    function activarValidacionesIndicacionMedica(){
        $("#form_agregar_indicacion_medica").bootstrapValidator('revalidateField', 'responsable_indicacion_medica');
        $("#form_agregar_indicacion_medica").bootstrapValidator('revalidateField', 'horario_indicacion_medica[]');
    }



    function resetAllIndicacionMedica(){

        //reset medicamento
        $("#horario_indicacion_medica").val('default').selectpicker('deselectAll');
        $("#horario_indicacion_medica").selectpicker('refresh').change();
        $("#responsable_indicacion_medica").val('').change();

    }

    
    function obtenerIndicacionMedica(idIndicacion,tipo) {
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/obtenerIndicacionMedica/"+idIndicacion+"/"+tipo,
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                if(data.info){
                    swalInfo2.fire({
                    title: 'Información',
                    text:data.info,
                    showConfirmButton: false,
                    timer:3000
                });
                $('#tabledIndicacionesMedicas').DataTable().ajax.reload();
                cargarDatosIndicacionMedica();
                }else{
                    var titulo = '';
                    if(tipo == 1){
                        titulo = "Modificar datos indicaciones medicas";
                    }else if(tipo == 2){
                        titulo = "Terminar datos indicaciones medicas";
                    }else if(tipo == 3){
                        titulo = "Eliminar datos indicaciones medicas";
                    }
                    $("#modalModificarIndicacionMedica .modal-title").html(titulo);
                    $("#modalModificarIndicacionMedica .modal-body").html(data.contenido);
                    $("#modalModificarIndicacionMedica").modal();
                }

            },
            error: function(error){
                $("#btnIndicacionMedica").prop("disabled", false);
                console.log(error);
            }
        });	

    }
    
    function obtenerIndicacionEliminarTerminarMedica(idIndicacion,tipo_modificacion) {
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/obtenerIndicacionEliminarTerminarMedica",
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
                $('#modalTestIndicacionMedica').modal('hide');
                swalInfo2.fire({
                    title: 'Información',
                    text:data.exito,
                    showConfirmButton: false,
                    timer:3000
                });
                $('#tabledIndicacionesMedicas').DataTable().ajax.reload();
                cargarDatosIndicacionMedica();
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
                    boton_eliminarTerminar = '<a type="button" class="btn btn-'+color_boton+'"  onclick="eliminarFilaIndicacionMedica('+data.contenido[0].id+',2)" style="margin-top:15%;">Terminar Todo</a>';
                }else if(tipo_modificacion == 3){
                    color_boton = 'danger';
                    titulo = "Eliminar datos indicaciones medicas";
                    signo = 'X';
                    boton_eliminarTerminar = '<a type="button" class="btn btn-'+color_boton+'" onclick="eliminarFilaIndicacionMedica('+data.contenido[0].id+',3)" style="margin-top:15%;">Eliminar Todo</a>';
                }
                var datoHora = '';
                arr_horarios.forEach(function(hora) {
                    datoHora += '<div  class="'+color+'"><div class=""><button class="btn btn-'+color_boton+' botonCerrar" type="button"  onclick="eliminarHoraIndicacionMedica('+hora+','+data.contenido[0].id+','+tipo_modificacion+')">'+signo+'</button><div class="valorInterno">'+hora+'</div></div></div>';
                    }); 
                var tipo_formulario = data.contenido[0].tipo;
                
                
                $('#modalTestIndicacionMedica').modal('show');
                $('#modalTestIndicacionMedica .modal-title').html(titulo);
                document.getElementById("tipo_eliminar_medica").innerHTML = tipo_formulario+":";
                document.getElementById("horas-eliminar-indicacion-medica-container").innerHTML = datoHora;
                document.getElementById('btn_actualizar_eliminar_indicacion_medica').innerHTML = boton_eliminarTerminar;
               
             
                }
            
            },
            error: function(error){
                $("#btnIndicacionMedica").prop("disabled", false);
                console.log(error);
            }
        });	

    }


    function eliminarHoraIndicacionMedica(hora,idIndicacion,tipo_modificacion){
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarTerminarPCIndicacionHoraMedica",
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
                                    obtenerIndicacionEliminarTerminarMedica(res.nueva_id,tipo_modificacion);
                                    //actualizar tabla
                                    tableIndicacionesMedicas.api().ajax.reload();
                                    }, 2000)
                                },
                            });	
                            }else if(res.error){
                                $('#modalTestIndicacionMedica').modal('hide');
                                swalInfo2.fire({
                                    title: 'Información',
                                    text:res.error,
                                    showConfirmButton: false,
                                    timer:3000
                                });
                                $('#tabledIndicacionesMedicas').DataTable().ajax.reload();
                            }                         

                        },
                        error: function(xhr, status, error){
                            var error_json = JSON.parse(xhr.responseText);
                            	swalError.fire({
                                title: 'Error',
                                text:error_json.error
                                });
                                cargarTablaIndicacionMedica();
                                cargarDatosIndicacionMedica();
                        }
                    });	
            }
        });

    }

    function eliminarFilaIndicacionMedica (idIndicacion,tipo_modificacion) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarTerminarPCIndicacionMedica",
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
                            tableIndicacionesMedicas.api().ajax.reload();
                            if(typeof tipo_2 === 'undefined' || tipo_2 != ''){
                                $("#modalTestIndicacionMedica").modal('hide');
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
                                    cargarTablaIndicacionMedica();
                                    cargarDatosIndicacionMedica();
                                }
                                });
						
                        }
                    });				
                }				
            }
        });  
        	
    }

    function cargarTablaIndicacionMedica(){
        if (typeof tableIndicacionesMedicas !== 'undefined' && tableIndicacionesMedicas !== null) {
            tableIndicacionesMedicas.api().ajax.reload(false);
        }else{
            generarTablaIndicacionesMedicas();
        }
    }

    function cargarDatosIndicacionMedica(){
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/cargarDatosIndicacionMedica/"+{{$caso}},
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){        
                if(data.ultimaIndicacion != null){
                    console.log(data)
                    $('.no-existen-datos-indicacion').hide();
                    $('.medico-agregar-indicacion-container').hide();
                    $('.sueros-indicacion-medica').hide();
                    $('.agregar-indicacion-container').show();
                    $('.listado_indicaciones_medicas_container').show();
                    $('#id_indicacion_medica').val(data.ultimaIndicacion.id);

                   

                    var options_indicacion_medica = "";

                    if(data.ultimaIndicacion.horas_signos_vitales != null){
                        options_indicacion_medica  += "<option value='Control de signos vitales'>Control de signos vitales</option>";
                        $('#signos_horas_indicacion_medica').text(data.ultimaIndicacion.horas_signos_vitales);
                        if(data.ultimaIndicacion.detalle_signos_vitales != null){
                            $('#signos_comentario_indicacion_medica').text(data.ultimaIndicacion.detalle_signos_vitales);
                        }else{
                            $('#signos_comentario_indicacion_medica').text("Sin Informacion");
                        }
                    }
                    if(data.ultimaIndicacion.horas_hemoglucotest != null){
                        options_indicacion_medica  += "<option value='Control de hemoglucotest'>Control de hemoglucotest</option>";
                        $('#hemoglucotest_horas_indicacion_medica').text(data.ultimaIndicacion.horas_hemoglucotest);
                        if(data.ultimaIndicacion.detalle_hemoglucotest != null){
                            $('#hemoglucotest_comentario_indicacion_medica').text(data.ultimaIndicacion.detalle_hemoglucotest);
                        }else{
                            $('#hemoglucotest_comentario_indicacion_medica').text("Sin Informacion");
                        }
                    }

                    if(data.ultimaIndicacion.sueros == true){
                        if(data.sueros){
                            sueros = data.sueros;
                            options_indicacion_medica_suero = "";
                            if(sueros.length !== 0){
                               sueros.forEach(function(suero){
                                   options_indicacion_medica_suero  += "<option value='"+suero['id']+"'>"+suero['nombre_unidad']+"</option>";
                               });
                               $('#sueros_agregar_indicacion_medica').html(options_indicacion_medica_suero);
                               
                               $('#sueros_agregar_indicacion_medica').val(data.ultimaIndicacion.suero).change();
                            }
                            ml_suero = "Sin información";
                            if (data.ultimaIndicacion.mililitro != null && data.ultimaIndicacion.mililitro != "null") {
                                ml_suero = data.ultimaIndicacion.mililitro ;
                            }
                            $('#mililitro_suero_indicacion_medica').text(ml_suero);
                        }
                        options_indicacion_medica  += "<option value='Suero'>Suero</option>";
                    }
                    if(data.farmacos.length !== 0){
                      var farmacos = data.farmacos;
                        options_indicacion_medica_farmacos = "";
                       
                        options_indicacion_medica  += "<option value='Farmacos'>Farmacos</option>";

                        data.farmacos.forEach(function(farmaco){
                            options_indicacion_medica_farmacos  += "<option value='"+farmaco['id']+"'>"+farmaco['nombre_unidad']+"</option>";
                        });
                        $('#farmacos_agregar_indicacion_medica').html(options_indicacion_medica_farmacos);
                        
                        $('#farmacos_agregar_indicacion_medica').val(data.ultimaIndicacion.suero).change();

                        intervalo_farmaco = "Sin información";
                        if (data.farmacos[0].intervalo_farmaco != null && data.farmacos[0].intervalo_farmaco != "null") {
                            intervalo_farmaco = data.farmacos[0].intervalo_farmaco;
                        }
                        $('#via_indicacion_medica').text(data.farmacos[0].via_administracion);
                        $('#intervalo_indicacion_medica').text(intervalo_farmaco);
                    }

                    $('#tipo_agregar_indicacion_medica').html(options_indicacion_medica);

                    if( $('#tipo_agregar_indicacion_medica').val() == 'Farmacos'){
                        $('.sueros-indicacion-medica').hide();
                        $('.medico-agregar-indicacion-container').show();
                        $(".farmacos-indicacion-medica").show();
                        $('.hora-hemoglucotest-indicacion-medica').hide();
                        $('.hora-signos-indicacion-medica').hide();


                    }else if($('#tipo_agregar_indicacion_medica').val() == 'Suero'){
                        $('.sueros-indicacion-medica').show();
                        $('.medico-agregar-indicacion-container').hide();
                        $(".farmacos-indicacion-medica").hide();
                        $('.hora-hemoglucotest-indicacion-medica').hide();
                        $('.hora-signos-indicacion-medica').hide();
                    }
                    else if($('#tipo_agregar_indicacion_medica').val() == 'Control de signos vitales'){
                        $('.sueros-indicacion-medica').hide();
                        $('.hora-signos-indicacion-medica').show();
                        $('.medico-agregar-indicacion-container').hide();
                        $(".farmacos-indicacion-medica").hide();
                        $('.hora-hemoglucotest-indicacion-medica').hide();
                    }else if($('#tipo_agregar_indicacion_medica').val() == 'Control de hemoglucotest'){
                        $('.sueros-indicacion-medica').hide();
                        $('.hora-hemoglucotest-indicacion-medica').show();
                        $('.medico-agregar-indicacion-container').hide();
                        $(".farmacos-indicacion-medica").hide();
                        $('.hora-signos-indicacion-medica').hide();
                    }

                    cantidad_indicaciones = document.getElementById("tipo_agregar_indicacion_medica").length;

                    if(cantidad_indicaciones == 0){
                        $('.no-existen-datos-indicacion').show();
                        $('.medico-agregar-indicacion-container').hide();
                        $(".farmacos-indicacion-medica").hide();
                        $('.agregar-indicacion-container').hide();
                        $('.listado_indicaciones_medicas_container').hide();
                        $('.sueros-indicacion-medica').hide();
                        $('.hora-hemoglucotest-indicacion-medica').hide();
                        $('.hora-signos-indicacion-medica').hide();
                        $('#tipo_agregar_indicacion_medica').html("");
                        $('#farmacos_agregar_indicacion_medica').html("");
                        $('#via_indicacion_medica').val("");
                        $('#intervalo_indicacion_medica').val("");
                    }
                }else{
                    $('.no-existen-datos-indicacion').show();
                    $('.medico-agregar-indicacion-container').hide();
                    $(".farmacos-indicacion-medica").hide();
                    $('.agregar-indicacion-container').hide();
                    $('.listado_indicaciones_medicas_container').hide();
                    $('.sueros-indicacion-medica').hide();
                    $('.hora-hemoglucotest-indicacion-medica').hide();
                    $('.hora-signos-indicacion-medica').hide();
                    $('#tipo_agregar_indicacion_medica').html("");
                    $('#farmacos_agregar_indicacion_medica').html("");
                    $('#via_indicacion_medica').val("");
                    $('#intervalo_indicacion_medica').val("");
                }
            },
            error: function(error){
                console.log(error);
            },
        });		
    }

    $(document).ready(function() {


        tableIndicacionesMedicas = null;

        $("#horario_indicacion_medica").selectpicker();

        $("#form_agregar_indicacion_medica").bootstrapValidator({
            excluded: [':disabled'],
            fields: {  
                'horario_indicacion_medica[]':{
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
                'responsable_indicacion_medica':{
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
                        $("#btn_guardar_agregar_indicacion_medica").prop("disabled", true);				
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/addDatosIndicacionMedica",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                //reset
                                resetAllIndicacionMedica();
                                activarValidacionesIndicacionMedica();
                                if(data.exito){
                                    swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                    });
                                    
                                    /* actualizar tabla  */
                                    tableIndicacionesMedicas.api().ajax.reload();
                                }

                                if(data.error){
                                    swalError.fire({
                                        title: 'Error',
                                        text: data.error
                                    }).then(function(result) {
                                        cargarTablaIndicacionMedica();
                                        cargarDatosIndicacionMedica();
                                    });
                                }

                                

                            },
                            error: function(xhr, status, error){
                                var error_json = JSON.parse(xhr.responseText);
                                            swalError.fire({
                                            title: 'Error',
                                            text:error_json.error
                                            });
                                cargarTablaIndicacionMedica();
                                cargarDatosIndicacionMedica();
                                activarValidacionesIndicacionMedica();
                            },
                            complete: function (){
                                $("#btn_guardar_agregar_indicacion_medica").prop("disabled", false);
                            }
                        });				
                    }			
                }
            });  
        });

        activarValidacionesIndicacionMedica();


        $( "#planificacion" ).click(function() {
            cargarTablaIndicacionMedica();
            cargarDatosIndicacionMedica();
        });

        $( "#pAt" ).click(function() {
            cargarTablaIndicacionMedica();
            cargarDatosIndicacionMedica();
        });

        //* TIPO INDICACIÓN */
        $(".tipo-agregar-indicacion-container").on('change', '#tipo_agregar_indicacion_medica', function(){   
            var tipo_2 = $(this).val();

            if (tipo_2 === 'Farmacos'){
                //reset
                resetAllIndicacionMedica();

                //habilitar seccion farmaco
                $(".medico-agregar-indicacion-container").show();
                $(".farmacos-indicacion-medica").show();
                $('.sueros-indicacion-medica').hide();
                $('.hora-signos-indicacion-medica').hide();
                $('.hora-hemoglucotest-indicacion-medica').hide();
                activarValidacionesIndicacionMedica();


            }else if (tipo_2 === 'Control de signos vitales'){
                 //reset
                 resetAllIndicacionMedica();
                
                //habilitar seccion farmaco
                $(".medico-agregar-indicacion-container").hide();
                $(".farmacos-indicacion-medica").hide();
                $('.hora-signos-indicacion-medica').show();
                $('.hora-hemoglucotest-indicacion-medica').hide();
                $('.sueros-indicacion-medica').hide();

            }else if (tipo_2 === 'Control de hemoglucotest'){
                //reset
                resetAllIndicacionMedica();
                
                //habilitar seccion farmaco
                $(".medico-agregar-indicacion-container").hide();
                $(".farmacos-indicacion-medica").hide();
                $('.hora-signos-indicacion-medica').hide();
                $('.hora-hemoglucotest-indicacion-medica').show();
                $('.sueros-indicacion-medica').hide();

            }else if (tipo_2 === 'Suero'){
                //reset
                resetAllIndicacionMedica();
                
                //habilitar seccion farmaco
                $(".medico-agregar-indicacion-container").hide();
                $(".farmacos-indicacion-medica").hide();
                $('.hora-signos-indicacion-medica').hide();
                $('.hora-hemoglucotest-indicacion-medica').hide();
                $('.sueros-indicacion-medica').show();
            }


        });
        
        $(".farmacos-indicacion-medica").on('change', '#farmacos_agregar_indicacion_medica', function(){   
            var tipoFarmaco = $(this).val();
            var id_indicacion_medica = $('#id_indicacion_medica').val();
            var id_caso_agregar_indicacion_medica = $('#id_caso_agregar_indicacion_medica').val();
                $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/obtenerDatosIndicacionFarmacos/"+id_caso_agregar_indicacion_medica+"/"+tipoFarmaco+"/"+id_indicacion_medica,
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "get",
                        success: function(data){
                            if(data.via_administracion){
                               $('#via_indicacion_medica').text(data.via_administracion) ;
                            }
                            if(data.intervalo){
                                $('#intervalo_indicacion_medica').text(data.intervalo) ;
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });	
        });

        $("#tipo_agregar_indicacion_medica").trigger("change");

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
               
                <div class="no-existen-datos-indicacion">
                    <div class="alert alert-warning" role="alert">
                        <p style="text-align: center">
                            AVISO: NO EXISTEN INDICACIÓNES MEDICAS.
                        </p>
                    </div>
                </div>
                <div class="agregar-indicacion-container">
                    {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_agregar_indicacion_medica')) }}
                    {{ Form::hidden ('id_caso_agregar_indicacion_medica', $caso, array('id' => 'id_caso_agregar_indicacion_medica') )}}
                    {{ Form::hidden ('id_indicacion_medica','', array('id' => 'id_indicacion_medica') )}}


                    <div class="tipo-agregar-indicacion-container">
                        <div class="col-md-12">
                            <div class="col-md-4"> 
                                <div class="form-group">
                                    {{Form::label('', "INGRESO DE", array( ))}} <br>
                                    {{Form::select('tipo_agregar_indicacion_medica', array(),null ,array('class' => 'form-control', 'id' => 'tipo_agregar_indicacion_medica'))}}
                                </div>
                            </div>

                            <div class="col-md-8 sueros-indicacion-medica">
                                <div class="col-md-6" style="padding-left: 4px;padding-right: 22px;">
                                    <div class="col-md-offset-1"> 
                                        <div class="form-group"> 
                                            <label>Sueros</label>
                                            {{Form::select('sueros_agregar_indicacion_medica', array(),null ,array('class' => 'form-control', 'id' => 'sueros_agregar_indicacion_medica'))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-offset-1"> 
                                        <div class="form-group"> 
                                            <label>Mililitro (ml) Total</label>
                                            <p id="mililitro_suero_indicacion_medica"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 hora-signos-indicacion-medica">
                                <div class="col-md-3">
                                    <div class="col-md-offset-1"> 
                                        <div class="form-group"> 
                                            <label>Cada Cuantas Horas</label>
                                            <p id="signos_horas_indicacion_medica"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="col-md-offset-1"> 
                                        <div class="form-group"> 
                                            <label>Comentario</label>
                                            <p id="signos_comentario_indicacion_medica"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 hora-hemoglucotest-indicacion-medica">
                                <div class="col-md-3">
                                    <div class="col-md-offset-1"> 
                                        <div class="form-group"> 
                                            <label>Cada Cuantas Horas</label>
                                            <p id="hemoglucotest_horas_indicacion_medica"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="col-md-offset-1"> 
                                        <div class="form-group"> 
                                            <label>Comentario</label>
                                            <p id="hemoglucotest_comentario_indicacion_medica"></p>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-4 farmacos-indicacion-medica">
                                <div class="col-md-offset-1"> 
                                    <div class="form-group"> 
                                        <label for="">LISTADO DE FARMACOS</label>
                                        {{Form::select('farmacos_agregar_indicacion_medica', array(),null ,array('class' => 'form-control', 'id' => 'farmacos_agregar_indicacion_medica'))}}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-4 pl-0">
                                    <div class="form-group"> 
                                        <label>HORARIO</label>
                                        {{Form::select('horario_indicacion_medica[]', [ '00'=> '00', '01' => '01', '02' => '02', '03' => '03','04'=> '04', '05' => '05', '06' => '06', '07' => '07', '08'=> '08', '09' => '09', '10' => '10', '11' => '11','12'=> '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19'=> '19', '20' => '20', '21' => '21', '22' => '22','23'=> '23'],null ,array('class' => 'form-control selectpicker', 'id' => 'horario_indicacion_medica', 'multiple'))}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-offset-1">
                                        <div class="form-group">
                                            <label>RESPONSABLE</label>
                                            {{Form::select('responsable_indicacion_medica', array('1' => 'Enfermera', '2' => 'Tens'), null, array('id' => 'responsable_indicacion_medica', 'class' => 'form-control', 'placeholder' => 'seleccione un responsable'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> 


                    <div class="medico-agregar-indicacion-container">
                        <div class="col-md-12 ">
                            <div class="col-md-4"> 
                                <div class="form-group">
                                    {{Form::label('', "VÍA DE ADMINISTRACIÓN:", array( ))}}
                                   <p id="via_indicacion_medica"></p>
                                </div>  
                            </div>
                            <div class="col-md-2"> 
                                <div class="col-md-offset-1"> 
                                    <div class="form-group"> 
                                        <label>INTERVALO</label>
                                       <p id="intervalo_indicacion_medica"></p> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <div class="btn-guardar-agregar-indicacion-container">
                        <div class="col-md-12" style="margin-top:15px;">
                                <button type="submit" class="btn btn-success pull-right" id="btn_guardar_agregar_indicacion_medica">Guardar</button>
                        </div>
                    </div>



                    {{ Form::close() }} 
                </div>

                <div class="listado_indicaciones_medicas_container">
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

                    <table id="tabledIndicacionesMedicas" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 20%">INDICACIÓN</th>
                                <th style="width: 25%">HORARIO DÍA</th>
                                <th style="width: 15%">HORARIO NOCHE</th>
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
</div>


<div id="modalTestIndicacionMedica" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
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
                            <p id="tipo_eliminar_medica"></p>
                            </div>
                        </div>
                        <div class="col-md-4 p-0">
                            <div class="form-group">
                            </div>
                        </div>
                    </div>
                </div>
    
                <div id="horas-eliminar-indicacion-medica-container">
                
                </div>
                <div class="form-group col-md-offset-9" id="btn_actualizar_eliminar_indicacion_medica">
                
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
        </div>
    </div>
</div>
<div id="modalModificarIndicacionMedica" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
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

