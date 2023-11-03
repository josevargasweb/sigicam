<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
</head>
<body>

	<div class="row">
    <h3 style="text-align: center;">Informe Diario</h3>
    <p style="font-size: 14px; text-align: center;">Pacientes sin categorizar.</p>

    </div>

    <br>
	<div class="row">
		<table id="tablaVistaLista" class="table  table-condensed table-collapse">
			<thead>
                <tr style="background:#399865;">
                    <th style="text-align:center; border: 1px solid black;">Rut</th>
                    <th style="text-align:center; border: 1px solid black;">Nombre</th>
                    <th style="text-align:center; border: 1px solid black;">Fecha nacimiento</th>
                    <th style="text-align:center; border: 1px solid black;">Area funcional</th>
                    <th style="text-align:center; border: 1px solid black;">Unidad funcional</th>
                    <th style="text-align:center; border: 1px solid black;">sala</th>
                    <th style="text-align:center; border: 1px solid black;">cama</th>
                </tr>

			</thead>
			<tbody>
                @foreach($html as $r)
                  <tr>
                      <td>{{$r['rut']}}</td>
                      <td>{{$r['nombre']}}</td>
                      <td>{{$r['fecha_nacimiento']}}</td>
                      <td>{{$r["area_funcional"]}}</td>
                      <td>{{$r["unidad_funcional"]}}</td>
                      <td>{{$r["sala"]}}</td>
                      <td>{{$r["cama"]}}</td>
                  </tr>


                @endforeach

			</tbody>
		</table>
	</div>

</body>
</html>
