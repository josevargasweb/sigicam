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
            <div class="box3 titulo" style="width:60%;text-align:center"><b>NOTA DE CARGO DE PACIENTE HOSPITALIZADO </b></div>
            
        </div>

        <div style="margin-top:10px;">
            <div class="letra box3" style="width: 33%;">
                SALA: <b>{{$infoGeneral["nombreSala"]}}</b>
            </div>
            <div class="letra box3" style="width: 33%;">
                MES:    <b>{{ $fecha[0] }}</b>
            </div>
            <div class="letra box3" style="width: 33%;">
                AÑO: <b>{{ $fecha[1] }}</b>
            </div>
        </div>
    
        <div style="margin-top:10px;">
            <div class="letra box3" style="width: 60%;">
                NOMBRE PACIENTE: <b style="margin-left: 10px;">{{ $paciente["nombre"] }}</b>
            </div>
            <div class="letra box3" style="width: 33%;">
                PREVISIÓN: <b style="margin-left: 10px;">{{ $paciente["prevision"] }}</b>
            </div>
        </div>
        <div style="margin-top:10px;">
            <div class="letra box3" style="width: 33%;">
                R.U.T.: <b style="margin-left: 10px;"> {{ $paciente["rut"] }}</b> 
            </div>
            <div class="letra box3" style="width: 33%;">
                HOSPITALIZACIÓN: <b>{{ $infoGeneral["fechaIngreso"] }}</b>
            </div>
        </div>
        <div style="margin-top:10px;">
            <div class="letra box3" style="width: 33%;">

            </div>
            <div class="letra box3" style="width: 33%;">
                CONSULTA: <b>{{ $fechaActual }}</b>
            </div>
        </div>
        
        <div hidden>
            {{ $key_s = 3 }}
            {{ $key_m = 3 }}
            {{ $key_i = 3 }}
        </div>

        <div class="row" style="margin-top:10px;">
			<table id="insumos" style=" " class="table">
				<thead class="letra">
                    <tr style="background-color: #1E9966">
                        <td style="width: 125px !important;color: #FFFFFF" rowspan="2"><b>PRODUCTO</b> </td>					
                        <td style="width: 50px;color: #FFFFFF" align="center" rowspan="2"><b>CÓDIGO</b> </td>
                        @if(count($fechas) > 0)
                            <td style="color: #FFFFFF" align="center" colspan="{{ count($fechas) }}"><b>DÍAS</b> </td>     
                        @endif             
                        <td style="width: 10px;color: #FFFFFF" rowspan="2"><b>CANT.</b> </td>
                        <td style="width: 10px;color: #FFFFFF" rowspan="2"><b>VALOR</b> </td>
                    </tr>    
                    <tr style="background-color: #1E9966">
                        @foreach($fechas as $f)
                            <td style="width: 13px;color: #FFFFFF" align="center">{{ $f }}</td>
                        @endforeach   
                    </tr>                
				</thead>
				<tbody class="letra">

                    @foreach ($datos as $dato)
                        <tr>
                            @foreach($dato as $key_i => $d)
                                 @if ($key_i > 1)
                                    <td align="center">{{ $d }}</td>
                                @else
                                    <td>{{ $d }}</td>
                                @endif
                            @endforeach
                        </tr>
                        
                    @endforeach                    
                </tbody>
                
                <tfoot class="letra">
                    <tr style="font-weight: bold;background-color: #1E9966;">
                        <td style="color: #FFFFFF" colspan="{{ $key_i }}">TOTAL</td>
                        <td style="color: #FFFFFF" align="center">${{ $total }}</td>
                    </tr>
                </tfoot>
			</table>
			
        </div>
			
	
</body>
</html>