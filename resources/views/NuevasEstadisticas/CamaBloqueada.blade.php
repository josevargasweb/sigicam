@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadísticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de camas bloqueadas</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")

<script>
    $(window).load(function() {
		$(".loader").fadeOut("slow");
	});

$(function() {
    $("#estadistica").collapse();

    table=$('#listaCamasBloqueadas').dataTable({	
        
        "bJQueryUI": true,
        "iDisplayLength": 10,
        "ajax": "{{asset('estadisticas/camas/estCamasBloqueadas')}}",
        "language": {
            "sUrl": "{{URL::to('/')}}/js/spanish.txt"
        },
        "sPaginationType": "full_numbers"
    });
    
    var chart =Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: 'Fuente: SIGICAM'
        },
        xAxis: {
            categories: ['1', '2', '3', '4', '5', '6',
                '7', '8', '9', '10', '11', '12', '13', 
                '14', '15', '16', '17', '18', '19', '20', '21', '22',
                '23', '24', '25', '26', '27', '28', '29', '30', '31'],
            crosshair: true,
            title:{
                text: 'Día del mes'
            }
        },
        yAxis: {
            title: {
                text: 'Cantidad de camas',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        },
        legend: {
            shadow: false
        },
        tooltip: {
            headerFormat: 'día {point.key} <br/>',
            shared: true
        },
        plotOptions: {
            column: {
                grouping: false,
                shadow: false,
                borderWidth: 0
            }
        },
        series: [{
            type: 'area',
            name: 'Total Camas',
            color: 'rgba(124, 181, 236, 0.75)',
            data: [140, 90, 40]
            
        },
        {
            name: 'Camas Bloqueadas',
            type: 'area',
            data: [1,5,9],
            color: 'rgba(67, 67, 72, 0.75)',
            tooltip: {
                valueSuffix: ''
            },
            dataLabels: {
                    enabled: true,
                    format: "{point.y}"
            }
        } ]
    });

    chart.showLoading('<img src="{{URL::to('/')}}/images/default.gif">');
    $.ajax({
        url: "{{asset('graficoBloqueadas')}}",
        type: "get",
        dataType: "json",
        data: {'anno': 0, 'mes': 0, 'establecimiento': 8, 'tipo_cama': 13},
        success: function(data){
            chart.hideLoading();
            chart.update({
                title:{
                    text: data.titulo
                },
                series: [
                {
                    data: data.total_comparacion,
                    dataLabels: {
                            enabled: true,
                            format: "{point.y} "
                    },
                },{
                    data: data.resultados,
                    dataLabels: {
                            enabled: true,
                            format: "{point.y} "
                    },
                }]
            });
        },
        error: function(error){
            console.log("error: ", error)
        }
    });
    
    $(".fecha-grafico").datepicker({
        startView: 'months',
        minViewMode: "months",
        autoclose: true,
        language: "es",
        format: "mm-yyyy",
        //todayHighlight: true,
        endDate: "+0d",
    });

    $("#btn-grafico").on("click", function(){
        var valor = $("#fecha-grafico").val();
        if(valor == ""){
           	swalWarning.fire({
            title: 'Información',
            text:"Debe seleccionar una fecha"
            });
        }else{
            var mes = $("#fecha-grafico").datepicker('getDate').getMonth()+1;
            var anno = $("#fecha-grafico").datepicker('getDate').getFullYear();
            var establecimiento = $("#establecimiento").val();
            var tipo_cama = $("#tipo_cama").val() == '' ? 'TODOS' : $("#tipo_cama").val();
            chart.showLoading('<img src="{{URL::to('/')}}/images/default.gif">');
            $.ajax({
                url: "{{asset('graficoBloqueadas')}}",
                type: "get",
                dataType: "json",
                data: {'anno': anno, 'mes': mes, 'establecimiento': establecimiento, 'tipo_cama': tipo_cama},
                success: function(data){
                    chart.hideLoading();
                    chart.update({
                        title:{
                            text: data.titulo
                        },
                        series: [{
                            data: data.total_comparacion,
                            dataLabels: {
                                    enabled: true,
                                    format: "{point.y} "
                            },
                        },
                        {
                            data: data.resultados,
                            dataLabels: {
                                    enabled: true,
                                    format: "{point.y} "
                            },
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">Pacientes</span>: <b>{point.y}</b> de '+data.total_cie10_pie+' ({point.percentage:.0f}%)<br/>',
                                shared: true,
                            },
                        }]
                    });
                },
                error: function(error){
                    console.log("error: ", error)
                }
            });
        }
    });

    $("#establecimiento").on('change', function(){
        $.ajax({
            url: "{{asset('estadisticas/pacientesD2D3Datos')}}",
            type: "get",
            dataType: "json",
            data: {'establecimiento': $(this).val()},
            success: function(data){
                table.fnClearTable();
                if(data.aaData.length > 0){
                    table.fnAddData(data.aaData);
                }
            },
            error: function(error){
                console.log("error:"+JSON.stringify(error));
                console.log(error);
            }
        });
    });
    
});

</script>

