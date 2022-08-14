<script>
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 8) {
            return true;
        }
        patron = /[A-Za-z]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function validarNumero(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 8) return true;
        patron = /[0-9-]/;
        te = String.fromCharCode(tecla);
        return patron.test(te);
    }

    function email(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 8) return true;
        patron = /[A-Za-z0-9@_.]/;
        te = String.fromCharCode(tecla);
        return patron.test(te);
    }
</script>
<?php
require 'conexion.php';
require 'bitacora.php';
session_start();
date_default_timezone_set('America/Tegucigalpa');
$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$creador = $_SESSION['id'];

switch ($_GET['op']) {
    case 'añadir':
        $coddepartamento = $_POST['departamentosolicitudañadir'];
        $codsolicitud = $_POST['tiposolicitudañadir'];
        $codempleado = $_SESSION['cod_empleado'];
        $fechaingreso = $_POST['fechahorasolicitud'];
        $fechahorainicio = $_POST['fechainiciosolicitud'];
        $fechahorafinal = $_POST['fechafinalsolicitud'];
        $justificacion = $_POST['justificacionsolicitud'];
        $acuentade = $_POST['acuentadesolicitud'];

        $fechaactual = date("Y-n-j H:i:s");

        if ((strtotime($fechaactual)) <= (strtotime($fechahorainicio))) {
            if ((strtotime($fechahorainicio)) <= (strtotime($fechahorafinal))) {
                $sql = "INSERT INTO `tbl_solicitudes` (`id_tipo_solicitud`, `cod_departamento`, `cod_empleado`, `fechahora_ingreso`, `fecha_inicio`, `fecha_fin`, `justificacion`, `cod_estado`,`opcion_permiso`) 
                                        VALUES ('$codsolicitud', '$coddepartamento', '$codempleado','$fechaingreso', '$fechahorainicio', '$fechahorafinal', '$justificacion', 3, '$acuentade')";
                $resultado = $mysqli->query($sql);

                if ($codsolicitud == 1) {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=15";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                } else {
                    $sql5 = "UPDATE tbl_empleados SET numero_permisos_empleado= numero_permisos_empleado+1 where cod_empleado='$codempleado'";
                    $resultado5 = $mysqli->query($sql5);

                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=16";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                }

                echo "<script>alert('Su solicitud ha sido ingresada satisfactoriamente, esté pendiente de su revisión y aprobación.')</script>";
                echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
            } else {
                echo "<script>alert('Error de solicitud, su fecha/hora de salida o inicio debe ser mayor ala fecha/hora de regreso o final')</script>";
                echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
            }
        } else {
            echo "<script>alert('Error de solicitud, no puede ingresar solicitudes que necesita antes de la fecha actual')</script>";
            echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
        }
        break;

    case 'editar':
        $codigosolicitud = $_POST['codsolicitudeditar'];
        $coddepartamento = $_POST['departamentosolicitudeditar'];
        $codsolicitud = $_POST['tiposolicitudeditar'];
        $codempleado = $_SESSION['cod_empleado'];
        $fechaingreso = $_POST['fechahorasolicitudeditar'];
        $fechahorainicio = $_POST['fechainiciosolicitudeditar'];
        $fechahorafinal = $_POST['fechafinalsolicitudeditar'];
        $justificacion = $_POST['justificacionsolicitudeditar'];
        $acuentade = $_POST['acuentadesolicitudeditar'];

        $fechaactual = date("Y-n-j H:i:s");

        if ((strtotime($fechaactual)) <= (strtotime($fechahorainicio))) {
            if ((strtotime($fechahorainicio)) <= (strtotime($fechahorafinal))) {
                $sql = "UPDATE `tbl_solicitudes` SET id_tipo_solicitud='$codsolicitud',cod_departamento='$coddepartamento',cod_empleado='$codempleado', fechahora_ingreso='$fechaingreso', fecha_inicio='$fechahorainicio', fecha_fin='$fechahorafinal', justificacion='$justificacion', cod_estado=3,opcion_permiso='$acuentade' WHERE id_solicitud='$codigosolicitud'";
                $resultado = $mysqli->query($sql);

                if ($codsolicitud == 1) {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=19";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                } else {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=20";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                }

                echo "<script>alert('Su solicitud ha sido actualizada satisfactoriamente, esté pendiente de su revisión y aprobación nuevamente.')</script>";
                echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
            } else {
                echo "<script>alert('Error de actualizaciòn, su fecha/hora de salida o inicio debe ser mayor ala fecha/hora de regreso o final')</script>";
                echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
            }
        } else {
            echo "<script>alert('Error de actualizaciòn, no puede ingresar solicitudes que necesita antes de la fecha actual')</script>";
            echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
        }
        break;

    case 'eliminar':
        if (isset($_POST['id'])) {
            $id = $_POST['id'];

            $sql = "DELETE FROM tbl_solicitudes                 
            WHERE id_solicitud = $id;";

            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=21";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = $row5['objeto'];
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            if ($conexion->query($sql)) {
                echo "<script>alert('La solicitud ha sido eliminada de forma exitosa.')</script>";
                echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
            } else {
                echo "<script>alert('Error')</script>";
                echo "<script>setTimeout(\"location.href='../empleadosgestiones'\",1)</script>";
            }
        }
}
