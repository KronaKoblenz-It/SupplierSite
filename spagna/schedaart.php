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

$maga = $_GET['maga'];
$art = $_GET['art'];
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$art\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Scheda di magazzino","$art - " . $row[0]);

// Giacenza iniziale
$Query = "SELECT MAGGIAC.GIACINI ";
$Query .= "FROM MAGGIAC ";
$Query .= "WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$art\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
$row = db_fetch_row($queryexe);

print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th height=\"22\">Data</th>\n"); 
print("<th height=\"22\">Riferimento</th>\n"); 
print("<th height=\"22\">Carico</th>\n"); 
print("<th height=\"22\">Scarico</th>\n"); 
print("<th height=\"22\">Progressivo</th>\n"); 
print("<th height=\"22\">Lotto</th>\n"); 
print("</tr>\n");

$progr = $row[0];
print ("<tr bgcolor=\"#ccffcc\">\n"); 
print ("<td>01/01/" . current_year() . "</td>\n"); 
print ("<td>Giacenza iniziale</td>\n"); 
print ("<td align=\"right\">" . $row[0] . "</td>\n"); 
print ("<td align=\"right\">&nbsp;</td>\n"); 
print ("<td align=\"right\">$progr</td>\n"); 
print ("<td>&nbsp;</td>\n"); 
print ("</tr>\n"); 
 
//query database 
$Query = "SELECT QUANTITA, QTACAR, QTASCAR, QTARET, DATAMOV, RIFDOC, LOTTO ";
$Query .= "FROM MAGMOV ";
$Query .= "WHERE CODICEARTI = \"$art\" AND MAGAZZINO = \"$maga\" ";
$Query .= "ORDER BY DATAMOV ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
while($row = db_fetch_row($queryexe)) { 
    $progr += ($row[1] > 0 || $row[3] > 0 ? $row[0] : -$row[0]);
	print ("<tr bgcolor=\"#ccffcc\">\n"); 
	print ("<td>" . format_date($row[4]) . "</td>\n"); 
	print ("<td>" . $row[5] . "</td>\n"); 
	print ("<td align=\"right\">" . ($row[1] > 0 || $row[3] > 0 ? $row[0] : "&nbsp;") . "</td>\n"); 
	print ("<td align=\"right\">" . ($row[2] > 0 || $row[3] < 0 ? $row[0] : "&nbsp;") . "</td>\n"); 
	print ("<td align=\"right\">$progr</td>\n"); 
	print ("<td>" . $row[6] . "</td>\n"); 
	print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</table>\n<br>\n");
goMain();
footer();
?>