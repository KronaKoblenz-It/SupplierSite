<?php
include('../libs/Barcode.php');
require('../libs/fpdf.php');
include("header.php");
include("db-utils.php");

/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
$conn = db_connect($dbase);

$cCodice = $_GET['art'];
$cLotto = $_GET['lotto'];
$cDesc = (isset($_GET['desc']) ? $_GET['desc'] : "");
$code = (isset($_GET['code']) ? $_GET['code'] : "");
$cliven = (isset($_GET['cliven']) ? $_GET['cliven'] : "");
$clidest = (isset($_GET['clidest']) ? $_GET['clidest'] : "");
$dataEvas = (isset($_GET['devas']) ? $_GET['devas'] : "");

$dataEvas = date("F Y", strtotime($dataEvas));

$mesi = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");

// -------------------------------------------------- //
//                      USEFUL
// -------------------------------------------------- //
class eFPDF extends FPDF{
  function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0) {
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

$Query = "SELECT CODARTFOR FROM CODALT WHERE CODICEARTI = \"$cCodice\" AND CODCLIFOR = \"$cliven\"";
$queryexe = db_query($conn, $Query);
$row = mysql_fetch_object($queryexe);
if($row->CODARTFOR != ""){
	$cCodiceR = $row->CODARTFOR;
} else {
	$cCodiceR = $cCodice;
}

$Query = "SELECT DESCRIZION, NOTE, QTACONF, UNMISURA FROM MAGART WHERE CODICE = \"$cCodice\"";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
if("" == $cDesc) {
	$cDesc = $row->DESCRIZION;
}
$cNote = $row->NOTE;
$nQtaconf = ($row->QTACONF == 0 ? 1 : $row->QTACONF);
$cUM = ($row->UNMISURA == "PZ" ? "Pc" : ($row->UNMISURA == "CF" ? "Set" : $row->UNMISURA) );

if("" == $code) {
	$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 7 AND CODICEARTI = \"$cCodice\"";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	$row = mysql_fetch_object($queryexe);
	$code = $row->ALIAS;
}

$barcode = "";
$codkrona = "";
$codlingua = "IT";
$pathlogo = "../img/loghi/LOGO-KRONA.JPG";
$rupiaimg = "../img/rupia.png";

if("" != $cliven){
  $Query = "SELECT U_BARCODE FROM CODALT WHERE CODICEARTI = '" . $cCodice . "' AND CODCLIFOR = '" . $cliven . "'";
  $queryexe = db_query($conn, $Query) or die(mysql_error());
  $row = mysql_fetch_object($queryexe);
  $barcode = $row->U_BARCODE;

  $Query = "SELECT DESCRIZION, U_CODKRONA, LINGUA, U_LOGO FROM ANAGRAFE WHERE CODICE = '" . $cliven . "'";
  $queryexe = db_query($conn, $Query) or die(mysql_error());
  $row = mysql_fetch_object($queryexe);
  $codkrona = $row->U_CODKRONA;
  $codlingua = $row->LINGUA;
  $pathlogo = $row->U_LOGO;
  $importatore = $row->DESCRIZION;
}


if(trim($codlingua) != "IT"){
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

//Sostituisco <br> con \n
$cNotes = explode("<br>", $cNote);
$nNotes = count($cNotes);

//$pathlogo = "D:\\Arca\\Arca_Italia\\loghi_sito\\logo-krona.jpg";
if($pathlogo == ""){
    $pathlogo = "../img/loghi/LOGO-KRONA.JPG";
}
if($pathlogo != "../img/loghi/LOGO-KRONA.JPG"){
    $pathlogo = str_replace("D:\\ARCA\\ARCA_ITALIA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
}

$numpack = 0;
$Query = "SELECT QTACONF, U_CE, UNMISURA1, FATT1, UNMISURA2, FATT2, UNMISURA3, FATT3, U_PEFC FROM MAGART WHERE CODICE = '" . $cCodice . "'";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
if($cUM.trim() == "Set") {
  $numpack = 0;
	/*if("PZ" == $row->UNMISURA1) {
		$numpack = floor(1/$row->FATT1);
	} else if("PZ" == $row->UNMISURA2) {
		$numpack = floor(1/$row->FATT2);
	} else if("PZ" == $row->UNMISURA3) {
		$numpack = floor(1/$row->FATT3);
	} else {
		$numpack = $row->QTACONF;
	}*/
} elseif ($cUM.trim() == "Pc") {
  $numpack = 1;
	// if("CF" == $row->UNMISURA1) {
	// 	$numpack = $row->FATT1;
	// } else if("CF" == $row->UNMISURA2) {
	// $numpack = $row->FATT2;
	// } else if("CF" == $row->UNMISURA3) {
	// 	$numpack = $row->FATT3;
	// } else {
	// 	$numpack = $row->QTACONF;
	// }
}
$lCE = $row->U_CE;

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
$pdf = new eFPDF("L","pt",array(730*72/254, 1100*72/254));
$pdf->AddPage();

if($numpack != 0){
  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 20, "Imported by:");
  $pdf->SetFont('Arial','B','9');
  $pdf->Text(55, 20, $importatore);

  $Query = "SELECT RAGIONESOC, SUPPRAGSOC, INDIRIZZO, U_IND2, LOCALITA, PERSONARIF, FAX ";
  $Query .= "FROM DESTINAZ WHERE CODICEDES = \"$clidest\" AND CODICECF = \"$cliven\"";
  $queryexe = db_query($conn, $Query) or die(mysql_error());
  $row = mysql_fetch_object($queryexe);
  $pdf->SetFont('Arial','','9');
  $pdf->Text(55, 35, trim($row->RAGIONESOC) . " " . trim($row->SUPPRAGSOC));
  $pdf->Text(55, 50, $row->INDIRIZZO);
  $pdf->Text(55, 65, $row->U_IND2);
  $pdf->Text(55, 80, trim($row->LOCALITA) . " - INDIA");

  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 95, "Email:");
  $pdf->SetFont('Arial','B','9');
  $pdf->Text(55, 95, $row->PERSONARIF);

  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 110, "Customer care:");
  $pdf->SetFont('Arial','B','9');
  $pdf->Text(55, 110, $row->FAX);

  $pdf->SetFont('Arial','B','6');
  $pdf->Text(160, 110, "Mfg. Date:");
  $pdf->SetFont('Arial','B','9');
  //$pdf->Text(192, 110, date("F") . " " . date("Y"));
  $pdf->Text(192, 110, $dataEvas);

  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 130, "Product:");
  $pdf->SetFont('Arial','B','9');
  $pdf->Text(55, 130, $cDesc);

  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 145, "Article No:");
  $pdf->SetFont('Arial','B','9');
  if ($cCodiceAlt == ""){
  	$pdf->Text(55, 145, $cCodiceR);
  } else {
  	$pdf->Text(55, 145, $cCodiceAlt);
  }

  $pdf->SetFont('Arial','B','6');
  $pdf->Text(160, 145, "Quantity:");
  $pdf->SetFont('Arial','B','9');
  $pdf->Text(200, 145, $numpack . " " . $cUM);

  $Query = "SELECT PREZZO FROM U_LISTIN WHERE CODICEARTI = \"$cCodice\" AND CODCLIFOR = \"$cliven\"";
  $queryexe = db_query($conn, $Query) or die(mysql_error());
  $row = mysql_fetch_object($queryexe);
  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 190, "MRP inc. of all taxes:");
  $pdf->SetFont('Arial','B','9');
  $prezzo_str = str_replace(".", ",", (string)$row->PREZZO);
  $prezzo_str = strrpos($prezzo_str, ",")==0 ? $prezzo_str.",00" : $prezzo_str;
  $prezzo_str = strlen($prezzo_str)-strrpos($prezzo_str, ",")<3 ? $prezzo_str."0" : $prezzo_str;	
  $pdf->Image($rupiaimg, 75, 183, 5);
