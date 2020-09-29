<?php 
header('Content-Type: text/xml');
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase); 

$cCodiceCF = $_GET['codcf'];
$cCodiceArt = $_GET['codart'];

$Query = <<<EOT
select U_TESTE_LISTEP.ID, U_TESTE_LISTEP.LOTTO, U_TESTE_LISTEP.DATA
from U_TESTE_LISTEP
where U_TESTE_LISTEP.FORNITORE = '$cCodiceCF' 
and U_TESTE_LISTEP.ARTICOLO = '$cCodiceArt'
order by DATA DESC
EOT;
$queryexe = db_query($conn, $Query) or die(mysql_error()); 

print("<listaord>\n"); 
while($row = db_fetch_row($queryexe)) { 
  print("<ordine>\n"); 
  print("<id_testa>"  . $row[0] . "</id_testa>\n"); 
  print("<lotto>" . $row[1] . "</lotto>\n");
  print("<data>" . $row[2] . "</data>\n"); 
  print("</ordine>\n");

}
print("</listaord>\n");

//-----------------------
//diconnect from database 
db_close($conn); 
?>