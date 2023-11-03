<script>

    function funcionesTablaRiesgoCaida() {
        var tcriterioEdad = 0;
        var tcriterioComprConciencia = 0;
        var tcriterioAgiPsicomotora = 0;
        var tcriterioLimSensorial = 0;
        var tcriterioLimMotora = 0;
        var ttotal = 0;
        
        $("#tablariesgocaidas").DataTable().rows().data().each(function(valor, index){
            tcriterioEdad = ($("#tcriterioEdad"+index).val() == 'true')?1: 0;
            tcriterioComprConciencia = ($("#tcriterioComprConciencia"+index).val() == 'true')?2: 0;
            tcriterioAgiPsicomotora = ($("#tcriterioAgiPsicomotora"+index).val() == 'true')?2: 0;
            tcriterioLimSensorial = ($("#tcriterioLimSensorial"+index).val() == 'true')?1: 0;
            tcriterioLimMotora = ($("#tcriterioLimMotora"+index).val() == 'true')?1: 0;
            console.log(tcriterioLimMotora);
        });
        ttotal = tcriterioEdad+tcriterioComprConciencia+tcriterioAgiPsicomotora+tcriterioLimSensorial+tcriterioLimMotora;
        
        $("#tcriterioEdad").val(tcriterioEdad);
        $("#tcriterioComprConciencia").val(tcriterioComprConciencia);
        $("#tcriterioAgiPsicomotora").val(tcriterioAgiPsicomotora);
        $("#tcriterioLimSensorial").val(tcriterioLimSensorial);
        $("#tcriterioLimMotora").val(tcriterioLimMotora);
        $("#ttotal").val(ttotal);

        $(".calculartTotal").change(function(){
            var idFila=$(this).data("id");
            criterioEdad = ($("#tcriterioEdad"+idFila).val() == 'true')?1: 0;
            criterioComprConciencia = ($("#tcriterioComprConciencia"+idFila).val() == 'true')?2: 0;
            criterioAgiPsicomotora = ($("#tcriterioAgiPsicomotora"+idFila).val() == 'true')?2: 0;
            criterioLimSensorial = ($("#tcriterioLimSensorial"+idFila).val() == 'true')?1: 0;
            criterioLimMotora = ($("#tcriterioLimMotora"+idFila).val() == 'true')?1: 0;
            $("#ttotal"+idFila).val(criterioEdad+criterioComprConciencia+criterioAgiPsicomotora+criterioLimSensorial+criterioLimMotora);
        });

        $('.dPriesgo').datetimepicker({
            format: 'LT'
        });
    }

    function generarRiesgoCaidas(){
        tableRiesgoCaidas = $("#tablariesgocaidas").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerRiesgoCaidas/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": funcionesTablaRiesgoCaida
        });
    }

    function eliminarRiesgoCaida(idSolicitud) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarRiesgoCaida",
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
                                tableRiesgoCaidas.api().ajax.reload(funcionesTablaRiesgoCaida,false);
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
                    tableRiesgoCaidas.api().ajax.reload(funcionesTablaRiesgoCaida,false);
                }				
            }
        }); 
    }

    function modificarRiesgoCaida(idSolicitud, idFila) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarRiesgoCaida",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            horario: $("#thorario"+idFila).val(),
                            criterioEdad: $("#tcriterioEdad"+idFila).val(),
                            criterioComprConciencia: $("#tcriterioComprConciencia"+idFila).val(),
                            criterioAgiPsicomotora: $("#tcriterioAgiPsicomotora"+idFila).val(),
                            criterioLimSensorial: $("#tcriterioLimSensorial"+idFila).val(),
                            criterioLimMotora: $("#tcriterioLimMotora"+idFila).val(),
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
                                tableRiesgoCaidas.api().ajax.reload(funcionesTablaRiesgoCaida,false);
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
                    tableRiesgoCaidas.api().ajax.reload(funcionesTablaRiesgoCaida,false);
                }				
            }
        });  
        	
    }


    $(document).ready(function() {

        $( "#11ab" ).click(function() {
            if (typeof tableRiesgoCaidas !== 'undefined') {
                // tableRiesgoCaidas.api().ajax.reload(validarVolumen,false);
                tableRiesgoCaidas.api().ajax.reload(funcionesTablaRiesgoCaida,false);
            }else{
                generarRiesgoCaidas();
            }
        });

        $( ".calcularRiesgoCaida" ).change(function() {
            var total = 0;
            
            total += ($("#criterioEdad").val() === "true")?1:0;
            total += ($("#criterioComprConciencia").val() === "true")?2:0;
            total += ($("#criterioAgiPsicomotora").val() === "true")?2:0;
            total += ($("#criterioLimSensorial").val() === "true")?1:0;
            total += ($("#criterioLimMotora").val() === "true")?1:0;
            $("#totalEnfermeria").val(total);
        });
        
        $("#HERiesgoCaida").bootstrapValidator({
            excluded: ':disabled',
            fields: {  
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitarRiesgoCaida").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarRiesgoCaida",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnSolicitarRiesgoCaida").prop("disabled", false);
                                
                                if (data.exito) {
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        });
                                    $("#HERiesgoCaida").trigger("reset");
                                    /* aactualizar tabla con pendientes */
                                    // tableRiesgoCaidas.api().ajax.reload(validarVolumen, false);
                                    tableRiesgoCaidas.api().ajax.reload(funcionesTablaRiesgoCaida,false);
                                    // tableRiesgoCaidas.api().ajax.reload();
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
                                $("#btnSolicitarRiesgoCaida").prop("disabled", false);
                                console.log(error);
                            }
                        });				
                    }				
                }
            });  
            $("#btnSolicitarRiesgoCaida").prop("disabled", false);

        });
        
        $(function () {
            $('.dPRCaida').datetimepicker({
                format: 'LT'
            });
        });
    });
    

