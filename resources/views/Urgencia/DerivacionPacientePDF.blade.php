<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{ HTML::style('css/estilos_pdf.css') }}
    {{ HTML::style('css/bootstrap.min.css') }}
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                {{$establecimiento->nombre}}
            </div> 
            <div class="col-md-4">
            <h4 class="text-center"><b>Formulario Derivación</b></h4>
            </div> 
            <div class="col-md-4 text-right">
            Fecha: {{Carbon\Carbon::now()->format('d-m-Y')}}
            </div> 
        </div>
    </div>
    <br>

    <legend>Datos Generales</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
            {{Form::label('', "Nombre Paciente", array( ))}}
            <p class="box4">{{$nombreCompleto}}</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "RUN", array( ))}}
            <p class="box4">{{$rutDv}}</p>
            </div>
            <div class="col-md-4">
            {{Form::label('', "Edad", array( ))}}
            <p class="box4">{{$fechaNacimiento}} ({{$edad}})</p>
            </div>
            <div class="col-md-2">
            {{Form::label('', "Grupo Etario", array( ))}}
            <p class="box4">{{$grupoEtareo}}</p>
            </div>
        </div>
    </div>
    <br>

    <legend>Datos de derivación</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {{Form::label('', "Fecha de Hospitalización", array( ))}}
                    <p>{{$fechaHospitalizacion}}</p>
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Fecha Derivación", array( ))}}
                    <p>{{$fecha_egreso_derivacion}}</p>
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Unidad Funcional", array( ))}}
                    <p>{{$nombreUnidad}}</p>
                </div>
            </div>

        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {{Form::label('', "Tipo de Traslado", array( ))}}
                    @if(isset($info['tipo_traslado']))
                        <p>{{$info['tipo_traslado']}}</p>
                    @else
                        <p>Sin Especificar</p>
                    @endif
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Motivo Derivación", array( ))}}
                    @if(isset($motivo_derivacion))
                        <p>{{$motivo_derivacion}}</p>
                    @else
                        <p>Sin Especificar</p>
                    @endif
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Tipo Cama", array( ))}}
                    @if(isset($tipo_cama))
                        <p>{{$tipo_cama}}</p>
                    @else
                        <p>Sin Especificar</p>
                    @endif
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Médico Derivador", array( ))}}
                    @if (isset($info['id_medico']) && isset($info['nombreMedico']))
                        <p>{{$info['nombreMedico']}}</p>
                    @else
                        <p>Sin especificar</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {{Form::label('', "Ges", array( ))}}
                    @if(isset($info['ges']))
                        @if ($info['ges'] == true)
                            <p>Si</p>
                        @elseif ($info['ges'] == false)
                            <p>No</p>                            
                        @endif
                    @else
                        <p>Sin especificar</p>
                    @endif
                    
                </div>
                <div class="col-md-3">
                    {{Form::label('', "UGCC", array( ))}}
                    @if(isset($info['ges']))
                        @if ($info['ugcc'] == true)
                            <p>Si</p>
                        @elseif ($info['ugcc'] == false)
                            <p>No</p>
                        @endif
                    @else
                        <p>Sin especificar</p>
                    @endif                    
                </div>
                @if (isset($info['ugcc']) && $info['ugcc'] == true)
                    <div class="col-md-3">
                        {{Form::label('', "Especificar", array( ))}}
                        @if(isset($info['ges']))
                            @if ($info['t_ugcc'] == '1')
                                <p>Privado-GRD</p>
                            @elseif ($info['t_ugcc'] == '2')
                                <p>GDR-Hospital</p>
                            @elseif ($info['t_ugcc'] == '3')
                                <p>Privado No ranqueado</p>
                            @endif
                        @else
                            <p>Sin especificar</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    {{Form::label('', "Tipo de Centro", array( ))}}
                    @if(isset($tipo_centro))
                        <p>{{$tipo_centro}}</p>
                    @else
                        <p>Sin Especificar</p>
                    @endif
                </div>
                @if ($centro_derivacion != '')
                    <div class="col-md-6">
                        {{Form::label('', "Centro de Derivación", array( ))}}
                        <p>{{$centro_derivacion}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {{Form::label('', "Vía de traslado", array( ))}}
                    @if(isset($info['via_traslado']))
                        @if ($info['via_traslado'] == '1')
                            <p>Aéreo</p>
                        @elseif ($info['via_traslado'] == '2')
                            <p>Terrestre</p>
                        @endif
                    @else
                        <p>Sin especificar</p>
                    @endif  
                </div>
                @if (isset($info['via_traslado']) && $info['via_traslado'] == '2')
                    <div class="col-md-3">
                        {{Form::label('', "Especificar", array( ))}}
                        @if(isset($info['via_traslado']))
                            @if ($info['via_traslado'] == '1')
                                <p>M1 Básico</p>
                            @elseif ($info['via_traslado'] == '2')
                                <p>M2 Avanzado</p>
                            @elseif ($info['via_traslado'] == '3')
                                <p>M3 medicalizado</p>
                            @endif
                        @else
                            <p>Sin especificar</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {{Form::label('', "Tramo", array( ))}}
                    @if(isset($info['tramo']))
                        <p>{{$info['tramo']}}</p>
                    @else
                        <p>Sin Especificar</p>
                    @endif
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Comuna origen", array( ))}}
                    <p>{{$comuna_origen}}</p>
                </div>
                <div class="col-md-3">
                    {{Form::label('', "Comuna destino", array( ))}}
                    <p>{{$comuna_destino}}</p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if( isset($info['tramo']) && ($info['tramo'] == 'ida' || $info['tramo'] == 'ida-rescate'))
                    <div class="col-md-3">
                        {{Form::label('', "Fecha de ida", array( ))}}
                        @if($fecha_ida != '')
                            <p>{{$fecha_ida}}</p>
                        @else
                            <p>Sin especificar</p>
                        @endif
                    </div>
                @endif
                @if(isset($info['tramo']) && $info['tramo'] == 'ida-rescate')
                    <div class="col-md-3">
                        {{Form::label('', "Fecha rescate", array( ))}}
                        @if($fecha_rescate != '')
                            <p>{{$fecha_rescate}}</p>
                        @else
                            <p>Sin especificar</p>
                        @endif
                    </div>
                @endif
                <div class="col-md-3">
                    {{Form::label('', "Estado paciente", array( ))}} 
                    <p>{{$estado_paciente}}</p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    {{Form::label('', "Movil", array( ))}}
                    <p>{{$movil}}</p>
                </div>
                
                @if(isset($info['movil']) && $info['movil'] == '2')
                    <div class="col-md-3">
                        {{Form::label('', "Especificar", array( ))}}
                        @if(isset($info['compra_servicio']) && ($info['compra_servicio'] != null || $info['compra_servicio'] != ''))
                            <p>{{$compra_servicio}}</p>
                        @else
                            <p>Sin especificar</p>
                        @endif
                    </div>
                @endif
                @if(isset($info['compra_servicio']) && $info['compra_servicio'] == '3')
                    <div class="col-md-3">
                        {{Form::label('', "Especificar", array( ))}}
                        <p>{{$compra_servicio_otro}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                {{Form::label('', "Comentarios", array( ))}}
                @if(isset($info['comentarios']) && $info['comentarios'] != '')
                    <p>{{$info['comentarios']}}</p>
                @else
                    <p>Sin comentarios</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>