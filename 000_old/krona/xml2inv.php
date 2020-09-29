<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
$connectionstring = db_connect($dbase); 
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$magf = "F" . substr($cookie[0],2);

banner("Caricamento inventario da Excel",$cookie[1]);

$err = false;
if ($_FILES["file"]["type"] == "text/xml") {
	if ($_FILES["file"]["error"] > 0) {
	   echo "Errore: " . $_FILES["file"]["error"] . "<br>";
	   $err = true;
	} 
} else {
	echo "Errore: File di tipo non corretto<br>";
	$err = true;
}

if(!$err) {
	$xml = DOMDocument::load($_FILES["file"]["tmp_name"]); 
	$list = $xml->getElementsByTagName("Worksheet"); 
	if($list->length != 1) {
		echo "Errore: File non riconosciuto";
		$err = true;
	}
}

if(!$err) {
	$rowslist = $xml->getElementsByTagName("Row"); 
	if($rowslist->length < 2) {
		echo "Errore: il file non contiene dati";
		$err = true;
	}
}

if(!$err) {
	$cellslist = $rowslist->item(0)->getElementsByTagName("Cell"); 
	if($cellslist->length < 5) {
		echo "Errore: mancano alcune colonne";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(0)) != "Magazzino") {
		echo "Errore: colonna 'Magazzino' non trovata";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(1)) != "Codice") {
		echo "Errore: colonna 'Codice' non trovata";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(3)) != "Quantita") {
		echo "Errore: colonna 'Quantita' non trovata";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(4)) != "Lotto") {
		echo "Errore: colonna 'Lotto' non trovata";
		$err = true;
	}
}

if(!$err) {
	print("<table class	=\"list\">\n");
	print("<tr class=\"list\">\n");
	print("<th class=\"list\">Magazzino</th>\n");
	print("<th class=\"list\">Codice</th>\n");
	print("<th class=\"list\">Descrizione</th>\n");
	print("<th class=\"list\">Quantita</th>\n");
	print("<th class=\"list\">Lotto</th>\n");
	print("</tr>\n");
	for( $j = 1; $j < $rowslist->length; $j++) {
		$cellslist = $rowslist->item($j)->getElementsByTagName("Cell"); 
		if($cellslist->length <4) {
			echo "<tr><td colspan=\"5\">Riga incompleta</td></tr>";
			$err = true;
			continue;
		}

		$maga = getCellValue($cellslist->item(0));
		if($maga == $magf) {
			$descm = $cookie[1];
		} else {
			$descm = "Magazzino non valido!";
			$err = true;
		}

		$codice = getCellValue($cellslist->item(1));
		$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$codice\"";
		$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		if($rs = db_fetch_row($queryexe)) {
			$desc = $rs[0];
		} else {
			$desc = "Articolo non trovato!";
			$err = true;
		}
		
		$quantita = getCellValue($cellslist->item(3));
		
		if($cellslist->length > 4) {
			$lotto = getCellValue($cellslist->item(4));
		} else {
			$lotto = "&nbsp;";
		}
		
		print("<tr class=\"list\">\n");
		print("<td class=\"list\">$maga-$descm</td>\n");
		print("<td class=\"list\">$codice</td>\n");
		print("<td class=\"list\">$desc</td>\n");
		print("<td class=\"list\">$quantita</td>\n");
		print("<td class=\"list\">$lotto</td>\n");
		print("</tr>\n");
	}
	print("</table>\n");
}   

print("<br>\n");
if($err) {
	print("Sono presenti errori: correggerli e reinviare il file.\n");
} else {
    //rifacciamo il giro importando veramente
	for( $j = 1; $j < $rowslist->length; $j++) {
		$cellslist = $rowslist->item($j)->getElementsByTagName("Cell"); 
		$maga = getCellValue($cellslist->item(0));
		$codice = getCellValue($cellslist->item(1));
		$quantita = getCellValue($cellslist->item(3));
		if($cellslist->length > 4) {
			$lotto = getCellValue($cellslist->item(4));
		} else {
			$lotto = "";
		}
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

function getCellValue($doc) {
	if( $doc->getElementsByTagName("Data")->length > 0) { 
        return $doc->getElementsByTagName("Data")->item(0)->nodeValue;
	} else {
		return '';
	}
}
?>