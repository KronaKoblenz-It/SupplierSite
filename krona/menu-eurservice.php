<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$forn = $cookie[0];

banner("Lanci di produzione",$cookie[1]);

print("<table class=\"list\">\n");

	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0352", _("Lancio produzione MAT.AZ.0352"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0353", _("Lancio produzione MAT.AZ.0353"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0354", _("Lancio produzione MAT.AZ.0354"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0363", _("Lancio produzione MAT.AZ.0363"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0364", _("Lancio produzione MAT.AZ.0364"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0365", _("Lancio produzione MAT.AZ.0365"));
	menuItem("askdb-eurservice.php?gruppo=UT25024A00300", _("Lancio produzione UT25024A00300"));
	menuItem("askdb-eurservice.php?gruppo=MAT.AZ.0415", _("Lancio produzione MAT.AZ.0415"));

menuItem("menu-for.php", _("Menu principale"));
print("</table>\n");

footer();
?>