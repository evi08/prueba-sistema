<?php

require 'conexion.php';
require 'bitacora.php';

session_start();

$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$creador = $_SESSION['id'];

switch ($_GET['op']) {
    case 'añadir':
        /*  [puesto] => Arquitecto [funciones] => Diseñar edificios llamativos  */
        
        $especialidad = $_POST['especialidad'];
        $descripcion = $_POST['descripcion'];

        $sql = "INSERT INTO `tbl_especialidades`(`cod_especialidad`, `nombre_especialidad`, `descripcion_especialidad`) 
        VALUES ('','$especialidad','$descripcion');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=40";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio una nueva especialidad";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('especialidad insertada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../especialidades'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../especialidades'\",1)</script>";
        }

        break;

    case 'editar':
        $id = $_POST['id'];
        $especialidad = $_POST['especialidad'];
        $descripcion = $_POST['descripcion'];
      

        $sql = "UPDATE `tbl_especialidades` 
      SET `nombre_especialidad`='$especialidad',`descripcion_especialidad`='$descripcion' WHERE `cod_especialidad` = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=40";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito una  especialidad";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Especialidad modificada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../especialidades'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../especialidades'\",1)</script>";
        }
        break;

    case 'eliminar':
        print_r($_POST);
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_especialidades` WHERE `cod_especialidad` = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=40";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una  especialidad";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Especialidad eliminada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../especialidades'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../especialidades'\",1)</script>";
        }

        break;
}