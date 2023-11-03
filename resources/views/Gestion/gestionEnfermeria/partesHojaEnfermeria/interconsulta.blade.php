<script>

    function generarTablaInterconsultas(){
        tablaInterconsultas = $("#interconsultasPendientes").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerInterconsultas/{{ $caso }}" ,
                type: 'post'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function eliminarInterconsulta(idSolicitud) {
        bootbox.confirm({
            message: "<h4>¿Está seguro de eliminar esta interconsulta?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarInterconsulta",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(res){
                            
                        swalExito.fire({
						    title: 'Exito!',
						    text: res.exito,
						    });
                                    
                            //actualizar tabla
                            tablaInterconsultas.api().ajax.reload();


                        },
                        error: function(xhr, status, error){
                            var error_json = JSON.parse(xhr.responseText);
                            swalError.fire({
						title: 'Error',
						text:error_json.error
						});
                        },
                        complete: function (){
                            $("#btnSolicitarInterconsulta").prop("disabled", false);
                        }
                    });				
                }			
            }
        }); 
    }


    function finalizarInterconsulta(idSolicitud, idFila) {

        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/finalizarInterconsulta",
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:  {
                id: idSolicitud,
                estado: $("#estadoI"+idFila).val(),
            },
            dataType: "json",
            type: "post",
            success: function(res){
                swalExito.fire({
                title: 'Exito!',
                text: res.exito,
                });
                
                //actualizar tabla
                tablaInterconsultas.api().ajax.reload();
            },
            error: function(xhr, status, error){
                var error_json = JSON.parse(xhr.responseText);
                swalExito.fire({
                title: 'Exito!',
                text: error_json.error,
                });            
            },
            complete: function (){
                $("#btnSolicitarInterconsulta").prop("disabled", false);
            }            
        });				
    }

    function cargarVistaGestionInterconsultas(){
        if (typeof tablaInterconsultas !== 'undefined') {
            tablaInterconsultas.api().ajax.reload();
        }else{
            generarTablaInterconsultas();
        }
    }


    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "12b"){
                //console.log("tabRdc gestion interconsultas: ", tabRdc);
                cargarVistaGestionInterconsultas();
            }

        });


        $( "#12ab" ).click(function() {
            cargarVistaGestionInterconsultas();
        });

        $("#btnSolicitarInterconsulta").prop("disabled", true);

        $("#HEInterconsulta").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'tipo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar el nombre de la interconsulta'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            // data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitarInterconsulta").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarInterconsulta",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnSolicitarInterconsulta").prop("disabled", false);
                                //console.log(data);
                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });


                                    $("#HEInterconsulta").trigger("reset");
                                    tablaInterconsultas.api().ajax.reload();
                                    $("#btnSolicitarInterconsulta").prop("disabled", true);
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
                                $("#btnSolicitarInterconsulta").prop("disabled", false);
                                console.log(error);
                            }
                        });
                    }
                }            
            });
            $("#btnSolicitarInterconsulta").prop("disabled", false);  
        });


    });
</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEInterconsulta')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>GESTIÓN DE INTERCONSULTAS</h4>
            </div>
            <div class="panel-body">
                <legend>Interconsultas pendientes e ingresadas hoy</legend>
                <table id="interconsultasPendientes" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SOLICITADAS</th>
                            <th>ESTADO</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <br><br>

                <div class="interconsultaExtra"></div>
            </div>
        </div>
    </div>
</div>

{{ Form::close() }}
