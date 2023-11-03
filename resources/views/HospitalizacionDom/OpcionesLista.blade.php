<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    Opciones
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">
  <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="reingresarListaEspera({{$idCaso}}, '{{$nombrePaciente}}', '{{$rutPaciente}}')">Rehospitalizar</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="darAlta({{$idCaso}}, {{$idLista}}, '{{$sexo}}')">Egreso</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick='comentario({{$idCaso}}, {{$idLista}})'>Registro de actividades</a></li>
    <li role="presentation"><a href='{{URL::to('/')}}/paciente/editar/{{$idPaciente}}'>Editar paciente</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="verDiagnosticos({{$idCaso}}, '{{$nombreCompleto}}')">Ver / Editar diagnósticos</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" class='cursor' onclick="verRiesgos({{$idCaso}})">Ver / Editar Categorización</a></li>
  </ul>
</div>
