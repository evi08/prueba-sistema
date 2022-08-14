<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/Exception.php';
require '../../PHPMailer/PHPMailer.php';
require '../../PHPMailer/SMTP.php';

require "../../modelos/conexion.php";
require "../../modelos/generador_contraseñas.php";
 
$conexion = $mysqli;

if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
}

$destinatario = $_SESSION['usuario'];
    $contraseña = generar_contraseña(8);
echo $destinatario;
//Envio de contraseña a la base de datos
 

    $sql = "SELECT cod_usuario, nombre_usuario_correo
    FROM tbl_usuarios_login
    WHERE nombre_usuario_correo ='$destinatario'";
    
    $resultado = $conexion->query($sql);
    
    $num = $resultado->num_rows;

    if ($num > 0) {
        $row = $resultado->fetch_assoc();
        $id = $row['cod_usuario'];
	$correo = $row['nombre_usuario_correo'];

        
	$sql = "update tbl_usuarios_login set clave_usuario = sha1('$contraseña'),modificado_por='$id',fecha_modificacion=now(),cod_estado=1,fecha_caducidad = date_add(now(), interval 30 day)
    where cod_usuario = $id;";
        
	//$resultado = $conexion->query($sql);

        if ($conexion->query($sql)){
        
        echo "<script>alert('Registrado en la base de datos exitosamente')</script>";
    
	    }
	

    } else {
        echo "<script>alert('El usuario no existe, ingrese sus datos nuevamente')
        location.href='recuperar_password.php';
        </script>";
     
    }


/*
update pruebas_fechas set fecha1 = '2022-06-29 07:56:50', fecha2 = now()
where idpruebas_fechas = 1; 
select timestampdiff(day, fecha1, date_add(now(), interval 1 day)) diferencia 
from pruebas_fechas where idpruebas_fechas = 1;
update tbl_ms_usuarios set vencimiento_contraseña_temporal = date_add(now(), interval 1 day)
where id_usuario = 2;
 */

    //Envio de correo   

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'mauriciorenecarcamorivera1@gmail.com';                     //SMTP username
        $mail->Password   = 'mcfilaoniaiqnwxo';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('mauriciorenecarcamorivera1@gmail.com', 'NPH');
        $mail->addAddress($correo);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Recuperacion de credenciales inicio de sesión NPH';
        $mail->Body    = 'Esta es su nueva contraseña para acceder al sistema de NPH: <b>' . $contraseña . '</b>
                         <br>
                         <br>Expirara dentro de 24 horas, periodo en cual deberá cambiarla.
                         <br>
                         <br>Inicie sesión en el sistema mediante la siguiente página:
                         <br><a href="index.php">http://nph.com/index.php</a>
                         <br>
                         <br>
                         <br>Gracias,
                         <br>Equipo de NPH
                         <br>
                         <br><div class="small mb-3 text-muted">Estas recibiendo este email porque un reinicio de contraseña fue solicitado desde tu cuenta.</div>';
                         
                         
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo "<script>
        location.href='../../index.php';
        alert('Se redireccionará a inicio de sesión. Correo enviado exitosamente a $destinatario');
        </script>";
        //echo "<script>setTimeout(\"location.href='index.php'\",1000)</script>";
    } catch (Exception $e) {
        echo "El correo no fué enviado.  Error: {$mail->ErrorInfo}";
    }