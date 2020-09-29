<?php 
header('Content-Type: text/xml');
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase); 

$cCodiceCF = $_GET['codcf'];
$cCodiceArt = $_GET['codart'];

$Query = "SELECT DOCRIG.ID_TESTA, DOCTES.TIPODOC, DOCTES.NUMERODOC, DOCTES.DATADOC, DOCRIG.DATAINIZIO, DOCRIG.QUANTITARE, DOCRIG.ID ";
$Query .= " FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA";
$Query .= " WHERE DOCRIG.CODICEARTI =\"$cCodiceArt\" AND DOCTES.CODICECF =\"$cCodiceCF\" AND DOCRIG.QUANTITARE > 0";
$Query .= " AND (DOCTES.TIPODOC=\"FO\" OR DOCTES.TIPODOC=\"LO\" OR DOCTES.TIPODOC=\"OF\" OR DOCTES.TIPODOC=\"OL\")";
$Query .= " ORDER BY DOCRIG.DATACONSEG";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 

print("<listaord>\n"); 
while($row = db_fetch_row($queryexe)) { 
  print("<ordine>\n"); 
  print("<id_testa>"  . $row[0] . "</id_testa>\n"); 
  print("<tipodoc>" . $row[1] . "</tipodoc>\n");
  print("<numerodoc>" . $row[2] . "</numerodoc>\n"); 
  print("<datadoc>" . format_date($row[3]) . "</datadoc>\n"); 
  print("<dataconseg>" . format_date($row[4]) . "</dataconseg>\n"); 
  print("<quantitare>" . $row[5] . "</quantitare>\n");  
  print("<id>"  . $row[6] . "</id>\n"); 
  print("</ordine>\n");

}
print("</listaord>\n");

//-----------------------
//diconnect from database 
db_close($conn); 
?>