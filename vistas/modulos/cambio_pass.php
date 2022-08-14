<?php
  require "../../modelos/conexion.php";
  include "../../modelos/bitacora.php";
  session_start();
  $id = $_SESSION['id'];
  
  function validar_clave($clave,&$error_clave){
    if(strlen($clave) < 6){
       $error_clave = "La clave debe tener al menos 6 caracteres";
       return false;
    }
    if(strlen($clave) > 16){
       $error_clave = "La clave no puede tener más de 16 caracteres";
       return false;
    }
    if (!preg_match('`[a-z]`',$clave)){
       $error_clave = "La clave debe tener al menos una letra minúscula";
       return false;
    }
    if (!preg_match('`[A-Z]`',$clave)){
       $error_clave = "La clave debe tener al menos una letra mayúscula";
       return false;
    }
    if (!preg_match('`[0-9]`',$clave)){
       $error_clave = "La clave debe tener al menos un caracter numérico";
       return false;
    }
    if (preg_match('" "',$clave)){
        $error_clave = "No se permiten espacios";
        return false;
     }
    $error_clave = "";
    return true;
 }

 $sql5 = "SELECT * FROM tbl_objetos where id_objeto=5";
 $resultado5 = $mysqli->query($sql5);
 $num5 = $resultado5->num_rows;

 if (isset($_POST['guardar'])) {
    $contra1 = $_POST['contra'];
    $contra2 = $_POST['contra1'];
    if ($contra1 == $contra2) {
        if (validar_clave($contra1, $error_encontrado)){
            $contra1 = sha1($contra1);
            $sql = "SELECT * FROM tbl_historial_contraseñas where contraseña=sha1('$contra1')";
            $resultado = $mysqli->query($sql);
            $num = $resultado->num_rows;

            if ($num > 0) {
                echo ("<div class='alert alert-danger'>Error:Contraseña en historial, no puede usarla, ingrese otra.</div>");
            } else {

                $sql = "INSERT INTO tbl_historial_contraseñas (id_usuario,contraseña,creado_por,fecha_creacion,modificado_por,fecha_modificacion) Values('$id',sha1('$contra1'),'$id',now(),'$id',now())";
                $mysqli->query($sql);
                $sql = "UPDATE tbl_usuarios_login set clave_usuario = SHA1('$contra1'),modificado_por='$id',fecha_modificacion=now(),numero_ingresos=numero_ingresos+1 where cod_usuario = '$id'";
                $mysqli->query($sql);
                $row5 = $resultado5->fetch_assoc();
                $id_objeto=$row5['id_objeto'];
                $accion=$row5['objeto'];
                $descripcion= $row5['descripcion_objeto'];
                event_bitacora($id_objeto, $accion,$descripcion);
                echo "<script> alert ('Configuración de seguridad completa, será redireccionado a inicio de sesión.');
                location.href ='../../index.php';
                </script>";
                unset($_SESSION['id']);
            }
        }else{
            echo ("<div class='alert alert-danger'>Su contraseña no es valida: . $error_encontrado;</div>");
        }
    } else {
        echo "<script> alert ('Las contraseñas no coinciden, ingreselas nuevamente')</script>";
          

    }
}
?>
<script>
    function solonumeros(evt){
        if(window.event){
        keynum=evt.keyCode;
        }else{
        keynum=evt.which;
        }
    if(keynum>47 && keynum<58 || keynum==8 || keynum==13){
        return true;
    }else{
        alert("Para este campo solo son permitidos números.");  
        return false; 
    }
    }
//onkeypress="return solonumeros(event);" agrgar esta propiedad
    function evitarespeciales(e){
        key=e.keyCode || e.which;
        tecla=String.fromCharCode(key).toString();
        letras="ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789.@";
        especiales=[8,13];
        tecla_especial=false;
        for(var i in especiales){
            if(key==especiales[i]){
                tecla_especial=true;
                break;
            }
        }
        if(letras.indexOf(tecla)==-1 && !tecla_especial){
            swal.fire({icon:'info',
                tittle:'dato',
                text:'Intenta ingresar un valor no permitido'});
            return false;
        }
    }

    function evitarespacio(ev){
        key=ev.keyCode || ev.which;
        tecla=String.fromCharCode(key).toString();
        letras="ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789.*/+-,;_{[}]¿¡?=)(&%$#<>@!:";
        especiales=[8,13];
        tecla_especial=false;
        for(var i in especiales){
            if(key==especiales[i]){
                tecla_especial=true;
                break;
            }
        }
        if(letras.indexOf(tecla)==-1 && !tecla_especial){
            swal.fire({icon:'info',
                tittle:'dato',
                text:'Intenta ingresar un valor no permitido'});
            return false;
        }
    }

    
</script>

<!DOCTYPE html5>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Registrarse-NPH</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-2 rounded-lg mt-5" style="margin:20px">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Cambio de contraseña</h3>
                                </div>
                                <div class="card-body">
                                    <form class="" method="POST" action="">
                                        <div class="form-row " style="margin:20px 20px 10px 20px">
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Contraseña</b> </label>
                                                    <input class="form-control py-4" id="contra" type="text" name="contra" onkeypress="return evitarespacio(event);" maxlength="16" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Confirme contraseña</b> </label>
                                                    <input class="form-control py-4" id="contra1" type="text" name="contra1" onkeypress="return evitarespacio(event);" maxlength="16" required />
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 text-center">
                                                <div style="margin:30px 20px 10px 20px" class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button class="btn btn-primary btn-block" type="submit" name="guardar">Terminar</button></div>
                                            </div>
                                           
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script type="text/javascript">
    function myFuction() {
    var x=document.getElementById("contra");
    var y=document.getElementById("contra1");
    if (x.type=="password") {
    x.type="text"; 
    }else{
        x.type="password"; 
        }
    }

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>