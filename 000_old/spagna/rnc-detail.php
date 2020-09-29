<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
$id = $_GET['id'];

$connectionstring = db_connect($dbase); 
$Query = "SELECT * FROM ISORNC WHERE ID = $id";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$rnc = mysql_fetch_object($queryexe);
banner("Rapporto non conformit&agrave;",$rnc->DESCRIZION);

function prt_row( $title, $data) {
  print("<tr>\n<th align=\"left\" bgcolor=\"#CCFFFF\">$title</th>\n");	
  print("<td align=\"left\" bgcolor=\"#ccffcc\">" . ("" == $data ? "&nbsp;" : $data) . "</td>\n</tr>\n");	
}

print("<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\" >\n");
prt_row("Data", format_date($rnc->DATAREG) );  
$Query = "SELECT DESCRIZION FROM ISOCAUSE WHERE CODICE = \"" . $rnc->CAUSA . "\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
prt_row("Tipo", $row[0] );  

prt_row("Descrizione", $rnc->DESCRIZION);
prt_row("Riferimento", $rnc->DOCTIP . " " . $rnc->DOCNMOV);

$Query = "SELECT DESCRIZION FROM DIPENDENTI WHERE CODICE = \"" . $rnc->U_DIP1 . "\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
prt_row("Aperta da", $row[0] );  

prt_row("Chiusa il", format_date($rnc->DATAEND) );  
prt_row("Dettagli", $rnc->DETTAGLIO);
prt_row("Azione", $rnc->AZIONE);
prt_row("Verifica", $rnc->VERIFYAPP);
prt_row("Entro il", format_date($rnc->VAPPDATE) );  

//diconnect from database 
db_close($connectionstring); 


print("</table>\n");

print("<br>\n");
goMain();
footer();
?>