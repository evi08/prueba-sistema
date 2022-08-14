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
		$sqlgestionrequi = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=57";
		$resultadogestionrequi = $mysqli->query($sqlgestionrequi);
		$filasgestionrequi = $resultadogestionrequi->num_rows;

		if ($filasgestionrequi  > 0) {
			$rowgestionrequi = $resultadogestionrequi->fetch_assoc();
			$permisoinserciongestionrequi = $rowgestionrequi['permiso_insercion'];
			$permisoeliminaciongestionrequi = $rowgestionrequi['permiso_eliminacion'];
			$permisoactualizaciongestionrequi = $rowgestionrequi['permiso_actualizacion'];
			$permisoconsultagestionrequi = $rowgestionrequi['permiso_consultar'];
			if ($permisoconsultagestionrequi == 1) {
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

				$sql = "SELECT s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre,\" \",em.segundo_nombre,\" \",em.primer_apellido,\" \",em.segundo_apellido) empleado, h.hogar  , s.fechahora_ingreso, s.justificacion, e.nombre_del_estado 
	FROM tbl_solicitudes s, tbl_departamentos d, tbl_empleados em, tbl_hogares h, tbl_estado_entrega_aprobacion e, tbl_requisicion_activos ra 
	WHERE d.cod_departamento = s.cod_departamento AND em.cod_empleado = s.cod_empleado AND h.id_hogar = ra.id_hogar AND e.cod_estado = s.cod_estado AND s.id_solicitud = ra.id_solicitud AND s.id_tipo_solicitud = 7 
	ORDER BY s.id_solicitud DESC;";
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
									<h1 class="m-0">Solicitudes de requisición</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Solicitudes de requisición</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->
					<form action="reportes_nph/reporterequi.php" id="frm_enviar_requi" method="post">
						<input type="hidden" name="filtrorequi" id="filtrorequi">
						<div>
							<button id="btn_enviar_requi" class="btn btn-danger" name="btn_enviar_requi" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Solicitud de Requisiciones</button>
						</div>
					</form>
                    <script>
						$('#btn_enviar_requi').on('click', function() {

							var filtro = $('#requi_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtrorequi').val(filtro);

							console.log('#filtrorequi');
							console.log($('#filtrorequi').val());
							document.getElementById('frm_enviar_requi').submit();

                        });
                        </script>
					
					<div>
						<!-- TABLA PRINCIPAL -->
						<div class="card-body" style="overflow-x:auto;" id="reporte">
							<div class="table-responsive" id="reporte">
								<table id="requi" class="table table-bordered ">
									<thead>
										<tr>
											<th>Opciones</th>
											<th>Id</th> <!-- tbl_solicitudes -->
											<th>Departamento</th> <!-- tbl_solicitudes -->
											<th>Empleado</th> <!-- tbl_solicitudes -->
											<th>Hogar</th> <!-- tbl_solicitudes -->
											<th>Fecha de solicitud</th> <!-- tbl_solicitudes -->
											<th>Justificacion</th> <!-- tbl_solicitudes -->
											<th>Estado solicitud</th> <!-- tbl_solicitudes -->
										</tr>
									</thead>
									<tbody>
										<?php while ($row_solicitudes = $resultado->fetch_assoc()) { ?>
											<tr>
												<td>
													<div class="d-flex m-0">
														<?php if ($permisoactualizaciongestionrequi == 1) { ?>
															<!-- Button trigger modal Editar-->
															<div class="p-1 m-0">
																<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
															</div>
														<?php } ?>
														<!-- Button trigger modal Eliminar-->
														<?php if ($permisoeliminaciongestionrequi == 1) { ?>
															<div class="p-1 m-0">
																<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
															</div>
														<?php } ?>

														<div class="p-1 m-0">
															<button class="btn btn-primary btn-xs btnVer" style="border:20px;background:dodgerblue" type="button" name="" data-bs-toggle="modal" data-bs-target="#modalVer"><i class="nav-icon fas fa-eye"></i></button>
														</div>

														<?php if ($tipo_usuario == 1) { ?>
															<!-- Button trigger modal Confirmar-->
															<label for="">|</label>
															<div class="p-1 m-0">
																<button type="button" id="" class="btn btn-primary btn-xs btn_aprobar" style="border:20px;background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalConfirmar">
																	<i class="nav-icon fas fa-user-check"></i>
																</button>
															</div>
															<!-- Button trigger modal Confirmar-->
															<div class="p-1 m-0">
																<button type="button" id="" class="btn btn-primary btn-xs btn_rechazar" style="border:20px;background:red" data-bs-toggle="modal" data-bs-target="#modalConfirmar">
																	<i class="nav-icon fas fa-user-alt-slash"></i>
																</button>
															</div>
															<!-- Button trigger modal Confirmar-->
															<label for="">|</label>
															<div class="p-1 m-0">
																<button type="button" id="" class="btn btn-primary btn-xs btn_devolver" style="border:20px;background:brown" data-bs-toggle="modal" data-bs-target="#modalConfirmar">
																	<i class="nav-icon fas fa-arrow-alt-circle-left"></i>
																</button>
															</div>
														<?php } ?>
													</div>

												</td>
												<td><?php echo $row_solicitudes['id_solicitud']; ?></td>
												<td><?php echo $row_solicitudes['nombre_departamento']; ?></td>
												<td><?php echo $row_solicitudes['empleado']; ?></td>
												<td><?php echo $row_solicitudes['hogar']; ?></td>
												<td><?php echo $row_solicitudes['fechahora_ingreso']; ?></td>
												<td><?php echo $row_solicitudes['justificacion']; ?></td>
												<td><?php echo $row_solicitudes['nombre_del_estado']; ?></td>
											</tr>
										<?php } ?>

										<?php if ($permisoinserciongestionrequi == 1) { ?>
											<h1 class="box-title">
												<!-- Button trigger modal Añadir-->
												<button type="button" id="btnAñadir" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadir">
													<i class="fa fa-plus-circle"></i>&nbsp; Nueva solicitud
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
						<!-- Modal Filtro de categoria activos-->
						<div class="modal fade" id="filtrorequi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
									</div>
									<div class="modal-body">
										<form action="reportes_nph/reporterequi.php" method="post">

											<div class="form-group">
												<?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
												<div class="col-md-8">
													<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtraciòn</b> </label><input class="form-control py-4" id="filtrorequi" type="text" name="filtrorequi" placeholder="Ingrese una coincidencia" maxlength="" /></div>
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

						<!-- Modal CONFIRMAR-->
						<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Confirmar solicitud</h5>
									</div>
									<div class="modal-body">
										<form id="frm_confirmar" action="modelos/crud_requisiciones.php?op=confirmar" method="post">
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

						<!-- Modal VER-->
						<div class="modal fade" id="modalVer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<div id="cargando" style="display: flex;">
											<h5 class="modal-title" id="exampleModalLabel" style="padding: 0 10 0 0;">Ver solicitud</h5>
											<div class="spinner-border text-left" role="status">
												<span class="sr-only">Loading...</span>
											</div>
										</div>
									</div>
									<div class="modal-body">
										<!-- FORMULARIO -->
										<form id="formVer" action="" method="post">
											<input type="hidden" id="id_ver" name="id">

											<div class="form-group">
												<label class="small mb-1" for="id2_ver">Id</label>
												<input class="form-control" id="id2_ver" type="text" name="" value="" onKeyUP="this.value=this.value.toUpperCase();" disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="">Departamento</label>
												<input class="form-control" id="" type="text" name="" value="<?php echo $row_usuario['nombre_departamento']; ?>" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="">Empleado</label>
												<input class="form-control" id="" type="text" name="" value="<?php echo $row_usuario['empleado']; ?>" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="justificacion">Justificacion</label>
												<textarea class="form-control" id="justificacion_ver" type="" name="justificacion" value="" placeholder="Ingrese justificacion" disabled required></textarea>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="hogar">Seleccione un hogar</label>
												<input class="form-control" id="hogar_ver" type="text" name="" value="" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="estado_ver">Estado</label>
												<input class="form-control" id="estado_ver" type="text" name="" value="" required disabled />
											</div>

											<!-- TABLA -->
											<div class="form-group">
												<label class="small mb-1" for="detalle">Detalle de compra</label>
												<div class="table-responsive">
													<input type="hidden" id="nfilas_ver" name="nfilas">
													<table id="" class="table table-bordered">
														<thead>
															<!-- TITULOS -->
															<th>Producto</th>
															<th>Cantidad</th>
														</thead><!-- /TITULOS -->
														<tbody id="detalles_ver">
															<!-- CUERPO -->
														</tbody><!-- /CUERPO -->
													</table>
												</div>
											</div> <!-- /TABLA -->

											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div> <!-- / Modal VER -->

						<!-- Modal ELIMINAR-->
						<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Eliminación solicitud</h5>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_requisiciones.php?op=eliminar" method="post">

											<div class="form-group">
												<label id="">¿Esta seguro de eliminar la solicitud&nbsp;</label><label id="id_eliminar_label"></label>
											</div>

											<div class="form-group">
												<input type="hidden" name="id" id="id_eliminar">
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
										<div id="cargando" style="display: flex;">
											<h5 class="modal-title" id="exampleModalLabel" style="padding: 0 10 0 0;">Editar solicitud</h5>
											<div class="spinner-border text-left" role="status">
												<span class="sr-only">Loading...</span>
											</div>
										</div>
									</div>
									<div class="modal-body">
										<!-- FORMULARIO -->
										<form id="formEditar" action="modelos/crud_requisiciones.php?op=editar" method="post">
											<input type="hidden" id="id_editar" name="id">

											<div class="form-group">
												<label class="small mb-1" for="id2_editar">Id</label>
												<input class="form-control" id="id2_editar" type="text" name="" value="" onKeyUP="this.value=this.value.toUpperCase();" disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="">Departamento</label>
												<input class="form-control" id="" type="text" name="" value="<?php echo $row_usuario['nombre_departamento']; ?>" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="">Empleado</label>
												<input class="form-control" id="" type="text" name="" value="<?php echo $row_usuario['empleado']; ?>" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="hogar">Seleccione un hogar</label>
												<select class="form-control" type="text" id="hogar" name="hogar" placeholder="Seleccione hogar" required>
													<?php $sql = "SELECT * FROM `tbl_hogares` ORDER BY `hogar`;";
													$resultado = $mysqli->query($sql);
													while ($row = $resultado->fetch_assoc()) {
														$codigoRol = $row['id_hogar'];
														$nombreRol = $row['hogar']; ?>
														<option value=<?php echo $codigoRol ?>><?php echo $nombreRol ?> <?php } ?></option>
												</select>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="justificacion">Justificacion</label>
												<textarea class="form-control" id="justificacion_editar" type="" name="justificacion" value="" placeholder="Ingrese justificacion" required></textarea>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="estado">Seleccione un estado</label>
												<select class="form-control" type="text" id="estado_editar" name="estado" placeholder="Seleccione estado">
													<?php $sql = "SELECT `cod_estado`, `nombre_del_estado` FROM `tbl_estado_entrega_aprobacion`;";
													$resultado = $mysqli->query($sql);
													while ($row = $resultado->fetch_assoc()) {
														$codigoRol = $row['cod_estado'];
														$nombreRol = $row['nombre_del_estado']; ?>
														<option value=<?php echo $codigoRol ?>><?php echo $nombreRol ?> <?php } ?></option>
												</select>
											</div>

											<!-- TABLA -->
											<div class="form-group">
												<label class="small mb-1" for="detalle">Detalle de compra</label>
												<div class="table-responsive">
													<input type="hidden" id="nfilas_editar" name="nfilas">

													<table id="detalle_editar" class="table table-bordered">
														<thead>
															<!-- TITULOS -->
															<th></th>
															<th>Producto</th>
															<th>Cantidad</th>
														</thead>
														<tbody id="detalles_editar">
															<!-- CUERPO -->
														</tbody>

														<tfoot>
															<!-- FOOTER -->
															<tr>
																<!-- FILA AÑADIR DETALLE -->
																<td>
																	<!-- BOTTON AÑADIR -->
																	<div class="col-md-6">
																		<button type="button" id="btn_añadir_editar" class="btn btn-success btn-xs" style="border:20px;background:dodgerblue" onclick="añadir_detalle_editar()">
																			<i class="fa fa-plus-circle"></i>
																		</button>
																	</div>
																</td>

																<td>
																	<!-- SELECT -->
																	<input class="form-control" type="text" id="producto_editar" name="producto" value="" list="roles_datalist_editar" placeholder="Seleccione producto">
																	<datalist id="roles_datalist_editar">
																		<?php $sql = "SELECT `id_producto`,`nombre_producto` FROM `tbl_productos` ORDER BY `nombre_producto`;";
																		$resultado = $mysqli->query($sql);
																		while ($row = $resultado->fetch_assoc()) {;
																			$codigoRol = $row['id_producto'];
																			$nombreRol = $row['nombre_producto']; ?>
																			<option value=<?php echo $nombreRol; ?>></option> <?php } ?>
																	</datalist>
																</td>

																<td><input class="form-control" type="text" id="cantidad_editar" name="cantidad" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>
															</tr>
														</tfoot>
													</table>
												</div>
											</div> <!-- /TABLA -->

											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
												<button type="submit" class="btn btn-primary" disabled id="btn_submit_editar" onclick="validar_tabla_detalles_editar()">Guardar cambios</button>
											</div>
										</form>
										">
									</div>
								</div>
							</div>
						</div> <!-- / Modal EDITAR -->

						<!-- Modal AÑADIR-->
						<div class="modal fade" id="modalAñadir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<div id="cargando" style="display: flex;">
											<h5 class="modal-title" id="exampleModalLabel" style="padding: 0 10 0 0;">Añadir solicitud</h5>
										</div>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<!-- FORMULARIO -->
										<form id="formAñadir" action="modelos/crud_requisiciones.php?op=añadir" method="post">

											<div class="form-group">
												<label class="small mb-1" for="">Departamento</label>
												<input class="form-control" id="" type="text" name="" value="<?php echo $row_usuario['nombre_departamento']; ?>" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="">Empleado</label>
												<input class="form-control" id="" type="text" name="" value="<?php echo $row_usuario['empleado']; ?>" onkeypress="return evitarespeciales(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Usuario de empleado" required disabled />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="hogar">Seleccione un hogar</label>
												<select class="form-control" type="text" id="hogar" name="hogar" placeholder="Seleccione hogar" required>
													<?php $sql = "SELECT * FROM `tbl_hogares` ORDER BY `hogar`;";
													$resultado = $mysqli->query($sql);
													while ($row = $resultado->fetch_assoc()) {
														$codigoRol = $row['id_hogar'];
														$nombreRol = $row['hogar']; ?>
														<option value=<?php echo $codigoRol ?>><?php echo $nombreRol ?> <?php } ?></option>
												</select>
											</div>

											<div class="form-group">
												<label class="small mb-1" for="justificacion">Justificacion</label>
												<input class="form-control" id="" type="text" name="justificacion" value="" placeholder="Ingrese justificacion" required></input>
											</div>

											<!-- TABLA -->
											<div class="form-group">
												<label class="small mb-1" for="detalle">Detalle de requisición</label>
												<div class="table-responsive">
													<input type="hidden" id="nfilas" name="nfilas">

													<table id="detalle" name="detalle" class="table table-bordered">
														<thead>
															<!-- TITULOS -->
															<th></th>
															<th>Producto</th>
															<th>Cantidad</th>
														</thead>
														<tbody id="detalles" class="detalles">
															<!-- CUERPO -->
														</tbody>

														<tfoot>
															<!-- FOOTER -->

															<tr>
																<!-- FILA AÑADIR DETALLE -->
																<td>
																	<!-- BOTTON AÑADIR -->
																	<div class="col-md-6">
																		<button type="button" class="btn btn-success btn-xs" style="border:20px;background:dodgerblue" onclick="añadir_detalle()">
																			<i class="fa fa-plus-circle"></i>
																		</button>
																	</div>
																</td>

																<td>
																	<!-- SELECT -->
																	<input class="form-control" type="text" id="producto" name="producto" value="" list="roles_datalist" placeholder="Seleccione producto">
																	<datalist id="roles_datalist">
																		<?php $sql = "SELECT `id_producto`,`nombre_producto` FROM `tbl_productos` ORDER BY `nombre_producto`;";
																		$resultado = $mysqli->query($sql);
																		while ($row = $resultado->fetch_assoc()) {;
																			$codigoRol = $row['id_producto'];
																			$nombreRol = $row['nombre_producto']; ?>
																			<option value=<?php echo $nombreRol; ?>></option> <?php } ?>
																	</datalist>
																</td>

																<td><input class="form-control" type="text" id="cantidad" name="cantidad" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>
															</tr>
														</tfoot>
													</table>
												</div>
											</div> <!-- /TABLA -->

											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
												<button type="submit" class="btn btn-primary" onclick="validar_tabla_detalles()">Guardar cambios</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div> <!-- / Modal AÑADIR -->

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

				<!-- SCRIPTS TABLA AÑADIR -->
				<script>
					var $nfilas = 0;

					function validar_tabla_detalles() {

						if (document.getElementById('detalles').childElementCount > 0) {} else {
							$('#formAñadir button:last').attr("disabled", false);
							alert('Insertar al menos una fila en el detalle');
							die;
						}
					}

					function añadir_detalle() {
						$nfilas++;
						document.getElementById('nfilas').value = $nfilas;

						var $producto = document.getElementById('producto').value;
						var $cantidad = document.getElementById('cantidad').value;

						if ($producto == "" || $cantidad == "") {
							alert("Llenar todos los datos para añadir")
						} else {
							document.getElementById('producto').value = "";
							document.getElementById('cantidad').value = "";

							$('#detalles').append(
								'<tr>' +
								'<td>' +
								'<div class="col-md-6">' +
								'<button class="btn btn-primary btn-xs" style="border:20px;background:red " id="" onclick="eliminar_detalle(this)" type="button" name="eliminar_usuario"><i class="fa fa-minus-circle"></i></button>' +
								'</div>' +
								'</td>' +

								'<td>' +
								'<input  type="hidden" id="producto' + $nfilas + '" name="producto' + $nfilas + '" value="' + $producto + '">' +
								'<label for="">' + $producto + '</label>' +
								'</td>' +

								'<td>' +
								'<input  type="hidden" id="cantidad' + $nfilas + '" name="cantidad' + $nfilas + '" value="' + $cantidad + '" >' +
								'<label for="">' + $cantidad + '</label>' +
								'</td>' +
								'</tr>');
						}
						//total();
					}
					
					function eliminar_detalle(elemento) {
						elemento.closest('tr').remove();
						total();
					};					
				</script>
				<!-- SCRIPTS TABLA EDITAR -->
				<script>
					var $nfilas_editar = 0;

					function validar_tabla_detalles_editar() {
						/* 	if (document.getElementById('lbltotal_detalle_editar').innerText == 'NaN') {
								alert('Agregue detalles de compra validos');
								die;
							} */
						if (document.getElementById('detalles_editar').childElementCount > 0) {

						} else {
							alert('Insertar al menos una fila en el detalle');
							die;
						}
					}

					function añadir_detalle_editar() {
						$nfilas_editar++;
						document.getElementById('nfilas_editar').value = $nfilas_editar;

						var $producto = document.getElementById('producto_editar').value;
						var $cantidad = document.getElementById('cantidad_editar').value;
						/* var $precio = document.getElementById('detalle_precio_editar').value; */


						if ($producto == "" || $cantidad == "") {
							alert("Llenar todos los datos para añadir")
						} else {
							document.getElementById('producto_editar').value = "";
							document.getElementById('cantidad_editar').value = "";

							$('#detalles_editar').append(
								'<tr>' +
								'<td>' +
								'<div class="col-md-6">' +
								'<button class="btn btn-primary btn-xs" style="border:20px;background:red" id="" onclick="eliminar_detalle_editar(this)" type="button" name="eliminar_usuario"><i class="nav-icon fas fa-minus-circle"></i></button>' +
								'</div>' +
								'</td>' +

								'<td>' +
								'<input  type="hidden" id="producto' + $nfilas_editar + '" name="producto' + $nfilas_editar + '" value="' + $producto + '">' +
								'<label for="">' + $producto + '</label>' +
								'</td>' +

								'<td>' +
								'<input  type="hidden" id="cantidad' + $nfilas_editar + '" name="cantidad' + $nfilas_editar + '" value="' + $cantidad + '" >' +
								'<label for="">' + $cantidad + '</label>' +
								'</td>' +
								'</tr>');
							console.log('ok');
						}
						total_editar();

					}

					/* function total_editar() {
						var tabla = $('#detalles_editar tr');
						var $xtotal = 0;

						for (let index = 0; index < tabla.length; index++) {
							var fila = tabla[index];
							var celdas = fila.getElementsByTagName('input');
							var n1 = celdas[1].value;
							var n2 = celdas[2].value;
							$xtotal += n1 * n2;
						}
						document.getElementById('lbltotal_detalle_editar').innerText = $xtotal;
						document.getElementById('total_detalle_editar').value = $xtotal;
						document.getElementById('nfilas_editar').value = $nfilas_editar;
					} */

					function eliminar_detalle_editar(elemento) {
						elemento.closest('tr').remove();
						total_editar();
					}

					function eliminar_detalles_editar() {
						$('#detalles_editar').html('');
						total_editar();
					}
				</script>
				<!-- SCRIPTS TABLA VER -->
				<script>
					var $nfilas_ver = 0;

					function añadir_detalle_ver() {
						$nfilas_editar++;
						document.getElementById('nfilas_editar').value = $nfilas_editar;

						var $producto = document.getElementById('producto_editar').value;
						var $cantidad = document.getElementById('cantidad_editar').value;


						if ($producto == "" || $cantidad == "") {
							alert("Llenar todos los datos para añadir")
						} else {
							document.getElementById('producto_editar').value = "";
							document.getElementById('cantidad_editar').value = "";

							$('#detalles_editar').append(
								'<tr>' +
								'<td>' +
								'<div class="col-md-6">' +
								'<button class="btn btn-primary btn-xs" style="border:20px;background:red" id="" onclick="eliminar_detalle_editar(this)" type="button" name="eliminar_usuario"><i class="nav-icon fas fa-minus-circle"></i></button>' +
								'</div>' +
								'</td>' +

								'<td>' +
								'<input  type="hidden" id="producto' + $nfilas_editar + '" name="producto' + $nfilas_editar + '" value="' + $producto + '">' +
								'<label for="">' + $producto + '</label>' +
								'</td>' +

								'<td>' +
								'<input  type="hidden" id="cantidad' + $nfilas_editar + '" name="cantidad' + $nfilas_editar + '" value="' + $cantidad + '" >' +
								'<label for="">' + $cantidad + '</label>' +
								'</td>' +
								'</tr>');
							console.log('ok');
						}
						total_editar();

					}

					/* function total_ver() {
						var tabla = $('#detalles_ver tr');
						var $xtotal = 0;

						for (let index = 0; index < tabla.length; index++) {
							var fila = tabla[index];
							var celdas = fila.getElementsByTagName('input');
							var n1 = celdas[1].value;
							var n2 = celdas[2].value;
							$xtotal += n1 * n2;
						}
						document.getElementById('lbltotal_detalle_ver').innerText = $xtotal;
					} */

					function eliminar_detalles_ver() {
						$('#detalles_ver').html('');
						total_ver();
					}
				</script>
				<!-- VALIDACIONES -->
				<script>
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
				</script>
				<!-- EVENTOS -->
				<script>
					$('.btn_aprobar').on('click', function() {
						$tr = $(this).closest('tr');
						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de aprobar la solicitud ' + datos[1] + '?';
						$('#id_estado_confirmar').val(1);
						$('#id_confirmar').val(datos[1]);
						$('#estado_confirmar').val(datos[7]);
						console.log($('#id_estado_confirmar').val());
						console.log($('#id_confirmar').val());
						console.log($('#estado_confirmar').val());						
					});

					$('.btn_rechazar').on('click', function() {
						$tr = $(this).closest('tr');
						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de rechazar la solicitud ' + datos[1] + '?';
						$('#id_estado_confirmar').val(2);
						$('#id_confirmar').val(datos[1]);
						$('#estado_confirmar').val(datos[7]);
						console.log($('#id_estado_confirmar').val());
						console.log($('#id_confirmar').val());
						console.log($('#estado_confirmar').val());						
					});

					$('.btn_devolver').on('click', function() {
						$tr = $(this).closest('tr');
						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de devolver y rechazar la solicitud ' + datos[1] + '?';
						$('#id_estado_confirmar').val(3);
						$('#id_confirmar').val(datos[1]);
						$('#estado_confirmar').val(datos[7]);
						console.log($('#id_estado_confirmar').val());
						console.log($('#id_confirmar').val());
						console.log($('#estado_confirmar').val());
						
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

						if (id_estado == 3) {
							if (estado == 'Rechazada') {
								alert('La solicitud ya se encuentra actualmente rechazada');
								die();
							} else {
								if (estado == 'Pendiente') {
									alert('La solicitud aun esta pendiente de aprobacion o rechazo');
									die();
								} else {
									document.getElementById('frm_confirmar').submit();
								}
							}
						}
					}

					$('.btnEditar').on('click', function() {
						$('.spinner-border').fadeIn();
						$('#detalles_editar').html('');
						//document.getElementById('lbltotal_detalle_editar').innerHTML = 0;
						$('#btn_submit_editar').attr('disabled', true);
						$('#btn_añadir_editar').attr('disabled', true);

						$tr = $(this).closest('tr');

						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						$('#estado_editar').val(3);
						$('#id_editar').val(datos[1]);
						$('#id2_editar').val(datos[1]);
						$('#justificacion_editar').val(datos[6]);
						var id_solicitud = datos[1];
						var url_cargar_detalles = 'modelos/crud_requisiciones.php?op=cargar&id=' + id_solicitud;
						console.log(url_cargar_detalles);

						$.ajax({
							url: url_cargar_detalles,
							type: 'POST',
							datatype: 'JSON',
							contentType: 'application/json',
							success: function(response) {
								var $data = JSON.parse(response);
								var valores = '';
								console.log($data);

								for (let i = 0; i < $data.length; i++) {
									var producto = $data[i].producto;
									var cantidad = $data[i].cantidad;
									valores += '<tr>' +
										'<td>' +
										'<div class="col-md-6">' +
										'<button class="btn btn-primary btn-xs" style="border:20px;background:red" id="" onclick="eliminar_detalle_editar(this)" type="button" name="eliminar_usuario"><i class="nav-icon fas fa-minus-circle"></i></button>' +
										'</div>' +
										'</td>' +

										'<td>' +
										'<input  type="hidden" id="producto' + i + '" name="producto' + i + '" value="' + producto + '">' +
										'<label for="">' + producto + '</label>' +
										'</td>' +

										'<td>' +
										'<input  type="hidden" id="cantidad' + i + '" name="cantidad' + i + '" value="' + cantidad + '">' +
										'<label for="">' + cantidad + '</label>' +
										'</td>'
									$nfilas_editar++;
								}
								$('#detalles_editar').append(valores);
								$('#btn_submit_editar').removeAttr('disabled');
								$('#btn_añadir_editar').removeAttr('disabled');
								$('.spinner-border').fadeOut();

								//total_editar();
							}
						})
					});
					$('.btnVer').on('click', function() {
						$('.spinner-border').fadeIn();
						$('#detalles_ver').html('');
						//document.getElementById('lbltotal_detalle_ver').innerHTML = 0;

						$tr = $(this).closest('tr');

						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						$('#id_ver').val(datos[1]);
						$('#id2_ver').val(datos[1]);
						$('#justificacion_ver').val(datos[6]);
						//hogar_ver
						$('#hogar_ver').val(datos[4]);

						$('#estado_ver').val(datos[7]);
						var id_solicitud = datos[1];
						var url_cargar_detalles = 'modelos/crud_requisiciones.php?op=cargar&id=' + id_solicitud;
						console.log(url_cargar_detalles);
						$.ajax({
							url: url_cargar_detalles,
							type: 'POST',
							datatype: 'JSON',
							contentType: 'application/json',
							success: function(response) {
								var $data = JSON.parse(response);
								var valores = '';
								console.log($data);

								for (let i = 0; i < $data.length; i++) {
									var producto = $data[i].producto;
									var precio_compra = $data[i].precio_compra;
									var cantidad = $data[i].cantidad;
									valores += '<tr>' +
										'<td>' +
										'<input  type="hidden" id="producto' + i + '" name="producto' + i + '" value="' + producto + '">' +
										'<label for="">' + producto + '</label>' +
										'</td>' +

										'<td>' +
										'<input  type="hidden" id="cantidad' + i + '" name="cantidad' + i + '" value="' + cantidad + '">' +
										'<label for="">' + cantidad + '</label>' +
										'</td>' +
										'</tr>';
									$nfilas_ver++;
								}
								$('#detalles_ver').append(valores);
								$('.spinner-border').fadeOut();
								//total_ver();
							}
						})
					});
					$('.btnAñadir').on('click', function() {});
					$('.btnEliminar').on('click', function() {
						$tr = $(this).closest('tr');

						var datos = $tr.children("td").map(function() {
							return $(this).text();
						});
						$('#id_eliminar').val(datos[1]);
						$('#id_eliminar_label').text(datos[1] + '?');
					});

					$(document).ready(function() {						

						var table = $('#requi').DataTable({
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