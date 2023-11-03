<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 5/15/15
 * Time: 1:52 PM
 */

?>

<script>

    /* eliminar la fila de datatable */
    function eliminarEEP(idExamen) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de eliminar este examen?</h4>",				
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
                        url: "{{URL::to('/')}}/eliminarEEP",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idExamen
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnSolicitarImagen").prop("disabled", false);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                /* aactualizar tabla con pendientes */
                                tableEEP.api().ajax.reload();
                            }

                            if (data.error) {
                              swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    //location.href="/derivaciones/$tipo";
                                    location.reload();
                                }
                                });
                            }
                        },
                        error: function(error){
                            $("#btnSolicitarImagen").prop("disabled", false);
                            console.log(error);
                        }
                    });				
                }else{
                    tableEEP.api().ajax.reload();
                }				
            }
        }); 
    }

    /* Editar fila */
    function modificarEEP(idExamen, idFila) {
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
                        url: "{{URL::to('/')}}/modificarExamenImagen",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idExamen,
                            examen: $("#nombreEEP"+idFila).val(),
                            pendiente: $("#pendiente"+idFila).val(),
                            tipo: $("#tipo"+idFila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                /* aactualizar tabla con pendientes */
                                tableEEP.api().ajax.reload();
                                /* Si estamos en lista de estudios esta tiene que recarfgar la tabla de examenes tambien */
                                if (typeof tablaListaEEP != "undefined") {
                                    tablaListaEEP.api().ajax.reload();
                                }
                            }

                            if (data.error) {
                              swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    location.reload();
                                    //location.href="/derivaciones/$tipo";
                                }
                                });
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });				
                }else{
                    tableEEP.api().ajax.reload();
                }				
            }
        });  
        	
    }

    function generarTablaEEP() {
        tableEEP = $("#historialEEP").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerEEP/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": function () {
                /* añadir cualquier script que desees ejecutar dentro del datatable */

                /* Sumar pendientes y modificar valor en info paciente */
                var tPendientes = 0;
                $('#historialEEP').DataTable().rows().data().each(function(valor, index){
                    @if(Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS
                    && Session::get("usuario")->tipo !== TipoUsuario::CENSO && Session::get("usuario")->tipo)
                        tPendientes += ($("#pendiente"+index).val() == 'true' )? 1 : 0;
                    @else
                        tPendientes += (valor[1] == 'Si' )? 1 : 0;
                    @endif
                });
                $("#numPendientes").html(tPendientes);

                /* falta la parte cuando no tiene pendientes */
                
            }
        });
    }

    /* var _ingresarDiagnostico = function (form) {
        
        $.ajax({
            url: "{{ URL::to('/')}}/ingresarExamen",
            data: form.serialize(),
            type: "post",
            dataType: "json",
            success: function (data) {
                $("#modalVerExamenes .modal-body").html(data.contenido);
                $("#examen_id").val("");
                $("#nuevo-examen").val("");
                $("#especialista").val("");
                $('#pendiente_examen').prop('checked', false);
            },
            error: function (error) {

            }
        });
    } */

    $(function () {
        generarTablaEEP();

        $("#modalVerExamenes").css("overflow", "auto"); 

        $("#formIngresarExamen").bootstrapValidator({
            excluded: ':disabled',
            fields: {                  
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnGuardarEEP").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({				
                    message: "<h4>¿Está seguro de ingresar esta información?</h4>",				
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
                    swalCargando.fire({});							
                    if(result){					
                        $.ajax({
                            url: "{{URL::to('/')}}/ingresarExamen",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                swalCargando.close();
                                Swal.hideLoading();
                                if (data.exito) {

                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    tableEEP.api().ajax.reload();
                                    /* Si estamos en lista de estudios esta tiene que recarfgar la tabla de examenes tambien */
                                    if (typeof tablaListaEEP != "undefined") {
                                        tablaListaEEP.api().ajax.reload();
                                    }
                                }

                                if (data.error) {

                                   swalError.fire({
                                title: 'Error',
                                text:data.error
                                }).then(function(result) {
                                if (result.isDenied) {
                                    location . reload();
                                }
                                });
                                }
                            },
                            error: function(error){
                                swalCargando.close();
                                Swal.hideLoading();
                                console.log(error);
                            }
                        });				
                    }			
                }
            });  
            $("#btnGuardarEEP").prop("disabled", false);
        });

        /* $(".editar_examen").click(function(){
            $("#btnCambiarCategoria").val("Editar");
            $("#nombreForm").html("Editar"); 
            $('#AgregarNuevo').attr("hidden",false);

            examen = $(this).data("examen");
            //console.log(examen);
            $("#examen_id").val(examen.id);
            $("#nuevo-examen").val(examen.examen);
            $('#pendiente_examen').val($("#"+examen.id).val());
            $("#tipo_examen").val(examen.tipo);
            $("#especialista").val(examen.medico_examen);

            if(examen.tipo == "espera evaluación especialista" ){
                $("#divEspecialista").show();
            }
            else{
                $("#divEspecialista").hide();
            }

            
        }); */


        $("#tipo_examen").change(function(){
            if($(this).val() == "espera evaluación especialista" ){
                console.log("asd");
                $("#divEspecialista").show();
            }
            else{
                $("#divEspecialista").hide();
            }
        });

        $(".cambioPendiente").change(function(){ 
            console.log($(this).data("examen").id); 
            console.log($(this).val()); 
            /* var id = $(this).data("examen").id; */ 
            /* console.log($("#"+id).html()); */ 
            /* if ($(this).val() == "false" ) { 
                $("#"+id).html("No"); 
            }else{ 
                $("#"+id).html("Si"); 
            } */ 
             
            $.ajax({ 
                url: "{{ URL::to('/')}}/updatePendiente", 
                data: { 
                    estado : $(this).val(), 
                    id_examen : $(this).data("examen").id 
                }, 
                type: "post", 
                dataType: "json", 
                success: function (data) { 
                    console.log("data",data); 
                }, 
                error: function (error) { 
                } 
            }); 
 
        });


    });
