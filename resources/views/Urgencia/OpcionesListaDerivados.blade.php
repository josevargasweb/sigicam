@if(Session::get('usuario')->tipo != 'director' && Session::get('usuario')->tipo != 'medico_jefe_servicio')
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    Opciones
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">
    @if(Session::get('usuario')->tipo != TipoUsuario::ESTADISTICAS)
    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="quitarDerivado({{$idLista}})">Derivación Realizada</a></li>
    @endif

    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="formularioDerivacion({{$idCaso}},{{$idLista}})">Información Formulario Derivación</a></li>
    
    @if(Session::get('usuario')->tipo != TipoUsuario::ESTADISTICAS)
    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="obtenerListaComentariosDerivado({{$idCaso}},{{$idLista}})">Bitacora de  Comentarios</a></li>
    @endif
    @if(Session::get('usuario')->tipo != TipoUsuario::ESTADISTICAS)
    <li role="presentation"><a class='cursor' onclick='documentosDerivacion({{$idCaso}})'>Documentos</a></li>
    @endif
  </ul>
</div>
@endif
