<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$connectionstring = db_connect($dbase); 
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

banner("Menu Principale",$cookie[1]);

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
if("F01021" == $cookie[0]) {
	print("<th class=\"menu\"><a href=\"cli-detail.php?id=" . $cookie[0] . "\">Carichi di produzione</a></th>\n");
} else {
	print("<th class=\"menu\"><a href=\"cli-detail.php?id=" . $cookie[0] . '">'.$str_eleord[$lang]."</a></th>\n");
}
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"askdb.php\">Stampa Etichette Produzione</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"ddttoload.php\">Bolle in attesa di acquisizione</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"ddtimport.php\">Caricamento bolle da file XML</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"ddtimportxls.php\">Caricamento bolle da file Excel</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"giornalemaga.php\">Situazione magazzino</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"bollecons.php?id=" . $cookie[0] . "\">DDT Materiale da lavorare</a></th>\n");
print("</tr>\n");

print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"rnc.php\">Rapporti di non conformit&agrave;</a></th>\n");
print("</tr>\n");

print("<tr class=\"list\">\n"); 
$Query = "select data from u_invfine where magazzino=\"$maga\"";	
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
if( !($row = db_fetch_row($queryexe)) ) {
	print("<th class=\"menu\"><a href=\"inventario.php\">Inserimento inventario</a></th>\n");
} else {
	print("<th class=\"menu\">Inventario chiuso il " . format_date($row[0]) . "</th>\n");
} 
print("</tr>\n");

print("<tr class=\"list\">\n"); 
$Query = "select data from u_invfine where magazzino=\"$maga\"";	
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
if( !($row = db_fetch_row($queryexe)) ) {
	print("<th class=\"menu\"><a href=\"inv_xls.php\">Caricamento inventario da Excel</a></th>\n");
} else {
	print("<th class=\"menu\">Inventario chiuso il " . format_date($row[0]) . "</th>\n");
} 
print("</tr>\n");
	
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"inv-list.php\">Verifica inventario</a></th>\n");
print("</tr>\n");

print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"manualistica.php\">Manualistica</a></th>\n");
print("</tr>\n");
print("</table>\n");

//diconnect from database 
db_close($connectionstring); 

footer();
?>