<?php

if(!isset($_SESSION['session']) && $_SESSION['session']!='ok'){
  echo "<script>
        location.href='../../index.php';
        alert('Usted necesita iniciar sesión para acceder a esta página');
        </script>";
}
?>
<footer class="main-footer" >
    <strong >Copyright &copy; 2022 <a href="https://fundacion-nph.org/nph-honduras/">NPH HONDURAS</a>.</strong>
    Todos los derechos reservados.
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>