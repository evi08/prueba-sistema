<?php
require('../vistas/modulos/REPORTES/fpdf/fpdf.php');
include('../modelos/conexion.php');
include('../modelos/bitacora.php');

session_start();
//REGISTRO EN BITACORA DE REPORTE
$sqlbitacora = "SELECT * FROM tbl_objetos where id_objeto=55";
$resultadobitacora = $mysqli->query($sqlbitacora);

$rowbitacora = $resultadobitacora->fetch_assoc();
$id_objeto = $rowbitacora['id_objeto'];
$accion = "Solicitó reporte en el historial de compras del sistema";
$descripcion = $rowbitacora['descripcion_objeto'];
event_bitacora($id_objeto, $accion, $descripcion);
//TERMINA REGISTRO EN BITACORA

$filtrocompra = $_POST['filtrocompras'];
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
		$this->SetX(122);
		$this->Cell(10, 15, utf8_decode('REPORTE LISTADO DE COMPRAS '));
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
		$this->Cell(15, 12, 'Num', 1, 0, 'C', 1);
		$this->Cell(25, 12, utf8_decode("Id_compras"), 1, 0, 'C', 1);
		$this->Cell(35, 12, utf8_decode("Departamento"), 1, 0, 'C', 1);
		$this->Cell(60, 12, utf8_decode("Empleado"), 1, 0, 'C', 1);
		$this->Cell(40, 12, utf8_decode("Fecha Solicitud"), 1, 0, 'C', 1);
		$this->Cell(35, 12, utf8_decode("Justificacion"), 1, 0, 'C', 1);
		$this->Cell(30, 12, utf8_decode("Proveedor"), 1, 0, 'C', 1);
		$this->Cell(35, 12, utf8_decode("Total a Pagar"), 1, 0, 'C', 1);
		$this->Cell(35, 12, utf8_decode("Estado Solicitud"), 1, 1, 'C', 1);//salto de linea

		//El ancho de las celdas
		$this->SetWidths(array(15, 25, 35, 60, 40, 35, 30, 35, 35)); //???
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

if($filtrocompra==""){
    $sql = "SELECT s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre,\" \",em.segundo_nombre,\" \",em.primer_apellido,\" \",em.segundo_apellido) empleado, s.fechahora_ingreso, s.justificacion, p.nombre_proveedor, c.total_pagar, e.nombre_del_estado 
FROM tbl_solicitudes s, tbl_departamentos d, tbl_empleados em, tbl_compras c, tbl_estado_entrega_aprobacion e, tbl_proveedores p 
WHERE d.cod_departamento = s.cod_departamento AND em.cod_empleado = s.cod_empleado AND c.cod_proveedor = p.cod_proveedor AND c.id_solicitud = s.id_solicitud AND e.cod_estado = s.cod_estado AND s.id_tipo_solicitud = 3 
ORDER BY s.id_solicitud DESC;";
$resultado = $mysqli->query($sql);

}else{
$coincidenciacompra="%".$filtrocompra."%";
$sql = "SELECT s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre,\" \",em.segundo_nombre,\" \",em.primer_apellido,\" \",em.segundo_apellido) empleado, s.fechahora_ingreso, s.justificacion, p.nombre_proveedor, c.total_pagar, e.nombre_del_estado 
FROM tbl_solicitudes s, tbl_departamentos d, tbl_empleados em, tbl_compras c, tbl_estado_entrega_aprobacion e, tbl_proveedores p 
WHERE (d.cod_departamento = s.cod_departamento AND em.cod_empleado = s.cod_empleado AND c.cod_proveedor = p.cod_proveedor AND c.id_solicitud = s.id_solicitud AND e.cod_estado = s.cod_estado AND s.id_tipo_solicitud = 3 )and
s.id_solicitud like '$coincidenciacompra'
or d.nombre_departamento like '$coincidenciacompra'
or empleado like '$coincidenciacompra'
or  s.fechahora_ingreso like '$coincidenciacompra'
or s.justificacion like '$coincidenciacompra'
or p.nombre_proveedor like '$coincidenciacompra'
or c.total_pagar like '$coincidenciacompra'
or e.nombre_del_estado like '$coincidenciacompra'";
$resultado = $mysqli->query($sql);
}
//ESTO SE DEBERIA DE BORRAR PERO SI LO HAGO SALE ERROR EN LINEA 249//
$sql = "SELECT s.id_solicitud, d.nombre_departamento, concat(em.primer_nombre,\" \",em.segundo_nombre,\" \",em.primer_apellido,\" \",em.segundo_apellido) empleado, s.fechahora_ingreso, s.justificacion, p.nombre_proveedor, c.total_pagar, e.nombre_del_estado 
FROM tbl_solicitudes s, tbl_departamentos d, tbl_empleados em, tbl_compras c, tbl_estado_entrega_aprobacion e, tbl_proveedores p 
WHERE d.cod_departamento = s.cod_departamento AND em.cod_empleado = s.cod_empleado AND c.cod_proveedor = p.cod_proveedor AND c.id_solicitud = s.id_solicitud AND e.cod_estado = s.cod_estado AND s.id_tipo_solicitud = 3 
ORDER BY s.id_solicitud DESC;";
$resultado = $mysqli->query($sql);

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

while ($data = $resultado->fetch_assoc()) {
	$pdf->SetFont('Arial', '', 10);
	$pdf->setX(15);
	$pdf->Row(array($i + 1, utf8_decode($data['id_solicitud']), utf8_decode($data['nombre_departamento']), utf8_decode($data['empleado']), utf8_decode($data['fechahora_ingreso']), utf8_decode($data['justificacion']), utf8_decode($data['nombre_proveedor']), utf8_decode($data['total_pagar']), utf8_decode($data['nombre_del_estado'])), 25); //EL 28 ES EL MARGEN QUE TIENE DE DERECHA
	$i = $i + 1;
}

// cell(ancho, largo, contenido,borde?, salto de linea?)

$pdf->Output('I', 'Reportecompras.pdf');
