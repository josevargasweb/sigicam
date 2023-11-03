<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="{{ URL::asset('favicon.ico') }}" type="image/x-icon" rel="shortcut icon"/>
    {{HTML::style('css/bootstrap.css') }}
    
    {{ HTML::script('js/jquery-1.11.1.min.js') }}
    {{ HTML::script('js/promise.min.js') }}
    {{ HTML::style('css/sweetalert2.min.css') }}
    {{ HTML::script('js/sweetalert2.all.min.js') }}
    {{ HTML::script('js/bootstrap.js') }}
    {{ HTML::script('js/bootstrapValidator.min.js') }}
    {{ HTML::script('js/language/es_ES.js') }}

    {{ HTML::script('js/funciones.js') }}
    {{ HTML::script('js/bootbox.min.js') }}
    

    {{ HTML::style('css/estiloLogin.css') }}
    {{ HTML::style('css/jquerysctipttop.css') }}
     {{-- HTML::style('http://www.jqueryscript.net/css/jquerysctipttop.css')  --}}
    

    <title>Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público</title>
    <script>
        $(function () {
          $('.yield-contenido').hide();
          $('.yield-contenido').fadeIn(500);
          $('a[href="' + window.location.href + '"]').addClass('menu-index-active');


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

            $("#formCorreoContacto").bootstrapValidator({
                feedbackIcons: {
                  valid: 'glyphicon glyphicon-ok',
                  invalid: 'glyphicon glyphicon-remove',
                  validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    nombreContacto: {
                        validators: {
                            notEmpty: {
                                message: 'El nombre es obligatorio'
                            }
                        }
                    },
                    correoContacto: {
                        validators: {
                            notEmpty: {
                                message: 'El correo es obligatorio'
                            },
                            regexp: {
                              regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                              message: 'El formato del correo es inválido'
                            }
                        }
                    },
                    comentarioContacto: {
                        validators: {
                            notEmpty: {
                                message: 'El comentario es obligatorio'
                            }
                        }
                    }
                }
            }).on("success.form.bv", function (evt) {
              $("#formCorreoContacto input[type='submit']").prop("disabled", true);
              evt.preventDefault(evt);
              var $form = $(evt.target);
              $.ajax({
                    url: $form.prop("action"),//BASE + "/doLogin",
                    /*headers: {        
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },*/
                    data: $form.serialize(),
                    dataType: "json",
                    type: "post",
                    success: function (data) {
                      console.log(data);
                      if(data.exito){
                       	swalExito.fire({
                        title: 'Exito!',
                        text: data.exito,
                        didOpen: function() {
                          setTimeout(function() {
                        location . reload();
                          }, 2000)
                        },
                        });


                      } 
                      if(data.error) swalError.fire({
                                      title: 'Error',
                                      text:data.error
                                      });
                      $("#formCorreoContacto input[type='submit']").prop("disabled", false);
                    },
                    error: function (error) {
                        $("#formCorreoContacto input[type='submit']").prop("disabled", false);
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
      <div class="col-sm-2 col-xs-6 nombre-proyecto">
        <a href="{{URL::to('/')}}"><b>SIGI</b>CAM</a>
      </div>

      <nav class="navbar navbar-inverse col-sm-10 col-xs-6 menu-opciones" role="navigation">

        <div class="navbar-header"> 
            <button type="button" class="navbar-toggle navbar-toggle-index" data-toggle="collapse" data-target=".navbar-ex1-collapse">
              <span class="sr-only">Desplegar navegación</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse navbar-ex1-collapse"> 
          <ul class="nav navbar-nav navbar-right" id="menu-index">                
            <li><a href="{{URL::to('/')}}/acerca">ACERCA DE</a></li>
            <li><a href="{{URL::to('/')}}/equipo">EQUIPO</a></li>
            <li><a href="{{URL::to('/')}}/contacto">CONTACTO</a></li>       
            <li class="hidden-xs"><a class="btn-iniciar-sesion" data-toggle="modal" data-target="#modal-iniciar-sesion">INICIAR SESIÓN</a></li>
          </ul>
        </div>
                  
      </nav>
    </div>
  </header>

  <div class="col-sm-12 cont-logo">
    <img src="{{URL::to('/')}}/img/Sigicamcirculo.png" alt="Logo SIGICAM" title="Logo SIGICAM"> 
  </div>


  <div class="yield-contenido col-sm-12 sin-pad-lateral">
    @yield("contenido")
  </div>


  <footer class="col-sm-12 cont-footer">
    <div class="col-sm-4 col-xs-12 cont-footer-logo">
      <img src="{{URL::to('/')}}/img/delfooter3.png" alt="Logo SIGICAM" title="Logo SIGICAM">
    </div>
    <div class="col-sm-8 col-xs-12 cont-footer-texto">
      <div class="col-sm-offset-1 col-sm-4 col-xs-6">
        <span class="cont-footer-titulo">SIGICAM</span>
        <p class="cont-footer-desc">Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público</p>
      </div>
      <div class="col-xs-6">
        <div class="col-sm-4">
          <span class="cont-footer-titulo">FONO</span>
          <p class="cont-footer-desc">+56322603643</p>
        </div>
        <div class="col-sm-4">
          <span class="cont-footer-titulo">DIRECCIÓN</span>
          <p class="cont-footer-desc">General Cruz 222, Valparaíso</p>
        </div>
        <div class="col-sm-4">
          <span class="cont-footer-titulo">EMAIL</span>
          <p class="cont-footer-desc">labitec@uv.cl</p>
        </div>
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

            <div class="form-group">
              <label for="rut" class="sr-only">Run</label>
              <input type="text" id="rut" class="form-control" placeholder="Run sin puntos ni guión" required autofocus name="rut" autocomplete="off" onkeypress="return textoRut(event);">
            </div>
            <div class="form-group">
              <label for="inputPassword" class="sr-only">Password</label>
              <input type="password" id="password" class="form-control" placeholder="Contraseña" required name="password" autocomplete="off">
            </div>                     
           
            <button class="btn" type="submit">Ingresar</button>            
                      
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
