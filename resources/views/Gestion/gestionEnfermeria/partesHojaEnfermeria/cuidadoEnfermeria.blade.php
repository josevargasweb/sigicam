<script>

    function cargarCuidados(){
        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/obtenerCuidados/{{ $caso }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                $("#contenidoHTML").empty();
                $("#contenidoHTML").html(data.contenido);

            },
            error: function(error){
                console.log(error);
            }
        });

        $.ajax({
            url: "{{URL::to('/gestionEnfermeria')}}/infocuidadoseindicaciones/{{ $caso }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                arreglosViejosLimpios();
                cuidados_old.push(data.cuidados);
                cuidados_realizados_old.push(data.cuidados_realizados);
                indicaciones_old.push(data.indicaciones);
                indicaciones_realizadas_old.push(data.indicaciones_realizadas);
            },
            error: function(error){
                console.log(error);
            }
        });
    }

    function arreglosViejosLimpios(){
        cuidados_old = [];
        cuidados_realizados_old = [];
        indicaciones_old = [];
        indicaciones_realizadas_old = [];
    }

    function arreglosNuevosLimpios(){
        cuidados_new = [];
        cuidados_realizados_new = [];
        indicaciones_new = [];
        indicaciones_realizadas_new = [];
    }

    function mensajeFechaRealizadosVacia(){
        swalError.fire({
            title: 'Error',
            text:"Debe ingresar una fecha"
        });
    }
    
    $(document).ready(function() {

        arreglosViejosLimpios();

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "9b"){
                console.log("tabRdc cuidados enfermeria: ", tabRdc);
                cargarCuidados();
            }

        });

        $( "#9ab" ).click(function() {
            cargarCuidados();
        });

        $("#comprobarNuevosDatos").click(function(){
            $.ajax({
                url: "{{URL::to('/gestionEnfermeria')}}/infocuidadoseindicaciones/{{ $caso }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "get",
                success: function(data){
                    arreglosNuevosLimpios();
                    cuidados_new.push(data.cuidados);
                    cuidados_realizados_new.push(data.cuidados_realizados);
                    indicaciones_new.push(data.indicaciones);
                    indicaciones_realizadas_new.push(data.indicaciones_realizadas);
                    nuevos_cuidados = false;
                    nuevos_cuidados_realizados = false;
                    nuevas_indicaciones = false;
                    nuevas_indicaciones_realizadas = false;

                    if (JSON.stringify(cuidados_old) == JSON.stringify(cuidados_new))
                    {
                        nuevos_cuidados = false;
                    }else{
                        nuevos_cuidados = true;
                    }

                    if (JSON.stringify(cuidados_realizados_old) == JSON.stringify(cuidados_realizados_new))
                    {
                        nuevos_cuidados_realizados = false;
                    }else{
                        nuevos_cuidados_realizados = true;
                    }

                    if (JSON.stringify(indicaciones_old) == JSON.stringify(indicaciones_new))
                    {
                        nuevas_indicaciones = false;
                    }else{
                        nuevas_indicaciones = true;
                    }

                    if (JSON.stringify(indicaciones_realizadas_old) == JSON.stringify(indicaciones_realizadas_new))
                    {
                        nuevas_indicaciones_realizadas = false;
                    }else{
                        nuevas_indicaciones_realizadas = true;
                    }

                    if(nuevos_cuidados || nuevos_cuidados_realizados || nuevas_indicaciones || nuevas_indicaciones_realizadas){
                        bootbox.confirm({				
                            message: "<h4>Existe nueva información de cuidados de enfermeria. <br>¿Desea actualizar la información?</h4>",				
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
                                    cargarCuidados();
                                    arreglosViejosLimpios();
                                    cuidados_old.push(data.cuidados);
                                    cuidados_realizados_old.push(data.cuidados_realizados);
                                    indicaciones_old.push(data.indicaciones);
                                    indicaciones_realizadas_old.push(data.indicaciones_realizadas);
                                }				
                            }
                        }); 
                    }else{
                        swalInfo2.fire({
                        title: 'Información',
                        text:"No existe nueva información"
                        });

                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        });

        $("#pdfCuidadosRealizados").click(function(){
            fecha_pdf_realizados = $("#fecha_pdf_realizados").val();
            caso = "{{$caso}}";
            if(fecha_pdf_realizados){
                window.location.href = "{{url('gestionEnfermeria/pdfCuidadosRealizados')}}"+"/"+caso+"/"+fecha_pdf_realizados;
            }else{
                mensajeFechaRealizadosVacia();
            }
        });

        $("#fecha_pdf_realizados").on("keyup", function(){
            value = $(this).val();
            if(!$(this).val()){
                mensajeFechaRealizadosVacia();
            }
        });
    });

</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

<fieldset>
    {{-- <legend>Obtener cuidados realizados</legend> --}}
    <div>
        <label for="inicio">Fecha</label>
    </div>
    <div class="form-group col-md-2">
        <input id="fecha_pdf_realizados" class="form-control fecha-sel" style="margin-left: -17px;" type="text" value='{{\Carbon\Carbon::now()->format("d-m-Y")}}' autocomplete="off">
    </div>
                    
    <div class="form-group">
        <button id="pdfCuidadosRealizados" class="btn btn-danger">PDF Cuidados Realizados</button>
    </div>
</fieldset>
<br>
<div class="row" id="controlEnfermeriaMove"> 
    <div class="col-md-12 formulario">
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>CUIDADOS DE ENFERMERIA</h4><button class="btn btn-primary" style="float: right; margin-top: -35px;" id="comprobarNuevosDatos">Recargar Datos</button>
            </div>
            <div class="panel-body">
                <div id="contenidoHTML"></div>
            </div>
        </div>
    </div>
</div>
