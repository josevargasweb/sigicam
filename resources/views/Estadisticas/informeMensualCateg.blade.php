@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte mensual categorización</a></li>
@stop

@section("script")

<script>
    $(function() {
    	$("#estadistica").collapse();

		/*$.ajax({
			url: "{{asset('estadisticas/informeMensualCategDatos')}}",
			type: "post",
			dataType: "json",
			data: {'anno': 0, 'mes': 0},
			success: function(data){
				console.log("data: ", data);
				
			},
			error: function(error){
				console.log("error: ", error)
			}
		});
        */
        

   
        
		$(".fecha-grafico").datepicker({
			startView: 'months',
			minViewMode: "months",
    		autoclose: true,
    		language: "es",
    		format: "mm-yyyy",
    		//todayHighlight: true,
    		endDate: "+0d"
    	});

		$("#formMensual").on("submit", function(){
			var valor = $("#fecha-grafico").val();
			if(valor == ""){
				swalWarning.fire({
				title: 'Información',
				text:"Debe seleccionar una fecha"
				});
			}else{
				var mes = $("#fecha-grafico").datepicker('getDate').getMonth()+1;
				var anno = $("#fecha-grafico").datepicker('getDate').getFullYear();

				$("#anno").val(anno);
                $("#mes").val(mes);
			}
		});
	
	});
</script>

@stop

@section("section")
	<fieldset>
		<div class="col-sm-12">
			<div class="col-sm-12">
				<label>Seleccione fecha</label>
			</div>
			<div class="col-sm-2 form-group">
				<input type="text" id="fecha-grafico" class="form-control fecha-grafico">
			</div>

			
			
			
                {{Form::open(["url"=>asset('estadisticas/informeMensualCategDatos') , "method"=>"GET", "id"=>"formMensual"])}}
					<input type="hidden" name="anno" id="anno" >
					<input type="hidden" name="mes" id="mes" >
					
					@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
					<div class="col-sm-6 form-group">
						{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
					</div>
					@else
						{{ Form::hidden('establecimiento', Session::get("usuario")->establecimiento, array('id' => 'establecimiento')) }}
					@endif
				
						<input type="submit" value="Exportar" class="btn btn-primary" style="font-size: 14px; color: white !important;">
                {{Form::close()}}
				
		</div>
		<div class="col-md-12">
			
		</div>
	</fieldset>
@stop
