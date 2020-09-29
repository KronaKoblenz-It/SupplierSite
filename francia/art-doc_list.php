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
	    {column_number : 3, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 7, filter_type: "auto_complete"}
		]);
EOT;

head(dataTableInit($inc));
$codcf = $_GET['id'];
$um = $_GET['um'];
$art = $_GET['art'];

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

$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$art\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$rowa = db_fetch_row($queryexe);
banner($str_eleord[$lang]. " $art<br>\n" . $rowa[0],htmlentities($row[0]));

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_dataord[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_numero[$lang] . "</th>\n"); 
print("<th class=\"list\">Riga</th>\n"); 
print("<th class=\"list\">" . $str_dataprevcons[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_um[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_quantita[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_residuo[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_stato[$lang] . "</th>\n"); 
//print("<th class=\"list\">&nbsp;</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT ID,DATADOC,DATACONSEG,NUMERODOC,TIPODOC,NUMERORIGA,QUANTITA,QUANTITARE FROM DOCRIG ";
$Query .= "WHERE CODICECF = \"$codcf\" AND CODICEARTI = \"$art\" AND UNMISURA = \"$um\" AND " ;
$Query .= ("F01021" == $codcf ? "(TIPODOC=\"CP\" or TIPODOC=\"OI\")" : "(TIPODOC=\"OC\" or TIPODOC=\"FO\" or TIPODOC=\"LO\" or TIPODOC=\"OF\" or TIPODOC=\"OL\")");
$Query .= " ORDER BY DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = mysql_fetch_object($queryexe)) 
    { 
    $datadoc = format_date($row->DATADOC); 
    $datacons = format_date($row->DATACONSEG); 
    $stato = $row->QUANTITARE >0 ? $str_nonevaso[$lang] : $str_evaso[$lang];
     
    //format results 
    print("<tr class=\"list\"");
	if( $row->QUANTITARE == 0) {
		print(" style=\"background-color: #ff8080;\"");
	}
	print(">\n"); 
    print("<td class=\"list\"><a href=\"art-distbase.php?id=" . $row->ID . "\" >$datadoc</a></td>\n"); 
    print("<td class=\"list\">" . $row->TIPODOC . " " . $row->NUMERODOC . "</td>\n"); 
    print("<td class=\"list\">" . $row->NUMERORIGA . "</td>\n"); 
    print("<td class=\"list\">$datacons</td>\n"); 
    print("<td class=\"list\" style=\"text-align: center;\">$um</td>\n"); 
    print("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITA . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITARE . "</td>\n"); 
    print("<td class=\"list\">$stato</td>\n"); 
//    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"doc2xml.php?id=" . $row[1] . "\" >");
// 	  print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
//	  print("</a></td>\n"); 
    print("</tr>\n"); 
    } 

//disconnect from database 
db_close($connectionstring); 

print("</tbody>\n</table>\n");

print("<br>\n");
print("<a class=\"bottommenu\" href=\"art-detail.php?id=$codcf\">");
print("<img style=\"border: none;\" src=\"../img/05_edit.gif\" alt=\"" . $str_eleord[$lang] . " per articolo\">" . $str_eleord[$lang] . " per articolo</a>\n");
print("<br>\n");

goMain();
footer();
?>