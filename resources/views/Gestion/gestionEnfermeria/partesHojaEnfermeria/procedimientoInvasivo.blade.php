<script>

    function funcionesTablaProcedimientosInvasivos() {
        $('.dPProcInvasivo').datetimepicker({
            format: 'LT'
        });
    }

    function generarTablaProcedimientosInvasivos(){
        tablePInvasivos = $("#tablaProcedimientosInvasivos").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerProcedimientosInvasivos/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": funcionesTablaProcedimientosInvasivos
        });
    }

    function eliminarProcedimiento(idSolicitud) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de eliminar este registro?</h4>",				
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarProcedimientoInvasivo",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            console.log(data);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                /* aactualizar tabla con pendientes */
                                tablePInvasivos.api().ajax.reload(funcionesTablaProcedimientosInvasivos,false);
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
                            console.log(error);
                        }
                    });				
                }else{
                    tablePInvasivos.api().ajax.reload(funcionesTablaProcedimientosInvasivos,false);
                }				
            }
        }); 
    }
                    
    function finalizarProcedimiento(idSolicitud) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de finalizar este antibiótico?</h4>",				
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
                console.log('This was logged in the callback: ' + result);					
                if(result){					
                    $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/finalizarProcedimientoInvasivo",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnGuardarProcedimiento").prop("disabled", false);
                            console.log(data);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tablePInvasivos.api().ajax.reload(funcionesTablaProcedimientosInvasivos,false);
                                $("#btnGuardarProcedimiento").prop("disabled", true);
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
                            $("#btnGuardarProcedimiento").prop("disabled", false);
                            console.log(error);
                        }
                    });				
                }else{
                    tablePInvasivos.api().ajax.reload(funcionesTablaProcedimientosInvasivos,false);
                }				
            }
        }); 
    }
            
    $(document).ready(function() {

        $("#13ab").click(function() {
            if (typeof tablePInvasivos !== 'undefined') {
                tablePInvasivos.api().ajax.reload(funcionesTablaProcedimientosInvasivos,false);
            }else{
                generarTablaProcedimientosInvasivos();
            }
        });

        $("#btnGuardarProcedimiento").prop("disabled", true);
        $("#numeroP").prop("disabled", true);
        $("#fechaP").prop("disabled", true);
        $("#estadoP").prop("disabled", true);

        $(".selectTipoProc").on('change', function() {
            $("#numeroP").val('');
            $("#fechaP").val('');
            let tipoP = $(".selectTipoProc").val();
            if(tipoP == ''){
                $("#numeroP").prop("disabled", true);
                $("#fechaP").prop("disabled", true);
                $("#estadoP").prop("disabled", true);
                $("#btnGuardarProcedimiento").prop("disabled", true);
            }else{
                $("#numeroP").prop("disabled", false);
                $("#fechaP").prop("disabled", false);
                $("#estadoP").prop("disabled", false);
                // $("#btnGuardarProcedimiento").prop("disabled", true);
            }
        });



        $("#HEProcedimientosInvasivos").bootstrapValidator({
            excluded: ':disabled',
            fields: {  
                'fechaProcedimiento': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar la fecha en que fue realizado el procedimiento'
                        }
                    }
                },
                'numero': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar el número'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            // data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnGuardarProcedimiento").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
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
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarProcedimientoInvasivo",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnGuardarProcedimiento").prop("disabled", false);
                                console.log(data);
                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    $("#HEProcedimientosInvasivos").trigger("reset");
                                    $("#numeroP").prop("disabled", true);
                                    $("#fechaP").prop("disabled", true);
                                    $("#estadoP").prop("disabled", true);
                                    tablePInvasivos.api().ajax.reload(funcionesTablaProcedimientosInvasivos,false);
                                    $("#btnGuardarProcedimiento").prop("disabled", true);
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
                                $("#btnGuardarProcedimiento").prop("disabled", false);
                                console.log(error);
                            }
                        });				
                    }				
                }            
            });
            $("#btnGuardarProcedimiento").prop("disabled", false);  
        });

        $("#numeroP").on('keyup change', function(e) {
            $('#HEProcedimientosInvasivos').bootstrapValidator('revalidateField', $(this));
        });

        $('.dPProcedimientos').datetimepicker({
            // format: 'LT',
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
        }).on('dp.change', function (e) { 
            $('#HEProcedimientosInvasivos').bootstrapValidator('revalidateField', $(this));
        });
    });

</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEProcedimientosInvasivos')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
{{-- {{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormEnfermeria') )}} --}}
    
<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>CONTROL PROCEDIMIENTOS INVASIVOS Y ESTADÍA</h4>
            </div>

            <div class="panel-body">
                <legend>Ingresar Procedimientos Invasivos</legend>
                <div class="col-md-12">
                    <div class="col-md-2">TIPO</div>
                    <div class="col-md-2">NÚMERO</div>
                    <div class="col-md-2">ESTADO</div>
                    <div class="col-md-2">HORARIO</div>
                    {{-- <div class="col-md-2"></div>
                    <div class="col-md-2"></div> --}}
                </div>
                
                <br>

                <div>
                    <div class="col-md-12">
                        <div class="col-md-2"><div class="form-group">{{Form::select('tipoP', array('1' => 'SNG','2' => 'SNY', '3' => 'VVP', '4' => 'CUP'), null, array('class' => 'form-control selectTipoProc', 'placeholder' => 'Seleccione'))}}</div></div>
                        <div class="col-md-2"><div class="form-group">{{Form::number('numeroP', null, array( 'class' => 'form-control valor', 'min' => '0', 'id' => 'numeroP'))}}</div></div>
                        <div class="col-md-2"> <div class="form-group"> {{Form::select('estadoP', array('1' => 'Pendiente', '2' => 'Finalizado'), null, array( 'class' => 'form-control', 'id' => 'estadoP')) }} </div> </div>
                        <div class="col-md-2"><div class="form-group">{{Form::text('fechaProcedimiento', null, array( 'class' => 'dPProcedimientos form-control', 'id' => 'fechaP'))}}</div></div>
                        <div class="col-md-1"> 
                            <button type="submit" class="btn btn-primary" id="btnGuardarProcedimiento">Guardar</button>
                        </div>
                    </div>
                </div>

                <br>

                <legend>Procedimientos invasivos y estadía</legend>
                <table id="tablaProcedimientosInvasivos" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>PROCEDIMIENTOS</th>
                            <th>NÚMERO</th>
                            <th>ESTADO</th>
                            <th>FECHA PROCEDIMIENTO</th>
                            <th>DÍAS</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{ Form::close() }} 