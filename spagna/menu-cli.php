<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                          		     		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner($str_mainmenu[$lang],$cookie[1]);

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"cli-detail.php?id=" . $cookie[0] . '">'.$str_eleord[$lang]."</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"rnc.php\">Rapporti di non conformit&agrave;</a></th>\n");
print("</tr>\n");
print("</table>\n");

footer();
?>