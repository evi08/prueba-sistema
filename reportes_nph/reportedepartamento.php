<?php
require('../vistas/modulos/REPORTES/fpdf/fpdf.php');
include('../modelos/conexion.php');
include('../modelos/bitacora.php');

session_start();
//REGISTRO EN BITACORA DE REPORTE
$sqlbitacora = "SELECT * FROM tbl_objetos where id_objeto=26";
$resultadobitacora = $mysqli->query($sqlbitacora);

$rowbitacora = $resultadobitacora->fetch_assoc();
$id_objeto = $rowbitacora['id_objeto'];
$accion = "Solicitó reporte en el historial de departamentos del sistema";
$descripcion = $rowbitacora['descripcion_objeto'];
event_bitacora($id_objeto, $accion, $descripcion);
//TERMINA REGISTRO EN BITACORA

$filtrodepto = $_POST['filtrodepartamento'];
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
		$this->Cell(10, 20, utf8_decode('                     REPORTE DE DEPARTAMENTOS '));
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
		$this->Cell(20, 12, utf8_decode("Cod"), 1, 0, 'C', 1);
		$this->Cell(50, 12, utf8_decode("Departamento"), 1, 0, 'C', 1);
        $this->Cell(50, 12, utf8_decode("Empleados"), 1, 0, 'C', 1);
		$this->Cell(80, 12, utf8_decode("Función"), 1, 0, 'C', 1);
		$this->Cell(60, 12, utf8_decode("Descripción"), 1, 1, 'C', 1);
		//El ancho de las celdas
		$this->SetWidths(array(20, 20, 50, 50, 80, 60)); //???
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

if ($filtrodepto != "") {
	$coincidenciadepto = "%".$filtrodepto."%";
	$sqldepartamento = "SELECT * from tbl_departamentos
 	where cod_departamento like '$coincidenciadepto' 
 	or nombre_departamento like '$coincidenciadepto' 
 	or cantidad_empleados like '$coincidenciadepto'
	or funcion_del_departamento like '$coincidenciadepto'
	or descripcion like '$coincidenciadepto'";
	$resultadodepartamento = $mysqli->query($sqldepartamento);
} else { 
	$sqldepartamento = "SELECT * FROM `tbl_departamentos`;";
	$resultadodepartamento = $mysqli->query($sqldepartamento);
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

while ($data = $resultadodepartamento->fetch_assoc()) {
	$pdf->SetFont('Arial', '', 10);
	$pdf->setX(15);
	$pdf->Row(array($i + 1, utf8_decode($data['cod_departamento']), utf8_decode($data['nombre_departamento']), utf8_decode($data['cantidad_empleados']), utf8_decode($data['funcion_del_departamento']), utf8_decode($data['descripcion'])) , 25); //EL 28 ES EL MARGEN QUE TIENE DE DERECHA
	$i = $i + 1;
}

// cell(ancho, largo, contenido,borde?, salto de linea?)

$pdf->Output('I', 'reportedepartamento.pdf');
