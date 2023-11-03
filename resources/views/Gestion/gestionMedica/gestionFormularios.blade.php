<style>
    textarea { resize: none; }
</style>
<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li id="hA" class="active" >
            <a  href="#1for" data-toggle="tab">Notificación GES</a>
        </li>
        <li id="hipd">
            <a href="#2for" data-toggle="tab">IPD</a>
        </li>
        <li id="hca">
            <a href="#3for" data-toggle="tab">Uso Restringido</a>
        </li>
    </ul>

    <div class="tab-content clearfix">
        <br>
        <div class="tab-pane active" id="1for">
            @include('Gestion.gestionMedica.partesFormularioMedicos.notificacionGes')
        </div>
        <div class="tab-pane" id="2for">
            @include('Gestion.gestionMedica.partesFormularioMedicos.informeProcesoDiagnostico')
        </div>
        <div class="tab-pane" id="3for">
            @include('Gestion.gestionMedica.partesFormularioMedicos.usoRestringido')
        </div>
    </div>
</div>


        