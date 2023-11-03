<script>
</script>

<div style="padding-left: 0px;">
    <ul  class="nav nav-pills  segundoNav">
        <li id="pAt" class="active" >
            <a  href="#1p" data-toggle="tab">Planificación de atención</a>
        </li>
        <li id="pN">
            <a href="#2p" data-toggle="tab">Novedades</a>
        </li>
        <li id="rP">
            <a href="#5p" data-toggle="tab">Resumen</a>
        </li>
    </ul>

    <div class="tab-content clearfix" id="tabsPlanificacionCuidados">
      <br>
        <div class="col-sm-12" id="curacionesSimples" style="margin-left: -1%;"></div>
        <br><br>
        <div class="tab-pane active" id="1p">
            @include('Gestion.gestionEnfermeria.partesPlanificacionCuidados.planificacion')
        </div>
        <div class="tab-pane" id="2p">
            @include('Gestion.gestionEnfermeria.partesPlanificacionCuidados.novedades')
        </div>
        <div class="tab-pane" id="5p">
            @include('Gestion.gestionEnfermeria.partesPlanificacionCuidados.resumen')
        </div>
    </div>

</div>
