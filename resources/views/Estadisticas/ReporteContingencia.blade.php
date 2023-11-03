@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de contingencias</a></li>
@stop

@section("script")

<script>
	var getDatos = function(fecha_desde, fecha, estab){
		console.log("obteniendo datos de alta");
		estab = typeof estab !== 'undefined' ? estab : '';
        //if (estab != '') estab = "/" + estab;
        $.ajax({
            url: '{{URL::route("estContingencia")}}/datos',
            data: {"fecha-inicio":fecha_desde, "fecha":fecha, "estab":estab},
            dataType: "json",
            type: "get",
            success: function(response){

				var addData = [];

				for(i=0;i<response.solicitudes.length;i++){
					addData.push([response.solicitudes[i].nombre, response.solicitudes[i].count]);
				
				}

            	var tabla=$("#tablaAlta").dataTable();


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
    $(function() {
    	$("#estadistica").collapse();

    	$(".fecha-sel").datepicker({
    		autoclose: true,
    		language: "es",
    		format: "dd-mm-yyyy",
    		todayHighlight: true,
    		endDate: "+0d"
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
		<legend>Reporte de alta</legend>

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
		<div id="contenido"></div>
		
	</fieldset>



    <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                    <table id="tablaAlta" class="table table-striped table-bordered table-hover">
                        <tfoot>
                            <tr>
                               <th>Hospital</th>
                                <th>Total Solicitudes de contingencia</th>
                            </tr>
                        </tfoot>
                        <thead>
                            <tr>
                               
                              <th>Unidad</th> 
                                <th>Total Solicitudes de contingencia</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>


<br><br>
@stop
