<!DOCTYPE html5>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Empleados gestiones</title>
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
		$sqlgestionempleados = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=38";
		$resultadogestionempleados = $mysqli->query($sqlgestionempleados);
		$filasgestionempleados = $resultadogestionempleados->num_rows;

		if ($filasgestionempleados > 0) {
			$rowgestionempleados = $resultadogestionempleados->fetch_assoc();
			$permisoinserciongestionempleados = $rowgestionempleados['permiso_insercion'];
			$permisoeliminaciongestionempleados = $rowgestionempleados['permiso_eliminacion'];
			$permisoactualizaciongestionempleados = $rowgestionempleados['permiso_actualizacion'];
			$permisoconsultagestionempleados = $rowgestionempleados['permiso_consultar'];
			if ($permisoconsultagestionempleados == 1) {
				$id = $_SESSION['id'];

				$sql3 = "SELECT *from tbl_usuarios_login
				WHERE cod_usuario='$id'";
				$resultado3 = $mysqli->query($sql3);
				$row3 = $resultado3->fetch_assoc();


				$tipo_usuario = $row3['id_rol_usuario'];
				$codempleado = $row3['cod_empleado'];
				$where = "";
				if ($tipo_usuario == 1) {
					$where = "";
				} else {
					$where = "WHERE cod_empleado='$codempleado'";
				}

				$sql = "SELECT * FROM tbl_solicitudes  $where";
				$resultado = $mysqli->query($sql);
				$num = $resultado->num_rows;

				$sql8 = "SELECT *from tbl_empleados
	WHERE cod_empleado='$codempleado'";
				$resultado8 = $mysqli->query($sql8);
				$row8 = $resultado8->fetch_assoc();
				$_SESSION['cod_empleado'] = $codempleado;
	?>

				<div class="content-wrapper">
					<!-- Content Header (Page header) -->
					<div class="content-header">
						<div class="container-fluid">
							<div class="row mb-2">
								<div class="col-sm-6">
									<h1 class="m-0">Solicitudes empleados</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Gestión empleados</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->
					<div>
					<form action="reportes_nph/reportegestionesempleados.php" id="frm_enviar_filtro_gestiones" method="post">
						<input type="hidden" name="filtro_gestiones" id="filtro_gestiones">
						<div>
							<button id="btn_formulario_gestiones" class="btn btn-danger" name="btn_formulario_gestiones" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Gestiones Empleados</button>
						</div>
					</form>
                    <script>
						$('#btn_formulario_gestiones').on('click', function() {

							var filtro = $('#gestionempleados_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_gestiones').val(filtro);

							console.log('#filtro_gestiones');
							console.log($('#filtro_gestiones').val());
							document.getElementById('frm_enviar_filtro_gestiones').submit();

						});
                    </script> 
						<div class="card-body" style="overflow-x:auto;">
							<div class="table-responsive">
								<table id="gestionempleados" class="table table-bordered ">
									<thead>
										<tr>
											<th>Opciones</th>
											<th>Código</th>
											<th>Tipo</th>
											<th>Departamento</th>
											<th>Empleado</th>
											<th>Fecha/Hora de ingreso</th>
											<th>Fecha/Hora inicio</th>
											<th>Fecha/Hora fin</th>
											<th>Justificación</th>
											<th>Observaciones</th>
											<th>A cuenta de</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($row = $resultado->fetch_assoc()) {
											$cod_solicitud = $row['id_solicitud'];
											$codtipo_solicitud = $row['id_tipo_solicitud'];
											$cod_departamento = $row['cod_departamento'];
											$cod_empleado = $row['cod_empleado'];
											$cod_estado = $row['cod_estado'];

											$sql1 = "SELECT * from tbl_tipo_solicitudes WHERE id_tipo_solicitud='$codtipo_solicitud'";
											$resultado1 = $mysqli->query($sql1);
											$row1 = $resultado1->fetch_assoc();

											$sql2 = "SELECT * from tbl_departamentos
												  WHERE cod_departamento='$cod_departamento'";
											$resultado2 = $mysqli->query($sql2);
											$row2 = $resultado2->fetch_assoc();

											$sql3 = "SELECT * from tbl_empleados
												  WHERE cod_empleado='$cod_empleado'";
											$resultado3 = $mysqli->query($sql3);
											$row3 = $resultado3->fetch_assoc();

											$sql4 = "SELECT * from tbl_estado_entrega_aprobacion
												  WHERE cod_estado='$cod_estado'";
											$resultado4 = $mysqli->query($sql4);
											$row4 = $resultado4->fetch_assoc();


										?>
											<tr>
												<td>
													<?php if ($permisoactualizaciongestionempleados == 1) { ?>
														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnEditar" style="border:20px" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarsolicitud"><i class="nav-icon fas fa-pen"></i></button>
														</div>
													<?php } ?>
													<?php if ($permisoeliminaciongestionempleados == 1) { ?>
														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminarsolicitud"><i class="nav-icon fas fa-trash"></i></button>
														</div>
													<?php } ?>
												</td>

												<td><?php echo $row['id_solicitud']; ?></td>
												<td><?php echo $row1['solicitud']; ?></td>
												<td><?php echo $row2['nombre_departamento']; ?></td>
												<td><?php echo $row3['primer_nombre'] . " " . $row3['primer_apellido']; ?></td>
												<td><?php echo $row['fechahora_ingreso']; ?></td>
												<td><?php echo $row['fecha_inicio']; ?></td>
												<td><?php echo $row['fecha_fin']; ?></td>
												<td><?php echo $row['justificacion']; ?></td>
												<td><?php echo $row4['detalles_del_estado']; ?></td>
												<td><?php echo $row['opcion_permiso']; ?></td>
												<td><?php echo $row4['nombre_del_estado']; ?></td>

											</tr>

										<?php } ?>
										<?php if ($permisoinserciongestionempleados == 1) { ?>
											<h1 class="box-title">
												<!-- Button trigger modal Añadir-->
												<button type="button" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadirsolicitud">
													<i class="fa fa-plus-circle"></i>&nbsp; Ingresar nueva solicitud
												</button>
											</h1>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="modal fade" id="modalEliminarsolicitud" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Eliminación de solicitu</h5>
									</div>
									<div class="modal-body">
										<form action="modelos/crudempleados_gestiones.php?op=eliminar" method="post">

											<div class="form-group">
												<?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar la siguiente solicitud?,se eliminaran todos los registros y acciones asociadas a esta solicitud.</div>"); ?>
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
						<!-- Modal Filtro de empleados-->
                        <div class="modal fade" id="filtrogestiones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reportegestionesempleados.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtración</b> </label><input class="form-control py-4" id="filtrogestiones" type="text" name="filtrogestiones" placeholder="Ingrese una coincidencia" maxlength=""  /></div>
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
						<div class="modal fade" id="modalEditarsolicitud" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Editar información de la solicitud</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crudempleados_gestiones.php?op=editar" method="post">

											<div class="form-group">
												<input type="hidden" name="id" id="idEditar">
											</div>

											<!-- select rol-->
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Código de solicitud/<b> </label><input class="form-control py-4" id="codsolicitudeditar" type="text" name="codsolicitudeditar" placeholder="" readonly required /></div>
											</div>
											<div class="col-md-6">
												<label class="small mb-1" for="rol">Departamento solicitante</label>
												<select class="form-control" id="departamentosolicitudeditar" name="departamentosolicitudeditar" required>
													<?php $sql7 = "SELECT * FROM tbl_departamentos";
													$resultado7 = $mysqli->query($sql7);

													while ($row7 = $resultado7->fetch_assoc()) { ?>
														<option value=<?php echo $row7['cod_departamento']; ?>> <?php echo $row7['nombre_departamento'];
																											} ?> </option>
												</select>
											</div>
											<div class="col-md-6">
												<label class="small mb-1" for="rol">Seleccione tipo de solicitud</label>
												<select class="form-control" id="tiposolicitudeditar" name="tiposolicitudeditar" required>
													<?php $sql = "SELECT * FROM tbl_tipo_solicitudes";
													$resultado = $mysqli->query($sql);

													while ($row = $resultado->fetch_assoc()) { ?>
														<option value=<?php echo $row['id_tipo_solicitud']; ?>> <?php echo $row['solicitud'];
																											} ?> </option>
												</select>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Solicitante</b> </label><input class="form-control py-4" id="nombresolicitanteeditar" type="text" name="nombresolocitanteeditar" placeholder="" readonly required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Fecha/Hora ingreso solicitud</b> </label><input class="form-control py-4" id="fechahorasolicitudeditar" type="text" name="fechahorasolicitudeditar" value="<?php $fechahora = date("Y-n-j H:i:s");
																																																																						echo $fechahora; ?>" placeholder="" readonly required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Fecha y hora inicio</b> </label><input class="form-control py-4" id="fechainiciosolicitudeditar" type="datetime-local" min="<?php $fechahora = date('d/m/y', time());
																																																															echo $fechahora; ?>" name="fechainiciosolicitudeditar" required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Fecha y hora final</b> </label><input class="form-control py-4" id="fechafinalsolicitudeditar" type="datetime-local" min="" name="fechafinalsolicitudeditar" required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Escriba una justificación</b> </label><input class="form-control py-4" id="justificacionsolicitudeditar" type="text" maxlength="200" name="justificacionsolicitudeditar" required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>A cuenta de(vacaciones, sueldo o sin goce de sueldo etc)</b> </label><input class="form-control py-4" id="acuentadesolicitudeditar" type="text" maxlength="200" name="acuentadesolicitudeditar" required /></div>
											</div>

											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
												<button type="submit" class="btn btn-primary">Actualizar información</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>

						<!-- Modal AÑADIR-->
						<div class="modal fade" id="modalAñadirsolicitud" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Ingresar nueva solicitud</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crudempleados_gestiones.php?op=añadir" method="post">
											<div class="col-md-6">
												<label class="small mb-1" for="rol">Departamento solicitante</label>
												<select class="form-control" id="departamentosolicitudañadir" name="departamentosolicitudañadir" required>
													<?php $sql7 = "SELECT * FROM tbl_departamentos";
													$resultado7 = $mysqli->query($sql7);

													while ($row7 = $resultado7->fetch_assoc()) { ?>
														<option value=<?php echo $row7['cod_departamento']; ?>> <?php echo $row7['nombre_departamento'];
																											} ?> </option>
												</select>
											</div>
											<div class="col-md-6">
												<label class="small mb-1" for="rol">Seleccione tipo de solicitud</label>
												<select class="form-control" id="tiposolicitudañadir" name="tiposolicitudañadir" required>
													<?php $sql = "SELECT * FROM tbl_tipo_solicitudes";
													$resultado = $mysqli->query($sql);

													while ($row = $resultado->fetch_assoc()) { ?>
														<option value=<?php echo $row['id_tipo_solicitud']; ?>> <?php echo $row['solicitud'];
																											} ?> </option>
												</select>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Solicitante</b> </label><input class="form-control py-4" id="nombresolicitante" type="text" name="nombresolocitante" value="<?php echo $row8['primer_nombre'] . " " . $row8['primer_apellido']; ?>" placeholder="" readonly required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Fecha/Hora ingreso solicitud</b> </label><input class="form-control py-4" id="fechahorasolicitud" type="text" name="fechahorasolicitud" value="<?php $fechahora = date("Y-n-j H:i:s");
																																																																			echo $fechahora; ?>" placeholder="" readonly required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Fecha y hora inicio</b> </label><input class="form-control py-4" id="fechainiciosolicitud" type="datetime-local" min="<?php $fechahora = date('d/m/y', time());
																																																													echo $fechahora; ?>" name="fechainiciosolicitud" required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Fecha y hora final</b> </label><input class="form-control py-4" id="fechafinalsolicitud" type="datetime-local" min="" name="fechafinalsolicitud" required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Escriba una justificación</b> </label><input class="form-control py-4" id="justificacionsolicitud" type="text" maxlength="200" name="justificacionsolicitud" required /></div>
											</div>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>A cuenta de(vacaciones, sueldo o sin goce de sueldo etc)</b> </label><input class="form-control py-4" id="acuentadesolicitud" type="text" maxlength="200" name="acuentadesolicitud" required /></div>
											</div>

									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
										<button type="submit" name="registrarempleado" class="btn btn-primary">Ingresar solicitud</button>
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


					$('.btnEditar').on('click', function() {
						$tr = $(this).closest('tr');

						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						$('#codsolicitudeditar').val(datos[1]);
						$('#nombresolicitanteeditar').val(datos[4]);
						$('#fechahorasolicitudeditar').val(datos[5]);
						$('#fechainiciosolicitudeditar').val(datos[6]);
						$('#fechafinalsolicitudeditar').val(datos[7]);
						$('#justificacionsolicitudeditar').val(datos[8]);
						$('#acuentadesolicitudeditar').val(datos[10]);

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
						$('#idEliminarLabel').text(datos[2]);
					});
					let temp = $("#btn1").clone();
					$("#btn1").click(function() {
						$("#btn1").after(temp);
					});
					$(document).ready(function() {
						var table = $('#gestionempleados').DataTable({
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

					function check(e) {
						tecla = (document.all) ? e.keyCode : e.which;
						if (tecla == 8) {
							return true;
						}
						patron = /[A-Za-z]/;
						tecla_final = String.fromCharCode(tecla);
						return patron.test(tecla_final);
					}

					function validarNumero(e) {
						tecla = (document.all) ? e.keyCode : e.which;
						if (tecla == 8) return true;
						patron = /[0-9-]/;
						te = String.fromCharCode(tecla);
						return patron.test(te);
					}

					function email(e) {
						tecla = (document.all) ? e.keyCode : e.which;
						if (tecla == 8) return true;
						patron = /[A-Za-z0-9@_.]/;
						te = String.fromCharCode(tecla);
						return patron.test(te);
					}
				</script>

</html>