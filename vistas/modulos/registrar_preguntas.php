<?php
require "../../modelos/bitacora.php";
require "conexion.php";
session_start();

$sql = "SELECT id_pregunta,pregunta FROM tbl_preguntas_usuarios";
//echo $sql;
$resultado = $mysqli->query($sql);
$preguntas_contestadas=1;
$codigo_usuario=$_SESSION['pasar_numero_usuario'];
  while ($preguntas_contestadas<4) {
    if(isset($_POST['continuar_preguntas']) && !empty($_POST['respuestas_registro']) && !empty($_POST['pregunta_seleccionada'])){
        $sql1="UPDATE tbl_usuarios_login SET num_preguntas_contestadas =num_preguntas_contestadas,creado_por=$codigo_usuario,fecha_creacion=now(),modificado_por=$codigo_usuario,fecha_modificacion=NOW() WHERE cod_usuario= '$codigo_usuario'";
        $resultado1 = $mysqli->query($sql1);
        $respuesta=$_POST['respuestas_registro'];

        $pregunta = $_POST['pregunta_seleccionada'];
        $sql2 = "SELECT id_pregunta FROM tbl_preguntas_usuarios WHERE pregunta='$pregunta'";
        $resultado2 = $mysqli->query($sql2);
        $num2 = $resultado2->num_rows;
        $row2 = $resultado2->fetch_assoc();
        $numeropregunta=$row2['id_pregunta'];

        $sql3="insert into tbl_preguntas_respuestas_usuarios values('$$numeropregunta','$codigo_usuario','$respuesta','$codigo_usuario',now(),'$codigo_usuario',now()";
        $resultado3 = $mysqli->query($sql3);

        $preguntas_contestadas=$preguntas_contestadas+1;
    }else {
        //function_alert("Por favor seleccione una pregunta y asigne una respuesta");
  }
  header("Location: index.php");
  function_alert("Inicie sesión nuevamente");  
}
function function_alert($message) {
    echo "<script>alert('$message');</script>";
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
        <title>Preguntas</title>

        <link href="css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
      <a href="register.php">ir a Registro</a>
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <div class="card shadow-lg border-2 rounded-lg mt-5" style="margin:20px">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Registre las preguntas de seguridad</h3></div>
                                    <div class="card-body">
                                        <form class="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                <div class="form-row" style="margin:20px" >
                                                  <div class="col-md-10">
                                                      <select name="combo_preguntas" id="combo_preguntasid">
                                                        <option name="pregunta_seleccionada" value="0" >Selecione una pregunta</option>
                                                        <?php while($row = $resultado->fetch_assoc()){ ?>
                                                            <option   required value="<?php echo $row['id_pregunta'];?>"><?php echo $row['pregunta']; ?></option>
                                                        <?php } ?>
                                                      </select>
                                                  </div>

                                                  <div class="col-md-10">
                                                      <div class="form-group"><label class="small mb-1" for="nomb_empleadoid"><b>Ingrese una respuesta a su pregunta</b> </label><input class="form-control py-4" id="respuesta_id" type="text" maxlength="100" placeholder="Ingrese una respuesta" value="" name="respuestas_registro" required/></div>
                                                  </div>


                                                </div>

                                              <div class="form-row " style="margin:20px 20px 10px 20px">
                                                  <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0"><button class="btn btn-primary" type="submit" name="continuar_preguntas">Continuar</button></div>
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
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Vilches 2022</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
