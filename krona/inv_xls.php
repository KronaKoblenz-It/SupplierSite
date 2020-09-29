<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

include_once("header.php"); 
include_once("db-utils.php");
include_once("inv_lib.php");
head();

pagestart();
include("inv_common.php");
banner("Caricamento inventario$attr da Excel",$cookie[1]);

script();
download();
upload();



print("<br>\n");
goMenu();
footer();

?>
