<?php
include('../libs/Barcode.php');
require('../libs/fpdf.php');
include("header.php");
include("db-utils.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
$conn = db_connect($dbase); 

$cCodice = $_GET['art'];
$cLotto = $_GET['lotto'];
$cDesc = (isset($_GET['desc']) ? $_GET['desc'] : "");
$code = (isset($_GET['code']) ? $_GET['code'] : "");
$cliven = (isset($_GET['cliven']) ? $_GET['cliven'] : "");

$dataStampa = date("d/m/Y", time());
$codForn = $cookie[0];
  
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
//Come prima cosa verifico se il codice articolo esiste o è un codice alternativo
$cCodiceAlt = "";
if($cCodice != ""){
    $Query = "SELECT CODICE FROM MAGART WHERE CODICE = \"$cCodice\"";
    $queryexe = db_query($conn, $Query);
    $row = mysql_fetch_object($queryexe);
    if($row->CODICE == ""){
        //Vuol dire che il codice è un codice alternativo
        $Query = "SELECT CODICEARTI FROM CODALT WHERE CODARTFOR = \"$cCodice\" AND CODCLIFOR = \"$cliven\"";
        $queryexe = db_query($conn, $Query);
        $row = mysql_fetch_object($queryexe);
        $cCodiceAlt = $cCodice;
        $cCodice = $row->CODICEARTI;
    }
}

if("" == $code) {
    $Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 8 AND CODICEARTI = \"$cCodice\"";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $code = $row->ALIAS;
}

$barcode = "";
$codkrona = "";
$codlingua = "IT";
$pathlogo = "../img/loghi/LOGO-KRONA.JPG";

if("" != $cliven){
    $Query = "SELECT U_BARCODE FROM CODALT WHERE CODICEARTI = '" . $cCodice . "' AND CODCLIFOR = '" . $cliven . "'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $barcode = $row->U_BARCODE;

    $Query = "SELECT U_CODKRONA, LINGUA, U_LOGO FROM ANAGRAFE WHERE CODICE = '" . $cliven . "'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $codkrona = $row->U_CODKRONA;
    $codlingua = $row->LINGUA;
    $pathlogo = $row->U_LOGO;
}

//Adesso vado a pescare i dati del 2° collo
$cDescExtra = "";
if($cCodice!=""){
    $Query = "SELECT MAGART.U_ARTCOLLO, MAGART.U_DESCOLLO as DESCOLLOEXTRA, MAGART.U_NCOLLI, COLLO.CODICE, COLLO.DESCRIZION FROM MAGART LEFT JOIN MAGART as COLLO ON MAGART.U_ARTCOLLO = COLLO.CODICE WHERE MAGART.CODICE = \"$cCodice\"";
    $queryexe = db_query($conn, $Query);
    $row = mysql_fetch_object($queryexe);
    if($row->CODICE != ""){
        //Inserisco i dati del 2° collo
        $cCodice = $row->CODICE;
        $cDescExtra = trim($row->DESCOLLOEXTRA);
    }
}

//Descrizione 2° collo
$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$cCodice\"";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
$cDesc = $row->DESCRIZION;

if(trim($codlingua) != "IT"){
    $Query = "SELECT DESCRIZION FROM MAGLANG WHERE CODICEARTI = '" . $cCodice ."' AND CODLINGUA = '" . trim($codlingua) . "'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    if($row->DESCRIZION != ""){
        //Almeno se la descrizione è vuota prendo la descrizione passata (che è quella in italiano)
        $cDesc = $row->DESCRIZION;
    }
}

