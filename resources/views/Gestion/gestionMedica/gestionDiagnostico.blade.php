<style>
    textarea { resize: none; }
</style>
<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li id="hAx" class="active" >
            <a  href="#1hx" data-toggle="tab">Diagnósticos</a>
        </li>
        <li id="hRx">
            <a href="#2hx" data-toggle="tab">Historial</a>
        </li>
    </ul>

    <div class="tab-content clearfix">
        <br>
        <div class="tab-pane active" id="1hx">
            @include('Gestion.gestionMedica.diagnosticos.agregarDiagnosticos')
        </div>
        <div class="tab-pane" id="2hx">
            @include('Gestion.gestionMedica.diagnosticos.historialDiagnosticos')
        </div>
    </div>
</div>

