<?php
require('../vistas/modulos/REPORTES/fpdf/fpdf.php');
include('../modelos/conexion.php');
include('../modelos/bitacora.php');

session_start();
//REGISTRO EN BITACORA DE REPORTE
$sqlbitacora = "SELECT * FROM tbl_objetos where id_objeto=29";
$resultadobitacora = $mysqli->query($sqlbitacora);

$rowbitacora = $resultadobitacora->fetch_assoc();
$id_objeto = $rowbitacora['id_objeto'];
$accion = "Solicitó reporte en el historial de usuarios del sistema";
$descripcion = $rowbitacora['descripcion_objeto'];
event_bitacora($id_objeto, $accion, $descripcion);
//TERMINA REGISTRO EN BITACORA

$codusuario = $_SESSION['id'];
$usuarioreporteparametros = $_SESSION['usuario'];
$empresanombre = $_SESSION['nombreempresa'];
$filtrousuarios = $_POST['filtro_usuario'];
class PDF extends FPDF
{
    function Header()
    {
        $empresanombre = $_SESSION['nombreempresa'];
        $codusuario = $_SESSION['id'];
        $usuarioreporteparametros = $_SESSION['usuario'];
        date_default_timezone_set("America/Tegucigalpa");
        //$this->Image('img/triangulosrecortados.png',0,0,50);
        $this->Image('../Vistas/modulos/REPORTES/img/LOGO1.jpg', 10, 10, 20);
        $this->Image('../Vistas/modulos/REPORTES/img/LOGO1.jpg', 320, 10, 20);
        $this->SetY(10);
        $this->SetX(100);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(10, 5, utf8_decode($empresanombre), 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->SetY(30);
        $this->SetX(100);
        $this->Cell(10, 10, utf8_decode('REPORTE DE LISTADO DE TODOS LOS USUARIOS REGISTRADOS'));
        $this->SetX(5);
        $this->Ln(11);
        //$this->Cell(40,5,date('d/m/Y') ,00,1,'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(320, 15, "Fecha y hora de reporte: " . date('d/m/Y | g:i:a'), 0, 1, 'R');

        $this->Ln(10);

        // -----------ENCABEZADO------------------
        $this->SetX(25);
        $this->SetFillColor(72, 208, 234);
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(20, 12, 'Num', 1, 0, 'C', 1);
        $this->Cell(20, 12, utf8_decode("Rol"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Estado"), 1, 0, 'C', 1);
        $this->Cell(40, 12, utf8_decode("Usuario"), 1, 0, 'C', 1);
        $this->Cell(60, 12, utf8_decode("Clave/encriptada"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Empleado"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Último login"), 1, 0, 'C', 1);
        $this->Cell(50, 12, utf8_decode("N° ingresos"), 1, 0, 'C', 1);
        $this->Cell(40, 12, utf8_decode("Preg. contestadas"), 1, 1, 'C', 1); //salto de linea
        //El ancho de las celdas
        $this->SetWidths(array(20, 20, 30, 40, 60, 30, 30, 50, 40)); //???
    }

    // Pie de página

    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetFont('helvetica', 'B', 9);
        $this->SetY(-15);


        //$this->Line(10,287,200,287);
        $this->Cell(170, 0, utf8_decode('NPH-SGSR 2022 © Todos los derechos reservados.'), 0, 0, 'C');
        $this->Cell(0, 0, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // --------------------METODO PARA ADAPTAR LAS CELDAS------------------------------
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data, $setX) //
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }

        $h = 8 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h, $setX);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h, 'DF');
            //Print the text
            $this->MultiCell($w, 8, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h, $setX)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage('LANDSCAPE', 'LEGAL'); //añade l apagina / en blanco
            $this->SetX($setX);
        }
        if ($setX == 100) {
            $this->SetX(100);
        } else {
            $this->SetX($setX);
        }
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }

            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }

                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
    // -----------------------------------TERMINA---------------------------------
}

