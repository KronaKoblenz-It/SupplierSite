<?php
require('fpdf_rotate.php');
include("db-utils.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2016 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
$conn = db_connect($dbase); 

$cCodice = $_GET['art'];
$cLotto = $_GET['lotto'];
$cDesc = (isset($_GET['desc']) ? $_GET['desc'] : "");
$code = (isset($_GET['code']) ? trim($_GET['code']) : "");
$cliven = (isset($_GET['cliven']) ? trim($_GET['cliven']) : "");
$mode = (isset($_GET['mode']) ? $_GET['mode'] : "CF");

$dataStampa = date("d/m/Y", time());
$codForn = $cookie[0];


class eFPDF extends FPDF{
	function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0)
	{
		$font_angle+=90+$txt_angle;
		$txt_angle*=M_PI/180;
		$font_angle*=M_PI/180;

		$txt_dx=cos($txt_angle);
		$txt_dy=sin($txt_angle);
		$font_dx=cos($font_angle);
		$font_dy=sin($font_angle);

		$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	}
}

// -------------------------------------------------- //
//                  PROPERTIES
// -------------------------------------------------- //
//Come prima cosa verifico se il codice articolo ha dicitura 1 o 2 PO
$confez = 0;
if (strpos($cCodice, '2 PO') !== false) {
    $confez = 2;
} else {
	$confez = 1;
}

$codlingua = "UK";
$pathlogo = $baselogo;

if($codlingua != "IT"){
     $Query = "SELECT DESCRIZION, NOTE FROM MAGLANG WHERE CODICEARTI = '" . $cCodice ."' AND CODLINGUA = '" . trim($codlingua) . "'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    if($row->DESCRIZION != ""){
        //Almeno se la descrizione è vuota prendo la descrizione passata (che è quella in italiano)
        $cDesc = $row->DESCRIZION;
    }
    if($row->NOTE != ""){
        //Almeno se la descrizione è vuota prendo la descrizione passata (che è quella in italiano)
        $cNote = $row->NOTE;
    }
}

if($pathlogo == ""){
    $pathlogo = $baselogo;
}

//se il logo è personalizzato 
if($pathlogo != $baselogo){
    $pathlogo = str_replace("D:\\ARCA\\ARCA_ITALIA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
}


$fontSize = 9;
$marge    = 2;   // between barcode and hri in pixel
$height   = 25;   // barcode height in 1D ; module size in 2D
$width    = 1;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation

// -------------------------------------------------- //
//            ALLOCATE GD RESSOURCE
// -------------------------------------------------- //

$bcwidth = 880;
$bcheight = 180;

// -------------------------------------------------- //
//            ALLOCATE FPDF RESSOURCE
// -------------------------------------------------- //
//$hostname = gethostname();
$hostname = "http://intranet.krona.it/libs";
$pdf = new eFPDF("L","pt",array(360*72/254, 880*72/254));
$pdf->AddPage();
$pdf->AddFont('Arial','','arial.php');

$x0 = $ex*4*72;
$x = $x0 + 2.9*72;	 // barcode center
$y0 = $ey*360/254*72;
$y = $y0 + 50;	// barcode center



// -------------------------------------------------- //
//                      LOGO
// -------------------------------------------------- //
$pdf->Image($pathlogo, 150, 7 , 100);


// INIZIO A DISEGNARE L?ETICHETTA
///////////////////////////////////////////////////////////

$pdf->SetFont('Arial','B','10');
$pdf->Text(12, 28, "Typ zawiasu:");

$pdf->SetFont('Arial','','9');
$pdf->Text(12, 38,"Zawias bezprzylgowy 3D Elegance");

///

$pdf->SetFont('Arial','B','10');
$txt = iconv("UTF-8", "windows-1257", "Ilosc:");
$pdf->Text(12, 56, $txt);

$pdf->SetFont('Arial','','9');
if ($confez==1) {
	$pdf->Text(65, 56, "1szt");
} else {
	$pdf->Text(65, 56, "2szt");
}


/////

$pdf->SetFont('Arial','B','10');
$pdf->Text(12, 75, "Wykonczenie:");

if (strpos($cCodice, 'BI') !== false) {
    $cNote = "biały BI (White)";
} 
if (strpos($cCodice, 'NS') !== false) {
    $cNote = "nikiel matowy NS (Nickel Mat)";
}
if (strpos($cCodice, 'CL') !== false) {
    $cNote = "chrom połysk CL (Chrom Polish)";
}
if (strpos($cCodice, 'OL') !== false || strpos($cCodice, 'OS') !== false) {
    $cNote = "złoty matowy, połysk OL, OS (Gold Mat, Gold Polish)";
}

$cNote = iconv("UTF-8", "windows-1257", $cNote);
$pdf->SetFont('Arial','','9');
$pdf->Text(18, 85, $cNote);

////////

$pdf->Output(); 
?>