</script>

<fieldset>
    @if(!$some && Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS
        && Session::get("usuario")->tipo !== TipoUsuario::CENSO )
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'formIngresarExamen')) }}
            {{ Form::hidden('caso', $caso, array('class' => '')) }}
            <div class="col-md-12">
                <div id="divEspecialista" class="form-group" hidden>
                    <label>Especialista</label>
                    {{ Form::text('especialista', null, array("class"=>"form-control", "id"=>"especialista")) }}

                </div>

                <legend>Ingresar nuevos exámenes, estudios o procedimientos</legend>
                <div class="col-md-12">
                    <div class="col-md-3"> EXÁMENES / ESTUDIOS / PROCEDIMIENTOS</div>
                    <div class="col-md-3"> TIPO</div>
                    <div class="col-md-3"> PENDIENTE</div>
                </div>
                <br>
                <div class="examenImagen" >
                    <div class="col-md-12 moduloExamen">
                        
                        <div class="col-md-3"> <div class="form-group"> {{Form::text('nuevo-examen', null, array( 'class' => 'form-control')) }} </div> </div>
                        <div class="col-md-3"> <div class="form-group"> {{Form::select('tipo_examen', $tipos_examen,null, array('class' => 'form-control'))}} </div> </div>
                        <div class="col-md-3"> <div class="form-group"> {{Form::select('pendiente_examen', array("true" => "Si", "false" => "No"), "true", array( 'class' => 'form-control')) }} </div> </div>
                        <div class="col-md-2"> 
                            <button type="submit" class="btn btn-primary" id="btnGuardarEEP">Guardar</button>
                        </div>   
                    </div>
                </div >
            </div>
        {{ Form::close() }}
    @endif

    
    <div class="col-md-12">
        <legend>Historial</legend>
        <table id="historialEEP" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>EXÁMENES / ESTUDIOS / PROCEDIMIENTOS</th>
                    <th>PENDIENTE</th>
                    <th>TIPO</th>
                    <th>USUARIO</th>
                    @if(!$some && Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::ESTADISTICAS
                    && Session::get("usuario")->tipo !== TipoUsuario::CENSO && Session::get("usuario")->tipo)
                        <th>OPCIONES</th>
                    @endif
                </tr>
            </thead>
            <tbody>
    
            </tbody>
        </table>
    </div>
    

    

</fieldset>




    
