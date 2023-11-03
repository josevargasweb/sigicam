@extends("Templates/template")

@section("titulo")
Inicio
@stop

@section("script")

<style type="text/css">
	div#cargandiwi {
		background-image: url("{{URL::to('/')}}/images/default.gif");
		background-repeat: no-repeat;
		position: absolute;
		width: 100%;
		height: 100%;
		margin-left: 40%;
		margin-top: 80px;
	}
	.al_centro{
		text-align:center;
		position:relative;
	}
	.img_simbologia{
		max-width:45px;
		max-height:30px;
		margin: auto;
		left: 0;
		right: 30px;
		position: absolute;
	}

	#columna1, #columna2, #columna3{
		border-bottom: none;
	}

	#botonVerCategorizados{
		background-color: #1e9966;
		border-color: #1e9966;
	}

	#botonVerCategorizados:hover{
		background-color: #24ab73;
		border-color: #24ab73;
	}



	@media(min-width: 1920px){
		#d2yd3{
			margin-top: 15px;
		}
	}

	@media(max-width: 1046px) and (min-width: 992px){
		.tamano-cuadros{
			padding-right: 0;
		}
	}

</style>
<script>
	$(window).resize(function(){
		var width = $(window).width();
		if(width >= 1200 && width <= 1300){
			$('#cama_estada').removeClass('col-lg-6').addClass('col-lg-9');
		}
		else{
			$('#cama_estada').removeClass('col-lg-9').addClass('col-lg-6');
		}
		})
	.resize()


	$(function(){
		reporteCamas();
		$(document).on('mouseover',".divContenedorNombre",function(){
		//console.log($(this).attr("data-nombre"));
		nombreUnidad = $(this).attr("data-nombre");
		if(nombreUnidad == 'null'){
			nombreUnidad = "";
		}
		$(this)
		.attr('data-original-title', nombreUnidad)
			.tooltip('show');
		});



		//funcion para marcar el area en el menu de gestion de camas
		$(document).on('click','.marcarArea', function(){
			/* console.log("algo", $(this).data("id")); */
			var seleccionado = $(this).data("id");
			if(seleccionado != null){
				$("#"+seleccionado).css("background-color", "#D1FEBC");
				sessionStorage.setItem("seleccionado_menu", seleccionado);
			}

		});



		/**
		 * Carga por ajax el resumen de cada unidad
		 */
		@if(  Auth::user()->tipo != "admin_ss" &&  Auth::user()->tipo != "monitoreo_ssvq" && Auth::user()->tipo != "admin_iaas" && Auth::user()->tipo != "oirs")
			$.ajax({

					// url: "{{URL::to('/')}}/index1",
					url: "{{URL::to('/')}}/resumenCamasIndex",
				data: {id: 1},
				type: "get",
				dataType: "json",
				success: function(data){
					/* console.log("datos: ",data); */
					//reporteCamas();
					alerta();
					var sumaLibre=0;
					var sumaOcupada=0;
					var sumaReconvertida=0;
					var sumaBloqueada=0;
					var sumaDotacion=0;
					var sumaIDOtacion=0;
					/* console.log(data); */
					var totalFila =0;
					var sumaTotalFila =0;
					var espacioArea = 'colspan="2"';
					var sumaIndiceOcupacional = 0;// usada para el promedio
					var cantidadUnidades= 0;
					$.each(data.resumen, function(index, value){
						cantidadUnidades +=1;
						$("#cargandiwi").hide();
						if(value.cantidad_infectados > 0){
							$("#menu"+value.id).show();
						}

						sumaLibre = parseInt(sumaLibre)+parseInt(value.libres);
						sumaOcupada= parseInt(sumaOcupada)+parseInt(value.ocupadas);
						sumaReconvertida= parseInt(sumaReconvertida)+parseInt(value.reconvertidas);
						sumaBloqueada= parseInt(sumaBloqueada)+parseInt(value.bloqueadas);
						sumaDotacion= parseInt(sumaDotacion)+parseInt(value.dotacion);
						indiceOcupacion= ((parseInt(value.ocupadas)+parseInt(value.libres)) == 0) ? 0 : (parseInt(value.ocupadas)/(parseInt(value.ocupadas)+parseInt(value.libres)))*100;
						/* if(parseInt(value.dotacion)<=0){
							indiceOcupacion=0;
						}else{
							// // indiceOcupacion= (parseInt(value.ocupadas) == 0)? 0:(parseInt(value.ocupadas)*100)/(parseInt(value.dotacion) - parseInt(value.bloqueadas));
							
						} */
						
						totalFila = parseInt(value.libres) + parseInt(value.ocupadas) + parseInt(value.bloqueadas);
						sumaTotalFila = sumaTotalFila + totalFila;
						// sumaIDOtacion = sumaIDOtacion + indiceOcupacion; //suma de indice de dotacion
						sumaIndiceOcupacional += indiceOcupacion;

					if(data.usuario == 'admin_ss' || data.usuario == 'monitoreo_ssvq' || data.usuario == 'admin_iaas')
					{
						var columna = "<a class='divContenedorNombre' data-nombre='"+value.tooltip+"' data-toggle='tooltip' data-placement='top' href='index/camas/"+value.id+"'>"+value.nombre+"</a>";
						$('#resumen').append('<tr><td>'+columna+'</td><td>'+value.dotacion+'</td><td>'+value.libres+'</td><td>'+value.ocupadas+'</td><td>'/* +value.reconvertidas+'</td><td>' */+value.bloqueadas+'</td> <td>'+totalFila+'</td><td>'+indiceOcupacion.toFixed(1).replace(".", ",")+'%</td></tr>  ');
						espacioArea = "";
					}else{

							@if (Auth::user()->tipo == "medico" || Auth::user()->tipo == "tens" || Auth::user()->tipo == "grd" || Auth::user()->tipo == "encargado_hosp_domiciliaria")
								var columna = "<p data-nombre='"+value.tooltip+"' data-toggle='tooltip' data-placement='top' class='marcarArea divContenedorNombre' data-id='"+value.id_area_funcional+"'>"+value.nombre+"</p>";
							@else
								var columna = "<a data-nombre='"+value.tooltip+"' data-toggle='tooltip' data-placement='top' class='marcarArea divContenedorNombre' data-id='"+value.id_area_funcional+"' href='unidad/"+value.url+"'>"+value.nombre+"</a>";
							@endif

						$('#resumen').append('<tr><td>'+value.nombre_area+'</td><td>'+columna+'</td><td>'+value.dotacion+'</td><td>'+value.libres+'</td><td>'+value.ocupadas+'</td><td>'/* +value.reconvertidas+'</td><td>' */+value.bloqueadas+'</td>  <td>'+totalFila+'</td> <td>'+indiceOcupacion.toFixed(1).replace(".", ",")+'%</td></tr>  ');
					}

						//$('#resumen').append('<tr><td>'+value.nombre_area+'</td><td>'+columna+'</td><td>'+value.libres+'</td><td>'+value.ocupadas+'</td><td>'/* +value.reconvertidas+'</td><td>' */+value.bloqueadas+'</td> <td>'+totalFila+'</td></tr>  ');
						totalFila =0;
					});

					//promedioIndiceOcupacional = (cantidadUnidades == 0)?0:sumaIndiceOcupacional/cantidadUnidades;

					// indiceTotalOcupacion = (sumaOcupada*100)/(sumaDotacion-sumaBloqueada); //indice de dotacion segun el total de las camas
					indiceTotalOcupacion = (sumaOcupada/(sumaOcupada+sumaLibre))*100;

				$('#resumen').append('<tr><td '+espacioArea+'>TOTAL</td><td><b>'+sumaDotacion+'</b></td><td><b>'+sumaLibre+'</b></td><td><b>'+sumaOcupada+'</b></td><td><b>'/* +sumaReconvertida+'</b></td><td><b>' */+sumaBloqueada+'</b></td><td><b>'+sumaTotalFila+'</b></td><td>  <b>'+indiceTotalOcupacion.toFixed(1).replace(".", ",")+'% <br></b></td></tr>');

					$('#ocupadas').html(sumaOcupada);
					$('#ocupadas').removeClass("calculando");

					$('#libres').html(sumaLibre);
					$('#libres').removeClass("calculando");


					//$("#ocultar-cat").show();
					$("#container").show();




				},
				beforeSend: function( xhr ) {

				},
				error: function(error){
					console.log(error);

				}
			});
		@endif
	});

		var reporteCamas = function(){
			$.ajax({
					url: "{{URL::to('/')}}/reporteUsoDeCamas",
					type: "GET",
					dataType: "json",
					success: function(data){
						/* console.log(data);
						console.log(data.data[0].tipo);
						console.log(data.data[0].porcentaje); */

						$("#campoTitulo1").html(data.data[0].porcentaje);
						$("#cantidad1").html(data.data[0].ocupadas + " de " + data.data[0].total);
						//$("#texto1").html("CAMA "+data.data[0].tipo);

						$("#campoTitulo2").html(data.data[1].porcentaje);
						$("#cantidad2").html(data.data[1].ocupadas + " de " + data.data[1].total);
						//$("#texto2").html("CAMA "+data.data[1].tipo);

						$("#campoTitulo3").html(data.data[2].porcentaje);
						$("#cantidad3").html(data.data[2].ocupadas + " de " + data.data[2].total);
						//$("#texto3").html(data.data[2].tipo);

						$('#listaEspera').html(data.data.TotalListaEspera);
						$('#listaEspera').removeClass("calculando");

						$('#listaTransito').html(data.data.TotalListaTransito);
						$('#listaTransito').removeClass("calculando");


						$('#d2yd3').html(data.data.total_categorizacionD2yD3);
						$('#d2yd3').removeClass("calculando");

						$('#categorizados').html(data.data.total_categorizados);
						$('#categorizados').removeClass("calculando");

						$('#egresos').html(data.data.total_egresos);
						$('#egresos').removeClass("calculando");

						$('#prom_estada').html(data.data.estada_promedio);
						$('#prom_estada').removeClass("calculando");

						$('#listaExamenes').html(data.data.examenes);
						$('#listaExamenes').removeClass("calculando");






					},
					error: function(error){
						console.log(error);
					}
				});

		}

		var alerta = function(){
			//alerta de pacientes en lista de TRANSITO con color rojo >12 hrs
				var mensaje2 ="";
				$.ajax({
					url: "{{URL::to('/')}}/alertaPacienteEspera",
					type: "GET",
					dataType: "json",
					success: function(data){
						var mensaje1 = "Existen "+data.espera+" pacientes que exceden 12 horas en lista de espera";
						if(data.espera == 0){
							mensaje1 = "No existen pacientes que exceden 12 horas en lista de espera";
						}
							mensaje2 = "Existen "+data.transito+" pacientes que exceden 12 horas en lista de tránsito";
							if(data.transito == 0){
								mensaje2 = "No existen pacientes que exceden 12 horas en lista de tránsito";
							}
							mensaje3 = data.categorizar;
						 /* if (data.espera >= 1){ */
						 	document.getElementById("columna1").innerHTML = mensaje1;
						 	document.getElementById("columna2").innerHTML = mensaje2;
						 	document.getElementById("columna3").innerHTML = mensaje3;
						 	/*
							bootbox.dialog({
								message: "<h4>"+mensaje1+" <br><br>"+mensaje2+" <br><br>"+mensaje3+"</h4>",
								title: "AVISO",
								buttons: {
									main: {
										label: "Aceptar",
										className: "btn-primary",

									}
								}
							}); /*
						/* } */
					},
					error: function(error){
						console.log(error);
					}
				});
		}
		$("#btn-informe-excel").on("click", function(){
			window.location.href = "{{url('descargarExcelResumencamas')}}";
		});
