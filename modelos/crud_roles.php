<?php
require 'conexion.php';
require 'bitacora.php';
session_start();

$conexion = $mysqli;
date_default_timezone_set('America/Tegucigalpa');
if ((!isset($_SESSION['session'])) && $_SESSION['session'] != 'ok') {
    header("Location: logout.php");
}

$creador = $_SESSION['id'];
switch ($_GET['op']) {
    case 'añadir':
        $rol = $_POST['rolnombreañadir'];
        $roldetalle = $_POST['roldetalleañadir'];

        $sqlverificarrol = "SELECT * FROM tbl_roles_usuarios WHERE rol='$rol'";
        $resultadoverificarrol = $mysqli->query($sqlverificarrol);
        $numverificarrol = $resultadoverificarrol->num_rows;
        if ($numverificarrol == 0) {
            $sql = "INSERT INTO tbl_roles_usuarios (rol,detalles_rol,creado_por,fecha_creacion,modificado_por,fecha_modificacion)
                        Values('$rol','$roldetalle','$creador',now(),'$creador',now())";
            $resultado = $mysqli->query($sql);

            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=23";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = $row5['objeto'];
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Se registró correctamente el rol, para que este rol pueda ser usado tiene que asignarle permisos.');
                    location.href='../rolesusuarios';
                    </script>";
        } else {
            echo "<script>alert('Error al registrar rol, usted no puede registrar mas de una vez un rol.');
                    location.href='../rolesusuarios';
                    </script>";
        }
        break;

    case 'editar':
        $codigorol = $_POST['roleditar'];
        $rol = $_POST['rolnombreeditar'];
        $roldetalle = $_POST['roldetalleeditar'];
        $sql = "UPDATE tbl_roles_usuarios SET rol='$rol',detalles_rol='$roldetalle',modificado_por='$creador',fecha_modificacion=now() WHERE id_rol='$codigorol'";
        $resultado = $mysqli->query($sql);

        $sql5 = "SELECT * FROM tbl_objetos where id_objeto=24";
        $resultado5 = $mysqli->query($sql5);
        $num5 = $resultado5->num_rows;

        $row5 = $resultado5->fetch_assoc();
        $id_objeto = $row5['id_objeto'];
        $accion = $row5['objeto'];
        $descripcion = $row5['descripcion_objeto'];
        event_bitacora($id_objeto, $accion, $descripcion);

        echo "<script>alert('Se actualizó correctamente el rol, la actualización no afecta a los usuarios asociados al mismo.');
                    location.href='../rolesusuarios';
                    </script>";
        break;

    case 'eliminar':
        if (isset($_POST['id'])) {
            $idrol = $_POST['idroleliminar'];

            if ($idrol != 1 and $idrol != 18) {
                try{
                    $sqleliminarrol = "DELETE FROM tbl_roles_usuarios  WHERE id_rol = '$idrol'";
                    $resultadorol = $mysqli->query($sqleliminarrol);

                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=25";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);

                    echo "<script>alert('El rol fué eliminado satisfactoriamente.')</script>";
                    echo "<script>setTimeout(\"location.href='../rolesusuarios'\",1)</script>";
               
                }catch(Exception){ 
                    echo "<script>alert('Error de eliminación de roles. Un rol no puede ser eliminado debido a su relación con otros registros(Módulos, usuarios etc)')</script>";
                    echo "<script>setTimeout(\"location.href='../rolesusuarios'\",1)</script>";
                }

                    
            } else {
                echo "<script>alert('IMPORTANTE:*Rol no eliminado,este rol* es un único y con excepciones de eliminación, puede que sea el rol principal o de usuarios nuevos.');
            location.href='../rolesusuarios'</script>";
            }
        }
}
