<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Planificación De Transporte</title>
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
		$sqlgestionplanificacion = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=27";
		$resultadogestionplanificacion = $mysqli->query($sqlgestionplanificacion);
		$filasgestionplanificacion = $resultadogestionplanificacion->num_rows;

		if ($filasgestionplanificacion > 0) {
			$rowgestionplanificacion = $resultadogestionplanificacion->fetch_assoc();
			$permisoinserciongestionplanificacion = $rowgestionplanificacion['permiso_insercion'];
			$permisoeliminaciongestionplanificacion = $rowgestionplanificacion['permiso_eliminacion'];
			$permisoactualizaciongestionplanificacion = $rowgestionplanificacion['permiso_actualizacion'];
			$permisoconsultagestionplanificacion = $rowgestionplanificacion['permiso_consultar'];
			if ($permisoconsultagestionplanificacion == 1) {
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

				$sql = "SELECT pl.cod_planificacion, pl.id_solicitud, concat(em.primer_nombre, ' ', em.segundo_nombre, ' ', em.primer_apellido, ' ', em.segundo_apellido) empleado, concat(em2.primer_nombre, ' ', em2.segundo_nombre, ' ', em2.primer_apellido, ' ', em2.segundo_apellido) cod_empleado_motorista, pl.Cantidad_personas,v.tipo_vehiculo, s.fecha_inicio, s.fecha_fin, s.justificacion, pl.observacion 
   				FROM tbl_planificacion_transporte pl, tbl_vehiculos v, tbl_solicitudes s, tbl_empleados em, tbl_empleados em2  
				WHERE em.cod_empleado = s.cod_empleado AND pl.cod_vehiculo = v.cod_vehiculo AND s.id_solicitud = pl.id_solicitud AND em2.cod_empleado = pl.cod_empleado_motorista and s.cod_estado = 1 ORDER BY cod_planificacion DESC;";
				$resultado_solicitudes = $mysqli->query($sql);
				$num = $resultado_solicitudes->num_rows;
				$_SESSION['cantidadusuarios'] = $num;

				$sql = "SELECT pl.cod_planificacion, pl.id_solicitud, concat(em.primer_nombre, ' ', em.segundo_nombre, ' ', em.primer_apellido, ' ', em.segundo_apellido) empleado, pl.`cod_empleado_motorista` cod_empleado_motorista, pl.Cantidad_personas, pl.cod_vehiculo tipo_vehiculo, s.fecha_inicio, s.fecha_fin, s.justificacion, pl.observacion 
				FROM tbl_planificacion_transporte pl, tbl_solicitudes s, tbl_empleados em 
				WHERE em.cod_empleado = s.cod_empleado AND pl.cod_vehiculo IS NULL AND pl.cod_empleado_motorista IS NULL AND s.id_solicitud = pl.id_solicitud AND s.cod_estado = 1 
				ORDER BY cod_planificacion DESC;";
				$resultado_solicitudes2 = $mysqli->query($sql);
				$num2 = $resultado_solicitudes2->num_rows;
				$_SESSION['cantidadusuarios'] += $num2;






	?>

				<div class="content-wrapper">
					<!-- Content Header (Page header) -->
					<div class="content-header">
						<div class="container-fluid">
							<div class="row mb-2">
								<div class="col-sm-6">
									<h1 class="m-0">Planificación De Transporte</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Planificación De Transporte</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->
					</div>

					<!-- /.content-header -->

					<form action="reportes_nph/reporteplanificaciontransporte1.php" id="frm_enviar_planificacion" method="post">
						<input type="hidden" name="filtro" id="input_planificacion">
						<div>
							<button id="btn_enviar_formulario" class="btn btn-danger" name="btnreporteplanificaciontransporte" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de planificacion transporte</button>
						</div>
					</form>
					<script>
						$('#btn_enviar_formulario').on('click', function() {

							var filtro = $('#planificacion_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#input_planificacion').val(filtro);

							console.log('#input_planificacion');
							console.log($('#input_planificacion').val());
							document.getElementById('frm_enviar_planificacion').submit();
						});
					</script>
					<div>
						<div>

							<div class="card-body" style="overflow-x:auto;">
								<div class="table-responsive">
									<table id="planificacion" class="table table-bordered ">

										<thead>
											<tr>
												<th>Opciones</th>
												<th>Id</th> <!-- tbl_solicitudes -->
												<th>ID_Solicitud</th> <!-- tbl_solicitudes -->
												<th>Empleado Responsable</th> <!-- tbl_solicitudes -->
												<th>Empleado Motorista</th> <!-- tbl_solicitudes -->
												<!-- Cantidad_personas -->
												<th>Cantidad personas</th> <!-- tbl_solicitudes -->
												<th>Vehiculo</th> <!-- tbl_solicitudes -->
												<th>Fecha de Entrada</th>
												<th>Fecha de Salida</th> <!-- tbl_solicitudes -->
												<th>Actividad</th> <!-- tbl_solicitudes -->
												<th>Observacion</th> <!-- tbl_solicitudes -->
											</tr>
										</thead>
										<tbody>
											<?php if ($num2 > 0) {
												while ($row_solicitudes2 = $resultado_solicitudes2->fetch_assoc()) { ?>
													<tr>
														<td>
															<?php if ($permisoactualizaciongestionplanificacion == 1) { ?>
																<!-- BOTTON EDITAR -->
																<div class="col-md-6">
																	<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
																</div>
															<?php } ?>
															<!-- BOTTON ELIMINAR -->
															<?php if ($permisoeliminaciongestionplanificacion == 1) { ?>
																<div class="col-md-6">
																	<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_vehiculo" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
																</div>
															<?php } ?>
														</td>
														<!-- fecha_inicio, s.fecha_fin -->
														<td><?php echo $row_solicitudes2['cod_planificacion']; ?></td>
														<td><?php echo $row_solicitudes2['id_solicitud']; ?></td>
														<td><?php echo $row_solicitudes2['empleado']; ?></td>
														<td><?php echo 'NO ASIGNADO'; ?></td>
														<td><?php echo $row_solicitudes2['Cantidad_personas']; ?></td>
														<td><?php echo 'NO ASIGNADO'; ?></td>
														<!--  -->
														<td><?php echo $row_solicitudes2['fecha_inicio']; ?></td>
														<td><?php echo $row_solicitudes2['fecha_fin']; ?></td>
														<td><?php echo $row_solicitudes2['justificacion']; ?></td>
														<td><?php echo $row_solicitudes2['observacion']; ?></td>
													</tr>
											<?php }
											} ?>

											<?php while ($row_solicitudes = $resultado_solicitudes->fetch_assoc()) { ?>
												<tr>
													<td>
														<?php if ($permisoactualizaciongestionplanificacion == 1) { ?>
															<!-- BOTTON EDITAR -->
															<div class="col-md-6">
																<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
															</div>
														<?php } ?>
														<!-- BOTTON ELIMINAR -->
														<?php if ($permisoeliminaciongestionplanificacion == 1) { ?>
															<div class="col-md-6">
																<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_vehiculo" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
															</div>
														<?php } ?>
													</td>
													<!-- fecha_inicio, s.fecha_fin -->
													<td><?php echo $row_solicitudes['cod_planificacion']; ?></td>
													<td><?php echo $row_solicitudes['id_solicitud']; ?></td>
													<td><?php echo $row_solicitudes['empleado']; ?></td>
													<td><?php echo $row_solicitudes['cod_empleado_motorista']; ?></td>
													<td><?php echo $row_solicitudes['Cantidad_personas']; ?></td>
													<td><?php echo $row_solicitudes['tipo_vehiculo']; ?></td>
													<td><?php echo $row_solicitudes['fecha_inicio']; ?></td>
													<td><?php echo $row_solicitudes['fecha_fin']; ?></td>
													<td><?php echo $row_solicitudes['justificacion']; ?></td>
													<td><?php echo $row_solicitudes['observacion']; ?></td>
												</tr>
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

							<!-- Modal ELIMINAR-->
							<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">Eliminación De Solicitud</h5>
										</div>
										<div class="modal-body">
											<form action="modelos/crud_planificacion.php?op=eliminar" method="post">

												<div class="form-group">
													<?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar la siguiente solicitud?,sé eliminaran los historiales almacenados en la base de datos.</div>"); ?>
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
											<h5 class="modal-title" id="exampleModalLabel">Editar Solicitud De Transporte</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
										</div>
										<div class="modal-body">
											<form action="modelos/crud_planificacion.php?op=editar" method="post">
												<!-- FORMULARIO -->

												<input type="hidden" name="id" id="id_editar">

												<div class="form-group">
													<label class="small mb-1" for="descripcion">Id Solicitud</label>
													<input class="form-control" id="id_Solicitud_editar" type="text" name="id_Solicitud_editar" value="" maxlength="30" required readonly />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="empleado">Seleccione un Empleado Motorista</label>
													<select class="form-control" id="empleado_editar" name="empleado" required>
														<?php $sql = "SELECT cod_empleado, concat(primer_nombre, ' ', segundo_nombre, ' ', primer_apellido, ' ', segundo_apellido) empleado FROM `tbl_empleados` WHERE cod_puesto_empleado=8;";
														$resultado = $mysqli->query($sql);
														while ($row = $resultado->fetch_assoc()) { ?>
															<option value=<?php echo $row['cod_empleado']; ?>> <?php echo $row['empleado'];
																											} ?></option>
													</select>
												</div>

												<div class="form-group">
													<label class="small mb-1" for="vehiculo">Seleccione un vehiculo</label>
													<select class="form-control" id="vehiculo_editar" name="vehiculo" required>
														<?php $sql = "SELECT * FROM `tbl_vehiculos`;";
														$resultado = $mysqli->query($sql);
														while ($row = $resultado->fetch_assoc()) { ?>
															<option value=<?php echo $row['cod_vehiculo']; ?>> <?php echo $row['tipo_vehiculo'];
																											} ?></option>
													</select>
												</div>												
												<div class="form-group">
													<label class="small mb-1" for="actividad">Actividad</label>
													<input class="form-control" id="actividad_editar" type="text" name="actividad" value="" placeholder="Detalle alguna la actividad" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">observacion</label>
													<input class="form-control" id="observacion_editar" type="text" name="observacion" value="" placeholder="detalle alguna observacion" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" required />
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

		<td><input class="form-control" type="text" id="precio1" name="precio1" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>
	</tr> 
-->
					<script>
						$('#btn_enviar_formulario').on('click', function() {

							var filtro = $('#usuarios_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#input_probar').val(filtro);

							console.log('#input_probar');
							console.log($('#input_probar').val());
							document.getElementById('frm_enviar_filtro').submit();

						});
					</script>

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
							letras = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz ";
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
							letras = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789-";
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
							$('#id_editar').val(datos[1]);
							$('#id_Solicitud_editar').val(datos[1]);
							$('#empleado_editar').val(datos[4]);
							$('#vehiculo_editar').val(datos[5]);
							$('#fecha_inicio_Editar').val(datos[6]);
							$('#fecha_final_editar').val(datos[7]);
							$('#observacion_editar').val(datos[9]);
							$('#actividad_editar').val(datos[8]);
							//actividad_editar
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
							var table = $('#planificacion').DataTable({
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