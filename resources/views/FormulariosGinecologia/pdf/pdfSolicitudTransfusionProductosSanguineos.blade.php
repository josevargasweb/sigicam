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
				vertical-align: top;
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
			.item-5{
				width:19%;
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
				margin-top:120px;
			}
			.otra_pagina{
				page-break-after: always;
			}
			.separador{
				border:none;
				border-top-style:solid;
				border-top-width:1px;
				border-top-color:black;
			}
			.logo{
				display: inline-block;
				width:10%;
			}
			.logo img{
				width:100%;
			}
			.hospital{
				text-align:center;
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
			<h3>Solicitud de transfusión de productos sanguíneos</h3>
		</div>
		<!-- Datos personales -->
		<div class="datos_personales margen">
			<div class="item item-4">
				<label>Nombre paciente:</label>
				<span>{{$formulario->nombre}} {{$formulario->apellido_paterno}} {{$formulario->apellido_materno}}</span>
			</div>
			<div class="item item-4">
				<label>Rut:</label>
				<span>{{$formulario->run}}-{{$formulario->dv}}</span>
			</div>
			<div class="item item-4">
				<label>Ficha:</label>
				<span>{{$formulario->ficha_clinica}}</span>
			</div>
			<div class="margen"></div>
			
			<div class="item item-4">
				<label>Edad:</label>
				<span>{{$formulario->edad}}</span>
			</div>
			<div class="item item-4">
				<label>Servicio:</label>
				<span>{{$formulario->servicio}}</span>
			</div>
			<div class="item item-4">
				<label>Sala:</label>
				<span>{{$formulario->sala}}</span>
			</div>
			<div class="item item-4">
				<label>Cama:</label>
				<span>{{$formulario->cama}}</span>
			</div>
		</div>
		<!-- sección 1 -->
		<div class="margen">
			<div class="fila">
				<div class="item item-3">
					<label>Diagnóstico</label>
					<span>{{$formulario->diagnostico}}</span>
				</div>
				<div class="item item-3">
					<label>Trans. previas</label>
					<span>{{$formulario->transf_previas}}</span>
				</div>
				<div class="item item-3">
					<label>Reacciones transfusiones</label>
					<span>{{$formulario->reacciones_transfusiones}}</span>
				</div>
			</div>
			<div class="fila">
				<div class="item item-3">
					<label>N° Embarazos</label>
					<span>{{$formulario->n_embarazos}}</span>
				</div>
				<div class="item item-3">
					<label>TTPA</label>
					<span>{{$formulario->ttpa}}</span>
				</div>
				<div class="item item-3">
					<label>TP</label>
					<span>{{$formulario->tp}}</span>
				</div>
			</div>
			<div class="fila">
				<div class="item item-3">
					<label>Plaq</label>
					<span>{{$formulario->plaq}}</span>
				</div>
				<div class="item item-3">
					<label>Hb</label>
					<span>{{$formulario->hb}}</span>
				</div>
				<div class="item item-3">
					<label>Hto</label>
					<span>{{$formulario->hto}}</span>
				</div>
			</div>
		</div>
		<div>
			<fieldset class="margen">
				<legend>G. rojos</legend>
				<div class="izquierda">
					<div class="item item-1">
						<label>Cantidad</label>
						<span>{{$formulario->g_rojos_cantidad}}</span>
					</div>
					<br>
					<div class="item item-1">
						<label>Horario</label>
						<span>{{$formulario->g_rojos_horario}}</span>
					</div>
				</div>
				<div class="derecha">
					<div class="item item-3">
						<label>Observaciones</label>
						<p>{{$formulario->g_rojos_observaciones}}</p>
					</div>
				</div>
			</fieldset>
			
			<fieldset class="margen">
				<legend>P. Fresco</legend>
				<div class="izquierda">
					<div class="item item-1">
						<label>Cantidad</label>
						<span>{{$formulario->p_fresco_cantidad}}</span>
					</div>
					<br>
					<div class="item item-1">
						<label>Horario</label>
						<span>{{$formulario->p_fresco_horario}}</span>
					</div>
				</div>
				<div class="derecha">
					<div class="item item-3">
						<label>Observaciones</label>
						<p>{{$formulario->p_fresco_observaciones}}</p>
					</div>
				</div>
			</fieldset>
			
			<fieldset class="margen">
				<legend>Plaquetas</legend>
				<div class="izquierda">
					<div class="item item-1">
						<label>Cantidad</label>
						<span>{{$formulario->plaquetas_cantidad}}</span>
					</div>
					<br>
					<div class="item item-1">
						<label>Horario</label>
						<span>{{$formulario->plaquetas_horario}}</span>
					</div>
				</div>
				<div class="derecha">
					<div class="item item-3">
						<label>Observaciones</label>
						<p>{{$formulario->plaquetas_observaciones}}</p>
					</div>
				</div>
			</fieldset>
			
			<fieldset class="margen">
				<legend>Crioprec.</legend>
				<div class="izquierda">
					<div class="item item-1">
						<label>Cantidad</label>
						<span>{{$formulario->crioprec_cantidad}}</span>
					</div>
					<br>
					<div class="item item-1">
						<label>Horario</label>
						<span>{{$formulario->crioprec_horario}}</span>
					</div>
				</div>
				<div class="derecha">
					<div class="item item-3">
						<label>Observaciones</label>
						<p>{{$formulario->crioprec_observaciones}}</p>
					</div>
				</div>
			</fieldset>
			
			<fieldset class="margen">
				<legend>Exsanguineot.</legend>
				<div class="izquierda">
					<div class="item item-1">
						<label>Cantidad</label>
						<span>{{$formulario->exsanguineot_cantidad}}</span>
					</div>
					<br>
					<div class="item item-1">
						<label>Horario</label>
						<span>{{$formulario->exsanguineot_horario}}</span>
					</div>
				</div>
				<div class="derecha">
					<div class="item item-3">
						<label>Observaciones</label>
						<p>{{$formulario->exsanguineot_observaciones}}</p>
					</div>
				</div>
			</fieldset>
			
			<div>
				<div class="item item-2">
					<label>Leucorreducidos</label>
					<span>{{$formulario->leucorreducidos}}</span>
				</div>
				<div class="item item-2">
					<label>Irradiado</label>
					<span>{{$formulario->irradiado}}</span>
				</div>
			</div>
			<div class="otra_pagina"></div>
			<fieldset>
				<legend>Recepción</legend>
				<div class="item item-2">
					<label>Responsable</label>
					<span>{{$formulario->responsable_recepcion}}</span>
				</div>
				<div class="item item-2">
					<label>Fecha y hora</label>
					<span>{{$formulario->fecha_recepcion}}</span>
				</div>
				<div class="margen"></div>
				<div class="margen item item-1">
					<input type="radio" @if($formulario->gravedad == "inmediata") checked @endif> Inmediata (Sin pruebas cruzadas, sin clasif, ABO Rh(D) Sin Ac. Irregulares)<br>
					<input type="radio" @if($formulario->gravedad == "urgente") checked @endif> Urgente (Con pruebas cruzadas en 90 minutos) <br>
					<input type="radio" @if($formulario->gravedad == "no_urgente") checked @endif> No urgente (Con pruebas cruzadas en 24 horas) <br>
				</div>
				<div class="item item-2">
					<input type="checkbox" @if($formulario->reserva_pabellon) checked @endif>  Reserva de pabellón
				</div>
				<div class="item item-2">
					<label>Fecha y hora</label>
					<span>{{$formulario->fecha_reserva_pabellon}}</span>
				</div>
				<div class="margen"></div>
				<div class="item item-2">
					<label>Médico responsable</label>
					<span>{{$formulario->medico_responsable}}</span>
				</div>
				<div class="item item-2">
					<label>Fecha y hora solicitud</label>
					<span>{{$formulario->fecha_solicitud}}</span>
				</div>
				<div class="margen"></div>
				<div class="item item-1">
					<label>Observaciones</label>
					<p>{{$formulario->observaciones}}</p>
				</div>
			</fieldset>
		</div>
		<div>
			<h4>Estudios inmunohematológicos</h4>
			<fieldset class="margen">
				<legend>Clasif. ABO Rh (D)</legend>
				<div class="item item-2">
					<label>Fecha</label>
					<span>{{$formulario->clasific_abo_fecha}}</span>
				</div>
				<div class="item item-2">
					<label>Resp</label>
					<span>{{$formulario->clasific_abo_resp}}</span>
				</div>
			</fieldset>
			
			<fieldset class="margen">
				<legend>Reclasif. ABO Rh (D)</legend>
				<div class="item item-2">
					<label>Fecha</label>
					<span>{{$formulario->reclasific_abo_fecha}}</span>
				</div>
				<div class="item item-2">
					<label>Resp</label>
					<span>{{$formulario->reclasific_abo_resp}}</span>
				</div>
			</fieldset>
			
			<fieldset class="margen">
				<legend>Ac. Irregulares.</legend>
				<div class="item item-2">
					<label>TCD</label>
					<span>{{$formulario->ac_irregulares_tcd}}</span>
				</div>
				<div class="item item-2">
					<label>CA</label>
					<span>{{$formulario->ac_irregulares_ca}}</span>
				</div>
			</fieldset>
			
			<div >
				<div class="izquierda">
					<label>Unidades compatibles</label>
					<p>{{$formulario->unidades_compatibles}}</p>
				</div>
				<div class="derecha">
					<div class="item item-1">
						<label>Hora</label>
						<span>{{$formulario->unidades_compatibles_hora}}</span>
					</div>
					<div class="item item-1">
						<label>Resp</label>
						<span>{{$formulario->unidades_compatibles_resp}}</span>
					</div>
				</div>
			</div>
			<div class="limpiar"></div>
		</div>
		
		@foreach($instalaciones as $instalacion)
		<div class="otra_pagina"></div>
		<div class="datos_personales margen">
			<div class="item item-4">
				<label>Nombre paciente:</label>
				<span>{{$formulario->nombre}} {{$formulario->apellido_paterno}} {{$formulario->apellido_materno}}</span>
			</div>
			<div class="item item-4">
				<label>Rut:</label>
				<span>{{$formulario->run}}-{{$formulario->dv}}</span>
			</div>
			<div class="item item-4">
				<label>Ficha:</label>
				<span>{{$formulario->ficha_clinica}}</span>
			</div>
			<div class="margen"></div>
			
			<div class="item item-4">
				<label>Edad:</label>
				<span>{{$formulario->edad}}</span>
			</div>
			<div class="item item-4">
				<label>Servicio:</label>
				<span>{{$formulario->servicio}}</span>
			</div>
			<div class="item item-4">
				<label>Sala:</label>
				<span>{{$formulario->sala}}</span>
			</div>
			<div class="item item-4">
				<label>Cama:</label>
				<span>{{$formulario->cama}}</span>
			</div>
		</div>
		<hr class="separador">
		<h3>Instalación</h3>
		<div class="item item-3">
			<label>Fecha:</label>
			<span>{{$instalacion->fecha_instalacion}}</span>
		</div>
		<div class="item item-3">
			<label>N° Matraz:</label>
			<span>{{$instalacion->n_maltraz}}</span>
		</div>
		<div class="item item-3">
			<label>Grupo ABO:</label>
			<span>{{$instalacion->grupo_abo}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-2">
			<label>PSL:</label>
			<span>{{$instalacion->psl}}</span>
		</div>
		<div class="item item-2">
			<label>Cantidad:</label>
			<span>{{$instalacion->cantidad}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-5">
			<label>T°:</label>
			<span>{{$instalacion->temp}}</span>
		</div>
		<div class="item item-5">
			<label>Pulso:</label>
			<span>{{$instalacion->pulso}}</span>
		</div>
		<div class="item item-5">
			<label>P.arterial:</label>
			<span>{{$instalacion->p_arterial}}</span>
		</div>
		<div class="item item-5">
		</div>
		<div class="item item-5">
			<label>Responsable:</label>
			<span>{{$instalacion->responsable}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-5">
			<label>T° 10°:</label>
			<span>{{$instalacion->temp_10}}</span>
		</div>
		<div class="item item-5">
			<label>Pulso 10°:</label>
			<span>{{$instalacion->pulso_10}}</span>
		</div>
		<div class="item item-5">
			<label>PA 10°:</label>
			<span>{{$instalacion->p_arterial_10}}</span>
		</div>
		<div class="item item-5">
		</div>
		<div class="item item-5">
			<label>Responsable:</label>
			<span>{{$instalacion->responsable_10}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-5">
			<label>T° 30°:</label>
			<span>{{$instalacion->temp_30}}</span>
		</div>
		<div class="item item-5">
			<label>Pulso 30°:</label>
			<span>{{$instalacion->pulso_30}}</span>
		</div>
		<div class="item item-5">
			<label>PA 30°:</label>
			<span>{{$instalacion->p_arterial_30}}</span>
		</div>
		<div class="item item-5">
			<label>Hora:</label>
			<span>{{$instalacion->hora_30}}</span>
		</div>
		<div class="item item-5">
			<label>Responsable:</label>
			<span>{{$instalacion->responsable_30}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-5">
			<label>T° 60°:</label>
			<span>{{$instalacion->temp_60}}</span>
		</div>
		<div class="item item-5">
			<label>Pulso 60°:</label>
			<span>{{$instalacion->pulso_60}}</span>
		</div>
		<div class="item item-5">
			<label>PA 60°:</label>
			<span>{{$instalacion->p_arterial_60}}</span>
		</div>
		<div class="item item-5">
			<label>Hora:</label>
			<span>{{$instalacion->hora_60}}</span>
		</div>
		<div class="item item-5">
			<label>Responsable:</label>
			<span>{{$instalacion->responsable_60}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-2">
			<label>Reacción adversa transfusional:</label>
			<span>{{$instalacion->reaccion_adversa_transfusional}}</span>
		</div>
		<div class="item item-2">
			<label>Folio ficha R.A.T:</label>
			<span>{{$instalacion->folio_ficha_rat}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-2">
			<label>Tratamiento:</label>
			<span>{{$instalacion->tratamiento}}</span>
		</div>
		<div class="item item-2">
			<label>Médico responsable:</label>
			<span>{{$instalacion->medico_responsable}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="item item-2">
			<label>Responsable toma de muestra:</label>
			<span>{{$instalacion->responsable_toma_muestra}}</span>
		</div>
		<div class="item item-2">
			<label>Hora:</label>
			<span>{{$instalacion->hora}}</span>
		</div>
		<div class="margen"></div>
		
		<div class="firmas">
			<div class="izquierda centro">
				<hr class="raya_firma">
				<p>Despacho</p>
			</div>
			<div class="derecha centro">
				<hr class="raya_firma">
				<p>Entrega</p>
			</div>
			<div class="limpiar"></div>
		</div>
		@endforeach
	</body>
</html>