@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte riesgo y categorización</a></li>
@stop

@section("script")

<script>
	$(window).load(function() {
		$(".loader").fadeOut("slow");
	});
	
    $(function() {

    	$("#estadistica").collapse();
        

   
		$(".fecha-grafico").datepicker({
			startView: 'months',
			minViewMode: "months",
    		autoclose: true,
    		language: "es",
    		format: "mm-yyyy",
    		//todayHighlight: true,
    		endDate: "+0d"
    	});

        $(".fecha-sel").datepicker({
    		autoclose: true,
    		language: "es",
    		format: "dd-mm-yyyy",
    		todayHighlight: true,
    		endDate: "+0d"
    	});

		
		/* $("#establecimiento").on('change', function(){
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
		}); */
	
	});
</script>

<style>
	.loader {
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url("{{URL::to('/')}}/images/default.gif") 50% 50% no-repeat rgb(249,249,249);
		opacity: .8;
	}

	.primerNav{
		background-color:#1E9966;
	}
		.primerNav > li > a{
			color: #fff !important;
		}

			.primerNav > li.active > a, .primerNav > li.active > a:hover,.primerNav > li.active > a:focus,.primerNav > li > a:hover, .primerNav > li > a:focus{
				background-color:#c35c6b;
			}

	.segundoNav{
		background-color:#c35c6b
	}

		.segundoNav > li > a{
			color: #fff !important;
		}

			.segundoNav > li.active > a, .segundoNav > li.active > a:hover,.segundoNav > li.active > a:focus, .segundoNav > li > a:hover, .segundoNav > li > a:focus{
				background-color:#bce8f1;
				color: #1e9966 !important;
			}
</style>

@stop

@section("estilo-tablas-verdes")
	{{ HTML::style('css/estiloTablasVerdes.css') }}
@stop

@section("section")

	<div class="row">
		<div id="exTab1" class="container" >	
			<ul  class="nav nav-pills primerNav">
				@if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'director' || Session::get('usuario')->tipo == 'medico_jefe_servicio' || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
					<li class="nav" id="RRC1"><a href="#porServicio" data-toggle="tab">Pacientes categorizados por servicio</a></li>
				@endif
				<li class="nav in active" id="RRC2"><a href="#informeMensual" data-toggle="tab">Informe mensual</a></li>
				<li class="nav" id="RRC3"><a href="#D2yD3" data-toggle="tab">Pacientes categorizados D2 y D3</a></li>
				<li class="nav" id="RRC4"><a href="#riesgo" data-toggle="tab">Riesgos</a></li>
				
			</ul>
			<div class="tab-content clearfix">
				
				<div class="tab-pane pane" style="padding-top:10px;" id="porServicio">
					@include('Estadisticas.reporteCategorizacion.categorizacionPorServicio')				
				</div>
	
				<div class="tab-pane pane in active" style="padding-top:10px;" id="informeMensual">
					@include('Estadisticas.reporteCategorizacion.informeMensual')				
				</div>
				
				<div class="tab-pane pane" id="D2yD3">
					@include('Estadisticas.reporteCategorizacion.pacientesCategorizados')
				</div>
				<div class="tab-pane pane" style="padding-top:10px;" id="riesgo">
					@include('Estadisticas.reporteCategorizacion.riesgos')
				</div>
			</div>
		</div>
  	</div>
	
@stop