//$pathlogo = "D:\\Arca\\Arca_Italia\\loghi_sito\\logo-krona.jpg";
if($pathlogo == ""){
    $pathlogo = "../img/loghi/LOGO-KRONA.JPG";
}
if($pathlogo != "../img/loghi/LOGO-KRONA.JPG"){
    $pathlogo = str_replace("D:\\ARCA\\ARCA_ITALIA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
}

$numpack = 0;
$Query = "SELECT QTACONF, U_CE, U_PEFC FROM MAGART WHERE CODICE = '" . $cCodice . "'";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
$numpack = 1;
$lCE = $row->U_CE;
$lPEFC = $row->U_PEFC;


$fontSize = 9;
$marge    = 2;   // between barcode and hri in pixel
$height   = 25;   // barcode height in 1D ; module size in 2D
$width    = 1;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
  
//  $code     = '123456789012'; // barcode, of course ;)
$type     = 'ean13';
$black    = '000000'; // color in hexa

// -------------------------------------------------- //
//            ALLOCATE GD RESSOURCE
// -------------------------------------------------- //

$bcwidth = 880;
$bcheight = 180;
$im     = imagecreatetruecolor($bcwidth, $bcheight);
$black  = ImageColorAllocate($im,0x00,0x00,0x00);
$white  = ImageColorAllocate($im,0xff,0xff,0xff);
$red    = ImageColorAllocate($im,0xff,0x00,0x00);
$blue   = ImageColorAllocate($im,0x00,0x00,0xff);
imagefilledrectangle($im, 0, 0, $bcwidth, $bcheight, $white);
  
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
	  

	  // logo
//	  $pdf->Image("$dbase.jpg", $x0+15, $y0+65, 30 );
	  
// -------------------------------------------------- //
//                      BARCODE
// -------------------------------------------------- //
//Creo il codice a barre in una immagine e poi lo appiccico nell'etichetta
//      if("" == $barcode){
//          //prendo l'alias
//          $data = Barcode::fpdf($pdf, $black, 56, 17, $angle, $type, array('code'=>$code), $width, $height);
//      }
//      else{
//          //altrimenti prendo il barcode alternativo
//          $data = Barcode::fpdf($pdf, $black, 56, 17, $angle, $type, array('code'=>$barcode), $width, $height);
//      }
//
//      $pdf->SetFillColor(255,255,255);
//      $pdf->Rect(0,0,120,8, 'F');
//      $pdf->Rect(0,25,120,5,'F');
//      $pdf->Rect(12,21,41,5,'F');
//      $pdf->Rect(58,21,41,5,'F');
if("" == $barcode){
    //prendo l'alias
    $data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$code), 9, $bcheight);
}
else{
    //altrimenti prendo il barcode alternativo
    $data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$barcode), 9, $bcheight);
}
//Creo immagine con il codice a barre
imagejpeg($im, "etichetta.jpg");
imagedestroy($im);

$pdf->Image("etichetta.jpg", 15, 7, 100 );


// -------------------------------------------------- //
//                      HRI
// -------------------------------------------------- //

$ean13_1 = substr($data['hri'], 0, 1);
$ean13_2 = substr($data['hri'], 1, 6);
$ean13_3 = substr($data['hri'], 7, 6);
$pdf->SetFont('Arial','',$fontSize);
$pdf->SetTextColor(0, 0, 0);
$len = $pdf->GetStringWidth($data['hri']);
//Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
//	  $pdf->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);

$pdf->SetFillColor(255,255,255);
$pdf->Rect(20,22,43,8,'F');
$pdf->Rect(67,22,43,8,'F');
$pdf->Text(10, 30, $ean13_1);
$pdf->Text(27, 30, $ean13_2);
$pdf->Text(73, 30, $ean13_3);

$pdf->SetFillColor(0,0,0);

// -------------------------------------------------- //
//                      BARCODE LOTTO
// -------------------------------------------------- //
// -------------------------------------------------- //
//            ALLOCATE GD RESSOURCE
//// -------------------------------------------------- //
$bclottolen = strlen($cLotto);
$charwidth = 55;
$bclottowidth = $bclottolen * $charwidth + 120;
$bclottoheight = 60;
$im     = imagecreatetruecolor($bclottowidth, $bclottoheight);
$black  = ImageColorAllocate($im,0x00,0x00,0x00);
$white  = ImageColorAllocate($im,0xff,0xff,0xff);
$red    = ImageColorAllocate($im,0xff,0x00,0x00);
$blue   = ImageColorAllocate($im,0x00,0x00,0xff);
imagefilledrectangle($im, 0, 0, $bclottowidth, $bclottoheight, $white);

