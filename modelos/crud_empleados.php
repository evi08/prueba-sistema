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
        function function_alert($message)
        {
            echo "<script>alert('$message');</script>";
        }
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime('+6 month', strtotime($fecha));
        $nuevafecha = date('Y-m-j', $nuevafecha);

        $pnombre = $_POST['ingresoprimernombre'];
        $snombre = $_POST['ingresosegundonombre'];
        $papellido = $_POST['ingresoprimerapellido'];
        $sapellido = $_POST['ingresosegundoapellido'];
        $correop = $_POST['ingresocorreopersonal'];
        $correoe = $_POST['ingresocorreoempresa'];
        $telefono = $_POST['ingresotelefono'];
        $direccion = $_POST['ingresodireccion'];
        $identidad = $_POST['ingresoidentidad'];
        $contratacion = $_POST['ingresocontratacion'];
        $nacimiento = $_POST['ingresonacimiento'];
        $horario = $_POST['diastrabajoañadir']." ".$_POST['horariodetrabajoentrada'] . "-" . $_POST['horariodetrabajosalida'];
        $especialidad = $_POST['especialidadañadir'];
        $Puesto = $_POST['puestoañadir'];
        $tipo_Contrato = $_POST['contratoañadir'];
        $cod_departamento = $_POST['departamentoañadir'];


        if (preg_match("/^[0-9]{4}-[0-9]{4}$/", $telefono)) {
            $telefono = "504" . " " . $telefono;
            if (preg_match("/^[0-9]{4}-[0-9]{4}-[0-9]{5}$/", $identidad)) {
                $sql4 = "SELECT cod_empleado from tbl_empleados WHERE correo_personal='$correop' or telefono='$telefono'or identidad='$identidad'";
                $resultado4 = $mysqli->query($sql4);
                $num4 = $resultado4->num_rows;
                if ($num4 > 0) {
                    echo "<script>alert('Este empleado o datos ya están registrados.Télefono/correo personal o identidad ya registrados.')</script>";
                    echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
                } else {
                    $sql2 = "INSERT INTO `tbl_empleados` (`primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo_personal`, `correo_empresa`, `telefono`, `direccion`, `identidad`, `fecha_contratacion`, `fecha_nacimiento`, `cod_tipo_contrato`, `horario_trabajo_empleado`, `fecha_baja_empleado`, `razon_baja_empleado`, `numero_permisos_empleado`, `cod_de_especialidad_empleado`, `cod_puesto_empleado`) 
                        VALUES ('$pnombre', '$snombre', '$papellido','$sapellido', '$correop', '$correoe', '$telefono', '$direccion', '$identidad', '$contratacion', '$nacimiento', '$tipo_Contrato', '$horario', NULL, NULL, 0, $especialidad, $Puesto)";
                    $resultado2 = $mysqli->query($sql2);

                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=12";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = $row5['objeto'];
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);

                    $sql5 = "SELECT * FROM tbl_empleados where correo_personal='$correop'";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;
                    if ($num5 > 0) {
                        $row5 = $resultado5->fetch_assoc();
                        $id_empleado = $row5['cod_empleado'];
                        $sql = "INSERT INTO `tbl_empleados_departamentos` (`cod_empleado`, `cod_departamento`) 
                        VALUES ('$id_empleado', '$cod_departamento' )";
                        $resultado = $mysqli->query($sql);
                    }

                    echo "<script>alert('El empleado fué registrado correctamente al sistema.')</script>";
                    echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
                }
            } else {
                echo "<script>alert('El número de identidad no es válido,ejemplo identidad válida:0809-1991-00421')</script>";
                echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
            }
        } else {
            echo "<script>alert('El número de télefono no es válido,ejemplo télefono válida:8842-7046')</script>";
            echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
        }
        break;

    case 'editar':
        $pnombre = $_POST['ingresoprimernombreeditar'];
        $snombre = $_POST['ingresosegundonombreeditar'];
        $papellido = $_POST['ingresoprimerapellidoeditar'];
        $sapellido = $_POST['ingresosegundoapellidoeditar'];
        $correop = $_POST['ingresocorreopersonaleditar'];
        $correoe = $_POST['ingresocorreoempresaeditar'];
        $telefono = $_POST['ingresotelefonoeditar'];
        $direccion = $_POST['ingresodireccioneditar'];
        $identidad = $_POST['ingresoidentidadeditar'];
        $contratacion = $_POST['ingresocontratacioneditar'];
        $nacimiento = $_POST['ingresonacimientoeditar'];
        $horario = $_POST['diastrabajoeditar']." ".$_POST['horariodetrabajoentradaeditar'] . "-" . $_POST['horariodetrabajosalidaeditar'];
        $especialidad = $_POST['especialidadeditar'];
        $Puesto = $_POST['puestoeditar'];
        $tipo_Contrato = $_POST['contratoeditar'];
        $cod_departamento = $_POST['departamentoeditar'];
        $razonbaja = $_POST['razonbajaeditar'];
        $fechabaja = $_POST['fechabajaeditar'];


        $codempleado = $_POST['codempleadoeditar'];
        if (preg_match("/^[0-9]{4}-[0-9]{4}$/", $telefono)) {
            $telefono = "504" . " " . $telefono;
            if (preg_match("/^[0-9]{4}-[0-9]{4}-[0-9]{5}$/", $identidad)) {
                $sql = "UPDATE tbl_empleados
                SET primer_nombre ='$pnombre', segundo_nombre='$snombre', segundo_apellido='$sapellido', primer_apellido='$papellido', correo_personal='$correop',correo_empresa='$correoe', telefono='$telefono',direccion='$direccion',identidad='$identidad',fecha_contratacion='$contratacion',fecha_nacimiento='$nacimiento',cod_tipo_contrato='$tipo_Contrato',horario_trabajo_empleado='$horario',fecha_baja_empleado='$fechabaja',razon_baja_empleado='$razonbaja',cod_de_especialidad_empleado='$especialidad',cod_puesto_empleado='$Puesto' WHERE cod_empleado = '$codempleado'";
                $resultado = $mysqli->query($sql);

                $sql = "UPDATE tbl_empleados_departamentos SET cod_departamento='$cod_departamento' where cod_empleado='$codempleado'";
                $resultado = $mysqli->query($sql);

                $sql5 = "SELECT * FROM tbl_objetos where id_objeto=14";
                $resultado5 = $mysqli->query($sql5);
                $num5 = $resultado5->num_rows;

                $row5 = $resultado5->fetch_assoc();
                $id_objeto = $row5['id_objeto'];
                $accion = $row5['objeto'];
                $descripcion = $row5['descripcion_objeto'];
                event_bitacora($id_objeto, $accion, $descripcion);

                echo "<script>alert('El empleado fué actualizado correctamente al sistema.')</script>";
                echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
            } else {
                echo "<script>alert('El número de identidad no es válido,ejemplo identidad válida:0809-1991-00421')</script>";
                echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
            }
        } else {
            echo "<script>alert('El número de télefono no es válido,ejemplo télefono válida:8842-7046')</script>";
            echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
        }
        break;

    case 'eliminar':
        $codempleadoeliminar = $_POST['codeliminarempleado'];
        try {
            $sqleliminarempleado = "DELETE FROM tbl_empleados                 
            WHERE cod_empleado = '$codempleadoeliminar'";
            $resultadoeliminarempleado = $mysqli->query($sqleliminarempleado);

            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=13";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = $row5['objeto'];
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            echo "<script>alert('El empleado fué eliminado satisfactoriamente.')</script>";
            echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
        } catch (Exception) {
            echo "<script>alert('Error de eliminación empleado. El empleado no puede ser eliminado debido a su relación con otros registros(Módulos, usuarios etc)')</script>";
            echo "<script>setTimeout(\"location.href='../empleados'\",1)</script>";
        }
}
