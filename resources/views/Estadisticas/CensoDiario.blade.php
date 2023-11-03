@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de casos sociales</a></li>
@stop

@section("script")
<script>

$(function() {
    $('#fechaActual').datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        maxDate: moment().format("YYYY/MM/DD")
    });
    
});

</script>

@stop

@section("section")
	<fieldset>
        <legend>Gráfico SIR</legend>
        <div class="col-md-12">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="text" class="form-control" id="fechaActual"></input>
                </div>
            </div>
        </div>
    </fieldset>

@stop
