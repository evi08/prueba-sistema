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
        $categoria = $_POST['categoria'];
        $proveedor = $_POST['proveedor'];
        $activo = $_POST['activo'];
        $descripcion = $_POST['descripcion'];
        $presentacion = $_POST['presentacion'];

        $sql = "INSERT INTO `tbl_activos`(`cod_categoria_activo`, `cod_proveedor_activo`, `nombre_activo`, `descripcion_activo`, `presentacion_activo`) 
        VALUES (
            $categoria,
            $proveedor,
            '$activo',
            '$descripcion',
            '$presentacion');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=43";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario añadio un activo";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Activo insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../verinventario'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../verinventario'\",1)</script>";
        }

        break;

    case 'editar':
        $id = $_POST['id'];
        $categoria = $_POST['categoria'];
        $proveedor = $_POST['proveedor'];
        $activo = $_POST['activo'];
        $descripcion = $_POST['descripcion'];
        $presentacion = $_POST['presentacion'];

        $sql = "UPDATE `tbl_activos` 
        SET 
        `cod_categoria_activo`= $categoria,
        `cod_proveedor_activo`= $proveedor,
        `nombre_activo`='$activo',
        `descripcion_activo`='$descripcion',
        `presentacion_activo`='$presentacion' 
        WHERE `cod_activo` = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=43";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario edito un activo";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Activo insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../verinventario'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../verinventario'\",1)</script>";
        }        
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_activos` WHERE cod_activo = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=43";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario elimino un activo";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Activo eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../verinventario'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../verinventario'\",1)</script>";
        }
        break;
}
