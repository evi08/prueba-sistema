<?php

require 'conexion.php';
require 'bitacora.php';

session_start();

$conexion = $mysqli;

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

date_default_timezone_set('America/Tegucigalpa');
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

        $justificacion = $_POST['justificacion'];
        $proveedor = $_POST['proveedor'];
        $nfilas = $_POST['nfilas'];
        $total_detalle = $_POST['total_detalle'];

        $justificacion = $_POST['justificacion'];
        $proveedor = $_POST['proveedor'];
        $nfilas = $_POST['nfilas'];
        $total_detalle = $_POST['total_detalle'];

        for ($i = 1; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {
                $producto = $_POST['producto' . $i];
                $sql = "SELECT `id_producto` FROM `tbl_productos` WHERE `nombre_producto` = '$producto';";
                if ($conexion->query($sql)->num_rows == 0) {
                    echo "<script>alert('El producto $producto no se en cuentra en la base de datos')</script>";
                    echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                    die;
                }
            }
        }

        /* QUERYS */
        /* BITACORA INSERTAR */
        $sql = "INSERT INTO `tbl_solicitudes`(`id_solicitud`, `id_tipo_solicitud`, `cod_departamento`, `cod_empleado`, `fechahora_ingreso`, `fecha_fin`, `fecha_inicio`, `justificacion`, `opcion_permiso`, `cod_estado`) 
        VALUES ('','3','$cod_departamento', '$id_empleado', '$hora' ,'','','$justificacion','','3');";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio una nueva solicitud";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);

            //echo "<br>Solicitud insertada con exito";
        } else {
            echo "<script>alert('Error al inserta solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die;
        }

        $sql = "SELECT `id_solicitud` FROM `tbl_solicitudes` 
        WHERE `id_tipo_solicitud` = 3 AND `cod_empleado` = $id_empleado AND `fechahora_ingreso` = '$hora';";
        if ($conexion->query($sql)) {
            $resultado = $conexion->query($sql);
            $row = $resultado->fetch_assoc();
            $id_solicitud = $row['id_solicitud'];
            //echo "<br>Solicitud encontrada con exito";
        } else {
            echo "<script>alert('Error al buscar solicitud')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die();
        }

        /* BITACORA INSERTAR */
        $sql = "INSERT INTO `tbl_compras`(`id_compra`, `id_solicitud`, `cod_proveedor`, `fecha_compra`, `fechahora_ingreso_solicitud`, `total_pagar`) 
        VALUES ('','$id_solicitud','$proveedor','','$hora','$total_detalle')";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario añadio una nueva compra";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            //echo "<br>Compra insertado con exito";
        } else {
            echo "<script>alert('Error al insertar compra')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die();
        }

        $sql = "SELECT `id_compra` FROM `tbl_compras` WHERE `id_solicitud` = $id_solicitud;";
        if ($conexion->query($sql)) {
            $resultado = $conexion->query($sql);
            $row = $resultado->fetch_assoc();
            $id_compra = $row['id_compra'];
            //echo "<br>Compra encontrada con exito";
        } else {
            echo "<script>alert('Error al encontrar compra')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die();
        }

        for ($i = 1; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {

                $producto = $_POST['producto' . $i];
                $cantidad = $_POST['cantidad' . $i];
                $precio_compra = $_POST['precio' . $i];

                /* BITACORA INSERTAR */
                $sql = "INSERT INTO `tbl_detalle_compra`(`id_detalle_compra`, `id_compra`, `id_producto`, `cantidad`, `precio_compra`) 
                VALUES ('','$id_compra', (SELECT id_producto FROM tbl_productos WHERE nombre_producto = '$producto'), '$cantidad', '$precio_compra');";
                if ($conexion->query($sql)) {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario añadio un nuevo detalle compra";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);                  
                } else {
                    echo "<script>alert('Error al insertar detalle $i')</script>";
                    echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                    die;
                }
            } else {
                
            }
        }/* / QUERYS */
        echo "<script>alert('Solicitud añadida con exito')</script>";
        echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";

        break;

    case 'editar':
        $id = $_POST['id'];
        $justificacion = $_POST['justificacion'];
        $proveedor = $_POST['proveedor'];
        $estado = $_POST['estado'];
        $nfilas = $_POST['nfilas'];
        $total_detalle = $_POST['total_detalle'];

        for ($i = 1; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {
                $producto = $_POST['producto' . $i];
                $sql = "SELECT `id_producto` FROM `tbl_productos` WHERE `nombre_producto` = '$producto';";
                if ($conexion->query($sql)->num_rows == 0) {
                    echo "<script>alert('El producto $producto no se en cuentra en la base de datos')</script>";
                    echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                    die;
                }
            }
        }

        /* BIACORA ACTUALIZAR */
        $sql = "UPDATE `tbl_solicitudes` 
        SET `fecha_fin`='', `justificacion`='$justificacion'
        WHERE `id_solicitud` = '$id';";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario actualizo una nueva solicitud";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);            
        } else {
            echo "<script>alert('Error al insertar detalle')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die;
        }

        $sql = "DELETE FROM `tbl_compras` WHERE `id_solicitud` = $id;";
        if ($conexion->query($sql)) {
            
        } else {
            echo "<script>alert('Error al insertar detalle')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die;
        }

        /* BITACORA ACTUALIZAR */
        $sql = "INSERT INTO `tbl_compras`(`id_compra`, `id_solicitud`, `cod_proveedor`, `fecha_compra`, `fechahora_ingreso_solicitud`, `total_pagar`) 
        VALUES ('','$id','$proveedor','','$hora','$total_detalle')";
        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario actualizo una compra";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);            
        } else {
            echo "<script>alert('Error al insertar compra')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            die();
        }

        for ($i = 0; $i <= $nfilas; $i++) {
            if (isset($_POST['producto' . $i])) {
                $producto = $_POST['producto' . $i];
                $cantidad = $_POST['cantidad' . $i];
                $precio_compra = $_POST['precio' . $i];

                /* BITACORA ACTUALIZAR */
                $sql = "INSERT INTO `tbl_detalle_compra`(`id_detalle_compra`, `id_compra`, `id_producto`, `cantidad`, `precio_compra`) 
                VALUES ('',
                (SELECT c.id_compra FROM tbl_compras c WHERE c.id_solicitud = $id), 
                (SELECT id_producto FROM tbl_productos WHERE nombre_producto = '$producto'), '$cantidad', '$precio_compra');";
                if ($conexion->query($sql)) {
                    $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
                    $resultado5 = $mysqli->query($sql5);
                    $num5 = $resultado5->num_rows;

                    $row5 = $resultado5->fetch_assoc();
                    $id_objeto = $row5['id_objeto'];
                    $accion = "El usuario actualizo un nuevo detalle compra";
                    $descripcion = $row5['descripcion_objeto'];
                    event_bitacora($id_objeto, $accion, $descripcion);
                    //echo "<br>Detalle $i insertado con exito";
                } else {
                    echo "<script>alert('Error al insertar detalle $i')</script>";
                    echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                    die;
                }
            } else {
                
            }
        }
        echo "<script>alert('Solicitud editada con exito')</script>";
        echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";

        break;

    case 'eliminar':
        $id = $_POST['id'];

        /* BITACORA ELIMINAR */
        $sql = "DELETE FROM `tbl_solicitudes` WHERE id_solicitud = '$id';";

        if ($conexion->query($sql)) {
            $sql5 = "SELECT * FROM tbl_objetos where id_objeto=55";
            $resultado5 = $mysqli->query($sql5);
            $num5 = $resultado5->num_rows;

            $row5 = $resultado5->fetch_assoc();
            $id_objeto = $row5['id_objeto'];
            $accion = "El usuario elimino una solicitud compra";
            $descripcion = $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion, $descripcion);
            echo "<script>alert('Solicitud eliminada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
        } else {
            echo "<script>alert('Error al eliminar detalle')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
        }

        break;

    case 'cargar':
        $id = $_GET['id'];
        $sql = "SELECT p.nombre_producto producto, d.cantidad cantidad, d.precio_compra 
        FROM tbl_detalle_compra d, tbl_productos p, tbl_compras c, tbl_solicitudes s 
        WHERE p.id_producto = d.id_producto AND d.id_compra = c.id_compra AND c.id_solicitud = s.id_solicitud AND s.id_solicitud = $id";

        echo json_encode($conexion->query($sql)->fetch_all(MYSQLI_ASSOC));

        break;

    case 'confirmar':        
        $id = $_POST['id'];
        $estado = $_POST['estado'];

        if ($estado == 2) {

            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 2 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
                /* BITACORA ACTUALIZAR */
                echo "<script>alert('Solicitud rechazada con exito')</script>";
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                die;
            } else {
                echo "<script>alert('Error al rechazar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                die;
            }
        }

        $sql = "SELECT d.id_producto, SUM(d.cantidad) cantidad, i.cantidad inventario, p.capacidad_min, p.capacidad_max 
        FROM tbl_detalle_compra d, tbl_solicitudes s, tbl_compras c, tbl_productos p, tbl_inventarios i 
        WHERE s.id_solicitud = c.id_solicitud AND c.id_compra = d.id_compra AND p.id_producto = d.id_producto 
        AND d.id_producto = i.id_producto AND s.id_solicitud = $id 
        GROUP BY d.id_producto;";

        if ($resultado = $conexion->query($sql)) {
            if ($conexion->query($sql)->num_rows < 1) {
                echo "<script>alert('Compra no contiene detalles')</script>";
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                die;
            }
        } else {
            echo "<script>alert('Error encontrar detalles')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
        }


        if ($estado == 1) {
            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 1 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
                /* BITACORA ACTUALIZAR */
            } else {
                echo "<script>alert('Error al modificar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
            }

            while ($row = $resultado->fetch_assoc()) {
                $cantidad = $row['cantidad'];
                $inventario = $row['inventario'];
                $capacidad_min = $row['capacidad_min'];
                $capacidad_max = $row['capacidad_max'];
                $id_producto = $row['id_producto'];

                $sql = "UPDATE `tbl_inventarios` SET `cantidad` = (`cantidad` + $cantidad) WHERE `id_producto`= $id_producto;";
                if ($conexion->query($sql)) {
                    /* BITACORA ACTUALIZAR */

                    $sql_kardex = "INSERT INTO `tbl_kardex`(`id_producto`, `cantidad`, `cod_empleado`, `fecha_hora`, `tipo_movimiento`) 
                    VALUES ('$id_producto', $cantidad, $id_empleado, '$hora', 'ENTRADA');";
                    if ($conexion->query($sql_kardex)) {
                        /* BITACORA INSERTAR */
                    } else {
                        
                        echo "<script>alert('Error al modificar kardex ')</script>";
                        echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                        die;
                    }
                } else {
                    echo "<script>alert('Error al modificar inventario')</script>";
                    echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                    die;
                }
            }
            echo "<script>alert('Solicitud aceptada con exito')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
        }

        if ($estado == 3) {
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
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                die;
            }

            $sql_update = "UPDATE `tbl_solicitudes` SET `cod_estado`= 5 WHERE `id_solicitud` = $id;";
            if ($conexion->query($sql_update)) {
            } else {
                echo "<script>alert('Error al modificar solicitud')</script>";
                echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                die;
            }

            $resultado = $conexion->query($sql);
            while ($row = $resultado->fetch_assoc()) {
                $cantidad = $row['cantidad'];
                $inventario = $row['inventario'];
                $id_producto = $row['id_producto'];

                $sql_inventario = "UPDATE `tbl_inventarios` SET `cantidad` = (`cantidad` - $cantidad) WHERE `id_producto`= $id_producto;";
                if ($conexion->query($sql_inventario)) {
                    /* BITACORA ACTUALIZAR */

                    $sql_kardex = "INSERT INTO `tbl_kardex`(`id_producto`, `cantidad`, `cod_empleado`, `fecha_hora`, `tipo_movimiento`) 
                    VALUES ('$id_producto', $cantidad, $id_empleado, '$hora', 'SALIDA');";
                    if ($conexion->query($sql_kardex)) {
                        /* BITACORA INSERTAR */
                    } else {
                        $error = mysqli_error($conexion);
                        echo "<script>alert('Error al modificar kardex inventario: $error')</script>";
                        echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                        die;
                    }
                } else {
                    echo "<script>alert('Error al modificar inventario')</script>";
                    echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
                    die;
                }                
            }
            echo "<script>alert('Compra devuelta con exito')</script>";
            echo "<script>setTimeout(\"location.href='../compras'\",1)</script>";
        }
}
