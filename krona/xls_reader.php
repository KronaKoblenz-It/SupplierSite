<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");
require_once '../phpexcel/PHPExcel/IOFactory.php';

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);

$conn = db_connect($dbase); 
$anno = current_year();

head();
banner("Importazione file Excel");

$err = false;
	if ($_FILES["file"]["error"] > 0) {
	   echo "Errore: " . $_FILES["file"]["error"] . "<br />";
	   $err = true;
	} else {
//	   echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//	   echo "Type: " . $_FILES["file"]["type"] . "<br />";
//	   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//	   echo "Stored in: " . $_FILES["file"]["tmp_name"];
	}

if(!$err) {
//$file = $_FILES["file"]["tmp_name"];
//$contents = file($file); 
//$string = implode($contents); 


	$objPHPExcel = PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		$worksheetTitle     = $worksheet->getTitle();
		$highestRow         = $worksheet->getHighestRow(); // e.g. 10
		$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$nrColumns = ord($highestColumn) - 64;
		echo "<br>\n<table class=\"list\">\n";
		$row = 1;
		echo "<tr class=\"list\">\n";
		for ($col = 0; $col < $highestColumnIndex; ++ $col) {
			$cell = $worksheet->getCellByColumnAndRow($col, $row);
			$val = $cell->getValue();
			echo "<th class=\"list\">$val</th>\n";
		}
		echo "</tr>\n";
		for ($row = 2; $row <= $highestRow; ++ $row) {
			echo "<tr class=\"list\">\n";
			for ($col = 0; $col < $highestColumnIndex; ++ $col) {
				$cell = $worksheet->getCellByColumnAndRow($col, $row);
				$val = $cell->getValue();
				echo "<td class=\"list\">$val</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

}
if($err) {
	print("<br>File non caricato.\n");
} else {
	print("<br>File caricato.\n");
}
print("<br>\n<br>\n");
print("<a href=\"ddtimportxls.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo caricamento\">Nuovo caricamento</a>\n");


print("<br>\n");
goMain();
footer();



?>