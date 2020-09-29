<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "auto_complete"},
	    {column_number : 3, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));
$connectionstring = db_connect($dbase);

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$cf = $cookie[0];
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$cf\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner(_("DDT materiali da lavorare"),htmlentities($row[0]));
$maga = "F" . substr($cf,2);

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . _("Data") . "</th>\n"); 
print("<th class=\"list\">" . _("Ns. riferimento") . "</th>\n"); 
print("<th class=\"list\">" . _("Fornitore") . "</th>\n"); 
print("<th class=\"list\">" . _("Rif. fornitore") . "</th>\n"); 
print("<th class=\"list\">&nbsp;</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT DOCTES.DATADOC, DOCTES.ID, DOCTES.NUMERODOCF, DOCTES.TIPODOC, DOCTES.NUMERODOC";
$Query .= ", ANAGRAFE.DESCRIZION, DOCTES.CODICECF";
$Query .= " FROM DOCTES INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF";
$Query .= " WHERE MAGARRIVO = \"$maga\"";
$Query .= " AND (TIPODOC=\"BT\" or TIPODOC=\"CE\" or TIPODOC=\"RL\" or TIPODOC=\"TL\" or TIPODOC=\"SK\" or TIPODOC=\"KS\") ORDER BY DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $data = format_date($row[0]); 
    $addr = $row[3] . " " . $row[4]; 
    if($row[6] == $cf) {
		$name = "KRONA KOBLENZ S.P.A.";
	} else {
		$name = htmlentities($row[5]);
	}
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\"><a href=\"doc-detail.php?id=" . $row[1] . "\" >$data</a></td>\n"); 
    print ("<td class=\"list\">$addr</td>\n"); 
    print ("<td class=\"list\">$name</td>\n"); 
    print ("<td class=\"list\">" . $row[2] . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"doc2xml.php?id=" . $row[1] . "\" >");
	print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print("</a></td>\n"); 
   print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 

print("</tbody>\n<tfoot>\n<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"5\" align=\"right\" valign=\"center\"><a href=\"doc2xml.php?mode=tl\" >"); 
print("<h3>Scarica Tutto <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n</tfoot>\n");

print("</table>\n");

print("<br>\n");
goMain();
footer();
?>