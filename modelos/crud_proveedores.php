<?php

require 'conexion.php';
require 'bitacora.php';

session_start();

$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$id = $_SESSION['id'];

switch ($_GET['op']) {
    case 'añadir':
        //[categoria] => 1 [proveedor] => 1 [activo] => ddddd [descripcion] => oioi [presentacion] => oioii 
        
        $nombre = $_POST['proveedor'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo_proveedor'];
        $telefono = $_POST['telefono_proveedor'];
        
//INSERT INTO `tbl_categoria_activos`(`cod_categoria_activo`, `nombre_categoria`, `descripcion`) VALUES 
        $sql = "INSERT INTO `tbl_proveedores`(`nombre_proveedor`, `direccion_proveedor`, `correo_proveedor`,`telefono_proveedor`) 
        VALUES (
            '$nombre',
            '$direccion',
            '$correo',
            '$telefono');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=49";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio un nuevo proveedor";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Proveedor insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../proveedores'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../proveedores'\",1)</script>";
        }

        
        break;

    case 'editar':
        $id = $_POST['id'];
        $cod_proveedor = $_POST['cod_proveedor'];
        $nombre = $_POST['nombre_proveedor'];
        $direccion = $_POST['direccion_proveedor'];
        $correo = $_POST['correo_proveedor'];
        $telefono = $_POST['telefono_proveedor'];

        $sql = "UPDATE `tbl_proveedores` 
        SET 
        `nombre_proveedor`= '$nombre',
        `direccion_proveedor`='$direccion',
        `correo_proveedor`='$correo',
        `telefono_proveedor`='$telefono'
        WHERE `cod_proveedor` = '$cod_proveedor';";
//Array ( [id] => 3 [cod_proveedor] => 3 [proveedor] => Pollo rey [direccion] => Col. hato [correo_proveedor] => pollo@gmail.com [telefono_proveedor] => 9654-8547 )
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=49";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito un nuevo proveedor";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Proveedor editado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../proveedores'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../proveedores'\",1)</script>";
        }        
        
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_proveedores` WHERE cod_proveedor = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=49";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino un nuevo proveedor";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Categoria Activo eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../proveedores'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../proveedores'\",1)</script>";
        }

        echo 'añadir';
        break;
}
