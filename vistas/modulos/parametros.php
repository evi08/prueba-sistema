<!DOCTYPE html5>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parámetros</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="../dist/css/alt/estilos.css">
    <style>
        /*estilos para la tabla*/
        table th {
            background-color: dodgerblue;
            color: white;
        }
    </style>
</head>

<body>
    <?php
    require "modelos/conexion.php";
    if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
        echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
    } else {
        $codrolusuario = $_SESSION['codigo_rol'];
        $sqlgestionparametros = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=32";
        $resultadogestionparametros = $mysqli->query($sqlgestionparametros);
        $filasgestionparametros = $resultadogestionparametros->num_rows;

        if ($filasgestionparametros  > 0) {
            $rowgestionparametros = $resultadogestionparametros->fetch_assoc();
            $permisoactualizaciongestionparametros = $rowgestionparametros['permiso_actualizacion'];
            $permisoconsultagestionparametros = $rowgestionparametros['permiso_consultar'];
            if ($permisoconsultagestionparametros == 1) {
                $id = $_SESSION['id'];



                $sql = "SELECT * FROM tbl_parametros";
                $resultado = $mysqli->query($sql);
                $num = $resultado->num_rows;

    ?>

                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class="m-0">PARÁMETROS</h1>
                                </div><!-- /.col -->
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">NPH</a></li>
                                        <li class="breadcrumb-item active">Parámetros</li>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.container-fluid -->

                    </div>
                    <!-- /.content-header -->
                    <div>
                        <form action="reportes_nph/reporteparametros.php" id="frm_enviar_filtro_parametros" method="post">
                            <input type="hidden" name="filtro_parametro" id="filtro_parametro">
                            <div>
                                <button id="btn_formulario_parametros" class="btn btn-danger" name="btn_formulario_parametros" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Parametros</button>
                            </div>
                        </form>
                        <script>
                            $('#btn_formulario_parametros').on('click', function() {

                                var filtro = $('#parametros_filter > label > input[type=search]').val();
                                console.log(filtro);
                                $('#filtro_parametro').val(filtro);

                                console.log('#filtro_parametro');
                                console.log($('#filtro_parametro').val());
                                document.getElementById('frm_enviar_filtro_parametros').submit();

                            });
                        </script>    
                        <div class="card-body" style="overflow-x:auto;">
                            <div class="table-responsive">
                                <table id="parametros" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Identificador</th>
                                            <th>Parametro</th>
                                            <th>Valor</th>
                                            <th>Usuario</th>
                                            <th>Creado por</th>
                                            <th>Fecha creación</th>
                                            <th>Modificado por</th>
                                            <th>Última modificación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $resultado->fetch_assoc()) {
                                            $usuarioparametro = $row['id_usuario'];
                                            $creadopor = $row['creado_por'];
                                            $modificadopor = $row['modificado_por'];

                                            $sql1 = "SELECT * from tbl_usuarios_login
												WHERE cod_usuario='$usuarioparametro'";
                                            $resultado1 = $mysqli->query($sql1);
                                            $row1 = $resultado1->fetch_assoc();

                                            $sql2 = "SELECT * from tbl_usuarios_login
												WHERE cod_usuario='$creadopor'";
                                            $resultado2 = $mysqli->query($sql2);
                                            $row2 = $resultado2->fetch_assoc();

                                            $sql3 = "SELECT * from tbl_usuarios_login
												WHERE cod_usuario='$modificadopor'";
                                            $resultado3 = $mysqli->query($sql3);
                                            $row3 = $resultado3->fetch_assoc();

                                        ?>
                                            <tr>

                                                <td>
                                                    <?php if ($permisoactualizaciongestionparametros == 1) { ?>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarparametro"><i class="nav-icon fas fa-pen"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                </td>

                                                <td><?php echo $row['id_parametro']; ?></td>
                                                <td><?php echo $row['parametro']; ?></td>
                                                <td><?php echo $row['valor']; ?></td>
                                                <td><?php echo  $row1['nombre_usuario_correo']; ?></td>
                                                <td><?php echo $row2['nombre_usuario_correo']; ?></td>
                                                <td><?php echo $row['fecha_creacion']; ?></td>
                                                <td><?php echo $row3['nombre_usuario_correo']; ?></td>
                                                <td><?php echo $row['fecha_modificacion']; ?></td>
                                            </tr>

                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!-- Modal Filtro de empleados-->
                        <div class="modal fade" id="filtroparametro" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reporteparametros.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtración</b> </label><input class="form-control py-4" id="filtroempleados" type="text" name="filtroparametro" placeholder="Ingrese una coincidencia" maxlength=""  /></div>
                                                </div>
                                                <label id="idEliminarLabel"></label>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="id" id="idEliminar">
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
                                                <button type="submit" class="btn btn-danger"><i class="nav-icon fas fa-file-pdf"></i> Reporte</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal EDITAR-->
                        <div class="modal fade" id="modalEditarparametro" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Editar parámetro(valor)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_parametros.php?op=editar" method="post">

                                            <div class="form-group">
                                                <input type="hidden" name="id" id="idEditar">
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="correo_electronico">Código de parámetro</label>
                                                <input class="form-control" id="codparametroeditar" type="text" name="codparametroeditar" placeholder="" required readonly />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="correo_electronico">Nombre del parámetro</label>
                                                <input class="form-control" id="nombreparametroeditar" type="text" name="nombreparametroeditar" placeholder="" onKeyUP="this.value=this.value.toUpperCase();" required readonly />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="correo_electronico">Valor del parámetro</label>
                                                <input class="form-control" id="valorparametroeditar" type="text" name="valorparametroeditar" placeholder="" onKeyUP="this.value=this.value.toUpperCase();" required />
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    <?php
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
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js" type="text/javascript"></script>
    <script type="text/javascript">
        $('.btnEditar').on('click', function() {
            $tr = $(this).closest('tr');

            var datos = $tr.children("td").map(function() {
                return $(this).text();
            });
            $('#codparametroeditar').val(datos[1]);
            $('#nombreparametroeditar').val(datos[2]);
            $('#valorparametroeditar').val(datos[3]);
        });


        $(document).ready(function() {
            var table = $('#parametros').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                responsive: "true",
            });
            //Creamos una fila en el head de la tabla y lo clonamos para cada columna
        });
    </script>

</html>