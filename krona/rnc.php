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
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "text"},
	    {column_number : 3, filter_type: "text"},
	    {column_number : 4, filter_type: "auto_complete"},
	    {column_number : 5, filter_type: "auto_complete"}
		]);
EOT;

head(dataTableInit($inc));
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);

$connectionstring = db_connect($dbase); 
banner("Lista rapporti non conformit&agrave;",$cookie[1]);

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">Data</th>\n"); 
print("<th class=\"list\">Numero</th>\n"); 
print("<th class=\"list\">Riferimento</th>\n"); 
print("<th class=\"list\">Descrizione</th>\n"); 
print("<th class=\"list\">Tipo</th>\n"); 
print("<th class=\"list\">Stato</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT ISORNC.DATAREG,ISORNC.DOCNMOV,ISORNC.DOCTIP,ISORNC.DATAEND,ISORNC.DESCRIZION,ISORNC.ID, ISORNC.NUMMOV,";
$Query .= "ISOCAUSE.DESCRIZION AS TIPONC ";
$Query .= "FROM ISORNC LEFT OUTER JOIN ISOCAUSE ON ISOCAUSE.CODICE = ISORNC.CAUSA ";
$Query .= "WHERE CODFOR = \"" . $cookie[0] ."\" " ;
$Query .= "ORDER BY ISORNC.DATAREG DESC ";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $datareg = format_date($row[0]); 
    $stato = $row[3] >0 ? "Chiusa" : "Aperta";
     
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\"><a href=\"rnc-detail.php?id=" . $row[5] . "\" >$datareg</a></td>\n"); 
    print ("<td class=\"list\">" . $row[6] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[2] . " " . $row[1] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[4] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[7] . "</td>\n"); 
    print ("<td class=\"list\">$stato</td>\n"); 
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</tbody>\n</table>\n");

print("<br>\n");
goMain();
footer();
?>