@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de casos sociales</a></li>
@stop

@section("estilo-tablas-verdes")
{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("script")

<script>	
restaFechas = function(f1,f2)
 {
    //2015/01/01
    //01/01/2015
	if (f1 == null) {
		return "No disponible";
	}else{
		var aFecha1 = f1.split('-');
		var aFecha2 = f2.split('-');
		var fFecha1 = Date.UTC(aFecha1[0],aFecha1[1]-1,aFecha1[2]);
		var fFecha2 = Date.UTC(aFecha2[0],aFecha2[1]-1,aFecha2[2]);
		var dif = fFecha2 - fFecha1;
		var anios = Math.floor((dif / (1000 * 60 * 60 * 24))/365);
		return anios;
	}	
	
 }


	
    $(function() {

		var getDatos = function(fecha_desde, fecha, estab){
			console.log("obteniendo datos de casos sociales");
			estab = typeof estab !== 'undefined' ? estab : '';
			//if (estab != '') estab = "/" + estab;
			$.ajax({
				url: '{{URL::route("estCasoSocial")}}/datos',
				data: {"fecha-inicio":fecha_desde, "fecha":fecha, "estab":estab},
				dataType: "json",
				type: "get",
				success: function(data){
					$('#casos').highcharts(data.g_caso_social);

					fecha = new Date();
					fecha = fecha.getFullYear() + "-" + (fecha.getMonth()+1) + "-" +fecha.getDate();
					var addData = [];
					//console.log("datos: ",data.tabla_caso_social.length);
					for(var i=0;i<data.tabla_caso_social.length;i++){
						//console.log(data.tabla_caso_social[i]);

						rutCompleto = data.tabla_caso_social[i].rut + "-" + data.tabla_caso_social[i].dv;

						nombreCompleto = data.tabla_caso_social[i].nombre_paciente + " " + data.tabla_caso_social[i].apellido_paterno + " " + data.tabla_caso_social[i].apellido_materno;
						
						addData.push([
							data.tabla_caso_social[i].caso_social?"abierto":"cerrado",
							rutCompleto,
							nombreCompleto,
							restaFechas(data.tabla_caso_social[i].fecha_nacimiento, fecha),
							data.tabla_caso_social[i].nombre,
							 ''
						]);

					}
					console.log("addData",addData);
					var tabla=$("#js-tabla").dataTable();


					tabla.fnClearTable();
					if(addData.length > 0)
					tabla.fnAddData(addData);



				},
				error: function(error){
					console.log("error:"+JSON.stringify(error));
					console.log(error);
				}
			});
		}


    	$("#estadistica").collapse();

    	$(".fecha-sel").datepicker({
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

		$("#updateEstadistica").submit(function(ev){
			ev.preventDefault();
			getDatos($("#fecha-inicio").val(), $("#fecha").val(), $("#establecimiento").val());
			return false;
		});

		getDatos( '{{  \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y") }}','{{  \Carbon\Carbon::now()->format("d-m-Y") }}' );

    });
</script>

@stop

@section("section")
	<fieldset>
		<legend>Reporte de casos sociales</legend>

		<div class="row">
			{{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadistica', 'style' => 'padding-left: 15px;')) }}
            <div class="form-group">
                {{Form::text('fecha-inicio', \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y"), array('id' => 'fecha-inicio', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
            </div>
            <div class="form-group">
				{{Form::text('fecha', $fecha, array('id' => 'fecha', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
			</div>
			@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
				<div class="form-group">
					{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
				</div>
			@endif
						<!--<div class="form-group">
			{{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
		</div>-->
				<div class="form-group">
					{{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary')) }}
				</div>
				{{ Form::close() }}
		</div>
		<br><br>

        <br>

		<div id="casos"></div>
		<br><br>
		<fieldset>
			<table id="js-tabla" class="table  table-condensed table-hover">
				<thead>
					<tr>
						<th>Estado</th>
						<th>RUN</th>
						<th>Nombre</th>	
						<th>Edad</th>
						<th>Hospital</th>
						<th>Diagnóstico</th>
					</tr>
				</thead>
			  <tbody>
	
			  </tbody>
			</table>
		</fieldset>
	</fieldset>
<br><br>
@stop
