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
//print($maga);

banner("Menu Principale",$cookie[1]);

//Avviso Temporaneo
if(in_array($cookie[0], array("F01021", "F00019", "F00051", "F00103", "F00255", "F00269", "F00276", "F00289", "F00331", "F00393", "F00496", "F00497", "F00499", "F00508", "F00715", "F00754", "F00833", "F00838", "F00866", "F00961", "F00963", "F01111", "F01280", "F01328", "F01338", "F01396", "F01420", "F01428", "F01487", "F01514", "F01538", "F01540", "F01559", "F01571", "F01584", "F01585", "F01606", "F01616", "F01618", "F01630", "F01726", "F01810", "F02015", "F02077", "F02253", "F02386", "F02513", "F02522", "F11196"))) {
	print("<div style=\"float: middle\" id=\"avviso\">\n");
	print("<fieldset style=\"width: 70%; float: center\"><legend><h3> AVVISO IMPORTANTE</h3></legend>\n");
	print("<p>Attenzione l'inventario di quest'anno acquista un particolare valore,
	poich&egrave; dal 2014 non saranno possibili movimentazioni di materiale con
	quantitativi inferiori o uguali a 0.</p></fieldset>\n");
	print("</div>\n");
}
print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
if("F01021" == $cookie[0]) {
	print("<th class=\"menu\"><a href=\"cli-detail.php?id=" . $cookie[0] . "\">Carichi di produzione & Ordini Lav. Interni</a></th>\n");
} else {
	print("<th class=\"menu\"><a href=\"cli-detail.php?id=" . $cookie[0] . '">'.$str_eleord[$lang]."</a></th>\n");
}
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"askdb.php\">Inserimento bolla</a></th>\n");
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