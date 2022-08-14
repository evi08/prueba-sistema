<?php
require 'conexion.php';
require 'bitacora.php';
session_start();
$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$creador = $_SESSION['id'];

// ( [id] => 38 [id_Solicitud_editar] => 38 [empleado] => 72 [vehiculo] => 11 [fecha_inicio] => 2022-08-12T10:46 [fecha_final] => 2022-08-12T10:46 [observacion] => LISTO )
switch ($_GET['op']) {
    case 'editar':
        /* [id] => 70 [id_Solicitud_editar] => 70 [empleado] => 72 [vehiculo] => 1 
        [fecha_inicio] => 2022-08-12T11:52 [fecha_final] => 2022-09-02T11:52 [observacion] =>  actividad SADASD ) */
        $id = $_POST['id'];
        $empleado = $_POST['empleado'];
        $vehiculo = $_POST['vehiculo'];        
        $observacion = $_POST['observacion'];
        $actividad = $_POST['actividad'];


        $sql = "UPDATE `tbl_planificacion_transporte` 
        SET        
        `cod_empleado_motorista`='$empleado',
        `cod_vehiculo`='$vehiculo',       
        `actividad`='$actividad',
        `observacion`='$observacion' 
        WHERE `cod_planificacion`='$id';";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=59";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito una planificacion de transporte";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Planificacion modificada con exito')</script>";
           // echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        } else {
            echo "<script>alert('Error al modificar planificacion')</script>";
            //echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        }
        print_r($_POST);
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_planificacion_transporte` WHERE cod_planificacion = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=59";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una planificacion de transporte";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('planificacion eliminada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        }

        echo 'añadir';
        break;
}
