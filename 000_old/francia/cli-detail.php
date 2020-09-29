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
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $_GET['id'] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner($str_eleord[$lang],htmlentities($row[0]));


print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataord[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_dataprevcons[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_stato[$lang] . "</th>\n"); 
print("</tr>\n");

//SQL query  
$Query = "SELECT DATADOC,ID,NUMRIGHEPR,DATACONSEG,NUMERODOC FROM DOCTES WHERE CODICECF = \"" . $_GET['id'] ;
$Query .= "\" AND (TIPODOC=\"OC\" or TIPODOC=\"FO\" or TIPODOC=\"LO\" or TIPODOC=\"OF\" or TIPODOC=\"OL\") ORDER BY DATADOC DESC";

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
    print("<td class=\"list\">" . $row[4] . "</td>\n"); 
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