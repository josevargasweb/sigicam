@extends("Templates/template")
{{ HTML::script('js/ProgressBar.js') }}

{{ HTML::style('css/ProgressBar.css') }}

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de camas</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")

<script>

	$(".tablaOcupados").hide();
	
	var actualizarListaUnidades = function(unidad){
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
						var option="<option value='0'>Todas las unidades</option>";
    					$("#unidades").append(option);
    					if(data.length == 0) $("#unidades").append("<option value='0'>Todas las unidades</option>");
    				},
    				error: function(error){
    					console.log(error)
    				}
    			});
    		}
    	}
	@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
		actualizarListaUnidades($("#establecimiento").val());
	@endif
	var getDatos = function(fecha, estab, unidad){
		estab = typeof estab !== 'undefined' ? estab : '';
		unidad = typeof unidad !== 'undefined' ? unidad : '';

        if (estab != '') estab = "/" + estab;
        if (unidad != '') unidad = "/" + unidad;
        $.ajax({
            url: '{{URL::route("estCamas")}}/datos/' + fecha + estab + unidad,
            dataType: "json",
            type: "get",
            success: function(data){

				


            	data.g_ranking.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
            	data.g_estadia.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
                if(data.g_tipos.plotOptions.column != null)
            	{
					data.g_tipos.plotOptions.column.dataLabels.formatter = function(){if(this.y==0) return ''; else return this.y;};
            	}
                /* $('#dotacionCamas').highcharts(data.g_dotacion); */
				$('#dotacionCamas').highcharts(data.nueva_dotacion);
                $('#graficoRanking').highcharts(data.g_ranking);
                $('#graficoMensual').highcharts(data.g_estadia);
                /* $('#dotacionTipos').highcharts(data.g_tipos); */
				$('#dotacionTipos').highcharts(data.nuevo_tipos);
				
                /* $('#cmdisp').html(data.r_dotacion.disponibles); */
				$('#cmdisp').html(data.r_dotacion.nueva_disponibles);
                /* $('#cmocup').html(data.r_dotacion.ocupadas); */
				$('#cmocup').html(data.r_dotacion.nueva_ocupadas);
                /* $('#cmresv').html(data.r_dotacion.reservadas); */
				/* $('#cmdhab').html(data.r_dotacion.deshabilitadas); */
                $('#cmdhab').html(data.r_dotacion.nueva_deshabilitadas);

				/* var tablaDotacion = $("#js-tabla").dataTable();
				tablaDotacion.fnClearTable();
				for (let index = 0; index < data['detalles_dotacion'].length; index++) { 
                    
					
                    tablaDotacion.fnAddData([ 
                        data['detalles_dotacion'][index].establecimiento, 
                        data['detalles_dotacion'][index].servicio, 
                        data['detalles_dotacion'][index].estado_cama,
                        data['detalles_dotacion'][index].rut, 
                        data['detalles_dotacion'][index].nombre, 
                        data['detalles_dotacion'][index].fecha_solicitud, 
                        data['detalles_dotacion'][index].fecha_asignacion                  
                    ]); 
                     
                } 
				 */
				 
				var tablaTipo = $("#js-tabla").dataTable();
				$(".tablaOcupados").show();
				tablaTipo.fnClearTable();
				for (let index = 0; index < data['detalles_tipo'].length; index++) { 

                    tablaTipo.fnAddData([ 
                        data['detalles_tipo'][index].establecimiento, 
                        data['detalles_tipo'][index].servicio, 
                        data['detalles_tipo'][index].estado,
						data['detalles_tipo'][index].tipo_cama,
                        data['detalles_tipo'][index].rut, 
                        data['detalles_tipo'][index].nombre, 
                        data['detalles_tipo'][index].fecha_solicitud, 
                        data['detalles_tipo'][index].fecha_asignacion                  
                    ]); 
                     
                } 
				
				var tabla=$("#js-tabla3").dataTable();
            },
            error: function(error){
                console.log(error);
            }
        });
    }


    $(function() {
    	$("#estadistica").collapse();

    	$("#fecha").datepicker({
			autoclose: true,
			language: "es",
			format: "dd-mm-yyyy",
			todayHighlight: true,
			endDate: "+0d"
		});

		$('#js-tabla').dataTable({
            "aaSorting": [[0, "desc"]],
            //dom: 'Bfrtip',
            //buttons: ['excel'],
            "iDisplayLength": 10,
            "bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

		@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
				$("#establecimiento").on("change", function(){
					var unidad=$(this).val();
					actualizarListaUnidades(unidad);
				});
		@else
				actualizarListaUnidades( {{ Session::get("usuario")->establecimiento }} );
		@endif

		$("#updateEstadistica").submit(function(ev){
			ev.preventDefault();
			getDatos($("#fecha").val(), $("#establecimiento").val() , $("#unidades").val() );
			return false;
		});

		getDatos( '{{  date('d-m-Y') }}' );

    });
</script>

@stop

@section("section")

<fieldset>
	<legend>Reporte de camas</legend>
	<div class="row">
		{{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadistica', 'style' => 'padding-left: 15px;')) }}
		<div class="form-group">
			{{Form::text('fecha', $fecha, array('id' => 'fecha', 'class' => 'form-control', 'placeholder' => 'Fecha'))}}
		</div>
		@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
		<div class="form-group">
			{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
		</div>
		<div class="form-group">
			{{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
		</div>
		@else
		<div class="form-group">
			{{ Form::hidden('establecimiento', Session::get("usuario")->establecimiento, array('id' => 'establecimiento')) }}
			{{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'style' => 'display: none;')) }}

		</div>
		@endif
		<div class="form-group">
			{{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary')) }}
		</div>
		{{ Form::close() }}
	</div>
	<br><br>
	<div class="row tablaOcupados">
		<div class="col-md-4">
			<ul class="list-group">
				<li class="list-group-item"><span id="cmdisp" class="badge"></span> <strong>Camas disponibles</strong> </li>
				<li class="list-group-item"><span id="cmocup" class="badge"></span> <strong>Camas ocupadas</strong> </li>
				{{-- <li class="list-group-item"><span id="cmresv" class="badge"></span> <strong>Camas reservadas</strong> </li> --}}
				<li class="list-group-item"><span id="cmdhab" class="badge"></span> <strong>Camas deshabilitadas</strong> </li>
			</ul>
		</div>

		<div class="col-md-8">
			<div id="dotacionCamas"></div>
		</div>

	</div>

	<div class="row tablaOcupados">
		<div class="col-md-12">
			<table id="js-tabla" class="table  table-condensed table-hover">
            <thead>
				<tr>
					<th>Establecimiento</th>
					<th>Servicio</th>
					<th>Estado Cama</th>
					<th>Tipo Cama</th>
					<th>Run paciente</th>
					<th>Nombre paciente</th>
					<th>Fecha solicitud</th>
					<th>Fecha asignacion</th>
				</tr>
            </thead>
          <tbody>

          </tbody>
        </table>
		</div>
	</div>

	<div class="row" style="margin-top: 20px;">
		<div class="col-md-12">
			<div id="dotacionTipos"></div>
		</div>
	</div>

	{{-- <div class="row">
		<div class="col-md-12">
			<table id="js-tabla2">
            <thead>
				<th>Establecimiento</th>
				<th>Servicio</th>
				<th>Estado Cama</th>
				<th>Tipo Cama</th>
				<th>Run paciente</th>
				<th>Nombre paciente</th>
				<th>Fecha solicitud</th>
				<th>Fecha asignacion</th>
            </thead>
          <tbody>

          </tbody>
        </table>
		</div>
	</div> --}}

	<div class="row" style="margin-top: 20px;">
		<div class="col-md-12">
			<div id="graficoMensual"></div>
		</div>
	</div>

	<div class="row" style="margin-top: 20px;">
		<div class="col-md-12">
			<div id="graficoRanking"></div>
		</div>
	</div>

	
</fieldset>
<br><br>
@stop
