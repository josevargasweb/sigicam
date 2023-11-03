<div id="exTab1" style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li class="active"  id="1ab">
            <a  href="#1b" data-toggle="tab">Control signos vitales</a>
        </li>
		@if($sub_categoria == 4)
		<li id="13ab">
            <a  href="#13b" data-toggle="tab">Ventilación mecánica</a>
        </li>
		@endif
        <li id="2ab">
            <a href="#2b" data-toggle="tab">Balance ingresos-egresos</a>
        </li>
        <li id="4ab">
            <a href="#4b" data-toggle="tab">Examenes laboratorio</a>
        </li>
        <li id="5ab">
            <a href="#5b" data-toggle="tab">Examenes imagenes</a>
        </li>
        <li id="6ab">
            <a href="#6b" data-toggle="tab">Control días estada y otros</a>
        </li>
        <li id="9ab">
            <a href="#9b" data-toggle="tab">Registro de Enfermería</a>
        </li>
{{--
        <li id="11ab">
            <a href="#11b" data-toggle="tab">Riesgo caida</a>
        </li>
        --}}
        <li id="12ab">
            <a href="#12b" data-toggle="tab">Gestión de interconsultas</a>
        </li>
    </ul>

    <div class="tab-content clearfix" id="tabsRegistroDiarioCuidados">
        <br>
        <div class="tab-pane active" id="1b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.signosVitales')
        </div>
		@if($sub_categoria == 4)
		<div class="tab-pane" id="13b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.ventilacionMecanica')
        </div>
		@endif
        <div class="tab-pane" id="2b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.balanceIngresosEgresos')
        </div>
        <div class="tab-pane" id="4b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.examenLaboratorio')
        </div>
        <div class="tab-pane" id="5b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.examenImagen')
        </div>
        <div class="tab-pane" id="6b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.controlEstada')
        </div>
        <div class="tab-pane" id="9b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.registroEnfermeria')
        </div>
        {{--
        <div class="tab-pane" id="11b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.riesgoCaida')
        </div>
        --}}
        <div class="tab-pane" id="12b">
            @include('Gestion.gestionEnfermeria.partesHojaEnfermeria.interconsulta')
        </div>
    </div>

</div>


{{-- {{ HTML::link("gestionEnfermeria/$caso/histHojaEnfemeria", 'Ver Historial', ['class' => 'btn btn-default']) }} --}}



{{-- <div class="">
    @include("Gestion.gestionEnfermeria.hojaEnfermeriaForm")
</div> --}}