@stop

@section("section")
<style>
    .primerNav{
  background-color:#1E9966;
    }
  .primerNav > li > a{
    color: #fff !important;
    }

    .primerNav > li.active > a, .primerNav > li.active > a:hover,.primerNav > li.active > a:focus,.primerNav > li > a:hover, .primerNav > li > a:focus{
      background-color:#c35c6b;
    }

    .loader {
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url("{{URL::to('/')}}/images/default.gif") 50% 50% no-repeat rgb(249,249,249);
		opacity: .8;
	}    
</style>

    {{-- <fieldset> --}}
		<div class="row">
            <div id="exTab1" class="container">
                <ul class="nav nav-pills primerNav">
                    <li id="grafico" class="active"><a href="#1g" data-toggle="tab">Gráfico Camas Bloqueadas</a></li>
                    <li id="tabla"><a href="#1t" data-toggle="tab">Reporte Camas Bloqueadas</a></li>
                </ul>
                <div class="tab-content clearfix">
                    <div class="tab-pane active" id="1g">
                        <fieldset>
                            <div class="col-sm-12"><h5>Seleccione fecha</h5></div>
                            <div class="col-sm-2 form-group">
                                <input type="text" id="fecha-grafico" class="form-control fecha-grafico" value= "{{ Carbon\Carbon::now()->format('m-Y') }}">
                            </div>
                            <div class="col-sm-2 form-group">
                                {{Form::select("tipo_cama", App\Models\TipoCama::seleccion(), 13, ["class" => "form-control", "id" => "tipo_cama", "placeholder" => "TODOS"]) }}
                            </div>
                            @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS)
                                <div class="col-sm-4 form-group">
                                    <label>Establecimiento</label>
                                    {{ Form::select('establecimiento', $establecimiento, 8, array('id' => 'establecimiento', 'class' => 'form-control')) }}
                                </div>
                            @endif
                            <div class="col-sm-2 form-group">
                                <button id="btn-grafico" class="btn btn-primary">Generar gráfico</button>
                            </div>
                            <div class="col-md-12">
                                <div id="container" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="tab-pane" id="1t">
                        <fieldset>
                            <div class="form-inline">
                                {{ HTML::link(URL::route('camasBloqueadasExcel'), 'Reporte Excel', ['class' => 'btn btn-success']) }}
                                {{ HTML::link(URL::route('camasBloqueadasPdf'), 'Reporte Pdf', ['class' => 'btn btn-danger']) }}
                            </div>
                            <br>
                            <legend>Reporte de Camas Bloqueadas</legend>
                            
                            <div class="table-responsive">
                            <table id="listaCamasBloqueadas" class="table  table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Nombre cama</th>
                                        <th>Nombre sala</th>
                                        <th>Nombre unidad</th>
                                        <th>Nombre area funcional</th>
                                        <th>Fecha de bloqueo</th>
                                        <th>Días de bloqueo</th>
                                        <th>Comentario bloqueo</th>
                                        <th>Fecha de habilitacion</th>
                                        <th>Comentario de habilitacion</th>                    
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            </div>
                        </fieldset>
                    </div>

                </div>
            </div>
        </div>
            {{-- 
                <div class="col-sm-12"><h5>Seleccione fecha</h5></div>
			<div class="col-sm-2 form-group">
				<input type="text" id="fecha-grafico" class="form-control fecha-grafico">
            </div>
            <div class="col-sm-2 form-group">
                {{Form::select("tipo_cama", App\Models\TipoCama::seleccion(), null, ["class" => "form-control", "id" => "tipo_cama", "placeholder" => "TODOS"]) }}
            </div>
			@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS)
				<div class="col-sm-4 form-group">
					<label>Establecimiento</label>
					{{ Form::select('establecimiento', $establecimiento, 8, array('id' => 'establecimiento', 'class' => 'form-control')) }}
				</div>
			@endif
			<div class="col-sm-2 form-group">
				<button id="btn-grafico" class="btn btn-primary">Generar gráfico</button>
			</div>
        </div>
        <div class="row">
			<div id="container" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
		</div>
	</fieldset>

    <fieldset>
        <div class="form-inline">
            {{ HTML::link(URL::route('camasBloqueadasExcel'), 'Reporte Excel', ['class' => 'btn btn-success']) }}
            {{ HTML::link(URL::route('camasBloqueadasPdf'), 'Reporte Pdf', ['class' => 'btn btn-danger']) }}
        </div>
        <br>
        <legend>Reporte de Camas Bloqueadas</legend>
        
        <div class="table-responsive">
        <table id="listaCamasBloqueadas" class="table  table-condensed table-hover">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Nombre cama</th>
                    <th>Nombre sala</th>
                    <th>Nombre unidad</th>
                    <th>Nombre area funcional</th>
                    <th>Fecha de bloqueo</th>
                    <th>Días de bloqueo</th>
                    <th>Comentario bloqueo</th>
                    <th>Fecha de habilitacion</th>
                    <th>Comentario de habilitacion</th>                    
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        </div>
    </fieldset> --}}
@stop
