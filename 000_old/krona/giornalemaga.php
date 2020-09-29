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
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$finito = "";

$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Inventario magazzino",$row[0]);

$Query = "SELECT finito FROM u_invfine WHERE magazzino = '" . $maga . "'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
$row = db_fetch_row($queryexe);
$finito = $row[0];
$eserc = $row[0] == 1 ? "2013" : "2012";

//echo $Query;
//echo $finito;
//echo $eserc;

//Lista articoli da considerare
$Query = "SELECT MAGGIAC.ARTICOLO, MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET, ";
$Query .= "MAGART.DESCRIZION ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "WHERE MAGAZZINO = \"$maga\" " . ($finito == 1 ? " AND ESERCIZIO = '2013'" : " AND ESERCIZIO = '2012' ");
$Query .= "ORDER BY ARTICOLO";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//print("<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<table class=\"list\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th class=\"list\">Articolo</th>\n"); 
print("<th class=\"list\">Descrizione</th>\n"); 
print("<th class=\"list\">Giacenza</th>\n"); 
print("<th class=\"list\">&nbsp;</th>\n"); 
print("</tr>\n");

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\"><a href=\"schedaart.php?art=" . urlencode($row[0]) . "&maga=$maga&esercizio=$eserc\" >" . $row[0] . "</a></td>\n");
    print ("<td class=\"list\">" . $row[2] . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . $row[1] . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: center;\"><a href=\"schedaartx.php?art=" . urlencode($row[0]) . "&maga=$maga\" >" );
	print ("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print ("</a></td>\n");
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 

print("<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"4\" align=\"right\" valign=\"center\"><a href=\"schedaartx.php?maga=$maga\" >"); 
print("<h3>Scarica Tutto <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n");

print("</table>\n");
print("<br>\n");
goMain();
footer();
?>