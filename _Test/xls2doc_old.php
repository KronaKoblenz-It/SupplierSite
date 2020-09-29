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
		
		$lastbolla = 0;
		$id_testa = (time() % 10000000) * 100;
		$id = ($id_testa % 1000000)*1000;
		for ($row = 2; $row <= $highestRow; ++ $row) {
			$bollaNum = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
			$espldistin = ($worksheet->getCellByColumnAndRow(7, $row)->getValue() == 0 ? "P" : "C");
			$art = trim($worksheet->getCellByColumnAndRow($espldistin == "P" ? 2 : 9, $row));
			$artp = trim($worksheet->getCellByColumnAndRow(2, $row));
			$lottop = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
			// scrittura testa (se serve)
			if($lastbolla != ($bollaNum . $artp . $lottop)) {
				$id_testa++;
				$Query = "INSERT INTO U_BARDT ";
				$Query .= "(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, DEL) VALUES ( ";
				$Query .= "$id_testa, ";
				$Query .= "'" . date("Y-m-d") . "', '" . date("Y") . "', ";
				$Query .= "\"$fornitore\", ";
				$tipodoc = "CE";
				$Query .= "\"$tipodoc\", \"\", \"$bollaNum\", ";
				$Query .= "\"$maga\", \"00001\", 0 )";
				
			//	print($Query."<br>");
				$rs = db_query($conn, $Query) or die(mysql_error()); 
				$lastbolla = $bollaNum . $artp . $lottop;
			}
			
			// scrittura riga
			$q = "SELECT DESCRIZION FROM MAGART WHERE CODICE=\"$art\"";
//			print($q."<br>");
			$rs = db_query($conn, $q) or die(mysql_error()); 
			$rw = db_fetch_row($rs);
			$desc = $rw[0];
			
			$nrif = explode(" ", $worksheet->getCellByColumnAndRow(5, $row)->getValue());
			$nriga = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
			$q = "SELECT ID, ID_TESTA FROM DOCRIG ";
			$q .= "WHERE TIPODOC=\"" . $nrif[0] . "\" AND NUMERODOC = \"";
			$q .= $nrif[1];
			$q .= "\" and numeroriga = $nriga";
//			print($q."<br>");
			$rs = db_query($conn, $q) or die(mysql_error()); 
			if( !($rw = db_fetch_row($rs)) ) {
				// non ho trovato il riferimento
				$q = "SELECT ID, ID_TESTA FROM DOCRIG ";
				$q .= "WHERE TIPODOC=\"" . $nrif[0][1] . $nrif[0][0] . "\" AND NUMERODOC = \"";
				$q .= $nrif[1];
				$nriga++;
				$q .= "\" and numeroriga = $nriga";
//				print($q."<br>");
				$rs = db_query($conn, $q) or die(mysql_error()); 
				if( !($rw = db_fetch_row($rs)) ) {
					$rw[0] = 0;
					$rw[1] = 0;
				}
			}

			$id++;
			if( $rw[0] != 0) {
				$Query = "INSERT INTO U_BARDR ";
				$Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, DEL) VALUES ( ";
				$Query .= "$id, ";
				$Query .= "$id_testa, ";
				$Query .= "\"$espldistin\", ";
				$Query .= "'" . date("Y-m-d") . "', ";
				$Query .= "\"$fornitore\", ";
				$Query .= "\"$tipodoc\", ";
				$Query .= "\"$art\", ";
				$Query .= "\"" . str_replace('"', '',$desc) . "\", ";
				$Query .= $worksheet->getCellByColumnAndRow($espldistin == "P" ? 3 : 10, $row)->getValue() . ", ";
				$Query .= "\"" . $worksheet->getCellByColumnAndRow($espldistin == "P" ? 4 : 11, $row)->getValue() . "\", ";
				$Query .= '"", ';
				$Query .= "\"$maga\", \"00001\", ";
				$Query .= $rw[1] . ", ";
				$Query .= $rw[0] . ", ";
				$Query .= "0 )";
				
	//			print($Query."<br>");
				$rs = db_query($conn, $Query) or die(mysql_error()); 
			}
			// scrivo la riga a monitor
			echo "<tr id=\"$id\" class=\"list\">\n";
			for ($col = 0; $col < $highestColumnIndex; ++ $col) {
				$cell = $worksheet->getCellByColumnAndRow($col, $row);
				$val = $cell->getValue();
				if(($col == 5 || $col == 6) && $rw[0] == 0) {
					echo "<td class=\"error\">$val<br>Non trovato</td>\n";
					$err = true;
				} else {
					echo "<td class=\"list\">$val</td>\n";
				}
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

}


if($err) {
	print("<br>Errore nel caricamento.\n");
/*	$Query = "DELETE FROM U_BARDR WHERE ID_TESTA = $id_testa";
	$rs = db_query($conn, $Query) or die(mysql_error());
	$Query = "DELETE FROM U_BARDT WHERE ID = $id_testa";
	$rs = db_query($conn, $Query) or die(mysql_error()); */
	mail("ced@k-group.com", "Importazione_Bolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si è verificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("ced-it@k-group.com", "ImportazioneBolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si è verificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("spedizioni@koblenz.it", "ImportazioneBolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si è verificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("b.vaccari@vmgroup.it", "Importazione_Bolle_$cookie[0]_$cookie[1]",  "Krona Koblenz - Attenzione si è verificato un Errore nell'inserimento Bolla da Excel.", "From: automatico@k-group.com");
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