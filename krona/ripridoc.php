<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$conn = db_connect($dbase); 
 
head();
banner("Documento ripristinato");

if($_GET["id"] > 0) {
	$Query = "UPDATE U_BARDT SET DEL=0 WHERE ID=".$_GET["id"] ;
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$Query = "UPDATE U_BARDR SET DEL=0 WHERE ID_TESTA=".$_GET["id"] ;
	$rs = db_query($conn, $Query) or die(mysql_error()); 
}

print ("<br/><a href=\"ripristinoddt.php\">Torna alla lista documenti</a>\n");
 
footer();

?>