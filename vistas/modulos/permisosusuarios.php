<!DOCTYPE html5>
<html lang="es">

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
    if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
        echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
    } else {
        $codrolusuario = $_SESSION['codigo_rol'];
        $sqlgestionpermisos = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=31";
        $resultadogestionpermisos = $mysqli->query($sqlgestionpermisos);
        $filasgestionpermisos = $resultadogestionpermisos->num_rows;

        if ($filasgestionpermisos  > 0) {
            $rowgestionpermisos = $resultadogestionpermisos->fetch_assoc();
            $permisoinserciongestionpermisos = $rowgestionpermisos['permiso_insercion'];
            $permisoeliminaciongestionpermisos = $rowgestionpermisos['permiso_eliminacion'];
            $permisoactualizaciongestionpermisos = $rowgestionpermisos['permiso_actualizacion'];
            $permisoconsultagestionpermisos = $rowgestionpermisos['permiso_consultar'];
            if ($permisoconsultagestionpermisos == 1) {
                $id = $_SESSION['id'];
                $sql = "SELECT * FROM tbl_permisos";
                $resultado = $mysqli->query($sql);
                $num = $resultado->num_rows;
    ?>
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class="m-0">PERMISOS</h1>
                                </div><!-- /.col -->
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">NPH</a></li>
                                        <li class="breadcrumb-item active">Permisos de usuarios</li>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.container-fluid -->

                    </div>
                    <!-- /.content-header -->
                    <div>
                    <form action="reportes_nph/reportepermisos.php" id="frm_enviar_filtro_permisos" method="post">
						<input type="hidden" name="filtro_permisos" id="filtro_permisos">
						<div>
							<button id="btn_formulario_permisos" class="btn btn-danger" name="btn_formulario_permisos" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Permisos</button>
						</div>
					</form>
                    <script>
						$('#btn_formulario_permisos').on('click', function() {

							var filtro = $('#rolespermisos_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_permisos').val(filtro);

							console.log('#filtro_permisos');
							console.log($('#filtro_permisos').val());
							document.getElementById('frm_enviar_filtro_permisos').submit();

						});
                    </script> 
                        <div class="card-body" style="overflow-x:auto;">
                            <div class="table-responsive">
                                <table id="rolespermisos" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Rol</th>
                                            <th>Objeto/Pantalla</th>
                                            <th>Inserción</th>
                                            <th>Actualización</th>
                                            <th>Eliminación</th>+
                                            <th>Consultar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $resultado->fetch_assoc()) {
                                            $codigorol = $row['id_rol'];
                                            $codigoobjeto = $row['id_objeto'];
                                            if ($row['permiso_insercion'] == 0) {
                                                $insertar = "No";
                                            } else {
                                                $insertar = "Sí";
                                            }

                                            if ($row['permiso_eliminacion'] == 0) {
                                                $eliminar = "No";
                                            } else {
                                                $eliminar = "Sí";
                                            }

                                            if ($row['permiso_actualizacion'] == 0) {
                                                $actualizar = "No";
                                            } else {
                                                $actualizar = "Sí";
                                            }

                                            if ($row['permiso_consultar'] == 0) {
                                                $consultar = "No";
                                            } else {
                                                $consultar = "Sí";
                                            }

                                            $sql1 = "SELECT * FROM tbl_roles_usuarios WHERE id_rol='$codigorol'";
                                            $resultado1 = $mysqli->query($sql1);
                                            $num1 = $resultado1->num_rows;
                                            if ($num1 > 0) {
                                                $row1 = $resultado1->fetch_assoc();
                                                $rol = $row1['rol'];
                                            } else {
                                                $rol = "Ninguno";
                                            }

                                            $sql2 = "SELECT * FROM tbl_objetos WHERE id_objeto='$codigoobjeto'";
                                            $resultado2 = $mysqli->query($sql2);
                                            $num2 = $resultado2->num_rows;
                                            if ($num2 > 0) {
                                                $row2 = $resultado2->fetch_assoc();
                                                $objeto = $row2['objeto'];
                                            } else {
                                                $objeto = "Ninguno";
                                            }

                                        ?>
                                            <tr>
                                                <td>
                                                    <?php if ($permisoactualizaciongestionpermisos == 1) { ?>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarpermisosrol"><i class="nav-icon fas fa-pen"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($permisoeliminaciongestionpermisos == 1) { ?>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminarpermisosrol"><i class="nav-icon fas fa-trash"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                </td>

                                                <td><?php echo $rol; ?></td>
                                                <td><?php echo $objeto; ?></td>
                                                <td><?php echo $insertar; ?></td>
                                                <td><?php echo $actualizar; ?></td>
                                                <td><?php echo $eliminar; ?></td>
                                                <td><?php echo $consultar; ?></td>
                                            </tr>

                                        <?php } ?>
                                        <?php if ($permisoinserciongestionpermisos == 1) { ?>
                                            <h1 class="box-title">
                                                <!-- Button trigger modal Añadir-->
                                                <button type="button" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadirPermisosrol">
                                                    <i class="fa fa-plus-circle"></i>&nbsp; Asignar permisos a un rol
                                                </button>
                                            </h1>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Modal Filtro de usuarios-->
                        <div class="modal fade" id="filtropermisos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reportepermisos.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, códigos etc) se mostraran los datos que coinciden con lo que usted ingresó, si no ingresa ningún dato, se traeran todos los registros de roles en el sistema.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtraciòn</b> </label><input class="form-control py-4" id="filtropermisos" type="text" name="filtropermisos" placeholder="Ingrese una coincidencia" maxlength="" /></div>
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
                        <div class="modal fade" id="modalEliminarpermisosrol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Eliminar permisos a rol</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crudpermisos.php?op=eliminar" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar los permisos asignados, con la siguiente información...</div>"); ?>
                                                <label class="small mb-1">Rol</label>
                                                <input class="form-control" id="roleliminarpermisos" type="text" name="roleliminarpermisos" readonly />
                                                <label class="small mb-1">Módulo ó pantalla</label>
                                                <input class="form-control" id="moduloeliminarpermisos" type="text" name="moduloeliminarpermisos" readonly />
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
                        <div class="modal fade" id="modalEditarpermisosrol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Editar permisos de rol</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crudpermisos.php?op=editar" method="post">

                                            <div class="form-group">
                                                <input type="hidden" name="id" id="idEditar">
                                            </div>

                                            <!-- select rol-->
                                            <div class="form-group">
                                                <label class="small mb-1" for="correo_electronico"> Nombre de rol</label>
                                                <input class="form-control" id="permisosroleditar" type="text" name="permisosroleditar" placeholder="" required readonly />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="correo_electronico">Módulo ó pantalla asignada</label>
                                                <input class="form-control" id="permisosmoduloeditar" type="text" name="permisosmoduloeditar" placeholder="" maxlength="" required readonly />
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="correo_electronico">Actuazación de permisos</label>
                                                <div class="col-md-8">
                                                    <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="insertareditar" name="insertareditar">&nbsp;&nbsp;Permiso de insertar ó registrar.</div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="actualizareditar" name="actualizareditar">&nbsp;&nbsp;Permiso de editar ó actualizar.</div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="eliminareditar" name="eliminareditar">&nbsp;&nbsp;Permiso de eliminar.</div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="consultareditar" name="consultareditar">&nbsp;&nbsp;Permiso de consultar ó ver.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Actualizar permisos</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal AÑADIR-->
                        <div class="modal fade" id="modalAñadirPermisosrol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Asignación de permiso</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crudpermisos.php?op=añadir" method="post">

                                            <!-- select rol-->
                                            <div class="form-group">
                                                <label class="small mb-1" for="rol">Selecione un rol</label>
                                                <select class="form-control" id="rolañadirpermiso" name="rolañadirpermiso" required>
                                                    <?php $sql = "SELECT * FROM tbl_roles_usuarios ";
                                                    $resultado = $mysqli->query($sql);
                                                    while ($row = $resultado->fetch_assoc()) {
                                                        $codigoRol = $row['id_rol'];
                                                        $nombreRol = $row['rol']; ?>
                                                        <option value=<?php echo $codigoRol; ?>> <?php echo $nombreRol;
                                                                                                } ?> </option>
                                                </select>
                                            </div>
                                            <!-- select objeto-->
                                            <div class="form-group">
                                                <label class="small mb-1" for="rol">Selecione un módulo</label>
                                                <select class="form-control" id="objetoañadirpermiso" name="objetoañadirpermiso" required>
                                                    <?php $sqlobjetos = "SELECT * FROM tbl_objetos WHERE tipo_objeto='Modulo'";
                                                    $resultadoobjetos = $mysqli->query($sqlobjetos);

                                                    while ($rowobjetos = $resultadoobjetos->fetch_assoc()) { ?>
                                                        <option value=<?php echo $rowobjetos['id_objeto']; ?>> <?php echo $rowobjetos['objeto'];
                                                                                                            } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="insertar" name="insertar">&nbsp;&nbsp;Permiso de insertar ó registrar.</div>
                                            </div>
                                            <div class="col-md-8">
                                                <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="actualizar" name="actualizar">&nbsp;&nbsp;Permiso de editar ó actualizar.</div>
                                            </div>
                                            <div class="col-md-8">
                                                <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="eliminar" name="eliminar">&nbsp;&nbsp;Permiso de eliminar.</div>
                                            </div>
                                            <div class="col-md-8">
                                                <div style="margin-top:2px;"><input style="margin-left:20px;" type="checkbox" id="consultar" name="consultar">&nbsp;&nbsp;Permiso de consultar ó ver.</div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Registrar permisos de rol</button>
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

                    function mostrarContraseña(id) {
                        var x = document.getElementById(id);
                        if (x.type == "password") {
                            x.type = "text";
                        } else {
                            x.type = "password";
                        }
                    }

                    $('.btnEditar').on('click', function() {
                        $tr = $(this).closest('tr');

                        var datos = $tr.children("td").map(function() {
                            return $(this).text();
                        });
                        $('#permisosroleditar').val(datos[1]);
                        $('#permisosmoduloeditar').val(datos[2]);

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
                        $('#roleliminarpermisos').val(datos[1]);
                        $('#moduloeliminarpermisos').val(datos[2]);
                    });
                    let temp = $("#btn1").clone();
                    $("#btn1").click(function() {
                        $("#btn1").after(temp);
                    });
                    $(document).ready(function() {
                        var table = $('#rolespermisos').DataTable({
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