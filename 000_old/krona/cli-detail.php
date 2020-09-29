<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
$codcf = $_GET['id'];
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$codcf\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
if("F01021" == $codcf) {
	banner("Carichi di produzione",htmlentities($row[0]));
} else {
	banner($str_eleord[$lang],htmlentities($row[0]));
}

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataord[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_dataprevcons[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_stato[$lang] . "</th>\n"); 
print("<th class=\"list\">&nbsp;</th>\n"); 
print("</tr>\n");

//SQL query  
$Query = "SELECT DATADOC,ID,NUMRIGHEPR,DATACONSEG,NUMERODOC,TIPODOC FROM DOCTES WHERE CODICECF = \"$codcf\" AND " ;
$Query .= ("F01021" == $codcf ? "TIPODOC=\"CP\"" : "(TIPODOC=\"OC\" or TIPODOC=\"FO\" or TIPODOC=\"LO\" or TIPODOC=\"OF\" or TIPODOC=\"OL\")");
$Query .= " ORDER BY DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($row[0]); 
    $addr = format_date($row[3]); 
    $stato = $row[2] >0 ? $str_nonevaso[$lang] : $str_evaso[$lang];
     
    //format results 
    print("<tr class=\"list\"");
	if( $row[2] == 0) {
		print(" style=\"background-color: #ff8080;\"");
	}
	print(">\n"); 
    print("<td class=\"list\"><a href=\"doc-detail.php?id=" . $row[1] . "\" >$name</a></td>\n"); 
    print("<td class=\"list\">" . $row[5] . " " . $row[4] . "</td>\n"); 
    print("<td class=\"list\">$addr</td>\n"); 
    print("<td class=\"list\">$stato</td>\n"); 
    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"doc2xml.php?id=" . $row[1] . "\" >");
	print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print("</a></td>\n"); 
    print("</tr>\n"); 
    } 

//disconnect from database 
db_close($connectionstring); 

print("<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"5\" align=\"right\" valign=\"center\"><a href=\"doc2xml.php?mode=of\" >"); 
print("<h3>Scarica Tutto <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n");

print("</table>\n");

print("<br>\n");
goMain();
footer();
?>