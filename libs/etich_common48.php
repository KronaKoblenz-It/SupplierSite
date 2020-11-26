<?php
require('fpdf.php');
include("db-utils.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
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
//Come prima cosa verifico se il codice articolo esiste o è un codice alternativo
$cCodiceAlt = "";
if($cCodice != ""){
    $Query = "SELECT CODICE FROM MAGART WHERE CODICE = \"$cCodice\"";
    $queryexe = db_query($conn, $Query);
    $row = mysql_fetch_object($queryexe);
    if($row->CODICE == ""){
        //Vuol dire che il codice è un codice alternativo
        $Query = "SELECT CODICEARTI FROM CODALT WHERE CODARTFOR = '$cCodice' AND CODCLIFOR = '$cliven'";
        $queryexe = db_query($conn, $Query);
        $row = mysql_fetch_object($queryexe);
        $cCodiceAlt = $cCodice;
        $cCodice = $row->CODICEARTI;
    }
}

$Query = "SELECT DESCRIZION, UNMISURA FROM MAGART WHERE CODICE = '$cCodice'";
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
$ragsoc = "KRONA KOBLENZ spa";
$indirizzo = "v. Piane 90";
$localita = "47853 CORIANO (RN)";
$nazione = "IT - Italia";

if("" != $cliven && "C" != $cliven){
    $Query = "SELECT U_BARCODE FROM CODALT WHERE CODICEARTI = '$cCodice' AND CODCLIFOR = '$cliven'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $barcode = $row->U_BARCODE;

    $Query = <<<EOT
SELECT ANAGRAFE.U_CODKRONA, ANAGRAFE.LINGUA, ANAGRAFE.U_LOGO, ANAGRAFE.INDIRIZZO, ANAGRAFE.LOCALITA, 
ANAGRAFE.CAP, ANAGRAFE.CODNAZIONE, ANAGRAFE.DESCRIZION AS RAGSOC, ANAGRAFE.SUPRAGSOC,
NAZIONI.CODICEISO, NAZIONI.DESCRIZION
FROM ANAGRAFE 
LEFT OUTER JOIN NAZIONI ON NAZIONI.CODICE = ANAGRAFE.CODNAZIONE
WHERE ANAGRAFE.CODICE = '$cliven'
EOT;
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
	$ragsoc = trim(trim($row->RAGSOC) . " " . $row->SUPRAGSOC);
    $codkrona = $row->U_CODKRONA;
    $codlingua = trim($row->LINGUA);
    $pathlogo = trim($row->U_LOGO);
	$indirizzo = trim($row->INDIRIZZO);
	$localita = trim($row->CAP)." ".trim($row->LOCALITA);
	$codnazione = trim($row->CODNAZIONE);
	if($codnazione != "I" && $codnazione != "RSM") {
		$localita = "$codnazione-$localita";
	}
	$nazione = trim($row->CODICEISO) . " - " . trim($row->DESCRIZION);
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

// -----------------------------
// Appendo sempre una descrizione in inglese a meno che questa non sia la lingua principale
// -----------------------------
$cDesc_en = "";
if($codlingua != "UK") {
	$Query = "SELECT DESCRIZION FROM MAGLANG WHERE CODICEARTI = '$cCodice' AND CODLINGUA = 'UK'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $cDesc_en = trim($row->DESCRIZION);
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


// -------------------------------------------------- //
//            IMMAGINI HAFELE
// -------------------------------------------------- //
$img_hafele="";
if($cliven == "C04173") {
	$Query = "SELECT Disegno FROM U_HAFETI WHERE Codice = '$cCodice'";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	$row = mysql_fetch_object($queryexe);
	if($row->Disegno != ""){
        //Almeno se la descrizione è vuota prendo la descrizione passata (che è quella in italiano)
        $img_hafele = "../img/hafele/" . trim($row->Disegno) . ".JPG";
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
$pdf = new eFPDF("L","pt",array(480*72/254, 890*72/254));
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
//                      IMMAGINE HAFELE
// -------------------------------------------------- //
if($img_hafele != "") {
	$pdf->Image($img_hafele, 153, 8, 95 );
}
// -------------------------------------------------- //
//                      COD KRONA
// -------------------------------------------------- //
// Roberto 26.05.2020 Soppresso per spostare logo CE
// if($codkrona!='' || $codForn!=''){
	// $pdf->SetFont('Arial','B','6');
	// $pdf->Text(123, 11, "For.:");
	// $pdf->Text(123, 17, $codkrona);
	// $pdf->Text(123, 24, $codForn);
// }
 
// -------------------------------------------------- //
//                      LOGO E INDIRIZZO
// -------------------------------------------------- //
if($pathlogo == "../img/loghi/LOGO-KRONA.JPG"){
    $pdf->Image($pathlogo, 9, 98, 35 );
	$pdf->SetFont('Arial','B','6');
	$pdf->Text(50,104,$ragsoc);
	$pdf->Text(50,111,$indirizzo);
	$pdf->Text(50,118,$localita);
	$pdf->Text(50,125,$nazione);
}
else{
    $pdf->Image($pathlogo, 9, 98 ,55);
	$pdf->SetFont('Arial','B','6');
	$pdf->Text(75,104,$ragsoc);
	$pdf->Text(75,111,$indirizzo);
	$pdf->Text(75,118,$localita);
	$pdf->Text(75,125,$nazione);
}

// -------------------------------------------------- //
//                      LOGO CE
// -------------------------------------------------- //
if($lCE == true) {
// Roberto 26.05.2020 spostato in alto per allargare immagine	
//	$pdf->Image("../img/loghi/Ce-logo.jpg", 170, 100, 24 );
$pdf->Image("../img/loghi/Ce-logo.jpg", 125, 10, 20 );
}

// -------------------------------------------------- //
//                      LOGO PEFC
// -------------------------------------------------- //
if($lPEFC == true) {
	$pdf->Image("../img/loghi/LOGO-PEFC.JPG", 200, 71, 45 );
	$pdf->SetFont('Arial','B','6');
	$pdf->Text(170,125,"100% PEFC Certified");
	$pdf->Text(170,130,"ICILA-PEFC COC-002700");
}

// -------------------------------------------------- //
//                      LOGO PACKAGE
// -------------------------------------------------- //
$left = 102;
$pdf->SetFont('Arial','B','6');
if($mode == 'CF'){
	$pdf->Text($left, 94, 'CF');
}
if($mode == 'SC'){
	$pdf->Text($left, 94, 'SC');
}

$pdf->Image("../img/loghi/emblem_package.jpg", $left+12, 84, 10 );

// -------------------------------------------------- //
//                      QTA x CONF
// -------------------------------------------------- //
$pdf->SetFont('Arial','B','6');
$pdf->Text($left+25, 94, 'pz');
$pdf->SetFont('Arial','B','12');
$pdf->Text($left+33, 94, $numpack);


// -------------------------------------------------- //
//                DESCRIZIONE ARTICOLO
// -------------------------------------------------- //

$pdf->SetFont('Arial','','8');
$pdf->Text(9,68,trim($cDesc));
$pdf->Text(9,79,trim($cDesc_en));

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
