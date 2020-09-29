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

$Query = "SELECT U_BARDR.ID_TESTA, U_BARDR.LOTTO, U_BARDT.NUMERODOCF";
$Query .= " FROM U_BARDR INNER JOIN U_BARDT ON U_BARDT.ID = U_BARDR.ID_TESTA";
$Query .= " WHERE U_BARDR.CODICEARTI =\"$cCodiceArt\" AND U_BARDT.CODICECF =\"$cCodiceCF\"";
$Query .= " AND U_BARDR.ESPLDISTIN=\"P\"";
$Query .= " ORDER BY U_BARDR.LOTTO";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 

print("<listaord>\n"); 
while($row = db_fetch_row($queryexe)) { 
  print("<ordine>\n"); 
  print("<id_testa>"  . $row[0] . "</id_testa>\n"); 
  print("<lotto>" . $row[1] . "</lotto>\n");
  print("<numerodocf>" . $row[2] . "</numerodocf>\n"); 
  print("</ordine>\n");

}
print("</listaord>\n");

//-----------------------
//diconnect from database 
db_close($conn); 
?>