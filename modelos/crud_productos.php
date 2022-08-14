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
        
        $nombre = $_POST['producto'];
        $capacidadmin = $_POST['capacidadmin'];
        $capacidadmax = $_POST['capacidadmax'];

        $sqlverificar = "SELECT * FROM tbl_productos where nombre_producto = '$nombre'";
        $resultado = $mysqli->query($sqlverificar);
        $num = $resultado->num_rows;

        if($num==0) {
            

        //INSERT INTO `tbl_categoria_activos`(`cod_categoria_activo`, `nombre_categoria`, `descripcion`) VALUES 
        $sql = "INSERT INTO `tbl_productos`(`nombre_producto`, `capacidad_min`, `capacidad_max`) 
        VALUES (
            '$nombre',
            $capacidadmin ,
            $capacidadmax);";

        
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=48";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio un nuevo producto";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            $sql = "SELECT * FROM tbl_productos where nombre_producto = '$nombre'";
        $resultado = $mysqli->query($sql);
        $row = $resultado->fetch_assoc();
        $cod_producto = $row ['id_producto'];

            $sql = "INSERT INTO `tbl_inventarios`(`id_producto`, `cantidad`) 
            VALUES (
                '$cod_producto',
                  0);";
                $resultado = $mysqli->query($sql);
            echo "<script>alert('Producto insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
        
        } else {
            

            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
        }

    } else {
        echo "<script>alert('Usted no puede registrar productos dos veces')</script>";
        echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
    }

        break;

    case 'editar':
        $id = $_POST['id'];
        $nombre = $_POST['producto'];
        $capacidadmin = $_POST['capacidadmin'];
        $capacidadmax = $_POST['capacidadmax'];

        $sql = "UPDATE `tbl_productos` SET `nombre_producto`= '$nombre',`capacidad_min`= '$capacidadmin',`capacidad_max`= '$capacidadmax' 
        WHERE `id_producto`= '$id';";
//Array ( [id] => 2 [producto] => Arroz [capacidadmin] => 22 [capacidadmax] => 30 )
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=48";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito un nuevo producto";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Producto editado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
        }        
        
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_productos` WHERE id_producto = '$id';";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=48";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino un nuevo producto";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Producto eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../productos'\",1)</script>";
        }

        echo 'añadir';
        break;
}
