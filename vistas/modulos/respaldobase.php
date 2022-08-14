<?php
require "modelos/conexion.php";
if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
    echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
} else {
    $codrolusuario = $_SESSION['codigo_rol'];
    $sqlgestioncopiadb = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=42";
    $resultadocopiadb = $mysqli->query($sqlgestioncopiadb);
    $filasgestioncopiadb = $resultadocopiadb->num_rows;

    if ($filasgestioncopiadb  > 0) {
        $rowgestioncopiadb = $resultadocopiadb->fetch_assoc();
        $permisoconsultacopiadb = $rowgestioncopiadb['permiso_consultar'];
        $permisoinsertarcopiadb = $rowgestioncopiadb['permiso_insercion'];
        $permisoactualizarcopiadb = $rowgestioncopiadb['permiso_actualizacion'];
        $permisoeliminarcopiadb = $rowgestioncopiadb['permiso_eliminacion'];
        if ($permisoconsultacopiadb == 1 && $permisoinsertarcopiadb == 1 && $permisoactualizarcopiadb == 1 && $permisoeliminarcopiadb == 1) {
            $sqltamaño = "";
            if (isset($_POST['guardar'])) {
                // variables
                $dbhost = 'nph.ca7eckccl7rg.us-east-2.rds.amazonaws.com:3306';
                $dbname = 'nph';
                $dbuser = 'admin';
                $dbpass = 'Sistemas.2020';
                //Nombre del archivo natural
                $nombredb = $_POST['nombrearchivo'];
                $backup_file = $nombredb . "-" . date("Y-m-d-H-i-s") . ".sql";

                // comandos a ejecutar
                $commands = array(
                    "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass -v $dbname > $backup_file",
                    "bzip2 $backup_file"
                );

                // ejecución y salida de éxito o errores
                foreach ($commands as $command) {
                    system($command, $output);
                    echo $output;
                    echo "<script>
        location.href='respaldobase';
        alert('La copia de su base de datos fué exitosa');
        </script>";
                }
            }
        } else {
            echo "<script>
    location.href='index.php';
    alert('Su rol no le permite o tiene restringido el acceso a esta pantalla.');
    </script>";
        }
    } else {
        echo "<script>
location.href='index.php';
alert('Su rol no tiene permisos asignados para esta pantalla,no puede hacer transacciones en este espacio, será redireccionado a inicio, para mayor informaciòn puede contactarse con el administrador.');
</script>";
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-12">
                <div class="col-sm-12">
                    <h1 class="m-0">Respaldo</h1>
                </div><!-- /.col -->
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">NPH</a></li>
                        <li class="breadcrumb-item active">Respaldo de información</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="container">
        <div class="card-body">
            <form class="" method="POST" action="">
                <div class="form-row " style="margin:20px 20px 10px 20px">
                    <div class="col-md-7">
                        <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Nombre de la base de datos</b> </label>
                            <input class="form-control py-4" id="nombrebase" value="nph" type="text" name="nombrebase" readonly />
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Peso o tamaño en memoria</b> </label>
                            <input class="form-control py-4" id="pesobase" value="<?php echo $sqltamaño; ?>" type="text" name="pesobase" readonly />
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Seleccione una carpeta</b> </label>
                            <input class="form-control py-4" id="carpetabase" type="file" name="carpetabase " />
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Asignele un nombre al archivo</b> </label>
                            <input class="form-control py-4" id="nombrearchivo" type="text" name="nombrearchivo" maxlength="20" required />
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="margin:20px 20px 10px 100px" class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                        <button class="btn btn-primary btn-block" type="submit" name="guardar">Guardar respaldo</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- Main content -->

</div>
<!-- /.content-wrapper -->