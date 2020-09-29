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
	    {column_number : 3, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 4, filter_type: "auto_complete"}
		]);
EOT;

head(dataTableInit($inc));

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);

$connectionstring = db_connect($dbase); 
banner($str_eleord[$lang],htmlentities($cookie[1]));


print("<table id=\"maintable\" class=\"list\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataord[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_nome[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_dataprevcons[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_stato[$lang] . "</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");


$Query =  "SELECT DOCTES.DATADOC,DOCTES.ID,DOCTES.NUMRIGHEPR,DOCTES.DATACONSEG,DOCTES.NUMERODOC,DOCTES.TIPODOC,";
$Query .= "ANAGRAFE.DESCRIZION FROM DOCTES ";
$Query .= "LEFT JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF ";
$Query .= "WHERE (DOCTES.AGENTE = '" . substr($cookie[0],1) . "' OR DOCTES.AGENTE2 = '" .substr($cookie[0],1) . "') " ;
$Query .= " AND (TIPODOC='OC' or TIPODOC='FO' or TIPODOC='LO' or TIPODOC='OF' or TIPODOC='OL' or TIPODOC='EC' or TIPODOC='EQ' or TIPODOC='XC') ORDER BY DATADOC DESC";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

while($row = mysql_fetch_object($queryexe)) { 
    $datadoc = format_date($row->DATADOC); 
    $dataconseg = format_date($row->DATACONSEG); 
    $stato = $row->NUMRIGHEPR >0 ? $str_nonevaso[$lang] : $str_evaso[$lang];
     
    //format results 
    print("<tr class=\"list\"");
	if( $row->NUMRIGHEPR == 0) {
		print(" style=\"background-color: #ff8080;\"");
	}
	print(">\n"); 
    print("<td class=\"list\"><a href=\"doc-detail.php?id=" . $row->ID . "\" >$datadoc</a></td>\n"); 
    print("<td class=\"list\">" . $row->TIPODOC . " " . $row->NUMERODOC . "</td>\n");
    print("<td class=\"list\">" . $row->DESCRIZION . "</td>\n");
    print("<td class=\"list\">$dataconseg</td>\n");
    print("<td class=\"list\">$stato</td>\n"); 
    print("</tr>\n"); 
} 

//diconnect from database 
db_close($connectionstring); 

print("</tbody>\n</table>\n");

print("<br>\n");
goMain();
footer();
?>