<?php
require_once "/var/www/html/camas/app/controllers/SesionController.php";
require_once "/var/www/html/camas/app/controllers/BaseController.php";
//include ("/usr/local/www/camas/app/controllers/SesionController.php");
$mail=new SesionController();
$mail->enviarCorreoContacto();
?>
