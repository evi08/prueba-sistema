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
        
        $nombre = $_POST['hogar'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        
//INSERT INTO `tbl_categoria_activos`(`cod_categoria_activo`, `nombre_categoria`, `descripcion`) VALUES 
        $sql = "INSERT INTO `tbl_hogares`(`hogar`, `direccion`, `telefono`) 
        VALUES (
            '$nombre',
            '$direccion',
            '$telefono');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=47";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario añadio un nuevo Hogar";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Hogar insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../hogares'\",1)</script>";
        } else {
            echo "<script>alert('No es posible añadir el hogar')</script>";
            echo "<script>setTimeout(\"location.href='../hogares'\",1)</script>";
        }

        
        break;
        //Array ( [id] => [id_hogar] => 8 [hogar] => Agencia2 NPH [direccion] => Col. hato [telefono] => 9643-5584 )
    case 'editar':
        $id_hogar = $_POST['id_hogar'];
        $nombre = $_POST['hogar_editar'];
        $direccion = $_POST['direccion_editar'];
        $telefono = $_POST['telefono_editar'];

        $sql = "UPDATE `tbl_hogares` 
        SET 
        `hogar`= '$nombre',
        `direccion`='$direccion',
        `telefono`='$telefono'
        WHERE `id_hogar` = '$id_hogar'";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=47";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario edito un nuevo Hogar";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Hogar editado con exito')</script>";
           echo "<script>setTimeout(\"location.href='../hogares'\",1)</script>";
        } else {
            echo "<script>alert('No es posible editar el Hogar')</script>";
           echo "<script>setTimeout(\"location.href='../hogares'\",1)</script>";
        }        
     
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_hogares` WHERE id_hogar = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=47";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario elimino un nuevo Hogar";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Hogar eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../hogares'\",1)</script>";
        } else {
            echo "<script>alert('Ocurrio un error al emininar el Hogar')</script>";
            echo "<script>setTimeout(\"location.href='../hogares'\",1)</script>";
        }

        echo 'añadir';
        break;
}
