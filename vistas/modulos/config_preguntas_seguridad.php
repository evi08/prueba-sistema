<?php
require "../../modelos/conexion.php";
require "../../modelos/bitacora.php";
session_start();
$id = $_SESSION['id'];

$sql = "SELECT * FROM tbl_parametros where parametro = 'ADMIN_PREGUNTAS' and id_usuario='$id'";
$resultado = $mysqli->query($sql);
$num = $resultado->num_rows;

$sql5 = "SELECT * FROM tbl_objetos where id_objeto=4";
$resultado5 = $mysqli->query($sql5);
$num5 = $resultado5->num_rows;
if($num>0){
    $filas=$resultado->fetch_assoc();
    $pregu = $filas['valor'];
    $sql1= "SELECT * FROM tbl_preguntas_respuestas_usuarios where id_usuario = '$id'";
    $resultado1 = $mysqli->query($sql1);
    $num1 = $resultado1->num_rows;

    $sql2 = "SELECT * FROM tbl_preguntas_usuarios";
    $resultado2 = $mysqli->query($sql2);
    $num2 = $resultado2->num_rows;
    if ($pregu>$num1) {
        if($num2>0){
            if (isset($_POST['guardar'])) {
                $pregunta = $_POST['pregunta'];
                $resp = $_POST['respuesta'];
                $pregu = "SELECT * from tbl_preguntas_respuestas_usuarios where id_pregunta = '$pregunta' and id_usuario = '$id'";
                $numrespp = $mysqli->query($pregu);
                $num4 = $numrespp->num_rows;
                if ($num4 > 0) {
                    echo ("<div class='alert alert-danger'>ERROR:Usted no puede registrar la misma pregunta.</div>");
                } else {
                    $numres = "INSERT INTO tbl_preguntas_respuestas_usuarios values('$pregunta','$id','$resp','$id',now(),'$id',now())";
                    $numrespp = $mysqli->query($numres);
                    $sql3="UPDATE tbl_usuarios_login SET num_preguntas_contestadas = num_preguntas_contestadas+1 WHERE cod_usuario= '$id'";
                    $resultado3 = $mysqli->query($sql3);
                    echo "<script> alert ('Registro satisfactoriamente.');</script>" ;
                }
            }

        }else{
            echo "<script> alert ('Nota:No hay preguntas registradas, contactese con el administrador');
            location.href ='../index.php';
            </script>";  
         }
    } else {
            $row5 = $resultado5->fetch_assoc();
            $id_objeto=$row5['id_objeto'];
            $accion=$row5['objeto'];
            $descripcion= $row5['descripcion_objeto'];
            event_bitacora($id_objeto, $accion,$descripcion);
        echo "<script> alert ('Usted ya tiene registrado todas sus preguntas de seguridad, será redireccionado cambiar su contraseña');
            location.href ='cambio_pass.php';
            </script>";
    }
}else{
    echo "<script> alert ('Usted no tiene asignados un número de preguntas configurables, contactese con el administrador');
        location.href ='../../index.php';
        </script>";
}

    ?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>preguntas de seguridad-NPH</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-2 rounded-lg mt-5" style="margin:20px">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Registre sus preguntas de seguridad</h3>
                                </div>
                                <div class="card-body">

                                    <form class="" method="POST" action="">

                                        <div class="form-row " style="margin:20px 20px 10px 20px">
                                            <div class="col-md-10">
                                                <label class="small mb-1" for="inputConfirmPassword">Seleccione una pregunta de la lista</label>
                                                <select name="pregunta" id="combo_preguntasid1" required class="form-control">
                                                    <option value="0">selecciona una pregunta</option>
                                                    <?php while ($row2 = $resultado2->fetch_assoc()) { ?>
                                                        <option required value="<?php echo $row2['id_pregunta']; ?>"><?php echo $row2['pregunta']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-group"><label class="small mb-1" for="inputConfirmPassword">Ingrese una respuesta</label>
                                                    <input autocomplete="off" class="form-control py-4" type="text" name="respuesta" maxlength="100" placeholder="Escriba su respuesta" required />
                                                </div>
                                            </div>


                                            <div class="form-row " style="margin:20px 20px 10px 20px">
                                                <div class="col-md-7">
                                                    <div><button class="btn btn-primary" type="submit" name="guardar">Siguinte</button></div>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>