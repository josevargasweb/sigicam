@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

    <script>
         function generarTablaEnCurso() {
            tableEnCurso = $("#tablaEnCurso").dataTable({
                "iDisplayLength": 10,
                "ordering": true,
                "searching": true,
                "ajax": {
                    url: "{{URL::to('trasladoInterno/getTableEnviadas/encurso')}}" ,
                    type: 'GET'
                },
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
                /* "initComplete":  */
            });
        }

        function generarTablaAceptado() {
            tableAceptado = $("#tablaAceptado").dataTable({
                "iDisplayLength": 10,
                "ordering": true,
                "searching": true,
                "ajax": {
                    url: "{{URL::to('trasladoInterno/getTableEnviadas/aceptado')}}" ,
                    type: 'GET'
                },
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
                /* "initComplete":  */
            });
        }

        function generarTablaRechazado() {
            tableRechazado = $("#tablaRechazado").dataTable({
                "iDisplayLength": 10,
                "ordering": true,
                "searching": true,
                "ajax": {
                    url: "{{URL::to('trasladoInterno/getTableEnviadas/rechazado')}}" ,
                    type: 'GET'
                },
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
                /* "initComplete":  */
            });
        }

    </script>
    @include("TrasladoInterno.script")
    <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
    <li><a href="#">Urgencia</a></li>
    <li><a href="#">Espera de traslado</a></li>
@stop

@section("section")

@include("TrasladoInterno.css")

<fieldset>
	<legend>Espera de traslado interno</legend>
	<div class="table-responsive">

    <div role="tabpanel">
        <div id="tabs">   
            <ul class="nav nav-tabs primerNav" role="tablist">
                <li role="presentation" class="active" id="enCurso"><a href="#id-motivo-1" role="tab" data-toggle="tab">En Curso</a></li>
                <li role="presentation" class="" id="aceptada"><a href="#id-motivo-2" role="tab" data-toggle="tab">Aceptado</a></li>
                <li role="presentation" class="" id="rechazado"><a href="#id-motivo-3" role="tab" data-toggle="tab">Rechazado</a></li>
            </ul>
        </div>
        <br>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="id-motivo-1">
                <table id="tablaEnCurso" class="table  table-condensed table-hover">
                    <thead>
                        <tr style="background:#399865;">
                            <th style="width:100px">Run</th>
                            <th>Nombre Completo</th>
                            <th>Fecha nacimiento</th>
                            <th>Diagnóstico</th>
                            <th width="120">Fecha solicitud</th>
                            <th>Servicio Origen</th>
                            <th>Servicio Destino</th>
                            <th>Usuario que solicita</th>
                            <th>Riesgo - Dependencia</th>                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="id-motivo-2">
                <table id="tablaAceptado" class="table  table-condensed table-hover">
                    <thead>
                        <tr style="background:#399865;">
                            <th style="width:100px">Run</th>
                            <th>Nombre Completo</th>
                            <th>Fecha nacimiento</th>
                            <th>Diagnóstico</th>
                            <th width="120">Fecha solicitud</th>
                            <th>Servicio Origen</th>
                            <th>Servicio Destino</th>
                            <th>Usuario que solicita</th>
                            <th>Riesgo - Dependencia</th>                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="id-motivo-3">
                <table id="tablaRechazado" class="table  table-condensed table-hover">
                    <thead>
                        <tr style="background:#399865;">
                            <th style="width:100px">Run</th>
                            <th>Nombre Completo</th>
                            <th>Fecha nacimiento</th>
                            <th>Comentario</th>
                            <th width="120">Fecha solicitud</th>
                            <th>Servicio Origen</th>
                            <th>Servicio Destino</th>
                            <th>Usuario que solicita</th>
                            <th>Riesgo - Dependencia</th>                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
	    </div>
</div>
</fieldset>



<div id="modalIngresar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Asignar cama</h4>
			</div>
			<div class="modal-body">
                <div id="mensajeError" class="alert alert-danger" role="alert" hidden>
                    <h4>La cama no puede ser seleccionada</h4>
                </div>
				<div class="row">
					<ul id="tabUnidad" class="nav nav-tabs" role="tablist">
					</ul>
					<div id="contentUnidad" class="tab-content">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

@stop