<?php

require 'conexion.php';

session_start();

$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$creador = $_SESSION['id'];

switch ($_GET['op']) {
    case 'añadir':
        /*  [puesto] => Arquitecto [salario] => 40000 [funciones] => Diseñar edificios llamativos  */
        
        $puesto = $_POST['puesto'];
        $salario = $_POST['salario'];
        $funciones = $_POST['funciones'];

        $sql = "INSERT INTO `tbl_puesto_empleados`(`cod_puesto`, `nombre_puesto`, `salario_puesto`, `funciones_puesto`) 
        VALUES ('','$puesto',$salario,'$funciones');";

        if ($conexion->query($sql)) {
            echo "<script>alert('Puesto insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        }


        break;

    case 'editar':
        $id = $_POST['id'];
        $puesto = $_POST['puesto'];
        $salario = $_POST['salario'];
        $funciones = $_POST['funciones'];

        $sql = "UPDATE `tbl_puesto_empleados` 
        SET `nombre_puesto`='$puesto',`salario_puesto`=$salario,`funciones_puesto`='$funciones' WHERE `cod_puesto` = $id;";

        if ($conexion->query($sql)) {
            echo "<script>alert('Puesto modificado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        }


        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_puesto_empleados` WHERE `cod_puesto` = $id;";

        if ($conexion->query($sql)) {
            echo "<script>alert('Puesto eliminado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../puestos'\",1)</script>";
        }
        break;
}