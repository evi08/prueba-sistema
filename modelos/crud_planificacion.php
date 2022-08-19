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
        $id = $_POST['id'];
        $empleado = $_POST['empleado'];
        $vehiculo = $_POST['vehiculo'];
        $observacion = $_POST['observacion'];
        $actividad = $_POST['actividad'];

        $fecha_salida_editar = $_POST['fecha_salida_editar'];
        $fecha_regreso_editar = $_POST['fecha_regreso_editar'];

        $conteo1 = 0;
        $conteo2 = 0;
        $mensaje1 = '';
        $mensaje2 = '';

        $sql = "SELECT p.id_solicitud, s.fecha_inicio, s.fecha_fin 
        FROM `tbl_planificacion_transporte` p, tbl_solicitudes s 
        WHERE p.id_solicitud = s.id_solicitud AND s.cod_estado = 1 AND p.cod_empleado_motorista = $empleado AND p.id_solicitud != $id AND
        (('$fecha_salida_editar' BETWEEN s.fecha_inicio AND s.fecha_fin) OR 
        ('$fecha_regreso_editar' BETWEEN s.fecha_inicio AND s.fecha_fin) OR 
        (s.fecha_inicio BETWEEN '$fecha_salida_editar' AND '$fecha_regreso_editar') OR 
        (s.fecha_fin BETWEEN '$fecha_salida_editar' AND '$fecha_regreso_editar')) 
        ORDER BY p.id_solicitud DESC;";

        $resultado = $conexion->query($sql);
        while ($row = $resultado->fetch_assoc()) {
            $conteo1++;
            $mensaje1 .= ' ' . strval($row['id_solicitud'])  . ' (' . strval($row['fecha_inicio']) . ' - ' . strval($row['fecha_fin']) . '),';
        }

        $sql = "SELECT p.id_solicitud, s.fecha_inicio, s.fecha_fin 
        FROM `tbl_planificacion_transporte` p, tbl_solicitudes s 
        WHERE p.id_solicitud = s.id_solicitud AND s.cod_estado = 1 AND p.cod_vehiculo = $vehiculo AND p.id_solicitud != $id AND
        (('$fecha_salida_editar' BETWEEN s.fecha_inicio AND s.fecha_fin) OR 
        ('$fecha_regreso_editar' BETWEEN s.fecha_inicio AND s.fecha_fin) OR 
        (s.fecha_inicio BETWEEN '$fecha_salida_editar' AND '$fecha_regreso_editar') OR 
        (s.fecha_fin BETWEEN '$fecha_salida_editar' AND '$fecha_regreso_editar')) 
        ORDER BY p.id_solicitud DESC;";

        $resultado = $conexion->query($sql);
        while ($row = $resultado->fetch_assoc()) {
            $conteo2++;
            $mensaje2 .= ' ' . strval($row['id_solicitud'])  . ' (' . strval($row['fecha_inicio']) . ' - ' . strval($row['fecha_fin']) . '),';
        }

        if ($conteo1 > 0 || $conteo2 > 0) {
            if ($conteo1 > 0) {
                $mensaje1 = substr_replace($mensaje1, '.', -1);
                echo "<script>alert('El empleado motorista ya tiene asignadas las siguientes solicitudes:$mensaje1')</script>";
            }
            if ($conteo2 > 0) {
                $mensaje2 = substr_replace($mensaje2, '.', -1);
                echo "<script>alert('El vehiculo ya tiene asignadas las siguientes solicitudes:$mensaje2')</script>";
            }
            die;
        }



        // [id] => 318 [id_Solicitud_editar] => ALEX ELAN SANCHES VILCHES [empleado] => 72 [cantidad_editar] => 6 [vehiculo] => 1 [fecha_salida_editar] => 2022-08-17 21:19:00 
        // [fecha_regreso_editar] => 2022-08-16 21:19:00 [actividad] => SI [observacion]

        // $sql = "UPDATE `tbl_planificacion_transporte` 
        // SET        
        // `cod_empleado_motorista`='$empleado',
        // `cod_vehiculo`='$vehiculo',       
        // `actividad`='$actividad',
        // `observacion`='$observacion' 
        // WHERE `id_solicitud`='$id';";

        // if ($conexion->query($sql)) {
        //     $sql5 = "SELECT * FROM tbl_objetos where id_objeto=59";
        //     $resultado5 = $mysqli->query($sql5);
        //     $num5 = $resultado5->num_rows;

        //     $row5 = $resultado5->fetch_assoc();
        //     $id_objeto = $row5['id_objeto'];
        //     $accion = "El usuario edito una planificacion de transporte";
        //     $descripcion = $row5['descripcion_objeto'];
        //     event_bitacora($id_objeto, $accion, $descripcion);
        //     echo "<script>alert('Planificacion modificada con exito')</script>";
        //    // echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        // } else {
        //     echo "<script>alert('Error al modificar planificacion')</script>";
        //     echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        // }
        //print_r($_POST);
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_planificacion_transporte` WHERE id_solicitud = $id;";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=59";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una planificacion de transporte";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Planificacion eliminada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        } else {
            echo "<script>alert('Error')</script>";
            echo "<script>setTimeout(\"location.href='../Planificacion'\",1)</script>";
        }
        
        break;
}
