@extends("Templates/template")
{{ HTML::script('js/ProgressBar.js') }}

{{ HTML::style('css/ProgressBar.css') }}

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Camas inhabilitadas</a></li>
@stop

@section("script")

<script>
    var minHeight=300;

    var getDatos = function(){
        $.ajax({
            url: '{{URL::route("estDeshabilitadas")}}/datos',
            dataType: "json",
            type: "get",
            data: $("#updateEstadistica").serialize(),
            success: function(data){
                $(".titulo_1").html(data.titulos[0]);
                $(".titulo_2").html(data.titulos[1]);
                $(".titulo_3").html(data.titulos[2]);
                $(".titulo_4").html(data.titulos[3]);
                var inner = '';
                $.each(data.promedios, function(i, val){
                    inner += '<li class="list-group-item"><span class="badge">'+ val +'</span> <strong>'+i+':</strong></li>';
                });
                $("#reporte-promedios").html(inner);
                var height=$("#reporte-promedios").height();
                if(height <= minHeight) height+=minHeight;
                $("#camasDeshabilitadas").height(height);   
                $('#camasDeshabilitadas').highcharts(data.g_camas);
                var tabla=$("#tablaCamasDeshabilitadas").dataTable().columnFilter({
                    aoColumns: [
                    {type: "select", values: data.especialidades},
                    null,
                    null
                    ]
                });
                tabla.fnClearTable(); 
                if(data.t_camas.length > 0) tabla.fnAddData(data.t_camas);            
            },
            error: function(error){
                console.log(error);
            }
        });
    }
    $(function() {
    	$("#estadistica").collapse();

    	$(".fecha").datepicker({
    		autoclose: true,
    		language: "es",
    		format: "dd-mm-yyyy",
    		todayHighlight: true,
    		endDate: "+0d"
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
			getDatos();
            return false;
		});
		
    	$('#tablaCamasDeshabilitadas').dataTable({	
    		"aaSorting": [[0, "desc"]],
    		"iDisplayLength": 15,
    		"bJQueryUI": true,
    		"oLanguage": {
    			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
    		},
    		aoColumns : [
                		{"mData" : "nombre_unidad"},
	                	{"mData" : "id_sala"},
	                	{"mData" : "id_cama"},
	                	{"mData" : "tiempo"}
            ]
    	});

        getDatos();

        
    });
</script>

@stop

@section("section")
<fieldset>
	<legend>Camas inhabilitadas</legend>
	<div class="row">
        {{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadistica', 'style' => 'padding-left: 15px;')) }}
        <div class="form-group">
            {{Form::text('fecha_desde', $fecha_desde, array('id' => 'fecha_desde', 'class' => 'fecha form-control', 'placeholder' => 'Fecha'))}}
        </div>
        <div class="form-group">
            {{Form::text('fecha_hasta', $fecha_hasta, array('id' => 'fecha_hasta', 'class' => 'fecha form-control', 'placeholder' => 'Fecha'))}}
        </div>
        
        <div class="form-group">
        @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
            {{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
        @else
            {{ Form::hidden('establecimiento', Session::get("usuario")->establecimiento, array('id' => 'establecimiento')) }}
        @endif
        </div>
        <div class="form-group">
            {{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary')) }}
        </div>
        {{ Form::close() }}
    </div>
	<br><br>
	<div class="row">
		<div class="col-md-4">
            <h4>Tiempo total de camas inhabilitadas:</h4>
			<ul class="list-group" id="reporte-promedios"></ul>
		</div>
		<div class="col-md-8">
			<div id="camasDeshabilitadas"></div>
		</div>
	</div>
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-12">
			<div class="table-responsive">
			<table id="tablaCamasDeshabilitadas" class="table table-striped table-bordered table-hover">
				<tfoot>
					<tr>
						<th class="titulo_1">Especialidad</th>
						<th class="titulo_2">Sala</th>
						<th class="titulo_3">Cama</th>
						<th class="titulo_4">Tiempo total inhabilitadas (días)</th>
					</tr>
				</tfoot>
				<thead>
					<tr>
						<th class="titulo_1">Especialidad</th>
						<th class="titulo_2">Sala</th>
						<th class="titulo_3">Cama</th>
						<th class="titulo_4">Tiempo total inhabilitadas (días)</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			</div>
		</div>
	</div>
</fieldset>
<br><br>
@stop
