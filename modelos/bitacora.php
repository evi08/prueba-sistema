<?php
function event_bitacora($id_objeto, $accion, $descripcion){
include 'conexion.php';
date_default_timezone_set("America/Tegucigalpa");
$fechaaccion = date("Y-n-j H:i:s");
$usuario =$_SESSION['usuario'];
$consulta="SELECT cod_usuario
             FROM tbl_usuarios_login
             WHERE nombre_usuario_correo ='$usuario'";
             
$sql = "SELECT * FROM tbl_usuarios_login WHERE nombre_usuario_correo ='$usuario'";
$resultado = $mysqli->query($sql);
$num = $resultado->num_rows;
  if($num>0) {
    $row = $resultado->fetch_assoc();
$cod_usuario=$row['cod_usuario'];
  }else{
    $cod_usuario="";
  }
    if($cod_usuario!=""){
        $consultaBitacora=mysqli_query($mysqli,"INSERT INTO tbl_ms_bitacora (id_objeto, cod_usuario, ejecutor,fecha, accion, descripcion)
                            VALUES('$id_objeto','$cod_usuario','$usuario',now(),'$accion','$descripcion')");
    }else{
        $consultaBitacora=mysqli_query($mysqli,"INSERT INTO tbl_ms_bitacora (id_objeto, cod_usuario, ejecutor,fecha, accion, descripcion)
                            VALUES('$id_objeto',1,'$usuario','$fechaaccion','$accion','$descripcion')");  
    }
}
