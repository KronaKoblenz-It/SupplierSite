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
	    {column_number : 2, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 3, filter_type: "auto_complete"}
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
if("F01021" == $codcf) {
	banner("Carichi di produzione",htmlentities($row[0]));
} else {
	banner($str_eleord[$lang],htmlentities($row[0]));
}

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataord[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_dataprevcons[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_stato[$lang] . "</th>\n"); 
print("<th class=\"list\">XML</th>\n"); 
print("<th class=\"list\">Excel</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT DATADOC,ID,NUMRIGHEPR,DATACONSEG,NUMERODOC,TIPODOC FROM DOCTES WHERE CODICECF = '$codcf' AND TIPODOC IN " ;
$Query .= ("F01021" == $codcf ? "('CP', 'OI', 'GC')" : "('OC', 'FO', 'LO', 'OF', 'OL', 'EC', 'EQ', 'XC', 'TR', 'OM', 'OW', 'MO', 'WO')");
$Query .= " ORDER BY DATADOC DESC";

//print($Query);
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
    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"doc2xls.php?id=" . $row[1] . "\" >");
	print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print("</a></td>\n"); 
   print("</tr>\n"); 
    } 

//disconnect from database 
db_close($connectionstring); 

print("</tbody>\n<tfoot>\n<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"6\" align=\"right\" valign=\"center\"><a href=\"doc2xml.php?mode=of\" >"); 
print("<h3>Scarica Tutto XML<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");
print("</tr>\n");

print("</tfoot>\n</table>\n");

print("<br>\n");
goMain();
footer();
?>