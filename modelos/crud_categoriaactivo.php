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
        
        $nombre = $_POST['activo'];
        $descripcion = $_POST['descripcion'];
        
//INSERT INTO `tbl_categoria_activos`(`cod_categoria_activo`, `nombre_categoria`, `descripcion`) VALUES 
        $sql = "INSERT INTO `tbl_categoria_activos`(`nombre_categoria`, `descripcion`) 
        VALUES (
            '$nombre',
            '$descripcion');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=44";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario añadio una nueva categoria";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Categoria Activo insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../categoriaactivo'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../categoriaactivo'\",1)</script>";
        }

        
        break;

    case 'editar':
        $id = $_POST['id'];
        $cod_categoria = $_POST['id_categoria_editar'];
        $nombre = $_POST['categoria_editar'];
        $descripcion = $_POST['descripcion'];

        $sql = "UPDATE `tbl_categoria_activos` 
        SET 
        `nombre_categoria`= '$nombre',
        `descripcion`='$descripcion'
        WHERE `cod_categoria_activo` = '$cod_categoria'";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=44";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito una nueva categoria";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Categoria Activo editado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../categoriaactivo'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../categoriaactivo'\",1)</script>";
        }        

        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_categoria_activos` WHERE cod_categoria_activo = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=44";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una nueva categoria";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Categoria Activo eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../categoriaactivo'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../categoriaactivo'\",1)</script>";
        }

        echo 'añadir';
        break;
}
