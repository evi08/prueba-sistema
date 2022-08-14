<?php
require 'conexion.php';
require 'bitacora.php';
session_start();

$conexion = $mysqli;
date_default_timezone_set('America/Tegucigalpa');
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}
function validar_clave($clave, &$error_clave)
{
    if (strlen($clave) < 6) {
        $error_clave = "La clave debe tener al menos 6 caracteres";
        return false;
    }
    if (strlen($clave) > 16) {
        $error_clave = "La clave no puede tener más de 16 caracteres";
        return false;
    }
    if (!preg_match('`[a-z]`', $clave)) {
        $error_clave = "La clave debe tener al menos una letra minúscula";
        return false;
    }
    if (!preg_match('`[A-Z]`', $clave)) {
        $error_clave = "La clave debe tener al menos una letra mayúscula";
        return false;
    }
    if (!preg_match('`[0-9]`', $clave)) {
        $error_clave = "La clave debe tener al menos un caracter numérico";
        return false;
    }
    if (preg_match('" "', $clave)) {
        $error_clave = "No se permiten espacios";
        return false;
    }
    $error_clave = "";
    return true;
}


$creador = $_SESSION['id'];
switch ($_GET['op']) {
    case 'añadir':
        if (isset($_POST['rol'])) {
            $rol = $_POST['rol'];
            $correo_electronico = $_POST['usuario_añadir'];
            $contraseña = $_POST['contraseña'];
            $contraseña1 = $_POST['password2'];
            $estado = $_POST['estado'];
            $codempleado = $_POST['empleado'];
            $intentos = $_POST['intentos_añadir'];
            $preguntas = $_POST['preguntas_añadir'];

            $sql = "SELECT * FROM tbl_usuarios_login WHERE cod_empleado='$codempleado'";
            $resultado2 = $mysqli->query($sql);
            $num2 = $resultado2->num_rows;

            if ($num2 == 0) {
                if ($contraseña == $contraseña1) {
                    if (validar_clave($contraseña, $error_encontrado)) {
                        $sql = "INSERT into tbl_usuarios_login (id_rol_usuario, nombre_usuario_correo, clave_usuario,cod_empleado, fecha_ultima_conexion, num_preguntas_contestadas, numero_ingresos, fecha_caducidad, creado_por, fecha_creacion, cod_estado,modificado_por,fecha_modificacion) 
                        values ('$rol', '$correo_electronico', sha1('$contraseña'),'$codempleado', now(), 0, 0, date_add(now(), interval 360 day), '$creador', now(), '$estado','$creador',now());";
                        $resultado = $mysqli->query($sql) or die($mysql_error());

                        $sql = "SELECT * FROM tbl_usuarios_login WHERE cod_empleado='$codempleado'";
                        $resultado = $mysqli->query($sql);
                        $row = $resultado->fetch_assoc();
                        $codusuario = $row['cod_usuario'];

                        $sql = "INSERT INTO tbl_historial_contraseñas (id_usuario,contraseña,creado_por,fecha_creacion,modificado_por,fecha_modificacion) Values('$codusuario',sha1('$contraseña'),'$codusuario',now(),'$codusuario',now())";
                        $resultado = $mysqli->query($sql);

                        $sql = "INSERT INTO tbl_parametros (parametro,valor,id_usuario,creado_por,fecha_creacion,modificado_por,fecha_modificacion) Values('ADMIN_INTENTOS','$intentos','$codusuario','$codusuario',now(),'$codusuario',now())";
                        $resultado = $mysqli->query($sql);

                        $sql = "INSERT INTO tbl_parametros (parametro,valor,id_usuario,creado_por,fecha_creacion,modificado_por,fecha_modificacion) Values('ADMIN_PREGUNTAS','$preguntas','$codusuario','$codusuario',now(),'$codusuario',now())";
                        $resultado = $mysqli->query($sql);

                        $sql5 = "SELECT * FROM tbl_objetos where id_objeto=9";
                        $resultado5 = $mysqli->query($sql5);
                        $num5 = $resultado5->num_rows;

                        $row5 = $resultado5->fetch_assoc();
                        $id_objeto = $row5['id_objeto'];
                        $accion = $row5['objeto'];
                        $descripcion = $row5['descripcion_objeto'];
                        event_bitacora($id_objeto, $accion, $descripcion);

                        echo "<script>alert('El usuario se ingresó correctamente al sistema, el usuario recibirá un correo con sus credenciales.');
                    location.href='../usuarios';
                    </script>";
                    } else {
                        echo "<script>alert('Estimado usuario, su contraseña debe contener: al menos 6 y màximo 6 caracteres,nùmero, caracter especial y letra minùscula.');
                        location.href='../usuarios';
                        </script>";
                    }
                } else {
                    echo "<script>alert('Estimado usuario, las contraseñas ingresadas no coinciden, ingrese la informaciòn nuevamente.');
                    location.href='../usuarios';
                    </script>";
                }
            } else {
                echo "<script>alert('Estimado usuario, el empleado a quien le desea asignar el usuario nuevo ya tiene una cuenta.');
                location.href='../usuarios';
                </script>";
            }
        }
        break;

    case 'editar':
        if (isset($_POST['rol'])) {
            $rol = $_POST['rol'];
            $correo_electronico = $_POST['usuario_editar'];
            $contraseña = $_POST['contraseñaeditar'];
            $contraseña1 = $_POST['password2editar'];
            $estado = $_POST['estado'];
            $fecha_vencimiento = $_POST['fecha_vencimiento'];
            $codusuario = $_POST['codusuario_editar'];
            if ($contraseña == $contraseña1) {
                if (validar_clave($contraseña, $error_encontrado)) {
                    $sql = "UPDATE tbl_usuarios_login SET id_rol_usuario ='$rol', nombre_usuario_correo='$correo_electronico', clave_usuario=sha1('$contraseña'), fecha_caducidad='$fecha_vencimiento', cod_estado='$estado',modificado_por='$codusuario',fecha_modificacion=now() 
                    WHERE cod_usuario = '$codusuario';";
                    $resultado = $mysqli->query($sql);

                    $sql = "INSERT INTO tbl_historial_contraseñas (id_usuario,contraseña,creado_por,fecha_creacion,modificado_por,fecha_modificacion) Values('$codusuario',sha1('$contraseña'),'$codusuario',now(),$codusuario,now())";
                    $mysqli->query($sql);

                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=10";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);

                    echo "<script>alert('El usuario fuè actualizado correctamente.')
                    location.href='../usuarios';
                    </script>";
                } else {
                    echo "<script>alert('Estimado usuario, su contraseña debe contener: al menos 6 y màximo 6 caracteres,nùmero, caracter especial y letra minùscula.');
                location.href='../usuarios';
                </script>";
                }
            } else {
                echo "<script>alert('Estimado usuario, las contraseñas ingresadas no coinciden, ingrese la informaciòn nuevamente.');
            location.href='../usuarios';
            </script>";
            }
        }
        break;

    case 'eliminar':
        if (isset($_POST['id'])) {
            $codusuario = $_POST['id'];
            $sql = "SELECT * FROM tbl_usuarios_login WHERE cod_usuario='$codusuario'";
            $resultado = $mysqli->query($sql);
            $row = $resultado->fetch_assoc();
            $idrol = $row['id_rol_usuario'];

            $sql2 = "SELECT * FROM tbl_usuarios_login WHERE id_rol_usuario=1";
            $resultado2 = $mysqli->query($sql2);
            $num2 = $resultado2->num_rows;

            if ($idrol != 1 or $num2 > 1) {
                try{
                $sql = "DELETE FROM tbl_usuarios_login  WHERE cod_usuario = $codusuario;";
                $resultado = $mysqli->query($sql) or die($mysql_error());

                $sql5 = "SELECT * FROM tbl_objetos where id_objeto=11";
                $resultado5 = $mysqli->query($sql5);
                $num5 = $resultado5->num_rows;

                $row5 = $resultado5->fetch_assoc();
                $id_objeto = $row5['id_objeto'];
                $accion = $row5['objeto'];
                $descripcion = $row5['descripcion_objeto'];
                event_bitacora($id_objeto, $accion, $descripcion);

                echo "<script>alert('El usuario fuè eliminado exitosamente del sistema');
                location.href='../usuarios';
                </script>";
                }Catch (Exception ){
                    echo "<script>alert('El usuario no puede ser eliminadop del sistema ya que tiene restricciones por relación con otras tablas.');
                    location.href='../usuarios';
                    </script>";
                }
            } else {
                echo "<script>alert('IMPORTANTE:El usuario que intenta eliminar es el ùnico usuario principal, no es posible eliminarlo.');
            location.href='../usuarios'</script>";
            }
        }
}
