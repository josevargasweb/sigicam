

<div class="dropdown">
  @if(Session::get("usuario")->tipo != TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="{{$color}}">
    Opciones 
    <span class="caret"></span>
  </button>
  @endif
  <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">
    @if(Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::USUARIO)

   
      @if(App\Models\Usuario::horarioInhabil())
        {{-- Es horario inhabil y pueden soliicitar cama durante todo el día --}}
        <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick='ingresar({{$idCaso}}, {{$idLista}})'>Asignar cama</a></li>
  
      @else
        {{-- En horario habil, solo pueden realizar solicitudes de cama, desde las  17:01 a 7:59--}}
        @if( App\Models\Usuario::horaInhabil() < 8 || App\Models\Usuario::horaInhabil() >= 17) 
            <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick='ingresar({{$idCaso}}, {{$idLista}})'>Asignar cama</a></li>
          
        @else
          {{-- <p>Solo se puede realizar asignación de cama de Lunes a Viernes desde las <b>17:01 a 7:59</b> o <b>feriados</b> todo el día </p> --}}
        @endif
  
      @endif
  
    @elseif(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER  || Session::get("usuario")->tipo === TipoUsuario::MASTERSS || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get('usuario')->tipo == 'matrona_neonatologia')
      <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="ingresar({{$idCaso}}, {{$idLista}}, '{{$nombreCompleto}}')">Asignar cama</a></li>
    @endif
    {{-- <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick='ingresar({{$idCaso}}, {{$idLista}})'>Asignar cama</a></li> --}}
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="darAlta({{$idCaso}}, {{$idLista}}, '{{$ficha}}', '{{$nombreCompleto}}', '{{$sexo}}')">Egreso</a></li>
    {{-- <li role="presentation"><a href='{{URL::to('/')}}/paciente/editar/{{$idPaciente}}'>Editar paciente</a></li> --}}
    <li role="presentation"><a role="menuitem" tabindex="-1" class="cursor" onclick="editarPaciente({{$idPaciente}},{{$idCaso}})">Editar paciente</a></li>
    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="verDiagnosticos({{$idCaso}}, '{{$nombreCompleto}}')">Editar diagnostico</a></li>
    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="agregarComentario({{$idLista}}, '{{$nombreCompleto}}')">Agregar comentario</a></li>
    <li role="presentation"><a class='cursor' onclick='cambiarUnidad({{$idCaso}})'>Editar unidad</a></li>
    {{-- <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="agregarUbicacion({{$idLista}}, '{{$nombreCompleto}}', '{{$ubicacion}}')">Agregar ubicación</a></li> --}}
  </ul>
</div>

<script>
    $( document ).ready(function() {
      @if(Auth::user()->tipo == "gestion_clinica" || Auth::user()->tipo == "enfermeraP" || Auth::user()->tipo == "usuario")
        console.log("ud. es enfermera o urgencia");
      @endif
    });
    /* console.log("hoadasdasdasdla"); */
</script>