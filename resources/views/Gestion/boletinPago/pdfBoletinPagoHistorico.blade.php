<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>

    <style>

        .centrar{
            text-align:center;
        }
    
        /* .mover{
            margin-left:100px !important;
        } */
    
        .letra {
            font-size: x-small;
        }

        .letra2 {
            font-size: 12px;
        }

        .titulo{
            font-size: large;
        }

        .box2 {
            display: inline-block;
            width: 170px;
            height: 10px;
            border-top: 1px solid;
            margin-left:60px;
            }
    
        .box3{
            display: inline-block;
        }

        table, td {
            border: 1px solid black;
        }
    </style>

</head>
<body>


        <div style="margin-top:10px;">
            <div class="box3 letra" style="width:20%;">
                <div>
                    <b>{{$infoGeneral["nombreEstablecimiento"]}}</b> 
                </div> 
                <div>
                    <b>{{$infoGeneral["nombreUnidad"]}}</b>
                </div>
            </div>
            <div class="box3 titulo" style="width:60%;text-align:center"><b>HISTORÍCO DE CARGO DE PACIENTE HOSPITALIZADO </b></div>
            
        </div>
    
        <div style="margin-top:10px;">
            <div class="letra box3" style="width: 33%;">
                NOMBRE PACIENTE: <b style="margin-left: 10px;">{{ $paciente["nombre"] }}</b>
            </div>
            <div class="letra box3" style="width: 33%;">
                R.U.T.: <b style="margin-left: 10px;"> {{ $paciente["rut"] }}</b> 
            </div>
            <div class="letra box3" style="width: 33%;">
                PREVISIÓN: <b style="margin-left: 10px;">{{ $paciente["prevision"] }}</b>
            </div>
        </div>
        <div style="margin-top:10px;">
            <div class="letra box3" style="width: 33%;">
                SALA: <b>{{$infoGeneral["nombreSala"]}}</b>
            </div>
            <div class="letra box3" style="width: 33%;">
                HOSPITALIZACIÓN: <b>{{ $infoGeneral["fechaIngreso"] }}</b>
            </div>
            <div class="letra box3" style="width: 33%;">
                CONSULTA: <b>{{ $fechaActual }}</b>
            </div>
        </div>
        
		<div class="row" style="margin-top:10px;" align="center">
			<table id="insumos" style=" " class="table">
				<thead class="letra2">
                    <tr style="font-weight: bold; background-color: #1E9966;">
                        <td style="color: #FFFFFF">PRODUCTO</td>					
                        <td style="color: #FFFFFF" align="center">CÓDIGO</td>
                        <td style="color: #FFFFFF" align="center">FECHA</td>
                        <td style="color: #FFFFFF" align="center">UNIDADES</td>
                        <td style="color: #FFFFFF" align="center">VALOR TOTAL</td>
                    </tr>                    
				</thead>
				<tbody class="letra2">

                    @foreach ($datos as $d)
                        <tr>
                            <td>{{ $d["nombre"] }}</td>
                            <td align="center">{{ $d["codigo"] }}</td>
                            <td align="center">{{ $d["fecha"] }}</td>
                            <td align="center">{{ $d["unidades"] }}</td>
                            <td align="center">$ {{ $d["valor"] }}</td>
                        </tr>
                    @endforeach                    
                </tbody>
                
                <tfoot class="letra">
                    <tr style="font-weight: bold;background-color: #1E9966;">
                        <td style="width: 100px;color: #FFFFFF" align="center" colspan="4">TOTAL GASTOS</td>
                        <td style="width: 100px; font-size: 15px;color: #FFFFFF" align="center" > $ {{ $valor_total }}</td>
                    </tr>
                </tfoot>
            </table>

			
        </div>
        

            
			
	
</body>
</html>