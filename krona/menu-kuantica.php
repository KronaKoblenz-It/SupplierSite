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

menuItem("askdb-denken.php?gruppo=B0631", _("Lancio produzione MAT.AZ.0294"));
menuItem("askdb-denken.php?gruppo=B0630", _("Lancio produzione MAT.AZ.0297"));

menuItem("menu-for.php", _("Menu principale"));
print("</table>\n");

footer();
?>