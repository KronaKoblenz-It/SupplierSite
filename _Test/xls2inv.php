<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
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

banner("Caricamento inventario da Excel",$cookie[1] . " ($magf)");

$err = false;
if ($_FILES["file"]["error"] > 0) {
   echo "Errore: " . $_FILES["file"]["error"] . "<br>";
   $err = true;
} 

if(!$err) {
	$objPHPExcel = PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);
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
	echo "<br>\n<table class=\"list\">\n";
	$row = 2;
	echo "<tr class=\"list\">\n";
	for ($col = 0; $col < 6; ++ $col) {
		$cell = $worksheet->getCellByColumnAndRow($col, $row);
		$val = $cell->getValue();
		echo "<th class=\"list\">$val</th>\n";
	}
	echo "</tr>\n";
	for ($row = 3; $row <= $highestRow; ++ $row) {
		echo "<tr class=\"list\">\n";
//		for ($col = 0; $col < $highestColumnIndex; ++ $col) {
//			$cell = $worksheet->getCellByColumnAndRow($col, $row);
//			$val = $cell->getValue();
//			echo "<td class=\"list\">$val</td>\n";
//		}

		// codice magazzino
		$val = trim($worksheet->getCellByColumnAndRow(0, $row)->getValue());
		if($val == $magf) {
			echo "<td class=\"list\">$val</td>\n";
		} else {
			echo "<td class=\"error\">$val<br>Mag. non valido</td>\n";
			$err = true;
		}

		// codice articolo
		$val = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
		$Query = "SELECT DESCRIZION, LOTTI, UNMISURA FROM MAGART WHERE CODICE = \"$val\"";
		$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		if($rs = db_fetch_row($queryexe)) {
			echo "<td class=\"list\">$val</td>\n";
		} else {
			echo "<td class=\"error\">$val<br>Cod. non valido</td>\n";
			$err = true;
		}
		
		// descrizione
		echo "<td class=\"list\">" . $worksheet->getCellByColumnAndRow(2, $row)->getValue() . "</td>\n";

		// um
		$val = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
		if( $val != $rs[2]) {
			echo "<td class=\"error\">$val<br>Usare " . $rs[2] . "</td>\n";
			$err = true;
		} else {
			echo "<td class=\"list\">$val</td>\n";
		}

		// quantita
		$val = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
//		$val = (double)$val;
		if( $val <0 || !is_numeric($val) ) {
			echo "<td class=\"error\">$val</td>\n";
			$err = true;
		} else {
			echo "<td class=\"list\">$val</td>\n";
		}
		
		// lotto
		$val = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());
		if( $rs[1] and $val == "") {
			echo "<td class=\"error\">Lotto obbligatorio</td>\n";
			$err = true;
		} else {
			echo "<td class=\"list\">$val</td>\n";
		}
		
		// fine riga
		echo "</tr>\n";
	}
	echo "</table>\n";

}

print("<br>\n");

if($err) {
	print("Sono presenti errori: correggerli e reinviare il file.\n");
} else {

    //rifacciamo il giro importando veramente
	for ($row = 2; $row <= $highestRow; ++ $row) {
		$maga = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
		$codice = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
		$quantita = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
		$quantita = (double)$quantita;
		$lotto = $worksheet->getCellByColumnAndRow(5, $row)->getValue();

		$Query = "INSERT INTO u_invent (codicearti, quantita, magazzino, lotto) VALUES (";
		$Query .= "\"$codice\", $quantita, \"$maga\", \"$lotto\")";
//		print("$Query<br>");
		$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
	}

	print("Dati importati.\n");
}

print("<br>\n");
goMain();
footer();
?>