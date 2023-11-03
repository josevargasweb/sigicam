@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")
<script></script>
@stop

@section("miga")
<li><a href="#">Gestión de Egresos</a></li>
<li><a href="#">Gestionar Egresos</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section('section')
    <style></style>
    <fieldset>
        <legend>Egresados</legend>
        <br><br>
        <table id="listaEgresados" class="table table-condensed table-hover">
            <thead>
                <tr style="background: #399865">
                    <th>Nombre Completo</th>
                    <th>Run</th>
                    <th>Edad</th>
                    <th>Hospital de origen</th>
                    <th>Servicio de Procedencia</th>
                    <th>Médico ingresa</th>
                    <th>Diagnóstico Ingreso</th>
                    <th>Fecha Ingreso</th>
                    <th>Fecha Egreso</th>
                    <th>Diagnóstico Egreso</th>
                    <th>Procedimientos y Causa Externa</th>
                    <th>Destino</th>
                    <th>Medico Egresa</th>
                    <th>Opciones</th>
                </tr>
            </thead>
        </table>
    </fieldset>
@stop