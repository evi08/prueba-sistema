<?php
require 'conexion.php';
require 'bitacora.php';
session_start();

$conexion = $mysqli;
date_default_timezone_set('America/Tegucigalpa');
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$creador = $_SESSION['id'];
switch ($_GET['op']) {
    case 'editar':


        $codparametro = $_POST['codparametroeditar'];
        $valorparametroeditar = $_POST['valorparametroeditar'];


        $sql = "UPDATE tbl_parametros SET valor='$valorparametroeditar',modificado_por='$creador',fecha_modificacion=now() 
                    WHERE id_parametro = '$codparametro'";
        $resultado = $mysqli->query($sql);

        $sql5 = "SELECT * FROM tbl_objetos where id_objeto=36";
        $resultado5 = $mysqli->query($sql5);
        $num5 = $resultado5->num_rows;

        $row5 = $resultado5->fetch_assoc();
        $id_objeto = $row5['id_objeto'];
        $accion = $row5['objeto'];
        $descripcion = $row5['descripcion_objeto'];
        event_bitacora($id_objeto, $accion, $descripcion);

        echo "<script>alert('El valor del parámetro fué actualizado de forma satisfactoria.')
                    location.href='../parametros';
                    </script>";

        break;

}
