{{ HTML::script('js/ProgressBar.js') }}

{{ HTML::style('css/ProgressBar.css') }}
<script>

console.log("GMENSUAL");
var gmensual = {!!$g_mensual!!};
gmensual.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
var griesgo = {!!$g_riesgo!!};
griesgo.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
var gaceptadas = {!!$g_est_aceptadas!!};
gaceptadas.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
var grechazadas = {!!$g_est_rechazadas!!};
grechazadas.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
var gpromedio = {!!$g_est_promedio!!};
gpromedio.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};



var promedioRecibidas =0;
for(var i=0;i<gpromedio.series[0].data.length; i++)
{
    promedioRecibidas = promedioRecibidas + gpromedio.series[0].data[i];
    console.log(promedioRecibidas);
}

    promedio = promedioRecibidas /11;


var enviadas_vs_aceptadas = {!!$enviadas_vs_aceptadas!!}


    $("#estado_general_enviadas").highcharts({!!$g_general!!});
    $("#mensual").highcharts(gmensual);
    $("#por_riesgo").highcharts(griesgo);
    $("#aceptadas_por_establecimiento").highcharts(gaceptadas);
    $("#rechazadas_por_establecimiento").highcharts(grechazadas);
    $("#promedio_por_establecimiento").highcharts(gpromedio);
    $("#derivadas_vs_aceptadas").highcharts(enviadas_vs_aceptadas);
    @if($id_est)
        var grecibidas = {!!$g_riesgo_recibidas!!};
        grecibidas.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
        $("#estado_general_recibidas").highcharts({!!$g_general_recibidas!!});
        $("#por_riesgo_recibidas").highcharts(grecibidas);
        $("#rprome").html(Math.round(promedio) + " días");
    @else
        $("#prome").html(Math.round(promedio) + " días");
    @endif
</script>


<div class="row">
    @if($id_est)
    <legend>Solicitudes de derivación enviadas</legend>
    @else
        <legend>Total de solicitudes de derivación</legend>
    @endif
    <div class="col-md-4">
        <ul class="list-group">
            <li class="list-group-item"><span id="dcomp" class="badge">{{$enviadas_aceptadas}}</span> <strong>Aceptadas</strong></li>
            <li class="list-group-item"><span id="dpend" class="badge">{{$enviadas_aceptadas_pendiente}}</span> <strong>Aceptadas, cama pendiente</strong> </li>
            <li class="list-group-item"><span id="dcanc" class="badge">{{$enviadas_canceladas}}</span> <strong>Canceladas</strong> </li>
            <li class="list-group-item"><span id="despe" class="badge">{{$enviadas_en_espera}}</span> <strong>En espera</strong> </li>
            <li class="list-group-item"><span id="precb" class="badge">{{$enviadas_rechazadas}}</span> <strong>Rechazadas</strong> </li>
            <li class="list-group-item"><span id="prome" class="badge">{{$promedio_e}}</span> <strong>Promedio tiempo aceptación</strong> </li>

        </ul>
    </div>
    <div class="col-md-8">
        <div id="estado_general_enviadas"></div>
    </div>
</div>
@if($id_est)
<div class="row">
    <legend>Solicitudes de derivación recibidas</legend>
    <div class="col-md-4">
        <ul class="list-group">
            <li class="list-group-item"><span id="rdcomp" class="badge">{{$recibidas_aceptadas}}</span> <strong>Aceptadas</strong> </li>
            <li class="list-group-item"><span id="rdpend" class="badge">{{$recibidas_aceptadas_pendiente}}</span> <strong>Aceptadas, cama pendiente</strong> </li>
            <li class="list-group-item"><span id="rdcanc" class="badge">{{$recibidas_canceladas}}</span> <strong>Canceladas</strong> </li>
            <li class="list-group-item"><span id="rdespe" class="badge">{{$recibidas_en_espera}}</span> <strong>En espera</strong> </li>
            <li class="list-group-item"><span id="rprecb" class="badge">{{$recibidas_rechazadas}}</span> <strong>Rechazadas</strong> </li>
            <li class="list-group-item"><span id="rprome" class="badge">{{$promedio_r}}</span> <strong>Promedio tiempo aceptación</strong> </li>

        </ul>
    </div>
    <div class="col-md-8">
        <div id="estado_general_recibidas"></div>
    </div>
</div>
@endif
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div id="mensual"></div>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div id="por_riesgo"></div>
    </div>
</div>
@if($id_est)
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            <div id="por_riesgo_recibidas"></div>
        </div>
    </div>
@endif
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div id="aceptadas_por_establecimiento"></div>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div id="rechazadas_por_establecimiento"></div>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div id="promedio_por_establecimiento"></div>
    </div>


</div>
<div class="row" style="margin-top: 20px;">
   <div class="col-md-12">
    .
        <div id="derivadas_vs_aceptadas"></div>
    </div>

</div>
