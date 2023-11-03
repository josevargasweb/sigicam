@extends("Templates/template")

@section("titulo")
Reporte Especialidades
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte Especialidades</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")
<script>
    $(function(){
        $("#estadistica").collapse();

        $(".fecha-reporte").datepicker({
            startView: 'months',
            minViewMode: "months",
            autoclose: true,
            language: "es",
            format: "mm-yyyy",
            endDate: "+0d"
        });

        $("#btn-reporte-mensual").on("click", function(){
            var valor = $("#fecha-reporte-mensual").val();
            if(valor == ""){
               	swalWarning.fire({
                title: 'Información',
                text:"Debe seleccionar una fecha"
                });
            }else{
                var mes = $("#fecha-reporte-mensual").datepicker('getDate').getMonth()+1;
                var anno = $("#fecha-reporte-mensual").datepicker('getDate').getFullYear();
                console.log("mes: ", mes);
                console.log("anno: ", anno);
                
                location.href="{{url('estadisticas/excelReporteEspecialidades')}}/"+anno+"/"+mes+"/M";
            }
        });
    });

</script>

@stop

@section("section")
{{ HTML::style('css/navegadortab.css') }}
<style>
    .tt-input{
        width:100%;
    }
    .tt-query {
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .tt-hint {
    color: #999
    }

    .tt-menu {    /* used to be tt-dropdown-menu in older versions */
    /*width: 430px;*/
    margin-top: 4px;
    /* padding: 4px 0;*/
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
            border-radius: 4px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
            box-shadow: 0 5px 10px rgba(0,0,0,.2);
    }
    .tt-suggestion {
    /* padding: 3px 20px;*/
    line-height: 24px;
    }

    .tt-suggestion.tt-cursor,.tt-suggestion:hover {
    color: #fff;
    background-color: #1E9966;

    }

    .tt-suggestion p {
    margin: 0;
    }
    .twitter-typeahead{
        width:100%;
    }
</style>
<br>
<legend>Reporte Especialidades</legend>
<br>

<div class="row">
    <div class="container">
        <ul class="nav nav-pills primerNav">
            <li id="diario" class="active">
                <a href="#r_diario" data-toggle="tab">Reporte Diario</a>
            </li>
            <li id="mensual">
                <a href="#r_mensual" data-toggle="tab">Reporte Mensual</a>
            </li>
        </ul>

        <div class="tab-content clearfix">
            <br>
            <div class="tab-pane active" id="r_diario">
                <div class="col-md-12">
                    <a class="btn btn-success" href="{{ url('estadisticas/excelReporteEspecialidades/0/0/D') }}">Reporte Excel</a>
                    
                    <div class="table-responsive">
                    <br>
                    <table id="tablaDocDer" class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Indicación</th>
                                <th>Restricción</th>
                                <th>Medicina</th>
                                <th>Cirugia</th>
                                <th>Traumatologia</th>
                                <th>Neurologia</th>
                                <th>Urologia</th>
                                <th>Neurocirugia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="3" style="vertical-align: middle">Días de Hospitalizados</td>
                                <td>1 a 3 días</td>
                                <td>{{$datos['info_categorias'][1][1]}}</td>
                                <td>{{$datos['info_categorias'][1][2]}}</td>
                                <td>{{$datos['info_categorias'][1][3]}}</td>
                                <td>{{$datos['info_categorias'][1][4]}}</td>                    
                                <td>{{$datos['info_categorias'][1][5]}}</td>
                                <td>{{$datos['info_categorias'][1][6]}}</td>
                            </tr>
                            <tr>
                                <td>4 a 12 días</td>
                                <td>{{$datos["info_categorias"][2][1]}}</td>
                                <td>{{$datos["info_categorias"][2][2]}}</td>
                                <td>{{$datos["info_categorias"][2][3]}}</td>
                                <td>{{$datos["info_categorias"][2][4]}}</td>                    
                                <td>{{$datos["info_categorias"][2][5]}}</td>
                                <td>{{$datos["info_categorias"][2][6]}}</td>
                            </tr>
                            <tr>
                                <td>más 12 días</td>
                                <td>{{$datos['info_categorias'][3][1]}}</td>
                                <td>{{$datos['info_categorias'][3][2]}}</td>
                                <td>{{$datos['info_categorias'][3][3]}}</td>
                                <td>{{$datos['info_categorias'][3][4]}}</td>                    
                                <td>{{$datos['info_categorias'][3][5]}}</td>
                                <td>{{$datos['info_categorias'][3][6]}}</td>
                            </tr>
                            <tr>
                                <td colspan=2>Total Hospitalizados</td>
                                <td>{{$datos['total_especialidades'][1]}}</td>
                                <td>{{$datos['total_especialidades'][2]}}</td>
                                <td>{{$datos['total_especialidades'][3]}}</td>
                                <td>{{$datos['total_especialidades'][4]}}</td>
                                <td>{{$datos['total_especialidades'][5]}}</td>
                                <td>{{$datos['total_especialidades'][6]}}</td>
                            </tr>
                            <tr>
                                <td colspan=2>Promedio de estancia hospitalaria</td>
                                <td>{{ ($datos['total_dias'][1] != 0) ? round($datos['total_dias'][1] / $datos['total_especialidades'][1],0) : '0' }}</td>
                                <td>{{ ($datos['total_dias'][2] != 0) ? round($datos['total_dias'][2] / $datos['total_especialidades'][2],0) : '0' }}</td>
                                <td>{{ ($datos['total_dias'][3] != 0) ? round($datos['total_dias'][3] / $datos['total_especialidades'][3],0) : '0' }}</td>
                                <td>{{ ($datos['total_dias'][4] != 0) ? round($datos['total_dias'][4] / $datos['total_especialidades'][4],0) : '0' }}</td>
                                <td>{{ ($datos['total_dias'][5] != 0) ? round($datos['total_dias'][5] / $datos['total_especialidades'][5],0) : '0' }}</td>
                                <td>{{ ($datos['total_dias'][6] != 0) ? round($datos['total_dias'][6] / $datos['total_especialidades'][6],0) : '0' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="r_mensual">
                <fieldset>
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <label>Seleccione fecha:</label>
                        </div>
                        <div class="col-sm-2 form-group">
                            <input type="text" id="fecha-reporte-mensual" class="form-control fecha-reporte"  value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
                        </div>
                        <div class="col-sm-2 form-group">
                            <button id="btn-reporte-mensual" class="btn btn-primary">Reporte Excel</button>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
@stop
