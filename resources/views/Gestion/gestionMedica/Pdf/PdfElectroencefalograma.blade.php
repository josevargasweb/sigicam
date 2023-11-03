<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<style>
			label{
				font-weight: bold;
				width:100%;
			}
			.col{
				margin:0px;
				padding:0px;
				display:inline-block;
				vertical-align: top;
				margin-bottom:5px;
				float:left;
			}
			.col_100{
				width:100%;
			}
			.col_50{
				width:50%;
			}
			.col_25{
				width:25%;
			}
			.col_33{
				width:33.3%;
			}
			.espacio{
				height:20px;
			}
			.titulo{
				text-align:center;
			}
		</style>
	</head>
	<body>
		<h3 class="titulo">Solicitud Examen Electroencefalograma</h3>
		<div class=" col col_100">
			<label>Fecha:</label><span>{{$datos->fecha}}</span>
		</div>
		<div class="espacio"></div>
		<div class="col col_100">
			<div class="col col_25">
				<label>Nombre:</label><p>{{$datos->nombre}}</p>
			</div>
			<div class="col col_25">
				<label>RUT:</label><p>{{$datos->rut}}</p>
			</div>
			<div class="col col_25">
				<label>Edad:</label><p>{{$datos->edad}}</p>
			</div>
		</div>
		<div class="col col_100">
			<div class="col col_25">
				<label>Fecha de nacimiento:</label><p>{{$datos->fecha_nacimiento}}</p>
			</div>
			<div class="col col_25">
				<label>Diagnóstico:</label><p>{{$datos->diagnostico}}</p>
			</div>
			<div class="col col_25">
				<label>Comentario:</label><p>{{$datos->comentario_diagnostico}}</p>
			</div>
			<div class="col col_25">
				<label>Procedencia:</label><p>{{$datos->procedencia}}</p>
			</div>
		</div>
		<div class="col col_100">
			<div class="col col_25">
				<label>Preivision:</label><p>{{$datos->prevision}}</p>
			</div>
			<div class="col col_25">
				<label>Lesión en la neuroimagen: Localización</label><p>{{$datos->lesion_localizacion}}</p>
			</div>
			<div class="col col_25">
				<label>Intervención quirúrgica: Área</label><p>{{$datos->intervencion_area}}</p>
			</div>
			<div class="col col_25">
				<label>Medicamento</label><p>{{$datos->medicamento}}</p>
			</div>
		</div>
		<div class="col col_100">
			<div class="col col_25">
				<label>
Fecha última crisis:</label><p>{{$datos->fecha_ultima_crisis}}</p>
			</div>
			
		</div>
		<div class="col col_100">
			<div class="col col_50">
				<label>Categoría de prioridad:</label>
				<div>
					<input type="radio" @if($datos->lateralidad == "diestro") checked @endif>Diestro<br>
					<input type="radio" @if($datos->lateralidad == "zurdo") checked @endif>Zurdo<br>
					<input type="radio" @if($datos->lateralidad == "ninguno") checked @endif>Ninguno<br>
				</div>
			</div>
			<div class="col col_50">
				<label>Examen solicitado:</label>
				<div>
					<input type="checkbox" @if($datos->reposo) checked @endif>Reposo<br>
					<input type="checkbox" @if($datos->hiperventilacion) checked @endif>Hiperventilación<br>
					<input type="checkbox" @if($datos->fotoestimulacion) checked @endif>Fotoestimulación<br>
					<input type="checkbox" @if($datos->privacion_parcial_sueno_nino) checked @endif>Pivación parcial de sueño: niño<br>
					<input type="checkbox" @if($datos->privacion_total_sueno_adulto) checked @endif>Privación total de sueño: adulto<br>
					<input type="checkbox" @if($datos->eeg_con_induccion_sueno) checked @endif>EEG con inducción sueño<br>
				</div>
			</div>
		</div>
		@if($datos->eeg_con_induccion_sueno && $datos->edad < 18)
		<div class="col col_100">
			<div class="col col_25">
				<label>Medicamentos:</label><p>{{$datos->medicamentos}}</p>
			</div>
			<div class="col col_25">
				<label>Dosis:</label><p>{{$datos->dosis}}</p>
			</div>
			<div class="col col_25">
				<label>Via de administración:</label><p>{{$datos->via_administracion}}</p>
			</div>
			<div class="col col_25">
				<label>Horario previo a examen:</label><p>{{$datos->horario_previo_examen}}</p>
			</div>
		</div>
		@endif
	</body>
</html>