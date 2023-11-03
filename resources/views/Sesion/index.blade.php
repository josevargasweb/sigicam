@extends("Sesion/templateIndex")

@section("titulo")
Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público
@stop

@section("contenido")
<div class="col-sm-12 sin-pad-lateral">
    <div class="col-sm-12 cont1">
      <div class="col-sm-6">
        <div class="cont1-texto">
          <div class="cont1-texto-titulo"><b>Sig</b>icam</div>
          <span style="font-size:15px;">Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público [Código ID16I10449]</span>
        </div>
      </div> 
    </div>
    <div class="col-sm-12 cont2">
      <div class="col-sm-5 col-xs-7 cont2-desc">
        <div class="cont2-texto">
          El Sistema permite disminuir los tiempos de espera en los hospitales, a través de la administración inteligente y sistematizada de los procesos de asignación - reconversión de camas y traslado de pacientes, utilizando técnicas de investigación de operaciones, que permiten detectar restricciones y variables críticas que inciden en la gestión, funcionando como una herramienta de soporte a la toma decisiones.
        </div>
      </div> 
    </div> 
    <div class="col-sm-12 cont2-foto">
      <div class="cont2-imagen col-sm-offset-5 col-sm-7">
        <img src="{{URL::to('/')}}/img/segicaminfografia.png" alt="Infografía" title="Infografía"> 
      </div> 
    </div>
  </div>
 
  <div class="col-sm-12" style="display:none;">
    <div class="col-sm-12 cont3">
      <div class="cont1-texto-titulo cont3-titulo"><b>Noticias</b></div>
    </div>
    <div class="col-sm-12">
      <div class="jcarousel-wrapper">
        <div class="jcarousel2" id="fotos-patrimonios">
          <div class="loading">Cargando...</div>
        </div>
  
        <a href="#" class="jcarousel2-control-prev">&lsaquo;</a>
        <a href="#" class="jcarousel2-control-next">&rsaquo;</a>
      </div>
    </div>
  </div>  

  <div class="col-sm-12 cont4">
    <div class="col-sm-4">
        <div class="cont4-img-acerca">
        <img class="cont4-img" src="{{URL::to('/')}}/img/acercade.png" alt="Acerca de" title="Acerca de"> 
        <span class="cont4-titulo">ACERCA DE</span>
        <span class="cont4-desc">SIGICAM FONDEF IDeA en Dos etapas 2016</span>
        <a class="cont4-link" href="{{URL::to('/')}}/acerca">Ver más ></a>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="cont4-img-equipo">
        <img class="cont4-img" src="{{URL::to('/')}}/img/equipo.png" alt="Equipo" title="Equipo"> 
        <span class="cont4-titulo">EQUIPO</span>
        <span class="cont4-desc">Labitec centro tecnológico I+D+i </span>
        <a class="cont4-link" href="{{URL::to('/')}}/equipo">Ver más ></a>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="cont4-img-contacto">
        <img class="cont4-img" src="{{URL::to('/')}}/img/contacto.png" alt="Contacto" title="Contacto"> 
        <span class="cont4-titulo">CONTACTO</span>
        <span class="cont4-desc">Escríbenos tus dudas</span>
        <a class="cont4-link" href="{{URL::to('/')}}/contacto">Ver más ></a>
        </div>
    </div>
  </div>
@stop