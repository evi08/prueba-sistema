<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Proveedores</title>
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
        $sqlgestionproveedores = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=49";
        $resultadogestionproveedores = $mysqli->query($sqlgestionproveedores );
        $filasgestionproveedores = $resultadogestionproveedores->num_rows;

        if ($filasgestionproveedores  > 0) {
            $rowgestionproveedores = $resultadogestionproveedores->fetch_assoc();
            $permisoinserciongestionproveedores = $rowgestionproveedores['permiso_insercion'];
            $permisoeliminaciongestionproveedores = $rowgestionproveedores['permiso_eliminacion'];
            $permisoactualizaciongestionproveedores = $rowgestionproveedores['permiso_actualizacion'];
            $permisoconsultagestionproveedores = $rowgestionproveedores['permiso_consultar'];
            if ($permisoconsultagestionproveedores == 1) {
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

	$sql = "SELECT * FROM tbl_proveedores  $where";
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
						<h1 class="m-0">Proveedores</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">NPH</a></li>
							<li class="breadcrumb-item active">Proveedores</li>
						</ol>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.container-fluid -->

		</div>
		<!-- /.content-header -->
		<form action="reportes_nph/reporteproveedores.php" id="frm_enviar_filtro_proveedores" method="post">
						<input type="hidden" name="filtro_proveedor" id="filtro_proveedor">
						<div>
							<button id="btn_formulario_proveedor" class="btn btn-danger" name="btn_formulario_proveedor" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Proveedores</button>
						</div>
					</form>
                    <script>
						$('#btn_formulario_proveedor').on('click', function() {

							var filtro = $('#usuarios_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_proveedor').val(filtro);

							console.log('#filtro_proveedor');
							console.log($('#filtro_proveedor').val());
							document.getElementById('frm_enviar_filtro_proveedores').submit();

						});
                    </script> 
		<div>
		<div>
			<div class="card-body" style="overflow-x:auto;">
				<div class="table-responsive">
					<table id="usuarios" class="table table-bordered ">

						<thead>
							<tr>
								<th>Opciones</th>
								<th>Id</th>
								<th>Nombre Proveedor</th>
								<th>Direccion</th> 
								<th>Correo Proveedor</th> 
								<th>Telefono</th> 
								
							</tr>
						</thead>
						<tbody>
							<?php while ($row_proveedor = $resultado->fetch_assoc()) { ?>
								<tr>
									<td>
										<?php if ($permisoactualizaciongestionproveedores == 1) { ?>
											<!-- BOTTON EDITAR -->
											<div class="col-md-6">
												<button class="btn btn-primary btn-xs btnEditar" style="border:20px; background:green" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="nav-icon fas fa-pen"></i></button>
											</div>
											<?php } ?>
											<!-- BOTTON ELIMINAR -->
											<?php if ($permisoeliminaciongestionproveedores == 1) { ?>
											<div class="col-md-6">
												<button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_categoria" data-bs-toggle="modal" data-bs-target="#modalEliminar"><i class="nav-icon fas fa-trash"></i></button>
											</div>
										<?php } ?>
										<!-- a.cod_activo, c.nombre_categoria, p.nombre_proveedor, a.nombre_activo, a.descripcion_activo, a.presentacion_activo  -->
									</td>
									<td><?php echo $row_proveedor['cod_proveedor']; ?></td>
									<td><?php echo $row_proveedor['nombre_proveedor']; ?></td>
									<td><?php echo $row_proveedor['direccion_proveedor']; ?></td>
									<td><?php echo $row_proveedor['correo_proveedor']; ?></td>
									<td><?php echo $row_proveedor['telefono_proveedor']; ?></td>
									
								</tr>
							<?php } ?>

							<?php if ($permisoinserciongestionproveedores == 1) { ?>
								<h1 class="box-title">
									<!-- Button trigger modal Añadir-->
									<button type="button" id="btnAñadir" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadir">
										<i class="fa fa-plus-circle"></i>&nbsp; Nuevo Proveedor
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
                <div class="modal fade" id="filtroproveedores" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reporteproveedores.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtraciòn</b> </label><input class="form-control py-4" id="filtroproveedores" type="text" name="filtroproveedores" placeholder="Ingrese una coincidencia" maxlength=""  /></div>
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
							<h5 class="modal-title" id="exampleModalLabel">Eliminación de Proveedor</h5>
						</div>
						<div class="modal-body">
							<form action="modelos/crud_proveedores.php?op=eliminar" method="post">

								<div class="form-group">
									<?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar el siguiente proveedor?,sé eliminaran los historiales almacenados en la base de datos.</div>"); ?>
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
							<h5 class="modal-title" id="exampleModalLabel">Editar Proveedor</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
						</div>
						<div class="modal-body">
							<form action="modelos/crud_proveedores.php?op=editar" method="post">

								<!-- FORMULARIO -->

								<input type="hidden" name="id" id="id_editar">
								
								<div class="form-group">
								<label class="small mb-1" for="descripcion">Id Categoria</label>
									<input class="form-control" id="cod_proveedor" type="text" name="cod_proveedor" value=""   maxlength="30" required readonly/>	
								</div>

								<div class="form-group">
								<label class="small mb-1" for="descripcion">Edite nombre de proveedor</label>
									<input class="form-control" id="nombre_proveedor" type="text" name="nombre_proveedor" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);"value=""  placeholder="Ingrese Proveedor" maxlength="30" required/>	
								</div>


								<div class="form-group">
									<label class="small mb-1" for="descripcion">Edite Direccion Proveedor</label>
									<input class="form-control" id="direccion_proveedor" type="text" name="direccion_proveedor" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);"value=""  placeholder="Ingrese Direccion" maxlength="30" required/>
								</div>
								<div class="col-md-6">
                                    <div class="form-group"><label class="small mb-1" for="inputcorreoempresa"><b>Correo Proveedor</b> </label>
									<input class="form-control py-4" id="correo_proveedor" type="email" name="correo_proveedor" onkeypress="return email(event);" placeholder="ejemplocorreo@gmail.org" required /></div>
                                </div>
								<div class="col-md-6">
                                    <div class="form-group"><label class="small mb-1" for="inputnumerodetelefono"><b>Número De Teléfono</b> *Solo numeros</label>
									<input class="form-control py-4" id="telefono_proveedor" type="text" name="telefono_proveedor" onkeypress="return validarNumero(event);" placeholder="0000-0000" minlength="9" maxlength="9" min="0" required /></div>
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
							<h5 class="modal-title" id="exampleModalLabel">Añadir Proveedor</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
						</div>
						<div class="modal-body">
							<form action="modelos/crud_proveedores.php?op=añadir" method="post">

								<!-- FORMULARIO -->

								<div class="form-group">

								<div class="form-group">
								<label class="small mb-1" for="descripcion">Añada nombre de proveedor</label>
									<input class="form-control" id="proveedor" type="text" name="proveedor" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);"value=""  placeholder="Ingrese Proveedor" maxlength="30" required/>	
								</div>


								<div class="form-group">
									<label class="small mb-1" for="descripcion">Direccion Proveedor</label>
									<input class="form-control" id="direccion" type="text" name="direccion" onKeyUP="this.value=this.value.toUpperCase();" onkeypress="return evitarespeciales(event);"value=""  placeholder="Ingrese Direccion" maxlength="30" required/>
								</div>
								<div class="col-md-6">
                                    <div class="form-group"><label class="small mb-1" for="inputcorreoempresa"><b>Correo Proveedor</b> </label>
									<input class="form-control py-4" id="correo_proveedor" type="email" name="correo_proveedor" onkeypress="return email(event);" placeholder="ejemplocorreo@gmail.org" required /></div>
                                </div>
								<div class="col-md-6">
                                    <div class="form-group"><label class="small mb-1" for="inputnumerodetelefono"><b>Número De Teléfono</b> *Solo numeros</label>
									<input class="form-control py-4" id="telefono_proveedor" type="text" name="telefono_proveedor" onkeypress="return validarNumero(event);" placeholder="0000-0000" minlength="9" maxlength="9" min="0" required /></div>
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

			$('#detalles').append(
				'<tr>' +
				'<td>' + $nfilas + '</td>' +
				'<td>' +
				'<input class="form-control idetalle" type="text" id="producto' + $nfilas + '" name="producto' + $nfilas + '" list="roles" placeholder="Seleccione producto">' +
				'<datalist id="roles">' +
				'<option value="1">Mario</option>' +
				'<?php $sql = "SELECT * FROM tbl_roles_usuarios";
					$resultado = $mysqli->query($sql);
					while ($row = $resultado->fetch_assoc()) {
						$codigoRol = $row["id_rol"];
						$nombreRol = $row["rol"]; ?><option value=<?php echo $codigoRol; ?>> <?php echo $nombreRol;
																							} ?>' +
				'</option>' +
				'</datalist>' +
				'</td>' +
				'<td><input class="form-control idetalle" type="text" id="cantidad' + $nfilas + '" name="cantidad' + $nfilas + '" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td>' +
				'<td><input class="form-control idetalle" type="text" id="precio' + $nfilas + '" name="precio' + $nfilas + '" placeholder="Ingrese cantidad" onkeypress="return solonumerosydecimales(event);"></td></tr>');

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
			letras = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz0123456789. ";
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
			$('#cod_proveedor').val(datos[1]);
			$('#nombre_proveedor').val(datos[2]);
			$('#direccion_proveedor').val(datos[3]);
			$('#correo_proveedor').val(datos[4]);
			$('#telefono_proveedor').val(datos[5]);
			


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