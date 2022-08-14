<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kardex</title>
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
		$sqlgestionkardex = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=46";
		$resultadogestionkardex = $mysqli->query($sqlgestionkardex);
		$filasgestionkardex = $resultadogestionkardex->num_rows;

		if ($filasgestionkardex  > 0) {
			$rowgestionkardex = $resultadogestionkardex->fetch_assoc();
			$permisoconsultagestionkardex = $rowgestionkardex['permiso_consultar'];
			if ($permisoconsultagestionkardex == 1) {
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

				$sql = "SELECT `id_kardex`, p.nombre_producto Producto, `Cantidad_Producto`, concat(e.primer_nombre,\" \",e.segundo_nombre,\" \",e.primer_apellido,\" \",e.segundo_apellido) cod_empleado, `Fecha_Hora`, `Tipo_Movimiento` 
				FROM `tbl_kardex`, tbl_productos p, tbl_empleados e 
				WHERE p.id_producto = tbl_kardex.Producto AND e.cod_empleado = tbl_kardex.cod_empleado $where;";
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
									<h1 class="m-0">Movimientos inventario</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Historial de movimientos en inventario</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- /.content-header -->
					<form action="reportes_nph/reportekardex.php" id="frm_enviar_filtro_kardex" method="post">
						<input type="hidden" name="filtro_kardex" id="filtro_kardex">
						<div>
							<button id="btn_formulario_kardex" class="btn btn-danger" name="btn_formulario_kardex" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Gestion Usuarios</button>
						</div>
					</form>
					<script>
						$('#btn_formulario_kardex').on('click', function() {

							var filtro = $('#kardex_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_kardex').val(filtro);

							console.log('#filtro_kardex');
							console.log($('#filtro_kardex').val());
							document.getElementById('frm_enviar_filtro_kardex').submit();

						});
					</script>
					<div>
						<div class="card-body" style="overflow-x:auto;">
							<div class="table-responsive">
								<table id="kardex" class="table table-bordered ">

									<thead>
										<tr>
											<th>Id</th>
											<th>Producto</th>
											<th>Cantidad Producto</th>
											<th>Empleado</th>
											<th>Fecha</th>
											<th>Tipo Movimiento</th>

										</tr>
									</thead>
									<tbody>
										<?php while ($row_kardex = $resultado->fetch_assoc()) { ?>
											<tr>

												<td><?php echo $row_kardex['id_kardex']; ?></td>
												<td><?php echo $row_kardex['Producto']; ?></td>
												<td><?php echo $row_kardex['Cantidad_Producto']; ?></td>
												<td><?php echo $row_kardex['cod_empleado']; ?></td>
												<td><?php echo $row_kardex['Fecha_Hora']; ?></td>
												<td><?php echo $row_kardex['Tipo_Movimiento']; ?></td>

											</tr>
										<?php } ?>



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
						<div class="modal fade" id="filtrokardex" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
									</div>
									<div class="modal-body">
										<form action="reportes_nph/reportekardex.php" method="post">

											<div class="form-group">
												<?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
												<div class="col-md-8">
													<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtraciòn</b> </label><input class="form-control py-4" id="filtrokardex" type="text" name="filtrokardex" placeholder="Ingrese una coincidencia" maxlength="" /></div>
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
										<h5 class="modal-title" id="exampleModalLabel">Eliminación de Categoria Activo</h5>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_categoriaactivo.php?op=eliminar" method="post">

											<div class="form-group">
												<?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar la siguiente Categoria Activo?,sé eliminaran los historiales almacenados en la base de datos.</div>"); ?>
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
										<h5 class="modal-title" id="exampleModalLabel">Editar Categoria Activo</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_categoriaactivo.php?op=editar" method="post">

											<!-- FORMULARIO -->

											<input type="hidden" name="id" id="id_editar">

											<div class="form-group">
												<label class="small mb-1" for="categoria">Seleccione un categoria</label>
												<select class="form-control" id="categoria_editar" name="categoria" required>
													<?php $sql = "SELECT * FROM tbl_categoria_activos";
													$resultado = $mysqli->query($sql);
													while ($row = $resultado->fetch_assoc()) { ?>
														<option value=<?php echo $row['cod_categoria_activo']; ?>> <?php echo $row['nombre_categoria'];
																												} ?></option>
												</select>
											</div>


											<div class="form-group">
												<label class="small mb-1" for="descripcion">Descripcion</label>
												<input class="form-control" id="descripcion_editar" type="text" name="descripcion" value="" placeholder="Ingrese descripcion" maxlength="30" required />
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
										<h5 class="modal-title" id="exampleModalLabel">Añadir Categoria Activo</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
									</div>
									<div class="modal-body">
										<form action="modelos/crud_categoriaactivo.php?op=añadir" method="post">

											<!-- FORMULARIO -->

											<div class="form-group">
												<label class="small mb-1" for="activo">Ingrese una Categoria activo</label>
												<input class="form-control" id="activo" type="text" name="activo" value="" placeholder="Ingrese activo" maxlength="15" required />
											</div>

											<div class="form-group">
												<label class="small mb-1" for="descripcion">Descripcion</label>
												<input class="form-control" id="descripcion" type="text" name="descripcion" value="" placeholder="Ingrese descripcion" maxlength="30" required />
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

				<script type="text/javascript">
					function añadirfila() {
						var $nfilas = document.getElementById('detalles').getElementsByTagName('tr').length;
						$nfilas++;

						$('#nfilas').val($nfilas);						

					}



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
						//[categoria] => 1 [proveedor] => 1 [activo] => Lapiz [descripcion] => Para escribir [presentacion]
						$('#id_editar').val(datos[1]);

						$('#nombrecategoria_editar').val(datos[2]);
						$('#descripcion_editar').val(datos[3]);



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
						var table = $('#kardex').DataTable({
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