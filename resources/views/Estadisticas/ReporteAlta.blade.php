@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de Egresos</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
{{ HTML::style('css/navegadortab.css') }}
@stop

@section("script")

<script>

	function validarFolio(validar){
		if(validar == true){
			$("#inicioFolio").focus($("#inicioFolio").css({
				'border':'1px solid #a94442'
			}));
			$("#errorFolio").html('Debe ingresar el número de folio').css({
				'color': '#a94442'
			});
		}else{
			$("#inicioFolio").focus($("#inicioFolio").css({
				'border': '1px solid #ccc'
			}));
			$("#errorFolio").html('');
		}
	}

	function mensajeFechaInicioVacia(){
		swalWarning.fire({
			title: 'Información',
			html:"<h4>Debe seleccionar la fecha de inicio</h4>"
		});
	}

	function mensajeFechaTerminoVacia(){
		swalWarning.fire({
			title: 'Información',
			html:"<h4>Debe seleccionar la fecha de termino</h4>"
		});
	}

	function mensajeFechaInicioMayor(){
		swalWarning.fire({
			title: 'Información',
			html:"<h4>La fecha de inicio debe ser menor que la de termino</h4>"
		});
	}

	var getDatosIngresos = function(fecha_desde, fecha, estab){
		estab = typeof estab !== 'undefined' ? estab : '';
        //if (estab != '') estab = "/" + estab;
        $.ajax({
            url: '{{URL::route("estAlta")}}/datosIngresos',
            data: {"fecha-inicio":fecha_desde, "fecha":fecha, "estab":estab},
            dataType: "json",
            type: "get",
            success: function(response){
				var addData = [];

				$.each(response.especialidades, function(index, value) {
					addData.push([value[0], value[1]]);
				});


				var tablaIngresos=$("#tablaIngresos").dataTable();

                tablaIngresos.fnClearTable();
                if(addData.length > 0)
                	tablaIngresos.fnAddData(addData);

            },
            error: function(error){
            	console.log("error:"+JSON.stringify(error));
                console.log(error);
            }
        });
    }

	var getDatos = function(fecha_desde, fecha, estab){
		estab = typeof estab !== 'undefined' ? estab : '';
        //if (estab != '') estab = "/" + estab;
		$("#btnUpdate").attr('disabled', true);
		showLoad();
        $.ajax({
            url: '{{URL::route("estAlta")}}/datos',
            data: {"fecha-inicio":fecha_desde, "fecha":fecha, "estab":estab},
            dataType: "json",
            type: "get",
            success: function(response){

				var addData = [];

				$.each(response.especialidades, function(index, value) {
					addData.push([value[0], value[1]]);
				});


				var tabla=$("#tablaAlta").dataTable();

                tabla.fnClearTable();
                if(addData.length > 0)
				tabla.fnAddData(addData);
				hideLoad();
				$("#btnUpdate").attr('disabled', false);
            },
            error: function(error){
            	console.log("error:"+JSON.stringify(error));
                console.log(error);
				hideLoad();
				$("#btnUpdate").attr('disabled', false);
            }
        });
    }

    $(function() {
    	$("#estadistica").collapse();

    	$(".fecha-sel").datetimepicker({
    		format: "DD-MM-YYYY",
			locale: 'es'
    	});

		$('#tablaIngresos').dataTable({
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 15,
            "bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

        $('#tablaAlta').dataTable({
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 15,
            "bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });


    	$("#establecimiento").on("change", function(){
    		var unidad=$(this).val();
    		if(unidad == 0){
    			$("#unidades").prop("disabled", true).hide();
    		}
    		else{
    			$("#unidades").prop("disabled", false).show();
    			$.ajax({
    				url: "getUnidades",
    				type: "get",
    				dataType: "json",
    				data: {unidad: unidad},
    				success: function(data){
    					$("#unidades").empty();
    					for(var i=0; i < data.length; i++){
    						var option="<option value='"+data[i].id+"'>"+data[i].alias+"</option>";
    						$("#unidades").append(option);
    					}
    					if(data.length == 0) $("#unidades").append("<option value='0'>Todos</option>");
    				},
    				error: function(error){
    					console.log(error)
    				}
    			});
    		}
    	});

		//estadisticas de ingresos
		$("#updateEstadisticaIngresos").submit(function(ev){
			ev.preventDefault();
			getDatosIngresos($("#fechaInicioIngreso").val(), $("#fechaFinIngreso").val(), $("#establecimiento").val());
			return false;
		});

		//estadisticas de egresos
		$("#btnUpdate").click(function(){
			var fechaInicio = $("#fecha-inicio").val();
			var fechaTermino = $("#fecha").val();
			if(fechaInicio == ""){
				mensajeFechaInicioVacia();
			}else if(fechaTermino == ""){
				mensajeFechaTerminoVacia();
			}else{
				if(fechaInicio != "" && fechaTermino != ""){
					var inicio = new Date($("#fecha-inicio").data("DateTimePicker").date().toDate());
					var termino = new Date($("#fecha").data("DateTimePicker").date().toDate());
					if(inicio > termino){
						mensajeFechaInicioMayor();
					}else{
						getDatos($("#fecha-inicio").val(), $("#fecha").val(), $("#establecimiento").val());			
					}
				}
			}
		});

		//PDF ingresos
		$("#informeIngresos").click(function(){
			fecha_inicio = $("#fechaInicioIngreso").val();
			fecha_fin =  $("#fechaFinIngreso").val();
			estab = $("#establecimiento").val();
			window.location.href = "{{url('estadisticas/IngresosYEgresos/pdfInformeIngresos')}}"+"/"+fecha_inicio+"/"+fecha_fin+"/"+estab;
		});

		//EXCEL ingresos Serena
		$("#informeIngresosExcel").click(function(){
			fecha_inicio = $("#fechaInicioIngreso").val();
			fecha_fin =  $("#fechaFinIngreso").val();
			estab = $("#establecimiento").val();
			window.location.href = "{{url('estadisticas/IngresosYEgresos/excelInformeIngresos')}}"+"/"+fecha_inicio+"/"+fecha_fin+"/"+estab;
		});

		
		//PDF egresos
		$("#informeEgreso").click(function(){
			fecha_inicio = $("#fecha-inicio").val();
			fecha_fin =  $("#fecha").val();
			estab = $("#establecimiento").val();
			reporte = 'pdf';
			if(fecha_inicio == ""){
				mensajeFechaInicioVacia();
			}else if(fecha_fin == ""){
				mensajeFechaTerminoVacia();
			}else{
				if(fecha_inicio != "" && fecha_fin != ""){
					var inicio = new Date($("#fecha-inicio").data("DateTimePicker").date().toDate());
					var termino = new Date($("#fecha").data("DateTimePicker").date().toDate());
					if(inicio > termino){
						mensajeFechaInicioMayor();
					}else{
						window.location.href = "{{url('estadisticas/IngresosYEgresos/informeEgreso')}}"+"/"+fecha_inicio+"/"+fecha_fin+"/"+reporte+"/"+estab;	
					}
				}
			}
		});

		//Excel egresos
		$("#excelEgreso").click(function(){
			fecha_inicio = $("#fecha-inicio").val();
			fecha_fin =  $("#fecha").val();
			estab = $("#establecimiento").val();
			reporte = 'excel';
			if(fecha_inicio == ""){
				mensajeFechaInicioVacia();
			}else if(fecha_fin == ""){
				mensajeFechaTerminoVacia();
			}else{
				if(fecha_inicio != "" && fecha_fin != ""){
					var inicio = new Date($("#fecha-inicio").data("DateTimePicker").date().toDate());
					var termino = new Date($("#fecha").data("DateTimePicker").date().toDate());
					if(inicio > termino){
						mensajeFechaInicioMayor();
					}else{
						window.location.href = "{{url('estadisticas/IngresosYEgresos/informeEgreso')}}"+"/"+fecha_inicio+"/"+fecha_fin+"/"+reporte+"/"+estab;
					}
				}
			}
		});

		$("#informeAccess").click(function(){
			fecha_inicio = $("#fecha-inicio").val();
			fecha_fin =  $("#fecha").val();
			estab = $("#establecimiento").val();
			window.location.href = "{{url('access/generarXls')}}"+"/"+fecha_inicio+"/"+fecha_fin+"/"+estab;
		});


		$("#foliar").click(function(){
			fecha_inicio = $("#fecha-inicio").val();
			fecha_fin =  $("#fecha").val();
			estab = $("#establecimiento").val();
			reporte = 'excel';
			if(fecha_inicio == ""){
				mensajeFechaInicioVacia();
			}else if(fecha_fin == ""){
				mensajeFechaTerminoVacia();
			}else{
				if(fecha_inicio != "" && fecha_fin != ""){
					var inicio = new Date($("#fecha-inicio").data("DateTimePicker").date().toDate());
					var termino = new Date($("#fecha").data("DateTimePicker").date().toDate());
					if(inicio > termino){
						mensajeFechaInicioMayor();
					}else{
						validarFolio(validar=false);
						$("#inicioFolio").val('');
						$("#modalFoliar").modal("show");
					}
				}
			}
		});

		$("#informeFoliados").click(function(){
			fecha_inicio = $("#fecha-inicio").val();
			fecha_fin =  $("#fecha").val();
			estab = $("#establecimiento").val();
			folio = $("#inicioFolio").val();
			if(folio != ""){
				validarFolio(validar=false);
				window.location.href = "{{url('estadisticas/IngresosYEgresos/pdfInformeFoliados')}}"+"/"+fecha_inicio+"/"+fecha_fin+"/"+estab+"/"+folio;
			}else{
				validarFolio(validar=true);
				swalWarning.fire({
					title: 'Información',
					html:"<h4>Debe ingresar el número de folio</h4>"
				});
			}

		});

		$("#inicioFolio").keyup(function() {
			validar = ($(this).val() == "") ? true : false;
			validarFolio(validar);
		});

		$(".fecha-sel").on("dp.change keyup", function() {
			if($(this).val() == ""){
                $(this).focus($(this).css({
                    'border': '1px solid #a94442'
                }));
                $(this).next().html('Debe ingresar una fecha').css({
				'color': '#a94442'
				});
            }else{
                $(this).focus($(this).css({
                    'border': '1px solid #ccc'
                }));
                $(this).next().html('');
            }
        });

		getDatos( '{{  \Carbon\Carbon::now()->subDays(7)->format("d-m-Y") }}','{{  \Carbon\Carbon::now()->format("d-m-Y") }}' );
        //getDatosIngresos( '{{  \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y") }}','{{  \Carbon\Carbon::now()->format("d-m-Y") }}' );
    });
</script>

@stop

@section("section")
	<legend>Reporte de Egresos</legend>

	<fieldset>

		<div class="container" >
            <ul class="nav nav-tabs">
                <li class="nav active"><a href="#RIngresos" data-toggle="tab">Reporte de Ingresos</a></li>
                <li class="nav"><a href="#REgresos" data-toggle="tab">Reporte de Egresos</a></li>                
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">

                <div class="tab-pane fade in active" style="padding-top:10px;" id="RIngresos">
                    <fieldset>
						<legend>Seleccionar mes</legend>

						<div class="row">
							<div class="col-sm-6">
								{{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadisticaIngresos', 'style' => 'padding-left: 15px;')) }}
								<div>
									<label for="inicio">Inicio</label>
									<label style="margin-left: 30%;" for="inicio">Termino</label>
								</div>
								<div class="form-group">
									{{Form::text('fechaInicioIngreso', \Carbon\Carbon::now()->subDays(7)->format("d-m-Y"), array('id' => 'fechaInicioIngreso', 'class' => 'form-control fecha-sel'))}}
								</div>
								<div class="form-group">
									{{Form::text('fechaFinIngreso', $fecha, array('id' => 'fechaFinIngreso', 'class' => 'form-control fecha-sel'))}}
								</div>
								{{-- if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
									<div class="form-group">
										 Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento'))
									</div>
								else --}}
									{{Form::hidden('establecimiento',$estab, array("id"=>"establecimiento"))}}
								{{-- endif --}}
									<div class="form-group">
										{{Form::submit('Actualizar', array('id' => 'btnUpdateIngresos', 'class' => 'btn btn-primary')) }}
									</div>
								{{ Form::close() }}

							</div>
							<div class="col-sm-6" style="margin-top: 22px;">
								<button id="informeIngresos" class="btn btn-danger">PDF Ingresos</button>
								<button id="informeIngresosExcel" class="btn btn-success">Exel Ingresos</button>
								{{-- <button id="informeAccess" class="btn btn-primary">Access</button> --}}
								{{--<button id="informeAccess" class="btn btn-primary">Access</button>--}}
								{{-- <button id="foliar" class="btn btn-primary" data-toggle="modal" data-target="#modalFoliar">Egresos foliados</button> --}}
							</div>

						</div>
					</fieldset>

					<br><br>
					<div id="contenidoIngresos"></div>

					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
							<table id="tablaIngresos" class="table  table-condensed table-hover">
								<thead>
									<tr>
									  	<th>Unidad</th>
										<th>Total Ingresos</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
				
                <div class="tab-pane fade" style="padding-top:10px;" id="REgresos">
                    <fieldset>
						<legend>Seleccionar fecha </legend>
						{{-- <p><b>Nota:</b> Esta tabla no considera pacientes egresados en "Espera de cama" ni tampoco "Espera de hospitalización"</p> --}}

						<div class="row">
							<div class="col-sm-6">
								{{-- {{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadistica', 'style' => 'padding-left: 15px;')) }} --}}
								<div>
									<label for="inicio">Inicio</label>
									<label style="margin-left: 30%;" for="inicio">Termino</label>
								</div>
								<div class="col-sm-4 form-group">
									{{-- {{Form::text('fecha-inicio', \Carbon\Carbon::now()->subDays(7)->format("d-m-Y"), array('id' => 'fecha-inicio', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}} --}}
									<input type="text" name="fecha-inicio" id="fecha-inicio" class="form-control fecha-sel" value="{{\Carbon\Carbon::now()->subDays(7)->format("d-m-Y")}}">
									<span class="errorFecha"></span>
								</div>
								<div class="col-sm-4 form-group">
									{{-- {{Form::text('fecha', $fecha, array('id' => 'fecha', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}} --}}
									<input type="text" name="fecha" id="fecha" class="form-control fecha-sel" value="{{$fecha}}">
									<span class="errorFecha"></span>
								</div>
								@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
									<div class="form-group">
										{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
									</div>
								@else
									{{-- {{Form::hidden('establecimiento',$estab, array("id"=>"establecimiento"))}} --}}
									<input type="hidden" name="establecimiento" value="{{$estab}}" id="establecimiento">
								@endif

											<!--<div class="form-group">
								{{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
							</div>-->
									<div class="form-group">
										{{-- {{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary')) }} --}}
										<button class="btn btn-primary" id="btnUpdate"> Actualizar</button>
									</div>
									{{-- {{ Form::close() }} --}}

							</div>
							<div class="col-sm-6" style="margin-top: 22px;">
								<button id="informeEgreso" class="btn btn-danger">PDF Egresos</button>
								<button id="excelEgreso" class="btn btn-success">Excel Egresos</button>
								{{-- <button id="informeAccess" class="btn btn-primary">Access</button> --}}
								{{--<button id="informeAccess" class="btn btn-primary">Access</button>--}}
								<button id="foliar" class="btn btn-primary">Egresos foliados</button>
							</div>

						</div>
					</fieldset>

					<br><br>
					<div id="contenido"></div>

					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
							<table id="tablaAlta" class="table  table-condensed table-hover">
								<thead>
									<tr>

									  <th>Unidad</th>
										<th>Total altas</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							</div>
						</div>
					</div>
					
                </div>
                
            </div>
        </div>
		
	</fieldset>

	<br><br>


	<!-- Modal -->
	<div class="modal fade" id="modalFoliar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputEmail1">Número de folio</label>
						<input type="number" id="inicioFolio" class="form-control">
						<span id="errorFolio"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="informeFoliados" class="btn btn-primary">imprimir</button>
				</div>
			</div>
		</div>
	</div>

@stop