if("" != $cLotto){
    $data = Barcode::gd($im, $black, $bclottowidth/2, $bclottoheight/2, $angle, 'code39', array('code'=>$cLotto), 4, $bclottoheight);

}

//Creo immagine con il codice a barre
imagejpeg($im, "etichettalotto.jpg");
imagedestroy($im);

$pdf->Image("etichettalotto.jpg", 12, 38, 130 );
$pdf->SetFillColor(255,255,255);
$pdf->Rect(0,31,200,8, 'F');
$pdf->Rect(0,51,200,25, 'F');

$pdf->SetFont('Arial','B','6');
$pdf->Text(13, 57, "Lotto: $cLotto");
// INSERISCO DATA DI PRODUZIONE
$pdf->Text(100, 57, "$dataStampa");
//    $pdf->Rect(0,25,100,5,'F');
//    $pdf->Rect(14,21,32,5,'F');
//    $pdf->Rect(49,21,32,5,'F');
	  
// -------------------------------------------------- //
//                      LOTTO
// -------------------------------------------------- //

//  	  $pdf->SetFont('Arial','B','6');
//	  $pdf->Text(105, 13, "Lotto: $cLotto");
//      $data = Barcode::fpdf($pdf, $black, $x0+190, $y0+85, $angle, 'code39', array('code'=>$cLotto), $width, 20);

// -------------------------------------------------- //
//                      COD KRONA
// -------------------------------------------------- //
if($codkrona!='' || $codForn!=''){
	$pdf->SetFont('Arial','B','6');
	$pdf->Text(125, 22, "For.: $codkrona");
	$pdf->Text(125, 28, "$codForn");
}
// -------------------------------------------------- //
//                      LOGO
// -------------------------------------------------- //
if($pathlogo == "../img/loghi/LOGO-KRONA.jpg"){
    $pdf->Image($pathlogo, 205, 7, 35 );
}
else{
    $pdf->Image($pathlogo, 185, 7 ,55);
}

// -------------------------------------------------- //
//                      LOGO CE
// -------------------------------------------------- //
if($lCE == true) {
	$pdf->Image("../img/loghi/Ce-logo.jpg", 220, 64, 24 );
}
// -------------------------------------------------- //
//                      LOGO PEFC
// -------------------------------------------------- //
if($lPEFC == true) {
	$pdf->SetFont('Arial','B','8');
	$pdf->Text(20,78,"100% PEFC Certified-ICILA-PEFC COC-002700");
	$pdf->Image("../img/loghi/LOGO-PEFC.JPG", 135, 2, 49 );
}

// -------------------------------------------------- //
//                      LOGO PACKAGE
// -------------------------------------------------- //
$pdf->Image("../img/loghi/emblem_package.jpg", 170, 84, 10 );

// -------------------------------------------------- //
//                      QTA x CONF
// -------------------------------------------------- //
$pdf->SetFont('Arial','B','6');
$pdf->Text(185, 94, 'CF');
$pdf->SetFont('Arial','B','12');
//$pdf->Text(200, 94, $numpack);
$pdf->Text(200, 94, 1);

// -------------------------------------------------- //
//                DESCRIZIONE ARTICOLO
// -------------------------------------------------- //

$pdf->SetFont('Arial','','8');
//$pdf->Text(9,70,$cDesc);
$pdf->Text(9,74,$cDescExtra);
//      $pdf->SetXY(0, 60);
//      $pdf->MultiCell(160,2, $cDesc, 0, 'L');

// -------------------------------------------------- //
//                CODICE ARTICOLO
// -------------------------------------------------- //
$pdf->SetFont('Arial','B','6');
$pdf->Text(9, 94, "Art.");
$pdf->SetFont('Arial','B','12');
if($cCodiceAlt == ""){
    $pdf->Text(25, 94, $cCodice);
}
else{
    $pdf->Text(25, 94, $cCodiceAlt);
}

 //   }
 // }	
$pdf->Output();
?>