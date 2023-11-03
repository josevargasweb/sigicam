@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte Resumen</a></li>
@stop

@section("script")

    <script>
        $(function() {

            $.ajax({
                url: "{{asset('estadisticas/reporteUrgenciasGeneral')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                //data: $('#estDirector').serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    console.log("buena: ", data);
                    //numeros
                    $("#dato1").html("<b>"+data.dato1+"</b>");
                    $("#dato2").html("<b>"+data.dato2+"</b>");
                    $("#dato3").html("<b>"+data.dato3+"</b>");
                    $("#dato4").html("<b>"+data.dato4+"</b>");
                },
                error: function(error){
                    console.log("error: ", error);
                }
            });
            
     });

    </script>


@stop

@section("section")
    <style>
        .numeroActual {
            color: #6A7888;
            margin-top: 0px !important;
            margin-bottom: 0px !important;

        }

        .tituloReporte {
            color: #6A7888;

        }

        .estAnterior {
            color: #6A7888;
        }
        .tamano {
            font-size: 15px !important;
        }
    
    </style>

    <fieldset>
        <legend>Reportes de urgencia</legend>
        <br><br>
        <div class="row" id="urgenciaActual">

            <div class="col-sm-2">
                <label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="55" height="35" alt=""> Tiempo promedio en espera (Hrs)</label>
                <label><h1 class="numeroActual" id="dato1"></h1></label>
                <p class="numeroActual"><label >Actual</label> </p>
            </div>

            <div class="col-sm-1">
                    <div style="height: 100px;
                    border-right: 3px solid #6A7888;"></div>
            </div>
            

            <div class="col-sm-2">
                <label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="35" height="35" alt=""> Pacientes con t° espera > 12 horas</label>
                <label><h1 class="numeroActual" id="dato2"></h1></label>
                <p class="numeroActual"><label >Actual</label> </p>
            </div>

            <div class="col-sm-1">
                    <div style="height: 100px;
                    border-right: 3px solid #6A7888;"></div>
            </div>

            <div class="col-sm-2">
                <label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="35" height="35" alt=""> Casos ingresados hospitalizados</label>
                <label><h1 class="numeroActual" id="dato3"></h1></label>
                <p class="numeroActual"><label >Actual</label> </p>
            </div>


            <div class="col-sm-1">
                    <div style="height: 100px;
                    border-right: 3px solid #6A7888;"></div>
            </div>

            <div class="col-sm-2">
                <label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="35" height="35" alt=""> Casos ingresados</label>
                <label><h1 class="numeroActual" id="dato4"></h1></label>
                <p class="numeroActual"><label >Actual</label> </p> 
            </div>

            <div class="col-sm-1">
                    <div style="height: 100px;
                    border-right: 3px solid #6A7888;"></div>
            </div>
            

            
            
        </div>
        <br><br>


        <br>
        <div id="dia" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>

        <br>
        <div id="mes" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>
        

        <br>

        <div id="cie10" style="min-width: 310px; height: 600px; margin: 0 auto" hidden></div>

        <br>

    </fieldset>

@stop