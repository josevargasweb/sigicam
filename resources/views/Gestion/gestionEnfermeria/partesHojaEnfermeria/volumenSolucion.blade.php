<script>

    /* Editar fila */
    function modificarVolumen(idSolicitud, idFila) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarVolumenes",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            inicio: $("#inicio"+idFila).val(),
                            termino: $("#termino"+idFila).val(),
                            tDia: $("#tDia"+idFila).val(),
                            tNoche: $("#tNoche"+idFila).val(),
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
                                tableVolumenesActual.api().ajax.reload(validarVolumen, false);
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
                    tableVolumenesActual.api().ajax.reload(validarVolumen, false);
                }				
            }
        });  
        	
    }

    function validarVolumen () {
        /* añadir cualquier script que desees ejecutar dentro del datatable */
        var tDia = 0;
        var tNoche = 0;
        var tTotal = 0;
        $('#volumenSolucion').DataTable().rows().data().each(function(valor, index){
            tDia += ($("#tDia"+index).val())?parseFloat($("#tDia"+index).val()): 0;
            tNoche += (parseFloat($("#tNoche"+index).val()))? parseFloat($("#tNoche"+index).val()): 0;
        });
        tTotal = tDia+tNoche;
        
        $("#tDia").val(verificarfloat(tDia));
        $("#tNoche").val(verificarfloat(tNoche));
        $("#tTotal").val(verificarfloat(tTotal));
        $("#tIngreso").val(verificarfloat(tTotal));
        var total_IE1 = tTotal - parseFloat($("#tEgreso").val());
        $("#tIETotal").val(verificarfloat(total_IE1));
        
        /* Calcular suma de valores */
        $(".calcularTotal").keyup(function(){
            var idFila=$(this).data("id");
            dia = ($("#tDia"+idFila).val())?parseFloat($("#tDia"+idFila).val()):0;
            noche = ($("#tNoche"+idFila).val())?parseFloat($("#tNoche"+idFila).val()):0;
            let total = dia+noche;
            $("#tTotal"+idFila).val(verificarfloat(total));
        });

        /* activar el datetimepicker de la tabla */
        $('.dPVolumen').datetimepicker({
            format: 'LT'
        });
     }

    function generarTablaVolumen() {
        tableVolumenesActual = $("#volumenSolucion").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerVolumenesSolucionesPendientes/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": validarVolumen
        });
    }

    /* eliminar la fila de datatable */
    function eliminarVolumen(idSolicitud) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarVolumen",
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
                                tableVolumenesActual.api().ajax.reload(validarVolumen, false);
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
                    tableVolumenesActual.api().ajax.reload(validarVolumen, false);
                }				
            }
        }); 
    }

    function cargarVistaVolumenSolucion(){
        if (typeof tableVolumenesActual !== 'undefined') {
            tableVolumenesActual.api().ajax.reload(validarVolumen,false);
        }else{
            generarTablaVolumen();
        }
    }

    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;
            
            if(tabRdc == "2b"){
                console.log("tabRdc ingresos: ", tabRdc);
                cargarVistaVolumenSolucion();
            }

        });

        $( "#2ab" ).click(function() {
            cargarVistaVolumenSolucion();
        });

        $("#HEVolumenSolucion").bootstrapValidator({
            excluded: ':disabled',
            fields: {  
            }
        }).on('status.field.bv', function(e, data) {
            // data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            // $("#btnSolicitarVolumen").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/addVolumenSolucion",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnSolicitarVolumen").prop("disabled", false);
                                
                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    tableVolumenesActual.api().ajax.reload(validarVolumen, false);
                                    $("#HEVolumenSolucion").get(0).reset();
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
                                $("#btnSolicitarVolumen").prop("disabled", false);
                                console.log(error);
                            }
                        });				
                    }				
                }
            });  
            $("#btnSolicitarVolumen").prop("disabled", false);

        });

        $(function () {
            $('.dPVSolucion').datetimepicker({
                format: 'LT'
            });

            /* Calcular suma de valores */
            $(".calTotal").keyup(function(){
                dia = ($("#volumenDia").val())?parseFloat($("#volumenDia").val()):0;
                noche = ($("#volumenNoche").val())?parseFloat($("#volumenNoche").val()):0;
                let total = dia+noche;
                $("#totalVol").val(verificarfloat(total));
            });
        });
    });        

    

</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEVolumenSolucion')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
{{-- {{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormEnfermeria') )}} --}}
    
<div class="row">    
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>INGRESOS</h4>
            </div>
    
            <div class="panel-body" id="columenesSoluciones">

                <legend>Ingresar nuevos suero</legend>

                <div class="col-md-12" style="text-align:center;">
                    <div class="col-md-2"> TIPO SOLUCIÓN</div>
                    <div class="col-md-1 calInput"> TURNO DÍA</div>
                    <div class="col-md-1 calInput"> TURNO NOCHE</div>
                    <div class="col-md-1"> VOLUMEN</div>
                    <div class="col-md-2"> INICIO</div>
                    <div class="col-md-2"> TÉRMINO</div>
                </div>

                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('tipoVolumen', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-1 calInput"> <div class="form-group"> {{Form::number('volumenDia', null, array( 'class' => 'form-control calTotal', 'placeholder' => 'ml/kg/horas', 'step' => '0.1', 'min' => '0', 'id' => 'volumenDia'))}} </div> </div>
                    <div class="col-md-1 calInput"> <div class="form-group"> {{Form::number('volumenNoche', null, array( 'class' => 'form-control calTotal', 'placeholder' => 'ml/kg/horas', 'step' => '0.1', 'min' => '0', 'id' => 'volumenNoche'))}} </div> </div>
                    <div class="col-md-1"> <div class="form-group"> {{Form::text('totalVolumen', null, array( 'class' => 'form-control', 'disabled', 'id' => 'totalVol'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('inicio', null, array( 'class' => 'dPVSolucion form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('termino', null, array( 'class' => 'dPVSolucion form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> 
                        {{-- <button type="button" class="btn-xs btn-danger" onclick="eliminar(this)">Eliminar</button> --}}
                        <button type="submit" class="btn btn-primary" id="btnSolicitarVolumen">Guardar</button>
                    </div>   
                </div>
                 
                <br><br>

                <legend>Plan de suero diario</legend>
                <table id="volumenSolucion" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>TIPO SOLUCIÓN</th>
                            <th>TURNO DÍA</th>
                            <th>TURNO NOCHE</th>
                            <th>VOLUMEN</th>
                            <th>INICIO</th>
                            <th>TÉRMINO</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>

                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-2">
                        <label for="">Total Volumen Día</label>
                        {{Form::text('totalVolumenDia', null, array( 'class' => 'form-control', 'disabled','id' => 'tDia'))}}
                    </div>
                    <div class="col-md-2">
                        <label for="">Total Volumen Noche</label>
                        {{Form::text('totalVolumenDia', null, array( 'class' => 'form-control', 'disabled','id' => 'tNoche'))}}
                    </div>
                    <div class="col-md-2">
                        <label for="">Total Volumen</label>
                        {{Form::text('totalVolumenDia', null, array( 'class' => 'form-control', 'disabled','id' => 'tTotal'))}}
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>
</div>

{{ Form::close() }} 