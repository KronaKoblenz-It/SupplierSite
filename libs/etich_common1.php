<?php
require('fpdf.php');
include("db-utils.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
$conn = db_connect($dbase); 

$cCodice = $_GET['art'];
$cLotto = $_GET['lotto'];
$cDesc = (isset($_GET['desc']) ? $_GET['desc'] : "");
$code = (isset($_GET['code']) ? trim($_GET['code']) : "");
$cliven = (isset($_GET['cliven']) ? trim($_GET['cliven']) : "");
$mode = (isset($_GET['mode']) ? $_GET['mode'] : "CF");

if(!$ditta){
	$ditta = (isset($_GET['ditta']) ? $_GET['ditta'] : "");
}


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

$Query = "SELECT DESCRIZION, UNMISURA FROM MAGART WHERE CODICE = \"$cCodice\"";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
$cUMPr = $row->UNMISURA;
if("" == $cDesc) {
	$cDesc = $row->DESCRIZION;
}

if($cUMPr == "PC" && $mode == "PZ"){
	$mode = "PC";
}

if("" == $code) {
	if($mode == "PZ" || $mode == "CF" || $mode == "PC") {
		if($cUMPr.trim() == $mode) {
			$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 8 AND CODICEARTI = '$cCodice'";
		} else {
			$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 7 AND CODICEARTI = '$cCodice'";
		}
	} else {
		$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 6 AND CODICEARTI = '$cCodice'";
	}
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($queryexe);
	$code = $row->ALIAS;

	// 28.02.2017 testo se esiste un alias 5 (codice EAN registrato) e nel caso lo utilizzo
	if($cUMPr == $mode) {
		$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 5 AND CODICEARTI = '$cCodice'";
		$queryexe = db_query($conn, $Query) or die(mysql_error()); 
		while($row = mysql_fetch_object($queryexe)) {
			$code = $row->ALIAS;
		}
	}
}

$barcode = "";
$codkrona = "";
$codlingua = "IT";
$pathlogo = $baselogo;

if("" != $cliven && "C" != $cliven){
    $Query = "SELECT U_BARCODE FROM CODALT WHERE CODICEARTI = '$cCodice' AND CODCLIFOR = '$cliven'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $barcode = $row->U_BARCODE;

    $Query = "SELECT U_CODKRONA, LINGUA, U_LOGO FROM ANAGRAFE WHERE CODICE = '$cliven'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $codkrona = $row->U_CODKRONA;
    $codlingua = trim($row->LINGUA);
    $pathlogo = trim($row->U_LOGO);
}
if($codlingua == "IN"){
	$codlingua = "UK";
}

if($codlingua != "IT"){
    $Query = "SELECT DESCRIZION FROM MAGLANG WHERE CODICEARTI = '$cCodice' AND CODLINGUA = '$codlingua'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    if($row->DESCRIZION != ""){
        //Almeno se la descrizione è vuota prendo la descrizione passata (che è quella in italiano)
        $cDesc = $row->DESCRIZION;
    }
}

if($pathlogo == ""){
    $pathlogo = $baselogo;
}

