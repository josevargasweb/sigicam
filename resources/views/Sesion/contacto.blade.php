@extends("Sesion/templateIndex")

@section("titulo")
Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público
@stop

@section("contenido")

<div class="col-sm-12 cont-contacto">
    <div class="col-sm-12 cont-contacto-encabezado">
        <div class="cont-contacto-titulo">Contáctanos</div>
        <hr class="cont-contacto-barra">
        <div class="cont-contacto-instrucciones">Estamos para ayudarte. Si tienes preguntas simplemente llámanos, escríbenos o usa el formulario de contacto a continuación.</div>
    </div>
    <div class="col-sm-7 sin-pad-lateral cont-contacto-izq">
        {{ Form::open(array('url' => 'enviarCorreoContacto', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formCorreoContacto')) }}                    
                       
            <div class="error form-group">
                <input id="nombreContacto" name="nombreContacto" class="form-control" type="text" placeholder="Nombre *"/>
            </div>
            <div class="error form-group">
                <input id="correoContacto" name="correoContacto" class="form-control" type="text" placeholder="Correo electrónico *"/>
            </div>
            <div class="error form-group">
                <input id="asuntoContacto" name="asuntoContacto" class="form-control" type="text" placeholder="Asunto"/>
            </div>
            <div class="error form-group">
                <textarea id="comentarioContacto" name="comentarioContacto" class="form-control" placeholder="Mensaje *" rows="4"></textarea>
            </div>
            <div class="cont-contacto-botones">                       
                <button type="submit" class="btn">ENVIAR MENSAJE</button>
                <button type="reset" class="btn">CANCELAR</button>
            </div> 
        
        {{ Form::close() }} 
    </div>
    <div class="col-sm-5 cont-contacto-der">
        <span class="tabla-contacto-titulo col-sm-12 sin-pad-lateral">Dirección</span>
            
        <div class="tabla-contacto col-sm-12 sin-pad-lateral">           
            <div class="col-sm-12 sin-pad-lateral">
                <div class="col-xs-1 sin-pad-lateral" class="iconos-td-contacto"><img src="{{URL::to('/')}}/img/direccion.png" class="iconos-contacto"></div>
                <div class="col-xs-11">General cruz<br> Número 222<br>Región de Valparaíso</div> 
            </div>
            <div class="col-sm-12 sin-pad-lateral">
                <div class="col-xs-1 sin-pad-lateral" class="iconos-td-contacto"><img src="{{URL::to('/')}}/img/fono.png" class="iconos-contacto"></div>
                <div class="col-xs-11">+56322603643</div> 
            </div>
            <div class="col-sm-12 sin-pad-lateral">
                <div class="col-xs-1 sin-pad-lateral" class="iconos-td-contacto"><img src="{{URL::to('/')}}/img/mail.png" class="iconos-contacto"></div>
                <div class="col-xs-11 resaltado-verde">sigicam@uv.cl</div> 
            </div>        
        </div>
    </div>

</div>

@stop

