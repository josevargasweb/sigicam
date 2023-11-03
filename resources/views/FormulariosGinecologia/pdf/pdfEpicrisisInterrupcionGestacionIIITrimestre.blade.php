<!DOCTYPE html>
<html>
	<head>
	 	<meta charset="UTF-8">
		<style>
			.titulo{
				text-align:center;
				text-transform: uppercase;
			}
			.izquierda{
				float:left;
				width:50%;
			}
			.derecha{
				float:right;
				width:50%;
			}
			.limpiar{
				clear:both;
			}
			label{
				font-weight:600;
			}
			legend{
				background-color:white;
				text-align:left;
				font-size:14px;
				border:none;
				font-weight:bold;
			}
			fieldset{
				border-style:solid;
				border-width:1px;
				border-color:black;
				padding:32px;
			}
			.margen{
				margin-bottom:24px;
			}
			.margen2{
				margin-bottom:12px;
			}
			.margen3{
				margin-bottom:48px;
			}
			.mini_fieldset{
				width:40%;
				height:100%;
				overflow:hidden;
			}
			.cont_mini_fieldset{
				height:80px;
			}
			.caja{
				border-style:solid;
				border-width:1px;
				border-color:black;
				width:95%;
				margin:0px;
				min-height:40px;
				padding:12px;
				overflow:hidden;
			}

			ul{
				font-weight:normal;
			}
			.fila{
				width:inherit;
			}
			.item{
				display:inline-block;
			}
			.item-1{
				width:100%;
			}
			.item-2{
				width:49%;
			}
			.item-3{
				width:30%;
			}
			.item-4{
				width:24%;
			}
			.seccion_fecha{
				padding:24px;
			}
			.raya_firma{
				width:100px;
				border-style:solid;
				border-width:1px;
				border-color:black;
				display:block;
			}
			.centro{
				text-align:center;
			}
			.firmas{
				margin-top:80px;
			}
			.logo{
				display: inline-block;
				width:10%;
			}
			.logo img{
				width:100%;
			}
			.hospital{
				text-align:right;
				display:inline-block;
				float:right;
			}
		</style>
	</head>
	<body>
		<div class="logo">
			<img src="{{$formulario->logo_hospital}}">
		</div>
		<div class="hospital">
			<h1>{{$formulario->nombre_hospital}}</h1>
		</div>
		<!-- Título -->
		<div class="titulo margen">
			<h3>Epicrisis para interrupción de gestación III trimestre</h3>
		</div>
		<!-- Datos personales -->
		<div class="datos_personales margen">
			<div class="izquierda">
				<label>Nombre paciente:</label>
				<span>{{$formulario->nombre}} {{$formulario->apellido_paterno}} {{$formulario->apellido_materno}}</span>
			</div>
			<div class="derecha">
				<label>Rut:</label>
				<span>{{$formulario->run}}-{{$formulario->dv}}</span>
			</div>
			<div class="limpiar"></div>
			<div class="izquierda">
				<label>Nombre del establecimiento de salud:</label>
				<span>{{$formulario->nombre_establecimiento}}</span>
			</div>
			<div class="derecha">
				<label>Fecha:</label>
				<span>{{$formulario->fecha}}</span>
			</div>
			<div class="limpiar"></div>
			<div class="">
				<label>Región:</label>
				<span>{{$formulario->nombre_region}}</span>
			</div>
			<div class="limpiar"></div>
			
			<div class="izquierda">
				<label>Servicio:</label>
				<span>{{$formulario->servicio}}</span>
			</div>
			<div class="limpiar"></div>
			<div class="izquierda">
				<label>Sala:</label>
				<span>{{$formulario->sala}}</span>
			</div>
			<div class="derecha">
				<label>Cama:</label>
				<span>{{$formulario->cama}}</span>
			</div>
			
		</div>
		<!-- sección 1 -->
		<div class="caja margen">
			<div class="cont_mini_fieldset margen">
				<!-- antecedentes generales -->
				<fieldset class="izquierda mini_fieldset">
					<legend>1.- Antecedentes generales</legend>
					<div class="izquierda">
						<label>Edad:</label>
						<span>{{$formulario->edad}}</span>
						<span> años</span>
					</div>
					
					<div class="limpiar"></div>
					
					<div class="izquierda">
						<label>P:</label>
						<span>{{$formulario->p}}</span>
					</div>
					<div class="derecha">
						<label>V:</label>
						<span>{{$formulario->v}}</span>
					</div>
					
					<div class="limpiar"></div>
					
					<div class="">
						<label>Edad gestacional:</label>
						<span>{{$formulario->edad_gestacional}}</span>
						<span>semanas</span>
					</div>
					
					<div class="limpiar"></div>
					<div class="">
						<label>Edad gestacional en días:</label>
						<span>{{$formulario->edad_gestacional_dias}}</span>
						<span>días</span>
					</div>
					
					<div class="limpiar"></div>
					<div class="">
						<label>Observaciones:</label>
						<span>{{$formulario->edad_gestacional_observacion}}</span>
					</div>
					
					<div class="limpiar"></div>
				</fieldset>
				<!-- diagnóstico de patología -->
				<fieldset class="derecha mini_fieldset">
					<legend>2.- Diagnóstico de patología agregada</legend>
					<div class="">
						<label>2.1.-</label>
						<span>{{$formulario->diagnostico_patologia_1}}</span>
					</div>
					<div class="">
						<label>2.2.-</label>
						<span>{{$formulario->diagnostico_patologia_2}}</span>
					</div>
					<div class="">
						<label>2.3.-</label>
						<span>{{$formulario->diagnostico_patologia_3}}</span>
					</div>
				</fieldset>
			</div>
			<!-- condiciones obstétricas -->
			<fieldset>
				<legend>3.- Condiciones obstétricas</legend>
				<div class="fila margen2">
					<div class="item item-3">
						<label>ALT. UT.:</label>
						<span>{{$formulario->alt_ut}}</span>
					</div>
					<div class="item item-3">
						<label>CONS.:</label>
						<span>{{$formulario->cons}}</span>
					</div>
					<div class="item item-3">
						<label>PRESENTACIÓN:</label>
						<span>{{$formulario->presentacion}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-1">
						<label>TACTO VAGINAL:</label>
						<span>{{$formulario->tacto_vaginal}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-1">
						<label>PLANO DE LA PRESENTACION:</label>
						<span>{{$formulario->plano_presentacion}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-3">
						<label>CUELLO POSICIÓN:</label>
						<span>{{$formulario->cuello_posicion}}</span>
					</div>
					<div class="item item-3">
						<label>CONSIST.:</label>
						<span>{{$formulario->cuello_consist}}</span>
					</div>
					<div class="item item-3">
						<label>BORRAMIENTO:</label>
						<span>{{$formulario->cuello_borramiento}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-3">
						<label>DILAT.:</label>
						<span>{{$formulario->dilat}}</span>
					</div>
					<div class="item item-3">
						<label>BISHOP:</label>
						<span>{{$formulario->bishop}}</span>
					</div>
					<div class="item item-3">
						<label>PUNTOS:</label>
						<span>{{$formulario->puntos}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-1">
						<label>PELVIMETRIA:</label>
						<span>{{$formulario->pelvimetria}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-4">
						<label>CD:</label>
						<span>{{$formulario->cd}}</span>
					</div>
					<div class="item item-4">
						<label>CV:</label>
						<span>{{$formulario->cv}}</span>
					</div>
					<div class="item item-4">
						<label>EC:</label>
						<span>{{$formulario->ec}}</span>
					</div>
					<div class="item item-4">
						<label>RN:</label>
						<span>{{$formulario->rn}}</span>
					</div>
				</div>
				<div class="fila margen2">
					<div class="item item-2">
						<label>TIPO:</label>
						<span>{{$formulario->tipo}}</span>
					</div>
					<div class="item item-2">
						<label>PROPORCIÓN PELVIS FETAL:</label>
						<span>{{$formulario->proporcion_pelvis_fetal}}</span>
					</div>
				</div>
			</fieldset>
			
			<!-- estado unidad -->
			<fieldset>
				<legend>4.- Estado unidad feto placentario</legend>
				<div class="">
					<label>4.1.- Normal</label>
					<span>{{$formulario->normal}}</span>
				</div>
				<div class="">
					<label>4.2.- Alterado (XINT-TTO-E)</label>
					<span>{{$formulario->alterado}}</span>
				</div>
				<div class="">
					<label>4.3.- Peso estimado fetal (gramos)</label>
					<span>{{$formulario->peso_estimado_fetal}}</span>
				</div>
			</fieldset>
			
			<div class="cont_mini_fieldset margen">
				<!-- indicación de la interrupción -->
				<fieldset class="izquierda mini_fieldset">
					<legend>5.- Indicación de la interrupción</legend>
					<div class="">
						<label>5.1.-</label>
						<span>{{$formulario->indicacion_1}}</span>
					</div>
					<div class="">
						<label>5.2.-</label>
						<span>{{$formulario->indicacion_2}}</span>
					</div><div class="">
						<label>5.3.-</label>
						<span>{{$formulario->indicacion_3}}</span>
					</div>
				</fieldset>
				<!-- vía solicitada -->
				<fieldset class="derecha mini_fieldset">
					<legend>6.- Vía solicitada</legend>
					<div class="">
						<input type="radio" @if($formulario->via_solicitada == "induccion") checked @endif>
						<label>6.1.- INDUCCIÓN</label>
					</div>
					<div class="">
						<input type="radio" @if($formulario->via_solicitada == "induccion_monitorizada") checked @endif>
						<label>6.2.- INDUCCIÓN MONITORIZADA</label>
					</div>
					<div class="">
						<input type="radio" @if($formulario->via_solicitada == "cesarea") checked @endif>
						<label>6.3.- CESÁREA</label>
					</div>
				</fieldset>
			</div>
			<div class="limpiar"></div>
			<!-- fecha -->
			<div class="seccion_fecha">
				<label>7.- Fecha intervención</label>
				<span>{{$formulario->fecha_intervencion}}</span>
				<label>Hora</label>
				<span>{{$formulario->hora_intervencion}}</span>
			</div>
		</div>
		<!-- firmas -->
		<div class="firmas">
			<div class="izquierda centro">
				<hr class="raya_firma">
				<span>Jefe de sección</span>
			</div>
			<div class="derecha centro">
				<hr class="raya_firma">
				<span>Médico</span>
			</div>
			<div class="limpiar"></div>
		</div>
	</body>
</html>