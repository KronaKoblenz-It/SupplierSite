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

$conn = db_connect($dbase); 
 
$anno = current_year();

head();
banner($_POST["descrizion"]);

$rnc = "INSERT INTO W_ISORNC (";
$rnc .= "ESERCIZIO, CTIPORAPP, CODFOR, DATAREG, DATAINIT, DESCRIZION, DETTAGLIO, DOCESER, DOCTIP, DOCNMOV, CAUSA, CODOP, U_IDRESO,CODICEARTI, QUANTITA";
$rnc .= ") VALUES (";

$rnc .= "\"$anno\", ";
$rnc .= "\"0002\", ";
$rnc .= "\"" . $_POST["codicecf"] . "\", ";

$rnc .= "'" . date("Y-m-d") . "', ";
$rnc .= "'" . date("Y-m-d") . "', ";

$rnc .= "\"" . $_POST["descrizion"] . "\", ";
$rnc .= "\"" . $_POST["dettaglio"] . "\", ";

$rnc .= "\"" . $_POST["doceser"] . "\", ";
$rnc .= "\"" . $_POST["tipodoc"] . "\", ";
$rnc .= "\"" . $_POST["numerodoc"] . "\", ";
$rnc .= "\"" . $_POST["causa"] . "\", ";

$rnc .= "\"$fornitore\", ";
$rnc .= $_POST["id_doc"] . ", ";

$rnc .= "\"" . $_POST["codicearti"] . "\", ";
$rnc .= $_POST["quantita"] ;

$rnc .= ")";

$queryexe = db_query($conn, $rnc) or die(mysql_error() ); 

print("<br>Documento caricato.\n");

$Query = "SELECT ID_TESTA FROM DOCRIG WHERE ID =" . $_POST["id_doc"];
$queryexe = db_query($conn, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
header("location: doc-detail.php?id=" . $row[0]);

print("<br>\n");
footer();

?>