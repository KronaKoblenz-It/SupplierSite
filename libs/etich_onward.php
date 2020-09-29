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

// -------------------------------------------------- //
//                  PROPERTIES
// -------------------------------------------------- //
//Come prima cosa verifico se il codice articolo esiste o è un codice alternativo
switch($mode) {
	case 'CF':
		$idalias = 7;
		break;
	case 'PZ':
		$idalias = 8;
		break;
	case 'SC':
		$idalias = 6;
		break;
}
$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = $idalias AND CODICEARTI = '$cCodice'";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($queryexe);
$code = $row->ALIAS;


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
if($mode == 'SC' or $mode == 'CF')
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
$hostname = "http://intranet.krona.it/libs";
// if($mode != "CF") {
	// $pdf = new PDF_Rotate("L","pt",array(360*72/254, 880*72/254));
// } else {
	$pdf = new PDF_Rotate("L","pt",array(730*72/254, 1100*72/254));
// }
$pdf->AddPage();

if ($numpack == 0) {
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
if($mode != "CF") {
	$top = 100;
	$left = 58;
	$height = 60;
	$pdf->Image("$hostname/barcodeimage.php?code=0$bc&ext=.png", $left, $top, 140, $height );
	
	//  HRI
	$ean13_1 = substr($bc, 0, 1);
	$ean13_2 = substr($bc, 1, 5);
	$ean13_3 = substr($bc, 6, 5);
	$ean13_4 = substr($bc, 11, 1);
	$pdf->SetFont('Arial','B','7');
	$pdf->SetTextColor(0, 0, 0);
	$len = $pdf->GetStringWidth($bc);

	$pdf->SetFillColor(255,255,255);
	$pdf->Rect($left+18,$top+$height-4,43,8,'F');
	$pdf->Rect($left+83,$top+$height-4,43,8,'F');
	$pdf->Text($left-6, $top+$height+2, $ean13_1);
	$pdf->Text($left+30, $top+$height+2, $ean13_2);
	$pdf->Text($left+95, $top+$height+2, $ean13_3);
	$pdf->Text($left+142, $top+$height+2, $ean13_4);

	$pdf->SetFillColor(0,0,0);
} else {
	$pdf->Image("$hostname/barcodeitf14.php?code=$bc&ext=.png", 50, 127, 211 );	
	$pdf->SetFont('Arial','',$fontSize);
	$pdf->SetTextColor(0, 0, 0);
	$len = $pdf->GetStringWidth($bc);
	$pdf->Text(155-$len/2, 180, $bc);
}



// -------------------------------------------------- //
//                      BARCODE LOTTO
// -------------------------------------------------- //
//if($mode == 'CF') {
	$pdf->RotatedImage("$hostname/barcodelotto.php?code=$cLotto&ext=.png", 295, 195, 130,15, 90 );

	$pdf->SetFont('Arial','B','6');
	$pdf->RotatedText(293, 185, "Lotto/Batch: $cLotto", 90);
//}

// -------------------------------------------------- //
//                      LOGO
// -------------------------------------------------- //
if($mode == 'SC') {
    $pdf->Image($pathlogo, 10, 2 ,120);
}



// -------------------------------------------------- //
//                      QTA x CONF
// -------------------------------------------------- //
if($mode != 'CF') {
/*	$pdf->SetFont('Arial','B','6');
	$pdf->Text(185, 94, 'pz');
	$pdf->SetFont('Arial','B','12');
	$pdf->Text(200, 94, $numpack);	*/
} else {
	$pdf->SetFont('Arial','','12');
	$pdf->Text(15, 40, 'THIS CASE CONTAINS - CET CAISSE CONTIENT');
	$pdf->Text(15, 52, 'ESTE CASO CONTIENE');
	$pdf->SetFont('Arial','B','24');
	$pdf->Text(145, 80, $numpack);	
	$pdf->SetFont('Arial','','10');
	$pdf->Text(140, 93, 'OF - DE');
	
}

// -------------------------------------------------- //
//                DESCRIZIONE ARTICOLO
// -------------------------------------------------- //
if($mode != 'CF') {
	$Query = "SELECT DESCRIZION, NOTE FROM MAGLANG WHERE CODICEARTI='$cCodice' AND CODLINGUA='UK'";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	$row = mysql_fetch_object($queryexe);
	$desc_uk = trim($row->DESCRIZION);
	$note_uk = trim($row->NOTE);
	
	$Query = "SELECT DESCRIZION, NOTE FROM MAGLANG WHERE CODICEARTI='$cCodice' AND CODLINGUA='FR'";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	$row = mysql_fetch_object($queryexe);
	$desc_fr = trim($row->DESCRIZION);
	$note_fr = trim($row->NOTE);
	
	$Query = "SELECT DESCRIZION, NOTE FROM MAGLANG WHERE CODICEARTI='$cCodice' AND CODLINGUA='ES'";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	$row = mysql_fetch_object($queryexe);
	$desc_es = trim($row->DESCRIZION);
	$note_es = trim($row->NOTE);
	
	$pdf->SetFont('Arial','B','8');
	$pdf->Text(81,48,"$desc_uk - $desc_fr - $desc_es");	
	$pdf->SetFont('Arial','B','7');
	$pdf->Text(12,60,$note_uk);	
	$pdf->Text(12,70,$note_fr);	
	$pdf->Text(12,80,$note_es);	

}

// -------------------------------------------------- //
//                CODICE ARTICOLO
// -------------------------------------------------- //
if($mode != 'CF') {
	$pdf->SetFont('Arial','B','14');
	$pdf->Text(12, 48, $cCodice);
} else {
	$pdf->SetFont('Arial','B','24');
	$len = $pdf->GetStringWidth($cCodice);
	$pdf->Text(155-$len/2, 123, $cCodice);	
}

// made in italy
$madeinitaly = "MADE IN ITALY-FABRIQUE EN ITALIE-HECHO EN ITAL";
if($mode == 'CF') {
	$pdf->SetFont('Arial','','8');
	$pdf->Text(15, 194, $madeinitaly);
} else {
	$pdf->SetFont('Arial','B','6');
	$pdf->Text(15, 188, $madeinitaly);
	$pdf->Text(15, 196, "ONWARD KITCHENER, ON, N2C 0A2");
}

$pdf->Output(); 
?>
