<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head></head>
<body>

	<div class="row">
		<h4>SIGICAM - DIAS SIN CATEGORIZAR</h4>	
    </div>
    
	<div class="row">
        <table>
            <thead>
                {{-- <tr>
                    <td>
                        <b>Lista de Espera</b>
                    </td>
                </tr> --}}
                <tr>
                    <td>
                        <b>Fecha : {{\Carbon\Carbon::now()->format('d-m-Y')}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Establecimiento: {{ $establecimiento->nombre }}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Nombre: {{$paciente->nombre}} {{$paciente->apellido_paterno}} {{$paciente->apellido_materno}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Rut: {{$paciente->rut}}-{{$paciente->dv}}</b>
                    </td>
                </tr>
            </thead>
        </table>
    </div>

    <div class="row">
        <table id="as">
            <thead>
                <tr>
                    <th>Indice</th>
                    <th>Fecha sin Categorizar</th>
                </tr>   
            </thead>
            <tbody>
                @foreach($datos as $key => $nocat)
                    <tr>
                        <td style="text-align: left">{{$key+1}}</td>
                        <td>{{Carbon\Carbon::parse($nocat->date_trunc)->format('d/m/Y')}}</td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>