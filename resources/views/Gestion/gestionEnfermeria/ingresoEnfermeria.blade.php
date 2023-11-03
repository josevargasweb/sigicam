<style>
    textarea { resize: none; }
</style>
<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li id="hA" class="active" >
            <a  href="#1h" data-toggle="tab">Anamnesis</a>
        </li>
        <li id="hG">
            <a href="#2h" data-toggle="tab">Examen Físico General</a>
        </li>
        <li id="hS">
            <a href="#3h" data-toggle="tab">Examen Físico Segmentario</a>
        </li>
		@if($sub_categoria == 1)
		<li id="hGO">
            <a href="#6h" data-toggle="tab">Examen Ginecoobstétrico</a>
        </li>
		@endif
        <li id="hO">
            <a href="#4h" data-toggle="tab">Otros</a>
        </li>
        <li id="hR">
            <a href="#5h" data-toggle="tab">Resumen</a>
        </li>
    </ul>

    <div class="tab-content clearfix" id="tabsIngresoEnfermeria">
        <br>
        {{-- {{ HTML::link("gestionEnfermeria/$caso/obtenerHistorialIngresoEnfermeria", 'Historico', ['class' => 'btn btn-danger']) }}
        {{ HTML::link(URL::route('excelListaDerivados'), 'Historico', ['class' => 'btn btn-danger']) }}  --}}
        <div class="tab-pane active" id="1h">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.anamnesis')
        </div>
        <div class="tab-pane" id="2h">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.examenFisicoGeneral')
        </div>
        <div class="tab-pane" id="3h">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.examenFisicoSegmentario')
        </div>
		@if($sub_categoria == 1)
		<div class="tab-pane" id="6h">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.examenGinecoobstetrico')
        </div>
		@endif
        <div class="tab-pane" id="4h"> 
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.otros')
        </div>
        <div class="tab-pane" id="5h">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.resumen')
        </div>
    </div>
</div>

{{-- <legend class="text-center" id="legendIngresoEnfermeria"><u>Hoja de Ingreso de Enfermería</u></legend> --}}
