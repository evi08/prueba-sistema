<?php
require 'conexion.php';
require 'bitacora.php';
session_start();

$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}
$creador = $_SESSION['id'];
$sql = "SELECT u.id_rol_usuario, u.cod_empleado, ed.cod_departamento, d.nombre_departamento, concat(e.primer_nombre,\" \",e.segundo_nombre,\" \",e.primer_apellido,\" \",e.segundo_apellido) empleado 
	FROM tbl_usuarios_login u, tbl_empleados e, tbl_departamentos d, tbl_empleados_departamentos ed 
	WHERE ed.cod_empleado = u.cod_empleado AND d.cod_departamento = ed.cod_departamento AND e.cod_empleado = u.cod_empleado AND u.cod_usuario = $creador;";
$resultado = $conexion->query($sql);
$row_usuario = $resultado->fetch_assoc();

$hora = date('Y-m-d H:i:s', time());
switch ($_GET['op']) {
    case 'añadir':
        $hora = date('Y-m-d H:i:s', time());
        $departamento = $_POST['departamento'];
        $id_empleado = $row_usuario['cod_empleado'];


        $hora = date('Y-m-d H:i:s', time());
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_final = $_POST['fecha_final'];
        $justificacion = $_POST['justificacion'];
        $permiso = $_POST['permiso'];
        $cantidad = $_POST['cantidad'];
        //SELECT * FROM `tbl_solicitudes` WHERE `fecha_inicio` < '2022-08-09 11:36:00' AND '2022-08-09 11:36:00' < `fecha_fin` AND `id_tipo_solicitud` = 5 AND `cod_estado` = 1;

        $sql = "INSERT INTO `tbl_solicitudes`(`id_tipo_solicitud`, `cod_departamento`, `cod_empleado`, `fechahora_ingreso`,`fecha_fin`, 
        `fecha_inicio`, `justificacion`, `opcion_permiso`, `cod_estado`)
        VALUES (5, '$departamento', '$id_empleado', '$hora', '$fecha_final', '$fecha_inicio', '$justificacion','$permiso', 3);";

        if ($conexion->query($sql)) {
            
        } else {
            echo "<script>alert('Error al insertar solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        }

        $sql = "INSERT INTO `tbl_planificacion_transporte`(`cod_planificacion`, `id_solicitud`, `Cantidad_personas`, `fecha_salida`, `fecha_entrada`, `actividad`, `observacion`) 
        VALUES ('', (SELECT s.id_solicitud FROM tbl_solicitudes s WHERE s.fechahora_ingreso = '$hora' AND s.cod_empleado = $id_empleado), '$cantidad', '$fecha_final', '$fecha_inicio', '', '');";


        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=27";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio un nueva solicitud de transporte";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Solicitud insertada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        } else {
            echo "<script>alert('Error al insertar solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        }


        break;

    case 'editar':
        $id = $_POST['id'];
        $dep = $_POST['departamento'];
        $empleado = $row_usuario['cod_empleado'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_final = $_POST['fecha_final'];
        $justificacion = $_POST['justificacion'];
        $permiso = $_POST['permiso'];
        $cantidad_personas = $_POST['capacidad'];
        $hora = date('Y-m-d H:i:s', time());



        $sql = "UPDATE `tbl_solicitudes` 
            SET `cod_departamento`=$dep,
                `cod_empleado`=$empleado,                
                `fecha_inicio` ='$fecha_inicio',
                `fecha_fin` ='$fecha_final',                
                `justificacion` ='$justificacion', 
                `opcion_permiso`='$permiso'              
            WHERE `id_solicitud`=$id;";
        print_r($_POST);

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=27";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito una solicitud de transporte";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            // echo '<br>Solicitud modificada con exito';
            // echo "<script>alert('Solicitud modificada con exito')</script>";
            // echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        } else {
            echo "<script>alert('Error al modificar solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        }

        $sql = "UPDATE `tbl_planificacion_transporte` SET 
        `cod_empleado_motorista`= '',
        `cod_vehiculo`= '',
        `actividad`= '',
        `observacion`= '';";

        // print_r($_POST);

        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_solicitudes` WHERE id_solicitud = '$id';";


        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=27";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una solicitud de transporte";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('Solicitud eliminada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        } else {
            echo "<script>alert('Error de eliminación')</script>";
            //echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        }

        break;

    case 'confirmar':
        $id = $_POST['id'];
        $estado = $_POST['estado'];

        /* RECHAZO DE SOLICITUD */
        if ($estado == 2) {
            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 2 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
                echo "<script>alert('Solicitud rechazada con exito')</script>";
                echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
                die;
            } else {
                echo "<script>alert('Error al rechazar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
                die;
            }
        }

        /* ACEPTACION DE SOLICITUD */
        if ($estado == 1) {
            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 1 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
            } else {
                echo "<script>alert('Error al modificar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
            }
            echo "<script>alert('Solicitud aprobada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../Transporte'\",1)</script>";
        }
}
