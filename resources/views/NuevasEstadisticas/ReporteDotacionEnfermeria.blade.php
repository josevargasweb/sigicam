@extends("Templates/template")


@section("titulo")
Estadísticas
@stop


@section("miga")
<li><a href="#">Estadísticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte dotacion enfermeria</a></li>
@stop


@section("script")

<script>

</script>

@stop


@section("section")

    <style>
        .table.table-bordered.categorizacion > thead > tr > th {
            color: white;
            font-size: 15px;
            }
            .table-bordered > tbody > tr > th {
                background: #F5F5F5;
                color: #695959;
                /* font-size: 15px; */
            }
            .categorizacion {
                width: 100% !important;
                background: #1E9966;
            }
            .loader {
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: url("{{URL::to('/')}}/images/default.gif") 50% 50% no-repeat rgb(249,249,249);
                opacity: .8;
          }
    </style>

    <table class="table table-striped table-dark">
        <body>
            <legend>Reporte de dotación de enfermeras</legend>
            <div>
                <p>Los valores en <b>Plomo</b> corresponden al tipo de camas, mientras que el <b style="color:red">Rojo</b> es dependiendo de su categorización  </p>
            </div>
            <div>
                <table class="table table-bordered categorizacion tabla-sigicam">
                    <thead>
                        <tr>
                            <th>Tipo Cama</th>
                            <th>n° camas ocupadas</th>
                            <th>Enfermeras necesarias</th>
                            <th>Tens necesarias</th>
                            <th>Matronas necesarias</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($response_enfermeras as $key => $tipo)
                            {{-- <th>{{$key}}</th> --}}
                            @foreach($tipo as $key2 => $tipo2)
                            <tr>
                                <th>{{$key." ".$key2 }}</th>
                                <th>{{$datos[$key] [$key2][0]->total_camas }} <b style="color:red">({{$response_enfermeras2[$key][$key2]}})</b></th>
                                <th>{{ ($key != "PED")?round($tipo2,2):'0' }} 
                                    <b style="color:red">({{ ($key != "PED")?round($response_enfermeras_cate[$key][$key2],2):'0' }})</b></th>
                                <th>{{ round($response_tens[$key] [$key2],2)}} <b style="color:red">({{round($response_tens_cate[$key][$key2],2)}})</b></th>
                                <th>{{ ($key == "PED")?round($tipo2,2):'0' }}
                                        <b style="color:red">({{ ($key == "PED")?round($response_enfermeras_cate[$key][$key2],2):'0' }})</b></th>
                            </tr>
                                
                            @endforeach    
                        @endforeach
                    </tbody>
                </table>

            </div>


        </body>
    </table>

@stop