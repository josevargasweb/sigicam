<div class="row">
	<div class="col-md-2">
	</div>
	<div class="col-md-10">
		<div class="row">
			<div class="col-md-6">
				<h1>Gestión de Camas</h1>
			</div>
			<div class="col-md-3">
				<h3 id="nombreEstab">@if(Session::has("nombreEstablecimiento")) {{ Session::get("nombreEstablecimiento") }} @endif</h3>
			</div>
		</div>
		<div class="row">
			@yield("miga")
		</div>
	</div>



</div>