</script>
@stop

@section("css")
<style type="text/css">
	.notify{
		z-index: 9999;
	}
	.notify-content{
		font-size: 28px;
	}
	.table.table-bordered.categorizacion > thead > tr > th {
	color: white;
	font-size: 15px;
	}
	.table-bordered > tbody > tr > th {
		background: #F5F5F5;
		color: #695959;
		/* font-size: 15px; */
	}
	.categorizacion {
		width: 100% !important;
		background: #1E9966;
	}

	.calculando {
		font-size: 23px;
	}

/*diseño*/
	.tituloReporte {
		color: #6A7888;

	}

	.main-overview {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(225px, 1fr)); /* Where the magic happens */
		grid-auto-rows: 74px;
		grid-gap: 10px;
		margin: 20px;
	}

	.overviewcard {
		height: 70px !important;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 10px;
		background-color: #cff0ce;
	}

	.numeroActual {
		color: #6A7888;
		margin-top: 0px !important;
		margin-bottom: 0px !important;
		/*font-size: 15px !important;*/
	}

	.tamano {
		font-size: 13px !important;
	}

	h1 {
		font-size: 25px;
		text-align: center;
		font-weight: bold;
	}
	.padding_corregidgo{
		padding-left: 5px;
		padding-right: 5px;
		padding-top: 5px;
		padding-bottom: 5px;
	}
