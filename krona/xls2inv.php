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
include("inv_lib.php");
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

if(!$err) {
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
	echo "<br>\n<table class=\"list\">\n";
	$row = 2;
	echo "<tr class=\"list\">\n";
	/*for ($col = 0; $col < 6; ++ $col) {
		$cell = $worksheet->getCellByColumnAndRow($col, $row);
		$val = $cell->getValue();
		echo "<th class=\"list\">$val</th>\n";
	}*/
	echo "<th class=\"list\">Cod. Mag.</th>\n"; //0
	echo "<th class=\"list\">Cod. Art.</th>\n"; //1
	echo "<th class=\"list\">Descr. Art.</th>\n"; //2
	echo "<th class=\"list\">U.M.</th>\n"; //3
	echo "<th class=\"list\">Quantita' Inventariata</th>\n"; //4
	echo "<th class=\"list\">Lotto</th>\n"; //6
	echo "<th class=\"list\">Warning</th>\n"; 
	echo "</tr>\n";
	
	for ($row = 3; $row <= $highestRow; ++ $row) {
		$warn = "";
		echo "<tr class=\"list\">\n";
//		for ($col = 0; $col < $highestColumnIndex; ++ $col) {
//			$cell = $worksheet->getCellByColumnAndRow($col, $row);
//			$val = $cell->getValue();
//			echo "<td class=\"list\">$val</td>\n";
//		}

		// codice magazzino
		$val = trim($worksheet->getCellByColumnAndRow(0, $row)->getValue());
		$val = strtoupper ($val);
		if($val == $magf) {
			echo "<td class=\"list\">$val</td>\n";
		} else {
			echo "<td class=\"error\">$val<br>Mag. non valido</td>\n";
			$err = true;
		}
		$maga = $val;

		// codice articolo
		$val = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
		$val = strtoupper($val);
		$Query = "SELECT DESCRIZION, LOTTI, UNMISURA, NOINVENT FROM MAGART WHERE CODICE = '$val'";
		$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		if($rs = db_fetch_row($queryexe)) {
			if($mode == "attr" && $rs[3] == 0) {
				echo "<td class=\"error\">$val<br><b>Non &egrave; attrezzatura</b></td>\n";
				$err = true;				
			} else if($mode != "attr" && $rs[3] == 1) {
				echo "<td class=\"error\">$val<br><b>Cod. di attrezzatura</b></td>\n";
				$err = true;								
			}
			else	
				echo "<td class=\"list\">$val</td>\n";
		} else {
			echo "<td class=\"error\">$val<br><b>Cod. non valido</b></td>\n";
			$err = true;
		}
		$cCod = $val;
		
		// descrizione
		echo "<td class=\"list\">" . trim($rs[0]) . "</td>\n";

		// um
		$val = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
		$val = strtoupper($val);
		if( $val != $rs[2]) {
			echo "<td class=\"error\">$val<br>Usare " . $rs[2] . "</td>\n";
			$err = true;
			$warn = "<b>ATTENZIONE!</b></br>L'unita' di misura di inventario deve essere pari</br> a quella principale gestita dai nostri sistemi</br>";
		} else {
			echo "<td class=\"list\">$val</td>\n";
		}
		
		// quantita
		$val = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
		$qta = (double)$val;
		if( $val <0 || !is_numeric($val) ) {
			echo "<td class=\"error\">$val</td>\n";
			$err = true;
		} else {
			echo "<td class=\"list\">$val</td>\n";
		}
		
		// lotto
		$val = trim($worksheet->getCellByColumnAndRow(6, $row)->getValue());
		$val = strtoupper($val);
		if( $rs[1] and $val == "") {
			echo "<td class=\"error\">Lotto obbligatorio</td>\n";
			$err = true;
			$warn = $warn. "<b>ATTENZIONE!</b></br>In Assenza di codice LOTTO inserire il codice generico '<b>INV-2019</b>'.";	
		} else {
			if (!$rs[1] and $val != ""){
				echo "<td class=\"error\">$val</td>\n";
				$err = true;
				$warn = $warn. "<b>ATTENZIONE!</b></br>L'ARTICOLO presente NON necessita di codice LOTTO.";
			} else {
				echo "<td class=\"list\">$val</td>\n";
			}
		}

		//Confronto con Giacenza
		if($warn == ""){
		if($val != ""){
			$Query = "SELECT (PROGQTACAR-PROGQTASCA+PROGQTARET) AS GIACENZA FROM MAGGIACL WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$cCod\" AND LOTTO = \"$val\" ";
			$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
			if($rw = db_fetch_row($qe)) {
				$giac = (double)$rw[0];
				if ($qta < 0){
					$warn = "<b>SOLO PER SEGNALAZIONE</b></br>QUANTITA' INVENTARIATA NON PUO' ESSERE NEGATIVA";
				} else {
				if ($giac < 0 && $qta = 0){
					$warn = "";
				} else {
					if ($giac < 0 && $qta > 0){
						$warn = "<b>SOLO PER SEGNALAZIONE</b></br>LA GIACENZA ERA NEGATIVA";
					} else {
						$limite = $giac*0.1;
						$diff = $giac-$qta;
						if(abs($diff)>$limite){
							if($diff>$limite){
								$warn = "<b>SOLO PER SEGNALAZIONE</b></br>QUANTITA' INVENTARIATA E' DEL 10 % MINORE</br>ALLA GIACENZA PRECEDENTE" ;
							} else {
								$warn = "<b>SOLO PER SEGNALAZIONE</b></br>QUANTITA' INVENTARIATA E' DEL 10 % MAGGIORE</br>ALLA GIACENZA PRECEDENTE" ;
							}
						}
					}								
				}
				}
			} else {
				$warn = "<b>SOLO PER SEGNALAZIONE</b></br>IL LOTTO NON ERA PRESENTE IN MAG." ;
			}
		} else {
			$Query = "SELECT (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA ";
			$Query .= "FROM MAGGIAC ";
			$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\" AND MAGGIAC.ARTICOLO=\"$cCod\" ";
			$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
			if($rw = db_fetch_row($qe)){
				$giac = (double)$rw[0];
				if ($qta < 0){
					$warn = "<b>SOLO PER SEGNALAZIONE</b></br>QUANTITA' INVENTARIATA NON PUO' ESSERE NEGATIVA";
				} else {
				if ($giac < 0 && $qta = 0){
					$warn = "";
				} else {
					if ($giac < 0 && $qta > 0){
						$warn = "<b>SOLO PER SEGNALAZIONE</b></br>LA GIACENZA ERA NEGATIVA";
					} else {
						$limite = $giac*0.1;
						$diff = $giac-$qta;
						if(abs($diff)>$limite){
							if($diff>$limite){
								$warn = "<b>SOLO PER SEGNALAZIONE</b></br>QUANTITA' INVENTARIATA E' DEL 10% MINORE</br>ALLA GIACENZA PRECEDENTE" ;
							} else {
								$warn = "<b>SOLO PER SEGNALAZIONE</b></br>QUANTITA' INVENTARIATA E' DEL 10% MAGGIORE</br>ALLA GIACENZA PRECEDENTE" ;
							}
						}
					}								
				}
				}
			} else {
				$warn = "<b>SOLO PER SEGNALAZIONE</b></br>L'ARTICOLO NON ERA PRESENTE IN MAG." ;
			}
		}
		}
		if( $warn != "") {
			echo "<td class=\"warn\">$warn</td>\n";
		} else {
			echo "<td class=\"list\">$warn</td>\n";
		}
		
		
		// fine riga
		echo "</tr>\n";
	}
	echo "</table>\n";

}

print("<br>\n");

if($err) {
	print("<br><div style=\"text-align: center; font-size:14px;\"><b style=\"font-size:20px;\">ATTENZIONE! </b><b>Sono presenti errori: correggerli e reinviare il file.</b></div>\n");
} else {

	print("<br><div style=\"text-align: center; font-size:14px;\"><b style=\"font-size:20px;\">OK! </b><b>Compilazione senza errori.</br>Continua con 'Procedi'.</b></div>\n");
//	print("<form action=\"xls2invdb.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
//	print("<input type=\"hidden\" name=\"file\" id=\"file\" value=\"$nameFile\">\n"); 
	print("<form action=\"xls2invdb.php\" method=\"GET\">\n");
	hiddenField("mode", $mode);
	hiddenField("file", $nameFile);
	print("<br>\n");
 
	print("<div style=\"text-align: center;\"><input type=\"submit\" id=\"btnok\" value=\"PROCEDI!\" ></div>\n");
	print("</form>\n");
}

print("<br>\n");
goMenu();
footer();
?>