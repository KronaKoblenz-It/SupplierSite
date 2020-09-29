<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$conn = db_connect($dbase); 
 
head();
banner("Documento cancellato");

if($_GET["id"] > 0) {
	$Query = "DELETE FROM U_BARDT WHERE ID=".$_GET["id"] ;
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$Query = "DELETE FROM U_BARDR WHERE ID_TESTA=".$_GET["id"] ;
	$rs = db_query($conn, $Query) or die(mysql_error()); 
}

print ("<br/><a href=\"menu-for.php\">Menu principale</a>\n");

 
footer();

?>