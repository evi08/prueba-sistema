<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGSR|NPH HONDURAS</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="vistas/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="vistas/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="vistas/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="vistas/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="vistas/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="vistas/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="vistas/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="vistas/plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!--Datatable CSS-->
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="vistas/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  <link rel="" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>

  <!-- jQuery -->
  <script src="vistas/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="vistas/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="vistas/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="vistas/plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="vistas/plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="vistas/plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="vistas/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="vistas/plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="vistas/plugins/moment/moment.min.js"></script>
  <script src="vistas/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="vistas/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="vistas/plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="vistas/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="vistas/dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="vistas/dist/js/pages/dashboard.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="vistas/dist/js/demo.js"></script>

  <!-- Datatable Jquery-->
  <script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="vistas/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script type="text/javascript" src="vistas/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <!--SweetAlert2-->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>

<!-- Preloader -->

<?php
if (isset($_SESSION['session']) && $_SESSION['session'] == 'ok') {
  echo '<body class="hold-transition sidebar-mini layout-fixed">';
  echo '<div class="wrapper">';
  include "vistas/modulos/encabezado.php";
  include "vistas/modulos/menu.php";

  if (isset($_GET['ruta'])) {
    if (
      $_GET['ruta'] == "inicio" ||
      $_GET['ruta'] == "verrequisicionesactivos" ||
      $_GET['ruta'] == "detallerequisiciones" ||
      $_GET['ruta'] == "nuevasolicitudtransporte" ||
      $_GET['ruta'] == "reporterequisiciones" ||
      $_GET['ruta'] == "reportetransporte" ||
      $_GET['ruta'] == "usuarios" ||
      $_GET['ruta'] == "verinventario" ||
      $_GET['ruta'] == "categoriaactivo" ||
      $_GET['ruta'] == "versolicitudestransporte" ||
      $_GET['ruta'] == "empleados" ||
      $_GET['ruta'] == "departamentos" ||
      $_GET['ruta'] == "Transporte" ||
      $_GET['ruta'] == "Vehiculos" ||
      $_GET['ruta'] == "Planificacion" ||
      $_GET['ruta'] == "vacaciones" ||
      $_GET['ruta'] == "empleadosgestiones" ||
      $_GET['ruta'] == "rolesusuarios" ||
      $_GET['ruta'] == "permisosusuarios" ||
      $_GET['ruta'] == "puestos" ||
      $_GET['ruta'] == "especialidades" ||
      $_GET['ruta'] == "compras" ||
      $_GET['ruta'] == "proveedores" ||
      $_GET['ruta'] == "kardex" ||
      $_GET['ruta'] == "detallecompra" ||
      $_GET['ruta'] == "productos" ||
      $_GET['ruta'] == "vercompras" ||
      $_GET['ruta'] == "respaldobase" ||
      $_GET['ruta'] == "parametros" ||
      $_GET['ruta'] == "historial" ||
      $_GET['ruta'] == "hogares" ||
      $_GET['ruta'] == "requisiciones" ||     
      $_GET['ruta'] == "errores" ||  
      $_GET['ruta'] == "miinformacion" ||  
      $_GET['ruta'] == "inventario" ) {

      include "vistas/modulos/" . $_GET['ruta'] . ".php";
    } else {
      include "vistas/modulos/404.php";
    }
  }
  include "vistas/modulos/footer.php";
} else {
  include "vistas/modulos/login.php";
  echo '<body class="hold-transition sidebar-mini layout-fixed">';
}
?>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script src="vistas/js/plantilla.js"></script>
<script src="vistas/js/usuarios.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>


<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
</body>

</html>