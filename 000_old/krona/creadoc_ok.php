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
 
$count = strtoupper($_POST['count']);
$anno = current_year();

head();
banner($_POST["padre"]);

if($_POST["idtesta"] > 0) {
	$Query = "DELETE FROM U_BARDT WHERE ID=".$_POST["idtesta"];
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$Query = "DELETE FROM U_BARDR WHERE ID_TESTA=".$_POST["idtesta"];
	$rs = db_query($conn, $Query) or die(mysql_error()); 
}

$Query = "SELECT TIPODOC, NUMERODOC, DATADOC FROM DOCTES WHERE ID = " . $_POST["rift"];
//print($Query."<br>");
$rs = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($rs);
$rif_tipodoc = $row->TIPODOC;
$rif_numerodoc = $row->NUMERODOC;
$rif_datadoc = $row->DATADOC;

$id_testa = (time() % 10000) + substr($fornitore, -4)*10000;
$Query = "INSERT INTO U_BARDT ";
$Query .= "(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, RIF_TIPODOC, RIF_NUMERODOC, DEL) VALUES ( ";
$Query .= "$id_testa, ";
$Query .= "'" . date("Y-m-d") . "', '" . date("Y") . "', ";
$Query .= "\"$fornitore\", ";
$Query .= "\"CE\", \"\", \"" . $_POST['numerodocf'] . "\", ";
$Query .= "\"$maga\", \"00001\", \"" . $rif_tipodoc . "\", \"" . $rif_numerodoc . "\" ,0 )";
//print($Query."<br>");
$rs = db_query($conn, $Query) or die(mysql_error()); 


$id = $id_testa *100;

// riga di commento
//$Query = "SELECT TIPODOC, NUMERODOC, DATADOC FROM DOCTES WHERE ID = " . $_POST["rift"];
//$rs = db_query($conn, $Query) or die(mysql_error());
//$row = mysql_fetch_object($rs);
$id = scriviRiga($id, $id_testa, "", $fornitore, "", 1, "", $maga, "Rif. " . $rif_tipodoc . " " . $rif_numerodoc . " del " . format_date($rif_datadoc));

// riga del padre
$id = scriviRiga($id, $id_testa, "P", $fornitore, $_POST["padre"], $_POST["quantita"], $_POST["lottopadre"], $maga, "");

// righe componenti
for($j = 1; $j <= $count; $j++) {
  $id = scriviRiga($id, $id_testa, 'C', $fornitore, $_POST["code$j"], $_POST["qta$j"], $_POST["lotto$j"], $maga, "");
} 
print("<br>Documento caricato.\n");

print("<br>\n");
print("<a href=\"askdb.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuova bolla\">Nuova bolla</a>\n");
print("<br>\n");
goMain();
footer();

function scriviRiga($id, $id_testa, $espldistin, $fornitore, $codicearti, $qta, $lotto, $maga, $descrizion) {
global $conn;
$Query = "INSERT INTO U_BARDR ";
$Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, DEL) VALUES ( ";
$Query .= "$id, ";
$Query .= "$id_testa, ";
$Query .= "\"$espldistin\", ";
$Query .= "'" . date("Y-m-d") . "', ";
$Query .= "\"$fornitore\", ";
$Query .= "\"CE\", ";
$Query .= "\"$codicearti\", ";
if( $codicearti != "") {
  $q1 = "SELECT DESCRIZION FROM MAGART WHERE CODICE =\"$codicearti\"";
//print($q1."<br>");
  $rs = db_query($conn, $q1) or die(mysql_error()); 
  $row = mysql_fetch_object($rs);
  $Query .= "\"" . str_replace('"', '""', $row->DESCRIZION) . "\", ";
} else {
  $Query .= "\"$descrizion\", ";
}
$Query .= "$qta, ";
$Query .= "\"$lotto\", ";
$Query .= '"", ';
$Query .= "\"$maga\", \"00001\", ";
$Query .= $_POST["rift"] . ", ". $_POST["rifr"] . ", 0 )";
//print($Query."<br>");
$rs = db_query($conn, $Query) or die(mysql_error()); 
return $id + 1;  
}
?>