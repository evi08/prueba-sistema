<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Solicitud De Transporte</title>
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
		$sqlgestiontransporte = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=27";
		$resultadogestiontransporte = $mysqli->query($sqlgestiontransporte);
		$filasgestiontransporte = $resultadogestiontransporte->num_rows;

		if ($filasgestiontransporte  > 0) {
			$rowgestiontransporte = $resultadogestiontransporte->fetch_assoc();
			$permisoinserciongestiontransporte = $rowgestiontransporte['permiso_insercion'];
			$permisoeliminaciongestiontransporte = $rowgestiontransporte['permiso_eliminacion'];
			$permisoactualizaciongestiontransporte = $rowgestiontransporte['permiso_actualizacion'];
			$permisoconsultagestiontransporte = $rowgestiontransporte['permiso_consultar'];
			if ($permisoconsultagestiontransporte == 1) {
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

				$sql = "SELECT s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre, ' ', em.segundo_nombre, ' ', em.primer_apellido, ' ', em.segundo_apellido) empleado, s.fechahora_ingreso, s.fecha_fin, s.fecha_inicio, p.Cantidad_personas, s.justificacion, s.opcion_permiso, e.nombre_del_estado 
				FROM tbl_solicitudes s,  tbl_departamentos d, tbl_empleados em, tbl_estado_entrega_aprobacion e, tbl_planificacion_transporte p 
				WHERE d.cod_departamento = s.cod_departamento AND em.cod_empleado = s.cod_empleado AND e.cod_estado = s.cod_estado AND s.id_tipo_solicitud = 5 AND p.id_solicitud = s.id_solicitud
				ORDER BY id_solicitud DESC;";
				$resultado = $mysqli->query($sql);
				$num = $resultado->num_rows;
				$_SESSION['cantidadusuarios'] = $num; ?>

				<div class="content-wrapper">
					<!-- Content Header (Page header) -->
					<div class="content-header">
						<div class="container-fluid">
							<div class="row mb-2">
								<div class="col-sm-6">
									<h1 class="m-0">Solicitud De Transporte</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Solicitud De Transporte</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->

					<form action="reportes_nph/reportetransporte.php" id="frm_enviar_transporte" method="post">
						<input type="hidden" name="filtrotransporte" id="input_transporte">
						<div>
							<button id="btn_enviar_formulario" class="btn btn-danger" name="btnreportetransporte" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Transporte</button>
						</div>
					</form>
					<script>
						$('#btn_enviar_formulario').on('click', function() {

							var filtro = $('#Transporte_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#input_transporte').val(filtro);

							console.log('#input_transporte');
							console.log($('#input_transporte').val());
							document.getElementById('frm_enviar_transporte').submit();

						});
					</script>
					<div>
						<div>

							<!-- TABLA -->
							<div class="card-body" style="overflow-x:auto;">
								<div class="table-responsive">
									<table id="Transporte" class="table table-bordered ">
										<thead>
											<tr>
												<th>Opciones</th>
												<th>Id</th> <!-- tbl_solicitudes -->
												<th>Departamento</th> <!-- tbl_solicitudes -->
												<th>Empleado</th> <!-- tbl_solicitudes -->
												<th>Fecha de solicitud</th> <!-- tbl_solicitudes -->
												<th>Fecha de inicio</th> <!-- tbl_solicitudes -->
												<th>Fecha de fin</th> <!-- tbl_solicitudes -->
												<th>Cantidad de personas</th>
												<th>Justificacion</th> <!-- tbl_solicitudes -->
												<th>Detalle de Permiso</th> <!-- tbl_solicitudes -->
												<th>Estado solicitud</th> <!-- tbl_solicitudes -->
											</tr>
										</thead>
										<tbody>
											<?php while ($row_solicitudes = $resultado->fetch_assoc()) { ?>
												<tr>
													<td>
														<div class="d-flex m-0">
															<?php if ($permisoactualizaciongestiontransporte == 1) { ?>
																<!-- Button trigger modal Editar-->
																<div class="p-1 m-0">
																	<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
																</div>
															<?php } ?>
															<!-- Button trigger modal Eliminar-->
															<?php if ($permisoeliminaciongestiontransporte == 1) { ?>
																<div class="p-1 m-0">
																	<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
																</div>
															<?php } ?>

															<?php if ($tipo_usuario == 1) { ?>
																<!-- Button trigger modal Aprobar-->
																<label for="">|</label>
																<div class="p-1 m-0">
																	<button type="button" id="" class="btn btn-primary btn-xs btn_aprobar" style="border:20px;background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalConfirmar">
																		<i class="nav-icon fas fa-user-check"></i>
																	</button>
																</div>
																<!-- Button trigger modal Rechazar-->
																<div class="p-1 m-0">
																	<button type="button" id="" class="btn btn-primary btn-xs btn_rechazar" style="border:20px;background:red" data-bs-toggle="modal" data-bs-target="#modalConfirmar">
																		<i class="nav-icon fas fa-user-alt-slash"></i>
																	</button>
																</div>
															<?php } ?>
														</div>
													</td>
													<!-- s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre, ' ', em.segundo_nombre, ' ', em.primer_apellido, ' ', em.segundo_apellido) 
													empleado, s.fechahora_ingreso, s.fecha_fin, s.fecha_inicio, 
													p.Cantidad_personas, s.justificacion, s.opcion_permiso, e.nombre_del_estado -->
													<td><?php echo $row_solicitudes['id_solicitud']; ?></td>
													<td><?php echo $row_solicitudes['nombre_departamento']; ?></td>
													<td><?php echo $row_solicitudes['empleado']; ?></td>
													<td><?php echo $row_solicitudes['fechahora_ingreso']; ?></td>
													<td><?php echo $row_solicitudes['fecha_inicio']; ?></td>
													<td><?php echo $row_solicitudes['fecha_fin']; ?></td>
													<td><?php echo $row_solicitudes['Cantidad_personas']; ?></td>
													<td><?php echo $row_solicitudes['justificacion']; ?></td>
													<td><?php echo $row_solicitudes['opcion_permiso']; ?></td>
													<td><?php echo $row_solicitudes['nombre_del_estado']; ?></td>
												</tr>
											<?php } ?>

											<?php if ($permisoinserciongestiontransporte == 1) { ?>
												<h1 class="box-title">
													<!-- Button trigger modal Añadir-->
													<button type="button" id="btnAñadir" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadir">
														<i class="fa fa-plus-circle"></i>&nbsp; Nueva Solicitud De Transporte
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

							<!-- Modal CONFIRMAR-->
							<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">Confirmar Solicitud De Transporte</h5>
										</div>
										<div class="modal-body">
											<form id="frm_confirmar" action="modelos/crud_transporte.php?op=confirmar" method="post">
												<!-- FORMULARIO -->

												<div class="form-group">
													<label id="label_id_confirmar"></label>
												</div>
												<!-- label_id_confirmar id_confirmar -->

												<div class="form-group">
													<input type="hidden" name="id" id="id_confirmar">
												</div>

												<div class="form-group">
													<input type="hidden" name="estado" id="id_estado_confirmar">
												</div>

												<div class="form-group">
													<input type="hidden" name="" id="estado_confirmar">
												</div>

												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
													<button type="button" class="btn btn-primary" onclick="validar_confirmar()">Confirmar</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div> <!-- Modal confirmar-->

							<!-- Modal ELIMINAR-->
							<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">Eliminación De Solicitud transporte</h5>
										</div>
										<div class="modal-body">
											<form action="modelos/crud_transporte.php?op=eliminar" method="post">

												<div class="form-group">
													<?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar la siguiente solicitud?,sé eliminaran los historiales almacenados en la base de datos.</div>"); ?>
													<label id="idEliminarLabel"></label>
												</div>

												<div class="form-group">
													<input type="hidden" name="ideliminarsolitransporte" id="ideliminarsolitransporte">
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
											<form action="modelos/crud_transporte.php?op=editar" method="post">
												<!-- FORMULARIO -->

												<input type="hidden" name="id" id="id_editar">

												<div class="form-group">
													<label class="small mb-1" for="descripcion">Id Solicitud</label>
													<input class="form-control" id="id_Solicitud_editar" type="text" name="id_Solicitud_editar" value="" maxlength="30" required readonly />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="departamento">Seleccione un departamento</label>
													<select class="form-control" id="departamento_editar" name="departamento" required>
														<?php $sql = "SELECT * FROM tbl_departamentos";
														$resultado = $mysqli->query($sql);
														while ($row = $resultado->fetch_assoc()) { ?>
															<option value=<?php echo $row['cod_departamento']; ?>><?php echo $row['nombre_departamento']; ?><?php	} ?></option>
													</select>
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Fecha de Inicio</label>
													<input class="form-control py-4" id="fecha_inicio_Editar" type="Datetime-local" name="fecha_inicio" placeholder="Ingrese Fecha y hora de salida" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Fecha final</label>
													<input class="form-control" id="fecha_final_editar" type="Datetime-local" name="fecha_final" placeholder="Ingrese Fecha y hora de entrada" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">cantidad de personas</label>
													<input class="form-control" id="capacidad_editar" type="number" name="capacidad" value="" placeholder="Ingrese cantidad de personas" min=0 max=10 maxlength="5" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Justificacion</label>
													<input class="form-control" id="justificacion_editar" type="text" name="justificacion" value="" placeholder="Ingrese justificacion" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Permiso</label>
													<input class="form-control" id="permiso_editar" type="text" name="permiso" value="" placeholder="detalle sus permiso" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);" required />
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
											<h5 class="modal-title" id="exampleModalLabel">Añadir Solicitud De Transporte</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
										</div>
										<div class="modal-body">
											<form action="modelos/crud_transporte.php?op=añadir" method="post">

												<!-- FORMULARIO -->

												<div class="form-group">
													<label class="small mb-1" for="">Seleccione un departamento</label>
													<select class="form-control" id="departamento" name="departamento" required>
														<?php $sql = "SELECT * FROM tbl_departamentos";
														$resultado = $mysqli->query($sql);
														while ($row = $resultado->fetch_assoc()) { ?>
															<option value=<?php echo $row['cod_departamento']; ?>> <?php echo $row['nombre_departamento'];
																												} ?></option>
													</select>
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Fecha de Inicio</label>
													<input class="form-control py-4" id="fecha_inicio" type="datetime-local" name="fecha_inicio" placeholder="Ingrese Fecha y hora de salida" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Fecha final</label>
													<input class="form-control" id="fecha_final" type="Datetime-local" name="fecha_final" placeholder="Ingrese Fecha y hora de entrada" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">cantidad de personas</label>
													<input class="form-control" id="cantidad" type="number" name="cantidad" value="" placeholder="Ingrese cantidad de personas" min=0 max=10 maxlength="5" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Justificacion</label>
													<input class="form-control" id="justificacion" type="text" name="justificacion" value="" placeholder="Ingrese justificacion" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" required />
												</div>

												<div class="form-group">
													<label class="small mb-1" for="">Permiso</label>
													<input class="form-control" id="permiso" type="text" name="permiso" value="" placeholder="detalle sus permiso" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" required />
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
	} ?>
					</div>
					<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
					<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
					<script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
					<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
					<!-- JavaScript Bundle with Popper -->
					<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
					<script src="/bootstrap/js/bootstrap.bundle.min.js"></script>
					<script src="js/scripts.js" type="text/javascript"></script>

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

					<script>
						// btnProbar	

						$('.btn_aprobar').on('click', function() {
							$tr = $(this).closest('tr');
							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de aprobar la solicitud ' + datos[1] + '?';
							$('#id_estado_confirmar').val(1);
							$('#id_confirmar').val(datos[1]);
							$('#estado_confirmar').val(datos[10]);
						});
						//btn_devolver

						$('.btn_rechazar').on('click', function() {
							$tr = $(this).closest('tr');
							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de rechazar la solicitud ' + datos[1] + '?';
							$('#id_estado_confirmar').val(2);
							$('#id_confirmar').val(datos[1]);
							$('#estado_confirmar').val(datos[10]);

						});

						function validar_confirmar() {
							var estado = document.getElementById('estado_confirmar').value;
							var id_estado = document.getElementById('id_estado_confirmar').value;

							if (id_estado == 1) {
								if (estado == 'Aprobada') {
									alert('La solicitud ya se encuentra actualmente aprobada');
									die();
								} else {
									document.getElementById('frm_confirmar').submit();
								}
							}

							if (id_estado == 2) {
								if (estado == 'Rechazada') {
									alert('La solicitud ya se encuentra actualmente rechazada');
									die();
								} else {
									document.getElementById('frm_confirmar').submit();
								}
							}
						}
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
							$('#departamento_editar').val(1);
							$('#fecha_inicio_Editar').val(datos[5]);
							$('#fecha_final_editar').val(datos[6]);
							$('#capacidad_editar').val(datos[7]);
							$('#justificacion_editar').val(datos[8]);
							$('#permiso_editar').val(datos[9]);
							$('#vehiculo_Editar').val(null);
							$('#estado_Editar').val(null);
						});
						
						$('.btnEliminar').on('click', function() {

							$tr = $(this).closest('tr');

							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							$('#ideliminarsolitransporte').val(datos[1]);
							$('#idEliminarLabel').text(datos[3]);
						});

						$(document).ready(function() {
							var table = $('#Transporte').DataTable({
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