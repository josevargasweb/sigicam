<style>
    textarea { resize: none; }
</style>
<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li id="idIndicacion" class="active" >
            <a  href="#tabIndicacion" data-toggle="tab" id="tabIndicacionx">Indicaciones</a>
        </li>
        <li id="idHistoralIndicacion">
            <a href="#tabHistorialIndicacion" data-toggle="tab" id="tabHistorialIndicacionx">Historial</a>
        </li>
        <li id="idPdfResumenIndicaciones">
            <a href="#tabPdfIndicaciones" data-toggle="tab" id="tabPdfIndicacionesx">PDF Historial</a>
        </li>
    </ul>

    <div class="tab-content clearfix" id="tabsIndicacionesMedicas">
        <br>
        <div class="tab-pane active" id="tabIndicacion">
            @include('Gestion.gestionMedica.Indicaciones')
        </div>
        <div class="tab-pane" id="tabHistorialIndicacion">
            @include('Gestion.gestionMedica.HistorialIndicaciones')
        </div>
        <div class="tab-pane" id="tabPdfIndicaciones">
            @include('Gestion.gestionMedica.ResumenIndicaciones')
        </div>
    </div>
</div>

