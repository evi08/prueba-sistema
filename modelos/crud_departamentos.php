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
        //Array ( [departamento] => Enfermeria [cantidad] => 4 [funcion] => Atención medica [descripcion] => Brindar atención medica a los empleados )
        $departamento = $_POST['departamento'];
        $cantidad = $_POST['cantidad'];
        $funcion = $_POST['funcion'];
        $descripcion = $_POST['descripcion'];

        $sql = "INSERT INTO `tbl_departamentos`(`nombre_departamento`, `cantidad_empleados`, `funcion_del_departamento`, `descripcion`)
         VALUES (
            '$departamento',
            $cantidad,
            '$funcion',
            '$descripcion');";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=26";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio un nuevo departamento";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Departamento insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
        }

        break;

    case 'editar':

        $id = $_POST['id'];
        $departamento = $_POST['departamento'];
        $cantidad = $_POST['cantidad'];
        $funcion = $_POST['funcion'];
        $descripcion = $_POST['descripcion'];

        $sql = "UPDATE `tbl_departamentos` 
        SET 
        `nombre_departamento`= '$departamento',
        `cantidad_empleados`='$cantidad',
        `funcion_del_departamento`='$funcion',
        `descripcion`='$descripcion' 
        WHERE `cod_departamento` = '$id'";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=26";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito un departamento";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Departamento insertado con exito')</script>";
            echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
        }

        break;

    case 'eliminar':
        $iddepto = $_POST['iddepto'];
        if ($iddepto != 13) {
            try {
                $sqldeptos = "DELETE FROM `tbl_departamentos` WHERE cod_departamento = '$iddepto'";
                $resultadodeptos = $mysqli->query($sqldeptos);

                $sql5 = "SELECT * FROM tbl_objetos where id_objeto=26";
                $resultado5 = $mysqli->query($sql5);
                $num5 = $resultado5->num_rows;

                $row5 = $resultado5->fetch_assoc();
                $id_objeto = $row5['id_objeto'];
                $accion = "El usuario elimino un  departamento";
                $descripcion = $row5['descripcion_objeto'];
                event_bitacora($id_objeto, $accion, $descripcion);

                echo "<script>alert('Departamento eliminado con exito')</script>";
                echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
            } catch (Exception) {
                echo "<script>alert('El departamento no puede ser eliminado por razones de restricciones o vinculación con empleados')</script>";
                echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
            }
        } else {
            echo "<script>alert('El departamento asignado a empleados nuevos no se puede eliminar')</script>";
            echo "<script>setTimeout(\"location.href='../departamentos'\",1)</script>";
        }
}
