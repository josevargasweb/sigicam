<script>


    /* Editar fila */
    function modificarLaboratorio(idSolicitud, idFila) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarLaboratorio",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            estadoLab: $("#estadoLab"+idFila).val(),
                            tomado: $("#tomado"+idFila).val()
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
                                tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
                            }

                            if (data.error) {
                              swalError.fire({
						title: 'Error',
						text:data.error
						}).then(function(result) {
						if (result.isDenied) {
							  location . reload();

						}
						})
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
                }
            }
        });  

    }


    /* eliminar la fila de datatable */
    function eliminarLaboratorio(idSolicitud) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarLaboratorio",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
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
                                tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
                            }

                            if (data.error) {
                              swalError.fire({
						title: 'Error',
						text:data.error
						}).then(function(result) {
						if (result.isDenied) {
							  location . reload();

						}
						})
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
                }
            }
        }); 
    }
    function validarLaboratorio() {
        /* añadir cualquier script que desees ejecutar dentro del datatable */

        /* Calcular suma de valores */
        /*  $(".calcularTotal").keyup(function(){
            var idFila=$(this).data("id");
            dia = ($("#tDia"+idFila).val())?parseInt($("#tDia"+idFila).val()):0;
            noche = ($("#tNoche"+idFila).val())?parseInt($("#tNoche"+idFila).val()):0;
            $("#tTotal"+idFila).val(dia+noche);
        }); */
        /* activar el datetimepicker de la tabla */
        $('.dPELaboratorio').datetimepicker({
            format: 'LT'
        });

        $('.fechaProgramada').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        });
    }

    function generarTablaLaboratorio() {
        tableLaboratorioActual = $("#examenLaboratorio").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerExamenesLaboratorio/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": validarLaboratorio
        });
    }

    function cargarVistaExamenLaboratorio(){
        if (typeof tableLaboratorioActual !== 'undefined') {
                tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
        }else{
            generarTablaLaboratorio();
        }
    }

    $(document).ready(function() {
        $(".fechaProgramada").hide();
        $(".fechap").hide();

        $("#hojaDeEnfermeria").click(function(){
            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            var tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "4b"){
                console.log("tabRdcx examen laboratorio: ", tabRdc);
                cargarVistaExamenLaboratorio();
            }
        });

        $( "#4ab" ).click(function() {
            cargarVistaExamenLaboratorio();
        });

        $("#HEExamenLaboratorio").bootstrapValidator({
            excluded: ':disabled',
            fields: {
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitarLaboratorio").prop("disabled", true);
            fechatoma = $(".dPELaboratorio").val();
            evt.preventDefault(evt);
            var $form = $(evt.target);
            console.log($(".dPELaboratorio").val());
            if(fechatoma == ""){
            swalInfo2.fire({
					title: 'Información',
					text:"La fecha para la toma de examen no ha sido asignada!"
					});
            $("#btnSolicitarLaboratorio").prop("disabled", false);
          }else{
            bootbox.confirm({
                    message: "<h4>¿Está seguro de ingresar este examen?</h4>",
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
                            url: "{{URL::to('/gestionEnfermeria')}}/addExamenLaboratorio",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnSolicitarLaboratorio").prop("disabled", false);

                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
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
                                $("#btnSolicitarLaboratorio").prop("disabled", false);
                                console.log(error);
                                tableLaboratorioActual.api().ajax.reload(validarLaboratorio, false);
                            }
                        });
                    }
                }
            }); 
          }
        });

        $(function () {
            $('.dPELaboratorio').datetimepicker({
                format: 'LT'
            });

            $('.fechaProgramada').datetimepicker({
                format: "DD-MM-YYYY HH:mm",
                locale: 'es'
            });
        });

        $('.estadoLab').change(function(){
          	var value=$(".estadoLab").val();
            console.log(value);
          if(value != 2){
            $(".fechaProgramada").hide();
            $(".fechap").hide();
          }else{
            $(".fechaProgramada").show();
            $(".fechap").show();
          }

        });

    });


</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEExamenLaboratorio')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
{{-- {{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormEnfermeria') )}} --}}

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>EXAMENES LABORATORIO</h4>
            </div>

            <div class="panel-body" id="examenesLab">

                <legend>Ingresar nuevos examenes</legend>

                <div class="col-md-12">
                    <div class="col-md-3"> SOLICITADOS</div>
                    <div class="col-md-2"> TOMADOS</div>
                </div>

                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('solicitado', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematológicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('tomado', null, array( 'class' => 'dPELaboratorio form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="fechap col-md-3"> <div class="form-group"> {{Form::text('fechaProgramada', null, array( 'class' => 'fechaProgramada form-control', 'placeholder' => 'DD-MM-YYYY HH:mm'))}} </div> </div>
                    <div class="col-md-2">
                        {{-- <button type="button" class="btn-xs btn-danger" onclick="eliminar(this)">Eliminar</button> --}}
                        <button type="submit" class="btn btn-primary" id="btnSolicitarLaboratorio">Guardar</button>
                    </div>
                </div>

                <legend>Listado de examenes</legend>
                <table id="examenLaboratorio" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SOLICITADOS</th>
                            <th>TOMADOS</th>
                            <th style="text-align: center;">RESULTADOS PENDIENTES</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


{{ Form::close() }}
