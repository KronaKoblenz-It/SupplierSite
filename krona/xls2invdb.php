<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
require_once '../phpexcel/PHPExcel/IOFactory.php';

$connectionstring = db_connect($dbase); 
head();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$magf = "F" . substr($cookie[0],2);
include("inv_common.php");

$nameFile = isset($_GET['file']) ? $_GET['file'] : (isset($_POST['file']) ? $_POST['file'] : '');

if ($nameFile !== ""){
	//print(UPLOAD_DIR.$nameFile);
	$file = UPLOAD_DIR.$nameFile;
} else {
	$file = $_FILES["file"]["tmp_name"];
}

banner("Caricamento inventario$attr da Excel",$cookie[1] . " ($magf)");

$err = false;
/*
if ($_FILES["file"]["error"] > 0) {
   echo "Errore: " . $_FILES["file"]["error"] . "<br>";
   $err = true;
} */
if ($file["error"] > 0) {
   echo "Errore: " . $file["error"] . "<br />";
   $err = true;
}

if($mode=="attr")
	$table = "u_inventa";
else
	$table = "u_invent";

if(!$err) {

	//CANCELLO L'EVENTUALE INVIO PRECEDENTE
	$Query = "DELETE FROM $table WHERE magazzino='$magf' ";
	$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );

	//PROCEDO CON L'INSERIMENTO
	//$objPHPExcel = PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);
	$objPHPExcel = PHPExcel_IOFactory::load($file);
	$worksheet = $objPHPExcel->setActiveSheetIndex(0); 
	$worksheetTitle     = $worksheet->getTitle();
	$highestRow         = $worksheet->getHighestRow(); // e.g. 10
	$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	$nrColumns = ord($highestColumn) - 64;


	if($highestRow < 3) {
		echo "Errore: il file non contiene dati";
		$err = true;
	}

	if($highestColumnIndex < 6) {
		echo "Errore: mancano alcune colonne";
		$err = true;
	}
}

if(!$err) {
		
	for ($row = 3; $row <= $highestRow; ++ $row) {
		$maga = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
		$maga = strtoupper($maga);
		$codice = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
		$codice = strtoupper($codice);
		$quantita = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
		$quantita = (double)$quantita;
		$lotto = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
		$lotto = strtoupper($lotto);
		
		$Query = "INSERT INTO $table (codicearti, quantita, magazzino, lotto) VALUES (";
		$Query .= "'$codice', $quantita, '$maga', '$lotto')";
//		print("$Query<br>");
		$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
	}

}

print("<br>\n");

if($err) {
	print("Sono presenti errori: correggerli e reinviare il file.\n");
} else {
	print("Dati importati.\n Verrà automaticamente reindirizzata alla pagina riepilogativa in 3 sec.");
	header("Refresh: 3; URL=inv-list.php?mode=$mode");
}

print("<br>\n");
goMain();
footer();
?>