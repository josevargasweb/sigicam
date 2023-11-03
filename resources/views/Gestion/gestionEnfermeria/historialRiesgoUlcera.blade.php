@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")
    @include('Gestion.gestionEnfermeria.partials.scriptRiesgoUlceras')
    <script>
    $(document).ready(function(){
        historial = $("#escalaRiesgoUlceras").dataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
            },
        });

        $.ajax({
            url: "{{URL::to('')}}/buscarHistorialriesgoUlceras",
            headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {"idCaso": {{$caso}}},
                    dataType: "json",
                    type: "post",
                    success: function(data){
                        if(data.error){
                            console.log("error: no se encuentran datos");
                        }
                        console.log(data);
                        historial.fnClearTable();
                        if(data.length !=0) historial.fnAddData(data);
                    },
                    error: function(error){
                        console.log(error);
                    }
            });
        });

        function editar(id_formulario_riesgo_ulcera){
                id = id_formulario_riesgo_ulcera;
                $.ajax({
                    url: "{{URL::to('')}}/editarRiesgoUlceras"+"/"+id,                  
                    headers: {                                 
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
                    },                            
                    type: "get",                            
                    dataType: "json",                             
                    success: function(data){
                        console.log(data);
                        $("#percepcion_sensorial").val(data.datos.percepcion_sensorial).change();
                        $("#exposicion_humedad").val(data.datos.exposicion_humedad).change();
                        $("#actividad").val(data.datos.actividad).change();
                        $("#movilidad").val(data.datos.movilidad).change();
                        $("#nutricion").val(data.datos.nutricion).change();
                        $("#peligro_lesiones").val(data.datos.peligro_lesiones).change();
                        $("#id_formulario_riesgo_ulcera").val(data.datos.id);
                        $("#guardarUlceras").val("Editar Información");

                        var total = 0;
                        percepcion_sensorial  = $("#percepcion_sensorial").val();
                        exposicion_humedad = $("#exposicion_humedad").val();
                        actividad = $("#actividad").val();
                        movilidad = $("#movilidad").val();
                        nutricion = $("#nutricion").val();
                        peligro_lesiones = $("#peligro_lesiones").val();
                        suma = Number(percepcion_sensorial) + Number(exposicion_humedad) + Number(actividad) + Number(movilidad) + Number(nutricion) + Number(peligro_lesiones);

                        $("#totalUlceras").val(suma);
                        if(suma == 0){
                        $("#detalleRiesgoUlcera").val("");
                        }else if(suma <= 12){
                            $("#detalleRiesgoUlcera").val("Alto");
                           // $("#detalleRiesgoUlcera").css("background-color","#ff0000");
                        }else if(suma >= 13 && suma <= 15){
                            $("#detalleRiesgoUlcera").val("Medio");
                            //$("#detalleRiesgoUlcera").css("background-color","#ffc000");
                        }else if(suma >= 16){
                            $("#detalleRiesgoUlcera").val("Bajo");
                            //$("#detalleRiesgoUlcera").css("background-color","#92d050");
                        }            
                    },                            
                    error: function(error){                                
                        console.log(error);                            
                    }                        
                    });  
                    $('#modalFormRiesgoUlceras').modal('show');
            }
    </script>   
@stop

@section("section")
<div class="container">
    <fieldset>
        <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
        {{-- <br> --}}
        <div class="row">
            <div class="col-md-12" style="text-align:center;"><h4>Historial Escala Evaluación Riesgo de Lesiones por Presión</h4>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{ HTML::link(URL::route('pdfHistorialriesgoUlcera', [$caso]), 'Historial PDF', ['class' => 'btn btn-danger']) }}
                </div>
            </div>
            <div class="col-md-12">
                <br>
                Nombre Paciente: {{$paciente}}
            </div>
        </div>
        <br>
        <table id="escalaRiesgoUlceras" class="table  table-condensed table-hover">
            <thead>
                <tr style="background:#399865;">
                    <th>Opciones</th>
                    <th>Usuario aplica</th>
                    <th>Fecha Aplicación</th>
                    <th>Total</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </fieldset>
    
    <div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="modalFormRiesgoUlceras">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Escala Evaluación Riesgo de Lesiones por Presión</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'riesgoUlceraform', 'autocomplete' => 'off')) }}
                    <input type="hidden" value="En Curso" name="tipoFormUlcera" id="tipoFormUlcera">
                    <input type="hidden" value="{{$caso}}" name="idCaso">
                    <input type="hidden" value="" name="id_formulario_riesgo_ulcera" id="id_formulario_riesgo_ulcera">

                    @include('Gestion.gestionEnfermeria.partials.FormRiesgoUlceras')   
                    {{ Form::close() }}
                </div>   
            </div>
        </div>
    </div>
@stop