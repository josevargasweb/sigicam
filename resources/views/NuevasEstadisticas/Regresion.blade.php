@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de casos sociales</a></li>
@stop

@section("script")
<!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
<!-- <script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script> -->
<script>

$(function() {
    $("#tomadecisiones").collapse();
    $("#div-aplicar").hide();
    $("#div-resultado").hide();

    $("#generar").on("click", function(){
        $("#div-aplicar").show();
    });

    $("#aplicar").on("click", function(){
        $.ajax({
            url: "{{asset('estadisticas/camas/aplicarRegresion')}}",
            method: "GET",
            success: function(data){
                console.log("buena: ", data);
                $("#resultado").val(data);
                $("#div-resultado").show();
            },
            error: function(error){
                console.log("error: ", error);
            }
        });
    });
});

</script>

@stop

@section("section")
<fieldset>
        <legend>Regresión Lineal</legend>
        <form id="formSir" role="form" method="POST" action="http://localhost/sigicam/public/getSir">
            <div class="col-md-12">
                <div class="checkbox">
                    <div class="col-sm-2">
                        <label class="checkbox-inline"><input type="checkbox" value="1" checked disabled>Campo 1</label>
                    </div>
                    <div class="col-sm-2">
                        <label class="checkbox-inline"><input type="checkbox" value="2" checked disabled>Campo 2</label>
                    </div>
                    <div class="col-sm-2">
                        <label class="checkbox-inline"><input type="checkbox" value="3" checked disabled>Campo 3</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group col-sm-4">
                    <button id="generar" type="button" class="btn btn-primary">Generar Archivo</button>
                </div>
            </div>
        </form>
        
        <div id="div-aplicar" class="col-md-12" style="margin-top:15px;">
            <div class="col-md-12">
                <p style="font-size: 16px;">El archivo ha sido generado</p>
                <button id="aplicar" class="btn btn-primary">Aplicar regresión lineal</button>
            </div>
        </div>

        <div id="div-resultado" class="col-md-12" style="margin-top:15px;">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Resultado</label>
                    <input class="form-control" id="resultado">
                </div>
            </div>
        </div>

    </fieldset>

@stop
