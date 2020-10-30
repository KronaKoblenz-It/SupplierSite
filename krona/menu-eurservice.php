<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$forn = $cookie[0];
$connectionstring = db_connect($dbase); 

banner("Lanci di produzione",$cookie[1]);

print("<table class=\"list\">\n");

$Query = "select CODICE from GESTIONE_MACCHINE";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
while($row = mysql_fetch_object($queryexe)) {
	menuItem("askdb-eurservice.php?gruppo={$row->CODICE}", _("Lancio produzione {$row->CODICE}"));
}

/*
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0352", _("Lancio produzione MAT.AZ.0352"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0353", _("Lancio produzione MAT.AZ.0353"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0354", _("Lancio produzione MAT.AZ.0354"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0363", _("Lancio produzione MAT.AZ.0363"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0364", _("Lancio produzione MAT.AZ.0364"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0365", _("Lancio produzione MAT.AZ.0365"));
	menuItem("askdb-eurservice.php?gruppo=UT25024A00300", _("Lancio produzione UT25024A00300"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0415", _("Lancio produzione MAT.AZ.0415"));
*/
menuItem("menu-for.php", _("Menu principale"));
print("</table>\n");

footer();
?>