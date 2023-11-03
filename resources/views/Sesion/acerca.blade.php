@extends("Sesion/templateIndex")

@section("titulo")
Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público
@stop

@section("contenido")

<div class="col-sm-12 cont-acerca1">
    <div class="row sin-mar-lateral">
        <div class="col-sm-6 cont-acerca1-izq">
            <span class="cont-acerca1-titulo">ACERCA DE</span>
            <span class="cont-acerca1-desc">El Sistema permite disminuir los tiempos de espera en los hospitales, a través de la administración inteligente y sistematizada de los procesos de asignación - reconversión de camas y traslado de pacientes, utilizando técnicas de investigación de operaciones, que permiten detectar restricciones y variables críticas que inciden en la gestión, funcionando como una herramienta de soporte a la toma decisiones.</span>
        </div>
        <div class="col-sm-6 cont-acerca1-der"></div>
    </div>
</div>
{{-- <div class="col-sm-6 cont-acerca2-izq">    
    <div class="col-xs-6">
        <img class="cont-acerca2-img" src="{{URL::to('/')}}/img/camaverde.png" alt="Cama libre" title="Cama libre"> 
        <span class="cont-acerca2-titulo">Cama libre</span>
        <span class="cont-acerca2-desc">Indica que la cama se encuentra disponible</span>
    </div>
    <div class="col-xs-6">
        <img class="cont-acerca2-img" src="{{URL::to('/')}}/img/camaroja.png" alt="Cama ocupada" title="Cama ocupada"> 
        <span class="cont-acerca2-titulo">Cama ocupada</span>
        <span class="cont-acerca2-desc">Indica que la cama está asignada a un paciente</span>
    </div>
</div>
<div class="col-sm-6 cont-acerca2-der">   
    <div class="col-xs-6">
        <img class="cont-acerca2-img" src="{{URL::to('/')}}/img/camaazul.png" alt="Cama reconvertida" title="Cama reconvertida"> 
        <span class="cont-acerca2-titulo">Cama reconvertida</span>
        <span class="cont-acerca2-desc">Indica que la cama pertenece temporalmente a otra especialidad</span>
    </div>
    <div class="col-xs-6">
        <img class="cont-acerca2-img" src="{{URL::to('/')}}/img/camanegra.png" alt="Cama ocupada" title="Cama ocupada"> 
        <span class="cont-acerca2-titulo">Cama ocupada</span>
        <span class="cont-acerca2-desc">Indica que la cama no puede ser utilizada</span>
    </div>
</div> --}}

@stop