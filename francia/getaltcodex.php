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

$id = $_GET['id'];

$Query = "SELECT DOCRIG.CODICEARTI, DOCRIG.DESCRIZION,";
$Query .= " CODALT.CODARTFOR, CODALT.U_BARCODE";
$Query .= " FROM DOCRIG LEFT OUTER JOIN CODALT ON CODALT.CODICEARTI = DOCRIG.CODICEARTI AND CODALT.CODCLIFOR = DOCRIG.U_CLIVEN";
$Query .= " WHERE DOCRIG.ID = " . $id;
$queryexe = db_query($conn, $Query) or die(mysql_error()); 

print("<artinfo>\n"); 
while($row = db_fetch_row($queryexe)) { 
  print("<codice>"  . (is_null($row[2]) ? $row[0] : $row[2]) . "</codice>\n"); 
  print("<descrizion>" . $row[1] . "</descrizion>\n");
  print("<barcode>" . (is_null($row[3]) ? "" : $row[3]) . "</barcode>\n"); 
}
print("</artinfo>\n");

//-----------------------
//diconnect from database 
db_close($conn); 
?>