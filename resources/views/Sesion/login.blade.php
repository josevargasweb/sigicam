<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="{{ URL::asset('favicon.ico') }}" type="image/x-icon" rel="shortcut icon"/>
    {{HTML::style('css/bootstrap.css') }}
    {{HTML::style('css/estilos.css') }}
    {{ HTML::style('css/bootstrapValidator.min.css') }}
    {{HTML::script('js/jquery-1.11.1.min.js') }}
    {{HTML::script('js/bootstrap.js') }}
    {{ HTML::script('js/bootstrapValidator.min.js') }}
    {{ HTML::script('js/language/es_ES.js') }}
    {{ HTML::script('js/funciones.js') }}

    {{ HTML::style('css/estiloLogin.css') }}
    {{ HTML::style('css/jquerysctipttop.css') }}

    <title>Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público</title>
    <script>
        $(function () {
            
            var BASE = "{{ URL::to('/') }}";

            $("#frmLogin").bootstrapValidator({
                fields: {
                    rut: {
                        validators: {
                            notEmpty: {
                                message: 'El run es obligatorio'
                            },
                            callback: {
                                callback: function (value, validator, $field) {
                                    if (value === '') {
                                        return true;
                                    }
                                    var rut = value.substring(0, value.length - 1);
                                    var dv = value[value.length - 1];
                                    var valido = esRutValido(rut, dv);
                                    if (!valido) {
                                        return {
                                            valid: false,
                                            message: 'Run no coincide con dígito verificador'
                                        }
                                    }
                                    return true;
                                }
                            }
                        }
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: 'La contraseña es obligatoria'
                            }
                        }
                    } 
                }
            }).on("success.form.bv", function (evt) {
              console.log($('meta[name="csrf-token"]').attr('content'));
                evt.preventDefault(evt);
                var $form = $(evt.target);
                $.ajax({
                    url: BASE + "/doLogin",
                    headers: {        
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $form.serialize(),
                    dataType: "json",
                    type: "post",
                    success: function (data) {
                        $("#ingresar").prop("disabled", false);
                        if (data.error) {
                            $("#msgLogin strong").text(data.error);
                            $("#msgLogin").show();
                        }
                        else location.href = data.href;
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            })
        });
    </script>

    <meta name="csrf-token" content="{{{ Session::token() }}}">
</head>
<body>
  
  <header class="contenedor-navegacion col-sm-12">
    <div class="contenedor-menu col-xs-12">
      <div class="col-xs-4 nombre-proyecto">
        <a href=""><b>SIGI</b>CAM</a>
      </div>

      <nav class="navbar navbar-inverse col-xs-8 menu-opciones" role="navigation">

        <div class="navbar-header"> 
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
              <span class="sr-only">Desplegar navegación</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse navbar-ex1-collapse"> 
          <ul class="nav navbar-nav navbar-right" id="">                
            <li class="" ><a href="{{URL::to('/')}}/login" class=""> </a></li> 
            <li><a data-toggle="modal" data-target="#modal-iniciar-sesion">INICIAR SESIÓN</a></li>
          </ul>
        </div>
                  
      </nav>
    </div>
  </header>

  <div class="col-sm-12 cont-logo">
    <img src="{{URL::to('/')}}/img/Sigicamcirculo.png" alt="Logo SIGICAM" title="Logo SIGICAM"> 
  </div>
  
  <div class="col-sm-12 sin-pad-lateral">
    <div class="col-sm-12 cont1">
      <div class="col-sm-6">
        <div class="cont1-texto">
          <div class="cont1-texto-titulo"><b>Sig</b>icam</div>
          <span>Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público [Código ID16I10449]</span>
        </div>
      </div> 
    </div>
    <div class="col-sm-12 cont2">
      <div class="col-sm-5 cont2-desc">
        <div class="cont2-texto">
          El Sistema permite disminuir los tiempos de espera en los hospitales, a través de la administración inteligente y sistematizada de los procesos de asignación - reconversión de camas y traslado de pacientes, utilizando técnicas de investigación de operaciones, que permiten detectar restricciones y variables críticas que inciden en la gestión, funcionando como una herramienta de soporte a la toma decisiones.
        </div>
      </div>
      <div class="cont2-imagen col-sm-7">
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
    </div>
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
    </div>
  </div>

  <footer class="col-sm-12 cont-footer">
    <div class="col-sm-offset-4 col-sm-8">
      <div class="col-sm-offset-1 col-sm-2">
      </div>
      <div class="col-sm-1">
      </div>
      <div class="col-sm-1">
      </div>
      <div class="col-sm-1">
      </div>
    </div>

  </footer>







  <!-- Modal -->
  <div class="modal fade" id="modal-iniciar-sesion" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title modal-iniciar-sesion-titulo">Iniciar sesión</h4>
        </div>
        <div class="modal-body" style="display: grid;">
          <form id="frmLogin" autocomplete='off'>

            <div class="form-group inputLoginCamas">
              <label for="rut" class="sr-only">Run</label>
              <input type="text" id="rut" class="form-control" placeholder="Run sin puntos ni guión" required autofocus name="rut" autocomplete="off" onkeypress="return textoRut(event);">
            </div>
            <div class="form-group inputLoginCamas">
              <label for="inputPassword" class="sr-only">Password</label>
              <input type="password" id="password" class="form-control" placeholder="Contraseña" required name="password" autocomplete="off">
            </div>
                     
            <div class="col-sm-5 padL0 texto-derecha btnIngresar" >
              <button class="btn btn-primary-verde" type="submit">Ingresar</button>
            </div>     
                      
            <div id="msgLogin" class="col-md-12 col-sm-12 alert alert-danger" style="display: none; margin-top:10px;">
                <strong></strong>
            </div>
                                            
          </form> 
        </div>
      </div>
      
    </div>
  </div>
        


        
</body>
</html>
