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
$magSfridi = "S" . substr($cookie[0], 2);
$forn = $cookie[0];
$isCdep = false;

$Query = "SELECT CODICEMAG FROM u_magcdep WHERE CODICECF=\"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
if($row = mysql_fetch_object($queryexe)) {
	$isCdep = true;
	$maga = $row->CODICEMAG;
}
$Query = "SELECT magazzino FROM MAGGIAC WHERE magazzino=\"$magSfridi\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error());
if ($row = mysql_fetch_object($queryexe)) {
	$isSFRIDI = true;
}
banner("Menu Inventari",$cookie[1]);

print("<table class=\"list\">\n");

// Inventario materiali
print("<tr class=\"list\">\n");
$Query = "select data from u_invfine where magazzino=\"$maga\" and finito=1 ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
if( !($row = db_fetch_row($queryexe))) {
	print("<th class=\"menu\"><a href=\"inv_xls.php?mode=\">Caricamento inventario PRODOTTI da Excel</a></th>\n");
} else {
	print("<th class=\"menu\">" . _("Inventario PRODOTTI chiuso il") . " " . format_date($row[0]) . "</th>\n");
}
print("</tr>\n");
menuItem("inv-list.php?mode=", _("Verifica inventario PRODOTTI"));
print("<tr class=\"list\">\n<th><hr/></th></tr>\n");

// Inventario attrezzature
print("<tr class=\"list\">\n");
$Query = "select data from u_invfinea where magazzino=\"$maga\" and finito=1 ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
if( !($row = db_fetch_row($queryexe))) {
	print("<th class=\"menu\"><a href=\"inv_xls.php?mode=attr\">Caricamento inventario ATTREZZATURE da Excel</a></th>\n");
} else {
	print("<th class=\"menu\">" . _("Inventario ATTREZZATURE chiuso il") . " " . format_date($row[0]) . "</th>\n");
}
print("</tr>\n");
menuItem("inv-list.php?mode=attr", _("Verifica inventario ATTREZZATURE"));
print("<tr class=\"list\">\n<th><hr/></th></tr>\n");

// Inventario mag SFRIDI
if($isSFRIDI){
	print("<tr class=\"list\">\n");
	$Query = "select data from u_invfine where magazzino=\"$magSfridi\" and finito=1 ";
	$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
	if (!($row = db_fetch_row($queryexe))) {
		print("<th class=\"menu\"><a href=\"inv_xls.php?mode=sfridi\">Caricamento inventario SFRIDI da Excel</a></th>\n");
	} else {
		print("<th class=\"menu\">" . _("Inventario SFRIDI chiuso il") . " " . format_date($row[0]) . "</th>\n");
	}
	print("</tr>\n");
	menuItem("inv-list.php?mode=sfridi", _("Verifica inventario SFRIDI"));
}
print("<tr class=\"list\">\n<th><hr/></th></tr>\n");

// manuale
$value="04_Inventario_di_magazzino_rel_del_18_07_19.pdf";
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
