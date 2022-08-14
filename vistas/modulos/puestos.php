<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Solicitudes de compra</title>
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
		$sqlpermisopuestos = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=39";
		$resultadopermisopuestos = $mysqli->query($sqlpermisopuestos);
		$filaspermisopuestos = $resultadopermisopuestos->num_rows;

		if ($filaspermisopuestos > 0) {
			$rowpermisospuestos = $resultadopermisopuestos->fetch_assoc();
			$permisoinsercionpuestos = $rowpermisospuestos['permiso_insercion'];
			$permisoeliminacionpuestos = $rowpermisospuestos['permiso_eliminacion'];
			$permisoactualizacionpuestos = $rowpermisospuestos['permiso_actualizacion'];
			$permisoconsultapuestos = $rowpermisospuestos['permiso_consultar'];
			if ($permisoconsultapuestos == 1) {
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

				$sql = "SELECT `cod_puesto`, `nombre_puesto`, `salario_puesto`, `funciones_puesto` FROM `tbl_puesto_empleados`;";
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
									<h1 class="m-0">Puestos</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Puestos</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->
					<form action="reportes_nph/reportepuestos.php" id="frm_enviar_filtro_puestos" method="post">
						<input type="hidden" name="filtro_puesto" id="filtro_puesto">
						<div>
							<button id="btn_formulario_puestos" class="btn btn-danger" name="btn_formulario_puestos" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Puestos</button>
						</div>
					</form>
                    <script>
						$('#btn_formulario_puestos').on('click', function() {

							var filtro = $('#puestos_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_puesto').val(filtro);

							console.log('#filtro_puesto');
							console.log($('#filtro_puesto').val());
							document.getElementById('frm_enviar_filtro_puestos').submit();

						});
                    </script> 
					<div>

					<div>

						<div class="card-body" style="overflow-x:auto;">
							<div class="table-responsive">
								<table id="puestos" class="table table-bordered ">

									<thead>
										<tr>
											<th>Opciones</th>
											<th>Id</th>
											<th>Puesto</th>
											<th>Salario</th>
											<th>Funciones</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($row_solicitudes = $resultado->fetch_assoc()) { ?>
											<tr>
												<td>
													<?php if ($permisoactualizacionpuestos == 1) { ?>
														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
														</div>
													<?php } ?>
													<?php if ($permisoeliminacionpuestos == 1) { ?>
														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
														</div>
													<?php } ?>
												</td>
												<!-- SELECT `cod_puesto`, `nombre_puesto`, `salario_puesto`, `funciones_puesto` FROM `tbl_puesto_empleados`; -->
												<td><?php echo $row_solicitudes['cod_puesto']; ?></td>
												<td><?php echo $row_solicitudes['nombre_puesto']; ?></td>
												<td><?php echo $row_solicitudes['salario_puesto']; ?></td>
												<td><?php echo $row_solicitudes['funciones_puesto']; ?></td>
											</tr>
										<?php } ?>

										<?php if ($permisoinsercionpuestos == 1) { ?>
											<h1 class="box-title">
												<!-- Button trigger modal Añadir-->
												<button type="button" id="btnAñadir" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadir">
													<i class="fa fa-plus-circle"></i>&nbsp; Nuevo puesto
												</button>
											</h1>
										<?php } ?>
									</tbody>
								</table>

								<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
								<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
								<script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
							</div>
						</div>
						<!-- Modal Filtro de empleados-->
                        <div class="modal fade" id="filtropuestos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reportepuestos.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtración</b> </label><input class="form-control py-4" id="filtroempleados" type="text" name="filtropuestos" placeholder="Ingrese una coincidencia" maxlength=""  /></div>
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
										<h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_puestos.php?op=eliminar" method="post">

											<div class="form-group">
												<label for="">¿Esta seguro que desa eliminar el puesto&nbsp;</label><label id="idEliminarLabel"></label>
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
										<h5 class="modal-title" id="exampleModalLabel">Editar</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_puestos.php?op=editar" method="post">
											<!-- FORMULARIO -->

											<input id="id_editar" type="hidden" name="id" value="" />

											<div class="form-group">
												<label class="small mb-1" for="id">Id</label>
												<input class="form-control" id="id2_editar" type="text" name="id2" value="" maxlength="29" disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="puesto">Puesto Empleado</label>
												<input class="form-control" id="puesto_editar" type="text" name="puesto" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" value="" placeholder="Ingrese puesto" maxlength="29" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="salario">Salario</label>
												<input class="form-control" id="salario_editar" type="text" name="salario" value="" placeholder="Ingrese salario" onkeypress="return solonumerosydecimales(event);" maxlength="9" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="funciones">Funciones Empleado</label>
												<textarea class="form-control" id="funciones_editar" type="" name="funciones" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" value="" placeholder="Ingrese funciones" maxlength="200" required></textarea>
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
										<h5 class="modal-title" id="exampleModalLabel">Añadir solicitud</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<!-- FORMULARIO -->
										<form action="modelos/crud_puestos.php?op=añadir" method="post">
											<!-- SELECT `cod_puesto`, `nombre_puesto`, `salario_puesto`, `funciones_puesto` FROM `tbl_puesto_empleados`; -->

											<div class="form-group">
												<label class="small mb-1" for="puesto">Puesto Empleado</label>
												<input class="form-control" id="puesto" type="text" name="puesto" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" value="" placeholder="Ingrese puesto" maxlength="29" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="salario">Salario</label>
												<input class="form-control" id="salario" type="text" name="salario" value="" placeholder="Ingrese salario" onkeypress="return solonumerosydecimales(event);" maxlength="9" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="funciones">Funciones Empleado </label>
												<textarea class="form-control" id="funciones" type="" name="funciones" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" value="" placeholder="Ingrese funciones" maxlength="200" required></textarea>
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
					<label for=""></label>
					<div></div>
				</div>
				<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
				<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
				<script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
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
						letras = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz. ";
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
						/* id2_editar $puesto',`salario_puesto`=$salario,`funciones_puesto`='$funciones'  */
						$tr = $(this).closest('tr');

						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						$('#id_editar').val(datos[1]);
						$('#id2_editar').val(datos[1]);
						$('#puesto_editar').val(datos[2]);
						$('#salario_editar').val(datos[3]);
						$('#funciones_editar').val(datos[4]);
					});


					$('.btnEliminar').on('click', function() {

						$tr = $(this).closest('tr');

						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						$('#idEliminar').val(datos[1]);
						$('#idEliminarLabel').text(datos[2] + '?');
					});

					$(document).ready(function() {
						var table = $('#puestos').DataTable({
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