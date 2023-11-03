@if(Session::get('usuario')->tipo != 'director' 
&& Session::get('usuario')->tipo != 'medico_jefe_servicio'
)
  <a type="button" class="btn btn-success dropdown-toggle" onclick="darAltaPreAlta({{$idCaso}}, {{$idPreAlta}}, '{{$ficha}}', '{{$nombreCompleto}}', {{$idCama}}, '{{$sexo}}')">Egresar</a>
@endif