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
        $vehiculo = $_POST['vehiculo'];
        $capacidad = $_POST['capacidad'];
        $placa = $_POST['placa'];
        $descripcion_vehiculo = $_POST['descripcion'];

        $sql = "INSERT INTO `tbl_vehiculos` (`cod_vehiculo`,`tipo_vehiculo`, `capacidad_personas`, `placa`,`descripcion`) VALUES 
            ('','$vehiculo', $capacidad, '$placa', '$descripcion_vehiculo');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=28";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio un nuevo vehiculo";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('vehiculo insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Vehiculos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../Vehiculos'\",1)</script>";
        }
        break;


    case 'editar':
        $id = $_POST['id'];
        $vehiculo = $_POST['vehiculo'];
        $capacidad = $_POST['capacidad'];
        $placa = $_POST['placa'];
        $descripcion_vehiculo = $_POST['descripcion'];

        $sql = "UPDATE `tbl_vehiculos`
        SET `tipo_vehiculo` = '$vehiculo',
         `capacidad_personas` = '$capacidad',
         `placa` = '$placa', 
         `descripcion` = '$descripcion_vehiculo'
         WHERE `cod_vehiculo`='$id';";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=28";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito una vehiculo";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('vehiculo modificado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Vehiculos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../Vehiculos'\",1)</script>";
        }        

        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_vehiculos` WHERE cod_vehiculo = $id;";
    

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=28";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino un vehiculo";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('vehiculo eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Vehiculos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../Vehiculos'\",1)</script>";
        }  

        echo 'añadir';
        break;           
}
