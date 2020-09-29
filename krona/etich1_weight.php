<?php
include('../libs/Barcode.php');
require('../libs/fpdf.php');
include("header.php");
include("db-utils.php");

/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
$conn = db_connect($dbase); 

$cCodice = $_GET['art'];
$cDesc = (isset($_GET['desc']) ? $_GET['desc'] : "");
$code = (isset($_GET['code']) ? $_GET['code'] : "");
  
  // -------------------------------------------------- //
  //                      USEFUL
  // -------------------------------------------------- //
  
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
if("" == $cDesc) {  
	$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$cCodice\""; 
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($queryexe);
	$cDesc = $row->DESCRIZION;
}

if("" == $code) {
	$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 8 AND CODICEARTI = \"$cCodice\""; 
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($queryexe);
	$code = $row->ALIAS;
}
  
  $fontSize = 10;
  $marge    = 2;   // between barcode and hri in pixel
  $height   = 20;   // barcode height in 1D ; module size in 2D
  $width    = 0.95;    // barcode height in 1D ; not use in 2D
  $angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
  
//  $code     = '123456789012'; // barcode, of course ;)
  $type     = 'code39';
  $black    = '000000'; // color in hexa
  
  
  // -------------------------------------------------- //
  //            ALLOCATE FPDF RESSOURCE
  // -------------------------------------------------- //

  $mid_x = 550*72/254;
  $pdf = new eFPDF("P","pt",array(1100*72/254, 730*72/254));
  $pdf->AddPage();

  // altri testi
  $text = "BEWARE!";
  $pdf->SetFont('Arial','B','36');
  $pdf->SetX($pdf->lMargin);
  $pdf->SetY(250*72/254);
  $pdf->Cell(0,0,$text,0,0,'C');

  $pdf->SetFont('Arial', 'B', '24');
  $pdf->SetX($pdf->lMargin);
  $pdf->SetY(440*72/254);
  $pdf->Cell(0, 0, "Weight over", 0, 0, 'C');

  $pdf->SetX($pdf->lMargin);
  $pdf->SetY(560*72/254);
  $pdf->Cell(0, 0, "15", 0, 0, 'C');

  $pdf->SetX($pdf->lMargin);
  $pdf->SetY(680*72/254);
  $pdf->Cell(0, 0, "KGs", 0, 0, 'C');

  $pdf->SetX($pdf->lMargin);
  $pdf->SetY(0);
  $pdf->SetLineWidth(10*72/254);
  $pdf->Rect(30*72/254, 350*72/254, 670*72/254, 400*72/254);

  $pdf->Line(30*72/254, 780*72/254, 700*72/254, 780*72/254);

  $pdf->Output();
?>