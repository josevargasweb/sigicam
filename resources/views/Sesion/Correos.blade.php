<html>
<head>
	<title>Contacto - {{$asunto}}
	</title>
	<style>
		.titulo{
			text-align: center;
			width: 100%;
			padding: 15px;
			color: white;
			background-color: #1E9966; /*verde*/
			font-weight: bold;
			font-size: 19px;
			margin-bottom: 5px;
		}
		.texto{
		
			padding: 15px;
			border: 3px solid #1E9966; /*verde*/
			margin-top: 2px;
			color: #383838; /*gris*/
		}
	</style>
</head>
<body>
<div class="titulo">
	Contacto - {{$asunto}}
</div>
<div class="texto">
	Nombre solicitante: {{ $nombre }} 
	<br>
	<br>
	Mensaje: {{ $texto }}
	<br>
	<br>
	Responder a: {{ $correo }}
	<br>
	<br>
</div>
<br>
<b>Enviado desde SIGICAM</b>
</body>
</html>
