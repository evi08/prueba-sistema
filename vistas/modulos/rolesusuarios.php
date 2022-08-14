<!DOCTYPE html5>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roles usuarios</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  <link rel="" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
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
  date_default_timezone_set('America/Tegucigalpa');
  if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
    echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
  } else {
    $codrolusuario = $_SESSION['codigo_rol'];
    $sqlgestionroles = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=30";
    $resultadogestionroles = $mysqli->query($sqlgestionroles);
    $filasgestionroles = $resultadogestionroles->num_rows;

    if ($filasgestionroles > 0) {
      $rowgestionroles = $resultadogestionroles->fetch_assoc();
      $permisoinserciongestionroles = $rowgestionroles['permiso_insercion'];
      $permisoeliminaciongestionroles = $rowgestionroles['permiso_eliminacion'];
      $permisoactualizaciongestionroles = $rowgestionroles['permiso_actualizacion'];
      $permisoconsultagestionroles = $rowgestionroles['permiso_consultar'];
      if ($permisoconsultagestionroles == 1) {
        $id = $_SESSION['id'];
        $sql = "SELECT * FROM tbl_roles_usuarios";
        $resultado = $mysqli->query($sql);
        $num = $resultado->num_rows;
  ?>

        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Roles de roles</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">NPH</a></li>
                    <li class="breadcrumb-item active">Roles de usuarios</li>
                  </ol>
                </div><!-- /.col -->
              </div><!-- /.row -->
            </div><!-- /.container-fluid -->

          </div>
          <!-- /.content-header -->
          <div>
            <form action="reportes_nph/reporteroles.php" id="frm_enviar_filtro_roles" method="post">
              <input type="hidden" name="filtro_rol" id="filtro_rol">
              <div>
                <button id="btn_formulario_roles" class="btn btn-danger" name="btn_formulario_roles" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Roles</button>
              </div>
            </form>
            <script>
              $('#btn_formulario_roles').on('click', function() {

                var filtro = $('#roles_filter > label > input[type=search]').val();
                console.log(filtro);
                $('#filtro_rol').val(filtro);

                console.log('#filtro_rol');
                console.log($('#filtro_rol').val());
                document.getElementById('frm_enviar_filtro_roles').submit();

              });
            </script> 
            <div class="card-body" style="overflow-x:auto;">
              <div class="table-responsive">
                <table id="roles" class="table table-bordered ">
                  <thead>
                    <tr>
                      <th>Opciones</th>
                      <th>Código de rol</th>
                      <th>Nombre rol</th>
                      <th>Detalles de rol</th>
                      <th>Creado por</th>
                      <th>Fecha creación</th>+
                      <th>Modificado por</th>
                      <th>última modificación</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $resultado->fetch_assoc()) {
                      $creado = $row['creado_por'];
                      $modificado = $row['modificado_por'];

                      $sql1 = "SELECT * FROM tbl_usuarios_login WHERE creado_por='$creado'";
                      $resultado1 = $mysqli->query($sql1);
                      $num1 = $resultado1->num_rows;
                      $row1 = $resultado1->fetch_assoc();

                      $sql2 = "SELECT * FROM tbl_usuarios_login WHERE modificado_por='$modificado'";
                      $resultado2 = $mysqli->query($sql2);
                      $num2 = $resultado2->num_rows;
                      $row2 = $resultado2->fetch_assoc();
                    ?>
                      <tr>
                        <td>
                          <?php if ($permisoactualizaciongestionroles == 1) { ?>
                            <div class="col-md-6">
                              <button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarrol"><i class="nav-icon fas fa-pen"></i></button>
                            </div>
                          <?php } ?>
                          <?php if ($permisoeliminaciongestionroles == 1) { ?>
                            <div class="col-md-6">
                              <button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminarrol"><i class="nav-icon fas fa-trash"></i></button>
                            </div>
                          <?php } ?>
                        </td>

                        <td><?php echo $row['id_rol']; ?></td>
                        <td><?php echo $row['rol']; ?></td>
                        <td><?php echo $row['detalles_rol']; ?></td>
                        <td><?php echo $row1['nombre_usuario_correo']; ?></td>
                        <td><?php echo $row['fecha_creacion']; ?></td>
                        <td><?php echo $row2['nombre_usuario_correo']; ?></td>
                        <td><?php echo $row['fecha_modificacion']; ?></td>
                      </tr>

                    <?php } ?>
                    <?php if ($permisoinserciongestionroles == 1) { ?>
                      <h1 class="box-title">
                        <!-- Button trigger modal Añadir-->
                        <button type="button" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadirrol">
                          <i class="fa fa-plus-circle"></i>&nbsp; Nuevo rol de usuario
                        </button>
                      </h1>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Modal Filtro de usuarios-->
            <div class="modal fade" id="filtrorolesusuarios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                  </div>
                  <div class="modal-body">
                    <form action="reportes_nph/reporteroles.php" method="post">

                      <div class="form-group">
                        <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, códigos etc) se mostraran los datos que coinciden con lo que usted ingresó, si no ingresa ningún dato, se traeran todos los registros de los roles en el sistema.</div>"); ?>
                        <div class="col-md-8">
                          <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtración</b> </label><input class="form-control py-4" id="filtrorolesusuarios" type="text" name="filtrorolesusuarios" placeholder="Ingrese una coincidencia" maxlength="" /></div>
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
            <!-- Modal ELIMINAR-->
            <div class="modal fade" id="modalEliminarrol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Eliminación de rol</h5>
                  </div>
                  <div class="modal-body">
                    <form action="modelos/crud_roles.php?op=eliminar" method="post">

                      <div class="form-group">
                        <?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar al siguiente rol?,sé eliminará el rol de usuarios asociados y su rol será nulo. El usuario que tenía asociado este rol solo tendrá acceso a los módulos que son de uso general</div>"); ?>
                        <label class="small mb-1">Código de rol</label>
                        <input class="form-control" id="idroleliminar" type="text" name="idroleliminar" readonly />
                        <label id="idEliminarLabel" name="idroleliminar"></label>
                      </div>

                      <div class="form-group">
                        <input type="hidden" name="id" id="idEliminar">
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal EDITAR-->
            <div class="modal fade" id="modalEditarrol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                  </div>
                  <div class="modal-body">
                    <form action="modelos/crud_roles.php?op=editar" method="post">

                      <div class="form-group">
                        <input type="hidden" name="id" id="idEditar">
                      </div>

                      <!-- select rol-->
                      <div class="form-group">
                        <label class="small mb-1" for="correo_electronico">Código de rol</label>
                        <input class="form-control" id="roleditar" type="text" name="roleditar" placeholder="" required readonly />
                      </div>
                      <div class="form-group">
                        <label class="small mb-1" for="correo_electronico">Nombre del rol</label>
                        <input class="form-control" id="rolnombreeditar" type="text" name="rolnombreeditar" placeholder="Ingrese un nombre al rol" maxlength="30" required />
                      </div>
                      <div class="form-group">
                        <label class="small mb-1" for="correo_electronico">Detalles del rol</label>
                        <input class="form-control" id="roldetalleeditar" type="text" name="roldetalleeditar" placeholder="Detalles sobre el rol" maxlength="100" required />
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Actualizar rol</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal AÑADIR-->
            <div class="modal fade" id="modalAñadirrol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Añadir un nuevo rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                  </div>
                  <div class="modal-body">
                    <form action="modelos/crud_roles.php?op=añadir" method="post">

                      <!-- select rol-->
                      <div class="form-group">
                        <label class="small mb-1" for="correo_electronico">Nombre del rol</label>
                        <input class="form-control" id="rolnombreañadir" type="text" name="rolnombreañadir" placeholder="Ingrese un nombre al rol" maxlength="30" required />
                      </div>
                      <div class="form-group">
                        <label class="small mb-1" for="correo_electronico">Detalles del rol</label>
                        <input class="form-control" id="roldetalleañadir" type="text" name="roldetalleañadir" placeholder="Detalles sobre el rol" maxlength="100" required />
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                      </div>
                    </form>
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
            $('#roleditar').val(datos[1]);
            $('#rolnombreeditar').val(datos[2]);
            $('#roldetalleeditar').val(datos[3]);

          });

          $('.btnAñadir').on('click', function() {
            $('#rol').val(null);
            $('#empleado').val(null);
            $('#correo_electronico').val("");
            $('#contraseña').val("");
            $('#estado').val(null);
          });

          $('.btnEliminar').on('click', function() {

            $tr = $(this).closest('tr');

            var datos = $tr.children("td").map(function() {
              return $(this).text();
            });
            $('#idroleliminar').val(datos[1]);
            $('#idEliminarLabel').text(datos[2]);
          });
          let temp = $("#btn1").clone();
          $("#btn1").click(function() {
            $("#btn1").after(temp);
          });
          $(document).ready(function() {
            var table = $('#roles').DataTable({
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
            });
          });
        </script>

</html>