//se il logo è personalizzato 
if($pathlogo != $baselogo){
    $pathlogo = str_replace("D:\\ARCA\\ARCA_FRANCIA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
    $pathlogo = str_replace("D:\\ARCA\\ARCA_SPAGNA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
    $pathlogo = str_replace("D:\\ARCA\\ARCA_ITALIA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
}


$numpack = 0;
$Query = "SELECT QTACONF, U_CE, UNMISURA1, FATT1, UNMISURA2, FATT2, UNMISURA3, FATT3, U_PEFC FROM MAGART WHERE CODICE = '$cCodice'";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
$numpack = 1;
$lCE = $row->U_CE;
$lPEFC = $row->U_PEFC;
$noetichetta = false;
if($mode == 'SC' || $mode == 'CF')
{
	if($row->UNMISURA1 == $mode){
		$numpack = $row->FATT1;
	}
	else if($row->UNMISURA2 == $mode){
		$numpack = $row->FATT2;
	}
	else if($row->UNMISURA3 == $mode){
		$numpack = $row->FATT3;
	}
	else{
		$numpack = 0;
	}
}
if($cUMPr.trim() == "CF" && $mode == 'CF') {
	if("PZ" == $row->UNMISURA1) {
		$numpack = floor(xRound2(1/$row->FATT1));
	} else if("PZ" == $row->UNMISURA2) {
		$numpack = floor(xRound2(1/$row->FATT2));
	} else if("PZ" == $row->UNMISURA3) {
		$numpack = floor(xRound2(1/$row->FATT3));
	} else {
		$numpack = $row->QTACONF;
	}
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

if ($numpack == 0){
	$pdf->SetFont('Arial','B','32');
	$pdf->Text(30, 62, "X");
	$pdf->SetFont('Arial','B','12');
	$pdf->Text(55, 55, "Nessun Etichetta per $mode");
	$pdf->Output();
	return;
}


$x0 = $ex*4*72;
$x = $x0 + 2.9*72;	 // barcode center
$y0 = $ey*360/254*72;
$y = $y0 + 50;	// barcode center

if("" == $barcode){
    //prendo l'alias
    $bc = $code;
}
else{
    //altrimenti prendo il barcode alternativo
	$bc = $barcode;
}
//Creo immagine con il codice a barre
$pdf->Image("$hostname/barcodeimage.php?code=$bc&ext=.png", 15, 7, 100 );


// -------------------------------------------------- //
//                      HRI
// -------------------------------------------------- //

$ean13_1 = substr($bc, 0, 1);
$ean13_2 = substr($bc, 1, 6);
$ean13_3 = substr($bc, 7, 6);
$pdf->SetFont('Arial','',$fontSize);
$pdf->SetTextColor(0, 0, 0);
$len = $pdf->GetStringWidth($bc);

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
$pdf->Image("$hostname/barcodelotto.php?code=$cLotto&ext=.png", 7, 38, 130 );

$pdf->SetFillColor(255,255,255);
$pdf->Rect(0,31,200,8, 'F');
$pdf->Rect(0,51,200,25, 'F');

$pdf->SetFont('Arial','B','6');
$pdf->Text(13, 57, "Lotto: $cLotto");
// INSERISCO DATA DI PRODUZIONE
$pdf->Text(100, 57, "$dataStampa");

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
	if($ditta == "FR"){
		$pdf->SetFont('Arial','B','6');
		$pdf->Text(20,78,"100% PEFC Certified-ICILA-PEFC COC-002766");
		$pdf->Image("../img/loghi/LOGO-PEFC-FR.JPG", 135, 2, 49 );
	} else {
		$pdf->SetFont('Arial','B','6');
		$pdf->Text(20,78,"100% PEFC Certified-ICILA-PEFC COC-002700");
		$pdf->Image("../img/loghi/LOGO-PEFC.JPG", 135, 2, 49 );
	}
}

// -------------------------------------------------- //
//                      LOGO PACKAGE
// -------------------------------------------------- //
$pdf->SetFont('Arial','B','6');
if($mode == 'CF'){
	$pdf->Text(155, 94, 'CF');
}
if($mode == 'SC'){
	$pdf->Text(155, 94, 'SC');
}

$pdf->Image("../img/loghi/emblem_package.jpg", 170, 84, 10 );

// -------------------------------------------------- //
//                      QTA x CONF
// -------------------------------------------------- //
$pdf->SetFont('Arial','B','6');
$pdf->Text(185, 94, 'pz');
$pdf->SetFont('Arial','B','12');
$pdf->Text(200, 94, $numpack);

// -------------------------------------------------- //
//                DESCRIZIONE ARTICOLO
// -------------------------------------------------- //

$pdf->SetFont('Arial','','8');
$pdf->Text(9,70,$cDesc);

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

$pdf->Output(); 
?>
