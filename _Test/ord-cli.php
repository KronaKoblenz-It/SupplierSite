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
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);

$connectionstring = db_connect($dbase); 
banner($str_eleord[$lang],htmlentities($cookie[1]));


print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataord[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_nome[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_dataprevcons[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_stato[$lang] . "</th>\n"); 
print("</tr>\n");


$Query =  "SELECT DOCTES.DATADOC,DOCTES.ID,DOCTES.NUMRIGHEPR,DOCTES.DATACONSEG,DOCTES.NUMERODOC,ANAGRAFE.DESCRIZION FROM DOCTES ";
$Query .= "LEFT JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF ";
$Query .= "WHERE (DOCTES.AGENTE = '" . substr($cookie[0],1) . "' OR DOCTES.AGENTE2 = '" .substr($cookie[0],1) . "') " ;
$Query .= " AND (TIPODOC='OC' or TIPODOC='FO' or TIPODOC='LO' or TIPODOC='OF' or TIPODOC='OL') ORDER BY DATADOC DESC";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

while($row = db_fetch_row($queryexe)) { 
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
    print("<td class=\"list\">" . $row[4] . "</td>\n");
    print("<td class=\"list\">" . $row[5] . "</td>\n");
    print("<td class=\"list\">$addr</td>\n");
    print("<td class=\"list\">$stato</td>\n"); 
    print("</tr>\n"); 
} 

//diconnect from database 
db_close($connectionstring); 

print("</table>\n");

print("<br>\n");
goMain();
footer();
?>