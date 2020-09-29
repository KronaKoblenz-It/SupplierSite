<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 1, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));
$codcf = $_GET['id'];
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$codLogin = $cookie[0];
$codType = $cookie[2];
$connectionstring = db_connect($dbase); 


if( "A" == $codType) {
	$Query = "SELECT AGENTE FROM ANAGRAFE WHERE CODICE=\"$codcf\"";
//	echo"$Query<br>\n";
	$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
	$row = db_fetch_row($queryexe);
	$pageOk = (trim($codLogin) == trim("A".$row[0]) );
//	echo "*".trim($codLogin) ."==". trim("A".$row[0])."*";
} else {
	$pageOk = ($codcf == $codLogin);
}

if( !$pageOk) {
	header("Location: login.php");
}

$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$codcf\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Elenco DDT di consegna",htmlentities($row[0]));


print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataddt[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n"); 
print("<th class=\"list\">XML</th>\n"); 
print("<th class=\"list\">Excel</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT DATADOC,ID,NUMRIGHEPR,DATACONSEG,NUMERODOC,TIPODOC FROM DOCTES WHERE CODICECF = \"$codcf\" AND " ;
$Query .= "(TIPODOC=\"BO\" or TIPODOC=\"BS\" or TIPODOC=\"BV\" or TIPODOC=\"BI\")" ;
$Query .= " ORDER BY DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($row[0]); 
     
    //format results 
    print("<tr class=\"list\"");
	print(">\n"); 
    print("<td class=\"list\"><a href=\"ddt-detail.php?id=" . $row[1] . "\" >$name</a></td>\n"); 
    print("<td class=\"list\">" . $row[5] . " " . $row[4] . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"doc2xml.php?id=" . $row[1] . "\" >");
	print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print("</a></td>\n"); 
    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"doc2xls.php?id=" . $row[1] . "\" >");
	print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print("</a></td>\n"); 
   print("</tr>\n"); 
    } 

//disconnect from database 
db_close($connectionstring); 

print("</tbody>\n<tfoot>\n<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"4\" align=\"right\" valign=\"center\"><a href=\"doc2xml.php?mode=bo\" >"); 
print("<h3>Scarica Tutto XML <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n");

print("</tfoot>\n</table>\n");

print("<br>\n");
goMain();
footer();
?>