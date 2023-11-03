<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - HISTORÍCO DE CARGO DE PACIENTE HOSPITALIZADO </h4>	
	</div>

	<table>
		<thead>
			<tr>
				<td>
					<b>{{$info["infoGeneral"]["nombreEstablecimiento"]}}</b>
				</td>
			</tr>
			
		</thead>
	</table>
	{{-- Informacion de sala y mes consulta --}}
	<table>
		<thead>
			<tr>
				<td>UNIDAD:</td>
				<td colspan="2"><b>{{$info["infoGeneral"]["nombreUnidad"]}}</b></td>
				<td>SALA:</td>
				<td colspan="2"><b>{{$info["infoGeneral"]["nombreSala"]}}</b></td>
			</tr>
		</thead>
	</table>
	{{-- Informacion paciente --}}
	<table>
		<thead>
			<tr>
				<td>NOMBRE PACIENTE:</td>
				<td colspan="5"><b>{{$info["paciente"]["nombre"]}}</b></td>
				<td>R.U.T.:</td>
				<td colspan="2"><b>{{$info["paciente"]["rut"]}}</b></td>
				<td>PREVISIÓN:</td>
				<td colspan="2"><b>{{$info["paciente"]["prevision"]}}</b></td>
			</tr>
		</thead>
	</table>

	{{-- Infomracion hospitalizacion y consulta --}}
	<table>
		<thead>
			<tr>
				<td>HOSPITALIZACIÓN:</td>
				<td colspan="5"><b>{{$info["infoGeneral"]["fechaIngreso"]}}</b></td>
				<td>CONSULTA:</td>
				<td colspan="2"><b>{{$info["fechaActual"]}}</b></td>
			</tr>
		</thead>
	</table>

	<table>
        <thead >
            <tr style="background-color: #1E9966;">
                <td style="color: #FFFFFF"><b>PRODUCTO</b> </td>					
                <td style="color: #FFFFFF" align="center"><b>CÓDIGO</b> </td>
                <td style="color: #FFFFFF" align="center"><b>FECHA</b> </td>
                <td style="color: #FFFFFF" align="center"><b>UNIDADES</b> </td>
                <td style="color: #FFFFFF" align="center"><b>VALOR TOTAL</b> </td>
            </tr>                    
        </thead>
        <tbody >

            @foreach ($info["datos"] as $d)
                <tr>
                    <td>{{ $d["nombre"] }}</td>
                    <td align="center">{{ $d["codigo"] }}</td>
                    <td align="center">{{ $d["fecha"] }}</td>
                    <td align="center">{{ $d["unidades"] }}</td>
                    <td align="center">$ {{ $d["valor"] }}</td>
                </tr>
            @endforeach                    
        </tbody>
        
        <tfoot >
            <tr style="font-weight: bold;background-color: #1E9966;">
                <td style="color: #FFFFFF" align="center" colspan="4"><b>TOTAL GASTOS</b> </td>
                <td style="color: #FFFFFF" align="center" > $ {{ $info["valor_total"] }}</td>
            </tr>
        </tfoot>
    </table>
	
		
</body>
</html>