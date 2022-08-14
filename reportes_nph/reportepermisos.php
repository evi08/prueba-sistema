<?php
require('../vistas/modulos/REPORTES/fpdf/fpdf.php');
include('../modelos/conexion.php');
include('../modelos/bitacora.php');

session_start();
//REGISTRO EN BITACORA DE REPORTE
$sqlbitacora = "SELECT * FROM tbl_objetos where id_objeto=31";
$resultadobitacora = $mysqli->query($sqlbitacora);

$rowbitacora = $resultadobitacora->fetch_assoc();
$id_objeto = $rowbitacora['id_objeto'];
$accion = "Solicitó reporte en el historial de permisos del usuario del sistema";
$descripcion = $rowbitacora['descripcion_objeto'];
event_bitacora($id_objeto, $accion, $descripcion);
//TERMINA REGISTRO EN BITACORA

$filtropermisos=$_POST['filtro_permisos'];
$codusuario = $_SESSION['id'];
$usuarioreporteparametros = $_SESSION['usuario'];
$empresanombre = $_SESSION['nombreempresa'];
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
		$this->Cell(10, 10, utf8_decode('    REPORTE DE PERMISOS ASIGNADOS A ROLES'));
        $this->SetX(5);
        $this->Ln(11);
        //$this->Cell(40,5,date('d/m/Y') ,00,1,'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(320, 10, "Fecha y hora de reporte: " . date('d/m/Y | g:i:a'), 0, 1, 'R');
       
        $this->Ln(10);

        // -----------ENCABEZADO------------------
        $this->SetX(25);
        $this->SetFillColor(72, 208, 234);
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(20, 12, 'Num', 1, 0, 'C', 1);
        $this->Cell(40, 12, utf8_decode("Rol"), 1, 0, 'C', 1);
        $this->Cell(40, 12, utf8_decode("Objeto/pantalla"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Insertar"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Eliminar"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Actualizar"), 1, 0, 'C', 1);
        $this->Cell(30, 12, utf8_decode("Consultar/ver"), 1, 0, 'C', 1);
        $this->Cell(50, 12, utf8_decode("Modificado por"), 1, 0, 'C', 1);
        $this->Cell(50, 12, utf8_decode("última modificación"), 1, 1, 'C', 1); //salto de linea
        //El ancho de las celdas
        $this->SetWidths(array(20, 40, 40, 30, 30, 30, 30, 50, 50)); //???
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
if($filtropermisos==""){
    $sqlpermisos = "SELECT * from tbl_permisos";
    $resultadopermisos = $mysqli->query($sqlpermisos);
}else{
$coincidenciapermisos="%".$filtropermisos."%";
$sqlpermisos = "SELECT r.rol, o.objeto, p.permiso_insercion, p.permiso_eliminacion, p.permiso_actualizacion, p.permiso_consultar , 
p.creado_por, p.fecha_creacion, p.modificado_por, p.fecha_modificacion
from tbl_roles_usuarios r,  tbl_objetos o, tbl_permisos p
where (p.id_rol = r.id_rol and p.id_objeto = o.id_objeto) and 
r.rol like '$coincidenciapermisos'
or o.objeto like '$coincidenciapermisos'
or p.permiso_insercion like '$coincidenciapermisos'
or p.permiso_eliminacion like '$coincidenciapermisos'
or p.permiso_actualizacion like '$coincidenciapermisos'
or p.permiso_consultar like '$coincidenciapermisos'
or p.creado_por like '$coincidenciapermisos'
or p.fecha_creacion like '$coincidenciapermisos'
or p.modificado_por like '$coincidenciapermisos'
or p.fecha_modificacion like '$coincidenciapermisos'";
$resultadopermisos = $mysqli->query($sqlpermisos);
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

while ($data = $resultadopermisos->fetch_assoc()) {
    //extracción del rol
   
    //Verificación de permisos y conversión a Sí/No
    if($data['permiso_insercion']==1){
        $pi="Sí";
    }else{
        $pi="No";
    }
    if($data['permiso_eliminacion']==1){
        $pe="Sí";
    }else{
        $pe="No";
    }
    if($data['permiso_actualizacion']==1){
        $pa="Sí";
    }else{
        $pa="No";
    }
    if($data['permiso_consultar']==1){
        $pc="Sí";
    }else{
        $pc="No";
    }
    //llenado de arreglo y tabla
    $pdf->SetFont('Arial', '', 10);
    $pdf->setX(15);
    $pdf->Row(array($i + 1, utf8_decode($data['rol']), utf8_decode($data['objeto']), utf8_decode($pi),  utf8_decode($pe), utf8_decode($pa), utf8_decode($pc), utf8_decode($data['modificado_por']), utf8_decode($data['fecha_modificacion'])), 25); //EL 28 ES EL MARGEN QUE TIENE DE DERECHA
    $i = $i + 1;
}

// cell(ancho, largo, contenido,borde?, salto de linea?)

$pdf->Output('I', 'Reportebitacora.pdf');
