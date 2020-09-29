<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head();

$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
include("dropdown_lib.php");
print("<script type=\"text/javascript\" src=\"ajaxlib.js\"></script>\n");
print("<script type=\"text/javascript\" src=\"select_lib.js\"></script>\n");
print("<script type=\"text/javascript\" src=\"inventario.js\"></script>\n");

banner("Lettura inventario");

print("\n<form action=\"inv_getqta.php\" method=\"get\">\n");

print("<table>\n");
print("<tr>\n");
print("<td><label for=\"articolo\">Articolo:</label></td>\n"); 
print("<td><input type=\"text\" name=\"articolo\" id=\"articolo\" onblur=\"listaLottix(this.value, '$maga');\"></td>\n");
print("</tr><tr>\n");
print("<td><label for=\"lotto\">Lotto:</label></td>\n"); 
print("<td>\n");
//print("<input type=\"text\" name=\"lotto\" id=\"lotto\">\n");
print(ddBox("lotto"));
print("</td>\n");
print("</tr>\n");
print("</table>\n");

print("<input type=\"submit\" value=\"Cerca\">\n");
print("</form>\n");

print ("<br><a href=\"menu-for.php\"><img border=\"0\" src=\"b_home.gif\" alt=\"Menu principale\">Menu principale</a>\n");
footer();
?>