/*diseño*/
</style>
@stop

@section("section")
<script>

</script>
@if(Session::get('usuario')->tipo != "oirs")
<div class="col-sm-12">
	<div class="alert alert-success alert-dismissible fade in" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">×</span></button> <strong>Nueva actualización 16/12/2020:</strong>
			Actualizaciones al día.
	</div>

	@if(Auth::user()->tipo != "admin_ss" &&  Auth::user()->tipo != "monitoreo_ssvq" && Auth::user()->tipo != "admin_iaas" && Session::get('usuario')->tipo != TipoUsuario::ADMINCOMERCIAL && Session::get('usuario')->tipo != TipoUsuario::MEDICO)
		<table class="table alert alert-warning alert-dismissible fade in">
			<thead>
			<tr>
			<th id="columna1" class="col-xs-4">Cargando información...</th>
			<th id="columna2" class="col-xs-4">Cargando información...</th>
			<th id="columna3" class="col-xs-4">Cargando información...</th>
			</tr>
			</thead>
		</table>
	@endif
	@if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'director' || Session::get('usuario')->tipo == 'medico_jefe_servicio' || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
		<a id="botonVerCategorizados" href="{{URL::to('/')}}/estadisticas/reporteRiesgoCategorizacion" type="button" class="btn btn-success">Ver Categorizados por servicio</a>
	@endif

	@if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'director' || Session::get('usuario')->tipo == 'medico_jefe_servicio')
		<a id="botonVerCategorizados" href="{{URL::to('/')}}/estadisticas/reporteRiesgoCategorizacion#pacientesD2yD3" type="button" class="btn btn-success">Paciente categorizados D2 y D3</a>
	@endif
