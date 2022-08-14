<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departamentos</title>
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
        $sqlgestiondepartamentos = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=26";
        $resultadogestiondepartamentos = $mysqli->query($sqlgestiondepartamentos);
        $filasgestiondepartamentos = $resultadogestiondepartamentos->num_rows;

        if ($filasgestiondepartamentos > 0) {
            $rowgestiondepartamentos = $resultadogestiondepartamentos->fetch_assoc();
            $permisoinserciongestiondepartamentos = $rowgestiondepartamentos['permiso_insercion'];
            $permisoeliminaciongestiondepartamentos = $rowgestiondepartamentos['permiso_eliminacion'];
            $permisoactualizaciongestiondepartamentos = $rowgestiondepartamentos['permiso_actualizacion'];
            $permisoconsultagestiondepartamentos = $rowgestiondepartamentos['permiso_consultar'];
            if ($permisoconsultagestiondepartamentos == 1) {
                $id = $_SESSION['id'];

                $sql = "SELECT u.id_rol_usuario, u.cod_empleado, ed.cod_departamento, d.nombre_departamento, concat(e.primer_nombre,\" \",e.segundo_nombre,\" \",e.primer_apellido,\" \",e.segundo_apellido) empleado 
	FROM tbl_usuarios_login u, tbl_empleados e, tbl_departamentos d, tbl_empleados_departamentos ed 
	WHERE ed.cod_empleado = u.cod_empleado AND d.cod_departamento = ed.cod_departamento AND e.cod_empleado = u.cod_empleado AND u.cod_usuario = $id;";
                $resultado = $mysqli->query($sql);
                $row_usuario = $resultado->fetch_assoc();

                //cod_empleado
                $id_empleado = $row_usuario['cod_empleado'];
                $tipo_usuario = $row_usuario['id_rol_usuario'];
                $where = "";
                if ($tipo_usuario == 1) {
                    $where = "";
                } else {
                    $where = "AND cod_empleado = $id_empleado";
                }

                $sql = "SELECT d.cod_departamento, d.nombre_departamento, d.cantidad_empleados, d.funcion_del_departamento, d.descripcion
                FROM tbl_departamentos d 
                WHERE d.cod_departamento = cod_departamento;";
                $resultado = $mysqli->query($sql);
                $num = $resultado->num_rows;
                $_SESSION['cantidadusuarios'] = $num;
    ?>

                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class="m-0">Departamentos</h1>
                                </div><!-- /.col -->
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">NPH</a></li>
                                        <li class="breadcrumb-item active">Departamentos de la Fundación </li>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.container-fluid -->

                    </div>
                    <!-- /.content-header -->
                    <form action="reportes_nph/reportedepartamento.php" id="frm_enviar_departamento" method="post">
						<input type="hidden" name="filtrodepartamento" id="input_departamento">
						<div>
							<button id="btn_enviar_formulario" class="btn btn-danger" name="btnreportedepartamento" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Departamento</button>
						</div>
					</form>
                    <script>
						$('#btn_enviar_formulario').on('click', function() {

							var filtro = $('#departamentos_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#input_departamento').val(filtro);

							console.log('#input_departamento');
							console.log($('#input_departamento').val());
							document.getElementById('frm_enviar_departamento').submit();

                        });
                        </script>
					<div>
						<div>
                        <div class="card-body" style="overflow-x:auto;">
                            <div class="table-responsive">
                                <table id="departamentos" class="table table-bordered ">

                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Id</th>
                                            <th>Departamento</th>
                                            <th>N° Empleados</th>
                                            <th>Función </th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row_deptos = $resultado->fetch_assoc()) { ?>
                                            <tr>
                                                <td>
                                                    <?php if ($permisoactualizaciongestiondepartamentos == 1) { ?>
                                                        <!-- BOTTON EDITAR -->
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                    <!-- BOTTON ELIMINAR -->
                                                    <?php if ($permisoeliminaciongestiondepartamentos == 1) { ?>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                    <!-- a.cod_activo, c.nombre_categoria, p.nombre_proveedor, a.nombre_activo, a.descripcion_activo, a.presentacion_activo  -->
                                                </td>
                                                <td><?php echo $row_deptos['cod_departamento']; ?></td>
                                                <td><?php echo $row_deptos['nombre_departamento']; ?></td>
                                                <td><?php echo $row_deptos['cantidad_empleados']; ?></td>
                                                <td><?php echo $row_deptos['funcion_del_departamento']; ?></td>
                                                <td><?php echo $row_deptos['descripcion']; ?></td>
                                            </tr>
                                        <?php } ?>

                                        <?php if ($permisoinserciongestiondepartamentos == 1) { ?>
                                            <h1 class="box-title">
                                                <!-- Button trigger modal Añadir-->
                                                <button type="button" id="btnAñadir" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadir">
                                                    <i class="fa fa-plus-circle"></i>&nbsp; Nuevo Departamento
                                                </button>
                                            </h1>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
                                <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
                                <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>

                                <script>
                                    let temp = $("#btn1").clone();
                                    $("#btn1").click(function() {
                                        $("#btn1").after(temp);
                                    });
                                </script>
                            </div>
                        </div>
                        <!-- Modal Filtro de empleados-->
                        <div class="modal fade" id="filtrodepartamento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reportedepartamento.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtración</b> </label><input class="form-control py-4" id="filtroempleados" type="text" name="filtrodepartamento" placeholder="Ingrese una coincidencia" maxlength=""  /></div>
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
                        <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Eliminación del Departamento</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_departamentos.php?op=eliminar" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar al siguiente usuario?,sé eliminaran los historiales de contraseña, registros y transacciones realizados por el usuario, asì como aucalquier información asociada al usuario en especifico.</div>"); ?>
                                                <label id="idEliminarLabel"></label>
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
                        <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Editar Departamento</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_departamentos.php?op=editar" method="post">

                                            <!-- FORMULARIO -->

                                            <input type="hidden" name="id" id="id_editar">

                                            <div class="form-group">
                                                <label class="small mb-1" for="departamento">Seleccione un departamento</label>
                                                <select class="form-control" id="departamento_editar" name="departamento" required>
                                                    <?php $sql = "SELECT * FROM tbl_departamentos";
                                                    $resultado = $mysqli->query($sql);
                                                    while ($row = $resultado->fetch_assoc()) { ?>
                                                        <option value=<?php echo $row['cod_departamento']; ?>> <?php echo $row['nombre_departamento'];
                                                                                                            } ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="cantidad">Cantidad Empleados</label>
                                                <input class="form-control" id="cantidad_editar" type="number" name="cantidad" value="" placeholder="Ingrese la cantidad de Empleados" maxlength="15" required />
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="funcion">Funcion del Departamento</label>
                                                <input class="form-control" id="funcion_editar" type="text" name="funcion" value="" placeholder="Ingrese la función del Departamento" maxlength="15" required />
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="descripcion">Descripcion</label>
                                                <input class="form-control" id="descripcion_editar" type="text" name="descripcion" value="" placeholder="Ingrese descripcion" maxlength="100" required />
                                            </div>


                                            <!-- FORMULARIO -->
                                            <div class="form-group">
                                                <label class="small mb-1" for="departamento">Añadir un Departamento</label>
                                                <input class="form-control" id="departamento" type="text" name="departamento" value="" placeholder="Ingrese nuevo Departamento" maxlength="20" required />

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


                        <!-- Modal AÑADIR-->
                        <div class="modal fade" id="modalAñadir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Añadir Departamento</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_departamentos.php?op=añadir" method="post">

                                            <!-- FORMULARIO -->
                                            <div class="form-group">
                                                <label class="small mb-1" for="departamento">Añada un Departamento</label>
                                                <input class="form-control" id="departamento" type="text" name="departamento" value="" placeholder="Ingrese nuevo Departamento" maxlength="20" required />
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="cantidad">Cantidad Empleados</label>
                                                <input class="form-control" id="cantidad" type="number" name="cantidad" value="" placeholder="Ingrese cantidad empleados" maxlength="15" required />
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="funcion">Funcion del Departamento</label>
                                                <input class="form-control" id="funcion" type="text" name="funcion" value="" placeholder="Ingrese la funcion" maxlength="50" required />
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="descripcion">Descripcion</label>
                                                <input class="form-control" id="descripcion" type="text" name="descripcion" value="" placeholder="Ingrese descripcion" maxlength="100" required />
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
                <!-- JavaScript Bundle with Popper -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
                <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>
                <script src="js/scripts.js" type="text/javascript"></script>

                <!-- 	
	<tr>
		<td><input class="form-control" type="text" id="producto1" name="producto1" list="roles" placeholder="Seleccione producto">
			<datalist id="roles"><option value="1">Mario</option>
				<?php $sql = "SELECT * FROM tbl_roles_usuarios";
                $resultado = $mysqli->query($sql);
                while ($row = $resultado->fetch_assoc()) {
                    $codigoRol = $row['id_rol'];
                    $nombreRol = $row['rol']; ?><option value=<?php echo $codigoRol; ?>> <?php echo $nombreRol;
                                                                                        } ?></option>
			</datalist></td>
		<td><input class="form-control" type="number" id="cantidad1" name="cantidad1" placeholder="Ingrese cantidad"></td>

=======
		<td><input class="form-control" type="text" id="precio1" name="precio1" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>
	</tr> 
-->

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

                    function solonumerosydecimales(evt) {
                        if (window.event) {
                            keynum = evt.keyCode;
                        } else {
                            keynum = evt.which;
                        }
                        if (keynum > 47 && keynum < 58 || keynum == 8 || keynum == 13 || keynum == 46) {
                            return true;
                        } else {
                            alert("Para este campo solo son permitidos números.");
                            return false;
                        }
                    }
                    //onkeypress="return solonumerosydecimales(event);" agrgar esta propiedad

                    /* solonumerosydecimales */

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
                        //[departamento] => Enfermeria [cantidad] => 4 [funcion] => Atención medica [descripcion]
                        $('#id_editar').val(datos[1]);
                        $('#departamento_editar').val(datos[2]);
                        $('#cantidad_editar').val(datos[3]);
                        $('#funcion_editar').val(datos[4]);
                        $('#descripcion_editar').val(datos[5]);
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
                        $('#idEliminar').val(datos[1]);
                        $('#idEliminarLabel').text(datos[3]);
                    });

                    $(document).ready(function() {
                        var table = $('#departamentos').DataTable({
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
                        //Creamos una fila en el head de la tabla y lo clonamos para cada columna
                    });
                </script>

</html>