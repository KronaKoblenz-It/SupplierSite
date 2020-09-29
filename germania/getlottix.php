<?php 
header('Content-Type: text/xml');
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase); 

$cCodice = $_GET['cod'];
$cMaga = $_GET['mag'];

$Query = "SELECT LOTTO FROM MAGGIACL WHERE ARTICOLO =\"$cCodice\" AND MAGAZZINO =\"$cMaga\"";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 

$out = "<listalotti>";
while($row = db_fetch_row($queryexe)) { 
  $out .= "<codice>" . $row[0] . "</codice>";
}
$out .= "</listalotti>";

print($out); 

//-----------------------
//diconnect from database 
db_close($connectionstring); 
?>