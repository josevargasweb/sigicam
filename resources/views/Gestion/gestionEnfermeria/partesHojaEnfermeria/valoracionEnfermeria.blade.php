<script>


    function generarTablaValoracion() {
        tableValoracion = $("#valoracionesEnfermeria").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerValoracionesEnfermeria/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": function () {
                /* añadir cualquier script que desees ejecutar dentro del datatable */
            }
        });
    }

    function cargarVistaValoracionEnfermeria(){
        if (typeof tableValoracion !== 'undefined') {
            tableValoracion.api().ajax.reload();
        }else{
            generarTablaValoracion();
        }
    }

    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "9b"){
                console.log("tabRdc valoracion enfermeria : ", tabRdc);
                cargarVistaValoracionEnfermeria();
            }

        });

        $( "#9ab" ).click(function() {
            cargarVistaValoracionEnfermeria();
        });

        $( "#1ve" ).click(function() {
            if (typeof tableValoracion !== 'undefined') {
                tableValoracion.api().ajax.reload();
            }else{
                generarTablaValoracion();
            }
        });

        $("#HEValoracionEnfermeria").bootstrapValidator({
            excluded: ':disabled',
            fields: {  
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnValoracionEnfermeria").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/addValoracionEnfermeria",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnValoracionEnfermeria").prop("disabled", false);
                                if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });

                                    tableValoracion.api().ajax.reload();

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
                                $("#btnValoracionEnfermeria").prop("disabled", false);
                            
                                console.log(error);
                            }
                        });
                    }else{
                        tableValoracion.api().ajax.reload();
                    }
                }	
            });  
        });
    });
    

</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'HEValoracionEnfermeria')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
    
<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>VALORACIÓN EN ENFERMERÍA</h4>
            </div>

            <div class="panel-body" id="indicaciones">
                <legend>Ingresar nueva valoración enfermería</legend>
                <div class="col-md-12">
                    <div class="col-md-4"> NOMBRE ENFERMERA(O)</div>
                    <div class="col-md-4 col-md-offset-1"> OBSERVACIÓN</div>
                </div>

                <div class="col-md-12 moduloExamen">
                    <div class="col-md-4" > 
                        @if(Auth::user()->tipo == "gestion_clinica" || Auth::user()->tipo == "master")
                            <div class="form-group"> {{Form::text('nombre', Auth::user()->nombres." ". Auth::user()->apellido_paterno." ". Auth::user()->apellido_materno, array('id' => 'nombre', 'class' => 'form-control', 'disabled'))}} </div>
                        @else
                            <div class="form-group"> {{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control', 'disabled'))}} </div>
                        @endif    
                    </div>
                    <div class="col-md-4 col-md-offset-1"> 
                        <div class="form-group"> {{Form::textarea('observacion', null, array('id' => 'observacion', 'class' => 'form-control', 'rows' => '3','style' => 'resize:none'))}} </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnValoracionEnfermeria">Guardar</button>
                    </div>
                </div>

                <br><br>

                <legend>Listado de valoraciones enfermería realizados</legend>
                <table id="valoracionesEnfermeria" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ENFERMERA(O)</th>
                            <th>FECHA</th>
                            <th>OBSERVACIÓN</th>
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