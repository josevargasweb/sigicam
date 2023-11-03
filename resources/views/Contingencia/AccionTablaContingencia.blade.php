<div class="dropdown">
	<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
	Acciones
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		<li role="presentation">{{ HTML::link(URL::route("verPDF", $id), 'Ver informe', array('target' => '_blank', 'role' => 'menuitem', 'tabindex' => '-1'))}}</li>
		<li role="presentation">{{ HTML::link(URL::route("descargarPDF", $id), 'Descargar informe', array('role' => 'menuitem', 'tabindex' => '-1'))}}</li>
		<li role="presentation">{{ HTML::link(URL::route("verContingencia", $id), 'Ver contingencia', array('role' => 'menuitem', 'tabindex' => '-1'))}}</li>
		@if(Session::get("usuario")->tipo === TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::ADMIN)
			<li role="presentation"><a onclick="anularContingencia({{$id}});" class="cursor" role="menuitem" tabindex="-1">Anular contengencia</a></li>
		@endif
	</ul>
</div>