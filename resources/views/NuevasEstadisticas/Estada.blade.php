@extends("Templates/template")

@section("titulo")
Reporte de estada
@stop

@section("miga")
<li><a href="#">Estadísticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de estada</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
{{ HTML::style('css/navegadortab.css') }}
@stop

@section("script")
<script>

    var excelEntregaTurno=function(){
        var fechaIngresada = $("#fechaEntregaTurno").val();
        var fecha = moment(fechaIngresada, 'DD-MM-YYYY');

        if(fechaIngresada == ''){
                    swalWarning.fire({
					title: 'Información',
					text:"La fecha no debe quedar vacía"
					});
        }else{
            var now = moment();
            var now2 = moment(now).format('DD-MM-YYYY');
            var q = moment(fecha).isAfter(now);
            if(q == true){
                  swalWarning.fire({
					title: 'Información',
					text:"La fecha seleccionada no debe ser mayor a la fecha actual"
					});
            }else{
                // bootbox.alert("<h4>funciona</h4>");
                location.href="{{asset('estadisticas/camas/estadisticaCambioTurno')}}/"+fechaIngresada;
            }
        }
 	}
//pruebas
$(function() {
    $("#estadistica").collapse();

    $("#fechaEntregaTurno").datepicker({
        maxDate: 0, //no se porque no funciona :c
        format: 'dd-mm-yyyy',
        endDate: "+0d"
    });

    // $("#fechaEntregaTurno").on('keyup change', function(){
    //     var fecha = $(this).val();
    //     if(fecha == ''){
    //         bootbox.alert("<h4>La fecha no puede quedar vacia</h4>");
    //     }
    // });

    var fechaExport = new Date().toJSON().slice(0,10);
    table=$('#listaEsperaResumen').dataTable({
        dom: 'Bfrtip',
			buttons: [
        		{
                    extend: 'excelHtml5',
					messageTop: 'Reporte de estada ('+fechaExport+')',
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7,8,9,10,11]
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
        "order": [[ 10, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [0,1,2,3,4,5,6,7,8,9,11,12] }
        ],
        "bJQueryUI": true,
        "iDisplayLength": 10,
        //"order": [[ 5, "asc" ]],
        "ajax": "{{asset('estadisticas/camas/estadisticaEstada')}}",
        "language": {
            "sUrl": "{{URL::to('/')}}/js/spanish.txt"
        },
        "sPaginationType": "full_numbers",
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            //console.log("hola", nRow);
        }
    });

    $("#dias").on('change', function(){
        var dias = $(this).val();
        $.ajax({
            url: "{{asset('estadisticas/camas/estadisticaEstada')}}",
            type: "get",
            dataType: "json",
            data: {'dias': dias},
            success: function(data){
                table.fnClearTable();
                if(data.aaData.length > 0){
                    table.fnAddData(data.aaData);
                }
                var texto = "Personas con estadía superior a los "+dias+" días hospitalizados en el establecimiento.";
                $("#superior").text(texto);
            },
            error: function(error){
                console.log("error:"+JSON.stringify(error));
                console.log(error);
            }
        });
    });

    table2=$('#listaEsperaServicio').dataTable({
        "bJQueryUI": true,
        "iDisplayLength": 10,
        //"order": [[ 5, "asc" ]],
        "ajax": {
            "url": "{{asset('estadisticas/camas/estadisticaEstadaTotal')}}",
            "type": "post"
        },
        "language": {
            "sUrl": "{{URL::to('/')}}/js/spanish.txt"
        },
        "sPaginationType": "full_numbers",
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        }
    });

    $("#fecha-inicio").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});
    $("#fecha-fin").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});
    $("#fechaEntregaTurno").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});

    $("#formPromedioEstada").bootstrapValidator({
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		fields: {
			'fecha-inicio': {
				validators:{
					notEmpty:{
						message: "Debe ingresar la fecha"
                    },
                    callback: {
						callback: function(value, validator, $field){
							var esMayor=compararFecha(value, $("#fecha-fin").val());
							if(esMayor){
                                return {valid: false, message: "La fecha inicio no puede ser mayor a la fecha fin"};
							}else{
                                //$('#formPromedioEstada').bootstrapValidator('revalidateField', 'fecha-fin');
                            }
							var esValidao=validarFormatoFecha(value);
							if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
							return true;
                        }
                    }
				}
			},
			'fecha-fin': {
				validators: {
					notEmpty: {
							message: 'Debe ingresar la fecha'
                        },
                        callback: {
						callback: function(value, validator, $field){
							var esMayor=compararFecha($("#fecha-inicio").val(), value);
							if(esMayor){
								return {valid: false, message: "La fecha fin no puede ser menor a la fecha inicio"};
							}else{
                                $('#formPromedioEstada').bootstrapValidator('revalidateField', 'fecha-inicio');
                            }
							var esValidao=validarFormatoFecha(value);
							if(!esValidao) return {valid: false, message: "Formato de fecha inválido"};
							return true;
						}
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on('error.form.bv', function(e) {
		console.log(e);
	}).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
        var $form = $(evt.target);
		var form = $(this).serializeArray();
        $.ajax({
            url: "{{asset('estadisticas/camas/estadisticaEstadaTotal')}}",
            type: "post",
            dataType: "json",
            data: form,
            success: function(data){
                table2.fnClearTable();
                if(data.aaData.length > 0){
                    table2.fnAddData(data.aaData);
                }
            },
            error: function(error){
                console.log("error:"+JSON.stringify(error));
                console.log(error);
            }
        });
	});
});

//PDF A GENERAR
$("#listaEsperaResumenPdf").click(function(){
  dias = $("#dias").val();
  reporte = 'pdf';
  window.location.href = "{{url('estadisticas/camas/estadisticaEstadaReporte')}}/"+dias+"/"+reporte;
});

$("#listaEsperaResumenExcel").click(function(){
  dias = $("#dias").val();
  reporte = 'excel';
  window.location.href = "{{url('estadisticas/camas/estadisticaEstadaReporte')}}/"+dias+"/"+reporte;
});

</script>

@stop

@section("section")
    <div class="container">
        <ul class="nav nav-pills primerNav">
            <li class="nav in active"><a href="#cantidad-dias" data-toggle="tab">Reporte por cantidad de días</a></li>
            <li class="nav"><a href="#servicio" data-toggle="tab">Reporte por servicio</a></li>
            <li class="nav"><a href="#entrega" data-toggle="tab">Reporte entrega de turno</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade in active" style="padding-top:10px;" id="cantidad-dias">
                <legend>Reporte de Estada</legend>
                <div class="col-md-12" style="margin-left: -28px;">
                    <div class="col-md-3">
                        <label>Seleccione cantidad de días</label>
                        {{ Form::select('dias', $dias, 6, array('id' => 'dias', 'class' => 'form-control')) }}
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <button id="listaEsperaResumenPdf" class="btn btn-danger" style="margin-top: 23px;">Descarga de PDF</button>
                    </div>
                    <div class="col-md-3">
                        <button id="listaEsperaResumenExcel" class="btn btn-success" style="margin-top: 23px;">Descarga de Excel </button>
                    </div>
                </div>

                <div class="table-responsive col-sm-12 sin-pad-izq">
                <div class="row col-sm-12">
                    <p id="superior">Personas con estadía superior a los 6 días hospitalizados en el establecimiento.</p>

                </div>
                    <table id="listaEsperaResumen" class="table  table-condensed table-hover">

                        <thead>
                            <tr>

                                <th>Run</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Fecha nacimiento</th>
                                <th>Diagnóstico</th>
                                <th>Complemento diagnóstico</th>
                                <th>Fecha solicitud</th>
                                <th>Fecha asignación</th>
                                <th width="120">Fecha hospitalización</th>
                                <th>Cama</th>
                                <th>Sala</th>
                                <th>Área funcional</th>
                                <th>Opciónes</th>
                            </tr>
                        </thead>

                        <tbody></tbody>

                    </table>
                </div>
            </div>
            <div class="tab-pane fade" style="padding-top:10px;" id="servicio">
                {{-- <div class="col-sm-12 sin-pad-izq" style="margin-top:30px;">
                    <h4>Promedio de estada por servicio</h4>
                </div> --}}
                <legend>Promedio de estada por servicio</legend>
                <div class="col-sm-12 sin-pad-izq" style="margin-top:15px; margin-bottom:15px;">
                    {{ Form::open(array('class' => 'form-horizontal',  'id' => 'formPromedioEstada', 'autocomplete' => 'off')) }}
                        <div class="form-group col-md-3">
                            <div class="col-sm-12 sin-pad-izq">
                                <label for="fecha-inicio" class="control-label" title="Fecha de nacimiento">Fecha de inicio </label>
                                {{Form::text('fecha-inicio', date('d-m-Y'), array('id' => 'fecha-inicio', 'class' => 'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <div class="col-sm-12">
                                <label for="fecha-fin" class="control-label" title="Fecha de nacimiento">Fecha fin </label>
                                {{Form::text('fecha-fin', date('d-m-Y'), array('id' => 'fecha-fin', 'class' => 'form-control'))}}
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label for="btn-promedio" class="control-label" title="Fecha de nacimiento">&nbsp;&nbsp;</label>
                            {{ Form::submit("Buscar", array("id" => "btn-promedio", "class" => "btn btn-primary")) }}
                        </div>
                    {{ Form::close() }}
                </div>

                <div class="table-responsive col-sm-6 sin-pad-izq">
                    <table id="listaEsperaServicio" class="table  table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" style="padding-top:10px;" id="entrega">
                <div class="col-sm-12 sin-pad-izq" style="margin-bottom:15px;">
                    <div class="form-group col-md-2">
                        <div class="col-sm-12 sin-pad-izq">
                            <label for="fecha" class="control-label" title="Fecha entrega de turno">Fecha: </label>
                            {{Form::text('fecha', date('d-m-Y'), array('id' => 'fechaEntregaTurno', 'class' => 'form-control','readonly'))}}
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label for="btn-cambio" class="control-label" title="">&nbsp;&nbsp;</label>
                        <button id="btn_cambio" class="btn btn-success" onclick="excelEntregaTurno()">Descargar Excel</button>
                    </div>
                </div>


            </div>
    </div>
@stop
