<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{ HTML::style('css/estilos_pdf.css') }}
    {{ HTML::style('css/bootstrap.min.css') }}
    <style>
        legend{
            font-size:14px;
            font-weight:bold;
        }

        label{
            font-size:12px;
            font-weight:bold;
        }

        p{
            font-size:12px;
        }

        .lh20{
            line-height:20px;
        }

        .lh25{
            line-height:25px;
        }

        p,li{
            letter-spacing:2px;
        }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center"><b>Solicitud De Interconsulta o Derivación</b></h4>
            </div> 
        </div>
    </div>
    <br>
	<legend>Datos de Admisión</legend>
    <hr>
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-4" style="">
                <b> {{Form::label('', "1.- Servicio de Salud: ", array('class' => 'letra12' ))}} </b> 
				@if(isset($establecimiento) && $establecimiento->servicio_salud != null)
					<p class="letra">{{$establecimiento->servicio_salud}}</p>
				@else
					<p class="letra">Sin información</p>
				@endif
            </div>
            <div class="col-md-8" style="">
			<b> {{Form::label('', "2.- Establecimiento: ", array('class' => 'letra12' ))}} </b>
				@if(isset($establecimiento) && $establecimiento->nombre_establecimiento != null)
					<p class="letra">{{$establecimiento->nombre_establecimiento}} </p>
				@else
					<p class="letra">Sin información</p>
				@endif
			</div>
			<div class="col-md-6" style="">
				<b> {{Form::label('', "3.- Especialidad: ", array( 'class' => 'letra12'))}} </b>
				@if(isset($especialidad) && isset($especialidad->nombre) && $especialidad->nombre !== null && $especialidad->nombre !== '')
                    <p>{{$especialidad->nombre}}</p>
                @else
                    <p>Sin información</p>
                @endif 
			</div>
			<div class="col-md-6" style="">
					<b> {{Form::label('', "4.- Unidad: ", array( 'class' => 'letra12'))}} </b>
				@if(isset($establecimiento) && $establecimiento->unidad)
					<p class="letra">{{$establecimiento->unidad}} </p>
				@else
					<p class="letra">Sin información</p>
				@endif
            </div>
        </div>
    </div>
    <br>
	<legend>Datos del (de la) paciente</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="col-md-12">
                    {{Form::label('', "5.- Nombre:", array( ))}}
                </div>
                @if(isset($infoPaciente) && $infoPaciente->nombre != null )
                    @if( $infoPaciente->apellido_paterno != null )
                        <div class="col-md-4">
                            <p>{{$infoPaciente->apellido_paterno}}</p>
                        </div>
                    @endif 
                    @if( $infoPaciente->apellido_materno != null )
                        <div class="col-md-4">
                            <p>{{$infoPaciente->apellido_materno}}</p>
                        </div>
                    @endif
                    @if( $infoPaciente->nombre != null )
                        <div class="col-md-4">
                            <p>{{$infoPaciente->nombre}}</p>
                        </div>
                    @endif
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-6">
                {{Form::label('', "6.- Historia Clínica", array( ))}}
                @if(isset($establecimiento) && $establecimiento->ficha_clinica != null && $establecimiento->ficha_clinica != '')
                    <p>{{$establecimiento->ficha_clinica}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-3">
                <div class="col-md-3">
                    {{Form::label('', "7.- RUT:", array( ))}}
                </div>
                <div class="col-md-9">
                    @if(isset($infoPaciente) && $infoPaciente->rut !== null  && $infoPaciente->rut !== '' && $infoPaciente->dv !== null && $infoPaciente->dv !== '')
                            <p class="lh20">{{$infoPaciente->rut}}-{{$infoPaciente->dv}}</p>
                    @else
                        <p class="lh20">Sin información</p>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <div class="col-md-8">
                    {{Form::label('', "8.- Recien nacido, RUT beneficiario:", array( ))}}
                </div>
                <div class="col-md-4">
                    @if(isset($infoPaciente) && $infoPaciente->rut_madre !== null && $infoPaciente->dv_madre !== null)
                        <p class="lh20">{{$infoPaciente->rut_madre}}-{{$infoPaciente->dv_madre}}</p>
                    @else
                        <p class="lh20">Sin información</p>
                    @endif
                </div>
            </div>
        
            <div class="col-md-4">
                <div class="col-md-3">
                    {{Form::label('', "9.- Género", array( ))}}
                </div>
                <div class="col-md-9">
                    @if(isset($infoPaciente) && $infoPaciente->sexo != null && $infoPaciente->sexo === 'masculino')
                        <p class="lh20">Hombre</p>
                    @elseif(isset($infoPaciente) && $infoPaciente->sexo != null && $infoPaciente->sexo === 'femenino')
                        <p class="lh20">Mujer</p>
                    @elseif(isset($infoPaciente) && $infoPaciente->sexo != null && $infoPaciente->sexo === 'indefinido')
                        <p class="lh20">Intersex</p>
                    @elseif(isset($infoPaciente) && $infoPaciente->sexo != null && $infoPaciente->sexo === 'desconocido')
                        <p class="lh20">Desconocido</p>
                    @else
                        <p class="lh20">Sin información</p>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <div class="col-md-6">
                    {{Form::label('', "10.- Fecha de nacimiento:", array( ))}}
                </div>
                <div class="col-md-6">
                    @if(isset($infoPaciente) && $infoPaciente->fecha_nacimiento != null)
                        <p class="lh20">{{ Carbon\Carbon::parse($infoPaciente->fecha_nacimiento)->format('d-m-Y') }}</p>
                    @else
                        <p class="lh20">Sin información</p>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-3">
                    {{Form::label('', "11.- Edad:", array( ))}}
                </div>
                <div class="col-md-8">
                    @if(isset($edad) && $edad != null)
                        <p class="lh20">{{ Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento)}} AÑOS</p>
                    @else
                        <p class="lh20">Sin información</p>
                    @endif
                </div>
            </div>
           <div class="col-md-12">
                {{Form::label('', "12.- Domicilio (calle, número, número interior, bloque(block),villa, localidad):", array('class' => 'col-md-12'))}}
                @if(isset($infoPaciente) && ($infoPaciente->observacion != null && $infoPaciente->observacion != '' ||  $infoPaciente->calle != null && $infoPaciente->calle != '' || $infoPaciente->numero != null && $infoPaciente->numero != ''))
                    <p class="lt2">{{ ($infoPaciente->calle != null) ? $infoPaciente->calle : ''}} {{( $infoPaciente->calle != null && ($infoPaciente->numero != null ||$infoPaciente->observacion != null) ) ? ',' : ''}} {{ ($infoPaciente->numero != null) ? $infoPaciente->numero : ''}} {{($infoPaciente->numero != null && ($infoPaciente->observacion != null) ) ? ',' : '' }} {{($infoPaciente->observacion != null) ? $infoPaciente->observacion: ''}}</p>
                @else
                    <p class="lt2">Sin información</p>
                @endif
           </div>
           <div class="col-md-3">
                {{Form::label('', "13.- Comuna residencia:", array('class' => 'col-md-12'))}}
                @if(isset($region) && $region != null)
                    @php 
                        $region_consulta = App\Models\Region::select('nombre_region')->where('id_region',$region)->first();
                    @endphp
                @endif
                @if(isset($comuna) && $comuna != null)
                    @php 
                        $comuna_consulta = App\Models\Comuna::select('nombre_comuna')->where('id_comuna',$comuna)->first();
                    @endphp
                @endif
                    <p>{{(isset($comuna_consulta->nombre_comuna) && $comuna_consulta->nombre_comuna != null) ? $comuna_consulta->nombre_comuna : ''}}{{(isset($region_consulta->nombre_region) && $region_consulta->nombre_region != null) ?  ',' : ''}}{{(isset($region_consulta->nombre_region) && $region_consulta->nombre_region != null) ?  $region_consulta->nombre_region : ''}}</p>

                    <p></p>
           </div>
            <div class="col-md-2">
                <div class="col-md-12">
                    {{Form::label('', "14 .- Teléfono 1:", array( ))}}
                </div>
                @if(isset($telefonos) && count($telefonos) > 0 && isset($telefonos[0]->telefono))
                    <p>{{$telefonos[0]->telefono}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-2">
                <div class="col-md-12">
                    {{Form::label('', "15 .- Teléfono 2:", array( ))}}
                </div>
                @if(isset($telefonos) && count($telefonos) > 0  && isset($telefonos[1]->telefono))
                    <p>{{$telefonos[1]->telefono}}</p>
                @else
                    <p>Sin información</p>
                @endif
            </div>
            <div class="col-md-3">
                {{Form::label('', "16.- Correo Electrónico:", array('class' => 'col-md-12'))}}
                    @if(isset($infoPaciente) && $infoPaciente->correo != null)
                        <p class="lh20">{{ $infoPaciente->correo }}</p>
                    @else
                        <p class="lh20">Sin información</p>
                    @endif
            </div>
        </div>
    </div>
    <br>
    <legend>Datos Clinicos</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    {{Form::label('', "17.- Se deriba para atención en establecimiento:", array('class' => 'col-md-12'))}}
                    @if(isset($red_publica) && isset($red_publica->nombre) && $red_publica->nombre !== null && $red_publica->nombre !== '')
                        <p>{{$red_publica->nombre}}</p>
                    @elseif(isset($red_privada) && isset($red_privada->nombre) && $red_privada->nombre !== null && $red_privada->nombre !== '')
                        <p>{{$red_privada->nombre}}</p>
                    @else
                        <p>Sin información</p>
                    @endif
                </div>
                <div class="col-md-6">
                    {{Form::label('', "18.- Especialidad:", array('class' => 'col-md-12'))}}
                    @if(isset($especialidad_dirigido) && isset($especialidad_dirigido->nombre) && $especialidad_dirigido->nombre !== null && $especialidad_dirigido->nombre !== '')
                        <p>{{$especialidad_dirigido->nombre}}</p>
                    @else
                        <p>Sin información</p>
                    @endif 
                </div>
            </div>

            <div class="col-md-12">
                {{Form::label('', "19.- Confirmación Diagnóstico:", array('class' => 'col-md-12'))}}
                <div class="col-md-4">
                    @if(isset($interconsulta) && $interconsulta->tipo_diagnostico != null && $interconsulta->tipo_diagnostico === 'confirmacion Diagnóstica')
                    	<p>Confirmación Diagnóstica</p>
                    @elseif(isset($interconsulta) && $interconsulta->tipo_diagnostico != null && $interconsulta->tipo_diagnostico === 'paciente en tratamiento')
                    	<p>Paciente en tratamiento</p>
                    @elseif(isset($interconsulta) && $interconsulta->tipo_diagnostico != null && $interconsulta->tipo_diagnostico === 'realizar Tratamiento')
                    	<p>Realizar tratamiento</p>
                    @elseif(isset($interconsulta) && $interconsulta->tipo_diagnostico != null && $interconsulta->tipo_diagnostico === 'otro')
                    	<p>Otro</p>
                    @endif
                </div>
				@if(isset($interconsulta) && $interconsulta->tipo_diagnostico != null && $interconsulta->tipo_diagnostico === 'otro' && $interconsulta->tipo_diagnostico_otro != null)
					<div class="col-md-8">
						{{Form::label('', "Especifique:", array('class' => 'col-md-2'))}}
                        <div class="col-md-10">
                            <p class="lh20">{{$interconsulta->tipo_diagnostico_otro}}</p>
                        </div>
					</div>
				@endif
            </div>
            <div class="col-md-12">
                {{Form::label('', "20.- Hipótesis diagnóstica o diagnóstico:", array('class' => 'col-md-12'))}}
                @if(isset($diagnosticos) && count($diagnosticos) > 0)
                    <div class="col-md-12">
                        @foreach ($diagnosticos as $diagnostico)
                        <ul style="padding-left:15px;">
                            <li>
                            {{$diagnostico->diagnostico != null ? $diagnostico->diagnostico : ''}} {{$diagnostico->id_cie_10 != '' ? $diagnostico->id_cie_10 : ''}} {{$diagnostico->comentario != null ? ':' : ''}} {{$diagnostico->comentario != null ? $diagnostico->comentario : ''}}
                            </li>
                        </ul>
                        @endforeach
                    </div>
                @endif
            </div>
			<div class="col-md-6">
                <div class="col-md-12">
                    <div class="col-md-8">
                        {{Form::label('', "21.- ¿Sospecha problema de salud AUGE?:", array('class' => 'col-md-12'))}}
                    </div>
                    <div class="col-md-4">
                        @if(isset($interconsulta) && $interconsulta->problema_salud_auge != null && $interconsulta->problema_salud_auge)
                            <p>Si</p>
                        @else
                            <p>No</p>
                        @endif
                    </div>
                    @if(isset($interconsulta) && $interconsulta->problema_salud_auge != null && $interconsulta->problema_salud_auge)
                        <div class="col-md-12">
                            {{Form::label('', "Especificar:", array( ))}}
                            @if(isset($interconsulta) && $interconsulta->especificar_problema_salud_auge != null)
                                <p>{{$interconsulta->especificar_problema_salud_auge}}</p>
                            @else
                                <p>Sin información</p>
                            @endif
                        </div>
                    @endif
                </div>
			</div>
			<div class="col-md-6">
                <div class="col-md-12">
                    {{Form::label('', "22.- Subgrupo o subprograma de salud AUGE (si corresponde):", array('class' => 'col-md-12'))}}
                    @if(isset($interconsulta) && $interconsulta->sub_programa_salud_auge != null)
                        <p>{{$interconsulta->sub_programa_salud_auge}}</p>
                    @else
                        <p>Sin información</p>
                    @endif
                </div>
			</div>
	
			<div class="col-md-12">
				{{Form::label('', "23.- Fundamentos del diagnóstico:", array( ))}}
				@if(isset($interconsulta) && $interconsulta->fundamentos_diagnostico != null)
					<p>{{$interconsulta->fundamentos_diagnostico}}</p>
				@else
					<p>Sin información</p>
				@endif
			</div>
			<div class="col-md-12">
				{{Form::label('', "24.- Exámenes realizados:", array( ))}}
				@if(isset($interconsulta) && $interconsulta->examenes_realizados != null)
					<p>{{$interconsulta->examenes_realizados}}</p>
				@else
					<p>Sin información</p>
				@endif
			</div>
        </div>
    </div>
	<br>
    <legend>Datos de(la) profesional</legend>
    <hr>
    <div class="container-fluid">
        <div class="row">
			<div class="col-md-6">
				{{Form::label('', "25.- Nombre:", array( 'class' => 'col-md-12'))}}
                @if(isset($usuario))
                    @if( $usuario->apellido_paterno != null )
                        <div class="col-md-4">
                            <p>{{$usuario->apellido_paterno}}</p>
                        </div>
                    @endif 
                    @if( $usuario->apellido_materno != null )
                        <div class="col-md-4">
                            <p>{{$usuario->apellido_materno}}</p>
                        </div>
                    @endif
                    @if( $usuario->nombres != null )
                        <div class="col-md-4">
                            <p>{{$usuario->nombres}}</p>
                        </div>
                    @endif
                @else
                    <p>Sin información</p>
                @endif
			</div>
			<div class="col-md-12">
                {{Form::label('', "26.- RUT:", array( 'class' => 'col-md-12'))}}
                <div class="col-md-6">
                    @if(isset($usuario) && $usuario->rut_usuario != null  && $usuario->dv_usuario != null)
                        <p>{{$usuario->rut_usuario}}-{{$usuario->dv_usuario}}</p>
                    @else
                        <p>Sin información</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="col-md-4">
                        {{Form::label('', "Firma profesional", array( ))}} 
                    </div>
                    <div class="col-md-6">
                       _____________________________________________________
                    </div>
                </div>
			</div>
		</div>
	</div>
</body>
</html>