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
			.item-1_5{
				width:68%;
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
			body{
				width: 8.5in;
				height:11in;
			}
			img{
				width:100%;
			}
			th{
				text-align:left;
			}
			table{
				page-break-inside: avoid;
			}
			table,td,th{
				border-style:solid;
				border-width:1px;
				border-color:black;
				border-collapse: collapse;
				padding:2px;
			}
			.ancho{
				width:100%;
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
			<h3>Partograma</h3>
		</div>
		<!-- Datos personales -->
		<div class="datos_personales margen">
			<div class="item item-2">
				<label>Nombre paciente:</label>
				<span>{{$formulario->nombre}} {{$formulario->apellido_paterno}} {{$formulario->apellido_materno}}</span>
			</div>
			<div class="item item-3">
				<label>Rut:</label>
				<span>{{$formulario->run}}-{{$formulario->dv}}</span>
			</div>
			<div class="margen"></div>
			<div class="item item-2">
				<label>Ficha:</label>
				<span>{{$formulario->ficha_clinica}}</span>
			</div>
			<div class="item item-3">
				<label>Edad:</label>
				<span>{{$formulario->edad}}</span>
			</div>
			
			<div class="margen"></div>
			<div class="item item-2">
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
		<!-- gráfico partograma -->
		<div>
			@foreach($imagenes as $imagen)
				<img src="{{$imagen}}">
			@endforeach
		</div>
		
		<!-- tabla -->
		@foreach($tabla as $minitabla)
		<table class="margen">
			<caption>Seguimiento</caption>
			<tr>
				<th>
					Hora
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->hora}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					LCF
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->lcf}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					P.A.
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->pa_s}} / {{$celda->pa_d}}
				</td>
				@endforeach
				
			</tr>
			<tr>
				<th>
					PULSO
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->pulso}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					D.U./FREC.
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->du}}/{{$celda->frecuencia_cardiaca}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					DURACIÓN (s)
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->duracion}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INTENSIDAD
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->intensidad}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					CUELLO
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->cuello}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					MEMBRANAS
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->membrana}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					L.A.
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->la}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					USO BALÓN
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->uso_balon}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					POSICIÓN (MATERNA)
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->posicion_materna}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					MONITOREO
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->monitoreo}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					EXAMINADOR
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->nombre_examinador}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					ANELGESIA PERIDURAL
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->analgesia_peridural ? "Sí" : "No"}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					ANELGESIA PERIDURAL OBSERVACIONES
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->observaciones_analgesia_peridural}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INSTALACIÓN DE VÍAS
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->instalacion_via ? "Sí" : "No"}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INSTALACIÓN DE VÍA NÚMERO
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->numero_instalacion_via}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INSTALACIÓN DE VÍA OBSERVACIONES
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->observacion_instalacion_via}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INSTALACIÓN DE SONDA VESICAL
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->sonda_vesical ? "Sí" : "No"}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INSTALACIÓN DE SONDA VESICAL NÚMERO
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->numero_sonda_vesical}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					INSTALACIÓN DE SONDA VESICAL OBSERVACIONES
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->observacion_sonda_vesical}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					CATETERISMO VESICAL
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->cateterismo_vesical ? "Sí" : "No"}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					CATETERISMO VESICAL NÚMERO
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->numero_cateterismo_vesical}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					CATETERISMO VESICAL OBSERVACIONES
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->observacion_cateterismo_vesical}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					ALERGIAS
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->alergia ? "Sí" : "No"}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					ALERGIAS OBSERVACIONES
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->detalle_alergia}}
				</td>
				@endforeach
			</tr>
			<tr>
				<th>
					MEDIAS ATE
				</th>
				@foreach($minitabla as $celda)
				<td>
					{{$celda->medias_ate ? "Sí" : "No"}}
				</td>
				@endforeach
			</tr>
		</table>
		@endforeach
		<!-- evolución -->
		<table class="ancho">
			<caption>Evolución</caption>
			<theader>
				<th>
					Fecha
				</th>
				<th>
					Observación
				</th>
				<th>
					Usuario responsable
				</th>
			</theader>
			<tbody>
				@foreach($evolucion as $evo)
				<tr>
					<td>{{$evo->fecha_evolucion}}</td>
					<td>{{$evo->observacion_evolucion}}</td>
					<td>{{$evo->nombre_usuario_responsable}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</body>
</html>