</script>

<style>

   .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HERiesgoCaida')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
{{-- {{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormEnfermeria') )}} --}}
    
<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>VALORACIÓN RIESGO DE CAIDAS EN PACIENTE HOSPITALIZADO</h4>
            </div>
            
            <div class="panel-body">
                <legend>Puntaje</legend>
                <div class="col-md-12">
                    <div class="col-md-12" >
                        <div class="col-md-2">{{Form::label('', 'Puntaje de 2 a 4')}} </div>
                        <div class="col-md-2">{{Form::label('', 'Cama con barandas')}} </div>
                    </div>
            
                    <div class="col-md-12">
                        <div class="col-md-2">{{Form::label('', 'Puntaje de 5 a 7')}} </div>
                        <div class="col-md-4">{{Form::label('', 'Cama con barandas y contención')}} </div>
                    </div>
                </div>

                <br><br>

                <legend>Ingresar riesgo caída</legend>
                <div class="col-md-14">
                    <div class="col-md-1"> HORARIO</div>
                    <div class="col-md-2"> EDAD >65 ó < 2 AÑOS</div>
                    <div class="col-md-2"> COMPROMISO CONCIENCIA</div>
                    <div class="col-md-2"> AGITACIÓN PSICOMOTORA</div>
                    <div class="col-md-2"> LIMITACIÓN SENSORIAL</div>
                    <div class="col-md-2"> LIMITACIÓN MOTORA</div>
                    <div class="col-md-1"> TOTAL</div>
                </div>

                <br><br>

                <div class="riesgocaida">
                    <div class="col-md-14">
                        <div class="col-md-1"> <div class="form-group"> {{Form::text('horario', null, array( 'class' => 'dPRCaida form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{ Form::select('criterioEdad', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioEdad', 'class' => 'calcularRiesgoCaida form-control')) }}  </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{ Form::select('criterioComprConciencia', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioComprConciencia', 'class' => 'calcularRiesgoCaida form-control')) }}  </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{ Form::select('criterioAgiPsicomotora', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioAgiPsicomotora', 'class' => 'calcularRiesgoCaida form-control')) }}  </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{ Form::select('criterioLimSensorial', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioLimSensorial', 'class' => 'calcularRiesgoCaida form-control')) }}  </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{ Form::select('criterioLimMotora', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioLimMotora', 'class' => 'calcularRiesgoCaida form-control')) }}  </div> </div>
                        <div class="col-md-1"> <div class="form-group"> {{Form::text('total', 0, array('id' => 'totalEnfermeria', 'class' => 'form-control', 'disabled'))}}  </div> </div>
                        <div class="col-md-1"> 
                            <button type="submit" class="btn btn-primary" id="btnSolicitarRiesgoCaida">Guardar</button>
                        </div> 
                    </div>
                </div>

            </div>
            <br><br>
                <legend>Registros de riesgo caida de hoy</legend>
                <table id="tablariesgocaidas" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SOLICITAD0S</th>
                            <th>FECHA</th>
                            <th>EDAD >65 ó < 2 AÑOS</th>
                            <th>COMPROMISO CONCIENCIA</th>
                            <th>AGITACIÓN PSICOMOTORA</th>
                            <th>LIMITACIÓN SENSORIAL</th>
                            <th>LIMITACIÓN MOTORA</th>
                            <th>TOTAL</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            <br><br>
        </div>
    </div>
</div>
{{ Form::close() }} 