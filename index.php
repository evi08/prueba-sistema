<?php
session_start();
require_once "controladores/plantilla.controlador.php";
$plantilla=new ControladorPlantilla();
$plantilla->ctrPlantilla();
