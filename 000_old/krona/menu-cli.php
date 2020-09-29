<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                          		     		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner($str_mainmenu[$lang],$cookie[1]);

print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<td height=\"22\"><a href=\"cli-detail.php?id=" . $cookie[0] . '">'.$str_eleord[$lang]."</a></td>\n");
print("</tr>\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<td height=\"22\"><a href=\"rnc.php\">Rapporti di non conformit&agrave;</a></td>\n");
print("</tr>\n");
print("</table>\n");

footer();
?>