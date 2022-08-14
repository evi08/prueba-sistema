<?php
require "modelos/conexion.php";
include "modelos/bitacora.php";
function function_alert($message)
{
    echo "<script>alert('$message');</script>";
}

$numusuario = "";

//PARTE SEGURIDAD
if (isset($_POST['ingresar'])) {

    $usuario = $_POST['nombre_usuario'];
    $password = $_POST['password'];

    $sql = "SELECT *FROM tbl_usuarios_login
  WHERE nombre_usuario_correo='$usuario'";
    //echo $sql;
    $resultado = $mysqli->query($sql);
    $num = $resultado->num_rows;

    if ($num > 0) {
        $row = $resultado->fetch_assoc();
        $password_bd = $row['clave_usuario'];
        $numusuario = $row['cod_usuario'];
        $ingresos = $row['numero_ingresos'];
        $rol = $row['id_rol_usuario'];
        $contestadas = $row['num_preguntas_contestadas'];

        $sql = "SELECT valor FROM tbl_parametros WHERE id_usuario=$numusuario and parametro='ADMIN_INTENTOS'";
        //echo $sql;
        $resultado4 = $mysqli->query($sql);
        $num4 = $resultado4->num_rows;
        $row4 = $resultado4->fetch_assoc();
        $intentos = $row4['valor'];

        $pass_c = sha1($password);
        if ($row['cod_estado'] == 2 or $row['cod_estado'] == 3) {
            echo ("<div class='alert alert-danger'>Error:Usuario inactivo ó bloqueado contacte al administrador para mayor información</div>");
        } else {
            if ($password_bd == $pass_c) {
                $sqlparametros = "SELECT * FROM tbl_parametros WHERE parametro='NOMBRE_EMPRESA'";
                $resultadoparametros = $mysqli->query($sqlparametros);
                $rowparametros = $resultadoparametros->fetch_assoc();

                $_SESSION['pasar_numero_usuario'] = $row['cod_usuario'];
                $_SESSION['session'] = 'ok';
                $_SESSION['nombreempresa'] =$rowparametros['valor'];
                $_SESSION['cod_empleado'] = $row['cod_empleado'];
                $_SESSION['id'] = $numusuario;
                $_SESSION['usuario'] = $row['nombre_usuario_correo'];
                $_SESSION['codigo_rol'] = $rol;
                $_SESSION['time'] = time();
                //Insersión en bitacora -->
                $id_objeto = 1;
                $accion = 'Inicio sesión';
                $descripcion = 'El usuario fue logeado correctamente';
                event_bitacora($id_objeto, $accion, $descripcion);
                if ($row['cod_estado'] == 4) {
                    echo "<script>
                    location.href='vistas/modulos/config_preguntas_seguridad.php';
                    </script>";
                } else {
                    $sql2 = "UPDATE tbl_usuarios_login SET numero_ingresos = numero_ingresos + 1, fecha_ultima_conexion= NOW(),fecha_ultima_conexion=now() WHERE cod_usuario= '$numusuario'";
                    $resultado2 = $mysqli->query($sql2);
                    echo "<script>
                    location.href='index.php';
                    </script>";
                }
            } else {
                if ($rol == 1) {
                    echo ("<div class='alert alert-danger'>Error:Contraseña y/o usuario incorrecto</div>");
                } else {
                    $sql2 = "SELECT valor FROM tbl_parametros WHERE id_usuario=$numusuario and parametro='ADMIN_INTENTOS'";
                    $resultado2 = $mysqli->query($sql2);
                    $num2 = $resultado2->num_rows;
                    $row2 = $resultado2->fetch_assoc();
                    $intentos = $row2['valor'];
                    if ($intentos > 0 && $rol != 1) {
                        echo ("<div class='alert alert-danger'>Error:Usuario y/o contraseña incorrecta.</div>");
                        $sql3 = "UPDATE tbl_parametros SET valor = valor-1 WHERE id_usuario= '$numusuario' and parametro='ADMIN_INTENTOS'";
                        $resultado3 = $mysqli->query($sql3);
                    } else {
                        echo ("<div class='alert alert-danger'>USUARIO BLOQUEADO, comuniquese con el administrador</div>");
                        $sql3 = "UPDATE tbl_usuarios_login SET cod_estado = 3 WHERE cod_usuario= '$numusuario'";
                        $resultado3 = $mysqli->query($sql3);
                    }
                }
            }
        }
    } else {
        echo ("<div class='alert alert-danger'>Nota:Este usuario no existe, registrese</div>");
    }
}
?>

<script>
    function solonumeros(evt) {
        if (window.event) {
            keynum = evt.keyCode;
        } else {
            keynum = evt.which;
        }
        if (keynum > 47 && keynum < 58 || keynum == 8 || keynum == 13) {
            return true;
        } else {
            alert("Para este campo solo son permitidos números.");
            return false;
        }
    }
    //onkeypress="return solonumeros(event);" agrgar esta propiedad
    function evitarespeciales(e) {
        key = e.keyCode || e.which;
        tecla = String.fromCharCode(key).toString();
        letras = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789.@";
        especiales = [8, 13];
        tecla_especial = false;
        for (var i in especiales) {
            if (key == especiales[i]) {
                tecla_especial = true;
                break;
            }
        }
        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            swal.fire({
                icon: 'info',
                tittle: 'dato',
                text: 'Intenta ingresar un valor no permitido'
            });
            return false;
        }
    }

    function evitarespacio(ev) {
        key = ev.keyCode || ev.which;
        tecla = String.fromCharCode(key).toString();
        letras = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789.*/+-,;_{[}]¿¡?=)(&%$#<>@!:";
        especiales = [8, 13];
        tecla_especial = false;
        for (var i in especiales) {
            if (key == especiales[i]) {
                tecla_especial = true;
                break;
            }
        }
        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            swal.fire({
                icon: 'info',
                tittle: 'dato',
                text: 'Intenta ingresar un valor no permitido'
            });
            return false;
        }
    }
</script>


<!DOCTYPE html5>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autentificación NPH</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
</head>

<body class="hold-transition login-page" oncopy="return false" onpaste="return false">
    <div class="login-box">
        <div class="login-logo">
            <img class="" src="vistas/dist/img/login_logo.png" alt="" height="60" width="60">
            <a><b>LOGIN SGRS-NPH</b></a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg"><span class="fas fa-users"></span> Autentificación de usuarios</p>
                <img class="" src="vistas/img/nph_encabezadologin.png" alt="" height="100" width="330">
                <form class="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <label for="mostrar_clave">Ingrese su usuario</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Ingrese su usuario" id="nombre_usuario" type="text" autocomplete="off" name="nombre_usuario" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" autofocus placeholder="Ingrese su usuario" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <label for="mostrar_clave">Ingrese su contraseña(Sin espacios)</label>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="password" type="password" autocomplete="off" name="password" onkeypress="return evitarespacio(event);" placeholder="Ingrese su contraseña" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="mostrar_clave" onclick="myFuction()">&nbsp;&nbsp;Mostrar contraseña</div>

                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0"><a class="small" style="background:white" href="vistas/modulos/password.php"><b>Olvidó su contraseña?</b></a>
                        <button class="btn btn-primary" type="submit" name="ingresar">Ingresar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <div class="small" style="background:white"><a href="vistas/modulos/register.php"><b>Necesita una cuentas? </b></a></div>
            </div>
        </div>
        <!-- /.login-card-body -->
    </div>
    <script type="text/javascript">
        function myFuction() {
            var x = document.getElementById("password");
            if (x.type == "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>