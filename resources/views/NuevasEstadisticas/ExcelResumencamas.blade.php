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
                    <th style="text-align:center; border: 1px solid black;">Area Funcional</th>
                    <th style="text-align:center; border: 1px solid black;">Servicio</th>
                    <th style="text-align:center; border: 1px solid black;">Dotación</th>
                    <th style="text-align:center; border: 1px solid black;">Cama libre</th>
                    <th style="text-align:center; border: 1px solid black;">Cama ocupada</th>
                    <th style="text-align:center; border: 1px solid black;">Cama bloqueada</th>
                    <th style="text-align:center; border: 1px solid black;">Total</th>
                    <th style="text-align:center; border: 1px solid black;">Índice Ocupación</th>
                    
                </tr>
			</thead>
			<tbody>
                 
                    {{ $dotacionTotal = 0 }}
                    {{ $libresTotal = 0}}
                    {{ $ocupadasTotal = 0 }}
                    {{ $bloqueadasTotal = 0 }}
                    {{ $ocupacionalTotal = 0 }}
                    {{ $totalTotal = 0 }}
                    {{ $sumaIndice = 0 }}
                
                @foreach($html["informacion"] as $info)
                    {{ $indice = ($info[4] == 0 || ($info[2]-$info[5]) == 0)?0:($info[4]*100)/($info[4]+$info[3])}}
                    {{ $total = ($info[3] + $info[4] + $info[5])}}
                    {{ $dotacionTotal += $info[2] }}                        
                    {{ $libresTotal += $info[3] }}
                    {{ $ocupadasTotal += $info[4] }}
                    {{ $bloqueadasTotal += $info[5] }}
                    {{ $totalTotal += $total }}
                    {{ $sumaIndice += $indice }}
                    {{ $cantidadUnidades = $loop->iteration }}
                    {{ $totalIndices = ( ($ocupadasTotal == 0 || ($libresTotal + $ocupadasTotal) == 0))?0: (($ocupadasTotal)/ ($ocupadasTotal + $libresTotal)) *100}}
                   {{--  {{ $totalIndices = ($cantidadUnidades == 0) ? 0 : $sumaIndice/$cantidadUnidades}} --}}

                    <tr>
                        <td>{{$info[0]}}</td>
                        <td>{{$info[1]}}</td>
                        <td>{{$info[2]}}</td>
                        <td>{{$info[3]}}</td>
                        <td>{{$info[4]}}</td>
                        <td>{{$info[5]}}</td>                        
                        <td>{{$total}}</td>
                        <td>{{ number_format($indice,1,',','') }}%</td>

                    </tr>
                @endforeach
                <tr>
                    <td>TOTAL</td>
                    <td></td>
                    <td>{{$dotacionTotal}}</td>
                    <td>{{$libresTotal}}</td>
                    <td>{{$ocupadasTotal}}</td>
                    <td>{{$bloqueadasTotal}}</td>                    
                    <td>{{$totalTotal}}</td>
                    <td>{{ number_format($totalIndices,1,',','')  }}%</td>
    
                </tr>

			</tbody>
		</table>
	</div>

</body>
</html>
