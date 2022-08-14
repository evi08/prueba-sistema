<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Solicitudes de compra</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link id="estilos" rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
	<link id="estilos" rel="" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
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
		$sqlgestioncompra = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=55";
		$resultadogestioncompra = $mysqli->query($sqlgestioncompra);
		$filasgestioncompra = $resultadogestioncompra->num_rows;

		if ($filasgestioncompra  > 0) {
			$rowgestioncompra = $resultadogestioncompra->fetch_assoc();
			$permisoinserciongestioncompra = $rowgestioncompra['permiso_insercion'];
			$permisoeliminaciongestioncompra = $rowgestioncompra['permiso_eliminacion'];
			$permisoactualizaciongestioncompra = $rowgestioncompra['permiso_actualizacion'];
			$permisoconsultagestioncompra = $rowgestioncompra['permiso_consultar'];
			if ($permisoconsultagestioncompra == 1) {
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

				$sql = "SELECT s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre,\" \",em.segundo_nombre,\" \",em.primer_apellido,\" \",em.segundo_apellido) empleado, s.fechahora_ingreso, s.justificacion, p.nombre_proveedor, c.total_pagar, e.nombre_del_estado 
	FROM tbl_solicitudes s, tbl_departamentos d, tbl_empleados em, tbl_compras c, tbl_estado_entrega_aprobacion e, tbl_proveedores p 
	WHERE d.cod_departamento = s.cod_departamento AND em.cod_empleado = s.cod_empleado AND c.cod_proveedor = p.cod_proveedor AND c.id_solicitud = s.id_solicitud AND e.cod_estado = s.cod_estado AND s.id_tipo_solicitud = 3 $where
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
									<h1 class="m-0">Solicitudes de compra</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Solicitudes de compra</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->

					<form action="reportes_nph/reportecompras.php" id="frm_enviar_compras" method="post">
						<input type="hidden" name="filtrocompras" id="filtrocompras">
						<div>
							<button id="btn_enviar_compras" class="btn btn-danger" name="btn_enviar_compras" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Solicitud de Compras</button>
						</div>
					</form>
                    <script>
						$('#btn_enviar_compras').on('click', function() {

							var filtro = $('#compras_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtrocompras').val(filtro);

							console.log('#filtrocompras');
							console.log($('#filtrocompras').val());
							document.getElementById('frm_enviar_compras').submit();

                        });
                        </script>

					<div>
						<div>

							<div class="card-body" style="overflow-x:auto;" id="reporte">
								<div class="table-responsive" id="reporte">
									<table id="compras" class="table table-bordered ">
										<thead>
											<tr>
												<th>Opciones</th>
												<th>Id</th> <!-- tbl_solicitudes -->
												<th>Departamento</th> <!-- tbl_solicitudes -->
												<th>Empleado</th> <!-- tbl_solicitudes -->
												<th>Fecha de solicitud</th> <!-- tbl_solicitudes -->
												<th>Justificacion</th> <!-- tbl_solicitudes -->
												<th>Proveedor</th>
												<th>Total a pagar</th> <!-- tbl_compras -->
												<th>Estado solicitud</th> <!-- tbl_solicitudes -->
											</tr>
										</thead>
										<tbody>
											<?php while ($row_solicitudes = $resultado->fetch_assoc()) { ?>
												<tr>
													<td>
														<div class="d-flex m-0">
															<?php if ($permisoactualizaciongestioncompra == 1) { ?>
																<!-- Button trigger modal Editar-->
																<div class="p-1 m-0">
																	<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
																</div>
															<?php } ?>
															<!-- Button trigger modal Eliminar-->
															<?php if ($permisoeliminaciongestioncompra == 1) { ?>
																<div class="p-1 m-0">
																	<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
																</div>
															<?php } ?>
															<!-- Button trigger modal Ver-->
															<div class="p-1 m-0">
																<button class="btn btn-primary btn-xs btnVer" style="border:20px;background:dodgerblue" type="button" name="" data-bs-toggle="modal" data-bs-target="#modalVer"><i class="nav-icon fas fa-eye"></i></button>
															</div>

															
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
													<td><?php echo $row_solicitudes['fechahora_ingreso']; ?></td>
													<td><?php echo $row_solicitudes['justificacion']; ?></td>
													<td><?php echo $row_solicitudes['nombre_proveedor']; ?></td>
													<td><?php echo $row_solicitudes['total_pagar']; ?></td>
													<td><?php echo $row_solicitudes['nombre_del_estado']; ?></td>
												</tr>
											<?php } ?>

											<?php if ($permisoinserciongestioncompra == 1) { ?>
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

							<!-- Modal CONFIRMAR-->
							<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">Confirmar solicitud</h5>
										</div>
										<div class="modal-body">
											<form id="frm_confirmar" action="modelos/crud_compras.php?op=confirmar" method="post">
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
													<label class="small mb-1" for="proveedor_ver">Proveedor</label>
													<input class="form-control" id="proveedor_ver" type="text" name="" value="" required disabled />
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
																<th>Precio</th>
															</thead><!-- /TITULOS -->
															<tbody id="detalles_ver">
																<!-- CUERPO -->
															</tbody><!-- /CUERPO -->
															<tfoot>
																<!-- FOOTER -->
																<tr>
																	<!-- FILA TOTAL -->
																	<td></td>
																	<td>Total</td>
																	<td><label for="" id="lbltotal_detalle_ver">0</label></td>
																</tr><!-- /FILA TOTAL -->
															</tfoot><!-- FOOTER -->
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
											<form action="modelos/crud_compras.php?op=eliminar" method="post">

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
											<form id="formEditar" action="modelos/crud_compras.php?op=editar" method="post">
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
													<label class="small mb-1" for="justificacion">Justificacion</label>
													<textarea class="form-control" id="justificacion_editar" type="" name="justificacion" value="" placeholder="Ingrese justificacion" required></textarea>
												</div>

												<div class="form-group">
													<label class="small mb-1" for="categoria">Seleccione un proveedor</label>
													<select class="form-control" type="text" id="proveedor_editar" name="proveedor" placeholder="Seleccione proveedor">
														<?php $sql = "SELECT `cod_proveedor`,`nombre_proveedor` FROM `tbl_proveedores` order BY `nombre_proveedor`;";
														$resultado = $mysqli->query($sql);
														while ($row = $resultado->fetch_assoc()) {
															$codigoRol = $row['cod_proveedor'];
															$nombreRol = $row['nombre_proveedor']; ?>
															<option value=<?php echo $codigoRol ?>><?php echo $nombreRol ?> <?php } ?></option>
													</select>
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
																<th>Precio</th>
															</thead>
															<tbody id="detalles_editar">
																<!-- CUERPO -->
															</tbody>

															<tfoot>
																<!-- FOOTER -->
																<tr>
																	<!-- FILA TOTAL -->
																	<td>
																		<div class=" col-md-6">
																			<button type="button" class="btn btn-success btn-xs" style="border:20px;background:red" onclick="eliminar_detalles_editar()">
																				<i class="fa fa-trash"></i>
																			</button>
																		</div>
																	</td>
																	<td></td>
																	<td>Total</td>
																	<td><label for="" id="lbltotal_detalle_editar">0</label></td>
																	<input type="hidden" id="total_detalle_editar" name="total_detalle">
																</tr>
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

																	<td><input class="form-control" type="text" id="detalle_precio_editar" name="precio" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>
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
											<h5 class="modal-title" id="exampleModalLabel">Añadir solicitud</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
										</div>
										<div class="modal-body">
											<!-- FORMULARIO -->
											<form id="formAñadir" action="modelos/crud_compras.php?op=añadir" method="post">

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
													<textarea class="form-control" id="" type="" name="justificacion" value="" placeholder="Ingrese justificacion" required></textarea>
												</div>

												<div class="form-group">
													<label class="small mb-1" for="categoria">Seleccione un proveedor</label>
													<select class="form-control" type="text" id="proveedor" name="proveedor" placeholder="Seleccione proveedor">
														<?php $sql = "SELECT `cod_proveedor`,`nombre_proveedor` FROM `tbl_proveedores` order BY `nombre_proveedor`;";
														$resultado = $mysqli->query($sql);
														while ($row = $resultado->fetch_assoc()) {
															$codigoRol = $row['cod_proveedor'];
															$nombreRol = $row['nombre_proveedor']; ?>
															<option value=<?php echo $codigoRol ?>><?php echo $nombreRol ?> <?php } ?></option>
													</select>
												</div>

												<!-- TABLA -->
												<div class="form-group">
													<label class="small mb-1" for="detalle">Detalle de compra</label>
													<div class="table-responsive">
														<input type="hidden" id="nfilas" name="nfilas">

														<table id="detalle" name="detalle" class="table table-bordered">
															<thead>
																<!-- TITULOS -->
																<th></th>
																<th>Producto</th>
																<th>Cantidad</th>
																<th>Precio</th>
															</thead>
															<tbody id="detalles" class="detalles">
																<!-- CUERPO -->
															</tbody>

															<tfoot>
																<!-- FOOTER -->
																<tr>
																	<!-- FILA TOTAL -->
																	<td>
																		<div class="col-md-6">
																			<button type="button" class="btn btn-success btn-xs" style="border:20px;background:red" onclick="eliminar_detalles()">
																				<i class="fa fa-trash"></i>
																			</button>
																		</div>
																	</td>
																	<td></td>
																	<td>Total</td>
																	<td><label for="" id="lbltotal_detalle">0</label></td>
																	<input type="hidden" id="total_detalle" name="total_detalle">
																</tr>
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

																	<td><input class="form-control" type="text" id="detalle_precio" name="precio" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>
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
						$('#btn_enviar_formulario').on('click', function() {

							var filtro = $('#usuarios_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#input_probar').val(filtro);

							console.log('#input_probar');
							console.log($('#input_probar').val());
							document.getElementById('frm_enviar_filtro').submit();

						});

						var $nfilas = 0;

						function validar_tabla_detalles() {
							if (document.getElementById('lbltotal_detalle').innerText == 'NaN') {
								alert('Agregue detalles de compra validos');
								die;
							}
							if (document.getElementById('detalles').childElementCount > 0) {
								
							} else {
								alert('Insertar al menos una fila en el detalle');
								die;
							}
						}

						function añadir_detalle() {
							$nfilas++;
							document.getElementById('nfilas').value = $nfilas;

							var $producto = document.getElementById('producto').value;
							var $cantidad = document.getElementById('cantidad').value;
							var $precio = document.getElementById('detalle_precio').value;

							if ($producto == "" || $cantidad == "" || $precio == "") {
								alert("Llenar todos los datos para añadir")
							} else {
								document.getElementById('producto').value = "";
								document.getElementById('cantidad').value = "";
								document.getElementById('detalle_precio').value = "";
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

									'<td>' +
									'<input  type="hidden" id="detalle_precio' + $nfilas + '" name="precio' + $nfilas + '" value=" ' + $precio + ' "    >' +
									'<label for="">' + $precio + '</label>' +
									'</td>' +
									'</tr>');
							}
							total();
						}

						function total() {
							var tabla = $('#detalles tr');
							var $xtotal = 0;

							for (let index = 0; index < tabla.length; index++) {
								var fila = tabla[index];
								var celdas = fila.getElementsByTagName('input');
								var n1 = celdas[1].value;
								var n2 = celdas[2].value;
								try {
									$xtotal += n1 * n2;
								} catch (error) {
									alert('Error, vuelva a ingresar detalles');
									die;
								}
							}
							document.getElementById('lbltotal_detalle').innerText = $xtotal;
							document.getElementById('total_detalle').value = $xtotal;
						}

						function eliminar_detalle(elemento) {
							elemento.closest('tr').remove();
							total();
						};

						function eliminar_detalles() {
							$('#detalles').html('');
							total();
						}
					</script>
					<!-- SCRIPTS TABLA EDITAR -->
					<script>
						var $nfilas_editar = 0;

						function validar_tabla_detalles_editar() {
							if (document.getElementById('lbltotal_detalle_editar').innerText == 'NaN') {
								alert('Agregue detalles de compra validos');
								die;
							}
							if (document.getElementById('detalles_editar').childElementCount > 0) {
								// document.getElementById('formEditar').submit();
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
							var $precio = document.getElementById('detalle_precio_editar').value;


							if ($producto == "" || $cantidad == "" || $precio == "") {
								alert("Llenar todos los datos para añadir")
							} else {
								document.getElementById('producto_editar').value = "";
								document.getElementById('cantidad_editar').value = "";
								document.getElementById('detalle_precio_editar').value = "";

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

									'<td>' +
									'<input  type="hidden" id="detalle_precio' + $nfilas_editar + '" name="precio' + $nfilas_editar + '" value=" ' + $precio + ' "    >' +
									'<label for="">' + $precio + '</label>' +
									'</td>' +
									'</tr>');
								console.log('ok');
							}
							total_editar();

						}

						function total_editar() {
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
						}

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
							var $precio = document.getElementById('detalle_precio_editar').value;


							if ($producto == "" || $cantidad == "" || $precio == "") {
								alert("Llenar todos los datos para añadir")
							} else {
								document.getElementById('producto_editar').value = "";
								document.getElementById('cantidad_editar').value = "";
								document.getElementById('detalle_precio_editar').value = "";

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

									'<td>' +
									'<input  type="hidden" id="detalle_precio' + $nfilas_editar + '" name="precio' + $nfilas_editar + '" value=" ' + $precio + ' "    >' +
									'<label for="">' + $precio + '</label>' +
									'</td>' +
									'</tr>');
								console.log('ok');
							}
							total_editar();

						}

						function total_ver() {
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
						}

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
						// btnProbar	

						$('.btn_aprobar').on('click', function() {
							$tr = $(this).closest('tr');
							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de aprobar la solicitud ' + datos[1] + '?';
							$('#id_estado_confirmar').val(1);
							$('#id_confirmar').val(datos[1]);
							$('#estado_confirmar').val(datos[8]);
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
							$('#estado_confirmar').val(datos[8]);

						});

						$('.btn_devolver').on('click', function() {
							$tr = $(this).closest('tr');
							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							document.getElementById('label_id_confirmar').innerText = '¿Esta seguro de devolver y rechazar la solicitud ' + datos[1] + '?';
							$('#id_estado_confirmar').val(3);
							$('#id_confirmar').val(datos[1]);
							$('#estado_confirmar').val(datos[8]);
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
							document.getElementById('lbltotal_detalle_editar').innerHTML = 0;
							$('#btn_submit_editar').attr('disabled', true);
							$('#btn_añadir_editar').attr('disabled', true);

							$tr = $(this).closest('tr');

							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							$('#estado_editar').val(3);
							$('#id_editar').val(datos[1]);
							$('#id2_editar').val(datos[1]);
							$('#justificacion_editar').val(datos[5]);
							var id_solicitud = datos[1];
							var url_cargar_detalles = 'modelos/crud_compras.php?op=cargar&id=' + id_solicitud;
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
											'</td>' +

											'<td>' +
											'<input  type="hidden" id="detalle_precio' + i + '" name="precio' + i + '" value=" ' + precio_compra + '">' +
											'<label for="">' + precio_compra + '</label>' +
											'</td>' +
											'</tr>';
										$nfilas_editar++;
									}
									$('#detalles_editar').append(valores);
									$('#btn_submit_editar').removeAttr('disabled');
									$('#btn_añadir_editar').removeAttr('disabled');
									$('.spinner-border').fadeOut();

									total_editar();
								}
							})
						});

						
						$('.btnVer').on('click', function() {
							$('.spinner-border').fadeIn();
							$('#detalles_ver').html('');
							document.getElementById('lbltotal_detalle_ver').innerHTML = 0;
							$tr = $(this).closest('tr');

							var datos = $tr.children("td").map(function() {
								return $(this).text();
							});
							$('#id_ver').val(datos[1]);
							$('#id2_ver').val(datos[1]);
							$('#justificacion_ver').val(datos[5]);
							$('#proveedor_ver').val(datos[6]);
							$('#estado_ver').val(datos[8]);
							var id_solicitud = datos[1];
							var url_cargar_detalles = 'modelos/crud_compras.php?op=cargar&id=' + id_solicitud;
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

											'<td>' +
											'<input  type="hidden" id="detalle_precio' + i + '" name="precio' + i + '" value=" ' + precio_compra + '">' +
											'<label for="">' + precio_compra + '</label>' +
											'</td>' +
											'</tr>';
										$nfilas_ver++;
									}
									$('#detalles_ver').append(valores);
									$('.spinner-border').fadeOut();
									total_ver();
								}
							})
						});

						$('.btnAñadir').on('click', function() {
							$('#estado').val(3);
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
							$('#id_eliminar').val(datos[1]);
							$('#id_eliminar_label').text(datos[1] + '?');
						});

						$(document).ready(function() {
							var table = $('#compras').DataTable({
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


						$('#btnPdf').on('click', function() {
							document.getElementById('usuarios_filter').hidden = true;

							$('#reporte').css({
								"margin": "0",
								"padding": "0",
								"border": "0",
								"line-height": "0",
							});
							//style="font: size 12px;"

							$('body').css({
								"margin": "0",
								"padding": "0",
								"border": "0",
								"line-height": "0",
								"font": "size 12px"
							});

							$("button").css({
								"width": "0",
								"height": "0",
								"margin": "0",
								"padding": "0",
								"border": "0",
								"line-height": "0",
							});
							document.getElementsByTagName('th')[0].innerText = '';
							$("#reporte th:first-child").css({
								"width": "0",
								"height": "0",
							});

							for (let index = 0; index < document.getElementsByTagName('tr').length; index++) {
								console.log(document.getElementsByTagName('tr').length);
								var element = document.getElementsByTagName('tr')[index + 1];
								try {
									element.getElementsByTagName('td')[0].innerHTML = index + 1;
								} catch (error) {}
							}
							var today = new Date();
							var now = today.toLocaleString();
							var fecha = '<label id="label_reporte" style="text-align:right ; width: 96%;display:inline-block; padding: 2%; padding-bottom: 0%;">' + now + '</label>';

							window.scroll(0, 0);
							var h1 = $('h1:first').css({
								"padding-top": "1%",
								"padding-bottom": "2%",
								"text-align": "center",
								"width": "100%",
								"display": "block"
							});
							$('#reporte').prepend(h1);
							$('#reporte').prepend('<h4 style="padding-top: 4%; padding-bottom: 1%; text-align: center; width: 100%; display:block">FUNDACION NUESTROS PEQUEÑOS HERMANOS</h4>');
							$('#reporte').prepend(fecha);

							var envio = document.getElementById('reporte');

							html2pdf().
							set({
									margin: 0.4,
									filename: 'reporte.pdf',
									image: {
										type: 'jpeg',
										quality: 0.98
									},
									html2canvas: {
										scale: 3,
										letterRendering: true
									},
									jsPDF: {
										unit: "in",
										format: "letter",
										orientation: 'landscape'
									}
								}).from(envio).save()
								.catch(err => console.log('Hecho'))
								.finally()
								.then(() => {
									location.reload()
								});
						});
					</script>

</html>