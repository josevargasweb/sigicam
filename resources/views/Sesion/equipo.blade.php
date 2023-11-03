@extends("Sesion/templateIndex")

@section("titulo")
Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público
@stop

@section("contenido")

<div class="col-sm-12 cont-equipo">
    <div class="cont-equipo-titulo">Equipo</div>
    <div class="col-sm-8">
        <div class="col-sm-12 cont-equipo-individual">
            <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_carla.jpg" alt="CARLA TARAMASCO - Directora de proyecto" title="CARLA TARAMASCO - Directora de proyecto"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">CARLA TARAMASCO</span>
                    <span class="cont-equipo-desc-cargo">Directora de proyecto</span>
                    <span class="cont-equipo-desc-detalle">Académica e investigadora Escuela Ingeniería Civil Informática (Universidad de Valparaíso). PhD Sistemas Complejos (École polytechnique, París) Master en Ciencias Cognitivas (École Normale Superieure, París). Ingeniera Informática (Universidad de Valparaíso).</span>
                </div>
            </div>
        </div>
        <div class="col-sm-12 cont-equipo-individual">
            <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_rodrigo.jpg" alt="RODRIGO OLIVARES - Director alterno" title="RODRIGO OLIVARES - Director alterno"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">RODRIGO OLIVARES</span>
                    <span class="cont-equipo-desc-cargo">Director alterno</span>
                    <span class="cont-equipo-desc-detalle">Académico e investigador de la Escuela de Ingeniería Civil Informática de la Universidad de Valparaíso. Magister en Ciencias de la Ingeniería Informática y actualmente curso el programa de Doctorado en Ingeniería Informática, en la Pontificia Universidad Católica de Valparaíso. Mi título profesional es Ingeniero en Informática Aplicada, de la Universidad de Valparaíso.</span>
                </div>
            </div>
        </div>

        <div class="col-sm-12 cont-equipo-individual">
            <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_HernanAstudillo.jpg" alt="HERNÁN ASTUDILLO - Investigador" title="HERNÁN ASTUDILLO - Investigador"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">HERNÁN ASTUDILLO</span>
                    <span class="cont-equipo-desc-cargo">Investigador</span>
                    <span class="cont-equipo-desc-detalle">Ph. D. Information and Computer Science. M.Sc. Information and Computer Science. Ingeniero Civil en Informática, UTFSM (Universidad Técnica Federico Santa María).</span>
                </div>
            </div>
        </div>

        {{-- <div class="col-sm-12 cont-equipo-individual">
            <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_nicole.jpg" alt="NICOLE BARBEHITO - Desarrollador" title="NICOLE BARBEHITO - Desarrollador"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">NICOLE BARBEHITO</span>
                    <span class="cont-equipo-desc-cargo">Desarrollador</span>
                    <span class="cont-equipo-desc-detalle">Ingeniera en Informática, UV (Universidad de Valparaíso).</span>
                </div>
            </div>
        </div> --}}

        <div class="col-sm-12 cont-equipo-individual">
            <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_paola.jpg" alt="PAOLA VIEYTES - Coordinadora local" title="PAOLA VIEYTES - Coordinadora local"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">PAOLA VIEYTES </span>
                    <span class="cont-equipo-desc-cargo">Coordinadora local</span>
                    <span class="cont-equipo-desc-detalle">Tecnologo Medico, Magister Salud Pública y Gestión.</span>
                </div>
            </div>
        </div>


        <div class="col-sm-12 cont-equipo-individual">
                <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_kathy_cuad.jpg" alt="KATHERINE FIGUEROA - Desarrollador" title="KATHERINE FIGUEROA - Desarrollador"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">KATHERINE FIGUEROA</span>
                    <span class="cont-equipo-desc-cargo">Desarrollador</span>
                    <span class="cont-equipo-desc-detalle">Ingeniera Civil en Informática, UV (Universidad de Valparaíso).</span>
                </div>
                </div>
        </div>

        <div class="col-sm-12 cont-equipo-individual">
                <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/foto_matias.jpg" alt="MATÍAS VILLAR - Desarrollador" title="MATÍAS VILLAR - Desarrollador"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">MATÍAS VILLAR</span>
                    <span class="cont-equipo-desc-cargo">Desarrollador</span>
                    <span class="cont-equipo-desc-detalle">Ingeniero de Ejecución en Informática, PUCV (Pontificia Universidad Católica de Valparaíso).</span>
                </div>
                </div>
        </div>

        <div class="col-sm-12 cont-equipo-individual">
                <div class="cont-equipo-particular">
                <img class="cont-equipo-img" src="{{URL::to('/')}}/img/equipo-miembro.png" alt="EDUARDO ARELLANO - Desarrollador" title="EDUARDO ARELLANO - Desarrollador"> 
                <div class="cont-equipo-desc">
                    <span class="cont-equipo-desc-titulo">EDUARDO ARELLANO</span>
                    <span class="cont-equipo-desc-cargo">Desarrollador</span>
                    <span class="cont-equipo-desc-detalle"></span>
                </div>
                </div>
        </div>
    </div>
    <div class="col-sm-4">
        <img class="cont-equipo-infografia" src="{{URL::to('/')}}/img/equipodetrabajoalolargo.png">                 
    </div>

</div>

@stop
