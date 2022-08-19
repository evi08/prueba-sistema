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

    case 'cargar_historial':

        $producto = $_GET['producto'];
        $sql = "SELECT `tipo_movimiento`, `cantidad`, concat(e.primer_nombre, ' ', e.segundo_nombre, ' ', e.primer_apellido, ' ', e.segundo_apellido) empleado, `fecha_hora` FROM tbl_kardex k, tbl_productos p, tbl_empleados e 
        WHERE p.id_producto = k.id_producto AND e.cod_empleado = k.cod_empleado AND p.nombre_producto = '$producto' 
        ORDER BY fecha_hora DESC";

        echo json_encode($conexion->query($sql)->fetch_all(MYSQLI_ASSOC));

        break;

    case 'cargar_historial_por_fechas':
        $producto = $_GET['producto'];
        $fecha_desde = $_GET['fecha_desde'];
        $fecha_hasta = $_GET['fecha_hasta'];

        $sql = "SELECT `tipo_movimiento`, `cantidad`, concat(e.primer_nombre, ' ', e.segundo_nombre, ' ', e.primer_apellido, ' ', e.segundo_apellido) empleado, `fecha_hora` 
        FROM tbl_kardex k, tbl_productos p, tbl_empleados e 
        WHERE p.id_producto = k.id_producto AND e.cod_empleado = k.cod_empleado AND p.nombre_producto = '$producto' 
        AND (fecha_hora BETWEEN '$fecha_desde' AND '$fecha_hasta') 
        ORDER BY fecha_hora DESC;";

        echo json_encode($conexion->query($sql)->fetch_all(MYSQLI_ASSOC));

        break;

        // print_r($_GET);

}
