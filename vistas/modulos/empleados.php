<!DOCTYPE html5>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
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
        $sqlpermisoempleados = "SELECT * FROM tbl_permisos WHERE id_rol='$codrolusuario' AND id_objeto=37";
        $resultadopermisoempleados = $mysqli->query($sqlpermisoempleados);
        $filaspermisoempleados = $resultadopermisoempleados->num_rows;

        if ($filaspermisoempleados > 0) {
            $rowpermisosadminempleados = $resultadopermisoempleados->fetch_assoc();
            $permisoinsercionusuario = $rowpermisosadminempleados['permiso_insercion'];
            $permisoeliminacionempleados = $rowpermisosadminempleados['permiso_eliminacion'];
            $permisoactualizacionempleados = $rowpermisosadminempleados['permiso_actualizacion'];
            $permisoconsultaempleados = $rowpermisosadminempleados['permiso_consultar'];
            if ($permisoconsultaempleados == 1) {
                $id = $_SESSION['id'];

                $sql3 = "SELECT *from tbl_usuarios_login
	            WHERE cod_usuario='$id'";
                $resultado3 = $mysqli->query($sql3);
                $row3 = $resultado3->fetch_assoc();


                $tipo_usuario = $row3['id_rol_usuario'];
                $where = "";
                if ($tipo_usuario == 1) {
                    $where = "";
                } else {
                    $where = "WHERE cod_usuario=$id";
                }

                $sql = "SELECT * FROM tbl_empleados  $where";
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
                                    <h1 class="m-0">Empleados</h1>
                                </div><!-- /.col -->
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">NPH</a></li>
                                        <li class="breadcrumb-item active">Empleados</li>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.container-fluid -->

                    </div>
                    <!-- /.content-header -->
                    <div>
                    <form action="reportes_nph/reporteempleados.php" id="frm_enviar_filtro_empleados" method="post">
						<input type="hidden" name="filtro_empleados" id="filtro_empleados">
						<div>
							<button id="btn_formulario_empleado" class="btn btn-danger" name="btn_formulario_empleado" target="_blank" style="border:20px;margin: 20px;" type="button"><i class="nav-icon fas fa-file-pdf"></i> Reporte de Empleados</button>
						</div>
					</form>
                    <script>
						$('#btn_formulario_empleado').on('click', function() {

							var filtro = $('#empleados_filter > label > input[type=search]').val();
							console.log(filtro);
							$('#filtro_empleados').val(filtro);

							console.log('#filtro_empleados');
							console.log($('#filtro_empleados').val());
							document.getElementById('frm_enviar_filtro_empleados').submit();

						});
                    </script> 
                        <div class="card-body" style="overflow-x:auto;">
                            <div class="table-responsive">
                                <table id="empleados" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Correo personal</th>
                                            <th>Correo empresa</th>
                                            <th>Télefono</th>
                                            <th>Dirección</th>
                                            <th>Identidad</th>
                                            <th>Fecha contratación</th>
                                            <th>Fecha nacimiento</th>
                                            <th>Contrato</th>
                                            <th>Horario</th>
                                            <th>Razón de baja</th>
                                            <th>Fecha de baja</th>
                                            <th>Permisos solicitados</th>
                                            <th>Especialidad</th>
                                            <th>Puesto</th>
                                            <th>Departamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $resultado->fetch_assoc()) {
                                            $nombre = $row['primer_nombre'] . " " . $row['segundo_nombre'] . " " . $row['primer_apellido'] . " " . $row['segundo_apellido'];
                                            $cod_contrato = $row['cod_tipo_contrato'];
                                            $cod_especialidad = $row['cod_de_especialidad_empleado'];
                                            $cod_puesto = $row['cod_puesto_empleado'];
                                            $codigoempleado = $row['cod_empleado'];

                                            $sql1 = "SELECT * from tbl_tipo_contrato WHERE cod_tipo_contrato='$cod_contrato'";
                                            $resultado1 = $mysqli->query($sql1);
                                            $row1 = $resultado1->fetch_assoc();
                                            $contrato = $row1['nombre_tipo_contrato'];


                                            $sql2 = "SELECT * from tbl_especialidades
												  WHERE cod_especialidad='$cod_especialidad'";
                                            $resultado2 = $mysqli->query($sql2);
                                            $row2 = $resultado2->fetch_assoc();

                                            $sql3 = "SELECT * from tbl_puesto_empleados
												  WHERE cod_puesto='$cod_puesto'";
                                            $resultado3 = $mysqli->query($sql3);
                                            $row3 = $resultado3->fetch_assoc();

                                            $sql4 = "SELECT * from tbl_empleados_departamentos
                                WHERE cod_empleado='$codigoempleado'";
                                            $resultado4 = $mysqli->query($sql4);
                                            $num4 = $resultado4->num_rows;
                                            if ($num4 > 0) {
                                                $row4 = $resultado4->fetch_assoc();
                                                $codigodepartamento = $row4['cod_departamento'];

                                                $sql5 = "SELECT * from tbl_departamentos
                                    WHERE cod_departamento='$codigodepartamento'";
                                                $resultado5 = $mysqli->query($sql5);
                                                $row5 = $resultado5->fetch_assoc();
                                                $departamento = $row5['nombre_departamento'];
                                            } else {
                                                $departamento = "No asignado";
                                            }

                                        ?>
                                            <tr>

                                                <td>
                                                    <?php if ($permisoactualizacionempleados == 1) { ?>
                                                        <div class="p-1 m-0">
                                                            <button class="btn btn-primary btn-xs btnEditar" style="border:20px" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarempleado"><i class="nav-icon fas fa-pen"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($permisoeliminacionempleados == 1) { ?>
                                                        <div class="p-1 m-0">
                                                            <button class="btn btn-primary btn-xs btnEliminar" style="border:20px;background:red" type="button" name="eliminar_usuario" data-bs-toggle="modal" data-bs-target="#modalEliminarempleado"><i class="nav-icon fas fa-trash"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                </td>

                                                <td><?php echo $row['cod_empleado']; ?></td>
                                                <td><?php echo $nombre; ?></td>
                                                <td><?php echo $row['correo_personal']; ?></td>
                                                <td><?php echo $row['correo_empresa']; ?></td>
                                                <td><?php echo $row['telefono']; ?></td>
                                                <td><?php echo $row['direccion']; ?></td>
                                                <td><?php echo $row['identidad']; ?></td>
                                                <td><?php echo $row['fecha_contratacion']; ?></td>
                                                <td><?php echo $row['fecha_nacimiento']; ?></td>
                                                <td><?php echo $contrato; ?></td>
                                                <td><?php echo $row['horario_trabajo_empleado']; ?></td>
                                                <td><?php echo $row['razon_baja_empleado']; ?></td>
                                                <td><?php echo $row['fecha_baja_empleado']; ?></td>
                                                <td><?php echo $row['numero_permisos_empleado']; ?></td>
                                                <td><?php echo $row2['nombre_especialidad']; ?></td>
                                                <td><?php echo $row3['nombre_puesto']; ?></td>
                                                <td><?php echo $departamento; ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($permisoinsercionusuario == 1) { ?>
                                            <h1 class="box-title">
                                                <!-- Button trigger modal Añadir-->
                                                <button type="button" class="btn btn-success btnAñadir" style="background:dodgerblue" data-bs-toggle="modal" data-bs-target="#modalAñadirempleado">
                                                    <i class="fa fa-plus-circle"></i>&nbsp; Nuevo empleado
                                                </button>
                                                </button>
                                            </h1>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Modal ELIMINAR-->
                        <div class="modal fade" id="modalEliminarempleado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Eliminación de empleado</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_empleados.php?op=eliminar" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-danger'>¿Usted está seguro(a) que desea eliminar al siguiente empleado,se eliminaran registros y transacciones realizados por el usuario, así como cualquier información asociada al usuario en especifico.</div>"); ?>
                                                <div class="col-md-6">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Id de empleado</b> </label><input class="form-control py-4" id="codeliminarempleado" type="text" name="codeliminarempleado" placeholder="" maxlength="" required readonly /></div>
                                                </div>
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
                        <div class="modal fade" id="filtroempleados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Pre-filtrado de datos previo a generar reporte</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="reportes_nph/reporteempleados.php" method="post">

                                            <div class="form-group">
                                                <?php echo ("<div class='alert alert-info'>Estimado usuario(a) en este espacio puede ingresar contenido de pre-filtrado(letras, còdigos etc) se mostraran los datos que coinciden con lo que usted ingresò, si no ingresa ningùn dato, se traeran todos los registros de empleados.</div>"); ?>
                                                <div class="col-md-8">
                                                    <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Ingrese valores de filtraciòn</b> </label><input class="form-control py-4" id="filtroempleados" type="text" name="filtroempleados" placeholder="Ingrese una coincidencia" maxlength=""  /></div>
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
                        <div class="modal fade" id="modalEditarempleado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Editar información de empleado</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_empleados.php?op=editar" method="post">

                                            <div class="form-group">
                                                <input type="hidden" name="id" id="idEditar">
                                            </div>

                                            <!-- select rol-->
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Código de empleado</b> </label><input class="form-control py-4" id="codempleadoeditar" type="text" name="codempleadoeditar" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="" required readonly /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione un departamento</label>
                                                <select class="form-control" id="departamentoeditar" name="departamentoeditar" required>
                                                    <?php $sql7 = "SELECT * FROM tbl_departamentos";
                                                    $resultado7 = $mysqli->query($sql7);

                                                    while ($row7 = $resultado7->fetch_assoc()) { ?>
                                                        <option value=<?php echo $row7['cod_departamento']; ?>> <?php echo $row7['nombre_departamento'];
                                                                                                            } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione tipo de contrato</label>
                                                <select class="form-control" id="contratoeditar" name="contratoeditar" required>
                                                    <?php $sql = "SELECT * FROM tbl_tipo_contrato";
                                                    $resultado = $mysqli->query($sql);

                                                    while ($row = $resultado->fetch_assoc()) {
                                                        $codcontrato = $row['cod_tipo_contrato'];
                                                        $nombrecontrato = $row['nombre_tipo_contrato']; ?>
                                                        <option value=<?php echo $codcontrato; ?>> <?php echo $nombrecontrato;
                                                                                                } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione una especialidad</label>
                                                <select class="form-control" id="especialidadeditar" name="especialidadeditar" required>
                                                    <?php $sql5 = "SELECT * FROM tbl_especialidades";
                                                    $resultado5 = $mysqli->query($sql5);
                                                    while ($row5 = $resultado5->fetch_assoc()) {
                                                    ?>
                                                        <option value=<?php echo $row5['cod_especialidad']; ?>> <?php echo $row5['nombre_especialidad'];
                                                                                                            } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione un puesto</label>
                                                <select class="form-control" id="puestoeditar" name="puestoeditar" required>
                                                    <?php $sql6 = "SELECT * FROM tbl_puesto_empleados";
                                                    $resultado6 = $mysqli->query($sql6);
                                                    while ($row6 = $resultado6->fetch_assoc()) {
                                                    ?>
                                                        <option value=<?php echo $row6['cod_puesto']; ?>> <?php echo $row6['nombre_puesto'];
                                                                                                        } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Primer Nombre</b> </label><input class="form-control py-4" id="Ingresoprimernombreeditar" type="text" name="ingresoprimernombreeditar" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Primer Nombre" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Segundo Nombre</b> </label><input class="form-control py-4" id="Ingresosegundonombreeditar" type="text" name="ingresosegundonombreeditar" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Segundo Nombre" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputprimerapellido"><b>Primer Apellido</b> </label><input class="form-control py-4" id="Ingresoprimerapellidoeditar" type="text" name="ingresoprimerapellidoeditar" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Primer Apellido" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputsegundoapellido"><b>Segundo Apellido</b> </label><input class="form-control py-4" id="Ingresosegundoapellidoeditar" type="text" name="ingresosegundoapellidoeditar" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Segundo Apellido" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputcorreopersonal"><b>Correo personal</b>*Este será su usuario.</label><input class="form-control py-4" id="Ingresocorreopersonaleditar" type="email" name="ingresocorreopersonaleditar" onkeypress="return email(event);" placeholder="ejemplocorreo@gmail.com" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputcorreoempresa"><b>Correo Empresa</b> </label><input class="form-control py-4" id="Ingresocorreoempresaeditar" type="email" name="ingresocorreoempresaeditar" onkeypress="return email(event);" placeholder="ejemplocorreo@gmail.org" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputnumerodetelefono"><b>Número De Teléfono</b> *Solo numeros</label><input class="form-control py-4" id="Ingresotelefonoeditar" type="text" name="ingresotelefonoeditar" onkeypress="return validarNumero(event);" placeholder="0000-0000" minlength="9" maxlength="9" min="0" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputfechanacimiento"><b>Fecha De Nacimiento</b> </label><input class="form-control py-4" id="Fechadenacimientoeditar" type="Date" name="ingresonacimientoeditar" placeholder="Su fecha de nacimiento" minlength="1940" maxlength="2004" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputidentidad"><b>Número De Identidad</b>*Solo números y sin guiones</label><input class="form-control py-4" id="Ingresesunumerodeidentidadeditar" type="text" name="ingresoidentidadeditar" onkeypress="return validarNumero(event);" placeholder="0000-0000-00000" minlength="15" maxlength="15" min="0" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputidentidad"><b>Razón de baja(No es obligatorio)</b></label><input class="form-control py-4" id="razonbajaeditar" type="text" name="razonbajaeditar" minlength="" maxlength="100" /></div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group"><label class="small mb-1" for="inputidentidad"><b>Fecha de baja(No es obligatorio)</b></label><input class="form-control py-4" id="fechabajaeditar" type="date" name="fechabajaeditar" /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputfechacontratacion"><b>Fecha De Contratacion</b> </label><input class="form-control py-4" id="Ingresofechadecontratacioneditar" type="Date" name="ingresocontratacioneditar" placeholder="Su fecha contratacion" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputdireccion"><b>Direccion Completa(barrio, colonia, #casa etc)</b> </label><input class="form-control py-4" id="Ingresodireccioneditar" type="text" name="ingresodireccioneditar" placeholder="Su Direccion" maxlength="200" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputhorariotrabajo"><b>Dias de trabajo</b> </label><input class="form-control py-4" id="diastrabajoeditar" type="text" name="diastrabajoeditar" placeholder="Lunes-Viernes" maxlength="20" required /></div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group"><label class="small mb-1" for="inputhorariotrabajo"><b>Hora de entrada</b> </label><input class="form-control py-4" id="horariodetrabajoentradaeditar" type="time" name="horariodetrabajoentradaeditar" placeholder="Su hora de entrada" maxlength="20" required /></div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group"><label class="small mb-1" for="inputhorariotrabajo"><b>Hora de salida</b> </label><input class="form-control py-4" id="horariodetrabajosalidaeditar" type="time" name="horariodetrabajosalidaeditar" placeholder="Su hora de entrada" maxlength="20" required /></div>
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
                        <div class="modal fade" id="modalAñadirempleado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Registrar empleado nuevo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="modelos/crud_empleados.php?op=añadir" method="post">

                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione un departamento</label>
                                                <select class="form-control" id="departamentoañadir" name="departamentoañadir" required>
                                                    <?php $sql7 = "SELECT * FROM tbl_departamentos";
                                                    $resultado7 = $mysqli->query($sql7);

                                                    while ($row7 = $resultado7->fetch_assoc()) { ?>
                                                        <option value=<?php echo $row7['cod_departamento']; ?>> <?php echo $row7['nombre_departamento'];
                                                                                                            } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione tipo de contrato</label>
                                                <select class="form-control" id="contratoañadir" name="contratoañadir" required>
                                                    <?php $sql = "SELECT * FROM tbl_tipo_contrato";
                                                    $resultado = $mysqli->query($sql);

                                                    while ($row = $resultado->fetch_assoc()) {
                                                        $codcontrato = $row['cod_tipo_contrato'];
                                                        $nombrecontrato = $row['nombre_tipo_contrato']; ?>
                                                        <option value=<?php echo $codcontrato; ?>> <?php echo $nombrecontrato;
                                                                                                } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione una especialidad</label>
                                                <select class="form-control" id="especialidadañadir" name="especialidadañadir" required>
                                                    <?php $sql5 = "SELECT * FROM tbl_especialidades";
                                                    $resultado5 = $mysqli->query($sql5);
                                                    while ($row5 = $resultado5->fetch_assoc()) {
                                                    ?>
                                                        <option value=<?php echo $row5['cod_especialidad']; ?>> <?php echo $row5['nombre_especialidad'];
                                                                                                            } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="rol">Seleccione un puesto</label>
                                                <select class="form-control" id="puestoañadir" name="puestoañadir" required>
                                                    <?php $sql6 = "SELECT * FROM tbl_puesto_empleados";
                                                    $resultado6 = $mysqli->query($sql6);
                                                    while ($row6 = $resultado6->fetch_assoc()) {
                                                    ?>
                                                        <option value=<?php echo $row6['cod_puesto']; ?>> <?php echo $row6['nombre_puesto'];
                                                                                                        } ?> </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputprimernombre"><b>Primer Nombre</b> </label><input class="form-control py-4" id="Ingresoprimernombre" type="text" name="ingresoprimernombre" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Primer Nombre" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputsegundonombre"><b>Segundo Nombre</b> </label><input class="form-control py-4" id="Ingresosegundonombre" type="text" name="ingresosegundonombre" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Segundo Nombre" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputprimerapellido"><b>Primer Apellido</b> </label><input class="form-control py-4" id="Ingresoprimerapellido" type="text" name="ingresoprimerapellido" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Primer Apellido" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputsegundoapellido"><b>Segundo Apellido</b> </label><input class="form-control py-4" id="Ingresosegundoapellido" type="text" name="ingresosegundoapellido" onkeypress="return check(event);" onKeyUP="this.value=this.value.toUpperCase();" placeholder="Su Segundo Apellido" maxlength="10" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputcorreopersonal"><b>Correo personal</b>*Este será su usuario.</label><input class="form-control py-4" id="Ingresocorreopersonal" type="email" name="ingresocorreopersonal" onkeypress="return email(event);" placeholder="ejemplocorreo@gmail.com" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputcorreoempresa"><b>Correo Empresa</b> </label><input class="form-control py-4" id="Ingresocorreoempresa" type="email" name="ingresocorreoempresa" onkeypress="return email(event);" placeholder="ejemplocorreo@gmail.org" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputnumerodetelefono"><b>Número De Teléfono</b> *Solo numeros</label><input class="form-control py-4" id="Ingresotelefono" type="text" name="ingresotelefono" onkeypress="return validarNumero(event);" placeholder="0000-0000" minlength="9" maxlength="9" min="0" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputfechanacimiento"><b>Fecha De Nacimiento</b> </label><input class="form-control py-4" id="Fechadenacimiento" type="Date" name="ingresonacimiento" placeholder="Su fecha de nacimiento" minlength="1940" maxlength="2004" required /></div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group"><label class="small mb-1" for="inputidentidad"><b>Número De Identidad</b>*Solo números y sin guiones</label><input class="form-control py-4" id="Ingresesunumerodeidentidad" type="text" name="ingresoidentidad" onkeypress="return validarNumero(event);" placeholder="0000-0000-00000" minlength="15" maxlength="15" min="0" required /></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group"><label class="small mb-1" for="inputfechacontratacion"><b>Fecha De Contratacion</b> </label><input class="form-control py-4" id="Ingresofechadecontratacion" type="Date" name="ingresocontratacion" placeholder="Su fecha contratacion" required /></div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="form-group"><label class="small mb-1" for="inputdireccion"><b>Direccion Completa(barrio, colonia, #casa etc)</b> </label><input class="form-control py-4" id="Ingresodireccion" type="text" name="ingresodireccion" placeholder="Su Direccion" maxlength="200" required /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputhorariotrabajo"><b>Dias de trabajo</b> </label><input class="form-control py-4" id="diastrabajoañadir" type="text" name="diastrabajoañadir" placeholder="Lunes-Viernes" maxlength="20" required /></div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group"><label class="small mb-1" for="inputhorariotrabajo"><b>Hora de entrada</b> </label><input class="form-control py-4" id="horariodetrabajoentrada" type="time" name="horariodetrabajoentrada" placeholder="Su hora de entrada" maxlength="20" required /></div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group"><label class="small mb-1" for="inputhorariotrabajo"><b>Hora de salida</b> </label><input class="form-control py-4" id="horariodetrabajosalida" type="time" name="horariodetrabajosalida" placeholder="Su hora de entrada" maxlength="20" required /></div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" name="registrarempleado" class="btn btn-primary">Registrar empleado</button>
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
                        $('#codempleadoeditar').val(datos[1]);
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
                        $('#codeliminarempleado').val(datos[1]);
                        $('#idEliminarLabel').text(datos[2]);
                    });
                    $(document).ready(function() {
                        var table = $('#empleados').DataTable({
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