
<?php
include_once "../../modelos/conexion.php";
include_once "../../modelos/bitacora.php";
session_start();

$sql5 = "SELECT * FROM tbl_objetos where id_objeto=6";
 $resultado5 = $mysqli->query($sql5);
 $num5 = $resultado5->num_rows;

 
if(isset($_POST['recuperarpassword']) && $_POST['recuperar']==1 && !empty($_POST['usuario'])) {
    $usuarionombre=$_POST['usuario'];
    $opcion=$_POST['recuperar'];

    $mysql = "SELECT *FROM tbl_usuarios_login where nombre_usuario_correo='$usuarionombre'";
    $resultado = $mysqli->query($mysql);
    $num = $resultado->num_rows;
    if($num>0){
    $row = $resultado->fetch_assoc();
    $_SESSION['id'] = $row['cod_usuario']; 
    $_SESSION['usuario'] = $row['nombre_usuario_correo']; 

    $row5 = $resultado5->fetch_assoc();
    $id_objeto=$row5['id_objeto'];
    $accion=$row5['objeto'];
    $descripcion= $row5['descripcion_objeto'];
    event_bitacora($id_objeto, $accion,$descripcion);

    echo "<script> 
    alert ('Será redireccionado a recuperación de contraseña por pregunta secreta');
    location.href ='recuperacion_password_preguntas.php';</script>";
    }else{
        echo ("<div class='alert alert-danger'>El usuario que usted ingresó no existe</div>");
    }
}else{
    if(isset($_POST['recuperarpassword']) && $_POST['recuperar']==2 && !empty($_POST['usuario'])) {
        $sql5 = "SELECT * FROM tbl_objetos where id_objeto=8";
        $resultado5 = $mysqli->query($sql5);
        $num5 = $resultado5->num_rows;
        
        $_SESSION['usuario'] = $_POST['usuario'];
        $row5 = $resultado5->fetch_assoc();
        $id_objeto=$row5['id_objeto'];
        $accion=$row5['objeto'];
        $descripcion= $row5['descripcion_objeto'];
        event_bitacora($id_objeto, $accion,$descripcion);

        echo "<script> 
        location.href ='recuperar_password.php';</script>"; 
        
    }else{
        echo ("<div class='alert alert-danger'>Por favor llene el campo de usuario</div>");
    }
} 
?>


<script>
   function evitarespeciales(e){
        key=e.keyCode || e.which;
        tecla=String.fromCharCode(key).toString();
        letras="ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789@.";
        especiales=[8,13];
        tecla_especial=false;
        for(var i in especiales){
            if(key==especiales[i]){
                tecla_especial=true;
                break;
            }
        }
        if(letras.indexOf(tecla)==-1 && !tecla_especial){
            swal.fire({icon:'info',
                tittle:'dato',
                text:'Intenta ingresar un valor no permitido'});
            return false;
        }
    }

    function enviar(destino) {
        document.formulario.action = destino
        document.formulario.submit();
    }
</script>

<!DOCTYPE html5>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Validación de usuarios</title>

  <!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
<link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
<link rel="stylesheet" href="../dist/css/adminlte.css">
</head>
<body class="hold-transition login-page" oncopy="return false" onpaste="return false">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Sistema SGRS-NPH</b></a>
  </div>
  <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"><span class="fas fa-users"></span> Recuperación de contraseña</p>
                                    <form name="formulario" method="post">
                                        <select name="recuperar" id="recuperar" >
                                            <option value="1">Recuperación por pregunta.</option>
                                            <option value="2">Recuperación por correo.</option>
                                        </select>
                                        <div class="form-group">
                                            <h8>*Si solicita por correo, revise su bandeja de entrada, posteriormente ingrese con sus nuevas credenciales</h8> <br>
                                            <label class="small mb-1" for="usuario">Ingrese su correo o usuario</label>
                                            <input class="form-control py-4" id="usuario" type="text" name="usuario" autocomplete="off" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Ingrese su usuario" required/>
                                        </div>

                                        <div class="form-group d-flex align-items-center justify-content-center my-3">
                                            <button type="submit" name="recuperarpassword" id="recuperarpassword" class="btn btn-primary form-control" >Siguiente</button>
                                        </div>

                                        <div class="text-center">
                                            <div class="small"><a href="../../index.php">Regresar a inicio de sesion</a></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="register.php">Necesitas cuenta en NPH?</a></div>
                                </div>
        </div>
    </div>
    <!-- /.login-card-body -->
</div>
    <script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                         
</body>

</html>