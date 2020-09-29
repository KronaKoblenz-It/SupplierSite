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
$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$cCodice\""; 
$queryexe = db_query($conn, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($queryexe);
$cDesc = $row->DESCRIZION;

$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 8 AND CODICEARTI = \"$cCodice\""; 
$queryexe = db_query($conn, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($queryexe);
$code = $row->ALIAS;
  
  $fontSize = 12;
  $marge    = 2;   // between barcode and hri in pixel
  $height   = 60;   // barcode height in 1D ; module size in 2D
  $width    = 0.8;    // barcode height in 1D ; not use in 2D
  $angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
  
//  $code     = '123456789012'; // barcode, of course ;)
  $type     = 'ean13';
  $black    = '000000'; // color in hexa
  
  
  // -------------------------------------------------- //
  //            ALLOCATE FPDF RESSOURCE
  // -------------------------------------------------- //
    
  $pdf = new eFPDF("L","pt",array(360*72/254, 880*72/254));
  $pdf->AddPage();
  
  
//  for($ex=0; $ex<2; $ex++) {
    $x0 = $ex*4*72;
	$x = $x0 + 2.9*72;	 // barcode center
//    for($ey=0; $ey<8; $ey++) {
	  $y0 = $ey*360/254*72;
	  $y = $y0 + 50;	// barcode center
	  
	  // altri testi
	  $pdf->SetFont('Arial','B','14');
	  $pdf->Text($x0+15, $y0+19, $cCodice);
	  $pdf->SetFont('Arial','','11');
//	  $pdf->Text($x0+20, $y0+48, $cDesc);
	  $pdf->SetXY($x0+12, $y0+22);
	  $pdf->MultiCell(160,11, $cDesc, 0, 'L');
	  
	  // logo
	  $pdf->Image("$dbase.jpg", $x0+15, $y0+65, 30 );
	  
	  // -------------------------------------------------- //
	  //                      BARCODE
	  // -------------------------------------------------- //
	  
	  $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
	  
	  // -------------------------------------------------- //
	  //                      HRI
	  // -------------------------------------------------- //
	  
	  $pdf->SetFont('Arial','',$fontSize);
	  $pdf->SetTextColor(0, 0, 0);
	  $len = $pdf->GetStringWidth($data['hri']);
	  Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
	  $pdf->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);
 //   }
 // }	
  $pdf->Output();
?>