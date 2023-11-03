<script>

    /* Editar fila */
    function modificarControl(idSolicitud, idFila) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarControlEgreso",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            dia: $("#diaControl"+idFila).val(),
                            noche: $("#nocheControl"+idFila).val(),
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
                                tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
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
                    tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
                }				
            }
        });  
        	
    }

    /* eliminar la fila de datatable */
    function eliminarControl(idSolicitud) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de eliminar este control egreso?</h4>",				
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarControlEgreso",
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
                                tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
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
                    tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
                }				
            }
        }); 
    }
    function validarVolumenesEgreso() {
        var tEDia = 0;
        var tENoche = 0;
        var tETotal = 0;
        $("#controlEgreso").DataTable().rows().data().each(function(valor,index){
            tEDia += ($("#diaControl"+index).val())?parseFloat($("#diaControl"+index).val()): 0;
            tENoche += ($("#nocheControl"+index).val())?parseFloat($("#nocheControl"+index).val()): 0;
        });
        tETotal = tEDia+tENoche;

        $("#tEDia").val(verificarfloat(tEDia));
        $("#tENoche").val(verificarfloat(tENoche));
        $("#tETotal").val(verificarfloat(tETotal));
        $("#tEgreso").val(verificarfloat(tETotal));
        var total_IE2 = parseFloat($("#tIngreso").val()) - (tETotal); 
        $("#tIETotal").val(verificarfloat(total_IE2));


        $(".controlFTotal").keyup(function(){
            var idFila=$(this).data("id");
            dia = ($("#diaControl"+idFila).val())?parseFloat($("#diaControl"+idFila).val()):0;
            noche = ($("#nocheControl"+idFila).val())?parseFloat($("#nocheControl"+idFila).val()):0;
            let total = dia+noche;
            $("#totalControl"+idFila).val(verificarfloat(total));
        });
    }

    function generarTablaControlEgreso() {
        tableControlEgresoActual = $("#controlEgreso").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerControlesEgresos/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": validarVolumenesEgreso 
        });
    }

    function cargarVistaControlEgreso(){
        if (typeof tableControlEgresoActual !== 'undefined') {
            tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
        }else{
            generarTablaControlEgreso();
        }
    }

    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){
            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            var tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "2b"){
                console.log("tabRdcx control egresos : ", tabRdc);
                cargarVistaControlEgreso();
            }
        });

        $( "#2ab" ).click(function() {
            cargarVistaControlEgreso();
        });

        $("#HEControlEgreso").bootstrapValidator({
            excluded: ':disabled',
            fields: {  
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitarEgresos").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $.ajax({
                url: "{{URL::to('/gestionEnfermeria')}}/addControlEgreso",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    $("#btnSolicitarEgresos").prop("disabled", false);
                    console.log(data);
                    if (data.exito) {
                        swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						});

                        tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
                        $("#HEControlEgreso").get(0).reset();
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
                    $("#btnSolicitarEgresos").prop("disabled", false);
                    tableControlEgresoActual.api().ajax.reload(validarVolumenesEgreso, false);
                }
            });

        });

        $(function (){
            /* Calcular suma de valores */
            $(".controlTotal").keyup(function(){
                edia = ($("#diaControl").val())?parseFloat($("#diaControl").val()):0;
                enoche = ($("#nocheControl").val())?parseFloat($("#nocheControl").val()):0;
                let total = edia+enoche;
                $("#totalControl").val(verificarfloat(total));
            });
        });
    });
    

</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEControlEgreso')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
    
<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>EGRESOS</h4>
            </div>
    
            <div class="panel-body" id="controlEgresos">

               <legend>Ingresar nuevo control egreso</legend>

                <div class="col-md-12">
                    <div class="col-md-2"> CONTROL</div>
                    <div class="col-md-2"> DÍA</div>
                    <div class="col-md-2"> NOCHE</div>
                    <div class="col-md-2"> TOTAL</div>
                    <div class="col-md-2"> OBSERVACIÓN</div>
                </div>

                <div class="col-md-12 controlEgreso">
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('control', array('1' => 'Diuresis', '2' => 'Deposiciones', '3' => 'Vomitos', '4' => 'Sng', '5' => 'Drenaje 1', '6' => 'Drenaje 2', '7' => 'Drenaje 3', '8' => 'Perd. Insensibles', '9' => 'Otros'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('dia', null, array( 'class' => 'controlTotal form-control', 'placeholder' => 'ml/kg/horas', 'step' => '0.1', 'min' => '0', 'id' => 'diaControl'))}} </div> </div> 
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('noche', null, array( 'class' => 'controlTotal form-control', 'placeholder' => 'ml/kg/horas', 'step' => '0.1', 'min' => '0', 'id' => 'nocheControl'))}} </div> </div> 
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('total', null, array( 'class' => 'form-control', 'disabled', 'id' => 'totalControl'))}} </div> </div>      
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('observacion', null, array( 'class' => 'form-control'))}} </div> </div> 
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnSolicitarEgresos">Guardar</button>
                    </div>   
                </div>

                <legend>Controles de egreso realizados hoy</legend>
                <table id="controlEgreso" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>CONTROL</th>
                            <th>DÍA</th>
                            <th>NOCHE</th>
                            <th>TOTAL</th>
                            <th>OBSERV.</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>

                <div class="col-md-12 volumenesEgresos">
                    <div class="col-md-2">
                        <label for="">Total Volumen Día</label>
                        {{Form::text('totalEgresoDia', null, array( 'class' => 'form-control', 'disabled','id' => 'tEDia'))}}
                    </div>
                    <div class="col-md-2">
                        <label for="">Total Volumen Noche</label>
                        {{Form::text('totalEgresoDia', null, array( 'class' => 'form-control', 'disabled','id' => 'tENoche'))}}
                    </div>
                    <div class="col-md-2">
                        <label for="">Total Volumen</label>
                        {{Form::text('totalEgresoDia', null, array( 'class' => 'form-control', 'disabled','id' => 'tETotal'))}}
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>
</div>


{{ Form::close() }} 

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>TOTAL</h4>
            </div>
        
            <div class="panel-body">
                <div class="col-md-12 Total IngresoEgresos">
                    <div class="col-md-2">
                        <label for="">Total Ingreso</label>
                        {{Form::text('totalIngreso', null, array( 'class' => 'form-control', 'disabled','id' => 'tIngreso'))}}
                    </div>
                    <div class="col-md-2">
                        <label for="">Total Egreso</label>
                        {{Form::text('totalEgreso', null, array( 'class' => 'form-control', 'disabled','id' => 'tEgreso'))}}
                    </div>
                    <div class="col-md-2">
                        <label for="">Total Volumen</label>
                        {{Form::text('totalIngresoEgreso', null, array( 'class' => 'form-control', 'disabled','id' => 'tIETotal'))}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
