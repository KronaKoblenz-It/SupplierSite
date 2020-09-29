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
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "text"},
	    {column_number : 1, filter_type: "text"}
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
banner(_("Materiali da lavorare"),htmlentities($row[0]));
$maga = "F" . substr($cf,2);
 
print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
trList();
thList( _("Codice") ); 
thList( _("Descrizione") ); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT DISTINCT DOCRIG.CODICEARTI, DOCRIG.DESCRIZION ";
$Query .= " FROM DOCRIG INNER JOIN DOCTES ON DOCRIG.ID_TESTA = DOCTES.ID";
$Query .= " WHERE DOCTES.MAGARRIVO = \"$maga\"";
$Query .= " AND (DOCRIG.TIPODOC=\"BT\" or DOCRIG.TIPODOC=\"CE\" or DOCRIG.TIPODOC=\"RL\" or DOCRIG.TIPODOC=\"TL\") ";
$Query .= " AND DOCRIG.CODICEARTI != \"\" AND DOCRIG.ESPLDISTIN != \"C\" ";
$Query .= " ORDER BY CODICEARTI";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
		trList(); 
		tdList("<a href=\"artcons-detail.php?art=" . urlencode($row[0]) . "\" >". $row[0] ."</a>"); 
		tdList($row[1]); 
		print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 

print("</tbody>\n");
print("</table>\n");

print("<br>\n");
goMain();
footer();
?>