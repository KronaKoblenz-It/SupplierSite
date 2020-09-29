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
	banner(_("Carichi di produzione per articolo"),htmlentities($row[0]));
} else {
	banner(_("Elenco ordini per articolo"),htmlentities($row[0]));
}

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_codice[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_desc[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_um[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_quantita[$lang] . "</th>\n"); 
print("<th class=\"list\">" . $str_residuo[$lang] . "</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT CODICEARTI, SUM(QUANTITA) AS QTAORD, SUM(QUANTITARE) AS QTARE, MAX(DESCRIZION) AS DESCR, UNMISURA FROM DOCRIG";
$Query .= " WHERE CODICEARTI != \"\" AND CODICECF = \"$codcf\" AND " ;
$Query .= ("F01021" == $codcf ? "(TIPODOC=\"CP\" or TIPODOC=\"OI\")" : "(TIPODOC=\"OC\" or TIPODOC=\"FO\" or TIPODOC=\"LO\" or TIPODOC=\"OF\" or TIPODOC=\"OL\" or TIPODOC=\"OM\" or TIPODOC=\"OW\" or TIPODOC=\"MO\" or TIPODOC=\"WO\")");
$Query .= " GROUP BY CODICEARTI, UNMISURA";
//print("$Query<br>");

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = mysql_fetch_object($queryexe)) 
    { 
    //format results 
    print("<tr class=\"list\">\n"); 
    print("<td class=\"list\"><a href=\"art-doc_list.php?id=$codcf&art=" . urlencode($row->CODICEARTI) . "&um=" . urlencode($row->UNMISURA));
	print("\" >" . $row->CODICEARTI . "</a></td>\n"); 
    print("<td class=\"list\">" . $row->DESCR . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: center;\">" . $row->UNMISURA . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: right;\">" . $row->QTAORD . "</td>\n"); 
    print("<td class=\"list\" style=\"text-align: right;\">" . $row->QTARE . "</td>\n"); 
    print("</tr>\n"); 
    } 

//disconnect from database 
db_close($connectionstring); 

print("</tbody>\n");
print("</table>\n");

print("<br>\n");
goMain();
footer();
?>