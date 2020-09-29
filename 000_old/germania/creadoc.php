<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
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

$id_testa = time();
$Query = "INSERT INTO U_BARDT ";
$Query .= "(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO) VALUES ( ";
$Query .= "$id_testa, ";
$Query .= "'" . date("Y-m-d") . "', '" . date("Y") . "', ";
$Query .= "\"$fornitore\", ";
$Query .= "\"RL\", \"\", \"" . $_POST['numerodocf'] . "\", ";
$Query .= "\"$maga\", \"00001\" )";
$rs = db_query($conn, $Query) or die(mysql_error()); 


$id = ($id_testa % 1000000)*100;
$Query = "INSERT INTO U_BARDR ";
$Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO) VALUES ( ";
$Query .= "$id, ";
$Query .= "$id_testa, ";
$Query .= '"P", ';
$Query .= "'" . date("Y-m-d") . "', ";
$Query .= "\"$fornitore\", ";
$Query .= "\"RL\", ";
$Query .= '"' . $_POST["padre"] . '", ';
$Query .= $_POST["quantita"] . ', ';
$Query .= '"' . $_POST["lottopadre"] . '", ';
$Query .= '"", ';
$Query .= "\"$maga\", \"00001\" )";
//print($Query);
$rs = db_query($conn, $Query) or die(mysql_error()); 

$id++;

for($j = 1; $j <= $count; $j++) {
  $Query = "INSERT INTO U_BARDR ";
  $Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO) VALUES ( ";
  $Query .= "$id, ";
  $Query .= "$id_testa, ";
  $Query .= '"C", ';
  $Query .= "'" . date("Y-m-d") . "', ";
  $Query .= "\"$fornitore\", ";
  $Query .= "\"RL\", ";
  $Query .= '"' . $_POST["code$j"] . '", ';
  $Query .= $_POST["qta$j"] . ', ';
  $Query .= '"' . $_POST["lotto$j"] . '", "", ';
  $Query .= "\"$maga\", \"00001\" )";
  $rs = db_query($conn, $Query) or die(mysql_error()); 
  $id++;
}
//	$ret = shell_exec("d:\arca\arca_italia\autorun-barcode.bat");
	print("Documento caricato.\n");

	print ("<br/><a href=\"askdb.php\">Altra ricerca</a>\n");
	print ("<br/><a href=\"menu-for.php\">Menu principale</a>\n");

 
footer();

?>