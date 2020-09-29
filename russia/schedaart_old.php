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
$eserc = $_GET['esercizio'];
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$art\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Scheda di magazzino","$art - " . $row[0]);

// Giacenza iniziale
$Query = "SELECT MAGGIAC.GIACINI ";
$Query .= "FROM MAGGIAC ";
$Query .= "WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$art\" AND ESERCIZIO = \"$eserc\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
$row = db_fetch_row($queryexe);
//echo $Query;

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">Data</th>\n"); 
print("<th class=\"list\">Riferimento</th>\n"); 
print("<th class=\"list\">Carico</th>\n"); 
print("<th class=\"list\">Scarico</th>\n"); 
print("<th class=\"list\">Progressivo</th>\n"); 
print("<th class=\"list\">Lotto</th>\n"); 
print("</tr>\n");

$progr = $row[0];
print ("<tr class=\"list\">\n"); 
print ("<td class=\"list\">01/01/" . current_year() . "</td>\n"); 
print ("<td class=\"list\">Giacenza iniziale</td>\n"); 
print ("<td class=\"list\" style=\"text-align: right;\">" . $row[0] . "</td>\n"); 
print ("<td class=\"list\" style=\"text-align: right;\">&nbsp;</td>\n"); 
print ("<td class=\"list\" style=\"text-align: right;\">$progr</td>\n"); 
print ("<td class=\"list\">&nbsp;</td>\n"); 
print ("</tr>\n"); 
 
//query database 
$Query = "SELECT QUANTITA, QTACAR, QTASCAR, QTARET, DATAMOV, RIFDOC, LOTTO ";
$Query .= "FROM MAGMOV ";
$Query .= "WHERE CODICEARTI = \"$art\" AND MAGAZZINO = \"$maga\" ";
$Query .= "ORDER BY DATAMOV ";

$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
while($row = db_fetch_row($queryexe)) { 
    $progr += ($row[1] > 0 || $row[3] > 0 ? $row[0] : -$row[0]);
	print ("<tr class=\"list\">\n"); 
	print ("<td class=\"list\">" . format_date($row[4]) . "</td>\n"); 
	print ("<td class=\"list\">" . $row[5] . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: right;\">" . ($row[1] > 0 || $row[3] > 0 ? $row[0] : "&nbsp;") . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: right;\">" . ($row[2] > 0 || $row[3] < 0 ? $row[0] : "&nbsp;") . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: right;\">$progr</td>\n"); 
	print ("<td class=\"list\">" . $row[6] . "</td>\n"); 
	print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 
print("</table>\n<br>\n");

print("<br>\n");
print("<a class=\"bottommenu\" href=\"giornalemaga.php\">");
print("<img style=\"border: 0px;\" src=\"../img/05_edit.gif\" alt=\"Menu precedente\">Menu precedente</a>\n");
goMain();
footer();
?>