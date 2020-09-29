<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner("Menu Principale",$cookie[1]);

print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"menu\"><a href=\"cli-detail.php?id=" . $cookie[0] . '">'.$str_eleord[$lang]."</a></th>\n");
print("</tr>\n");
print("<tr class=\"list\">\n");
print('<th class="menu"><a href="askdb.php">Inserimento bolla</a></th> ');
print("</tr> ");
print("<tr class=\"list\">\n");
print('<th class="menu"><a href="ddttoload.php">Bolle in attesa di acquisizione</a></th> ');
print("</tr> ");
print("<tr class=\"list\">\n");
print('<th class="menu"><a href="inventario.php">Inserimento inventario</a></th> ');
print("</tr> ");
print("</table>");

footer();
?>