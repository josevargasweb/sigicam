<html>
    <p style="color:black">Estimado usuario,</p>
    <p style="color:black">su cuenta ha sido creada satisfactoriamente.</p>
     

    <p style="color:black"><u>Datos de la cuenta:</u></p>
    <p><b>Usuario:</b> {{$usuario->rut}}{{($usuario->dv == 10)?"K":$usuario->dv}}</p>
    
    <p><b>Contraseña:</b> {{$clave}}</p>
    <p style="color:black"><b>Recuerde iniciar sesión y cambiar su contraseña en el siguiente enlace.</b></p>
    <a href="http://10.3.162.185/sigicam/public/">Entrar a Sigicam Copiapo</a>
    <p style="color:black">Saludos</p>
    <br>
</html>