//  $pdf->Text(75, 190, "RS " . $prezzo_str );
  $pdf->Text(90, 190, $prezzo_str );
  if($numpack!=1){
    $prezzo_val = $row->PREZZO*$numpack;
    $prezzo_str = str_replace(".", ",", (string)$prezzo_val);
    $prezzo_str = strrpos($prezzo_str, ",")==0 ? $prezzo_str.",00" : $prezzo_str;
    $prezzo_str = strlen($prezzo_str)-strrpos($prezzo_str, ",")<3 ? $prezzo_str."0" : $prezzo_str;	
//    $pdf->Text(175, 190, "x " . $numpack . " " . $cUM . " RS " . $prezzo_str );
	$pdf->Text(175, 190, "x " . $numpack . " " . $cUM );
	$pdf->Image($rupiaimg, 220, 183, 5);
	$pdf->Text(235, 190, $prezzo_str );
  }
  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 200, "Country of origin: Italy");

  
  $pdf->SetFont('Arial','B','6');
  $pdf->Text(10, 160, "Description:");
  $pdf->SetFont('Arial','','6');
  //$pdf->Text(55, 160, $cNote);
  $pdf->SetXY(50, 150);
  $pdf->SetAutoPageBreak(false,0);
  if ($nNotes > 1){
  	$dist=160;
  	for ($i=0; $i<=$nNotes; $i++){
  		$pdf->Text(55, $dist, $cNotes[$i]);
  		$dist += 9;
  	}
  }
  else {
  	$pdf->MultiCell(250,16, $cNote);
  }
} else {
  	$pdf->SetFont('Arial','B','32');
  	$pdf->Text(30, 62, "X");
  	$pdf->SetFont('Arial','B','12');
  	$pdf->Text(55, 55, "Nessun Etichetta per SC");
}

$pdf->Output();

?>
