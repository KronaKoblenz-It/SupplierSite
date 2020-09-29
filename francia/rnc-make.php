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
head();
$id = $_GET['id'];
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$cf = $cookie[0];

$connectionstring = db_connect($dbase); 
$Query = "SELECT DOCRIG.DESCRIZION, DOCTES.TIPODOC, DOCTES.NUMERODOC, DOCTES.DATADOC, DOCTES.CODICECF, DOCTES.ESERCIZIO ";
$Query .= ",DOCRIG.CODICEARTI, DOCRIG.QUANTITA ";
$Query .= "FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA ";
$Query .= "WHERE DOCRIG.ID = $id";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$rnc = mysql_fetch_object($queryexe);
banner("Rapporto non conformit&agrave;",$rnc->DESCRIZION);

function prt_row($title, $data, $id, $readonly, $hidden, $size) {
	if( !$hidden) {
		print("<tr class=\"list\">\n");
		print("<th class=\"list\"><label for=\"$id\">$title</label></th>\n");	
		print("<td class=\"list\" style=\"padding: 2px;\">");
		print("<input type=\"text\" size=\"$size\"");
	} else {
		print("<input type=\"hidden\"");
	}
	print(" name=\"$id\" id=\"$id\"");
	if( $readonly) {
		print(" readonly=\"readonly\" style=\"background-color: #dddddd;\"");
	}  
	print(" value=\"" . htmlentities($data) . "\">&nbsp;\n");
	if( !$hidden) {
		print("</td>\n</tr>\n");	
	}
}

print("<form method=\"post\" action=\"rnc-write.php\">\n");
print("<table class=\"list\">\n");
prt_row("Data", format_date(date("Y-m-d")),"data",true,false,15 );  

print("<tr class=\"list\">\n");
print("<th class=\"list\"><label for=\"causa\">Causa della NC</label></th>\n");	
print("<td class=\"list\">\n");
print("<select name=\"causa\" id=\"causa\">\n");
$Query = "SELECT CODICE, DESCRIZION FROM ISOCAUSE";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
while($row = db_fetch_row($queryexe)) {
	print("<option value=\"" . $row[0] . "\">" . $row[1] . "</option>\n");  
}
print("</select>\n");
print("</td>\n</tr>\n");	

prt_row("Descrizione", $rnc->DESCRIZION, "descrizion", true, false,60);

$codicecf = ($rnc->CODICECF == $cf ? "F01021" : $rnc->CODICECF);
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE=\"$codicecf\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
prt_row("Fornitore", $row[0], "descforn", true, false, 60);

prt_row("Tipo Doc.", $rnc->TIPODOC, "tipodoc", true, false,5);
prt_row("Numero Doc.", $rnc->NUMERODOC, "numerodoc", true, false,15);
prt_row("Data Doc.", format_date($rnc->DATADOC), "datadoc", true, false,15);
prt_row("Articolo", $rnc->CODICEARTI, "codicearti", true, false,20);
prt_row("Quantit&agrave;", $rnc->QUANTITA, "quantita", false, false,20);

print("<tr class=\"list\">\n");
print("<th class=\"list\"><label for=\"dettaglio\">Dettagli</label></th>\n");	
print("<td class=\"list\">\n");
print("<textarea name=\"dettaglio\" id=\"dettaglio\" rows=\"4\" cols=\"60\"></textarea>\n");
print("</td>\n</tr>\n");	

//diconnect from database 
db_close($connectionstring); 
print("</table>\n");

prt_row("ID Doc.", $id, "id_doc", false, true,30);
prt_row("Codice Forn.", $codicecf, "codicecf", false, true,30);
prt_row("Esercizio Doc.", $rnc->ESERCIZIO, "doceser", false, true,30);
print("<input type=\"submit\" value=\"Inserisci RNC\">\n");

print("</form>\n");
print("<br>\n");
goMain();
footer();
?>