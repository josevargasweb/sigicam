@if(Session::get('usuario')->tipo != 'director' 
&& Session::get('usuario')->tipo != 'medico_jefe_servicio'
)
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    Opciones
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">
    @if(Session::get('usuario')->tipo == 'gestion_clinica' || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS || Session::get('usuario')->tipo == 'matrona_neonatologia')
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick='ingresar({{$idCaso}}, {{$idLista}}, {{$idCama}})'>Hospitalizar paciente</a></li>
    @endif
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="darAltaTransito({{$idCaso}}, {{$idLista}}, '{{$ficha}}', '{{$nombreCompleto}}', {{$idCama}}, '{{$sexo}}')">Egreso</a></li>
    @if(Session::get('usuario')->tipo != 'medico_jefe_servicio')
      <!-- <li role="presentation"><a href='{{URL::to('/')}}/paciente/editar/{{$idPaciente}}'>Editar paciente</a></li> -->
      <li role="presentation"><a role="menuitem" tabindex="-1" class="cursor" onclick="editarPaciente({{$idPaciente}},{{$idCaso}})">Editar paciente</a></li>
      <!-- <li role="presentation"><a href='{{URL::to('/')}}/regresarEspera/{{$idCaso}}'>Regresar a lista espera</a></li> -->
      <li role="presentation"><a role="menuitem" tabindex="-1" class="cursor" onclick="regresarListaEspera({{$idCaso}})">Regresar a lista espera</a></li>
      <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="editarCama({{$idCaso}}, {{$idLista}}, '{{$nombreCompleto}}')">Editar cama</a></li>
      @if(Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::USUARIO)
      @endif
    @endif
  </ul>
</div>
@endif