</div>
@endif

<div class="col-sm-12">
	<br>
	<div class="col-xs-12 col-sm-12 col-md-12">
		@if(Session::get('usuario')->tipo != 'visualizador' && Session::get('usuario')->tipo != "oirs")

<fieldset>
	<legend id="porcentaje">Porcentaje de cama ocupada por tipo</legend>
	<div class="row" id="urgenciaActual" style="margin-bottom:20px;">

		{{--primera linea--}}
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="50" height="30" alt=""> Camas Críticas</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="campoTitulo1">Calculando...</h1>
					<p class="numeroActual tamano" id="cantidad1"></p>
				</div>
			</div>
		</div>

		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="50" height="30" alt=""> Camas Básicas</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="campoTitulo2">Calculando...</h1>
					<p class="numeroActual tamano" id="cantidad2"></p>
				</div>
			</div>
		</div>

		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="50" height="30" alt=""> Camas Medias</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="campoTitulo3">Calculando...</h1>
					<p class="numeroActual tamano" id="cantidad3"></p>
				</div>
			</div>
		</div>

		<br>
		{{--segunda linea--}}
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="40" height="40" alt=""> Lista Espera</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="listaEspera">Calculando...</h1>
				</div>
			</div>
		</div>

		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="40" height="40" alt=""> Lista Transito</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual  calculando" id="listaTransito">Calculando...</h1>
				</div>
			</div>
		</div>

		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="40" height="40" alt=""> Examenes Pendientes</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual  calculando" id="listaExamenes">Calculando...</h1>
				</div>
			</div>
		</div>

		<br>
		{{--tercera linea--}}
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="50" height="30" alt=""> Camas Ocupadas</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="ocupadas">Calculando...</h1>
				</div>
			</div>
		</div>
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="50" height="30" alt=""> Camas Libres</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="libres">Calculando...</h1>
				</div>
			</div>
		</div>
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="50" height="30" > Camas Estada	</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="prom_estada">Calculando...</h1>
				</div>
			</div>
		</div>

		<br>
		{{--cuarta linea--}}
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40" alt=""> Pacientes D2 y D3</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="d2yd3">Calculando...</h1>
				</div>
			</div>
		</div>
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40" alt=""> Pacientes categorizados</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="categorizados">Calculando...</h1>
				</div>
			</div>
		</div>
		<div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
			<div class="overviewcard">
				<div class="overviewcard__icon">
					<label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="30" height="40" alt=""> Pacientes egresados</label>
				</div>
				<div class="overviewcard__info" >
					<h1 class="numeroActual calculando" id="egresos">Calculando...</h1>
				</div>
			</div>
		</div>

			{{--diseño nuevo--}}

		<div class="row" id="urgenciaActual"></div>
	</div>
