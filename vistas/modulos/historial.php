<!DOCTYPE html5>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>HISTORIAL</title>
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
	require "modelos/bitacora.php";
	if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
		echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
	} else {
		$codrolusuario = $_SESSION['codigo_rol'];
		$sqlgestionbitacora = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=41";
		$resultadogestionbitacora = $mysqli->query($sqlgestionbitacora);
		$filasgestionbitacora = $resultadogestionbitacora->num_rows;

		if ($filasgestionbitacora  > 0) {
			$rowgestionbitacora = $resultadogestionbitacora->fetch_assoc();
			$permisoconsultagestionbitacora = $rowgestionbitacora['permiso_consultar'];
			if ($permisoconsultagestionbitacora == 1) {
				$id = $_SESSION['id'];

				$sql = "SELECT * FROM tbl_ms_bitacora";
				$resultado = $mysqli->query($sql);
				$num = $resultado->num_rows;

	?>

				<div class="content-wrapper">
					<!-- Content Header (Page header) -->
					<div class="content-header">
						<div class="container-fluid">
							<div class="row mb-2">
								<div class="col-sm-6">
									<h1 class="m-0">Registros del sistema</h1>
								</div><!-- /.col -->
								<div class="col-sm-6">
									<ol class="breadcrumb float-sm-right">
										<li class="breadcrumb-item"><a href="#">NPH</a></li>
										<li class="breadcrumb-item active">Historial</li>
									</ol>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.container-fluid -->

					</div>
					<!-- Modal Filtro de empleados-->
					<div class="modal fade" id="filtrobitacora" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
								</div>
								<div class="modal-body">
									<form action="reportes_nph/reportebitacora.php" method="post">

										<div class="form-group">
											<?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
											<div class="col-md-8">
												<div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtración</b> </label><input class="form-control py-4" id="filtroempleados" type="text" name="filtrobitacora" placeholder="Ingrese una coincidencia" maxlength="" /></div>
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
					<!-- /.content-header -->
					<div>
						<form action="reportes_nph/reportebitacora.php" id="frm_enviar_filtro_historial" method="post">
							<input type="hidden" name="filtro_bitacora" id="filtro_bitacora">
							<div>
								<button id="btn_formulario_historial" class="btn btn-danger" name="btn_formulario_historial" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Bitacora</button>
							</div>
						</form>
						<script>
							$('#btn_formulario_historial').on('click', function() {

								var filtro = $('#bitacora_filter > label > input[type=search]').val();
								console.log(filtro);
								$('#filtro_bitacora').val(filtro);

								console.log('#filtro_bitacora');
								console.log($('#filtro_bitacora').val());
								document.getElementById('frm_enviar_filtro_historial').submit();

							});
						</script>
						<div class="card-body" style="overflow-x:auto;">
							<div class="table-responsive">
								<table id="bitacora" class="table table-bordered ">
									<thead>
										<tr>
											<th>Código</th>
											<th>Id objeto</th>
											<th>Cod. usuario ejecutor</th>
											<th>Usuario ejecutor</th>
											<th>Fecha acción</th>
											<th>Acción</th>
											<th>Descripción</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($row = $resultado->fetch_assoc()) {
										?>
											<tr>

												<td><?php echo $row['id_bitacora']; ?></td>
												<td><?php echo $row['id_objeto']; ?></td>
												<td><?php echo $row['cod_usuario']; ?></td>
												<td><?php echo  $row['ejecutor']; ?></td>
												<td><?php echo $row['fecha']; ?></td>
												<td><?php echo $row['accion']; ?></td>
												<td><?php echo $row['descripcion']; ?></td>
											</tr>

										<?php } ?>
									</tbody>
								</table>

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
		$(document).ready(function() {
			var table = $('#bitacora').DataTable({
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
				responsive: "true",
			});
			//Creamos una fila en el head de la tabla y lo clonamos para cada columna
		});
	</script>

</html>