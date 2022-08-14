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

//cod_empleado
$id_empleado = $row_usuario['cod_empleado'];
$cod_departamento = $row_usuario['cod_departamento'];
$hora = date('Y-m-d H:i:s', time());


switch ($_GET['op']) {
    case 'añadir':
        $hogar = $_POST['hogar'];
        $justificacion = $_POST['justificacion'];
        $nfilas = $_POST['nfilas'];

        for ($i = 1; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {
                $producto = $_POST['producto' . $i];
                $sql = "SELECT `id_producto` FROM `tbl_productos` WHERE `nombre_producto` = '$producto';";
                if ($conexion->query($sql)->num_rows == 0) {
                    echo "<script>alert('El producto $producto no se en cuentra en la base de datos')</script>";
                    echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                    die;
                }
            }
        }
        $sql = "INSERT INTO `tbl_solicitudes`(`id_solicitud`, `id_tipo_solicitud`, `cod_departamento`, `cod_empleado`, `fechahora_ingreso`, `fecha_fin`, `fecha_inicio`, `justificacion`, `opcion_permiso`, `cod_estado`) 
        VALUES ('','7','$cod_departamento', '$id_empleado', '$hora' ,'','','$justificacion','','3');";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio una nueva solicitud";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            // echo "Solicitud insertada con exito";
        } else {
            echo "<script>alert('Error al inserta solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die;
        }

        $sql = "SELECT `id_solicitud` FROM `tbl_solicitudes` 
        WHERE `id_tipo_solicitud` = 7 AND `cod_empleado` = $id_empleado AND `fechahora_ingreso` = '$hora';";
        if ($conexion->query($sql)) {
            $resultado = $conexion->query($sql);
            $row = $resultado->fetch_assoc();
            $id_solicitud = $row['id_solicitud'];
            // echo "<br>Solicitud encontrada con exito";
        } else {
            echo "<script>alert('Error al buscar solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die();
        }

        $sql = "INSERT INTO `tbl_requisicion_activos`(`cod_requisicion`, `id_hogar`, `id_solicitud`, `fecha_ingreso`) 
        VALUES ('','$hogar','$id_solicitud', '$hora');";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio una nueva requisicion";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            // echo "<br>Requisicion insertado con exito";
        } else {
            echo "<script>alert('Error al insertar compra')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die();
        }

        $sql = "SELECT `cod_requisicion` FROM `tbl_requisicion_activos` WHERE `id_solicitud` =  $id_solicitud;";
        if ($conexion->query($sql)) {
            $resultado = $conexion->query($sql);
            $row = $resultado->fetch_assoc();
            $cod_requisicion = $row['cod_requisicion'];
            // echo "<br>Compra encontrada con exito";
        } else {
            echo "<script>alert('Error al encontrar compra')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die();
        }

        for ($i = 1; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {

                $producto = $_POST['producto' . $i];
                $cantidad = $_POST['cantidad' . $i];

                $sql = "INSERT INTO `tbl_detalle_requisiciones`(`id_detalle_requisicion`, `cod_requisicion`, `id_producto`, `cantidad`, `descripcion`) 
                VALUES ('', '$cod_requisicion',
                (SELECT id_producto FROM tbl_productos WHERE tbl_productos.nombre_producto = '$producto'),'$cantidad','')";
                if ($conexion->query($sql)) {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario añadio un nuevo detalle requisicion";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                    // echo "<br>Detalle $i insertado con exito";
                } else {
                    echo "<script>alert('Error al insertar detalle $i')</script>";
                    echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                    die;
                }
            } else {
                // echo "falta<br>";
            }
        }
        echo "<script>alert('Solicitud ingresada con exito')</script>";
        echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";

        // print_r($_POST);
        // echo '<br>';
        // print_r($_GET);
        // echo '<br>';
        // echo $hora;
         break;

    case 'editar':
        $id = $_POST['id'];
        //[id] => 170 [hogar] => 16 [justificacion] => 2022-08-10 17:08:28 [estado] => 3 [nfilas]
        $hora = date('Y-m-d H:i:s', time());
        $justificacion = $_POST['justificacion'];
        $estado = $_POST['estado'];
        $nfilas = $_POST['nfilas'];
        $hogar = $_POST['hogar'];

        for ($i = 1; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {
                $producto = $_POST['producto' . $i];
                $sql = "SELECT `id_producto` FROM `tbl_productos` WHERE `nombre_producto` = '$producto';";
                if ($conexion->query($sql)->num_rows == 0) {
                    echo "<script>alert('El producto $producto no se en cuentra en la base de datos')</script>";
                    echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                    die;
                }
            }
        }

        $sql = "UPDATE `tbl_solicitudes` 
        SET `fecha_fin`='', `justificacion`='$justificacion'
        WHERE `id_solicitud` = '$id';";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito  una  solicitud";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            //echo "<br>Solicitud modificada con exito";
        } else {
            echo "<script>alert('Error al insertar detalle')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die;
        }

        $sql = "DELETE FROM `tbl_requisicion_activos` WHERE `id_solicitud` = $id;";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario edito una  requisicion";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            //echo "<br>Compra eliminada con exito";
        } else {
            echo "<script>alert('Error al insertar detalle')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die;
        }

        $sql = "INSERT INTO `tbl_requisicion_activos`(`cod_requisicion`, `id_hogar`, `id_solicitud`, `fecha_ingreso`) 
        VALUES ('','$hogar','$id', '$hora');";
        if ($conexion->query($sql)) {
            //echo "<br>Compra insertada con exito";
        } else {
            echo "<script>alert('Error al insertar compra')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die();
        }


        for ($i = 0; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {
                $producto = $_POST['producto' . $i];
                $cantidad = $_POST['cantidad' . $i];

                $sql = "INSERT INTO `tbl_detalle_requisiciones`(`id_detalle_requisicion`, `cod_requisicion`, `id_producto`, `cantidad`) 
                VALUES ('',
                (SELECT c.cod_requisicion FROM tbl_requisicion_activos c WHERE c.id_solicitud = $id), 
                (SELECT id_producto FROM tbl_productos WHERE nombre_producto = '$producto'), '$cantidad');";
                if ($conexion->query($sql)) {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario edito un detalle requisicion";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                    //echo "<br>Detalle $i insertado con exito";
                } else {
                    echo "<script>alert('Error al insertar detalle $i')</script>";
                    echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                    die;
                }
            } else {
                //echo "falta<br>";
            }
        }

        /* print_r($_GET);
        echo '<br>';
        print_r($_POST);
        echo 'añadir'; */
        break;

    case 'eliminar':
        $id = $_POST['id'];
        $sql = "DELETE FROM `tbl_solicitudes` WHERE id_solicitud = '$id';";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=57";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una solicitud de requiciones";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Solicitud eliminada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
        } else {
            echo "<script>alert('Error al eliminar detalle')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
        }

        //print_r($_POST);

        break;

    case 'cargar':
        $id = $_GET['id'];
        $sql = "SELECT p.nombre_producto producto, SUM(d.cantidad) cantidad 
        FROM tbl_detalle_requisiciones d, tbl_productos p, tbl_requisicion_activos c, tbl_solicitudes s 
        WHERE p.id_producto = d.id_producto AND d.cod_requisicion = c.cod_requisicion AND 
        c.id_solicitud = s.id_solicitud AND s.id_solicitud = $id GROUP BY p.nombre_producto;";

        echo json_encode($conexion->query($sql)->fetch_all(MYSQLI_ASSOC));

        break;

    case 'confirmar':
        
        $id = $_POST['id'];
        $estado = $_POST['estado'];

        /* RECHAZO DE SOLICITUD */
        if ($estado == 2) {
            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 2 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
                echo "<script>alert('Solicitud modificada con exito')</script>";
                echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                die;
            } else {
                echo "<script>alert('Error al modificar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                die;
            }
        }

        /* BUSQUEDA DE DETALLE */
        $sql = "SELECT d.id_producto, SUM(d.cantidad) cantidad, i.cantidad inventario, p.capacidad_min, p.capacidad_max 
                FROM tbl_detalle_requisiciones d, tbl_solicitudes s, tbl_requisicion_activos c, tbl_productos p, tbl_inventarios i 
                WHERE s.id_solicitud = c.id_solicitud AND c.cod_requisicion = d.cod_requisicion AND p.id_producto = d.id_producto 
                AND d.id_producto = i.id_producto AND s.id_solicitud = $id 
                GROUP BY d.id_producto;";

        if ($resultado = $conexion->query($sql)) {
            // echo "Requisicion encontrada con exito<br>";
            if ($conexion->query($sql)->num_rows < 1) {
                echo "<script>alert('Compra no contiene detalles')</script>";
                echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                die;
            }
        } else {
            echo "<script>alert('Error encontrar detalles')</script>";
            echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            die;
        }

        if ($estado == 1) {
            while ($row = $resultado->fetch_assoc()) {
                $conteo = 0;
                $mensaje = '';
                $cantidad = $row['cantidad'];
                $inventario = $row['inventario'];
                $id_producto = $row['id_producto'];
                if ($inventario - $cantidad < 0) {
                    $sql_producto = "SELECT nombre_producto FROM tbl_productos WHERE id_producto = $id_producto";
                    $row_producto = $conexion->query($sql_producto)->fetch_assoc();
                    $mensaje = $mensaje . ' ' . $row_producto['nombre_producto'];
                    $conteo++;
                }
            }
            if ($conteo > 0) {
                echo "<script>alert('No se encuentran existencias de los siguientes productos para ser devueltos: $mensaje')</script>";
                echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                die;
            }

            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 1 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
                // echo "Solicitud modificada con exito";
            } else {
                echo "<script>alert('Error al modificar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            }

            /* ACTUALIZACION DE INVENTARIO */

            if ($resultado = $conexion->query($sql)) {
                // echo "Detalles encontrados con exito<br>";                
            } else {
                echo "<script>alert('Error encontrar detalles')</script>";
                echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
            }
            while ($row = $resultado->fetch_assoc()) {
                $cantidad = $row['cantidad'];
                $inventario = $row['inventario'];
                $capacidad_min = $row['capacidad_min'];
                $capacidad_max = $row['capacidad_max'];
                $id_producto = $row['id_producto'];

                $sql_inventario = "UPDATE `tbl_inventarios` SET `cantidad` = (`cantidad` - $cantidad) WHERE `id_producto`= $id_producto;";
                if ($conexion->query($sql_inventario)) {
                    // echo "'Inventario modificado con exito'<br>";
                    $sql_kardex = "INSERT INTO `tbl_kardex`( `Producto`, `Cantidad_Producto`, `cod_empleado`, `Fecha_Hora`, `Tipo_Movimiento`) 
                VALUES ('$id_producto', $cantidad, $id_empleado, now(), 'SALIDA');";
                    if ($conexion->query($sql_kardex)) {
                        // echo "Kardex modificado con exito";
                    } else {
                        // echo "Error al modificar kardex<br>";
                        die;
                    }
                } else {
                    echo "<script>alert('Error al modificar inventario')</script>";
                    // echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                }
                /* INSERCION EN BITACORA */
            }
        }

        if ($estado == 3) {
            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 2 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
                // echo "Solicitud modificada con exito";
            } else {
                echo "<script>alert('Error al modificar solicitud')</script>";
                //echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            }

            while ($row = $resultado->fetch_assoc()) {
                $cantidad = $row['cantidad'];
                $inventario = $row['inventario'];
                $capacidad_min = $row['capacidad_min'];
                $capacidad_max = $row['capacidad_max'];
                $id_producto = $row['id_producto'];

                $sql = "UPDATE `tbl_inventarios` SET `cantidad` = (`cantidad` + $cantidad) WHERE `id_producto`= $id_producto;";
                if ($conexion->query($sql)) {
                    //echo "Inventario modificado con exito";
                    $sql = "INSERT INTO `tbl_kardex`( `Producto`, `Cantidad_Producto`, `cod_empleado`, `Fecha_Hora`, `Tipo_Movimiento`) 
                VALUES ('$id_producto', $cantidad, $id_empleado, now(), 'ENTRADA');";
                    if ($conexion->query($sql)) {
                        //echo "Kardex modificado con exito";
                    } else {
                        echo "<script>alert('Error al modificar inventario')</script>";
                        echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                        die;
                    }
                } else {
                    echo "<script>alert('Error al modificar inventario')</script>";
                    echo "<script>setTimeout(\"location.href='../requisiciones'\",1)</script>";
                    die;
                }
            }
        }
        break;
}
