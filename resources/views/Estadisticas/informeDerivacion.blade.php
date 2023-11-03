@extends("Templates/template")

@section("titulo")
Reporte derivación
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte derivación</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
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
    var anno = new Date().getFullYear();
    var mes = new Date().getMonth() +1;
    var fechaExport = new Date().toJSON().slice(0,10);
    table = $('#tablaDocDer').dataTable({ 
        dom: 'Bfrtip',
			buttons: [
        		{
					extend: 'excelHtml5',
					messageTop: 'Pacientes con documentos derivación ('+fechaExport+')',
					exportOptions: {
						columns: [0,1,2,3,4,5]
                    } ,
                    text: 'Exportar',
                    className: 'btn btn-default',
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						var clRow = $('row', sheet);
						//$('row c', sheet).attr( 's', '25' );  //bordes
						$('row:first c', sheet).attr( 's', '67' ); //color verde, letra blanca, centrado
						$('row', sheet).attr('ht',15);
						$('row:first', sheet).attr( 'ht', 50 ); //ancho columna
						$('row:eq(1) c', sheet).attr('s','67'); //color verde, letra blanca, centrado
					}
				}
    		],
        "aaSorting": [[0, "desc"]],
        "iDisplayLength": 15,
        "bJQueryUI": true,
        "ajax": {
                "url": "{{asset('estadisticas/informeDerivacionDatos')}}",
                "data": {"anno": anno, "mes": mes},
                "type": "get"
            },
        "oLanguage": {
            "sUrl": "{{URL::to('/')}}/js/spanish.txt"
        }
    });

    $(".fecha-grafico").datepicker({
        startView: 'months',
        minViewMode: "months",
        autoclose: true,
        language: "es",
        format: "mm-yyyy",
        //todayHighlight: true,
        endDate: "+0d"
    });
    
    $("#btn-grafico").on("click", function(){
        var valor = $("#fecha-grafico").val();
        if(valor == ""){
           swalWarning.fire({
            title: 'Información',
            text:"Debe seleccionar una fecha"
            });

        }else{
            var mes1 = $("#fecha-grafico").datepicker('getDate').getMonth()+1;
            var anno1 = $("#fecha-grafico").datepicker('getDate').getFullYear();
            //console.log("mes: ", mes);
            //table.api().ajax.reload();
            $.ajax({
                url: "{{asset('estadisticas/informeDerivacionDatos')}}",
                type: "get",
                dataType: "json",
                data: {'anno': anno1, 'mes': mes1},
                success: function(data){
                    console.log("data derivacion: ", data);
                    table.fnClearTable();
                    $("#informacion").html('');
                    if(data.aaData.length > 0){
                        table.fnAddData(data.aaData);
                    }
                },
                error: function(error){
                    console.log("error: ", error)
                }
            });
        }
    });

    $("#tablaDocDer").on("click", "a.info-paciente", function(){
        $.ajax({
            url: $(this).prop("href"),
            type: "GET",
            success: function(data){
                console.log(data);
                $("#informacion").css({display:'initial'}).html(data);
                /* $("#modalDocumentosDerivacion2 .modal-body").html(data);
			    $("#modalDocumentosDerivacion2").modal(); */
            },
            error: function(error){
                console.log(error);
            }

        });
        return false;
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
<legend>Reporte derivación</legend>
<br>
<div class="row">
    <div class="col-sm-12">
        <label>Seleccione fecha</label>
    </div>
    <div class="col-sm-2 form-group">
        <input type="text" id="fecha-grafico" class="form-control fecha-grafico">
    </div>
    <div class="col-sm-2 form-group">
        <button id="btn-grafico" class="btn btn-primary">Buscar</button>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
        <table id="tablaDocDer" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th>Rut</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Fecha nacimiento</th>
                    <th>Fecha hospitalización</th>
                    <th>Tiempo documento</th>
                </tr>
            </thead>
            <tbody>
            
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div id="informacion" class="col-md-12">
    </div>
</div>

<div id="modalDocumentosDerivacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
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
</div>

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
