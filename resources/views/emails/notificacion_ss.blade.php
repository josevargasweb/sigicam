<html>
<head>
</head>
<body style="font-family:sans;">
	Estimado(a) {{ $nombre }},<br><br>

	Los siguientes pacientes que se encuentran en el extra sistema pueden ser rescatados.

	<table  style="border-collapse: collapse;border: 1px solid gray ;padding:5px;"><thead style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">
	<tr class="border-collapse: collapse;border: 1px solid gray ;padding:5px;">
		<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Run</th>
		<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Nombre</th>
		<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Establecimiento origen</th>
		<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Establecimiento extra sistema</th>
		<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Servicio</th>
	</tr></thead>
	<tbody>{!! $contenido !!}</tbody>
	</table>
	<br>
	Por favor rescatar a los pacientes a la brevedad.
	<br>
	<br>
	<p style="font-size:x-small;">Este mensaje fue autogenerado el {{ date('d/m/Y') }} a las {{ date('H:i:s') }} en {{ URL::to("/") }}. </p>
</body>

</html>
