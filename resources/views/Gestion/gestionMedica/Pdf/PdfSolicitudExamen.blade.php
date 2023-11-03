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
		<h3 class="titulo">Solicitud Examen Electromiografía y Neuroconducción</h3>
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
		</div>
		<div class="col col_100">
			<div class="col col_25">
				<label>Peso:</label><p>{{$datos->peso}}</p>
			</div>
			<div class="col col_25">
				<label>Talla:</label><p>{{$datos->talla}}</p>
			</div>
		</div>
		<div class="col col_100">
			<div class="col col_50">
				<label>Categoría de prioridad:</label>
				<div>
					<input type="checkbox" @if($datos->urgente) checked @endif>Urgente<br>
					<input type="checkbox" @if($datos->medio_urgente) checked @endif>Medio urgente de 1 a 2 meses<br>
					<input type="checkbox" @if($datos->puede_esperar) checked @endif>Puede esperar más de 3 meses<br>
				</div>
			</div>
			<div class="col col_50">
				<label>Categoría de prioridad:</label>
				<div>
					<input type="checkbox" @if($datos->ecocardiograma) checked @endif>Ecocardiograma 2 doppler color 2-D<br>
					<input type="checkbox" @if($datos->test_esfuerzo) checked @endif>Test de esfuerzo<br>
					<input type="checkbox" @if($datos->holter_presion) checked @endif>Holter de presión<br>
					<input type="checkbox" @if($datos->holter_arritmia) checked @endif>Holter de arritmia<br>
				</div>
			</div>
		</div>
	</body>
</html>