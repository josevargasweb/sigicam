<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li class="active"  id="1hc">
            <a  href="#1ch" data-toggle="tab">Valoración de Herida</a>
        </li>
        <li id="3hc">
            <a  href="#3ch" data-toggle="tab">Curaciones</a>
        </li>
        <li id="2hc">
            <a href="#2ch" data-toggle="tab">Busqueda</a>
        </li>

    </ul>

    <div class="tab-content clearfix" id="tabsHojaCuraciones">
        <br>
        <div class="tab-pane active" id="1ch">
            @include('Gestion.gestionEnfermeria.partesHojaCuraciones.valoracionHerida')
        </div>
        <div class="tab-pane" id="3ch">
            @include('Gestion.gestionEnfermeria.partesHojaCuraciones.curacionSimple')
        </div>
        <div class="tab-pane" id="2ch">
            @include('Gestion.gestionEnfermeria.partesHojaCuraciones.busquedaCuracion')
        </div>
    </div>

</div>
