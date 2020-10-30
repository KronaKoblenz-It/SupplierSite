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

banner("Configurazione macchine",$cookie[1]);

print("<table class=\"list\">\n");

$Query = "select CODICE from GESTIONE_MACCHINE";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
while($row = mysql_fetch_object($queryexe)) {
	menuItem("config-eurservice.php?gruppo={$row->CODICE}", _("Configurazione {$row->CODICE}"));
}

menuItem("menu-for.php", _("Menu principale"));
print("</table>\n");

footer();
?>