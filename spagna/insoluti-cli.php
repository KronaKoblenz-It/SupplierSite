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
	    {column_number : 0, filter_type: "range_date", date_format: "yyyy-mm-dd"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "auto_complete"},
	    {column_number : 3, filter_type: "auto_complete"},
	    {column_number : 4, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 5, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);

$connectionstring = db_connect($dbase); 
banner("Insoluti clienti",htmlentities($cookie[1]));


print("<table id=\"maintable\" class=\"list\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_scadenza[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_importo[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_tipo[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_nome[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_data[$lang] . " " . $str_fatturatocon[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n");
print("</tr>\n</thead>\n<tbody>\n");


$Query =  "SELECT SCADENZE.DATASCAD, SCADENZE.IMPEFFVAL, SCADENZE.TIPO, ANAGRAFE.DESCRIZION, SCADENZE.DATAFATT, SCADENZE.NUMFATT FROM SCADENZE ";
$Query .= "LEFT JOIN ANAGRAFE ON ANAGRAFE.CODICE = SCADENZE.CODCF ";
$Query .= "WHERE (SCADENZE.CODAG = '" . substr($cookie[0],1) . "' OR SCADENZE.CODAG2 = '" .substr($cookie[0],1) . "') " ;
$Query .= " AND SCADENZE.PAGATO=0 AND DATASCAD < CURDATE() ORDER BY DATASCAD ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

while($row = mysql_fetch_object($queryexe)) { 
     
    //format results 
    print("<tr class=\"list\"");
	print(">\n"); 
    print("<td class=\"list\">" . $row->DATASCAD . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: right\">" . number_format($row->IMPEFFVAL, 2, ',', '.') . "</td>\n");
    print("<td class=\"list\">" . scad_tipo($row->TIPO, $lang) . "</td>\n");
    print("<td class=\"list\">" . $row->DESCRIZION . "</td>\n");
    print("<td class=\"list\">" . format_date($row->DATAFATT) . "</td>\n");
    print("<td class=\"list\">" . $row->NUMFATT . "</td>\n"); 
    print("</tr>\n"); 
} 

//diconnect from database 
db_close($connectionstring); 

print("</tbody>\n</table>\n");

print("<br>\n");
goMain();
footer();
?>