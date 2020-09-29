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
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Inventario magazzino",$row[0]);

//Lista articoli da considerare
$Query = "SELECT MAGGIAC.ARTICOLO, MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET, ";
$Query .= "MAGART.DESCRIZION ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "WHERE MAGAZZINO = \"$maga\" ORDER BY ARTICOLO";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th height=\"22\">Articolo</th>\n"); 
print("<th height=\"22\">Descrizione</th>\n"); 
print("<th height=\"22\">Giacenza</th>\n"); 
print("</tr>\n");

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    //format results 
    print ("<tr bgcolor=\"#ccffcc\">\n"); 
    print ("<td><a href=\"schedaart.php?art=" . urlencode($row[0]) . "&maga=$maga\" >" . $row[0] . "</a></td>\n"); 
    print ("<td>" . $row[2] . "</td>\n"); 
    print ("<td align=\"right\">" . $row[1] . "</td>\n"); 
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</table>\n");
print ("<br><a href=\"menu-for.php\"><img border=\"0\" src=\"b_home.gif\" alt=\"Menu principale\">Menu principale</a>\n");

footer();
?>