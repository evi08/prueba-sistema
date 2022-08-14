<?php

$usuario = $_SESSION['usuario'];
if (!isset($_SESSION['session']) && $_SESSION['session'] != 'ok') {
  echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
}

?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="inicio" class="brand-link">
    <img src="vistas/img/nph_logo.png" alt="Su logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">NPH HONDURAS</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="vistas/img/usuario_logo.png" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $usuario; ?></a>
      </div>


      <!-- SidebarSearch Form -->
    </div>
    <!-- SidebarSearch Form -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header">MENU</li>

        <!-- DEPARTAMENTOS -->
        <li class="nav-item">
          <a href="departamentos" class="nav-link">
            <i class="nav-icon fas fa-warehouse"></i>
            <p>
              Departamentos
            </p>
          </a>
        </li>
        <!-- HOGARES -->
        <li class="nav-item">
          <a href="hogares" class="nav-link">
            <i class="nav-icon fas fa-warehouse"></i>
            <p>
              Hogares
            </p>
          </a>
        </li>

        <!-- TRANSPORTE -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-car"></i>
            <p>
              Transporte
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="Transporte" class="nav-link">
                <i class="nav-icon 	fas fa-plus-square"></i>
                <p>solicitar Transporte</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Vehiculos" class="nav-link">
                <i class="nav-icon fas fa-car-side"></i>
                <p>Vehiculos</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Planificacion" class="nav-link">
                <i class="nav-icon fas fa-file"></i>
                <p>Planificacion de transporte</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- INVENTARIOS -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-boxes"></i>
            <p>
              Inventarios
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <!-- requisiciones -->
            <li class="nav-item">
              <a href="requisiciones" class="nav-link">
                <i class="nav-icon fas fa-plus"></i>
                <p>Solicitudes de requisición</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="inventario" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Inventario</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="verinventario" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Activos</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="categoriaactivo" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Categoria Activos</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="verrequisicionesactivos" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Requisición Activo</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="detallerequisiciones" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Detalle Requisiciones</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="kardex" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Kardex</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- RECURSOS HUMANOS -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-anchor"></i>
            <p>
              Recursos Humanos
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="empleados" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Empleados</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="empleadosgestiones" class="nav-link">
                <i class="nav-icon fas fa-folder-plus"></i>
                <p>Gestiones</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="puestos" class="nav-link">
                <i class="nav-icon fas fa-folder-plus"></i>
                <p>Puestos</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="especialidades" class="nav-link">
                <i class="nav-icon fas fa-folder-plus"></i>
                <p>Especialidades</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- USUARIOS -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Usuarios
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="usuarios" class="nav-link">
                <i class="nav-icon fas fa-folder-plus"></i>
                <p>Gestion de usuarios</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="rolesusuarios" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Roles de usuarios</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="permisosusuarios" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Permisos</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- COMPRAS -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cart-plus"></i>
            <p>
              Compras
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="compras" class="nav-link">
                <i class="nav-icon fas fa-plus"></i>
                <p>Solicitudes</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="vercompras" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Ver Compras</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="detallecompra" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Detalle Compra</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="productos" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Productos</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="proveedores" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Proveedores</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ADMINISTRACION -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-hammer"></i>
            <p>
              Administración
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="parametros" class="nav-link">
                <i class="nav-icon fas fa-list-ol"></i>
                <p>Parámetros</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="historial" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Actividad de sistema</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="errores" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Errores </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="respaldobase" class="nav-link">
                <i class="far fa-copy"></i>
                <p>Respaldo de información </p>
              </a>
            </li>

          </ul>
        </li>
        <li class="nav-item">
          <a href="miinformacion" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>
              Mi información
            </p>
          </a>
        </li>
        <!-- AYUDA -->
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>