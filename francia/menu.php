<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/

include("header.php");
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner("Menu Principale",$cookie[1]);

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"rubrica-cli.php\">" . $str_rubrica[$lang] . "</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"ord-cli.php\">" . $str_eleord[$lang] . "</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"insoluti-cli.php\">Insoluti Clienti</a></th>\n");
print("</tr>\n");
print("</table>\n");

footer();
?>