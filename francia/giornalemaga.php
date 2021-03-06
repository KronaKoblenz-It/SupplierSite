<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "text"},
	    {column_number : 1, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$finito = "";

$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner(_("Inventario magazzino") . " ".current_year(),$row[0]);

/*$Query = "SELECT finito FROM u_invfine WHERE magazzino = '" . $maga . "'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
$row = db_fetch_row($queryexe);
$finito = $row[0];
$eserc = $row[0] == 1 ? "2013" : "2012";*/
$eserc = current_year();

//echo $Query;
print("".$finito."");
//echo $eserc;

//Lista articoli da considerare
$Query = "SELECT MAGGIAC.ARTICOLO, (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA, ";
$Query .= "MAGART.DESCRIZION ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "WHERE MAGAZZINO = \"$maga\" AND ESERCIZIO =\"$eserc\"";
$Query .= "ORDER BY ARTICOLO";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//print("<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<table id=\"maintable\" class=\"list\">\n");
print("<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . _("Articolo") . "</th>\n"); 
print("<th class=\"list\">" . _("Descrizione") . "</th>\n"); 
print("<th class=\"list\">" . _("Giacenza") . "</th>\n"); 
print("<th class=\"list\">&nbsp;</th>\n"); 
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\"><a href=\"giacArtDetail.php?art=" . urlencode($row[0]) . "&maga=$maga&esercizio=$eserc\" >" . $row[0] . "</a></td>\n");
    print ("<td class=\"list\">" . $row[2] . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number(xRound($row[1])) . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: center;\"><a href=\"schedaartx.php?art=" . urlencode($row[0]) . "&maga=$maga&esercizio=$eserc\" >" );
	print ("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print ("</a></td>\n");
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 
print("</tbody>\n");
//print("</table>\n");

//print("<table class=\"list\">\n");
print("<tfoot>\n");
print("<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"4\" align=\"right\" valign=\"center\"><a href=\"schedaartx.php?maga=$maga&esercizio=$eserc\" >"); 
print("<h3>Scarica Tutto <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n");

print("</tfoot>\n</table>\n");
print("<br>\n");
goMain();
footer();
?>