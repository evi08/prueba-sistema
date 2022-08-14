<?php
require "../../modelos/conexion.php";
require "../../modelos/bitacora.php";
session_start();
$sql5 = "SELECT * FROM tbl_objetos where id_objeto=7";
 $resultado5 = $mysqli->query($sql5);
 $num5 = $resultado5->num_rows;

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
       $error_clave = "La clave debe tener al menos un número";
       return false;
    }
    if (preg_match('" "',$clave)){
        $error_clave = "No se permiten espacios";
        return false;
     }
     if (!preg_match('"^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{3,}$"',$clave)){
        $error_clave = "Ingrese al menos un caracter especial";
        return false;
     }
    $error_clave = "";
    return true;
 }


$id = $_SESSION['id'];
       
if($id!=""){
    $sql4 = "SELECT *from tbl_usuarios_login where cod_usuario = '$id'";
    $resultado4 = $mysqli->query($sql4);
    $num4 = $resultado4->num_rows;
    $filas=$resultado4->fetch_assoc();
    $contestadas=$filas['num_preguntas_contestadas'];
    if($contestadas>0){
        $sql2 = "SELECT *from tbl_preguntas_respuestas_usuarios where id_usuario='$id'";
        $resultado2 = $mysqli->query($sql2);

        if (isset($_POST['verificar']) && !empty($_POST['respuesta'])) {
            $pregunta = $_POST['pregunta'];
            $respuesta = $_POST['respuesta'];
            $contra1=$_POST['nuevapassword'];
            $contra2=$_POST['confirmarpassword'];
            $_SESSION['usuario'] = $filas['nombre_usuario_correo'];
                    if($pregunta!=0){
                        $sql = "SELECT *FROM tbl_preguntas_respuestas_usuarios where id_pregunta= '$pregunta' and id_usuario='$id'";
                        $resultado = $mysqli->query($sql); 
                        $row=$resultado->fetch_assoc();
                        $respuestaregistrada=$row['respuesta'];
                        if($respuesta==$respuestaregistrada){
                            if($contra1==$contra2){
                                if (validar_clave($contra1, $error_encontrado)){
                                $sql3 = "SELECT *from tbl_historial_contraseñas where contraseña = SHA1('$contra1')";
                                $resultado3 = $mysqli->query($sql3);
                                $num3 = $resultado3->num_rows;
                                    if($num3==0){
                                        $sql = "INSERT INTO tbl_historial_contraseñas (id_usuario, contraseña,creado_por,fecha_creacion,modificado_por,fecha_modificacion)
                                        VALUES('$id', SHA1('$contra1'), '$id',now(),'$id',now())";
                                        $resultado = $mysqli->query($sql);

                                        $sql3="UPDATE tbl_usuarios_login SET clave_usuario =SHA1('$contra1'),cod_estado=1,modificado_por='$id',fecha_modificacion=now() WHERE cod_usuario= '$id' ";
                                        $resultado3 = $mysqli->query($sql3);

                                        $row5 = $resultado5->fetch_assoc();
                                        $id_objeto=$row5['id_objeto'];
                                        $accion=$row5['objeto'];
                                        $descripcion= $row5['descripcion_objeto'];
                                        event_bitacora($id_objeto, $accion,$descripcion);

                                        echo "<script> 
                                        location.href ='../../index.php';
                                        alert ('Muy bien, incie sesión con sus nuevas credenciales, será redireccionado al inicio de sesión');
                                        </script>";
                                        session_destroy();
                                    }else{
                                        echo ("<div class='alert alert-danger'>No puede usar esta contraseña, ingrese una diferente</div>");
                                        }
                                }else{
                                    echo ("<div class='alert alert-danger'>Su contraseña no es valida: . $error_encontrado;</div>");
                                    }
                            }else{
                                echo ("<div class='alert alert-danger'>Su contraseñas no coinciden</div>");
                            }
                        }else{
                            echo ("<div class='alert alert-danger'>Su respuesta ingresada no es correcta</div>");
                        }
                    }else{
                        echo ("<div class='alert alert-danger'>Es obligatorio que seleccione una pregunta para su respuesta</div>");
                    }
        }
    }else{
        echo "<script> 
        alert ('Usted no tiene preguntas registradas, no puede usar este metodo de recuperación');
        location.href ='password.php';
        </script>";

    }
}else{
    echo "<script> 
    location.href ='password.php';
    </script>";
}
?>
<script>
function evitarespacio(ev){
        key=ev.keyCode || ev.which;
        tecla=String.fromCharCode(key).toString();
        letras="ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789.*/+-,;_{[}]¿¡?=)(&%$#<>@";
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

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Recuperación contraseña-NPH</title>
    <link href="css/styles.css" rel="stylesheet" />
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Recuperación via pregunta</h3></div>
                                <div class="card-body">

                                    <form class="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"  >

                                        <div class="form-row " style="margin:20px 20px 10px 20px">
                                            <div class="col-md-9" >
                                            <div class="card-header"><h3 class="text-center font-weight-light my-1">Verificacón de pregunta secreta</h3></div>
                                                <label class="small mb-1" for="inputConfirmPassword">Seleccione una pregunta:</label>
                                                <select name="pregunta" id="combo_preguntasid1" >
                                                    <option name="preguntaoption" value="0">Seleccione una pregunta</option>
                                                    <?php while ($row2 = $resultado2->fetch_assoc()) {
                                                    $codpregunta=$row2['id_pregunta'];
                                                     $sql3 = "SELECT pregunta from tbl_preguntas_usuarios where id_pregunta='$codpregunta'";
                                                     $resultado3 = $mysqli->query($sql3);
                                                     $row3 = $resultado3->fetch_assoc();
                                                    ?>
                                                        <option value="<?php echo $row2['id_pregunta']; ?>"><?php echo $row3['pregunta']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                                <div class="form-group"><label class="small mb-1" for="inputrespuesa">*Ingrese una respuesta</label>
                                                    <input class="form-control py-4" type="text" id="respuesta" name="respuesta" autocomplete="off" maxlength="100" placeholder="Escriba su respuesta" required  />
                                                </div>
                                            </div>
                                            <div class="card-header col-md-11" ><h3 class="text-center font-weight-light my-1">Registro de su nueva contraseña</h3><p><b>Su contraseña debe contener:</b>  <br><small>
                                                            °Al menos 6 caracteres y como máximo 16 caracteres. <br>
                                                            °Al menos una letra mayúscula y una letra mayúscula.. <br>
                                                            °Al menos una número. <br>
                                                            °No se permiten espacios.</p></small></div>
                                            <div class="col-md-9">
                                                <div class="form-group"><label class="small mb-1" for="inputnuevaPassword"><b> *Ingrese su nueva contraseña. </b> </label>
                                                    <input class="form-control py-4" type="text" id="nuevapassword" name="nuevapassword" onkeypress="return evitarespacio(event);" autocomplete="off" maxlength="16" minlength="6" placeholder="Ingrese una contraseña"  required/>
                                                
                                                <div class="form-row " style="margin:20px 20px 10px 20px"> 
                                                </div>
                                                
                                                <div class="form-group"><label class="small mb-1" for="inputConfirmPassword" ><b> *Confirme su nueva contraseña. </b></label>
                                                    <input class="form-control py-4" type="text" id="confirmarpassword" name="confirmarpassword" onkeypress="return evitarespacio(event);" autocomplete="off" maxlength="16" minlength="6" placeholder="Confirme su contraseña"  required/>
                                                </div>
                                                    <div><button class="btn btn-primary"  type="submit"  name="verificar" >Verificar y terminar</button></div>
                                                </div> 
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>