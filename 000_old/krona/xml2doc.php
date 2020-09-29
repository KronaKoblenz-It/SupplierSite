<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);

$conn = db_connect($dbase); 
$anno = current_year();

head();
banner("Importazione file");

$err = false;
if ($_FILES["file"]["type"] == "text/xml") {
	if ($_FILES["file"]["error"] > 0) {
	   echo "Errore: " . $_FILES["file"]["error"] . "<br />";
	   $err = true;
	} else {
//	   echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//	   echo "Type: " . $_FILES["file"]["type"] . "<br />";
//	   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//	   echo "Stored in: " . $_FILES["file"]["tmp_name"];
	}
} else {
	echo "Errore: File di tipo non corretto<br />";
	$err = true;
}

if(!$err) {
//$file = $_FILES["file"]["tmp_name"];
//$contents = file($file); 
//$string = implode($contents); 


	$xml = DOMDocument::load($_FILES["file"]["tmp_name"]); 
//	echo $xml->saveXML() . "\n";

	$list = $xml->getElementsByTagNameNS("http://www.Project-srl.it/ArcaWeb","doc"); 
//	echo $list->length;
	if($list->length != 1) {
		echo "Errore: File XML non riconosciuto";
		$err = true;
	}
}

if(!$err) {
	$id_testa = time();
	$list = $xml->getElementsByTagNameNS("http://www.Project-srl.it/ArcaWeb","doctes");
//	for($j=0; $j < $list->length; $j++) {
	if( $list->length == 1) {
	    $riga = $list->item(0);
		$Query = "INSERT INTO U_BARDT ";
		$Query .= "(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, DEL) VALUES ( ";
//		$Query .= getTagValue($riga, "id_testa") . ", ";
		$Query .= "$id_testa, ";
		$Query .= "'" . date("Y-m-d") . "', '" . date("Y") . "', ";
//		$Query .= "\"" . getTagValue($riga, "codicecf") . "\", ";
		$Query .= "\"$fornitore\", ";
		$tipodoc = getTagValue($riga, "tipodoc");
		$Query .= "\"$tipodoc\", \"\", \"" . getTagValue($riga, "numerodocf") . "\", ";
//		$Query .= "\"" . getTagValue($riga, "magpartenz") . "\", \"" . getTagValue($riga, "magarrivo") . "\", 0 )";
		$Query .= "\"$maga\", \"" . ($tipodoc == "SK" ? "SC" : "00001") ."\", 0 )";
//		print($Query."<br>");
		$rs = db_query($conn, $Query) or die(mysql_error()); 
	} else {
		$err = true;
		if( $list->length > 0 ) {
			echo "Errore: è ammesso un solo record di testa.";
		} else {
			echo "Errore: record di testa mancante.";
		}
	}
}

if(!$err) {
	$id = ($id_testa % 1000000)*1000;
	$list = $xml->getElementsByTagNameNS("http://www.Project-srl.it/ArcaWeb","docrig");
	for($j=0; $j < $list->length; $j++) {
	    $riga = $list->item($j);
		$Query = "INSERT INTO U_BARDR ";
		$Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, DEL) VALUES ( ";
//		$Query .= getTagValue($riga, "id_riga") . ", ";
//		$Query .= getTagValue($riga, "id_testa") . ", ";
		$Query .= "$id, ";
		$id++;
		$Query .= "$id_testa, ";
		$Query .= "\"" . getTagValue($riga, "espldistin") . "\", ";
		$Query .= "'" . date("Y-m-d") . "', ";
//		$Query .= "\"" . getTagValue($riga, "codicecf") . "\", ";
		$Query .= "\"$fornitore\", ";
		$Query .= "\"$tipodoc\", ";
		$Query .= "\"" . getTagValue($riga, "codicearti") . "\", ";
		$Query .= "\"" . str_replace('"', '',getTagValue($riga, "descrizion")) . "\", ";
		$Query .= getTagValue($riga, "quantita") . ", ";
		$Query .= "\"" . getTagValue($riga, "lotto") . "\", ";
		$Query .= '"", ';
//		$Query .= "\"" . getTagValue($riga, "magpartenz") . "\", ";
//		$Query .= "\"" . getTagValue($riga, "magarrivo") . "\", ";
		$Query .= "\"$maga\", \"" . ($tipodoc == "SK" ? "SC" : "00001") ."\", ";
		$Query .= getTagValue($riga, "riffromt") . ", ";
		$Query .= getTagValue($riga, "riffromr") . ", ";
		$Query .= "0 )";
		
//		print($Query."<br>");
		$rs = db_query($conn, $Query) or die(mysql_error()); 
	}
}

if($err) {
	print("<br>File non caricato.\n");
} else {
	print("<br>File caricato.\n");
}
print("<br>\n<br>\n");
print("<a href=\"ddtimport.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo caricamento\">Nuovo caricamento</a>\n");


print("<br>\n");
goMain();
footer();


function getTagValue($doc, $tag) {
	if( $doc->getElementsByTagNameNS("http://www.Project-srl.it/ArcaWeb",$tag)->length > 0) { 
        return $doc->getElementsByTagNameNS("http://www.Project-srl.it/ArcaWeb",$tag)->item(0)->nodeValue;
	} else {
		return '';
	}
}
?>