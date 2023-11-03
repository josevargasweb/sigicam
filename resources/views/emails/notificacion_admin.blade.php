<html>
<head>
</head>
<body style="font-family:sans;">
Estimado(a) {{ $nombre }},<br><br>

Los siguientes pacientes en el extrasistema tienen cupos disponibles de camas
(incluídas reconvertibles) dentro de los servicios.
<table  style="border-collapse: collapse;border: 1px solid gray ;padding:5px;"><thead style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">
<tr style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">
	<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Run</th>
	<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Nombre</th>
	<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Establecimiento extrasistema</th>
	<th style="border-collapse: collapse;border: 1px solid gray ;padding:5px;">Servicio</th>
</tr></thead>
<tbody style"border-collapse: collapse;border: 1px solid gray ;padding:5px;">{!! $contenido !!}</tbody>
</table>

</body>

</html>
