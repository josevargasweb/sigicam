@if(Session::get('usuario')->tipo != 'director' && Session::get('usuario')->tipo != 'medico_jefe_servicio')
<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    Opciones
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">
    <!-- inicio opcion recuperacion --> 
    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="quitarDerivado({{$idCaso}})">Sacar de derivados</a></li> 
    <!-- fin opcion recuperacion -->
    <!-- editar comentario -->
    <li role="presentation"><a role='menuitem' tabindex="-1" class='cursor' onclick="obtenerListaComentariosDerivado({{$idCaso}})">Bitacora de  Comentarios</a></li> 
  </ul>
</div>
@endif