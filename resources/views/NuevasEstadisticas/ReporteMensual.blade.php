@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadísticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte Mensual Estadístico</a></li>
@stop

@section("script")

<script>
    $(function() {
        $("#estadistica").collapse();

        $("#fecha_informe_rem").datepicker({
            startView: 'months',
            minViewMode: "months",
            autoclose: true,
            language: "es",
            format: "mm-yyyy",
            //todayHighlight: true,
            endDate: "-1m",
        });

        var d = new Date();
        var month = d.getMonth();
        var year = d.getFullYear();

        $("#fecha_informe_rem").datepicker().datepicker("setDate", month+"-"+year);

        $("#btn-informe-pdf").on("click", function(){
            var valor = $("#fecha_informe_rem").val();
            if(valor == ""){
               	swalWarning.fire({
                title: 'Información',
                text:"Debe seleccionar una fecha"
                });
            }else{
                var mes = $("#fecha_informe_rem").datepicker('getDate').getMonth()+1;
                var anno = $("#fecha_informe_rem").datepicker('getDate').getFullYear();
                var servicio = $("#servicio").val();
                
                window.location.href = "{{url('estadisticas/descargarInformeRem')}}/"+anno+"/"+mes;
            }
        });

        $("#btn-informe-excel").on("click", function(){
            var valor = $("#fecha_informe_rem").val();
            if(valor == ""){
               	swalWarning.fire({
                title: 'Información',
                text:"Debe seleccionar una fecha"
                });
            }else{
                var mes = $("#fecha_informe_rem").datepicker('getDate').getMonth()+1;
                var anno = $("#fecha_informe_rem").datepicker('getDate').getFullYear();
                var servicio = $("#servicio").val();
                
                window.location.href = "{{url('estadisticas/descargarExcelRem')}}/"+anno+"/"+mes;
            }
        });
    });
</script>

@stop

@section("section")

    <style>
        .table > thead:first-child > tr:first-child > th{
            color: cornsilk;
        }

        .table > thead:first-child > tr:first-child {
            border-left: 2px solid !important;
        }

        table > thead:first-child > tr:first-child > th, th{
            vertical-align: middle;
        }
    </style>

    <div class="row">
        <h4>REM 20</h4>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group">
            <label>Seleccione fecha</label>
            <input type="text" id="fecha_informe_rem" class="form-control">
        </div>
        

        <div class="col-sm-2 form-group" style="margin-top:20px;">
            <button id="btn-informe-pdf" class="btn btn-danger">Descargar PDF</button>
        </div>

        <div class="col-sm-2 form-group" style="margin-top:20px;">
            <button id="btn-informe-excel" class="btn btn-success">Descargar Excel</button>
        </div>
    </div>

    <br>
    
@stop
