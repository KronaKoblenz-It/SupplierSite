<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
head();

banner("Caricamento bolle da file Excel","");

print("<form action=\"xls2doc.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
print("<label for=\"file\">Filename:</label>\n");
print("<input type=\"file\" name=\"file\" id=\"file\">\n"); 
print("<br>\n");
 
print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" >\n");
print("</form>\n");

print("<br>\n");
goMain();
footer();
?>