//------------------OBTENES LOS DATOS DE LA BASE DE DATOS-------------------------
if ($filtrousuarios == "") {
    $sqlusuarios = "SELECT ro.rol,es.estado,us.nombre_usuario_correo,us.clave_usuario, em.primer_nombre,
    em.segundo_nombre,em.primer_apellido,em.segundo_apellido,us.fecha_ultima_conexion,us.numero_ingresos, 
    us.num_preguntas_contestadas from tbl_roles_usuarios ro,tbl_usuarios_login us,tbl_estado_usuarios es,tbl_empleados em
    where us.id_rol_usuario=ro.id_rol and us.cod_estado=es.codigo_estado and us.cod_empleado=em.cod_empleado";
    $resultadousuarios = $mysqli->query($sqlusuarios);
} else {
    $coincidenciausuarios = "%" . $filtrousuarios . "%";

    $sqlusuarios = "SELECT ro.rol,es.estado,us.nombre_usuario_correo,us.clave_usuario, concat(em.primer_nombre,\" \",em.segundo_nombre,\" \",em.primer_apellido,\" \",em.segundo_apellido) empleado,us.fecha_ultima_conexion,us.numero_ingresos, 
    us.num_preguntas_contestadas from tbl_roles_usuarios ro,tbl_usuarios_login us,tbl_estado_usuarios es,tbl_empleados em
    where (us.id_rol_usuario=ro.id_rol and us.cod_estado=es.codigo_estado and us.cod_empleado=em.cod_empleado)
    and ro.rol like '$coincidenciausuarios'
    or es.estado like '$coincidenciausuarios'
    or us.nombre_usuario_correo like '$coincidenciausuarios'
    or us.clave_usuario like '$coincidenciausuarios'
    or empleado like '$coincidenciausuarios'
    or us.fecha_ultima_conexion like '$coincidenciausuarios'
    or us.num_preguntas_contestadas like '$coincidenciausuarios'
    or us.numero_ingresos like '$coincidenciausuarios'";
    $resultadousuarios = $mysqli->query($sqlusuarios);
}

/* IMPORTANTE: si estan usando MVC o algún CORE de php les recomiendo hacer uso del metodo
que se llama *select_all* ya que es el que haria uso del *fetchall* tal y como ven en la linea 161
ya que es el que devuelve un array de todos los registros de la base de datos 
si hacen uso de el metodo *select* hara uso de fetch y este solo selecciona una linea*/

//--------------TERMINA BASE DE DATOS-----------------------------------------------

// Creación del objeto de la clase heredada
$pdf = new PDF(); //hacemos una instancia de la clase
$pdf->AliasNbPages();
$pdf->AddPage('LANDSCAPE', 'LEGAL'); //añade l apagina / en blanco
$pdf->SetMargins(10, 10, 10, 10); //MARGENE2
$pdf->SetAutoPageBreak(true, 20); //salto de pagina automatico

// -------TERMINA----ENCABEZADO------------------

$pdf->SetFillColor(252, 254, 254); //color de fondo rgb
$pdf->SetDrawColor(61, 61, 61); //color de linea  rgb

$i = 0;

while ($data = $resultadousuarios->fetch_assoc()) {
    //Extracción de rol


    $pdf->SetFont('Arial', '', 10);
    $pdf->setX(15);
    $pdf->Row(array($i + 1, utf8_decode($data['rol']), utf8_decode($data['estado']), utf8_decode($data['nombre_usuario_correo']),  utf8_decode($data['clave_usuario']), utf8_decode($data['empleado']) , utf8_decode($data['fecha_ultima_conexion']), utf8_decode($data['numero_ingresos']), utf8_decode($data['num_preguntas_contestadas'])), 25); //EL 28 ES EL MARGEN QUE TIENE DE DERECHA
    $i = $i + 1;
}

// cell(ancho, largo, contenido,borde?, salto de linea?)

$pdf->Output('I', 'Reportebitacora.pdf');
