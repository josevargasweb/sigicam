@extends("Templates/template")

@section("titulo")
Estadísticas por Diagnóstico
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte por riesgo</a></li>
@stop

@section("script")
<script>
$("#estadistica").collapse();
    

var documentosDerivacion = function (idCaso){
    $.ajax({
		url: "{{ URL::to('/documentosDerivacion') }}",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {caso: idCaso},
		dataType: "json",
		type: "post",
		success: function(data){
			console.log(data);
			$("#modalDocumentosDerivacion .modal-body").html(data.contenido);
			$("#modalDocumentosDerivacion").modal();
		},
		error: function(error){
			console.log(error);
		}
	});
}
$(function() {
    table = $('#tablaDocDer').dataTable({ 
        "aaSorting": [[0, "asc"]],
        "iDisplayLength": 15,
        "bJQueryUI": true,
        "ajax": {
                "url": "{{asset('estadisticas/pacientesD2D3Datos')}}",
            },
        "oLanguage": {
            "sUrl": "{{URL::to('/')}}/js/spanish.txt"
        }
    });
});

</script>

@stop

@section("section")

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

    #consultar{
        margin-top: 20px;
    }
</style>
<br>
<legend>Pacientes D2 y D3</legend>
<br>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
        <table id="tablaDocDer" class="table table-striped table-bordered table-hover">
            <tfoot>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Rut</th>
                    <th>Comuna</th>
                    <th>Diagnóstico</th>
                    <th>Exámenes pendientes</th>
                    <th>Cama</th>
                    <th>Sala</th>
                    <th>Área funcional</th>
                    <th>Servicio</th>
                </tr>
            </tfoot>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Rut</th>
                    <th>Comuna</th>
                    <th>Diagnóstico</th>
                    <th>Exámenes pendientes</th>
                    <th>Cama</th>
                    <th>Sala</th>
                    <th>Área funcional</th>
                    <th>Servicio</th>
                </tr>
            </thead>
            <tbody>
            
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- <div class="row" style="margin-top: 20px;">
    <div id="informacion" class="col-md-12">
    </div>
</div> -->

<!-- <div id="modalDocumentosDerivacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Documentos de derivación</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div> -->

<!-- <div id="modalDocumentosDerivacion2" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Documentos de derivación</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div> -->

@stop
