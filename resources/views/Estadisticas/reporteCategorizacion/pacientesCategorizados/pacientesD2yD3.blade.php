<script>

    

    $(function() {
        var vez = 1;// variable
        var chart = Highcharts.chart('container', {
			chart: {
				zoomType: 'xy'
			},
			title: {
				text: 'Pacientes categorizados D2 y D3'
			},
			subtitle: {
				text: 'Fuente: SIGICAM, no incluye los servicios de obtetricia, pensionado y salud mental'
			},
			xAxis: [{
				categories: ['1', '2', '3', '4', '5', '6',
					'7', '8', '9', '10', '11', '12', '13', 
					'14', '15', '16', '17', '18', '19', '20', '21', '22',
					'23', '24', '25', '26', '27', '28', '29', '30', '31'],
				crosshair: true,
				title:{
					text: 'Día del mes'
				}
			}],
			yAxis: [{
				title: {
					text: 'Pacientes D2 + D3',
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
			}],
			tooltip: {
				shared: true,
				headerFormat: 'día {point.key} <br/>',
			},
			plotOptions: {
                spline: {
                    marker: {
                        enabled: false
                    }
                }
            },
			legend: {
				layout: 'vertical',
				verticalAlign: 'bottom',
				floating: false,
				backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
			},
			series: [
			{
				name: 'Número pacientes',
				type: 'spline',
				yAxis: 0,
				data: [0,0,0],
				tooltip: {
					valueSuffix: ''
				},
				dataLabels: {
					enabled: true,
					format: "{point.y}"
				},
				enableMouseTracking: true,

			},
			{
				name: ' % Pacientes',
				type: 'column',
				yAxis: 0,
				data: [1,5,9],
				tooltip: {
					valueSuffix: ' %'
				},
				color: '#1E9966',
				dataLabels: {
					enabled: true,
					format: "{point.y}%"
				},
				enableMouseTracking: true,


			},
			{
				name: 'Referencia',
				type: 'spline',
				yAxis: 0,
				data: [15,15,15],
				tooltip: {
					valueSuffix: ' %'
				}

			}]
		});

        function mostrarCategorizadorD2yD3(){
            if (vez == 1) {
				chart.showLoading('<img src="{{URL::to('/')}}/images/default.gif">');
                vez++;
                $.ajax({
                    url: "{{asset('graficoCat')}}",
                    type: "get",
                    dataType: "json",
                    data: {'anno': 0, 'mes': 0, 'establecimiento': 8},
                    success: function(data){
						chart.hideLoading();
                        chart.update({
                            series: [
                            {
                                data: data.cantidad
                            },
                            {
                                data: data.resultados
                            },
                            {
                                data: data.limite
                            }]
                        });
                    },
                    error: function(error){
                        console.log("error: ", error)
                    }
                });
            }
            
        }
        
        
        //carga 1 vez la tabla de pacientes categorizados en d2 y d3 cuando aprieta en la primera barra
        $('#RRC3').on('click', function (e) {
            if ($( "#exTab1 ul li.nav.active" ).eq(1).attr("id") == "PC1") {
                mostrarCategorizadorD2yD3();
            }
		});

       

		$('#fecha_grafico').on('change', function (e) {
			$('#pacienteCategorizado').bootstrapValidator('revalidateField', 'fecha_grafico');
		});

		$('#categorizacionUI').on('change', function (e) {
			$('#pacienteCategorizado').bootstrapValidator('revalidateField', 'categorizacionUI');
		});

		$("#pacienteCategorizado").bootstrapValidator({
            fields:{
				fecha_grafico: {
					validators:{
						notEmpty: {
							message: 'Debe especificar la fecha'
						}
					}
				}
            }
        }).on('success.form.bv', function(e){
			chart.showLoading('<img src="{{URL::to('/')}}/images/default.gif">');
            e.preventDefault();
            var form = $(e.target);

            var mes = $("#fecha_grafico").datepicker('getDate').getMonth()+1;
			var anno = $("#fecha_grafico").datepicker('getDate').getFullYear();
			var establecimiento = $("#establecimiento").val();
			$.ajax({
				url: "{{asset('graficoCat')}}",
				type: "get",
				dataType: "json",
				data: {'anno': anno, 'mes': mes, 'establecimiento': establecimiento},
				success: function(data){
					chart.hideLoading();
					chart.update({
						series: [
						{
							data: data.cantidad
						},
						{
							data: data.resultados
						},
						{
							data: data.limite
						}]
					});
				},
				error: function(error){
					console.log("error: ", error)
				}
			});
        });
        
        var fechaExport = new Date().toJSON().slice(0,10);
		table = $('#tablaDocDer').dataTable({ 
			dom: 'Bfrtip',
			buttons: [
        		{
					extend: 'excelHtml5',
					messageTop: 'Pacientes D2 y D3 ('+fechaExport+')',
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7,8,9,10,11,12]
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
			"aaSorting": [[0, "asc"]],
			"order": [[ 6, "desc" ]],
			"columnDefs": [
				{ "orderable": false, "targets": [0,1,2,3,4,5,,7,8,9,10,11,12] }
			],
			"iDisplayLength": 10,
			"bJQueryUI": true,
			"ajax": {
					"url": "{{asset('estadisticas/pacientesD2D3Datos')}}",
					type: "get",
					dataType: "json",
					data: {'establecimiento': 8},
				},
			"oLanguage": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}
		});

        $("#establecimiento").on("change", function(){
    		var unidad=$(this).val();
    		if(unidad == 0){
    			$("#unidades").prop("disabled", true).hide();
    		}else{
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
            
            $.ajax({
				url: "{{asset('estadisticas/pacientesD2D3Datos')}}",
				type: "get",
				dataType: "json",
				data: {'establecimiento': $(this).val()},
				success: function(data){
					console.log("data derivacion: ", data);
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
        
        $("#updatePacientesD2").submit(function(ev){
            ev.preventDefault();
            console.log("hola");
            var tabla=$("#tablaDocDer").dataTable();
            tabla.fnClearTable();
            fecha = $("#fechaD2D3").val();
            $.ajax({
                url: "{{asset('estadisticas/pacientesD2D3Datos')}}",
                data: {"fecha":fecha},
                dataType: "json",
                type: "get",
                success: function(response){
                    var addData = [];
                    tabla.fnClearTable();
                    if(response.aaData.length > 0)
                        tabla.fnAddData(response.aaData);   
                },
                error: function(error){
                    console.log("error:"+JSON.stringify(error));
                    console.log(error);
                }
            });
        });
    });
    
</script>


<fieldset>
    <legend id="pacientesD2yD3">Pacientes categorizados D2 y D3</legend>
    <fieldset>

		{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'pacienteCategorizado')) }}
        <div class="col-md-12">
            <div class="col-md-3 form-group">
                <label>Seleccione fecha</label>
                <input name="fecha_grafico" type="text" id="fecha_grafico" class="form-control fecha-grafico" value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
            </div>
            @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS)
                <div class="col-sm-4 form-group">
                    <label>Establecimiento</label>
                    {{ Form::select('establecimiento', $establecimiento, 8, array('id' => 'establecimiento', 'class' => 'form-control')) }}
                </div>
			@endif				
            <div class="col-md-3">
                <button id="btn-grafico" class="btn btn-primary" style="margin-top: 20px;">Generar gráfico</button>
            </div>
        </div>
        <div class="col-md-12">
            <div id="container" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
		</div>
		{{ Form::close() }}
    </fieldset>
    <legend>Pacientes D2 y D3 actualmente</legend>
    <div class="row">
        {{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updatePacientesD2', 'style' => 'padding-left: 15px;')) }}
        <div class="form-group">
            {{Form::text('fechaD2D3', $fecha, array('id' => 'fechaD2D3', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
		</div>

        {{--
        @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
            <div class="form-group">
                {{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
            </div>
        @endif
        --}}
                    <!--<div class="form-group">
        {{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
    </div>-->
        <div class="form-group">
            {{Form::submit('Actualizar', array('class' => 'btnUpdateCategorizacion', 'class' => 'btn btn-primary')) }}
        </div>
        {{ Form::close() }}
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
            <table id="tablaDocDer" class="table table-condensed table-hover" style = "overflow-x: scroll;">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Rut</th>
                        <th>Comuna</th>
                        <th>Diagnóstico</th>
                        <!-- <th>Exámenes pendientes</th> -->
                        <th>Cama</th>
                        <th>Sala</th>
                        <th>Servicio</th>
                        <th>Área funcional</th>
                        <th>Comentario</th>
                        <th>Fecha hospitalización</th>
                        <th>Tiempo estada</th>
                        <th>Categorización</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
            </div>
        </div>
    </div>
</fieldset>