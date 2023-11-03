<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
</head>
<body>

	<div class="row">
        <h4>SIGICAM</h4>

    </div>

    <br>
	<div class="row">
		<table id="tablaVistaLista" class="table  table-condensed table-collapse">
			<thead>
                <tr style="background:#399865;">
                    <th style="text-align:center; border: 1px solid black;">Fecha</th>
                    <th style="text-align:center; border: 1px solid black;">Categoría</th>
                    <th style="text-align:center; border: 1px solid black;">Servicios</th>
                    <th style="text-align:center; border: 1px solid black;">Área Funcional</th>
                    <th style="text-align:center; border: 1px solid black;">Estado paciente</th>
                    @if( $html["edad"] <= 15 || $html["edad"] == '')
                        <th style="text-align:center; border: 1px solid black;">Acompañamiento</th>
                    @endif
                    <th style="text-align:center; border: 1px solid black;">Especialidades</th>
                </tr>
			</thead>
			<tbody>

                @foreach($html["informacion"] as $info)

                    <tr>
                        <td>{{$info[0]}}</td>
                        <td>{{$info[1]}}</td>
                        <td>{{$info[2]}}</td>
                        <td>{{$info[3]}}</td>
                        <td>{!!$info[4]!!}</td>
                        @if( $html["edad"] <= 15 || $html["edad"] == '')
                            <td>{!!$info[6]!!}</td>
                        @endif
                        <td>{!!$info[5]!!}</td>
                    </tr>
                @endforeach

			</tbody>
		</table>
	</div>

</body>
</html>
