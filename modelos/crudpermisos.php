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
        if (isset($_POST['insertar'])) {
            $permisoinsertar = 1;
        } else {
            $permisoinsertar = 0;
        }

        if (isset($_POST['actualizar'])) {
            $permisoactualizar = 1;
        } else {
            $permisoactualizar = 0;
        }

        if (isset($_POST['eliminar'])) {
            $permisoeliminar = 1;
        } else {
            $permisoeliminar = 0;
        }

        if (isset($_POST['consultar'])) {
            $permisoconsultar = 1;
        } else {
            $permisoconsultar = 0;
        }


        $rol = $_POST['rolañadirpermiso'];
        $objeto = $_POST['objetoañadirpermiso'];

        $sqlexiste = "SELECT * FROM tbl_permisos WHERE id_rol='$rol' AND id_objeto='$objeto'";
        $resultadoexiste = $mysqli->query($sqlexiste);
        $numexiste = $resultadoexiste->num_rows;
        if ($numexiste == 0) {
            $sql = "INSERT INTO tbl_permisos (id_rol,id_objeto,permiso_insercion,permiso_eliminacion,permiso_actualizacion,permiso_consultar,creado_por,fecha_creacion,modificado_por,fecha_modificacion)
                        Values('$rol','$objeto','$permisoinsertar','$permisoeliminar','$permisoactualizar','$permisoconsultar','$creador',now(),'$creador',now())";
            $resultado = $mysqli->query($sql);

            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=33";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = $row5['objeto'];
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Se registró correctamente el rol, a los usuarios asignados a este rol se le le asigan los accesos automaticamente.');
                    location.href='../permisosusuarios';
                    </script>";
        } else {
            echo "<script>alert('Permisos no registrados,no le puede asignar dos veces permisos a un rol sobre un módulo.');
                    location.href='../permisosusuarios';
                    </script>";
        }
        break;

    case 'editar':
        if (isset($_POST['insertareditar'])) {
            $permisoinsertar = 1;
        } else {
            $permisoinsertar = 0;
        }

        if (isset($_POST['actualizareditar'])) {
            $permisoactualizar = 1;
        } else {
            $permisoactualizar = 0;
        }

        if (isset($_POST['eliminareditar'])) {
            $permisoeliminar = 1;
        } else {
            $permisoeliminar = 0;
        }

        if (isset($_POST['consultareditar'])) {
            $permisoconsultar = 1;
        } else {
            $permisoconsultar = 0;
        }


        $rol = $_POST['permisosroleditar'];
        $objeto = $_POST['permisosmoduloeditar'];

        $sqlobjetoeditarpermiso = "SELECT * FROM tbl_objetos where objeto='$objeto'";
        $resultadoobjetoeditarpermiso = $mysqli->query($sqlobjetoeditarpermiso);
        $roweditarpermisosmodulo = $resultadoobjetoeditarpermiso->fetch_assoc();
        $codigoeditarpermisosmodulo = $roweditarpermisosmodulo['id_objeto'];

        $sqlroleditarpermiso = "SELECT * FROM tbl_roles_usuarios where rol='$rol'";
        $resultadoroleditarpermiso = $mysqli->query($sqlroleditarpermiso);
        $roweditarpermisorol = $resultadoroleditarpermiso->fetch_assoc();
        $codigoeditarpermisosrol = $roweditarpermisorol['id_rol'];

        $sql = "UPDATE tbl_permisos SET permiso_insercion='$permisoinsertar',permiso_eliminacion='$permisoeliminar',permiso_actualizacion='$permisoactualizar',permiso_consultar='$permisoconsultar',modificado_por='$creador',fecha_modificacion=now() WHERE id_rol='$codigoeditarpermisosrol' AND id_objeto='$codigoeditarpermisosmodulo' ";
        $resultado = $mysqli->query($sql);

        $sql5 = "SELECT * FROM tbl_objetos where id_objeto=34";
        $resultado5 = $mysqli->query($sql5);
        $num5 = $resultado5->num_rows;

        $row5 = $resultado5->fetch_assoc();
        $id_objeto = $row5['id_objeto'];
        $accion = $row5['objeto'];
        $descripcion = $row5['descripcion_objeto'];
        event_bitacora($id_objeto, $accion, $descripcion);

        echo "<script>alert('Se actualizaron correctamente los permisos. se modificaran automaticamente los permisos a los uusarios asignados a este rol');
                    location.href='../permisosusuarios';
                    </script>";
        break;
    case 'eliminar':
        $roleliminar = $_POST['roleliminarpermisos'];
        $moduloeliminar = $_POST['moduloeliminarpermisos'];

        $sqlobjetoeliminar = "SELECT * FROM tbl_objetos where objeto='$moduloeliminar'";
        $resultadoobjetoeliminar = $mysqli->query($sqlobjetoeliminar);
        $numobjetoeliminar = $resultadoobjetoeliminar->num_rows;
        $rowobjetoeliminar = $resultadoobjetoeliminar->fetch_assoc();
        $codobjetoeliminar = $rowobjetoeliminar['id_objeto'];

        $sqlmoduloeliminar = "SELECT * FROM tbl_roles_usuarios where rol='$roleliminar'";
        $resultadomoduloeliminar = $mysqli->query($sqlmoduloeliminar);
        $nummoduloeliminar = $resultadomoduloeliminar->num_rows;
        $rowmoduloeliminar = $resultadomoduloeliminar->fetch_assoc();
        $codroleliminar = $rowmoduloeliminar['id_rol'];

        $sql = "DELETE FROM tbl_permisos  WHERE id_rol = '$codroleliminar' AND id_objeto='$codobjetoeliminar'";
        if ($conexion->query($sql)) {
            echo "<script>alert('El rol fué eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        } else {
            echo "<script>alert('Error de eliminación de roles. Un rol no puede ser eliminado debido a su relación con otros registros(Módulos, usuarios etc)')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        }
        $sql5 = "SELECT * FROM tbl_objetos where id_objeto=35";
        $resultado5 = $mysqli->query($sql5);
        $num5 = $resultado5->num_rows;

        $row5 = $resultado5->fetch_assoc();
        $id_objeto = $row5['id_objeto'];
        $accion = $row5['objeto'];
        $descripcion = $row5['descripcion_objeto'];
        event_bitacora($id_objeto, $accion, $descripcion);

        echo "<script>alert('Los permisos de rol fueron eliminados satisfactoriamente.');
                    location.href='../permisosusuarios';
                    </script>";
}
