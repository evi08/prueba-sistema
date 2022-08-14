<?php
require "modelos/conexion.php";
if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
    echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
} else {
    date_default_timezone_set('America/Tegucigalpa');
   
    $codempleadosession = $_SESSION['cod_empleado'];
    $usuariosession = $_SESSION['usuario'];
    $codrolsession = $_SESSION['codigo_rol'];
    $codusuariosession = $_SESSION['pasar_numero_usuario'];

    $sqlusuariosinfo="select * from tbl_roles_usuarios where id_rol=
    (select id_rol_usuario from tbl_usuarios_login where id_rol_usuario='$codrolsession' and cod_usuario='$codusuariosession')";
    $resultadousuariosinfo = $mysqli->query($sqlusuariosinfo);
    $rowusuariosinfo = $resultadousuariosinfo->fetch_assoc();

    $sqldeptoinfo="select * from tbl_departamentos where cod_departamento=
    (select cod_departamento from tbl_empleados_departamentos where cod_empleado='$codempleadosession')";
    $resultadodeptoinfo = $mysqli->query($sqldeptoinfo);
    $rowdeptoinfo = $resultadodeptoinfo->fetch_assoc();

    $sqlempleadosinfo = "select emp.primer_nombre,emp.segundo_nombre,emp.primer_apellido,emp.segundo_apellido,
    emp.correo_personal,emp.correo_empresa,emp.telefono,emp.direccion,emp.identidad,emp.fecha_contratacion,
    emp.fecha_nacimiento,con.nombre_tipo_contrato,emp.horario_trabajo_empleado,emp.fecha_baja_empleado,
    emp.razon_baja_empleado,emp.numero_permisos_empleado,esp.nombre_especialidad,pues.nombre_puesto
    from tbl_empleados emp, tbl_tipo_contrato con, tbl_especialidades esp,tbl_puesto_empleados pues
    where emp.cod_empleado= '$codempleadosession' and emp.cod_tipo_contrato=con.cod_tipo_contrato
    and emp.cod_de_especialidad_empleado=esp.cod_especialidad and emp.cod_puesto_empleado=pues.cod_puesto";
    $resultadoinfoempleados = $mysqli->query($sqlempleadosinfo);
    $rowconsultaempleadosinfo = $resultadoinfoempleados->fetch_assoc();

    
    $fechaactualinfo =  date('m-d-Y');
    $fechacontratoinfo=$rowconsultaempleadosinfo['fecha_contratacion'];
    $fechaformateadaactual=strtotime($fechaactualinfo);
    $fechaformateadainfo=strtotime($fechaactualinfo);
   
?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card  border-2 rounded-lg mt-5" style="margin:20px">
                    <div class="card-header">
                        <h3 class="text-center "><img class="" src="vistas/img/nph_encabezadologin.png" alt="" height="100" width="330"><br> Mi información personal</h3>
                    </div>
                    <div class="card-body">
                        <form class="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-row " style="margin:20px 20px 10px 20px">
                                <div class="col-md-12">
                                    <label for="">Nombre completo:    <?php echo $rowconsultaempleadosinfo['primer_nombre'] . " " . $rowconsultaempleadosinfo['segundo_nombre'] . " " . $rowconsultaempleadosinfo['primer_apellido'] . " " . $rowconsultaempleadosinfo['segundo_apellido'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Correo personal:     <?php echo $rowconsultaempleadosinfo['correo_personal'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Correo empresa:      <?php echo $rowconsultaempleadosinfo['correo_empresa'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Número de télefono:   <?php echo $rowconsultaempleadosinfo['telefono'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Fecha de nacimiento:  <?php echo $rowconsultaempleadosinfo['fecha_nacimiento'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Número de identidad: <?php echo $rowconsultaempleadosinfo['identidad'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Fecha de contratación: <?php echo $rowconsultaempleadosinfo['fecha_contratacion'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Su dirección completa: <?php echo $rowconsultaempleadosinfo['direccion'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Horario de trabajo: <?php echo $rowconsultaempleadosinfo['horario_trabajo_empleado'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Especialidad: <?php echo $rowconsultaempleadosinfo['nombre_especialidad'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Puesto de trabajo:<?php echo $rowconsultaempleadosinfo['nombre_puesto'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Tipo de contrato:<?php echo $rowconsultaempleadosinfo['nombre_tipo_contrato'] ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Departamento asignado:<?php echo $rowdeptoinfo['nombre_departamento'];?> </label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Su rol asignado:<?php echo $rowusuariosinfo['rol'];?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Su usuario:<?php echo $usuariosession;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Tiempo de contrato: <?php echo $fechaformateadaactual ;?></label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Dias de vacaciones:</label>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Permisos solicitados:<?php echo $rowconsultaempleadosinfo['numero_permisos_empleado']." ". "permisos solicitados." ;?></label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
    ?>