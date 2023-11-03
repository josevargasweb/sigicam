<style>
    textarea { resize: none; }
</style>
<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li id="idExamenImagen" class="active" >
            <a  href="#tabExamenImagen" data-toggle="tab">Solicitud Examen Imageneología</a>
        </li>
        <li id="idExamenLaborario">
            <a  href="#tabExamenLab" data-toggle="tab">Examenes de laboratorio</a>
        </li>
		<li id="idElectroNeuro" >
            <a  href="#tabElectroNeuro" data-toggle="tab">Solicitud Examen Electro y Neuro</a>
        </li>
		<li id="idElectroencefalograma" >
            <a  href="#tabElectroencefalograma" data-toggle="tab">Solicitud Electroencefalograma</a>
        </li>
		<li id="idSolicitudExamen" >
            <a  href="#tabSolicitudExamen" data-toggle="tab">Otros exámenes de imágenes</a>
        </li>
        {{-- <li id="idHistoralExamen">
            <a href="#tabHistorialExamen" data-toggle="tab">Historial</a>
        </li> --}}
    </ul>

    <div class="tab-content clearfix" id="tabsGestionExamenesMedicos">
        <br>
        <div class="tab-pane active" id="tabExamenImagen">
            @include('Gestion.gestionMedica.Examenes')
        </div>
        <div class="tab-pane" id="tabExamenLab">
            @include('Gestion.gestionMedica.ExamenesLaboratorio')
        </div>
		<div class="tab-pane" id="tabElectroNeuro">
            @include('Gestion.gestionMedica.ElectroNeuro')
        </div>
		<div class="tab-pane" id="tabElectroencefalograma">
            @include('Gestion.gestionMedica.Electroencefalograma')
        </div>
		<div class="tab-pane" id="tabSolicitudExamen">
            @include('Gestion.gestionMedica.SolicitudExamen')
        </div>
    </div>
</div>

