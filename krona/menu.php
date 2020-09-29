<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/

include("header.php");
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner("Menu Principale",$cookie[1]);

print("<table class=\"list\">\n");
menuItem("rubrica-cli.php", $str_rubrica[$lang]);
menuItem("ord-cli.php", $str_eleord[$lang]);
menuItem("insoluti-cli.php", _("Insoluti Clienti"));
menuItem("enasarco.php", "Situazione Enasarco");
print("</tr>\n");
print("</table>\n");

footer();
?>