</fieldset>

		<fieldset>
			<legend>Resumen camas</legend>
			<div class="col-sm-2 form-group">
					<button id="btn-informe-excel" class="btn btn-success">Descargar Excel</button>
			</div>
			<div class="table-responsive">

			<div id="cargandiwi">

				</div>

				<table id="resumen" class="table table-bordered table-striped" style="width: 100% !important" width="100%">
					<thead style="text-align: center;">
						<tr>
							@if(Auth::user()->tipo == "admin_ss" || Auth::user()->tipo == "monitoreo_ssvq" || Auth::user()->tipo == "admin_iaas")

							@else
							<th>Area Funcional</th>
							@endif


							<th>Servicio</th>
							<th>Dotación</th>
							<th>
								<figure>{{ HTML::image('img/SIN_PACIENTE.png', null, $attributes = array("class" => "cama")) }}
									<br><br>
									 <figcaption>
										 <label>Cama libre</label>
									</figcaption> 
								</figure>
							</th>
							<th>
								<figure>{{ HTML::image('img/cama_borde_negro.png', null, $attributes = array("class" => "cama")) }}	
									<br><br>
									<figcaption>
										<label>Cama ocupada</label>
									</figcaption> 
								</figure>
							</th>
							<th>
								<figure>{{ HTML::image('img/cama_bloqueada.png', null, $attributes = array("class" => "cama")) }}
									<br><br> 
									<figcaption>
										<label>Cama bloqueada</label>
									</figcaption> 
								</figure>
							</th>
							<th>Total</th>
							<th><figcaption><label>Índice Ocupación</label></figcaption> </th>
						</tr>
					</thead>

				</table>

			</div>

		</fieldset>
		@endif
		@if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'director' || Session::get('usuario')->tipo == 'medico_jefe_servicio')
			<fieldset style="display: none;">
				<div class="col-md-12">
					<div id="container" style="min-width: 310px; height: 400px; margin: 40px auto" hidden></div>
				</div>

			</fieldset>
		@endif
	</div>
</div>
@stop
