

<div class="row">
	<div id="exTab1" class="container" >	
		<ul  class="nav nav-pills segundoNav">
			<li class="nav in active" id="PC1"><a href="#reporteD2yD3" data-toggle="tab">Pacientes categorizados D2 y D3</a></li>
			<li class="nav" id="PC2"><a href="#reporteCategorizados" data-toggle="tab">Pacientes categorizados</a></li>
			
		</ul>
		<div class="tab-content clearfix">
			
			<div class="tab-pane pane  in active" style="padding-top:10px;" id="reporteD2yD3">
				@include('Estadisticas.reporteCategorizacion.pacientesCategorizados.pacientesD2yD3')				
			</div>

			<div class="tab-pane pane" style="padding-top:10px;" id="reporteCategorizados">
				@include('Estadisticas.reporteCategorizacion.pacientesCategorizados.reporteCategorizacion')				
			</div>
		</div>
	</div>
</div>

