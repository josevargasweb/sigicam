<html>
    <p style="color:black">Estimado usuario,</p>
    <p style="color:black">su contraseña ha sido actualizada satisfactoriamente.</p>    

    <p style="color:black"><u>Datos de la cuenta:</u></p>
    <p><b>Usuario:</b> {{$usuario->rut}}{{($usuario->dv == 10)?"K":$usuario->dv}}</p>

    <p><b>Nueva contraseña:</b> {{$clave}}</p>
    <p style="color:black"><b>Inicie sesión con su nueva contraseña en el siguiente enlace.</b></p>
    <a href="http://10.3.162.185/sigicam/public/">Entrar a Sigicam Copiapo</a>
    <p style="color:black">Saludos</p>
</html>