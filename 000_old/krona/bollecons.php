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
$connectionstring = db_connect($dbase); 

$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$cf = $cookie[0];
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$cf\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("DDT materiali da lavorare",htmlentities($row[0]));
$maga = "F" . substr($cf,2);

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">Data</th>\n"); 
print("<th class=\"list\">Ns. riferimento</th>\n"); 
print("<th class=\"list\">Fornitore</th>\n"); 
print("<th class=\"list\">Rif. fornitore</th>\n"); 
print("<th class=\"list\">&nbsp;</th>\n"); 
print("</tr>\n");

//SQL query  
$Query = "SELECT DOCTES.DATADOC, DOCTES.ID, DOCTES.NUMERODOCF, DOCTES.TIPODOC, DOCTES.NUMERODOC";
$Query .= ", ANAGRAFE.DESCRIZION, DOCTES.CODICECF";
$Query .= " FROM DOCTES INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF";
$Query .= " WHERE MAGARRIVO = \"$maga\"";
$Query .= " AND (TIPODOC=\"BT\" or TIPODOC=\"CE\" or TIPODOC=\"RL\" or TIPODOC=\"TL\") ORDER BY DATADOC DESC";

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

print("<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"5\" align=\"right\" valign=\"center\"><a href=\"doc2xml.php?mode=tl\" >"); 
print("<h3>Scarica Tutto <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n");

print("</table>\n");

print("<br>\n");
goMain();
footer();
?>