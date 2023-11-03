<!DOCTYPE html>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <head>
        <style>
            .letra {
                font-size: 12px;
            }

            .titulo{
                font-size: large;
            }
        
            .box3{
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div style="margin-top:10px;">
            <div class="box3 letra" style="width:20%;">
                <div>
                    {{$establecimiento->nombre}}
                </div> 
            </div>
            <h4 class="box3 titulo" style="width:60%;text-align:center"><b>HISTORIAL DE VISITAS</b></h4>
            <div class="box3 letra" style="width:19%;">
                <div>
                    Fecha: {{$fecha}}
                </div>
            </div>
        </div>

        <div class="letra">
            <div class="box3 letra" style="width:35%;">
                <b>Nombre paciente:</b>  {{ $infoPaciente[0]["nombre_completo"]}}
            </div>
            <div class="box3 letra" style="width:15%;">
                <b>RUN:</b>  {{ $infoPaciente[0]["rut"] }}-{{ ($infoPaciente[0]["dv"] == 10)?'K':$infoPaciente[0]["dv"] }}
            </div>
            <div class=" box3 letra" style="width:15%;">
                <b>Recibe visitas:</b> {{($infoPaciente[0]["recibe_visitas"]) == true ? 'Si' : 'No'}} 
            </div>
            @if($infoPaciente[0]["recibe_visitas"] == true)
                <div class="box3 letra" style="width:15%;">
                    <b>Numero personas:</b> {{($infoPaciente[0]["num_personas_visitas"]) ? $infoPaciente[0]["num_personas_visitas"] : 0}} 
                </div>
                <div class="box3 letra" style="width:15%;">
                    <b>Cantidad horas:</b> {{$infoPaciente[0]["cant_horas_visitas"]}} 
                </div>
            @endif
        </div>

        <div class="row" style="margin-top:10px;">

            <table id="as" style="margin-left:20px;">
                <thead class="letra">
                    <tr>
                        <th style="width:5%">N°</th>
                        <th style="width:10%">Fecha</th>
                        <th style="width:10%">Hora ingreso</th>
                        <th style="width:20%">Nombre completo</th>
                        <th style="width:10%">Run</th>
                        <th style="width:10%">Teléfono</th>
                        <th style="width:20%">Relación con el paciente</th>
                        <th style="width:20%">Usuario responsable</th>
                    </tr>
                </thead>
                <tbody class="letra">
                    @foreach($infoVisitas as $resp)
                    <tr>
                        <td style="text-align: center">{{$resp[0]}}</td>
                        <td style="text-align: center">{{$resp[1]}}</td>
                        <td style="text-align: center">{{$resp[2]}}</td>
                        <td style="text-align: center">{{$resp[3]}}</td>
                        <td style="text-align: center">{{$resp[4]}}</td>
                        <td style="text-align: center">{{$resp[5]}}</td>
                        <td style="text-align: center">{{$resp[6]}}</td>
                        <td style="text-align: center">{{$resp[7]}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>