<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$connectionstring = db_connect($dbase);
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$forn = $cookie[0];
$isCdep = false;

$Query = "SELECT CODICEMAG FROM u_magcdep WHERE CODICECF=\"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
if($row = mysql_fetch_object($queryexe)) {
	$isCdep = true;
	$maga = $row->CODICEMAG;
}
banner("Menu Inventari",$cookie[1]);

print("<table class=\"list\">\n");

// Inventario materiali
print("<tr class=\"list\">\n");
$Query = "select data from u_invfine where magazzino=\"$maga\" and finito=1 ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
if( !($row = db_fetch_row($queryexe))) {
	print("<th class=\"menu\"><a href=\"inv_xls.php?mode=\">Caricamento inventario da Excel</a></th>\n");
} else {
	print("<th class=\"menu\">" . _("Inventario chiuso il") . " " . format_date($row[0]) . "</th>\n");
}
print("</tr>\n");
menuItem("inv-list.php?mode=", _("Verifica inventario"));

// Inventario attrezzature
print("<tr class=\"list\">\n");
$Query = "select data from u_invfinea where magazzino=\"$maga\" and finito=1 ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
if( !($row = db_fetch_row($queryexe))) {
	print("<th class=\"menu\"><a href=\"inv_xls.php?mode=attr\">Caricamento inventario attrezzature da Excel</a></th>\n");
} else {
	print("<th class=\"menu\">" . _("Inventario attrezzature chiuso il") . " " . format_date($row[0]) . "</th>\n");
}
print("</tr>\n");
menuItem("inv-list.php?mode=attr", _("Verifica inventario attrezzature"));

// manuale
$value="../manuali/04_Inventario_di_magazzino.pdf";
print("<tr class=\"list\">\n");
print("<th class=\"menu\">
	<a href=\"./manuali/$value\" target=\"_blank\">
	  <img src=\"../img/10_pdf.gif\" alt=\"download\" style=\"border: none;\">
	  &nbsp;Manuale
	</a>
  </th>\n");
print("</tr>\n");

menuItem("menu-for.php", _("Menu principale"));

print("</table>\n");


//diconnect from database
db_close($connectionstring);

footer();
?>
