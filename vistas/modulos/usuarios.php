<!DOCTYPE html5>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Usuarios</title>
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
		$sqlpermisousuario = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=29";
		$resultadopermisosrol = $mysqli->query($sqlpermisousuario);
		$filaspermisousuario = $resultadopermisosrol->num_rows;

		if ($filaspermisousuario > 0) {
			$rowpermisosadminusuarios = $resultadopermisosrol->fetch_assoc();
			$permisoinsercionusuario = $rowpermisosadminusuarios['permiso_insercion'];
			$permisoeliminacionusuario = $rowpermisosadminusuarios['permiso_eliminacion'];
			$permisoactualizacionusuario = $rowpermisosadminusuarios['permiso_actualizacion'];
			$permisoconsultausuario = $rowpermisosadminusuarios['permiso_consultar'];
			if ($permisoconsultausuario == 1) {
				$id = $_SESSION['id'];
				$sql3 = "SELECT *from tbl_usuarios_login WHERE cod_usuario='$id'";
				$resultado3 = $mysqli->query($sql3);
				$row3 = $resultado3->fetch_assoc();


				$tipo_usuario = $row3['id_rol_usuario'];
				$where = "";
				if ($tipo_usuario == 1) {
					$where = "";
				} else {
					$where = "WHERE cod_usuario=$id";
				}

				$sql = "SELECT * FROM tbl_usuarios_login  $where";
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
									<h1 class="m-0">Gestión de usuarios</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Usuarios</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->
					<div>
					<form action="reportes_nph/reporteusuarios.php" id="frm_enviar_filtro_usuario" method="post">
						<input type="hidden" name="filtro_usuario" id="filtro_usuario">
						<div>
							<button id="btn_formulario_usuario" class="btn btn-danger" name="btn_formulario_usuario"  style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Gestion Usuarios</button>
						</div>
					</form>
                    <script>
						$('#btn_formulario_usuario').on('click', function() {

							var filtro = $('#usuarios_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_usuario').val(filtro);

							console.log('#filtro_usuario');
							console.log($('#filtro_usuario').val());
							document.getElementById('frm_enviar_filtro_usuario').submit();

						});
                    </script> 
						<div class="card-body" style="overflow-x:auto;">
							<div class="table-responsive">
								<table id="usuarios" class="table table-bordered ">
									<thead>
										<tr>
											<th>Opciones</th>
											<th>Id</th>
											<th>Tipo Usuario</th>
											<th>Usuario</th>
											<th>Clave</th>
											<th>Última sesión</th>
											<th>ingresos al sistema</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($row = $resultado->fetch_assoc()) {
											$usuario = $row['nombre_usuario_correo'];
											$idrol = $row['id_rol_usuario'];
											$codestado = $row['cod_estado'];

											$sql1 = "SELECT * from tbl_estado_usuarios
												WHERE codigo_estado='$codestado'";
											$resultado1 = $mysqli->query($sql1);
											$row1 = $resultado1->fetch_assoc();
											$estado = $row1['estado'];


											$sql2 = "SELECT * from tbl_roles_usuarios
												  WHERE id_rol='$idrol'";
											$resultado2 = $mysqli->query($sql2);
											$row2 = $resultado2->fetch_assoc();

										?>
											<tr>

												<td>
													<?php if ($permisoactualizacionusuario == 1) { ?>
														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
														</div>
													<?php } ?>
													<?php if ($permisoeliminacionusuario == 1) { ?>
														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
														</div>
													<?php } ?>
												</td>

												<td><?php echo $row['cod_usuario']; ?></td>
												<td><?php echo $row2['rol']; ?></td>
												<td><?php echo $row['nombre_usuario_correo']; ?></td>
												<td><?php echo $row['clave_usuario']; ?></td>
												<td><?php echo $row['fecha_ultima_conexion']; ?></td>
												<td><?php echo $row['numero_ingresos']; ?></td>
												<td><?php echo $estado; ?></td>
											</tr>

										<?php } ?>
										<?php if ($permisoinsercionusuario == 1) { ?>
											<h1 class="box-title">
												<!-- Button trigger modal Añadir-->
												<button type="button" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadir">
													<i class="fa fa-user-plus"></i>&nbsp; Nuevo usuario
												</button>
											<?php } ?>
											</h1>
									</tbody>
								</table>
							</div>
						</div>
						<!-- Modal Filtro de usuarios-->
						<div class="modal fade" id="filtrogestionusuarios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
									</div>
									<div class="modal-body">
										<form action="reportes_nph/reporteusuarios.php" method="post">

											<div class="form-group">
												<?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, códigos etc) se mostraran los datos que coinciden con lo que usted ingresó, si no ingresa ningún dato, se traeran todos los registros de usuarios en el sistema.</div>"); ?>
												<div class="col-md-8">
													<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtraciòn</b> </label><input class="form-control py-4" id="filtrousuarios" type="text" name="filtrousuarios" placeholder="Ingrese una coincidencia" maxlength="" /></div>
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
										<h5 class="modal-title" id="exampleModalLabel">Eliminación de usuario</h5>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_usuarios.php?op=eliminar" method="post">

											<div class="form-group">
												<?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar al siguiente usuario?,sé eliminaran los historiales de contraseña, registros y transacciones realizados por el usuario, asì como aucalquier información asociada al usuario en especifico.</div>"); ?>
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
						<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Editar</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_usuarios.php?op=editar" method="post">

											<div class="form-group">
												<input type="hidden" name="id" id="idEditar">
											</div>

											<!-- select rol-->
											<div class="form-group">
												<label class="small mb-1" for="rol">Rol</label>
												<select class="form-control" id="rolEditar" name="rol" required>
													<?php $sql = "SELECT * FROM tbl_roles_usuarios";
													$resultado = $mysqli->query($sql);

													while ($row = $resultado->fetch_assoc()) {
														$codigoRol = $row['id_rol'];
														$nombreRol = $row['rol']; ?>
														<option value=<?php echo $codigoRol; ?>> <?php echo $nombreRol;
																								} ?> </option>
												</select>
											</div>
											<div class="form-group">
												<label class="small mb-1" for="correo_electronico">Còdigo de usuario</label>
												<input class="form-control" id="codusuario_editar" type="text" name="codusuario_editar" placeholder="" required readonly />
											</div>
											<div class="form-group">
												<label class="small mb-1" for="correo_electronico">Usuario</label>
												<input class="form-control" id="usuario_editar" type="text" name="usuario_editar" placeholder="Ingrese un usuario" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="contraseña">Contraseña</label>
												<input class="form-control" id="contraseñaeditar" type="password" name="contraseñaeditar" placeholder="Ingrese contraseña" required />
												<div style="margin-top:2px;">
													<input style="margin-left:20px;" type="checkbox" id="mostrar_clave" onclick="mostrarContraseña('contraseñaeditar')" onkeypress="return evitarespacio(event);">&nbsp;&nbsp;Mostrar contraseña
												</div>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="contraseña">Confirmar contraseña</label>
												<input class="form-control" id="contraseña2editar" type="password" name="password2editar" placeholder="Ingrese contraseña" required />
												<div style="margin-top:2px;">
													<input style="margin-left:20px;" type="checkbox" id="mostrar_clave" onclick="mostrarContraseña('contraseña2editar')" onkeypress="return evitarespacio(event);">&nbsp;&nbsp;Mostrar contraseña
												</div>
											</div>

											<div class="form-group d-inline-block">
												<label class="small mb-1" for="fecha_vencimiento">Fecha de vencimiento</label>
												<input class="form-control" value="" id="fecha_vencimientoEditar" type="date" name="fecha_vencimiento" min="2022-07-20" placeholder="Ingrese fecha de vencimiento" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="estado">Estado</label>
												<select class="form-control" id="estadoEditar" name="estado" required>
													<?php $sql4 = "SELECT * FROM tbl_estado_usuarios";
													$resultado4 = $mysqli->query($sql4);

													while ($row4 = $resultado4->fetch_assoc()) { ?>
														<option value=<?php echo $row4['codigo_estado']; ?>> <?php echo $row4['estado'];
																											} ?> </option>
												</select>
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
										<h5 class="modal-title" id="exampleModalLabel">Añadir usuario</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_usuarios.php?op=añadir" method="post">

											<!-- select rol-->
											<div class="form-group">
												<label class="small mb-1" for="rol">Seleccione un rol</label>
												<select class="form-control" id="rol" name="rol" required>
													<?php $sql = "SELECT * FROM tbl_roles_usuarios";
													$resultado = $mysqli->query($sql);

													while ($row = $resultado->fetch_assoc()) {
														$codigoRol = $row['id_rol'];
														$nombreRol = $row['rol']; ?>
														<option value=<?php echo $codigoRol; ?>> <?php echo $nombreRol;
																								} ?> </option>
												</select>
											</div>
											<div class="form-group">
												<label class="small mb-1" for="rol">Seleccione un empleado</label>
												<select class="form-control" id="empleado" name="empleado" required>
													<?php $sql5 = "SELECT * FROM tbl_empleados";
													$resultado5 = $mysqli->query($sql5);

													while ($row5 = $resultado5->fetch_assoc()) {
													?>
														<option value=<?php echo $row5['cod_empleado']; ?>> <?php echo $row5['primer_nombre'] . " " . $row5['primer_apellido'];
																										} ?> </option>
												</select>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="correo_electronico">Usuario o correo</label>
												<input class="form-control" id="usuario_añadir" type="text" name="usuario_añadir" value="" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required />
											</div>
											<div class="form-group">
												<label class="small mb-1" for="correo_electronico">Asignele un parametro de intentos válidos</label>
												<input class="form-control" id="intentos_añadir" type="number" name="intentos_añadir" value="" placeholder="Ingrese un número" min="3" max="10" maxlength="2" required />
											</div>
											<div class="form-group">
												<label class="small mb-1" for="correo_electronico">Asignele un parametro de preguntas de seguridad</label>
												<input class="form-control" id="preguntas_añadir" type="number" name="preguntas_añadir" value="" placeholder="Ingrese un número" min="3" max="10" maxlength="2" required />
											</div>
											<div class="form-group">
												<label class="small mb-1" for="correo_electronico">Correo electronico(se enviaran los datos)</label>
												<input class="form-control" id="correo_editar" type="email" name="correo_añadir" value="" placeholder="Correo electronico" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="contraseña">Contraseña</label>
												<input class="form-control" id="contraseña" type="password" name="contraseña" placeholder="Ingrese contraseña" required />
												<div style="margin-top:2px;">
													<input style="margin-left:20px;" type="checkbox" id="mostrar_clave" onclick="mostrarContraseña('contraseña')" onkeypress="return evitarespacio(event);">&nbsp;&nbsp;Mostrar contraseña
												</div>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="contraseña">Confirmar contraseña</label>
												<input class="form-control" id="contraseña2" type="password" name="password2" value="" placeholder="Ingrese contraseña" required />
												<div style="margin-top:2px;">
													<input style="margin-left:20px;" type="checkbox" id="mostrar_clave" onclick="mostrarContraseña('contraseña2')" onkeypress="return evitarespacio(event);">&nbsp;&nbsp;Mostrar contraseña
												</div>
											</div>


											<div class="form-group d-inline-block">
												<label class="small mb-1" for="fecha_vencimiento">Fecha de vencimiento</label>
												<input class="form-control" value="<?php echo date('d-m-Y', strtotime(date('d-m-Y') . "+ 360 days")); ?>" id="fecha_vencimiento" type="text" name="fecha_vencimiento" placeholder="Ingrese fecha de vencimiento" disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="estado">Estado</label>
												<select class="form-control" id="estado" name="estado" required>
													<?php $sql4 = "SELECT * FROM tbl_estado_usuarios";
													$resultado4 = $mysqli->query($sql4);

													while ($row4 = $resultado4->fetch_assoc()) { ?>
														<option value=<?php echo $row4['codigo_estado']; ?>> <?php echo $row4['estado'];
																											} ?> </option>
												</select>
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
						$('#codusuario_editar').val(datos[1]);
						$('#usuario_editar').val(datos[3]);
						$('#rolEditar').val(null);
						$('#correo_electronicoEditar').val(datos[3]);
						$('#contraseñaeditar').val(datos[3]);
						$('#contraseña2editar').val(datos[3]);
						$('#fecha_creacionEditar').val(datos[3]);
						$('#fecha_vencimientoEditar').val(datos[8]);
						$('#estadoEditar').val(null);
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
					let temp = $("#btn1").clone();
					$("#btn1").click(function() {
						$("#btn1").after(temp);
					});
					$(document).ready(function() {
						var table = $('#usuarios').DataTable({
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